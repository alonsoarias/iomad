/**
 * Platform Usage Report - Dashboard JavaScript Module
 *
 * @module     report_platform_usage/dashboard
 * @package    report_platform_usage
 * @copyright  2024 IOMAD
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/notification', 'core/str', 'core/templates'],
    function($, Ajax, Notification, Str, Templates) {
    'use strict';

    /**
     * Dashboard controller
     */
    var Dashboard = {
        // Configuration
        config: {
            ajaxUrl: '',
            courseId: 0,
            inCourseContext: false,
            sesskey: ''
        },

        // Chart instances
        charts: {},

        // Current data
        currentData: {},

        // Language strings
        strings: {},

        // Color palette
        colors: {
            brand: {
                primary: '#6366f1',
                primaryLight: '#818cf8',
                primaryDark: '#4f46e5'
            },
            success: {
                primary: '#10b981',
                light: '#34d399'
            },
            warning: {
                primary: '#f59e0b',
                light: '#fbbf24'
            },
            info: {
                primary: '#06b6d4',
                light: '#22d3ee'
            },
            error: {
                primary: '#f43f5e',
                light: '#fb7185'
            },
            gray: {
                50: '#f8fafc',
                100: '#f1f5f9',
                200: '#e2e8f0',
                500: '#64748b',
                700: '#334155',
                900: '#0f172a'
            }
        },

        /**
         * Initialize the dashboard
         * @param {Object} config Configuration object
         * @param {Object} initialData Initial report data
         * @param {Object} strings Language strings
         */
        init: function(config, initialData, strings) {
            this.config = $.extend(this.config, config);
            this.currentData = initialData;
            this.strings = strings;

            this.setupChartDefaults();
            this.initCharts(initialData);
            this.bindEvents();
            this.initTooltips();
        },

        /**
         * Setup Chart.js global defaults
         */
        setupChartDefaults: function() {
            if (typeof Chart === 'undefined') {
                return;
            }

            Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";
            Chart.defaults.font.size = 12;
            Chart.defaults.color = this.colors.gray[500];
            Chart.defaults.plugins.legend.labels.usePointStyle = true;
            Chart.defaults.plugins.legend.labels.padding = 16;
            Chart.defaults.plugins.tooltip.backgroundColor = this.colors.gray[900];
            Chart.defaults.plugins.tooltip.titleColor = '#ffffff';
            Chart.defaults.plugins.tooltip.bodyColor = '#e2e8f0';
            Chart.defaults.plugins.tooltip.borderColor = this.colors.gray[700];
            Chart.defaults.plugins.tooltip.borderWidth = 1;
            Chart.defaults.plugins.tooltip.padding = 12;
            Chart.defaults.plugins.tooltip.cornerRadius = 8;
            Chart.defaults.plugins.tooltip.displayColors = true;
            Chart.defaults.plugins.tooltip.boxPadding = 4;
        },

        /**
         * Initialize Bootstrap tooltips
         */
        initTooltips: function() {
            var self = this;
            // Initialize all tooltips
            $('[data-toggle="tooltip"]').tooltip({
                container: 'body',
                html: true,
                trigger: 'hover focus'
            });

            // Re-initialize on dynamic content
            $(document).on('shown.bs.tab', function() {
                $('[data-toggle="tooltip"]').tooltip('dispose').tooltip({
                    container: 'body',
                    html: true,
                    trigger: 'hover focus'
                });
            });
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            var self = this;

            // Global filter button
            $('#apply-global-filter').on('click', function() {
                var companyId = $('#companyid').val() || 0;
                var datefrom = $('#global-datefrom').val();
                var dateto = $('#global-dateto').val();
                self.loadReportData(companyId, datefrom, dateto);
            });

            // Company filter change
            $('#companyid').on('change', function() {
                var companyId = $(this).val();
                var datefrom = $('#global-datefrom').val();
                var dateto = $('#global-dateto').val();
                self.loadReportData(companyId, datefrom, dateto);
            });

            // Course context filter button
            $('#apply-course-filter').on('click', function() {
                var datefrom = $('#course-datefrom').val();
                var dateto = $('#course-dateto').val();
                self.loadCourseReportData(datefrom, dateto);
            });
        },

        /**
         * Initialize all charts
         * @param {Object} data Report data
         */
        initCharts: function(data) {
            var self = this;

            // Destroy existing charts
            Object.keys(this.charts).forEach(function(key) {
                if (self.charts[key] && typeof self.charts[key].destroy === 'function') {
                    self.charts[key].destroy();
                }
            });
            this.charts = {};

            // Daily Logins Chart
            this.createDailyLoginsChart(data);

            // User Activity Chart
            this.createUserActivityChart(data);

            // Course Access Chart
            this.createCourseAccessChart(data);

            // Completion Trends Chart
            this.createCompletionTrendsChart(data);

            // Dedication Chart
            this.createDedicationChart(data);
        },

        /**
         * Create Daily Logins Line Chart
         */
        createDailyLoginsChart: function(data) {
            var canvas = document.getElementById('dailyLoginsChart');
            if (!canvas || !data.daily_logins || !data.daily_logins.labels) {
                return;
            }

            var ctx = canvas.getContext('2d');
            this.charts.dailyLogins = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.daily_logins.labels,
                    datasets: [
                        {
                            label: this.strings.logins || 'Logins',
                            data: data.daily_logins.logins,
                            borderColor: this.colors.brand.primary,
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: this.strings.uniqueusers || 'Unique users',
                            data: data.daily_logins.unique_users,
                            borderColor: this.colors.success.primary,
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        },

        /**
         * Create User Activity Doughnut Chart
         */
        createUserActivityChart: function(data) {
            var canvas = document.getElementById('userActivityChart');
            if (!canvas) {
                return;
            }

            var activeUsers, inactiveUsers;
            if (this.config.inCourseContext && data.course_stats) {
                activeUsers = data.course_stats.active_users || 0;
                inactiveUsers = data.course_stats.inactive_users || 0;
            } else if (data.user_summary) {
                activeUsers = data.user_summary.active || 0;
                inactiveUsers = data.user_summary.inactive || 0;
            } else {
                return;
            }

            var ctx = canvas.getContext('2d');
            this.charts.userActivity = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: [
                        this.strings.activeusers || 'Active users',
                        this.strings.inactiveusers || 'Inactive users'
                    ],
                    datasets: [{
                        data: [activeUsers, inactiveUsers],
                        backgroundColor: [this.colors.success.primary, this.colors.error.primary],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        },

        /**
         * Create Course Access Trends Chart
         */
        createCourseAccessChart: function(data) {
            var canvas = document.getElementById('courseAccessChart');
            if (!canvas || !data.course_access_trends || !data.course_access_trends.labels) {
                return;
            }

            var ctx = canvas.getContext('2d');
            this.charts.courseAccess = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.course_access_trends.labels,
                    datasets: [{
                        label: this.strings.courseaccesses || 'Course accesses',
                        data: data.course_access_trends.data,
                        borderColor: this.colors.warning.primary,
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        },

        /**
         * Create Completion Trends Chart
         */
        createCompletionTrendsChart: function(data) {
            var canvas = document.getElementById('completionTrendsChart');
            if (!canvas || !data.completion_trends || !data.completion_trends.labels) {
                return;
            }

            var ctx = canvas.getContext('2d');
            this.charts.completionTrends = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.completion_trends.labels,
                    datasets: [{
                        label: this.strings.completions || 'Completions',
                        data: data.completion_trends.data,
                        borderColor: this.colors.info.primary,
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        },

        /**
         * Create Dedication Chart (horizontal bar)
         */
        createDedicationChart: function(data) {
            var canvas = document.getElementById('dedicationChart');
            if (!canvas || !data.top_dedication || data.top_dedication.length === 0) {
                return;
            }

            var ctx = canvas.getContext('2d');
            this.charts.dedication = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.top_dedication.map(function(c) {
                        var name = c.shortname || c.fullname || '';
                        return name.length > 18 ? name.substring(0, 15) + '...' : name;
                    }),
                    datasets: [{
                        label: this.strings.dedicationpercent || 'Share',
                        data: data.top_dedication.map(function(c) {
                            return c.dedication_percent;
                        }),
                        backgroundColor: this.colors.brand.primary,
                        borderColor: this.colors.brand.primaryDark,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        },

        /**
         * Load report data via AJAX
         * @param {number} companyId Company ID
         * @param {string} datefrom Date from
         * @param {string} dateto Date to
         */
        loadReportData: function(companyId, datefrom, dateto) {
            var self = this;
            var $loading = $('#loading-indicator');
            $loading.show();

            var datefromTs = new Date(datefrom).getTime() / 1000;
            var datetoTs = new Date(dateto).getTime() / 1000 + 86399;

            $.ajax({
                url: this.config.ajaxUrl,
                method: 'GET',
                data: {
                    companyid: companyId,
                    datefrom: Math.floor(datefromTs),
                    dateto: Math.floor(datetoTs)
                },
                dataType: 'json'
            }).done(function(data) {
                self.currentData = data;
                self.updateSummaryCards(data);
                self.initCharts(data);
                self.updateTables(data);
                self.updateExportLinks(companyId, datefrom, dateto);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                Notification.exception({message: 'Error loading report data: ' + errorThrown});
            }).always(function() {
                $loading.hide();
            });
        },

        /**
         * Load course report data via AJAX
         * @param {string} datefrom Date from
         * @param {string} dateto Date to
         */
        loadCourseReportData: function(datefrom, dateto) {
            var self = this;
            var $loading = $('#course-loading-indicator');
            $loading.show();

            var datefromTs = new Date(datefrom).getTime() / 1000;
            var datetoTs = new Date(dateto).getTime() / 1000 + 86399;

            $.ajax({
                url: this.config.ajaxUrl,
                method: 'GET',
                data: {
                    courseid: this.config.courseId,
                    datefrom: Math.floor(datefromTs),
                    dateto: Math.floor(datetoTs)
                },
                dataType: 'json'
            }).done(function(data) {
                self.currentData = data;
                self.updateSummaryCards(data);
                self.initCharts(data);
                self.updateTables(data);
                self.updateCourseExportLinks(datefrom, dateto);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                Notification.exception({message: 'Error loading course report data: ' + errorThrown});
            }).always(function() {
                $loading.hide();
            });
        },

        /**
         * Update summary cards with new data
         * @param {Object} data Report data
         */
        updateSummaryCards: function(data) {
            // Platform Access card
            this.updateElement('logins-today', this.formatNumber(data.login_summary.logins_today));
            this.updateElement('logins-week', this.formatNumber(data.login_summary.logins_week));
            this.updateElement('logins-month', this.formatNumber(data.login_summary.logins_month));
            this.updateElement('unique-month', this.formatNumber(data.login_summary.unique_users_month));
            this.updateElement('unique-week', this.formatNumber(data.login_summary.unique_users_week));

            // User Summary card
            if (data.user_summary) {
                this.updateElement('total-users', this.formatNumber(data.user_summary.total));
                this.updateElement('active-users', this.formatNumber(data.user_summary.active));
                this.updateElement('inactive-users', this.formatNumber(data.user_summary.inactive));
            }

            // Completions card
            if (data.completions_summary) {
                this.updateElement('completions-today', this.formatNumber(data.completions_summary.completions_today));
                this.updateElement('completions-week', this.formatNumber(data.completions_summary.completions_week));
                this.updateElement('completions-month', this.formatNumber(data.completions_summary.completions_month));
                this.updateElement('total-completions', this.formatNumber(data.completions_summary.total_completions));
            }

            // Daily Users card
            if (data.daily_users && data.daily_users.data) {
                var maxDaily = Math.max.apply(null, data.daily_users.data) || 0;
                var avgDaily = data.daily_users.data.length > 0 ?
                    Math.round(data.daily_users.data.reduce(function(a, b) { return a + b; }, 0) / data.daily_users.data.length) : 0;
                var todayDaily = data.daily_users.data[data.daily_users.data.length - 1] || 0;
                this.updateElement('daily-today', this.formatNumber(todayDaily));
                this.updateElement('daily-avg', this.formatNumber(avgDaily));
                this.updateElement('daily-max', this.formatNumber(maxDaily));
            }

            // Course-specific cards
            if (data.course_stats) {
                this.updateElement('course-accesses', this.formatNumber(data.course_stats.accesses || 0));
            }
        },

        /**
         * Update tables with new data
         * @param {Object} data Report data
         */
        updateTables: function(data) {
            // Update courses table
            this.updateCoursesTable(data.top_courses || []);

            // Update activities table
            this.updateActivitiesTable(data.top_activities || []);

            // Update daily users table
            this.updateDailyUsersTable(data.daily_users || {});
        },

        /**
         * Update courses table
         * @param {Array} courses List of courses
         */
        updateCoursesTable: function(courses) {
            var $container = $('#top-courses-table');
            if (!$container.length) {
                return;
            }

            if (courses.length === 0) {
                $container.html('<p class="text-muted">' + (this.strings.nodata || 'No data') + '</p>');
                return;
            }

            var html = '<div class="table-responsive"><table class="table table-striped table-sm">';
            html += '<thead class="thead-light"><tr>';
            html += '<th>' + (this.strings.coursename || 'Course') + '</th>';
            html += '<th class="text-right">' + (this.strings.courseaccesses || 'Accesses') + '</th>';
            html += '<th class="text-right">' + (this.strings.uniqueusers || 'Unique users') + '</th>';
            html += '</tr></thead><tbody>';

            courses.forEach(function(course) {
                html += '<tr>';
                html += '<td>' + this.escapeHtml(course.fullname) + '</td>';
                html += '<td class="text-right">' + this.formatNumber(course.access_count) + '</td>';
                html += '<td class="text-right">' + this.formatNumber(course.unique_users) + '</td>';
                html += '</tr>';
            }, this);

            html += '</tbody></table></div>';
            $container.html(html);
        },

        /**
         * Update activities table
         * @param {Array} activities List of activities
         */
        updateActivitiesTable: function(activities) {
            var $container = $('#top-activities-table');
            if (!$container.length) {
                return;
            }

            if (activities.length === 0) {
                $container.html('<p class="text-muted">' + (this.strings.nodata || 'No data') + '</p>');
                return;
            }

            var html = '<div class="table-responsive"><table class="table table-striped table-sm">';
            html += '<thead class="thead-light"><tr>';
            html += '<th>' + (this.strings.activityname || 'Activity') + '</th>';
            html += '<th>' + (this.strings.activitytype || 'Type') + '</th>';
            html += '<th class="text-right">' + (this.strings.activityaccesses || 'Views') + '</th>';
            html += '<th class="text-right">' + (this.strings.uniqueusers || 'Unique users') + '</th>';
            html += '</tr></thead><tbody>';

            activities.forEach(function(activity) {
                html += '<tr>';
                html += '<td>' + this.escapeHtml(activity.name) + '</td>';
                html += '<td><span class="badge badge-secondary">' + this.escapeHtml(activity.type_name || activity.type) + '</span></td>';
                html += '<td class="text-right">' + this.formatNumber(activity.access_count) + '</td>';
                html += '<td class="text-right">' + this.formatNumber(activity.unique_users) + '</td>';
                html += '</tr>';
            }, this);

            html += '</tbody></table></div>';
            $container.html(html);
        },

        /**
         * Update daily users table
         * @param {Object} dailyUsers Daily users data
         */
        updateDailyUsersTable: function(dailyUsers) {
            var $container = $('#daily-users-table');
            if (!$container.length || !dailyUsers.records) {
                return;
            }

            if (dailyUsers.records.length === 0) {
                $container.html('<p class="text-muted p-3">' + (this.strings.nodata || 'No data') + '</p>');
                return;
            }

            var html = '<div class="table-responsive" style="max-height: 250px; overflow-y: auto;">';
            html += '<table class="table table-striped table-sm mb-0">';
            html += '<thead class="thead-light" style="position: sticky; top: 0;"><tr>';
            html += '<th>' + (this.strings.date || 'Date') + '</th>';
            html += '<th class="text-right">' + (this.strings.uniqueusers || 'Unique users') + '</th>';
            html += '</tr></thead><tbody>';

            dailyUsers.records.forEach(function(record) {
                html += '<tr>';
                html += '<td>' + record.fecha_formateada + '</td>';
                html += '<td class="text-right"><span class="badge badge-primary">' + this.formatNumber(record.cantidad_usuarios) + '</span></td>';
                html += '</tr>';
            }, this);

            html += '</tbody></table></div>';
            $container.html(html);
        },

        /**
         * Update export links with current filters
         * @param {number} companyId Company ID
         * @param {string} datefrom Date from
         * @param {string} dateto Date to
         */
        updateExportLinks: function(companyId, datefrom, dateto) {
            var datefromTs = Math.floor(new Date(datefrom).getTime() / 1000);
            var datetoTs = Math.floor(new Date(dateto).getTime() / 1000 + 86399);

            $('#export-excel, #export-csv').each(function() {
                var $link = $(this);
                var url = $link.attr('href');
                url = url.replace(/companyid=\d+/, 'companyid=' + companyId);
                url = url.replace(/datefrom=\d+/, 'datefrom=' + datefromTs);
                url = url.replace(/dateto=\d+/, 'dateto=' + datetoTs);
                $link.attr('href', url);
            });
        },

        /**
         * Update course export links with current filters
         * @param {string} datefrom Date from
         * @param {string} dateto Date to
         */
        updateCourseExportLinks: function(datefrom, dateto) {
            var datefromTs = Math.floor(new Date(datefrom).getTime() / 1000);
            var datetoTs = Math.floor(new Date(dateto).getTime() / 1000 + 86399);

            $('#export-excel-course, #export-csv-course').each(function() {
                var $link = $(this);
                var url = $link.attr('href');
                url = url.replace(/datefrom=\d+/, 'datefrom=' + datefromTs);
                url = url.replace(/dateto=\d+/, 'dateto=' + datetoTs);
                $link.attr('href', url);
            });
        },

        /**
         * Update element text content
         * @param {string} id Element ID
         * @param {string} value New value
         */
        updateElement: function(id, value) {
            var $el = $('#' + id);
            if ($el.length) {
                $el.text(value);
            }
        },

        /**
         * Format number with thousands separator
         * @param {number} num Number to format
         * @returns {string} Formatted number
         */
        formatNumber: function(num) {
            if (num === null || num === undefined) {
                return '0';
            }
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },

        /**
         * Escape HTML special characters
         * @param {string} text Text to escape
         * @returns {string} Escaped text
         */
        escapeHtml: function(text) {
            if (!text) {
                return '';
            }
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    return {
        /**
         * Initialize the dashboard module
         * @param {Object} config Configuration
         * @param {Object} initialData Initial data
         * @param {Object} strings Language strings
         */
        init: function(config, initialData, strings) {
            Dashboard.init(config, initialData, strings);
        }
    };
});

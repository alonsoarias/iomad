<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Usage Monitor Report main dashboard page.
 *
 * This is the main entry point for the usage monitor report,
 * displaying disk usage, user statistics, and various metrics.
 *
 * @package     report_usage_monitor
 * @author      Alonso Arias <soporte@ingeweb.co>
 * @copyright   2025 Alonso Arias <soporte@ingeweb.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/report/usage_monitor/locallib.php');

admin_externalpage_setup('report_usage_monitor', '', null, '', ['pagelayout' => 'admin']);

// ============================================================
// DATA PREPARATION
// ============================================================

$reportconfig = get_config('report_usage_monitor');

// Disk usage data
$disk_usage_bytes = (int)($reportconfig->totalusagereadable ?? 0) + (int)($reportconfig->totalusagereadabledb ?? 0);
$quotadisk_bytes  = ((int)($reportconfig->disk_quota ?? 0)) * 1024 * 1024 * 1024;
$disk_usage_gb = !empty($reportconfig->disk_usage_gb) ? $reportconfig->disk_usage_gb : display_size_in_gb($disk_usage_bytes, 2);
$quotadisk_gb = !empty($reportconfig->quotadisk_gb) ? $reportconfig->quotadisk_gb : display_size_in_gb($quotadisk_bytes, 2);
$disk_percent = !empty($reportconfig->disk_percent) ? (float)$reportconfig->disk_percent :
    (($quotadisk_bytes > 0) ? ($disk_usage_bytes / $quotadisk_bytes * 100) : 0);
$disk_warning_class = ($disk_percent < 70) ? 'bg-success' : (($disk_percent < 90) ? 'bg-warning' : 'bg-danger');

// Users data
$users_today = (int)($reportconfig->totalusersdaily ?? 0);
$max_users_threshold = (int)($reportconfig->max_daily_users_threshold ?? 100);
$users_percent = ($max_users_threshold > 0) ? ($users_today / $max_users_threshold * 100) : 0;
$users_warning_class = ($users_percent < 70) ? 'bg-success' : (($users_percent < 90) ? 'bg-warning' : 'bg-danger');

// Last execution timestamps
$lastexec_disk_ts = !empty($reportconfig->lastexecutioncalculate) ? $reportconfig->lastexecutioncalculate : 0;
$lastexec_disk = (!is_numeric($lastexec_disk_ts) || $lastexec_disk_ts <= 0) ?
    get_string('notcalculatedyet', 'report_usage_monitor') : date('d/m/Y H:i', (int)$lastexec_disk_ts);

$lastexec_users_ts = !empty($reportconfig->lastexecution) ? $reportconfig->lastexecution : 0;
$lastexec_users = (!is_numeric($lastexec_users_ts) || $lastexec_users_ts <= 0) ?
    get_string('notcalculatedyet', 'report_usage_monitor') : date('d/m/Y H:i', (int)$lastexec_users_ts);

// Max 90 days data
$max_90_days_users = $reportconfig->max_userdaily_for_90_days_users ?? get_string('notcalculatedyet', 'report_usage_monitor');
$max_90_days_date_ts = $reportconfig->max_userdaily_for_90_days_date ?? 0;
$max_90_days_date = (!is_numeric($max_90_days_date_ts) || $max_90_days_date_ts <= 0) ?
    get_string('notcalculatedyet', 'report_usage_monitor') : date('d/m/Y', (int)$max_90_days_date_ts);

// Directory analysis
$dir_analysis_json = $reportconfig->dir_analysis ?? '{}';
$dir_analysis = json_decode($dir_analysis_json, true);
if (empty($dir_analysis) || !is_array($dir_analysis)) {
    $dir_analysis = ['database' => 0, 'filedir' => 0, 'cache' => 0, 'others' => 0];
}

// Prepare directories for template
$directories = [];
if (!empty($dir_analysis) && $disk_usage_bytes > 0) {
    $dir_items = [
        'database' => ['label' => get_string('database', 'report_usage_monitor'), 'size' => (int)($dir_analysis['database'] ?? 0)],
        'filedir'  => ['label' => get_string('files_dir', 'report_usage_monitor'), 'size' => (int)($dir_analysis['filedir'] ?? 0)],
        'cache'    => ['label' => get_string('cache', 'report_usage_monitor'), 'size' => (int)($dir_analysis['cache'] ?? 0)],
        'others'   => ['label' => get_string('others', 'report_usage_monitor'), 'size' => (int)($dir_analysis['others'] ?? 0)],
    ];
    uasort($dir_items, function($a, $b) { return $b['size'] - $a['size']; });
    foreach ($dir_items as $dir_data) {
        $directories[] = [
            'label' => $dir_data['label'],
            'size_formatted' => display_size_in_gb($dir_data['size'], 2),
            'percent' => round(($dir_data['size'] / $disk_usage_bytes) * 100, 1)
        ];
    }
}

// Largest courses
$largest_courses_json = $reportconfig->largest_courses ?? '[]';
$largest_courses = json_decode($largest_courses_json);
if (empty($largest_courses)) {
    $largest_courses = get_largest_courses(5);
}

// Format largest courses for template
$largest_courses_formatted = [];
foreach ($largest_courses as $course) {
    $largest_courses_formatted[] = [
        'id' => $course->id,
        'fullname' => format_string($course->fullname),
        'course_url' => $CFG->wwwroot . '/course/view.php?id=' . $course->id,
        'totalsize_formatted' => display_size($course->totalsize),
        'backupcount' => $course->backupcount
    ];
}

// Users daily (last 10 days)
$userdaily_records = $DB->get_records_sql(report_user_daily_sql());
$formatted_userdaily_records = [];
foreach ($userdaily_records as $record) {
    $formatted_userdaily_records[] = [
        'conteo_accesos_unicos' => $record->conteo_accesos_unicos,
        'fecha_formateada' => is_numeric($record->timestamp_fecha) ? date('d/m/Y', (int)$record->timestamp_fecha) : date('d/m/Y')
    ];
}

// Top users daily
$userdaily_recordstop = $DB->get_records_sql(report_user_daily_top_sql());
$formatted_userdaily_recordstop = [];
foreach ($userdaily_recordstop as $record) {
    $percent = ($max_users_threshold > 0) ? round(($record->cantidad_usuarios / $max_users_threshold) * 100, 1) : 0;
    $formatted_userdaily_recordstop[] = [
        'cantidad_usuarios' => $record->cantidad_usuarios,
        'fecha_formateada' => is_numeric($record->timestamp_fecha) ? date('d/m/Y', (int)$record->timestamp_fecha) : date('d/m/Y'),
        'percent' => $percent,
        'percent_class' => ($percent >= 90) ? 'text-danger fw-bold' : (($percent >= 70) ? 'text-warning' : '')
    ];
}

// System info
$totalcourses = $DB->count_records('course');
$activeusers = max(0, $DB->count_records('user', ['deleted' => 0, 'suspended' => 0]) - 1);
$suspendedusers = $DB->count_records('user', ['deleted' => 0, 'suspended' => 1]);
$backup_max_kept = get_config('backup', 'backup_auto_max_kept') ?? 0;

// Disk history (last 30 days)
$month_ago = time() - (30 * 24 * 60 * 60);
$disk_history = $DB->get_records_sql(
    "SELECT id, timecreated, value, percentage FROM {report_usage_monitor_history} WHERE type = 'disk' AND timecreated > ? ORDER BY timecreated ASC",
    [$month_ago]
);

$disk_history_labels = [];
$disk_history_data = [];
$daily_data = [];
foreach ($disk_history as $record) {
    if (is_numeric($record->timecreated) && $record->timecreated > 0) {
        $date_key = date('Y-m-d', (int)$record->timecreated);
        $current_percentage = ($quotadisk_bytes > 0) ? round(($record->value / $quotadisk_bytes) * 100, 1) : 0;
        $daily_data[$date_key] = [
            'label' => date('d/m', (int)$record->timecreated),
            'percentage' => $current_percentage
        ];
    }
}
foreach ($daily_data as $data) {
    $disk_history_labels[] = $data['label'];
    $disk_history_data[] = $data['percentage'];
}

// Course access trends (last 30 days)
$course_access_trends = get_course_access_trends(30);
$most_accessed_courses = get_most_accessed_courses(10, 30);
$course_completion_trends = get_course_completion_trends(30);
$access_summary = get_access_summary(30);
$completion_summary = get_completion_summary(30);

// Prepare chart data
$access_trends_labels = [];
$access_trends_data = [];
foreach ($course_access_trends as $trend) {
    $access_trends_labels[] = $trend->fecha_formateada;
    $access_trends_data[] = $trend->total_accesses;
}

$completion_trends_labels = [];
$completion_trends_data = [];
foreach ($course_completion_trends as $trend) {
    $completion_trends_labels[] = $trend->fecha_formateada;
    $completion_trends_data[] = $trend->completions;
}

// Prepare data for line chart (users last 10 days)
$last10daysLabels = [];
$last10daysData = [];
$last10daysDataRaw = [];
foreach ($formatted_userdaily_records as $day) {
    $last10daysLabels[] = $day['fecha_formateada'];
    $percent = ($max_users_threshold > 0) ? min(100, round(($day['conteo_accesos_unicos'] / $max_users_threshold) * 100, 1)) : 0;
    $last10daysData[] = $percent;
    $last10daysDataRaw[] = (int)$day['conteo_accesos_unicos'];
}

// Doughnut chart data
$doughnutLabels = [
    get_string('database', 'report_usage_monitor'),
    get_string('files_dir', 'report_usage_monitor'),
    get_string('cache', 'report_usage_monitor'),
    get_string('others', 'report_usage_monitor')
];
$doughnutData = [
    display_size_in_gb($dir_analysis['database'] ?? 0, 2),
    display_size_in_gb($dir_analysis['filedir'] ?? 0, 2),
    display_size_in_gb($dir_analysis['cache'] ?? 0, 2),
    display_size_in_gb($dir_analysis['others'] ?? 0, 2)
];

// Check if report_coursesize plugin is installed
$reportplugins = \core_plugin_manager::instance()->get_plugins_of_type('report');
$coursesize_installed = isset($reportplugins['coursesize']);

// Format most accessed courses for template
$most_accessed_formatted = [];
$rank = 1;
foreach ($most_accessed_courses as $course) {
    $most_accessed_formatted[] = [
        'rank' => $rank++,
        'id' => $course->id,
        'fullname' => format_string($course->fullname),
        'shortname' => $course->shortname,
        'course_url' => $CFG->wwwroot . '/course/view.php?id=' . $course->id,
        'total_accesses_formatted' => number_format($course->total_accesses),
        'unique_users_formatted' => number_format($course->unique_users)
    ];
}

// Course dedication data (block_dedication integration)
$dedication_available = is_block_dedication_installed();
$top_courses_dedication = [];
$dedication_summary = null;

if ($dedication_available) {
    $top_courses_dedication_raw = get_top_courses_by_dedication(10, 90);
    $dedication_summary = get_dedication_summary(90);

    // Format for template
    foreach ($top_courses_dedication_raw as $course) {
        $top_courses_dedication[] = [
            'rank' => $course->rank,
            'id' => $course->id,
            'fullname' => $course->fullname,
            'shortname' => $course->shortname,
            'course_url' => $CFG->wwwroot . '/course/view.php?id=' . $course->id,
            'total_dedication_formatted' => $course->total_dedication_formatted,
            'avg_dedication_formatted' => $course->avg_dedication_formatted,
            'enrolled_students' => $course->enrolled_students,
            'dedication_percent' => $course->dedication_percent
        ];
    }
}

// ============================================================
// PREPARE TEMPLATE CONTEXT
// ============================================================

$templatecontext = [
    // Disk data
    'disk_usage_gb' => $disk_usage_gb,
    'quotadisk_gb' => $quotadisk_gb,
    'disk_percent' => round($disk_percent, 1),
    'disk_percent_capped' => min($disk_percent, 100),
    'disk_warning_class' => $disk_warning_class,
    'lastexec_disk' => $lastexec_disk,
    'has_disk_data' => ($disk_usage_bytes > 0),
    'has_disk_history' => !empty($disk_history_labels),
    'directories' => $directories,
    'largest_courses' => $largest_courses_formatted,

    // Users data
    'users_today' => $users_today,
    'max_users_threshold' => $max_users_threshold,
    'users_percent' => round($users_percent, 1),
    'users_percent_capped' => min($users_percent, 100),
    'users_warning_class' => $users_warning_class,
    'lastexec_users' => $lastexec_users,
    'max_90_days_users' => $max_90_days_users,
    'max_90_days_date' => $max_90_days_date,
    'has_users_data' => !empty($last10daysLabels),
    'userdaily_records' => $formatted_userdaily_records,
    'userdaily_top' => $formatted_userdaily_recordstop,

    // Course data
    'has_access_trends' => !empty($access_trends_labels),
    'has_completion_trends' => !empty($completion_trends_labels),
    'most_accessed_courses' => $most_accessed_formatted,
    'access_summary' => [
        'total_accesses_formatted' => number_format($access_summary->total_accesses),
        'unique_users_formatted' => number_format($access_summary->unique_users),
        'unique_courses_formatted' => number_format($access_summary->unique_courses)
    ],
    'completion_summary' => [
        'total_completions_formatted' => number_format($completion_summary->total_completions)
    ],

    // System info
    'moodle_release' => $CFG->release,
    'totalcourses' => $totalcourses,
    'activeusers' => $activeusers,
    'suspendedusers' => $suspendedusers,
    'backup_max_kept' => $backup_max_kept,

    // Recommendations
    'disk_warning' => ($disk_percent > 70),
    'disk_alert_class' => ($disk_percent > 90) ? 'danger' : 'warning',
    'users_warning' => ($users_percent > 70),
    'users_alert_class' => ($users_percent > 90) ? 'danger' : 'warning',

    // Plugin info
    'coursesize_installed' => $coursesize_installed,
    'coursesize_url' => $CFG->wwwroot . '/report/coursesize/index.php',

    // Course dedication data
    'dedication_available' => $dedication_available,
    'has_dedication_data' => !empty($top_courses_dedication),
    'top_courses_dedication' => $top_courses_dedication,
    'dedication_summary' => $dedication_summary ? [
        'unique_courses' => $dedication_summary->unique_courses,
        'unique_users' => $dedication_summary->unique_users,
        'session_limit_formatted' => $dedication_summary->session_limit_formatted ?? '',
        'period_days' => $dedication_summary->period_days ?? 90
    ] : null
];

// ============================================================
// OUTPUT
// ============================================================

// Load external CSS styles
$PAGE->requires->css('/report/usage_monitor/styles.css');

echo $OUTPUT->header();
echo '<div class="alert alert-info mb-2 text-center small disclaimer-banner">' . get_string('exclusivedisclaimer', 'report_usage_monitor') . '</div>';
echo $OUTPUT->heading(get_string('dashboard_title', 'report_usage_monitor'));

// Render the dashboard template
echo $OUTPUT->render_from_template('report_usage_monitor/dashboard', $templatecontext);

// Chart.js and JavaScript
?>
<!-- Chart.js CDN with integrity check -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
(function() {
    'use strict';

    // Chart configuration
    var chartConfig = {
        doughnut: {
            labels: <?php echo json_encode($doughnutLabels); ?>,
            data: <?php echo json_encode($doughnutData); ?>,
            hasData: <?php echo ($disk_usage_bytes > 0) ? 'true' : 'false'; ?>
        },
        diskHistory: {
            labels: <?php echo json_encode($disk_history_labels); ?>,
            data: <?php echo json_encode($disk_history_data); ?>,
            hasData: <?php echo !empty($disk_history_labels) ? 'true' : 'false'; ?>
        },
        users10days: {
            labels: <?php echo json_encode($last10daysLabels); ?>,
            data: <?php echo json_encode($last10daysData); ?>,
            dataRaw: <?php echo json_encode($last10daysDataRaw); ?>,
            hasData: <?php echo !empty($last10daysLabels) ? 'true' : 'false'; ?>
        },
        accessTrends: {
            labels: <?php echo json_encode($access_trends_labels); ?>,
            data: <?php echo json_encode($access_trends_data); ?>,
            hasData: <?php echo !empty($access_trends_labels) ? 'true' : 'false'; ?>
        },
        completionTrends: {
            labels: <?php echo json_encode($completion_trends_labels); ?>,
            data: <?php echo json_encode($completion_trends_data); ?>,
            hasData: <?php echo !empty($completion_trends_labels) ? 'true' : 'false'; ?>
        }
    };

    // Strings
    var strings = {
        usersQuantity: '<?php echo get_string('usersquantity', 'report_usage_monitor'); ?>',
        percentUsed: '<?php echo get_string('percentage_used', 'report_usage_monitor'); ?>',
        warning70: '<?php echo get_string('warning70', 'report_usage_monitor'); ?>',
        critical90: '<?php echo get_string('critical90', 'report_usage_monitor'); ?>',
        limit100: '<?php echo get_string('limit100', 'report_usage_monitor'); ?>',
        percentThreshold: '<?php echo get_string('percent_of_threshold', 'report_usage_monitor'); ?>',
        totalAccesses: '<?php echo get_string('total_accesses', 'report_usage_monitor'); ?>',
        completions: '<?php echo get_string('completions', 'report_usage_monitor'); ?>'
    };

    // Chart instances storage
    var charts = {};

    // Wait for Chart.js to load
    function waitForChartJS(callback, maxAttempts) {
        maxAttempts = maxAttempts || 50;
        var attempts = 0;

        function check() {
            attempts++;
            if (typeof Chart !== 'undefined') {
                callback();
            } else if (attempts < maxAttempts) {
                setTimeout(check, 100);
            } else {
                console.error('Chart.js failed to load');
            }
        }
        check();
    }

    // Initialize a chart safely with fixed dimensions
    function initChart(canvasId, config) {
        var canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        var container = canvas.parentElement;
        if (!container) return null;

        // Destroy existing chart if any
        if (charts[canvasId]) {
            charts[canvasId].destroy();
            charts[canvasId] = null;
        }

        // Get container dimensions
        var containerHeight = container.offsetHeight || 250;
        var containerWidth = container.offsetWidth || 400;

        // Set canvas dimensions explicitly
        canvas.style.width = containerWidth + 'px';
        canvas.style.height = containerHeight + 'px';
        canvas.width = containerWidth;
        canvas.height = containerHeight;

        // Ensure responsive is true but aspect ratio is false
        if (!config.options) config.options = {};
        config.options.responsive = true;
        config.options.maintainAspectRatio = false;
        config.options.animation = { duration: 300 };

        // Add resize delay to prevent loops
        if (!config.options.resizeDelay) {
            config.options.resizeDelay = 100;
        }

        try {
            charts[canvasId] = new Chart(canvas, config);
            return charts[canvasId];
        } catch (e) {
            console.error('Error creating chart ' + canvasId + ':', e);
            return null;
        }
    }

    // Create threshold lines dataset
    function createThresholdLines(count) {
        return [
            {
                label: strings.warning70,
                data: Array(count).fill(70),
                borderColor: '#ffc107',
                borderDash: [5, 5],
                pointRadius: 0,
                fill: false
            },
            {
                label: strings.critical90,
                data: Array(count).fill(90),
                borderColor: '#dc3545',
                borderDash: [5, 5],
                pointRadius: 0,
                fill: false
            },
            {
                label: strings.limit100,
                data: Array(count).fill(100),
                borderColor: '#6c757d',
                borderDash: [2, 2],
                pointRadius: 0,
                fill: false
            }
        ];
    }

    // Initialize all charts
    function initCharts() {
        // Doughnut chart
        if (chartConfig.doughnut.hasData) {
            initChart('chart-doughnut', {
                type: 'doughnut',
                data: {
                    labels: chartConfig.doughnut.labels,
                    datasets: [{
                        data: chartConfig.doughnut.data,
                        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dee2e6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ctx.label + ': ' + ctx.parsed + ' GB';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Disk history chart
        if (chartConfig.diskHistory.hasData) {
            var diskDatasets = [{
                label: strings.percentUsed,
                data: chartConfig.diskHistory.data,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true,
                tension: 0.2
            }].concat(createThresholdLines(chartConfig.diskHistory.labels.length));

            initChart('chart-disk-history', {
                type: 'line',
                data: {
                    labels: chartConfig.diskHistory.labels,
                    datasets: diskDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: function(v) { return v + '%'; } }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
        }

        // Users 10 days chart
        if (chartConfig.users10days.hasData) {
            var usersDatasets = [{
                label: strings.usersQuantity,
                data: chartConfig.users10days.data,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true,
                tension: 0.2
            }].concat(createThresholdLines(chartConfig.users10days.labels.length));

            initChart('chart-users-10days', {
                type: 'line',
                data: {
                    labels: chartConfig.users10days.labels,
                    datasets: usersDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: function(v) { return v + '%'; } },
                            title: { display: true, text: strings.percentThreshold }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    if (ctx.datasetIndex === 0) {
                                        return chartConfig.users10days.dataRaw[ctx.dataIndex] + ' (' + ctx.parsed.y + '%)';
                                    }
                                    return ctx.dataset.label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Access trends chart
        if (chartConfig.accessTrends.hasData) {
            initChart('chart-access-trends', {
                type: 'line',
                data: {
                    labels: chartConfig.accessTrends.labels,
                    datasets: [{
                        label: strings.totalAccesses,
                        data: chartConfig.accessTrends.data,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        fill: true,
                        tension: 0.2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // Completion trends chart
        if (chartConfig.completionTrends.hasData) {
            initChart('chart-completion-trends', {
                type: 'line',
                data: {
                    labels: chartConfig.completionTrends.labels,
                    datasets: [{
                        label: strings.completions,
                        data: chartConfig.completionTrends.data,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        fill: true,
                        tension: 0.2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            waitForChartJS(initCharts);
        });
    } else {
        waitForChartJS(initCharts);
    }

    // Debounce function
    function debounce(func, wait) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }

    // Handle tab changes to resize charts (with debounce)
    var handleTabResize = debounce(function() {
        Object.keys(charts).forEach(function(key) {
            if (charts[key] && charts[key].canvas) {
                var container = charts[key].canvas.parentElement;
                if (container && container.offsetWidth > 0) {
                    charts[key].resize();
                }
            }
        });
    }, 150);

    // Listen for both Bootstrap 4 and 5 tab events
    document.addEventListener('shown.bs.tab', handleTabResize);
    document.addEventListener('shown.tab', handleTabResize);

    // Also handle window resize with debounce
    window.addEventListener('resize', handleTabResize);

    // Manual tab handling fallback (for when Bootstrap tabs don't work)
    function initManualTabs() {
        var tabLinks = document.querySelectorAll('#dashboardTabs .nav-link');
        var tabPanes = document.querySelectorAll('#dashboardTabContent .tab-pane');

        tabLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                // Get target pane ID
                var targetId = this.getAttribute('href');
                if (!targetId) return;

                // Remove active from all tabs
                tabLinks.forEach(function(l) {
                    l.classList.remove('active');
                    l.setAttribute('aria-selected', 'false');
                });

                // Remove active/show from all panes
                tabPanes.forEach(function(p) {
                    p.classList.remove('show', 'active');
                });

                // Activate clicked tab
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');

                // Show target pane
                var targetPane = document.querySelector(targetId);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }

                // Trigger resize for charts after a short delay
                setTimeout(handleTabResize, 100);
            });
        });
    }

    // Initialize manual tabs after DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initManualTabs);
    } else {
        initManualTabs();
    }

    // Initialize tooltips (Bootstrap 4 and 5 compatible)
    function initTooltips() {
        // Try Bootstrap 5 first
        if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(el) {
                new bootstrap.Tooltip(el, { placement: 'top', html: false });
            });
        }
        // Try Bootstrap 4 / jQuery
        else if (typeof jQuery !== 'undefined' && typeof jQuery.fn.tooltip !== 'undefined') {
            jQuery('[data-toggle="tooltip"]').tooltip({ placement: 'top', html: false });
        }
        // Fallback: Use native title attributes (already set)
    }

    // Initialize tooltips after DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTooltips);
    } else {
        setTimeout(initTooltips, 100);
    }
})();
</script>

<?php
echo $OUTPUT->footer();

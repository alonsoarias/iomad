// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Vacancy filter module with AJAX filtering.
 *
 * @module     local_jobboard/vacancy_filter
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification', 'core/templates', 'core/str'],
    function($, Ajax, Notification, Templates, Str) {

    /**
     * Vacancy Filter class.
     *
     * @param {string} selector Container selector
     */
    var VacancyFilter = function(selector) {
        this.container = $(selector);
        this.filterForm = this.container.find('[data-region="filter-form"]');
        this.vacancyCards = this.container.find('[data-region="vacancy-cards"]');
        this.debounceTimer = null;
        this.loadingString = 'Loading...'; // Default fallback.

        this.init();
    };

    /**
     * Initialize the filter.
     */
    VacancyFilter.prototype.init = function() {
        var self = this;
        // Pre-load the loading string for use in spinner.
        Str.get_string('loading', 'local_jobboard').then(function(str) {
            self.loadingString = str;
        }).catch(function() {
            // Keep default fallback.
        });
        this.registerEventListeners();
    };

    /**
     * Register event listeners.
     */
    VacancyFilter.prototype.registerEventListeners = function() {
        var self = this;

        // Filter form changes.
        this.filterForm.on('change', 'select', function() {
            self.applyFilters();
        });

        // Search input with debounce.
        this.filterForm.on('input', 'input[type="text"], input[type="search"]', function() {
            clearTimeout(self.debounceTimer);
            self.debounceTimer = setTimeout(function() {
                self.applyFilters();
            }, 300);
        });

        // Clear filters button.
        this.filterForm.on('click', '.btn-clear-filters', function(e) {
            e.preventDefault();
            self.clearFilters();
        });

        // Pagination links.
        this.container.on('click', '.pagination a', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            if (page) {
                self.applyFilters(page);
            }
        });
    };

    /**
     * Apply current filters.
     *
     * @param {number} page Page number (optional)
     */
    VacancyFilter.prototype.applyFilters = function(page) {
        var self = this;
        var filters = this.getFilterValues();

        if (page) {
            filters.page = page;
        }

        this.showLoading();

        Ajax.call([{
            methodname: 'local_jobboard_filter_vacancies',
            args: filters
        }])[0].then(function(response) {
            return self.renderResults(response);
        }).catch(function(error) {
            self.hideLoading();
            Notification.exception(error);
        });
    };

    /**
     * Get current filter values.
     *
     * @return {object} Filter values
     */
    VacancyFilter.prototype.getFilterValues = function() {
        var filters = {};

        this.filterForm.find('input, select').each(function() {
            var input = $(this);
            var name = input.attr('name');
            var value = input.val();

            if (name && value) {
                filters[name] = value;
            }
        });

        return filters;
    };

    /**
     * Clear all filters.
     */
    VacancyFilter.prototype.clearFilters = function() {
        this.filterForm.find('input[type="text"], input[type="search"]').val('');
        this.filterForm.find('select').prop('selectedIndex', 0);
        this.applyFilters();
    };

    /**
     * Show loading state.
     */
    VacancyFilter.prototype.showLoading = function() {
        this.vacancyCards.addClass('loading');
        if (!this.vacancyCards.find('.loading-overlay').length) {
            this.vacancyCards.append(
                '<div class="loading-overlay">' +
                '<div class="spinner-border text-primary" role="status">' +
                '<span class="sr-only">' + this.loadingString + '</span>' +
                '</div></div>'
            );
        }
    };

    /**
     * Hide loading state.
     */
    VacancyFilter.prototype.hideLoading = function() {
        this.vacancyCards.removeClass('loading');
        this.vacancyCards.find('.loading-overlay').remove();
    };

    /**
     * Render filter results.
     *
     * @param {object} response Server response
     * @return {Promise}
     */
    VacancyFilter.prototype.renderResults = function(response) {
        var self = this;

        if (response.html) {
            this.vacancyCards.html(response.html);
            this.hideLoading();
            return $.Deferred().resolve().promise();
        }

        if (response.vacancies && response.vacancies.length > 0) {
            var promises = response.vacancies.map(function(vacancy) {
                return Templates.render('local_jobboard/vacancy_card', vacancy);
            });

            return $.when.apply($, promises).then(function() {
                var html = Array.prototype.slice.call(arguments).map(function(result) {
                    return '<div class="col-lg-6 col-xl-4">' + result + '</div>';
                }).join('');

                self.vacancyCards.html(html);
                self.hideLoading();

                // Update pagination if provided.
                if (response.pagination) {
                    self.container.find('.pagination').replaceWith(response.pagination);
                }
            });
        } else {
            return Str.get_string('noresults', 'local_jobboard').then(function(str) {
                self.vacancyCards.html(
                    '<div class="col-12"><div class="alert alert-info">' + str + '</div></div>'
                );
                self.hideLoading();
                return str;
            });
        }
    };

    return {
        /**
         * Initialize vacancy filter.
         *
         * @param {string} selector Container selector
         */
        init: function(selector) {
            return new VacancyFilter(selector);
        }
    };
});

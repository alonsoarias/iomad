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
 * Public filters module for vacancy/convocatoria list filtering.
 *
 * @module     local_jobboard/public_filters
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/str', 'core/notification'], function(Str, Notification) {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        formSelector: null,
        resultsSelector: null,
        searchTimeout: null,
        initialized: false
    };

    /**
     * Debounce delay in milliseconds.
     * @type {number}
     */
    var DEBOUNCE_DELAY = 300;

    /**
     * Build query string from form data.
     *
     * @param {HTMLFormElement} form The filter form.
     * @return {string} Query string.
     */
    var buildQueryString = function(form) {
        var formData = new FormData(form);
        var params = new URLSearchParams();

        formData.forEach(function(value, key) {
            if (value !== '' && value !== '0') {
                params.append(key, value);
            }
        });

        return params.toString();
    };

    /**
     * Update the URL without reloading.
     *
     * @param {string} queryString The query string.
     */
    var updateUrl = function(queryString) {
        var url = window.location.pathname;
        if (queryString) {
            url += '?' + queryString;
        }
        window.history.pushState({}, '', url);
    };

    /**
     * Load filtered results via AJAX.
     *
     * @param {HTMLFormElement} form The filter form.
     */
    var loadResults = function(form) {
        var resultsContainer = document.querySelector(state.resultsSelector);
        if (!resultsContainer) {
            // Fall back to form submission if no results container.
            form.submit();
            return;
        }

        var queryString = buildQueryString(form);
        var url = form.action + (queryString ? '?' + queryString + '&ajax=1' : '?ajax=1');

        // Show loading state.
        resultsContainer.classList.add('jb-loading');

        fetch(url)
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(function(html) {
                resultsContainer.innerHTML = html;
                resultsContainer.classList.remove('jb-loading');
                updateUrl(queryString);

                // Dispatch event for other modules.
                var event = new CustomEvent('jobboard:filtersapplied', {
                    detail: {
                        queryString: queryString,
                        form: form
                    }
                });
                document.dispatchEvent(event);
            })
            .catch(function(error) {
                resultsContainer.classList.remove('jb-loading');
                Notification.exception(error);
            });
    };

    /**
     * Handle filter form change.
     *
     * @param {Event} e The change event.
     */
    var onFilterChange = function(e) {
        var form = e.target.closest(state.formSelector);
        if (!form) {
            return;
        }

        // Debounce for text inputs.
        if (e.target.type === 'text' || e.target.type === 'search') {
            clearTimeout(state.searchTimeout);
            state.searchTimeout = setTimeout(function() {
                loadResults(form);
            }, DEBOUNCE_DELAY);
        } else {
            // Immediate for selects and checkboxes.
            loadResults(form);
        }
    };

    /**
     * Handle filter form submission.
     *
     * @param {Event} e The submit event.
     */
    var onFilterSubmit = function(e) {
        var form = e.target.closest(state.formSelector);
        if (!form || !document.querySelector(state.resultsSelector)) {
            return; // Let form submit normally.
        }

        e.preventDefault();
        loadResults(form);
    };

    /**
     * Reset the filter form.
     *
     * @param {HTMLFormElement} form The filter form.
     */
    var resetFilters = function(form) {
        form.reset();
        loadResults(form);
    };

    /**
     * Handle reset button click.
     *
     * @param {Event} e The click event.
     */
    var onResetClick = function(e) {
        var button = e.target.closest('[data-action="reset-filters"]');
        if (!button) {
            return;
        }

        var form = button.closest(state.formSelector);
        if (form) {
            e.preventDefault();
            resetFilters(form);
        }
    };

    /**
     * Initialize the public filters module.
     *
     * @param {Object} config Configuration object.
     * @param {string} [config.formSelector='.jb-filter-form'] CSS selector for filter form.
     * @param {string} [config.resultsSelector='[data-region="vacancy-cards"]'] CSS selector for results.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        config = config || {};
        state.formSelector = config.formSelector || '.jb-filter-form';
        state.resultsSelector = config.resultsSelector || '[data-region="vacancy-cards"]';

        // Event delegation.
        document.body.addEventListener('change', onFilterChange);
        document.body.addEventListener('input', onFilterChange);
        document.body.addEventListener('submit', onFilterSubmit);
        document.body.addEventListener('click', onResetClick);

        state.initialized = true;
    };

    return {
        init: init,
        resetFilters: resetFilters
    };
});

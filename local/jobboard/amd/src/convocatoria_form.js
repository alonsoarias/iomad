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
 * Convocatoria form module for dynamic company/department selection.
 *
 * @module     local_jobboard/convocatoria_form
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/notification', 'core/str'], function(Notification, Str) {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        companySelect: null,
        departmentSelect: null,
        ajaxEndpoint: null,
        preselectedCompany: null,
        preselectedDepartment: null,
        initialized: false
    };

    /**
     * Show loading indicator on select.
     *
     * @param {HTMLSelectElement} select The select element.
     */
    var showLoading = function(select) {
        if (!select) {
            return;
        }
        select.disabled = true;
        select.classList.add('jb-loading');
    };

    /**
     * Hide loading indicator on select.
     *
     * @param {HTMLSelectElement} select The select element.
     */
    var hideLoading = function(select) {
        if (!select) {
            return;
        }
        select.disabled = false;
        select.classList.remove('jb-loading');
    };

    /**
     * Load departments for a company.
     *
     * @param {number} companyId The company ID.
     * @return {Promise} Promise resolving when complete.
     */
    var loadDepartments = function(companyId) {
        if (!companyId) {
            updateDepartmentOptions([]);
            return Promise.resolve();
        }

        var url = state.ajaxEndpoint + '/get_departments.php?companyid=' + encodeURIComponent(companyId);

        showLoading(state.departmentSelect);

        return fetch(url)
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(function(departments) {
                updateDepartmentOptions(departments);
                hideLoading(state.departmentSelect);
            })
            .catch(function(error) {
                hideLoading(state.departmentSelect);
                Notification.exception(error);
            });
    };

    /**
     * Update department select options.
     *
     * @param {Array} departments Array of department objects.
     */
    var updateDepartmentOptions = function(departments) {
        if (!state.departmentSelect) {
            return;
        }

        // Clear existing options except first (placeholder).
        while (state.departmentSelect.options.length > 1) {
            state.departmentSelect.remove(1);
        }

        // Add new options.
        departments.forEach(function(dept) {
            var option = document.createElement('option');
            option.value = dept.id;
            option.textContent = dept.name;

            // Check if this should be preselected.
            if (state.preselectedDepartment && dept.id == state.preselectedDepartment) {
                option.selected = true;
            }

            state.departmentSelect.appendChild(option);
        });

        // Enable/disable based on options.
        state.departmentSelect.disabled = departments.length === 0;

        // Clear preselected after first use.
        state.preselectedDepartment = null;
    };

    /**
     * Handle company select change.
     *
     * @param {Event} e The change event.
     */
    var onCompanyChange = function(e) {
        var companyId = e.target.value;

        // Reset department select.
        if (state.departmentSelect) {
            state.departmentSelect.value = '';
        }

        loadDepartments(companyId);
    };

    /**
     * Setup date field constraints.
     */
    var setupDateConstraints = function() {
        var startDateField = document.querySelector('#id_startdate, [name="startdate"]');
        var endDateField = document.querySelector('#id_enddate, [name="enddate"]');

        if (startDateField && endDateField) {
            // Update end date minimum when start date changes.
            startDateField.addEventListener('change', function() {
                if (startDateField.value) {
                    endDateField.min = startDateField.value;
                }
            });

            // Validate end date is after start date.
            endDateField.addEventListener('change', function() {
                if (startDateField.value && endDateField.value) {
                    if (new Date(endDateField.value) < new Date(startDateField.value)) {
                        Str.get_string('enddatebeforestart', 'local_jobboard').then(function(msg) {
                            Notification.addNotification({
                                message: msg,
                                type: 'error'
                            });
                        });
                        endDateField.value = '';
                    }
                }
            });
        }
    };

    /**
     * Initialize the convocatoria form module.
     *
     * @param {Object} config Configuration object.
     * @param {string} [config.companySelector='#id_companyid'] CSS selector for company select.
     * @param {string} [config.departmentSelector='#id_departmentid'] CSS selector for department select.
     * @param {string} [config.ajaxEndpoint] Base URL for AJAX endpoints.
     * @param {number} [config.preselectedCompany] Pre-selected company ID.
     * @param {number} [config.preselectedDepartment] Pre-selected department ID.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        config = config || {};
        state.ajaxEndpoint = config.ajaxEndpoint || M.cfg.wwwroot + '/local/jobboard/ajax';
        state.preselectedCompany = config.preselectedCompany;
        state.preselectedDepartment = config.preselectedDepartment;

        state.companySelect = document.querySelector(config.companySelector || '#id_companyid');
        state.departmentSelect = document.querySelector(config.departmentSelector || '#id_departmentid');

        if (state.companySelect) {
            state.companySelect.addEventListener('change', onCompanyChange);

            // If company is already selected, load departments.
            if (state.companySelect.value || state.preselectedCompany) {
                var companyId = state.companySelect.value || state.preselectedCompany;
                loadDepartments(companyId);
            }
        }

        setupDateConstraints();

        state.initialized = true;
    };

    return {
        init: init,
        loadDepartments: loadDepartments
    };
});

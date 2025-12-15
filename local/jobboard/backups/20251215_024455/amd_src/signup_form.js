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
 * Signup form module for handling IOMAD company/department selection.
 *
 * @module     local_jobboard/signup_form
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/ajax', 'core/notification', 'core/str'], function(Ajax, Notification, Str) {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        companySelect: null,
        departmentSelect: null,
        ajaxEndpoint: null,
        initialized: false
    };

    /**
     * Load departments for a given company.
     *
     * @param {number} companyId The company ID.
     * @return {Promise} Promise resolving with department data.
     */
    var loadDepartments = function(companyId) {
        if (!companyId || !state.ajaxEndpoint) {
            return Promise.resolve([]);
        }

        var url = state.ajaxEndpoint + '/get_departments.php?companyid=' + encodeURIComponent(companyId);

        return fetch(url)
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .catch(function(error) {
                Notification.exception(error);
                return [];
            });
    };

    /**
     * Update the department select options.
     *
     * @param {Array} departments Array of department objects.
     */
    var updateDepartmentOptions = function(departments) {
        if (!state.departmentSelect) {
            return;
        }

        // Clear existing options except the first (placeholder).
        while (state.departmentSelect.options.length > 1) {
            state.departmentSelect.remove(1);
        }

        // Add new options.
        departments.forEach(function(dept) {
            var option = document.createElement('option');
            option.value = dept.id;
            option.textContent = dept.name;
            state.departmentSelect.appendChild(option);
        });

        // Enable/disable based on available options.
        state.departmentSelect.disabled = departments.length === 0;
    };

    /**
     * Handle company selection change.
     *
     * @param {Event} e The change event.
     */
    var onCompanyChange = function(e) {
        var companyId = e.target.value;

        // Disable department while loading.
        if (state.departmentSelect) {
            state.departmentSelect.disabled = true;
        }

        loadDepartments(companyId).then(function(departments) {
            updateDepartmentOptions(departments);
        });
    };

    /**
     * Initialize the signup form module.
     *
     * @param {Object} config Configuration object.
     * @param {string} config.companySelector CSS selector for company select.
     * @param {string} config.departmentSelector CSS selector for department select.
     * @param {string} config.ajaxEndpoint Base URL for AJAX endpoints.
     * @param {number} [config.preselectedCompany] Pre-selected company ID.
     * @param {number} [config.preselectedDepartment] Pre-selected department ID.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        config = config || {};
        state.ajaxEndpoint = config.ajaxEndpoint || M.cfg.wwwroot + '/local/jobboard/ajax';

        state.companySelect = document.querySelector(config.companySelector || '#id_companyid');
        state.departmentSelect = document.querySelector(config.departmentSelector || '#id_departmentid');

        if (state.companySelect) {
            state.companySelect.addEventListener('change', onCompanyChange);

            // If there's a preselected company, load its departments.
            if (config.preselectedCompany) {
                loadDepartments(config.preselectedCompany).then(function(departments) {
                    updateDepartmentOptions(departments);

                    // Select preselected department if provided.
                    if (config.preselectedDepartment && state.departmentSelect) {
                        state.departmentSelect.value = config.preselectedDepartment;
                    }
                });
            }
        }

        state.initialized = true;
    };

    return {
        init: init
    };
});

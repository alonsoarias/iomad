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
 * Vacancy form module for dynamic company/department/convocatoria selection.
 *
 * @module     local_jobboard/vacancy_form
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
        convocatoriaSelect: null,
        ajaxEndpoint: null,
        preselected: {},
        initialized: false
    };

    /**
     * Show loading state on a select element.
     *
     * @param {HTMLSelectElement} select The select element.
     */
    var showLoading = function(select) {
        if (!select) {
            return;
        }
        select.disabled = true;
        select.classList.add('jb-select-loading');
    };

    /**
     * Hide loading state on a select element.
     *
     * @param {HTMLSelectElement} select The select element.
     */
    var hideLoading = function(select) {
        if (!select) {
            return;
        }
        select.disabled = false;
        select.classList.remove('jb-select-loading');
    };

    /**
     * Load data from AJAX endpoint.
     *
     * @param {string} endpoint The endpoint name (e.g., 'get_departments').
     * @param {Object} params Query parameters.
     * @return {Promise} Promise resolving with data array.
     */
    var loadData = function(endpoint, params) {
        var url = state.ajaxEndpoint + '/' + endpoint + '.php';
        var queryString = Object.keys(params)
            .map(function(key) {
                return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
            })
            .join('&');

        if (queryString) {
            url += '?' + queryString;
        }

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
     * Update select options.
     *
     * @param {HTMLSelectElement} select The select element.
     * @param {Array} options Array of option objects with id and name.
     * @param {string} [placeholder] Placeholder text for first option.
     * @param {number} [preselectedId] ID to preselect.
     */
    var updateOptions = function(select, options, placeholder, preselectedId) {
        if (!select) {
            return;
        }

        // Clear existing options except first.
        while (select.options.length > 1) {
            select.remove(1);
        }

        // Update placeholder if needed.
        if (placeholder && select.options.length > 0) {
            select.options[0].textContent = placeholder;
        }

        // Add new options.
        options.forEach(function(item) {
            var option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name || item.shortname;
            if (preselectedId && item.id == preselectedId) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        // Enable/disable based on options.
        select.disabled = options.length === 0;
    };

    /**
     * Load departments for a company.
     *
     * @param {number} companyId The company ID.
     */
    var loadDepartments = function(companyId) {
        if (!companyId || !state.departmentSelect) {
            updateOptions(state.departmentSelect, []);
            return;
        }

        showLoading(state.departmentSelect);

        loadData('get_departments', {companyid: companyId}).then(function(departments) {
            updateOptions(
                state.departmentSelect,
                departments,
                null,
                state.preselected.department
            );
            hideLoading(state.departmentSelect);

            // Clear preselected after first use.
            delete state.preselected.department;
        });
    };

    /**
     * Load convocatorias for a company.
     *
     * @param {number} companyId The company ID.
     */
    var loadConvocatorias = function(companyId) {
        if (!companyId || !state.convocatoriaSelect) {
            return;
        }

        showLoading(state.convocatoriaSelect);

        loadData('get_convocatorias', {companyid: companyId}).then(function(convocatorias) {
            updateOptions(
                state.convocatoriaSelect,
                convocatorias,
                null,
                state.preselected.convocatoria
            );
            hideLoading(state.convocatoriaSelect);

            // Clear preselected after first use.
            delete state.preselected.convocatoria;
        });
    };

    /**
     * Handle company change.
     *
     * @param {Event} e The change event.
     */
    var onCompanyChange = function(e) {
        var companyId = e.target.value;

        // Reset dependent selects.
        if (state.departmentSelect) {
            state.departmentSelect.value = '';
        }
        if (state.convocatoriaSelect) {
            state.convocatoriaSelect.value = '';
        }

        // Load dependent data.
        loadDepartments(companyId);
        loadConvocatorias(companyId);
    };

    /**
     * Initialize the vacancy form module.
     *
     * @param {Object} config Configuration object.
     * @param {string} [config.companySelector='#id_companyid'] CSS selector for company select.
     * @param {string} [config.departmentSelector='#id_departmentid'] CSS selector for department select.
     * @param {string} [config.convocatoriaSelector='#id_convocatoriaid'] CSS selector for convocatoria select.
     * @param {string} [config.ajaxEndpoint] Base URL for AJAX endpoints.
     * @param {number} [config.preselectedCompany] Pre-selected company ID.
     * @param {number} [config.preselectedDepartment] Pre-selected department ID.
     * @param {number} [config.preselectedConvocatoria] Pre-selected convocatoria ID.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        config = config || {};
        state.ajaxEndpoint = config.ajaxEndpoint || M.cfg.wwwroot + '/local/jobboard/ajax';

        state.companySelect = document.querySelector(config.companySelector || '#id_companyid');
        state.departmentSelect = document.querySelector(config.departmentSelector || '#id_departmentid');
        state.convocatoriaSelect = document.querySelector(config.convocatoriaSelector || '#id_convocatoriaid');

        state.preselected = {
            company: config.preselectedCompany,
            department: config.preselectedDepartment,
            convocatoria: config.preselectedConvocatoria
        };

        if (state.companySelect) {
            state.companySelect.addEventListener('change', onCompanyChange);

            // Load initial data if company is preselected.
            if (config.preselectedCompany && state.companySelect.value) {
                loadDepartments(state.companySelect.value);
                loadConvocatorias(state.companySelect.value);
            }
        }

        state.initialized = true;
    };

    return {
        init: init,
        loadDepartments: loadDepartments,
        loadConvocatorias: loadConvocatorias
    };
});

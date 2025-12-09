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
 * AMD module for public filters with dynamic company and department loading.
 *
 * This module handles AJAX-based filtering for public pages using the
 * company_loader and department_loader modules for consistent behavior.
 *
 * @module     local_jobboard/public_filters
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'local_jobboard/department_loader',
    'local_jobboard/company_loader'
], function($, DepartmentLoader, CompanyLoader) {
    'use strict';

    /**
     * Load departments for a given company (wrapper for backward compatibility).
     *
     * @param {jQuery} departmentSelect The department select element.
     * @param {number} companyId The company ID.
     * @param {number} preselect Optional department ID to preselect.
     * @param {string} allLabel Label for "All" option.
     */
    var loadDepartments = function(departmentSelect, companyId, preselect, allLabel) {
        DepartmentLoader.loadDepartments(departmentSelect, companyId, preselect, allLabel);
    };

    /**
     * Initialize the public filters handlers.
     *
     * @param {Object} options Configuration options.
     * @param {string} options.companySelector Selector for company element.
     * @param {string} options.departmentSelector Selector for department element.
     * @param {number} options.preselect Optional department ID to preselect.
     * @param {string} options.allLabel Label for "All" option.
     * @param {boolean} options.loadCompaniesAjax Whether to load companies via AJAX.
     */
    var init = function(options) {
        options = options || {};

        var companySelector = options.companySelector || '#filter-companyid';
        var departmentSelector = options.departmentSelector || '#filter-departmentid';
        var allLabel = options.allLabel || 'Todas las modalidades';

        var companySelect = $(companySelector);
        var departmentSelect = $(departmentSelector);

        // Exit if elements not found.
        if (!companySelect.length || !departmentSelect.length) {
            // eslint-disable-next-line no-console
            console.log('JobBoard public_filters: Company or department selects not found');
            return;
        }

        // eslint-disable-next-line no-console
        console.log('JobBoard public_filters: Initialized');

        // Use company_loader for AJAX loading if enabled.
        if (options.loadCompaniesAjax) {
            CompanyLoader.initFilter({
                companySelector: companySelector,
                departmentSelector: departmentSelector,
                departmentPreselect: options.preselect,
                allDepartmentsLabel: allLabel
            });
        } else {
            // Handle company change to load departments.
            companySelect.on('change', function() {
                var companyId = $(this).val();
                loadDepartments(departmentSelect, companyId, null, allLabel);
            });

            // Load departments if company is pre-selected.
            var initialCompany = companySelect.val();
            if (initialCompany && initialCompany !== '0' && initialCompany !== 0) {
                loadDepartments(departmentSelect, initialCompany, options.preselect, allLabel);
            }
        }
    };

    return {
        init: init,
        loadDepartments: loadDepartments
    };
});

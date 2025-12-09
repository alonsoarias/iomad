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
 * AMD module for public filters with dynamic department loading.
 *
 * @module     local_jobboard/public_filters
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    'use strict';

    /**
     * Load departments for a given company.
     *
     * @param {jQuery} departmentSelect The department select element.
     * @param {number} companyId The company ID.
     * @param {number} preselect Optional department ID to preselect.
     * @param {string} allLabel Label for "All" option.
     */
    var loadDepartments = function(departmentSelect, companyId, preselect, allLabel) {
        // Clear current options.
        departmentSelect.empty();

        // Add "All" option.
        departmentSelect.append($('<option>', {
            value: 0,
            text: allLabel || 'Todas las modalidades'
        }));

        if (!companyId || companyId === '0' || companyId === 0) {
            return;
        }

        // Show loading state.
        departmentSelect.prop('disabled', true);

        // Fetch departments via AJAX endpoint.
        $.ajax({
            url: M.cfg.wwwroot + '/local/jobboard/ajax/get_departments.php',
            method: 'GET',
            data: {
                companyid: parseInt(companyId, 10)
            },
            dataType: 'json'
        }).done(function(response) {
            departmentSelect.empty();
            departmentSelect.prop('disabled', false);

            // Add "All" option.
            departmentSelect.append($('<option>', {
                value: 0,
                text: allLabel || 'Todas las modalidades'
            }));

            // Add department options.
            if (response.success && response.departments && response.departments.length > 0) {
                $.each(response.departments, function(index, dept) {
                    var option = $('<option>', {
                        value: dept.id,
                        text: dept.name
                    });
                    // Preselect if specified.
                    if (preselect && parseInt(preselect, 10) === parseInt(dept.id, 10)) {
                        option.prop('selected', true);
                    }
                    departmentSelect.append(option);
                });
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            departmentSelect.empty();
            departmentSelect.prop('disabled', false);
            departmentSelect.append($('<option>', {
                value: 0,
                text: allLabel || 'Todas las modalidades'
            }));
            // eslint-disable-next-line no-console
            console.error('Error loading departments:', textStatus, errorThrown);
        });
    };

    /**
     * Initialize the public filters handlers.
     *
     * @param {Object} options Configuration options.
     * @param {string} options.companySelector Selector for company element.
     * @param {string} options.departmentSelector Selector for department element.
     * @param {number} options.preselect Optional department ID to preselect.
     * @param {string} options.allLabel Label for "All" option.
     */
    var init = function(options) {
        options = options || {};

        var companySelect = $(options.companySelector || '#filter-companyid');
        var departmentSelect = $(options.departmentSelector || '#filter-departmentid');
        var allLabel = options.allLabel || 'Todas las modalidades';

        // Exit if elements not found.
        if (!companySelect.length || !departmentSelect.length) {
            // eslint-disable-next-line no-console
            console.log('JobBoard public_filters: Company or department selects not found');
            return;
        }

        // eslint-disable-next-line no-console
        console.log('JobBoard public_filters: Initialized');

        // Handle company change.
        companySelect.on('change', function() {
            var companyId = $(this).val();
            loadDepartments(departmentSelect, companyId, null, allLabel);
        });

        // Load departments if company is pre-selected.
        var initialCompany = companySelect.val();
        if (initialCompany && initialCompany !== '0' && initialCompany !== 0) {
            loadDepartments(departmentSelect, initialCompany, options.preselect, allLabel);
        }
    };

    return {
        init: init,
        loadDepartments: loadDepartments
    };
});

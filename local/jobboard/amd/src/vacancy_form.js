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
 * Vacancy form AMD module for local_jobboard.
 *
 * Handles dynamic loading of departments based on company selection.
 *
 * @module     local_jobboard/vacancy_form
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification', 'core/str'], function($, Ajax, Notification, Str) {
    'use strict';

    /**
     * Initialize the vacancy form handlers.
     */
    var init = function() {
        var companySelect = $('#id_companyid');
        var departmentSelect = $('#id_departmentid');

        if (!companySelect.length || !departmentSelect.length) {
            return;
        }

        // Store the current department ID if editing.
        var currentDepartmentId = departmentSelect.val();

        // Handle company change.
        companySelect.on('change', function() {
            var companyId = $(this).val();

            // Clear and disable department select while loading.
            departmentSelect.prop('disabled', true);

            if (!companyId || companyId == '0') {
                // No company selected, clear departments.
                Str.get_string('selectdepartment', 'local_jobboard').then(function(str) {
                    departmentSelect.empty().append($('<option>', {
                        value: 0,
                        text: str
                    }));
                    departmentSelect.prop('disabled', false);
                }).catch(Notification.exception);
                return;
            }

            // Fetch departments for the selected company.
            Ajax.call([{
                methodname: 'local_jobboard_get_departments',
                args: {
                    companyid: parseInt(companyId, 10)
                }
            }])[0].then(function(departments) {
                return Str.get_string('selectdepartment', 'local_jobboard').then(function(str) {
                    departmentSelect.empty();
                    departmentSelect.append($('<option>', {
                        value: 0,
                        text: str
                    }));

                    if (departments && departments.length > 0) {
                        departments.forEach(function(dept) {
                            departmentSelect.append($('<option>', {
                                value: dept.id,
                                text: dept.name
                            }));
                        });
                    }

                    departmentSelect.prop('disabled', false);
                });
            }).catch(function(error) {
                Notification.exception(error);
                departmentSelect.prop('disabled', false);
            });
        });
    };

    return {
        init: init
    };
});

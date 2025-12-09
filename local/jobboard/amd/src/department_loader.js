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
 * AMD module for dynamic department loading based on company selection.
 *
 * This unified module can be used across all forms that have company/department selectors.
 * It uses the AJAX endpoint which works for both authenticated and guest users.
 *
 * @module     local_jobboard/department_loader
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str'], function($, Str) {
    'use strict';

    /**
     * Load departments for a given company.
     *
     * @param {jQuery} departmentSelect The department select element.
     * @param {number} companyId The company ID.
     * @param {number} preselect Optional department ID to preselect.
     * @param {string} allLabel Optional label for "All" option (used in filters).
     */
    var loadDepartments = function(departmentSelect, companyId, preselect, allLabel) {
        // Clear current options.
        departmentSelect.empty();

        // Determine the placeholder text.
        var placeholderKey = allLabel ? null : 'selectdepartment';
        var placeholderText = allLabel || 'Seleccionar modalidad...';

        if (!companyId || companyId === '0' || companyId === 0) {
            // No company selected, add placeholder.
            if (placeholderKey) {
                Str.get_string(placeholderKey, 'local_jobboard').done(function(str) {
                    departmentSelect.append($('<option>', {
                        value: 0,
                        text: str
                    }));
                }).fail(function() {
                    departmentSelect.append($('<option>', {
                        value: 0,
                        text: placeholderText
                    }));
                });
            } else {
                departmentSelect.append($('<option>', {
                    value: 0,
                    text: placeholderText
                }));
            }
            return;
        }

        // Show loading state.
        departmentSelect.prop('disabled', true);
        Str.get_string('loading', 'local_jobboard').done(function(str) {
            departmentSelect.append($('<option>', {
                value: 0,
                text: str + '...'
            }));
        }).fail(function() {
            departmentSelect.append($('<option>', {
                value: 0,
                text: 'Cargando...'
            }));
        });

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

            // Determine placeholder text (use default first, then update async if needed).
            var defaultPlaceholder = allLabel || 'Seleccionar modalidad...';

            // Add placeholder option immediately.
            var placeholderOption = $('<option>', {
                value: 0,
                text: defaultPlaceholder
            });
            departmentSelect.append(placeholderOption);

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

            // Update placeholder text asynchronously (doesn't block options).
            if (!allLabel) {
                Str.get_string('selectdepartment', 'local_jobboard').done(function(str) {
                    placeholderOption.text(str);
                });
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            departmentSelect.empty();
            departmentSelect.prop('disabled', false);

            if (allLabel) {
                departmentSelect.append($('<option>', {
                    value: 0,
                    text: allLabel
                }));
            } else {
                Str.get_string('selectdepartment', 'local_jobboard').done(function(str) {
                    departmentSelect.append($('<option>', {
                        value: 0,
                        text: str
                    }));
                }).fail(function() {
                    departmentSelect.append($('<option>', {
                        value: 0,
                        text: placeholderText
                    }));
                });
            }
            // eslint-disable-next-line no-console
            console.error('Error loading departments:', textStatus, errorThrown);
        });
    };

    /**
     * Find elements using multiple selector strategies.
     *
     * @param {string} baseName The base name of the element (e.g., 'companyid').
     * @param {string} context Optional context selector to narrow search.
     * @return {jQuery} The found element or empty jQuery object.
     */
    var findElement = function(baseName, context) {
        var selectors = [
            'select[name="' + baseName + '"]',
            '#id_' + baseName,
            '#id_' + baseName + '_signup',
            '[id$="_' + baseName + '"]'
        ];

        var $context = context ? $(context) : $(document);

        for (var i = 0; i < selectors.length; i++) {
            var $el = $context.find(selectors[i]);
            if ($el.length) {
                return $el.first();
            }
        }

        // Also try without context.
        for (var j = 0; j < selectors.length; j++) {
            var $elem = $(selectors[j]);
            if ($elem.length) {
                return $elem.first();
            }
        }

        return $();
    };

    /**
     * Initialize department loading for a form.
     *
     * @param {Object} options Configuration options.
     * @param {string} options.companySelector Selector for company element.
     * @param {string} options.departmentSelector Selector for department element.
     * @param {string} options.formContext Optional form context selector.
     * @param {number} options.preselect Optional department ID to preselect.
     */
    var init = function(options) {
        options = options || {};

        var companySelect, departmentSelect;

        // Find company select.
        if (options.companySelector) {
            companySelect = $(options.companySelector);
        } else {
            companySelect = findElement('companyid', options.formContext);
        }

        // Find department select.
        if (options.departmentSelector) {
            departmentSelect = $(options.departmentSelector);
        } else {
            departmentSelect = findElement('departmentid', options.formContext);
        }

        // Exit if elements not found.
        if (!companySelect.length || !departmentSelect.length) {
            // eslint-disable-next-line no-console
            console.log('JobBoard department_loader: Company or department selects not found');
            return;
        }

        // eslint-disable-next-line no-console
        console.log('JobBoard department_loader: Initialized with',
            companySelect.attr('id') || companySelect.attr('name'),
            departmentSelect.attr('id') || departmentSelect.attr('name'));

        // Handle company change.
        companySelect.on('change', function() {
            var companyId = $(this).val();
            loadDepartments(departmentSelect, companyId);
        });

        // Load departments if company is pre-selected.
        var initialCompany = companySelect.val();
        if (initialCompany && initialCompany !== '0' && initialCompany !== 0) {
            loadDepartments(departmentSelect, initialCompany, options.preselect);
        }
    };

    return {
        init: init,
        loadDepartments: loadDepartments,
        findElement: findElement
    };
});

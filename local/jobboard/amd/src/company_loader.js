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
 * AMD module for dynamic company loading with autocomplete/search functionality.
 *
 * This module provides AJAX-based company selection for forms and filters.
 * It supports both autocomplete search and standard select dropdown modes.
 *
 * @module     local_jobboard/company_loader
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'local_jobboard/department_loader'], function($, Str, DepartmentLoader) {
    'use strict';

    /**
     * Load companies via AJAX.
     *
     * @param {string} search Optional search term.
     * @param {number} companyId Optional specific company ID to load.
     * @return {Promise} Promise resolving to array of companies.
     */
    var loadCompanies = function(search, companyId) {
        return new Promise(function(resolve, reject) {
            var data = {};
            if (search) {
                data.search = search;
            }
            if (companyId) {
                data.id = companyId;
            }

            $.ajax({
                url: M.cfg.wwwroot + '/local/jobboard/ajax/get_companies.php',
                method: 'GET',
                data: data,
                dataType: 'json'
            }).done(function(response) {
                if (response.success) {
                    resolve(response.companies);
                } else {
                    reject(response.error || 'Unknown error');
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                reject(textStatus + ': ' + errorThrown);
            });
        });
    };

    /**
     * Populate a select element with companies.
     *
     * @param {jQuery} selectElement The select element to populate.
     * @param {Array} companies Array of company objects.
     * @param {number} preselect Optional company ID to preselect.
     * @param {string} placeholder Placeholder text for empty option.
     */
    var populateSelect = function(selectElement, companies, preselect, placeholder) {
        selectElement.empty();

        // Add placeholder option.
        selectElement.append($('<option>', {
            value: 0,
            text: placeholder || 'Seleccionar...'
        }));

        // Add company options.
        $.each(companies, function(index, company) {
            var option = $('<option>', {
                value: company.id,
                text: company.name
            });
            if (preselect && parseInt(preselect, 10) === parseInt(company.id, 10)) {
                option.prop('selected', true);
            }
            selectElement.append(option);
        });
    };

    /**
     * Initialize company loader for a select element with search capability.
     *
     * @param {Object} options Configuration options.
     * @param {string} options.companySelector Selector for company element.
     * @param {string} options.departmentSelector Optional selector for department element (for cascading).
     * @param {number} options.preselect Optional company ID to preselect.
     * @param {string} options.placeholder Placeholder text.
     * @param {boolean} options.enableSearch Enable search functionality.
     */
    var init = function(options) {
        options = options || {};

        var companySelect = $(options.companySelector);
        if (!companySelect.length) {
            return;
        }

        var placeholder = options.placeholder || '';
        var preselect = options.preselect || 0;

        // Get placeholder from language string if not provided.
        if (!placeholder) {
            Str.get_string('selectcompany', 'local_jobboard').done(function(str) {
                placeholder = str;
            });
        }

        // Load companies initially.
        loadCompanies().then(function(companies) {
            populateSelect(companySelect, companies, preselect, placeholder);

            // If preselect is set and we have a department selector, trigger department load.
            if (preselect && options.departmentSelector) {
                var deptSelect = $(options.departmentSelector);
                if (deptSelect.length) {
                    DepartmentLoader.loadDepartments(deptSelect, preselect, options.departmentPreselect);
                }
            }
        }).catch(function(error) {
            // eslint-disable-next-line no-console
            console.error('Error loading companies:', error);
        });

        // Handle company change to load departments.
        if (options.departmentSelector) {
            companySelect.on('change', function() {
                var companyId = $(this).val();
                var deptSelect = $(options.departmentSelector);
                if (deptSelect.length) {
                    DepartmentLoader.loadDepartments(deptSelect, companyId);
                }
            });
        }

        // Enable search functionality if requested.
        if (options.enableSearch) {
            initSearchableSelect(companySelect, placeholder);
        }
    };

    /**
     * Initialize searchable select with autocomplete.
     *
     * @param {jQuery} selectElement The select element.
     * @param {string} placeholder Placeholder text.
     */
    var initSearchableSelect = function(selectElement, placeholder) {
        // Create search wrapper.
        var wrapper = $('<div>', {'class': 'jb-company-search-wrapper position-relative'});
        var searchInput = $('<input>', {
            'type': 'text',
            'class': 'form-control jb-company-search',
            'placeholder': placeholder,
            'autocomplete': 'off'
        });
        var dropdown = $('<div>', {'class': 'jb-company-dropdown dropdown-menu w-100', 'style': 'display:none;'});

        // Wrap the original select.
        selectElement.wrap(wrapper);
        selectElement.before(searchInput);
        selectElement.after(dropdown);
        selectElement.hide();

        var searchTimeout = null;

        // Handle search input.
        searchInput.on('input', function() {
            var searchTerm = $(this).val();

            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            if (searchTerm.length < 2) {
                dropdown.hide();
                return;
            }

            searchTimeout = setTimeout(function() {
                loadCompanies(searchTerm).then(function(companies) {
                    dropdown.empty();
                    if (companies.length === 0) {
                        Str.get_string('noresults', 'local_jobboard').done(function(str) {
                            dropdown.append($('<div>', {
                                'class': 'dropdown-item text-muted',
                                'text': str
                            }));
                        });
                    } else {
                        $.each(companies, function(index, company) {
                            var item = $('<a>', {
                                'class': 'dropdown-item',
                                'href': '#',
                                'text': company.name,
                                'data-id': company.id
                            });
                            item.on('click', function(e) {
                                e.preventDefault();
                                selectElement.val(company.id).trigger('change');
                                searchInput.val(company.name);
                                dropdown.hide();
                            });
                            dropdown.append(item);
                        });
                    }
                    dropdown.show();
                }).catch(function(error) {
                    // eslint-disable-next-line no-console
                    console.error('Search error:', error);
                });
            }, 300);
        });

        // Hide dropdown on blur.
        searchInput.on('blur', function() {
            setTimeout(function() {
                dropdown.hide();
            }, 200);
        });

        // Show dropdown on focus if has value.
        searchInput.on('focus', function() {
            if ($(this).val().length >= 2) {
                dropdown.show();
            }
        });

        // Set initial value if preselected.
        var selectedOption = selectElement.find('option:selected');
        if (selectedOption.val() && selectedOption.val() !== '0') {
            searchInput.val(selectedOption.text());
        }
    };

    /**
     * Initialize for filter forms (simpler version without search box).
     *
     * @param {Object} options Configuration options.
     */
    var initFilter = function(options) {
        options = options || {};

        var companySelect = $(options.companySelector || '#filter-companyid');
        var departmentSelect = $(options.departmentSelector || '#filter-departmentid');

        if (!companySelect.length) {
            return;
        }

        // Handle company change.
        companySelect.on('change', function() {
            var companyId = $(this).val();
            if (departmentSelect.length) {
                DepartmentLoader.loadDepartments(
                    departmentSelect,
                    companyId,
                    null,
                    options.allDepartmentsLabel
                );
            }
        });

        // Load departments if company is pre-selected.
        var initialCompany = companySelect.val();
        if (initialCompany && initialCompany !== '0') {
            DepartmentLoader.loadDepartments(
                departmentSelect,
                initialCompany,
                options.departmentPreselect,
                options.allDepartmentsLabel
            );
        }
    };

    return {
        init: init,
        initFilter: initFilter,
        loadCompanies: loadCompanies,
        populateSelect: populateSelect
    };
});

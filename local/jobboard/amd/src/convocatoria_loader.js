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
 * AMD module for dynamic convocatoria loading with autocomplete/search functionality.
 *
 * This module provides AJAX-based convocatoria selection for forms and filters.
 *
 * @module     local_jobboard/convocatoria_loader
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str'], function($, Str) {
    'use strict';

    /**
     * Load convocatorias via AJAX.
     *
     * @param {Object} params Search parameters.
     * @param {string} params.search Optional search term.
     * @param {number} params.id Optional specific convocatoria ID to load.
     * @param {number} params.companyid Optional company ID filter.
     * @param {string} params.status Optional status filter.
     * @param {boolean} params.includeall Include all statuses.
     * @return {Promise} Promise resolving to array of convocatorias.
     */
    var loadConvocatorias = function(params) {
        params = params || {};

        return new Promise(function(resolve, reject) {
            $.ajax({
                url: M.cfg.wwwroot + '/local/jobboard/ajax/get_convocatorias.php',
                method: 'GET',
                data: params,
                dataType: 'json'
            }).done(function(response) {
                if (response.success) {
                    resolve(response.convocatorias);
                } else {
                    reject(response.error || 'Unknown error');
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                reject(textStatus + ': ' + errorThrown);
            });
        });
    };

    /**
     * Populate a select element with convocatorias.
     *
     * @param {jQuery} selectElement The select element to populate.
     * @param {Array} convocatorias Array of convocatoria objects.
     * @param {number} preselect Optional convocatoria ID to preselect.
     * @param {string} placeholder Placeholder text for empty option.
     */
    var populateSelect = function(selectElement, convocatorias, preselect, placeholder) {
        selectElement.empty();

        // Add placeholder option.
        selectElement.append($('<option>', {
            value: '',
            text: placeholder || 'Seleccionar convocatoria...'
        }));

        // Add convocatoria options.
        $.each(convocatorias, function(index, conv) {
            var option = $('<option>', {
                value: conv.id,
                text: conv.label || (conv.code + ' - ' + conv.name)
            });
            // Add status as data attribute.
            option.attr('data-status', conv.status);

            if (preselect && parseInt(preselect, 10) === parseInt(conv.id, 10)) {
                option.prop('selected', true);
            }
            selectElement.append(option);
        });
    };

    /**
     * Initialize convocatoria loader for a select element.
     *
     * @param {Object} options Configuration options.
     * @param {string} options.convocatoriaSelector Selector for convocatoria element.
     * @param {string} options.companySelector Optional selector for company element (for filtering).
     * @param {number} options.preselect Optional convocatoria ID to preselect.
     * @param {string} options.placeholder Placeholder text.
     * @param {string} options.status Filter by status (e.g., 'open').
     * @param {boolean} options.includeall Include all statuses.
     * @param {boolean} options.enableSearch Enable search functionality.
     */
    var init = function(options) {
        options = options || {};

        var convSelect = $(options.convocatoriaSelector);
        if (!convSelect.length) {
            return;
        }

        var placeholder = options.placeholder || '';
        var preselect = options.preselect || 0;

        // Get placeholder from language string if not provided.
        if (!placeholder) {
            Str.get_string('selectconvocatoria', 'local_jobboard').done(function(str) {
                placeholder = str;
                loadInitialConvocatorias();
            }).fail(function() {
                placeholder = 'Seleccionar convocatoria...';
                loadInitialConvocatorias();
            });
        } else {
            loadInitialConvocatorias();
        }

        /**
         * Load initial convocatorias.
         */
        function loadInitialConvocatorias() {
            var params = {
                includeall: options.includeall ? 1 : 0
            };

            if (options.status) {
                params.status = options.status;
            }

            // If there's a company selector, get its value.
            if (options.companySelector) {
                var companySelect = $(options.companySelector);
                if (companySelect.length && companySelect.val()) {
                    params.companyid = companySelect.val();
                }
            }

            loadConvocatorias(params).then(function(convocatorias) {
                populateSelect(convSelect, convocatorias, preselect, placeholder);
            }).catch(function(error) {
                // eslint-disable-next-line no-console
                console.error('Error loading convocatorias:', error);
            });
        }

        // If there's a company selector, reload convocatorias when company changes.
        if (options.companySelector) {
            $(options.companySelector).on('change', function() {
                var companyId = $(this).val();
                var params = {
                    includeall: options.includeall ? 1 : 0
                };

                if (options.status) {
                    params.status = options.status;
                }

                if (companyId) {
                    params.companyid = companyId;
                }

                loadConvocatorias(params).then(function(convocatorias) {
                    populateSelect(convSelect, convocatorias, null, placeholder);
                }).catch(function(error) {
                    // eslint-disable-next-line no-console
                    console.error('Error reloading convocatorias:', error);
                });
            });
        }

        // Enable search functionality if requested.
        if (options.enableSearch) {
            initSearchableSelect(convSelect, placeholder, options);
        }
    };

    /**
     * Initialize searchable select with autocomplete.
     *
     * @param {jQuery} selectElement The select element.
     * @param {string} placeholder Placeholder text.
     * @param {Object} options Original options.
     */
    var initSearchableSelect = function(selectElement, placeholder, options) {
        // Create search wrapper.
        var wrapper = $('<div>', {'class': 'jb-convocatoria-search-wrapper position-relative'});
        var searchInput = $('<input>', {
            'type': 'text',
            'class': 'form-control jb-convocatoria-search',
            'placeholder': placeholder,
            'autocomplete': 'off'
        });
        var dropdown = $('<div>', {'class': 'jb-convocatoria-dropdown dropdown-menu w-100', 'style': 'display:none;'});

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
                var params = {
                    search: searchTerm,
                    includeall: options.includeall ? 1 : 0
                };

                if (options.status) {
                    params.status = options.status;
                }

                loadConvocatorias(params).then(function(convocatorias) {
                    dropdown.empty();
                    if (convocatorias.length === 0) {
                        Str.get_string('noresults', 'local_jobboard').done(function(str) {
                            dropdown.append($('<div>', {
                                'class': 'dropdown-item text-muted',
                                'text': str
                            }));
                        });
                    } else {
                        $.each(convocatorias, function(index, conv) {
                            var statusClass = conv.status === 'open' ? 'text-success' : 'text-muted';
                            var item = $('<a>', {
                                'class': 'dropdown-item',
                                'href': '#',
                                'html': '<span class="font-weight-bold">' + conv.code + '</span> - ' +
                                        conv.name + ' <small class="' + statusClass + '">(' + conv.status + ')</small>',
                                'data-id': conv.id
                            });
                            item.on('click', function(e) {
                                e.preventDefault();
                                selectElement.val(conv.id).trigger('change');
                                searchInput.val(conv.code + ' - ' + conv.name);
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
        if (selectedOption.val() && selectedOption.val() !== '') {
            searchInput.val(selectedOption.text());
        }
    };

    /**
     * Initialize for filter forms.
     *
     * @param {Object} options Configuration options.
     */
    var initFilter = function(options) {
        options = options || {};

        var convSelect = $(options.convocatoriaSelector || '#filter-convocatoria');

        if (!convSelect.length) {
            return;
        }

        var placeholder = options.allLabel || 'Todas las convocatorias';

        // Load convocatorias.
        var params = {
            includeall: options.includeall ? 1 : 0
        };

        if (options.status) {
            params.status = options.status;
        }

        loadConvocatorias(params).then(function(convocatorias) {
            convSelect.empty();

            // Add "All" option.
            convSelect.append($('<option>', {
                value: 0,
                text: placeholder
            }));

            // Add convocatoria options.
            $.each(convocatorias, function(index, conv) {
                var option = $('<option>', {
                    value: conv.id,
                    text: conv.label || (conv.code + ' - ' + conv.name)
                });
                if (options.preselect && parseInt(options.preselect, 10) === parseInt(conv.id, 10)) {
                    option.prop('selected', true);
                }
                convSelect.append(option);
            });
        }).catch(function(error) {
            // eslint-disable-next-line no-console
            console.error('Error loading convocatorias for filter:', error);
        });
    };

    return {
        init: init,
        initFilter: initFilter,
        loadConvocatorias: loadConvocatorias,
        populateSelect: populateSelect
    };
});

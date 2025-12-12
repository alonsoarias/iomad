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
 * Bulk actions module for checkbox selection and batch operations.
 *
 * @module     local_jobboard/bulk_actions
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/modal_factory', 'core/modal_events', 'core/str', 'core/notification'],
    function(ModalFactory, ModalEvents, Str, Notification) {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        checkboxSelector: null,
        selectAllSelector: null,
        toolbarSelector: null,
        countSelector: null,
        selectedItems: [],
        initialized: false
    };

    /**
     * CSS classes used by the module.
     * @type {Object}
     */
    var CLASSES = {
        selected: 'jb-row-selected',
        toolbarVisible: 'jb-bulk-toolbar-visible',
        toolbarHidden: 'jb-bulk-toolbar-hidden'
    };

    /**
     * Get all item checkboxes.
     *
     * @return {NodeList} List of checkbox elements.
     */
    var getCheckboxes = function() {
        return document.querySelectorAll(state.checkboxSelector);
    };

    /**
     * Get the select all checkbox.
     *
     * @return {HTMLInputElement|null} The select all checkbox or null.
     */
    var getSelectAllCheckbox = function() {
        return document.querySelector(state.selectAllSelector);
    };

    /**
     * Update the selected items array.
     */
    var updateSelectedItems = function() {
        state.selectedItems = [];
        getCheckboxes().forEach(function(checkbox) {
            if (checkbox.checked) {
                state.selectedItems.push(checkbox.value);
            }
        });
    };

    /**
     * Update the bulk actions toolbar visibility.
     */
    var updateToolbar = function() {
        var toolbar = document.querySelector(state.toolbarSelector);
        var countElement = document.querySelector(state.countSelector);

        if (!toolbar) {
            return;
        }

        if (state.selectedItems.length > 0) {
            toolbar.classList.remove(CLASSES.toolbarHidden);
            toolbar.classList.add(CLASSES.toolbarVisible);
            toolbar.style.display = '';

            if (countElement) {
                countElement.textContent = state.selectedItems.length;
            }
        } else {
            toolbar.classList.remove(CLASSES.toolbarVisible);
            toolbar.classList.add(CLASSES.toolbarHidden);
            toolbar.style.display = 'none';
        }
    };

    /**
     * Update the select all checkbox state.
     */
    var updateSelectAllState = function() {
        var selectAll = getSelectAllCheckbox();
        if (!selectAll) {
            return;
        }

        var checkboxes = getCheckboxes();
        var checkedCount = state.selectedItems.length;
        var totalCount = checkboxes.length;

        selectAll.checked = checkedCount > 0 && checkedCount === totalCount;
        selectAll.indeterminate = checkedCount > 0 && checkedCount < totalCount;
    };

    /**
     * Update row selection visual state.
     */
    var updateRowStates = function() {
        getCheckboxes().forEach(function(checkbox) {
            var row = checkbox.closest('tr, .jb-list-item, .jb-card');
            if (row) {
                if (checkbox.checked) {
                    row.classList.add(CLASSES.selected);
                } else {
                    row.classList.remove(CLASSES.selected);
                }
            }
        });
    };

    /**
     * Handle individual checkbox change.
     *
     * @param {Event} e The change event.
     */
    var onCheckboxChange = function(e) {
        if (!e.target.matches(state.checkboxSelector)) {
            return;
        }

        updateSelectedItems();
        updateToolbar();
        updateSelectAllState();
        updateRowStates();

        // Dispatch custom event.
        var event = new CustomEvent('jobboard:selectionchange', {
            detail: {
                selectedItems: state.selectedItems,
                count: state.selectedItems.length
            }
        });
        document.dispatchEvent(event);
    };

    /**
     * Handle select all checkbox change.
     *
     * @param {Event} e The change event.
     */
    var onSelectAllChange = function(e) {
        if (!e.target.matches(state.selectAllSelector)) {
            return;
        }

        var isChecked = e.target.checked;
        getCheckboxes().forEach(function(checkbox) {
            checkbox.checked = isChecked;
        });

        updateSelectedItems();
        updateToolbar();
        updateRowStates();
    };

    /**
     * Show confirmation modal for bulk action.
     *
     * @param {string} action The action name.
     * @param {Function} callback Callback to execute on confirm.
     */
    var confirmBulkAction = function(action, callback) {
        var count = state.selectedItems.length;

        Str.get_strings([
            {key: 'confirm', component: 'core'},
            {key: 'bulkaction_' + action + '_confirm', component: 'local_jobboard', param: count},
            {key: 'yes', component: 'core'},
            {key: 'cancel', component: 'core'}
        ]).then(function(strings) {
            return ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: strings[0],
                body: strings[1]
            });
        }).then(function(modal) {
            modal.setSaveButtonText(strings[2]);
            modal.show();

            modal.getRoot().on(ModalEvents.save, function() {
                if (typeof callback === 'function') {
                    callback(state.selectedItems);
                }
            });

            modal.getRoot().on(ModalEvents.hidden, function() {
                modal.destroy();
            });
        }).catch(function(error) {
            Notification.exception(error);
        });
    };

    /**
     * Handle bulk action button click.
     *
     * @param {Event} e The click event.
     */
    var onBulkActionClick = function(e) {
        var button = e.target.closest('[data-bulk-action]');
        if (!button) {
            return;
        }

        e.preventDefault();

        if (state.selectedItems.length === 0) {
            Str.get_string('noitemsselected', 'local_jobboard').then(function(msg) {
                Notification.addNotification({
                    message: msg,
                    type: 'warning'
                });
            });
            return;
        }

        var action = button.dataset.bulkAction;
        var form = button.closest('form');

        confirmBulkAction(action, function(selectedIds) {
            if (form) {
                // Add hidden input with selected IDs.
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selectedids';
                input.value = selectedIds.join(',');
                form.appendChild(input);

                // Add action input.
                var actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'bulkaction';
                actionInput.value = action;
                form.appendChild(actionInput);

                form.submit();
            } else {
                // Dispatch event for custom handling.
                var event = new CustomEvent('jobboard:bulkaction', {
                    detail: {
                        action: action,
                        selectedIds: selectedIds
                    }
                });
                document.dispatchEvent(event);
            }
        });
    };

    /**
     * Clear all selections.
     */
    var clearSelection = function() {
        getCheckboxes().forEach(function(checkbox) {
            checkbox.checked = false;
        });

        var selectAll = getSelectAllCheckbox();
        if (selectAll) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        }

        updateSelectedItems();
        updateToolbar();
        updateRowStates();
    };

    /**
     * Get currently selected item IDs.
     *
     * @return {Array} Array of selected IDs.
     */
    var getSelectedItems = function() {
        return state.selectedItems.slice();
    };

    /**
     * Initialize the bulk actions module.
     *
     * @param {Object} config Configuration object.
     * @param {string} [config.checkboxSelector='input.jb-bulk-checkbox'] Selector for item checkboxes.
     * @param {string} [config.selectAllSelector='input.jb-select-all'] Selector for select all checkbox.
     * @param {string} [config.toolbarSelector='.jb-bulk-toolbar'] Selector for bulk actions toolbar.
     * @param {string} [config.countSelector='.jb-selected-count'] Selector for selected count display.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        config = config || {};
        state.checkboxSelector = config.checkboxSelector || 'input.jb-bulk-checkbox';
        state.selectAllSelector = config.selectAllSelector || 'input.jb-select-all';
        state.toolbarSelector = config.toolbarSelector || '.jb-bulk-toolbar';
        state.countSelector = config.countSelector || '.jb-selected-count';

        // Event delegation.
        document.body.addEventListener('change', onCheckboxChange);
        document.body.addEventListener('change', onSelectAllChange);
        document.body.addEventListener('click', onBulkActionClick);

        // Initial state.
        updateSelectedItems();
        updateToolbar();

        state.initialized = true;
    };

    return {
        init: init,
        getSelectedItems: getSelectedItems,
        clearSelection: clearSelection
    };
});

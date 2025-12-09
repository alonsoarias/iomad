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
 * AMD module for "Select All" checkbox functionality.
 *
 * Provides reusable select all/none functionality for checkbox lists.
 * Used in bulk validation and reviewer assignment pages.
 *
 * @module     local_jobboard/checkbox_selectall
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    'use strict';

    /**
     * Initialize select all checkbox handler.
     *
     * @param {Object} options Configuration options.
     * @param {string} options.selectAllId ID of the select all checkbox (without #).
     * @param {string} options.checkboxClass Class of target checkboxes (without .).
     * @param {Function} options.onSelect Optional callback when selection changes.
     */
    var init = function(options) {
        options = options || {};

        var selectAllId = options.selectAllId || 'selectall';
        var checkboxClass = options.checkboxClass || 'item-checkbox';
        var onSelect = options.onSelect || null;

        var $selectAll = $('#' + selectAllId);
        var checkboxSelector = '.' + checkboxClass;

        if (!$selectAll.length) {
            return;
        }

        // Handle select all checkbox change.
        $selectAll.on('change', function() {
            var isChecked = $(this).prop('checked');
            $(checkboxSelector).prop('checked', isChecked);

            if (typeof onSelect === 'function') {
                onSelect(getSelectedCount(checkboxSelector));
            }
        });

        // Handle individual checkbox changes to update select all state.
        $(document).on('change', checkboxSelector, function() {
            updateSelectAllState($selectAll, checkboxSelector);

            if (typeof onSelect === 'function') {
                onSelect(getSelectedCount(checkboxSelector));
            }
        });

        // Initialize state.
        updateSelectAllState($selectAll, checkboxSelector);
    };

    /**
     * Update the select all checkbox state based on individual checkboxes.
     *
     * @param {jQuery} $selectAll The select all checkbox element.
     * @param {string} checkboxSelector Selector for target checkboxes.
     */
    var updateSelectAllState = function($selectAll, checkboxSelector) {
        var $checkboxes = $(checkboxSelector);
        var total = $checkboxes.length;
        var checked = $checkboxes.filter(':checked').length;

        if (total === 0) {
            $selectAll.prop('checked', false);
            $selectAll.prop('indeterminate', false);
        } else if (checked === 0) {
            $selectAll.prop('checked', false);
            $selectAll.prop('indeterminate', false);
        } else if (checked === total) {
            $selectAll.prop('checked', true);
            $selectAll.prop('indeterminate', false);
        } else {
            $selectAll.prop('checked', false);
            $selectAll.prop('indeterminate', true);
        }
    };

    /**
     * Get the count of selected checkboxes.
     *
     * @param {string} checkboxSelector Selector for target checkboxes.
     * @return {number} Number of selected checkboxes.
     */
    var getSelectedCount = function(checkboxSelector) {
        return $(checkboxSelector).filter(':checked').length;
    };

    /**
     * Get all selected checkbox values.
     *
     * @param {string} checkboxSelector Selector for target checkboxes.
     * @return {Array} Array of selected checkbox values.
     */
    var getSelectedValues = function(checkboxSelector) {
        var values = [];
        $(checkboxSelector).filter(':checked').each(function() {
            values.push($(this).val());
        });
        return values;
    };

    return {
        init: init,
        getSelectedCount: getSelectedCount,
        getSelectedValues: getSelectedValues
    };
});

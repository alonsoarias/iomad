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
 * Bulk selection module for applications in review page.
 *
 * @module     local_jobboard/bulk_selection
 * @copyright  2024-2025 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

    /**
     * Initialize bulk selection functionality.
     *
     * @param {Object} config - Configuration options.
     */
    function init(config) {
        const selectAllCheckbox = document.getElementById('jb-select-all');
        const bulkActionsBar = document.getElementById('jb-bulk-actions-bar');
        const selectedCountBadge = document.getElementById('jb-selected-count');
        const downloadSelectedBtn = document.getElementById('jb-download-selected-btn');
        const clearSelectionBtn = document.getElementById('jb-clear-selection-btn');

        if (!selectAllCheckbox || !bulkActionsBar) {
            return; // Elements not found, probably not on the list view.
        }

        /**
         * Get all application checkboxes.
         *
         * @returns {NodeList} All application checkboxes.
         */
        function getCheckboxes() {
            return document.querySelectorAll('.jb-application-checkbox');
        }

        /**
         * Get selected application IDs.
         *
         * @returns {Array} Array of selected application IDs.
         */
        function getSelectedIds() {
            const selected = [];
            getCheckboxes().forEach(function(checkbox) {
                if (checkbox.checked) {
                    selected.push(checkbox.value);
                }
            });
            return selected;
        }

        /**
         * Update the bulk actions bar visibility and count.
         */
        function updateBulkActionsBar() {
            const selectedIds = getSelectedIds();
            const count = selectedIds.length;

            if (count > 0) {
                bulkActionsBar.classList.remove('d-none');
                selectedCountBadge.textContent = count;
            } else {
                bulkActionsBar.classList.add('d-none');
            }

            // Update select all checkbox state.
            const allCheckboxes = getCheckboxes();
            if (allCheckboxes.length > 0) {
                const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(allCheckboxes).some(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
        }

        // Handle select all checkbox.
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            getCheckboxes().forEach(function(checkbox) {
                checkbox.checked = isChecked;
            });
            updateBulkActionsBar();
        });

        // Handle individual checkbox changes.
        document.querySelectorAll('.jb-application-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', updateBulkActionsBar);
        });

        // Handle clear selection button.
        if (clearSelectionBtn) {
            clearSelectionBtn.addEventListener('click', function() {
                selectAllCheckbox.checked = false;
                getCheckboxes().forEach(function(checkbox) {
                    checkbox.checked = false;
                });
                updateBulkActionsBar();
            });
        }

        // Handle download selected button.
        if (downloadSelectedBtn) {
            downloadSelectedBtn.addEventListener('click', function() {
                const selectedIds = getSelectedIds();
                if (selectedIds.length === 0) {
                    return;
                }

                // Build download URL with selected IDs.
                const baseUrl = this.dataset.url;
                const url = baseUrl + '&applicationids=' + selectedIds.join(',');

                // Open download in new window/tab.
                window.open(url, '_blank');
            });
        }

        // Initialize state.
        updateBulkActionsBar();
    }

    return {
        init: init
    };
});

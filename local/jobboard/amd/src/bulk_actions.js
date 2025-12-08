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
 * Bulk actions and confirmation modal AMD module for local_jobboard.
 *
 * Handles bulk selection, bulk actions, and confirmation modals for delete operations.
 *
 * @module     local_jobboard/bulk_actions
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification', 'core/str'], function($, Notification, Str) {
    'use strict';

    /**
     * Modal helper object for Bootstrap 4 compatible modal handling.
     */
    var JBModal = {
        /**
         * Show a modal.
         * @param {HTMLElement} modal The modal element.
         */
        show: function(modal) {
            if (!modal) {
                return;
            }
            $(modal).addClass('show');
            modal.style.display = 'block';
            modal.setAttribute('aria-hidden', 'false');
            $('body').addClass('modal-open');
            // Add backdrop.
            var backdrop = $('<div>', {
                'class': 'modal-backdrop fade show',
                'id': 'jb-modal-backdrop'
            });
            $('body').append(backdrop);
        },

        /**
         * Hide a modal.
         * @param {HTMLElement} modal The modal element.
         */
        hide: function(modal) {
            if (!modal) {
                return;
            }
            $(modal).removeClass('show');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            $('body').removeClass('modal-open');
            // Remove backdrop.
            $('#jb-modal-backdrop').remove();
        }
    };

    /**
     * Pending action callback.
     * @type {Function|null}
     */
    var pendingAction = null;

    /**
     * Update the bulk toolbar visibility and state.
     * @param {HTMLElement} element The triggering element.
     */
    var updateBulkToolbar = function(element) {
        var $form = $(element).closest('form');
        if (!$form.length) {
            $form = $(document);
        }
        var $toolbar = $form.find('.jb-bulk-toolbar');
        if (!$toolbar.length) {
            return;
        }

        var $checkboxes = $form.find('.jb-bulk-item');
        var $checked = $form.find('.jb-bulk-item:checked');
        var count = $checked.length;

        // Update count.
        $toolbar.find('.jb-selected-count').text(count);

        // Show/hide toolbar.
        if (count > 0) {
            $toolbar.removeClass('d-none');
        } else {
            $toolbar.addClass('d-none');
        }

        // Enable/disable action buttons.
        $toolbar.find('.jb-bulk-action').prop('disabled', count === 0);

        // Update select all checkbox.
        var $selectAll = $form.find('.jb-select-all');
        if ($selectAll.length) {
            $selectAll.prop('checked', $checkboxes.length > 0 && $checkboxes.length === $checked.length);
            $selectAll[0].indeterminate = count > 0 && count < $checkboxes.length;
        }
    };

    /**
     * Submit a bulk action.
     * @param {HTMLElement} form The form element.
     * @param {string} action The action to perform.
     */
    var submitBulkAction = function(form, action) {
        var $form = $(form);
        var $actionInput = $form.find('input[name="bulkaction"]');
        if (!$actionInput.length) {
            $actionInput = $('<input>', {
                type: 'hidden',
                name: 'bulkaction'
            });
            $form.append($actionInput);
        }
        $actionInput.val(action);
        form.submit();
    };

    /**
     * Show a confirmation modal.
     * @param {string} message The confirmation message.
     * @param {Function} onConfirm Callback when confirmed.
     */
    var showConfirmModal = function(message, onConfirm) {
        var modal = document.getElementById('jb-confirm-modal');
        if (modal) {
            var $modal = $(modal);
            var $msgEl = $modal.find('.jb-modal-message');

            if ($msgEl.length) {
                $msgEl.text(message);
            }

            // Store the action callback.
            pendingAction = onConfirm;

            // Show modal.
            JBModal.show(modal);
        } else {
            // Fallback to native confirm if modal not found.
            Str.get_string('confirm').then(function() {
                if (window.confirm(message) && onConfirm) {
                    onConfirm();
                }
            }).catch(function() {
                if (window.confirm(message) && onConfirm) {
                    onConfirm();
                }
            });
        }
    };

    /**
     * Initialize event handlers.
     */
    var init = function() {
        // Select all functionality.
        $(document).on('change', '.jb-select-all', function() {
            var target = $(this).data('target');
            var checked = $(this).prop('checked');
            $(target).prop('checked', checked);
            updateBulkToolbar(this);
        });

        // Individual checkbox change.
        $(document).on('change', '.jb-bulk-item', function() {
            updateBulkToolbar(this);
        });

        // Bulk action buttons.
        $(document).on('click', '.jb-bulk-action', function(e) {
            e.preventDefault();
            var action = $(this).data('action');
            var confirmMsg = $(this).data('confirm');
            var formId = $(this).data('form');
            var form = document.getElementById(formId);

            if (!form) {
                return;
            }

            if (confirmMsg) {
                showConfirmModal(confirmMsg, function() {
                    submitBulkAction(form, action);
                });
            } else {
                submitBulkAction(form, action);
            }
        });

        // Individual action buttons with confirmation.
        $(document).on('click', '.jb-confirm-trigger', function(e) {
            e.preventDefault();
            var confirmMsg = $(this).data('confirm');
            var url = $(this).attr('href');

            if (confirmMsg) {
                showConfirmModal(confirmMsg, function() {
                    window.location.href = url;
                });
            } else {
                window.location.href = url;
            }
        });

        // Modal confirm button.
        $(document).on('click', '.jb-modal-confirm', function() {
            var modal = document.getElementById('jb-confirm-modal');
            JBModal.hide(modal);
            if (pendingAction) {
                pendingAction();
                pendingAction = null;
            }
        });

        // Modal cancel/close buttons.
        $(document).on('click', '#jb-confirm-modal [data-dismiss="modal"], #jb-confirm-modal .close', function() {
            var modal = document.getElementById('jb-confirm-modal');
            JBModal.hide(modal);
            pendingAction = null;
        });

        // Close on backdrop click.
        $(document).on('click', '#jb-confirm-modal', function(e) {
            if (e.target === this) {
                JBModal.hide(this);
                pendingAction = null;
            }
        });

        // Close on Escape key.
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                var modal = document.getElementById('jb-confirm-modal');
                if (modal && $(modal).hasClass('show')) {
                    JBModal.hide(modal);
                    pendingAction = null;
                }
            }
        });
    };

    return {
        init: init
    };
});

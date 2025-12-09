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
 * AMD module for confirmation dialogs.
 *
 * Replaces inline onclick="return confirm()" with accessible modal dialogs.
 * Uses Moodle's core/notification module for consistent modal styling.
 *
 * @module     local_jobboard/confirm_action
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/notification', 'core/str'], function($, Notification, Str) {
    'use strict';

    /**
     * Initialize confirmation handlers for elements with data-confirm attribute.
     *
     * @param {Object} options Configuration options.
     * @param {string} options.selector Selector for confirm elements.
     */
    var init = function(options) {
        options = options || {};

        var selector = options.selector || '[data-confirm]';

        // Handle click events on elements with data-confirm attribute.
        $(document).on('click', selector, function(e) {
            var $el = $(this);
            var confirmMessage = $el.data('confirm');

            if (!confirmMessage) {
                return true;
            }

            e.preventDefault();
            e.stopPropagation();

            var href = $el.attr('href');
            var isForm = $el.is('button') && $el.closest('form').length > 0;

            // Get the confirmation title from data attribute or use default.
            var titleKey = $el.data('confirm-title') || 'confirm';

            Str.get_string(titleKey, 'moodle').then(function(title) {
                return Notification.confirm(
                    title,
                    confirmMessage,
                    Str.get_string('yes'),
                    Str.get_string('no'),
                    function() {
                        if (isForm) {
                            // Submit the form.
                            $el.closest('form').submit();
                        } else if (href) {
                            // Navigate to the href.
                            window.location.href = href;
                        }
                    }
                );
            }).catch(function() {
                // Fallback to browser confirm if string loading fails.
                if (window.confirm(confirmMessage)) {
                    if (isForm) {
                        $el.closest('form').submit();
                    } else if (href) {
                        window.location.href = href;
                    }
                }
            });

            return false;
        });
    };

    /**
     * Show a confirmation dialog programmatically.
     *
     * @param {string} message The confirmation message.
     * @param {Function} onConfirm Callback when confirmed.
     * @param {Function} onCancel Optional callback when cancelled.
     * @param {string} title Optional dialog title.
     */
    var confirm = function(message, onConfirm, onCancel, title) {
        title = title || 'Confirm';

        Notification.confirm(
            title,
            message,
            Str.get_string('yes'),
            Str.get_string('no'),
            function() {
                if (typeof onConfirm === 'function') {
                    onConfirm();
                }
            },
            function() {
                if (typeof onCancel === 'function') {
                    onCancel();
                }
            }
        );
    };

    return {
        init: init,
        confirm: confirm
    };
});

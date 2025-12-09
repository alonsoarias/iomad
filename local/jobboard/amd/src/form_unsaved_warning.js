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
 * Form unsaved changes warning module.
 *
 * Shows a confirmation dialog when the user tries to leave a page
 * with unsaved form changes.
 *
 * @module     local_jobboard/form_unsaved_warning
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/str', 'core/notification'], function($, Str, Notification) {
    'use strict';

    /**
     * Module state.
     */
    var state = {
        formSelector: 'form.mform',
        initialFormData: null,
        hasChanges: false,
        isSubmitting: false,
        warningMessage: '',
        excludedLinks: ['.btn-cancel', '[data-action="cancel"]', '[type="submit"]']
    };

    /**
     * Get current form data as a serialized string.
     *
     * @param {jQuery} form - The form element.
     * @returns {string} Serialized form data.
     */
    var getFormData = function(form) {
        return form.serialize();
    };

    /**
     * Check if the form has unsaved changes.
     *
     * @returns {boolean} True if there are unsaved changes.
     */
    var checkForChanges = function() {
        var form = $(state.formSelector);
        if (form.length === 0 || state.initialFormData === null) {
            return false;
        }
        var currentData = getFormData(form);
        return currentData !== state.initialFormData;
    };

    /**
     * Update the hasChanges state.
     */
    var updateChangesState = function() {
        state.hasChanges = checkForChanges();
    };

    /**
     * Show warning before leaving the page.
     *
     * @param {Event} e - The beforeunload event.
     * @returns {string|undefined} Warning message or undefined.
     */
    var beforeUnloadHandler = function(e) {
        if (state.hasChanges && !state.isSubmitting) {
            e.preventDefault();
            e.returnValue = state.warningMessage;
            return state.warningMessage;
        }
    };

    /**
     * Handle click on links that would navigate away.
     *
     * @param {Event} e - The click event.
     */
    var handleLinkClick = function(e) {
        // Skip if form is being submitted.
        if (state.isSubmitting) {
            return;
        }

        // Skip if no changes.
        if (!state.hasChanges) {
            return;
        }

        var link = $(e.currentTarget);

        // Skip excluded links (cancel buttons, submit buttons, etc.).
        for (var i = 0; i < state.excludedLinks.length; i++) {
            if (link.is(state.excludedLinks[i])) {
                return;
            }
        }

        // Skip if it's an anchor link or inline script link.
        var href = link.attr('href');
        // eslint-disable-next-line no-script-url
        if (!href || href === '#' || href.indexOf('javascript' + ':') === 0) {
            return;
        }

        // Prevent navigation and show confirmation.
        e.preventDefault();

        Notification.confirm(
            Str.get_string('unsavedchanges', 'local_jobboard'),
            state.warningMessage,
            Str.get_string('leave', 'local_jobboard'),
            Str.get_string('stay', 'local_jobboard'),
            function() {
                // User confirmed - navigate away.
                state.hasChanges = false;
                window.location.href = href;
            }
        );
    };

    /**
     * Handle form submission.
     */
    var handleFormSubmit = function() {
        state.isSubmitting = true;
    };

    /**
     * Initialize the module.
     *
     * @param {Object} config - Configuration options.
     * @param {string} [config.formSelector] - CSS selector for the form.
     * @param {Array} [config.excludedLinks] - Array of selectors for links to exclude.
     */
    var init = function(config) {
        config = config || {};

        // Apply configuration.
        if (config.formSelector) {
            state.formSelector = config.formSelector;
        }
        if (config.excludedLinks) {
            state.excludedLinks = state.excludedLinks.concat(config.excludedLinks);
        }

        // Load warning message string.
        Str.get_string('unsavedchangeswarning', 'local_jobboard').done(function(str) {
            state.warningMessage = str;
        }).fail(function() {
            state.warningMessage = 'You have unsaved changes. Are you sure you want to leave this page?';
        });

        // Wait for DOM to be ready.
        $(document).ready(function() {
            var form = $(state.formSelector);

            if (form.length === 0) {
                return;
            }

            // Store initial form data.
            // Small delay to allow dynamic fields to initialize.
            setTimeout(function() {
                state.initialFormData = getFormData(form);
            }, 500);

            // Listen for form changes.
            form.on('change input', 'input, select, textarea', function() {
                updateChangesState();
            });

            // Handle form submission.
            form.on('submit', handleFormSubmit);

            // Handle cancel button clicks.
            form.find('[name="cancel"], .btn-cancel').on('click', function() {
                state.isSubmitting = true;
            });

            // Listen for beforeunload.
            $(window).on('beforeunload', beforeUnloadHandler);

            // Listen for clicks on navigation links.
            $('a').not(state.excludedLinks.join(', ')).on('click', handleLinkClick);

            // Also handle the navigation footer links.
            $('.jb-navigation-footer a').on('click', handleLinkClick);
        });
    };

    return {
        init: init
    };
});

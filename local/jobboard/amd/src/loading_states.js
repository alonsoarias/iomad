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
 * Loading states module for form submission feedback.
 *
 * @module     local_jobboard/loading_states
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/str'], function(Str) {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        formSelector: null,
        loadingOverlay: null,
        initialized: false
    };

    /**
     * CSS classes used by the module.
     * @type {Object}
     */
    var CLASSES = {
        loading: 'jb-loading',
        loadingOverlay: 'jb-loading-overlay',
        spinner: 'jb-spinner',
        disabled: 'jb-btn-disabled'
    };

    /**
     * Create the loading overlay element.
     *
     * @return {HTMLElement} The loading overlay element.
     */
    var createLoadingOverlay = function() {
        var overlay = document.createElement('div');
        overlay.className = CLASSES.loadingOverlay;
        overlay.innerHTML = '<div class="' + CLASSES.spinner + '">' +
            '<div class="jb-spinner-border" role="status">' +
            '<span class="sr-only">Loading...</span>' +
            '</div>' +
            '</div>';
        overlay.style.display = 'none';
        document.body.appendChild(overlay);
        return overlay;
    };

    /**
     * Show loading state on form.
     *
     * @param {HTMLFormElement} form The form element.
     */
    var showLoading = function(form) {
        if (!form) {
            return;
        }

        form.classList.add(CLASSES.loading);

        // Disable all submit buttons.
        var buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        buttons.forEach(function(button) {
            button.disabled = true;
            button.classList.add(CLASSES.disabled);
            // Store original text.
            button.dataset.originalText = button.textContent || button.value;

            Str.get_string('loading', 'local_jobboard').then(function(loadingText) {
                if (button.tagName === 'BUTTON') {
                    button.textContent = loadingText + '...';
                } else {
                    button.value = loadingText + '...';
                }
            }).catch(function() {
                if (button.tagName === 'BUTTON') {
                    button.textContent = 'Loading...';
                } else {
                    button.value = 'Loading...';
                }
            });
        });

        // Show overlay.
        if (state.loadingOverlay) {
            state.loadingOverlay.style.display = 'flex';
        }
    };

    /**
     * Hide loading state on form.
     *
     * @param {HTMLFormElement} form The form element.
     */
    var hideLoading = function(form) {
        if (!form) {
            return;
        }

        form.classList.remove(CLASSES.loading);

        // Re-enable all submit buttons.
        var buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        buttons.forEach(function(button) {
            button.disabled = false;
            button.classList.remove(CLASSES.disabled);
            // Restore original text.
            if (button.dataset.originalText) {
                if (button.tagName === 'BUTTON') {
                    button.textContent = button.dataset.originalText;
                } else {
                    button.value = button.dataset.originalText;
                }
            }
        });

        // Hide overlay.
        if (state.loadingOverlay) {
            state.loadingOverlay.style.display = 'none';
        }
    };

    /**
     * Handle form submission.
     *
     * @param {Event} e The submit event.
     */
    var onFormSubmit = function(e) {
        var form = e.target;
        showLoading(form);
    };

    /**
     * Initialize the loading states module.
     *
     * @param {Object} config Configuration object.
     * @param {string} [config.formSelector='form.mform'] CSS selector for forms.
     * @param {boolean} [config.showOverlay=true] Whether to show full-page overlay.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        config = config || {};
        state.formSelector = config.formSelector || 'form.mform';

        // Create overlay if needed.
        if (config.showOverlay !== false) {
            state.loadingOverlay = createLoadingOverlay();
        }

        // Attach to all matching forms.
        var forms = document.querySelectorAll(state.formSelector);
        forms.forEach(function(form) {
            form.addEventListener('submit', onFormSubmit);
        });

        state.initialized = true;
    };

    return {
        init: init,
        showLoading: showLoading,
        hideLoading: hideLoading
    };
});

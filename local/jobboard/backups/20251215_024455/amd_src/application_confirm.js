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
 * Application confirmation module for final submission modal.
 *
 * @module     local_jobboard/application_confirm
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/modal_factory', 'core/modal_events', 'core/str', 'core/notification', 'core/templates'],
    function(ModalFactory, ModalEvents, Str, Notification, Templates) {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        formSelector: null,
        totalDocs: 0,
        uploadedDocs: 0,
        modal: null,
        initialized: false
    };

    /**
     * Check if all required documents are uploaded.
     *
     * @return {boolean} Whether all required documents are uploaded.
     */
    var checkDocumentsComplete = function() {
        return state.uploadedDocs >= state.totalDocs;
    };

    /**
     * Build the confirmation modal body.
     *
     * @return {Promise} Promise resolving with HTML string.
     */
    var buildConfirmationBody = function() {
        var context = {
            totalDocs: state.totalDocs,
            uploadedDocs: state.uploadedDocs,
            allUploaded: checkDocumentsComplete(),
            missingDocs: state.totalDocs - state.uploadedDocs
        };

        return Templates.render('local_jobboard/application_confirm_body', context).catch(function() {
            // Fallback to plain text if template not available.
            return Str.get_strings([
                {key: 'confirmsubmitapplication', component: 'local_jobboard'},
                {key: 'documentsprogress', component: 'local_jobboard'},
                {key: 'missingdocumentswarning', component: 'local_jobboard'}
            ]).then(function(strings) {
                var html = '<div class="jb-confirm-body">';
                html += '<p>' + strings[0] + '</p>';
                html += '<p>' + strings[1].replace('{uploaded}', state.uploadedDocs)
                    .replace('{total}', state.totalDocs) + '</p>';

                if (!checkDocumentsComplete()) {
                    html += '<div class="jb-alert jb-alert-warning">';
                    html += '<strong>' + strings[2].replace('{count}', context.missingDocs) + '</strong>';
                    html += '</div>';
                }

                html += '</div>';
                return html;
            });
        });
    };

    /**
     * Show the confirmation modal.
     *
     * @param {HTMLFormElement} form The application form.
     */
    var showConfirmation = function(form) {
        buildConfirmationBody().then(function(bodyHtml) {
            return Str.get_strings([
                {key: 'confirmsubmission', component: 'local_jobboard'},
                {key: 'submit', component: 'core'},
                {key: 'cancel', component: 'core'}
            ]).then(function(strings) {
                return ModalFactory.create({
                    type: ModalFactory.types.SAVE_CANCEL,
                    title: strings[0],
                    body: bodyHtml
                }).then(function(modal) {
                    state.modal = modal;
                    modal.setSaveButtonText(strings[1]);
                    modal.show();

                    modal.getRoot().on(ModalEvents.save, function() {
                        // Submit the form.
                        form.submit();
                    });

                    modal.getRoot().on(ModalEvents.hidden, function() {
                        modal.destroy();
                        state.modal = null;
                    });
                });
            });
        }).catch(function(error) {
            Notification.exception(error);
        });
    };

    /**
     * Handle form submission.
     *
     * @param {Event} e The submit event.
     */
    var onFormSubmit = function(e) {
        var form = e.target.closest(state.formSelector);
        if (!form) {
            return;
        }

        // Check if submit button was clicked (not just enter key).
        var submitButton = form.querySelector('[data-action="submit-application"]');
        if (!submitButton || document.activeElement !== submitButton) {
            return;
        }

        e.preventDefault();
        showConfirmation(form);
    };

    /**
     * Update document counts.
     *
     * @param {number} total Total required documents.
     * @param {number} uploaded Currently uploaded documents.
     */
    var updateDocumentCounts = function(total, uploaded) {
        state.totalDocs = total;
        state.uploadedDocs = uploaded;
    };

    /**
     * Listen for document upload events.
     */
    var setupDocumentListener = function() {
        document.addEventListener('jobboard:documentuploaded', function(e) {
            if (e.detail && typeof e.detail.uploadedCount !== 'undefined') {
                state.uploadedDocs = e.detail.uploadedCount;
            }
        });
    };

    /**
     * Initialize the application confirm module.
     *
     * @param {Object} config Configuration object.
     * @param {string} [config.formSelector='form.jb-application-form'] CSS selector for form.
     * @param {number} [config.totalDocs=0] Total required documents.
     * @param {number} [config.uploadedDocs=0] Currently uploaded documents.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        config = config || {};
        state.formSelector = config.formSelector || 'form.jb-application-form';
        state.totalDocs = config.totalDocs || 0;
        state.uploadedDocs = config.uploadedDocs || 0;

        document.body.addEventListener('submit', onFormSubmit);
        setupDocumentListener();

        state.initialized = true;
    };

    return {
        init: init,
        showConfirmation: showConfirmation,
        updateDocumentCounts: updateDocumentCounts
    };
});

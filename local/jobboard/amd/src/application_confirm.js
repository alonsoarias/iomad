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
 * Application confirmation modal module.
 *
 * Handles the confirmation dialog before submitting a job application.
 *
 * @module     local_jobboard/application_confirm
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/modal_factory', 'core/modal_events', 'core/str', 'core/notification'],
    function($, ModalFactory, ModalEvents, Str, Notification) {
    'use strict';

    var ApplicationConfirm = {
        modal: null,
        form: null,
        submitButton: null,

        /**
         * Initialize the confirmation module.
         *
         * @param {Object} config Configuration options
         * @param {string} config.formSelector Form selector
         * @param {number} config.totalDocs Total required documents
         * @param {number} config.uploadedDocs Number of uploaded documents
         */
        init: function(config) {
            this.config = $.extend({
                formSelector: 'form.mform',
                totalDocs: 0,
                uploadedDocs: 0
            }, config);

            this.form = $(this.config.formSelector);
            this.submitButton = this.form.find('input[type="submit"][name="submitbutton"]');

            if (!this.form.length || !this.submitButton.length) {
                return;
            }

            this.createModal();
            this.bindEvents();
        },

        /**
         * Create the confirmation modal.
         */
        createModal: function() {
            var self = this;

            Str.get_strings([
                {key: 'confirmapplication_title', component: 'local_jobboard'},
                {key: 'confirmapplication_text', component: 'local_jobboard'},
                {key: 'confirmapplication_docs', component: 'local_jobboard'},
                {key: 'confirmapplication_data', component: 'local_jobboard'},
                {key: 'confirmapplication_consent', component: 'local_jobboard'},
                {key: 'confirmapplication_final', component: 'local_jobboard'},
                {key: 'confirmsubmit', component: 'local_jobboard'},
                {key: 'cancelsubmit', component: 'local_jobboard'}
            ]).then(function(strings) {
                var body = '<div class="text-center mb-3">' +
                    '<div class="jb-confirm-icon info"><i class="fa fa-paper-plane"></i></div>' +
                    '<p class="lead">' + strings[1] + '</p>' +
                    '</div>' +
                    '<ul class="jb-confirm-checklist list-unstyled">' +
                    '<li><i class="fa fa-check-circle"></i> ' + strings[2] + '</li>' +
                    '<li><i class="fa fa-check-circle"></i> ' + strings[3] + '</li>' +
                    '<li><i class="fa fa-check-circle"></i> ' + strings[4] + '</li>' +
                    '</ul>' +
                    '<div class="alert alert-warning mt-3 mb-0">' +
                    '<i class="fa fa-exclamation-triangle mr-2"></i>' + strings[5] +
                    '</div>';

                return ModalFactory.create({
                    type: ModalFactory.types.SAVE_CANCEL,
                    title: strings[0],
                    body: body
                }).then(function(modal) {
                    self.modal = modal;

                    // Customize buttons
                    modal.setSaveButtonText(strings[6]);
                    modal.getRoot().addClass('jb-confirm-modal');
                    modal.getRoot().find('[data-action="cancel"]').text(strings[7]);

                    // Handle save button click
                    modal.getRoot().on(ModalEvents.save, function(e) {
                        e.preventDefault();
                        self.submitForm();
                    });

                    return modal;
                });
            }).catch(Notification.exception);
        },

        /**
         * Bind event handlers.
         */
        bindEvents: function() {
            var self = this;

            // Intercept form submission
            this.form.on('submit', function(e) {
                // Only intercept if it's the main submit button
                var $clicked = $(document.activeElement);
                if ($clicked.is(self.submitButton)) {
                    e.preventDefault();
                    self.showConfirmation();
                }
            });

            // Also handle direct click on submit button
            this.submitButton.on('click', function(e) {
                if (!$(this).data('confirmed')) {
                    e.preventDefault();
                    self.showConfirmation();
                }
            });
        },

        /**
         * Show the confirmation modal.
         */
        showConfirmation: function() {
            if (this.modal) {
                // Update document count in modal if needed
                this.updateDocumentStatus();
                this.modal.show();
            }
        },

        /**
         * Update document upload status in the modal.
         */
        updateDocumentStatus: function() {
            // Count uploaded documents
            var uploadedCount = 0;
            this.form.find('.filepicker-filelist .filepicker-filename').each(function() {
                if ($(this).text().trim()) {
                    uploadedCount++;
                }
            });

            this.config.uploadedDocs = uploadedCount;

            // Update visual indicator in modal if all docs uploaded
            var $docItem = this.modal.getRoot().find('.jb-confirm-checklist li:first-child');
            if (uploadedCount >= this.config.totalDocs) {
                $docItem.find('.fa').removeClass('fa-check-circle').addClass('fa-check-circle text-success');
            } else {
                $docItem.find('.fa').removeClass('text-success');
            }
        },

        /**
         * Submit the form after confirmation.
         */
        submitForm: function() {
            var self = this;

            // Mark as confirmed
            this.submitButton.data('confirmed', true);

            // Add loading state
            this.submitButton.prop('disabled', true);
            this.submitButton.addClass('is-loading');
            this.submitButton.val(M.util.get_string('applicationsubmitting', 'local_jobboard'));

            // Close modal
            this.modal.hide();

            // Submit the form
            setTimeout(function() {
                self.form.off('submit');
                self.submitButton.click();
            }, 100);
        }
    };

    return {
        /**
         * Initialize the application confirmation module.
         *
         * @param {Object} config Configuration options
         */
        init: function(config) {
            ApplicationConfirm.init(config);
        }
    };
});

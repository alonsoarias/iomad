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
 * Document upload module with drag & drop support.
 *
 * @module     local_jobboard/document_upload
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification', 'core/str', 'core/templates'],
    function($, Ajax, Notification, Str, Templates) {

    /**
     * Document Upload class.
     *
     * @param {string} selector Container selector
     * @param {object} config Configuration options
     */
    var DocumentUpload = function(selector, config) {
        this.container = $(selector);
        this.config = $.extend({
            maxSize: 5242880, // 5MB
            acceptedTypes: ['application/pdf'],
            uploadUrl: ''
        }, config);

        this.init();
    };

    /**
     * Initialize the document upload.
     */
    DocumentUpload.prototype.init = function() {
        this.registerEventListeners();
        this.initDragDrop();
        this.initFileInputLabels();
    };

    /**
     * Register event listeners.
     */
    DocumentUpload.prototype.registerEventListeners = function() {
        var self = this;

        // Form submission.
        this.container.on('submit', '.document-upload-form', function(e) {
            e.preventDefault();
            self.handleFormSubmit($(this));
        });

        // File input change.
        this.container.on('change', '.custom-file-input', function() {
            self.updateFileLabel($(this));
            self.validateFile($(this));
        });

        // Re-upload button.
        this.container.on('click', '.btn-reupload', function(e) {
            e.preventDefault();
            var card = $(this).closest('.document-upload-card');
            card.find('.document-upload-form').slideDown();
        });

        // Cancel re-upload.
        this.container.on('click', '.btn-cancel-reupload', function(e) {
            e.preventDefault();
            $(this).closest('.document-upload-form').slideUp();
        });
    };

    /**
     * Initialize drag & drop functionality.
     */
    DocumentUpload.prototype.initDragDrop = function() {
        var self = this;

        this.container.find('.document-upload-card').each(function() {
            var card = $(this);
            var form = card.find('.document-upload-form');

            if (form.length === 0) {
                return;
            }

            card.on('dragover dragenter', function(e) {
                e.preventDefault();
                e.stopPropagation();
                card.addClass('drag-over');
            });

            card.on('dragleave dragend drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                card.removeClass('drag-over');
            });

            card.on('drop', function(e) {
                var files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    var input = form.find('.custom-file-input');
                    input[0].files = files;
                    self.updateFileLabel(input);
                    if (self.validateFile(input)) {
                        form.submit();
                    }
                }
            });
        });
    };

    /**
     * Initialize file input labels.
     */
    DocumentUpload.prototype.initFileInputLabels = function() {
        this.container.find('.custom-file-input').each(function() {
            var input = $(this);
            if (input[0].files && input[0].files.length > 0) {
                var label = input.next('.custom-file-label');
                label.text(input[0].files[0].name);
            }
        });
    };

    /**
     * Update file input label with selected filename.
     *
     * @param {jQuery} input File input element
     */
    DocumentUpload.prototype.updateFileLabel = function(input) {
        var label = input.next('.custom-file-label');
        if (input[0].files && input[0].files.length > 0) {
            label.text(input[0].files[0].name);
        } else {
            Str.get_string('choosefiles', 'local_jobboard').then(function(str) {
                label.text(str);
                return str;
            }).catch(Notification.exception);
        }
    };

    /**
     * Validate file before upload.
     *
     * @param {jQuery} input File input element
     * @return {boolean} True if valid
     */
    DocumentUpload.prototype.validateFile = function(input) {
        var self = this;
        var file = input[0].files[0];
        var form = input.closest('form');
        var errorContainer = form.find('.upload-error');

        // Remove previous errors.
        errorContainer.remove();

        if (!file) {
            return false;
        }

        // Check file size.
        if (file.size > this.config.maxSize) {
            Str.get_string('error:filetoobig', 'local_jobboard').then(function(str) {
                self.showError(form, str);
                return str;
            }).catch(Notification.exception);
            input.val('');
            this.updateFileLabel(input);
            return false;
        }

        // Check file type.
        if (this.config.acceptedTypes.indexOf(file.type) === -1) {
            Str.get_string('error:invalidformat', 'local_jobboard').then(function(str) {
                self.showError(form, str);
                return str;
            }).catch(Notification.exception);
            input.val('');
            this.updateFileLabel(input);
            return false;
        }

        return true;
    };

    /**
     * Show error message.
     *
     * @param {jQuery} form Form element
     * @param {string} message Error message
     */
    DocumentUpload.prototype.showError = function(form, message) {
        var error = $('<div class="alert alert-danger upload-error mt-2"></div>').text(message);
        form.find('.form-group').first().after(error);
    };

    /**
     * Handle form submission.
     *
     * @param {jQuery} form Form element
     */
    DocumentUpload.prototype.handleFormSubmit = function(form) {
        var self = this;
        var input = form.find('.custom-file-input');

        if (!this.validateFile(input)) {
            return;
        }

        var submitButton = form.find('button[type="submit"]');
        var originalText = submitButton.html();

        // Show loading state.
        submitButton.prop('disabled', true);
        Str.get_string('processing', 'local_jobboard').then(function(str) {
            submitButton.html('<span class="spinner-border spinner-border-sm"></span> ' + str);
            return str;
        }).catch(Notification.exception);

        var formData = new FormData(form[0]);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        }).done(function(response) {
            if (response.success) {
                // Refresh the card with new content.
                self.refreshCard(form.closest('.document-upload-card'), response);
                Notification.addNotification({
                    message: response.message,
                    type: 'success'
                });
            } else {
                self.showError(form, response.error);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            self.showError(form, errorThrown);
        }).always(function() {
            submitButton.prop('disabled', false).html(originalText);
        });
    };

    /**
     * Refresh document card after upload.
     *
     * @param {jQuery} card Card element
     * @param {object} response Server response
     */
    DocumentUpload.prototype.refreshCard = function(card, response) {
        if (response.html) {
            card.replaceWith(response.html);
        } else if (response.templatedata) {
            Templates.render('local_jobboard/document_upload', response.templatedata)
                .then(function(html) {
                    card.replaceWith(html);
                    return html;
                })
                .catch(Notification.exception);
        } else {
            // Simple update.
            card.find('.document-upload-form').hide();
            card.find('.current-file').show();
        }
    };

    return {
        /**
         * Initialize document upload.
         *
         * @param {string} selector Container selector
         * @param {object} config Configuration options
         */
        init: function(selector, config) {
            return new DocumentUpload(selector, config);
        }
    };
});

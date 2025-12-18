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
 * Document review module for reviewing application documents.
 *
 * Handles document preview, rejection with reason prompt, and observation saving.
 *
 * @module     local_jobboard/document_review
 * @copyright  2024-2025 ISER - Instituto Superior de Educacion Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification', 'core/str'], function($, Ajax, Notification, Str) {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        applicationId: 0,
        strings: {},
        initialized: false
    };

    /**
     * Load required language strings.
     *
     * @return {Promise}
     */
    var loadStrings = function() {
        return Str.get_strings([
            {key: 'rejectreason_prompt', component: 'local_jobboard'},
            {key: 'rejectreason_required', component: 'local_jobboard'},
            {key: 'saving', component: 'local_jobboard'},
            {key: 'sending', component: 'local_jobboard'},
            {key: 'sent', component: 'local_jobboard'},
            {key: 'emailerror', component: 'local_jobboard'},
            {key: 'saveandsend', component: 'local_jobboard'}
        ]).then(function(strings) {
            state.strings = {
                rejectPrompt: strings[0],
                rejectRequired: strings[1],
                saving: strings[2],
                sending: strings[3],
                sent: strings[4],
                emailError: strings[5],
                saveAndSend: strings[6]
            };
            return strings;
        });
    };

    /**
     * Preview a document in the preview frame.
     *
     * @param {HTMLElement} element The clicked element with document data.
     */
    var previewDocument = function(element) {
        var previewUrl = element.dataset.previewUrl;
        var isPdf = element.dataset.isPdf === 'true' || element.dataset.isPdf === '1';
        var isImage = element.dataset.isImage === 'true' || element.dataset.isImage === '1';
        var typename = element.dataset.typename;
        var filename = element.dataset.filename;
        var downloadUrl = element.dataset.downloadUrl;

        // Update active state in list.
        document.querySelectorAll('[data-region="documents-list"] .list-group-item').forEach(function(item) {
            item.classList.remove('active', 'bg-primary-subtle');
        });
        element.classList.add('active', 'bg-primary-subtle');

        // Update title.
        var titleEl = document.querySelector('[data-region="preview-title"]');
        if (titleEl) {
            titleEl.textContent = filename || typename;
        }

        // Update subtitle.
        var subtitleEl = document.querySelector('[data-region="preview-subtitle"]');
        if (subtitleEl) {
            subtitleEl.textContent = typename;
        } else if (titleEl && titleEl.parentNode) {
            subtitleEl = document.createElement('small');
            subtitleEl.className = 'd-block text-muted';
            subtitleEl.dataset.region = 'preview-subtitle';
            subtitleEl.textContent = typename;
            titleEl.parentNode.appendChild(subtitleEl);
        }

        // Update download button.
        var downloadBtn = document.querySelector('[data-region="download-btn"]');
        if (downloadBtn && downloadUrl) {
            downloadBtn.href = downloadUrl;
        }

        // Update preview frame.
        var previewFrame = document.querySelector('[data-region="preview-frame"]');
        if (!previewFrame) {
            return;
        }

        if (previewUrl && (isPdf || isImage)) {
            if (isPdf) {
                previewFrame.innerHTML = '<iframe src="' + previewUrl + '" class="w-100 h-100 border-0" ' +
                    'style="min-height: 500px;"></iframe>';
            } else if (isImage) {
                previewFrame.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 p-4" ' +
                    'style="min-height: 500px;"><img src="' + previewUrl + '" alt="' + (filename || '') +
                    '" class="img-fluid shadow rounded" style="max-height: 480px;"></div>';
            }
        } else {
            var downloadHtml = downloadUrl ?
                '<a href="' + downloadUrl + '" class="btn btn-primary btn-sm" target="_blank">' +
                '<i class="fa fa-download me-2"></i>Download</a>' : '';
            previewFrame.innerHTML = '<div class="d-flex flex-column align-items-center justify-content-center ' +
                'h-100 text-muted" style="min-height: 500px;"><i class="fa fa-file fa-4x mb-3"></i>' +
                '<p class="mb-3">Preview not available</p>' + downloadHtml + '</div>';
        }
    };

    /**
     * Reject a document with a reason.
     *
     * @param {string} baseUrl The base URL for the reject action.
     * @param {string} sesskey The session key.
     * @param {string} applicationId The application ID.
     * @param {string} documentId The document ID.
     */
    var rejectDocument = function(baseUrl, sesskey, applicationId, documentId) {
        var reason = window.prompt(state.strings.rejectPrompt || 'Enter rejection reason:');

        if (reason === null) {
            return; // User cancelled.
        }

        if (reason.trim() === '') {
            window.alert(state.strings.rejectRequired || 'You must enter a reason to reject the document.');
            return;
        }

        // Create and submit form.
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = baseUrl;
        form.innerHTML = '<input type="hidden" name="sesskey" value="' + sesskey + '">' +
                         '<input type="hidden" name="view" value="review">' +
                         '<input type="hidden" name="applicationid" value="' + applicationId + '">' +
                         '<input type="hidden" name="documentid" value="' + documentId + '">' +
                         '<input type="hidden" name="action" value="reject">' +
                         '<input type="hidden" name="reason" value="' + reason.replace(/"/g, '&quot;') + '">';
        document.body.appendChild(form);
        form.submit();
    };

    /**
     * Save document observations and optionally send email.
     */
    var saveAndSendObservations = function() {
        var observations = [];
        $('.jb-doc-observation').each(function() {
            var docId = $(this).data('document-id');
            var text = $(this).val();
            if (docId) {
                observations.push({documentid: docId, observation: text});
            }
        });

        var btn = $('#saveAndSendBtn');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> ' + (state.strings.saving || 'Saving...'));

        Ajax.call([{
            methodname: 'local_jobboard_save_document_observations',
            args: {applicationid: state.applicationId, observations: JSON.stringify(observations)},
            done: function() {
                btn.html('<i class="fa fa-spinner fa-spin me-1"></i> ' + (state.strings.sending || 'Sending...'));
                Ajax.call([{
                    methodname: 'local_jobboard_send_observations_email',
                    args: {applicationid: state.applicationId, observations: JSON.stringify(observations)},
                    done: function() {
                        btn.prop('disabled', false).html('<i class="fa fa-check me-1"></i> ' + (state.strings.sent || 'Sent!'));
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    },
                    fail: function(error) {
                        btn.prop('disabled', false).html('<i class="fa fa-exclamation-triangle me-1"></i> ' +
                            (state.strings.emailError || 'Email error'));
                        Notification.exception(error);
                        setTimeout(function() {
                            window.location.reload();
                        }, 3000);
                    }
                }]);
            },
            fail: function(error) {
                btn.prop('disabled', false).html('<i class="fa fa-paper-plane me-1"></i> ' +
                    (state.strings.saveAndSend || 'Save and send'));
                Notification.exception(error);
            }
        }]);
    };

    /**
     * Auto-save observation on blur.
     *
     * @param {jQuery.Event} e The blur event.
     */
    var autoSaveObservation = function(e) {
        var $textarea = $(e.target);
        var docId = $textarea.data('document-id');
        var text = $textarea.val();

        Ajax.call([{
            methodname: 'local_jobboard_save_document_observations',
            args: {
                applicationid: state.applicationId,
                observations: JSON.stringify([{documentid: docId, observation: text}])
            },
            done: function() {
                $textarea.addClass('border-success');
                setTimeout(function() {
                    $textarea.removeClass('border-success');
                }, 1000);
            },
            fail: function() {
                // Silent fail - user can retry with save button.
            }
        }]);
    };

    /**
     * Initialize the document review module.
     *
     * @param {Object} config Configuration options.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        config = config || {};
        state.applicationId = config.applicationId || 0;

        // Load strings then set up event handlers.
        loadStrings().then(function() {
            // Document preview clicks.
            $(document).on('click', '[data-region="documents-list"] .list-group-item:not(.disabled)', function(e) {
                if ($(e.target).closest('button, a, .jb-doc-observation').length === 0) {
                    previewDocument(this);
                }
            });

            // Observation auto-save.
            $(document).on('blur', '.jb-doc-observation', autoSaveObservation);

            // Save and send button.
            $('#saveAndSendBtn').on('click', saveAndSendObservations);

            state.initialized = true;
        }).catch(function(error) {
            // eslint-disable-next-line no-console
            console.error('Failed to load strings:', error);
            // Continue without strings - will use fallbacks.
            state.initialized = true;
        });

        // Expose reject function globally for onclick handlers.
        window.jobboardRejectDocument = rejectDocument;
        window.jobboardPreviewDocument = previewDocument;
    };

    return {
        init: init,
        previewDocument: previewDocument,
        rejectDocument: rejectDocument,
        saveAndSendObservations: saveAndSendObservations
    };
});

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
 * Handles document preview, AJAX-based approval/rejection, and observation saving.
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
        initialized: false,
        processing: false
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
            {key: 'saveandsend', component: 'local_jobboard'},
            {key: 'observation_required_for_rejection', component: 'local_jobboard'},
            {key: 'documentvalidated', component: 'local_jobboard'},
            {key: 'documentrejected', component: 'local_jobboard'},
            {key: 'processing', component: 'local_jobboard'},
            {key: 'approve', component: 'local_jobboard'},
            {key: 'reject', component: 'local_jobboard'}
        ]).then(function(strings) {
            state.strings = {
                rejectPrompt: strings[0],
                rejectRequired: strings[1],
                saving: strings[2],
                sending: strings[3],
                sent: strings[4],
                emailError: strings[5],
                saveAndSend: strings[6],
                observationRequired: strings[7],
                documentValidated: strings[8],
                documentRejected: strings[9],
                processing: strings[10],
                approve: strings[11],
                reject: strings[12]
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
        var isText = element.dataset.isText === 'true' || element.dataset.isText === '1';
        var textContent = element.dataset.textContent || '';
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

        // Update download button visibility.
        var downloadBtn = document.querySelector('[data-region="download-btn"]');
        if (downloadBtn) {
            if (downloadUrl) {
                downloadBtn.href = downloadUrl;
                downloadBtn.style.display = '';
            } else {
                downloadBtn.style.display = 'none';
            }
        }

        // Update preview frame.
        var previewFrame = document.querySelector('[data-region="preview-frame"]');
        if (!previewFrame) {
            return;
        }

        if (isText && textContent) {
            // Text document preview - show the text content.
            previewFrame.innerHTML = '<div class="p-4" style="min-height: 500px; overflow-y: auto;">' +
                '<div class="card"><div class="card-header bg-info text-white">' +
                '<i class="fa fa-file-alt me-2"></i>' + (typename || 'Documento de texto') + '</div>' +
                '<div class="card-body"><div class="text-content" style="white-space: pre-wrap;">' +
                textContent + '</div></div></div></div>';
        } else if (previewUrl && (isPdf || isImage)) {
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
     * Update the UI after a document action (approve/reject).
     *
     * @param {int} documentId The document ID that was processed.
     * @param {string} newStatus The new status (approved/rejected).
     * @param {Object} stats The updated stats.
     * @param {int} nextDocumentId The next pending document ID.
     * @param {boolean} allReviewed Whether all documents are reviewed.
     */
    var updateDocumentUI = function(documentId, newStatus, stats, nextDocumentId, allReviewed) {
        // Status configuration.
        var statusConfig = {
            approved: {icon: 'check-circle', color: 'success', label: 'Aprobado'},
            rejected: {icon: 'times-circle', color: 'danger', label: 'Rechazado'},
            pending: {icon: 'clock', color: 'warning', label: 'Pendiente'}
        };

        var config = statusConfig[newStatus] || statusConfig.pending;

        // Find the document list item.
        var docItem = document.querySelector('[data-region="documents-list"] [data-document-id="' + documentId + '"]');
        if (docItem) {
            // Update status badge.
            var statusBadge = docItem.querySelector('.badge.bg-warning, .badge.bg-success, .badge.bg-danger');
            if (statusBadge) {
                statusBadge.className = 'badge bg-' + config.color + ' small flex-shrink-0';
                statusBadge.textContent = config.label;
            }

            // Update status icon.
            var statusIcon = docItem.querySelector('.fa-clock, .fa-check-circle, .fa-times-circle');
            if (statusIcon) {
                statusIcon.className = 'fa fa-' + config.icon + ' text-' + config.color + ' me-2 flex-shrink-0';
            }

            // Remove current state and actions.
            docItem.classList.remove('active', 'bg-primary-subtle');
            var actionsDiv = docItem.querySelector('.jb-doc-actions');
            if (actionsDiv) {
                actionsDiv.remove();
            }
        }

        // Update progress stats.
        var approvedEl = document.querySelector('.h5.text-success');
        var rejectedEl = document.querySelector('.h5.text-danger');
        var pendingEl = document.querySelector('.h5.text-warning');

        if (approvedEl) {
            approvedEl.textContent = stats.approved;
        }
        if (rejectedEl) {
            rejectedEl.textContent = stats.rejected;
        }
        if (pendingEl) {
            pendingEl.textContent = stats.pending;
        }

        // Update progress bars.
        var total = stats.total || 1;
        var approvedPercent = (stats.approved / total) * 100;
        var rejectedPercent = (stats.rejected / total) * 100;

        var approvedBar = document.querySelector('.progress-bar.bg-success');
        var rejectedBar = document.querySelector('.progress-bar.bg-danger');

        if (approvedBar) {
            approvedBar.style.width = approvedPercent + '%';
        }
        if (rejectedBar) {
            rejectedBar.style.width = rejectedPercent + '%';
        }

        // If there's a next document, make it current.
        if (nextDocumentId) {
            var nextDocItem = document.querySelector(
                '[data-region="documents-list"] [data-document-id="' + nextDocumentId + '"]'
            );
            if (nextDocItem) {
                // Load next document preview after a short delay.
                setTimeout(function() {
                    previewDocument(nextDocItem);
                    // Reload page to get updated UI with new current document.
                    window.location.reload();
                }, 500);
            }
        } else if (allReviewed) {
            // All documents reviewed - show success message and reload to show submit form.
            Notification.addNotification({
                message: state.strings.documentValidated || 'All documents reviewed!',
                type: 'success'
            });
            setTimeout(function() {
                window.location.reload();
            }, 1000);
        }
    };

    /**
     * Handle approve button click - AJAX approval.
     *
     * @param {Event} e The click event.
     */
    var handleApproveClick = function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (state.processing) {
            return;
        }

        var btn = e.currentTarget;
        var documentId = parseInt(btn.dataset.documentId, 10);
        var applicationId = parseInt(btn.dataset.applicationId, 10) || state.applicationId;

        if (!documentId || !applicationId) {
            return;
        }

        // Disable buttons and show processing.
        state.processing = true;
        var $btn = $(btn);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> ' +
            (state.strings.processing || 'Processing...'));

        // Also disable reject button.
        var rejectBtn = btn.closest('.d-flex').querySelector('.jb-reject-btn');
        if (rejectBtn) {
            $(rejectBtn).prop('disabled', true);
        }

        Ajax.call([{
            methodname: 'local_jobboard_validate_document',
            args: {documentid: documentId, applicationid: applicationId},
            done: function(response) {
                state.processing = false;
                if (response.success) {
                    Notification.addNotification({
                        message: response.message || state.strings.documentValidated || 'Document approved',
                        type: 'success'
                    });
                    updateDocumentUI(documentId, 'approved', response.stats, response.nextdocumentid, response.allreviewed);
                } else {
                    $btn.prop('disabled', false).html(originalHtml);
                    if (rejectBtn) {
                        $(rejectBtn).prop('disabled', false);
                    }
                    Notification.addNotification({
                        message: response.message || 'Error approving document',
                        type: 'error'
                    });
                }
            },
            fail: function(error) {
                state.processing = false;
                $btn.prop('disabled', false).html(originalHtml);
                if (rejectBtn) {
                    $(rejectBtn).prop('disabled', false);
                }
                Notification.exception(error);
            }
        }]);
    };

    /**
     * Handle reject form submit - validates observation and copies to reason field.
     *
     * @param {Event} e The submit event.
     */
    var handleRejectFormSubmit = function(e) {
        var form = e.currentTarget;
        var documentId = form.dataset.documentId;

        // Find the observation textarea for this document.
        var observationField = document.querySelector('#doc_observation_' + documentId);
        if (!observationField) {
            observationField = document.querySelector('[data-document-id="' + documentId + '"].jb-doc-observation');
        }

        var reason = observationField ? observationField.value.trim() : '';

        if (!reason) {
            // Observation is required for rejection - prevent form submission.
            e.preventDefault();
            e.stopPropagation();

            if (observationField) {
                observationField.classList.add('is-invalid', 'border-danger');
                observationField.focus();
                // Add shake animation.
                observationField.classList.add('jb-shake');
                setTimeout(function() {
                    observationField.classList.remove('jb-shake');
                }, 500);
            }
            // Show error message using notification.
            Notification.addNotification({
                message: state.strings.observationRequired || 'You must enter an observation to reject the document.',
                type: 'error'
            });
            return false;
        }

        // Remove validation styling if present.
        if (observationField) {
            observationField.classList.remove('is-invalid', 'border-danger');
        }

        // Copy observation to the hidden reason field in the form.
        var reasonInput = form.querySelector('.jb-reject-reason-input');
        if (reasonInput) {
            reasonInput.value = reason;
        }

        // Allow form submission to proceed.
        return true;
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
     * Set up all event handlers.
     */
    var setupEventHandlers = function() {
        // Document preview clicks.
        $(document).on('click', '[data-region="documents-list"] .list-group-item:not(.disabled)', function(e) {
            if ($(e.target).closest('button, a, .jb-doc-observation').length === 0) {
                previewDocument(this);
            }
        });

        // Observation auto-save.
        $(document).on('blur', '.jb-doc-observation', autoSaveObservation);

        // Clear validation styling on observation input.
        $(document).on('input', '.jb-doc-observation', function() {
            $(this).removeClass('is-invalid border-danger');
        });

        // Reject form submit handler - validates observation and copies to reason field.
        $(document).on('submit', '.jb-reject-form', handleRejectFormSubmit);

        // Save and send button.
        $('#saveAndSendBtn').on('click', saveAndSendObservations);
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

        // Set up event handlers immediately (don't wait for strings).
        setupEventHandlers();
        state.initialized = true;

        // Load strings asynchronously - handlers will use fallbacks if strings aren't ready.
        loadStrings().catch(function(error) {
            // eslint-disable-next-line no-console
            console.error('Failed to load strings:', error);
            // Handlers already set up, will use fallback strings.
        });

        // Expose preview function globally for onclick handlers.
        window.jobboardPreviewDocument = previewDocument;
    };

    return {
        init: init,
        previewDocument: previewDocument,
        saveAndSendObservations: saveAndSendObservations
    };
});

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
        isAdmin: false,
        bypassSequentialReview: false,
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
            {key: 'reject', component: 'local_jobboard'},
            {key: 'ok', component: 'core'},
            {key: 'observation_required_title', component: 'local_jobboard'}
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
                reject: strings[12],
                ok: strings[13],
                observationRequiredTitle: strings[14]
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
        // Decode base64 encoded text content if present.
        var textContentEncoded = element.dataset.textContentEncoded || '';
        var textContent = '';
        if (textContentEncoded) {
            try {
                textContent = atob(textContentEncoded);
            } catch (e) {
                // If decoding fails, try using the raw value.
                textContent = textContentEncoded;
            }
        }
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
            // Create wrapper elements safely.
            var wrapper = document.createElement('div');
            wrapper.className = 'p-4';
            wrapper.style.minHeight = '500px';
            wrapper.style.overflowY = 'auto';

            var card = document.createElement('div');
            card.className = 'card';

            var cardHeader = document.createElement('div');
            cardHeader.className = 'card-header bg-info text-white';
            cardHeader.innerHTML = '<i class="fa fa-file-alt me-2"></i>' +
                (typename ? typename.replace(/</g, '&lt;').replace(/>/g, '&gt;') : 'Documento de texto');

            var cardBody = document.createElement('div');
            cardBody.className = 'card-body';

            var contentDiv = document.createElement('div');
            contentDiv.className = 'text-content';
            contentDiv.style.whiteSpace = 'pre-wrap';
            // Render text content as HTML (from WYSIWYG editor) - sanitize basic XSS patterns.
            contentDiv.innerHTML = textContent;

            cardBody.appendChild(contentDiv);
            card.appendChild(cardHeader);
            card.appendChild(cardBody);
            wrapper.appendChild(card);
            previewFrame.innerHTML = '';
            previewFrame.appendChild(wrapper);
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
     * Update document UI with stored DOM references.
     * This version receives pre-stored references to avoid issues when user navigates during AJAX.
     *
     * @param {HTMLElement} docItem The document list item element (stored before AJAX).
     * @param {HTMLElement} actionsDiv The actions div element (stored before AJAX).
     * @param {int} documentId The document ID.
     * @param {string} newStatus The new status.
     * @param {Object} stats The updated stats.
     * @param {int} nextDocumentId The next pending document ID.
     * @param {boolean} allReviewed Whether all documents are reviewed.
     */
    var updateDocumentUIWithRefs = function(docItem, actionsDiv, documentId, newStatus, stats, nextDocumentId, allReviewed) {
        // eslint-disable-next-line no-console
        console.log('updateDocumentUIWithRefs called:', {
            documentId: documentId,
            newStatus: newStatus,
            nextDocumentId: nextDocumentId,
            allReviewed: allReviewed,
            hasDocItem: !!docItem,
            hasActionsDiv: !!actionsDiv
        });

        // Status configuration.
        var statusConfig = {
            approved: {icon: 'check-circle', color: 'success', label: 'Aprobado'},
            rejected: {icon: 'times-circle', color: 'danger', label: 'Rechazado'},
            pending: {icon: 'clock', color: 'warning', label: 'Pendiente'}
        };

        var config = statusConfig[newStatus] || statusConfig.pending;

        // Use stored reference if available, otherwise query.
        if (!docItem) {
            var selector = '[data-region="documents-list"] .list-group-item[data-document-id="' + documentId + '"]';
            docItem = document.querySelector(selector);
        }

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

            // Remove current state.
            docItem.classList.remove('active', 'bg-primary-subtle');
        }

        // Remove actions div using stored reference.
        if (actionsDiv && actionsDiv.parentNode) {
            actionsDiv.remove();
            // eslint-disable-next-line no-console
            console.log('actionsDiv removed using stored reference');
        } else if (docItem) {
            // Fallback: try to find actionsDiv inside docItem.
            var foundActionsDiv = docItem.querySelector('.jb-doc-actions');
            if (foundActionsDiv) {
                foundActionsDiv.remove();
                // eslint-disable-next-line no-console
                console.log('actionsDiv removed using fallback query');
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
                // Load next document preview and make it current without reload.
                setTimeout(function() {
                    makeDocumentCurrent(nextDocItem, nextDocumentId);
                    previewDocument(nextDocItem);
                }, 300);
            }
        } else if (allReviewed) {
            // All documents reviewed - show success message.
            Notification.addNotification({
                message: state.strings.documentValidated || 'All documents reviewed!',
                type: 'success'
            });
            // Update the UI to show all reviewed state.
            updateAllReviewedState();
        }

        // Update sequential review info.
        updateSequentialReviewInfo(stats);
    };

    /**
     * Make a document the current one for review (add action buttons).
     *
     * @param {HTMLElement} docItem The document list item element.
     * @param {int} documentId The document ID.
     */
    var makeDocumentCurrent = function(docItem, documentId) {
        // Remove current styling from all items.
        document.querySelectorAll('[data-region="documents-list"] .list-group-item').forEach(function(item) {
            item.classList.remove('active', 'bg-primary-subtle');
            var actions = item.querySelector('.jb-doc-actions');
            if (actions) {
                actions.remove();
            }
        });

        // Remove disabled state from this item (it may have been locked when page loaded).
        docItem.classList.remove('disabled', 'opacity-50');
        docItem.style.cursor = 'pointer';

        // Ensure the onclick handler is set for preview functionality.
        if (!docItem.hasAttribute('onclick')) {
            docItem.setAttribute('onclick', 'jobboardPreviewDocument(this)');
        }

        // Add current styling to this item.
        docItem.classList.add('active', 'bg-primary-subtle');

        // Get application ID.
        var appId = state.applicationId;

        // Create action buttons HTML.
        var actionsHtml = '<div class="mt-2 pt-2 border-top jb-doc-actions" ' +
            'data-document-id="' + documentId + '" data-application-id="' + appId + '">' +
            '<label for="doc_observation_' + documentId + '" class="form-label small fw-bold mb-1">' +
            (state.strings.documentObservation || 'Observation') +
            ' <span class="text-danger" title="Required for rejection">*</span></label>' +
            '<textarea name="doc_observation_' + documentId + '" id="doc_observation_' + documentId + '" ' +
            'class="form-control form-control-sm jb-doc-observation mb-2" rows="2" ' +
            'data-document-id="' + documentId + '" data-application-id="' + appId + '" ' +
            'placeholder="Enter observation..."></textarea>' +
            '<div class="d-flex gap-2">' +
            '<button type="button" class="btn btn-success jb-btn-success btn-sm flex-grow-1 jb-approve-btn" ' +
            'data-document-id="' + documentId + '" data-application-id="' + appId + '">' +
            '<i class="fa fa-check me-1"></i>' + (state.strings.approve || 'Approve') + '</button>' +
            '<button type="button" class="btn btn-danger jb-btn-danger btn-sm flex-grow-1 jb-reject-btn" ' +
            'data-document-id="' + documentId + '" data-application-id="' + appId + '">' +
            '<i class="fa fa-xmark me-1"></i>' + (state.strings.reject || 'Reject') + '</button>' +
            '</div>' +
            '<small class="text-muted d-block mt-1">' +
            '<i class="fa fa-info-circle me-1"></i>' +
            (state.strings.observationRequired || 'Observation required for rejection') +
            '</small></div>';

        // Append actions to doc item.
        docItem.insertAdjacentHTML('beforeend', actionsHtml);
    };

    /**
     * Update the UI to show all documents reviewed state.
     */
    var updateAllReviewedState = function() {
        // Hide pending alert.
        var pendingAlert = document.querySelector('.alert-info.jb-alert-info');
        if (pendingAlert) {
            pendingAlert.style.display = 'none';
        }

        // Show success alert if not already showing.
        var successAlert = document.querySelector('.alert-success.jb-alert-success');
        if (!successAlert) {
            var alertContainer = document.querySelector('.col-lg-8 .jb-page-header');
            if (alertContainer) {
                var newAlert = document.createElement('div');
                newAlert.className = 'alert alert-success jb-alert-success d-flex align-items-center mb-4';
                newAlert.setAttribute('role', 'alert');
                newAlert.innerHTML = '<i class="fa fa-check-circle fa-lg me-3"></i><div>' +
                    '<strong>All documents reviewed!</strong>' +
                    '<span class="d-block small">Ready to submit review.</span></div>';
                alertContainer.parentNode.insertBefore(newAlert, alertContainer.nextSibling);
            }
        } else {
            successAlert.style.display = 'flex';
        }

        // Show submit form in the progress card.
        var progressCard = document.querySelector('.card-body .mb-2 textarea[name="observations"]');
        if (progressCard) {
            progressCard.closest('.card').querySelector('form').style.display = 'block';
        }
    };

    /**
     * Update sequential review info display.
     *
     * @param {Object} stats The current stats.
     */
    var updateSequentialReviewInfo = function(stats) {
        var currentPos = (stats.approved || 0) + (stats.rejected || 0) + 1;
        var total = stats.total || 0;
        var displayPos = Math.min(currentPos, total);

        // Update the badge.
        var badge = document.querySelector('.alert-info.jb-alert-info .badge.bg-primary');
        if (badge) {
            badge.textContent = displayPos + ' / ' + total;
        }

        // Update the text "Revisando documento X de Y" by replacing numbers.
        var infoText = document.querySelector('.alert-info.jb-alert-info .d-block.small');
        if (infoText) {
            // Replace the numbers in the text (e.g., "Revisando documento 11 de 14" -> "Revisando documento 12 de 14").
            var text = infoText.textContent;
            // Match pattern: text number text number (handles different languages).
            text = text.replace(/(\D+)(\d+)(\D+)(\d+)/, '$1' + displayPos + '$3' + total);
            infoText.textContent = text;
        }
    };

    /**
     * Handle approve button click - Pure AJAX approval.
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
            Notification.addNotification({
                message: 'Error: Missing document or application ID',
                type: 'error'
            });
            return;
        }

        // Disable buttons and show processing.
        state.processing = true;
        var $btn = $(btn);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> ' +
            (state.strings.processing || 'Procesando...'));

        // Also disable reject button.
        var rejectBtn = btn.closest('.d-flex') ? btn.closest('.d-flex').querySelector('.jb-reject-btn') : null;
        if (rejectBtn) {
            $(rejectBtn).prop('disabled', true);
        }

        // IMPORTANT: Store references to DOM elements BEFORE the AJAX call.
        var actionsDiv = btn.closest('.jb-doc-actions');
        var docItem = actionsDiv ? actionsDiv.closest('.list-group-item') : null;

        Ajax.call([{
            methodname: 'local_jobboard_validate_document',
            args: {documentid: documentId, applicationid: applicationId},
            done: function(response) {
                // eslint-disable-next-line no-console
                console.log('Approve response:', response);
                state.processing = false;
                if (response.success) {
                    Notification.addNotification({
                        message: response.message || state.strings.documentValidated || 'Documento aprobado',
                        type: 'success'
                    });

                    // Update UI without page reload.
                    // 1. Find and update the current document in the list.
                    var currentDocItem = document.querySelector(
                        '[data-region="documents-list"] .list-group-item[data-document-id="' + documentId + '"]'
                    );
                    if (currentDocItem) {
                        // Update numbered badge (first badge - rounded-pill) to success color.
                        var numberBadge = currentDocItem.querySelector('.badge.rounded-pill');
                        if (numberBadge) {
                            numberBadge.className = 'badge rounded-pill bg-success me-2 flex-shrink-0';
                        }
                        // Update status badge (last badge in the flex container) to approved.
                        var statusBadge = currentDocItem.querySelector(
                            '.d-flex.justify-content-between > .badge'
                        );
                        if (statusBadge) {
                            statusBadge.className = 'badge bg-success small flex-shrink-0';
                            statusBadge.textContent = 'Aprobado';
                        }
                        // Update icon.
                        var icon = currentDocItem.querySelector('.fa-clock, .fa-check-circle, .fa-times-circle');
                        if (icon) {
                            icon.className = 'fa fa-check-circle text-success me-2 flex-shrink-0';
                        }
                        // Remove active state.
                        currentDocItem.classList.remove('active', 'bg-primary-subtle');
                    }

                    // 2. Remove action divs.
                    // For admins: only remove the current document's actions.
                    // For regular users: remove all actions (sequential review).
                    if (state.bypassSequentialReview) {
                        // Admin mode: only remove actions from the just-approved document.
                        var currentApproveActions = currentDocItem ? currentDocItem.querySelector('.jb-doc-actions') : null;
                        if (currentApproveActions) {
                            currentApproveActions.remove();
                        }
                    } else {
                        // Sequential mode: remove all action divs.
                        document.querySelectorAll('.jb-doc-actions, .editor_atto_wrap[data-document-id]').forEach(function(el) {
                            el.remove();
                        });
                    }

                    // 3. Update stats and progress.
                    var stats = response.stats;
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
                    var approvedPercent = Math.round((stats.approved / total) * 100);
                    var rejectedPercent = Math.round((stats.rejected / total) * 100);
                    var approvedBar = document.querySelector('.progress .progress-bar.bg-success');
                    var rejectedBar = document.querySelector('.progress .progress-bar.bg-danger');
                    if (approvedBar) {
                        approvedBar.style.width = approvedPercent + '%';
                    }
                    if (rejectedBar) {
                        rejectedBar.style.width = rejectedPercent + '%';
                    }

                    // Update sequential review info.
                    updateSequentialReviewInfo(stats);

                    // 4. Move to next document or show completion.
                    if (response.nextdocumentid) {
                        var nextDocItem = document.querySelector(
                            '[data-region="documents-list"] .list-group-item[data-document-id="' + response.nextdocumentid + '"]'
                        );
                        if (nextDocItem) {
                            setTimeout(function() {
                                makeDocumentCurrent(nextDocItem, response.nextdocumentid);
                                previewDocument(nextDocItem);
                            }, 300);
                        }
                    } else if (response.allreviewed) {
                        updateAllReviewedState();
                    }
                } else {
                    // AJAX returned error - show message and re-enable buttons.
                    $btn.prop('disabled', false).html(originalHtml);
                    if (rejectBtn) {
                        $(rejectBtn).prop('disabled', false);
                    }
                    Notification.addNotification({
                        message: response.message || 'Error al aprobar el documento',
                        type: 'error'
                    });
                }
            },
            fail: function(error) {
                // AJAX failed - show error and re-enable buttons.
                state.processing = false;
                $btn.prop('disabled', false).html(originalHtml);
                if (rejectBtn) {
                    $(rejectBtn).prop('disabled', false);
                }
                // eslint-disable-next-line no-console
                console.error('AJAX error:', error);
                Notification.addNotification({
                    message: 'Error de conexión. Por favor, recargue la página e intente de nuevo.',
                    type: 'error'
                });
            }
        }]);
    };

    /**
     * Handle reject button click - Pure AJAX rejection with observation validation.
     *
     * @param {Event} e The click event.
     */
    var handleRejectClick = function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (state.processing) {
            return;
        }

        var btn = e.currentTarget;
        var documentId = parseInt(btn.dataset.documentId, 10);
        var applicationId = parseInt(btn.dataset.applicationId, 10) || state.applicationId;

        if (!documentId || !applicationId) {
            Notification.addNotification({
                message: 'Error: Missing document or application ID',
                type: 'error'
            });
            return;
        }

        // Find the observation textarea for this document.
        var observationField = document.querySelector('#doc_observation_' + documentId);
        if (!observationField) {
            observationField = document.querySelector('[data-document-id="' + documentId + '"].jb-doc-observation');
        }

        // Get observation value - handle TinyMCE editor if present.
        var reason = '';
        if (observationField) {
            var editorId = observationField.id;
            // Check if TinyMCE is managing this field.
            if (window.tinyMCE && window.tinyMCE.get(editorId)) {
                var editor = window.tinyMCE.get(editorId);
                // Save editor content to textarea first.
                editor.save();
                // Get plain text content (strip HTML tags).
                reason = editor.getContent({format: 'text'}).trim();
            } else {
                reason = observationField.value.trim();
            }
        }

        if (!reason) {
            // Observation is required for rejection - show popup alert.
            if (observationField) {
                observationField.classList.add('is-invalid', 'border-danger');
                observationField.focus();
                // Add shake animation.
                observationField.classList.add('jb-shake');
                setTimeout(function() {
                    observationField.classList.remove('jb-shake');
                }, 500);
            }
            // Show popup alert for missing observation.
            Notification.alert(
                state.strings.observationRequiredTitle || 'Observación requerida',
                state.strings.observationRequired || 'Debe ingresar una observación para rechazar el documento.',
                state.strings.ok || 'Aceptar'
            );
            return;
        }

        // Remove validation styling if present.
        if (observationField) {
            observationField.classList.remove('is-invalid', 'border-danger');
        }

        // Disable buttons and show processing.
        state.processing = true;
        var $btn = $(btn);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> ' +
            (state.strings.processing || 'Procesando...'));

        // Also disable approve button.
        var approveBtn = btn.closest('.d-flex') ? btn.closest('.d-flex').querySelector('.jb-approve-btn') : null;
        if (approveBtn) {
            $(approveBtn).prop('disabled', true);
        }

        // IMPORTANT: Store references to DOM elements BEFORE the AJAX call.
        // This prevents issues if user clicks on another document while AJAX is pending.
        var actionsDiv = btn.closest('.jb-doc-actions');
        var docItem = actionsDiv ? actionsDiv.closest('.list-group-item') : null;

        // eslint-disable-next-line no-console
        console.log('Reject click - btn:', btn);
        // eslint-disable-next-line no-console
        console.log('Reject click - actionsDiv:', actionsDiv);
        // eslint-disable-next-line no-console
        console.log('Reject click - docItem:', docItem);
        // eslint-disable-next-line no-console
        console.log('Reject click - btn.parentElement:', btn.parentElement);
        // eslint-disable-next-line no-console
        console.log('Reject click - btn.parentElement.parentElement:', btn.parentElement ? btn.parentElement.parentElement : null);

        Ajax.call([{
            methodname: 'local_jobboard_reject_document',
            args: {documentid: documentId, applicationid: applicationId, reason: reason},
            done: function(response) {
                // eslint-disable-next-line no-console
                console.log('Reject response:', response);
                state.processing = false;
                if (response.success) {
                    Notification.addNotification({
                        message: response.message || state.strings.documentRejected || 'Documento rechazado',
                        type: 'success'
                    });

                    // Update UI without page reload.
                    // 1. Find and update the current document in the list.
                    var currentDocItem = document.querySelector(
                        '[data-region="documents-list"] .list-group-item[data-document-id="' + documentId + '"]'
                    );
                    if (currentDocItem) {
                        // Update numbered badge (first badge - rounded-pill) to danger color.
                        var numberBadge = currentDocItem.querySelector('.badge.rounded-pill');
                        if (numberBadge) {
                            numberBadge.className = 'badge rounded-pill bg-danger me-2 flex-shrink-0';
                        }
                        // Update status badge (last badge in the flex container) to rejected.
                        var statusBadge = currentDocItem.querySelector(
                            '.d-flex.justify-content-between > .badge'
                        );
                        if (statusBadge) {
                            statusBadge.className = 'badge bg-danger small flex-shrink-0';
                            statusBadge.textContent = 'Rechazado';
                        }
                        // Update icon.
                        var icon = currentDocItem.querySelector('.fa-clock, .fa-check-circle, .fa-times-circle');
                        if (icon) {
                            icon.className = 'fa fa-times-circle text-danger me-2 flex-shrink-0';
                        }
                        // Remove active state.
                        currentDocItem.classList.remove('active', 'bg-primary-subtle');
                    }

                    // 2. Remove action divs.
                    // For admins: only remove the current document's actions.
                    // For regular users: remove all actions (sequential review).
                    if (state.bypassSequentialReview) {
                        // Admin mode: only remove actions from the just-rejected document.
                        var currentRejectActions = currentDocItem ? currentDocItem.querySelector('.jb-doc-actions') : null;
                        if (currentRejectActions) {
                            currentRejectActions.remove();
                        }
                    } else {
                        // Sequential mode: remove all action divs.
                        document.querySelectorAll('.jb-doc-actions, .editor_atto_wrap[data-document-id]').forEach(function(el) {
                            el.remove();
                        });
                    }

                    // 3. Update stats and progress.
                    var stats = response.stats;
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
                    var approvedPercent = Math.round((stats.approved / total) * 100);
                    var rejectedPercent = Math.round((stats.rejected / total) * 100);
                    var approvedBar = document.querySelector('.progress .progress-bar.bg-success');
                    var rejectedBar = document.querySelector('.progress .progress-bar.bg-danger');
                    if (approvedBar) {
                        approvedBar.style.width = approvedPercent + '%';
                    }
                    if (rejectedBar) {
                        rejectedBar.style.width = rejectedPercent + '%';
                    }

                    // Update sequential review info.
                    updateSequentialReviewInfo(stats);

                    // 4. Move to next document or show completion.
                    if (response.nextdocumentid) {
                        var nextDocItem = document.querySelector(
                            '[data-region="documents-list"] .list-group-item[data-document-id="' + response.nextdocumentid + '"]'
                        );
                        if (nextDocItem) {
                            setTimeout(function() {
                                makeDocumentCurrent(nextDocItem, response.nextdocumentid);
                                previewDocument(nextDocItem);
                            }, 300);
                        }
                    } else if (response.allreviewed) {
                        updateAllReviewedState();
                    }
                } else {
                    // AJAX returned error - show message and re-enable buttons.
                    $btn.prop('disabled', false).html(originalHtml);
                    if (approveBtn) {
                        $(approveBtn).prop('disabled', false);
                    }
                    Notification.addNotification({
                        message: response.message || 'Error al rechazar el documento',
                        type: 'error'
                    });
                }
            },
            fail: function(error) {
                // AJAX failed - show error and re-enable buttons.
                state.processing = false;
                $btn.prop('disabled', false).html(originalHtml);
                if (approveBtn) {
                    $(approveBtn).prop('disabled', false);
                }
                // eslint-disable-next-line no-console
                console.error('AJAX error:', error);
                Notification.addNotification({
                    message: 'Error de conexión. Por favor, recargue la página e intente de nuevo.',
                    type: 'error'
                });
            }
        }]);
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
            // Don't trigger preview if clicking on action buttons, links, observations, or inside action area.
            if ($(e.target).closest('button, a, .jb-doc-observation, .jb-doc-actions').length === 0) {
                previewDocument(this);
            }
        });

        // Observation auto-save.
        $(document).on('blur', '.jb-doc-observation', autoSaveObservation);

        // Clear validation styling on observation input.
        $(document).on('input', '.jb-doc-observation', function() {
            $(this).removeClass('is-invalid border-danger');
        });

        // Approve button click handler - Pure AJAX.
        $(document).on('click', '.jb-approve-btn', handleApproveClick);

        // Reject button click handler - Pure AJAX.
        $(document).on('click', '.jb-reject-btn', handleRejectClick);

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
        state.isAdmin = config.isAdmin || false;
        state.bypassSequentialReview = config.bypassSequentialReview || false;

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

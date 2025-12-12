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
 * Grading panel module for mod_assign-style document review.
 *
 * Provides AJAX navigation, keyboard shortcuts, and inline document validation.
 *
 * @module     local_jobboard/grading_panel
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/ajax',
    'core/notification',
    'core/str',
    'core/templates',
    'core/modal_factory',
    'core/modal_events'
], function($, Ajax, Notification, Str, Templates, ModalFactory, ModalEvents) {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        applicationId: null,
        documentId: null,
        sesskey: null,
        canValidate: false,
        applications: [],
        documents: [],
        currentAppIndex: 0,
        currentDocIndex: 0,
        sidebarCollapsed: false,
        initialized: false
    };

    /**
     * DOM selectors.
     * @type {Object}
     */
    var SELECTORS = {
        panel: '[data-region="grading-panel"]',
        sidebar: '#jb-grading-sidebar',
        applicationList: '.jb-application-list',
        applicationItem: '.jb-application-item',
        documentPanel: '#jb-document-panel',
        documentList: '.jb-document-list',
        documentItem: '.jb-document-item',
        previewPanel: '#jb-preview-panel',
        previewContent: '#jb-preview-content',
        loadingOverlay: '#jb-loading-overlay',
        shortcutsModal: '#jb-shortcuts-modal',
        rejectReason: '#jb-reject-reason',
        filterInput: '#jb-application-filter',
        navPosition: '.jb-nav-position'
    };

    /**
     * Action data attributes.
     * @type {Object}
     */
    var ACTIONS = {
        toggleSidebar: '[data-action="toggle-sidebar"], [data-action="sidebar-toggle"]',
        previous: '[data-action="previous"]',
        next: '[data-action="next"]',
        loadApplication: '[data-action="load-application"]',
        loadDocument: '[data-action="load-document"]',
        approveDocument: '[data-action="approve-document"]',
        rejectDocument: '[data-action="reject-document"]',
        approveAll: '[data-action="approve-all"]',
        fullscreen: '[data-action="fullscreen"]',
        showShortcuts: '[data-action="show-shortcuts"]'
    };

    /**
     * Keyboard shortcut definitions.
     * @type {Object}
     */
    var SHORTCUTS = {
        'n': 'nextDocument',
        'p': 'previousDocument',
        'a': 'approveCurrentDocument',
        'r': 'focusRejectReason',
        'd': 'downloadCurrentDocument',
        'f': 'toggleFullscreen',
        's': 'toggleSidebar',
        'ArrowUp': 'previousDocument',
        'ArrowDown': 'nextDocument',
        'Escape': 'exitGrading',
        '?': 'showShortcuts'
    };

    /**
     * Show loading overlay.
     */
    var showLoading = function() {
        $(SELECTORS.loadingOverlay).removeClass('jb-d-none');
    };

    /**
     * Hide loading overlay.
     */
    var hideLoading = function() {
        $(SELECTORS.loadingOverlay).addClass('jb-d-none');
    };

    /**
     * Toggle sidebar visibility.
     */
    var toggleSidebar = function() {
        var $sidebar = $(SELECTORS.sidebar);
        state.sidebarCollapsed = !state.sidebarCollapsed;

        if (state.sidebarCollapsed) {
            $sidebar.addClass('jb-collapsed');
            $sidebar.find('[data-action="toggle-sidebar"]').attr('aria-expanded', 'false');
        } else {
            $sidebar.removeClass('jb-collapsed');
            $sidebar.find('[data-action="toggle-sidebar"]').attr('aria-expanded', 'true');
        }
    };

    /**
     * Load application via AJAX.
     *
     * @param {number} applicationId Application ID to load.
     * @param {number} [documentId] Optional document ID to select.
     */
    var loadApplication = function(applicationId, documentId) {
        showLoading();

        var request = {
            methodname: 'local_jobboard_get_application_review_data',
            args: {
                applicationid: applicationId,
                documentid: documentId || 0
            }
        };

        Ajax.call([request])[0]
            .then(function(response) {
                if (response.success) {
                    return updatePanel(response.data);
                } else {
                    throw new Error(response.message || 'Failed to load application');
                }
            })
            .then(function() {
                state.applicationId = applicationId;
                updateApplicationSelection(applicationId);
                updateUrl(applicationId, documentId);
                hideLoading();
            })
            .catch(function(error) {
                hideLoading();
                Notification.exception(error);
            });
    };

    /**
     * Load document preview via AJAX.
     *
     * @param {number} documentId Document ID to load.
     */
    var loadDocument = function(documentId) {
        showLoading();

        var request = {
            methodname: 'local_jobboard_get_document_preview_data',
            args: {
                documentid: documentId
            }
        };

        Ajax.call([request])[0]
            .then(function(response) {
                if (response.success) {
                    return updatePreviewPanel(response.data);
                } else {
                    throw new Error(response.message || 'Failed to load document');
                }
            })
            .then(function() {
                state.documentId = documentId;
                updateDocumentSelection(documentId);
                updateUrl(state.applicationId, documentId);
                hideLoading();
            })
            .catch(function(error) {
                hideLoading();
                Notification.exception(error);
            });
    };

    /**
     * Update the main panel with new data.
     *
     * @param {Object} data Application review data.
     * @return {Promise}
     */
    var updatePanel = function(data) {
        return Templates.render('local_jobboard/grading_panel_content', data)
            .then(function(html) {
                $(SELECTORS.panel).find('.jb-grading-content').html(html);
                // Update navigation
                $(SELECTORS.navPosition).html('<strong>' + data.currentindex + '</strong> / ' + data.totalcount);
                // Update applicant info
                $(SELECTORS.panel).find('.jb-applicant-info').html(
                    '<strong class="jb-fs-6">' + data.applicant.fullname + '</strong>' +
                    '<span class="jb-badge jb-badge-secondary jb-ml-2">' + data.vacancy.code + '</span>'
                );
                return;
            });
    };

    /**
     * Update the preview panel with document data.
     *
     * @param {Object} data Document preview data.
     * @return {Promise}
     */
    var updatePreviewPanel = function(data) {
        return Templates.render('local_jobboard/grading_preview', data)
            .then(function(html) {
                $(SELECTORS.previewPanel).html(html);
                return;
            });
    };

    /**
     * Update application selection in sidebar.
     *
     * @param {number} applicationId Selected application ID.
     */
    var updateApplicationSelection = function(applicationId) {
        $(SELECTORS.applicationItem).each(function() {
            var $item = $(this);
            var isSelected = $item.data('application-id') === applicationId;

            $item.toggleClass('jb-active', isSelected)
                .attr('aria-selected', isSelected ? 'true' : 'false')
                .attr('tabindex', isSelected ? '0' : '-1');
        });
    };

    /**
     * Update document selection in list.
     *
     * @param {number} documentId Selected document ID.
     */
    var updateDocumentSelection = function(documentId) {
        $(SELECTORS.documentItem).each(function() {
            var $item = $(this);
            var isSelected = $item.data('document-id') === documentId;

            $item.toggleClass('jb-active', isSelected)
                .attr('aria-selected', isSelected ? 'true' : 'false')
                .attr('tabindex', isSelected ? '0' : '-1');
        });

        // Update current document index
        var $items = $(SELECTORS.documentItem);
        $items.each(function(index) {
            if ($(this).data('document-id') === documentId) {
                state.currentDocIndex = index;
                return false;
            }
        });
    };

    /**
     * Update URL without page reload.
     *
     * @param {number} applicationId Application ID.
     * @param {number} [documentId] Document ID.
     */
    var updateUrl = function(applicationId, documentId) {
        var url = new URL(window.location);
        url.searchParams.set('applicationid', applicationId);
        if (documentId) {
            url.searchParams.set('documentid', documentId);
        } else {
            url.searchParams.delete('documentid');
        }
        window.history.replaceState({}, '', url);
    };

    /**
     * Navigate to next document.
     */
    var nextDocument = function() {
        var $items = $(SELECTORS.documentItem);
        if (state.currentDocIndex < $items.length - 1) {
            var $nextItem = $items.eq(state.currentDocIndex + 1);
            loadDocument($nextItem.data('document-id'));
        }
    };

    /**
     * Navigate to previous document.
     */
    var previousDocument = function() {
        if (state.currentDocIndex > 0) {
            var $items = $(SELECTORS.documentItem);
            var $prevItem = $items.eq(state.currentDocIndex - 1);
            loadDocument($prevItem.data('document-id'));
        }
    };

    /**
     * Navigate to next application.
     */
    var nextApplication = function() {
        var $nextBtn = $(ACTIONS.next);
        if (!$nextBtn.prop('disabled')) {
            var $currentItem = $(SELECTORS.applicationItem + '.jb-active');
            var $nextItem = $currentItem.next(SELECTORS.applicationItem);
            if ($nextItem.length) {
                loadApplication($nextItem.data('application-id'));
            }
        }
    };

    /**
     * Navigate to previous application.
     */
    var previousApplication = function() {
        var $prevBtn = $(ACTIONS.previous);
        if (!$prevBtn.prop('disabled')) {
            var $currentItem = $(SELECTORS.applicationItem + '.jb-active');
            var $prevItem = $currentItem.prev(SELECTORS.applicationItem);
            if ($prevItem.length) {
                loadApplication($prevItem.data('application-id'));
            }
        }
    };

    /**
     * Approve current document.
     */
    var approveCurrentDocument = function() {
        if (!state.canValidate || !state.documentId) {
            return;
        }

        var $approveBtn = $(ACTIONS.approveDocument);
        if ($approveBtn.length && !$approveBtn.prop('disabled')) {
            validateDocument(state.documentId, 'approved', '');
        }
    };

    /**
     * Focus reject reason select.
     */
    var focusRejectReason = function() {
        $(SELECTORS.rejectReason).focus();
    };

    /**
     * Download current document.
     */
    var downloadCurrentDocument = function() {
        var $downloadLink = $(SELECTORS.previewPanel).find('a[href*="download"]');
        if ($downloadLink.length) {
            window.open($downloadLink.attr('href'), '_blank');
        }
    };

    /**
     * Toggle fullscreen for preview.
     */
    var toggleFullscreen = function() {
        var $previewPanel = $(SELECTORS.previewPanel);
        $previewPanel.toggleClass('jb-fullscreen');

        if ($previewPanel.hasClass('jb-fullscreen')) {
            if (document.documentElement.requestFullscreen) {
                $previewPanel[0].requestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    };

    /**
     * Show keyboard shortcuts modal.
     */
    var showShortcuts = function() {
        $(SELECTORS.shortcutsModal).modal('show');
    };

    /**
     * Exit grading panel.
     */
    var exitGrading = function() {
        var $exitLink = $(SELECTORS.panel).find('a[href*="review"]').first();
        if ($exitLink.length) {
            window.location.href = $exitLink.attr('href');
        }
    };

    /**
     * Validate a document via AJAX.
     *
     * @param {number} documentId Document ID.
     * @param {string} status New status (approved/rejected).
     * @param {string} reason Rejection reason (if rejecting).
     * @param {string} [notes] Additional notes.
     */
    var validateDocument = function(documentId, status, reason, notes) {
        showLoading();

        var request = {
            methodname: 'local_jobboard_validate_document',
            args: {
                documentid: documentId,
                status: status,
                reason: reason || '',
                notes: notes || '',
                sesskey: state.sesskey
            }
        };

        Ajax.call([request])[0]
            .then(function(response) {
                if (response.success) {
                    // Update UI
                    updateDocumentStatus(documentId, status);
                    // Show notification
                    return Str.get_string('document_' + status, 'local_jobboard');
                } else {
                    throw new Error(response.message || 'Validation failed');
                }
            })
            .then(function(message) {
                Notification.addNotification({
                    message: message,
                    type: status === 'approved' ? 'success' : 'warning'
                });
                hideLoading();

                // Auto-advance to next pending document
                var $nextPending = $(SELECTORS.documentItem + '[data-status="pending"]').first();
                if ($nextPending.length && $nextPending.data('document-id') !== documentId) {
                    loadDocument($nextPending.data('document-id'));
                } else {
                    // Reload current document to update UI
                    loadDocument(documentId);
                }
            })
            .catch(function(error) {
                hideLoading();
                Notification.exception(error);
            });
    };

    /**
     * Update document status in the list.
     *
     * @param {number} documentId Document ID.
     * @param {string} status New status.
     */
    var updateDocumentStatus = function(documentId, status) {
        var $item = $(SELECTORS.documentItem + '[data-document-id="' + documentId + '"]');
        $item.removeClass('jb-doc-pending jb-doc-approved jb-doc-rejected')
            .addClass('jb-doc-' + status)
            .attr('data-status', status);

        // Update icon
        var iconClass = status === 'approved' ? 'fa-check jb-text-success' :
            (status === 'rejected' ? 'fa-times jb-text-danger' : 'fa-clock jb-text-warning');
        $item.find('.jb-doc-status-icon i')
            .removeClass('fa-check fa-times fa-clock jb-text-success jb-text-danger jb-text-warning')
            .addClass(iconClass);

        // Update badge
        if (status !== 'pending') {
            $item.find('.jb-doc-actions .jb-badge').remove();
        }

        // Update progress bar
        updateProgressBar();
    };

    /**
     * Update progress bar counts.
     */
    var updateProgressBar = function() {
        var $items = $(SELECTORS.documentItem);
        var total = $items.length;
        var approved = $items.filter('[data-status="approved"]').length;
        var rejected = $items.filter('[data-status="rejected"]').length;
        var pending = total - approved - rejected;

        var approvedPercent = total > 0 ? (approved / total * 100) : 0;
        var rejectedPercent = total > 0 ? (rejected / total * 100) : 0;

        var $progress = $(SELECTORS.documentPanel).find('.jb-progress');
        $progress.find('.jb-bg-success').css('width', approvedPercent + '%');
        $progress.find('.jb-bg-danger').css('width', rejectedPercent + '%');

        var $summary = $(SELECTORS.documentPanel).find('.jb-progress-summary');
        $summary.find('.jb-text-success').html('<i class="fa fa-check jb-mr-1"></i>' + approved);
        $summary.find('.jb-text-danger').html('<i class="fa fa-times jb-mr-1"></i>' + rejected);
        $summary.find('.jb-text-warning').html('<i class="fa fa-clock jb-mr-1"></i>' + pending);
    };

    /**
     * Handle keyboard shortcuts.
     *
     * @param {KeyboardEvent} e Keyboard event.
     */
    var handleKeyboard = function(e) {
        // Ignore if typing in an input
        if (e.target.matches('input, textarea, select')) {
            return;
        }

        var key = e.key;
        var action = null;

        // Handle shift combinations
        if (e.shiftKey && key.toLowerCase() === 'a') {
            // Approve all
            $(ACTIONS.approveAll).trigger('click');
            e.preventDefault();
            return;
        }

        // Handle regular shortcuts
        if (SHORTCUTS[key]) {
            action = SHORTCUTS[key];
        } else if (SHORTCUTS[key.toLowerCase()]) {
            action = SHORTCUTS[key.toLowerCase()];
        }

        if (action) {
            e.preventDefault();

            switch (action) {
                case 'nextDocument':
                    nextDocument();
                    break;
                case 'previousDocument':
                    previousDocument();
                    break;
                case 'approveCurrentDocument':
                    approveCurrentDocument();
                    break;
                case 'focusRejectReason':
                    focusRejectReason();
                    break;
                case 'downloadCurrentDocument':
                    downloadCurrentDocument();
                    break;
                case 'toggleFullscreen':
                    toggleFullscreen();
                    break;
                case 'toggleSidebar':
                    toggleSidebar();
                    break;
                case 'showShortcuts':
                    showShortcuts();
                    break;
                case 'exitGrading':
                    exitGrading();
                    break;
            }
        }
    };

    /**
     * Filter applications by name.
     *
     * @param {string} query Filter query.
     */
    var filterApplications = function(query) {
        query = query.toLowerCase().trim();

        $(SELECTORS.applicationItem).each(function() {
            var $item = $(this);
            var name = ($item.data('applicant-name') || '').toLowerCase();

            if (!query || name.indexOf(query) !== -1) {
                $item.removeClass('jb-d-none');
            } else {
                $item.addClass('jb-d-none');
            }
        });
    };

    /**
     * Initialize event handlers.
     */
    var initEventHandlers = function() {
        var $panel = $(SELECTORS.panel);

        // Toggle sidebar
        $panel.on('click', ACTIONS.toggleSidebar, function(e) {
            e.preventDefault();
            toggleSidebar();
        });

        // Navigation
        $panel.on('click', ACTIONS.previous, function(e) {
            e.preventDefault();
            previousApplication();
        });

        $panel.on('click', ACTIONS.next, function(e) {
            e.preventDefault();
            nextApplication();
        });

        // Load application
        $panel.on('click', ACTIONS.loadApplication, function(e) {
            e.preventDefault();
            var applicationId = $(this).closest(SELECTORS.applicationItem).data('application-id');
            loadApplication(applicationId);
        });

        // Load document
        $panel.on('click', ACTIONS.loadDocument, function(e) {
            e.preventDefault();
            var documentId = $(this).closest(SELECTORS.documentItem).data('document-id');
            loadDocument(documentId);
        });

        // Approve document
        $panel.on('click', ACTIONS.approveDocument, function(e) {
            e.preventDefault();
            var documentId = $(this).data('document-id');
            validateDocument(documentId, 'approved', '');
        });

        // Reject document
        $panel.on('click', ACTIONS.rejectDocument, function(e) {
            e.preventDefault();
            var documentId = $(this).data('document-id');
            var reason = $(SELECTORS.rejectReason).val();

            if (!reason) {
                Str.get_string('selectrejectreason', 'local_jobboard').then(function(msg) {
                    Notification.addNotification({
                        message: msg,
                        type: 'warning'
                    });
                });
                $(SELECTORS.rejectReason).focus();
                return;
            }

            validateDocument(documentId, 'rejected', reason);
        });

        // Approve all
        $panel.on('click', ACTIONS.approveAll, function(e) {
            e.preventDefault();

            Str.get_string('confirmapproveall', 'local_jobboard').then(function(message) {
                return ModalFactory.create({
                    type: ModalFactory.types.SAVE_CANCEL,
                    title: Str.get_string('approveall', 'local_jobboard'),
                    body: message
                });
            }).then(function(modal) {
                modal.show();

                modal.getRoot().on(ModalEvents.save, function() {
                    var pendingDocs = $(SELECTORS.documentItem + '[data-status="pending"]');
                    var promises = [];

                    pendingDocs.each(function() {
                        var docId = $(this).data('document-id');
                        promises.push(validateDocument(docId, 'approved', ''));
                    });

                    // Note: This is simplified. In production, batch AJAX would be better.
                });

                modal.getRoot().on(ModalEvents.hidden, function() {
                    modal.destroy();
                });
            });
        });

        // Fullscreen
        $panel.on('click', ACTIONS.fullscreen, function(e) {
            e.preventDefault();
            toggleFullscreen();
        });

        // Show shortcuts
        $panel.on('click', ACTIONS.showShortcuts, function(e) {
            e.preventDefault();
            showShortcuts();
        });

        // Filter input
        $(SELECTORS.filterInput).on('input', function() {
            filterApplications($(this).val());
        });

        // Keyboard shortcuts
        $(document).on('keydown', handleKeyboard);

        // Handle fullscreen exit
        $(document).on('fullscreenchange', function() {
            if (!document.fullscreenElement) {
                $(SELECTORS.previewPanel).removeClass('jb-fullscreen');
            }
        });
    };

    /**
     * Initialize the grading panel.
     *
     * @param {Object} config Configuration object.
     * @param {number} config.applicationId Initial application ID.
     * @param {string} config.sesskey Session key.
     * @param {boolean} config.canValidate Whether user can validate.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        state.applicationId = config.applicationId;
        state.sesskey = config.sesskey;
        state.canValidate = config.canValidate;

        // Get initial document ID from URL or first pending
        var urlParams = new URLSearchParams(window.location.search);
        state.documentId = parseInt(urlParams.get('documentid'), 10) || null;

        initEventHandlers();

        // Set initial document selection
        if (!state.documentId) {
            var $firstPending = $(SELECTORS.documentItem + '[data-status="pending"]').first();
            if ($firstPending.length) {
                state.documentId = $firstPending.data('document-id');
                updateDocumentSelection(state.documentId);
            }
        }

        state.initialized = true;
    };

    return {
        init: init
    };
});

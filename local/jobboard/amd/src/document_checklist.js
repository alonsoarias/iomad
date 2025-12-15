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
 * Document checklist module for tracking file uploads in real-time.
 *
 * Monitors Moodle file manager elements and updates the sidebar checklist
 * to show which documents have been uploaded.
 *
 * @module     local_jobboard/document_checklist
 * @copyright  2024-2025 ISER - Instituto Superior de Educacion Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        checklistContainer: null,
        fileManagers: {},
        uploadedCount: 0,
        totalCount: 0,
        initialized: false
    };

    /**
     * Selectors used by the module.
     * @type {Object}
     */
    var SELECTORS = {
        checklist: '[data-region="document-checklist"]',
        checklistItems: '[data-region="checklist-items"]',
        checklistItem: '[data-doc-code]',
        checklistProgress: '[data-region="checklist-progress"]',
        checklistCounter: '[data-count="uploaded"]',
        checklistBadge: '[data-region="checklist-counter"]',
        allCompleteMessage: '[data-region="all-complete"]',
        fileManager: '.filemanager',
        fileManagerFiles: '.fp-content .fp-file',
        scrollButton: '[data-action="scroll-to-doc"]'
    };

    /**
     * CSS classes.
     * @type {Object}
     */
    var CLASSES = {
        checked: 'checked',
        unchecked: 'unchecked',
        hidden: 'd-none',
        success: 'bg-success',
        warning: 'bg-warning',
        textDark: 'text-dark'
    };

    /**
     * Get all file manager elements mapped by document code.
     *
     * @return {Object} Map of document code to file manager element.
     */
    var mapFileManagers = function() {
        var managers = {};
        var fileManagerContainers = document.querySelectorAll('[id^="fitem_id_doc_"]');

        fileManagerContainers.forEach(function(container) {
            // Extract document code from ID (fitem_id_doc_CODENAME).
            var match = container.id.match(/fitem_id_doc_(.+)/);
            if (match && match[1]) {
                var code = match[1];
                var fm = container.querySelector(SELECTORS.fileManager);
                if (fm) {
                    managers[code] = fm;
                }
            }
        });

        return managers;
    };

    /**
     * Check if a file manager has files uploaded.
     *
     * @param {HTMLElement} fileManager The file manager element.
     * @return {boolean} True if files are present.
     */
    var hasFiles = function(fileManager) {
        if (!fileManager) {
            return false;
        }

        // Check for files in the file manager.
        var files = fileManager.querySelectorAll('.fp-file, .fp-filename-icon');
        if (files.length > 0) {
            return true;
        }

        // Alternative: check if there's a file icon or filename displayed.
        var hasContent = fileManager.querySelector('.fp-content .fp-file') ||
                        fileManager.querySelector('.fp-filename') ||
                        fileManager.querySelector('.filepicker-filelist .filepicker-filename');

        return !!hasContent;
    };

    /**
     * Update a single checklist item.
     *
     * @param {string} docCode The document code.
     * @param {boolean} isUploaded Whether the document is uploaded.
     */
    var updateChecklistItem = function(docCode, isUploaded) {
        var item = document.querySelector('[data-doc-code="' + docCode + '"]');
        if (!item) {
            return;
        }

        var checkedIcon = item.querySelector('[data-icon="checked"]');
        var uncheckedIcon = item.querySelector('[data-icon="unchecked"]');
        var uploadedStatus = item.querySelector('[data-status="uploaded"]');
        var pendingStatus = item.querySelector('[data-status="pending"]');

        if (isUploaded) {
            item.classList.remove(CLASSES.unchecked);
            item.classList.add(CLASSES.checked);

            if (checkedIcon) {
                checkedIcon.classList.remove(CLASSES.hidden);
            }
            if (uncheckedIcon) {
                uncheckedIcon.classList.add(CLASSES.hidden);
            }
            if (uploadedStatus) {
                uploadedStatus.classList.remove(CLASSES.hidden);
            }
            if (pendingStatus) {
                pendingStatus.classList.add(CLASSES.hidden);
            }
        } else {
            item.classList.remove(CLASSES.checked);
            item.classList.add(CLASSES.unchecked);

            if (checkedIcon) {
                checkedIcon.classList.add(CLASSES.hidden);
            }
            if (uncheckedIcon) {
                uncheckedIcon.classList.remove(CLASSES.hidden);
            }
            if (uploadedStatus) {
                uploadedStatus.classList.add(CLASSES.hidden);
            }
            if (pendingStatus) {
                pendingStatus.classList.remove(CLASSES.hidden);
            }
        }
    };

    /**
     * Update the progress bar and counter.
     */
    var updateProgress = function() {
        var progressBar = document.querySelector(SELECTORS.checklistProgress);
        var counter = document.querySelector(SELECTORS.checklistCounter);
        var badge = document.querySelector(SELECTORS.checklistBadge);
        var allComplete = document.querySelector(SELECTORS.allCompleteMessage);

        var percent = state.totalCount > 0 ? Math.round((state.uploadedCount / state.totalCount) * 100) : 0;

        if (progressBar) {
            progressBar.style.width = percent + '%';
        }

        if (counter) {
            counter.textContent = state.uploadedCount;
        }

        // Update badge color based on progress.
        if (badge) {
            badge.classList.remove('bg-secondary', CLASSES.success, CLASSES.warning, CLASSES.textDark);
            if (state.uploadedCount === state.totalCount && state.totalCount > 0) {
                badge.classList.add(CLASSES.success);
            } else if (state.uploadedCount > 0) {
                badge.classList.add(CLASSES.warning, CLASSES.textDark);
            } else {
                badge.classList.add('bg-secondary');
            }
        }

        // Show "all complete" message.
        if (allComplete) {
            if (state.uploadedCount === state.totalCount && state.totalCount > 0) {
                allComplete.classList.remove(CLASSES.hidden);
            } else {
                allComplete.classList.add(CLASSES.hidden);
            }
        }

        // Dispatch event for other modules.
        document.dispatchEvent(new CustomEvent('jobboard:documentprogress', {
            detail: {
                uploaded: state.uploadedCount,
                total: state.totalCount,
                percent: percent
            }
        }));
    };

    /**
     * Scan all file managers and update checklist state.
     */
    var scanFileManagers = function() {
        var uploadedCount = 0;

        Object.keys(state.fileManagers).forEach(function(code) {
            var fm = state.fileManagers[code];
            var isUploaded = hasFiles(fm);

            updateChecklistItem(code, isUploaded);

            if (isUploaded) {
                uploadedCount++;
            }
        });

        state.uploadedCount = uploadedCount;
        updateProgress();
    };

    /**
     * Set up mutation observers to watch for file changes.
     */
    var setupObservers = function() {
        var observerConfig = {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'style']
        };

        var observer = new MutationObserver(function() {
            // Debounce the scan.
            clearTimeout(state.scanTimeout);
            state.scanTimeout = setTimeout(scanFileManagers, 300);
        });

        // Observe each file manager.
        Object.values(state.fileManagers).forEach(function(fm) {
            observer.observe(fm, observerConfig);
        });
    };

    /**
     * Scroll to a document field in the form.
     *
     * @param {string} targetId The target element ID.
     */
    var scrollToDocument = function(targetId) {
        // Try to find the form element.
        var element = document.getElementById('fitem_id_' + targetId) ||
                     document.getElementById('id_' + targetId) ||
                     document.getElementById(targetId);

        if (!element) {
            return;
        }

        // Find the parent fieldset or section.
        var section = element.closest('fieldset') || element.closest('.fitem');

        // Ensure section is visible (expand if collapsed).
        if (section) {
            var collapse = section.querySelector('.fcontainer.collapsed');
            if (collapse) {
                var toggle = section.querySelector('a.fheader');
                if (toggle) {
                    toggle.click();
                }
            }
        }

        // Scroll with offset for navbar.
        var offset = 120;
        var rect = element.getBoundingClientRect();
        var scrollTop = window.pageYOffset + rect.top - offset;

        window.scrollTo({
            top: Math.max(0, scrollTop),
            behavior: 'smooth'
        });

        // Focus the file manager or first input.
        setTimeout(function() {
            var focusable = element.querySelector('input, button, [tabindex]:not([tabindex="-1"])');
            if (focusable) {
                focusable.focus();
            }
        }, 500);
    };

    /**
     * Handle scroll button clicks.
     *
     * @param {Event} e The click event.
     */
    var onScrollClick = function(e) {
        var button = e.target.closest(SELECTORS.scrollButton);
        if (!button) {
            return;
        }

        e.preventDefault();
        var target = button.dataset.target;
        if (target) {
            scrollToDocument(target);
        }
    };

    /**
     * Initialize the document checklist module.
     *
     * @param {Object} config Configuration options.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        // Find the checklist container.
        state.checklistContainer = document.querySelector(SELECTORS.checklist);
        if (!state.checklistContainer) {
            return;
        }

        // Get total count from config or data attribute.
        config = config || {};
        state.totalCount = config.totalDocs || parseInt(state.checklistContainer.dataset.total, 10) || 0;

        // Map file managers to document codes.
        state.fileManagers = mapFileManagers();

        if (Object.keys(state.fileManagers).length === 0) {
            return;
        }

        // Initial scan.
        scanFileManagers();

        // Set up observers for real-time updates.
        setupObservers();

        // Event listeners.
        document.addEventListener('click', onScrollClick);

        // Also scan on form events.
        document.addEventListener('change', function(e) {
            if (e.target.closest(SELECTORS.fileManager)) {
                setTimeout(scanFileManagers, 500);
            }
        });

        state.initialized = true;
    };

    return {
        init: init,
        scan: scanFileManagers,
        scrollToDocument: scrollToDocument
    };
});

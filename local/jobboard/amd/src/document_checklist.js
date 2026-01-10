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
        textFields: {},  // Track text/textarea/editor fields.
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
            // Extract document code from ID (fitem_id_doc_CODENAME or fitem_id_doc_CODENAME_editor).
            var match = container.id.match(/fitem_id_doc_([^_]+)(?:_editor)?$/);
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
     * Get all text input fields (textarea, editor, text) mapped by document code.
     *
     * @return {Object} Map of document code to input element info.
     */
    var mapTextFields = function() {
        var fields = {};
        var containers = document.querySelectorAll('[id^="fitem_id_doc_"]');

        containers.forEach(function(container) {
            // Extract document code from ID.
            // For editor fields: fitem_id_doc_carta_intencion_editor
            // For textarea/text fields: fitem_id_doc_carta_intencion
            var match = container.id.match(/fitem_id_doc_(.+?)(?:_editor)?$/);
            if (match && match[1]) {
                var code = match[1];

                // Skip if already found as file manager.
                if (state.fileManagers && state.fileManagers[code]) {
                    return;
                }

                // Check for editor (TinyMCE/Atto).
                var editorFrame = container.querySelector('iframe');
                var editorTextarea = container.querySelector('textarea[id$="_editor"]');
                var regularTextarea = container.querySelector('textarea:not([id$="_editor"])');
                var textInput = container.querySelector('input[type="text"]');

                if (editorFrame || editorTextarea) {
                    // Moodle editor field.
                    fields[code] = {
                        type: 'editor',
                        container: container,
                        textarea: container.querySelector('textarea'),
                        iframe: editorFrame
                    };
                } else if (regularTextarea) {
                    // Regular textarea.
                    fields[code] = {
                        type: 'textarea',
                        container: container,
                        element: regularTextarea
                    };
                } else if (textInput) {
                    // Text input.
                    fields[code] = {
                        type: 'text',
                        container: container,
                        element: textInput
                    };
                }
            }
        });

        return fields;
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
     * Check if a text field has meaningful content.
     *
     * @param {Object} fieldInfo The field info object.
     * @return {boolean} True if field has content.
     */
    var hasTextContent = function(fieldInfo) {
        if (!fieldInfo) {
            return false;
        }

        var content = '';

        if (fieldInfo.type === 'editor') {
            // For Moodle editor, check the hidden textarea that stores the content.
            if (fieldInfo.textarea) {
                content = fieldInfo.textarea.value || '';
            }
            // Also try to get content from iframe if available.
            if (!content && fieldInfo.iframe) {
                try {
                    var iframeDoc = fieldInfo.iframe.contentDocument || fieldInfo.iframe.contentWindow.document;
                    content = iframeDoc.body ? iframeDoc.body.textContent : '';
                } catch (e) {
                    // Cross-origin or access denied.
                }
            }
        } else if (fieldInfo.element) {
            // For textarea or text input.
            content = fieldInfo.element.value || '';
        }

        // Strip HTML tags and check if there's actual text content.
        // Minimum 10 characters to consider it "filled".
        var textOnly = content.replace(/<[^>]*>/g, '').trim();
        return textOnly.length >= 10;
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
     * Scan all file managers and text fields, update checklist state.
     */
    var scanFileManagers = function() {
        var uploadedCount = 0;

        // Scan file managers.
        Object.keys(state.fileManagers).forEach(function(code) {
            var fm = state.fileManagers[code];
            var isUploaded = hasFiles(fm);

            updateChecklistItem(code, isUploaded);

            if (isUploaded) {
                uploadedCount++;
            }
        });

        // Scan text fields (textarea, editor, text input).
        Object.keys(state.textFields).forEach(function(code) {
            var fieldInfo = state.textFields[code];
            var hasContent = hasTextContent(fieldInfo);

            updateChecklistItem(code, hasContent);

            if (hasContent) {
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

        // Observe text field containers for editor changes.
        Object.values(state.textFields).forEach(function(fieldInfo) {
            if (fieldInfo.container) {
                observer.observe(fieldInfo.container, observerConfig);
            }
        });
    };

    /**
     * Set up input event listeners for text fields.
     */
    var setupTextFieldListeners = function() {
        Object.keys(state.textFields).forEach(function(code) {
            var fieldInfo = state.textFields[code];

            if (fieldInfo.type === 'textarea' && fieldInfo.element) {
                // Listen for input on textarea.
                fieldInfo.element.addEventListener('input', function() {
                    clearTimeout(state.scanTimeout);
                    state.scanTimeout = setTimeout(scanFileManagers, 500);
                });
                fieldInfo.element.addEventListener('blur', scanFileManagers);
            } else if (fieldInfo.type === 'text' && fieldInfo.element) {
                // Listen for input on text field.
                fieldInfo.element.addEventListener('input', function() {
                    clearTimeout(state.scanTimeout);
                    state.scanTimeout = setTimeout(scanFileManagers, 500);
                });
                fieldInfo.element.addEventListener('blur', scanFileManagers);
            } else if (fieldInfo.type === 'editor') {
                // For editor, listen on the textarea (TinyMCE syncs to it).
                if (fieldInfo.textarea) {
                    fieldInfo.textarea.addEventListener('input', function() {
                        clearTimeout(state.scanTimeout);
                        state.scanTimeout = setTimeout(scanFileManagers, 500);
                    });
                    fieldInfo.textarea.addEventListener('change', scanFileManagers);
                }
                // Also try to listen to the editor iframe content.
                if (fieldInfo.iframe) {
                    try {
                        var iframeDoc = fieldInfo.iframe.contentDocument || fieldInfo.iframe.contentWindow.document;
                        iframeDoc.addEventListener('input', function() {
                            clearTimeout(state.scanTimeout);
                            state.scanTimeout = setTimeout(scanFileManagers, 500);
                        });
                        iframeDoc.addEventListener('blur', scanFileManagers);
                    } catch (e) {
                        // Cross-origin or access denied - rely on mutation observer.
                    }
                }
            }
        });

        // Listen for TinyMCE events globally.
        if (typeof window.tinyMCE !== 'undefined') {
            window.tinyMCE.on('AddEditor', function(e) {
                e.editor.on('input change blur keyup', function() {
                    clearTimeout(state.scanTimeout);
                    state.scanTimeout = setTimeout(scanFileManagers, 500);
                });
            });
        }
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
     * @param {number} [config.totalDocs] Total number of documents.
     * @param {Array} [config.uploadedCodes] Array of document codes that are already uploaded.
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

        // Get initial uploaded count from data attribute if available.
        var initialUploaded = parseInt(state.checklistContainer.dataset.uploaded, 10) || 0;
        state.uploadedCount = initialUploaded;

        // Apply initial state from pre-uploaded documents (passed from PHP).
        if (config.uploadedCodes && Array.isArray(config.uploadedCodes)) {
            config.uploadedCodes.forEach(function(code) {
                updateChecklistItem(code, true);
            });
        }

        // Update progress with initial state.
        updateProgress();

        // Map file managers to document codes.
        state.fileManagers = mapFileManagers();

        // Map text fields (textarea, editor, text input) to document codes.
        state.textFields = mapTextFields();

        // Check if we have any fields to monitor.
        var hasFileManagers = Object.keys(state.fileManagers).length > 0;
        var hasTextFields = Object.keys(state.textFields).length > 0;

        if (!hasFileManagers && !hasTextFields) {
            state.initialized = true;
            return;
        }

        // Delayed initial scan to allow file managers to fully render.
        // This is important because Moodle file managers load content via AJAX.
        setTimeout(function() {
            scanFileManagers();
        }, 1000);

        // Additional delayed scan for slower connections.
        setTimeout(function() {
            scanFileManagers();
        }, 3000);

        // Set up observers for real-time updates.
        setupObservers();

        // Set up text field listeners.
        if (hasTextFields) {
            setupTextFieldListeners();
        }

        // Event listeners.
        document.addEventListener('click', onScrollClick);

        // Also scan on form events.
        document.addEventListener('change', function(e) {
            if (e.target.closest(SELECTORS.fileManager)) {
                setTimeout(scanFileManagers, 500);
            }
        });

        // Listen for changes in textareas and inputs.
        document.addEventListener('input', function(e) {
            if (e.target.tagName === 'TEXTAREA' || e.target.type === 'text') {
                clearTimeout(state.scanTimeout);
                state.scanTimeout = setTimeout(scanFileManagers, 500);
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

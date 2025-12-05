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
 * Document preview module for local_jobboard.
 *
 * Provides inline preview functionality for PDF and image documents
 * without requiring file download. Uses PDF.js for PDF rendering.
 *
 * @module     local_jobboard/document_preview
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/modal_factory', 'core/modal_events', 'core/str', 'core/notification'],
    function($, ModalFactory, ModalEvents, Str, Notification) {

    /**
     * PDF.js library URL (bundled with Moodle or CDN fallback).
     * @type {string}
     */
    const PDFJS_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
    const PDFJS_WORKER_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    /**
     * Track if PDF.js is loaded.
     * @type {boolean}
     */
    let pdfjsLoaded = false;

    /**
     * Current PDF document.
     * @type {Object|null}
     */
    let currentPdf = null;

    /**
     * Current page number.
     * @type {number}
     */
    let currentPage = 1;

    /**
     * Current zoom level.
     * @type {number}
     */
    let currentZoom = 1.0;

    /**
     * Load PDF.js library dynamically.
     *
     * @return {Promise} Resolves when PDF.js is loaded.
     */
    const loadPdfJs = function() {
        return new Promise(function(resolve, reject) {
            if (pdfjsLoaded && window.pdfjsLib) {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = PDFJS_URL;
            script.onload = function() {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJS_WORKER_URL;
                pdfjsLoaded = true;
                resolve();
            };
            script.onerror = function() {
                reject(new Error('Failed to load PDF.js'));
            };
            document.head.appendChild(script);
        });
    };

    /**
     * Render a PDF page to canvas.
     *
     * @param {Object} page PDF page object.
     * @param {HTMLCanvasElement} canvas Canvas element.
     * @param {number} zoom Zoom level.
     * @return {Promise} Resolves when rendering is complete.
     */
    const renderPage = function(page, canvas, zoom) {
        const viewport = page.getViewport({scale: zoom});
        const context = canvas.getContext('2d');

        canvas.height = viewport.height;
        canvas.width = viewport.width;

        return page.render({
            canvasContext: context,
            viewport: viewport
        }).promise;
    };

    /**
     * Update the page display.
     *
     * @param {HTMLElement} container Preview container.
     */
    const updatePageDisplay = function(container) {
        if (!currentPdf) {
            return;
        }

        const pageInfo = container.querySelector('.preview-page-info');
        if (pageInfo) {
            pageInfo.textContent = currentPage + ' / ' + currentPdf.numPages;
        }

        // Update button states.
        const prevBtn = container.querySelector('[data-action="prev-page"]');
        const nextBtn = container.querySelector('[data-action="next-page"]');
        if (prevBtn) {
            prevBtn.disabled = currentPage <= 1;
        }
        if (nextBtn) {
            nextBtn.disabled = currentPage >= currentPdf.numPages;
        }
    };

    /**
     * Display a specific page of the PDF.
     *
     * @param {HTMLElement} container Preview container.
     * @param {number} pageNum Page number to display.
     * @return {Promise} Resolves when page is displayed.
     */
    const displayPage = function(container, pageNum) {
        if (!currentPdf || pageNum < 1 || pageNum > currentPdf.numPages) {
            return Promise.resolve();
        }

        currentPage = pageNum;
        const canvas = container.querySelector('.preview-canvas');

        return currentPdf.getPage(pageNum).then(function(page) {
            return renderPage(page, canvas, currentZoom);
        }).then(function() {
            updatePageDisplay(container);
        });
    };

    /**
     * Create the preview modal content.
     *
     * @param {string} filename Document filename.
     * @param {string} mimetype Document MIME type.
     * @return {string} HTML content.
     */
    const createPreviewContent = function(filename, mimetype) {
        const isPdf = mimetype === 'application/pdf';
        const isImage = mimetype.startsWith('image/');

        let content = '<div class="document-preview-container">';

        // Toolbar.
        content += '<div class="preview-toolbar d-flex justify-content-between align-items-center mb-2 p-2 bg-light">';
        content += '<div class="preview-filename text-truncate"><strong>' + filename + '</strong></div>';
        content += '<div class="preview-controls">';

        if (isPdf) {
            content += '<button type="button" class="btn btn-sm btn-outline-secondary mr-1" ' +
                'data-action="prev-page" title="Previous page"><i class="fa fa-chevron-left"></i></button>';
            content += '<span class="preview-page-info mx-2">1 / 1</span>';
            content += '<button type="button" class="btn btn-sm btn-outline-secondary mr-2" ' +
                'data-action="next-page" title="Next page"><i class="fa fa-chevron-right"></i></button>';
        }

        content += '<button type="button" class="btn btn-sm btn-outline-secondary mr-1" ' +
            'data-action="zoom-out" title="Zoom out"><i class="fa fa-search-minus"></i></button>';
        content += '<span class="preview-zoom-info mx-1">100%</span>';
        content += '<button type="button" class="btn btn-sm btn-outline-secondary mr-2" ' +
            'data-action="zoom-in" title="Zoom in"><i class="fa fa-search-plus"></i></button>';
        content += '<button type="button" class="btn btn-sm btn-outline-secondary" ' +
            'data-action="rotate" title="Rotate"><i class="fa fa-rotate-right"></i></button>';

        content += '</div>';
        content += '</div>';

        // Preview area.
        content += '<div class="preview-area text-center" style="max-height: 70vh; overflow: auto;">';

        if (isPdf) {
            content += '<canvas class="preview-canvas border"></canvas>';
        } else if (isImage) {
            content += '<img class="preview-image img-fluid border" alt="' + filename + '" ' +
                'style="max-width: 100%; transition: transform 0.2s;">';
        } else {
            content += '<div class="alert alert-info">';
            content += 'Preview not available for this file type. ';
            content += '<a href="#" class="download-link">Download to view</a>';
            content += '</div>';
        }

        content += '</div>';
        content += '</div>';

        return content;
    };

    /**
     * Initialize preview controls.
     *
     * @param {Object} modal Modal instance.
     * @param {string} url Document URL.
     * @param {string} mimetype Document MIME type.
     */
    const initPreviewControls = function(modal, url, mimetype) {
        const container = modal.getRoot()[0];
        const isPdf = mimetype === 'application/pdf';
        const isImage = mimetype.startsWith('image/');
        let rotation = 0;

        // Zoom controls.
        container.querySelector('[data-action="zoom-in"]').addEventListener('click', function() {
            currentZoom = Math.min(currentZoom + 0.25, 3.0);
            updateZoom(container, isPdf, isImage);
        });

        container.querySelector('[data-action="zoom-out"]').addEventListener('click', function() {
            currentZoom = Math.max(currentZoom - 0.25, 0.5);
            updateZoom(container, isPdf, isImage);
        });

        // Rotation.
        container.querySelector('[data-action="rotate"]').addEventListener('click', function() {
            rotation = (rotation + 90) % 360;
            const target = isPdf ? container.querySelector('.preview-canvas') :
                container.querySelector('.preview-image');
            if (target) {
                target.style.transform = 'rotate(' + rotation + 'deg) scale(' + currentZoom + ')';
            }
        });

        // PDF navigation.
        if (isPdf) {
            container.querySelector('[data-action="prev-page"]').addEventListener('click', function() {
                displayPage(container, currentPage - 1);
            });

            container.querySelector('[data-action="next-page"]').addEventListener('click', function() {
                displayPage(container, currentPage + 1);
            });
        }

        // Load content.
        if (isPdf) {
            loadPdfContent(container, url);
        } else if (isImage) {
            loadImageContent(container, url);
        }

        // Download link.
        const downloadLink = container.querySelector('.download-link');
        if (downloadLink) {
            downloadLink.href = url;
            downloadLink.target = '_blank';
        }
    };

    /**
     * Update zoom display.
     *
     * @param {HTMLElement} container Preview container.
     * @param {boolean} isPdf Is PDF document.
     * @param {boolean} isImage Is image document.
     */
    const updateZoom = function(container, isPdf, isImage) {
        const zoomInfo = container.querySelector('.preview-zoom-info');
        if (zoomInfo) {
            zoomInfo.textContent = Math.round(currentZoom * 100) + '%';
        }

        if (isPdf && currentPdf) {
            displayPage(container, currentPage);
        } else if (isImage) {
            const img = container.querySelector('.preview-image');
            if (img) {
                img.style.transform = 'scale(' + currentZoom + ')';
            }
        }
    };

    /**
     * Load PDF content.
     *
     * @param {HTMLElement} container Preview container.
     * @param {string} url PDF URL.
     */
    const loadPdfContent = function(container, url) {
        const canvas = container.querySelector('.preview-canvas');

        // Show loading indicator.
        canvas.style.display = 'none';
        const loading = document.createElement('div');
        loading.className = 'preview-loading text-center py-5';
        loading.innerHTML = '<i class="fa fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading PDF...</p>';
        canvas.parentNode.insertBefore(loading, canvas);

        loadPdfJs().then(function() {
            return window.pdfjsLib.getDocument(url).promise;
        }).then(function(pdf) {
            currentPdf = pdf;
            currentPage = 1;
            loading.remove();
            canvas.style.display = 'block';
            return displayPage(container, 1);
        }).catch(function(error) {
            loading.innerHTML = '<div class="alert alert-danger">' +
                'Failed to load PDF: ' + error.message + '</div>';
            // eslint-disable-next-line no-console
            console.error('PDF load error:', error);
        });
    };

    /**
     * Load image content.
     *
     * @param {HTMLElement} container Preview container.
     * @param {string} url Image URL.
     */
    const loadImageContent = function(container, url) {
        const img = container.querySelector('.preview-image');
        img.src = url;
        img.onerror = function() {
            img.parentNode.innerHTML = '<div class="alert alert-danger">Failed to load image</div>';
        };
    };

    /**
     * Open document preview modal.
     *
     * @param {string} url Document URL.
     * @param {string} filename Document filename.
     * @param {string} mimetype Document MIME type.
     */
    const openPreview = function(url, filename, mimetype) {
        // Reset state.
        currentPdf = null;
        currentPage = 1;
        currentZoom = 1.0;

        Str.get_string('previewdocument', 'local_jobboard').then(function(title) {
            return ModalFactory.create({
                type: ModalFactory.types.DEFAULT,
                title: title,
                body: createPreviewContent(filename, mimetype),
                large: true
            });
        }).then(function(modal) {
            modal.getRoot().on(ModalEvents.hidden, function() {
                modal.destroy();
            });

            modal.show();
            initPreviewControls(modal, url, mimetype);
        }).catch(Notification.exception);
    };

    /**
     * Initialize document preview functionality.
     *
     * Attaches event listeners to elements with data-preview-url attribute.
     */
    const init = function() {
        $(document).on('click', '[data-preview-url]', function(e) {
            e.preventDefault();

            const $this = $(this);
            const url = $this.data('preview-url');
            const filename = $this.data('preview-filename') || 'Document';
            const mimetype = $this.data('preview-mimetype') || 'application/pdf';

            openPreview(url, filename, mimetype);
        });
    };

    return {
        init: init,
        openPreview: openPreview
    };
});

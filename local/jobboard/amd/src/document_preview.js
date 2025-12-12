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
 * Document preview module for viewing and validating documents.
 *
 * @module     local_jobboard/document_preview
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/modal_factory', 'core/modal_events', 'core/str', 'core/notification'],
    function(ModalFactory, ModalEvents, Str, Notification) {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        modal: null,
        currentDocument: null,
        initialized: false
    };

    /**
     * Supported file types for inline preview.
     * @type {Object}
     */
    var SUPPORTED_TYPES = {
        image: ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        pdf: ['pdf'],
        video: ['mp4', 'webm'],
        audio: ['mp3', 'wav', 'ogg']
    };

    /**
     * Get file extension from filename.
     *
     * @param {string} filename The filename.
     * @return {string} The file extension in lowercase.
     */
    var getFileExtension = function(filename) {
        return filename.split('.').pop().toLowerCase();
    };

    /**
     * Get the type category for a file.
     *
     * @param {string} filename The filename.
     * @return {string|null} The type category or null if not supported.
     */
    var getFileType = function(filename) {
        var ext = getFileExtension(filename);
        for (var type in SUPPORTED_TYPES) {
            if (SUPPORTED_TYPES[type].indexOf(ext) !== -1) {
                return type;
            }
        }
        return null;
    };

    /**
     * Build preview HTML based on file type.
     *
     * @param {Object} doc Document object with url and filename.
     * @return {string} HTML for preview.
     */
    var buildPreviewHtml = function(doc) {
        var type = getFileType(doc.filename);
        var html = '<div class="jb-document-preview-container">';

        switch (type) {
            case 'image':
                html += '<img src="' + doc.url + '" alt="' + doc.filename + '" class="jb-preview-image" />';
                break;
            case 'pdf':
                html += '<iframe src="' + doc.url + '" class="jb-preview-pdf" frameborder="0"></iframe>';
                break;
            case 'video':
                html += '<video controls class="jb-preview-video"><source src="' + doc.url + '"></video>';
                break;
            case 'audio':
                html += '<audio controls class="jb-preview-audio"><source src="' + doc.url + '"></audio>';
                break;
            default:
                html += '<div class="jb-preview-unsupported">';
                html += '<p class="jb-text-muted">Preview not available for this file type.</p>';
                html += '<a href="' + doc.url + '" class="jb-btn jb-btn-primary" target="_blank">Download</a>';
                html += '</div>';
        }

        html += '</div>';
        return html;
    };

    /**
     * Show the document preview modal.
     *
     * @param {Object} doc Document object.
     * @param {string} doc.url Document URL.
     * @param {string} doc.filename Document filename.
     * @param {string} [doc.title] Document title for modal header.
     */
    var showPreview = function(doc) {
        state.currentDocument = doc;

        Str.get_string('documentpreview', 'local_jobboard').then(function(title) {
            return ModalFactory.create({
                title: doc.title || title,
                body: buildPreviewHtml(doc),
                large: true
            });
        }).then(function(modal) {
            state.modal = modal;
            modal.show();

            // Clean up on close.
            modal.getRoot().on(ModalEvents.hidden, function() {
                modal.destroy();
                state.modal = null;
                state.currentDocument = null;
            });
        }).catch(function(error) {
            Notification.exception(error);
        });
    };

    /**
     * Handle document click events.
     *
     * @param {Event} e The click event.
     */
    var onDocumentClick = function(e) {
        var trigger = e.target.closest('[data-action="preview-document"]');
        if (!trigger) {
            return;
        }

        e.preventDefault();

        var doc = {
            url: trigger.dataset.url || trigger.href,
            filename: trigger.dataset.filename || trigger.textContent.trim(),
            title: trigger.dataset.title || null
        };

        showPreview(doc);
    };

    /**
     * Initialize the document preview module.
     *
     * @param {Object} config Configuration object.
     * @param {string} [config.containerSelector='body'] Container to attach click handler.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        config = config || {};
        var container = document.querySelector(config.containerSelector || 'body');

        if (container) {
            container.addEventListener('click', onDocumentClick);
        }

        state.initialized = true;
    };

    return {
        init: init,
        showPreview: showPreview
    };
});

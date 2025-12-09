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
 * Email template live preview module.
 *
 * @module     local_jobboard/email_template_preview
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    'use strict';

    /**
     * Sample data for placeholder replacement in preview.
     */
    var sampleData = {
        '{fullname}': 'John Doe',
        '{vacancy_code}': 'VAC-2024-001',
        '{vacancy_title}': 'Full-time Mathematics Professor',
        '{application_id}': '12345',
        '{application_url}': 'https://example.com/application/view?id=12345',
        '{sitename}': 'Job Board System',
        '{rejected_docs}': '- Professional Certificate: Document illegible\n- Background Check: Document expired',
        '{observations}': 'Please ensure documents are clear and up to date.',
        '{summary}': 'Documents reviewed: 5\nApproved: 3\nRejected: 2',
        '{action_required}': 'Please reupload the rejected documents.',
        '{interview_date}': 'Monday, December 15, 2024 at 10:00 AM',
        '{interview_location}': 'Building A, Room 205',
        '{interview_notes}': 'Please bring a copy of your documents.',
        '{notes}': 'Additional information will be sent via email.'
    };

    /**
     * Replace placeholders with sample data.
     *
     * @param {string} text Text with placeholders.
     * @return {string} Text with placeholders replaced.
     */
    var replacePlaceholders = function(text) {
        if (!text) {
            return '';
        }
        var result = text;
        Object.keys(sampleData).forEach(function(key) {
            var regex = new RegExp(key.replace(/[{}]/g, '\\$&'), 'g');
            result = result.replace(regex, sampleData[key]);
        });
        return result;
    };

    /**
     * Get subject field value.
     *
     * @return {string} Subject text.
     */
    var getSubject = function() {
        return $('input[name="subject"]').val() || '';
    };

    /**
     * Get body content from the editor.
     *
     * @return {string} Body HTML content.
     */
    var getBody = function() {
        // Try to get from Atto editor first.
        var editor = document.querySelector('#id_body_editoreditable');
        if (editor) {
            return editor.innerHTML || '';
        }

        // Try to get from TinyMCE.
        if (typeof window.tinyMCE !== 'undefined') {
            var tinyEditor = window.tinyMCE.get('id_body_editor');
            if (tinyEditor) {
                return tinyEditor.getContent();
            }
        }

        // Fallback to textarea.
        var textarea = document.querySelector('#id_body_editor');
        if (textarea) {
            return textarea.value || '';
        }

        return '';
    };

    /**
     * Escape HTML for display.
     *
     * @param {string} text Text to escape.
     * @return {string} Escaped text.
     */
    var escapeHtml = function(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };

    /**
     * Update the preview panel.
     */
    var updatePreview = function() {
        var subject = getSubject();
        var body = getBody();

        var previewSubject = replacePlaceholders(subject);
        var previewBody = replacePlaceholders(body);

        // Build preview HTML.
        var previewHtml = '<div class="email-preview-card">';
        previewHtml += '<div class="email-preview-header bg-primary text-white p-2 rounded-top">';
        previewHtml += '<i class="fa fa-envelope mr-2"></i>Email Preview';
        previewHtml += '</div>';
        previewHtml += '<div class="email-preview-content border border-top-0 rounded-bottom p-3">';
        previewHtml += '<div class="email-preview-subject mb-3">';
        previewHtml += '<small class="text-muted d-block">Subject:</small>';
        previewHtml += '<strong>' + escapeHtml(previewSubject) + '</strong>';
        previewHtml += '</div>';
        previewHtml += '<hr>';
        previewHtml += '<div class="email-preview-body">';
        previewHtml += '<small class="text-muted d-block mb-2">Body:</small>';
        previewHtml += '<div class="bg-white p-3 border rounded">' + previewBody + '</div>';
        previewHtml += '</div>';
        previewHtml += '</div>';
        previewHtml += '</div>';

        $('#template-preview').html(previewHtml);
    };

    /**
     * Initialize the live preview.
     *
     * @param {object} config Configuration object.
     */
    var init = function(config) {
        // eslint-disable-next-line no-unused-vars
        config = config || {};

        // Update preview on page load.
        $(document).ready(function() {
            // Initial preview update with delay to wait for editor initialization.
            setTimeout(updatePreview, 1000);

            // Listen for subject changes.
            $('input[name="subject"]').on('input change', function() {
                updatePreview();
            });

            // Listen for Atto editor changes.
            var attoEditor = document.querySelector('#id_body_editoreditable');
            if (attoEditor) {
                var observer = new MutationObserver(function() {
                    updatePreview();
                });
                observer.observe(attoEditor, {
                    childList: true,
                    subtree: true,
                    characterData: true
                });
            }

            // Listen for TinyMCE changes.
            if (typeof window.tinyMCE !== 'undefined') {
                var checkTinyMCE = setInterval(function() {
                    var tinyEditor = window.tinyMCE.get('id_body_editor');
                    if (tinyEditor) {
                        clearInterval(checkTinyMCE);
                        tinyEditor.on('change input keyup', function() {
                            updatePreview();
                        });
                    }
                }, 500);
            }

            // Listen for textarea changes as fallback.
            $('#id_body_editor').on('input change', function() {
                updatePreview();
            });

            // Auto-expand preview section.
            var previewHeader = $('[data-target="#id_previewheader"]').first();
            if (previewHeader.length) {
                previewHeader.attr('aria-expanded', 'true');
                $('#id_previewheader').addClass('show');
            }

            // Update preview every 2 seconds as backup.
            setInterval(updatePreview, 2000);
        });
    };

    return {
        init: init,
        updatePreview: updatePreview
    };
});

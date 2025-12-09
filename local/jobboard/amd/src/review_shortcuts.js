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
 * Keyboard shortcuts module for document review.
 *
 * @module     local_jobboard/review_shortcuts
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'core/modal_factory', 'core/notification'],
    function($, Str, ModalFactory, Notification) {
    'use strict';

    /**
     * Keyboard shortcuts handler.
     */
    var ReviewShortcuts = {
        /**
         * Shortcut key mappings.
         */
        SHORTCUTS: {
            'a': 'approve',
            'r': 'reject',
            'n': 'next',
            'p': 'previous',
            's': 'save',
            '?': 'help',
            'h': 'help'
        },

        /**
         * Current document index.
         */
        currentIndex: 0,

        /**
         * Total documents.
         */
        totalDocuments: 0,

        /**
         * Help modal instance.
         */
        helpModal: null,

        /**
         * Configuration options.
         */
        config: {},

        /**
         * Initialize shortcuts.
         *
         * @param {Object} config Configuration options
         * @param {string} config.documentSelector Selector for document items
         * @param {string} config.approveSelector Selector for approve buttons
         * @param {string} config.rejectSelector Selector for reject buttons
         * @param {string} config.saveSelector Selector for save button
         */
        init: function(config) {
            this.config = $.extend({
                documentSelector: '.jb-doc-item',
                approveSelector: '.jb-approve-btn',
                rejectSelector: '.jb-reject-btn',
                saveSelector: '#saveReviewBtn',
                showHelpOnStart: true
            }, config);

            this.$documents = $(this.config.documentSelector);
            this.totalDocuments = this.$documents.length;

            if (this.totalDocuments === 0) {
                return;
            }

            this.bindKeyboard();
            this.showShortcutsHelp();
            this.highlightCurrent();

            if (this.config.showHelpOnStart) {
                this.showHelpToast();
            }
        },

        /**
         * Bind keyboard event handlers.
         */
        bindKeyboard: function() {
            var self = this;

            $(document).on('keydown', function(e) {
                // Ignore if typing in an input
                if ($(e.target).is('input, textarea, select, [contenteditable="true"]')) {
                    return;
                }

                var key = e.key.toLowerCase();
                var action = self.SHORTCUTS[key];

                if (action) {
                    e.preventDefault();
                    self.handleAction(action);
                }
            });
        },

        /**
         * Handle shortcut action.
         *
         * @param {string} action Action name
         */
        handleAction: function(action) {
            switch (action) {
                case 'approve':
                    this.approveCurrentDocument();
                    break;
                case 'reject':
                    this.rejectCurrentDocument();
                    break;
                case 'next':
                    this.navigateNext();
                    break;
                case 'previous':
                    this.navigatePrevious();
                    break;
                case 'save':
                    this.saveReview();
                    break;
                case 'help':
                    this.showHelpModal();
                    break;
            }
        },

        /**
         * Approve the current document.
         */
        approveCurrentDocument: function() {
            var $current = this.$documents.eq(this.currentIndex);
            var $approveBtn = $current.find(this.config.approveSelector);

            if ($approveBtn.length && !$approveBtn.prop('disabled')) {
                $approveBtn.click();
                this.showFeedback('approved');
            }
        },

        /**
         * Reject the current document.
         */
        rejectCurrentDocument: function() {
            var $current = this.$documents.eq(this.currentIndex);
            var $rejectBtn = $current.find(this.config.rejectSelector);

            if ($rejectBtn.length && !$rejectBtn.prop('disabled')) {
                $rejectBtn.click();
            }
        },

        /**
         * Navigate to next document.
         */
        navigateNext: function() {
            if (this.currentIndex < this.totalDocuments - 1) {
                this.currentIndex++;
                this.highlightCurrent();
                this.scrollToCurrent();
            }
        },

        /**
         * Navigate to previous document.
         */
        navigatePrevious: function() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.highlightCurrent();
                this.scrollToCurrent();
            }
        },

        /**
         * Save the review.
         */
        saveReview: function() {
            var $saveBtn = $(this.config.saveSelector);
            if ($saveBtn.length && !$saveBtn.prop('disabled')) {
                $saveBtn.click();
            }
        },

        /**
         * Highlight the current document.
         */
        highlightCurrent: function() {
            this.$documents.removeClass('active border-primary');
            this.$documents.eq(this.currentIndex)
                .addClass('active border-primary')
                .attr('aria-current', 'true');

            // Update aria for other items
            this.$documents.not('.active').removeAttr('aria-current');
        },

        /**
         * Scroll to the current document.
         */
        scrollToCurrent: function() {
            var $current = this.$documents.eq(this.currentIndex);
            if ($current.length) {
                $('html, body').animate({
                    scrollTop: $current.offset().top - 100
                }, 300);
            }
        },

        /**
         * Show visual feedback for action.
         *
         * @param {string} type Feedback type
         */
        showFeedback: function(type) {
            var $current = this.$documents.eq(this.currentIndex);
            var feedbackClass = type === 'approved' ? 'bg-success' : 'bg-danger';

            $current.addClass(feedbackClass + ' text-white');
            setTimeout(function() {
                $current.removeClass(feedbackClass + ' text-white');
            }, 500);
        },

        /**
         * Show shortcuts help panel.
         */
        showShortcutsHelp: function() {
            var helpHtml = '<div class="jb-shortcuts-help" role="region" aria-label="Keyboard shortcuts">';
            helpHtml += '<strong>Atajos:</strong> ';
            helpHtml += '<kbd>A</kbd> Aprobar ';
            helpHtml += '<kbd>R</kbd> Rechazar ';
            helpHtml += '<kbd>N</kbd>/<kbd>P</kbd> Navegar ';
            helpHtml += '<kbd>S</kbd> Guardar ';
            helpHtml += '<kbd>?</kbd> Ayuda';
            helpHtml += '</div>';

            $('body').append(helpHtml);
        },

        /**
         * Show help toast notification.
         */
        showHelpToast: function() {
            Str.get_string('keyboardshortcuts', 'local_jobboard').then(function(str) {
                // Just log for now, could show a toast
                window.console.log(str + ': Press ? for help');
            }).catch(function() {
                // Ignore
            });
        },

        /**
         * Show help modal with all shortcuts.
         */
        showHelpModal: function() {
            var self = this;

            if (this.helpModal) {
                this.helpModal.show();
                return;
            }

            Str.get_strings([
                {key: 'shortcutshelp_title', component: 'local_jobboard'},
                {key: 'shortcut_approve', component: 'local_jobboard'},
                {key: 'shortcut_reject', component: 'local_jobboard'},
                {key: 'shortcut_next', component: 'local_jobboard'},
                {key: 'shortcut_previous', component: 'local_jobboard'},
                {key: 'shortcut_save', component: 'local_jobboard'},
                {key: 'shortcut_help', component: 'local_jobboard'}
            ]).then(function(strings) {
                var body = '<div class="jb-shortcuts-modal">';
                body += '<div class="jb-shortcut-row"><span>' + strings[1] + '</span><kbd>A</kbd></div>';
                body += '<div class="jb-shortcut-row"><span>' + strings[2] + '</span><kbd>R</kbd></div>';
                body += '<div class="jb-shortcut-row"><span>' + strings[3] + '</span><kbd>N</kbd></div>';
                body += '<div class="jb-shortcut-row"><span>' + strings[4] + '</span><kbd>P</kbd></div>';
                body += '<div class="jb-shortcut-row"><span>' + strings[5] + '</span><kbd>S</kbd></div>';
                body += '<div class="jb-shortcut-row"><span>' + strings[6] + '</span><kbd>?</kbd> / <kbd>H</kbd></div>';
                body += '</div>';

                return ModalFactory.create({
                    type: ModalFactory.types.DEFAULT,
                    title: strings[0],
                    body: body
                });
            }).then(function(modal) {
                self.helpModal = modal;
                modal.show();
            }).catch(Notification.exception);
        }
    };

    return {
        /**
         * Initialize review shortcuts.
         *
         * @param {Object} config Configuration options
         */
        init: function(config) {
            ReviewShortcuts.init(config);
        }
    };
});

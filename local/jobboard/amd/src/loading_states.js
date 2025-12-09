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
 * Loading states module for visual feedback during async operations.
 *
 * @module     local_jobboard/loading_states
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    'use strict';

    var LoadingStates = {
        /**
         * Add loading state to a button.
         *
         * @param {jQuery|string} button Button element or selector
         * @param {string} loadingText Optional loading text
         */
        buttonLoading: function(button, loadingText) {
            var $btn = $(button);
            if (!$btn.length) {
                return;
            }

            // Store original state
            $btn.data('original-text', $btn.val() || $btn.text());
            $btn.data('original-html', $btn.html());
            $btn.data('was-disabled', $btn.prop('disabled'));

            // Apply loading state
            $btn.addClass('is-loading');
            $btn.prop('disabled', true);

            if (loadingText) {
                if ($btn.is('input')) {
                    $btn.val(loadingText);
                } else {
                    $btn.html('<i class="fa fa-spinner fa-spin mr-2"></i>' + loadingText);
                }
            }
        },

        /**
         * Remove loading state from a button.
         *
         * @param {jQuery|string} button Button element or selector
         */
        buttonReset: function(button) {
            var $btn = $(button);
            if (!$btn.length) {
                return;
            }

            $btn.removeClass('is-loading');

            // Restore original state
            if ($btn.is('input')) {
                $btn.val($btn.data('original-text'));
            } else {
                $btn.html($btn.data('original-html'));
            }

            if (!$btn.data('was-disabled')) {
                $btn.prop('disabled', false);
            }
        },

        /**
         * Add loading overlay to a container.
         *
         * @param {jQuery|string} container Container element or selector
         */
        containerLoading: function(container) {
            var $container = $(container);
            if (!$container.length) {
                return;
            }

            $container.addClass('jb-loading-overlay is-loading');
        },

        /**
         * Remove loading overlay from a container.
         *
         * @param {jQuery|string} container Container element or selector
         */
        containerReset: function(container) {
            var $container = $(container);
            if (!$container.length) {
                return;
            }

            $container.removeClass('jb-loading-overlay is-loading');
        },

        /**
         * Show skeleton loading placeholders.
         *
         * @param {jQuery|string} container Container element or selector
         * @param {Object} options Skeleton options
         * @param {number} options.cards Number of skeleton cards to show
         * @param {string} options.type Type of skeleton (card, list, text)
         */
        showSkeleton: function(container, options) {
            var $container = $(container);
            if (!$container.length) {
                return;
            }

            options = $.extend({
                cards: 3,
                type: 'card'
            }, options);

            var html = '';

            if (options.type === 'card') {
                for (var i = 0; i < options.cards; i++) {
                    html += '<div class="col-md-4 mb-4">';
                    html += '<div class="card">';
                    html += '<div class="card-body">';
                    html += '<div class="jb-skeleton jb-skeleton-title"></div>';
                    html += '<div class="jb-skeleton jb-skeleton-text"></div>';
                    html += '<div class="jb-skeleton jb-skeleton-text short"></div>';
                    html += '</div></div></div>';
                }
            } else if (options.type === 'list') {
                html += '<div class="list-group">';
                for (var j = 0; j < options.cards; j++) {
                    html += '<div class="list-group-item">';
                    html += '<div class="d-flex align-items-center">';
                    html += '<div class="jb-skeleton jb-skeleton-avatar mr-3"></div>';
                    html += '<div class="flex-grow-1">';
                    html += '<div class="jb-skeleton jb-skeleton-text" style="width: 60%"></div>';
                    html += '<div class="jb-skeleton jb-skeleton-text short"></div>';
                    html += '</div></div></div>';
                }
                html += '</div>';
            } else if (options.type === 'text') {
                for (var k = 0; k < options.cards; k++) {
                    html += '<div class="jb-skeleton jb-skeleton-text mb-2"></div>';
                }
            }

            $container.data('original-content', $container.html());
            $container.html(html);
        },

        /**
         * Remove skeleton loading and restore content.
         *
         * @param {jQuery|string} container Container element or selector
         * @param {string} newContent Optional new content to display
         */
        hideSkeleton: function(container, newContent) {
            var $container = $(container);
            if (!$container.length) {
                return;
            }

            if (newContent !== undefined) {
                $container.html(newContent);
            } else if ($container.data('original-content')) {
                $container.html($container.data('original-content'));
            }
        },

        /**
         * Auto-initialize loading states for forms.
         */
        initFormHandlers: function() {
            var self = this;

            // Handle form submissions
            $(document).on('submit', 'form.mform', function() {
                var $form = $(this);
                var $submitBtn = $form.find('input[type="submit"][name="submitbutton"]');

                if ($submitBtn.length && !$submitBtn.hasClass('is-loading')) {
                    self.buttonLoading($submitBtn,
                        M.util.get_string('processingrequest', 'local_jobboard'));
                }
            });

            // Handle AJAX button clicks
            $(document).on('click', '[data-loading="true"]', function() {
                var $btn = $(this);
                var loadingText = $btn.data('loading-text') ||
                    M.util.get_string('loadinginprogress', 'local_jobboard');
                self.buttonLoading($btn, loadingText);
            });
        }
    };

    return {
        /**
         * Add loading state to a button.
         *
         * @param {jQuery|string} button Button element or selector
         * @param {string} loadingText Optional loading text
         */
        buttonLoading: function(button, loadingText) {
            LoadingStates.buttonLoading(button, loadingText);
        },

        /**
         * Remove loading state from a button.
         *
         * @param {jQuery|string} button Button element or selector
         */
        buttonReset: function(button) {
            LoadingStates.buttonReset(button);
        },

        /**
         * Add loading overlay to a container.
         *
         * @param {jQuery|string} container Container element or selector
         */
        containerLoading: function(container) {
            LoadingStates.containerLoading(container);
        },

        /**
         * Remove loading overlay from a container.
         *
         * @param {jQuery|string} container Container element or selector
         */
        containerReset: function(container) {
            LoadingStates.containerReset(container);
        },

        /**
         * Show skeleton loading placeholders.
         *
         * @param {jQuery|string} container Container element or selector
         * @param {Object} options Skeleton options
         */
        showSkeleton: function(container, options) {
            LoadingStates.showSkeleton(container, options);
        },

        /**
         * Remove skeleton loading.
         *
         * @param {jQuery|string} container Container element or selector
         * @param {string} newContent Optional new content
         */
        hideSkeleton: function(container, newContent) {
            LoadingStates.hideSkeleton(container, newContent);
        },

        /**
         * Initialize form handlers for automatic loading states.
         */
        init: function() {
            LoadingStates.initFormHandlers();
        }
    };
});

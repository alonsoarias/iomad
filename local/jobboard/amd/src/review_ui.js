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
 * AMD module for document review UI enhancements.
 *
 * Handles tooltips, collapsible sections, and other UI interactions
 * for the document review page.
 *
 * @module     local_jobboard/review_ui
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    'use strict';

    /**
     * Initialize Bootstrap tooltips.
     */
    var initTooltips = function() {
        $('[data-toggle="tooltip"]').tooltip();
    };

    /**
     * Initialize collapsible card handlers.
     */
    var initCollapsibles = function() {
        // Handle collapse icon rotation.
        $('.jb-guidelines-card .card-header').on('click', function() {
            var $icon = $(this).find('.jb-collapse-icon');
            var $collapse = $($(this).data('target'));

            $collapse.on('shown.bs.collapse', function() {
                $icon.removeClass('collapsed');
            });

            $collapse.on('hidden.bs.collapse', function() {
                $icon.addClass('collapsed');
            });
        });
    };

    /**
     * Update progress steps based on document review status.
     *
     * @param {Object} stats Document statistics.
     * @param {number} stats.approved Number of approved documents.
     * @param {number} stats.rejected Number of rejected documents.
     * @param {number} stats.pending Number of pending documents.
     */
    var updateReviewProgress = function(stats) {
        var currentStep = 0;

        if (stats.approved + stats.rejected > 0) {
            currentStep = 1;
        }
        if (stats.pending === 0) {
            currentStep = 2;
        }

        $('.jb-step').each(function(i) {
            $(this).removeClass('active completed');
            if (i < currentStep) {
                $(this).addClass('completed');
            }
            if (i === currentStep) {
                $(this).addClass('active');
            }
        });
    };

    /**
     * Initialize the module.
     *
     * @param {Object} options Configuration options.
     * @param {Object} options.stats Initial document statistics.
     */
    var init = function(options) {
        options = options || {};

        // Initialize tooltips.
        initTooltips();

        // Initialize collapsible sections.
        initCollapsibles();

        // Update progress if stats provided.
        if (options.stats) {
            updateReviewProgress(options.stats);
        }
    };

    return {
        init: init,
        initTooltips: initTooltips,
        updateReviewProgress: updateReviewProgress
    };
});

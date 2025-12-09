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
 * AMD module for application form progress tracking.
 *
 * Handles the progress indicator steps and tooltip initialization
 * for the application form.
 *
 * @module     local_jobboard/apply_progress
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    'use strict';

    /**
     * Section IDs to step index mapping.
     * @type {Object}
     */
    var SECTION_STEP_MAP = {
        'consentheader': 0,
        'documentsheader': 1,
        'additionalheader': 2,
        'declarationheader': 3
    };

    /**
     * Update progress steps UI.
     *
     * @param {number} currentStep The current active step index.
     */
    var updateProgressSteps = function(currentStep) {
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
     * Initialize section header click handlers.
     */
    var initSectionHandlers = function() {
        var sections = Object.keys(SECTION_STEP_MAP);

        sections.forEach(function(sectionId) {
            var el = document.getElementById('id_' + sectionId);
            if (el) {
                el.addEventListener('click', function() {
                    var step = SECTION_STEP_MAP[sectionId];
                    updateProgressSteps(step);
                });
            }
        });
    };

    /**
     * Initialize Bootstrap tooltips.
     */
    var initTooltips = function() {
        $('[data-toggle="tooltip"]').tooltip();
    };

    /**
     * Initialize the module.
     *
     * @param {Object} options Configuration options.
     * @param {number} options.initialStep Initial step to highlight (default: 0).
     */
    var init = function(options) {
        options = options || {};

        // Initialize tooltips.
        initTooltips();

        // Initialize section handlers.
        initSectionHandlers();

        // Set initial step if provided.
        if (typeof options.initialStep === 'number') {
            updateProgressSteps(options.initialStep);
        }
    };

    return {
        init: init,
        updateProgressSteps: updateProgressSteps
    };
});

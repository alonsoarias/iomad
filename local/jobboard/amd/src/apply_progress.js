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
 * Handles the progress indicator steps, clickable navigation,
 * and scroll-spy functionality for the application form.
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
        'id_consentheader': 0,
        'id_documentsheader': 1,
        'id_additionalheader': 2,
        'id_declarationheader': 3
    };

    /**
     * Track completed sections.
     * @type {Set}
     */
    var completedSections = new Set();

    /**
     * Update progress steps UI.
     *
     * @param {number} currentStep The current active step index.
     */
    var updateProgressSteps = function(currentStep) {
        $('.jb-step').each(function(i) {
            var $step = $(this);
            $step.removeClass('active');

            if (i < currentStep || completedSections.has(i)) {
                $step.addClass('completed');
                // Show checkmark, hide number.
                $step.find('.jb-step-number').addClass('d-none');
                $step.find('.jb-step-checkmark').removeClass('d-none');
            } else {
                $step.removeClass('completed');
                // Show number, hide checkmark.
                $step.find('.jb-step-number').removeClass('d-none');
                $step.find('.jb-step-checkmark').addClass('d-none');
            }

            if (i === currentStep) {
                $step.addClass('active');
            }
        });
    };

    /**
     * Smooth scroll to target element.
     *
     * @param {string} targetId The target element ID.
     */
    var scrollToSection = function(targetId) {
        var $target = $('#' + targetId);
        if ($target.length) {
            // Expand the header if it's collapsed.
            var $content = $target.next('.fcontainer, .fitem, .collapsible-actions');
            if ($target.hasClass('collapsed')) {
                $target.removeClass('collapsed');
                $target.attr('aria-expanded', 'true');
            }

            // Scroll with offset for fixed header.
            var offset = 80;
            $('html, body').animate({
                scrollTop: $target.offset().top - offset
            }, 300);
        }
    };

    /**
     * Initialize clickable progress step handlers.
     */
    var initStepClickHandlers = function() {
        $('.jb-step[data-target]').on('click', function(e) {
            e.preventDefault();

            var targetId = $(this).data('target');
            var stepIndex = parseInt($(this).data('step'), 10) - 1;

            scrollToSection(targetId);
            updateProgressSteps(stepIndex);
        });
    };

    /**
     * Initialize section header click handlers for backward compatibility.
     */
    var initSectionHandlers = function() {
        var sections = Object.keys(SECTION_STEP_MAP);

        sections.forEach(function(sectionId) {
            var el = document.getElementById(sectionId);
            if (el) {
                el.addEventListener('click', function() {
                    var step = SECTION_STEP_MAP[sectionId];
                    updateProgressSteps(step);
                });
            }
        });
    };

    /**
     * Initialize scroll-spy to update progress as user scrolls.
     */
    var initScrollSpy = function() {
        var sections = Object.keys(SECTION_STEP_MAP);
        var offset = 150; // Offset from top.

        $(window).on('scroll', function() {
            var scrollPos = $(window).scrollTop() + offset;
            var currentStep = 0;

            sections.forEach(function(sectionId, index) {
                var $section = $('#' + sectionId);
                if ($section.length && $section.offset().top <= scrollPos) {
                    currentStep = index;
                }
            });

            // Update active step without removing completed status.
            $('.jb-step').removeClass('active');
            $('.jb-step').eq(currentStep).addClass('active');
        });
    };

    /**
     * Mark a section as completed.
     *
     * @param {number} stepIndex The step index to mark as completed.
     */
    var markSectionCompleted = function(stepIndex) {
        completedSections.add(stepIndex);
        var $step = $('.jb-step').eq(stepIndex);
        $step.addClass('completed');
        $step.find('.jb-step-number').addClass('d-none');
        $step.find('.jb-step-checkmark').removeClass('d-none');
    };

    /**
     * Check for filled form fields and mark sections complete.
     */
    var checkFormCompletion = function() {
        // Check consent section.
        var consentChecked = $('#id_consentaccepted').is(':checked');
        var signatureFilled = $('#id_digitalsignature').val().trim() !== '';
        if (consentChecked && signatureFilled) {
            markSectionCompleted(0);
        }

        // Check if any files are uploaded (documents section).
        var hasFiles = $('input[name^="doc_"]').filter(function() {
            return $(this).val() !== '';
        }).length > 0;
        if (hasFiles) {
            markSectionCompleted(1);
        }

        // Check declaration section.
        var declarationChecked = $('#id_declarationaccepted').is(':checked');
        if (declarationChecked) {
            markSectionCompleted(3);
        }
    };

    /**
     * Initialize form field change listeners.
     */
    var initFormListeners = function() {
        // Consent checkbox and signature.
        $('#id_consentaccepted, #id_digitalsignature').on('change keyup', function() {
            var consentChecked = $('#id_consentaccepted').is(':checked');
            var signatureFilled = $('#id_digitalsignature').val().trim() !== '';
            if (consentChecked && signatureFilled) {
                markSectionCompleted(0);
            }
        });

        // Declaration checkbox.
        $('#id_declarationaccepted').on('change', function() {
            if ($(this).is(':checked')) {
                markSectionCompleted(3);
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

        // Initialize clickable step handlers.
        initStepClickHandlers();

        // Initialize section handlers.
        initSectionHandlers();

        // Initialize scroll spy.
        initScrollSpy();

        // Initialize form listeners.
        initFormListeners();

        // Check initial form completion.
        checkFormCompletion();

        // Set initial step if provided.
        if (typeof options.initialStep === 'number') {
            updateProgressSteps(options.initialStep);
        }
    };

    return {
        init: init,
        updateProgressSteps: updateProgressSteps,
        markSectionCompleted: markSectionCompleted
    };
});

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
 * AMD module for application form tab-based navigation.
 *
 * Handles the progress indicator steps and tab switching
 * functionality for the application form.
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
    var SECTION_HEADERS = [
        'id_consentheader',
        'id_documentsheader',
        'id_additionalheader',
        'id_declarationheader'
    ];

    /**
     * Current active step (0-indexed).
     * @type {number}
     */
    var currentStep = 0;

    /**
     * Total number of steps.
     * @type {number}
     */
    var totalSteps = 4;

    /**
     * Track completed sections.
     * @type {Set}
     */
    var completedSections = new Set();

    /**
     * Language strings.
     * @type {Object}
     */
    var strings = {
        previous: 'Previous',
        next: 'Next',
        submit: 'Submit Application',
        step: 'Step',
        of: 'of',
        completerequiredfields: 'Please complete all required fields before continuing.'
    };

    /**
     * Tab mode enabled.
     * @type {boolean}
     */
    var tabMode = false;

    /**
     * Get the fieldset container for a section header.
     *
     * @param {string} headerId The header element ID.
     * @return {jQuery} The fieldset element.
     */
    var getSectionFieldset = function(headerId) {
        var $header = $('#' + headerId);
        // In Moodle forms, the header is inside a fieldset.
        return $header.closest('fieldset');
    };

    /**
     * Show a specific tab/section.
     *
     * @param {number} stepIndex The step index to show (0-indexed).
     */
    var showTab = function(stepIndex) {
        if (stepIndex < 0 || stepIndex >= totalSteps) {
            return;
        }

        // Hide all sections.
        SECTION_HEADERS.forEach(function(headerId) {
            var $fieldset = getSectionFieldset(headerId);
            $fieldset.addClass('jb-tab-hidden');
        });

        // Show the target section.
        var targetHeader = SECTION_HEADERS[stepIndex];
        var $targetFieldset = getSectionFieldset(targetHeader);
        $targetFieldset.removeClass('jb-tab-hidden');

        // Expand the header if collapsed.
        var $header = $('#' + targetHeader);
        if ($header.hasClass('collapsed')) {
            $header.removeClass('collapsed');
            $header.attr('aria-expanded', 'true');
        }

        // Update current step.
        currentStep = stepIndex;

        // Update progress steps UI.
        updateProgressSteps(stepIndex);

        // Update navigation buttons.
        updateNavigationButtons();

        // Scroll to top of form.
        $('html, body').animate({
            scrollTop: $('#application-progress').offset().top - 20
        }, 300);
    };

    /**
     * Update progress steps UI.
     *
     * @param {number} activeStep The current active step index.
     */
    var updateProgressSteps = function(activeStep) {
        $('.jb-step').each(function(i) {
            var $step = $(this);
            $step.removeClass('active');
            $step.attr('aria-selected', 'false');

            if (completedSections.has(i)) {
                $step.addClass('completed');
                $step.find('.jb-step-number').addClass('d-none');
                $step.find('.jb-step-checkmark').removeClass('d-none');
            } else {
                $step.removeClass('completed');
                $step.find('.jb-step-number').removeClass('d-none');
                $step.find('.jb-step-checkmark').addClass('d-none');
            }

            if (i === activeStep) {
                $step.addClass('active');
                $step.attr('aria-selected', 'true');
            }
        });

        // Update step connectors.
        $('.jb-step-connector').each(function(i) {
            if (i < activeStep || completedSections.has(i)) {
                $(this).addClass('completed');
            } else {
                $(this).removeClass('completed');
            }
        });
    };

    /**
     * Update navigation buttons based on current step.
     */
    var updateNavigationButtons = function() {
        var $navContainer = $('#jb-tab-navigation');
        $navContainer.removeClass('d-none').empty();

        var html = '<div class="d-flex justify-content-between align-items-center">';

        // Previous button (hidden on first step).
        if (currentStep > 0) {
            html += '<button type="button" class="btn btn-outline-secondary jb-btn-prev">';
            html += '<i class="fa fa-arrow-left mr-2"></i>' + strings.previous;
            html += '</button>';
        } else {
            html += '<div></div>'; // Placeholder for alignment.
        }

        // Step indicator.
        html += '<span class="text-muted small">';
        html += strings.step + ' ' + (currentStep + 1) + ' ' + strings.of + ' ' + totalSteps;
        html += '</span>';

        // Next button or show submit button on last step.
        if (currentStep < totalSteps - 1) {
            html += '<button type="button" class="btn btn-primary jb-btn-next">';
            html += strings.next + ' <i class="fa fa-arrow-right ml-2"></i>';
            html += '</button>';
        } else {
            // On last step, show a hint about the submit button.
            html += '<span class="text-success small">';
            html += '<i class="fa fa-check-circle mr-1"></i>' + strings.submit;
            html += '</span>';
        }

        html += '</div>';

        $navContainer.html(html);

        // Bind button events.
        $navContainer.find('.jb-btn-prev').on('click', function(e) {
            e.preventDefault();
            goToPreviousStep();
        });

        $navContainer.find('.jb-btn-next').on('click', function(e) {
            e.preventDefault();
            goToNextStep();
        });
    };

    /**
     * Validate current section before moving to next.
     *
     * @return {boolean} True if validation passes.
     */
    var validateCurrentSection = function() {
        var headerId = SECTION_HEADERS[currentStep];
        var $fieldset = getSectionFieldset(headerId);

        var isValid = true;

        // Check required fields in current section.
        $fieldset.find('input[required], select[required], textarea[required]').each(function() {
            if (!this.checkValidity()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Special validation for consent section.
        if (currentStep === 0) {
            var consentChecked = $('#id_consentaccepted').is(':checked');
            var signatureFilled = $('#id_digitalsignature').val().trim() !== '';
            if (!consentChecked || !signatureFilled) {
                isValid = false;
                if (!consentChecked) {
                    $('#id_consentaccepted').addClass('is-invalid');
                }
                if (!signatureFilled) {
                    $('#id_digitalsignature').addClass('is-invalid');
                }
            }
        }

        // Special validation for declaration section.
        if (currentStep === 3) {
            var declarationChecked = $('#id_declarationaccepted').is(':checked');
            if (!declarationChecked) {
                isValid = false;
                $('#id_declarationaccepted').addClass('is-invalid');
            }
        }

        return isValid;
    };

    /**
     * Go to the next step.
     */
    var goToNextStep = function() {
        if (currentStep >= totalSteps - 1) {
            return;
        }

        // Validate current section.
        if (!validateCurrentSection()) {
            // Show validation message.
            showValidationAlert();
            return;
        }

        // Mark current section as completed.
        markSectionCompleted(currentStep);

        // Go to next step.
        showTab(currentStep + 1);
    };

    /**
     * Go to the previous step.
     */
    var goToPreviousStep = function() {
        if (currentStep <= 0) {
            return;
        }

        showTab(currentStep - 1);
    };

    /**
     * Show validation alert.
     */
    var showValidationAlert = function() {
        // Remove existing alert.
        $('.jb-validation-alert').remove();

        var $alert = $('<div class="alert alert-warning jb-validation-alert mt-3 mb-3">' +
            '<i class="fa fa-exclamation-triangle mr-2"></i>' +
            strings.completerequiredfields +
            '</div>');

        $('#jb-tab-navigation').after($alert);

        // Auto-dismiss after 5 seconds.
        setTimeout(function() {
            $alert.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    };

    /**
     * Mark a section as completed.
     *
     * @param {number} stepIndex The step index to mark as completed.
     */
    var markSectionCompleted = function(stepIndex) {
        completedSections.add(stepIndex);
        updateProgressSteps(currentStep);
    };

    /**
     * Initialize clickable progress step handlers.
     */
    var initStepClickHandlers = function() {
        $('.jb-step[data-target]').on('click', function(e) {
            e.preventDefault();

            var stepIndex = parseInt($(this).data('step'), 10) - 1;

            // Only allow going to completed steps or current step.
            if (stepIndex <= currentStep || completedSections.has(stepIndex - 1)) {
                // Validate before moving forward.
                if (stepIndex > currentStep && !validateCurrentSection()) {
                    showValidationAlert();
                    return;
                }

                // Mark current as completed if moving forward.
                if (stepIndex > currentStep) {
                    markSectionCompleted(currentStep);
                }

                showTab(stepIndex);
            }
        });
    };

    /**
     * Initialize form field change listeners for auto-completion detection.
     */
    var initFormListeners = function() {
        // Consent checkbox and signature.
        $('#id_consentaccepted, #id_digitalsignature').on('change keyup', function() {
            var consentChecked = $('#id_consentaccepted').is(':checked');
            var signatureFilled = $('#id_digitalsignature').val().trim() !== '';
            if (consentChecked && signatureFilled) {
                $(this).removeClass('is-invalid');
            }
        });

        // Declaration checkbox.
        $('#id_declarationaccepted').on('change', function() {
            if ($(this).is(':checked')) {
                $(this).removeClass('is-invalid');
            }
        });

        // Clear validation state on input.
        $('input, select, textarea').on('input change', function() {
            if (this.checkValidity()) {
                $(this).removeClass('is-invalid');
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
     * Add CSS for hidden tabs.
     */
    var addTabStyles = function() {
        if ($('#jb-tab-styles').length === 0) {
            var styles = '<style id="jb-tab-styles">' +
                '.jb-tab-hidden { display: none !important; }' +
                '.jb-step { cursor: pointer; transition: all 0.2s ease; }' +
                '.jb-step:hover:not(.active) { opacity: 0.8; }' +
                '.jb-step.completed .jb-step-icon { background-color: #28a745 !important; border-color: #28a745 !important; }' +
                '.jb-step-connector.completed { background-color: #28a745 !important; }' +
                '.jb-validation-alert { animation: fadeIn 0.3s ease; }' +
                '@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }' +
                '</style>';
            $('head').append(styles);
        }
    };

    /**
     * Initialize the module.
     *
     * @param {Object} options Configuration options.
     * @param {number} options.initialStep Initial step to highlight (default: 0).
     * @param {boolean} options.tabMode Enable tab mode (default: false).
     * @param {Object} options.strings Language strings.
     */
    var init = function(options) {
        options = options || {};

        // Set tab mode.
        tabMode = options.tabMode || false;

        // Set language strings.
        if (options.strings) {
            $.extend(strings, options.strings);
        }

        // Add tab styles.
        addTabStyles();

        // Initialize tooltips.
        initTooltips();

        // Initialize clickable step handlers.
        initStepClickHandlers();

        // Initialize form listeners.
        initFormListeners();

        if (tabMode) {
            // Initialize tab mode - show only first section.
            showTab(options.initialStep || 0);
        } else {
            // Legacy scroll mode.
            if (typeof options.initialStep === 'number') {
                updateProgressSteps(options.initialStep);
            }
        }
    };

    return {
        init: init,
        showTab: showTab,
        updateProgressSteps: updateProgressSteps,
        markSectionCompleted: markSectionCompleted,
        goToNextStep: goToNextStep,
        goToPreviousStep: goToPreviousStep
    };
});

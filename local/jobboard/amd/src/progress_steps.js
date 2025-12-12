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
 * Progress steps module for multi-step form navigation.
 *
 * @module     local_jobboard/progress_steps
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/str'], function(Str) {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        container: null,
        steps: [],
        sectionMap: {},
        currentStep: 0,
        initialized: false
    };

    /**
     * CSS classes used by the module.
     * @type {Object}
     */
    var CLASSES = {
        step: 'jb-progress-step',
        stepActive: 'jb-progress-step-active',
        stepCompleted: 'jb-progress-step-completed',
        stepDisabled: 'jb-progress-step-disabled',
        stepNumber: 'jb-progress-step-number',
        stepLabel: 'jb-progress-step-label',
        connector: 'jb-progress-connector',
        connectorCompleted: 'jb-progress-connector-completed'
    };

    /**
     * Build the progress steps HTML.
     *
     * @return {string} HTML string for progress steps.
     */
    var buildStepsHtml = function() {
        var html = '<div class="jb-progress-steps jb-d-flex jb-justify-content-between">';

        state.steps.forEach(function(step, index) {
            var stepClass = CLASSES.step;
            if (index === state.currentStep) {
                stepClass += ' ' + CLASSES.stepActive;
            } else if (index < state.currentStep) {
                stepClass += ' ' + CLASSES.stepCompleted;
            } else {
                stepClass += ' ' + CLASSES.stepDisabled;
            }

            html += '<div class="' + stepClass + '" data-step="' + index + '">';
            html += '<span class="' + CLASSES.stepNumber + '">' + (index + 1) + '</span>';
            html += '<span class="' + CLASSES.stepLabel + '">' + step.label + '</span>';
            html += '</div>';

            // Add connector between steps (except after last).
            if (index < state.steps.length - 1) {
                var connectorClass = CLASSES.connector;
                if (index < state.currentStep) {
                    connectorClass += ' ' + CLASSES.connectorCompleted;
                }
                html += '<div class="' + connectorClass + '"></div>';
            }
        });

        html += '</div>';
        return html;
    };

    /**
     * Render the progress steps.
     */
    var render = function() {
        if (!state.container) {
            return;
        }
        state.container.innerHTML = buildStepsHtml();
    };

    /**
     * Show the section for a given step.
     *
     * @param {number} stepIndex The step index.
     */
    var showSection = function(stepIndex) {
        Object.keys(state.sectionMap).forEach(function(index) {
            var sectionId = state.sectionMap[index];
            var section = document.getElementById(sectionId);
            if (section) {
                section.style.display = parseInt(index, 10) === stepIndex ? '' : 'none';
            }
        });
    };

    /**
     * Go to a specific step.
     *
     * @param {number} stepIndex The step index to go to.
     */
    var goToStep = function(stepIndex) {
        if (stepIndex < 0 || stepIndex >= state.steps.length) {
            return;
        }

        state.currentStep = stepIndex;
        render();
        showSection(stepIndex);

        // Dispatch custom event.
        var event = new CustomEvent('jobboard:stepchange', {
            detail: {
                step: stepIndex,
                stepData: state.steps[stepIndex]
            }
        });
        document.dispatchEvent(event);
    };

    /**
     * Go to the next step.
     */
    var nextStep = function() {
        goToStep(state.currentStep + 1);
    };

    /**
     * Go to the previous step.
     */
    var prevStep = function() {
        goToStep(state.currentStep - 1);
    };

    /**
     * Get the current step index.
     *
     * @return {number} Current step index.
     */
    var getCurrentStep = function() {
        return state.currentStep;
    };

    /**
     * Mark current step as completed and go to next.
     */
    var completeCurrentStep = function() {
        if (state.currentStep < state.steps.length - 1) {
            nextStep();
        }
    };

    /**
     * Initialize the progress steps module.
     *
     * @param {Object} config Configuration object.
     * @param {string} config.containerId ID of the container element.
     * @param {Array} config.steps Array of step objects with 'label' property.
     * @param {Object} config.sectionMap Map of step index to section ID.
     * @param {number} [config.initialStep=0] Initial step index.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        config = config || {};

        state.container = document.getElementById(config.containerId);
        state.steps = config.steps || [];
        state.sectionMap = config.sectionMap || {};
        state.currentStep = config.initialStep || 0;

        if (state.container && state.steps.length > 0) {
            render();
            showSection(state.currentStep);
        }

        state.initialized = true;
    };

    return {
        init: init,
        goToStep: goToStep,
        nextStep: nextStep,
        prevStep: prevStep,
        getCurrentStep: getCurrentStep,
        completeCurrentStep: completeCurrentStep
    };
});

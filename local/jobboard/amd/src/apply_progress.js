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
 * Application progress module for step-based form navigation.
 *
 * Provides visual progress indication and smooth scrolling between form sections.
 * Works with Moodle form fieldsets (headers) as navigation targets.
 *
 * @module     local_jobboard/apply_progress
 * @copyright  2024-2025 ISER - Instituto Superior de Educacion Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        currentStep: 0,
        totalSteps: 0,
        steps: [],
        fieldsets: [],
        strings: {},
        tabMode: true,
        initialized: false
    };

    /**
     * CSS classes used by the module.
     * @type {Object}
     */
    var CLASSES = {
        stepActive: 'jb-active',
        stepCompleted: 'jb-completed',
        stepDisabled: 'jb-tab-disabled'
    };

    /**
     * Get the fieldset element for a step by target ID.
     *
     * @param {string} targetId The target element ID.
     * @return {HTMLElement|null} The fieldset element.
     */
    var getFieldsetByTarget = function(targetId) {
        if (!targetId) {
            return null;
        }

        // Try to find element directly.
        var element = document.getElementById(targetId);

        // If not found, try with 'id_' prefix (Moodle form convention).
        if (!element && !targetId.startsWith('id_')) {
            element = document.getElementById('id_' + targetId);
        }

        if (!element) {
            return null;
        }

        // Look for the parent fieldset containing the header.
        var parentFieldset = element.closest('fieldset');
        return parentFieldset || element;
    };

    /**
     * Update step visual states.
     */
    var updateStepStates = function() {
        state.steps.forEach(function(step, index) {
            step.classList.remove(CLASSES.stepActive, CLASSES.stepCompleted, CLASSES.stepDisabled);

            if (index === state.currentStep) {
                step.classList.add(CLASSES.stepActive);
            } else if (index < state.currentStep) {
                step.classList.add(CLASSES.stepCompleted);
            } else {
                step.classList.add(CLASSES.stepDisabled);
            }

            // Update aria attributes.
            step.setAttribute('aria-current', index === state.currentStep ? 'step' : 'false');
        });
    };

    /**
     * Scroll to a fieldset smoothly.
     *
     * @param {HTMLElement} fieldset The fieldset to scroll to.
     */
    var scrollToFieldset = function(fieldset) {
        if (!fieldset) {
            return;
        }

        // Account for sticky headers (navbar).
        var offset = 120;
        var rect = fieldset.getBoundingClientRect();
        var scrollTop = window.pageYOffset + rect.top - offset;

        window.scrollTo({
            top: Math.max(0, scrollTop),
            behavior: 'smooth'
        });

        // Focus the legend or first focusable element.
        setTimeout(function() {
            var legend = fieldset.querySelector('legend');
            if (legend) {
                legend.setAttribute('tabindex', '-1');
                legend.focus();
            } else {
                var focusable = fieldset.querySelector(
                    'input:not([type="hidden"]), select, textarea, button, [tabindex]:not([tabindex="-1"])'
                );
                if (focusable) {
                    focusable.focus();
                }
            }
        }, 500);
    };

    /**
     * Go to a specific step.
     *
     * @param {number} stepIndex The step index to navigate to.
     */
    var goToStep = function(stepIndex) {
        if (stepIndex < 0 || stepIndex >= state.totalSteps) {
            return;
        }

        state.currentStep = stepIndex;
        updateStepStates();

        var step = state.steps[stepIndex];
        if (step && step.dataset.target) {
            var fieldset = getFieldsetByTarget(step.dataset.target);
            if (fieldset) {
                scrollToFieldset(fieldset);

                // Expand if collapsed (Moodle collapse).
                var collapseBody = fieldset.querySelector('.fcontainer.collapsible');
                if (collapseBody && collapseBody.classList.contains('collapsed')) {
                    var toggleLink = fieldset.querySelector('a.fheader');
                    if (toggleLink) {
                        toggleLink.click();
                    }
                }
            }
        }

        // Dispatch custom event.
        document.dispatchEvent(new CustomEvent('jobboard:stepchange', {
            detail: {
                step: stepIndex,
                totalSteps: state.totalSteps
            }
        }));
    };

    /**
     * Handle step click.
     *
     * @param {Event} e The click event.
     */
    var onStepClick = function(e) {
        var step = e.target.closest('.jb-step[data-step]');
        if (!step) {
            return;
        }

        e.preventDefault();

        var stepIndex = parseInt(step.dataset.step, 10) - 1; // Steps are 1-indexed in HTML.

        // In tab mode, allow clicking completed steps or the next step.
        if (state.tabMode) {
            if (stepIndex <= state.currentStep || stepIndex === state.currentStep + 1) {
                goToStep(stepIndex);
            }
        } else {
            // Just scroll without changing progress.
            if (step.dataset.target) {
                var fieldset = getFieldsetByTarget(step.dataset.target);
                if (fieldset) {
                    scrollToFieldset(fieldset);
                }
            }
        }
    };

    /**
     * Calculate which step is currently visible based on scroll position.
     *
     * @return {number} The index of the currently visible step.
     */
    var calculateCurrentStepFromScroll = function() {
        var scrollPosition = window.pageYOffset + 200;

        for (var i = state.steps.length - 1; i >= 0; i--) {
            var step = state.steps[i];
            if (step && step.dataset.target) {
                var fieldset = getFieldsetByTarget(step.dataset.target);
                if (fieldset && fieldset.offsetTop <= scrollPosition) {
                    return i;
                }
            }
        }

        return 0;
    };

    /**
     * Update current step based on scroll position.
     */
    var updateStepOnScroll = function() {
        var newStep = calculateCurrentStepFromScroll();
        if (newStep !== state.currentStep) {
            state.currentStep = newStep;
            updateStepStates();
        }
    };

    /**
     * Debounce function.
     *
     * @param {Function} func Function to debounce.
     * @param {number} wait Wait time in ms.
     * @return {Function} Debounced function.
     */
    var debounce = function(func, wait) {
        var timeout;
        return function() {
            var context = this;
            var args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    };

    /**
     * Initialize the module.
     *
     * @param {Object} config Configuration object.
     * @param {number} config.initialStep Initial step index (0-based).
     * @param {boolean} config.tabMode Enable click navigation on steps.
     * @param {string} config.stepSelector CSS selector for step elements.
     * @param {Object} config.strings Localized strings.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        config = config || {};

        // Collect step elements.
        var stepSelector = config.stepSelector || '.jb-step[data-step]';
        state.steps = Array.from(document.querySelectorAll(stepSelector));
        state.totalSteps = state.steps.length;

        if (state.totalSteps === 0) {
            // Try alternative selector.
            state.steps = Array.from(document.querySelectorAll('[data-step]'));
            state.totalSteps = state.steps.length;
        }

        if (state.totalSteps === 0) {
            return;
        }

        // Set initial state.
        state.currentStep = config.initialStep || 0;
        state.tabMode = config.tabMode !== false;
        state.strings = config.strings || {};

        // Initial update.
        updateStepStates();

        // Event listeners.
        document.addEventListener('click', onStepClick);

        // Scroll-based progress update (debounced).
        var debouncedScrollUpdate = debounce(updateStepOnScroll, 150);
        window.addEventListener('scroll', debouncedScrollUpdate, {passive: true});

        state.initialized = true;
    };

    return {
        init: init,
        goToStep: goToStep,
        getCurrentStep: function() {
            return state.currentStep;
        },
        getTotalSteps: function() {
            return state.totalSteps;
        }
    };
});

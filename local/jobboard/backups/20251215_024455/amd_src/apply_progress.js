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
 * Application progress module for tab-based form navigation.
 *
 * @module     local_jobboard/apply_progress
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/str', 'core/notification'], function(Str, Notification) {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        currentStep: 0,
        totalSteps: 0,
        tabs: [],
        sections: [],
        strings: {},
        tabMode: false,
        initialized: false
    };

    /**
     * CSS classes used by the module.
     * @type {Object}
     */
    var CLASSES = {
        tabActive: 'jb-tab-active',
        tabCompleted: 'jb-tab-completed',
        tabDisabled: 'jb-tab-disabled',
        sectionVisible: 'jb-section-visible',
        sectionHidden: 'jb-section-hidden',
        navButton: 'jb-nav-button'
    };

    /**
     * Validate a step/section.
     *
     * @param {number} stepIndex The step index to validate.
     * @return {boolean} Whether the step is valid.
     */
    var validateStep = function(stepIndex) {
        var section = state.sections[stepIndex];
        if (!section) {
            return true;
        }

        // Check for required fields.
        var requiredFields = section.querySelectorAll('[required]');
        var valid = true;

        requiredFields.forEach(function(field) {
            if (!field.value || field.value.trim() === '') {
                valid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return valid;
    };

    /**
     * Update tab states based on current step.
     */
    var updateTabs = function() {
        state.tabs.forEach(function(tab, index) {
            tab.classList.remove(CLASSES.tabActive, CLASSES.tabCompleted, CLASSES.tabDisabled);

            if (index === state.currentStep) {
                tab.classList.add(CLASSES.tabActive);
            } else if (index < state.currentStep) {
                tab.classList.add(CLASSES.tabCompleted);
            } else {
                tab.classList.add(CLASSES.tabDisabled);
            }
        });
    };

    /**
     * Show only the current section.
     */
    var showCurrentSection = function() {
        state.sections.forEach(function(section, index) {
            if (index === state.currentStep) {
                section.classList.remove(CLASSES.sectionHidden);
                section.classList.add(CLASSES.sectionVisible);
                section.style.display = '';
            } else {
                section.classList.remove(CLASSES.sectionVisible);
                section.classList.add(CLASSES.sectionHidden);
                section.style.display = 'none';
            }
        });
    };

    /**
     * Update navigation buttons.
     */
    var updateNavButtons = function() {
        var prevButton = document.querySelector('[data-action="prev-step"]');
        var nextButton = document.querySelector('[data-action="next-step"]');
        var submitButton = document.querySelector('[data-action="submit-application"]');

        if (prevButton) {
            prevButton.style.display = state.currentStep > 0 ? '' : 'none';
        }

        if (nextButton) {
            nextButton.style.display = state.currentStep < state.totalSteps - 1 ? '' : 'none';
        }

        if (submitButton) {
            submitButton.style.display = state.currentStep === state.totalSteps - 1 ? '' : 'none';
        }
    };

    /**
     * Go to a specific step.
     *
     * @param {number} stepIndex The step index to go to.
     * @param {boolean} [skipValidation=false] Whether to skip validation.
     */
    var goToStep = function(stepIndex, skipValidation) {
        // Validate current step before moving forward.
        if (!skipValidation && stepIndex > state.currentStep) {
            if (!validateStep(state.currentStep)) {
                Str.get_string('pleasecompleterequiredfields', 'local_jobboard').then(function(msg) {
                    Notification.addNotification({
                        message: msg,
                        type: 'error'
                    });
                });
                return;
            }
        }

        if (stepIndex < 0 || stepIndex >= state.totalSteps) {
            return;
        }

        state.currentStep = stepIndex;
        updateTabs();
        showCurrentSection();
        updateNavButtons();

        // Scroll to top of section.
        var section = state.sections[stepIndex];
        if (section) {
            section.scrollIntoView({behavior: 'smooth', block: 'start'});
        }

        // Dispatch event.
        var event = new CustomEvent('jobboard:applystepchange', {
            detail: {
                step: stepIndex,
                totalSteps: state.totalSteps
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
        goToStep(state.currentStep - 1, true);
    };

    /**
     * Handle tab click.
     *
     * @param {Event} e The click event.
     */
    var onTabClick = function(e) {
        var tab = e.target.closest('[data-step]');
        if (!tab) {
            return;
        }

        e.preventDefault();
        var stepIndex = parseInt(tab.dataset.step, 10);

        // Only allow clicking on completed tabs or current+1 (if tab mode allows).
        if (state.tabMode && stepIndex <= state.currentStep + 1) {
            goToStep(stepIndex);
        }
    };

    /**
     * Handle navigation button clicks.
     *
     * @param {Event} e The click event.
     */
    var onNavClick = function(e) {
        var button = e.target.closest('[data-action]');
        if (!button) {
            return;
        }

        var action = button.dataset.action;

        switch (action) {
            case 'next-step':
                e.preventDefault();
                nextStep();
                break;
            case 'prev-step':
                e.preventDefault();
                prevStep();
                break;
        }
    };

    /**
     * Initialize the apply progress module.
     *
     * @param {Object} config Configuration object.
     * @param {number} [config.initialStep=0] Initial step index.
     * @param {boolean} [config.tabMode=false] Whether to enable tab click navigation.
     * @param {string} [config.tabSelector='[data-step]'] CSS selector for tabs.
     * @param {string} [config.sectionSelector='.jb-apply-section'] CSS selector for sections.
     * @param {Object} [config.strings] Localized strings.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        config = config || {};

        state.currentStep = config.initialStep || 0;
        state.tabMode = config.tabMode !== false;
        state.strings = config.strings || {};

        // Collect tabs and sections.
        state.tabs = Array.from(document.querySelectorAll(config.tabSelector || '[data-step]'));
        state.sections = Array.from(document.querySelectorAll(config.sectionSelector || '.jb-apply-section'));
        state.totalSteps = Math.max(state.tabs.length, state.sections.length);

        if (state.totalSteps === 0) {
            return;
        }

        // Initial state.
        updateTabs();
        showCurrentSection();
        updateNavButtons();

        // Event listeners.
        document.body.addEventListener('click', onTabClick);
        document.body.addEventListener('click', onNavClick);

        state.initialized = true;
    };

    return {
        init: init,
        goToStep: goToStep,
        nextStep: nextStep,
        prevStep: prevStep,
        getCurrentStep: function() {
            return state.currentStep;
        }
    };
});

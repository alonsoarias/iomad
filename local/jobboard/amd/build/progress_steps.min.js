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
 * Progress steps indicator module for multi-step forms.
 *
 * @module     local_jobboard/progress_steps
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    'use strict';

    /**
     * Progress steps class.
     *
     * @param {Object} config Configuration options
     * @param {string} config.containerId Container element ID
     * @param {Array} config.steps Array of step definitions
     * @param {Object} config.sectionMap Map of form section IDs to step indices
     */
    var ProgressSteps = function(config) {
        this.config = $.extend({
            containerId: 'jb-progress-container',
            steps: [],
            sectionMap: {},
            currentStep: 0
        }, config);

        this.$container = $('#' + this.config.containerId);
        this.init();
    };

    /**
     * Initialize the progress steps.
     */
    ProgressSteps.prototype.init = function() {
        if (!this.$container.length) {
            return;
        }

        this.renderSteps();
        this.bindEvents();
        this.updateStep(this.config.currentStep);
    };

    /**
     * Render the progress steps HTML.
     */
    ProgressSteps.prototype.renderSteps = function() {
        var html = '<div class="jb-progress-container">';
        html += '<ul class="jb-progress-steps" role="progressbar" aria-label="' +
                M.util.get_string('signup_progress', 'local_jobboard') + '">';

        this.config.steps.forEach(function(step, index) {
            var stepClass = 'jb-step';
            var ariaLabel = step.label;

            if (index < this.config.currentStep) {
                stepClass += ' completed';
                ariaLabel += ' - ' + M.util.get_string('completedstep', 'local_jobboard');
            } else if (index === this.config.currentStep) {
                stepClass += ' active';
                ariaLabel += ' - ' + M.util.get_string('currentstep', 'local_jobboard');
            } else {
                ariaLabel += ' - ' + M.util.get_string('pendingstep', 'local_jobboard');
            }

            html += '<li class="' + stepClass + '" data-step="' + index + '" aria-label="' + ariaLabel + '">';
            html += '<div class="jb-step-icon"><i class="fa fa-' + step.icon + '"></i></div>';
            html += '<span class="jb-step-label">' + step.label + '</span>';
            html += '</li>';
        }.bind(this));

        html += '</ul></div>';

        this.$container.html(html);
    };

    /**
     * Bind event handlers.
     */
    ProgressSteps.prototype.bindEvents = function() {
        var self = this;

        // Listen for form section expansions
        Object.keys(this.config.sectionMap).forEach(function(sectionId) {
            var $section = $('#id_' + sectionId);
            if ($section.length) {
                $section.on('click', function() {
                    var stepIndex = self.config.sectionMap[sectionId];
                    self.updateStep(stepIndex);
                });
            }
        });

        // Listen for field changes to mark sections as complete
        $('form.mform').on('change', 'input, select, textarea', function() {
            self.checkSectionCompletion();
        });
    };

    /**
     * Update the current step.
     *
     * @param {number} stepIndex Step index to set as current
     */
    ProgressSteps.prototype.updateStep = function(stepIndex) {
        this.config.currentStep = stepIndex;

        this.$container.find('.jb-step').each(function(index) {
            var $step = $(this);
            $step.removeClass('active completed');

            if (index < stepIndex) {
                $step.addClass('completed');
            } else if (index === stepIndex) {
                $step.addClass('active');
            }
        });
    };

    /**
     * Mark a step as completed.
     *
     * @param {number} stepIndex Step index to mark as completed
     */
    ProgressSteps.prototype.markCompleted = function(stepIndex) {
        var $step = this.$container.find('.jb-step').eq(stepIndex);
        $step.addClass('completed');
    };

    /**
     * Check section completion based on required fields.
     */
    ProgressSteps.prototype.checkSectionCompletion = function() {
        var self = this;

        Object.keys(this.config.sectionMap).forEach(function(sectionId) {
            var $section = $('#id_' + sectionId);
            if (!$section.length) {
                return;
            }

            var $fieldset = $section.closest('fieldset');
            var $requiredFields = $fieldset.find('[required], .required input, .required select, .required textarea');
            var allFilled = true;

            $requiredFields.each(function() {
                if (!$(this).val()) {
                    allFilled = false;
                    return false;
                }
            });

            if (allFilled && $requiredFields.length > 0) {
                var stepIndex = self.config.sectionMap[sectionId];
                self.markCompleted(stepIndex);
            }
        });
    };

    return {
        /**
         * Initialize progress steps.
         *
         * @param {Object} config Configuration object
         * @return {ProgressSteps} Progress steps instance
         */
        init: function(config) {
            return new ProgressSteps(config);
        }
    };
});

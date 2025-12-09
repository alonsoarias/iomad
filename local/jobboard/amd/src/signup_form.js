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
 * AMD module for signup form with dynamic department loading.
 *
 * @module     local_jobboard/signup_form
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'local_jobboard/department_loader'], function($, Str, DepartmentLoader) {
    'use strict';

    /**
     * Initialize the signup form handlers.
     *
     * @param {Object} options Configuration options.
     * @param {number} options.preselect Optional department ID to preselect.
     */
    var init = function(options) {
        options = options || {};

        // Initialize department loader with signup form context.
        DepartmentLoader.init({
            companySelector: '#id_companyid_signup, select[name="companyid"]',
            departmentSelector: '#id_departmentid_signup, select[name="departmentid"]',
            preselect: options.preselect
        });

        // Add password strength indicator.
        var passwordField = $('#id_password');
        if (passwordField.length) {
            var strengthIndicator = $('<div>', {
                'class': 'password-strength mt-1',
                'id': 'password-strength'
            });
            passwordField.after(strengthIndicator);

            passwordField.on('keyup', function() {
                var password = $(this).val();
                var strength = checkPasswordStrength(password);
                updateStrengthIndicator(strength);
            });
        }

        /**
         * Check password strength.
         *
         * @param {string} password The password to check.
         * @return {Object} Strength details.
         */
        var checkPasswordStrength = function(password) {
            var strength = {
                score: 0,
                level: 'weak'
            };

            if (!password) {
                return strength;
            }

            // Length check.
            if (password.length >= 8) {
                strength.score += 1;
            }
            if (password.length >= 12) {
                strength.score += 1;
            }

            // Character variety checks.
            if (/[a-z]/.test(password)) {
                strength.score += 1;
            }
            if (/[A-Z]/.test(password)) {
                strength.score += 1;
            }
            if (/[0-9]/.test(password)) {
                strength.score += 1;
            }
            if (/[^a-zA-Z0-9]/.test(password)) {
                strength.score += 1;
            }

            // Determine level.
            if (strength.score >= 5) {
                strength.level = 'strong';
            } else if (strength.score >= 3) {
                strength.level = 'medium';
            } else {
                strength.level = 'weak';
            }

            return strength;
        };

        /**
         * Update the strength indicator display.
         *
         * @param {Object} strength The strength object.
         */
        var updateStrengthIndicator = function(strength) {
            var indicator = $('#password-strength');
            var colors = {
                'weak': 'danger',
                'medium': 'warning',
                'strong': 'success'
            };

            Str.get_string('password_strength_' + strength.level, 'local_jobboard').done(function(str) {
                indicator.html(
                    '<small class="text-' + colors[strength.level] + '">' +
                    '<i class="fa fa-shield me-1"></i>' + str +
                    '</small>'
                );
            }).fail(function() {
                // Fallback if string not found.
                indicator.html(
                    '<small class="text-' + colors[strength.level] + '">' +
                    '<i class="fa fa-shield me-1"></i>' + strength.level +
                    '</small>'
                );
            });
        };

        // Email validation on blur.
        var emailField = $('#id_email');
        var email2Field = $('#id_email2');

        if (emailField.length && email2Field.length) {
            email2Field.on('blur', function() {
                var email1 = emailField.val();
                var email2 = $(this).val();

                if (email1 && email2 && email1 !== email2) {
                    $(this).addClass('is-invalid');
                    Str.get_string('emailnotmatch', 'local_jobboard').done(function(str) {
                        if (!$(this).siblings('.invalid-feedback').length) {
                            $(this).after('<div class="invalid-feedback">' + str + '</div>');
                        }
                    }.bind(this));
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').remove();
                }
            });
        }
    };

    return {
        init: init
    };
});

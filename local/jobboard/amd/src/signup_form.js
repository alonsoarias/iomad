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
define(['jquery', 'core/ajax', 'core/notification', 'core/str'], function($, Ajax, Notification, Str) {

    /**
     * Initialize the signup form handlers.
     */
    var init = function() {
        var companySelect = $('#id_companyid_signup');
        var departmentSelect = $('#id_departmentid_signup');

        // Exit if elements don't exist (not an IOMAD installation).
        if (!companySelect.length || !departmentSelect.length) {
            return;
        }

        /**
         * Load departments for the selected company.
         *
         * @param {number} companyId The selected company ID.
         */
        var loadDepartments = function(companyId) {
            // Clear current options.
            departmentSelect.empty();

            if (!companyId || companyId === '0') {
                // No company selected, add placeholder.
                Str.get_string('selectdepartment', 'local_jobboard').done(function(str) {
                    departmentSelect.append($('<option>', {
                        value: 0,
                        text: str
                    }));
                });
                return;
            }

            // Show loading state.
            departmentSelect.prop('disabled', true);
            Str.get_string('loading', 'local_jobboard').done(function(str) {
                departmentSelect.append($('<option>', {
                    value: 0,
                    text: str + '...'
                }));
            });

            // Fetch departments via AJAX.
            Ajax.call([{
                methodname: 'local_jobboard_get_departments',
                args: {
                    companyid: parseInt(companyId, 10)
                }
            }])[0].done(function(departments) {
                departmentSelect.empty();
                departmentSelect.prop('disabled', false);

                // Add placeholder option.
                Str.get_string('selectdepartment', 'local_jobboard').done(function(str) {
                    departmentSelect.append($('<option>', {
                        value: 0,
                        text: str
                    }));

                    // Add department options.
                    if (departments && departments.length > 0) {
                        $.each(departments, function(index, dept) {
                            departmentSelect.append($('<option>', {
                                value: dept.id,
                                text: dept.name
                            }));
                        });
                    }
                });

            }).fail(function(ex) {
                departmentSelect.empty();
                departmentSelect.prop('disabled', false);
                Str.get_string('selectdepartment', 'local_jobboard').done(function(str) {
                    departmentSelect.append($('<option>', {
                        value: 0,
                        text: str
                    }));
                });
                Notification.exception(ex);
            });
        };

        // Handle company change.
        companySelect.on('change', function() {
            var companyId = $(this).val();
            loadDepartments(companyId);
        });

        // Load departments if company is pre-selected.
        var initialCompany = companySelect.val();
        if (initialCompany && initialCompany !== '0') {
            loadDepartments(initialCompany);
        }

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

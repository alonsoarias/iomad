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
 * Form validation and UX enhancement module.
 *
 * Provides real-time validation, character counters, password strength,
 * file upload previews, and other form UX enhancements.
 *
 * @module     local_jobboard/form_validation
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/str', 'core/notification'], function(Str, Notification) {
    'use strict';

    /**
     * Module state and configuration.
     * @type {Object}
     */
    var state = {
        initialized: false,
        forms: new WeakMap(),
        config: {
            validateOnBlur: true,
            validateOnInput: false,
            showSuccessState: true,
            debounceDelay: 300,
            passwordMinLength: 8,
            charCounterWarningThreshold: 0.8,
            charCounterDangerThreshold: 0.95
        }
    };

    /**
     * Debounce utility function.
     *
     * @param {Function} func Function to debounce.
     * @param {number} wait Debounce delay in ms.
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
     * Initialize character counter for an input.
     *
     * @param {HTMLInputElement|HTMLTextAreaElement} input The input element.
     */
    var initCharCounter = function(input) {
        var maxLength = parseInt(input.getAttribute('maxlength'), 10);
        if (!maxLength) {
            return;
        }

        var counter = document.querySelector('.jb-char-counter[data-for="' + input.id + '"]');
        if (!counter) {
            return;
        }

        var currentSpan = counter.querySelector('.jb-char-current');
        if (!currentSpan) {
            return;
        }

        var updateCounter = function() {
            var length = input.value.length;
            var ratio = length / maxLength;

            currentSpan.textContent = length;

            // Update counter state based on thresholds.
            counter.classList.remove('jb-char-warning', 'jb-char-danger');

            if (ratio >= state.config.charCounterDangerThreshold) {
                counter.classList.add('jb-char-danger');
            } else if (ratio >= state.config.charCounterWarningThreshold) {
                counter.classList.add('jb-char-warning');
            }
        };

        // Initial update.
        updateCounter();

        // Update on input.
        input.addEventListener('input', updateCounter);
    };

    /**
     * Calculate password strength.
     *
     * @param {string} password The password to evaluate.
     * @return {Object} Strength result with level and score.
     */
    var calculatePasswordStrength = function(password) {
        var score = 0;
        var checks = {
            length: password.length >= state.config.passwordMinLength,
            lowercase: /[a-z]/.test(password),
            uppercase: /[A-Z]/.test(password),
            numbers: /\d/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };

        // Calculate score.
        if (checks.length) {
            score++;
        }
        if (checks.lowercase && checks.uppercase) {
            score++;
        }
        if (checks.numbers) {
            score++;
        }
        if (checks.special) {
            score++;
        }

        // Determine strength level.
        var level;
        if (score <= 1) {
            level = 'weak';
        } else if (score === 2) {
            level = 'fair';
        } else if (score === 3) {
            level = 'good';
        } else {
            level = 'strong';
        }

        return {
            level: level,
            score: score,
            checks: checks
        };
    };

    /**
     * Initialize password strength indicator.
     *
     * @param {HTMLInputElement} input The password input.
     */
    var initPasswordStrength = function(input) {
        // Find or create strength indicator.
        var container = input.closest('.mb-3') || input.parentElement;
        var strengthEl = container.querySelector('.jb-password-strength');

        if (!strengthEl) {
            // Create strength indicator.
            strengthEl = document.createElement('div');
            strengthEl.className = 'jb-password-strength';
            strengthEl.innerHTML =
                '<div class="jb-password-strength-bar"></div>' +
                '<div class="jb-password-strength-bar"></div>' +
                '<div class="jb-password-strength-bar"></div>' +
                '<div class="jb-password-strength-bar"></div>';
            input.parentElement.appendChild(strengthEl);

            // Create text label.
            var textEl = document.createElement('div');
            textEl.className = 'jb-password-strength-text text-muted';
            input.parentElement.appendChild(textEl);
        }

        var textEl = container.querySelector('.jb-password-strength-text');

        var updateStrength = function() {
            var result = calculatePasswordStrength(input.value);
            strengthEl.setAttribute('data-strength', result.level);

            if (textEl) {
                // Get localized strength text.
                Str.get_string('password_strength_' + result.level, 'local_jobboard')
                    .then(function(text) {
                        textEl.textContent = text;
                    })
                    .catch(function() {
                        // Fallback to English.
                        var texts = {
                            weak: 'Weak password',
                            fair: 'Fair password',
                            good: 'Good password',
                            strong: 'Strong password'
                        };
                        textEl.textContent = texts[result.level] || '';
                    });
            }
        };

        input.addEventListener('input', debounce(updateStrength, 150));
    };

    /**
     * Validate a single input field.
     *
     * @param {HTMLInputElement} input The input to validate.
     * @return {Object} Validation result with isValid and message.
     */
    var validateInput = function(input) {
        var result = {
            isValid: true,
            message: ''
        };

        // Skip disabled or readonly fields.
        if (input.disabled || input.readOnly) {
            return result;
        }

        // Check native validity.
        if (!input.checkValidity()) {
            result.isValid = false;
            result.message = input.validationMessage;
            return result;
        }

        // Custom validations based on data attributes.
        var customPattern = input.dataset.jbPattern;
        if (customPattern && input.value) {
            var regex = new RegExp(customPattern);
            if (!regex.test(input.value)) {
                result.isValid = false;
                result.message = input.dataset.jbPatternMessage || 'Invalid format';
            }
        }

        // Email validation enhancement.
        if (input.type === 'email' && input.value) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                result.isValid = false;
                result.message = 'Please enter a valid email address';
            }
        }

        // Min/max length for password.
        if (input.type === 'password' && input.value) {
            var minLength = parseInt(input.minLength || input.dataset.minLength, 10);
            if (minLength && input.value.length < minLength) {
                result.isValid = false;
                result.message = 'Password must be at least ' + minLength + ' characters';
            }
        }

        return result;
    };

    /**
     * Update visual state of a form group.
     *
     * @param {HTMLElement} input The input element.
     * @param {Object} validationResult The validation result.
     */
    var updateFieldState = function(input, validationResult) {
        var formGroup = input.closest('.mb-3') || input.closest('.form-floating') || input.parentElement;
        if (!formGroup) {
            return;
        }

        // Remove existing states.
        formGroup.classList.remove('jb-has-error', 'jb-has-success');
        input.classList.remove('is-invalid', 'is-valid');

        // Remove existing feedback.
        var existingFeedback = formGroup.querySelector('.jb-form-text-error:not([id])');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        if (!validationResult.isValid) {
            // Add error state.
            formGroup.classList.add('jb-has-error');
            input.classList.add('is-invalid');

            // Add error message if not already present.
            var errorId = input.id + '-error';
            var existingError = document.getElementById(errorId);
            if (!existingError && validationResult.message) {
                var errorEl = document.createElement('div');
                errorEl.className = 'jb-form-text jb-form-text-error invalid-feedback d-block';
                errorEl.setAttribute('role', 'alert');
                errorEl.innerHTML = '<i class="fa fa-circle-exclamation me-1" aria-hidden="true"></i>' +
                    validationResult.message;
                input.parentElement.appendChild(errorEl);
            }

            // Announce to screen readers.
            input.setAttribute('aria-invalid', 'true');
        } else if (state.config.showSuccessState && input.value) {
            // Add success state.
            formGroup.classList.add('jb-has-success');
            input.classList.add('is-valid');
            input.removeAttribute('aria-invalid');
        } else {
            input.removeAttribute('aria-invalid');
        }
    };

    /**
     * Initialize file input with drag-and-drop and preview.
     *
     * @param {HTMLInputElement} input The file input.
     */
    var initFileInput = function(input) {
        var container = input.closest('.mb-3') || input.parentElement;
        var dropZone = container.querySelector('.jb-file-input-label');

        if (!dropZone) {
            return;
        }

        // Drag and drop handlers.
        var handleDragOver = function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('jb-drag-over');
        };

        var handleDragLeave = function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('jb-drag-over');
        };

        var handleDrop = function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('jb-drag-over');

            var files = e.dataTransfer.files;
            if (files.length) {
                input.files = files;
                updateFileList(input, container);
            }
        };

        dropZone.addEventListener('dragover', handleDragOver);
        dropZone.addEventListener('dragleave', handleDragLeave);
        dropZone.addEventListener('drop', handleDrop);

        // File change handler.
        input.addEventListener('change', function() {
            updateFileList(input, container);
        });
    };

    /**
     * Update file list display.
     *
     * @param {HTMLInputElement} input The file input.
     * @param {HTMLElement} container The container element.
     */
    var updateFileList = function(input, container) {
        // Remove existing file list.
        var existingList = container.querySelector('.jb-file-list');
        if (existingList) {
            existingList.remove();
        }

        if (!input.files || !input.files.length) {
            return;
        }

        // Create file list.
        var list = document.createElement('ul');
        list.className = 'jb-file-list';

        Array.from(input.files).forEach(function(file, index) {
            var item = document.createElement('li');
            item.className = 'jb-file-item';

            var icon = getFileIcon(file.type);
            var size = formatFileSize(file.size);

            item.innerHTML =
                '<i class="fa fa-' + icon + ' jb-file-item-icon" aria-hidden="true"></i>' +
                '<div class="jb-file-item-info">' +
                '  <div class="jb-file-item-name">' + escapeHtml(file.name) + '</div>' +
                '  <div class="jb-file-item-size">' + size + '</div>' +
                '</div>' +
                '<button type="button" class="jb-file-item-remove btn btn-sm" data-index="' + index + '" ' +
                '        aria-label="Remove file">' +
                '  <i class="fa fa-times" aria-hidden="true"></i>' +
                '</button>';

            list.appendChild(item);
        });

        container.appendChild(list);

        // Handle remove clicks.
        list.querySelectorAll('.jb-file-item-remove').forEach(function(btn) {
            btn.addEventListener('click', function() {
                // Note: Cannot directly remove files from FileList, user must re-select.
                input.value = '';
                updateFileList(input, container);
            });
        });
    };

    /**
     * Get appropriate icon for file type.
     *
     * @param {string} mimeType The file MIME type.
     * @return {string} FontAwesome icon name.
     */
    var getFileIcon = function(mimeType) {
        if (mimeType.startsWith('image/')) {
            return 'image';
        }
        if (mimeType.startsWith('video/')) {
            return 'video';
        }
        if (mimeType.startsWith('audio/')) {
            return 'music';
        }
        if (mimeType === 'application/pdf') {
            return 'file-pdf';
        }
        if (mimeType.includes('word') || mimeType.includes('document')) {
            return 'file-word';
        }
        if (mimeType.includes('sheet') || mimeType.includes('excel')) {
            return 'file-excel';
        }
        if (mimeType.includes('zip') || mimeType.includes('compressed')) {
            return 'file-archive';
        }
        return 'file';
    };

    /**
     * Format file size in human-readable format.
     *
     * @param {number} bytes File size in bytes.
     * @return {string} Formatted size string.
     */
    var formatFileSize = function(bytes) {
        if (bytes === 0) {
            return '0 Bytes';
        }
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    /**
     * Escape HTML entities.
     *
     * @param {string} text Text to escape.
     * @return {string} Escaped text.
     */
    var escapeHtml = function(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };

    /**
     * Initialize search input with clear button.
     *
     * @param {HTMLInputElement} input The search input.
     */
    var initSearchInput = function(input) {
        var container = input.closest('.jb-search-input');
        if (!container) {
            return;
        }

        var clearBtn = container.querySelector('.jb-search-input-clear');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                input.value = '';
                input.focus();
                input.dispatchEvent(new Event('input', {bubbles: true}));
            });
        }
    };

    /**
     * Initialize toggle/switch input.
     *
     * @param {HTMLInputElement} input The toggle input.
     */
    var initToggle = function(input) {
        if (!input.classList.contains('jb-toggle-input')) {
            return;
        }

        // Add keyboard support.
        input.addEventListener('keydown', function(e) {
            if (e.key === ' ' || e.key === 'Enter') {
                e.preventDefault();
                input.checked = !input.checked;
                input.dispatchEvent(new Event('change', {bubbles: true}));
            }
        });
    };

    /**
     * Initialize number stepper controls.
     *
     * @param {HTMLElement} container The stepper container.
     */
    var initNumberStepper = function(container) {
        var input = container.querySelector('.jb-form-control[type="number"]');
        var decreaseBtn = container.querySelector('.jb-number-stepper-btn:first-child');
        var increaseBtn = container.querySelector('.jb-number-stepper-btn:last-child');

        if (!input || !decreaseBtn || !increaseBtn) {
            return;
        }

        var step = parseFloat(input.step) || 1;
        var min = parseFloat(input.min);
        var max = parseFloat(input.max);

        var updateValue = function(delta) {
            var currentValue = parseFloat(input.value) || 0;
            var newValue = currentValue + delta;

            if (!isNaN(min) && newValue < min) {
                newValue = min;
            }
            if (!isNaN(max) && newValue > max) {
                newValue = max;
            }

            input.value = newValue;
            input.dispatchEvent(new Event('change', {bubbles: true}));
        };

        decreaseBtn.addEventListener('click', function() {
            updateValue(-step);
        });

        increaseBtn.addEventListener('click', function() {
            updateValue(step);
        });
    };

    /**
     * Initialize a form with all UX enhancements.
     *
     * @param {HTMLFormElement} form The form element.
     * @param {Object} options Custom options.
     */
    var initForm = function(form, options) {
        if (state.forms.has(form)) {
            return;
        }

        options = Object.assign({}, state.config, options);
        state.forms.set(form, {options: options});

        // Initialize character counters.
        form.querySelectorAll('input[maxlength], textarea[maxlength]').forEach(function(input) {
            initCharCounter(input);
        });

        // Initialize password strength.
        form.querySelectorAll('input[type="password"][data-show-strength]').forEach(function(input) {
            initPasswordStrength(input);
        });

        // Initialize file inputs.
        form.querySelectorAll('input[type="file"]').forEach(function(input) {
            initFileInput(input);
        });

        // Initialize search inputs.
        form.querySelectorAll('.jb-search-input input').forEach(function(input) {
            initSearchInput(input);
        });

        // Initialize toggles.
        form.querySelectorAll('.jb-toggle-input').forEach(function(input) {
            initToggle(input);
        });

        // Initialize number steppers.
        form.querySelectorAll('.jb-number-stepper').forEach(function(container) {
            initNumberStepper(container);
        });

        // Set up validation handlers.
        var validateHandler = function(input) {
            var result = validateInput(input);
            updateFieldState(input, result);
        };

        var debouncedValidate = debounce(validateHandler, options.debounceDelay);

        form.querySelectorAll('input, select, textarea').forEach(function(input) {
            if (options.validateOnBlur) {
                input.addEventListener('blur', function() {
                    validateHandler(input);
                });
            }

            if (options.validateOnInput) {
                input.addEventListener('input', function() {
                    debouncedValidate(input);
                });
            }
        });

        // Form submit validation.
        form.addEventListener('submit', function(e) {
            var isValid = true;

            form.querySelectorAll('input, select, textarea').forEach(function(input) {
                var result = validateInput(input);
                updateFieldState(input, result);

                if (!result.isValid) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();

                // Focus first invalid field.
                var firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();

                    // Scroll into view.
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }

                // Notify user.
                Str.get_string('form_has_errors', 'local_jobboard')
                    .then(function(message) {
                        Notification.addNotification({
                            message: message,
                            type: 'error'
                        });
                    })
                    .catch(function() {
                        Notification.addNotification({
                            message: 'Please correct the errors in the form.',
                            type: 'error'
                        });
                    });
            }
        });
    };

    /**
     * Auto-initialize all forms with jb-form class.
     */
    var autoInit = function() {
        document.querySelectorAll('form.jb-form, form[data-jb-validate]').forEach(function(form) {
            var options = {};

            // Parse options from data attributes.
            if (form.dataset.jbValidateOnInput === 'true') {
                options.validateOnInput = true;
            }
            if (form.dataset.jbValidateOnBlur === 'false') {
                options.validateOnBlur = false;
            }
            if (form.dataset.jbShowSuccess === 'false') {
                options.showSuccessState = false;
            }

            initForm(form, options);
        });

        // Also initialize standalone components not in forms.
        document.querySelectorAll('.jb-char-counter[data-for]').forEach(function(counter) {
            var inputId = counter.dataset.for;
            var input = document.getElementById(inputId);
            if (input) {
                initCharCounter(input);
            }
        });
    };

    /**
     * Initialize the module.
     *
     * @param {Object} config Configuration options.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        // Merge configuration.
        if (config) {
            Object.assign(state.config, config);
        }

        // Auto-initialize when DOM is ready.
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', autoInit);
        } else {
            autoInit();
        }

        // Handle dynamically added forms.
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        if (node.matches && (node.matches('form.jb-form') || node.matches('form[data-jb-validate]'))) {
                            initForm(node);
                        }
                        node.querySelectorAll && node.querySelectorAll('form.jb-form, form[data-jb-validate]')
                            .forEach(function(form) {
                                initForm(form);
                            });
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        state.initialized = true;
    };

    return {
        init: init,
        initForm: initForm,
        validateInput: validateInput,
        calculatePasswordStrength: calculatePasswordStrength
    };
});

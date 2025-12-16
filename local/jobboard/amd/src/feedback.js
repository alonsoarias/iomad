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
 * Feedback and micro-interactions module.
 *
 * Provides toast notifications, tooltips, confirmation dialogs,
 * and other feedback mechanisms for enhanced UX.
 *
 * @module     local_jobboard/feedback
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/str', 'core/templates'], function(Str, Templates) {
    'use strict';

    /**
     * Toast container element.
     * @type {HTMLElement|null}
     */
    var toastContainer = null;

    /**
     * Active toasts.
     * @type {Map}
     */
    var activeToasts = new Map();

    /**
     * Default configuration.
     * @type {Object}
     */
    var config = {
        toastDuration: 5000,
        toastPosition: 'top-right',
        tooltipDelay: 200,
        tooltipTheme: 'dark'
    };

    /**
     * Generate unique ID.
     *
     * @return {string} Unique ID.
     */
    var generateId = function() {
        return 'jb-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    };

    /**
     * Get or create toast container.
     *
     * @param {string} position Position of container.
     * @return {HTMLElement} Toast container element.
     */
    var getToastContainer = function(position) {
        var containerId = 'jb-toast-container-' + position;
        var container = document.getElementById(containerId);

        if (!container) {
            container = document.createElement('div');
            container.id = containerId;
            container.className = 'jb-toast-container jb-toast-' + position;
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('aria-atomic', 'true');
            document.body.appendChild(container);
        }

        return container;
    };

    /**
     * Show a toast notification.
     *
     * @param {Object} options Toast options.
     * @param {string} options.type Type: success, error, warning, info.
     * @param {string} options.message Toast message.
     * @param {string} [options.title] Optional title.
     * @param {string} [options.icon] Custom icon.
     * @param {number} [options.duration] Duration in ms (0 = no auto-dismiss).
     * @param {string} [options.position] Position.
     * @param {Object} [options.action] Action button {label, url, callback}.
     * @param {boolean} [options.progress] Show progress bar.
     * @return {string} Toast ID.
     */
    var toast = function(options) {
        var id = generateId();
        var type = options.type || 'info';
        var duration = typeof options.duration === 'number' ? options.duration : config.toastDuration;
        var position = options.position || config.toastPosition;

        // Get icon based on type if not specified.
        var icon = options.icon;
        if (!icon) {
            var iconMap = {
                success: 'circle-check',
                error: 'circle-xmark',
                warning: 'triangle-exclamation',
                info: 'circle-info'
            };
            icon = iconMap[type] || 'circle-info';
        }

        // Create toast element.
        var toastEl = document.createElement('div');
        toastEl.id = id;
        toastEl.className = 'jb-toast jb-toast-' + type;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
        toastEl.setAttribute('aria-atomic', 'true');

        // Build toast HTML.
        var html = '<div class="jb-toast-icon">' +
            '<i class="fa fa-' + icon + '" aria-hidden="true"></i>' +
            '</div>' +
            '<div class="jb-toast-content">';

        if (options.title) {
            html += '<div class="jb-toast-title">' + escapeHtml(options.title) + '</div>';
        }

        html += '<div class="jb-toast-message">' + escapeHtml(options.message) + '</div>';

        if (options.action) {
            html += '<div class="jb-toast-action">' +
                '<button type="button" class="jb-toast-action-link" data-action="toast-action">' +
                escapeHtml(options.action.label) +
                '</button>' +
                '</div>';
        }

        html += '</div>' +
            '<button type="button" class="jb-toast-close" data-dismiss="toast" aria-label="Close">' +
            '<i class="fa fa-xmark" aria-hidden="true"></i>' +
            '</button>';

        if (options.progress && duration > 0) {
            html += '<div class="jb-toast-progress">' +
                '<div class="jb-toast-progress-bar" style="animation-duration: ' + duration + 'ms;"></div>' +
                '</div>';
        }

        toastEl.innerHTML = html;

        // Add to container.
        var container = getToastContainer(position);
        container.appendChild(toastEl);

        // Store toast data.
        var toastData = {
            element: toastEl,
            timeout: null,
            options: options
        };
        activeToasts.set(id, toastData);

        // Trigger animation.
        requestAnimationFrame(function() {
            toastEl.classList.add('jb-toast-visible');
        });

        // Set up auto-dismiss.
        if (duration > 0) {
            toastData.timeout = setTimeout(function() {
                dismissToast(id);
            }, duration);
        }

        // Set up close button.
        toastEl.querySelector('[data-dismiss="toast"]').addEventListener('click', function() {
            dismissToast(id);
        });

        // Set up action button.
        var actionBtn = toastEl.querySelector('[data-action="toast-action"]');
        if (actionBtn && options.action) {
            actionBtn.addEventListener('click', function() {
                if (options.action.callback) {
                    options.action.callback();
                }
                if (options.action.url) {
                    window.location.href = options.action.url;
                }
                dismissToast(id);
            });
        }

        return id;
    };

    /**
     * Dismiss a toast.
     *
     * @param {string} id Toast ID.
     */
    var dismissToast = function(id) {
        var toastData = activeToasts.get(id);
        if (!toastData) {
            return;
        }

        // Clear timeout.
        if (toastData.timeout) {
            clearTimeout(toastData.timeout);
        }

        // Animate out.
        toastData.element.classList.remove('jb-toast-visible');
        toastData.element.classList.add('jb-toast-exiting');

        // Remove after animation.
        setTimeout(function() {
            if (toastData.element.parentNode) {
                toastData.element.parentNode.removeChild(toastData.element);
            }
            activeToasts.delete(id);
        }, 300);
    };

    /**
     * Show success toast.
     *
     * @param {string} message Message.
     * @param {Object} [options] Additional options.
     * @return {string} Toast ID.
     */
    var success = function(message, options) {
        return toast(Object.assign({type: 'success', message: message}, options || {}));
    };

    /**
     * Show error toast.
     *
     * @param {string} message Message.
     * @param {Object} [options] Additional options.
     * @return {string} Toast ID.
     */
    var error = function(message, options) {
        return toast(Object.assign({type: 'error', message: message, duration: 8000}, options || {}));
    };

    /**
     * Show warning toast.
     *
     * @param {string} message Message.
     * @param {Object} [options] Additional options.
     * @return {string} Toast ID.
     */
    var warning = function(message, options) {
        return toast(Object.assign({type: 'warning', message: message}, options || {}));
    };

    /**
     * Show info toast.
     *
     * @param {string} message Message.
     * @param {Object} [options] Additional options.
     * @return {string} Toast ID.
     */
    var info = function(message, options) {
        return toast(Object.assign({type: 'info', message: message}, options || {}));
    };

    /**
     * Show confirmation dialog.
     *
     * @param {Object} options Dialog options.
     * @param {string} options.title Dialog title.
     * @param {string} options.message Dialog message.
     * @param {string} [options.type] Type: danger, warning, info.
     * @param {string} [options.confirmText] Confirm button text.
     * @param {string} [options.cancelText] Cancel button text.
     * @param {string} [options.requireInput] Text user must type to confirm.
     * @param {Function} [options.onConfirm] Confirm callback.
     * @param {Function} [options.onCancel] Cancel callback.
     * @return {Promise} Resolves with true (confirm) or false (cancel).
     */
    var confirm = function(options) {
        return new Promise(function(resolve) {
            var id = generateId();
            var type = options.type || 'warning';

            // Get icon based on type.
            var iconMap = {
                danger: 'trash',
                warning: 'triangle-exclamation',
                info: 'circle-info'
            };
            var icon = options.icon || iconMap[type] || 'triangle-exclamation';

            // Create modal backdrop.
            var backdrop = document.createElement('div');
            backdrop.className = 'jb-confirm-backdrop';
            backdrop.id = id + '-backdrop';

            // Create modal.
            var modal = document.createElement('div');
            modal.className = 'jb-confirm-modal';
            modal.id = id;
            modal.setAttribute('role', 'alertdialog');
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('aria-labelledby', id + '-title');
            modal.setAttribute('aria-describedby', id + '-message');

            var buttonColor = type === 'danger' ? 'danger' : (type === 'warning' ? 'warning' : 'primary');

            var html = '<div class="jb-confirm-content">' +
                '<div class="jb-confirm-icon-circle jb-confirm-icon-' + type + '">' +
                '<i class="fa fa-' + icon + '" aria-hidden="true"></i>' +
                '</div>' +
                '<h5 class="jb-confirm-title" id="' + id + '-title">' + escapeHtml(options.title) + '</h5>' +
                '<p class="jb-confirm-message" id="' + id + '-message">' + escapeHtml(options.message) + '</p>';

            if (options.requireInput) {
                html += '<div class="jb-confirm-input-wrapper">' +
                    '<label class="small text-muted mb-2">Type <strong>' + escapeHtml(options.requireInput) +
                    '</strong> to confirm</label>' +
                    '<input type="text" class="form-control jb-form-control text-center jb-confirm-input" ' +
                    'data-confirm-value="' + escapeHtml(options.requireInput) + '" autocomplete="off">' +
                    '</div>';
            }

            html += '<div class="jb-confirm-actions">' +
                '<button type="button" class="btn jb-btn-ghost jb-confirm-cancel">' +
                (options.cancelText || 'Cancel') +
                '</button>' +
                '<button type="button" class="btn jb-btn-' + buttonColor + ' jb-confirm-btn"' +
                (options.requireInput ? ' disabled' : '') + '>' +
                (options.confirmText || 'Confirm') +
                '</button>' +
                '</div>' +
                '</div>';

            modal.innerHTML = html;

            // Add to DOM.
            document.body.appendChild(backdrop);
            document.body.appendChild(modal);

            // Focus trap.
            var focusableEls = modal.querySelectorAll('button, input');
            var firstFocusable = focusableEls[0];
            var lastFocusable = focusableEls[focusableEls.length - 1];

            // Animate in.
            requestAnimationFrame(function() {
                backdrop.classList.add('jb-confirm-backdrop-visible');
                modal.classList.add('jb-confirm-modal-visible');
                if (options.requireInput) {
                    modal.querySelector('.jb-confirm-input').focus();
                } else {
                    modal.querySelector('.jb-confirm-cancel').focus();
                }
            });

            // Close function.
            var close = function(result) {
                backdrop.classList.remove('jb-confirm-backdrop-visible');
                modal.classList.remove('jb-confirm-modal-visible');

                setTimeout(function() {
                    backdrop.remove();
                    modal.remove();
                }, 200);

                if (result && options.onConfirm) {
                    options.onConfirm();
                } else if (!result && options.onCancel) {
                    options.onCancel();
                }

                resolve(result);
            };

            // Event handlers.
            modal.querySelector('.jb-confirm-cancel').addEventListener('click', function() {
                close(false);
            });

            modal.querySelector('.jb-confirm-btn').addEventListener('click', function() {
                if (!this.disabled) {
                    close(true);
                }
            });

            backdrop.addEventListener('click', function() {
                close(false);
            });

            // Handle require input.
            if (options.requireInput) {
                var input = modal.querySelector('.jb-confirm-input');
                var confirmBtn = modal.querySelector('.jb-confirm-btn');

                input.addEventListener('input', function() {
                    confirmBtn.disabled = input.value !== options.requireInput;
                });
            }

            // Keyboard handling.
            modal.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    close(false);
                }
                if (e.key === 'Tab') {
                    if (e.shiftKey && document.activeElement === firstFocusable) {
                        e.preventDefault();
                        lastFocusable.focus();
                    } else if (!e.shiftKey && document.activeElement === lastFocusable) {
                        e.preventDefault();
                        firstFocusable.focus();
                    }
                }
            });
        });
    };

    /**
     * Initialize tooltips.
     */
    var initTooltips = function() {
        document.querySelectorAll('[data-jb-tooltip]').forEach(function(el) {
            if (el.dataset.jbTooltipInitialized) {
                return;
            }

            var content = el.dataset.jbTooltip;
            var position = el.dataset.jbTooltipPosition || 'top';
            var delay = parseInt(el.dataset.jbTooltipDelay, 10) || config.tooltipDelay;
            var theme = el.dataset.jbTooltipTheme || config.tooltipTheme;

            var tooltip = null;
            var showTimeout = null;

            var show = function() {
                clearTimeout(showTimeout);
                showTimeout = setTimeout(function() {
                    tooltip = document.createElement('div');
                    tooltip.className = 'jb-tooltip jb-tooltip-' + position + ' jb-tooltip-' + theme;
                    tooltip.innerHTML = content;
                    tooltip.setAttribute('role', 'tooltip');
                    document.body.appendChild(tooltip);

                    // Position tooltip.
                    var rect = el.getBoundingClientRect();
                    var tooltipRect = tooltip.getBoundingClientRect();
                    var scrollY = window.scrollY || window.pageYOffset;
                    var scrollX = window.scrollX || window.pageXOffset;

                    var top, left;

                    switch (position) {
                        case 'top':
                            top = rect.top + scrollY - tooltipRect.height - 8;
                            left = rect.left + scrollX + (rect.width - tooltipRect.width) / 2;
                            break;
                        case 'bottom':
                            top = rect.bottom + scrollY + 8;
                            left = rect.left + scrollX + (rect.width - tooltipRect.width) / 2;
                            break;
                        case 'left':
                            top = rect.top + scrollY + (rect.height - tooltipRect.height) / 2;
                            left = rect.left + scrollX - tooltipRect.width - 8;
                            break;
                        case 'right':
                            top = rect.top + scrollY + (rect.height - tooltipRect.height) / 2;
                            left = rect.right + scrollX + 8;
                            break;
                    }

                    tooltip.style.top = top + 'px';
                    tooltip.style.left = left + 'px';

                    requestAnimationFrame(function() {
                        tooltip.classList.add('jb-tooltip-visible');
                    });
                }, delay);
            };

            var hide = function() {
                clearTimeout(showTimeout);
                if (tooltip) {
                    tooltip.classList.remove('jb-tooltip-visible');
                    setTimeout(function() {
                        if (tooltip && tooltip.parentNode) {
                            tooltip.parentNode.removeChild(tooltip);
                        }
                        tooltip = null;
                    }, 150);
                }
            };

            el.addEventListener('mouseenter', show);
            el.addEventListener('mouseleave', hide);
            el.addEventListener('focus', show);
            el.addEventListener('blur', hide);

            el.dataset.jbTooltipInitialized = 'true';
        });
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
     * Initialize the feedback module.
     *
     * @param {Object} [options] Configuration options.
     */
    var init = function(options) {
        if (options) {
            Object.assign(config, options);
        }

        // Initialize tooltips.
        initTooltips();

        // Re-initialize on DOM changes.
        var observer = new MutationObserver(function() {
            initTooltips();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        // Handle confirm dialog inputs.
        document.addEventListener('input', function(e) {
            if (e.target.matches('.jb-confirm-input[data-confirm-value]')) {
                var input = e.target;
                var confirmBtn = input.closest('.modal-body, .jb-confirm-content')
                    .querySelector('.jb-confirm-btn');
                if (confirmBtn) {
                    confirmBtn.disabled = input.value !== input.dataset.confirmValue;
                }
            }
        });
    };

    return {
        init: init,
        toast: toast,
        success: success,
        error: error,
        warning: warning,
        info: info,
        confirm: confirm,
        dismissToast: dismissToast,
        initTooltips: initTooltips
    };
});

/**
 * Common utilities module for local_jobboard.
 *
 * Shared utility functions used across the plugin.
 * NO Bootstrap dependencies - uses custom jb-* classes only.
 *
 * @module     local_jobboard/common
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/log', 'core/str'], function($, Log, Str) {
    'use strict';

    /**
     * CSS class names used by the plugin.
     * @type {Object}
     */
    var CSS = {
        // Layout
        row: 'jb-row',
        col: 'jb-col',
        container: 'jb-container',

        // Cards
        card: 'jb-card',
        cardBody: 'jb-card-body',
        cardHeader: 'jb-card-header',
        cardFooter: 'jb-card-footer',
        cardShadow: 'jb-card-shadow',

        // Buttons
        btn: 'jb-btn',
        btnPrimary: 'jb-btn-primary',
        btnSecondary: 'jb-btn-secondary',
        btnSuccess: 'jb-btn-success',
        btnDanger: 'jb-btn-danger',
        btnWarning: 'jb-btn-warning',
        btnInfo: 'jb-btn-info',
        btnSm: 'jb-btn-sm',
        btnLg: 'jb-btn-lg',
        btnBlock: 'jb-btn-block',

        // Alerts
        alert: 'jb-alert',
        alertPrimary: 'jb-alert-primary',
        alertSuccess: 'jb-alert-success',
        alertDanger: 'jb-alert-danger',
        alertWarning: 'jb-alert-warning',
        alertInfo: 'jb-alert-info',

        // Badges
        badge: 'jb-badge',
        badgePrimary: 'jb-badge-primary',
        badgeSuccess: 'jb-badge-success',
        badgeDanger: 'jb-badge-danger',
        badgeWarning: 'jb-badge-warning',
        badgeInfo: 'jb-badge-info',
        badgePill: 'jb-badge-pill',

        // Display
        dNone: 'jb-d-none',
        dBlock: 'jb-d-block',
        dFlex: 'jb-d-flex',
        dInline: 'jb-d-inline',
        dInlineBlock: 'jb-d-inline-block',

        // Text
        textMuted: 'jb-text-muted',
        textPrimary: 'jb-text-primary',
        textSuccess: 'jb-text-success',
        textDanger: 'jb-text-danger',
        textWarning: 'jb-text-warning',
        textCenter: 'jb-text-center',

        // Spacing
        mb0: 'jb-mb-0',
        mb1: 'jb-mb-1',
        mb2: 'jb-mb-2',
        mb3: 'jb-mb-3',
        mb4: 'jb-mb-4',
        mt3: 'jb-mt-3',
        mr1: 'jb-mr-1',
        mr2: 'jb-mr-2',
        ml1: 'jb-ml-1',
        p3: 'jb-p-3',

        // Loading
        loading: 'jb-loading',
        spinner: 'jb-spinner',

        // Visibility
        srOnly: 'jb-sr-only'
    };

    /**
     * Check if user prefers reduced motion.
     * @returns {boolean}
     */
    var prefersReducedMotion = function() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    };

    /**
     * Format a date for display.
     * @param {Date|number|string} date The date to format.
     * @param {string} format The format type ('short', 'long', 'relative').
     * @returns {string}
     */
    var formatDate = function(date, format) {
        var d = date instanceof Date ? date : new Date(date);

        if (isNaN(d.getTime())) {
            return '';
        }

        var options;
        switch (format) {
            case 'short':
                options = {day: '2-digit', month: '2-digit', year: 'numeric'};
                break;
            case 'long':
                options = {day: 'numeric', month: 'long', year: 'numeric'};
                break;
            case 'relative':
                return getRelativeTime(d);
            default:
                options = {day: '2-digit', month: '2-digit', year: 'numeric'};
        }

        return d.toLocaleDateString(M.cfg.language || 'en', options);
    };

    /**
     * Get relative time string (e.g., "2 days ago").
     * @param {Date} date The date.
     * @returns {string}
     */
    var getRelativeTime = function(date) {
        var now = new Date();
        var diff = now - date;
        var seconds = Math.floor(diff / 1000);
        var minutes = Math.floor(seconds / 60);
        var hours = Math.floor(minutes / 60);
        var days = Math.floor(hours / 24);

        if (days > 30) {
            return formatDate(date, 'short');
        } else if (days > 0) {
            return days + ' ' + (days === 1 ? 'day' : 'days') + ' ago';
        } else if (hours > 0) {
            return hours + ' ' + (hours === 1 ? 'hour' : 'hours') + ' ago';
        } else if (minutes > 0) {
            return minutes + ' ' + (minutes === 1 ? 'minute' : 'minutes') + ' ago';
        } else {
            return 'Just now';
        }
    };

    /**
     * Create an element with classes.
     * @param {string} tag The HTML tag.
     * @param {string|string[]} classes CSS classes to add.
     * @param {Object} attrs Additional attributes.
     * @returns {jQuery}
     */
    var createElement = function(tag, classes, attrs) {
        var $element = $('<' + tag + '>');

        if (classes) {
            if (Array.isArray(classes)) {
                $element.addClass(classes.join(' '));
            } else {
                $element.addClass(classes);
            }
        }

        if (attrs) {
            $element.attr(attrs);
        }

        return $element;
    };

    /**
     * Create a spinner element.
     * @param {string} size Size variant ('sm' or default).
     * @param {string} color Color variant.
     * @returns {jQuery}
     */
    var createSpinner = function(size, color) {
        var spinnerClass = CSS.spinner;
        if (size === 'sm') {
            spinnerClass += ' jb-spinner-sm';
        }
        if (color) {
            spinnerClass += ' jb-spinner-border-' + color;
        }

        return createElement('div', spinnerClass, {role: 'status'})
            .append(createElement('span', CSS.srOnly).text('Loading...'));
    };

    /**
     * Create an alert element.
     * @param {string} message The alert message.
     * @param {string} type Alert type (success, danger, warning, info).
     * @param {boolean} dismissible Whether the alert can be dismissed.
     * @returns {jQuery}
     */
    var createAlert = function(message, type, dismissible) {
        var alertClass = CSS.alert + ' jb-alert-' + (type || 'info');
        var $alert = createElement('div', alertClass, {role: 'alert'});

        $alert.html(message);

        if (dismissible) {
            var $closeBtn = createElement('button', 'jb-btn-close', {
                type: 'button',
                'aria-label': 'Close'
            });
            $closeBtn.on('click', function() {
                $alert.remove();
            });
            $alert.append($closeBtn);
        }

        return $alert;
    };

    /**
     * Create a badge element.
     * @param {string} text Badge text.
     * @param {string} color Color variant.
     * @param {boolean} pill Whether to use pill style.
     * @returns {jQuery}
     */
    var createBadge = function(text, color, pill) {
        var badgeClass = CSS.badge + ' jb-badge-' + (color || 'secondary');
        if (pill) {
            badgeClass += ' ' + CSS.badgePill;
        }
        return createElement('span', badgeClass).text(text);
    };

    /**
     * Debounce a function.
     * @param {Function} func The function to debounce.
     * @param {number} wait Wait time in milliseconds.
     * @returns {Function}
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
     * Throttle a function.
     * @param {Function} func The function to throttle.
     * @param {number} limit Limit in milliseconds.
     * @returns {Function}
     */
    var throttle = function(func, limit) {
        var inThrottle;
        return function() {
            var context = this;
            var args = arguments;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(function() {
                    inThrottle = false;
                }, limit);
            }
        };
    };

    /**
     * Escape HTML special characters.
     * @param {string} str The string to escape.
     * @returns {string}
     */
    var escapeHtml = function(str) {
        if (!str) {
            return '';
        }
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

    /**
     * Get a translated string.
     * @param {string} key The string key.
     * @param {string} component The component (defaults to local_jobboard).
     * @param {*} param Optional parameter.
     * @returns {Promise}
     */
    var getString = function(key, component, param) {
        component = component || 'local_jobboard';
        return Str.get_string(key, component, param);
    };

    /**
     * Get multiple translated strings.
     * @param {Array} keys Array of {key, component, param} objects.
     * @returns {Promise}
     */
    var getStrings = function(keys) {
        var requests = keys.map(function(item) {
            return {
                key: item.key,
                component: item.component || 'local_jobboard',
                param: item.param
            };
        });
        return Str.get_strings(requests);
    };

    /**
     * Make an AJAX request to the plugin.
     * @param {string} action The action name.
     * @param {Object} data Additional data.
     * @returns {Promise}
     */
    var ajax = function(action, data) {
        var requestData = $.extend({
            action: action,
            sesskey: M.cfg.sesskey
        }, data || {});

        return $.ajax({
            url: M.cfg.wwwroot + '/local/jobboard/ajax.php',
            method: 'POST',
            data: requestData,
            dataType: 'json'
        }).then(function(response) {
            if (response.error) {
                Log.error('[Jobboard] AJAX error:', response.error);
                return $.Deferred().reject(response.error).promise();
            }
            return response;
        }).catch(function(error) {
            Log.error('[Jobboard] AJAX failed:', error);
            throw error;
        });
    };

    return {
        CSS: CSS,
        prefersReducedMotion: prefersReducedMotion,
        formatDate: formatDate,
        createElement: createElement,
        createSpinner: createSpinner,
        createAlert: createAlert,
        createBadge: createBadge,
        debounce: debounce,
        throttle: throttle,
        escapeHtml: escapeHtml,
        getString: getString,
        getStrings: getStrings,
        ajax: ajax
    };
});

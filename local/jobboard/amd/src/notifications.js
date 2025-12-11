/**
 * Notifications module for local_jobboard.
 *
 * Handles toast notifications and alerts.
 * NO Bootstrap dependencies - uses custom jb-* classes only.
 *
 * @module     local_jobboard/notifications
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/log', 'local_jobboard/common'], function($, Log, Common) {
    'use strict';

    /**
     * Configuration.
     * @type {Object}
     */
    var config = {
        containerSelector: '.jb-notifications-container',
        defaultDuration: 5000,
        maxNotifications: 5
    };

    /**
     * Notification container element.
     * @type {jQuery}
     */
    var $container = null;

    /**
     * Get or create the notifications container.
     * @returns {jQuery}
     */
    var getContainer = function() {
        if ($container && $container.length) {
            return $container;
        }

        $container = $(config.containerSelector);

        if (!$container.length) {
            $container = $('<div>')
                .addClass('jb-notifications-container')
                .css({
                    position: 'fixed',
                    top: '20px',
                    right: '20px',
                    zIndex: 9999,
                    maxWidth: '400px',
                    width: '100%'
                })
                .appendTo('body');
        }

        return $container;
    };

    /**
     * Create a notification element.
     * @param {string} message The message.
     * @param {string} type Notification type (success, error, warning, info).
     * @param {Object} options Additional options.
     * @returns {jQuery}
     */
    var createNotification = function(message, type, options) {
        options = options || {};

        // Map type to alert class
        var alertType = type;
        if (type === 'error') {
            alertType = 'danger';
        }

        var $notification = $('<div>')
            .addClass('jb-notification jb-alert jb-alert-' + alertType)
            .css({
                marginBottom: '10px',
                boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
                opacity: 0,
                transform: 'translateX(100%)',
                transition: 'all 300ms ease-out'
            })
            .attr('role', 'alert')
            .attr('aria-live', 'polite');

        // Icon
        var icon = getIconForType(type);
        if (icon) {
            $notification.append(
                $('<i>').addClass('fa fa-' + icon + ' jb-mr-2').attr('aria-hidden', 'true')
            );
        }

        // Message
        $notification.append($('<span>').html(message));

        // Close button
        var $closeBtn = $('<button>')
            .addClass('jb-btn-close')
            .attr('type', 'button')
            .attr('aria-label', 'Close')
            .css({
                position: 'absolute',
                top: '8px',
                right: '8px'
            })
            .on('click', function() {
                dismissNotification($notification);
            });
        $notification.css('position', 'relative').css('paddingRight', '40px').append($closeBtn);

        return $notification;
    };

    /**
     * Get icon for notification type.
     * @param {string} type Notification type.
     * @returns {string}
     */
    var getIconForType = function(type) {
        var icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || icons.info;
    };

    /**
     * Show a notification.
     * @param {string} message The message.
     * @param {string} type Notification type (success, error, warning, info).
     * @param {Object} options Additional options (duration, etc.).
     */
    var show = function(message, type, options) {
        options = options || {};
        type = type || 'info';

        Log.debug('[Jobboard Notifications] Showing:', type, message);

        var $container = getContainer();
        var $notification = createNotification(message, type, options);

        // Limit number of notifications
        var $existing = $container.children('.jb-notification');
        if ($existing.length >= config.maxNotifications) {
            dismissNotification($existing.first());
        }

        $container.append($notification);

        // Trigger animation
        setTimeout(function() {
            $notification.css({
                opacity: 1,
                transform: 'translateX(0)'
            });
        }, 10);

        // Auto-dismiss
        var duration = options.duration !== undefined ? options.duration : config.defaultDuration;
        if (duration > 0) {
            setTimeout(function() {
                dismissNotification($notification);
            }, duration);
        }

        return $notification;
    };

    /**
     * Dismiss a notification.
     * @param {jQuery} $notification The notification element.
     */
    var dismissNotification = function($notification) {
        if (!$notification || !$notification.length) {
            return;
        }

        if (Common.prefersReducedMotion()) {
            $notification.remove();
            return;
        }

        $notification.css({
            opacity: 0,
            transform: 'translateX(100%)'
        });

        setTimeout(function() {
            $notification.remove();
        }, 300);
    };

    /**
     * Show success notification.
     * @param {string} message The message.
     * @param {Object} options Options.
     */
    var success = function(message, options) {
        return show(message, 'success', options);
    };

    /**
     * Show error notification.
     * @param {string} message The message.
     * @param {Object} options Options.
     */
    var error = function(message, options) {
        options = options || {};
        options.duration = options.duration || 8000; // Errors stay longer
        return show(message, 'error', options);
    };

    /**
     * Show warning notification.
     * @param {string} message The message.
     * @param {Object} options Options.
     */
    var warning = function(message, options) {
        return show(message, 'warning', options);
    };

    /**
     * Show info notification.
     * @param {string} message The message.
     * @param {Object} options Options.
     */
    var info = function(message, options) {
        return show(message, 'info', options);
    };

    /**
     * Clear all notifications.
     */
    var clearAll = function() {
        var $container = getContainer();
        $container.children('.jb-notification').each(function() {
            dismissNotification($(this));
        });
    };

    /**
     * Add inline alert to a container.
     * @param {jQuery|string} container Container element or selector.
     * @param {string} message The message.
     * @param {string} type Alert type.
     * @param {boolean} dismissible Whether alert can be dismissed.
     * @returns {jQuery}
     */
    var addInlineAlert = function(container, message, type, dismissible) {
        var $container = $(container);
        var $alert = Common.createAlert(message, type, dismissible);

        // Remove existing alerts of same type
        $container.find('.jb-alert-' + type).remove();

        $container.prepend($alert);

        if (!Common.prefersReducedMotion()) {
            $alert.css({opacity: 0}).animate({opacity: 1}, 300);
        }

        return $alert;
    };

    return {
        show: show,
        success: success,
        error: error,
        warning: warning,
        info: info,
        clearAll: clearAll,
        addInlineAlert: addInlineAlert
    };
});

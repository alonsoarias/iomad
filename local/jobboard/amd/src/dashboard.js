/**
 * Dashboard module for local_jobboard.
 *
 * Handles dashboard interactions and animations.
 * NO Bootstrap dependencies - uses custom jb-* classes only.
 *
 * @module     local_jobboard/dashboard
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/log'], function($, Log) {
    'use strict';

    /**
     * Module configuration.
     * @type {Object}
     */
    var config = {
        selectors: {
            dashboard: '.local-jobboard-dashboard',
            statCard: '.jb-stat-card',
            sectionCard: '.jb-section-card',
            actionCard: '.jb-action-card',
            alert: '.jb-alert',
            vacancyCard: '.jb-vacancy-card'
        },
        classes: {
            hover: 'jb-card-hover',
            shadow: 'jb-shadow',
            shadowLg: 'jb-shadow-lg',
            fadeIn: 'jb-fade-in',
            loading: 'jb-loading'
        },
        animations: {
            duration: 300,
            staggerDelay: 100
        }
    };

    /**
     * Check if user prefers reduced motion.
     * @returns {boolean}
     */
    var prefersReducedMotion = function() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    };

    /**
     * Initialize stat cards with staggered animation.
     */
    var initStatCards = function() {
        if (prefersReducedMotion()) {
            Log.debug('[Jobboard Dashboard] Reduced motion preference detected, skipping animations');
            return;
        }

        var $cards = $(config.selectors.statCard);
        $cards.each(function(index) {
            var $card = $(this);
            $card.css({
                opacity: 0,
                transform: 'translateY(20px)'
            });

            setTimeout(function() {
                $card.css({
                    opacity: 1,
                    transform: 'translateY(0)',
                    transition: 'all ' + config.animations.duration + 'ms ease-out'
                });
            }, index * config.animations.staggerDelay);
        });
    };

    /**
     * Initialize section card hover effects.
     */
    var initSectionCards = function() {
        var $cards = $(config.selectors.sectionCard);

        $cards.on('mouseenter', function() {
            $(this).addClass(config.classes.shadowLg);
        }).on('mouseleave', function() {
            $(this).removeClass(config.classes.shadowLg);
        });

        // Keyboard accessibility
        $cards.on('focus', 'a, button', function() {
            $(this).closest(config.selectors.sectionCard).addClass(config.classes.shadowLg);
        }).on('blur', 'a, button', function() {
            $(this).closest(config.selectors.sectionCard).removeClass(config.classes.shadowLg);
        });
    };

    /**
     * Initialize vacancy card interactions.
     */
    var initVacancyCards = function() {
        var $cards = $(config.selectors.vacancyCard);

        $cards.on('mouseenter', function() {
            if (!prefersReducedMotion()) {
                $(this).css('transform', 'translateY(-4px)');
            }
        }).on('mouseleave', function() {
            $(this).css('transform', 'translateY(0)');
        });
    };

    /**
     * Initialize auto-dismiss alerts.
     */
    var initAlerts = function() {
        $(config.selectors.alert).each(function() {
            var $alert = $(this);

            // Add close button if not present
            if (!$alert.find('.jb-btn-close').length) {
                var $closeBtn = $('<button>')
                    .addClass('jb-btn-close')
                    .attr('type', 'button')
                    .attr('aria-label', M.util.get_string('closealert', 'local_jobboard') || 'Close')
                    .on('click', function() {
                        dismissAlert($alert);
                    });
                $alert.css('position', 'relative').append($closeBtn);
            }
        });
    };

    /**
     * Dismiss an alert with animation.
     * @param {jQuery} $alert The alert element.
     */
    var dismissAlert = function($alert) {
        if (prefersReducedMotion()) {
            $alert.remove();
            return;
        }

        $alert.css({
            opacity: 0,
            transform: 'translateX(-20px)',
            transition: 'all ' + config.animations.duration + 'ms ease-out'
        });

        setTimeout(function() {
            $alert.remove();
        }, config.animations.duration);
    };

    /**
     * Show loading state on a container.
     * @param {jQuery|string} container The container element or selector.
     */
    var showLoading = function(container) {
        var $container = $(container);
        $container.addClass(config.classes.loading);

        var $spinner = $('<div>')
            .addClass('jb-spinner jb-spinner-border-primary')
            .attr('role', 'status')
            .append($('<span>').addClass('jb-sr-only').text('Loading...'));

        $container.append($spinner);
    };

    /**
     * Hide loading state.
     * @param {jQuery|string} container The container element or selector.
     */
    var hideLoading = function(container) {
        var $container = $(container);
        $container.removeClass(config.classes.loading);
        $container.find('.jb-spinner').remove();
    };

    /**
     * Refresh dashboard statistics via AJAX.
     * @returns {Promise}
     */
    var refreshStats = function() {
        Log.debug('[Jobboard Dashboard] Refreshing statistics');

        return $.ajax({
            url: M.cfg.wwwroot + '/local/jobboard/ajax.php',
            method: 'POST',
            data: {
                action: 'get_dashboard_stats',
                sesskey: M.cfg.sesskey
            },
            dataType: 'json'
        }).then(function(response) {
            if (response.success) {
                updateStatCards(response.data);
            }
            return response;
        }).catch(function(error) {
            Log.error('[Jobboard Dashboard] Failed to refresh stats:', error);
        });
    };

    /**
     * Update stat card values.
     * @param {Object} data Statistics data.
     */
    var updateStatCards = function(data) {
        if (!data) {
            return;
        }

        $(config.selectors.statCard).each(function() {
            var $card = $(this);
            var key = $card.data('stat-key');

            if (key && data[key] !== undefined) {
                var $value = $card.find('.jb-stat-card-value');
                var newValue = data[key];

                if (!prefersReducedMotion()) {
                    animateValue($value, parseInt($value.text()) || 0, newValue, 500);
                } else {
                    $value.text(newValue);
                }
            }
        });
    };

    /**
     * Animate a numeric value change.
     * @param {jQuery} $element The element to animate.
     * @param {number} start Starting value.
     * @param {number} end Ending value.
     * @param {number} duration Animation duration in ms.
     */
    var animateValue = function($element, start, end, duration) {
        var range = end - start;
        var startTime = null;

        var animate = function(timestamp) {
            if (!startTime) {
                startTime = timestamp;
            }

            var progress = Math.min((timestamp - startTime) / duration, 1);
            var current = Math.floor(start + (range * progress));
            $element.text(current);

            if (progress < 1) {
                window.requestAnimationFrame(animate);
            }
        };

        window.requestAnimationFrame(animate);
    };

    /**
     * Initialize dashboard module.
     */
    var init = function() {
        Log.debug('[Jobboard Dashboard] Initializing...');

        // Wait for DOM ready
        $(function() {
            var $dashboard = $(config.selectors.dashboard);

            if (!$dashboard.length) {
                Log.debug('[Jobboard Dashboard] Dashboard element not found');
                return;
            }

            // Initialize components
            initStatCards();
            initSectionCards();
            initVacancyCards();
            initAlerts();

            Log.debug('[Jobboard Dashboard] Initialization complete');
        });
    };

    return {
        init: init,
        showLoading: showLoading,
        hideLoading: hideLoading,
        refreshStats: refreshStats,
        dismissAlert: dismissAlert
    };
});

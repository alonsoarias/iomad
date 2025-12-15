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
 * Dashboard module for local_jobboard.
 *
 * Handles dashboard initialization and interactive elements.
 *
 * @module     local_jobboard/dashboard
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
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
        initialized: false,
        dashboardContainer: null
    };

    /**
     * Selectors used by the module.
     * @type {Object}
     */
    var SELECTORS = {
        dashboard: '[data-region="jobboard-dashboard"]',
        statCard: '.stat-card',
        sectionCard: '.card',
        alertsSection: '.jb-alerts-section',
        activitySection: '[aria-labelledby="activity-heading"]'
    };

    /**
     * Add hover effects to cards.
     */
    var initCardEffects = function() {
        var cards = document.querySelectorAll(SELECTORS.sectionCard);
        cards.forEach(function(card) {
            // Add subtle transition on hover (CSS handles this, but we can add focus support).
            card.addEventListener('focusin', function() {
                card.classList.add('shadow');
            });
            card.addEventListener('focusout', function() {
                card.classList.remove('shadow');
            });
        });
    };

    /**
     * Auto-dismiss alerts after a delay.
     */
    var initAlertDismissal = function() {
        var alertsSection = document.querySelector(SELECTORS.alertsSection);
        if (!alertsSection) {
            return;
        }

        var alerts = alertsSection.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert, index) {
            // Stagger dismissal for multiple alerts.
            setTimeout(function() {
                // Only auto-dismiss success alerts.
                if (alert.classList.contains('alert-success')) {
                    var closeBtn = alert.querySelector('[data-bs-dismiss="alert"]');
                    if (closeBtn) {
                        closeBtn.click();
                    }
                }
            }, 5000 + (index * 1000));
        });
    };

    /**
     * Initialize smooth scrolling for anchor links.
     */
    var initSmoothScroll = function() {
        var anchors = document.querySelectorAll('a[href^="#"]');
        anchors.forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                var targetId = anchor.getAttribute('href');
                if (targetId === '#' || targetId === '#main-content') {
                    return;
                }

                var target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Update focus for accessibility.
                    target.setAttribute('tabindex', '-1');
                    target.focus();
                }
            });
        });
    };

    /**
     * Initialize the dashboard module.
     */
    var init = function() {
        if (state.initialized) {
            return;
        }

        state.dashboardContainer = document.querySelector(SELECTORS.dashboard);
        if (!state.dashboardContainer) {
            return;
        }

        // Initialize features.
        initCardEffects();
        initAlertDismissal();
        initSmoothScroll();

        state.initialized = true;
    };

    return {
        init: init
    };
});

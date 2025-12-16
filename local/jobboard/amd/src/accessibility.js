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
 * Accessibility enhancement module.
 *
 * Provides focus management, screen reader announcements,
 * keyboard navigation enhancements, and ARIA live regions.
 *
 * @module     local_jobboard/accessibility
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/str'], function(Str) {
    'use strict';

    /**
     * Live region element for announcements.
     * @type {HTMLElement|null}
     */
    var liveRegion = null;

    /**
     * Assertive live region for urgent announcements.
     * @type {HTMLElement|null}
     */
    var assertiveLiveRegion = null;

    /**
     * Focus trap stack.
     * @type {Array}
     */
    var focusTrapStack = [];

    /**
     * Previously focused element.
     * @type {HTMLElement|null}
     */
    var previousFocus = null;

    /**
     * Create or get the live region element.
     *
     * @param {boolean} assertive Whether to use assertive announcements.
     * @return {HTMLElement} Live region element.
     */
    var getLiveRegion = function(assertive) {
        if (assertive) {
            if (!assertiveLiveRegion) {
                assertiveLiveRegion = document.createElement('div');
                assertiveLiveRegion.id = 'jb-live-region-assertive';
                assertiveLiveRegion.className = 'visually-hidden';
                assertiveLiveRegion.setAttribute('aria-live', 'assertive');
                assertiveLiveRegion.setAttribute('aria-atomic', 'true');
                assertiveLiveRegion.setAttribute('role', 'alert');
                document.body.appendChild(assertiveLiveRegion);
            }
            return assertiveLiveRegion;
        }

        if (!liveRegion) {
            liveRegion = document.createElement('div');
            liveRegion.id = 'jb-live-region-polite';
            liveRegion.className = 'visually-hidden';
            liveRegion.setAttribute('aria-live', 'polite');
            liveRegion.setAttribute('aria-atomic', 'true');
            liveRegion.setAttribute('role', 'status');
            document.body.appendChild(liveRegion);
        }
        return liveRegion;
    };

    /**
     * Announce a message to screen readers.
     *
     * @param {string} message Message to announce.
     * @param {Object} [options] Options.
     * @param {boolean} [options.assertive] Use assertive announcement.
     * @param {number} [options.delay] Delay before announcement in ms.
     * @param {boolean} [options.clear] Clear after announcement.
     */
    var announce = function(message, options) {
        options = options || {};
        var region = getLiveRegion(options.assertive);

        var doAnnounce = function() {
            // Clear and re-add to trigger announcement.
            region.textContent = '';
            requestAnimationFrame(function() {
                region.textContent = message;

                if (options.clear !== false) {
                    setTimeout(function() {
                        region.textContent = '';
                    }, 1000);
                }
            });
        };

        if (options.delay) {
            setTimeout(doAnnounce, options.delay);
        } else {
            doAnnounce();
        }
    };

    /**
     * Get all focusable elements within a container.
     *
     * @param {HTMLElement} container Container element.
     * @return {Array} Array of focusable elements.
     */
    var getFocusableElements = function(container) {
        var selector = [
            'a[href]',
            'button:not([disabled])',
            'input:not([disabled]):not([type="hidden"])',
            'select:not([disabled])',
            'textarea:not([disabled])',
            '[tabindex]:not([tabindex="-1"])',
            '[contenteditable="true"]'
        ].join(', ');

        var elements = container.querySelectorAll(selector);
        return Array.from(elements).filter(function(el) {
            return el.offsetParent !== null; // Visible elements only.
        });
    };

    /**
     * Create a focus trap within an element.
     *
     * @param {HTMLElement} element Element to trap focus within.
     * @param {Object} [options] Options.
     * @param {boolean} [options.initialFocus] Element to focus initially.
     * @param {boolean} [options.returnFocus] Return focus on release.
     * @return {Object} Focus trap controller with release method.
     */
    var trapFocus = function(element, options) {
        options = options || {};

        // Store current focus.
        previousFocus = document.activeElement;

        var focusableElements = getFocusableElements(element);
        var firstFocusable = focusableElements[0];
        var lastFocusable = focusableElements[focusableElements.length - 1];

        // Set initial focus.
        if (options.initialFocus) {
            if (typeof options.initialFocus === 'string') {
                var target = element.querySelector(options.initialFocus);
                if (target) {
                    target.focus();
                }
            } else if (options.initialFocus instanceof HTMLElement) {
                options.initialFocus.focus();
            }
        } else if (firstFocusable) {
            firstFocusable.focus();
        }

        // Handle Tab key.
        var handleKeydown = function(e) {
            if (e.key !== 'Tab') {
                return;
            }

            // Refresh focusable elements in case DOM changed.
            focusableElements = getFocusableElements(element);
            firstFocusable = focusableElements[0];
            lastFocusable = focusableElements[focusableElements.length - 1];

            if (e.shiftKey) {
                // Shift+Tab: wrap from first to last.
                if (document.activeElement === firstFocusable) {
                    e.preventDefault();
                    lastFocusable.focus();
                }
            } else {
                // Tab: wrap from last to first.
                if (document.activeElement === lastFocusable) {
                    e.preventDefault();
                    firstFocusable.focus();
                }
            }
        };

        element.addEventListener('keydown', handleKeydown);

        // Create trap controller.
        var trap = {
            element: element,
            release: function() {
                element.removeEventListener('keydown', handleKeydown);

                // Remove from stack.
                var index = focusTrapStack.indexOf(trap);
                if (index > -1) {
                    focusTrapStack.splice(index, 1);
                }

                // Return focus.
                if (options.returnFocus !== false && previousFocus) {
                    previousFocus.focus();
                    previousFocus = null;
                }
            },
            updateFocusableElements: function() {
                focusableElements = getFocusableElements(element);
                firstFocusable = focusableElements[0];
                lastFocusable = focusableElements[focusableElements.length - 1];
            }
        };

        focusTrapStack.push(trap);
        return trap;
    };

    /**
     * Move focus to an element with smooth scroll.
     *
     * @param {HTMLElement|string} target Target element or selector.
     * @param {Object} [options] Options.
     * @param {boolean} [options.preventScroll] Prevent scrolling.
     * @param {boolean} [options.announce] Announce to screen readers.
     * @param {string} [options.announceText] Custom announcement text.
     */
    var moveFocus = function(target, options) {
        options = options || {};

        var element = typeof target === 'string' ? document.querySelector(target) : target;
        if (!element) {
            return;
        }

        // Make element focusable if needed.
        if (!element.hasAttribute('tabindex')) {
            element.setAttribute('tabindex', '-1');
        }

        // Focus element.
        element.focus({
            preventScroll: options.preventScroll
        });

        // Scroll into view if not prevented.
        if (!options.preventScroll) {
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // Announce if requested.
        if (options.announce) {
            var text = options.announceText || element.textContent || element.getAttribute('aria-label');
            if (text) {
                announce(text.trim());
            }
        }
    };

    /**
     * Handle Escape key to close modals/dialogs.
     *
     * @param {Function} callback Callback to run on Escape.
     * @return {Function} Cleanup function.
     */
    var onEscape = function(callback) {
        var handler = function(e) {
            if (e.key === 'Escape') {
                callback(e);
            }
        };

        document.addEventListener('keydown', handler);

        return function() {
            document.removeEventListener('keydown', handler);
        };
    };

    /**
     * Initialize roving tabindex on a group of elements.
     *
     * @param {HTMLElement} container Container element.
     * @param {string} selector Selector for roving elements.
     * @param {Object} [options] Options.
     * @param {string} [options.orientation] Orientation: horizontal, vertical, both.
     * @param {boolean} [options.loop] Loop at ends.
     */
    var initRovingTabindex = function(container, selector, options) {
        options = options || {};
        var orientation = options.orientation || 'horizontal';
        var loop = options.loop !== false;

        var elements = container.querySelectorAll(selector);
        if (!elements.length) {
            return;
        }

        // Set initial tabindex.
        elements.forEach(function(el, index) {
            el.setAttribute('tabindex', index === 0 ? '0' : '-1');
        });

        var currentIndex = 0;

        var moveTo = function(index) {
            elements[currentIndex].setAttribute('tabindex', '-1');

            if (loop) {
                if (index < 0) {
                    index = elements.length - 1;
                }
                if (index >= elements.length) {
                    index = 0;
                }
            } else {
                index = Math.max(0, Math.min(index, elements.length - 1));
            }

            currentIndex = index;
            elements[currentIndex].setAttribute('tabindex', '0');
            elements[currentIndex].focus();
        };

        container.addEventListener('keydown', function(e) {
            var handled = false;

            switch (e.key) {
                case 'ArrowLeft':
                    if (orientation === 'horizontal' || orientation === 'both') {
                        moveTo(currentIndex - 1);
                        handled = true;
                    }
                    break;
                case 'ArrowRight':
                    if (orientation === 'horizontal' || orientation === 'both') {
                        moveTo(currentIndex + 1);
                        handled = true;
                    }
                    break;
                case 'ArrowUp':
                    if (orientation === 'vertical' || orientation === 'both') {
                        moveTo(currentIndex - 1);
                        handled = true;
                    }
                    break;
                case 'ArrowDown':
                    if (orientation === 'vertical' || orientation === 'both') {
                        moveTo(currentIndex + 1);
                        handled = true;
                    }
                    break;
                case 'Home':
                    moveTo(0);
                    handled = true;
                    break;
                case 'End':
                    moveTo(elements.length - 1);
                    handled = true;
                    break;
            }

            if (handled) {
                e.preventDefault();
            }
        });

        // Handle click to update current.
        elements.forEach(function(el, index) {
            el.addEventListener('click', function() {
                elements[currentIndex].setAttribute('tabindex', '-1');
                currentIndex = index;
                el.setAttribute('tabindex', '0');
            });
        });
    };

    /**
     * Check if user prefers reduced motion.
     *
     * @return {boolean} True if reduced motion is preferred.
     */
    var prefersReducedMotion = function() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    };

    /**
     * Check if user prefers high contrast.
     *
     * @return {boolean} True if high contrast is preferred.
     */
    var prefersHighContrast = function() {
        return window.matchMedia('(prefers-contrast: more)').matches ||
               window.matchMedia('(-ms-high-contrast: active)').matches;
    };

    /**
     * Initialize accessibility enhancements.
     *
     * @param {Object} [options] Configuration options.
     */
    var init = function(options) {
        options = options || {};

        // Create live regions.
        getLiveRegion(false);
        getLiveRegion(true);

        // Add keyboard user detection.
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                document.body.classList.add('jb-keyboard-user');
            }
        });

        document.addEventListener('mousedown', function() {
            document.body.classList.remove('jb-keyboard-user');
        });

        // Initialize skip links smooth scroll.
        document.querySelectorAll('.jb-skip-link').forEach(function(link) {
            link.addEventListener('click', function(e) {
                var targetId = link.getAttribute('href').slice(1);
                var target = document.getElementById(targetId);
                if (target) {
                    e.preventDefault();
                    moveFocus(target, {announce: true});
                }
            });
        });

        // Auto-focus first error on form submit.
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                setTimeout(function() {
                    var firstError = form.querySelector('.is-invalid, [aria-invalid="true"]');
                    if (firstError) {
                        moveFocus(firstError);
                        announce('Form has errors. Please correct them.');
                    }
                }, 100);
            });
        });

        // Announce page changes for SPAs.
        if (options.announcePageChanges) {
            var lastTitle = document.title;

            var observer = new MutationObserver(function() {
                if (document.title !== lastTitle) {
                    lastTitle = document.title;
                    announce('Page changed: ' + document.title);
                }
            });

            var titleEl = document.querySelector('title');
            if (titleEl) {
                observer.observe(titleEl, {childList: true});
            }
        }
    };

    return {
        init: init,
        announce: announce,
        trapFocus: trapFocus,
        moveFocus: moveFocus,
        getFocusableElements: getFocusableElements,
        onEscape: onEscape,
        initRovingTabindex: initRovingTabindex,
        prefersReducedMotion: prefersReducedMotion,
        prefersHighContrast: prefersHighContrast
    };
});

/**
 * Performance and perceived speed optimization module.
 *
 * @module     local_jobboard/performance
 * @copyright  2024 Starter Theme
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str'], function($, Str) {
    'use strict';

    // Configuration
    var config = {
        lazyLoadThreshold: 200,    // Pixels before viewport
        prefetchDelay: 150,        // ms before prefetch on hover
        progressBarSpeed: 300,     // ms for progress bar updates
        skeletonDelay: 100,        // ms before showing skeleton
        optimisticTimeout: 5000    // ms before optimistic update timeout
    };

    // State
    var state = {
        observer: null,
        progressBar: null,
        prefetchCache: new Set()
    };

    /**
     * Initialize all performance features.
     *
     * @param {Object} options - Configuration options
     */
    function init(options) {
        config = $.extend({}, config, options);

        initLazyLoading();
        initPrefetch();
        initProgressBar();
        initRippleEffect();
        initButtonLoading();
        initContentTransitions();
    }

    // -------------------------------------------------------------------------
    // Lazy Loading
    // -------------------------------------------------------------------------

    /**
     * Initialize lazy loading with Intersection Observer.
     */
    function initLazyLoading() {
        if (!('IntersectionObserver' in window)) {
            // Fallback: load all images immediately
            loadAllLazyImages();
            return;
        }

        state.observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var element = entry.target;
                    loadLazyElement(element);
                    state.observer.unobserve(element);
                }
            });
        }, {
            rootMargin: config.lazyLoadThreshold + 'px'
        });

        // Observe all lazy elements
        document.querySelectorAll('[data-lazy-src], .jb-lazy-img').forEach(function(el) {
            state.observer.observe(el);
        });

        // Re-observe when new content is added
        $(document).on('contentUpdated', function(e, container) {
            $(container).find('[data-lazy-src], .jb-lazy-img').each(function() {
                state.observer.observe(this);
            });
        });
    }

    /**
     * Load a lazy element.
     *
     * @param {HTMLElement} element - Element to load
     */
    function loadLazyElement(element) {
        var $el = $(element);

        if ($el.is('img')) {
            var src = $el.data('lazy-src') || $el.data('src');
            if (src) {
                // Create preloader
                var img = new Image();
                img.onload = function() {
                    $el.attr('src', src).addClass('jb-loaded');
                    $el.closest('.jb-lazy-wrapper').addClass('jb-loaded');
                };
                img.src = src;
            }
        } else if ($el.data('lazy-bg')) {
            // Background image
            $el.css('background-image', 'url(' + $el.data('lazy-bg') + ')');
            $el.addClass('jb-loaded');
        }
    }

    /**
     * Fallback: load all lazy images without observer.
     */
    function loadAllLazyImages() {
        $('[data-lazy-src], .jb-lazy-img').each(function() {
            loadLazyElement(this);
        });
    }

    // -------------------------------------------------------------------------
    // Link Prefetching
    // -------------------------------------------------------------------------

    /**
     * Initialize intelligent link prefetching.
     */
    function initPrefetch() {
        var prefetchTimer = null;

        $(document).on('mouseenter', 'a[href]:not([data-no-prefetch])', function() {
            var $link = $(this);
            var href = $link.attr('href');

            // Skip external links, anchors, and already prefetched
            if (!href ||
                href.startsWith('#') ||
                href.startsWith('javascript:') ||
                href.startsWith('mailto:') ||
                href.startsWith('tel:') ||
                state.prefetchCache.has(href)) {
                return;
            }

            // Delay prefetch to avoid prefetching on quick mouse-overs
            prefetchTimer = setTimeout(function() {
                prefetchUrl(href);
            }, config.prefetchDelay);
        });

        $(document).on('mouseleave', 'a[href]', function() {
            if (prefetchTimer) {
                clearTimeout(prefetchTimer);
                prefetchTimer = null;
            }
        });
    }

    /**
     * Prefetch a URL.
     *
     * @param {string} url - URL to prefetch
     */
    function prefetchUrl(url) {
        if (state.prefetchCache.has(url)) {
            return;
        }

        // Use <link rel="prefetch"> for supported browsers
        if ('prefetch' in document.createElement('link').relList) {
            var link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url;
            link.as = 'document';
            document.head.appendChild(link);
        }

        state.prefetchCache.add(url);
    }

    // -------------------------------------------------------------------------
    // Progress Bar
    // -------------------------------------------------------------------------

    /**
     * Initialize top progress bar for page navigation.
     */
    function initProgressBar() {
        // Create progress bar element
        state.progressBar = $('<div class="jb-progress-bar-top"></div>').appendTo('body');

        // Hook into page navigation
        $(window).on('beforeunload', function() {
            showProgress(30);
        });

        // Hook into AJAX requests
        $(document).ajaxStart(function() {
            showProgress(30);
        }).ajaxStop(function() {
            completeProgress();
        });
    }

    /**
     * Show progress bar at a percentage.
     *
     * @param {number} percent - Progress percentage
     */
    function showProgress(percent) {
        if (state.progressBar) {
            state.progressBar.css({
                width: percent + '%',
                opacity: 1
            });
        }
    }

    /**
     * Update progress bar.
     *
     * @param {number} percent - Progress percentage
     */
    function updateProgress(percent) {
        if (state.progressBar) {
            state.progressBar.css('width', percent + '%');
        }
    }

    /**
     * Complete progress bar animation.
     */
    function completeProgress() {
        if (state.progressBar) {
            state.progressBar.css('width', '100%');
            setTimeout(function() {
                state.progressBar.css({
                    opacity: 0,
                    width: '0%'
                });
            }, config.progressBarSpeed);
        }
    }

    // -------------------------------------------------------------------------
    // Ripple Effect
    // -------------------------------------------------------------------------

    /**
     * Initialize material-style ripple effect on buttons.
     */
    function initRippleEffect() {
        $(document).on('click', '.jb-ripple-container, .jb-btn', function(e) {
            var $el = $(this);

            // Don't add ripple if disabled
            if ($el.is(':disabled') || $el.hasClass('disabled')) {
                return;
            }

            // Calculate ripple position
            var rect = this.getBoundingClientRect();
            var x = e.clientX - rect.left;
            var y = e.clientY - rect.top;

            // Create ripple
            var $ripple = $('<span class="jb-ripple"></span>').css({
                left: x + 'px',
                top: y + 'px'
            });

            $el.append($ripple);

            // Remove ripple after animation
            setTimeout(function() {
                $ripple.remove();
            }, 600);
        });
    }

    // -------------------------------------------------------------------------
    // Button Loading States
    // -------------------------------------------------------------------------

    /**
     * Initialize button loading state management.
     */
    function initButtonLoading() {
        $(document).on('click', '[data-loading-text]', function() {
            var $btn = $(this);
            if (!$btn.hasClass('jb-btn-loading')) {
                setButtonLoading($btn, true);
            }
        });
    }

    /**
     * Set button loading state.
     *
     * @param {jQuery} $btn - Button element
     * @param {boolean} loading - Loading state
     */
    function setButtonLoading($btn, loading) {
        if (loading) {
            $btn.data('original-text', $btn.html());
            $btn.addClass('jb-btn-loading');
            if ($btn.data('loading-text')) {
                $btn.html($btn.data('loading-text'));
            }
        } else {
            $btn.removeClass('jb-btn-loading');
            if ($btn.data('original-text')) {
                $btn.html($btn.data('original-text'));
            }
        }
    }

    // -------------------------------------------------------------------------
    // Optimistic UI Updates
    // -------------------------------------------------------------------------

    /**
     * Perform an optimistic UI update.
     *
     * @param {Object} options - Update options
     * @param {jQuery} options.element - Element to update
     * @param {Function} options.optimisticUpdate - Function to apply optimistic update
     * @param {Function} options.serverAction - Promise that performs server action
     * @param {Function} options.rollback - Function to rollback on failure
     */
    function optimisticUpdate(options) {
        var $el = $(options.element);
        var originalState = $el.html();

        // Apply optimistic update immediately
        $el.addClass('jb-optimistic-pending');
        options.optimisticUpdate($el);

        // Perform server action
        var timeout = setTimeout(function() {
            rollbackUpdate($el, originalState, options.rollback);
        }, config.optimisticTimeout);

        options.serverAction()
            .then(function(response) {
                clearTimeout(timeout);
                $el.removeClass('jb-optimistic-pending').addClass('jb-optimistic-success');
                setTimeout(function() {
                    $el.removeClass('jb-optimistic-success');
                }, 600);

                if (options.onSuccess) {
                    options.onSuccess(response, $el);
                }
            })
            .catch(function(error) {
                clearTimeout(timeout);
                rollbackUpdate($el, originalState, options.rollback);

                if (options.onError) {
                    options.onError(error, $el);
                }
            });
    }

    /**
     * Rollback an optimistic update.
     *
     * @param {jQuery} $el - Element
     * @param {string} originalState - Original HTML
     * @param {Function} rollbackFn - Custom rollback function
     */
    function rollbackUpdate($el, originalState, rollbackFn) {
        $el.removeClass('jb-optimistic-pending').addClass('jb-optimistic-error');

        if (rollbackFn) {
            rollbackFn($el);
        } else {
            $el.html(originalState);
        }

        setTimeout(function() {
            $el.removeClass('jb-optimistic-error');
        }, 400);
    }

    // -------------------------------------------------------------------------
    // Content Transitions
    // -------------------------------------------------------------------------

    /**
     * Initialize smooth content transitions.
     */
    function initContentTransitions() {
        // Animate new content loaded via AJAX
        $(document).on('contentUpdated', function(e, container) {
            $(container).addClass('jb-content-loaded');
            setTimeout(function() {
                $(container).removeClass('jb-content-loaded');
            }, 300);
        });
    }

    /**
     * Replace content with smooth transition.
     *
     * @param {jQuery} $container - Container element
     * @param {string|jQuery} newContent - New content
     * @param {Function} callback - Callback after transition
     */
    function replaceContent($container, newContent, callback) {
        var $current = $container.children();

        // Fade out current content
        $current.addClass('jb-fade-swap-exit-active');

        setTimeout(function() {
            // Replace content
            $container.html(newContent);

            // Fade in new content
            var $new = $container.children();
            $new.addClass('jb-fade-swap-enter');

            // Trigger reflow
            $new[0].offsetHeight; // eslint-disable-line

            $new.removeClass('jb-fade-swap-enter').addClass('jb-fade-swap-enter-active');

            setTimeout(function() {
                $new.removeClass('jb-fade-swap-enter-active');
                if (callback) {
                    callback();
                }
            }, 200);
        }, 200);
    }

    // -------------------------------------------------------------------------
    // Skeleton Loading
    // -------------------------------------------------------------------------

    /**
     * Show skeleton loading state for an element.
     *
     * @param {jQuery} $container - Container to show skeleton in
     * @param {string} type - Skeleton type: text, card, table, list
     * @param {Object} options - Additional options
     */
    function showSkeleton($container, type, options) {
        options = options || {};
        var skeletonHtml = generateSkeletonHtml(type, options);

        $container.data('original-content', $container.html());
        $container.html(skeletonHtml);
        $container.attr('aria-busy', 'true');
    }

    /**
     * Hide skeleton and restore content.
     *
     * @param {jQuery} $container - Container with skeleton
     * @param {string} newContent - Optional new content (if not provided, restores original)
     */
    function hideSkeleton($container, newContent) {
        var content = newContent || $container.data('original-content');

        replaceContent($container, content, function() {
            $container.removeAttr('aria-busy');
            $container.removeData('original-content');
        });
    }

    /**
     * Generate skeleton HTML based on type.
     *
     * @param {string} type - Skeleton type
     * @param {Object} options - Options
     * @returns {string} Skeleton HTML
     */
    function generateSkeletonHtml(type, options) {
        var html = '<div class="jb-skeleton-wrapper" role="status" aria-label="Loading...">';

        switch (type) {
            case 'text':
                var lines = options.lines || 3;
                html += '<div class="jb-skeleton-text-block">';
                for (var i = 0; i < lines; i++) {
                    var width = 100 - (i * 10) - Math.random() * 15;
                    html += '<div class="jb-skeleton-text" style="width: ' + width + '%;"></div>';
                }
                html += '</div>';
                break;

            case 'card':
                html += '<div class="jb-skeleton-card">';
                html += '<div class="jb-skeleton-card-header">';
                html += '<div class="jb-skeleton-circle" style="width: 40px; height: 40px;"></div>';
                html += '<div class="jb-skeleton-title" style="width: 60%;"></div>';
                html += '</div>';
                html += '<div class="jb-skeleton-card-body">';
                html += '<div class="jb-skeleton-text" style="width: 100%;"></div>';
                html += '<div class="jb-skeleton-text" style="width: 85%;"></div>';
                html += '<div class="jb-skeleton-text" style="width: 70%;"></div>';
                html += '</div>';
                html += '</div>';
                break;

            case 'table':
                var rows = options.rows || 5;
                var cols = options.cols || 4;
                html += '<div class="table-responsive"><table class="table">';
                html += '<thead><tr>';
                for (var c = 0; c < cols; c++) {
                    html += '<th><div class="jb-skeleton-text" style="width: ' + (50 + Math.random() * 30) + '%;"></div></th>';
                }
                html += '</tr></thead><tbody>';
                for (var r = 0; r < rows; r++) {
                    html += '<tr>';
                    for (var cc = 0; cc < cols; cc++) {
                        html += '<td><div class="jb-skeleton-text"></div></td>';
                    }
                    html += '</tr>';
                }
                html += '</tbody></table></div>';
                break;

            case 'list':
                var items = options.items || 5;
                html += '<ul class="list-group">';
                for (var li = 0; li < items; li++) {
                    html += '<li class="list-group-item d-flex align-items-center">';
                    html += '<div class="jb-skeleton-circle me-3" style="width: 40px; height: 40px;"></div>';
                    html += '<div class="flex-grow-1">';
                    html += '<div class="jb-skeleton-text" style="width: ' + (60 + Math.random() * 20) + '%;"></div>';
                    html += '<div class="jb-skeleton-text" style="width: ' + (40 + Math.random() * 20) + '%;"></div>';
                    html += '</div>';
                    html += '</li>';
                }
                html += '</ul>';
                break;

            default:
                html += '<div class="jb-skeleton-text-block">';
                html += '<div class="jb-skeleton-text" style="width: 100%;"></div>';
                html += '<div class="jb-skeleton-text" style="width: 90%;"></div>';
                html += '<div class="jb-skeleton-text" style="width: 75%;"></div>';
                html += '</div>';
        }

        html += '<span class="visually-hidden">' + Str.get_string('loading') + '</span></div>';
        return html;
    }

    // -------------------------------------------------------------------------
    // Utility Functions
    // -------------------------------------------------------------------------

    /**
     * Debounce function calls.
     *
     * @param {Function} func - Function to debounce
     * @param {number} wait - Wait time in ms
     * @returns {Function} Debounced function
     */
    function debounce(func, wait) {
        var timeout;
        return function() {
            var context = this;
            var args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }

    /**
     * Throttle function calls.
     *
     * @param {Function} func - Function to throttle
     * @param {number} limit - Time limit in ms
     * @returns {Function} Throttled function
     */
    function throttle(func, limit) {
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
    }

    // Public API
    return {
        init: init,
        // Lazy loading
        loadLazyElement: loadLazyElement,
        // Progress bar
        showProgress: showProgress,
        updateProgress: updateProgress,
        completeProgress: completeProgress,
        // Button states
        setButtonLoading: setButtonLoading,
        // Optimistic UI
        optimisticUpdate: optimisticUpdate,
        // Content transitions
        replaceContent: replaceContent,
        // Skeleton
        showSkeleton: showSkeleton,
        hideSkeleton: hideSkeleton,
        // Utilities
        debounce: debounce,
        throttle: throttle
    };
});

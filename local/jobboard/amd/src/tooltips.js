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
 * AMD module for Bootstrap tooltip initialization.
 *
 * Replaces inline tooltip initialization with AMD module.
 * Can be loaded on any page that uses Bootstrap tooltips.
 *
 * @module     local_jobboard/tooltips
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    'use strict';

    /**
     * Initialize Bootstrap tooltips.
     *
     * @param {Object} options Configuration options.
     * @param {string} options.selector Selector for tooltip elements.
     * @param {string} options.placement Default placement (top, bottom, left, right).
     * @param {boolean} options.html Allow HTML content in tooltips.
     */
    var init = function(options) {
        options = options || {};

        var selector = options.selector || '[data-toggle="tooltip"]';
        var placement = options.placement || 'top';
        var html = options.html || false;

        // Initialize tooltips on matching elements.
        $(selector).tooltip({
            placement: placement,
            html: html
        });

        // Also handle dynamically added elements.
        $(document).on('mouseenter', selector, function() {
            var $el = $(this);
            if (!$el.data('bs.tooltip')) {
                $el.tooltip({
                    placement: placement,
                    html: html
                });
                $el.tooltip('show');
            }
        });
    };

    /**
     * Refresh tooltips (useful after dynamic content is added).
     *
     * @param {string} selector Optional selector to limit refresh scope.
     */
    var refresh = function(selector) {
        selector = selector || '[data-toggle="tooltip"]';
        $(selector).tooltip('dispose').tooltip();
    };

    /**
     * Destroy all tooltips.
     *
     * @param {string} selector Optional selector to limit scope.
     */
    var destroy = function(selector) {
        selector = selector || '[data-toggle="tooltip"]';
        $(selector).tooltip('dispose');
    };

    return {
        init: init,
        refresh: refresh,
        destroy: destroy
    };
});

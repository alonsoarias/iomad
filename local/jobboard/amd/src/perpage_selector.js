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
 * AMD module for per-page selector functionality.
 *
 * Replaces inline onchange="window.location.href=this.value" with AMD event handler.
 *
 * @module     local_jobboard/perpage_selector
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    'use strict';

    /**
     * Initialize per-page selector handlers.
     *
     * @param {Object} options Configuration options.
     * @param {string} options.selector Selector for per-page select elements.
     */
    var init = function(options) {
        options = options || {};

        var selector = options.selector || '.jb-perpage-select';

        // Handle change events on per-page select elements.
        $(document).on('change', selector, function() {
            var url = $(this).val();
            if (url) {
                window.location.href = url;
            }
        });
    };

    return {
        init: init
    };
});

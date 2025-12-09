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
 * AMD module for card action buttons.
 *
 * Handles click events on action buttons within stretched-link cards
 * to prevent event propagation to the card link.
 *
 * @module     local_jobboard/card_actions
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    'use strict';

    /**
     * Initialize card action handlers.
     *
     * @param {Object} options Configuration options.
     * @param {string} options.buttonSelector Selector for action buttons.
     * @param {string} options.cardSelector Selector for cards with stretched-link.
     */
    var init = function(options) {
        options = options || {};

        // Default to buttons and links within vacancy cards.
        var buttonSelector = options.buttonSelector || '.jb-vacancy-card .btn, .jb-vacancy-card .badge';
        var cardSelector = options.cardSelector || '.jb-vacancy-card';

        // Prevent click propagation from action buttons to stretched-link.
        $(document).on('click', buttonSelector, function(e) {
            // Check if this is within a card with stretched-link.
            var $card = $(this).closest(cardSelector);
            if ($card.find('.stretched-link').length > 0) {
                e.stopPropagation();
            }
        });
    };

    return {
        init: init
    };
});

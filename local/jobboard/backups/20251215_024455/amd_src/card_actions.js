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
 * Card actions module for handling vacancy/convocatoria card interactions.
 *
 * @module     local_jobboard/card_actions
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/modal_factory', 'core/modal_events', 'core/str', 'core/notification'],
    function(ModalFactory, ModalEvents, Str, Notification) {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        buttonSelector: null,
        cardSelector: null,
        initialized: false
    };

    /**
     * Handle card button click with confirmation.
     *
     * @param {Event} e The click event.
     * @param {string} action The action type.
     * @param {HTMLElement} card The card element.
     */
    var handleConfirmAction = function(e, action, card) {
        e.preventDefault();

        var stringKey = action + '_confirm';
        var url = e.target.closest('a').href;

        Str.get_strings([
            {key: stringKey, component: 'local_jobboard'},
            {key: 'confirm', component: 'core'},
            {key: 'cancel', component: 'core'}
        ]).then(function(strings) {
            return ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: strings[1],
                body: strings[0]
            });
        }).then(function(modal) {
            modal.setSaveButtonText(strings[1]);
            modal.show();

            modal.getRoot().on(ModalEvents.save, function() {
                window.location.href = url;
            });

            modal.getRoot().on(ModalEvents.hidden, function() {
                modal.destroy();
            });
        }).catch(function(error) {
            Notification.exception(error);
        });
    };

    /**
     * Handle quick view action.
     *
     * @param {Event} e The click event.
     * @param {HTMLElement} card The card element.
     */
    var handleQuickView = function(e, card) {
        e.preventDefault();

        var cardId = card.dataset.id;
        var cardType = card.dataset.type || 'vacancy';

        // Dispatch event for parent components to handle.
        var event = new CustomEvent('jobboard:quickview', {
            detail: {
                id: cardId,
                type: cardType,
                card: card
            }
        });
        document.dispatchEvent(event);
    };

    /**
     * Handle card hover effects.
     *
     * @param {Event} e The mouse event.
     */
    var handleCardHover = function(e) {
        var card = e.target.closest(state.cardSelector);
        if (!card) {
            return;
        }

        if (e.type === 'mouseenter') {
            card.classList.add('jb-card-hover');
        } else {
            card.classList.remove('jb-card-hover');
        }
    };

    /**
     * Handle button click events.
     *
     * @param {Event} e The click event.
     */
    var onButtonClick = function(e) {
        var button = e.target.closest(state.buttonSelector);
        if (!button) {
            return;
        }

        var action = button.dataset.action;
        var card = button.closest(state.cardSelector);

        switch (action) {
            case 'delete':
            case 'archive':
            case 'publish':
            case 'unpublish':
                handleConfirmAction(e, action, card);
                break;
            case 'quickview':
                handleQuickView(e, card);
                break;
            default:
                // Allow default behavior for unhandled actions.
                break;
        }
    };

    /**
     * Initialize the card actions module.
     *
     * @param {Object} config Configuration object.
     * @param {string} [config.buttonSelector='.jb-card-action'] CSS selector for action buttons.
     * @param {string} [config.cardSelector='.jb-vacancy-card'] CSS selector for cards.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        config = config || {};
        state.buttonSelector = config.buttonSelector || '.jb-card-action';
        state.cardSelector = config.cardSelector || '.jb-vacancy-card';

        // Event delegation on body.
        document.body.addEventListener('click', onButtonClick);
        document.body.addEventListener('mouseenter', handleCardHover, true);
        document.body.addEventListener('mouseleave', handleCardHover, true);

        state.initialized = true;
    };

    return {
        init: init
    };
});

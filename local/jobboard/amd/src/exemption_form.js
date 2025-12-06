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
 * AMD module for ISER exemption form quick selection buttons.
 *
 * @module     local_jobboard/exemption_form
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {
    'use strict';

    /**
     * Select all options in the multi-select element.
     *
     * @param {HTMLSelectElement} selectElement The select element
     */
    const selectAllDocs = function(selectElement) {
        for (let i = 0; i < selectElement.options.length; i++) {
            selectElement.options[i].selected = true;
        }
    };

    /**
     * Select only identity document options.
     *
     * @param {HTMLSelectElement} selectElement The select element
     */
    const selectIdentityDocs = function(selectElement) {
        const identity = ['cedula', 'rut', 'libreta_militar'];
        for (let i = 0; i < selectElement.options.length; i++) {
            selectElement.options[i].selected = identity.includes(selectElement.options[i].value);
        }
    };

    /**
     * Select only background check document options.
     *
     * @param {HTMLSelectElement} selectElement The select element
     */
    const selectBackgroundDocs = function(selectElement) {
        const background = [
            'antecedentes_procuraduria',
            'antecedentes_contraloria',
            'antecedentes_policia',
            'rnmc'
        ];
        for (let i = 0; i < selectElement.options.length; i++) {
            selectElement.options[i].selected = background.includes(selectElement.options[i].value);
        }
    };

    return {
        /**
         * Initialize the exemption form quick selection buttons.
         *
         * @param {string} selectId The ID of the multi-select element
         */
        init: function(selectId) {
            const selectElement = document.getElementById(selectId);
            if (!selectElement) {
                return;
            }

            // Find the button container.
            const container = document.querySelector('.exemption-quick-select');
            if (!container) {
                return;
            }

            // Attach event listeners to the buttons.
            const selectAllBtn = container.querySelector('[data-action="selectall"]');
            const selectIdentityBtn = container.querySelector('[data-action="selectidentity"]');
            const selectBackgroundBtn = container.querySelector('[data-action="selectbackground"]');

            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectAllDocs(selectElement);
                });
            }

            if (selectIdentityBtn) {
                selectIdentityBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectIdentityDocs(selectElement);
                });
            }

            if (selectBackgroundBtn) {
                selectBackgroundBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectBackgroundDocs(selectElement);
                });
            }
        }
    };
});

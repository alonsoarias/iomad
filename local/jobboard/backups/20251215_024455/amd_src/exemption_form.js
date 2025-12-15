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
 * Exemption form module for quick select document type groups.
 *
 * @module     local_jobboard/exemption_form
 * @copyright  2024-2025 ISER - Instituto Superior de Educación Rural
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/str', 'core/notification'], function(Str, Notification) {
    'use strict';

    /**
     * Module state.
     * @type {Object}
     */
    var state = {
        checkboxContainer: null,
        documentGroups: {},
        initialized: false
    };

    /**
     * Default document type groups.
     * @type {Object}
     */
    var DEFAULT_GROUPS = {
        identity: ['cedula', 'rut', 'military_card'],
        academic: ['undergraduate', 'graduate', 'graduation_cert', 'professional_card'],
        background: ['criminal_record', 'disciplinary_record', 'fiscal_record'],
        health: ['medical_cert', 'eps_cert', 'pension_cert'],
        employment: ['work_cert', 'cv', 'bank_cert']
    };

    /**
     * Get all document checkboxes.
     *
     * @return {NodeList} List of checkbox elements.
     */
    var getCheckboxes = function() {
        if (!state.checkboxContainer) {
            return document.querySelectorAll('input[type="checkbox"][name*="doctype"]');
        }
        return state.checkboxContainer.querySelectorAll('input[type="checkbox"]');
    };

    /**
     * Select all checkboxes.
     */
    var selectAll = function() {
        getCheckboxes().forEach(function(checkbox) {
            checkbox.checked = true;
        });
        updateCounter();
    };

    /**
     * Deselect all checkboxes.
     */
    var selectNone = function() {
        getCheckboxes().forEach(function(checkbox) {
            checkbox.checked = false;
        });
        updateCounter();
    };

    /**
     * Invert checkbox selection.
     */
    var invertSelection = function() {
        getCheckboxes().forEach(function(checkbox) {
            checkbox.checked = !checkbox.checked;
        });
        updateCounter();
    };

    /**
     * Select checkboxes by group.
     *
     * @param {string} groupName The group name to select.
     */
    var selectGroup = function(groupName) {
        var group = state.documentGroups[groupName] || DEFAULT_GROUPS[groupName];
        if (!group) {
            return;
        }

        getCheckboxes().forEach(function(checkbox) {
            var docType = checkbox.value || checkbox.dataset.doctype || '';
            if (group.indexOf(docType) !== -1) {
                checkbox.checked = true;
            }
        });
        updateCounter();
    };

    /**
     * Update the selected count display.
     */
    var updateCounter = function() {
        var counter = document.querySelector('.jb-exemption-counter');
        if (!counter) {
            return;
        }

        var checkboxes = getCheckboxes();
        var selectedCount = 0;
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                selectedCount++;
            }
        });

        counter.textContent = selectedCount + ' / ' + checkboxes.length;
    };

    /**
     * Handle quick select button click.
     *
     * @param {Event} e The click event.
     */
    var onQuickSelectClick = function(e) {
        var button = e.target.closest('[data-action]');
        if (!button) {
            return;
        }

        var action = button.dataset.action;

        switch (action) {
            case 'selectall':
                e.preventDefault();
                selectAll();
                break;
            case 'selectnone':
                e.preventDefault();
                selectNone();
                break;
            case 'selectinvert':
                e.preventDefault();
                invertSelection();
                break;
            case 'selectidentity':
                e.preventDefault();
                selectGroup('identity');
                break;
            case 'selectacademic':
                e.preventDefault();
                selectGroup('academic');
                break;
            case 'selectbackground':
                e.preventDefault();
                selectGroup('background');
                break;
            case 'selecthealth':
                e.preventDefault();
                selectGroup('health');
                break;
            case 'selectemployment':
                e.preventDefault();
                selectGroup('employment');
                break;
        }
    };

    /**
     * Handle checkbox change.
     *
     * @param {Event} e The change event.
     */
    var onCheckboxChange = function(e) {
        if (!e.target.matches('input[type="checkbox"]')) {
            return;
        }

        var container = e.target.closest('.jb-exemption-form, .jb-doctype-list');
        if (container && container === state.checkboxContainer) {
            updateCounter();
        }
    };

    /**
     * Build quick select buttons HTML.
     *
     * @return {Promise} Promise resolving with HTML string.
     */
    var buildQuickSelectButtons = function() {
        return Str.get_strings([
            {key: 'selectall', component: 'core'},
            {key: 'deselectall', component: 'core'},
            {key: 'selectidentity', component: 'local_jobboard'},
            {key: 'selectbackground', component: 'local_jobboard'}
        ]).then(function(strings) {
            var html = '<div class="jb-quick-select jb-btn-group jb-mb-3">';
            html += '<button type="button" class="jb-btn jb-btn-sm jb-btn-outline-secondary" ';
            html += 'data-action="selectall">' + strings[0] + '</button>';
            html += '<button type="button" class="jb-btn jb-btn-sm jb-btn-outline-secondary" ';
            html += 'data-action="selectnone">' + strings[1] + '</button>';
            html += '<button type="button" class="jb-btn jb-btn-sm jb-btn-outline-primary" ';
            html += 'data-action="selectidentity">' + strings[2] + '</button>';
            html += '<button type="button" class="jb-btn jb-btn-sm jb-btn-outline-primary" ';
            html += 'data-action="selectbackground">' + strings[3] + '</button>';
            html += '</div>';
            html += '<span class="jb-exemption-counter jb-badge jb-badge-secondary jb-ml-2"></span>';
            return html;
        });
    };

    /**
     * Initialize the exemption form module.
     *
     * @param {Object} config Configuration object.
     * @param {string} [config.containerSelector='.jb-exemption-form'] Container selector.
     * @param {Object} [config.documentGroups] Custom document groups definition.
     * @param {boolean} [config.addButtons=true] Whether to add quick select buttons.
     */
    var init = function(config) {
        if (state.initialized) {
            return;
        }

        config = config || {};

        state.checkboxContainer = document.querySelector(config.containerSelector || '.jb-exemption-form');
        state.documentGroups = config.documentGroups || DEFAULT_GROUPS;

        // Add quick select buttons if container exists and config allows.
        if (state.checkboxContainer && config.addButtons !== false) {
            var existingButtons = state.checkboxContainer.querySelector('.jb-quick-select');
            if (!existingButtons) {
                buildQuickSelectButtons().then(function(html) {
                    var wrapper = document.createElement('div');
                    wrapper.innerHTML = html;
                    state.checkboxContainer.insertBefore(wrapper, state.checkboxContainer.firstChild);
                    updateCounter();
                });
            } else {
                updateCounter();
            }
        }

        // Event listeners.
        document.body.addEventListener('click', onQuickSelectClick);
        document.body.addEventListener('change', onCheckboxChange);

        state.initialized = true;
    };

    return {
        init: init,
        selectAll: selectAll,
        selectNone: selectNone,
        selectGroup: selectGroup,
        invertSelection: invertSelection
    };
});

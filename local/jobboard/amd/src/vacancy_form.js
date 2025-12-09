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
 * Vacancy form AMD module for local_jobboard.
 *
 * Handles dynamic loading of companies, departments, and convocatorias via AJAX.
 *
 * @module     local_jobboard/vacancy_form
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'local_jobboard/department_loader',
    'local_jobboard/company_loader',
    'local_jobboard/convocatoria_loader'
], function($, DepartmentLoader, CompanyLoader, ConvocatoriaLoader) {
    'use strict';

    /**
     * Initialize the vacancy form handlers.
     *
     * @param {Object} options Configuration options.
     * @param {number} options.departmentPreselect Optional department ID to preselect.
     * @param {number} options.companyPreselect Optional company ID to preselect.
     * @param {number} options.convocatoriaPreselect Optional convocatoria ID to preselect.
     * @param {boolean} options.includeAllConvocatorias Include all convocatoria statuses.
     */
    var init = function(options) {
        options = options || {};

        // Initialize company loader with vacancy form selectors.
        var companySelect = $('#id_companyid');
        var departmentSelect = $('#id_departmentid');

        if (companySelect.length) {
            CompanyLoader.init({
                companySelector: '#id_companyid',
                departmentSelector: '#id_departmentid',
                preselect: options.companyPreselect,
                departmentPreselect: options.departmentPreselect
            });
        } else {
            // No company selector (non-IOMAD), just initialize department loader if needed.
            if (departmentSelect.length) {
                DepartmentLoader.init({
                    companySelector: '#id_companyid',
                    departmentSelector: '#id_departmentid',
                    preselect: options.departmentPreselect
                });
            }
        }

        // Initialize convocatoria loader.
        var convSelect = $('#id_convocatoriaid');
        if (convSelect.length) {
            ConvocatoriaLoader.init({
                convocatoriaSelector: '#id_convocatoriaid',
                companySelector: '#id_companyid',
                preselect: options.convocatoriaPreselect,
                includeall: options.includeAllConvocatorias
            });
        }
    };

    return {
        init: init
    };
});

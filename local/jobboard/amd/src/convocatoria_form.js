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
 * Convocatoria form AMD module for local_jobboard.
 *
 * Handles dynamic loading of companies and departments via AJAX.
 *
 * @module     local_jobboard/convocatoria_form
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'local_jobboard/department_loader',
    'local_jobboard/company_loader'
], function($, DepartmentLoader, CompanyLoader) {
    'use strict';

    /**
     * Initialize the convocatoria form handlers.
     *
     * @param {Object} options Configuration options.
     * @param {number} options.companyPreselect Optional company ID to preselect.
     * @param {number} options.departmentPreselect Optional department ID to preselect.
     */
    var init = function(options) {
        options = options || {};

        var companySelect = $('#id_companyid');
        var departmentSelect = $('#id_departmentid');

        // Initialize company loader if company selector exists.
        if (companySelect.length) {
            CompanyLoader.init({
                companySelector: '#id_companyid',
                departmentSelector: '#id_departmentid',
                preselect: options.companyPreselect,
                departmentPreselect: options.departmentPreselect
            });
        } else if (departmentSelect.length) {
            // Fallback to department loader only.
            DepartmentLoader.init({
                companySelector: '#id_companyid, select[name="companyid"]',
                departmentSelector: '#id_departmentid, select[name="departmentid"]',
                preselect: options.departmentPreselect
            });
        }
    };

    return {
        init: init
    };
});

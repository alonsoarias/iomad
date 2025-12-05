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
 * AMD module for the apply modal popup.
 *
 * Shows a modal asking if the user is registered or not when attempting
 * to apply for a vacancy.
 *
 * @module     local_jobboard/apply_modal
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/modal_factory', 'core/modal_events', 'core/str', 'core/notification'],
    function($, ModalFactory, ModalEvents, Str, Notification) {

    /**
     * Initialize the apply modal functionality.
     *
     * @param {number} vacancyId The vacancy ID.
     * @param {string} vacancyTitle The vacancy title.
     * @param {string} loginUrl The login URL.
     * @param {string} signupUrl The signup URL.
     */
    var init = function(vacancyId, vacancyTitle, loginUrl, signupUrl) {
        // Find all apply buttons.
        $('.jobboard-apply-btn').on('click', function(e) {
            e.preventDefault();

            var btnVacancyId = $(this).data('vacancyid') || vacancyId;
            var btnVacancyTitle = $(this).data('vacancytitle') || vacancyTitle;

            showApplyModal(btnVacancyId, btnVacancyTitle, loginUrl, signupUrl);
        });
    };

    /**
     * Show the apply modal.
     *
     * @param {number} vacancyId The vacancy ID.
     * @param {string} vacancyTitle The vacancy title.
     * @param {string} loginUrl The login URL.
     * @param {string} signupUrl The signup URL.
     */
    var showApplyModal = function(vacancyId, vacancyTitle, loginUrl, signupUrl) {
        // Get all the strings we need.
        var stringKeys = [
            {key: 'apply_modal_title', component: 'local_jobboard'},
            {key: 'apply_modal_question', component: 'local_jobboard'},
            {key: 'apply_modal_registered', component: 'local_jobboard'},
            {key: 'apply_modal_not_registered', component: 'local_jobboard'},
            {key: 'apply_modal_registered_desc', component: 'local_jobboard'},
            {key: 'apply_modal_not_registered_desc', component: 'local_jobboard'},
            {key: 'cancel', component: 'core'}
        ];

        Str.get_strings(stringKeys).then(function(strings) {
            var modalTitle = strings[0];
            var modalQuestion = strings[1];
            var registeredBtn = strings[2];
            var notRegisteredBtn = strings[3];
            var registeredDesc = strings[4];
            var notRegisteredDesc = strings[5];
            var cancelBtn = strings[6];

            // Build modal body HTML.
            var bodyHtml = '<div class="apply-modal-content text-center">';
            bodyHtml += '<p class="lead mb-4">' + modalQuestion + '</p>';

            // Registered option.
            bodyHtml += '<div class="row">';
            bodyHtml += '<div class="col-md-6 mb-3">';
            bodyHtml += '<div class="card h-100">';
            bodyHtml += '<div class="card-body">';
            bodyHtml += '<i class="fa fa-user-check fa-3x text-success mb-3"></i>';
            bodyHtml += '<h5 class="card-title">' + registeredBtn + '</h5>';
            bodyHtml += '<p class="card-text small text-muted">' + registeredDesc + '</p>';
            bodyHtml += '<a href="' + loginUrl + '&vacancyid=' + vacancyId + '" class="btn btn-success btn-block">';
            bodyHtml += '<i class="fa fa-sign-in me-2"></i>' + registeredBtn + '</a>';
            bodyHtml += '</div></div></div>';

            // Not registered option.
            bodyHtml += '<div class="col-md-6 mb-3">';
            bodyHtml += '<div class="card h-100">';
            bodyHtml += '<div class="card-body">';
            bodyHtml += '<i class="fa fa-user-plus fa-3x text-primary mb-3"></i>';
            bodyHtml += '<h5 class="card-title">' + notRegisteredBtn + '</h5>';
            bodyHtml += '<p class="card-text small text-muted">' + notRegisteredDesc + '</p>';
            bodyHtml += '<a href="' + signupUrl + '?vacancyid=' + vacancyId + '" class="btn btn-primary btn-block">';
            bodyHtml += '<i class="fa fa-user-plus me-2"></i>' + notRegisteredBtn + '</a>';
            bodyHtml += '</div></div></div>';
            bodyHtml += '</div></div>';

            return ModalFactory.create({
                type: ModalFactory.types.DEFAULT,
                title: modalTitle,
                body: bodyHtml,
                large: true
            });
        }).then(function(modal) {
            modal.getRoot().on(ModalEvents.hidden, function() {
                modal.destroy();
            });
            modal.show();
            return modal;
        }).catch(Notification.exception);
    };

    return {
        init: init,
        showApplyModal: showApplyModal
    };
});

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
 * API Token management module.
 *
 * @module     local_jobboard/token_manager
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification', 'core/str', 'core/modal_factory', 'core/modal_events'],
    function($, Ajax, Notification, Str, ModalFactory, ModalEvents) {

    /**
     * Token Manager class.
     */
    var TokenManager = function() {
        this.registerEventListeners();
    };

    /**
     * Register event listeners.
     */
    TokenManager.prototype.registerEventListeners = function() {
        $(document).on('click', '.token-action', this.handleTokenAction.bind(this));
        $(document).on('click', '.copy-token', this.copyTokenToClipboard.bind(this));
    };

    /**
     * Handle token action button clicks.
     *
     * @param {Event} e Click event
     */
    TokenManager.prototype.handleTokenAction = function(e) {
        e.preventDefault();
        var button = $(e.currentTarget);
        var action = button.data('action');
        var tokenId = button.data('token-id');

        switch (action) {
            case 'revoke':
                this.confirmAndRevoke(tokenId, button);
                break;
            case 'enable':
                this.enableToken(tokenId, button);
                break;
            case 'delete':
                this.confirmAndDelete(tokenId, button);
                break;
        }
    };

    /**
     * Confirm and revoke a token.
     *
     * @param {number} tokenId Token ID
     * @param {jQuery} button Button element
     */
    TokenManager.prototype.confirmAndRevoke = function(tokenId, button) {
        var self = this;
        var loadedStrings = null;
        Str.get_strings([
            {key: 'api:token:confirmrevoke', component: 'local_jobboard'},
            {key: 'api:token:revoke', component: 'local_jobboard'},
            {key: 'confirm', component: 'local_jobboard'},
            {key: 'cancel', component: 'local_jobboard'}
        ]).then(function(strings) {
            loadedStrings = strings;
            return ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: strings[1],
                body: strings[0]
            });
        }).then(function(modal) {
            modal.setSaveButtonText(loadedStrings[2]);
            modal.getRoot().on(ModalEvents.save, function() {
                self.revokeToken(tokenId, button);
            });
            modal.show();
            return modal;
        }).catch(Notification.exception);
    };

    /**
     * Revoke a token via AJAX.
     *
     * @param {number} tokenId Token ID
     * @param {jQuery} button Button element
     */
    TokenManager.prototype.revokeToken = function(tokenId, button) {
        var row = button.closest('tr');

        Ajax.call([{
            methodname: 'local_jobboard_revoke_token',
            args: {tokenid: tokenId}
        }])[0].then(function(response) {
            if (response.success) {
                // Update the row to show disabled status.
                row.find('.badge').removeClass('badge-success').addClass('badge-secondary')
                   .text(response.statuslabel);
                button.data('action', 'enable')
                      .removeClass('btn-outline-warning').addClass('btn-outline-success')
                      .attr('title', response.enablelabel)
                      .html('<i class="icon fa fa-eye"></i>');
                Notification.addNotification({
                    message: response.message,
                    type: 'success'
                });
            }
            return response;
        }).catch(Notification.exception);
    };

    /**
     * Enable a token via AJAX.
     *
     * @param {number} tokenId Token ID
     * @param {jQuery} button Button element
     */
    TokenManager.prototype.enableToken = function(tokenId, button) {
        var row = button.closest('tr');

        Ajax.call([{
            methodname: 'local_jobboard_enable_token',
            args: {tokenid: tokenId}
        }])[0].then(function(response) {
            if (response.success) {
                row.find('.badge').removeClass('badge-secondary').addClass('badge-success')
                   .text(response.statuslabel);
                button.data('action', 'revoke')
                      .removeClass('btn-outline-success').addClass('btn-outline-warning')
                      .attr('title', response.revokelabel)
                      .html('<i class="icon fa fa-eye-slash"></i>');
                Notification.addNotification({
                    message: response.message,
                    type: 'success'
                });
            }
            return response;
        }).catch(Notification.exception);
    };

    /**
     * Confirm and delete a token.
     *
     * @param {number} tokenId Token ID
     * @param {jQuery} button Button element
     */
    TokenManager.prototype.confirmAndDelete = function(tokenId, button) {
        var self = this;
        var loadedStrings = null;
        Str.get_strings([
            {key: 'api:token:confirmdelete', component: 'local_jobboard'},
            {key: 'api:token:delete', component: 'local_jobboard'},
            {key: 'confirm', component: 'local_jobboard'},
            {key: 'cancel', component: 'local_jobboard'}
        ]).then(function(strings) {
            loadedStrings = strings;
            return ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: strings[1],
                body: strings[0]
            });
        }).then(function(modal) {
            modal.setSaveButtonText(loadedStrings[2]);
            modal.getRoot().on(ModalEvents.save, function() {
                self.deleteToken(tokenId, button);
            });
            modal.show();
            return modal;
        }).catch(Notification.exception);
    };

    /**
     * Delete a token via AJAX.
     *
     * @param {number} tokenId Token ID
     * @param {jQuery} button Button element
     */
    TokenManager.prototype.deleteToken = function(tokenId, button) {
        var row = button.closest('tr');

        Ajax.call([{
            methodname: 'local_jobboard_delete_token',
            args: {tokenid: tokenId}
        }])[0].then(function(response) {
            if (response.success) {
                row.fadeOut(400, function() {
                    $(this).remove();
                });
                Notification.addNotification({
                    message: response.message,
                    type: 'success'
                });
            }
            return response;
        }).catch(Notification.exception);
    };

    /**
     * Copy token to clipboard.
     *
     * @param {Event} e Click event
     */
    TokenManager.prototype.copyTokenToClipboard = function(e) {
        e.preventDefault();
        var button = $(e.currentTarget);
        var token = button.data('token');

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(token).then(function() {
                Str.get_string('api:token:copied', 'local_jobboard').then(function(str) {
                    Notification.addNotification({
                        message: str,
                        type: 'success'
                    });
                    return str;
                }).catch(Notification.exception);
            }).catch(function() {
                // Fallback for older browsers.
                this.fallbackCopyToClipboard(token);
            }.bind(this));
        } else {
            this.fallbackCopyToClipboard(token);
        }
    };

    /**
     * Fallback copy to clipboard for older browsers.
     *
     * @param {string} text Text to copy
     */
    TokenManager.prototype.fallbackCopyToClipboard = function(text) {
        var textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            Str.get_string('api:token:copied', 'local_jobboard').then(function(str) {
                Notification.addNotification({
                    message: str,
                    type: 'success'
                });
                return str;
            }).catch(Notification.exception);
        } catch (err) {
            // Ignore errors.
        }
        document.body.removeChild(textArea);
    };

    return {
        /**
         * Initialize the token manager.
         */
        init: function() {
            return new TokenManager();
        }
    };
});

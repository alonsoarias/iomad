<?php
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
 * API token management page for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_jobboard\api_token;

admin_externalpage_setup('local_jobboard_tokens');

$action = optional_param('action', '', PARAM_ALPHA);
$tokenid = optional_param('id', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

$context = context_system::instance();
require_capability('local/jobboard:manageapitokens', $context);

$pageurl = new moodle_url('/local/jobboard/admin/tokens.php');

$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_title(get_string('apitokens', 'local_jobboard'));
$PAGE->set_heading(get_string('apitokens', 'local_jobboard'));

// Handle actions.
if ($action === 'create' && confirm_sesskey()) {
    // Process form submission for creating token.
    $mform = new \local_jobboard\forms\api_token_form();

    if ($mform->is_cancelled()) {
        redirect($pageurl);
    }

    if ($data = $mform->get_data()) {
        $permissions = [];
        foreach (api_token::PERMISSIONS as $perm) {
            $fieldname = 'perm_' . $perm;
            if (!empty($data->$fieldname)) {
                $permissions[] = $perm;
            }
        }

        $ipwhitelist = [];
        if (!empty($data->ipwhitelist)) {
            $ipwhitelist = array_filter(array_map('trim', explode("\n", $data->ipwhitelist)));
        }

        $result = api_token::create(
            $data->userid,
            $data->description,
            $permissions,
            $ipwhitelist,
            $data->validfrom ?: null,
            $data->validuntil ?: null
        );

        // Store the raw token temporarily for display.
        $SESSION->jobboard_new_token = $result['token'];

        redirect(new moodle_url($pageurl, ['action' => 'created', 'id' => $result['object']->id]));
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('api:token:create', 'local_jobboard'));
    $mform->display();
    echo $OUTPUT->footer();
    exit;
}

if ($action === 'created' && $tokenid) {
    // Show the newly created token.
    $token = api_token::get($tokenid);

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('api:token:created', 'local_jobboard'));

    // Display the raw token (only shown once).
    if (!empty($SESSION->jobboard_new_token)) {
        $rawtoken = $SESSION->jobboard_new_token;
        unset($SESSION->jobboard_new_token);

        echo $OUTPUT->notification(
            get_string('api:token:copywarning', 'local_jobboard'),
            'warning'
        );

        echo html_writer::start_div('alert alert-success');
        echo html_writer::tag('h4', get_string('api:token:yourtoken', 'local_jobboard'));
        echo html_writer::tag('code', $rawtoken, ['class' => 'user-select-all', 'style' => 'word-break: break-all;']);
        echo html_writer::end_div();

        echo html_writer::tag('p', get_string('api:token:usage', 'local_jobboard'));
        echo html_writer::tag('pre', 'Authorization: Bearer ' . $rawtoken);
    }

    echo $OUTPUT->single_button($pageurl, get_string('back'));
    echo $OUTPUT->footer();
    exit;
}

if ($action === 'revoke' && $tokenid) {
    $token = api_token::get($tokenid);

    if (!$token) {
        throw new moodle_exception('error:tokennotfound', 'local_jobboard');
    }

    if ($confirm && confirm_sesskey()) {
        $token->revoke();
        redirect($pageurl, get_string('api:token:revoked', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('api:token:revoke', 'local_jobboard'));
    echo $OUTPUT->confirm(
        get_string('api:token:revokeconfirm', 'local_jobboard', $token->description),
        new moodle_url($pageurl, ['action' => 'revoke', 'id' => $tokenid, 'confirm' => 1]),
        $pageurl
    );
    echo $OUTPUT->footer();
    exit;
}

if ($action === 'delete' && $tokenid) {
    $token = api_token::get($tokenid);

    if (!$token) {
        throw new moodle_exception('error:tokennotfound', 'local_jobboard');
    }

    if ($confirm && confirm_sesskey()) {
        $token->delete();
        redirect($pageurl, get_string('api:token:deleted', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('api:token:delete', 'local_jobboard'));
    echo $OUTPUT->confirm(
        get_string('api:token:deleteconfirm', 'local_jobboard', $token->description),
        new moodle_url($pageurl, ['action' => 'delete', 'id' => $tokenid, 'confirm' => 1]),
        $pageurl
    );
    echo $OUTPUT->footer();
    exit;
}

// List all tokens.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('apitokens', 'local_jobboard'));

// Add create button.
echo $OUTPUT->single_button(
    new moodle_url($pageurl, ['action' => 'create', 'sesskey' => sesskey()]),
    get_string('api:token:create', 'local_jobboard'),
    'get',
    ['class' => 'mb-3']
);

// Get all tokens.
$tokens = api_token::get_all();

if (empty($tokens)) {
    echo $OUTPUT->notification(get_string('api:token:none', 'local_jobboard'), 'info');
} else {
    $table = new html_table();
    $table->head = [
        get_string('description'),
        get_string('user'),
        get_string('permissions', 'local_jobboard'),
        get_string('status'),
        get_string('lastused', 'local_jobboard'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'generaltable';

    foreach ($tokens as $token) {
        $user = $token->get_user();
        $username = $user ? fullname($user) : get_string('unknownuser');

        $statusclass = $token->is_valid() ? 'badge-success' : 'badge-secondary';
        $statusbadge = html_writer::tag('span', $token->get_status_display(), ['class' => 'badge ' . $statusclass]);

        $lastused = $token->lastused ? userdate($token->lastused) : get_string('never');

        $actions = [];
        if ($token->enabled) {
            $actions[] = html_writer::link(
                new moodle_url($pageurl, ['action' => 'revoke', 'id' => $token->id, 'sesskey' => sesskey()]),
                get_string('revoke', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-warning']
            );
        }
        $actions[] = html_writer::link(
            new moodle_url($pageurl, ['action' => 'delete', 'id' => $token->id, 'sesskey' => sesskey()]),
            get_string('delete'),
            ['class' => 'btn btn-sm btn-danger']
        );

        $table->data[] = [
            s($token->description),
            $username,
            $token->get_permissions_display(),
            $statusbadge,
            $lastused,
            implode(' ', $actions),
        ];
    }

    echo html_writer::table($table);
}

// API information.
echo $OUTPUT->heading(get_string('api:info', 'local_jobboard'), 3);
echo html_writer::start_tag('ul');
echo html_writer::tag('li', get_string('api:baseurl', 'local_jobboard') . ': ' .
    html_writer::tag('code', $CFG->wwwroot . '/local/jobboard/api/v1/'));
echo html_writer::tag('li', get_string('api:ratelimit', 'local_jobboard') . ': ' . api_token::RATE_LIMIT . ' ' .
    get_string('api:requestsperhour', 'local_jobboard'));
echo html_writer::tag('li', get_string('api:authheader', 'local_jobboard') . ': ' .
    html_writer::tag('code', 'Authorization: Bearer &lt;token&gt;'));
echo html_writer::end_tag('ul');

echo $OUTPUT->footer();

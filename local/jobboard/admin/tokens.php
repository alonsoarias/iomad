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
 * Modern redesign with consistent UX pattern using ui_helper.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_jobboard\api_token;
use local_jobboard\output\ui_helper;

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
$PAGE->requires->css('/local/jobboard/styles.css');

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

    echo html_writer::start_div('local-jobboard-tokens');

    // Back button.
    echo ui_helper::page_header(get_string('api:token:create', 'local_jobboard'), [], [
        [
            'url' => $pageurl,
            'label' => get_string('back'),
            'icon' => 'arrow-left',
            'class' => 'btn btn-outline-secondary',
        ],
    ]);

    // Form card.
    echo html_writer::start_div('card shadow-sm');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5', '<i class="fa fa-key text-warning mr-2"></i>' .
        get_string('api:token:create', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    $mform->display();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div();

    echo $OUTPUT->footer();
    exit;
}

if ($action === 'created' && $tokenid) {
    // Show the newly created token.
    $token = api_token::get($tokenid);

    echo $OUTPUT->header();

    echo html_writer::start_div('local-jobboard-tokens');

    // Display the raw token (only shown once).
    if (!empty($SESSION->jobboard_new_token)) {
        $rawtoken = $SESSION->jobboard_new_token;
        unset($SESSION->jobboard_new_token);

        echo html_writer::start_div('card shadow-sm border-warning mb-4');
        echo html_writer::start_div('card-header bg-warning');
        echo html_writer::tag('h5', '<i class="fa fa-exclamation-triangle mr-2"></i>' .
            get_string('api:token:created', 'local_jobboard'), ['class' => 'mb-0']);
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');

        echo html_writer::div(
            '<i class="fa fa-info-circle mr-2"></i>' . get_string('api:token:copywarning', 'local_jobboard'),
            'alert alert-warning'
        );

        echo html_writer::start_div('alert alert-success');
        echo html_writer::tag('h5', '<i class="fa fa-key mr-2"></i>' .
            get_string('api:token:yourtoken', 'local_jobboard'), ['class' => 'alert-heading']);
        echo html_writer::tag('code', $rawtoken, [
            'class' => 'user-select-all d-block p-3 bg-light rounded',
            'style' => 'word-break: break-all; font-size: 0.9rem;',
        ]);
        echo html_writer::end_div();

        echo html_writer::tag('h6', get_string('api:token:usage', 'local_jobboard'), ['class' => 'mt-4']);
        echo html_writer::tag('pre', 'Authorization: Bearer ' . $rawtoken, [
            'class' => 'bg-dark text-light p-3 rounded',
        ]);

        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    echo html_writer::link($pageurl, '<i class="fa fa-arrow-left mr-2"></i>' . get_string('back'),
        ['class' => 'btn btn-primary']);

    echo html_writer::end_div();

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

    echo html_writer::start_div('local-jobboard-tokens');

    echo html_writer::start_div('card shadow-sm border-warning');
    echo html_writer::start_div('card-header bg-warning');
    echo html_writer::tag('h5', '<i class="fa fa-ban mr-2"></i>' .
        get_string('api:token:revoke', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    echo html_writer::tag('p', get_string('api:token:revokeconfirm', 'local_jobboard', $token->description));
    echo html_writer::tag('p', '<strong>' . get_string('description') . ':</strong> ' . s($token->description),
        ['class' => 'text-muted']);

    echo html_writer::start_div('mt-4');
    echo html_writer::link(
        new moodle_url($pageurl, ['action' => 'revoke', 'id' => $tokenid, 'confirm' => 1, 'sesskey' => sesskey()]),
        '<i class="fa fa-ban mr-2"></i>' . get_string('revoke', 'local_jobboard'),
        ['class' => 'btn btn-warning mr-2']
    );
    echo html_writer::link($pageurl, '<i class="fa fa-times mr-2"></i>' . get_string('cancel'),
        ['class' => 'btn btn-secondary']);
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div();

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

    echo html_writer::start_div('local-jobboard-tokens');

    echo html_writer::start_div('card shadow-sm border-danger');
    echo html_writer::start_div('card-header bg-danger text-white');
    echo html_writer::tag('h5', '<i class="fa fa-trash mr-2"></i>' .
        get_string('api:token:delete', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    echo html_writer::div(
        '<i class="fa fa-exclamation-triangle mr-2"></i>' . get_string('api:token:deleteconfirm', 'local_jobboard', $token->description),
        'alert alert-danger'
    );
    echo html_writer::tag('p', '<strong>' . get_string('description') . ':</strong> ' . s($token->description),
        ['class' => 'text-muted']);

    echo html_writer::start_div('mt-4');
    echo html_writer::link(
        new moodle_url($pageurl, ['action' => 'delete', 'id' => $tokenid, 'confirm' => 1, 'sesskey' => sesskey()]),
        '<i class="fa fa-trash mr-2"></i>' . get_string('delete'),
        ['class' => 'btn btn-danger mr-2']
    );
    echo html_writer::link($pageurl, '<i class="fa fa-times mr-2"></i>' . get_string('cancel'),
        ['class' => 'btn btn-secondary']);
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div();

    echo $OUTPUT->footer();
    exit;
}

// List all tokens.
$tokens = api_token::get_all();

// Calculate stats.
$totalTokens = count($tokens);
$activeTokens = 0;
$revokedTokens = 0;
$usedToday = 0;
$today = strtotime('today');
foreach ($tokens as $token) {
    if ($token->is_valid()) {
        $activeTokens++;
    }
    if (!$token->enabled) {
        $revokedTokens++;
    }
    if ($token->lastused && $token->lastused >= $today) {
        $usedToday++;
    }
}

echo $OUTPUT->header();

echo html_writer::start_div('local-jobboard-tokens');

// ============================================================================
// PAGE HEADER WITH ACTIONS
// ============================================================================
echo ui_helper::page_header(
    get_string('apitokens', 'local_jobboard'),
    [],
    [
        [
            'url' => new moodle_url($pageurl, ['action' => 'create', 'sesskey' => sesskey()]),
            'label' => get_string('api:token:create', 'local_jobboard'),
            'icon' => 'plus',
            'class' => 'btn btn-primary',
        ],
        [
            'url' => new moodle_url('/local/jobboard/index.php'),
            'label' => get_string('dashboard', 'local_jobboard'),
            'icon' => 'tachometer-alt',
            'class' => 'btn btn-outline-secondary',
        ],
    ]
);

// ============================================================================
// STATS CARDS
// ============================================================================
echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card((string)$totalTokens, get_string('totaltokens', 'local_jobboard'), 'primary', 'key');
echo ui_helper::stat_card((string)$activeTokens, get_string('activetokens', 'local_jobboard'), 'success', 'check-circle');
echo ui_helper::stat_card((string)$revokedTokens, get_string('revokedtokens', 'local_jobboard'), 'secondary', 'ban');
echo ui_helper::stat_card((string)$usedToday, get_string('usedtoday', 'local_jobboard'), 'info', 'clock');
echo html_writer::end_div();

// ============================================================================
// API INFORMATION CARD
// ============================================================================
echo html_writer::start_div('card shadow-sm mb-4 border-info');
echo html_writer::start_div('card-header bg-info text-white');
echo html_writer::tag('h5', '<i class="fa fa-info-circle mr-2"></i>' .
    get_string('api:info', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

echo html_writer::start_div('row');

echo html_writer::start_div('col-md-4 mb-3 mb-md-0');
echo html_writer::tag('h6', '<i class="fa fa-globe mr-2"></i>' . get_string('api:baseurl', 'local_jobboard'));
echo html_writer::tag('code', $CFG->wwwroot . '/local/jobboard/api/v1/', ['class' => 'd-block']);
echo html_writer::end_div();

echo html_writer::start_div('col-md-4 mb-3 mb-md-0');
echo html_writer::tag('h6', '<i class="fa fa-tachometer-alt mr-2"></i>' . get_string('api:ratelimit', 'local_jobboard'));
echo html_writer::tag('span', api_token::RATE_LIMIT . ' ' . get_string('api:requestsperhour', 'local_jobboard'),
    ['class' => 'badge badge-secondary']);
echo html_writer::end_div();

echo html_writer::start_div('col-md-4');
echo html_writer::tag('h6', '<i class="fa fa-lock mr-2"></i>' . get_string('api:authheader', 'local_jobboard'));
echo html_writer::tag('code', 'Authorization: Bearer &lt;token&gt;', ['class' => 'd-block']);
echo html_writer::end_div();

echo html_writer::end_div();

echo html_writer::end_div();
echo html_writer::end_div();

// ============================================================================
// TOKENS TABLE
// ============================================================================
if (empty($tokens)) {
    echo ui_helper::empty_state(
        get_string('api:token:none', 'local_jobboard'),
        'key',
        [
            'url' => new moodle_url($pageurl, ['action' => 'create', 'sesskey' => sesskey()]),
            'label' => get_string('api:token:create', 'local_jobboard'),
            'class' => 'btn btn-primary',
        ]
    );
} else {
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5', '<i class="fa fa-list text-primary mr-2"></i>' .
        get_string('tokenslist', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::tag('span', $totalTokens . ' ' . get_string('items', 'local_jobboard'),
        ['class' => 'badge badge-secondary']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body p-0');

    $headers = [
        get_string('description'),
        get_string('user'),
        get_string('permissions', 'local_jobboard'),
        get_string('status'),
        get_string('lastused', 'local_jobboard'),
        get_string('actions'),
    ];

    $rows = [];
    foreach ($tokens as $token) {
        $user = $token->get_user();
        $username = $user ? fullname($user) : get_string('unknownuser');

        $statusclass = $token->is_valid() ? 'badge-success' : 'badge-secondary';
        $statusbadge = html_writer::tag('span', $token->get_status_display(), ['class' => 'badge ' . $statusclass]);

        $lastused = $token->lastused
            ? html_writer::tag('span', userdate($token->lastused, '%Y-%m-%d %H:%M'), ['class' => 'text-muted'])
            : html_writer::tag('span', get_string('never'), ['class' => 'text-muted font-italic']);

        // Permissions badges.
        $perms = $token->get_permissions_display();
        $permsBadges = '';
        if ($perms) {
            $permsList = explode(', ', $perms);
            foreach ($permsList as $perm) {
                $permsBadges .= html_writer::tag('span', $perm, ['class' => 'badge badge-info mr-1 mb-1']);
            }
        }

        $actions = [];
        if ($token->enabled) {
            $actions[] = html_writer::link(
                new moodle_url($pageurl, ['action' => 'revoke', 'id' => $token->id, 'sesskey' => sesskey()]),
                '<i class="fa fa-ban"></i>',
                ['class' => 'btn btn-sm btn-outline-warning', 'title' => get_string('revoke', 'local_jobboard')]
            );
        }
        $actions[] = html_writer::link(
            new moodle_url($pageurl, ['action' => 'delete', 'id' => $token->id, 'sesskey' => sesskey()]),
            '<i class="fa fa-trash"></i>',
            ['class' => 'btn btn-sm btn-outline-danger', 'title' => get_string('delete')]
        );

        $actionshtml = html_writer::start_div('btn-group btn-group-sm', ['role' => 'group']);
        $actionshtml .= implode('', $actions);
        $actionshtml .= html_writer::end_div();

        $rows[] = [
            html_writer::tag('strong', s($token->description)),
            $username,
            $permsBadges ?: '<span class="text-muted">-</span>',
            $statusbadge,
            $lastused,
            $actionshtml,
        ];
    }

    echo ui_helper::data_table($headers, $rows, ['class' => 'mb-0']);

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card
}

// ============================================================================
// NAVIGATION FOOTER
// ============================================================================
echo html_writer::start_div('card mt-4 bg-light');
echo html_writer::start_div('card-body d-flex flex-wrap align-items-center justify-content-center');

echo html_writer::link(
    new moodle_url('/local/jobboard/index.php'),
    '<i class="fa fa-tachometer-alt mr-2"></i>' . get_string('dashboard', 'local_jobboard'),
    ['class' => 'btn btn-outline-secondary m-1']
);

echo html_writer::link(
    new moodle_url('/local/jobboard/admin/doctypes.php'),
    '<i class="fa fa-folder mr-2"></i>' . get_string('managedoctypes', 'local_jobboard'),
    ['class' => 'btn btn-outline-primary m-1']
);

echo html_writer::link(
    new moodle_url('/local/jobboard/admin/templates.php'),
    '<i class="fa fa-envelope mr-2"></i>' . get_string('emailtemplates', 'local_jobboard'),
    ['class' => 'btn btn-outline-info m-1']
);

echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-tokens

echo $OUTPUT->footer();

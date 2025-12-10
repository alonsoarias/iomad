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
 * Role management page for local_jobboard.
 *
 * Allows administrators to assign users to the plugin's custom roles:
 * - jobboard_reviewer: Document reviewers
 * - jobboard_coordinator: Selection coordinators
 * - jobboard_committee: Selection committee members
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

use local_jobboard\output\ui_helper;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:configure', $context);

// Parameters.
$action = optional_param('action', '', PARAM_ALPHA);
$roleshortname = optional_param('role', '', PARAM_ALPHANUMEXT);
$userid = optional_param('userid', 0, PARAM_INT);

// Page setup.
$PAGE->set_url(new moodle_url('/local/jobboard/admin/roles.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('manageroles', 'local_jobboard'));
$PAGE->set_heading(get_string('manageroles', 'local_jobboard'));
$PAGE->set_pagelayout('admin');
$PAGE->requires->css('/local/jobboard/styles.css');

// Define the plugin roles.
$pluginroles = [
    'jobboard_reviewer' => [
        'name' => get_string('role_reviewer', 'local_jobboard'),
        'description' => get_string('role_reviewer_desc', 'local_jobboard'),
        'icon' => 'clipboard-check',
        'color' => 'warning',
    ],
    'jobboard_coordinator' => [
        'name' => get_string('role_coordinator', 'local_jobboard'),
        'description' => get_string('role_coordinator_desc', 'local_jobboard'),
        'icon' => 'user-tie',
        'color' => 'primary',
    ],
    'jobboard_committee' => [
        'name' => get_string('role_committee', 'local_jobboard'),
        'description' => get_string('role_committee_desc', 'local_jobboard'),
        'icon' => 'users',
        'color' => 'success',
    ],
];

// Handle actions.
if ($action === 'assign' && confirm_sesskey()) {
    $userids = required_param_array('userids', PARAM_INT);

    if (!empty($roleshortname) && isset($pluginroles[$roleshortname])) {
        $role = $DB->get_record('role', ['shortname' => $roleshortname]);
        if ($role) {
            $assigned = 0;
            foreach ($userids as $uid) {
                if (!$DB->record_exists('role_assignments', [
                    'roleid' => $role->id,
                    'contextid' => $context->id,
                    'userid' => $uid,
                ])) {
                    role_assign($role->id, $uid, $context->id);
                    $assigned++;
                }
            }
            if ($assigned > 0) {
                \core\notification::success(get_string('usersassigned', 'local_jobboard', $assigned));
            }
        }
    }
    redirect(new moodle_url('/local/jobboard/admin/roles.php', ['role' => $roleshortname]));
}

if ($action === 'unassign' && confirm_sesskey() && $userid) {
    if (!empty($roleshortname) && isset($pluginroles[$roleshortname])) {
        $role = $DB->get_record('role', ['shortname' => $roleshortname]);
        if ($role) {
            role_unassign($role->id, $userid, $context->id);
            \core\notification::success(get_string('userunassigned', 'local_jobboard'));
        }
    }
    redirect(new moodle_url('/local/jobboard/admin/roles.php', ['role' => $roleshortname]));
}

// Get role statistics.
$rolestats = [];
foreach ($pluginroles as $shortname => $roledef) {
    $role = $DB->get_record('role', ['shortname' => $shortname]);
    if ($role) {
        $rolestats[$shortname] = $DB->count_records('role_assignments', [
            'roleid' => $role->id,
            'contextid' => $context->id,
        ]);
    } else {
        $rolestats[$shortname] = 0;
    }
}

echo $OUTPUT->header();

echo html_writer::start_div('local-jobboard-roles');

// ============================================================================
// PAGE HEADER WITH ACTIONS
// ============================================================================
echo ui_helper::page_header(
    get_string('manageroles', 'local_jobboard'),
    [],
    [
        [
            'url' => new moodle_url('/local/jobboard/index.php'),
            'label' => get_string('dashboard', 'local_jobboard'),
            'icon' => 'tachometer-alt',
            'class' => 'btn btn-outline-secondary',
        ],
    ]
);

// ============================================================================
// STATISTICS CARDS
// ============================================================================
echo html_writer::start_div('row mb-4');
$totalusers = array_sum($rolestats);
echo ui_helper::stat_card((string) $totalusers, get_string('totalassignedusers', 'local_jobboard'), 'info', 'users');
foreach ($pluginroles as $shortname => $roledef) {
    echo ui_helper::stat_card(
        (string) ($rolestats[$shortname] ?? 0),
        $roledef['name'],
        $roledef['color'],
        $roledef['icon'],
        new moodle_url('/local/jobboard/admin/roles.php', ['role' => $shortname])
    );
}
echo html_writer::end_div();

// ============================================================================
// ROLE CARDS
// ============================================================================
if (empty($roleshortname)) {
    // Show role selection cards.
    echo html_writer::tag('h4',
        '<i class="fa fa-user-tag mr-2"></i>' . get_string('selectroletoassign', 'local_jobboard'),
        ['class' => 'mb-3']
    );
    echo html_writer::start_div('row');

    foreach ($pluginroles as $shortname => $roledef) {
        $role = $DB->get_record('role', ['shortname' => $shortname]);
        $usercount = $rolestats[$shortname] ?? 0;

        echo html_writer::start_div('col-lg-4 col-md-6 mb-4');
        echo html_writer::start_div('card h-100 shadow-sm border-0');
        echo html_writer::start_div('card-body');

        // Header.
        echo html_writer::start_div('d-flex align-items-center mb-3');
        echo html_writer::tag('div',
            html_writer::tag('i', '', ['class' => "fa fa-{$roledef['icon']} fa-lg text-{$roledef['color']}"]),
            ['class' => "jb-icon-circle bg-{$roledef['color']}-light mr-3"]);
        echo html_writer::start_div();
        echo html_writer::tag('h5', $roledef['name'], ['class' => 'mb-0']);
        echo html_writer::tag('small',
            $usercount . ' ' . get_string('usersassignedcount', 'local_jobboard'),
            ['class' => 'text-muted']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();

        // Description.
        echo html_writer::tag('p', $roledef['description'], ['class' => 'text-muted small mb-3']);

        // Capabilities preview.
        $capspreview = local_jobboard_get_role_capabilities_preview($shortname);
        if (!empty($capspreview)) {
            echo html_writer::start_div('mb-3');
            echo html_writer::tag('small', get_string('capabilities', 'local_jobboard') . ':', ['class' => 'd-block text-muted mb-1']);
            foreach ($capspreview as $cap) {
                echo html_writer::tag('span', $cap, ['class' => 'badge badge-light mr-1 mb-1']);
            }
            echo html_writer::end_div();
        }

        // Actions.
        echo html_writer::start_div('d-flex');
        if ($role) {
            echo html_writer::link(
                new moodle_url('/local/jobboard/admin/roles.php', ['role' => $shortname]),
                '<i class="fa fa-users-cog mr-1"></i>' . get_string('manageusers', 'local_jobboard'),
                ['class' => "btn btn-{$roledef['color']} btn-sm"]
            );
        } else {
            echo html_writer::tag('span',
                '<i class="fa fa-exclamation-triangle mr-1"></i>' . get_string('rolenotcreated', 'local_jobboard'),
                ['class' => 'text-danger small']
            );
        }
        echo html_writer::end_div();

        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    echo html_writer::end_div();

} else {
    // Show users for selected role.
    $roledef = $pluginroles[$roleshortname] ?? null;
    $role = $DB->get_record('role', ['shortname' => $roleshortname]);

    if (!$roledef || !$role) {
        echo $OUTPUT->notification(get_string('invalidrole', 'local_jobboard'), 'error');
    } else {
        // Breadcrumb back.
        echo html_writer::link(
            new moodle_url('/local/jobboard/admin/roles.php'),
            '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtorolelist', 'local_jobboard'),
            ['class' => 'btn btn-outline-secondary btn-sm mb-3']
        );

        // Role header.
        echo html_writer::start_div('card shadow-sm mb-4');
        echo html_writer::start_div("card-header bg-{$roledef['color']} text-white");
        echo html_writer::start_div('d-flex justify-content-between align-items-center');
        echo html_writer::tag('h5',
            "<i class=\"fa fa-{$roledef['icon']} mr-2\"></i>{$roledef['name']}",
            ['class' => 'mb-0']
        );
        echo html_writer::tag('span',
            ($rolestats[$roleshortname] ?? 0) . ' ' . get_string('users'),
            ['class' => 'badge badge-light']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');
        echo html_writer::tag('p', $roledef['description'], ['class' => 'text-muted mb-0']);
        echo html_writer::end_div();
        echo html_writer::end_div();

        // Assigned users.
        $sql = "SELECT u.id, u.firstname, u.lastname, u.email, ra.timemodified as assigneddate
                  FROM {role_assignments} ra
                  JOIN {user} u ON u.id = ra.userid
                 WHERE ra.roleid = :roleid AND ra.contextid = :contextid
                 ORDER BY u.lastname, u.firstname";
        $assignedusers = $DB->get_records_sql($sql, [
            'roleid' => $role->id,
            'contextid' => $context->id,
        ]);

        echo html_writer::start_div('row');

        // Left column: Current users.
        echo html_writer::start_div('col-lg-7 mb-4');
        echo html_writer::start_div('card shadow-sm h-100');
        echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center');
        echo html_writer::tag('h6',
            '<i class="fa fa-users mr-2"></i>' . get_string('assignedusers', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::tag('span', count($assignedusers) . ' ' . get_string('users'), ['class' => 'badge badge-primary']);
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');

        if (empty($assignedusers)) {
            echo ui_helper::empty_state(get_string('nousersassigned', 'local_jobboard'), 'user-plus');
        } else {
            $headers = [
                get_string('user'),
                get_string('email'),
                get_string('assigned', 'local_jobboard'),
                get_string('actions'),
            ];

            $rows = [];
            foreach ($assignedusers as $user) {
                $userinfo = html_writer::tag('strong', fullname($user)) .
                    html_writer::tag('br') .
                    html_writer::tag('small', '#' . $user->id, ['class' => 'text-muted']);

                $unassignurl = new moodle_url('/local/jobboard/admin/roles.php', [
                    'action' => 'unassign',
                    'role' => $roleshortname,
                    'userid' => $user->id,
                    'sesskey' => sesskey(),
                ]);

                $actions = html_writer::link(
                    $unassignurl,
                    '<i class="fa fa-user-minus"></i>',
                    [
                        'class' => 'btn btn-sm btn-outline-danger',
                        'title' => get_string('unassign', 'local_jobboard'),
                        'onclick' => "return confirm('" . get_string('confirmunassign', 'local_jobboard') . "');",
                    ]
                );

                $rows[] = [
                    $userinfo,
                    $user->email,
                    userdate($user->assigneddate, get_string('strftimedateshort')),
                    $actions,
                ];
            }

            echo ui_helper::data_table($headers, $rows);
        }

        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();

        // Right column: Assign new users.
        echo html_writer::start_div('col-lg-5 mb-4');
        echo html_writer::start_div('card shadow-sm h-100');
        echo html_writer::start_div('card-header bg-success text-white');
        echo html_writer::tag('h6',
            '<i class="fa fa-user-plus mr-2"></i>' . get_string('assignnewusers', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');

        // User search/selection form.
        echo html_writer::start_tag('form', [
            'method' => 'post',
            'action' => new moodle_url('/local/jobboard/admin/roles.php'),
            'id' => 'assignform',
        ]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'assign']);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'role', 'value' => $roleshortname]);

        // Search input.
        echo html_writer::start_div('form-group');
        echo html_writer::tag('label', get_string('searchusers', 'local_jobboard'), ['for' => 'usersearch']);
        echo html_writer::empty_tag('input', [
            'type' => 'text',
            'class' => 'form-control',
            'id' => 'usersearch',
            'placeholder' => get_string('searchusersplaceholder', 'local_jobboard'),
            'autocomplete' => 'off',
        ]);
        echo html_writer::end_div();

        // User selection.
        echo html_writer::start_div('form-group');
        echo html_writer::tag('label', get_string('selectusers', 'local_jobboard'));

        // Get users not already assigned.
        $assigneduserids = array_keys($assignedusers);
        if (empty($assigneduserids)) {
            $assigneduserids = [0]; // Placeholder to avoid SQL error.
        }

        $sql = "SELECT u.id, u.firstname, u.lastname, u.email
                  FROM {user} u
                 WHERE u.deleted = 0
                   AND u.suspended = 0
                   AND u.id NOT IN (" . implode(',', $assigneduserids) . ")
                   AND u.id > 1
                 ORDER BY u.lastname, u.firstname
                 LIMIT 200";
        $availableusers = $DB->get_records_sql($sql);

        echo html_writer::start_tag('select', [
            'name' => 'userids[]',
            'id' => 'userselect',
            'class' => 'form-control',
            'multiple' => 'multiple',
            'size' => '10',
        ]);
        foreach ($availableusers as $user) {
            echo html_writer::tag('option',
                fullname($user) . ' (' . $user->email . ')',
                ['value' => $user->id]
            );
        }
        echo html_writer::end_tag('select');

        echo html_writer::tag('small',
            get_string('selectmultiplehelp', 'local_jobboard'),
            ['class' => 'form-text text-muted']
        );
        echo html_writer::end_div();

        echo html_writer::tag('button',
            '<i class="fa fa-user-plus mr-2"></i>' . get_string('assignselected', 'local_jobboard'),
            ['type' => 'submit', 'class' => 'btn btn-success btn-block']
        );

        echo html_writer::end_tag('form');

        // JavaScript for user search filtering.
        echo html_writer::script("
            document.getElementById('usersearch').addEventListener('keyup', function() {
                var filter = this.value.toLowerCase();
                var options = document.querySelectorAll('#userselect option');
                options.forEach(function(option) {
                    var text = option.textContent.toLowerCase();
                    option.style.display = text.includes(filter) ? '' : 'none';
                });
            });
        ");

        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();

        echo html_writer::end_div(); // row
    }
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
    new moodle_url('/local/jobboard/manage_committee.php'),
    '<i class="fa fa-users mr-2"></i>' . get_string('committees', 'local_jobboard'),
    ['class' => 'btn btn-outline-primary m-1']
);

echo html_writer::link(
    new moodle_url('/admin/settings.php', ['section' => 'local_jobboard']),
    '<i class="fa fa-cog mr-2"></i>' . get_string('pluginsettings', 'local_jobboard'),
    ['class' => 'btn btn-outline-info m-1']
);

echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-roles

echo $OUTPUT->footer();

/**
 * Get a preview of capabilities for a role.
 *
 * @param string $roleshortname Role shortname.
 * @return array Array of capability descriptions.
 */
function local_jobboard_get_role_capabilities_preview(string $roleshortname): array {
    $caps = [];

    switch ($roleshortname) {
        case 'jobboard_reviewer':
            $caps = [
                get_string('cap_review', 'local_jobboard'),
                get_string('cap_validate', 'local_jobboard'),
                get_string('cap_download', 'local_jobboard'),
            ];
            break;
        case 'jobboard_coordinator':
            $caps = [
                get_string('cap_manage', 'local_jobboard'),
                get_string('cap_createvacancy', 'local_jobboard'),
                get_string('cap_assignreviewers', 'local_jobboard'),
                get_string('cap_viewreports', 'local_jobboard'),
            ];
            break;
        case 'jobboard_committee':
            $caps = [
                get_string('cap_evaluate', 'local_jobboard'),
                get_string('cap_viewevaluations', 'local_jobboard'),
                get_string('cap_download', 'local_jobboard'),
            ];
            break;
    }

    return $caps;
}

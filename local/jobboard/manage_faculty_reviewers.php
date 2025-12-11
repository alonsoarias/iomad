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
 * Faculty reviewer management page.
 *
 * Assign and manage document reviewers per faculty/company.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\faculty_reviewer;
use local_jobboard\output\ui_helper;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:manageworkflow', $context);

// Parameters.
$companyid = optional_param('companyid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$userid = optional_param('userid', 0, PARAM_INT);
$usersearch = optional_param('usersearch', '', PARAM_TEXT);

// Page setup.
$urlparams = [];
if ($companyid) {
    $urlparams['companyid'] = $companyid;
}
$PAGE->set_url(new moodle_url('/local/jobboard/manage_faculty_reviewers.php', $urlparams));
$PAGE->set_context($context);
$PAGE->set_title(get_string('facultyreviewers', 'local_jobboard'));
$PAGE->set_heading(get_string('facultyreviewers', 'local_jobboard'));
$PAGE->set_pagelayout('admin');
$PAGE->requires->css('/local/jobboard/styles.css');

// Role definitions.
$reviewerroles = [
    faculty_reviewer::ROLE_LEAD => [
        'name' => get_string('role_lead_reviewer', 'local_jobboard'),
        'icon' => 'user-tie',
        'color' => 'primary',
    ],
    faculty_reviewer::ROLE_REVIEWER => [
        'name' => get_string('role_faculty_reviewer', 'local_jobboard'),
        'icon' => 'user-check',
        'color' => 'success',
    ],
];

// Handle actions.
if ($action === 'add' && confirm_sesskey()) {
    $addcompanyid = required_param('companyid', PARAM_INT);
    $adduserid = required_param('userid', PARAM_INT);
    $addrole = optional_param('role', faculty_reviewer::ROLE_REVIEWER, PARAM_ALPHA);

    $result = faculty_reviewer::add($addcompanyid, $adduserid, $addrole);
    if ($result) {
        // Assign the jobboard_reviewer role.
        $reviewerrole = $DB->get_record('role', ['shortname' => 'jobboard_reviewer']);
        if ($reviewerrole) {
            if (!$DB->record_exists('role_assignments', [
                'roleid' => $reviewerrole->id,
                'contextid' => $context->id,
                'userid' => $adduserid,
            ])) {
                role_assign($reviewerrole->id, $adduserid, $context->id);
            }
        }
        \core\notification::success(get_string('revieweradded', 'local_jobboard'));
    } else {
        \core\notification::error(get_string('revieweradderror', 'local_jobboard'));
    }
    redirect(new moodle_url('/local/jobboard/manage_faculty_reviewers.php', ['companyid' => $addcompanyid]));
}

if ($action === 'remove' && confirm_sesskey()) {
    $removecompanyid = required_param('companyid', PARAM_INT);
    $removeuserid = required_param('userid', PARAM_INT);

    $result = faculty_reviewer::remove($removecompanyid, $removeuserid);
    if ($result) {
        \core\notification::success(get_string('reviewerremoved', 'local_jobboard'));
    } else {
        \core\notification::error(get_string('reviewerremoveerror', 'local_jobboard'));
    }
    redirect(new moodle_url('/local/jobboard/manage_faculty_reviewers.php', ['companyid' => $removecompanyid]));
}

if ($action === 'changerole' && confirm_sesskey()) {
    $changecompanyid = required_param('companyid', PARAM_INT);
    $changeuserid = required_param('userid', PARAM_INT);
    $newrole = required_param('newrole', PARAM_ALPHA);

    $result = faculty_reviewer::update_role($changecompanyid, $changeuserid, $newrole);
    if ($result) {
        \core\notification::success(get_string('rolechanged', 'local_jobboard'));
    } else {
        \core\notification::error(get_string('rolechangeerror', 'local_jobboard'));
    }
    redirect(new moodle_url('/local/jobboard/manage_faculty_reviewers.php', ['companyid' => $changecompanyid]));
}

if ($action === 'togglestatus' && confirm_sesskey()) {
    $togglecompanyid = required_param('companyid', PARAM_INT);
    $toggleuserid = required_param('userid', PARAM_INT);

    $result = faculty_reviewer::toggle_status($togglecompanyid, $toggleuserid);
    if ($result) {
        \core\notification::success(get_string('statuschanged', 'local_jobboard'));
    } else {
        \core\notification::error(get_string('statuschangeerror', 'local_jobboard'));
    }
    redirect(new moodle_url('/local/jobboard/manage_faculty_reviewers.php', ['companyid' => $togglecompanyid]));
}

// Get statistics.
$stats = faculty_reviewer::get_statistics();

// Get all companies with reviewer info.
$companies = faculty_reviewer::get_companies_with_reviewers();

echo $OUTPUT->header();

echo html_writer::start_div('local-jobboard-faculty-reviewers');

// Page header.
echo ui_helper::page_header(
    get_string('facultyreviewers', 'local_jobboard'),
    [],
    [
        [
            'url' => new moodle_url('/local/jobboard/manage_committee.php'),
            'label' => get_string('committees', 'local_jobboard'),
            'icon' => 'users',
            'class' => 'btn btn-outline-info',
        ],
        [
            'url' => new moodle_url('/local/jobboard/assign_reviewer.php'),
            'label' => get_string('assignreviewer', 'local_jobboard'),
            'icon' => 'user-plus',
            'class' => 'btn btn-outline-primary',
        ],
        [
            'url' => new moodle_url('/local/jobboard/index.php'),
            'label' => get_string('dashboard', 'local_jobboard'),
            'icon' => 'tachometer-alt',
            'class' => 'btn btn-outline-secondary',
        ],
    ]
);

// Statistics cards.
echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card((string) $stats['total_reviewers'], get_string('totalreviewers', 'local_jobboard'), 'primary', 'users');
echo ui_helper::stat_card((string) $stats['active_reviewers'], get_string('activereviewers', 'local_jobboard'), 'success', 'user-check');
echo ui_helper::stat_card((string) $stats['lead_reviewers'], get_string('leadreviewers', 'local_jobboard'), 'info', 'user-tie');
echo ui_helper::stat_card((string) $stats['faculties_with_reviewers'], get_string('facultieswithreviewers', 'local_jobboard'), 'warning', 'building');
echo html_writer::end_div();

// Faculty filter.
$companyoptions = [0 => get_string('selectfaculty', 'local_jobboard')];
foreach ($companies as $c) {
    $label = format_string($c->name);
    if ($c->reviewer_count > 0) {
        $label .= ' (' . $c->reviewer_count . ' ' . get_string('reviewers', 'local_jobboard') . ')';
    }
    $companyoptions[$c->id] = $label;
}

$filterdefs = [
    [
        'type' => 'select',
        'name' => 'companyid',
        'label' => get_string('faculty', 'local_jobboard'),
        'options' => $companyoptions,
        'col' => 'col-md-6',
    ],
];

echo ui_helper::filter_form(
    (new moodle_url('/local/jobboard/manage_faculty_reviewers.php'))->out(false),
    $filterdefs,
    ['companyid' => $companyid],
    []
);

// Main content.
if (empty($companyid)) {
    // Show overview of all faculties.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5',
        '<i class="fa fa-building text-primary mr-2"></i>' . get_string('allfaculties', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    if (empty($companies)) {
        echo ui_helper::empty_state(get_string('nofacultieswithvacancies', 'local_jobboard'), 'building');
    } else {
        $headers = [
            get_string('faculty', 'local_jobboard'),
            get_string('reviewers', 'local_jobboard'),
            get_string('leadreviewers', 'local_jobboard'),
            get_string('actions'),
        ];

        $rows = [];
        foreach ($companies as $c) {
            $facultylink = html_writer::link(
                new moodle_url('/local/jobboard/manage_faculty_reviewers.php', ['companyid' => $c->id]),
                format_string($c->name)
            );

            $reviewersbadge = html_writer::tag('span', $c->reviewer_count,
                ['class' => 'badge badge-' . ($c->reviewer_count > 0 ? 'success' : 'secondary')]);

            $leadsbadge = html_writer::tag('span', $c->lead_count,
                ['class' => 'badge badge-' . ($c->lead_count > 0 ? 'primary' : 'secondary')]);

            $actions = html_writer::link(
                new moodle_url('/local/jobboard/manage_faculty_reviewers.php', ['companyid' => $c->id]),
                '<i class="fa fa-cog"></i> ' . get_string('manage', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-primary']
            );

            $rows[] = [$facultylink, $reviewersbadge, $leadsbadge, $actions];
        }

        echo ui_helper::data_table($headers, $rows);
    }

    echo html_writer::end_div();
    echo html_writer::end_div();

    // Show faculties without reviewers.
    $facultieswithout = array_filter($companies, fn($c) => $c->reviewer_count == 0);
    if (!empty($facultieswithout)) {
        echo html_writer::start_div('card shadow-sm mb-4 border-warning');
        echo html_writer::start_div('card-header bg-warning text-dark');
        echo html_writer::tag('h6',
            '<i class="fa fa-exclamation-triangle mr-2"></i>' . get_string('facultieswithoutreviewers', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');

        echo html_writer::start_div('list-group');
        foreach ($facultieswithout as $c) {
            echo html_writer::start_div('list-group-item d-flex justify-content-between align-items-center');
            echo html_writer::tag('span', format_string($c->name));
            echo html_writer::link(
                new moodle_url('/local/jobboard/manage_faculty_reviewers.php', ['companyid' => $c->id]),
                '<i class="fa fa-plus mr-1"></i>' . get_string('addreviewers', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-warning']
            );
            echo html_writer::end_div();
        }
        echo html_writer::end_div();

        echo html_writer::end_div();
        echo html_writer::end_div();
    }

} else {
    // Show reviewers for selected faculty.
    $company = $DB->get_record('company', ['id' => $companyid], '*', MUST_EXIST);
    $reviewers = faculty_reviewer::get_workload_for_company($companyid);

    // Back button.
    echo html_writer::link(
        new moodle_url('/local/jobboard/manage_faculty_reviewers.php'),
        '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtolist', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary btn-sm mb-3']
    );

    // Faculty header.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-primary text-white');
    echo html_writer::start_div('d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5',
        '<i class="fa fa-building mr-2"></i>' . format_string($company->name),
        ['class' => 'mb-0']
    );
    $vacancycount = $DB->count_records('local_jobboard_vacancy', ['companyid' => $companyid]);
    echo html_writer::tag('span', $vacancycount . ' ' . get_string('vacancies', 'local_jobboard'), ['class' => 'badge badge-light']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Current reviewers list.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-success text-white d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5',
        '<i class="fa fa-users mr-2"></i>' . get_string('assignedreviewers', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::tag('span', count($reviewers) . ' ' . get_string('reviewers', 'local_jobboard'),
        ['class' => 'badge badge-light']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    if (empty($reviewers)) {
        echo ui_helper::empty_state(get_string('noreviewersforfaculty', 'local_jobboard'), 'user-check');
    } else {
        echo html_writer::start_div('row');
        foreach ($reviewers as $rev) {
            $roledef = $reviewerroles[$rev->role] ?? $reviewerroles[faculty_reviewer::ROLE_REVIEWER];
            $statusclass = ($rev->status === faculty_reviewer::STATUS_ACTIVE) ? 'success' : 'secondary';

            echo html_writer::start_div('col-lg-4 col-md-6 mb-3');
            echo html_writer::start_div('card h-100 border-0 bg-light');
            echo html_writer::start_div('card-body text-center');

            // Avatar with role icon.
            echo html_writer::tag('div',
                html_writer::tag('i', '', ['class' => "fa fa-{$roledef['icon']} fa-2x text-{$roledef['color']}"]),
                ['class' => "jb-icon-circle bg-{$roledef['color']}-light mb-2 mx-auto",
                 'style' => 'width:60px;height:60px;display:flex;align-items:center;justify-content:center;border-radius:50%;']
            );

            // Name.
            echo html_writer::tag('h6', fullname($rev), ['class' => 'mb-1']);

            // Username.
            echo html_writer::tag('small', '@' . $rev->username, ['class' => 'd-block text-info mb-1']);

            // Role badge.
            echo html_writer::tag('span', $roledef['name'], ['class' => "badge badge-{$roledef['color']} mb-1"]);

            // Status badge.
            if ($rev->status !== faculty_reviewer::STATUS_ACTIVE) {
                echo html_writer::tag('span', get_string('inactive', 'local_jobboard'),
                    ['class' => 'badge badge-secondary ml-1 mb-1']);
            }

            // Email.
            echo html_writer::tag('small', $rev->email, ['class' => 'd-block text-muted mb-2']);

            // Workload stats.
            echo html_writer::start_div('small text-muted mb-2');
            echo '<i class="fa fa-clock mr-1"></i>' . get_string('pending', 'local_jobboard') . ': ';
            echo html_writer::tag('strong', $rev->pending_reviews);
            echo ' | <i class="fa fa-check mr-1"></i>' . get_string('completed', 'local_jobboard') . ': ';
            echo html_writer::tag('strong', $rev->completed_reviews);
            echo html_writer::end_div();

            // Actions.
            echo html_writer::start_div('btn-group btn-group-sm');

            // Change role dropdown.
            echo html_writer::start_div('dropdown');
            echo html_writer::tag('button',
                '<i class="fa fa-exchange-alt"></i>',
                [
                    'class' => 'btn btn-sm btn-outline-secondary dropdown-toggle',
                    'type' => 'button',
                    'data-toggle' => 'dropdown',
                    'title' => get_string('changerole', 'local_jobboard'),
                ]
            );
            echo html_writer::start_div('dropdown-menu');
            foreach ($reviewerroles as $rolecode => $roleinfo) {
                if ($rolecode !== $rev->role) {
                    $changeurl = new moodle_url('/local/jobboard/manage_faculty_reviewers.php', [
                        'action' => 'changerole',
                        'companyid' => $companyid,
                        'userid' => $rev->userid,
                        'newrole' => $rolecode,
                        'sesskey' => sesskey(),
                    ]);
                    echo html_writer::link($changeurl, $roleinfo['name'], ['class' => 'dropdown-item']);
                }
            }
            echo html_writer::end_div();
            echo html_writer::end_div();

            // Toggle status button.
            $toggleurl = new moodle_url('/local/jobboard/manage_faculty_reviewers.php', [
                'action' => 'togglestatus',
                'companyid' => $companyid,
                'userid' => $rev->userid,
                'sesskey' => sesskey(),
            ]);
            $toggleicon = ($rev->status === faculty_reviewer::STATUS_ACTIVE) ? 'pause' : 'play';
            $toggletitle = ($rev->status === faculty_reviewer::STATUS_ACTIVE) ?
                get_string('deactivate', 'local_jobboard') : get_string('activate', 'local_jobboard');
            echo html_writer::link($toggleurl,
                '<i class="fa fa-' . $toggleicon . '"></i>',
                ['class' => 'btn btn-sm btn-outline-warning', 'title' => $toggletitle]
            );

            // Remove button.
            $removeurl = new moodle_url('/local/jobboard/manage_faculty_reviewers.php', [
                'action' => 'remove',
                'companyid' => $companyid,
                'userid' => $rev->userid,
                'sesskey' => sesskey(),
            ]);
            echo html_writer::link($removeurl,
                '<i class="fa fa-user-minus"></i>',
                [
                    'class' => 'btn btn-sm btn-outline-danger',
                    'title' => get_string('removereviewer', 'local_jobboard'),
                    'onclick' => "return confirm('" . get_string('confirmremovereviewer', 'local_jobboard') . "');",
                ]
            );

            echo html_writer::end_div();

            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
        }
        echo html_writer::end_div();
    }

    echo html_writer::end_div();
    echo html_writer::end_div();

    // Add reviewer form.
    echo html_writer::start_div('card shadow-sm mb-4 border-primary');
    echo html_writer::start_div('card-header bg-primary text-white');
    echo html_writer::tag('h6',
        '<i class="fa fa-user-plus mr-2"></i>' . get_string('addreviewer', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    // Get existing reviewer user IDs.
    $existingreviewerids = array_column($reviewers, 'userid');
    if (empty($existingreviewerids)) {
        $existingreviewerids = [0];
    }

    // User search form.
    echo html_writer::start_tag('form', [
        'method' => 'get',
        'action' => new moodle_url('/local/jobboard/manage_faculty_reviewers.php'),
        'class' => 'mb-3',
    ]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'companyid', 'value' => $companyid]);
    echo html_writer::start_div('input-group');
    echo html_writer::empty_tag('input', [
        'type' => 'text',
        'name' => 'usersearch',
        'class' => 'form-control',
        'placeholder' => get_string('searchbyusername', 'local_jobboard'),
        'value' => $usersearch,
    ]);
    echo html_writer::start_div('input-group-append');
    echo html_writer::tag('button',
        '<i class="fa fa-search"></i>',
        ['type' => 'submit', 'class' => 'btn btn-outline-secondary']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_tag('form');

    // Get available users with reviewer capability.
    $searchsql = '';
    $searchparams = [];
    if (!empty($usersearch)) {
        $searchsql = " AND (u.firstname LIKE :search1 OR u.lastname LIKE :search2
                       OR u.email LIKE :search3 OR u.username LIKE :search4)";
        $searchparams = [
            'search1' => '%' . $usersearch . '%',
            'search2' => '%' . $usersearch . '%',
            'search3' => '%' . $usersearch . '%',
            'search4' => '%' . $usersearch . '%',
        ];
    }

    // Get users with reviewer capability who are not already assigned.
    $sql = "SELECT u.id, u.firstname, u.lastname, u.email, u.username
              FROM {user} u
              JOIN {role_assignments} ra ON ra.userid = u.id
              JOIN {role} r ON r.id = ra.roleid
              JOIN {role_capabilities} rc ON rc.roleid = r.id
             WHERE u.deleted = 0
               AND u.suspended = 0
               AND u.id NOT IN (" . implode(',', $existingreviewerids) . ")
               AND u.id > 1
               AND rc.capability = 'local/jobboard:reviewdocuments'
               AND rc.permission = 1
               $searchsql
             GROUP BY u.id, u.firstname, u.lastname, u.email, u.username
             ORDER BY u.lastname, u.firstname
             LIMIT 200";
    $availableusers = $DB->get_records_sql($sql, $searchparams);

    // If no users found with capability, fall back to searching all active users.
    if (empty($availableusers) && !empty($usersearch)) {
        $sql = "SELECT u.id, u.firstname, u.lastname, u.email, u.username
                  FROM {user} u
                 WHERE u.deleted = 0
                   AND u.suspended = 0
                   AND u.id NOT IN (" . implode(',', $existingreviewerids) . ")
                   AND u.id > 1
                   $searchsql
                 ORDER BY u.lastname, u.firstname
                 LIMIT 200";
        $availableusers = $DB->get_records_sql($sql, $searchparams);
    }

    echo html_writer::start_tag('form', [
        'method' => 'post',
        'action' => new moodle_url('/local/jobboard/manage_faculty_reviewers.php'),
        'class' => 'form-inline',
    ]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'add']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'companyid', 'value' => $companyid]);

    echo html_writer::start_div('form-group mr-2 mb-2');
    echo html_writer::start_tag('select', ['name' => 'userid', 'class' => 'form-control', 'required' => 'required']);
    echo html_writer::tag('option', get_string('selectuser', 'local_jobboard'), ['value' => '']);
    foreach ($availableusers as $user) {
        echo html_writer::tag('option',
            fullname($user) . ' (@' . $user->username . ') - ' . $user->email,
            ['value' => $user->id]
        );
    }
    echo html_writer::end_tag('select');
    echo html_writer::end_div();

    echo html_writer::start_div('form-group mr-2 mb-2');
    echo html_writer::start_tag('select', ['name' => 'role', 'class' => 'form-control', 'required' => 'required']);
    foreach ($reviewerroles as $rolecode => $roleinfo) {
        echo html_writer::tag('option', $roleinfo['name'], ['value' => $rolecode]);
    }
    echo html_writer::end_tag('select');
    echo html_writer::end_div();

    echo html_writer::tag('button',
        '<i class="fa fa-user-plus mr-1"></i>' . get_string('addreviewer', 'local_jobboard'),
        ['type' => 'submit', 'class' => 'btn btn-primary mb-2']
    );

    echo html_writer::end_tag('form');

    // Info alert.
    echo html_writer::start_div('alert alert-info mt-3');
    echo html_writer::tag('i', '', ['class' => 'fa fa-info-circle mr-2']);
    echo get_string('facultyreviewerhelp', 'local_jobboard');
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Navigation footer.
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
    ['class' => 'btn btn-outline-info m-1']
);

echo html_writer::link(
    new moodle_url('/local/jobboard/assign_reviewer.php'),
    '<i class="fa fa-user-plus mr-2"></i>' . get_string('assignreviewer', 'local_jobboard'),
    ['class' => 'btn btn-outline-primary m-1']
);

echo html_writer::link(
    new moodle_url('/local/jobboard/index.php', ['view' => 'review']),
    '<i class="fa fa-clipboard-check mr-2"></i>' . get_string('reviewapplications', 'local_jobboard'),
    ['class' => 'btn btn-outline-success m-1']
);

echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-faculty-reviewers

echo $OUTPUT->footer();

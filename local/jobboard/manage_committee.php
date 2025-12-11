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
 * Selection committee management page.
 *
 * Create, edit, and manage selection committees for faculties/companies.
 * Assign users with different roles (chair, evaluator, secretary, observer).
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\committee;
use local_jobboard\output\ui_helper;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:manageworkflow', $context);

// Parameters.
$companyid = optional_param('companyid', 0, PARAM_INT);
$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
// Support old parameter name for backwards compatibility.
if (!$vacancyid) {
    $vacancyid = optional_param('vacancy', 0, PARAM_INT);
}
$action = optional_param('action', '', PARAM_ALPHA);
$committeeid = optional_param('id', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$usersearch = optional_param('usersearch', '', PARAM_TEXT);

// Page setup.
$urlparams = [];
if ($companyid) {
    $urlparams['companyid'] = $companyid;
}
if ($vacancyid) {
    $urlparams['vacancyid'] = $vacancyid;
}
$PAGE->set_url(new moodle_url('/local/jobboard/manage_committee.php', $urlparams));
$PAGE->set_context($context);
$PAGE->set_title(get_string('committees', 'local_jobboard'));
$PAGE->set_heading(get_string('committees', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Role definitions.
$memberroles = [
    committee::ROLE_CHAIR => [
        'name' => get_string('role_chair', 'local_jobboard'),
        'icon' => 'user-tie',
        'color' => 'danger',
    ],
    committee::ROLE_SECRETARY => [
        'name' => get_string('role_secretary', 'local_jobboard'),
        'icon' => 'user-edit',
        'color' => 'primary',
    ],
    committee::ROLE_EVALUATOR => [
        'name' => get_string('role_evaluator', 'local_jobboard'),
        'icon' => 'user-check',
        'color' => 'success',
    ],
    committee::ROLE_OBSERVER => [
        'name' => get_string('role_observer', 'local_jobboard'),
        'icon' => 'user-clock',
        'color' => 'secondary',
    ],
];

// Handle actions.
if ($action === 'create' && confirm_sesskey()) {
    $createcompanyid = required_param('companyid', PARAM_INT);
    $name = required_param('name', PARAM_TEXT);

    // Get selected members.
    $chairid = required_param('chair', PARAM_INT);
    $members = [
        ['userid' => $chairid, 'role' => committee::ROLE_CHAIR],
    ];

    // Optional members.
    $secretaryid = optional_param('secretary', 0, PARAM_INT);
    if ($secretaryid) {
        $members[] = ['userid' => $secretaryid, 'role' => committee::ROLE_SECRETARY];
    }

    $evaluatorids = optional_param_array('evaluators', [], PARAM_INT);
    foreach ($evaluatorids as $eid) {
        if ($eid && $eid != $chairid && $eid != $secretaryid) {
            $members[] = ['userid' => $eid, 'role' => committee::ROLE_EVALUATOR];
        }
    }

    // Create committee for the faculty/company.
    $committeeid = committee::create_for_company($createcompanyid, $name, $members);
    if ($committeeid) {
        // Assign the jobboard_committee role to all members.
        $committeerole = $DB->get_record('role', ['shortname' => 'jobboard_committee']);
        if ($committeerole) {
            foreach ($members as $member) {
                if (!$DB->record_exists('role_assignments', [
                    'roleid' => $committeerole->id,
                    'contextid' => $context->id,
                    'userid' => $member['userid'],
                ])) {
                    role_assign($committeerole->id, $member['userid'], $context->id);
                }
            }
        }

        \core\notification::success(get_string('committeecreated', 'local_jobboard'));
    } else {
        \core\notification::error(get_string('committeecreateerror', 'local_jobboard'));
    }
    redirect(new moodle_url('/local/jobboard/manage_committee.php', ['companyid' => $createcompanyid]));
}

if ($action === 'addmember' && confirm_sesskey()) {
    // Support both parameter styles.
    $cid = $committeeid ?: optional_param('committee', 0, PARAM_INT);
    $uid = $userid ?: optional_param('userid', 0, PARAM_INT);
    $role = optional_param('memberrole', '', PARAM_ALPHA);
    if (!$role) {
        $role = optional_param('role', committee::ROLE_EVALUATOR, PARAM_ALPHA);
    }

    if ($cid && $uid) {
        $result = committee::add_member($cid, $uid, $role);
        if ($result) {
            // Assign the jobboard_committee role.
            $committeerole = $DB->get_record('role', ['shortname' => 'jobboard_committee']);
            if ($committeerole) {
                if (!$DB->record_exists('role_assignments', [
                    'roleid' => $committeerole->id,
                    'contextid' => $context->id,
                    'userid' => $uid,
                ])) {
                    role_assign($committeerole->id, $uid, $context->id);
                }
            }
            \core\notification::success(get_string('memberadded', 'local_jobboard'));
        } else {
            \core\notification::error(get_string('memberadderror', 'local_jobboard'));
        }

        // Get company ID from committee.
        $comm = $DB->get_record('local_jobboard_committee', ['id' => $cid]);
        $redirectparams = !empty($comm->companyid) ? ['companyid' => $comm->companyid] : ['vacancyid' => $comm->vacancyid];
        redirect(new moodle_url('/local/jobboard/manage_committee.php', $redirectparams));
    }
}

if ($action === 'removemember' && confirm_sesskey()) {
    $cid = $committeeid ?: optional_param('committee', 0, PARAM_INT);
    $uid = $userid ?: optional_param('userid', 0, PARAM_INT);

    if ($cid && $uid) {
        $result = committee::remove_member($cid, $uid);
        if ($result) {
            \core\notification::success(get_string('memberremoved', 'local_jobboard'));
        } else {
            \core\notification::error(get_string('memberremoveerror', 'local_jobboard'));
        }

        $comm = $DB->get_record('local_jobboard_committee', ['id' => $cid]);
        $redirectparams = !empty($comm->companyid) ? ['companyid' => $comm->companyid] : ['vacancyid' => $comm->vacancyid];
        redirect(new moodle_url('/local/jobboard/manage_committee.php', $redirectparams));
    }
}

if ($action === 'changerole' && confirm_sesskey()) {
    $cid = $committeeid ?: optional_param('committee', 0, PARAM_INT);
    $uid = $userid ?: optional_param('userid', 0, PARAM_INT);
    $newrole = required_param('newrole', PARAM_ALPHA);

    if ($cid && $uid) {
        $result = committee::update_member_role($cid, $uid, $newrole);
        if ($result) {
            \core\notification::success(get_string('rolechanged', 'local_jobboard'));
        } else {
            \core\notification::error(get_string('rolechangeerror', 'local_jobboard'));
        }

        $comm = $DB->get_record('local_jobboard_committee', ['id' => $cid]);
        $redirectparams = !empty($comm->companyid) ? ['companyid' => $comm->companyid] : ['vacancyid' => $comm->vacancyid];
        redirect(new moodle_url('/local/jobboard/manage_committee.php', $redirectparams));
    }
}

// Get statistics.
$totalcommittees = $DB->count_records('local_jobboard_committee');
$activecommittees = $DB->count_records('local_jobboard_committee', ['status' => 'active']);
$totalmembers = $DB->count_records('local_jobboard_committee_member');

// Get all IOMAD companies (faculties) with vacancies.
$companies = $DB->get_records_sql("
    SELECT DISTINCT c.id, c.name, c.shortname
      FROM {company} c
     WHERE c.id IN (
         SELECT DISTINCT companyid FROM {local_jobboard_vacancy} WHERE companyid IS NOT NULL
         UNION
         SELECT DISTINCT companyid FROM {local_jobboard_committee} WHERE companyid IS NOT NULL
     )
     ORDER BY c.name
");

// Get vacancies for legacy support (only those without company).
$vacancies = $DB->get_records_select('local_jobboard_vacancy',
    "status IN ('published', 'closed') AND (companyid IS NULL OR companyid = 0)",
    null, 'code ASC', 'id, code, title, status, companyid');

echo $OUTPUT->header();

echo html_writer::start_div('local-jobboard-committees');

// ============================================================================
// PAGE HEADER
// ============================================================================
echo ui_helper::page_header(
    get_string('committees', 'local_jobboard'),
    [],
    [
        [
            'url' => new moodle_url('/local/jobboard/admin/roles.php'),
            'label' => get_string('manageroles', 'local_jobboard'),
            'icon' => 'user-tag',
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

// ============================================================================
// STATISTICS CARDS
// ============================================================================
echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card((string) $totalcommittees, get_string('totalcommittees', 'local_jobboard'), 'primary', 'users');
echo ui_helper::stat_card((string) $activecommittees, get_string('activecommittees', 'local_jobboard'), 'success', 'users-cog');
echo ui_helper::stat_card((string) $totalmembers, get_string('totalcommmembers', 'local_jobboard'), 'info', 'user-friends');
echo html_writer::end_div();

// ============================================================================
// FACULTY/COMPANY FILTER (Primary filter)
// ============================================================================
$companyoptions = [0 => get_string('selectfaculty', 'local_jobboard')];
foreach ($companies as $c) {
    $companyoptions[$c->id] = format_string($c->name);
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
    (new moodle_url('/local/jobboard/manage_committee.php'))->out(false),
    $filterdefs,
    ['companyid' => $companyid],
    []
);

// ============================================================================
// COMMITTEES LIST OR DETAILS
// ============================================================================
if (empty($companyid) && empty($vacancyid)) {
    // Show list of all committees grouped by faculty.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5',
        '<i class="fa fa-users text-primary mr-2"></i>' . get_string('allcommittees', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    $sql = "SELECT c.*, comp.name as company_name, comp.shortname as company_shortname,
                   v.code as vacancy_code, v.title as vacancy_title,
                   (SELECT COUNT(*) FROM {local_jobboard_committee_member} WHERE committeeid = c.id) as membercount
              FROM {local_jobboard_committee} c
         LEFT JOIN {company} comp ON comp.id = c.companyid
         LEFT JOIN {local_jobboard_vacancy} v ON v.id = c.vacancyid
             ORDER BY comp.name, c.timecreated DESC";
    $committees = $DB->get_records_sql($sql);

    if (empty($committees)) {
        echo ui_helper::empty_state(get_string('nocommittees', 'local_jobboard'), 'users');
    } else {
        $headers = [
            get_string('faculty', 'local_jobboard'),
            get_string('committeename', 'local_jobboard'),
            get_string('members', 'local_jobboard'),
            get_string('status'),
            get_string('actions'),
        ];

        $rows = [];
        foreach ($committees as $comm) {
            // Determine link params based on whether it's a company or vacancy committee.
            if (!empty($comm->companyid)) {
                $linkparams = ['companyid' => $comm->companyid];
                $entityname = format_string($comm->company_name ?: $comm->company_shortname);
            } else {
                $linkparams = ['vacancyid' => $comm->vacancyid];
                $entityname = format_string($comm->vacancy_code . ' - ' . $comm->vacancy_title);
            }

            $entitylink = html_writer::link(
                new moodle_url('/local/jobboard/manage_committee.php', $linkparams),
                $entityname
            );

            $statusbadge = $comm->status === 'active'
                ? '<span class="badge badge-success">' . get_string('active', 'local_jobboard') . '</span>'
                : '<span class="badge badge-secondary">' . $comm->status . '</span>';

            $actions = html_writer::link(
                new moodle_url('/local/jobboard/manage_committee.php', $linkparams),
                '<i class="fa fa-edit"></i>',
                ['class' => 'btn btn-sm btn-outline-primary', 'title' => get_string('manage', 'local_jobboard')]
            );

            $rows[] = [
                $entitylink,
                format_string($comm->name),
                html_writer::tag('span', $comm->membercount, ['class' => 'badge badge-info']),
                $statusbadge,
                $actions,
            ];
        }

        echo ui_helper::data_table($headers, $rows);
    }

    echo html_writer::end_div();
    echo html_writer::end_div();

    // Faculties without committees.
    $companieswithoutcomm = [];
    foreach ($companies as $c) {
        if (!$DB->record_exists('local_jobboard_committee', ['companyid' => $c->id])) {
            $companieswithoutcomm[] = $c;
        }
    }

    if (!empty($companieswithoutcomm)) {
        echo html_writer::start_div('card shadow-sm mb-4 border-warning');
        echo html_writer::start_div('card-header bg-warning text-dark');
        echo html_writer::tag('h6',
            '<i class="fa fa-exclamation-triangle mr-2"></i>' . get_string('facultieswithoutcommittee', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');

        echo html_writer::start_div('list-group');
        foreach ($companieswithoutcomm as $c) {
            echo html_writer::start_div('list-group-item d-flex justify-content-between align-items-center');
            echo html_writer::tag('span', format_string($c->name));
            echo html_writer::link(
                new moodle_url('/local/jobboard/manage_committee.php', ['companyid' => $c->id]),
                '<i class="fa fa-plus mr-1"></i>' . get_string('createcommittee', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-warning']
            );
            echo html_writer::end_div();
        }
        echo html_writer::end_div();

        echo html_writer::end_div();
        echo html_writer::end_div();
    }

} else if ($companyid) {
    // Show committee for selected faculty/company.
    $company = $DB->get_record('company', ['id' => $companyid], '*', MUST_EXIST);
    $existingcommittee = committee::get_for_company($companyid);

    // Back button.
    echo html_writer::link(
        new moodle_url('/local/jobboard/manage_committee.php'),
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
    // Show number of vacancies in this faculty.
    $vacancycount = $DB->count_records('local_jobboard_vacancy', ['companyid' => $companyid]);
    echo html_writer::tag('span', $vacancycount . ' ' . get_string('vacancies', 'local_jobboard'), ['class' => 'badge badge-light']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    if ($existingcommittee) {
        // Show existing committee.
        echo html_writer::start_div('card shadow-sm mb-4');
        echo html_writer::start_div('card-header bg-success text-white d-flex justify-content-between align-items-center');
        echo html_writer::tag('h5',
            '<i class="fa fa-users mr-2"></i>' . format_string($existingcommittee->name),
            ['class' => 'mb-0']
        );
        echo html_writer::tag('span',
            count($existingcommittee->members) . ' ' . get_string('members', 'local_jobboard'),
            ['class' => 'badge badge-light']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');

        // Members list.
        if (!empty($existingcommittee->members)) {
            echo html_writer::start_div('row');
            foreach ($existingcommittee->members as $member) {
                $roledef = $memberroles[$member->role] ?? $memberroles[committee::ROLE_EVALUATOR];

                echo html_writer::start_div('col-lg-3 col-md-4 col-sm-6 mb-3');
                echo html_writer::start_div('card h-100 border-0 bg-light');
                echo html_writer::start_div('card-body text-center');

                // Avatar.
                echo html_writer::tag('div',
                    html_writer::tag('i', '', ['class' => "fa fa-{$roledef['icon']} fa-2x text-{$roledef['color']}"]),
                    ['class' => "jb-icon-circle jb-avatar-circle bg-{$roledef['color']}-light mb-2 mx-auto"]
                );

                // Name.
                echo html_writer::tag('h6', fullname($member), ['class' => 'mb-1']);

                // Username badge.
                echo html_writer::tag('small', '@' . $member->username, ['class' => 'd-block text-info mb-1']);

                // Role badge.
                echo html_writer::tag('span', $roledef['name'], ['class' => "badge badge-{$roledef['color']} mb-2"]);

                // Email.
                echo html_writer::tag('small', $member->email, ['class' => 'd-block text-muted mb-2']);

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
                        'aria-haspopup' => 'true',
                        'aria-expanded' => 'false',
                        'title' => get_string('changerole', 'local_jobboard'),
                    ]
                );
                echo html_writer::start_div('dropdown-menu');
                foreach ($memberroles as $rolecode => $roleinfo) {
                    if ($rolecode !== $member->role) {
                        $changeurl = new moodle_url('/local/jobboard/manage_committee.php', [
                            'action' => 'changerole',
                            'id' => $existingcommittee->id,
                            'userid' => $member->userid,
                            'newrole' => $rolecode,
                            'sesskey' => sesskey(),
                        ]);
                        echo html_writer::link($changeurl, $roleinfo['name'], ['class' => 'dropdown-item']);
                    }
                }
                echo html_writer::end_div();
                echo html_writer::end_div();

                // Remove button.
                $removeurl = new moodle_url('/local/jobboard/manage_committee.php', [
                    'action' => 'removemember',
                    'id' => $existingcommittee->id,
                    'userid' => $member->userid,
                    'sesskey' => sesskey(),
                ]);
                echo html_writer::link($removeurl,
                    '<i class="fa fa-user-minus"></i>',
                    [
                        'class' => 'btn btn-sm btn-outline-danger',
                        'title' => get_string('removemember', 'local_jobboard'),
                        'onclick' => "return confirm('" . get_string('confirmremovemember', 'local_jobboard') . "');",
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

        // Add member form with username search.
        echo html_writer::start_div('card shadow-sm mb-4 border-primary');
        echo html_writer::start_div('card-header bg-primary text-white');
        echo html_writer::tag('h6',
            '<i class="fa fa-user-plus mr-2"></i>' . get_string('addmember', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');

        // Get existing member user IDs.
        $existingmemberids = array_column((array) $existingcommittee->members, 'userid');
        if (empty($existingmemberids)) {
            $existingmemberids = [0];
        }

        // User search form.
        echo html_writer::start_tag('form', [
            'method' => 'get',
            'action' => new moodle_url('/local/jobboard/manage_committee.php'),
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

        // Get available users with search including username.
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

        $sql = "SELECT u.id, u.firstname, u.lastname, u.email, u.username
                  FROM {user} u
                 WHERE u.deleted = 0
                   AND u.suspended = 0
                   AND u.id NOT IN (" . implode(',', $existingmemberids) . ")
                   AND u.id > 1
                   $searchsql
                 ORDER BY u.lastname, u.firstname
                 LIMIT 200";
        $availableusers = $DB->get_records_sql($sql, $searchparams);

        echo html_writer::start_tag('form', [
            'method' => 'post',
            'action' => new moodle_url('/local/jobboard/manage_committee.php'),
            'class' => 'form-inline',
        ]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'addmember']);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $existingcommittee->id]);

        echo html_writer::start_div('form-group mr-2 mb-2');
        echo html_writer::start_tag('select', ['name' => 'userid', 'class' => 'form-control', 'required' => 'required']);
        echo html_writer::tag('option', get_string('selectuser', 'local_jobboard'), ['value' => '']);
        foreach ($availableusers as $user) {
            // Include username in the display.
            echo html_writer::tag('option',
                fullname($user) . ' (@' . $user->username . ') - ' . $user->email,
                ['value' => $user->id]
            );
        }
        echo html_writer::end_tag('select');
        echo html_writer::end_div();

        echo html_writer::start_div('form-group mr-2 mb-2');
        echo html_writer::start_tag('select', ['name' => 'memberrole', 'class' => 'form-control', 'required' => 'required']);
        foreach ($memberroles as $rolecode => $roleinfo) {
            echo html_writer::tag('option', $roleinfo['name'], ['value' => $rolecode]);
        }
        echo html_writer::end_tag('select');
        echo html_writer::end_div();

        echo html_writer::tag('button',
            '<i class="fa fa-user-plus mr-1"></i>' . get_string('addmember', 'local_jobboard'),
            ['type' => 'submit', 'class' => 'btn btn-primary mb-2']
        );

        echo html_writer::end_tag('form');

        echo html_writer::end_div();
        echo html_writer::end_div();

        // Show vacancies that use this committee.
        $facultyvacancies = $DB->get_records('local_jobboard_vacancy',
            ['companyid' => $companyid, 'status' => 'published'],
            'code ASC', 'id, code, title, status');

        if (!empty($facultyvacancies)) {
            echo html_writer::start_div('card shadow-sm mb-4');
            echo html_writer::start_div('card-header bg-white');
            echo html_writer::tag('h5',
                '<i class="fa fa-briefcase text-primary mr-2"></i>' . get_string('facultyvacancies', 'local_jobboard'),
                ['class' => 'mb-0']
            );
            echo html_writer::end_div();
            echo html_writer::start_div('card-body');

            echo html_writer::start_div('list-group');
            foreach ($facultyvacancies as $v) {
                echo html_writer::start_div('list-group-item d-flex justify-content-between align-items-center');
                echo html_writer::tag('span', format_string($v->code . ' - ' . $v->title));
                echo ui_helper::status_badge($v->status, 'vacancy');
                echo html_writer::end_div();
            }
            echo html_writer::end_div();

            echo html_writer::end_div();
            echo html_writer::end_div();
        }

    } else {
        // Create committee form for faculty.
        echo html_writer::start_div('card shadow-sm mb-4 border-success');
        echo html_writer::start_div('card-header bg-success text-white');
        echo html_writer::tag('h5',
            '<i class="fa fa-users mr-2"></i>' . get_string('createcommittee', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');

        // User search form.
        echo html_writer::start_tag('form', [
            'method' => 'get',
            'action' => new moodle_url('/local/jobboard/manage_committee.php'),
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

        // Get available users with search including username.
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

        $sql = "SELECT u.id, u.firstname, u.lastname, u.email, u.username
                  FROM {user} u
                 WHERE u.deleted = 0
                   AND u.suspended = 0
                   AND u.id > 1
                   $searchsql
                 ORDER BY u.lastname, u.firstname
                 LIMIT 200";
        $availableusers = $DB->get_records_sql($sql, $searchparams);

        echo html_writer::start_tag('form', [
            'method' => 'post',
            'action' => new moodle_url('/local/jobboard/manage_committee.php'),
        ]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'create']);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'companyid', 'value' => $companyid]);

        // Committee name.
        echo html_writer::start_div('form-group');
        echo html_writer::tag('label', get_string('committeename', 'local_jobboard'), ['for' => 'name']);
        $defaultname = get_string('facultycommitteedefaultname', 'local_jobboard', format_string($company->name));
        echo html_writer::empty_tag('input', [
            'type' => 'text',
            'name' => 'name',
            'id' => 'name',
            'class' => 'form-control',
            'value' => $defaultname,
            'required' => 'required',
        ]);
        echo html_writer::end_div();

        // Chair (required).
        echo html_writer::start_div('form-group');
        echo html_writer::tag('label',
            '<i class="fa fa-user-tie text-danger mr-1"></i>' . get_string('role_chair', 'local_jobboard') .
            ' <span class="text-danger">*</span>',
            ['for' => 'chair']
        );
        echo html_writer::start_tag('select', [
            'name' => 'chair',
            'id' => 'chair',
            'class' => 'form-control',
            'required' => 'required',
        ]);
        echo html_writer::tag('option', get_string('selectuser', 'local_jobboard'), ['value' => '']);
        foreach ($availableusers as $user) {
            echo html_writer::tag('option',
                fullname($user) . ' (@' . $user->username . ') - ' . $user->email,
                ['value' => $user->id]
            );
        }
        echo html_writer::end_tag('select');
        echo html_writer::tag('small',
            get_string('chairhelp', 'local_jobboard'),
            ['class' => 'form-text text-muted']
        );
        echo html_writer::end_div();

        // Secretary (optional).
        echo html_writer::start_div('form-group');
        echo html_writer::tag('label',
            '<i class="fa fa-user-edit text-primary mr-1"></i>' . get_string('role_secretary', 'local_jobboard'),
            ['for' => 'secretary']
        );
        echo html_writer::start_tag('select', ['name' => 'secretary', 'id' => 'secretary', 'class' => 'form-control']);
        echo html_writer::tag('option', get_string('nosecretaryoptional', 'local_jobboard'), ['value' => '']);
        foreach ($availableusers as $user) {
            echo html_writer::tag('option',
                fullname($user) . ' (@' . $user->username . ') - ' . $user->email,
                ['value' => $user->id]
            );
        }
        echo html_writer::end_tag('select');
        echo html_writer::end_div();

        // Evaluators (optional multiple).
        echo html_writer::start_div('form-group');
        echo html_writer::tag('label',
            '<i class="fa fa-user-check text-success mr-1"></i>' . get_string('role_evaluator', 'local_jobboard') .
            ' (' . get_string('optional', 'local_jobboard') . ')',
            ['for' => 'evaluators']
        );
        echo html_writer::start_tag('select', [
            'name' => 'evaluators[]',
            'id' => 'evaluators',
            'class' => 'form-control',
            'multiple' => 'multiple',
            'size' => '5',
        ]);
        foreach ($availableusers as $user) {
            echo html_writer::tag('option',
                fullname($user) . ' (@' . $user->username . ') - ' . $user->email,
                ['value' => $user->id]
            );
        }
        echo html_writer::end_tag('select');
        echo html_writer::tag('small',
            get_string('evaluatorshelp', 'local_jobboard'),
            ['class' => 'form-text text-muted']
        );
        echo html_writer::end_div();

        // Info alert.
        echo html_writer::start_div('alert alert-info');
        echo html_writer::tag('i', '', ['class' => 'fa fa-info-circle mr-2']);
        echo get_string('committeeautoroleassign', 'local_jobboard');
        echo html_writer::end_div();

        echo html_writer::tag('button',
            '<i class="fa fa-check mr-2"></i>' . get_string('createcommittee', 'local_jobboard'),
            ['type' => 'submit', 'class' => 'btn btn-success btn-lg']
        );

        echo html_writer::end_tag('form');

        echo html_writer::end_div();
        echo html_writer::end_div();
    }
} else if ($vacancyid) {
    // Legacy support: Show committee for selected vacancy.
    $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid], '*', MUST_EXIST);

    // If vacancy has a company, redirect to company view.
    if (!empty($vacancy->companyid)) {
        redirect(new moodle_url('/local/jobboard/manage_committee.php', ['companyid' => $vacancy->companyid]));
    }

    $existingcommittee = committee::get_for_vacancy($vacancyid);

    // Back button.
    echo html_writer::link(
        new moodle_url('/local/jobboard/manage_committee.php'),
        '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtolist', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary btn-sm mb-3']
    );

    // Vacancy header with notice.
    echo html_writer::start_div('alert alert-warning');
    echo html_writer::tag('i', '', ['class' => 'fa fa-exclamation-triangle mr-2']);
    echo get_string('legacyvacancycommittee', 'local_jobboard');
    echo html_writer::end_div();

    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-primary text-white');
    echo html_writer::start_div('d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5',
        '<i class="fa fa-briefcase mr-2"></i>' . format_string($vacancy->code . ' - ' . $vacancy->title),
        ['class' => 'mb-0']
    );
    echo ui_helper::status_badge($vacancy->status, 'vacancy');
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    if ($existingcommittee) {
        // Show existing committee info only - suggest migration to faculty.
        echo html_writer::start_div('card shadow-sm mb-4');
        echo html_writer::start_div('card-body');
        echo html_writer::tag('p', get_string('existingvacancycommittee', 'local_jobboard', format_string($existingcommittee->name)));
        echo html_writer::tag('p', get_string('membercount', 'local_jobboard', count($existingcommittee->members)));
        echo html_writer::end_div();
        echo html_writer::end_div();
    } else {
        echo ui_helper::empty_state(get_string('nocommitteeforthisvacancy', 'local_jobboard'), 'users');
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
    new moodle_url('/local/jobboard/admin/roles.php'),
    '<i class="fa fa-user-tag mr-2"></i>' . get_string('manageroles', 'local_jobboard'),
    ['class' => 'btn btn-outline-primary m-1']
);

echo html_writer::link(
    new moodle_url('/local/jobboard/index.php', ['view' => 'review']),
    '<i class="fa fa-clipboard-check mr-2"></i>' . get_string('reviewapplications', 'local_jobboard'),
    ['class' => 'btn btn-outline-info m-1']
);

echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-committees

echo $OUTPUT->footer();

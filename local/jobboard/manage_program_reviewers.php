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
 * Manage program reviewers page.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_jobboard\program_reviewer;
use local_jobboard\output\ui_helper;

require_login();
$context = context_system::instance();
require_capability('local/jobboard:manageworkflow', $context);

$categoryid = optional_param('categoryid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$userid = optional_param('userid', 0, PARAM_INT);

$pageurl = new moodle_url('/local/jobboard/manage_program_reviewers.php');
$PAGE->set_context($context);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('programreviewers', 'local_jobboard'));
$PAGE->set_heading(get_string('programreviewers', 'local_jobboard'));
$PAGE->requires->css('/local/jobboard/styles.css');

// Handle actions.
if ($action && confirm_sesskey()) {
    switch ($action) {
        case 'add':
            if ($categoryid && $userid) {
                $role = optional_param('role', program_reviewer::ROLE_REVIEWER, PARAM_ALPHA);
                $result = program_reviewer::add($categoryid, $userid, $role);
                if ($result) {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('revieweradded', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
                } else {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('revieweradderror', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
                }
            }
            break;

        case 'remove':
            if ($categoryid && $userid) {
                $result = program_reviewer::remove($categoryid, $userid);
                if ($result) {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('reviewerremoved', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
                } else {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('reviewerremoveerror', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
                }
            }
            break;

        case 'changerole':
            if ($categoryid && $userid) {
                $newrole = optional_param('newrole', '', PARAM_ALPHA);
                $result = program_reviewer::update_role($categoryid, $userid, $newrole);
                if ($result) {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('rolechanged', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
                } else {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('rolechangeerror', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
                }
            }
            break;

        case 'togglestatus':
            if ($categoryid && $userid) {
                $result = program_reviewer::toggle_status($categoryid, $userid);
                if ($result) {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('statuschanged', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
                } else {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('statuschangeerror', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
                }
            }
            break;
    }
}

echo $OUTPUT->header();
echo html_writer::start_div('local-jobboard-program-reviewers');

// Specific program view.
if ($categoryid > 0) {
    $category = $DB->get_record('course_categories', ['id' => $categoryid], '*', MUST_EXIST);

    // Page header.
    echo ui_helper::page_header(
        get_string('programreviewers', 'local_jobboard') . ': ' . format_string($category->name),
        [],
        [
            [
                'url' => $pageurl,
                'label' => get_string('backtolist', 'local_jobboard'),
                'icon' => 'arrow-left',
                'class' => 'btn btn-outline-secondary',
            ],
        ]
    );

    // Get reviewers for this program.
    $reviewers = program_reviewer::get_for_program($categoryid, false);

    // Add reviewer form.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-success text-white');
    echo html_writer::tag('h5', '<i class="fa fa-user-plus mr-2"></i>' . get_string('addreviewer', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    // Get users with reviewer capability.
    $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.email
              FROM {user} u
              JOIN {role_assignments} ra ON ra.userid = u.id
              JOIN {role} r ON r.id = ra.roleid
             WHERE u.deleted = 0 AND u.suspended = 0
               AND (r.shortname = 'jobboard_reviewer' OR r.shortname = 'manager' OR r.shortname = 'admin')
          ORDER BY u.lastname, u.firstname";
    $potentialusers = $DB->get_records_sql($sql);

    // Filter out already assigned.
    $assignedids = array_column($reviewers, 'userid');
    $availableusers = array_filter($potentialusers, function($u) use ($assignedids) {
        return !in_array($u->id, $assignedids);
    });

    if (!empty($availableusers)) {
        $addurl = new moodle_url($pageurl, [
            'action' => 'add',
            'categoryid' => $categoryid,
            'sesskey' => sesskey(),
        ]);

        echo html_writer::start_tag('form', ['method' => 'post', 'action' => $addurl, 'class' => 'form-inline']);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'add']);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'categoryid', 'value' => $categoryid]);

        // User select.
        $useroptions = ['' => get_string('selectuser', 'local_jobboard')];
        foreach ($availableusers as $user) {
            $useroptions[$user->id] = fullname($user) . ' (' . $user->email . ')';
        }
        echo html_writer::select($useroptions, 'userid', '', false, ['class' => 'form-control mr-2', 'style' => 'min-width: 300px;']);

        // Role select.
        $roleoptions = [
            program_reviewer::ROLE_REVIEWER => get_string('role_reviewer', 'local_jobboard'),
            program_reviewer::ROLE_LEAD => get_string('role_lead_reviewer', 'local_jobboard'),
        ];
        echo html_writer::select($roleoptions, 'role', program_reviewer::ROLE_REVIEWER, false, ['class' => 'form-control mr-2']);

        echo html_writer::tag('button', '<i class="fa fa-plus mr-1"></i>' . get_string('add'),
            ['type' => 'submit', 'class' => 'btn btn-success']);
        echo html_writer::end_tag('form');
    } else {
        echo html_writer::tag('p', get_string('nousersavailable', 'local_jobboard'), ['class' => 'text-muted mb-0']);
    }

    echo html_writer::end_div();
    echo html_writer::end_div();

    // Current reviewers table.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-primary text-white');
    echo html_writer::tag('h5', '<i class="fa fa-users mr-2"></i>' . get_string('assignedreviewers', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    if (empty($reviewers)) {
        echo html_writer::tag('p', get_string('noreviewersforprogram', 'local_jobboard'), ['class' => 'text-muted']);
    } else {
        echo html_writer::start_div('table-responsive');
        echo html_writer::start_tag('table', ['class' => 'table table-hover']);
        echo html_writer::start_tag('thead', ['class' => 'thead-light']);
        echo html_writer::start_tag('tr');
        echo html_writer::tag('th', get_string('user'));
        echo html_writer::tag('th', get_string('email'));
        echo html_writer::tag('th', get_string('role'));
        echo html_writer::tag('th', get_string('status'));
        echo html_writer::tag('th', get_string('actions'), ['class' => 'text-center']);
        echo html_writer::end_tag('tr');
        echo html_writer::end_tag('thead');
        echo html_writer::start_tag('tbody');

        foreach ($reviewers as $reviewer) {
            $rowclass = $reviewer->status === program_reviewer::STATUS_INACTIVE ? 'table-secondary' : '';
            echo html_writer::start_tag('tr', ['class' => $rowclass]);

            // User.
            $userpic = $OUTPUT->user_picture((object)['id' => $reviewer->userid, 'picture' => $reviewer->picture,
                'firstname' => $reviewer->firstname, 'lastname' => $reviewer->lastname, 'imagealt' => $reviewer->imagealt],
                ['size' => 30, 'class' => 'mr-2']);
            echo html_writer::tag('td', $userpic . s(fullname($reviewer)));

            // Email.
            echo html_writer::tag('td', s($reviewer->email));

            // Role badge.
            $rolebadge = $reviewer->role === program_reviewer::ROLE_LEAD
                ? '<span class="badge badge-warning">' . get_string('role_lead_reviewer', 'local_jobboard') . '</span>'
                : '<span class="badge badge-info">' . get_string('role_reviewer', 'local_jobboard') . '</span>';
            echo html_writer::tag('td', $rolebadge);

            // Status badge.
            $statusbadge = $reviewer->status === program_reviewer::STATUS_ACTIVE
                ? '<span class="badge badge-success">' . get_string('active') . '</span>'
                : '<span class="badge badge-secondary">' . get_string('inactive', 'local_jobboard') . '</span>';
            echo html_writer::tag('td', $statusbadge);

            // Actions.
            echo html_writer::start_tag('td', ['class' => 'text-center']);

            // Change role button.
            $newrole = $reviewer->role === program_reviewer::ROLE_LEAD ? program_reviewer::ROLE_REVIEWER : program_reviewer::ROLE_LEAD;
            $changeroleurl = new moodle_url($pageurl, [
                'action' => 'changerole',
                'categoryid' => $categoryid,
                'userid' => $reviewer->userid,
                'newrole' => $newrole,
                'sesskey' => sesskey(),
            ]);
            echo html_writer::link($changeroleurl, '<i class="fa fa-exchange-alt"></i>',
                ['class' => 'btn btn-sm btn-outline-info mr-1', 'title' => get_string('changerole', 'local_jobboard')]);

            // Toggle status button.
            $toggleurl = new moodle_url($pageurl, [
                'action' => 'togglestatus',
                'categoryid' => $categoryid,
                'userid' => $reviewer->userid,
                'sesskey' => sesskey(),
            ]);
            $toggleicon = $reviewer->status === program_reviewer::STATUS_ACTIVE ? 'fa-toggle-on text-success' : 'fa-toggle-off';
            echo html_writer::link($toggleurl, '<i class="fa ' . $toggleicon . '"></i>',
                ['class' => 'btn btn-sm btn-outline-secondary mr-1',
                 'title' => $reviewer->status === program_reviewer::STATUS_ACTIVE ? get_string('deactivate', 'local_jobboard') : get_string('activate', 'local_jobboard')]);

            // Remove button.
            $removeurl = new moodle_url($pageurl, [
                'action' => 'remove',
                'categoryid' => $categoryid,
                'userid' => $reviewer->userid,
                'sesskey' => sesskey(),
            ]);
            echo html_writer::link($removeurl, '<i class="fa fa-trash"></i>',
                ['class' => 'btn btn-sm btn-outline-danger', 'title' => get_string('remove'),
                 'onclick' => "return confirm('" . get_string('confirmremovereviewer', 'local_jobboard') . "');"]);

            echo html_writer::end_tag('td');
            echo html_writer::end_tag('tr');
        }

        echo html_writer::end_tag('tbody');
        echo html_writer::end_tag('table');
        echo html_writer::end_div();
    }

    echo html_writer::end_div();
    echo html_writer::end_div();

} else {
    // Programs list view.
    echo ui_helper::page_header(
        get_string('programreviewers', 'local_jobboard'),
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

    // Statistics.
    $stats = program_reviewer::get_statistics();

    echo html_writer::start_div('row mb-4');

    // Total reviewers.
    echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
    echo html_writer::start_div('card bg-primary text-white h-100');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('h2', $stats['users_as_reviewers'], ['class' => 'display-4 mb-0']);
    echo html_writer::tag('p', get_string('totalreviewers', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Active.
    echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
    echo html_writer::start_div('card bg-success text-white h-100');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('h2', $stats['active'], ['class' => 'display-4 mb-0']);
    echo html_writer::tag('p', get_string('activereviewers', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Lead reviewers.
    echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
    echo html_writer::start_div('card bg-warning text-dark h-100');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('h2', $stats['lead_reviewers'], ['class' => 'display-4 mb-0']);
    echo html_writer::tag('p', get_string('leadreviewers', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Programs with reviewers.
    echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
    echo html_writer::start_div('card bg-info text-white h-100');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('h2', $stats['programs_with_reviewers'], ['class' => 'display-4 mb-0']);
    echo html_writer::tag('p', get_string('programswithreviewers', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div();

    // Programs with reviewers.
    $programsWithReviewers = program_reviewer::get_programs_with_reviewers();

    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-primary text-white');
    echo html_writer::tag('h5', '<i class="fa fa-graduation-cap mr-2"></i>' . get_string('programswithreviewers', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    if (empty($programsWithReviewers)) {
        echo html_writer::tag('p', get_string('noprogramswithreviewers', 'local_jobboard'), ['class' => 'text-muted']);
    } else {
        echo html_writer::start_div('table-responsive');
        echo html_writer::start_tag('table', ['class' => 'table table-hover']);
        echo html_writer::start_tag('thead', ['class' => 'thead-light']);
        echo html_writer::start_tag('tr');
        echo html_writer::tag('th', get_string('program', 'local_jobboard'));
        echo html_writer::tag('th', get_string('reviewers', 'local_jobboard'), ['class' => 'text-center']);
        echo html_writer::tag('th', get_string('leadreviewers', 'local_jobboard'), ['class' => 'text-center']);
        echo html_writer::tag('th', get_string('actions'), ['class' => 'text-center']);
        echo html_writer::end_tag('tr');
        echo html_writer::end_tag('thead');
        echo html_writer::start_tag('tbody');

        foreach ($programsWithReviewers as $program) {
            echo html_writer::start_tag('tr');
            echo html_writer::tag('td', '<i class="fa fa-folder mr-2 text-warning"></i>' . format_string($program->name));
            echo html_writer::tag('td', $program->reviewer_count, ['class' => 'text-center']);
            echo html_writer::tag('td', $program->lead_count, ['class' => 'text-center']);
            echo html_writer::start_tag('td', ['class' => 'text-center']);
            echo html_writer::link(new moodle_url($pageurl, ['categoryid' => $program->id]),
                '<i class="fa fa-users mr-1"></i>' . get_string('manage'),
                ['class' => 'btn btn-sm btn-primary']);
            echo html_writer::end_tag('td');
            echo html_writer::end_tag('tr');
        }

        echo html_writer::end_tag('tbody');
        echo html_writer::end_tag('table');
        echo html_writer::end_div();
    }

    echo html_writer::end_div();
    echo html_writer::end_div();

    // All programs (for adding reviewers).
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-secondary text-white');
    echo html_writer::tag('h5', '<i class="fa fa-plus-circle mr-2"></i>' . get_string('addreviewerstoprogram', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    // Get all programs.
    $allcategories = $DB->get_records('course_categories', [], 'sortorder', 'id, name, parent, depth');

    if (!empty($allcategories)) {
        echo html_writer::start_div('list-group');
        foreach ($allcategories as $cat) {
            $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $cat->depth);
            $icon = $cat->depth == 1 ? 'fa-building' : 'fa-graduation-cap';
            $hasReviewers = isset($programsWithReviewers[$cat->id]);
            $badge = $hasReviewers ? '<span class="badge badge-success ml-2">' . $programsWithReviewers[$cat->id]->reviewer_count . '</span>' : '';

            echo html_writer::link(
                new moodle_url($pageurl, ['categoryid' => $cat->id]),
                $indent . '<i class="fa ' . $icon . ' mr-2"></i>' . format_string($cat->name) . $badge,
                ['class' => 'list-group-item list-group-item-action']
            );
        }
        echo html_writer::end_div();
    }

    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Help section.
echo html_writer::start_div('card shadow-sm mb-4 bg-light');
echo html_writer::start_div('card-body');
echo html_writer::tag('h6', '<i class="fa fa-info-circle mr-2"></i>' . get_string('help'), ['class' => 'mb-3']);
echo html_writer::tag('p', get_string('programreviewerhelp', 'local_jobboard'), ['class' => 'text-muted mb-0']);
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div();
echo $OUTPUT->footer();

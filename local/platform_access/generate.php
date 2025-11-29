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
 * Platform Access Generator processing page.
 *
 * @package   local_platform_access
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Require login.
require_login();

// Check capability.
$context = context_system::instance();
require_capability('local/platform_access:generate', $context);

// Require sesskey.
require_sesskey();

// Get parameters.
$companyid = required_param('companyid', PARAM_INT);
$accesstype = required_param('accesstype', PARAM_ALPHA);
$cleanbeforegenerate = required_param('cleanbeforegenerate', PARAM_INT);
$datefrom = required_param('datefrom', PARAM_INT);
$dateto = required_param('dateto', PARAM_INT);
$loginsmin = required_param('loginsmin', PARAM_INT);
$loginsmax = required_param('loginsmax', PARAM_INT);
$courseaccessmin = required_param('courseaccessmin', PARAM_INT);
$courseaccessmax = required_param('courseaccessmax', PARAM_INT);
$activityaccessmin = required_param('activityaccessmin', PARAM_INT);
$activityaccessmax = required_param('activityaccessmax', PARAM_INT);
$randomize = required_param('randomize', PARAM_INT);
$includeadmins = required_param('includeadmins', PARAM_INT);
$onlyactive = required_param('onlyactive', PARAM_INT);
$updateusercreated = required_param('updateusercreated', PARAM_INT);
$usercreateddate = required_param('usercreateddate', PARAM_INT);
$generatedashboard = optional_param('generatedashboard', 1, PARAM_INT);
$generatelogouts = optional_param('generatelogouts', 0, PARAM_INT);
$generatecompletions = optional_param('generatecompletions', 0, PARAM_INT);
$completionpercentmin = optional_param('completionpercentmin', 50, PARAM_INT);
$completionpercentmax = optional_param('completionpercentmax', 100, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

// Page setup.
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/platform_access/generate.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_platform_access'));
$PAGE->set_heading(get_string('pluginname', 'local_platform_access'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('generating', 'local_platform_access'));

// Confirmation check.
if (!$confirm) {
    $confirmurl = new moodle_url('/local/platform_access/generate.php', [
        'companyid' => $companyid,
        'accesstype' => $accesstype,
        'cleanbeforegenerate' => $cleanbeforegenerate,
        'datefrom' => $datefrom,
        'dateto' => $dateto,
        'loginsmin' => $loginsmin,
        'loginsmax' => $loginsmax,
        'courseaccessmin' => $courseaccessmin,
        'courseaccessmax' => $courseaccessmax,
        'activityaccessmin' => $activityaccessmin,
        'activityaccessmax' => $activityaccessmax,
        'randomize' => $randomize,
        'includeadmins' => $includeadmins,
        'onlyactive' => $onlyactive,
        'updateusercreated' => $updateusercreated,
        'usercreateddate' => $usercreateddate,
        'generatedashboard' => $generatedashboard,
        'generatelogouts' => $generatelogouts,
        'generatecompletions' => $generatecompletions,
        'completionpercentmin' => $completionpercentmin,
        'completionpercentmax' => $completionpercentmax,
        'sesskey' => sesskey(),
        'confirm' => 1,
    ]);
    $cancelurl = new moodle_url('/local/platform_access/index.php');
    echo $OUTPUT->confirm(
        get_string('confirmgenerate', 'local_platform_access'),
        $confirmurl,
        $cancelurl
    );
    echo $OUTPUT->footer();
    die();
}

// Increase execution time limit.
core_php_time_limit::raise(0);
raise_memory_limit(MEMORY_HUGE);

// Create generator.
$options = [
    'companyid' => $companyid,
    'datefrom' => $datefrom,
    'dateto' => $dateto,
    'loginsmin' => $loginsmin,
    'loginsmax' => $loginsmax,
    'courseaccessmin' => $courseaccessmin,
    'courseaccessmax' => $courseaccessmax,
    'activityaccessmin' => $activityaccessmin,
    'activityaccessmax' => $activityaccessmax,
    'randomize' => (bool) $randomize,
    'includeadmins' => (bool) $includeadmins,
    'onlyactive' => (bool) $onlyactive,
    'updateusercreated' => (bool) $updateusercreated,
    'usercreateddate' => $usercreateddate,
    'cleanbeforegenerate' => (bool) $cleanbeforegenerate,
    'accesstype' => $accesstype,
    'generatedashboard' => (bool) $generatedashboard,
    'generatelogouts' => (bool) $generatelogouts,
    'generatecompletions' => (bool) $generatecompletions,
    'completionpercentmin' => $completionpercentmin,
    'completionpercentmax' => $completionpercentmax,
];

$generator = new \local_platform_access\generator($options);

// Progress bar.
$users = $generator->get_users();
$totalusers = count($users);

if ($totalusers == 0) {
    echo $OUTPUT->notification(get_string('nousers', 'local_platform_access'), 'warning');
    echo $OUTPUT->continue_button(new moodle_url('/local/platform_access/index.php'));
    echo $OUTPUT->footer();
    die();
}

$progressbar = new progress_bar('generateprogress', 500, true);

echo html_writer::start_tag('div', ['class' => 'progress-container']);

// Run generator with progress callback.
$currentuser = 0;
$progresscallback = function($user, $stats) use ($progressbar, $totalusers, &$currentuser) {
    $currentuser++;
    $progressbar->update($currentuser, $totalusers,
        get_string('processinguser', 'local_platform_access', fullname($user))
    );
};

$stats = $generator->run($progresscallback);

echo html_writer::end_tag('div');

// Display summary.
echo $OUTPUT->heading(get_string('summary', 'local_platform_access'), 3);

$table = new html_table();
$table->attributes['class'] = 'generaltable';
$table->head = [get_string('status'), get_string('value', 'scorm')];
$table->data = [
    [get_string('usersprocessed', 'local_platform_access'), $stats['users_processed']],
    [get_string('usersupdated', 'local_platform_access'), $stats['users_updated'] ?? 0],
    [get_string('userswithoutenrollments', 'local_platform_access'), $stats['users_without_enrollments'] ?? 0],
    [get_string('recordsdeleted', 'local_platform_access'), $stats['records_deleted'] ?? 0],
    [get_string('loginsgenerated', 'local_platform_access'), $stats['logins_generated']],
    [get_string('courseaccessgenerated', 'local_platform_access'), $stats['course_access_generated']],
    [get_string('activityaccessgenerated', 'local_platform_access'), $stats['activity_access_generated']],
    [get_string('dashboardaccessgenerated', 'local_platform_access'), $stats['dashboard_access_generated'] ?? 0],
    [get_string('logoutsgenerated', 'local_platform_access'), $stats['logouts_generated'] ?? 0],
    [get_string('completionsgenerated', 'local_platform_access'), $stats['completions_generated'] ?? 0],
    [get_string('lastaccessupdated', 'local_platform_access'), $stats['lastaccess_updated']],
    [get_string('timecompleted', 'local_platform_access'), $stats['time_elapsed'] ?? 0],
];

echo html_writer::table($table);

echo $OUTPUT->notification(get_string('success', 'local_platform_access'), 'success');

echo $OUTPUT->continue_button(new moodle_url('/local/platform_access/index.php'));

echo $OUTPUT->footer();

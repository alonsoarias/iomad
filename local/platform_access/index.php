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
 * Platform Access Generator main page.
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

// Page setup.
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/platform_access/index.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_platform_access'));
$PAGE->set_heading(get_string('pluginname', 'local_platform_access'));

// Create form.
$mform = new \local_platform_access\form\generate_form();

// Process form.
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/admin/index.php'));
} else if ($data = $mform->get_data()) {
    // Redirect to processing page.
    $params = [
        'companyid' => $data->companyid,
        'accesstype' => $data->accesstype,
        'datefrom' => $data->datefrom,
        'dateto' => $data->dateto,
        'loginsperuser' => $data->loginsperuser,
        'courseaccessperuser' => $data->courseaccessperuser,
        'activityaccesspercourse' => $data->activityaccesspercourse,
        'randomize' => $data->randomize,
        'includeadmins' => $data->includeadmins,
        'onlyactive' => $data->onlyactive,
        'sesskey' => sesskey(),
        'confirm' => 1,
    ];
    redirect(new moodle_url('/local/platform_access/generate.php', $params));
}

// Output.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('generateaccess', 'local_platform_access'));

// Display form.
$mform->display();

echo $OUTPUT->footer();

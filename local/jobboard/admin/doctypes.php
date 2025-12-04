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
 * Document types management page for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('local_jobboard_doctypes');

$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
$pageurl = new moodle_url('/local/jobboard/admin/doctypes.php');

// Handle actions.
if ($action === 'toggle' && $id && confirm_sesskey()) {
    $doctype = $DB->get_record('local_jobboard_doctype', ['id' => $id], '*', MUST_EXIST);
    $doctype->enabled = $doctype->enabled ? 0 : 1;
    $doctype->timemodified = time();
    $DB->update_record('local_jobboard_doctype', $doctype);
    redirect($pageurl, get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Get all document types.
$doctypes = $DB->get_records('local_jobboard_doctype', null, 'sortorder ASC, code ASC');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managedoctypes', 'local_jobboard'));

echo '<div class="alert alert-info">' . get_string('doctypeshelp', 'local_jobboard') . '</div>';

if (empty($doctypes)) {
    echo $OUTPUT->notification(get_string('nodoctypes', 'local_jobboard'), 'info');
} else {
    $table = new html_table();
    $table->head = [
        get_string('code', 'local_jobboard'),
        get_string('name'),
        get_string('category', 'local_jobboard'),
        get_string('required', 'local_jobboard'),
        get_string('status'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'generaltable';

    foreach ($doctypes as $dt) {
        $statusbadge = $dt->enabled
            ? '<span class="badge badge-success">' . get_string('enabled', 'local_jobboard') . '</span>'
            : '<span class="badge badge-secondary">' . get_string('disabled', 'local_jobboard') . '</span>';

        $requiredbadge = $dt->required
            ? '<span class="badge badge-primary">' . get_string('yes') . '</span>'
            : '<span class="badge badge-secondary">' . get_string('no') . '</span>';

        $toggleurl = new moodle_url($pageurl, ['action' => 'toggle', 'id' => $dt->id, 'sesskey' => sesskey()]);
        $togglelabel = $dt->enabled ? get_string('disable') : get_string('enable');
        $actions = html_writer::link($toggleurl, $togglelabel, ['class' => 'btn btn-sm btn-outline-secondary']);

        $name = get_string_manager()->string_exists('doctype_' . $dt->code, 'local_jobboard')
            ? get_string('doctype_' . $dt->code, 'local_jobboard')
            : $dt->code;

        $category = !empty($dt->category) && get_string_manager()->string_exists('doccategory_' . $dt->category, 'local_jobboard')
            ? get_string('doccategory_' . $dt->category, 'local_jobboard')
            : ($dt->category ?? '-');

        $table->data[] = [
            format_string($dt->code),
            $name,
            $category,
            $requiredbadge,
            $statusbadge,
            $actions,
        ];
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();

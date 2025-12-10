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
 * Migration tool for local_jobboard.
 *
 * Exports and imports plugin configuration and data between Moodle instances.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once(__DIR__ . '/lib.php');

use local_jobboard\output\ui_helper;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:configure', $context);

$action = optional_param('action', '', PARAM_ALPHA);

$PAGE->set_url(new moodle_url('/local/jobboard/migrate.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('migrateplugin', 'local_jobboard'));
$PAGE->set_heading(get_string('migrateplugin', 'local_jobboard'));
$PAGE->set_pagelayout('admin');
$PAGE->requires->css('/local/jobboard/styles.css');

/**
 * Export form class.
 */
class migrate_export_form extends moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'exporthdr', get_string('exportdata', 'local_jobboard'));

        // What to export.
        $mform->addElement('advcheckbox', 'export_doctypes', '', get_string('doctypes', 'local_jobboard'));
        $mform->setDefault('export_doctypes', 1);

        $mform->addElement('advcheckbox', 'export_emailtemplates', '', get_string('emailtemplates', 'local_jobboard'));
        $mform->setDefault('export_emailtemplates', 1);

        $mform->addElement('advcheckbox', 'export_convocatorias', '', get_string('convocatorias', 'local_jobboard'));
        $mform->setDefault('export_convocatorias', 1);

        $mform->addElement('advcheckbox', 'export_vacancies', '', get_string('vacancies', 'local_jobboard'));
        $mform->setDefault('export_vacancies', 1);

        $mform->addElement('advcheckbox', 'export_settings', '', get_string('pluginsettings', 'local_jobboard'));
        $mform->setDefault('export_settings', 1);

        $mform->addElement('advcheckbox', 'export_exemptions', '', get_string('exemptions', 'local_jobboard'));
        $mform->setDefault('export_exemptions', 0);

        $this->add_action_buttons(true, get_string('exportdownload', 'local_jobboard'));
    }
}

/**
 * Import form class.
 */
class migrate_import_form extends moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'importhdr', get_string('importdata', 'local_jobboard'));

        $mform->addElement('filepicker', 'importfile', get_string('migrationfile', 'local_jobboard'), null, [
            'accepted_types' => ['.json'],
            'maxbytes' => 10485760, // 10MB.
        ]);
        $mform->addRule('importfile', null, 'required');

        $mform->addElement('advcheckbox', 'overwrite', '', get_string('overwriteexisting', 'local_jobboard'));
        $mform->setDefault('overwrite', 0);

        $mform->addElement('advcheckbox', 'dryrun', '', get_string('dryrunmode', 'local_jobboard'));
        $mform->setDefault('dryrun', 1);

        $this->add_action_buttons(true, get_string('importupload', 'local_jobboard'));
    }
}

/**
 * Export plugin data to JSON.
 *
 * @param array $options Export options.
 * @return array Exported data.
 */
function local_jobboard_export_data(array $options): array {
    global $DB;

    $data = [
        'plugin' => 'local_jobboard',
        'version' => get_config('local_jobboard', 'version'),
        'exported' => time(),
        'export_date' => userdate(time()),
    ];

    // Export document types.
    if (!empty($options['export_doctypes'])) {
        $doctypes = $DB->get_records('local_jobboard_doctype', [], 'sortorder ASC');
        $data['doctypes'] = array_values($doctypes);
    }

    // Export email templates.
    if (!empty($options['export_emailtemplates'])) {
        $templates = $DB->get_records('local_jobboard_email_template', [], 'code ASC');
        $data['email_templates'] = array_values($templates);
    }

    // Export convocatorias.
    if (!empty($options['export_convocatorias'])) {
        $convocatorias = $DB->get_records('local_jobboard_convocatoria', [], 'code ASC');
        // Get exemptions for each convocatoria.
        foreach ($convocatorias as &$conv) {
            $conv->exemptions = $DB->get_records('local_jobboard_conv_docexempt', ['convocatoriaid' => $conv->id]);
        }
        $data['convocatorias'] = array_values($convocatorias);
    }

    // Export vacancies.
    if (!empty($options['export_vacancies'])) {
        $vacancies = $DB->get_records('local_jobboard_vacancy', [], 'code ASC');
        $data['vacancies'] = array_values($vacancies);
    }

    // Export plugin settings.
    if (!empty($options['export_settings'])) {
        $settings = [];
        $configkeys = [
            'enable_public_page', 'public_page_title', 'public_page_description',
            'require_consent', 'consent_text', 'terms_url',
            'allowed_doctypes', 'max_file_size', 'urgent_days_threshold',
            'notification_email', 'enable_email_notifications',
        ];
        foreach ($configkeys as $key) {
            $value = get_config('local_jobboard', $key);
            if ($value !== false) {
                $settings[$key] = $value;
            }
        }
        $data['settings'] = $settings;
    }

    // Export exemptions (user-level).
    if (!empty($options['export_exemptions'])) {
        $sql = "SELECT e.*, u.username, u.email
                  FROM {local_jobboard_exemption} e
                  JOIN {user} u ON u.id = e.userid
                 ORDER BY u.username";
        $exemptions = $DB->get_records_sql($sql);
        $data['exemptions'] = array_values($exemptions);
    }

    return $data;
}

/**
 * Import plugin data from JSON.
 *
 * @param array $data Import data.
 * @param bool $overwrite Overwrite existing records.
 * @param bool $dryrun Dry run mode (no changes).
 * @return array Import results.
 */
function local_jobboard_import_data(array $data, bool $overwrite = false, bool $dryrun = false): array {
    global $DB, $USER;

    $results = [
        'success' => true,
        'messages' => [],
        'counts' => [
            'doctypes' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0],
            'email_templates' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0],
            'convocatorias' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0],
            'vacancies' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0],
            'settings' => ['updated' => 0],
            'exemptions' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0],
        ],
    ];

    $transaction = $dryrun ? null : $DB->start_delegated_transaction();

    try {
        // Import document types.
        if (!empty($data['doctypes'])) {
            foreach ($data['doctypes'] as $doctype) {
                $doctype = (object) $doctype;
                unset($doctype->id);

                $existing = $DB->get_record('local_jobboard_doctype', ['code' => $doctype->code]);

                if ($existing) {
                    if ($overwrite) {
                        $doctype->id = $existing->id;
                        if (!$dryrun) {
                            $DB->update_record('local_jobboard_doctype', $doctype);
                        }
                        $results['counts']['doctypes']['updated']++;
                    } else {
                        $results['counts']['doctypes']['skipped']++;
                    }
                } else {
                    $doctype->timecreated = time();
                    if (!$dryrun) {
                        $DB->insert_record('local_jobboard_doctype', $doctype);
                    }
                    $results['counts']['doctypes']['inserted']++;
                }
            }
            $results['messages'][] = get_string('importeddoctypes', 'local_jobboard', $results['counts']['doctypes']);
        }

        // Import email templates.
        if (!empty($data['email_templates'])) {
            foreach ($data['email_templates'] as $template) {
                $template = (object) $template;
                unset($template->id);

                $existing = $DB->get_record('local_jobboard_email_template', [
                    'code' => $template->code,
                    'companyid' => $template->companyid ?? 0,
                ]);

                if ($existing) {
                    if ($overwrite) {
                        $template->id = $existing->id;
                        $template->timemodified = time();
                        if (!$dryrun) {
                            $DB->update_record('local_jobboard_email_template', $template);
                        }
                        $results['counts']['email_templates']['updated']++;
                    } else {
                        $results['counts']['email_templates']['skipped']++;
                    }
                } else {
                    $template->timecreated = time();
                    if (!$dryrun) {
                        $DB->insert_record('local_jobboard_email_template', $template);
                    }
                    $results['counts']['email_templates']['inserted']++;
                }
            }
            $results['messages'][] = get_string('importedemails', 'local_jobboard', $results['counts']['email_templates']);
        }

        // Import convocatorias.
        $convocatoriamap = []; // Old ID => New ID mapping.
        if (!empty($data['convocatorias'])) {
            foreach ($data['convocatorias'] as $conv) {
                $conv = (object) $conv;
                $oldid = $conv->id;
                $exemptions = $conv->exemptions ?? [];
                unset($conv->id, $conv->exemptions);

                $existing = $DB->get_record('local_jobboard_convocatoria', ['code' => $conv->code]);

                if ($existing) {
                    if ($overwrite) {
                        $conv->id = $existing->id;
                        $conv->timemodified = time();
                        $conv->modifiedby = $USER->id;
                        if (!$dryrun) {
                            $DB->update_record('local_jobboard_convocatoria', $conv);
                        }
                        $convocatoriamap[$oldid] = $existing->id;
                        $results['counts']['convocatorias']['updated']++;
                    } else {
                        $convocatoriamap[$oldid] = $existing->id;
                        $results['counts']['convocatorias']['skipped']++;
                    }
                } else {
                    $conv->timecreated = time();
                    $conv->createdby = $USER->id;
                    $conv->companyid = null; // Clear company ID for new instance.
                    if (!$dryrun) {
                        $newid = $DB->insert_record('local_jobboard_convocatoria', $conv);
                        $convocatoriamap[$oldid] = $newid;
                    }
                    $results['counts']['convocatorias']['inserted']++;
                }

                // Import convocatoria exemptions.
                if (!empty($exemptions) && !$dryrun && isset($convocatoriamap[$oldid])) {
                    foreach ($exemptions as $exempt) {
                        $exempt = (object) $exempt;
                        unset($exempt->id);
                        $exempt->convocatoriaid = $convocatoriamap[$oldid];

                        // Find doctype by code.
                        $doctype = $DB->get_record('local_jobboard_doctype', ['code' => $exempt->doctypeid]);
                        if ($doctype) {
                            $exempt->doctypeid = $doctype->id;
                            $exempt->createdby = $USER->id;
                            $exempt->timecreated = time();

                            $existingexempt = $DB->get_record('local_jobboard_conv_docexempt', [
                                'convocatoriaid' => $exempt->convocatoriaid,
                                'doctypeid' => $exempt->doctypeid,
                            ]);
                            if (!$existingexempt) {
                                $DB->insert_record('local_jobboard_conv_docexempt', $exempt);
                            }
                        }
                    }
                }
            }
            $results['messages'][] = get_string('importedconvocatorias', 'local_jobboard', $results['counts']['convocatorias']);
        }

        // Import vacancies.
        if (!empty($data['vacancies'])) {
            foreach ($data['vacancies'] as $vacancy) {
                $vacancy = (object) $vacancy;
                $oldconvid = $vacancy->convocatoriaid ?? null;
                unset($vacancy->id);

                $existing = $DB->get_record('local_jobboard_vacancy', ['code' => $vacancy->code]);

                if ($existing) {
                    if ($overwrite) {
                        $vacancy->id = $existing->id;
                        $vacancy->timemodified = time();
                        $vacancy->modifiedby = $USER->id;
                        // Map convocatoria ID.
                        if ($oldconvid && isset($convocatoriamap[$oldconvid])) {
                            $vacancy->convocatoriaid = $convocatoriamap[$oldconvid];
                        }
                        if (!$dryrun) {
                            $DB->update_record('local_jobboard_vacancy', $vacancy);
                        }
                        $results['counts']['vacancies']['updated']++;
                    } else {
                        $results['counts']['vacancies']['skipped']++;
                    }
                } else {
                    $vacancy->timecreated = time();
                    $vacancy->createdby = $USER->id;
                    $vacancy->companyid = null; // Clear for new instance.
                    // Map convocatoria ID.
                    if ($oldconvid && isset($convocatoriamap[$oldconvid])) {
                        $vacancy->convocatoriaid = $convocatoriamap[$oldconvid];
                    }
                    if (!$dryrun) {
                        $DB->insert_record('local_jobboard_vacancy', $vacancy);
                    }
                    $results['counts']['vacancies']['inserted']++;
                }
            }
            $results['messages'][] = get_string('importedvacancies', 'local_jobboard', $results['counts']['vacancies']);
        }

        // Import settings.
        if (!empty($data['settings'])) {
            foreach ($data['settings'] as $key => $value) {
                if (!$dryrun) {
                    set_config($key, $value, 'local_jobboard');
                }
                $results['counts']['settings']['updated']++;
            }
            $results['messages'][] = get_string('importedsettings', 'local_jobboard', $results['counts']['settings']['updated']);
        }

        // Import user exemptions.
        if (!empty($data['exemptions'])) {
            foreach ($data['exemptions'] as $exemption) {
                $exemption = (object) $exemption;
                unset($exemption->id);

                // Find user by username or email.
                $user = $DB->get_record('user', ['username' => $exemption->username]);
                if (!$user) {
                    $user = $DB->get_record('user', ['email' => $exemption->email]);
                }

                if ($user) {
                    unset($exemption->username, $exemption->email);
                    $exemption->userid = $user->id;

                    $existing = $DB->get_record('local_jobboard_exemption', [
                        'userid' => $user->id,
                        'exemptiontype' => $exemption->exemptiontype,
                    ]);

                    if ($existing) {
                        if ($overwrite) {
                            $exemption->id = $existing->id;
                            $exemption->timemodified = time();
                            if (!$dryrun) {
                                $DB->update_record('local_jobboard_exemption', $exemption);
                            }
                            $results['counts']['exemptions']['updated']++;
                        } else {
                            $results['counts']['exemptions']['skipped']++;
                        }
                    } else {
                        $exemption->timecreated = time();
                        $exemption->createdby = $USER->id;
                        if (!$dryrun) {
                            $DB->insert_record('local_jobboard_exemption', $exemption);
                        }
                        $results['counts']['exemptions']['inserted']++;
                    }
                } else {
                    $results['counts']['exemptions']['skipped']++;
                }
            }
            $results['messages'][] = get_string('importedexemptions', 'local_jobboard', $results['counts']['exemptions']);
        }

        if (!$dryrun && $transaction) {
            $transaction->allow_commit();
        }

        if ($dryrun) {
            array_unshift($results['messages'], get_string('dryrunresults', 'local_jobboard'));
        }

    } catch (Exception $e) {
        if ($transaction) {
            $transaction->rollback($e);
        }
        $results['success'] = false;
        $results['messages'][] = get_string('importerror', 'local_jobboard') . ': ' . $e->getMessage();
    }

    return $results;
}

// Handle export action.
if ($action === 'export') {
    $exportform = new migrate_export_form();

    if ($data = $exportform->get_data()) {
        $exportdata = local_jobboard_export_data((array) $data);

        $filename = 'jobboard_migration_' . date('Y-m-d_His') . '.json';
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo json_encode($exportdata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Handle import action.
if ($action === 'import') {
    $importform = new migrate_import_form();

    if ($data = $importform->get_data()) {
        $content = $importform->get_file_content('importfile');
        $importdata = json_decode($content, true);

        if ($importdata && isset($importdata['plugin']) && $importdata['plugin'] === 'local_jobboard') {
            $results = local_jobboard_import_data($importdata, !empty($data->overwrite), !empty($data->dryrun));

            if (!empty($data->dryrun)) {
                // Show dry run results.
                echo $OUTPUT->header();
                echo html_writer::tag('h3', get_string('dryrunresults', 'local_jobboard'));

                foreach ($results['messages'] as $msg) {
                    echo html_writer::tag('p', $msg);
                }

                echo html_writer::link(
                    new moodle_url('/local/jobboard/migrate.php'),
                    get_string('back'),
                    ['class' => 'btn btn-primary mt-3']
                );
                echo $OUTPUT->footer();
                exit;
            }

            $type = $results['success'] ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_ERROR;
            redirect(
                new moodle_url('/local/jobboard/migrate.php'),
                implode('<br>', $results['messages']),
                null,
                $type
            );
        } else {
            redirect(
                new moodle_url('/local/jobboard/migrate.php'),
                get_string('invalidmigrationfile', 'local_jobboard'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }
    }
}

// Display page.
echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-dashboard');

// Page header.
echo html_writer::start_div('jb-welcome-section bg-gradient-primary text-white rounded-lg p-4 mb-4');
echo html_writer::start_div('d-flex justify-content-between align-items-center');
echo html_writer::start_div();
echo html_writer::tag('h2', get_string('migrateplugin', 'local_jobboard'), ['class' => 'mb-1 font-weight-bold']);
echo html_writer::tag('p', get_string('migrateplugin_desc', 'local_jobboard'), ['class' => 'mb-0 opacity-75']);
echo html_writer::end_div();
echo html_writer::tag('i', '', ['class' => 'fa fa-exchange-alt fa-3x opacity-25']);
echo html_writer::end_div();
echo html_writer::end_div();

// Two column layout.
echo html_writer::start_div('row');

// Export Section.
echo html_writer::start_div('col-lg-6 mb-4');
echo html_writer::start_div('card shadow-sm h-100');
echo html_writer::start_div('card-header bg-primary text-white');
echo html_writer::tag('h5', '<i class="fa fa-download mr-2"></i>' . get_string('exportdata', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

echo html_writer::tag('p', get_string('exportdata_desc', 'local_jobboard'), ['class' => 'text-muted mb-3']);

$exportform = new migrate_export_form(new moodle_url('/local/jobboard/migrate.php', ['action' => 'export']));
$exportform->display();

echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

// Import Section.
echo html_writer::start_div('col-lg-6 mb-4');
echo html_writer::start_div('card shadow-sm h-100');
echo html_writer::start_div('card-header bg-success text-white');
echo html_writer::tag('h5', '<i class="fa fa-upload mr-2"></i>' . get_string('importdata', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

echo html_writer::tag('p', get_string('importdata_desc', 'local_jobboard'), ['class' => 'text-muted mb-3']);

// Warning box.
echo html_writer::start_div('alert alert-warning');
echo html_writer::tag('i', '', ['class' => 'fa fa-exclamation-triangle mr-2']);
echo get_string('importwarning', 'local_jobboard');
echo html_writer::end_div();

$importform = new migrate_import_form(new moodle_url('/local/jobboard/migrate.php', ['action' => 'import']));
$importform->display();

echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // row

// Back to dashboard.
echo html_writer::start_div('mt-3');
echo html_writer::link(
    new moodle_url('/local/jobboard/index.php'),
    '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtodashboard', 'local_jobboard'),
    ['class' => 'btn btn-outline-secondary']
);
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-dashboard

echo $OUTPUT->footer();

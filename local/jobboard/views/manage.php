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
 * Vacancy management view for local_jobboard.
 *
 * Modern redesign using ui_helper for consistent styling.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

use local_jobboard\output\ui_helper;

// Require manage capability.
require_capability('local/jobboard:createvacancy', $context);

// Parameters.
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 25, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$status = optional_param('status', '', PARAM_ALPHA);
$companyid = optional_param('companyid', 0, PARAM_INT);
$departmentid = optional_param('departmentid', 0, PARAM_INT);
$convocatoriaid = optional_param('convocatoriaid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$vacancyid = optional_param('id', 0, PARAM_INT);

// Check if IOMAD is installed.
$isiomad = local_jobboard_is_iomad_installed();

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('managevacancies', 'local_jobboard'));
$PAGE->set_heading(get_string('managevacancies', 'local_jobboard'));

// Handle actions that don't need sesskey (just redirects).
if ($action === 'create') {
    redirect(new moodle_url('/local/jobboard/edit.php'));
}

if ($action === 'edit' && $vacancyid) {
    redirect(new moodle_url('/local/jobboard/edit.php', ['id' => $vacancyid]));
}

// Handle actions that modify data (require sesskey).
if ($action && $vacancyid && in_array($action, ['publish', 'unpublish', 'close', 'reopen', 'delete'])) {
    require_sesskey();

    $vacancy = \local_jobboard\vacancy::get($vacancyid);

    if (!$vacancy) {
        throw new moodle_exception('error:vacancynotfound', 'local_jobboard');
    }

    $redirecturl = new moodle_url('/local/jobboard/index.php', ['view' => 'manage']);

    switch ($action) {
        case 'publish':
            require_capability('local/jobboard:publishvacancy', $context);
            try {
                $vacancy->publish();
                redirect($redirecturl, get_string('vacancypublished', 'local_jobboard'), null,
                    \core\output\notification::NOTIFY_SUCCESS);
            } catch (Exception $e) {
                redirect($redirecturl, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
            }
            break;

        case 'unpublish':
            require_capability('local/jobboard:editvacancy', $context);
            try {
                $vacancy->unpublish();
                redirect($redirecturl, get_string('vacancyunpublished', 'local_jobboard'), null,
                    \core\output\notification::NOTIFY_SUCCESS);
            } catch (Exception $e) {
                redirect($redirecturl, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
            }
            break;

        case 'close':
            require_capability('local/jobboard:editvacancy', $context);
            try {
                $vacancy->close();
                redirect($redirecturl, get_string('vacancyclosed', 'local_jobboard'), null,
                    \core\output\notification::NOTIFY_SUCCESS);
            } catch (Exception $e) {
                redirect($redirecturl, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
            }
            break;

        case 'reopen':
            require_capability('local/jobboard:publishvacancy', $context);
            try {
                $vacancy->reopen();
                redirect($redirecturl, get_string('vacancyreopened', 'local_jobboard'), null,
                    \core\output\notification::NOTIFY_SUCCESS);
            } catch (Exception $e) {
                redirect($redirecturl, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
            }
            break;

        case 'delete':
            require_capability('local/jobboard:deletevacancy', $context);
            try {
                if (!$vacancy->can_delete()) {
                    $reason = $vacancy->get_delete_restriction_reason();
                    throw new moodle_exception('error:cannotdelete', 'local_jobboard', '', $reason);
                }
                $vacancy->delete();
                redirect($redirecturl, get_string('vacancydeleted', 'local_jobboard'), null,
                    \core\output\notification::NOTIFY_SUCCESS);
            } catch (Exception $e) {
                redirect($redirecturl, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
            }
            break;
    }
}

// Handle bulk actions.
$bulkaction = optional_param('bulkaction', '', PARAM_ALPHA);
$selectedids = optional_param_array('selected', [], PARAM_INT);

if ($bulkaction && !empty($selectedids) && confirm_sesskey()) {
    $redirecturl = new moodle_url('/local/jobboard/index.php', ['view' => 'manage']);
    $successcount = 0;
    $errorcount = 0;

    foreach ($selectedids as $vid) {
        try {
            $v = \local_jobboard\vacancy::get($vid);
            if (!$v) {
                continue;
            }

            switch ($bulkaction) {
                case 'publish':
                    if (has_capability('local/jobboard:publishvacancy', $context) && $v->can_publish()) {
                        $v->publish();
                        $successcount++;
                    }
                    break;
                case 'unpublish':
                    if (has_capability('local/jobboard:editvacancy', $context) && $v->status === 'published') {
                        $v->unpublish();
                        $successcount++;
                    }
                    break;
                case 'close':
                    if (has_capability('local/jobboard:editvacancy', $context) && $v->status === 'published') {
                        $v->close();
                        $successcount++;
                    }
                    break;
                case 'delete':
                    if (has_capability('local/jobboard:deletevacancy', $context) && $v->can_delete()) {
                        $v->delete();
                        $successcount++;
                    }
                    break;
            }
        } catch (Exception $e) {
            $errorcount++;
        }
    }

    if ($successcount > 0) {
        $msgkey = 'items' . $bulkaction . 'ed';
        if ($bulkaction === 'publish') {
            $msgkey = 'itemspublished';
        } elseif ($bulkaction === 'delete') {
            $msgkey = 'itemsdeleted';
        } elseif ($bulkaction === 'close') {
            $msgkey = 'itemsclosed';
        }
        \core\notification::success(get_string($msgkey, 'local_jobboard', $successcount));
    }
    if ($errorcount > 0) {
        \core\notification::error(get_string('bulkactionerrors', 'local_jobboard', $errorcount));
    }
    redirect($redirecturl);
}

// Build filters.
$filters = [];

if ($search) {
    $filters['search'] = $search;
}

if ($status) {
    $filters['status'] = $status;
}

if ($companyid) {
    $filters['companyid'] = $companyid;
}

if ($departmentid) {
    $filters['departmentid'] = $departmentid;
}

if ($convocatoriaid) {
    $filters['convocatoriaid'] = $convocatoriaid;
}

// For non-admin users, filter by their company.
if (!has_capability('local/jobboard:viewallvacancies', $context)) {
    $usercompanyid = local_jobboard_get_user_companyid();
    if ($usercompanyid) {
        $filters['companyid'] = $usercompanyid;
    }
}

// Get vacancies.
$result = \local_jobboard\vacancy::get_list($filters, 'timecreated', 'DESC', $page, $perpage);
$vacancies = $result['vacancies'];
$total = $result['total'];

// Get convocatoria info if filtering.
$convocatoriainfo = null;
if ($convocatoriaid) {
    $convocatoriainfo = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);
}

// Prepare vacancy data with application counts.
$vacancydata = [];
$statscounts = ['draft' => 0, 'published' => 0, 'closed' => 0, 'assigned' => 0];
foreach ($vacancies as $v) {
    // Count by status.
    if (isset($statscounts[$v->status])) {
        $statscounts[$v->status]++;
    }
    // Add application count.
    $v->application_count = $v->get_application_count();
    // Add company name if applicable.
    $v->companyname = $v->companyid ? $v->get_company_name() : null;
    // Get dates.
    $v->opendate = $v->get_open_date();
    $v->closedate = $v->get_close_date();
    $vacancydata[] = $v;
}

// Build capabilities array for renderer.
$caps = [
    'editvacancy' => has_capability('local/jobboard:editvacancy', $context),
    'publishvacancy' => has_capability('local/jobboard:publishvacancy', $context),
    'deletevacancy' => has_capability('local/jobboard:deletevacancy', $context),
    'viewallapplications' => has_capability('local/jobboard:viewallapplications', $context),
];

// Build filter values array.
$filtervalues = [
    'search' => $search,
    'status' => $status,
    'companyid' => $companyid,
    'departmentid' => $departmentid,
    'convocatoriaid' => $convocatoriaid,
];

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data using the renderer.
$data = $renderer->prepare_manage_page_data(
    $vacancydata,
    $total,
    $statscounts,
    $filtervalues,
    $convocatoriainfo,
    $page,
    $perpage,
    sesskey(),
    $caps
);

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_manage_page($data);

// Add JavaScript for AJAX department loading (IOMAD).
if ($isiomad && has_capability('local/jobboard:viewallvacancies', $context)) {
    $allDepartmentsLabel = get_string('alldepartments', 'local_jobboard');
    $PAGE->requires->js_call_amd('local_jobboard/public_filters', 'init', [[
        'companySelector' => '#filter-companyid',
        'departmentSelector' => '#filter-departmentid',
        'preselect' => $departmentid,
        'allLabel' => $allDepartmentsLabel,
    ]]);
}

echo $OUTPUT->footer();

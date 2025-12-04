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
 * Public vacancy detail page.
 *
 * This page displays a single vacancy in detail.
 * Public vacancies are accessible without authentication.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/local/jobboard/lib.php');

// Check if public page is enabled.
$enablepublic = get_config('local_jobboard', 'enable_public_page');
if (!$enablepublic) {
    throw new moodle_exception('error:publicpagedisabled', 'local_jobboard');
}

// Required parameters.
$id = required_param('id', PARAM_INT);

// Setup page context - no login required.
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/jobboard/public/vacancy.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$PAGE->add_body_class('local-jobboard-public local-jobboard-vacancy-detail');

// Get the vacancy.
$vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $id]);

if (!$vacancy) {
    throw new moodle_exception('error:vacancynotfound', 'local_jobboard');
}

// Check if vacancy is published.
if ($vacancy->status !== 'published') {
    throw new moodle_exception('error:vacancynotfound', 'local_jobboard');
}

// Check if vacancy is still open.
if ($vacancy->closedate < time()) {
    throw new moodle_exception('error:vacancyclosed', 'local_jobboard');
}

// Check if user is logged in.
$isloggedin = isloggedin() && !isguestuser();

// Check publication type access.
if ($vacancy->publicationtype === 'internal' && !$isloggedin) {
    // Redirect to login for internal vacancies.
    $loginurl = new moodle_url('/login/index.php', [
        'wantsurl' => $PAGE->url->out(false),
    ]);
    redirect($loginurl, get_string('error:loginrequiredforinternal', 'local_jobboard'));
}

// For internal vacancies, verify user has capability.
if ($vacancy->publicationtype === 'internal' && $isloggedin) {
    if (!has_capability('local/jobboard:viewinternalvacancies', $context)) {
        throw new moodle_exception('error:noaccess', 'local_jobboard');
    }
}

// Check company filtering for logged-in users.
if ($isloggedin && !empty($vacancy->companyid)) {
    if (!has_capability('local/jobboard:viewallvacancies', $context)) {
        $usercompanyid = local_jobboard_get_user_companyid($USER->id);
        if ($usercompanyid && $usercompanyid !== (int)$vacancy->companyid) {
            throw new moodle_exception('error:noaccess', 'local_jobboard');
        }
    }
}

// Set page title.
$PAGE->set_title($vacancy->title);
$PAGE->set_heading($vacancy->title);

// Get contract types for display.
$contracttypes = local_jobboard_get_contract_types();

// Get document requirements for this vacancy.
$docrequirements = $DB->get_records('local_jobboard_vacancy_docreqs', ['vacancyid' => $id], 'sortorder ASC');

// Get document types for display.
$doctypes = $DB->get_records_menu('local_jobboard_doctype', null, 'name ASC', 'id, name');

// Calculate days remaining.
$daysremaining = max(0, (int) floor(($vacancy->closedate - time()) / 86400));
$isurgent = $daysremaining <= 7;

// Start output.
echo $OUTPUT->header();

// Back link.
echo html_writer::start_div('mb-3');
echo html_writer::link(
    new moodle_url('/local/jobboard/public/index.php'),
    html_writer::tag('i', '', ['class' => 'fa fa-arrow-left me-1']) . get_string('backtovacancies', 'local_jobboard'),
    ['class' => 'btn btn-outline-secondary btn-sm']
);
echo html_writer::end_div();

// Main content container.
echo html_writer::start_div('row');

// Left column - vacancy details.
echo html_writer::start_div('col-lg-8');

// Vacancy header card.
echo html_writer::start_div('card mb-4');
echo html_writer::start_div('card-body');

// Title and badges.
echo html_writer::start_div('d-flex justify-content-between align-items-start mb-3');
echo html_writer::start_div();
echo html_writer::tag('h1', s($vacancy->title), ['class' => 'h3 mb-2']);
echo html_writer::tag('span', s($vacancy->code), ['class' => 'badge bg-secondary me-2']);
$typebadge = $vacancy->publicationtype === 'public'
    ? html_writer::tag('span', get_string('publicationtype:public', 'local_jobboard'), ['class' => 'badge bg-success'])
    : html_writer::tag('span', get_string('publicationtype:internal', 'local_jobboard'), ['class' => 'badge bg-info']);
echo $typebadge;
echo html_writer::end_div();
echo html_writer::end_div();

// Key details row.
echo html_writer::start_div('row g-3 mb-4');

// Location.
if (!empty($vacancy->location)) {
    echo html_writer::start_div('col-md-4');
    echo html_writer::start_div('d-flex align-items-center');
    echo html_writer::tag('i', '', ['class' => 'fa fa-map-marker fa-lg text-primary me-2']);
    echo html_writer::start_div();
    echo html_writer::tag('small', get_string('location', 'local_jobboard'), ['class' => 'text-muted d-block']);
    echo html_writer::tag('span', s($vacancy->location), ['class' => 'fw-bold']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Contract type.
if (!empty($vacancy->contracttype) && isset($contracttypes[$vacancy->contracttype])) {
    echo html_writer::start_div('col-md-4');
    echo html_writer::start_div('d-flex align-items-center');
    echo html_writer::tag('i', '', ['class' => 'fa fa-briefcase fa-lg text-primary me-2']);
    echo html_writer::start_div();
    echo html_writer::tag('small', get_string('contracttype', 'local_jobboard'), ['class' => 'text-muted d-block']);
    echo html_writer::tag('span', $contracttypes[$vacancy->contracttype], ['class' => 'fw-bold']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Positions.
echo html_writer::start_div('col-md-4');
echo html_writer::start_div('d-flex align-items-center');
echo html_writer::tag('i', '', ['class' => 'fa fa-users fa-lg text-primary me-2']);
echo html_writer::start_div();
echo html_writer::tag('small', get_string('positions', 'local_jobboard'), ['class' => 'text-muted d-block']);
echo html_writer::tag('span', $vacancy->positions, ['class' => 'fw-bold']);
echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // .row

// Duration and salary if available.
if (!empty($vacancy->duration) || !empty($vacancy->salary)) {
    echo html_writer::start_div('row g-3 mb-4');

    if (!empty($vacancy->duration)) {
        echo html_writer::start_div('col-md-6');
        echo html_writer::start_div('d-flex align-items-center');
        echo html_writer::tag('i', '', ['class' => 'fa fa-clock-o fa-lg text-primary me-2']);
        echo html_writer::start_div();
        echo html_writer::tag('small', get_string('duration', 'local_jobboard'), ['class' => 'text-muted d-block']);
        echo html_writer::tag('span', s($vacancy->duration), ['class' => 'fw-bold']);
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    if (!empty($vacancy->salary)) {
        echo html_writer::start_div('col-md-6');
        echo html_writer::start_div('d-flex align-items-center');
        echo html_writer::tag('i', '', ['class' => 'fa fa-money fa-lg text-primary me-2']);
        echo html_writer::start_div();
        echo html_writer::tag('small', get_string('salary', 'local_jobboard'), ['class' => 'text-muted d-block']);
        echo html_writer::tag('span', s($vacancy->salary), ['class' => 'fw-bold']);
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    echo html_writer::end_div();
}

// Department if available.
if (!empty($vacancy->department)) {
    echo html_writer::tag('p',
        html_writer::tag('strong', get_string('department', 'local_jobboard') . ': ') . s($vacancy->department),
        ['class' => 'mb-2']
    );
}

echo html_writer::end_div(); // .card-body
echo html_writer::end_div(); // .card

// Description card.
echo html_writer::start_div('card mb-4');
echo html_writer::start_div('card-header');
echo html_writer::tag('h5', get_string('vacancydescription', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::end_div();
echo html_writer::start_div('card-body');
echo format_text($vacancy->description, FORMAT_HTML, ['trusted' => false, 'noclean' => false]);
echo html_writer::end_div();
echo html_writer::end_div();

// Requirements card.
if (!empty($vacancy->requirements)) {
    echo html_writer::start_div('card mb-4');
    echo html_writer::start_div('card-header');
    echo html_writer::tag('h5', get_string('requirements', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo format_text($vacancy->requirements, FORMAT_HTML, ['trusted' => false, 'noclean' => false]);
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Desirable requirements card.
if (!empty($vacancy->desirable)) {
    echo html_writer::start_div('card mb-4');
    echo html_writer::start_div('card-header');
    echo html_writer::tag('h5', get_string('desirable', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo format_text($vacancy->desirable, FORMAT_HTML, ['trusted' => false, 'noclean' => false]);
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Required documents card.
if (!empty($docrequirements)) {
    echo html_writer::start_div('card mb-4');
    echo html_writer::start_div('card-header');
    echo html_writer::tag('h5', get_string('requireddocuments', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo html_writer::start_tag('ul', ['class' => 'list-group list-group-flush']);

    foreach ($docrequirements as $req) {
        $doctypename = isset($doctypes[$req->doctypeid]) ? $doctypes[$req->doctypeid] : get_string('unknown');
        $required = $req->isrequired ? html_writer::tag('span', get_string('required', 'local_jobboard'), ['class' => 'badge bg-danger ms-2'])
                                     : html_writer::tag('span', get_string('optional', 'local_jobboard'), ['class' => 'badge bg-secondary ms-2']);

        echo html_writer::tag('li', $doctypename . $required, ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
    }

    echo html_writer::end_tag('ul');
    echo html_writer::end_div();
    echo html_writer::end_div();
}

echo html_writer::end_div(); // .col-lg-8

// Right column - sidebar.
echo html_writer::start_div('col-lg-4');

// Apply card.
echo html_writer::start_div('card mb-4' . ($isurgent ? ' border-warning' : ''));
echo html_writer::start_div('card-body text-center');

// Closing date.
$closingclass = $isurgent ? 'text-warning' : 'text-muted';
$closingicon = $isurgent ? 'fa-exclamation-triangle' : 'fa-calendar';
echo html_writer::tag('p',
    html_writer::tag('i', '', ['class' => "fa $closingicon me-1"]) .
    get_string('closeson', 'local_jobboard') . ': ' .
    html_writer::tag('strong', userdate($vacancy->closedate, '%d/%m/%Y')),
    ['class' => 'mb-2 ' . $closingclass]
);

if ($isurgent) {
    echo html_writer::tag('p',
        html_writer::tag('strong', get_string('closesin', 'local_jobboard', $daysremaining)),
        ['class' => 'text-warning mb-3']
    );
} else {
    echo html_writer::tag('p',
        get_string('closesin', 'local_jobboard', $daysremaining),
        ['class' => 'text-muted mb-3']
    );
}

// Apply button.
if ($isloggedin) {
    if (has_capability('local/jobboard:apply', $context)) {
        // Check if user already applied.
        $existingapp = $DB->get_record('local_jobboard_application', [
            'vacancyid' => $id,
            'userid' => $USER->id,
        ], 'id, status');

        if ($existingapp) {
            $statuslabels = local_jobboard_get_application_statuses();
            echo html_writer::tag('p',
                get_string('alreadyapplied', 'local_jobboard'),
                ['class' => 'text-info mb-2']
            );
            echo html_writer::tag('p',
                get_string('applicationstatus', 'local_jobboard') . ': ' .
                html_writer::tag('strong', $statuslabels[$existingapp->status] ?? $existingapp->status),
                ['class' => 'mb-3']
            );
            echo html_writer::link(
                new moodle_url('/local/jobboard/applications.php'),
                get_string('viewmyapplications', 'local_jobboard'),
                ['class' => 'btn btn-outline-primary']
            );
        } else {
            // Check application limit.
            $allowmultiple = get_config('local_jobboard', 'allow_multiple_applications');
            $maxapps = (int) get_config('local_jobboard', 'max_active_applications');
            $canApply = true;
            $limitMessage = '';

            if (!has_capability('local/jobboard:unlimitedapplications', $context)) {
                if (!$allowmultiple) {
                    // Check if user has any active application.
                    $activecount = $DB->count_records_sql(
                        "SELECT COUNT(*) FROM {local_jobboard_application}
                         WHERE userid = :userid AND status NOT IN ('rejected', 'withdrawn', 'selected')",
                        ['userid' => $USER->id]
                    );
                    if ($activecount > 0) {
                        $canApply = false;
                        $limitMessage = get_string('error:multipleapplicationsnotallowed', 'local_jobboard');
                    }
                } elseif ($maxapps > 0) {
                    // Check against max active applications.
                    $activecount = $DB->count_records_sql(
                        "SELECT COUNT(*) FROM {local_jobboard_application}
                         WHERE userid = :userid AND status NOT IN ('rejected', 'withdrawn', 'selected')",
                        ['userid' => $USER->id]
                    );
                    if ($activecount >= $maxapps) {
                        $canApply = false;
                        $limitMessage = get_string('error:applicationlimitreached', 'local_jobboard', $maxapps);
                    }
                }
            }

            if ($canApply) {
                echo html_writer::link(
                    new moodle_url('/local/jobboard/apply.php', ['id' => $id]),
                    get_string('applynow', 'local_jobboard'),
                    ['class' => 'btn btn-primary btn-lg']
                );
            } else {
                echo html_writer::tag('p', $limitMessage, ['class' => 'text-warning mb-3']);
                echo html_writer::link(
                    new moodle_url('/local/jobboard/applications.php'),
                    get_string('viewmyapplications', 'local_jobboard'),
                    ['class' => 'btn btn-outline-secondary']
                );
            }
        }
    } else {
        echo html_writer::tag('p',
            get_string('noapplypermission', 'local_jobboard'),
            ['class' => 'text-muted']
        );
    }
} else {
    // Not logged in - show login/register buttons.
    echo html_writer::tag('p',
        get_string('loginrequiredtoapply', 'local_jobboard'),
        ['class' => 'text-muted mb-3']
    );
    echo html_writer::link(
        new moodle_url('/login/index.php', ['wantsurl' => (new moodle_url('/local/jobboard/apply.php', ['id' => $id]))->out(false)]),
        get_string('loginandapply', 'local_jobboard'),
        ['class' => 'btn btn-primary mb-2 d-block']
    );
    echo html_writer::link(
        new moodle_url('/login/signup.php'),
        get_string('createaccount'),
        ['class' => 'btn btn-outline-secondary d-block']
    );
}

echo html_writer::end_div(); // .card-body
echo html_writer::end_div(); // .card

// Important dates card.
echo html_writer::start_div('card mb-4');
echo html_writer::start_div('card-header');
echo html_writer::tag('h6', get_string('importantdates', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::end_div();
echo html_writer::start_tag('ul', ['class' => 'list-group list-group-flush']);

// Opening date.
echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between']);
echo html_writer::tag('span', get_string('opendate', 'local_jobboard'));
echo html_writer::tag('strong', userdate($vacancy->opendate, '%d/%m/%Y'));
echo html_writer::end_tag('li');

// Closing date.
echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between' . ($isurgent ? ' list-group-item-warning' : '')]);
echo html_writer::tag('span', get_string('closedate', 'local_jobboard'));
echo html_writer::tag('strong', userdate($vacancy->closedate, '%d/%m/%Y'));
echo html_writer::end_tag('li');

echo html_writer::end_tag('ul');
echo html_writer::end_div();

// Share card.
echo html_writer::start_div('card mb-4');
echo html_writer::start_div('card-header');
echo html_writer::tag('h6', get_string('sharethisvacancy', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

$shareurl = $PAGE->url->out(false);

// Copy link button.
echo html_writer::start_div('input-group mb-3');
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'class' => 'form-control form-control-sm',
    'value' => $shareurl,
    'readonly' => 'readonly',
    'id' => 'vacancy-share-url',
]);
echo html_writer::tag('button',
    html_writer::tag('i', '', ['class' => 'fa fa-copy']),
    [
        'class' => 'btn btn-outline-secondary btn-sm',
        'type' => 'button',
        'onclick' => 'navigator.clipboard.writeText(document.getElementById("vacancy-share-url").value);',
        'title' => get_string('copylink', 'local_jobboard'),
    ]
);
echo html_writer::end_div();

// Social share buttons.
echo html_writer::start_div('d-flex gap-2');
$encodedurl = urlencode($shareurl);
$encodedtitle = urlencode($vacancy->title);

// LinkedIn.
echo html_writer::link(
    "https://www.linkedin.com/sharing/share-offsite/?url={$encodedurl}",
    html_writer::tag('i', '', ['class' => 'fa fa-linkedin']),
    ['class' => 'btn btn-sm btn-outline-primary', 'target' => '_blank', 'rel' => 'noopener']
);

// Twitter/X.
echo html_writer::link(
    "https://twitter.com/intent/tweet?url={$encodedurl}&text={$encodedtitle}",
    html_writer::tag('i', '', ['class' => 'fa fa-twitter']),
    ['class' => 'btn btn-sm btn-outline-info', 'target' => '_blank', 'rel' => 'noopener']
);

// WhatsApp.
echo html_writer::link(
    "https://wa.me/?text={$encodedtitle}%20{$encodedurl}",
    html_writer::tag('i', '', ['class' => 'fa fa-whatsapp']),
    ['class' => 'btn btn-sm btn-outline-success', 'target' => '_blank', 'rel' => 'noopener']
);

// Email.
echo html_writer::link(
    "mailto:?subject={$encodedtitle}&body={$encodedurl}",
    html_writer::tag('i', '', ['class' => 'fa fa-envelope']),
    ['class' => 'btn btn-sm btn-outline-secondary']
);

echo html_writer::end_div();
echo html_writer::end_div(); // .card-body
echo html_writer::end_div(); // .card

echo html_writer::end_div(); // .col-lg-4

echo html_writer::end_div(); // .row

echo $OUTPUT->footer();

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
 * Public vacancy detail view for local_jobboard.
 *
 * This view shows the full details of a vacancy to public/non-authenticated users.
 * It follows the same design as vacancy.php but adapted for public access.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

use local_jobboard\output\ui_helper;

// Check if public page is enabled.
$enablepublic = get_config('local_jobboard', 'enable_public_page');
if (!$enablepublic) {
    throw new moodle_exception('error:publicpagedisabled', 'local_jobboard');
}

// Get vacancy ID.
$id = required_param('id', PARAM_INT);

// Get the vacancy.
$vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $id]);

if (!$vacancy || $vacancy->status !== 'published') {
    throw new moodle_exception('error:vacancynotfound', 'local_jobboard');
}

// Check if user is logged in.
$isloggedin = isloggedin() && !isguestuser();

// User capabilities.
$canviewinternal = $isloggedin && has_capability('local/jobboard:viewinternalvacancies', $context);
$canapply = $isloggedin && has_capability('local/jobboard:apply', $context);

// Check if vacancy is viewable (public or user has internal view capability).
if ($vacancy->publicationtype !== 'public' && !$canviewinternal) {
    throw new moodle_exception('error:vacancynotpublic', 'local_jobboard');
}

// Get convocatoria info.
$convocatoria = null;
if ($vacancy->convocatoriaid) {
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $vacancy->convocatoriaid]);
}

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title($vacancy->title);
$PAGE->set_heading($vacancy->title);
$PAGE->requires->css('/local/jobboard/styles.css');

// Get contract types for display.
$contracttypes = local_jobboard_get_contract_types();

// Calculate days remaining.
$closedate = $convocatoria ? $convocatoria->enddate : ($vacancy->closedate ?? time());
$daysRemaining = max(0, (int) floor(($closedate - time()) / 86400));
$isUrgent = $daysRemaining <= 7;
$isClosed = ($closedate < time());

// Check if user has already applied.
$hasApplied = false;
$userApplication = null;
if ($isloggedin) {
    $userApplication = $DB->get_record('local_jobboard_application', [
        'vacancyid' => $vacancy->id,
        'userid' => $USER->id,
    ]);
    $hasApplied = !empty($userApplication);
}

echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-vacancy-detail');

// ============================================================================
// PAGE HEADER (Breadcrumbs)
// ============================================================================
$breadcrumbs = [
    get_string('publicpagetitle', 'local_jobboard') => new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
];
if ($convocatoria) {
    $breadcrumbs[s($convocatoria->name)] = new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $convocatoria->id]);
}
$breadcrumbs[s($vacancy->title)] = null;

$headerActions = [];
if (!$isClosed && $canapply && !$hasApplied) {
    $headerActions[] = [
        'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
        'label' => get_string('apply', 'local_jobboard'),
        'icon' => 'paper-plane',
        'class' => 'btn btn-success btn-lg',
    ];
}

echo ui_helper::page_header(
    s($vacancy->title),
    $breadcrumbs,
    $headerActions
);

// ============================================================================
// STATUS BANNER
// ============================================================================
$bannerClass = 'alert d-flex justify-content-between align-items-center mb-4';
$bannerIcon = 'info-circle';

if ($hasApplied) {
    $bannerClass .= ' alert-info';
    $bannerMessage = get_string('alreadyapplied', 'local_jobboard');
    $bannerIcon = 'check-circle';
} elseif ($isClosed) {
    $bannerClass .= ' alert-secondary';
    $bannerMessage = get_string('vacancyclosed', 'local_jobboard');
    $bannerIcon = 'lock';
} elseif ($isUrgent) {
    $bannerClass .= ' alert-warning';
    $bannerMessage = get_string('closesindays', 'local_jobboard', $daysRemaining);
    $bannerIcon = 'clock';
} else {
    $bannerClass .= ' alert-success';
    $bannerMessage = get_string('vacancyopen', 'local_jobboard');
    $bannerIcon = 'door-open';
}

echo html_writer::start_div($bannerClass);
echo html_writer::tag('span',
    '<i class="fa fa-' . $bannerIcon . ' mr-2"></i>' . $bannerMessage,
    ['class' => 'font-weight-medium']
);
echo html_writer::start_div();
echo ui_helper::status_badge($vacancy->status, 'vacancy');
echo html_writer::tag('code', ' ' . s($vacancy->code), ['class' => 'ml-2']);
echo html_writer::end_div();
echo html_writer::end_div();

// ============================================================================
// MAIN CONTENT
// ============================================================================
echo html_writer::start_div('row');

// Left column - Main content.
echo html_writer::start_div('col-lg-8');

// Description card.
if (!empty($vacancy->description)) {
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-file-alt text-primary mr-2"></i>' . get_string('vacancydescription', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo format_text($vacancy->description, FORMAT_HTML, ['context' => $context]);
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Requirements card.
if (!empty($vacancy->requirements)) {
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-list-check text-warning mr-2"></i>' . get_string('requirements', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo format_text($vacancy->requirements, FORMAT_HTML, ['context' => $context]);
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Desirable card.
if (!empty($vacancy->desirable)) {
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-star text-info mr-2"></i>' . get_string('desirable', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo format_text($vacancy->desirable, FORMAT_HTML, ['context' => $context]);
    echo html_writer::end_div();
    echo html_writer::end_div();
}

echo html_writer::end_div(); // col-lg-8

// Right column - Sidebar.
echo html_writer::start_div('col-lg-4');

// Key details card.
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::start_div('card-header bg-primary text-white');
echo html_writer::tag('h5',
    '<i class="fa fa-info-circle mr-2"></i>' . get_string('details', 'local_jobboard'),
    ['class' => 'mb-0']
);
echo html_writer::end_div();
echo html_writer::start_div('card-body p-0');

echo html_writer::start_tag('ul', ['class' => 'list-group list-group-flush']);

// Contract type.
if (!empty($vacancy->contracttype)) {
    echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
    echo html_writer::tag('span', '<i class="fa fa-file-contract text-muted mr-2"></i>' . get_string('contracttype', 'local_jobboard'));
    echo html_writer::tag('strong', $contracttypes[$vacancy->contracttype] ?? $vacancy->contracttype);
    echo html_writer::end_tag('li');
}

// Duration.
if (!empty($vacancy->duration)) {
    echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
    echo html_writer::tag('span', '<i class="fa fa-hourglass-half text-muted mr-2"></i>' . get_string('duration', 'local_jobboard'));
    echo html_writer::tag('strong', s($vacancy->duration));
    echo html_writer::end_tag('li');
}

// Location.
if (!empty($vacancy->location)) {
    echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
    echo html_writer::tag('span', '<i class="fa fa-map-marker-alt text-muted mr-2"></i>' . get_string('location', 'local_jobboard'));
    echo html_writer::tag('strong', s($vacancy->location));
    echo html_writer::end_tag('li');
}

// Department.
if (!empty($vacancy->department)) {
    echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
    echo html_writer::tag('span', '<i class="fa fa-building text-muted mr-2"></i>' . get_string('department', 'local_jobboard'));
    echo html_writer::tag('strong', s($vacancy->department));
    echo html_writer::end_tag('li');
}

// Company (Iomad).
if (!empty($vacancy->companyid)) {
    $companyName = local_jobboard_get_company_name($vacancy->companyid);
    if (!empty($companyName)) {
        echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
        echo html_writer::tag('span', '<i class="fa fa-industry text-muted mr-2"></i>' . get_string('company', 'local_jobboard'));
        echo html_writer::tag('strong', s($companyName));
        echo html_writer::end_tag('li');
    }
}

// Positions.
echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
echo html_writer::tag('span', '<i class="fa fa-users text-muted mr-2"></i>' . get_string('positions', 'local_jobboard'));
echo html_writer::tag('span', $vacancy->positions, ['class' => 'badge badge-primary badge-pill']);
echo html_writer::end_tag('li');

// Convocatoria.
if ($convocatoria) {
    echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
    echo html_writer::tag('span', '<i class="fa fa-folder-open text-muted mr-2"></i>' . get_string('convocatoria', 'local_jobboard'));
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public_convocatoria', 'id' => $convocatoria->id]),
        s($convocatoria->code),
        ['class' => 'badge badge-info']
    );
    echo html_writer::end_tag('li');
}

echo html_writer::end_tag('ul');
echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

// Dates card.
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::start_div('card-header bg-white');
echo html_writer::tag('h5',
    '<i class="fa fa-calendar-alt text-primary mr-2"></i>' . get_string('dates', 'local_jobboard'),
    ['class' => 'mb-0']
);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

// Opening date.
if ($convocatoria) {
    echo html_writer::start_div('d-flex justify-content-between mb-2');
    echo html_writer::tag('span', get_string('startdate', 'local_jobboard'), ['class' => 'text-muted']);
    echo html_writer::tag('strong', userdate($convocatoria->startdate, get_string('strftimedate', 'langconfig')));
    echo html_writer::end_div();
}

// Closing date.
$closeDateClass = $isUrgent ? 'text-warning' : ($isClosed ? 'text-secondary' : 'text-success');
echo html_writer::start_div('d-flex justify-content-between mb-2');
echo html_writer::tag('span', get_string('closedate', 'local_jobboard'), ['class' => 'text-muted']);
echo html_writer::start_div();
echo html_writer::tag('strong', userdate($closedate, get_string('strftimedate', 'langconfig')), ['class' => $closeDateClass]);
if (!$isClosed && $daysRemaining >= 0) {
    echo html_writer::tag('span', ' (' . $daysRemaining . ' ' . get_string('days', 'local_jobboard') . ')',
        ['class' => 'small ' . $closeDateClass]);
}
echo html_writer::end_div();
echo html_writer::end_div();

// Progress bar for deadline.
if (!$isClosed && $convocatoria) {
    $totalDays = max(1, (int) floor(($convocatoria->enddate - $convocatoria->startdate) / 86400));
    $elapsedDays = max(0, (int) floor((time() - $convocatoria->startdate) / 86400));
    $progress = min(100, ($elapsedDays / $totalDays) * 100);
    $progressClass = $progress > 80 ? 'bg-danger' : ($progress > 50 ? 'bg-warning' : 'bg-success');

    echo html_writer::start_div('progress mt-3', ['style' => 'height: 8px;']);
    echo html_writer::div('', 'progress-bar ' . $progressClass, [
        'role' => 'progressbar',
        'style' => 'width: ' . $progress . '%',
        'aria-valuenow' => $progress,
        'aria-valuemin' => '0',
        'aria-valuemax' => '100',
    ]);
    echo html_writer::end_div();
    echo html_writer::tag('small', get_string('deadlineprogress', 'local_jobboard'), ['class' => 'text-muted']);
}

echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

// Share card (social networks).
$shareUrl = (new moodle_url('/local/jobboard/index.php', ['view' => 'public_vacancy', 'id' => $vacancy->id]))->out(false);
$shareTitle = rawurlencode($vacancy->title);
$shareText = rawurlencode(get_string('sharethisvacancy', 'local_jobboard') . ': ' . $vacancy->title);

echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::start_div('card-header bg-white');
echo html_writer::tag('h5',
    '<i class="fa fa-share-alt text-primary mr-2"></i>' . get_string('share', 'local_jobboard'),
    ['class' => 'mb-0']
);
echo html_writer::end_div();
echo html_writer::start_div('card-body text-center');

// Facebook.
$fbUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl);
echo html_writer::link($fbUrl,
    '<i class="fab fa-facebook-f"></i>',
    [
        'class' => 'btn btn-outline-primary rounded-circle mx-1',
        'style' => 'width: 45px; height: 45px; line-height: 32px;',
        'target' => '_blank',
        'title' => get_string('shareonfacebook', 'local_jobboard'),
        'rel' => 'noopener noreferrer',
    ]
);

// Twitter/X.
$twUrl = 'https://twitter.com/intent/tweet?text=' . $shareText . '&url=' . rawurlencode($shareUrl);
echo html_writer::link($twUrl,
    '<i class="fab fa-twitter"></i>',
    [
        'class' => 'btn btn-outline-info rounded-circle mx-1',
        'style' => 'width: 45px; height: 45px; line-height: 32px;',
        'target' => '_blank',
        'title' => get_string('shareontwitter', 'local_jobboard'),
        'rel' => 'noopener noreferrer',
    ]
);

// LinkedIn.
$liUrl = 'https://www.linkedin.com/shareArticle?mini=true&url=' . rawurlencode($shareUrl) . '&title=' . $shareTitle;
echo html_writer::link($liUrl,
    '<i class="fab fa-linkedin-in"></i>',
    [
        'class' => 'btn btn-outline-primary rounded-circle mx-1',
        'style' => 'width: 45px; height: 45px; line-height: 32px;',
        'target' => '_blank',
        'title' => get_string('shareonlinkedin', 'local_jobboard'),
        'rel' => 'noopener noreferrer',
    ]
);

// WhatsApp.
$waUrl = 'https://wa.me/?text=' . $shareText . '%20' . rawurlencode($shareUrl);
echo html_writer::link($waUrl,
    '<i class="fab fa-whatsapp"></i>',
    [
        'class' => 'btn btn-outline-success rounded-circle mx-1',
        'style' => 'width: 45px; height: 45px; line-height: 32px;',
        'target' => '_blank',
        'title' => 'WhatsApp',
        'rel' => 'noopener noreferrer',
    ]
);

echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

// Apply CTA card.
if (!$isClosed && !$hasApplied) {
    if ($canapply) {
        echo html_writer::start_div('card shadow-sm mb-4 border-success');
        echo html_writer::start_div('card-body text-center');
        echo html_writer::tag('h5', get_string('readytoapply', 'local_jobboard'), ['class' => 'card-title']);
        echo html_writer::tag('p', get_string('applynow_desc', 'local_jobboard'), ['class' => 'card-text text-muted']);
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
            '<i class="fa fa-paper-plane mr-2"></i>' . get_string('apply', 'local_jobboard'),
            ['class' => 'btn btn-success btn-lg btn-block']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
    } elseif (!$isloggedin) {
        // Not logged in - show login/register options.
        echo html_writer::start_div('card shadow-sm mb-4 border-primary');
        echo html_writer::start_div('card-body text-center');
        echo html_writer::tag('i', '', ['class' => 'fa fa-user-circle fa-3x text-muted mb-3']);
        echo html_writer::tag('h5', get_string('wanttoapply', 'local_jobboard'), ['class' => 'card-title']);
        echo html_writer::tag('p', get_string('loginrequiredtoapply', 'local_jobboard'), ['class' => 'card-text text-muted']);

        $wantsurl = (new moodle_url('/local/jobboard/index.php', [
            'view' => 'apply',
            'vacancyid' => $vacancy->id,
        ]))->out(false);

        echo html_writer::link(
            new moodle_url('/login/index.php', ['wantsurl' => $wantsurl]),
            '<i class="fa fa-sign-in-alt mr-2"></i>' . get_string('login'),
            ['class' => 'btn btn-primary btn-lg btn-block mb-2']
        );
        echo html_writer::link(
            new moodle_url('/local/jobboard/signup.php'),
            '<i class="fa fa-user-plus mr-2"></i>' . get_string('createaccount'),
            ['class' => 'btn btn-outline-primary btn-block']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
    }
} elseif ($hasApplied) {
    echo html_writer::start_div('card shadow-sm mb-4 border-info');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('h5',
        '<i class="fa fa-check-circle text-info mr-2"></i>' . get_string('applied', 'local_jobboard'),
        ['class' => 'card-title']
    );
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $userApplication->id]),
        '<i class="fa fa-eye mr-2"></i>' . get_string('viewmyapplication', 'local_jobboard'),
        ['class' => 'btn btn-info btn-block']
    );
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
        get_string('viewmyapplications', 'local_jobboard'),
        ['class' => 'btn btn-outline-info btn-block mt-2']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
}

echo html_writer::end_div(); // col-lg-4
echo html_writer::end_div(); // row

// ============================================================================
// BACK BUTTON
// ============================================================================
echo html_writer::start_div('mt-4');
if ($convocatoria) {
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $convocatoria->id]),
        '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtoconvocatoria', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary']
    );
} else {
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
        '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtoconvocatorias', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary']
    );
}
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-vacancy-detail

echo $OUTPUT->footer();

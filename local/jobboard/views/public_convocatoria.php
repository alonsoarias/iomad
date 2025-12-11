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
 * Public convocatoria detail view for local_jobboard.
 *
 * Shows convocatoria details without listing vacancies.
 * Provides a link to view vacancies separately.
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

// Parameters.
$convocatoriaid = required_param('id', PARAM_INT);

// Load convocatoria.
$convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);

if (!$convocatoria) {
    throw new moodle_exception('error:convocatorianotfound', 'local_jobboard');
}

// Check convocatoria is open or published.
$now = time();
if ($convocatoria->status !== 'open' || $convocatoria->enddate < $now) {
    throw new moodle_exception('error:convocatoriaclosed', 'local_jobboard');
}

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title($convocatoria->name);
$PAGE->set_heading($convocatoria->name);

// Log view (anonymous).
\local_jobboard\audit::log('public_convocatoria_viewed', 'convocatoria', $convocatoria->id);

// Check if user is logged in.
$isloggedin = isloggedin() && !isguestuser();

// Calculate timing.
$daysRemaining = max(0, (int) ceil(($convocatoria->enddate - $now) / 86400));
$isOpen = ($convocatoria->status === 'open' && $convocatoria->enddate >= $now);
$isClosingSoon = ($daysRemaining <= 7 && $daysRemaining > 0 && $isOpen);
$isClosed = !$isOpen;

// Get statistics using aggregation (avoids duplicate key error).
$stats = $DB->get_record_sql(
    "SELECT COUNT(*) as total_vacancies, COALESCE(SUM(v.positions), 0) as total_positions
       FROM {local_jobboard_vacancy} v
      WHERE v.convocatoriaid = :convid
        AND v.status = :status
        AND v.publicationtype = :pubtype",
    [
        'convid' => $convocatoriaid,
        'status' => 'published',
        'pubtype' => 'public',
    ]
);

$totalVacancies = (int) ($stats->total_vacancies ?? 0);
$totalPositions = (int) ($stats->total_positions ?? 0);

echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-vacancy-detail');

// ============================================================================
// PAGE HEADER (Breadcrumbs)
// ============================================================================
$breadcrumbs = [
    get_string('publicpagetitle', 'local_jobboard') => new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
    s($convocatoria->name) => null,
];

$headerActions = [];
if ($totalVacancies > 0) {
    $headerActions[] = [
        'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $convocatoria->id]),
        'label' => get_string('viewvacancies', 'local_jobboard') . ' (' . $totalVacancies . ')',
        'icon' => 'briefcase',
        'class' => 'btn btn-primary btn-lg',
    ];
}

echo ui_helper::page_header(s($convocatoria->name), $breadcrumbs, $headerActions);

// ============================================================================
// STATUS BANNER
// ============================================================================
$bannerClass = 'alert d-flex justify-content-between align-items-center mb-4';
$bannerIcon = 'info-circle';

if ($isClosed) {
    $bannerClass .= ' alert-secondary';
    $bannerMessage = get_string('convocatoriaclosed', 'local_jobboard');
    $bannerIcon = 'lock';
} elseif ($isClosingSoon) {
    $bannerClass .= ' alert-warning';
    $bannerMessage = get_string('closesindays', 'local_jobboard', $daysRemaining);
    $bannerIcon = 'clock';
} else {
    $bannerClass .= ' alert-success';
    $bannerMessage = get_string('convocatoria_status_open', 'local_jobboard');
    $bannerIcon = 'door-open';
}

echo html_writer::start_div($bannerClass);
echo html_writer::tag('span',
    '<i class="fa fa-' . $bannerIcon . ' mr-2"></i>' . $bannerMessage,
    ['class' => 'font-weight-medium']
);
echo html_writer::start_div();
echo ui_helper::status_badge($convocatoria->status, 'convocatoria');
echo html_writer::tag('code', ' ' . s($convocatoria->code), ['class' => 'ml-2']);
echo html_writer::end_div();
echo html_writer::end_div();

// ============================================================================
// MAIN CONTENT
// ============================================================================
echo html_writer::start_div('row');

// Left column - Main content.
echo html_writer::start_div('col-lg-8');

// Description card.
if (!empty($convocatoria->description)) {
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-file-alt text-primary mr-2"></i>' . get_string('description', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo format_text($convocatoria->description, FORMAT_HTML, ['context' => $context]);
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Terms card (if any).
if (!empty($convocatoria->terms)) {
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-gavel text-warning mr-2"></i>' . get_string('convocatoriaterms', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo format_text($convocatoria->terms, FORMAT_HTML, ['context' => $context]);
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

// Vacancies count.
echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
echo html_writer::tag('span', '<i class="fa fa-briefcase text-muted mr-2"></i>' . get_string('vacancies', 'local_jobboard'));
echo html_writer::tag('span', $totalVacancies, ['class' => 'badge badge-primary badge-pill']);
echo html_writer::end_tag('li');

// Positions count.
echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
echo html_writer::tag('span', '<i class="fa fa-users text-muted mr-2"></i>' . get_string('positions', 'local_jobboard'));
echo html_writer::tag('span', $totalPositions, ['class' => 'badge badge-success badge-pill']);
echo html_writer::end_tag('li');

// Status.
echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
echo html_writer::tag('span', '<i class="fa fa-flag text-muted mr-2"></i>' . get_string('status', 'local_jobboard'));
echo ui_helper::status_badge($convocatoria->status, 'convocatoria');
echo html_writer::end_tag('li');

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

// Start date.
echo html_writer::start_div('d-flex justify-content-between mb-2');
echo html_writer::tag('span', get_string('startdate', 'local_jobboard'), ['class' => 'text-muted']);
echo html_writer::tag('strong', userdate($convocatoria->startdate, get_string('strftimedate', 'langconfig')));
echo html_writer::end_div();

// End date.
$closeDateClass = $isClosingSoon ? 'text-warning' : ($isClosed ? 'text-secondary' : 'text-success');
echo html_writer::start_div('d-flex justify-content-between mb-2');
echo html_writer::tag('span', get_string('enddate', 'local_jobboard'), ['class' => 'text-muted']);
echo html_writer::start_div();
echo html_writer::tag('strong', userdate($convocatoria->enddate, get_string('strftimedate', 'langconfig')), ['class' => $closeDateClass]);
if (!$isClosed && $daysRemaining >= 0) {
    echo html_writer::tag('span', ' (' . $daysRemaining . ' ' . get_string('days', 'local_jobboard') . ')',
        ['class' => 'small ' . $closeDateClass]);
}
echo html_writer::end_div();
echo html_writer::end_div();

// Progress bar for deadline.
if (!$isClosed) {
    $totalDays = max(1, (int) floor(($convocatoria->enddate - $convocatoria->startdate) / 86400));
    $elapsedDays = max(0, (int) floor(($now - $convocatoria->startdate) / 86400));
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
$shareUrl = (new moodle_url('/local/jobboard/index.php', ['view' => 'public_convocatoria', 'id' => $convocatoria->id]))->out(false);
$shareTitle = rawurlencode($convocatoria->name);
$shareText = rawurlencode(get_string('convocatoria', 'local_jobboard') . ': ' . $convocatoria->name);

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

// View vacancies CTA card.
if ($totalVacancies > 0) {
    echo html_writer::start_div('card shadow-sm mb-4 border-primary');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('h5', get_string('viewvacancies', 'local_jobboard'), ['class' => 'card-title']);
    echo html_writer::tag('p',
        get_string('vacanciesavailable', 'local_jobboard', $totalVacancies),
        ['class' => 'card-text text-muted']
    );
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $convocatoria->id]),
        '<i class="fa fa-briefcase mr-2"></i>' . get_string('viewvacancies', 'local_jobboard'),
        ['class' => 'btn btn-primary btn-lg btn-block']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Login/Register CTA for non-authenticated users.
if (!$isloggedin) {
    echo html_writer::start_div('card shadow-sm mb-4 border-warning');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('i', '', ['class' => 'fa fa-user-circle fa-3x text-muted mb-3']);
    echo html_writer::tag('h5', get_string('wanttoapply', 'local_jobboard'), ['class' => 'card-title']);
    echo html_writer::tag('p', get_string('loginrequiredtoapply', 'local_jobboard'), ['class' => 'card-text text-muted']);

    echo html_writer::link(
        new moodle_url('/login/index.php'),
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

echo html_writer::end_div(); // col-lg-4
echo html_writer::end_div(); // row

// ============================================================================
// BACK BUTTON
// ============================================================================
echo html_writer::start_div('mt-4');
echo html_writer::link(
    new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
    '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtoconvocatorias', 'local_jobboard'),
    ['class' => 'btn btn-outline-secondary']
);
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-vacancy-detail

echo $OUTPUT->footer();

<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Usage Monitor Report main dashboard
 *
 * @package    report_usage_monitor
 * @copyright  2023 Soporte IngeWeb <soporte@ingeweb.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/report/usage_monitor/locallib.php');

admin_externalpage_setup('report_usage_monitor', '', null, '', ['pagelayout' => 'admin']);

// ============================================================
// DATA PREPARATION
// ============================================================

$reportconfig = get_config('report_usage_monitor');

// Disk usage data
$disk_usage_bytes = (int)($reportconfig->totalusagereadable ?? 0) + (int)($reportconfig->totalusagereadabledb ?? 0);
$quotadisk_bytes  = ((int)($reportconfig->disk_quota ?? 0)) * 1024 * 1024 * 1024;
$disk_usage_gb = !empty($reportconfig->disk_usage_gb) ? $reportconfig->disk_usage_gb : display_size_in_gb($disk_usage_bytes, 2);
$quotadisk_gb = !empty($reportconfig->quotadisk_gb) ? $reportconfig->quotadisk_gb : display_size_in_gb($quotadisk_bytes, 2);
$disk_percent = !empty($reportconfig->disk_percent) ? (float)$reportconfig->disk_percent :
    (($quotadisk_bytes > 0) ? ($disk_usage_bytes / $quotadisk_bytes * 100) : 0);
$disk_warning_class = ($disk_percent < 70) ? 'bg-success' : (($disk_percent < 90) ? 'bg-warning' : 'bg-danger');

// Users data
$users_today = (int)($reportconfig->totalusersdaily ?? 0);
$max_users_threshold = (int)($reportconfig->max_daily_users_threshold ?? 100);
$users_percent = ($max_users_threshold > 0) ? ($users_today / $max_users_threshold * 100) : 0;
$users_warning_class = ($users_percent < 70) ? 'bg-success' : (($users_percent < 90) ? 'bg-warning' : 'bg-danger');

// Last execution timestamps
$lastexec_disk_ts = !empty($reportconfig->lastexecutioncalculate) ? $reportconfig->lastexecutioncalculate : 0;
$lastexec_disk = (!is_numeric($lastexec_disk_ts) || $lastexec_disk_ts <= 0) ?
    get_string('notcalculatedyet', 'report_usage_monitor') : date('d/m/Y H:i', (int)$lastexec_disk_ts);

$lastexec_users_ts = !empty($reportconfig->lastexecution) ? $reportconfig->lastexecution : 0;
$lastexec_users = (!is_numeric($lastexec_users_ts) || $lastexec_users_ts <= 0) ?
    get_string('notcalculatedyet', 'report_usage_monitor') : date('d/m/Y H:i', (int)$lastexec_users_ts);

// Max 90 days data
$max_90_days_users = $reportconfig->max_userdaily_for_90_days_users ?? get_string('notcalculatedyet', 'report_usage_monitor');
$max_90_days_date_ts = $reportconfig->max_userdaily_for_90_days_date ?? 0;
$max_90_days_date = (!is_numeric($max_90_days_date_ts) || $max_90_days_date_ts <= 0) ?
    get_string('notcalculatedyet', 'report_usage_monitor') : date('d/m/Y', (int)$max_90_days_date_ts);

// Directory analysis
$dir_analysis_json = $reportconfig->dir_analysis ?? '{}';
$dir_analysis = json_decode($dir_analysis_json, true);
if (empty($dir_analysis) || !is_array($dir_analysis)) {
    $dir_analysis = ['database' => 0, 'filedir' => 0, 'cache' => 0, 'others' => 0];
}

// Largest courses
$largest_courses_json = $reportconfig->largest_courses ?? '[]';
$largest_courses = json_decode($largest_courses_json);
if (empty($largest_courses)) {
    $largest_courses = get_largest_courses(5);
}

// Users daily (last 10 days)
$userdaily_records = $DB->get_records_sql(report_user_daily_sql());
$formatted_userdaily_records = [];
foreach ($userdaily_records as $record) {
    $obj = new stdClass();
    $obj->conteo_accesos_unicos = $record->conteo_accesos_unicos;
    $obj->fecha_formateada = is_numeric($record->timestamp_fecha) ? date('d/m/Y', (int)$record->timestamp_fecha) : date('d/m/Y');
    $formatted_userdaily_records[] = $obj;
}

// Top users daily
$userdaily_recordstop = $DB->get_records_sql(report_user_daily_top_sql());
$formatted_userdaily_recordstop = [];
foreach ($userdaily_recordstop as $record) {
    $obj = new stdClass();
    $obj->cantidad_usuarios = $record->cantidad_usuarios;
    $obj->fecha_formateada = is_numeric($record->timestamp_fecha) ? date('d/m/Y', (int)$record->timestamp_fecha) : date('d/m/Y');
    $formatted_userdaily_recordstop[] = $obj;
}

// System info
$totalcourses = $DB->count_records('course');
$activeusers = max(0, $DB->count_records('user', ['deleted' => 0, 'suspended' => 0]) - 1);
$suspendedusers = $DB->count_records('user', ['deleted' => 0, 'suspended' => 1]);
$backup_max_kept = get_config('backup', 'backup_auto_max_kept') ?? 0;

// Disk history (last 30 days)
$month_ago = time() - (30 * 24 * 60 * 60);
$disk_history = $DB->get_records_sql(
    "SELECT id, timecreated, value, percentage FROM {report_usage_monitor_history} WHERE type = 'disk' AND timecreated > ? ORDER BY timecreated ASC",
    [$month_ago]
);

$disk_history_labels = [];
$disk_history_data = [];
$daily_data = [];
foreach ($disk_history as $record) {
    if (is_numeric($record->timecreated) && $record->timecreated > 0) {
        $date_key = date('Y-m-d', (int)$record->timecreated);
        $current_percentage = ($quotadisk_bytes > 0) ? round(($record->value / $quotadisk_bytes) * 100, 1) : 0;
        $daily_data[$date_key] = [
            'label' => date('d/m', (int)$record->timecreated),
            'percentage' => $current_percentage
        ];
    }
}
foreach ($daily_data as $data) {
    $disk_history_labels[] = $data['label'];
    $disk_history_data[] = $data['percentage'];
}

// Course access trends (last 30 days)
$course_access_trends = get_course_access_trends(30);
$most_accessed_courses = get_most_accessed_courses(10, 30);
$course_completion_trends = get_course_completion_trends(30);
$access_summary = get_access_summary(30);
$completion_summary = get_completion_summary(30);

// Prepare chart data
$access_trends_labels = [];
$access_trends_data = [];
foreach ($course_access_trends as $trend) {
    $access_trends_labels[] = $trend->fecha_formateada;
    $access_trends_data[] = $trend->total_accesses;
}

$completion_trends_labels = [];
$completion_trends_data = [];
foreach ($course_completion_trends as $trend) {
    $completion_trends_labels[] = $trend->fecha_formateada;
    $completion_trends_data[] = $trend->completions;
}

// Prepare data for line chart (users last 10 days)
$last10daysLabels = [];
$last10daysData = [];
$last10daysDataRaw = [];
foreach ($formatted_userdaily_records as $day) {
    $last10daysLabels[] = $day->fecha_formateada;
    $percent = ($max_users_threshold > 0) ? min(100, round(($day->conteo_accesos_unicos / $max_users_threshold) * 100, 1)) : 0;
    $last10daysData[] = $percent;
    $last10daysDataRaw[] = (int)$day->conteo_accesos_unicos;
}

// Doughnut chart data
$doughnutLabels = [
    get_string('database', 'report_usage_monitor'),
    get_string('files_dir', 'report_usage_monitor'),
    get_string('cache', 'report_usage_monitor'),
    get_string('others', 'report_usage_monitor')
];
$doughnutData = [
    display_size_in_gb($dir_analysis['database'] ?? 0, 2),
    display_size_in_gb($dir_analysis['filedir'] ?? 0, 2),
    display_size_in_gb($dir_analysis['cache'] ?? 0, 2),
    display_size_in_gb($dir_analysis['others'] ?? 0, 2)
];

// Check if report_coursesize plugin is installed
$reportplugins = \core_plugin_manager::instance()->get_plugins_of_type('report');
$coursesize_installed = isset($reportplugins['coursesize']);

// ============================================================
// OUTPUT
// ============================================================

echo $OUTPUT->header();
echo '<div class="alert alert-info mb-2 text-center small">' . get_string('exclusivedisclaimer', 'report_usage_monitor') . '</div>';
echo $OUTPUT->heading(get_string('dashboard_title', 'report_usage_monitor'));
?>

<div class="container-fluid usage-monitor-dashboard">
    <!-- STATUS CARDS ROW -->
    <div class="row mb-4">
        <!-- Disk Usage Card -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title mb-0 text-muted"><?php echo get_string('diskusage', 'report_usage_monitor'); ?></h6>
                        <span class="badge <?php echo $disk_warning_class; ?> rounded-pill"><?php echo round($disk_percent, 1); ?>%</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar <?php echo $disk_warning_class; ?>" style="width: <?php echo min($disk_percent, 100); ?>%;"></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="h5 mb-0"><?php echo $disk_usage_gb . ' / ' . $quotadisk_gb; ?> GB</span>
                    </div>
                    <small class="text-muted"><?php echo get_string('lastexecutioncalculate', 'report_usage_monitor', $lastexec_disk); ?></small>
                </div>
            </div>
        </div>

        <!-- Users Today Card -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title mb-0 text-muted"><?php echo get_string('users_today_card', 'report_usage_monitor'); ?></h6>
                        <span class="badge <?php echo $users_warning_class; ?> rounded-pill"><?php echo round($users_percent, 1); ?>%</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar <?php echo $users_warning_class; ?>" style="width: <?php echo min($users_percent, 100); ?>%;"></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="h5 mb-0"><?php echo $users_today . ' / ' . $max_users_threshold; ?></span>
                    </div>
                    <small class="text-muted"><?php echo get_string('lastexecution', 'report_usage_monitor', $lastexec_users); ?></small>
                </div>
            </div>
        </div>

        <!-- Max 90 Days Card -->
        <div class="col-lg-4 col-md-12 mb-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h6 class="card-title mb-2 text-muted"><?php echo get_string('max_userdaily_for_90_days', 'report_usage_monitor'); ?></h6>
                    <div class="h4 mb-1"><?php echo $max_90_days_users; ?> <small class="text-muted">/ <?php echo $max_users_threshold; ?></small></div>
                    <small class="text-muted"><?php echo get_string('date', 'report_usage_monitor'); ?>: <?php echo $max_90_days_date; ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN TABBED CONTENT -->
    <ul class="nav nav-tabs mb-3" id="dashboardTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="disk-tab" data-bs-toggle="tab" data-bs-target="#disk-content" type="button" role="tab">
                <?php echo get_string('diskusage', 'report_usage_monitor'); ?>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users-content" type="button" role="tab">
                <?php echo get_string('users_today_card', 'report_usage_monitor'); ?>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses-content" type="button" role="tab">
                <?php echo get_string('course_access_trends', 'report_usage_monitor'); ?>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system-content" type="button" role="tab">
                <?php echo get_string('system_info', 'report_usage_monitor'); ?>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="dashboardTabContent">
        <!-- DISK TAB -->
        <div class="tab-pane fade show active" id="disk-content" role="tabpanel">
            <div class="row">
                <!-- Disk Distribution Chart -->
                <div class="col-lg-5 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><?php echo get_string('disk_usage_distribution', 'report_usage_monitor'); ?></h6>
                        </div>
                        <div class="card-body">
                            <?php if ($disk_usage_bytes > 0): ?>
                                <div style="height: 280px; position: relative;">
                                    <canvas id="chart-doughnut"></canvas>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mb-0"><?php echo get_string('notcalculatedyet', 'report_usage_monitor'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Disk Tables -->
                <div class="col-lg-7 mb-4">
                    <!-- Usage by Directory -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><?php echo get_string('disk_usage_by_directory', 'report_usage_monitor'); ?></h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th><?php echo get_string('directory', 'report_usage_monitor'); ?></th>
                                        <th class="text-end"><?php echo get_string('size', 'report_usage_monitor'); ?></th>
                                        <th class="text-end"><?php echo get_string('percentage', 'report_usage_monitor'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($dir_analysis) && $disk_usage_bytes > 0):
                                        $directories = [
                                            'database' => ['label' => get_string('database', 'report_usage_monitor'), 'size' => (int)($dir_analysis['database'] ?? 0)],
                                            'filedir'  => ['label' => get_string('files_dir', 'report_usage_monitor'), 'size' => (int)($dir_analysis['filedir'] ?? 0)],
                                            'cache'    => ['label' => get_string('cache', 'report_usage_monitor'), 'size' => (int)($dir_analysis['cache'] ?? 0)],
                                            'others'   => ['label' => get_string('others', 'report_usage_monitor'), 'size' => (int)($dir_analysis['others'] ?? 0)],
                                        ];
                                        uasort($directories, function($a, $b) { return $b['size'] - $a['size']; });
                                        foreach ($directories as $dir_data):
                                            $percent = round(($dir_data['size'] / $disk_usage_bytes) * 100, 1);
                                    ?>
                                        <tr>
                                            <td><?php echo $dir_data['label']; ?></td>
                                            <td class="text-end"><?php echo display_size_in_gb($dir_data['size'], 2); ?> GB</td>
                                            <td class="text-end"><?php echo $percent; ?>%</td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td colspan="3" class="text-center text-muted"><?php echo get_string('notcalculatedyet', 'report_usage_monitor'); ?></td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Largest Courses -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><?php echo get_string('largest_courses', 'report_usage_monitor'); ?></h6>
                            <?php if ($coursesize_installed): ?>
                                <a href="<?php echo $CFG->wwwroot; ?>/report/coursesize/index.php" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <?php echo get_string('show_more_courses', 'report_usage_monitor'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th><?php echo get_string('course', 'report_usage_monitor'); ?></th>
                                        <th class="text-end"><?php echo get_string('size', 'report_usage_monitor'); ?></th>
                                        <th class="text-end"><?php echo get_string('backup_count', 'report_usage_monitor'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($largest_courses)): foreach ($largest_courses as $course): ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo $CFG->wwwroot . '/course/view.php?id=' . $course->id; ?>" class="text-truncate d-inline-block" style="max-width: 250px;">
                                                    <?php echo format_string($course->fullname); ?>
                                                </a>
                                            </td>
                                            <td class="text-end"><?php echo display_size($course->totalsize); ?></td>
                                            <td class="text-end"><?php echo $course->backupcount; ?></td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td colspan="3" class="text-center text-muted"><?php echo get_string('notcalculatedyet', 'report_usage_monitor'); ?></td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disk History Chart -->
            <?php if (!empty($disk_history_labels)): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><?php echo get_string('disk_usage_history', 'report_usage_monitor'); ?></h6>
                </div>
                <div class="card-body">
                    <div style="height: 250px; position: relative;">
                        <canvas id="chart-disk-history"></canvas>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- USERS TAB -->
        <div class="tab-pane fade" id="users-content" role="tabpanel">
            <div class="row">
                <!-- Last 10 Days Chart -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><?php echo get_string('lastusers', 'report_usage_monitor'); ?></h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($last10daysLabels)): ?>
                                <div style="height: 300px; position: relative;">
                                    <canvas id="chart-users-10days"></canvas>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mb-0"><?php echo get_string('notcalculatedyet', 'report_usage_monitor'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Last 10 Days Table -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><?php echo get_string('usertable', 'report_usage_monitor'); ?></h6>
                        </div>
                        <div class="table-responsive" style="max-height: 340px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th><?php echo get_string('date', 'report_usage_monitor'); ?></th>
                                        <th class="text-end"><?php echo get_string('usersquantity', 'report_usage_monitor'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($formatted_userdaily_records)): foreach ($formatted_userdaily_records as $daylog): ?>
                                        <tr>
                                            <td><?php echo $daylog->fecha_formateada; ?></td>
                                            <td class="text-end"><?php echo $daylog->conteo_accesos_unicos; ?></td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td colspan="2" class="text-center text-muted"><?php echo get_string('notcalculatedyet', 'report_usage_monitor'); ?></td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 10 Users Daily -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><?php echo get_string('topuser', 'report_usage_monitor'); ?></h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><?php echo get_string('date', 'report_usage_monitor'); ?></th>
                                <th class="text-end"><?php echo get_string('usersquantity', 'report_usage_monitor'); ?></th>
                                <th class="text-end"><?php echo get_string('percentage', 'report_usage_monitor'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($formatted_userdaily_recordstop)): foreach ($formatted_userdaily_recordstop as $log):
                                $percent = ($max_users_threshold > 0) ? round(($log->cantidad_usuarios / $max_users_threshold) * 100, 1) : 0;
                                $class = ($percent >= 90) ? 'text-danger fw-bold' : (($percent >= 70) ? 'text-warning' : '');
                            ?>
                                <tr>
                                    <td><?php echo $log->fecha_formateada; ?></td>
                                    <td class="text-end"><?php echo $log->cantidad_usuarios; ?></td>
                                    <td class="text-end <?php echo $class; ?>"><?php echo $percent; ?>%</td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="3" class="text-center text-muted"><?php echo get_string('notcalculatedyet', 'report_usage_monitor'); ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- COURSES TAB -->
        <div class="tab-pane fade" id="courses-content" role="tabpanel">
            <!-- Course Access Summary -->
            <div class="row mb-4">
                <div class="col-md-3 col-6 mb-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body py-3 text-center">
                            <div class="small opacity-75"><?php echo get_string('total_accesses', 'report_usage_monitor'); ?></div>
                            <div class="h4 mb-0"><?php echo number_format($access_summary->total_accesses); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <div class="card bg-success text-white">
                        <div class="card-body py-3 text-center">
                            <div class="small opacity-75"><?php echo get_string('unique_users', 'report_usage_monitor'); ?></div>
                            <div class="h4 mb-0"><?php echo number_format($access_summary->unique_users); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <div class="card bg-info text-white">
                        <div class="card-body py-3 text-center">
                            <div class="small opacity-75"><?php echo get_string('total_completions', 'report_usage_monitor'); ?></div>
                            <div class="h4 mb-0"><?php echo number_format($completion_summary->total_completions); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <div class="card bg-secondary text-white">
                        <div class="card-body py-3 text-center">
                            <div class="small opacity-75"><?php echo get_string('unique_courses', 'report_usage_monitor'); ?></div>
                            <div class="h4 mb-0"><?php echo number_format($access_summary->unique_courses); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Access Trends Chart -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><?php echo get_string('course_access_trends', 'report_usage_monitor'); ?></h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($access_trends_labels)): ?>
                                <div style="height: 250px; position: relative;">
                                    <canvas id="chart-access-trends"></canvas>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mb-0"><?php echo get_string('no_data_available', 'report_usage_monitor'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Completion Trends Chart -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><?php echo get_string('course_completion_trends', 'report_usage_monitor'); ?></h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($completion_trends_labels)): ?>
                                <div style="height: 250px; position: relative;">
                                    <canvas id="chart-completion-trends"></canvas>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mb-0"><?php echo get_string('no_data_available', 'report_usage_monitor'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Most Accessed Courses Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><?php echo get_string('most_accessed_courses', 'report_usage_monitor'); ?></h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th><?php echo get_string('course', 'report_usage_monitor'); ?></th>
                                <th class="text-end"><?php echo get_string('total_accesses', 'report_usage_monitor'); ?></th>
                                <th class="text-end"><?php echo get_string('unique_users', 'report_usage_monitor'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($most_accessed_courses)): $i = 1; foreach ($most_accessed_courses as $course): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td>
                                        <a href="<?php echo $CFG->wwwroot . '/course/view.php?id=' . $course->id; ?>">
                                            <?php echo format_string($course->fullname); ?>
                                        </a>
                                        <small class="text-muted d-block"><?php echo $course->shortname; ?></small>
                                    </td>
                                    <td class="text-end"><?php echo number_format($course->total_accesses); ?></td>
                                    <td class="text-end"><?php echo number_format($course->unique_users); ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="4" class="text-center text-muted"><?php echo get_string('no_data_available', 'report_usage_monitor'); ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SYSTEM TAB -->
        <div class="tab-pane fade" id="system-content" role="tabpanel">
            <div class="row">
                <!-- System Info -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><?php echo get_string('system_info', 'report_usage_monitor'); ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block"><?php echo get_string('moodle_version', 'report_usage_monitor'); ?></small>
                                        <strong><?php echo $CFG->release; ?></strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block"><?php echo get_string('total_courses', 'report_usage_monitor'); ?></small>
                                        <strong><?php echo $totalcourses; ?></strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block"><?php echo get_string('active_users', 'report_usage_monitor'); ?></small>
                                        <strong><?php echo $activeusers; ?></strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block"><?php echo get_string('suspended_users', 'report_usage_monitor'); ?></small>
                                        <strong><?php echo $suspendedusers; ?></strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block"><?php echo get_string('backup_per_course', 'report_usage_monitor'); ?></small>
                                        <strong><?php echo $backup_max_kept; ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><?php echo get_string('recommendations', 'report_usage_monitor'); ?></h6>
                        </div>
                        <div class="card-body">
                            <?php if ($disk_percent > 70): ?>
                                <div class="alert alert-<?php echo ($disk_percent > 90) ? 'danger' : 'warning'; ?> mb-3">
                                    <strong><?php echo get_string('space_saving_tips', 'report_usage_monitor'); ?></strong>
                                    <ul class="mb-0 mt-2 small">
                                        <li><?php echo get_string('tip_backups', 'report_usage_monitor', $backup_max_kept); ?></li>
                                        <li><?php echo get_string('tip_files', 'report_usage_monitor'); ?></li>
                                        <li><?php echo get_string('tip_cache', 'report_usage_monitor'); ?></li>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success mb-3">
                                    <i class="fa fa-check-circle me-2"></i><?php echo get_string('disk_usage_ok', 'report_usage_monitor'); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($users_percent > 70): ?>
                                <div class="alert alert-<?php echo ($users_percent > 90) ? 'danger' : 'warning'; ?> mb-0">
                                    <strong><?php echo get_string('user_limit_tips', 'report_usage_monitor'); ?></strong>
                                    <ul class="mb-0 mt-2 small">
                                        <li><?php echo get_string('tip_user_inactive', 'report_usage_monitor'); ?></li>
                                        <li><?php echo get_string('tip_user_limit', 'report_usage_monitor'); ?></li>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success mb-0">
                                    <i class="fa fa-check-circle me-2"></i><?php echo get_string('user_count_ok', 'report_usage_monitor'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Credits -->
<div class="mt-4 text-center text-muted small">
    <?php echo get_string('reportinfotext', 'report_usage_monitor'); ?>
</div>

<!-- Chart.js CDN with integrity check -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
(function() {
    'use strict';

    // Chart configuration
    var chartConfig = {
        doughnut: {
            labels: <?php echo json_encode($doughnutLabels); ?>,
            data: <?php echo json_encode($doughnutData); ?>,
            hasData: <?php echo ($disk_usage_bytes > 0) ? 'true' : 'false'; ?>
        },
        diskHistory: {
            labels: <?php echo json_encode($disk_history_labels); ?>,
            data: <?php echo json_encode($disk_history_data); ?>,
            hasData: <?php echo !empty($disk_history_labels) ? 'true' : 'false'; ?>
        },
        users10days: {
            labels: <?php echo json_encode($last10daysLabels); ?>,
            data: <?php echo json_encode($last10daysData); ?>,
            dataRaw: <?php echo json_encode($last10daysDataRaw); ?>,
            hasData: <?php echo !empty($last10daysLabels) ? 'true' : 'false'; ?>
        },
        accessTrends: {
            labels: <?php echo json_encode($access_trends_labels); ?>,
            data: <?php echo json_encode($access_trends_data); ?>,
            hasData: <?php echo !empty($access_trends_labels) ? 'true' : 'false'; ?>
        },
        completionTrends: {
            labels: <?php echo json_encode($completion_trends_labels); ?>,
            data: <?php echo json_encode($completion_trends_data); ?>,
            hasData: <?php echo !empty($completion_trends_labels) ? 'true' : 'false'; ?>
        }
    };

    // Strings
    var strings = {
        usersQuantity: '<?php echo get_string('usersquantity', 'report_usage_monitor'); ?>',
        percentUsed: '<?php echo get_string('percentage_used', 'report_usage_monitor'); ?>',
        warning70: '<?php echo get_string('warning70', 'report_usage_monitor'); ?>',
        critical90: '<?php echo get_string('critical90', 'report_usage_monitor'); ?>',
        limit100: '<?php echo get_string('limit100', 'report_usage_monitor'); ?>',
        percentThreshold: '<?php echo get_string('percent_of_threshold', 'report_usage_monitor'); ?>',
        totalAccesses: '<?php echo get_string('total_accesses', 'report_usage_monitor'); ?>',
        completions: '<?php echo get_string('completions', 'report_usage_monitor'); ?>'
    };

    // Chart instances storage
    var charts = {};

    // Wait for Chart.js to load
    function waitForChartJS(callback, maxAttempts) {
        maxAttempts = maxAttempts || 50;
        var attempts = 0;

        function check() {
            attempts++;
            if (typeof Chart !== 'undefined') {
                callback();
            } else if (attempts < maxAttempts) {
                setTimeout(check, 100);
            } else {
                console.error('Chart.js failed to load');
            }
        }
        check();
    }

    // Initialize a chart safely
    function initChart(canvasId, config) {
        var canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        // Destroy existing chart if any
        if (charts[canvasId]) {
            charts[canvasId].destroy();
        }

        try {
            charts[canvasId] = new Chart(canvas, config);
            return charts[canvasId];
        } catch (e) {
            console.error('Error creating chart ' + canvasId + ':', e);
            return null;
        }
    }

    // Create threshold lines dataset
    function createThresholdLines(count) {
        return [
            {
                label: strings.warning70,
                data: Array(count).fill(70),
                borderColor: '#ffc107',
                borderDash: [5, 5],
                pointRadius: 0,
                fill: false
            },
            {
                label: strings.critical90,
                data: Array(count).fill(90),
                borderColor: '#dc3545',
                borderDash: [5, 5],
                pointRadius: 0,
                fill: false
            },
            {
                label: strings.limit100,
                data: Array(count).fill(100),
                borderColor: '#6c757d',
                borderDash: [2, 2],
                pointRadius: 0,
                fill: false
            }
        ];
    }

    // Initialize all charts
    function initCharts() {
        // Doughnut chart
        if (chartConfig.doughnut.hasData) {
            initChart('chart-doughnut', {
                type: 'doughnut',
                data: {
                    labels: chartConfig.doughnut.labels,
                    datasets: [{
                        data: chartConfig.doughnut.data,
                        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dee2e6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ctx.label + ': ' + ctx.parsed + ' GB';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Disk history chart
        if (chartConfig.diskHistory.hasData) {
            var diskDatasets = [{
                label: strings.percentUsed,
                data: chartConfig.diskHistory.data,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true,
                tension: 0.2
            }].concat(createThresholdLines(chartConfig.diskHistory.labels.length));

            initChart('chart-disk-history', {
                type: 'line',
                data: {
                    labels: chartConfig.diskHistory.labels,
                    datasets: diskDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: function(v) { return v + '%'; } }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
        }

        // Users 10 days chart
        if (chartConfig.users10days.hasData) {
            var usersDatasets = [{
                label: strings.usersQuantity,
                data: chartConfig.users10days.data,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true,
                tension: 0.2
            }].concat(createThresholdLines(chartConfig.users10days.labels.length));

            initChart('chart-users-10days', {
                type: 'line',
                data: {
                    labels: chartConfig.users10days.labels,
                    datasets: usersDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: function(v) { return v + '%'; } },
                            title: { display: true, text: strings.percentThreshold }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    if (ctx.datasetIndex === 0) {
                                        return chartConfig.users10days.dataRaw[ctx.dataIndex] + ' (' + ctx.parsed.y + '%)';
                                    }
                                    return ctx.dataset.label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Access trends chart
        if (chartConfig.accessTrends.hasData) {
            initChart('chart-access-trends', {
                type: 'line',
                data: {
                    labels: chartConfig.accessTrends.labels,
                    datasets: [{
                        label: strings.totalAccesses,
                        data: chartConfig.accessTrends.data,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        fill: true,
                        tension: 0.2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // Completion trends chart
        if (chartConfig.completionTrends.hasData) {
            initChart('chart-completion-trends', {
                type: 'line',
                data: {
                    labels: chartConfig.completionTrends.labels,
                    datasets: [{
                        label: strings.completions,
                        data: chartConfig.completionTrends.data,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        fill: true,
                        tension: 0.2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            waitForChartJS(initCharts);
        });
    } else {
        waitForChartJS(initCharts);
    }

    // Handle tab changes to resize charts
    document.addEventListener('shown.bs.tab', function(e) {
        Object.keys(charts).forEach(function(key) {
            if (charts[key]) {
                charts[key].resize();
            }
        });
    });
})();
</script>

<style>
.usage-monitor-dashboard .card { transition: box-shadow 0.2s; }
.usage-monitor-dashboard .card:hover { box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; }
.usage-monitor-dashboard .nav-tabs .nav-link { color: #495057; }
.usage-monitor-dashboard .nav-tabs .nav-link.active { font-weight: 600; }
.usage-monitor-dashboard .table th { font-weight: 600; font-size: 0.85rem; }
.usage-monitor-dashboard .table td { font-size: 0.9rem; }
.usage-monitor-dashboard .badge { font-weight: 500; }
</style>

<?php
echo $OUTPUT->footer();

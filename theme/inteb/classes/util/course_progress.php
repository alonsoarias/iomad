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
 * Course progress utility class
 *
 * Based on format_remuiformat implementation for accurate progress calculation
 *
 * @package     theme_inteb
 * @copyright   2025 Vicerrectoría Académica ISER <vicerrectoria@iser.edu.co>
 * @author      Alonso Arias <soporteplataformas@iser.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_inteb\util;

defined('MOODLE_INTERNAL') || die();

use completion_info;
use core_completion\progress;

/**
 * Course progress utility class.
 *
 * Provides methods to calculate course and section completion progress.
 * Implementation is based on format_remuiformat for accurate progress
 * calculation using Moodle's core completion API.
 *
 * @package     theme_inteb
 * @copyright   2025 Vicerrectoría Académica ISER <vicerrectoria@iser.edu.co>
 * @author      Alonso Arias <soporteplataformas@iser.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_progress {

    /**
     * Get course overall progress percentage
     *
     * Implementation based on format_remuiformat/classes/external/course_progress_data.php
     * Uses Moodle's core API exactly as format_remuiformat does.
     *
     * @param object $course Course object
     * @param int $userid User ID (not used - API uses current user)
     * @return array Array with 'percentage' and 'hasprogress' keys
     */
    public static function get_course_progress($course, $userid = null) {
        global $DB;

        // First, verify the course has enablecompletion = 1 in the database
        $courserecord = $DB->get_record('course', ['id' => $course->id], 'id, enablecompletion');
        if (empty($courserecord) || empty($courserecord->enablecompletion)) {
            debugging('course_progress: Course ' . $course->id . ' has enablecompletion = 0 or not set', DEBUG_DEVELOPER);
            return [
                'hasprogress' => false,
                'percentage' => 0
            ];
        }

        // Check if completion is enabled using completion_info
        $completion = new completion_info($course);
        if (!$completion->is_enabled()) {
            debugging('course_progress: completion_info->is_enabled() returned false for course ' . $course->id, DEBUG_DEVELOPER);
            return [
                'hasprogress' => false,
                'percentage' => 0
            ];
        }

        // Verify that the course has at least one activity with completion tracking
        // This ensures we only show the progress header when there's actually something to track
        $modinfo = get_fast_modinfo($course);
        $hasActivitiesWithTracking = false;
        $activitiesCount = 0;
        $activitiesWithTrackingCount = 0;

        foreach ($modinfo->get_cms() as $cm) {
            // Skip labels and non-visible activities (same logic as section progress)
            if ($cm->modname === 'label' || !$cm->uservisible) {
                continue;
            }

            $activitiesCount++;

            // Check if this activity has completion tracking enabled
            // is_enabled($cm) returns: COMPLETION_TRACKING_NONE (0), COMPLETION_TRACKING_MANUAL (1), or COMPLETION_TRACKING_AUTOMATIC (2)
            $trackingType = $completion->is_enabled($cm);
            if ($trackingType && $trackingType != COMPLETION_TRACKING_NONE) {
                $hasActivitiesWithTracking = true;
                $activitiesWithTrackingCount++;
            }
        }

        // If course has completion enabled but no activities have tracking configured
        if (!$hasActivitiesWithTracking) {
            debugging('course_progress: Course ' . $course->id . ' has completion enabled but no activities ' .
                     'with completion tracking configured.', DEBUG_DEVELOPER);
            return [
                'hasprogress' => true,
                'hasactivitiestracking' => false,  // No activities have tracking configured
                'percentage' => 0,
                'activitiescount' => $activitiesCount,
                'activitieswithtracking' => 0
            ];
        }

        // Use Moodle's core API exactly as format_remuiformat does
        // Pass only the course object, NOT userid
        $percentage = progress::get_course_progress_percentage($course);

        // Handle NULL response - means 0% progress
        if (!is_null($percentage)) {
            $percentage = floor($percentage);
        } else {
            $percentage = 0;
        }

        debugging('course_progress: Course ' . $course->id . ' has progress. Activities with tracking: ' .
                 $activitiesWithTrackingCount . ', Percentage: ' . $percentage, DEBUG_DEVELOPER);

        return [
            'hasprogress' => true,
            'hasactivitiestracking' => true,  // Activities have tracking configured
            'percentage' => $percentage,
            'activitiescount' => $activitiesCount,
            'activitieswithtracking' => $activitiesWithTrackingCount
        ];
    }

    /**
     * Get section progress information
     *
     * Implementation based on format_remuiformat/classes/course_format_data_common_trait.php
     * Method: get_section_module_info() lines 305-325
     *
     * @param object $course Course object
     * @param object $section Section info object
     * @param int $userid User ID (default: current user)
     * @return array Array with progress information
     */
    public static function get_section_progress($course, $section, $userid = null) {
        global $USER;

        if ($userid === null) {
            $userid = $USER->id;
        }

        // Check if user can complete activities
        $cancomplete = isloggedin() && !isguestuser();
        if (!$cancomplete) {
            return [
                'hasprogress' => false,
                'percentage' => 0,
                'complete' => 0,
                'total' => 0
            ];
        }

        // Check if completion is enabled
        $completion = new completion_info($course);
        if (!$completion->is_enabled()) {
            return [
                'hasprogress' => false,
                'percentage' => 0,
                'complete' => 0,
                'total' => 0
            ];
        }

        // Get course modinfo
        $modinfo = get_fast_modinfo($course);

        // Check if section exists and has activities
        if (empty($modinfo->sections[$section->section])) {
            return [
                'hasprogress' => false,
                'percentage' => 0,
                'complete' => 0,
                'total' => 0
            ];
        }

        $total = 0;
        $complete = 0;

        // Count completed activities - EXACT implementation from format_remuiformat
        foreach ($modinfo->sections[$section->section] as $cmid) {
            $thismod = $modinfo->cms[$cmid];

            // Skip labels (format_remuiformat line 292-294)
            if ($thismod->modname == 'label') {
                continue;
            }

            // Only count if user visible (format_remuiformat line 297)
            if ($thismod->uservisible) {
                // Check if completion tracking is enabled for this activity (format_remuiformat line 305)
                if ($cancomplete && $completion->is_enabled($thismod) != COMPLETION_TRACKING_NONE) {
                    $total++;
                    $completiondata = $completion->get_data($thismod, true);

                    // Check if completed (format_remuiformat lines 308-310)
                    if ($completiondata->completionstate == COMPLETION_COMPLETE ||
                        $completiondata->completionstate == COMPLETION_COMPLETE_PASS) {
                        $complete++;
                    }
                }
            }
        }

        // Calculate percentage (format_remuiformat line 325)
        $percentage = 0;
        if ($total > 0) {
            $percentage = round(($complete / $total) * 100, 0);
        }

        return [
            'hasprogress' => ($total > 0),
            'percentage' => $percentage,
            'complete' => $complete,
            'total' => $total
        ];
    }

    /**
     * Get section progress by section ID
     *
     * @param int $courseid Course ID
     * @param int $sectionid Section ID
     * @param int $userid User ID (default: current user)
     * @return array Array with progress information
     */
    public static function get_section_progress_by_id($courseid, $sectionid, $userid = null) {
        global $DB;

        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
        $section = $DB->get_record('course_sections', ['id' => $sectionid], '*', MUST_EXIST);

        return self::get_section_progress($course, $section, $userid);
    }
}

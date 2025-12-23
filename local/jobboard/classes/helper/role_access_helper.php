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
 * Role access helper for local_jobboard.
 *
 * Provides role-based access control for the Dean and HR workflow.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\helper;

defined('MOODLE_INTERNAL') || die();

/**
 * Role access helper class.
 *
 * Manages date-based access control for Dean and HR roles in the workflow.
 */
class role_access_helper {

    /**
     * Check if Dean can access review for a convocatoria.
     *
     * @param \stdClass $convocatoria The convocatoria record.
     * @return bool True if Dean can access.
     */
    public static function can_dean_access(\stdClass $convocatoria): bool {
        $now = time();

        if (empty($convocatoria->dean_review_startdate) ||
            empty($convocatoria->dean_review_enddate)) {
            return false;
        }

        return ($now >= $convocatoria->dean_review_startdate &&
                $now <= $convocatoria->dean_review_enddate);
    }

    /**
     * Check if HR can access validation for a convocatoria.
     *
     * @param \stdClass $convocatoria The convocatoria record.
     * @return bool True if HR can access.
     */
    public static function can_hr_access(\stdClass $convocatoria): bool {
        $now = time();

        if (empty($convocatoria->hr_review_startdate) ||
            empty($convocatoria->hr_review_enddate)) {
            return false;
        }

        return ($now >= $convocatoria->hr_review_startdate &&
                $now <= $convocatoria->hr_review_enddate);
    }

    /**
     * Get review period status for Dean.
     *
     * @param \stdClass $convocatoria The convocatoria record.
     * @return string Status: 'not_configured', 'pending', 'active', 'ended'.
     */
    public static function get_dean_period_status(\stdClass $convocatoria): string {
        if (empty($convocatoria->dean_review_startdate) ||
            empty($convocatoria->dean_review_enddate)) {
            return 'not_configured';
        }

        $now = time();

        if ($now < $convocatoria->dean_review_startdate) {
            return 'pending';
        }

        if ($now > $convocatoria->dean_review_enddate) {
            return 'ended';
        }

        return 'active';
    }

    /**
     * Get review period status for HR.
     *
     * @param \stdClass $convocatoria The convocatoria record.
     * @return string Status: 'not_configured', 'pending', 'active', 'ended'.
     */
    public static function get_hr_period_status(\stdClass $convocatoria): string {
        if (empty($convocatoria->hr_review_startdate) ||
            empty($convocatoria->hr_review_enddate)) {
            return 'not_configured';
        }

        $now = time();

        if ($now < $convocatoria->hr_review_startdate) {
            return 'pending';
        }

        if ($now > $convocatoria->hr_review_enddate) {
            return 'ended';
        }

        return 'active';
    }

    /**
     * Get applications visible for a role in a convocatoria.
     *
     * @param int $convocatoriaid The convocatoria ID.
     * @param string $role The role shortname.
     * @return array Array of application records.
     */
    public static function get_visible_applications(int $convocatoriaid, string $role): array {
        global $DB;

        // Get vacancies for this convocatoria.
        $vacancyids = $DB->get_fieldset_select(
            'local_jobboard_vacancy',
            'id',
            'convocatoriaid = :convocatoriaid',
            ['convocatoriaid' => $convocatoriaid]
        );

        if (empty($vacancyids)) {
            return [];
        }

        list($insql, $params) = $DB->get_in_or_equal($vacancyids, SQL_PARAMS_NAMED);

        switch ($role) {
            case 'jobboard_dean':
                // Only applications pending dean review.
                $params['status'] = 'pending_dean_review';
                return $DB->get_records_select(
                    'local_jobboard_application',
                    "vacancyid $insql AND status = :status",
                    $params
                );

            case 'jobboard_hr':
                // Only applications pending HR validation.
                $params['status'] = 'pending_hr_validation';
                return $DB->get_records_select(
                    'local_jobboard_application',
                    "vacancyid $insql AND status = :status",
                    $params
                );

            default:
                return [];
        }
    }

    /**
     * Check if user has Dean role.
     *
     * @param int $userid User ID (0 for current user).
     * @return bool True if user is Dean.
     */
    public static function is_dean(int $userid = 0): bool {
        return self::has_role($userid, 'jobboard_dean');
    }

    /**
     * Check if user has HR role.
     *
     * @param int $userid User ID (0 for current user).
     * @return bool True if user is HR.
     */
    public static function is_hr(int $userid = 0): bool {
        return self::has_role($userid, 'jobboard_hr');
    }

    /**
     * Check if user has a specific role.
     *
     * @param int $userid User ID (0 for current user).
     * @param string $roleshortname Role shortname.
     * @return bool True if user has the role.
     */
    public static function has_role(int $userid, string $roleshortname): bool {
        global $USER, $DB;

        if (!$userid) {
            $userid = $USER->id;
        }

        $context = \context_system::instance();
        $role = $DB->get_record('role', ['shortname' => $roleshortname]);

        if (!$role) {
            return false;
        }

        return $DB->record_exists('role_assignments', [
            'roleid' => $role->id,
            'contextid' => $context->id,
            'userid' => $userid,
        ]);
    }

    /**
     * Get the jobboard role for a user.
     *
     * @param int $userid User ID (0 for current user).
     * @return string|null Role shortname or null if no jobboard role.
     */
    public static function get_user_jobboard_role(int $userid = 0): ?string {
        global $USER, $DB;

        if (!$userid) {
            $userid = $USER->id;
        }

        $context = \context_system::instance();
        $roles = get_user_roles($context, $userid);

        // Priority order for jobboard roles.
        // Only Dean and HR roles exist now.
        $jobboardroles = [
            'jobboard_dean',
            'jobboard_hr',
        ];

        foreach ($roles as $role) {
            if (in_array($role->shortname, $jobboardroles)) {
                return $role->shortname;
            }
        }

        return null;
    }

    /**
     * Check if user can approve/reject profiles (Dean action).
     *
     * @param \stdClass $convocatoria The convocatoria record.
     * @param int $userid User ID (0 for current user).
     * @return bool True if user can perform Dean actions.
     */
    public static function can_approve_profiles(\stdClass $convocatoria, int $userid = 0): bool {
        if (!self::is_dean($userid)) {
            return false;
        }

        return self::can_dean_access($convocatoria);
    }

    /**
     * Check if user can validate documents (HR action).
     *
     * @param \stdClass $convocatoria The convocatoria record.
     * @param int $userid User ID (0 for current user).
     * @return bool True if user can perform HR actions.
     */
    public static function can_validate_documents(\stdClass $convocatoria, int $userid = 0): bool {
        if (!self::is_hr($userid)) {
            return false;
        }

        return self::can_hr_access($convocatoria);
    }

    /**
     * Get convocatoria from vacancy.
     *
     * @param int $vacancyid The vacancy ID.
     * @return \stdClass|null The convocatoria record or null.
     */
    public static function get_convocatoria_from_vacancy(int $vacancyid): ?\stdClass {
        global $DB;

        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid]);
        if (!$vacancy || !$vacancy->convocatoriaid) {
            return null;
        }

        return $DB->get_record('local_jobboard_convocatoria', ['id' => $vacancy->convocatoriaid]);
    }
}

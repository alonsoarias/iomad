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
 * IOMAD helper class for local_jobboard.
 *
 * Provides IOMAD multi-tenant utilities for companies and departments.
 * Extracted from lib.php for better code organization.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\helper;

defined('MOODLE_INTERNAL') || die();

/**
 * IOMAD helper class.
 */
class iomad_helper {

    /**
     * Check if IOMAD is installed.
     *
     * @return bool True if IOMAD is installed.
     */
    public static function is_iomad_installed(): bool {
        global $DB;

        $dbman = $DB->get_manager();
        return $dbman->table_exists('company') && $dbman->table_exists('company_users');
    }

    /**
     * Get IOMAD installation type and details.
     *
     * @return array Installation info with keys: is_iomad, version, has_departments, has_companies.
     */
    public static function get_iomad_info(): array {
        global $DB;

        $info = [
            'is_iomad' => false,
            'version' => null,
            'has_departments' => false,
            'has_companies' => false,
        ];

        $dbman = $DB->get_manager();

        if (!$dbman->table_exists('company')) {
            return $info;
        }

        $info['is_iomad'] = true;
        $info['has_companies'] = true;

        if ($dbman->table_exists('department')) {
            $info['has_departments'] = true;
        }

        $iomadversion = get_config('local_iomad', 'version');
        if ($iomadversion) {
            $info['version'] = $iomadversion;
        }

        return $info;
    }

    /**
     * Get the user's company ID (for IOMAD multi-tenant).
     *
     * @param int|null $userid User ID or null for current user.
     * @return int|null Company ID or null if not in a company.
     */
    public static function get_user_companyid(?int $userid = null): ?int {
        global $DB, $USER;

        $userid = $userid ?? $USER->id;

        if (!file_exists(__DIR__ . '/../../../../local/iomad/lib/company.php')) {
            return null;
        }

        require_once(__DIR__ . '/../../../../local/iomad/lib/company.php');

        $companies = $DB->get_records_sql(
            "SELECT companyid FROM {company_users} WHERE userid = :userid ORDER BY lastused DESC, companyid DESC",
            ['userid' => $userid]
        );

        if (!empty($companies)) {
            $first = reset($companies);
            return (int) $first->companyid;
        }

        return null;
    }

    /**
     * Check if user can view a vacancy (respects multi-tenant).
     *
     * @param \stdClass $vacancy The vacancy record.
     * @param int|null $userid User ID or null for current user.
     * @return bool True if user can view the vacancy.
     */
    public static function can_user_view_vacancy(\stdClass $vacancy, ?int $userid = null): bool {
        global $USER, $DB;

        $userid = $userid ?? $USER->id;
        $context = \context_system::instance();

        // Admins/managers can always view.
        if (has_capability('local/jobboard:viewallvacancies', $context, $userid)) {
            return true;
        }

        // Users with an existing application can always view the vacancy
        // (even if closed) to check their application status.
        $hasapplication = $DB->record_exists('local_jobboard_application', [
            'vacancyid' => $vacancy->id,
            'userid' => $userid,
        ]);
        if ($hasapplication) {
            return true;
        }

        // For users without applications, vacancy must be published.
        if ($vacancy->status !== 'published') {
            return false;
        }

        if (empty($vacancy->companyid)) {
            return true;
        }

        $usercompanyid = self::get_user_companyid($userid);
        return $usercompanyid === (int) $vacancy->companyid;
    }

    /**
     * Get company name by ID.
     *
     * @param int $companyid The company ID.
     * @return string Company name or empty string.
     */
    public static function get_company_name(int $companyid): string {
        global $DB;

        if (!$companyid) {
            return '';
        }

        $company = $DB->get_record('company', ['id' => $companyid], 'name');
        return $company ? $company->name : '';
    }

    /**
     * Get list of available companies for dropdown.
     *
     * @return array Company ID => name.
     */
    public static function get_companies(): array {
        global $DB;

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('company')) {
            return [];
        }

        $companies = $DB->get_records('company', ['suspended' => 0], 'name ASC', 'id, name');

        $result = [];
        foreach ($companies as $company) {
            $result[$company->id] = $company->name;
        }

        return $result;
    }

    /**
     * Get list of departments for a company (IOMAD).
     *
     * Only returns child departments (parent > 0), excluding the root department.
     * In IOMAD, each company has a root department (parent = 0) that serves as
     * a container, and the actual departments are children of this root.
     *
     * @param int $companyid The company ID.
     * @param bool $includeroot Whether to include the root department (default: false).
     * @return array Department ID => name.
     */
    public static function get_departments(int $companyid, bool $includeroot = false): array {
        global $DB;

        if (!$companyid) {
            return [];
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('department')) {
            return [];
        }

        $sql = "SELECT id, name, parent FROM {department} WHERE company = :company";
        $params = ['company' => $companyid];

        if (!$includeroot) {
            $sql .= " AND parent > 0";
        }

        $sql .= " ORDER BY name ASC";

        $departments = $DB->get_records_sql($sql, $params);

        $result = [];
        foreach ($departments as $dept) {
            $result[$dept->id] = $dept->name;
        }

        return $result;
    }

    /**
     * Get department name by ID.
     *
     * @param int $departmentid The department ID.
     * @return string Department name or empty string.
     */
    public static function get_department_name(int $departmentid): string {
        global $DB;

        if (!$departmentid) {
            return '';
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('department')) {
            return '';
        }

        $dept = $DB->get_record('department', ['id' => $departmentid], 'name');
        return $dept ? $dept->name : '';
    }

    /**
     * Get list of companies the user has access to.
     *
     * For site admins, returns all companies.
     * For company managers, returns only their assigned companies.
     *
     * @param int|null $userid User ID or null for current user.
     * @return array Company ID => name.
     */
    public static function get_user_companies(?int $userid = null): array {
        global $DB, $USER;

        $userid = $userid ?? $USER->id;

        if (is_siteadmin($userid)) {
            return self::get_companies();
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('company_users')) {
            return [];
        }

        $sql = "SELECT c.id, c.name
                FROM {company} c
                JOIN {company_users} cu ON cu.companyid = c.id
                WHERE cu.userid = :userid AND c.suspended = 0
                ORDER BY c.name ASC";

        $companies = $DB->get_records_sql($sql, ['userid' => $userid]);

        $result = [];
        foreach ($companies as $company) {
            $result[$company->id] = $company->name;
        }

        return $result;
    }

    /**
     * Check if user belongs to a specific company.
     *
     * @param int $companyid The company ID.
     * @param int|null $userid User ID or null for current user.
     * @return bool True if user belongs to the company.
     */
    public static function user_belongs_to_company(int $companyid, ?int $userid = null): bool {
        global $DB, $USER;

        $userid = $userid ?? $USER->id;

        if (is_siteadmin($userid)) {
            return true;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('company_users')) {
            return false;
        }

        return $DB->record_exists('company_users', [
            'userid' => $userid,
            'companyid' => $companyid,
        ]);
    }

    /**
     * Assign a user to a company, removing from previous company if needed.
     *
     * This function handles:
     * - New user assignment to a company
     * - Changing user from one company to another
     * - Getting the default (root) department if none specified
     *
     * @param int $userid The user ID.
     * @param int $companyid The company ID to assign to.
     * @param int $departmentid The department ID (optional, will use root department if 0).
     * @return bool True on success, false on failure.
     */
    public static function assign_user_to_company(int $userid, int $companyid, int $departmentid = 0): bool {
        global $DB, $CFG;

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('company_users') || !$dbman->table_exists('company')) {
            return false;
        }

        // Verify company exists and is not suspended.
        $company = $DB->get_record('company', ['id' => $companyid, 'suspended' => 0]);
        if (!$company) {
            return false;
        }

        // Get the default (root) department for the company if none specified.
        if (!$departmentid && $dbman->table_exists('department')) {
            $rootdept = $DB->get_record('department', [
                'company' => $companyid,
                'parent' => 0,
            ], 'id', IGNORE_MULTIPLE);
            if ($rootdept) {
                $departmentid = $rootdept->id;
            }
        }

        // Validate department belongs to this company if specified.
        if ($departmentid && $dbman->table_exists('department')) {
            $dept = $DB->get_record('department', ['id' => $departmentid, 'company' => $companyid]);
            if (!$dept) {
                // Department doesn't belong to this company, get root department.
                $rootdept = $DB->get_record('department', [
                    'company' => $companyid,
                    'parent' => 0,
                ], 'id', IGNORE_MULTIPLE);
                if ($rootdept) {
                    $departmentid = $rootdept->id;
                }
            }
        }

        // Check if user already has any company assignments.
        $existingassignments = $DB->get_records('company_users', ['userid' => $userid]);

        // Check if already assigned to this exact company and department.
        $alreadyassigned = false;
        foreach ($existingassignments as $assignment) {
            if ((int)$assignment->companyid === $companyid) {
                $alreadyassigned = true;
                // Update the department and lastused if different.
                if ((int)$assignment->departmentid !== $departmentid || empty($assignment->lastused)) {
                    $assignment->departmentid = $departmentid;
                    $assignment->lastused = time();
                    $DB->update_record('company_users', $assignment);
                }
                break;
            }
        }

        if (!$alreadyassigned) {
            // Try to use IOMAD's native API if available.
            $iomadcompanyfile = $CFG->dirroot . '/local/iomad/lib/company.php';
            if (file_exists($iomadcompanyfile)) {
                require_once($iomadcompanyfile);

                // Remove from all previous companies first (for jobboard users, we want single company assignment).
                foreach ($existingassignments as $assignment) {
                    $DB->delete_records('company_users', ['id' => $assignment->id]);
                }

                // Use IOMAD's company class for proper assignment.
                try {
                    $companyobj = new \company($companyid);
                    // managertype = 0 (regular user), ws = false, import = false
                    return $companyobj->assign_user_to_company($userid, $departmentid, 0, false, false);
                } catch (\Exception $e) {
                    // Fallback to direct insert if IOMAD API fails.
                    debugging('IOMAD API failed, using direct insert: ' . $e->getMessage(), DEBUG_DEVELOPER);
                }
            }

            // Fallback: Direct database insert.
            // First remove any existing company assignments (single company per jobboard user).
            $DB->delete_records('company_users', ['userid' => $userid]);

            // Create the new company user record.
            $companyuser = new \stdClass();
            $companyuser->companyid = $companyid;
            $companyuser->userid = $userid;
            $companyuser->departmentid = $departmentid;
            $companyuser->managertype = 0; // Regular user.
            $companyuser->educator = 0;
            $companyuser->suspended = 0;
            $companyuser->lastused = time();

            return (bool) $DB->insert_record('company_users', $companyuser);
        }

        return true;
    }

    /**
     * Change a user's company assignment, removing from old company and assigning to new.
     *
     * @param int $userid The user ID.
     * @param int $newcompanyid The new company ID.
     * @param int $newdepartmentid The new department ID (optional).
     * @return bool True on success, false on failure.
     */
    public static function change_user_company(int $userid, int $newcompanyid, int $newdepartmentid = 0): bool {
        return self::assign_user_to_company($userid, $newcompanyid, $newdepartmentid);
    }

    /**
     * Get user's current company assignment details.
     *
     * @param int|null $userid User ID or null for current user.
     * @return object|null Object with companyid, departmentid, companyname, departmentname or null.
     */
    public static function get_user_company_assignment(?int $userid = null): ?object {
        global $DB, $USER;

        $userid = $userid ?? $USER->id;

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('company_users')) {
            return null;
        }

        $sql = "SELECT cu.id, cu.companyid, cu.departmentid, cu.managertype, cu.lastused,
                       c.name as companyname, c.shortname as companyshortname
                FROM {company_users} cu
                JOIN {company} c ON c.id = cu.companyid
                WHERE cu.userid = :userid
                ORDER BY cu.lastused DESC, cu.id DESC
                LIMIT 1";

        $assignment = $DB->get_record_sql($sql, ['userid' => $userid]);

        if ($assignment && $dbman->table_exists('department')) {
            $dept = $DB->get_record('department', ['id' => $assignment->departmentid], 'name');
            $assignment->departmentname = $dept ? $dept->name : '';
        }

        return $assignment ?: null;
    }
}

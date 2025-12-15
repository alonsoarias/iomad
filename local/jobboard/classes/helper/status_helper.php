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
 * Status helper class for local_jobboard.
 *
 * Provides status-related utilities for vacancies, applications, and documents.
 * Extracted from lib.php for better code organization.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\helper;

defined('MOODLE_INTERNAL') || die();

/**
 * Status helper class.
 */
class status_helper {

    /** @var string Vacancy status: draft */
    const VACANCY_DRAFT = 'draft';

    /** @var string Vacancy status: published */
    const VACANCY_PUBLISHED = 'published';

    /** @var string Vacancy status: closed */
    const VACANCY_CLOSED = 'closed';

    /** @var string Vacancy status: cancelled */
    const VACANCY_CANCELLED = 'cancelled';

    /** @var string Application status: draft */
    const APP_DRAFT = 'draft';

    /** @var string Application status: submitted */
    const APP_SUBMITTED = 'submitted';

    /** @var string Application status: under_review */
    const APP_UNDER_REVIEW = 'under_review';

    /** @var string Application status: approved */
    const APP_APPROVED = 'approved';

    /** @var string Application status: rejected */
    const APP_REJECTED = 'rejected';

    /** @var string Application status: withdrawn */
    const APP_WITHDRAWN = 'withdrawn';

    /** @var string Document status: pending */
    const DOC_PENDING = 'pending';

    /** @var string Document status: validated */
    const DOC_VALIDATED = 'validated';

    /** @var string Document status: rejected */
    const DOC_REJECTED = 'rejected';

    /** @var string Document status: reupload_required */
    const DOC_REUPLOAD = 'reupload_required';

    /**
     * Get vacancy status options.
     *
     * @return array Status code => label pairs.
     */
    public static function get_vacancy_statuses(): array {
        return [
            self::VACANCY_DRAFT => get_string('status_draft', 'local_jobboard'),
            self::VACANCY_PUBLISHED => get_string('status_published', 'local_jobboard'),
            self::VACANCY_CLOSED => get_string('status_closed', 'local_jobboard'),
            self::VACANCY_CANCELLED => get_string('status_cancelled', 'local_jobboard'),
        ];
    }

    /**
     * Get application status options.
     *
     * @return array Status code => label pairs.
     */
    public static function get_application_statuses(): array {
        return [
            self::APP_DRAFT => get_string('status_draft', 'local_jobboard'),
            self::APP_SUBMITTED => get_string('status_submitted', 'local_jobboard'),
            self::APP_UNDER_REVIEW => get_string('status_under_review', 'local_jobboard'),
            self::APP_APPROVED => get_string('status_approved', 'local_jobboard'),
            self::APP_REJECTED => get_string('status_rejected', 'local_jobboard'),
            self::APP_WITHDRAWN => get_string('status_withdrawn', 'local_jobboard'),
        ];
    }

    /**
     * Get document validation status options.
     *
     * @return array Status code => label pairs.
     */
    public static function get_document_statuses(): array {
        return [
            self::DOC_PENDING => get_string('status_pending', 'local_jobboard'),
            self::DOC_VALIDATED => get_string('status_validated', 'local_jobboard'),
            self::DOC_REJECTED => get_string('status_rejected', 'local_jobboard'),
            self::DOC_REUPLOAD => get_string('status_reupload', 'local_jobboard'),
        ];
    }

    /**
     * Get allowed status transitions for applications.
     *
     * @return array From status => [allowed to statuses].
     */
    public static function get_allowed_transitions(): array {
        return [
            self::APP_DRAFT => [self::APP_SUBMITTED, self::APP_WITHDRAWN],
            self::APP_SUBMITTED => [self::APP_UNDER_REVIEW, self::APP_WITHDRAWN],
            self::APP_UNDER_REVIEW => [self::APP_APPROVED, self::APP_REJECTED, self::APP_SUBMITTED],
            self::APP_APPROVED => [],
            self::APP_REJECTED => [self::APP_UNDER_REVIEW],
            self::APP_WITHDRAWN => [],
        ];
    }

    /**
     * Check if a status transition is allowed.
     *
     * @param string $from Current status.
     * @param string $to Target status.
     * @return bool True if transition is allowed.
     */
    public static function is_transition_allowed(string $from, string $to): bool {
        $transitions = self::get_allowed_transitions();
        return isset($transitions[$from]) && in_array($to, $transitions[$from]);
    }

    /**
     * Get contract type options.
     *
     * @return array Type code => label pairs.
     */
    public static function get_contract_types(): array {
        return [
            'full_time' => get_string('contract_fulltime', 'local_jobboard'),
            'part_time' => get_string('contract_parttime', 'local_jobboard'),
            'temporary' => get_string('contract_temporary', 'local_jobboard'),
            'hourly' => get_string('contract_hourly', 'local_jobboard'),
        ];
    }

    /**
     * Get work modality options.
     *
     * @return array Modality code => label pairs.
     */
    public static function get_modalities(): array {
        return [
            'onsite' => get_string('modality_onsite', 'local_jobboard'),
            'remote' => get_string('modality_remote', 'local_jobboard'),
            'hybrid' => get_string('modality_hybrid', 'local_jobboard'),
        ];
    }

    /**
     * Get status badge CSS class.
     *
     * @param string $status Status code.
     * @param string $type Type of status (vacancy, application, document).
     * @return string Bootstrap badge class.
     */
    public static function get_status_badge_class(string $status, string $type = 'application'): string {
        $classes = [
            'vacancy' => [
                self::VACANCY_DRAFT => 'bg-secondary',
                self::VACANCY_PUBLISHED => 'bg-success',
                self::VACANCY_CLOSED => 'bg-warning',
                self::VACANCY_CANCELLED => 'bg-danger',
            ],
            'application' => [
                self::APP_DRAFT => 'bg-secondary',
                self::APP_SUBMITTED => 'bg-info',
                self::APP_UNDER_REVIEW => 'bg-warning',
                self::APP_APPROVED => 'bg-success',
                self::APP_REJECTED => 'bg-danger',
                self::APP_WITHDRAWN => 'bg-dark',
            ],
            'document' => [
                self::DOC_PENDING => 'bg-warning',
                self::DOC_VALIDATED => 'bg-success',
                self::DOC_REJECTED => 'bg-danger',
                self::DOC_REUPLOAD => 'bg-info',
            ],
        ];

        return $classes[$type][$status] ?? 'bg-secondary';
    }

    /**
     * Get status label for display.
     *
     * @param string $status Status code.
     * @param string $type Type of status (vacancy, application, document).
     * @return string Localized status label.
     */
    public static function get_status_label(string $status, string $type = 'application'): string {
        switch ($type) {
            case 'vacancy':
                $statuses = self::get_vacancy_statuses();
                break;
            case 'document':
                $statuses = self::get_document_statuses();
                break;
            default:
                $statuses = self::get_application_statuses();
        }

        return $statuses[$status] ?? $status;
    }
}

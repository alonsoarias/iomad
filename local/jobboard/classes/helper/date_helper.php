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
 * Date helper class for local_jobboard.
 *
 * Provides date formatting and calculation utilities.
 * Extracted from lib.php for better code organization.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\helper;

defined('MOODLE_INTERNAL') || die();

/**
 * Date helper class.
 */
class date_helper {

    /** @var string Default date format */
    const FORMAT_DATE = '%d/%m/%Y';

    /** @var string Default datetime format */
    const FORMAT_DATETIME = '%d/%m/%Y %H:%M';

    /** @var string Short date format */
    const FORMAT_DATE_SHORT = '%d/%m/%y';

    /** @var string ISO date format */
    const FORMAT_ISO = '%Y-%m-%d';

    /** @var int Seconds in a day */
    const SECONDS_PER_DAY = 86400;

    /**
     * Format a timestamp for display.
     *
     * @param int|null $timestamp Unix timestamp.
     * @param string $format Date format (strftime format).
     * @return string Formatted date or empty string if null.
     */
    public static function format_date(?int $timestamp, string $format = self::FORMAT_DATE): string {
        if ($timestamp === null || $timestamp === 0) {
            return '';
        }
        return userdate($timestamp, $format);
    }

    /**
     * Format a timestamp for datetime display.
     *
     * @param int|null $timestamp Unix timestamp.
     * @return string Formatted datetime or empty string if null.
     */
    public static function format_datetime(?int $timestamp): string {
        if ($timestamp === null || $timestamp === 0) {
            return '';
        }
        return userdate($timestamp, self::FORMAT_DATETIME);
    }

    /**
     * Calculate days between two timestamps.
     *
     * @param int $from Start timestamp.
     * @param int $to End timestamp.
     * @return int Number of days (absolute value).
     */
    public static function days_between(int $from, int $to): int {
        return (int) floor(abs($to - $from) / self::SECONDS_PER_DAY);
    }

    /**
     * Calculate days remaining until a timestamp.
     *
     * @param int $timestamp Target timestamp.
     * @return int Days remaining (negative if past).
     */
    public static function days_remaining(int $timestamp): int {
        $now = time();
        $diff = $timestamp - $now;
        return (int) floor($diff / self::SECONDS_PER_DAY);
    }

    /**
     * Check if a timestamp is in the past.
     *
     * @param int $timestamp The timestamp to check.
     * @return bool True if in the past.
     */
    public static function is_past(int $timestamp): bool {
        return $timestamp < time();
    }

    /**
     * Check if a timestamp is in the future.
     *
     * @param int $timestamp The timestamp to check.
     * @return bool True if in the future.
     */
    public static function is_future(int $timestamp): bool {
        return $timestamp > time();
    }

    /**
     * Check if current time is within a date range.
     *
     * @param int $start Start timestamp.
     * @param int $end End timestamp.
     * @return bool True if current time is within range.
     */
    public static function is_within_range(int $start, int $end): bool {
        $now = time();
        return $now >= $start && $now <= $end;
    }

    /**
     * Get a human-readable relative time string.
     *
     * @param int $timestamp The timestamp.
     * @return string Relative time (e.g., "2 days ago", "in 3 hours").
     */
    public static function get_relative_time(int $timestamp): string {
        $now = time();
        $diff = $now - $timestamp;
        $absdiff = abs($diff);
        $future = $diff < 0;

        if ($absdiff < 60) {
            return get_string('justnow', 'local_jobboard');
        }

        if ($absdiff < 3600) {
            $minutes = floor($absdiff / 60);
            $key = $future ? 'inminutes' : 'minutesago';
            return get_string($key, 'local_jobboard', $minutes);
        }

        if ($absdiff < self::SECONDS_PER_DAY) {
            $hours = floor($absdiff / 3600);
            $key = $future ? 'inhours' : 'hoursago';
            return get_string($key, 'local_jobboard', $hours);
        }

        $days = floor($absdiff / self::SECONDS_PER_DAY);
        if ($days < 30) {
            $key = $future ? 'indays' : 'daysago';
            return get_string($key, 'local_jobboard', $days);
        }

        // For older dates, just return the formatted date.
        return self::format_date($timestamp);
    }

    /**
     * Parse a date string to timestamp.
     *
     * Supports various formats including DD/MM/YYYY, YYYY-MM-DD.
     *
     * @param string $datestring The date string.
     * @return int|null Unix timestamp or null if invalid.
     */
    public static function parse_date(string $datestring): ?int {
        $datestring = trim($datestring);

        if (empty($datestring)) {
            return null;
        }

        // Try DD/MM/YYYY format.
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $datestring, $matches)) {
            $timestamp = mktime(0, 0, 0, (int) $matches[2], (int) $matches[1], (int) $matches[4]);
            return $timestamp !== false ? $timestamp : null;
        }

        // Try YYYY-MM-DD format.
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $datestring, $matches)) {
            $timestamp = mktime(0, 0, 0, (int) $matches[2], (int) $matches[3], (int) $matches[1]);
            return $timestamp !== false ? $timestamp : null;
        }

        // Try strtotime as fallback.
        $timestamp = strtotime($datestring);
        return $timestamp !== false ? $timestamp : null;
    }

    /**
     * Get start of day timestamp.
     *
     * @param int|null $timestamp The timestamp or null for now.
     * @return int Start of day timestamp.
     */
    public static function start_of_day(?int $timestamp = null): int {
        $timestamp = $timestamp ?? time();
        return strtotime('today', $timestamp);
    }

    /**
     * Get end of day timestamp.
     *
     * @param int|null $timestamp The timestamp or null for now.
     * @return int End of day timestamp (23:59:59).
     */
    public static function end_of_day(?int $timestamp = null): int {
        $timestamp = $timestamp ?? time();
        return strtotime('tomorrow', $timestamp) - 1;
    }

    /**
     * Get the age in years from a birthdate timestamp.
     *
     * @param int $birthdate Birthdate timestamp.
     * @return int Age in years.
     */
    public static function calculate_age(int $birthdate): int {
        $birth = new \DateTime();
        $birth->setTimestamp($birthdate);
        $now = new \DateTime();
        return $now->diff($birth)->y;
    }

    /**
     * Format a date range for display.
     *
     * @param int $start Start timestamp.
     * @param int $end End timestamp.
     * @param string $separator Separator between dates.
     * @return string Formatted date range.
     */
    public static function format_range(int $start, int $end, string $separator = ' - '): string {
        return self::format_date($start) . $separator . self::format_date($end);
    }

    /**
     * Check if a convocatoria/vacancy period is currently active.
     *
     * @param int|null $startdate Start timestamp (null means no start restriction).
     * @param int|null $enddate End timestamp (null means no end restriction).
     * @return bool True if currently active.
     */
    public static function is_period_active(?int $startdate, ?int $enddate): bool {
        $now = time();

        if ($startdate !== null && $now < $startdate) {
            return false;
        }

        if ($enddate !== null && $now > $enddate) {
            return false;
        }

        return true;
    }

    /**
     * Get urgency level based on deadline.
     *
     * @param int $deadline The deadline timestamp.
     * @return string Urgency level: 'critical', 'warning', 'normal'.
     */
    public static function get_deadline_urgency(int $deadline): string {
        $days = self::days_remaining($deadline);

        if ($days < 0) {
            return 'expired';
        }

        if ($days <= 2) {
            return 'critical';
        }

        if ($days <= 7) {
            return 'warning';
        }

        return 'normal';
    }
}

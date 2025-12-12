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

declare(strict_types=1);

/**
 * Base renderer class for Job Board plugin.
 *
 * Contains shared utilities and helper methods used by all specialized renderers.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use moodle_url;

/**
 * Base renderer class with shared utilities.
 *
 * This class provides common helper methods and status mappings
 * that are used across all specialized Job Board renderers.
 */
abstract class renderer_base extends plugin_renderer_base {

    /**
     * CSS class mappings for vacancy status.
     */
    protected const STATUS_CLASSES = [
        'draft' => 'secondary',
        'published' => 'success',
        'closed' => 'danger',
        'archived' => 'dark',
        'pending' => 'warning',
    ];

    /**
     * CSS class mappings for application status.
     */
    protected const APPLICATION_STATUS_CLASSES = [
        'draft' => 'secondary',
        'submitted' => 'info',
        'reviewing' => 'primary',
        'approved' => 'success',
        'rejected' => 'danger',
        'withdrawn' => 'dark',
        'interview' => 'warning',
        'hired' => 'success',
    ];

    /**
     * CSS class mappings for document validation status.
     */
    protected const DOCUMENT_STATUS_CLASSES = [
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'expired' => 'dark',
        'reviewing' => 'info',
    ];

    /**
     * CSS class mappings for convocatoria status.
     */
    protected const CONVOCATORIA_STATUS_CLASSES = [
        'draft' => 'secondary',
        'open' => 'success',
        'closed' => 'danger',
        'archived' => 'dark',
    ];

    /**
     * Get CSS class for vacancy status.
     *
     * @param string $status Status code.
     * @return string CSS class.
     */
    public function get_status_class(string $status): string {
        return self::STATUS_CLASSES[$status] ?? 'secondary';
    }

    /**
     * Get CSS class for application status.
     *
     * @param string $status Status code.
     * @return string CSS class.
     */
    public function get_application_status_class(string $status): string {
        return self::APPLICATION_STATUS_CLASSES[$status] ?? 'secondary';
    }

    /**
     * Get CSS class for document validation status.
     *
     * @param string $status Status code.
     * @return string CSS class.
     */
    public function get_document_status_class(string $status): string {
        return self::DOCUMENT_STATUS_CLASSES[$status] ?? 'secondary';
    }

    /**
     * Get CSS class for convocatoria status.
     *
     * @param string $status Status code.
     * @return string CSS class.
     */
    public function get_convocatoria_status_class(string $status): string {
        return self::CONVOCATORIA_STATUS_CLASSES[$status] ?? 'secondary';
    }

    /**
     * Calculate days remaining until a date.
     *
     * @param int $timestamp Target timestamp.
     * @return int Number of days remaining (0 if past).
     */
    protected function calculate_days_remaining(int $timestamp): int {
        return max(0, (int) floor(($timestamp - time()) / 86400));
    }

    /**
     * Check if a date is urgent (within threshold days).
     *
     * @param int $timestamp Target timestamp.
     * @param int $threshold Number of days threshold.
     * @return bool True if urgent.
     */
    protected function is_date_urgent(int $timestamp, int $threshold = 7): bool {
        return $this->calculate_days_remaining($timestamp) <= $threshold;
    }

    /**
     * Format a date using standard format.
     *
     * @param int $timestamp Timestamp to format.
     * @param string $format Optional format string key.
     * @return string Formatted date.
     */
    protected function format_date(int $timestamp, string $format = 'strftimedate'): string {
        return userdate($timestamp, get_string($format, 'local_jobboard'));
    }

    /**
     * Format a datetime using standard format.
     *
     * @param int $timestamp Timestamp to format.
     * @return string Formatted datetime.
     */
    protected function format_datetime(int $timestamp): string {
        return userdate($timestamp, get_string('strftimedatetime', 'local_jobboard'));
    }

    /**
     * Build a moodle_url for the plugin.
     *
     * @param string $view View name.
     * @param array $params Additional parameters.
     * @return moodle_url URL object.
     */
    protected function build_url(string $view, array $params = []): moodle_url {
        return new moodle_url('/local/jobboard/index.php', array_merge(['view' => $view], $params));
    }

    /**
     * Get URL string for a view.
     *
     * @param string $view View name.
     * @param array $params Additional parameters.
     * @return string URL string.
     */
    protected function get_url(string $view, array $params = []): string {
        return $this->build_url($view, $params)->out(false);
    }

    /**
     * Shorten text to a maximum length.
     *
     * @param string $text Text to shorten.
     * @param int $maxlength Maximum length.
     * @param bool $striphtml Whether to strip HTML tags.
     * @return string Shortened text.
     */
    protected function shorten_text(string $text, int $maxlength = 150, bool $striphtml = true): string {
        if ($striphtml) {
            $text = strip_tags($text);
        }
        return shorten_text($text, $maxlength);
    }

    /**
     * Get full name of a user.
     *
     * @param int $userid User ID.
     * @return string Full name or empty string if not found.
     */
    protected function get_user_fullname(int $userid): string {
        $user = \core_user::get_user($userid);
        return $user ? fullname($user) : '';
    }

    /**
     * Calculate percentage.
     *
     * @param int $value Current value.
     * @param int $total Total value.
     * @return int Percentage (0-100).
     */
    protected function calculate_percentage(int $value, int $total): int {
        if ($total <= 0) {
            return 0;
        }
        return (int) round(($value / $total) * 100);
    }

    /**
     * Render an alert message.
     *
     * @param string $message Message text.
     * @param string $type Alert type (success, warning, danger, info).
     * @param bool $dismissible Whether alert can be dismissed.
     * @return string HTML output.
     */
    public function render_alert(string $message, string $type = 'info', bool $dismissible = true): string {
        return $this->render_from_template('local_jobboard/alert', [
            'message' => $message,
            'type' => $type,
            'dismissible' => $dismissible,
        ]);
    }

    /**
     * Render an empty state message.
     *
     * @param string $message Message to display.
     * @param string $icon Optional icon name.
     * @param string $actionurl Optional action URL.
     * @param string $actionlabel Optional action button label.
     * @return string HTML output.
     */
    public function render_empty_state(
        string $message,
        string $icon = 'inbox',
        string $actionurl = '',
        string $actionlabel = ''
    ): string {
        return $this->render_from_template('local_jobboard/empty_state', [
            'message' => $message,
            'icon' => $icon,
            'hasaction' => !empty($actionurl) && !empty($actionlabel),
            'actionurl' => $actionurl,
            'actionlabel' => $actionlabel,
        ]);
    }

    /**
     * Render a loading indicator.
     *
     * @param string $message Optional loading message.
     * @return string HTML output.
     */
    public function render_loading(string $message = ''): string {
        if (empty($message)) {
            $message = get_string('loading', 'local_jobboard');
        }
        return $this->render_from_template('local_jobboard/loading', [
            'message' => $message,
        ]);
    }

    /**
     * Render a badge.
     *
     * @param string $text Badge text.
     * @param string $type Badge type (primary, secondary, success, etc.).
     * @return string HTML output.
     */
    public function render_badge(string $text, string $type = 'secondary'): string {
        return '<span class="jb-badge jb-badge-' . $type . '">' . s($text) . '</span>';
    }

    /**
     * Render a status badge.
     *
     * @param string $status Status code.
     * @param string $type Status type (vacancy, application, document, convocatoria).
     * @return string HTML output.
     */
    public function render_status_badge(string $status, string $type = 'vacancy'): string {
        $method = 'get_' . ($type === 'vacancy' ? '' : $type . '_') . 'status_class';
        if (!method_exists($this, $method)) {
            $method = 'get_status_class';
        }
        $class = $this->$method($status);
        $label = get_string($type . 'status:' . $status, 'local_jobboard');
        return $this->render_badge($label, $class);
    }
}

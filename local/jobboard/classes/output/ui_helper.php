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
 * UI Helper class for consistent rendering across local_jobboard views.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class for rendering consistent UI components.
 */
class ui_helper {

    /**
     * Render page header with breadcrumb and title.
     *
     * @param string $title Page title.
     * @param array $breadcrumbs Array of [label => url] pairs.
     * @param array $actions Optional action buttons.
     * @return string HTML output.
     */
    public static function page_header(string $title, array $breadcrumbs = [], array $actions = []): string {
        $html = \html_writer::start_div('jb-page-header d-flex justify-content-between align-items-start mb-4');

        // Left side: breadcrumb and title.
        $html .= \html_writer::start_div('jb-header-left');

        // Breadcrumb.
        if (!empty($breadcrumbs)) {
            $html .= \html_writer::start_tag('nav', ['aria-label' => 'breadcrumb']);
            $html .= \html_writer::start_tag('ol', ['class' => 'breadcrumb bg-transparent p-0 mb-2']);

            foreach ($breadcrumbs as $label => $url) {
                if ($url === null) {
                    // Current page (no link).
                    $html .= \html_writer::tag('li', s($label),
                        ['class' => 'breadcrumb-item active', 'aria-current' => 'page']);
                } else {
                    $html .= \html_writer::tag('li',
                        \html_writer::link($url, s($label)),
                        ['class' => 'breadcrumb-item']);
                }
            }

            $html .= \html_writer::end_tag('ol');
            $html .= \html_writer::end_tag('nav');
        }

        // Title.
        $html .= \html_writer::tag('h2', s($title), ['class' => 'jb-page-title mb-0']);
        $html .= \html_writer::end_div();

        // Right side: action buttons.
        if (!empty($actions)) {
            $html .= \html_writer::start_div('jb-header-actions');
            foreach ($actions as $action) {
                $class = $action['class'] ?? 'btn btn-primary';
                $icon = isset($action['icon']) ? '<i class="fa fa-' . $action['icon'] . ' mr-2"></i>' : '';
                $html .= \html_writer::link(
                    $action['url'],
                    $icon . s($action['label']),
                    ['class' => $class . ' ml-2']
                );
            }
            $html .= \html_writer::end_div();
        }

        $html .= \html_writer::end_div();

        return $html;
    }

    /**
     * Render a statistics card.
     *
     * @param string $value The main value to display.
     * @param string $label Description label.
     * @param string $color Bootstrap color class (primary, success, warning, etc).
     * @param string|null $icon FontAwesome icon name (without fa-).
     * @param string|null $link Optional link URL.
     * @return string HTML output.
     */
    public static function stat_card($value, string $label, string $color = 'primary',
                                      ?string $icon = null, ?string $link = null): string {
        $html = \html_writer::start_div('col-lg-3 col-md-6 col-sm-6 mb-3');
        $cardClass = $link ? 'jb-stat-card card h-100 border-' . $color . ' cursor-pointer' :
                            'jb-stat-card card h-100 border-' . $color;

        if ($link) {
            $html .= \html_writer::start_tag('a', ['href' => $link, 'class' => 'text-decoration-none']);
        }

        $html .= \html_writer::start_div($cardClass);
        $html .= \html_writer::start_div('card-body d-flex align-items-center');

        // Icon.
        if ($icon) {
            $html .= \html_writer::start_div('jb-stat-icon mr-3');
            $html .= \html_writer::tag('i', '', ['class' => 'fa fa-' . $icon . ' fa-2x text-' . $color]);
            $html .= \html_writer::end_div();
        }

        // Value and label.
        $html .= \html_writer::start_div('jb-stat-content');
        $html .= \html_writer::tag('div', $value, ['class' => 'jb-stat-value text-' . $color]);
        $html .= \html_writer::tag('div', s($label), ['class' => 'jb-stat-label text-muted small']);
        $html .= \html_writer::end_div();

        $html .= \html_writer::end_div(); // card-body
        $html .= \html_writer::end_div(); // card

        if ($link) {
            $html .= \html_writer::end_tag('a');
        }

        $html .= \html_writer::end_div(); // col

        return $html;
    }

    /**
     * Render a status badge.
     *
     * @param string $status Status key.
     * @param string $type Type of status (vacancy, application, convocatoria).
     * @return string HTML output.
     */
    public static function status_badge(string $status, string $type = 'vacancy'): string {
        $colors = [
            'vacancy' => [
                'draft' => 'secondary',
                'published' => 'success',
                'closed' => 'warning',
                'assigned' => 'info',
            ],
            'application' => [
                'submitted' => 'info',
                'under_review' => 'warning',
                'docs_validated' => 'success',
                'docs_rejected' => 'danger',
                'interview' => 'purple',
                'selected' => 'success',
                'rejected' => 'secondary',
                'withdrawn' => 'dark',
            ],
            'convocatoria' => [
                'draft' => 'secondary',
                'open' => 'success',
                'closed' => 'warning',
                'archived' => 'dark',
            ],
        ];

        $color = $colors[$type][$status] ?? 'secondary';
        $label = get_string($type === 'application' ? 'appstatus:' . $status :
                           ($type === 'convocatoria' ? 'convocatoria_status_' . $status :
                            'status:' . $status), 'local_jobboard');

        return \html_writer::tag('span', s($label), ['class' => 'badge badge-' . $color]);
    }

    /**
     * Render action buttons group.
     *
     * @param array $actions Array of action definitions.
     * @return string HTML output.
     */
    public static function action_buttons(array $actions): string {
        global $OUTPUT;

        $html = \html_writer::start_div('jb-actions btn-group btn-group-sm');

        foreach ($actions as $action) {
            if (!($action['visible'] ?? true)) {
                continue;
            }

            $attrs = [
                'class' => 'btn ' . ($action['class'] ?? 'btn-outline-secondary'),
                'title' => $action['title'] ?? $action['label'] ?? '',
            ];

            if (!empty($action['confirm'])) {
                $attrs['onclick'] = "return confirm('" . addslashes($action['confirm']) . "');";
            }

            if (!empty($action['disabled'])) {
                $attrs['class'] .= ' disabled';
                $attrs['aria-disabled'] = 'true';
            }

            // Icon or label.
            $content = '';
            if (!empty($action['icon'])) {
                $content = $OUTPUT->pix_icon($action['icon'], $action['title'] ?? '');
            } else {
                $content = s($action['label'] ?? '');
            }

            $html .= \html_writer::link($action['url'], $content, $attrs);
        }

        $html .= \html_writer::end_div();

        return $html;
    }

    /**
     * Render an empty state message.
     *
     * @param string $message The message to display.
     * @param string|null $icon FontAwesome icon name.
     * @param array|null $action Optional action button.
     * @return string HTML output.
     */
    public static function empty_state(string $message, ?string $icon = 'inbox', ?array $action = null): string {
        $html = \html_writer::start_div('jb-empty-state text-center py-5');

        if ($icon) {
            $html .= \html_writer::tag('i', '', ['class' => 'fa fa-' . $icon . ' fa-4x text-muted mb-3']);
            $html .= \html_writer::empty_tag('br');
        }

        $html .= \html_writer::tag('p', s($message), ['class' => 'text-muted lead mb-3']);

        if ($action) {
            $html .= \html_writer::link(
                $action['url'],
                s($action['label']),
                ['class' => $action['class'] ?? 'btn btn-primary']
            );
        }

        $html .= \html_writer::end_div();

        return $html;
    }

    /**
     * Render a data table with consistent styling.
     *
     * @param array $headers Table headers.
     * @param array $rows Table rows (each row is an array of cell values).
     * @param array $options Table options (id, class, responsive).
     * @return string HTML output.
     */
    public static function data_table(array $headers, array $rows, array $options = []): string {
        $tableId = $options['id'] ?? 'jb-table-' . uniqid();
        $tableClass = 'table table-striped table-hover ' . ($options['class'] ?? '');
        $responsive = $options['responsive'] ?? true;

        $html = '';

        if ($responsive) {
            $html .= \html_writer::start_div('table-responsive');
        }

        $html .= \html_writer::start_tag('table', ['id' => $tableId, 'class' => trim($tableClass)]);

        // Header.
        $html .= \html_writer::start_tag('thead', ['class' => 'thead-light']);
        $html .= \html_writer::start_tag('tr');
        foreach ($headers as $header) {
            $html .= \html_writer::tag('th', $header);
        }
        $html .= \html_writer::end_tag('tr');
        $html .= \html_writer::end_tag('thead');

        // Body.
        $html .= \html_writer::start_tag('tbody');
        foreach ($rows as $row) {
            $html .= \html_writer::start_tag('tr');
            foreach ($row as $cell) {
                $html .= \html_writer::tag('td', $cell);
            }
            $html .= \html_writer::end_tag('tr');
        }
        $html .= \html_writer::end_tag('tbody');

        $html .= \html_writer::end_tag('table');

        if ($responsive) {
            $html .= \html_writer::end_div();
        }

        return $html;
    }

    /**
     * Render a filter form.
     *
     * @param string $actionUrl Form action URL.
     * @param array $filters Filter definitions.
     * @param array $currentValues Current filter values.
     * @param array $hiddenFields Hidden fields to include.
     * @return string HTML output.
     */
    public static function filter_form(string $actionUrl, array $filters, array $currentValues = [],
                                        array $hiddenFields = []): string {
        $html = \html_writer::start_tag('form', [
            'method' => 'get',
            'action' => $actionUrl,
            'class' => 'jb-filter-form mb-4',
        ]);

        // Hidden fields.
        foreach ($hiddenFields as $name => $value) {
            $html .= \html_writer::empty_tag('input', [
                'type' => 'hidden',
                'name' => $name,
                'value' => $value,
            ]);
        }

        $html .= \html_writer::start_div('row align-items-end');

        foreach ($filters as $filter) {
            $colClass = $filter['col'] ?? 'col-md-3';
            $html .= \html_writer::start_div($colClass . ' mb-2');

            if (!empty($filter['label'])) {
                $html .= \html_writer::tag('label', s($filter['label']), [
                    'for' => $filter['name'],
                    'class' => 'form-label small text-muted',
                ]);
            }

            $value = $currentValues[$filter['name']] ?? ($filter['default'] ?? '');

            switch ($filter['type']) {
                case 'text':
                    $html .= \html_writer::empty_tag('input', [
                        'type' => 'text',
                        'name' => $filter['name'],
                        'id' => $filter['name'],
                        'value' => $value,
                        'class' => 'form-control',
                        'placeholder' => $filter['placeholder'] ?? '',
                    ]);
                    break;

                case 'select':
                    $html .= \html_writer::select(
                        $filter['options'],
                        $filter['name'],
                        $value,
                        null,
                        ['class' => 'form-control', 'id' => $filter['name']]
                    );
                    break;
            }

            $html .= \html_writer::end_div();
        }

        // Submit button.
        $html .= \html_writer::start_div('col-md-auto mb-2');
        $html .= \html_writer::empty_tag('input', [
            'type' => 'submit',
            'value' => get_string('filter', 'local_jobboard'),
            'class' => 'btn btn-secondary',
        ]);
        $html .= \html_writer::end_div();

        $html .= \html_writer::end_div(); // row
        $html .= \html_writer::end_tag('form');

        return $html;
    }

    /**
     * Render an info card.
     *
     * @param string $title Card title.
     * @param string $content Card content.
     * @param string $color Border color.
     * @param string|null $icon Optional icon.
     * @return string HTML output.
     */
    public static function info_card(string $title, string $content, string $color = 'info',
                                      ?string $icon = null): string {
        $html = \html_writer::start_div('card border-left-' . $color . ' mb-3');
        $html .= \html_writer::start_div('card-body');

        $html .= \html_writer::start_div('d-flex align-items-center mb-2');
        if ($icon) {
            $html .= \html_writer::tag('i', '', ['class' => 'fa fa-' . $icon . ' text-' . $color . ' mr-2']);
        }
        $html .= \html_writer::tag('h5', s($title), ['class' => 'card-title mb-0']);
        $html .= \html_writer::end_div();

        $html .= \html_writer::tag('p', $content, ['class' => 'card-text text-muted mb-0']);

        $html .= \html_writer::end_div();
        $html .= \html_writer::end_div();

        return $html;
    }

    /**
     * Get page CSS.
     *
     * @return string CSS styles.
     */
    public static function get_inline_styles(): string {
        return '
        <style>
        /* UI Helper Styles */
        .jb-page-header {
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }
        .jb-page-title {
            font-weight: 600;
            color: #343a40;
        }
        .jb-stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-width: 0 0 0 4px !important;
        }
        .jb-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .jb-stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1.2;
        }
        .jb-stat-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .jb-empty-state {
            background: #f8f9fa;
            border-radius: 0.5rem;
        }
        .jb-filter-form {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
        }
        .jb-actions .btn {
            border-radius: 0;
        }
        .jb-actions .btn:first-child {
            border-radius: 0.25rem 0 0 0.25rem;
        }
        .jb-actions .btn:last-child {
            border-radius: 0 0.25rem 0.25rem 0;
        }
        .border-left-primary { border-left: 4px solid #007bff !important; }
        .border-left-success { border-left: 4px solid #28a745 !important; }
        .border-left-warning { border-left: 4px solid #ffc107 !important; }
        .border-left-danger { border-left: 4px solid #dc3545 !important; }
        .border-left-info { border-left: 4px solid #17a2b8 !important; }
        .cursor-pointer { cursor: pointer; }
        .badge-purple { background-color: #6f42c1; color: #fff; }
        </style>
        ';
    }
}

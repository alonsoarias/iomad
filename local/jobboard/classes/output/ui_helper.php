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
     * Render page header with optional action buttons.
     *
     * NOTE: Page title is handled by Moodle's $PAGE->set_heading().
     * NOTE: Breadcrumbs are handled by Moodle's native $PAGE->navbar.
     * This method ONLY renders action buttons to avoid duplication.
     *
     * @param string $title DEPRECATED - Ignored. Use $PAGE->set_heading() instead.
     * @param array $breadcrumbs DEPRECATED - Ignored. Use $PAGE->navbar instead.
     * @param array $actions Optional action buttons.
     * @return string HTML output.
     */
    public static function page_header(string $title, array $breadcrumbs = [], array $actions = []): string {
        // If no actions, return empty string (title/breadcrumbs handled by Moodle).
        if (empty($actions)) {
            return '';
        }

        $html = \html_writer::start_div('jb-page-header d-flex justify-content-end mb-4');

        // Action buttons only (title and breadcrumbs handled by Moodle's theme).
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
        // Color mappings aligned with renderer_base.php for consistency.
        $colors = [
            'vacancy' => [
                'draft' => 'secondary',
                'published' => 'success',
                'closed' => 'danger',
                'archived' => 'dark',
                'pending' => 'warning',
                'assigned' => 'primary',
            ],
            'application' => [
                'draft' => 'secondary',
                'submitted' => 'info',
                'reviewing' => 'primary',
                'under_review' => 'warning',
                'approved' => 'success',
                'docs_validated' => 'success',
                'rejected' => 'danger',
                'docs_rejected' => 'danger',
                'withdrawn' => 'dark',
                'interview' => 'warning',
                'hired' => 'success',
                'selected' => 'success',
            ],
            'convocatoria' => [
                'draft' => 'secondary',
                'open' => 'success',
                'closed' => 'danger',
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

            // Use data attribute for modal confirmation instead of onclick confirm().
            if (!empty($action['confirm'])) {
                $attrs['data-confirm'] = $action['confirm'];
                $attrs['data-confirm-action'] = 'link';
                $attrs['class'] .= ' jb-confirm-trigger';
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
        // Wrap in card for visual consistency.
        $html = \html_writer::start_div('card shadow-sm mb-4 jb-filter-card');
        $html .= \html_writer::start_div('card-body py-3');

        $html .= \html_writer::start_tag('form', [
            'method' => 'get',
            'action' => $actionUrl,
            'class' => 'jb-filter-form mb-0',
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
            $colClass = $filter['col'] ?? 'col-md-3 col-sm-6';
            $html .= \html_writer::start_div($colClass . ' mb-2 mb-md-0');

            if (!empty($filter['label'])) {
                $html .= \html_writer::tag('label', s($filter['label']), [
                    'for' => 'filter_' . $filter['name'],
                    'class' => 'form-label small text-muted mb-1',
                ]);
            }

            $value = $currentValues[$filter['name']] ?? ($filter['default'] ?? '');

            switch ($filter['type']) {
                case 'text':
                    $html .= \html_writer::empty_tag('input', [
                        'type' => 'text',
                        'name' => $filter['name'],
                        'id' => 'filter_' . $filter['name'],
                        'value' => $value,
                        'class' => 'form-control form-control-sm',
                        'placeholder' => $filter['placeholder'] ?? '',
                    ]);
                    break;

                case 'select':
                    // Build select manually to ensure proper visibility with all themes.
                    $selectId = 'filter_' . $filter['name'];
                    $html .= '<select name="' . s($filter['name']) . '" id="' . $selectId . '" ';
                    $html .= 'class="form-control form-control-sm jb-filter-select">';
                    foreach ($filter['options'] as $optVal => $optLabel) {
                        $selected = ((string)$optVal === (string)$value) ? ' selected="selected"' : '';
                        // Determine display label with multiple fallbacks.
                        $displayLabel = '';
                        if (is_string($optLabel) && trim($optLabel) !== '') {
                            $displayLabel = $optLabel;
                        } elseif (is_string($optVal) && trim($optVal) !== '') {
                            $displayLabel = $optVal;
                        } else {
                            // Fallback: use filter name + "All" as placeholder.
                            $displayLabel = get_string('all') . '...';
                        }
                        $html .= '<option value="' . s($optVal) . '"' . $selected . '>';
                        $html .= s($displayLabel) . '</option>';
                    }
                    $html .= '</select>';
                    break;

                case 'date':
                    $html .= \html_writer::empty_tag('input', [
                        'type' => 'date',
                        'name' => $filter['name'],
                        'id' => 'filter_' . $filter['name'],
                        'value' => $value,
                        'class' => 'form-control form-control-sm',
                    ]);
                    break;
            }

            $html .= \html_writer::end_div();
        }

        // Submit button.
        $html .= \html_writer::start_div('col-md-auto mb-2 mb-md-0');
        $html .= \html_writer::tag('button',
            '<i class="fa fa-search mr-1"></i>' . get_string('search'),
            [
                'type' => 'submit',
                'class' => 'btn btn-primary btn-sm',
            ]
        );
        $html .= \html_writer::end_div();

        $html .= \html_writer::end_div(); // row
        $html .= \html_writer::end_tag('form');

        $html .= \html_writer::end_div(); // card-body
        $html .= \html_writer::end_div(); // card

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
     * @deprecated since version 2.0.1 - Styles now in styles.css
     * @return string Empty string (styles migrated to styles.css).
     */
    public static function get_inline_styles(): string {
        // Styles have been migrated to styles.css for better caching and organization.
        return '';
    }

    /**
     * Render a per-page selector.
     *
     * @param \moodle_url $baseurl Base URL for the page.
     * @param int $currentperpage Current per-page value.
     * @param int $total Total number of records.
     * @param array $options Available per-page options.
     * @return string HTML output.
     */
    public static function perpage_selector(\moodle_url $baseurl, int $currentperpage, int $total,
                                            array $options = [10, 25, 50, 100]): string {
        if ($total <= min($options)) {
            return '';
        }

        $html = \html_writer::start_div('jb-perpage-selector d-inline-flex align-items-center');
        $html .= \html_writer::tag('span', get_string('show') . ':', ['class' => 'mr-2 small text-muted']);

        // Build select manually to ensure proper visibility with all themes.
        // Uses jb-perpage-select class for AMD module initialization.
        $html .= '<select class="form-control form-control-sm d-inline-block w-auto jb-perpage-select" ';
        $html .= 'aria-label="' . s(get_string('recordsperpage', 'local_jobboard')) . '">';

        foreach ($options as $option) {
            $url = clone $baseurl;
            $url->param('perpage', $option);
            $url->param('page', 0);
            $selected = ($option == $currentperpage) ? ' selected="selected"' : '';
            $html .= '<option value="' . s($url->out(false)) . '"' . $selected . '>' . $option . '</option>';
        }

        $html .= '</select>';

        $html .= \html_writer::tag('span', get_string('entries', 'local_jobboard'), ['class' => 'ml-2 small text-muted']);
        $html .= \html_writer::end_div();

        return $html;
    }

    /**
     * Render pagination controls with perpage selector.
     *
     * @param int $total Total records.
     * @param int $page Current page.
     * @param int $perpage Records per page.
     * @param \moodle_url $baseurl Base URL.
     * @return string HTML output.
     */
    public static function pagination_bar(int $total, int $page, int $perpage, \moodle_url $baseurl): string {
        global $OUTPUT;

        if ($total == 0) {
            return '';
        }

        $html = \html_writer::start_div('jb-pagination-container d-flex justify-content-between align-items-center mt-4 mb-3');

        // Results info.
        $start = ($page * $perpage) + 1;
        $end = min(($page + 1) * $perpage, $total);
        $html .= \html_writer::tag('div',
            get_string('showingxofy', 'local_jobboard', (object)['start' => $start, 'end' => $end, 'total' => $total]),
            ['class' => 'text-muted small']
        );

        // Perpage selector + pagination.
        $html .= \html_writer::start_div('d-flex align-items-center');
        $html .= self::perpage_selector($baseurl, $perpage, $total);

        if ($total > $perpage) {
            $html .= \html_writer::div($OUTPUT->paging_bar($total, $page, $perpage, $baseurl), 'ml-3');
        }

        $html .= \html_writer::end_div();
        $html .= \html_writer::end_div();

        return $html;
    }

    /**
     * Render bulk actions toolbar with select all functionality.
     *
     * @param string $formid Form ID for the bulk action form.
     * @param array $actions Available bulk actions.
     * @param string $itemclass CSS class for selectable items.
     * @return string HTML output.
     */
    public static function bulk_actions_toolbar(string $formid, array $actions, string $itemclass = 'jb-bulk-item'): string {
        $html = \html_writer::start_div('jb-bulk-toolbar alert alert-secondary d-none mb-3', ['id' => $formid . '-toolbar']);

        $html .= \html_writer::start_div('d-flex justify-content-between align-items-center');

        // Left side: select all checkbox and count.
        $html .= \html_writer::start_div('d-flex align-items-center');
        $html .= \html_writer::tag('label', '', [
            'class' => 'custom-control custom-checkbox mr-3 mb-0',
            'for' => $formid . '-select-all',
        ]);
        $html .= \html_writer::empty_tag('input', [
            'type' => 'checkbox',
            'class' => 'custom-control-input jb-select-all',
            'id' => $formid . '-select-all',
            'data-target' => '.' . $itemclass,
        ]);
        $html .= \html_writer::tag('label',
            get_string('selectall', 'local_jobboard'),
            ['class' => 'custom-control-label', 'for' => $formid . '-select-all']
        );
        $html .= \html_writer::tag('span', '0', [
            'class' => 'badge badge-primary ml-2 jb-selected-count',
            'id' => $formid . '-count',
        ]);
        $html .= \html_writer::tag('span', get_string('selected', 'local_jobboard'), ['class' => 'ml-1 small']);
        $html .= \html_writer::end_div();

        // Right side: action buttons.
        $html .= \html_writer::start_div('jb-bulk-actions');
        foreach ($actions as $action) {
            $attrs = [
                'class' => 'btn btn-sm ' . ($action['class'] ?? 'btn-outline-secondary') . ' ml-2 jb-bulk-action',
                'data-action' => $action['action'],
                'data-confirm' => $action['confirm'] ?? '',
                'data-form' => $formid,
                'disabled' => 'disabled',
            ];
            if (!empty($action['icon'])) {
                $html .= \html_writer::tag('button',
                    '<i class="fa fa-' . $action['icon'] . ' mr-1"></i>' . s($action['label']),
                    $attrs
                );
            } else {
                $html .= \html_writer::tag('button', s($action['label']), $attrs);
            }
        }
        $html .= \html_writer::end_div();

        $html .= \html_writer::end_div();
        $html .= \html_writer::end_div();

        return $html;
    }

    /**
     * Render a confirmation modal.
     *
     * @param string $id Modal ID.
     * @param string $title Modal title.
     * @param string $message Confirmation message.
     * @param string $confirmLabel Confirm button label.
     * @param string $confirmClass Confirm button class.
     * @return string HTML output.
     */
    public static function confirmation_modal(string $id, string $title, string $message,
                                               string $confirmLabel = null, string $confirmClass = 'btn-danger'): string {
        if ($confirmLabel === null) {
            $confirmLabel = get_string('confirm');
        }

        $html = '
        <div class="modal fade jb-confirm-modal" id="' . s($id) . '" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            <i class="fa fa-exclamation-triangle mr-2"></i>' . s($title) . '
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="' . get_string('close') . '">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="jb-modal-message">' . s($message) . '</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">' . get_string('cancel') . '</button>
                        <button type="button" class="btn ' . s($confirmClass) . ' jb-modal-confirm">' . s($confirmLabel) . '</button>
                    </div>
                </div>
            </div>
        </div>';

        return $html;
    }

    /**
     * Initialize bulk actions AMD module.
     *
     * This method should be called once per page to initialize the bulk actions functionality.
     * It loads the AMD module that handles bulk selection, actions, and confirmation modals.
     *
     * @return void
     */
    public static function init_bulk_actions(): void {
        global $PAGE;
        $PAGE->requires->js_call_amd('local_jobboard/bulk_actions', 'init');
    }

    /**
     * Get JavaScript for bulk actions and modals.
     *
     * @deprecated since version 2.0.29 - Use init_bulk_actions() instead which loads AMD module.
     * @return string Empty string (functionality moved to AMD module).
     */
    public static function get_bulk_actions_js(): string {
        // Functionality has been moved to AMD module local_jobboard/bulk_actions.
        // Call init_bulk_actions() to initialize the module.
        self::init_bulk_actions();
        return '';
    }
}

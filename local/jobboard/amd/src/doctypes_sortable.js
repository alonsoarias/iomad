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
 * Drag and drop reordering for document types.
 *
 * @module     local_jobboard/doctypes_sortable
 * @copyright  2024 ISER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Notification from 'core/notification';
import {get_string as getString} from 'core/str';

let draggedRow = null;
let placeholder = null;

/**
 * Initialize drag and drop for doctype table.
 *
 * @param {string} tableSelector - CSS selector for the table body
 * @param {string} ajaxUrl - URL for the AJAX reorder endpoint
 */
export const init = (tableSelector, ajaxUrl) => {
    const tbody = document.querySelector(tableSelector);
    if (!tbody) {
        return;
    }

    const rows = tbody.querySelectorAll('tr[data-doctype-id]');

    // Create placeholder element.
    placeholder = document.createElement('tr');
    placeholder.className = 'jb-drag-placeholder';
    placeholder.innerHTML = '<td colspan="8" class="bg-primary text-white text-center py-2">' +
        '<i class="fa fa-arrow-right me-2"></i>Drop here</td>';

    rows.forEach(row => {
        // Add drag handle.
        const firstCell = row.querySelector('td:first-child');
        if (firstCell) {
            const handle = document.createElement('span');
            handle.className = 'jb-drag-handle me-2';
            handle.innerHTML = '<i class="fa fa-grip-vertical text-muted"></i>';
            handle.setAttribute('title', 'Drag to reorder');
            handle.style.cursor = 'grab';
            firstCell.insertBefore(handle, firstCell.firstChild);
        }

        // Make row draggable.
        row.draggable = true;
        row.style.cursor = 'grab';

        // Drag events.
        row.addEventListener('dragstart', handleDragStart);
        row.addEventListener('dragend', handleDragEnd);
        row.addEventListener('dragover', handleDragOver);
        row.addEventListener('dragenter', handleDragEnter);
        row.addEventListener('dragleave', handleDragLeave);
        row.addEventListener('drop', handleDrop);
    });

    // Store ajax URL for later use.
    tbody.dataset.ajaxUrl = ajaxUrl;
};

/**
 * Handle drag start.
 * @param {DragEvent} e - The drag event
 */
const handleDragStart = (e) => {
    draggedRow = e.target.closest('tr');
    draggedRow.classList.add('jb-dragging');

    // Set drag data.
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', draggedRow.dataset.doctypeId);

    // Add visual feedback after a short delay.
    setTimeout(() => {
        draggedRow.style.opacity = '0.5';
    }, 0);
};

/**
 * Handle drag end.
 */
const handleDragEnd = () => {
    if (draggedRow) {
        draggedRow.classList.remove('jb-dragging');
        draggedRow.style.opacity = '1';
    }

    // Remove placeholder.
    if (placeholder.parentNode) {
        placeholder.parentNode.removeChild(placeholder);
    }

    // Remove all drag-over classes.
    document.querySelectorAll('.jb-drag-over').forEach(el => {
        el.classList.remove('jb-drag-over');
    });

    draggedRow = null;
};

/**
 * Handle drag over.
 * @param {DragEvent} e - The drag event
 */
const handleDragOver = (e) => {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
};

/**
 * Handle drag enter.
 * @param {DragEvent} e - The drag event
 */
const handleDragEnter = (e) => {
    e.preventDefault();
    const targetRow = e.target.closest('tr');

    if (targetRow && targetRow !== draggedRow && targetRow !== placeholder) {
        targetRow.classList.add('jb-drag-over');

        // Position placeholder.
        const tbody = targetRow.parentNode;
        const rect = targetRow.getBoundingClientRect();
        const middle = rect.top + rect.height / 2;

        if (e.clientY < middle) {
            tbody.insertBefore(placeholder, targetRow);
        } else {
            tbody.insertBefore(placeholder, targetRow.nextSibling);
        }
    }
};

/**
 * Handle drag leave.
 * @param {DragEvent} e - The drag event
 */
const handleDragLeave = (e) => {
    const targetRow = e.target.closest('tr');
    if (targetRow) {
        targetRow.classList.remove('jb-drag-over');
    }
};

/**
 * Handle drop.
 * @param {DragEvent} e - The drag event
 */
const handleDrop = (e) => {
    e.preventDefault();
    e.stopPropagation();

    const targetRow = e.target.closest('tr');

    if (targetRow && draggedRow && targetRow !== draggedRow) {
        const tbody = targetRow.parentNode;

        // Move the dragged row to the placeholder position.
        if (placeholder.parentNode === tbody) {
            tbody.insertBefore(draggedRow, placeholder);
        }

        // Save the new order via AJAX.
        saveOrder(tbody);
    }

    // Clean up.
    handleDragEnd();
};

/**
 * Save the new order via AJAX.
 * @param {HTMLElement} tbody - The table body element
 */
const saveOrder = async(tbody) => {
    const rows = tbody.querySelectorAll('tr[data-doctype-id]');
    const order = Array.from(rows).map(row => parseInt(row.dataset.doctypeId, 10));

    // Update visual sortorder numbers.
    rows.forEach((row, index) => {
        const badge = row.querySelector('td:first-child .badge');
        if (badge) {
            badge.textContent = index;
        }
    });

    // Show loading state.
    tbody.style.opacity = '0.7';

    try {
        const response = await fetch(tbody.dataset.ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                sesskey: M.cfg.sesskey,
                'order[]': order.join('&order[]='),
            }).toString().replace(/order%5B%5D=/g, 'order[]='),
        });

        const data = await response.json();

        if (data.success) {
            // Show success notification.
            getString('reorder_success', 'local_jobboard').then(str => {
                Notification.addNotification({
                    message: str,
                    type: 'success',
                });
            }).catch(() => {
                Notification.addNotification({
                    message: 'Order saved successfully',
                    type: 'success',
                });
            });
        } else {
            throw new Error(data.error || 'Failed to save order');
        }
    } catch (error) {
        // Show error notification.
        Notification.addNotification({
            message: error.message || 'Error saving order',
            type: 'error',
        });

        // Reload page to reset order.
        window.location.reload();
    } finally {
        tbody.style.opacity = '1';
    }
};

export default {
    init,
};

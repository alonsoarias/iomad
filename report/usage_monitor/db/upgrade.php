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
 * Plugin upgrade steps are defined here.
 *
 * @package     report_usage_monitor
 * @category    upgrade
 * @author      Alonso Arias <soporte@ingeweb.co>
 * @copyright   2025 Alonso Arias <soporte@ingeweb.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute report_usage_monitor upgrade from the given old version.
 *
 * @param int $oldversion The old version of the plugin.
 * @return bool True on success.
 */
function xmldb_report_usage_monitor_upgrade($oldversion) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/report/usage_monitor/locallib.php');
    $dbman = $DB->get_manager();

    if ($oldversion < 2022090200) {
        // Define table report_usage_monitor to be created.
        $table = new xmldb_table('report_usage_monitor');

        // Adding fields to table report_usage_monitor.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('fecha', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('cantidad_usuarios', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table report_usage_monitor.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally create table report_usage_monitor.
        try {
            if (!$dbman->table_exists($table)) {
                $dbman->create_table($table);
            }
        } catch (Exception $e) {
            debugging('Error creating table report_usage_monitor: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2022090200, 'report', 'usage_monitor');
    }

    if ($oldversion < 2022103100) {
        // Check the data type in the fecha column to avoid errors when converting existing timestamps.
        $records = $DB->get_records('report_usage_monitor', [], '', 'id, fecha');
        $needsconversion = false;

        foreach ($records as $record) {
            // If there is any record with date in text format (not numeric), we need to convert.
            if (!is_numeric($record->fecha) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $record->fecha)) {
                $needsconversion = true;
                break;
            }
        }

        if ($needsconversion) {
            // Start transaction to ensure data consistency.
            $transaction = $DB->start_delegated_transaction();
            try {
                // Update fecha field from date format to timestamp.
                // Use PHP-based conversion for cross-database compatibility (MySQL, PostgreSQL, etc.).
                $records = $DB->get_records('report_usage_monitor', [], '', 'id, fecha');
                foreach ($records as $record) {
                    if (!is_numeric($record->fecha) && preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $record->fecha, $matches)) {
                        // Convert dd/mm/yyyy format to Unix timestamp.
                        $day = (int) $matches[1];
                        $month = (int) $matches[2];
                        $year = (int) $matches[3];
                        $timestamp = mktime(0, 0, 0, $month, $day, $year);
                        if ($timestamp !== false) {
                            $DB->set_field('report_usage_monitor', 'fecha', $timestamp, ['id' => $record->id]);
                        }
                    }
                }
                $transaction->allow_commit();
            } catch (Exception $e) {
                $transaction->rollback($e);
                throw $e;
            }
        }

        // Ensure fecha field is INTEGER.
        $table = new xmldb_table('report_usage_monitor');
        $field = new xmldb_field('fecha', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_type($table, $field);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2022103100, 'report', 'usage_monitor');
    }

    if ($oldversion < 2025030403) {
        // Ensure fecha column in report_usage_monitor is INTEGER.
        $table = new xmldb_table('report_usage_monitor');

        // Identify common indices by naming convention.
        $indices = [
            'idx_fecha' => new xmldb_index('idx_fecha', XMLDB_INDEX_NOTUNIQUE, ['fecha']),
            'mdl_repousagmoni_fec_ix' => new xmldb_index('mdl_repousagmoni_fec_ix', XMLDB_INDEX_NOTUNIQUE, ['fecha']),
        ];

        // Drop all fecha-related indices.
        foreach ($indices as $indexname => $index) {
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
                mtrace("Dropped index {$indexname} on table report_usage_monitor");
            }
        }

        // Now change the field type.
        $field = new xmldb_field('fecha', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        if ($dbman->field_exists($table, $field)) {
            try {
                $dbman->change_field_type($table, $field);
                mtrace("Changed fecha field type to INTEGER in table report_usage_monitor");
            } catch (Exception $e) {
                debugging("Error changing field type: " . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }

        // Recreate the main index.
        $index = new xmldb_index('idx_fecha', XMLDB_INDEX_NOTUNIQUE, ['fecha']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
            mtrace("Recreated idx_fecha index on table report_usage_monitor");
        }

        // Fix any invalid data in the table.
        $transaction = $DB->start_delegated_transaction();
        try {
            // Find records with null or invalid dates.
            $records = $DB->get_records_sql(
                "SELECT id, fecha FROM {report_usage_monitor} WHERE fecha IS NULL OR fecha <= 0"
            );

            foreach ($records as $record) {
                // Use current date as fallback for any invalid date.
                $DB->set_field('report_usage_monitor', 'fecha', time(), ['id' => $record->id]);
                mtrace("Updated record ID {$record->id} with invalid fecha to current timestamp");
            }

            // Verify and update existing data to ensure they are valid UNIX timestamps.
            $allrecords = $DB->get_records('report_usage_monitor');
            foreach ($allrecords as $record) {
                // If the date appears to be a format or not a valid timestamp.
                if (!is_numeric($record->fecha) || $record->fecha < 946684800) { // 01/01/2000.
                    $timestamp = strtotime(date('Y-m-d', time()));
                    $DB->set_field('report_usage_monitor', 'fecha', $timestamp, ['id' => $record->id]);
                    mtrace("Updated record ID {$record->id}, fecha {$record->fecha} to timestamp {$timestamp}");
                }
            }

            $transaction->allow_commit();
        } catch (Exception $e) {
            $transaction->rollback($e);
            debugging("Error fixing invalid dates: " . $e->getMessage(), DEBUG_DEVELOPER);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025030403, 'report', 'usage_monitor');
    }

    return true;
}

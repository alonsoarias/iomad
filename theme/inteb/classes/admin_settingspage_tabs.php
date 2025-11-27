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
 * Admin settings page tabs handler for theme_inteb.
 *
 * Extends theme_boost_admin_settingspage_tabs to provide custom tabbed
 * settings pages for the INTEB theme configuration.
 *
 * @package     theme_inteb
 * @copyright   2025 Vicerrectoría Académica ISER <vicerrectoria@iser.edu.co>
 * @author      Alonso Arias <soporteplataformas@iser.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class theme_inteb_admin_settingspage_tabs
 *
 * Custom admin settings page with tabbed navigation for theme_inteb.
 *
 * @package     theme_inteb
 * @copyright   2025 Vicerrectoría Académica ISER <vicerrectoria@iser.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_inteb_admin_settingspage_tabs extends theme_boost_admin_settingspage_tabs
{

    /** @var array Lista de pestañas */
    public $tabs = array();

    /**
     * Obtiene las pestañas actuales
     */
    public function get_tabs()
    {
        return $this->tabs;
    }

    /**
     * Establece las pestañas
     */
    public function set_tabs($tabs, $reset = true)
    {
        if ($reset) {
            $this->tabs = array();
        }
        if (!empty($tabs)) {
            foreach ($tabs as $tab) {
                $original_name = $tab->name;
                $tab->name = str_replace('theme_remui', 'theme_inteb', $tab->name);
                if (!empty($tab->settings)) {
                    foreach ($tab->settings as $setting) {
                        $original_setting = $setting->name;
                        $setting->name = str_replace('theme_remui', 'theme_inteb', $setting->name);
                        $this->settings->{$setting->name} = $setting;
                    }
                }
                $this->tabs[] = $tab;
            }
        }
    }

    /**
     * Inserta una pestaña al inicio
     */
    public function insert_tab(admin_settingpage $tab)
    {
        if (!empty($tab->settings)) {
            foreach ($tab->settings as $setting) {
                $this->settings->{$setting->name} = $setting;
            }
        }
        array_unshift($this->tabs, $tab);
        return true;
    }

    /**
     * Añade una pestaña al final
     */
    public function add_tab(admin_settingpage $tab)
    {
        if (!empty($tab->settings)) {
            foreach ($tab->settings as $setting) {
                $this->settings->{$setting->name} = $setting;
            }
        }
        $this->tabs[] = $tab;
        return true;
    }
}

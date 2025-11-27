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
 * Theme settings utilities for the FNG theme.
 *
 * @package    theme_fng
 * @copyright  2025 Soporte fng <soporte@fng.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_fng\util;

defined('MOODLE_INTERNAL') || die();

use theme_config;

/**
 * Utility class for theme settings specifically for handling footer settings and personal area header.
 *
 * This class provides methods to retrieve theme configuration settings and prepare them
 * for use in templates. It handles settings related to footer content, personal area headers,
 * and my courses headers.
 *
 * @package    theme_fng
 * @copyright  2025 Soporte fng <soporte@fng.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings {
    /**
     * @var \stdClass The theme configuration object.
     */
    protected $theme;

    /**
     * Constructor that loads the current theme configuration.
     */
    public function __construct() {
        $this->theme = theme_config::load('fng');
    }

    /**
     * Retrieves footer settings for the theme.
     *
     * This method gathers the footer settings from the theme configuration
     * and prepares them for use in the footer template. It handles credits,
     * about title, about text, and footer section visibility settings.
     *
     * @return array Array containing context for the footer template with settings data.
     */
    public function footer() {
        $templatecontext = [];
        
        // Retrieve 'my_credit' from the theme settings
        $templatecontext['my_credit'] = get_string('credit', 'theme_fng');
        
        // Get and set about title properly
        $templatecontext['abouttitle'] = isset($this->theme->settings->fng_abouttitle) && !empty($this->theme->settings->fng_abouttitle)
            ? $this->theme->settings->fng_abouttitle 
            : get_string('abouttitle_default', 'theme_fng'); 
        
        // Get and set about text properly
        $templatecontext['abouttext'] = isset($this->theme->settings->fng_abouttext) && !empty($this->theme->settings->fng_abouttext)
            ? $this->theme->settings->fng_abouttext 
            : get_string('abouttext_default', 'theme_fng');
            
        // Check if footer sections should be hidden
        $templatecontext['hidefootersections'] = isset($this->theme->settings->fng_hidefootersections) 
            && $this->theme->settings->fng_hidefootersections == 1;
        
        return $templatecontext;
    }

    /**
     * Retrieves personal area header settings for the theme.
     *
     * This method gathers the personal area header settings from the theme configuration
     * and prepares them for use in the personal area header template. It handles
     * visibility settings and image URL configuration.
     *
     * @return array Array containing context for the personal area header template with settings data.
     */
    public function personal_area_header() {
        $templatecontext = [];

        // Check if header should be displayed
        $show_header = isset($this->theme->settings->fng_show_personalareaheader) 
            && $this->theme->settings->fng_show_personalareaheader == 1;
            
        if ($show_header) {
            // Retrieve header image URL from the theme settings or use a default
            $personalareaheader = $this->theme->setting_file_url('fng_personalareaheader', 'fng_personalareaheader');
            if (!empty($personalareaheader)) {
                $templatecontext['headerimage'] = [
                    'url' => $personalareaheader,
                    'title' => get_string('personalareaheader', 'theme_fng')
                ];
            } else {
                $templatecontext['headerimage'] = [
                    'url' => '',
                    'title' => get_string('defaultheader', 'theme_fng')
                ];
            }
        }

        return $templatecontext;
    }

    /**
     * Retrieves my courses header settings for the theme.
     *
     * This method gathers the my courses header settings from the theme configuration
     * and prepares them for use in the my courses header template. It handles
     * visibility settings and image URL configuration.
     *
     * @return array Array containing context for the my courses header template with settings data.
     */
    public function my_courses_header() {
        $templatecontext = [];

        // Check if header should be displayed
        $show_header = isset($this->theme->settings->fng_show_mycoursesheader) 
            && $this->theme->settings->fng_show_mycoursesheader == 1;
            
        if ($show_header) {
            // Retrieve header image URL from the theme settings or use a default
            $mycoursesheader = $this->theme->setting_file_url('fng_mycoursesheader', 'fng_mycoursesheader');
            if (!empty($mycoursesheader)) {
                $templatecontext['headerimage'] = [
                    'url' => $mycoursesheader,
                    'title' => get_string('mycoursesheader', 'theme_fng')
                ];
            } else {
                $templatecontext['headerimage'] = [
                    'url' => '',
                    'title' => get_string('defaultheader', 'theme_fng')
                ];
            }
        }

        return $templatecontext;
    }
}
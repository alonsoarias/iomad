<?php

namespace theme_inteb\util;

use theme_config;

/**
 * Utility class for theme settings specifically for handling footer settings and personal area header.
 */
class settings {
    /**
     * @var stdClass The theme configuration object.
     */
    protected $theme;

    /**
     * Constructor that loads the current theme configuration.
     */
    public function __construct() {
        $this->theme = theme_config::load('inteb');
    }

    /**
     * Retrieves footer settings for the theme.
     *
     * This method gathers the footer columns configuration from the theme settings
     * and prepares them for use in the footer template. GOV.CO compatible design.
     *
     * @return array Context for the footer template with settings data.
     */
    public function footer() {
        $templatecontext = [];

        // Retrieve 'my_credit' from the theme settings
        $templatecontext['my_credit'] = get_string('credit', 'theme_inteb');

        // Get number of columns (default: 3)
        $numcolumns = isset($this->theme->settings->ib_footercolumns)
            ? (int)$this->theme->settings->ib_footercolumns
            : 3;

        $templatecontext['footercolumns'] = $numcolumns;

        // Calculate Bootstrap column class based on number of columns
        $colclasses = [
            1 => 'col-12',
            2 => 'col-12 col-md-6',
            3 => 'col-12 col-md-4',
            4 => 'col-12 col-md-6 col-lg-3'
        ];
        $templatecontext['colclass'] = $colclasses[$numcolumns] ?? 'col-12 col-md-4';

        // Build columns array for mustache iteration
        $columns = [];
        for ($i = 1; $i <= $numcolumns; $i++) {
            $titleKey = 'ib_footercolumntitle' . $i;
            $contentKey = 'ib_footercolumn' . $i;

            $title = isset($this->theme->settings->$titleKey)
                ? $this->theme->settings->$titleKey
                : '';

            $content = isset($this->theme->settings->$contentKey) && !empty($this->theme->settings->$contentKey)
                ? $this->theme->settings->$contentKey
                : get_string('footercolumn' . $i . '_default', 'theme_inteb');

            $columns[] = [
                'number' => $i,
                'title' => $title,
                'hastitle' => !empty($title),
                'content' => $content,
                'hascontent' => !empty($content),
                'isfirst' => ($i === 1),
                'islast' => ($i === $numcolumns)
            ];
        }

        $templatecontext['columns'] = $columns;
        $templatecontext['hascolumns'] = !empty($columns);

        // Keep backward compatibility with old abouttitle/abouttext
        // (for any templates that still use these variables)
        if (!empty($columns)) {
            $templatecontext['abouttitle'] = ' '; // Non-empty to pass mustache condition
            $templatecontext['abouttext'] = ''; // Will be replaced by columns
        }

        return $templatecontext;
    }

    /**
     * Retrieves personal area header settings for the theme.
     *
     * This method gathers the 'personalareaheader' setting from the theme configuration
     * and prepares it for use in the personal area header template.
     *
     * @return array Context for the personal area header template with settings data.
     */
    public function personal_area_header() {
        $templatecontext = [];

        // Retrieve 'personalareaheader' from the theme settings or use a default value if not set.
        $personalareaheader = $this->theme->setting_file_url('ib_personalareaheader', 'ib_personalareaheader');
        if (!empty($personalareaheader)) {
            $templatecontext['headerimage'] = [
                'url' => $personalareaheader,
                'title' => get_string('personalareaheader', 'theme_inteb')
            ];
        } else {
            $templatecontext['headerimage'] = [
                'url' => '',
                'title' => get_string('defaultheader', 'theme_inteb')
            ];
        }

        return $templatecontext;
    }

    /**
     * Retrieves my courses header settings for the theme.
     *
     * This method gathers the 'mycoursesheader' setting from the theme configuration
     * and prepares it for use in the my courses header template.
     *
     * @return array Context for the my courses header template with settings data.
     */
    public function my_courses_header() {
        $templatecontext = [];

        // Retrieve 'mycoursesheader' from the theme settings or use a default value if not set.
        $mycoursesheader = $this->theme->setting_file_url('ib_mycoursesheader', 'ib_mycoursesheader');
        if (!empty($mycoursesheader)) {
            $templatecontext['headerimage'] = [
                'url' => $mycoursesheader,
                'title' => get_string('mycoursesheader', 'theme_inteb')
            ];
        } else {
            $templatecontext['headerimage'] = [
                'url' => '',
                'title' => get_string('defaultheader', 'theme_inteb')
            ];
        }

        return $templatecontext;
    }
}
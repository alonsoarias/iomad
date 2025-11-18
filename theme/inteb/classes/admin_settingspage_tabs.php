<?php
defined('MOODLE_INTERNAL') || die();

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

    /**
     * Generate the HTML output.
     *
     * @return string
     */
    public function output_html() {
        global $OUTPUT;

        $activetab = optional_param('activetab', '', PARAM_TEXT);
        $context = array('tabs' => array());
        $havesetactive = false;

        foreach ($this->get_tabs() as $tab) {
            $active = false;

            // Default to first tab it not told otherwise.
            if (empty($activetab) && !$havesetactive) {
                $active = true;
                $havesetactive = true;
            } else if ($activetab === $tab->name) {
                $active = true;
            }

            $context['tabs'][] = array(
                'name' => $tab->name,
                'displayname' => $tab->visiblename,
                'html' => $tab->output_html(),
                'active' => $active,
            );
        }

        if (empty($context['tabs'])) {
            return '';
        }

        return $OUTPUT->render_from_template('theme_inteb/admin_setting_tabs', $context);
    }
}

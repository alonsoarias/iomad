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

defined('MOODLE_INTERNAL') || die();

require(__DIR__ . '/../remui/settings.php');
require_once(__DIR__ . '/classes/admin_settingspage_tabs.php');
require_once($CFG->libdir . '/accesslib.php');
require_once(__DIR__ . '/lib.php');

// Capturar pestañas del tema padre (si existen)
$parent_tabs = null;
if (isset($settings) && method_exists($settings, 'get_tabs')) {
    $parent_tabs = $settings->get_tabs();
}

unset($settings);
$settings = null;

// Crear categoría en "Apariencia"
$ADMIN->add('appearance', new admin_category('theme_fng', get_string('pluginname', 'theme_fng')));

// Crear objeto de configuraciones con pestañas
$asettings = new theme_fng_admin_settingspage_tabs(
    'themesettingfng',
    get_string('themesettings', 'theme_fng'),
    'moodle/site:config'
);

if ($ADMIN->fulltree) {
    // Variables comunes
    $a = new stdClass();
    $a->example_banner = (string)$OUTPUT->image_url('example_banner', 'theme_fng');
    $a->cover_remui = (string)$OUTPUT->image_url('cover_remui', 'theme');
    $a->example_cover1 = (string)$OUTPUT->image_url('login_bg_corp', 'theme');
    $a->example_cover2 = (string)$OUTPUT->image_url('login_bg', 'theme');

    /* =========================================================================
       TAB 1: General Settings
       ========================================================================= */
    $page = new admin_settingpage('theme_fng_generals', get_string('generalsettings', 'theme_fng'));

    // --- Notificaciones Generales ---
    $page->add(new admin_setting_heading(
        'theme_fng/generalnoticeheading',
        get_string('generalnoticeheading', 'theme_fng'),
        ''
    ));

    $name = 'theme_fng/fng_generalnoticemode';
    $title = get_string('generalnoticemode', 'theme_fng');
    $description = get_string('generalnoticemodedesc', 'theme_fng');
    $default = 'off';
    $choices = [
        'off' => get_string('generalnoticemode_off', 'theme_fng'),
        'info' => get_string('generalnoticemode_info', 'theme_fng'),
        'danger' => get_string('generalnoticemode_danger', 'theme_fng')
    ];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_fng/fng_generalnotice';
    $title = get_string('generalnotice', 'theme_fng');
    $description = get_string('generalnoticedesc', 'theme_fng');
    $default = '<strong>Estamos trabajando</strong> para mejorar...';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // --- Chat Settings ---
    $page->add(new admin_setting_heading(
        'theme_fng/chatheading',
        get_string('chatheading', 'theme_fng'),
        ''
    ));

    $name = 'theme_fng/fng_enable_chat';
    $title = get_string('enable_chat', 'theme_fng');
    $description = get_string('enable_chatdesc', 'theme_fng');
    $default = 0;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_fng/fng_tawkto_embed_url';
    $title = get_string('tawkto_embed_url', 'theme_fng');
    $description = get_string('tawkto_embed_urldesc', 'theme_fng');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // --- Accessibility Settings ---
    $page->add(new admin_setting_heading(
        'theme_fng/accessibilityheading',
        get_string('accessibilityheading', 'theme_fng'),
        ''
    ));

    $name = 'theme_fng/fng_accessibility_widget';
    $title = get_string('accessibility_widget', 'theme_fng');
    $description = get_string('accessibility_widgetdesc', 'theme_fng');
    $default = 0;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // --- Content Protection Settings ---
    $page->add(new admin_setting_heading(
        'theme_fng/contentprotectionheading',
        get_string('contentprotectionheading', 'theme_fng'),
        ''
    ));

    $name = 'theme_fng/fng_copypaste_prevention';
    $title = get_string('copypaste_prevention', 'theme_fng');
    $description = get_string('copypaste_preventiondesc', 'theme_fng');
    $default = 0;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Roles para protección
    $roles = role_get_names(null, ROLENAME_ORIGINAL);
    $roles_array = [];
    foreach ($roles as $role) {
        $roles_array[$role->id] = $role->localname;
    }

    $name = 'theme_fng/fng_copypaste_roles';
    $title = get_string('copypaste_roles', 'theme_fng');
    $description = get_string('copypaste_rolesdesc', 'theme_fng');
    $default = [5]; // Rol de estudiante por defecto
    $setting = new admin_setting_configmultiselect($name, $title, $description, $default, $roles_array);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add_tab($page);

    /* =========================================================================
       TAB 2: Login Page Settings
       ========================================================================= */
    $page = new admin_settingpage('theme_fng_login', get_string('loginsettings', 'theme_fng'));

    // Configuración de Carrusel en Login
    $page->add(new admin_setting_heading(
        'theme_fng_carousel',
        get_string('carouselsettings', 'theme_fng'),
        get_string('carouselsettings_desc', 'theme_fng')
    ));

    // Número de slides
    $name = 'theme_fng/fng_numberofslides';
    $title = get_string('numberofslides', 'theme_fng');
    $description = get_string('numberofslides_desc', 'theme_fng');
    $choices = range(1, 5);
    $page->add(new admin_setting_configselect($name, $title, $description, 1, array_combine($choices, $choices)));

    // Settings para cada slide
    $numslides = get_config('theme_fng', 'fng_numberofslides') ?: 1;
    for ($i = 1; $i <= $numslides; $i++) {
        // Encabezado para cada slide
        $page->add(new admin_setting_heading(
            'theme_fng/slideheading' . $i,
            get_string('slideheading', 'theme_fng', $i),
            get_string('slideheading_desc', 'theme_fng', $i)
        ));
        
        // Imagen del slide (obligatoria)
        $name = 'theme_fng/fng_slideimage' . $i;
        $title = get_string('slideimage', 'theme_fng', $i);
        $description = get_string('slideimage_desc', 'theme_fng', $i);
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fng_slideimage' . $i, 0, [
            'subdirs' => 0,
            'accepted_types' => ['web_image']
        ]);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
        
        // Título del slide (opcional)
        $name = 'theme_fng/fng_slidetitle' . $i;
        $title = get_string('slidetitle', 'theme_fng', $i);
        $description = get_string('slidetitledesc', 'theme_fng', $i);
        $page->add(new admin_setting_configtext($name, $title, $description, ''));
        
        // URL del slide (opcional)
        $name = 'theme_fng/fng_slideurl' . $i;
        $title = get_string('slideurl', 'theme_fng', $i);
        $description = get_string('slideurldesc', 'theme_fng', $i);
        $page->add(new admin_setting_configtext($name, $title, $description, ''));
    }

    // Intervalo del carrusel
    $name = 'theme_fng/fng_carouselinterval';
    $title = get_string('carouselinterval', 'theme_fng');
    $description = get_string('carouselintervaldesc', 'theme_fng');
    $setting = new admin_setting_configtext($name, $title, $description, '5000');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add_tab($page);

    /* =========================================================================
       TAB 3: Dashboard Settings
       ========================================================================= */
    $page = new admin_settingpage('theme_fng_dashboard', get_string('dashboardsettings', 'theme_fng'));

    // Personal Area Header Settings
    $page->add(new admin_setting_heading(
        'theme_fng/personalareaheading',
        get_string('personalareaheading', 'theme_fng'),
        ''
    ));

    // Toggle de visibilidad del Personal Area Header
    $name = 'theme_fng/fng_show_personalareaheader';
    $title = get_string('show_personalareaheader', 'theme_fng');
    $description = get_string('show_personalareaheaderdesc', 'theme_fng');
    $default = 1;
    $choices = [
        0 => get_string('hide', 'theme_fng'),
        1 => get_string('show', 'theme_fng')
    ];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Imagen del Personal Area Header
    $name = 'theme_fng/fng_personalareaheader';
    $title = get_string('personalareaheader', 'theme_fng');
    $description = get_string('personalareaheaderdesc', 'theme_fng', $a);
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'fng_personalareaheader', 0, [
        'subdirs' => 0,
        'accepted_types' => 'web_image'
    ]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // My Courses Header Settings
    $page->add(new admin_setting_heading(
        'theme_fng/mycoursesheading',
        get_string('mycoursesheading', 'theme_fng'),
        ''
    ));

    // Toggle de visibilidad del My Courses Header
    $name = 'theme_fng/fng_show_mycoursesheader';
    $title = get_string('show_mycoursesheader', 'theme_fng');
    $description = get_string('show_mycoursesheaderdesc', 'theme_fng');
    $default = 1;
    $choices = [
        0 => get_string('hide', 'theme_fng'),
        1 => get_string('show', 'theme_fng')
    ];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Imagen del My Courses Header
    $name = 'theme_fng/fng_mycoursesheader';
    $title = get_string('mycoursesheader', 'theme_fng');
    $description = get_string('mycoursesheaderdesc', 'theme_fng', $a);
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'fng_mycoursesheader', 0, [
        'subdirs' => 0,
        'accepted_types' => 'web_image'
    ]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add_tab($page);

    /* =========================================================================
       TAB 4: Footer Settings
       ========================================================================= */
    $page = new admin_settingpage('theme_fng_footer', get_string('footersettings', 'theme_fng'));

    // Visibilidad del Footer
    $name = 'theme_fng/fng_hidefootersections';
    $title = get_string('hidefootersections', 'theme_fng');
    $description = get_string('hidefootersections_desc', 'theme_fng');
    $default = 0;
    $choices = [
        0 => get_string('show', 'theme_fng'),
        1 => get_string('hide', 'theme_fng')
    ];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // About Section
    $page->add(new admin_setting_heading(
        'theme_fng/footeraboutheading',
        get_string('footeraboutheading', 'theme_fng'),
        ''
    ));

    $name = 'theme_fng/fng_abouttitle';
    $title = get_string('abouttitle', 'theme_fng');
    $description = get_string('abouttitledesc', 'theme_fng');
    $default = get_string('abouttitle_default', 'theme_fng');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_fng/fng_abouttext';
    $title = get_string('abouttext', 'theme_fng');
    $description = get_string('abouttextdesc', 'theme_fng');
    $default = get_string('abouttext_default', 'theme_fng');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add_tab($page);

    // Si existen pestañas del tema padre, combinarlas
    if ($parent_tabs !== null) {
        $all_tabs = array_merge($asettings->get_tabs(), $parent_tabs);
        $asettings->set_tabs($all_tabs);
    }
}

// Agregar la página de configuraciones a la categoría de apariencia
$ADMIN->add('theme_fng', $asettings);
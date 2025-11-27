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
 * Login page layout for theme_fng.
 *
 * @package    theme_fng
 * @copyright  2025 Soporte fng
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Obtener atributos del body y la configuración del tema.
$bodyattributes = $OUTPUT->body_attributes();
$theme = theme_config::load('fng');

// Para el manejo de archivos.
$fs = get_file_storage();
$context = context_system::instance();

// Construimos el contexto para la plantilla.
$templatecontext = [
    'sitename'         => format_string(
        $SITE->fullname,
        true,
        ['context' => context_course::instance(SITEID), 'escape' => false]
    ),
    'output'           => $OUTPUT,
    'bodyattributes'   => $bodyattributes,
    'carouselimages'   => [],
    'carouselinterval' => isset($theme->settings->fng_carouselinterval) ? (int)$theme->settings->fng_carouselinterval : 5000,
    'my_credit'        => get_string('credit', 'theme_fng'),
    'hasgeneralnote'   => false,
    'generalnote'      => '',
    // Variables de control para la visualización.
    'has_carousel'     => false,
    'multiple_slides'  => false
];

// =========================================================================
// Carrusel de diapositivas
// =========================================================================
// Inicializamos las variables de control del carrusel
$templatecontext['has_carousel'] = false;
$templatecontext['multiple_slides'] = false;

// Se obtiene el número de slides configurado con el prefijo "fng_"
$numslides = isset($theme->settings->fng_numberofslides) && is_numeric($theme->settings->fng_numberofslides)
    ? (int)$theme->settings->fng_numberofslides
    : 1;

$validSlides = 0; // Contador de slides válidos

// Para depuración (opcional)
// if (debugging()) {
//     debugging('Número de slides configurados: ' . $numslides, DEBUG_DEVELOPER);
// }

for ($i = 1; $i <= $numslides; $i++) {
    // Obtenemos la URL de la imagen usando el método más directo
    $imageurl = $theme->setting_file_url("fng_slideimage{$i}", "fng_slideimage{$i}");
    
    if (!empty($imageurl)) {
        $validSlides++;

        // Extraer título
        $slidetitle = '';
        $slidetitlekey = "fng_slidetitle{$i}";
        if (isset($theme->settings->{$slidetitlekey})) {
            $slidetitle = format_string(
                $theme->settings->{$slidetitlekey},
                true,
                ['escape' => false]
            );
        }

        // Extraer URL
        $slideurl = '#';
        $slideurlkey = "fng_slideurl{$i}";
        if (!empty($theme->settings->{$slideurlkey})) {
            $slideurl = $theme->settings->{$slideurlkey};
        }

        // Se añade la diapositiva al array con sus settings
        $templatecontext['carouselimages'][] = [
            'url'       => $imageurl,
            'link'      => $slideurl,
            'title'     => $slidetitle,
            'has_title' => !empty($slidetitle),
            'has_link'  => (!empty($slideurl) && $slideurl !== '#'),
            'first'     => ($validSlides === 1),
            'index'     => ($validSlides - 1),
            'real_index'=> $i
        ];
    }
}

// Actualizamos las variables de control del carrusel
$templatecontext['has_carousel'] = ($validSlides > 0);
$templatecontext['multiple_slides'] = ($validSlides > 1);

// Si no hay slides válidos, usar una imagen por defecto
if (!$templatecontext['has_carousel']) {
    $defaultImage = $OUTPUT->image_url('slide0', 'theme_fng');
    $templatecontext['carouselimages'][] = [
        'url'       => (string)$defaultImage,
        'link'      => '#',
        'title'     => '',
        'has_title' => false,   // No mostrar título
        'has_link'  => false,   // No mostrar enlace
        'first'     => true,
        'index'     => 1,       // Se mostrará como "Slide 1"
        'real_index'=> 1
    ];
    $templatecontext['has_carousel'] = true;
    $templatecontext['multiple_slides'] = false;
}

// Renderizar la plantilla con este contexto
echo $OUTPUT->render_from_template('theme_fng/core/login-custom', $templatecontext);

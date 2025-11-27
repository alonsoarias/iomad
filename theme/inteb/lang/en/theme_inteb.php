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
 * Strings for component 'theme_inteb'
 *
 * @package   theme_inteb
 * @copyright (c) 2025 IngeWeb <soporte@ingeweb.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// General strings
$string['pluginname'] = 'INTEB';
$string['configtitle'] = 'INTEB Theme';
$string['choosereadme'] = 'INTEB is a child theme of RemUI adapted for INTEB needs.';

// New strings for scheduled tasks
$string['license_check_task'] = 'INTEB license verification task';
$string['license_activated'] = 'License automatically activated';
$string['license_activation_automatic'] = 'The license has been automatically activated. No additional action is required.';

// Theme settings
$string['themesettings'] = 'Theme settings';
$string['themesettingsgeneral'] = 'General';
$string['themesettingslogin'] = 'Login page';
$string['dashboardsettings'] = 'Dashboard';
$string['footersettings'] = 'Footer';

// General notice
$string['generalnoticemode'] = 'General notice mode';
$string['generalnoticemodedesc'] = 'Choose the type of general notice that will be displayed on the site.';
$string['generalnoticemode_off'] = 'Disabled';
$string['generalnoticemode_info'] = 'Information (blue)';
$string['generalnoticemode_danger'] = 'Warning (red)';
$string['generalnotice'] = 'General notice';
$string['generalnoticedesc'] = 'Text to be displayed in the general notice on all pages.';
$string['generalnotice_create'] = 'Click here to create a general notice';

// Chat settings
$string['themesettingschat'] = 'Chat settings';
$string['themesettingschatdesc'] = 'Configure the chat widget options for the site.';
$string['enable_chat'] = 'Enable chat';
$string['enable_chatdesc'] = 'Show chat widget on the site.';
$string['tawkto_embed_url'] = 'Tawk.to embed URL';
$string['tawkto_embed_urldesc'] = 'Enter the embed URL provided by Tawk.to.';

// Content protection
$string['themesettingscopypaste'] = 'Content protection';
$string['themesettingscopypaste_desc'] = 'Prevent certain roles from copying and pasting content.';
$string['copypaste_prevention'] = 'Copy and paste prevention';
$string['copypaste_preventiondesc'] = 'Prevent certain roles from copying and pasting content.';
$string['copypaste_roles'] = 'Roles to protect';
$string['copypaste_rolesdesc'] = 'Select the roles to which copy and paste prevention will be applied.';

// Carousel settings
$string['carouselsettings'] = 'Carousel settings';
$string['carouselsettings_desc'] = 'Configure the slides for the login page carousel.';
$string['numberofslides'] = 'Number of slides';
$string['numberofslides_desc'] = 'Select how many slides to show in the carousel.';
$string['slidetitle'] = 'Slide {$a} title';
$string['slidetitle_desc'] = 'Title for slide {$a}.';
$string['slideimage'] = 'Slide {$a} image';
$string['slideimage_desc'] = 'Upload an image for slide {$a} (recommended size: 1600x900px).';
$string['slideurl'] = 'Slide {$a} link';
$string['slideurldesc'] = 'URL that slide {$a} will link to.';
$string['carouselinterval'] = 'Carousel interval';
$string['carouselintervaldesc'] = 'Time in milliseconds between slide transitions.';

// Personal area headers
$string['showpersonalareaheader'] = 'Show personal area header';
$string['showpersonalareaheader_desc'] = 'Show a header image on the Personal Area page.';
$string['personalareaheader'] = 'Personal area header image';
$string['personalareaheaderdesc'] = 'Upload an image for the personal area header (recommended size: 1600x300px). {$a->example_banner}';

// My courses headers
$string['showmycoursesheader'] = 'Show My Courses header';
$string['showmycoursesheader_desc'] = 'Show a header image on the My Courses page.';
$string['mycoursesheader'] = 'My Courses header image';
$string['mycoursesheaderdesc'] = 'Upload an image for the My Courses header (recommended size: 1600x300px). {$a->example_banner}';

// Homepage
$string['hidefrontpagesections'] = 'Hide homepage sections';
$string['hidefrontpagesections_desc'] = 'Hide the frontend sections of the homepage.';

// Footer
$string['hidefootersections'] = 'Hide footer sections';
$string['hidefootersections_desc'] = 'Hide the footer sections.';
$string['abouttitle'] = 'About title';
$string['abouttitledesc'] = 'Title for the About section in the footer.';
$string['abouttitle_default'] = 'About us';
$string['abouttext'] = 'About text';
$string['abouttextdesc'] = 'Text for the About section in the footer. Includes institutional information and GOV.CO branding.';
$string['abouttext_default'] = '
<div class="iser-footer-content">
    <!-- Institutional Information -->
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-md-10 col-lg-8">
            <p class="mb-2"><strong>Instituto Superior de Educación Rural - ISER</strong></p>
            <p class="mb-1">Public Higher Education Institution</p>
            <p class="mb-1">Supervised by the Ministry of National Education</p>
            <p class="mb-0"><strong>NIT:</strong> 890.501.578-4</p>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-md-6 col-lg-4 mb-3 mb-md-0">
            <h4 class="h6 mb-2"><i class="fa fa-map-marker me-2"></i> Address</h4>
            <p class="mb-0 small">Calle 8 # 8-155, Barrio Chapinero<br>Pamplona, Norte de Santander<br>Colombia - P.O. Box 1031</p>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <h4 class="h6 mb-2"><i class="fa fa-phone me-2"></i> Contact</h4>
            <p class="mb-1 small"><strong>PBX:</strong> (607) 568 6868</p>
            <p class="mb-1 small"><strong>Email:</strong> <a href="mailto:iserpam@iser.edu.co">iserpam@iser.edu.co</a></p>
            <p class="mb-0 small"><strong>Hours:</strong> Mon-Fri 8:00-12:00 | 14:00-18:00</p>
        </div>
    </div>

    <!-- Social Media -->
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-md-8">
            <div class="social-links d-flex justify-content-center gap-3 flex-wrap">
                <a href="https://www.facebook.com/iser.pamplona" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-light" aria-label="Facebook ISER">
                    <i class="fa fa-facebook"></i> Facebook
                </a>
                <a href="https://twitter.com/Iser1956" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-light" aria-label="Twitter ISER">
                    <i class="fa fa-twitter"></i> Twitter
                </a>
                <a href="https://www.instagram.com/iser.oficial" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-light" aria-label="Instagram ISER">
                    <i class="fa fa-instagram"></i> Instagram
                </a>
                <a href="https://www.youtube.com/@ISER.Oficial" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-light" aria-label="YouTube ISER">
                    <i class="fa fa-youtube-play"></i> YouTube
                </a>
            </div>
        </div>
    </div>

    <!-- Institutional Links -->
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-md-10">
            <div class="institutional-links d-flex justify-content-center gap-3 flex-wrap small">
                <a href="https://www.iser.edu.co/" target="_blank" rel="noopener noreferrer">ISER Portal</a>
                <span class="text-muted">|</span>
                <a href="https://www.iser.edu.co/index.php/transparencia-y-acceso-a-la-informacion-publica/" target="_blank" rel="noopener noreferrer">Transparency</a>
                <span class="text-muted">|</span>
                <a href="https://ww1.iser.edu.co/iser/qrsIG/index.jsp" target="_blank" rel="noopener noreferrer">PQRS</a>
                <span class="text-muted">|</span>
                <a href="https://www.iser.edu.co/index.php/atencion-al-ciudadano-y-servicios/" target="_blank" rel="noopener noreferrer">Citizen Services</a>
                <span class="text-muted">|</span>
                <a href="https://www.iser.edu.co/index.php/oferta-academica-2024/" target="_blank" rel="noopener noreferrer">Academic Programs</a>
            </div>
        </div>
    </div>

    <!-- GOV.CO Logos and Country Brand -->
    <div class="row justify-content-center align-items-center mt-4 pt-3 border-top border-light">
        <div class="col-6 col-md-3 text-center mb-3 mb-md-0">
            <a href="https://www.gov.co/" target="_blank" rel="noopener noreferrer" aria-label="GOV.CO Portal">
                <img src="https://www.gov.co/assets/images/logo-gov-co-blanco.svg" alt="GOV.CO Logo" style="height: 40px; max-width: 100%;" loading="lazy">
            </a>
        </div>
        <div class="col-6 col-md-3 text-center">
            <a href="https://www.colombia.co/" target="_blank" rel="noopener noreferrer" aria-label="Colombia Country Brand">
                <img src="https://www.colombia.co/static/img/head/logo-header.svg" alt="Colombia Logo" style="height: 35px; max-width: 100%;" loading="lazy">
            </a>
        </div>
    </div>
</div>';
$string['credit'] = ' © 2025 - Todos los derechos reservados';

// Login page
$string['default_slide_title'] = 'Welcome to our educational platform';
$string['hide'] = 'Hide';
$string['show'] = 'Show';

// License
$string['activatelicense'] = 'Activate license';
$string['licenseactivation'] = 'License activation';
$string['licenseactivationdesc'] = 'The license is activated automatically. If you experience any issues, you can use the button below to activate it manually.';
$string['themeinfotext'] = '<div class="alert alert-info">
<p>The INTEB theme is a customized adaptation based on Edwiser RemUI.</p>
<p>This version includes automatic license activation and specific fixes for INTEB.</p>
<p>Version: 4.5.0</p>
</div>';

// Messages for unauthorized access
$string['unauthorized_access'] = 'Unauthorized Access';
$string['unauthorized_access_msg'] = 'You are accessing this site from an unauthorized domain. Please contact the system administrator.';
$string['devtools_access_disabled'] = 'Developer tools access is disabled on this site.';

// Additional dashboard strings
$string['dashboardpersonalizerinfo'] = 'Dashboard personalizer info';
$string['defaultheader'] = 'Default header';
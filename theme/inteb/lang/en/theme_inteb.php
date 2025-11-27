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

// Footer - GOV.CO columns configuration
$string['hidefootersections'] = 'Hide footer sections';
$string['hidefootersections_desc'] = 'Hide the footer sections.';

// Footer columns configuration
$string['footercolumnsheading'] = 'Footer columns configuration';
$string['footercolumnsheading_desc'] = 'Configure the number of columns and their content for the footer. GOV.CO compatible design.';
$string['footercolumns'] = 'Number of columns';
$string['footercolumns_desc'] = 'Select how many columns to display in the footer (1-4).';
$string['footercolumn'] = 'Column {$a}';
$string['footercolumn_desc'] = 'HTML content for footer column {$a}.';
$string['footercolumntitle'] = 'Column {$a} title';
$string['footercolumntitle_desc'] = 'Optional title for column {$a} (leave empty to hide title).';

// Default content for each column
$string['footercolumn1_default'] = '<div class="footer-logos-column">
    <div class="footer-logo-item mb-3">
        <a href="https://www.gov.co/" target="_blank" rel="noopener noreferrer" aria-label="GOV.CO Portal">
            <img src="/theme/inteb/pix/logo-govco.webp" alt="GOV.CO Logo" class="footer-logo-govco">
        </a>
    </div>
    <div class="footer-logo-item">
        <a href="https://www.colombia.co/" target="_blank" rel="noopener noreferrer" aria-label="Colombia Country Brand">
            <img src="/theme/inteb/pix/marca_pais.png" alt="Colombia Country Brand Logo" class="footer-logo-colombia">
        </a>
    </div>
</div>';

$string['footercolumn2_default'] = '<p class="footer-info-title"><strong>Instituto Superior de Educación Rural - ISER</strong></p>
<p class="footer-info-text">Public Higher Education Institution<br>Supervised by the Ministry of National Education</p>
<p class="footer-info-text"><strong>NIT:</strong> 890.501.578-4</p>
<p class="footer-info-title mt-3"><strong>Address</strong></p>
<p class="footer-info-text">Calle 8 # 8-155, Barrio Chapinero<br>Pamplona, Norte de Santander<br>Colombia - P.O. Box 1031</p>
<p class="footer-info-title mt-3"><strong>Contact</strong></p>
<p class="footer-info-text"><strong>PBX:</strong> (607) 568 6868</p>
<p class="footer-info-text"><strong>Email:</strong> <a href="mailto:iserpam@iser.edu.co">iserpam@iser.edu.co</a></p>
<p class="footer-info-title mt-3"><strong>Office Hours</strong></p>
<p class="footer-info-text">Monday to Friday 8:00 a.m. to 12:00 p.m. and 2:00 p.m. to 6:00 p.m.</p>';

$string['footercolumn3_default'] = '<p class="footer-info-title"><strong>Institutional Links</strong></p>
<p class="footer-info-text"><a href="https://www.iser.edu.co/" target="_blank" rel="noopener noreferrer">ISER Portal</a></p>
<p class="footer-info-text"><a href="https://www.iser.edu.co/index.php/transparencia-y-acceso-a-la-informacion-publica/" target="_blank" rel="noopener noreferrer">Transparency</a></p>
<p class="footer-info-text"><a href="https://ww1.iser.edu.co/iser/qrsIG/index.jsp" target="_blank" rel="noopener noreferrer">PQRS</a></p>
<p class="footer-info-text"><a href="https://www.iser.edu.co/index.php/atencion-al-ciudadano-y-servicios/" target="_blank" rel="noopener noreferrer">Citizen Services</a></p>
<p class="footer-info-title mt-3"><strong>Oversight Entities</strong></p>
<p class="footer-info-text"><a href="https://www.mineducacion.gov.co/" target="_blank" rel="noopener noreferrer">Ministry of National Education</a></p>
<p class="footer-info-text"><a href="https://www.contraloria.gov.co/" target="_blank" rel="noopener noreferrer">Comptroller General of the Republic</a></p>
<p class="footer-info-text"><a href="https://www.procuraduria.gov.co/" target="_blank" rel="noopener noreferrer">Attorney General of the Nation</a></p>
<p class="footer-info-title mt-3"><strong>Follow Us</strong></p>
<div class="footer-social-icons">
    <a href="https://www.facebook.com/iser.pamplona" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="footer-social-link"><i class="fa fa-facebook"></i></a>
    <a href="https://twitter.com/Iser1956" target="_blank" rel="noopener noreferrer" aria-label="Twitter" class="footer-social-link"><i class="fa fa-twitter"></i></a>
    <a href="https://www.instagram.com/iser.oficial" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="footer-social-link"><i class="fa fa-instagram"></i></a>
    <a href="https://www.youtube.com/@ISER.Oficial" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="footer-social-link"><i class="fa fa-youtube-play"></i></a>
</div>';

$string['footercolumn4_default'] = '<p class="footer-info-title"><strong>Follow Us</strong></p>
<div class="footer-social-icons">
    <a href="https://www.facebook.com/iser.pamplona" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="footer-social-link"><i class="fa fa-facebook"></i></a>
    <a href="https://twitter.com/Iser1956" target="_blank" rel="noopener noreferrer" aria-label="Twitter" class="footer-social-link"><i class="fa fa-twitter"></i></a>
    <a href="https://www.instagram.com/iser.oficial" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="footer-social-link"><i class="fa fa-instagram"></i></a>
    <a href="https://www.youtube.com/@ISER.Oficial" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="footer-social-link"><i class="fa fa-youtube-play"></i></a>
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
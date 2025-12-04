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
 * Post-installation script for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Run on plugin installation.
 *
 * @return bool
 */
function xmldb_local_jobboard_install() {
    global $DB;

    // Insert predefined document types.
    $doctypes = [
        [
            'code' => 'sigep',
            'name' => 'Formato Único Hoja de Vida SIGEP II',
            'description' => 'Hoja de vida actualizada en SIGEP II',
            'requirements' => 'Todos los campos diligenciados, experiencia conforme certificaciones laborales adjuntas, toda experiencia con soporte, debe estar firmado',
            'checklistitems' => json_encode([
                'Es el formato oficial de SIGEP II',
                'Todos los campos están diligenciados',
                'Experiencia relacionada corresponde con certificaciones laborales adjuntas',
                'Toda la experiencia relacionada tiene soporte documental',
                'Documento está firmado',
                'Datos personales son consistentes con otros documentos',
            ]),
            'externalurl' => '',
            'iserexempted' => 0,
            'defaultmaxagedays' => null,
            'sortorder' => 1,
            'enabled' => 1,
        ],
        [
            'code' => 'bienes_rentas',
            'name' => 'Formato Declaración de Bienes y Rentas',
            'description' => 'Declaración de bienes y rentas',
            'requirements' => 'Información de la vigencia inmediatamente anterior, debe estar firmado',
            'checklistitems' => json_encode([
                'Es el formato oficial de declaración',
                'Información corresponde a la vigencia inmediatamente anterior',
                'Documento está firmado',
                'Fecha de firma es visible',
                'Información es legible',
            ]),
            'externalurl' => '',
            'iserexempted' => 0,
            'defaultmaxagedays' => null,
            'sortorder' => 2,
            'enabled' => 1,
        ],
        [
            'code' => 'cedula',
            'name' => 'Cédula de Ciudadanía',
            'description' => 'Fotocopia de cédula',
            'requirements' => 'Copia en una sola página, legible',
            'checklistitems' => json_encode([
                'Documento legible',
                'Copia en una sola página (anverso y reverso en la misma hoja)',
                'Contiene foto del titular',
                'Número de cédula visible',
                'Nombre completo coincide con datos de postulación',
            ]),
            'externalurl' => '',
            'iserexempted' => 1,
            'defaultmaxagedays' => null,
            'sortorder' => 3,
            'enabled' => 1,
        ],
        [
            'code' => 'titulo_academico',
            'name' => 'Títulos Académicos',
            'description' => 'Copia de títulos académicos (pregrado, posgrado, especialización)',
            'requirements' => 'Legibles, completos, debe evidenciarse número de folio, registro y fecha. Títulos extranjeros: Diploma, acta de grado y resolución de convalidación',
            'checklistitems' => json_encode([
                'Es diploma o acta de grado legible y completo',
                'Nombre del programa es visible',
                'Título obtenido es claro',
                'Número de folio es visible',
                'Número de registro es visible',
                'Fecha de grado es visible',
                'Firma de autoridad universitaria presente',
                'Si es título extranjero: incluye diploma, acta de grado Y resolución de convalidación',
            ]),
            'externalurl' => '',
            'iserexempted' => 1,
            'defaultmaxagedays' => null,
            'sortorder' => 4,
            'enabled' => 1,
        ],
        [
            'code' => 'tarjeta_profesional',
            'name' => 'Tarjeta Profesional',
            'description' => 'Fotocopia de tarjeta profesional',
            'requirements' => 'Legible, vigente',
            'checklistitems' => json_encode([
                'Número de tarjeta profesional visible',
                'Nombre del profesional coincide',
                'Consejo profesional que la expide',
                'No está vencida (si tiene fecha)',
            ]),
            'externalurl' => '',
            'iserexempted' => 1,
            'defaultmaxagedays' => null,
            'sortorder' => 5,
            'enabled' => 1,
        ],
        [
            'code' => 'libreta_militar',
            'name' => 'Libreta Militar',
            'description' => 'Libreta militar o documento que acredite situación militar definida',
            'requirements' => 'Para hombres entre 18-28 años. Los declarados no aptos, exentos o que hayan superado edad máxima deben presentar certificado provisional',
            'checklistitems' => json_encode([
                'Es libreta militar O documento que acredite situación militar definida',
                'Aplica para hombres entre 18 y 28 años',
                'Si es declarado no apto, exento o superó edad máxima: certificado provisional',
                'Documento es legible',
                'Nombre coincide con datos de postulación',
            ]),
            'externalurl' => '',
            'iserexempted' => 1,
            'defaultmaxagedays' => null,
            'sortorder' => 6,
            'enabled' => 1,
        ],
        [
            'code' => 'formacion_complementaria',
            'name' => 'Certificados de Formación Complementaria',
            'description' => 'Fotocopia de certificados de formación complementaria',
            'requirements' => 'Legibles, completos',
            'checklistitems' => json_encode([
                'Certificado es legible',
                'Certificado está completo',
                'Nombre del programa/curso es visible',
                'Nombre del participante coincide con postulante',
                'Institución que expide es identificable',
                'Duración o intensidad horaria especificada',
                'Fecha de realización o expedición visible',
            ]),
            'externalurl' => '',
            'iserexempted' => 0,
            'defaultmaxagedays' => null,
            'sortorder' => 7,
            'enabled' => 1,
        ],
        [
            'code' => 'certificacion_laboral',
            'name' => 'Certificaciones Laborales',
            'description' => 'Constancias laborales',
            'requirements' => 'SOLO certificados laborales. NO son válidos: contratos, actas de finalización, actos administrativos de nombramientos',
            'checklistitems' => json_encode([
                'Es un certificado laboral (NO contrato, acta de finalización o acto administrativo)',
                'Contiene membrete de la empresa',
                'Indica fechas de vinculación (desde - hasta)',
                'Especifica cargo desempeñado',
                'Incluye firma de autoridad competente',
                'Tiene fecha de expedición reciente',
                'Documento es original o copia legible',
            ]),
            'externalurl' => '',
            'iserexempted' => 1,
            'defaultmaxagedays' => null,
            'sortorder' => 8,
            'enabled' => 1,
        ],
        [
            'code' => 'rut',
            'name' => 'RUT (Registro Único Tributario)',
            'description' => 'Fotocopia del RUT actualizado',
            'requirements' => 'Verificar fecha en parte inferior derecha del documento, debe estar actualizado',
            'checklistitems' => json_encode([
                'Documento legible',
                'Número de NIT/RUT visible',
                'Nombre/razón social coincide con datos del postulante',
                'Fecha de actualización visible en parte inferior derecha',
                'Fecha de actualización es reciente',
            ]),
            'externalurl' => '',
            'iserexempted' => 0,
            'defaultmaxagedays' => null,
            'sortorder' => 9,
            'enabled' => 1,
        ],
        [
            'code' => 'eps',
            'name' => 'Certificado EPS',
            'description' => 'Certificado de afiliación a EPS',
            'requirements' => 'Fecha de expedición no mayor a 30 días, debe evidenciar estado activo',
            'checklistitems' => json_encode([
                'Es certificado de afiliación (no carné)',
                'Nombre de la EPS',
                'Nombre del afiliado coincide',
                'Fecha de expedición no mayor a 30 días',
                'Estado: activo',
            ]),
            'externalurl' => '',
            'iserexempted' => 0,
            'defaultmaxagedays' => 30,
            'sortorder' => 10,
            'enabled' => 1,
        ],
        [
            'code' => 'pension',
            'name' => 'Certificado Pensión',
            'description' => 'Certificado del fondo de pensión',
            'requirements' => 'Fecha de expedición no mayor a 30 días. Pensionados: adjuntar resolución de pensión. Magisterio: adjuntar certificado RUAF',
            'checklistitems' => json_encode([
                'Es certificado de afiliación al fondo de pensión',
                'Nombre del fondo de pensiones visible',
                'Nombre del afiliado coincide con postulante',
                'Fecha de expedición no mayor a 30 días',
                'Estado: activo o cotizando',
                'CASO ESPECIAL Pensionados: adjuntar resolución de pensión',
                'CASO ESPECIAL Magisterio: adjuntar certificado RUAF',
            ]),
            'externalurl' => '',
            'iserexempted' => 0,
            'defaultmaxagedays' => 30,
            'sortorder' => 11,
            'enabled' => 1,
        ],
        [
            'code' => 'cuenta_bancaria',
            'name' => 'Certificado Cuenta Bancaria',
            'description' => 'Certificado de cuenta bancaria',
            'requirements' => 'Debe evidenciar número de cuenta, tipo de cuenta, entidad bancaria, cuenta a nombre del postulante',
            'checklistitems' => json_encode([
                'Es certificado oficial de la entidad bancaria',
                'Número de cuenta es visible',
                'Tipo de cuenta especificado (ahorros/corriente)',
                'Entidad bancaria identificada',
                'Cuenta está a nombre del postulante',
                'Nombres coinciden con documento de identidad',
            ]),
            'externalurl' => '',
            'iserexempted' => 0,
            'defaultmaxagedays' => null,
            'sortorder' => 12,
            'enabled' => 1,
        ],
        [
            'code' => 'antecedentes_disciplinarios',
            'name' => 'Antecedentes Disciplinarios',
            'description' => 'Certificado expedido por Procuraduría General de la Nación',
            'requirements' => 'Fecha reciente (no mayor a 30 días recomendado)',
            'checklistitems' => json_encode([
                'Es certificado oficial de la Procuraduría General de la Nación',
                'Nombre del consultado coincide con postulante',
                'Número de cédula coincide',
                'Fecha de expedición es reciente (preferible <30 días)',
                'Código de verificación visible (si aplica)',
                'Estado de antecedentes es claro',
            ]),
            'externalurl' => '',
            'iserexempted' => 0,
            'defaultmaxagedays' => 30,
            'sortorder' => 13,
            'enabled' => 1,
        ],
        [
            'code' => 'antecedentes_fiscales',
            'name' => 'Antecedentes Fiscales',
            'description' => 'Certificado expedido por Contraloría General de la Nación',
            'requirements' => 'Fecha reciente (no mayor a 30 días recomendado)',
            'checklistitems' => json_encode([
                'Es certificado oficial de la Contraloría General de la Nación',
                'Nombre del consultado coincide con postulante',
                'Número de cédula coincide',
                'Fecha de expedición es reciente (preferible <30 días)',
                'Código de verificación visible (si aplica)',
                'Estado de antecedentes es claro',
            ]),
            'externalurl' => '',
            'iserexempted' => 0,
            'defaultmaxagedays' => 30,
            'sortorder' => 14,
            'enabled' => 1,
        ],
        [
            'code' => 'antecedentes_judiciales',
            'name' => 'Antecedentes Judiciales',
            'description' => 'Certificado expedido por Policía Nacional',
            'requirements' => 'Fecha reciente (no mayor a 30 días recomendado)',
            'checklistitems' => json_encode([
                'Es certificado oficial de Policía Nacional',
                'Descargado desde portal oficial',
                'Nombre del consultado coincide',
                'Número de cédula coincide',
                'Fecha de expedición es reciente (<30 días)',
                'Tiene código de verificación',
            ]),
            'externalurl' => 'https://antecedentes.policia.gov.co:7005/WebJudicial',
            'iserexempted' => 0,
            'defaultmaxagedays' => 30,
            'sortorder' => 15,
            'enabled' => 1,
        ],
        [
            'code' => 'medidas_correctivas',
            'name' => 'Registro Nacional de Medidas Correctivas',
            'description' => 'Certificado de medidas correctivas',
            'requirements' => 'Descargado del portal oficial',
            'checklistitems' => json_encode([
                'Es certificado oficial de Policía Nacional',
                'Descargado desde portal oficial',
                'Nombre del consultado coincide con postulante',
                'Número de cédula coincide',
                'Fecha de expedición es reciente',
                'Estado es claro',
            ]),
            'externalurl' => 'https://srvcnpc.policia.gov.co/PSC/frm_cnp_consulta.aspx',
            'iserexempted' => 0,
            'defaultmaxagedays' => 30,
            'sortorder' => 16,
            'enabled' => 1,
        ],
        [
            'code' => 'inhabilidades',
            'name' => 'Consulta de Inhabilidades (Delitos Sexuales)',
            'description' => 'Consulta de inhabilidades por delitos sexuales contra menores (Ley 1918 de 2018)',
            'requirements' => 'Certificado descargado del portal oficial',
            'checklistitems' => json_encode([
                'Es certificado oficial del Sistema de Información de Inhabilidades',
                'Descargado desde portal oficial',
                'Nombre del consultado coincide con postulante',
                'Número de cédula coincide',
                'Fecha de expedición es reciente',
                'Resultado de consulta es claro',
            ]),
            'externalurl' => 'https://inhabilidades.policia.gov.co:8080/',
            'iserexempted' => 0,
            'defaultmaxagedays' => 30,
            'sortorder' => 17,
            'enabled' => 1,
        ],
        [
            'code' => 'redam',
            'name' => 'REDAM (Registro de Deudores Alimentarios Morosos)',
            'description' => 'Certificado REDAM',
            'requirements' => 'Registrarse en el portal y descargar certificado',
            'checklistitems' => json_encode([
                'Es certificado oficial de REDAM',
                'Descargado desde Carpeta Ciudadana',
                'Nombre del consultado coincide con postulante',
                'Número de cédula coincide',
                'Fecha de expedición es reciente',
                'Estado de registro es claro',
            ]),
            'externalurl' => 'https://carpetaciudadana.and.gov.co/inicio-de-sesion',
            'iserexempted' => 0,
            'defaultmaxagedays' => 30,
            'sortorder' => 18,
            'enabled' => 1,
        ],
    ];

    $now = time();
    foreach ($doctypes as $doctype) {
        $doctype['timecreated'] = $now;
        $DB->insert_record('local_jobboard_doctype', (object) $doctype);
    }

    // Insert default email templates.
    $templates = [
        [
            'code' => 'application_submitted',
            'name' => 'Confirmación de postulación',
            'subject' => 'Confirmación de postulación - {VACANCY_TITLE}',
            'body' => '<p>Estimado/a {USER_FULLNAME},</p>
<p>Su postulación para la vacante <strong>{VACANCY_CODE} - {VACANCY_TITLE}</strong> ha sido recibida exitosamente.</p>
<p>Fecha de postulación: {APPLICATION_DATE}</p>
<p>Estado actual: {CURRENT_STATUS}</p>
<p>Puede consultar el estado de su postulación en cualquier momento accediendo a: {APPLICATION_URL}</p>
<p>Gracias por su interés.</p>
<p>Atentamente,<br>Equipo de Recursos Humanos</p>',
            'bodyformat' => 1,
            'enabled' => 1,
        ],
        [
            'code' => 'under_review',
            'name' => 'Postulación en revisión',
            'subject' => 'Su postulación está siendo revisada - {VACANCY_TITLE}',
            'body' => '<p>Estimado/a {USER_FULLNAME},</p>
<p>Le informamos que su postulación para la vacante <strong>{VACANCY_CODE} - {VACANCY_TITLE}</strong> está siendo revisada por nuestro equipo.</p>
<p>Le notificaremos cuando tengamos novedades sobre el estado de su postulación.</p>
<p>Atentamente,<br>Equipo de Recursos Humanos</p>',
            'bodyformat' => 1,
            'enabled' => 1,
        ],
        [
            'code' => 'docs_rejected',
            'name' => 'Documentos rechazados',
            'subject' => 'Se requiere corrección de documentos - {VACANCY_TITLE}',
            'body' => '<p>Estimado/a {USER_FULLNAME},</p>
<p>Hemos revisado su postulación para la vacante <strong>{VACANCY_CODE} - {VACANCY_TITLE}</strong> y algunos documentos requieren corrección:</p>
{REJECTED_DOCUMENTS}
<p>Por favor, acceda al sistema para cargar los documentos corregidos: {APPLICATION_URL}</p>
<p>Atentamente,<br>Equipo de Recursos Humanos</p>',
            'bodyformat' => 1,
            'enabled' => 1,
        ],
        [
            'code' => 'docs_validated',
            'name' => 'Documentos validados',
            'subject' => 'Documentos validados exitosamente - {VACANCY_TITLE}',
            'body' => '<p>Estimado/a {USER_FULLNAME},</p>
<p>Sus documentos para la vacante <strong>{VACANCY_CODE} - {VACANCY_TITLE}</strong> han sido validados exitosamente.</p>
<p>Su postulación cumple con los requisitos documentales. Le informaremos sobre los siguientes pasos del proceso.</p>
<p>Atentamente,<br>Equipo de Recursos Humanos</p>',
            'bodyformat' => 1,
            'enabled' => 1,
        ],
        [
            'code' => 'interview',
            'name' => 'Citación a entrevista',
            'subject' => 'Citación a entrevista - {VACANCY_TITLE}',
            'body' => '<p>Estimado/a {USER_FULLNAME},</p>
<p>Nos complace informarle que ha sido seleccionado/a para la etapa de entrevista para la vacante <strong>{VACANCY_CODE} - {VACANCY_TITLE}</strong>.</p>
<p>Nos pondremos en contacto con usted para coordinar los detalles de la entrevista.</p>
<p>¡Felicitaciones!</p>
<p>Atentamente,<br>Equipo de Recursos Humanos</p>',
            'bodyformat' => 1,
            'enabled' => 1,
        ],
        [
            'code' => 'selected',
            'name' => 'Seleccionado',
            'subject' => 'Felicitaciones - Seleccionado para {VACANCY_TITLE}',
            'body' => '<p>Estimado/a {USER_FULLNAME},</p>
<p>Nos complace informarle que ha sido <strong>SELECCIONADO/A</strong> para la vacante <strong>{VACANCY_CODE} - {VACANCY_TITLE}</strong>.</p>
<p>Próximamente recibirá información sobre los siguientes pasos del proceso de vinculación.</p>
<p>¡Felicitaciones y bienvenido/a!</p>
<p>Atentamente,<br>Equipo de Recursos Humanos</p>',
            'bodyformat' => 1,
            'enabled' => 1,
        ],
        [
            'code' => 'rejected',
            'name' => 'No seleccionado',
            'subject' => 'Resultado de su postulación - {VACANCY_TITLE}',
            'body' => '<p>Estimado/a {USER_FULLNAME},</p>
<p>Agradecemos su interés en la vacante <strong>{VACANCY_CODE} - {VACANCY_TITLE}</strong>.</p>
<p>Después de evaluar cuidadosamente todas las postulaciones, lamentamos informarle que en esta ocasión no hemos podido seleccionar su perfil para continuar en el proceso.</p>
<p>Le animamos a postularse a futuras oportunidades que se ajusten a su perfil profesional.</p>
<p>Atentamente,<br>Equipo de Recursos Humanos</p>',
            'bodyformat' => 1,
            'enabled' => 1,
        ],
    ];

    foreach ($templates as $template) {
        $template['timecreated'] = $now;
        $DB->insert_record('local_jobboard_email_template', (object) $template);
    }

    // Import User Tours.
    local_jobboard_install_tours();

    return true;
}

/**
 * Install User Tours for the Job Board plugin.
 *
 * @return void
 */
function local_jobboard_install_tours(): void {
    global $CFG;

    // Check if User Tours tool is available.
    if (!class_exists('\tool_usertours\manager')) {
        return;
    }

    $toursdir = $CFG->dirroot . '/local/jobboard/db/tours/';
    $tourfiles = [
        'jobboard_admin_tour.json',
        'jobboard_applicant_tour.json',
    ];

    foreach ($tourfiles as $tourfile) {
        $filepath = $toursdir . $tourfile;
        if (file_exists($filepath)) {
            $tourjson = file_get_contents($filepath);
            if ($tourjson !== false) {
                try {
                    $tour = \tool_usertours\manager::import_tour_from_json($tourjson);

                    // Mark as shipped tour for tracking.
                    $tour->set_config('shipped_tour', true);
                    $tour->set_config('shipped_filename', $tourfile);
                    $tour->set_config('shipped_version', 1);
                    $tour->persist();
                } catch (\Exception $e) {
                    debugging('Failed to import tour ' . $tourfile . ': ' . $e->getMessage(), DEBUG_DEVELOPER);
                }
            }
        }
    }
}

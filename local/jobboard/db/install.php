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
 * @copyright 2024-2025 ISER
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

    // Insert predefined document types in the required order.
    $doctypes = local_jobboard_get_default_doctypes();

    $now = time();
    foreach ($doctypes as $doctype) {
        $doctype['timecreated'] = $now;
        $DB->insert_record('local_jobboard_doctype', (object) $doctype);
    }

    // Insert default email templates.
    $templates = local_jobboard_get_default_email_templates();

    foreach ($templates as $template) {
        $template['timecreated'] = $now;
        $DB->insert_record('local_jobboard_email_template', (object) $template);
    }

    // Import User Tours.
    local_jobboard_install_tours();

    // Create custom roles.
    local_jobboard_create_roles();

    return true;
}

/**
 * Get default document types for installation.
 *
 * Documents are ordered according to application process requirements.
 *
 * @return array Array of document type definitions.
 */
function local_jobboard_get_default_doctypes(): array {
    return [
        // 1. Carta de intención (campo de texto).
        [
            'code' => 'carta_intencion',
            'name' => 'Carta de Intención',
            'description' => 'Carta de intención del aspirante',
            'requirements' => 'Redacte su carta de intención explicando su motivación para aplicar',
            'checklistitems' => json_encode([
                'Expresa claramente la motivación para postularse',
                'Menciona experiencia relevante',
                'Indica disponibilidad y compromiso',
                'Redacción clara y profesional',
            ]),
            'externalurl' => '',
            'isrequired' => 1,
            'iserexempted' => 0,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'textarea',
            'category' => 'application',
            'defaultmaxagedays' => null,
            'sortorder' => 1,
            'enabled' => 1,
        ],
        // 2. Hoja de vida formato función pública (SIGEP II).
        [
            'code' => 'sigep',
            'name' => 'Hoja de Vida Formato Función Pública',
            'description' => 'Hoja de vida actualizada en formato SIGEP II',
            'requirements' => 'Todos los campos diligenciados, experiencia conforme certificaciones laborales adjuntas, toda experiencia con soporte, debe estar firmado',
            'checklistitems' => json_encode([
                'Es el formato oficial de SIGEP II',
                'Todos los campos están diligenciados',
                'Experiencia relacionada corresponde con certificaciones laborales adjuntas',
                'Toda la experiencia relacionada tiene soporte documental',
                'Documento está firmado',
                'Datos personales son consistentes con otros documentos',
            ]),
            'externalurl' => 'https://www.funcionpublica.gov.co/web/sigep2',
            'isrequired' => 1,
            'iserexempted' => 0,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'employment',
            'defaultmaxagedays' => null,
            'sortorder' => 2,
            'enabled' => 1,
        ],
        // 3. Formato Declaración de Bienes y Rentas.
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
            'isrequired' => 1,
            'iserexempted' => 0,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'employment',
            'defaultmaxagedays' => null,
            'sortorder' => 3,
            'enabled' => 1,
        ],
        // 4. Documento de identidad.
        [
            'code' => 'cedula',
            'name' => 'Documento de Identidad',
            'description' => 'Fotocopia de cédula de ciudadanía',
            'requirements' => 'Copia en una sola página, legible',
            'checklistitems' => json_encode([
                'Documento legible',
                'Copia en una sola página (anverso y reverso en la misma hoja)',
                'Contiene foto del titular',
                'Número de cédula visible',
                'Nombre completo coincide con datos de postulación',
            ]),
            'externalurl' => '',
            'isrequired' => 1,
            'iserexempted' => 1,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'identification',
            'defaultmaxagedays' => null,
            'sortorder' => 4,
            'enabled' => 1,
        ],
        // 5. Libreta militar (si aplica).
        [
            'code' => 'libreta_militar',
            'name' => 'Libreta Militar',
            'description' => 'Libreta militar o documento que acredite situación militar definida',
            'requirements' => 'Solo para hombres. Los declarados no aptos, exentos o que hayan superado edad máxima deben presentar certificado provisional',
            'checklistitems' => json_encode([
                'Es libreta militar O documento que acredite situación militar definida',
                'Aplica solo para hombres',
                'Si es declarado no apto, exento o superó edad máxima: certificado provisional',
                'Documento es legible',
                'Nombre coincide con datos de postulación',
            ]),
            'externalurl' => '',
            'isrequired' => 0,
            'iserexempted' => 1,
            'gender_condition' => 'M',
            'age_exemption_threshold' => 50,
            'profession_exempt' => null,
            'conditional_note' => 'Aplica solo para hombres menores de 50 años',
            'input_type' => 'file',
            'category' => 'identification',
            'defaultmaxagedays' => null,
            'sortorder' => 5,
            'enabled' => 1,
        ],
        // 6. Títulos pregrado y posgrado.
        [
            'code' => 'titulo_academico',
            'name' => 'Títulos Pregrado y Posgrado',
            'description' => 'Copia de títulos académicos (pregrado, posgrado, especialización, maestría, doctorado)',
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
            'isrequired' => 1,
            'iserexempted' => 1,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'academic',
            'defaultmaxagedays' => null,
            'sortorder' => 6,
            'enabled' => 1,
        ],
        // 7. Tarjeta profesional (si aplica).
        [
            'code' => 'tarjeta_profesional',
            'name' => 'Tarjeta Profesional',
            'description' => 'Fotocopia de tarjeta profesional',
            'requirements' => 'Legible, vigente. No aplica para licenciados en educación.',
            'checklistitems' => json_encode([
                'Número de tarjeta profesional visible',
                'Nombre del profesional coincide',
                'Consejo profesional que la expide',
                'No está vencida (si tiene fecha)',
            ]),
            'externalurl' => '',
            'isrequired' => 0,
            'iserexempted' => 1,
            'gender_condition' => null,
            'profession_exempt' => json_encode(['licenciatura']),
            'conditional_note' => 'No aplica para Licenciados en Educación',
            'input_type' => 'file',
            'category' => 'academic',
            'defaultmaxagedays' => null,
            'sortorder' => 7,
            'enabled' => 1,
        ],
        // 8. Certificaciones complementarias (mínimo 40 horas).
        [
            'code' => 'formacion_complementaria',
            'name' => 'Certificaciones Complementarias',
            'description' => 'Certificados de formación complementaria (mínimo 40 horas)',
            'requirements' => 'Legibles, completos. Mínimo 40 horas de intensidad por certificado.',
            'checklistitems' => json_encode([
                'Certificado es legible',
                'Certificado está completo',
                'Nombre del programa/curso es visible',
                'Nombre del participante coincide con postulante',
                'Institución que expide es identificable',
                'Duración o intensidad horaria especificada (mínimo 40 horas)',
                'Fecha de realización o expedición visible',
            ]),
            'externalurl' => '',
            'isrequired' => 0,
            'iserexempted' => 0,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'academic',
            'defaultmaxagedays' => null,
            'sortorder' => 8,
            'enabled' => 1,
        ],
        // 9. Experiencia docente.
        [
            'code' => 'experiencia_docente',
            'name' => 'Experiencia Docente',
            'description' => 'Certificaciones de experiencia en docencia',
            'requirements' => 'SOLO certificados laborales de experiencia docente. Deben indicar: institución, fechas, carga horaria, programas/asignaturas.',
            'checklistitems' => json_encode([
                'Es certificado laboral de institución educativa',
                'Indica fechas de vinculación (desde - hasta)',
                'Especifica cargo docente desempeñado',
                'Indica programas o asignaturas dictadas',
                'Menciona carga horaria o intensidad',
                'Incluye firma de autoridad competente',
                'Tiene fecha de expedición',
            ]),
            'externalurl' => '',
            'isrequired' => 0,
            'iserexempted' => 1,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'employment',
            'defaultmaxagedays' => null,
            'sortorder' => 9,
            'enabled' => 1,
        ],
        // 10. Experiencia profesional.
        [
            'code' => 'experiencia_profesional',
            'name' => 'Experiencia Profesional',
            'description' => 'Certificaciones de experiencia laboral profesional',
            'requirements' => 'SOLO certificados laborales. NO son válidos: contratos, actas de finalización, actos administrativos de nombramientos.',
            'checklistitems' => json_encode([
                'Es un certificado laboral (NO contrato, acta de finalización o acto administrativo)',
                'Contiene membrete de la empresa',
                'Indica fechas de vinculación (desde - hasta)',
                'Especifica cargo desempeñado',
                'Describe funciones o responsabilidades',
                'Incluye firma de autoridad competente',
                'Tiene fecha de expedición',
            ]),
            'externalurl' => '',
            'isrequired' => 0,
            'iserexempted' => 1,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'employment',
            'defaultmaxagedays' => null,
            'sortorder' => 10,
            'enabled' => 1,
        ],
        // 11. Formación en pedagogía.
        [
            'code' => 'formacion_pedagogia',
            'name' => 'Formación en Pedagogía',
            'description' => 'Certificados de formación en pedagogía, didáctica o enseñanza',
            'requirements' => 'Certificados de cursos, diplomados o programas en pedagogía, didáctica o metodologías de enseñanza.',
            'checklistitems' => json_encode([
                'Certificado es legible y completo',
                'Tema del curso es relacionado con pedagogía/didáctica',
                'Nombre del participante coincide con postulante',
                'Institución que expide es identificable',
                'Duración o intensidad horaria especificada',
                'Fecha de realización o expedición visible',
            ]),
            'externalurl' => '',
            'isrequired' => 0,
            'iserexempted' => 0,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'academic',
            'defaultmaxagedays' => null,
            'sortorder' => 11,
            'enabled' => 1,
        ],
        // 12. Formación en TIC.
        [
            'code' => 'formacion_tic',
            'name' => 'Formación en TIC',
            'description' => 'Certificados de formación en Tecnologías de la Información y Comunicación',
            'requirements' => 'Certificados de cursos, diplomados o programas en TIC aplicadas a la educación.',
            'checklistitems' => json_encode([
                'Certificado es legible y completo',
                'Tema del curso es relacionado con TIC',
                'Nombre del participante coincide con postulante',
                'Institución que expide es identificable',
                'Duración o intensidad horaria especificada',
                'Fecha de realización o expedición visible',
            ]),
            'externalurl' => '',
            'isrequired' => 0,
            'iserexempted' => 0,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'academic',
            'defaultmaxagedays' => null,
            'sortorder' => 12,
            'enabled' => 1,
        ],
        // 13. RUT actualizado.
        [
            'code' => 'rut',
            'name' => 'RUT Actualizado',
            'description' => 'Registro Único Tributario actualizado',
            'requirements' => 'Verificar fecha en parte inferior derecha del documento, debe estar actualizado.',
            'checklistitems' => json_encode([
                'Documento legible',
                'Número de NIT/RUT visible',
                'Nombre/razón social coincide con datos del postulante',
                'Fecha de actualización visible en parte inferior derecha',
                'Fecha de actualización es reciente',
            ]),
            'externalurl' => 'https://muisca.dian.gov.co/WebRutMuisca/DefInscripcionRutPortal.faces',
            'isrequired' => 1,
            'iserexempted' => 0,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'financial',
            'defaultmaxagedays' => null,
            'sortorder' => 13,
            'enabled' => 1,
        ],
        // 14. Antecedentes disciplinarios.
        [
            'code' => 'antecedentes_disciplinarios',
            'name' => 'Antecedentes Disciplinarios',
            'description' => 'Certificado de la Procuraduría General de la Nación',
            'requirements' => 'Fecha de expedición no mayor a 30 días recomendado.',
            'checklistitems' => json_encode([
                'Es certificado oficial de la Procuraduría General de la Nación',
                'Nombre del consultado coincide con postulante',
                'Número de cédula coincide',
                'Fecha de expedición es reciente (preferible <30 días)',
                'Código de verificación visible (si aplica)',
                'Estado de antecedentes es claro',
            ]),
            'externalurl' => 'https://www.procuraduria.gov.co/portal/Antecedentes.page',
            'isrequired' => 1,
            'iserexempted' => 0,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'legal',
            'defaultmaxagedays' => 30,
            'sortorder' => 14,
            'enabled' => 1,
        ],
        // 15. Antecedentes fiscales.
        [
            'code' => 'antecedentes_fiscales',
            'name' => 'Antecedentes Fiscales',
            'description' => 'Certificado de la Contraloría General de la República',
            'requirements' => 'Fecha de expedición no mayor a 30 días recomendado.',
            'checklistitems' => json_encode([
                'Es certificado oficial de la Contraloría General de la República',
                'Nombre del consultado coincide con postulante',
                'Número de cédula coincide',
                'Fecha de expedición es reciente (preferible <30 días)',
                'Código de verificación visible (si aplica)',
                'Estado de antecedentes es claro',
            ]),
            'externalurl' => 'https://www.contraloria.gov.co/web/certificados',
            'isrequired' => 1,
            'iserexempted' => 0,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'legal',
            'defaultmaxagedays' => 30,
            'sortorder' => 15,
            'enabled' => 1,
        ],
        // 16. Antecedentes judiciales.
        [
            'code' => 'antecedentes_judiciales',
            'name' => 'Antecedentes Judiciales',
            'description' => 'Certificado de la Policía Nacional',
            'requirements' => 'Fecha de expedición no mayor a 30 días recomendado.',
            'checklistitems' => json_encode([
                'Es certificado oficial de Policía Nacional',
                'Descargado desde portal oficial',
                'Nombre del consultado coincide',
                'Número de cédula coincide',
                'Fecha de expedición es reciente (<30 días)',
                'Tiene código de verificación',
            ]),
            'externalurl' => 'https://antecedentes.policia.gov.co:7005/WebJudicial',
            'isrequired' => 1,
            'iserexempted' => 0,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'legal',
            'defaultmaxagedays' => 30,
            'sortorder' => 16,
            'enabled' => 1,
        ],
        // 17. Registro Nacional de Medidas Correctivas.
        [
            'code' => 'medidas_correctivas',
            'name' => 'Registro Nacional de Medidas Correctivas',
            'description' => 'Certificado de medidas correctivas de la Policía Nacional',
            'requirements' => 'Descargado del portal oficial.',
            'checklistitems' => json_encode([
                'Es certificado oficial de Policía Nacional',
                'Descargado desde portal oficial',
                'Nombre del consultado coincide con postulante',
                'Número de cédula coincide',
                'Fecha de expedición es reciente',
                'Estado es claro',
            ]),
            'externalurl' => 'https://srvcnpc.policia.gov.co/PSC/frm_cnp_consulta.aspx',
            'isrequired' => 1,
            'iserexempted' => 0,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'legal',
            'defaultmaxagedays' => 30,
            'sortorder' => 17,
            'enabled' => 1,
        ],
        // 18. Consulta de Inhabilidades.
        [
            'code' => 'inhabilidades',
            'name' => 'Consulta de Inhabilidades',
            'description' => 'Consulta de inhabilidades por delitos sexuales contra menores (Ley 1918 de 2018)',
            'requirements' => 'Certificado descargado del portal oficial.',
            'checklistitems' => json_encode([
                'Es certificado oficial del Sistema de Información de Inhabilidades',
                'Descargado desde portal oficial',
                'Nombre del consultado coincide con postulante',
                'Número de cédula coincide',
                'Fecha de expedición es reciente',
                'Resultado de consulta es claro',
            ]),
            'externalurl' => 'https://inhabilidades.policia.gov.co:8080/',
            'isrequired' => 1,
            'iserexempted' => 0,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'legal',
            'defaultmaxagedays' => 30,
            'sortorder' => 18,
            'enabled' => 1,
        ],
        // 19. REDAM.
        [
            'code' => 'redam',
            'name' => 'REDAM',
            'description' => 'Registro de Deudores Alimentarios Morosos',
            'requirements' => 'Registrarse en el portal y descargar certificado.',
            'checklistitems' => json_encode([
                'Es certificado oficial de REDAM',
                'Descargado desde Carpeta Ciudadana',
                'Nombre del consultado coincide con postulante',
                'Número de cédula coincide',
                'Fecha de expedición es reciente',
                'Estado de registro es claro',
            ]),
            'externalurl' => 'https://carpetaciudadana.and.gov.co/inicio-de-sesion',
            'isrequired' => 1,
            'iserexempted' => 0,
            'gender_condition' => null,
            'profession_exempt' => null,
            'input_type' => 'file',
            'category' => 'legal',
            'defaultmaxagedays' => 30,
            'sortorder' => 19,
            'enabled' => 1,
        ],
    ];
}

/**
 * Get default email templates for installation.
 *
 * @return array Array of email template definitions.
 */
function local_jobboard_get_default_email_templates(): array {
    return [
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

    // All available tour files - ordered by user journey.
    $tourfiles = [
        // Main entry points.
        'tour_dashboard.json',
        'tour_public.json',
        // Convocatorias/Calls management.
        'tour_convocatorias.json',
        'tour_convocatoria_manage.json',
        // Vacancies.
        'tour_vacancies.json',
        'tour_vacancy.json',
        'tour_manage.json',
        // Application process.
        'tour_apply.json',
        'tour_application.json',
        'tour_myapplications.json',
        // Document management.
        'tour_documents.json',
        'tour_review.json',
        'tour_myreviews.json',
        'tour_validate_document.json',
        // Reports.
        'tour_reports.json',
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

/**
 * Create custom roles for the Job Board plugin.
 *
 * Creates three specialized roles:
 * - jobboard_reviewer: Can review and validate applicant documents
 * - jobboard_coordinator: Can manage vacancies and coordinate selection
 * - jobboard_committee: Can evaluate candidates in selection process
 *
 * @return void
 */
function local_jobboard_create_roles(): void {
    global $DB;

    // Ensure capabilities are loaded before trying to assign them.
    // During installation, access.php capabilities may not be in the DB yet.
    update_capabilities('local_jobboard');

    $systemcontext = context_system::instance();

    // Role: Document Reviewer.
    $reviewerrole = $DB->get_record('role', ['shortname' => 'jobboard_reviewer']);
    if (!$reviewerrole) {
        $reviewerroleid = create_role(
            get_string('role_reviewer', 'local_jobboard'),
            'jobboard_reviewer',
            get_string('role_reviewer_desc', 'local_jobboard'),
            'teacher'
        );

        // Assign capabilities for reviewer role.
        $reviewercaps = [
            'local/jobboard:view',
            'local/jobboard:viewinternal',
            'local/jobboard:review',
            'local/jobboard:validatedocuments',
            'local/jobboard:reviewdocuments',
            'local/jobboard:downloadanydocument',
        ];

        foreach ($reviewercaps as $cap) {
            assign_capability($cap, CAP_ALLOW, $reviewerroleid, $systemcontext->id);
        }

        // Set contexts where this role can be assigned.
        set_role_contextlevels($reviewerroleid, [CONTEXT_SYSTEM]);
    }

    // Role: Selection Coordinator.
    $coordinatorrole = $DB->get_record('role', ['shortname' => 'jobboard_coordinator']);
    if (!$coordinatorrole) {
        $coordinatorroleid = create_role(
            get_string('role_coordinator', 'local_jobboard'),
            'jobboard_coordinator',
            get_string('role_coordinator_desc', 'local_jobboard'),
            'editingteacher'
        );

        // Assign capabilities for coordinator role.
        $coordinatorcaps = [
            'local/jobboard:view',
            'local/jobboard:viewinternal',
            'local/jobboard:manage',
            'local/jobboard:createvacancy',
            'local/jobboard:editvacancy',
            'local/jobboard:publishvacancy',
            'local/jobboard:viewallvacancies',
            'local/jobboard:viewallapplications',
            'local/jobboard:changeapplicationstatus',
            'local/jobboard:assignreviewers',
            'local/jobboard:viewreports',
            'local/jobboard:viewevaluations',
            'local/jobboard:manageworkflow',
        ];

        foreach ($coordinatorcaps as $cap) {
            assign_capability($cap, CAP_ALLOW, $coordinatorroleid, $systemcontext->id);
        }

        set_role_contextlevels($coordinatorroleid, [CONTEXT_SYSTEM]);
    }

    // Role: Selection Committee Member.
    $committeerole = $DB->get_record('role', ['shortname' => 'jobboard_committee']);
    if (!$committeerole) {
        $committeeroleid = create_role(
            get_string('role_committee', 'local_jobboard'),
            'jobboard_committee',
            get_string('role_committee_desc', 'local_jobboard'),
            'teacher'
        );

        // Assign capabilities for committee role.
        $committeecaps = [
            'local/jobboard:view',
            'local/jobboard:viewinternal',
            'local/jobboard:evaluate',
            'local/jobboard:viewevaluations',
            'local/jobboard:downloadanydocument',
        ];

        foreach ($committeecaps as $cap) {
            assign_capability($cap, CAP_ALLOW, $committeeroleid, $systemcontext->id);
        }

        set_role_contextlevels($committeeroleid, [CONTEXT_SYSTEM]);
    }
}

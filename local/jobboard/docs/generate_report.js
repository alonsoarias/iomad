/**
 * Generador de Informe Técnico - Plugin local_jobboard
 *
 * Este script genera un documento Word (.docx) con el informe técnico
 * del plugin local_jobboard para ISER.
 *
 * Uso: npm run generate
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @license   GPL-3.0
 */

'use strict';

const officegen = require('officegen');
const fs = require('fs');
const path = require('path');

// =============================================================================
// CONFIGURACIÓN DE ESTILOS ISER
// =============================================================================

const ISER_COLORS = {
    green: 'FF1B9E88',      // Verde ISER
    yellow: 'FFFCBD05',     // Amarillo ISER
    red: 'FFEB4335',        // Rojo ISER
    gray: 'FF646363',       // Gris ISER
    black: 'FF000000',
    white: 'FFFFFFFF'
};

const STYLES = {
    title: {
        font_face: 'Arial',
        font_size: 24,
        bold: true,
        color: ISER_COLORS.red
    },
    heading1: {
        font_face: 'Arial',
        font_size: 16,
        bold: true,
        color: ISER_COLORS.red
    },
    heading2: {
        font_face: 'Arial',
        font_size: 14,
        bold: true,
        color: ISER_COLORS.green
    },
    heading3: {
        font_face: 'Arial',
        font_size: 12,
        bold: true,
        color: ISER_COLORS.gray
    },
    normal: {
        font_face: 'Arial',
        font_size: 12,
        color: ISER_COLORS.gray
    },
    tableHeader: {
        font_face: 'Arial',
        font_size: 10,
        bold: true,
        color: ISER_COLORS.white
    },
    tableCell: {
        font_face: 'Arial',
        font_size: 10,
        color: ISER_COLORS.gray
    },
    code: {
        font_face: 'Consolas',
        font_size: 10,
        color: ISER_COLORS.gray
    }
};

// =============================================================================
// FUNCIONES AUXILIARES
// =============================================================================

/**
 * Lee y parsea el archivo technical_data.json
 * @returns {Object} Datos técnicos del plugin
 */
function loadTechnicalData() {
    const dataPath = path.join(__dirname, 'technical_data.json');

    if (!fs.existsSync(dataPath)) {
        console.error('Error: No se encontró technical_data.json');
        console.error('Ejecute primero el análisis del plugin.');
        process.exit(1);
    }

    const rawData = fs.readFileSync(dataPath, 'utf8');
    return JSON.parse(rawData);
}

/**
 * Formatea una fecha para mostrar en el documento
 * @param {string} dateStr - Fecha en formato ISO
 * @returns {string} Fecha formateada
 */
function formatDate(dateStr) {
    const date = new Date(dateStr);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('es-CO', options);
}

/**
 * Agrega un párrafo vacío (salto de línea)
 * @param {Object} docx - Documento officegen
 * @param {number} count - Número de líneas vacías
 */
function addEmptyLines(docx, count = 1) {
    for (let i = 0; i < count; i++) {
        docx.createP();
    }
}

/**
 * Agrega un salto de página
 * @param {Object} docx - Documento officegen
 */
function addPageBreak(docx) {
    const p = docx.createP();
    p.addLineBreak();
    p.options = { ...p.options, pageBreak: true };
}

// =============================================================================
// GENERADORES DE SECCIONES
// =============================================================================

/**
 * Genera la portada del documento
 * @param {Object} docx - Documento officegen
 * @param {Object} data - Datos técnicos
 */
function generateCoverPage(docx, data) {
    addEmptyLines(docx, 3);

    // Logo placeholder
    let p = docx.createP({ align: 'center' });
    p.addText('[LOGO ISER]', {
        font_face: 'Arial',
        font_size: 14,
        color: ISER_COLORS.gray
    });

    addEmptyLines(docx, 2);

    // Institución
    p = docx.createP({ align: 'center' });
    p.addText('INSTITUTO SUPERIOR DE EDUCACIÓN RURAL', {
        font_face: 'Arial',
        font_size: 14,
        bold: true,
        color: ISER_COLORS.green
    });

    p = docx.createP({ align: 'center' });
    p.addText('ISER', {
        font_face: 'Arial',
        font_size: 12,
        color: ISER_COLORS.gray
    });

    addEmptyLines(docx, 4);

    // Título principal
    p = docx.createP({ align: 'center' });
    p.addText('INFORME TÉCNICO', STYLES.title);

    addEmptyLines(docx, 1);

    // Subtítulo
    p = docx.createP({ align: 'center' });
    p.addText('Plugin local_jobboard', {
        font_face: 'Arial',
        font_size: 18,
        bold: true,
        color: ISER_COLORS.gray
    });

    p = docx.createP({ align: 'center' });
    p.addText('Sistema de Gestión de Vacantes Docentes', {
        font_face: 'Arial',
        font_size: 14,
        color: ISER_COLORS.gray
    });

    addEmptyLines(docx, 3);

    // Versión
    p = docx.createP({ align: 'center' });
    p.addText(`Versión ${data.plugin_info.release}`, {
        font_face: 'Arial',
        font_size: 12,
        color: ISER_COLORS.gray
    });

    p = docx.createP({ align: 'center' });
    p.addText(`Build ${data.plugin_info.version}`, {
        font_face: 'Arial',
        font_size: 10,
        color: ISER_COLORS.gray
    });

    addEmptyLines(docx, 6);

    // Autor
    p = docx.createP({ align: 'center' });
    p.addText('Elaborado por:', {
        font_face: 'Arial',
        font_size: 10,
        color: ISER_COLORS.gray
    });

    p = docx.createP({ align: 'center' });
    p.addText(data.plugin_info.author.name, {
        font_face: 'Arial',
        font_size: 12,
        bold: true,
        color: ISER_COLORS.gray
    });

    p = docx.createP({ align: 'center' });
    p.addText(data.plugin_info.author.email, {
        font_face: 'Arial',
        font_size: 10,
        color: ISER_COLORS.green
    });

    addEmptyLines(docx, 2);

    // Fecha
    p = docx.createP({ align: 'center' });
    p.addText(formatDate(data.metadata.generated_at), {
        font_face: 'Arial',
        font_size: 10,
        color: ISER_COLORS.gray
    });

    addPageBreak(docx);
}

/**
 * Genera la tabla de contenido
 * @param {Object} docx - Documento officegen
 */
function generateTableOfContents(docx) {
    let p = docx.createP();
    p.addText('TABLA DE CONTENIDO', STYLES.heading1);

    addEmptyLines(docx, 1);

    const toc = [
        { num: '1', title: 'Información General del Plugin', page: '3' },
        { num: '2', title: 'Arquitectura del Sistema', page: '4' },
        { num: '2.1', title: 'Estructura de Directorios', page: '4', indent: true },
        { num: '2.2', title: 'Componentes Principales', page: '5', indent: true },
        { num: '3', title: 'Modelo de Datos', page: '6' },
        { num: '3.1', title: 'Diagrama Entidad-Relación', page: '6', indent: true },
        { num: '3.2', title: 'Descripción de Tablas', page: '7', indent: true },
        { num: '4', title: 'Clases Principales', page: '10' },
        { num: '4.1', title: 'Clase vacancy', page: '10', indent: true },
        { num: '4.2', title: 'Clase application', page: '11', indent: true },
        { num: '4.3', title: 'Clase document', page: '12', indent: true },
        { num: '4.4', title: 'Clase audit', page: '13', indent: true },
        { num: '5', title: 'Flujos de Trabajo', page: '14' },
        { num: '5.1', title: 'Flujo de Vacantes', page: '14', indent: true },
        { num: '5.2', title: 'Flujo de Postulación', page: '15', indent: true },
        { num: '5.3', title: 'Flujo de Validación de Documentos', page: '16', indent: true },
        { num: '6', title: 'Capabilities y Permisos', page: '17' },
        { num: '7', title: 'Servicios Web (API)', page: '19' },
        { num: '8', title: 'Eventos del Sistema', page: '20' },
        { num: '9', title: 'Tareas Programadas', page: '21' },
        { num: '10', title: 'Funciones de lib.php', page: '22' },
        { num: 'A', title: 'Anexo: Diagramas SVG', page: '24' }
    ];

    toc.forEach(item => {
        p = docx.createP();
        const indent = item.indent ? '      ' : '';
        p.addText(`${indent}${item.num}. ${item.title}`, {
            font_face: 'Arial',
            font_size: 11,
            color: ISER_COLORS.gray
        });
        p.addText(` ${'.' .repeat(60 - item.title.length - item.num.length)} `, {
            font_face: 'Arial',
            font_size: 11,
            color: ISER_COLORS.gray
        });
        p.addText(item.page, {
            font_face: 'Arial',
            font_size: 11,
            color: ISER_COLORS.gray
        });
    });

    addPageBreak(docx);
}

/**
 * Genera la sección de información general
 * @param {Object} docx - Documento officegen
 * @param {Object} data - Datos técnicos
 */
function generatePluginInfo(docx, data) {
    let p = docx.createP();
    p.addText('1. INFORMACIÓN GENERAL DEL PLUGIN', STYLES.heading1);

    addEmptyLines(docx, 1);

    const info = data.plugin_info;

    // Tabla de información básica
    const infoItems = [
        ['Componente', info.component],
        ['Nombre', info.name],
        ['Versión', `${info.release} (${info.version})`],
        ['Madurez', info.maturity],
        ['Moodle mínimo', info.moodle_minimum],
        ['Versiones soportadas', info.supported_versions.join(', ')],
        ['Licencia', info.license],
        ['Copyright', info.copyright]
    ];

    infoItems.forEach(([label, value]) => {
        p = docx.createP();
        p.addText(`${label}: `, {
            font_face: 'Arial',
            font_size: 12,
            bold: true,
            color: ISER_COLORS.gray
        });
        p.addText(value, STYLES.normal);
    });

    addEmptyLines(docx, 1);

    // Descripción
    p = docx.createP();
    p.addText('Descripción:', STYLES.heading3);

    p = docx.createP();
    p.addText(info.description, STYLES.normal);

    addEmptyLines(docx, 1);

    // Estadísticas
    p = docx.createP();
    p.addText('Estadísticas del Plugin:', STYLES.heading3);

    const stats = data.metadata;
    const statsItems = [
        ['Tablas de base de datos', stats.tables_count],
        ['Capabilities', stats.capabilities_count],
        ['Funciones de servicio web', stats.webservice_functions_count],
        ['Eventos', stats.events_count],
        ['Tareas programadas', stats.scheduled_tasks_count],
        ['Módulos AMD (JavaScript)', stats.amd_modules_count],
        ['Clases documentadas', stats.core_classes_documented],
        ['Funciones lib.php documentadas', stats.lib_functions_documented],
        ['Flujos de trabajo documentados', stats.workflows_documented]
    ];

    statsItems.forEach(([label, value]) => {
        p = docx.createP();
        p.addText(`  • ${label}: `, STYLES.normal);
        p.addText(String(value), {
            font_face: 'Arial',
            font_size: 12,
            bold: true,
            color: ISER_COLORS.green
        });
    });

    addPageBreak(docx);
}

/**
 * Genera la sección de arquitectura
 * @param {Object} docx - Documento officegen
 * @param {Object} data - Datos técnicos
 */
function generateArchitectureSection(docx, data) {
    let p = docx.createP();
    p.addText('2. ARQUITECTURA DEL SISTEMA', STYLES.heading1);

    addEmptyLines(docx, 1);

    // 2.1 Estructura de directorios
    p = docx.createP();
    p.addText('2.1 Estructura de Directorios', STYLES.heading2);

    addEmptyLines(docx, 1);

    p = docx.createP();
    p.addText('[Insertar diagrama: diagrama_arquitectura.svg]', {
        font_face: 'Arial',
        font_size: 11,
        italic: true,
        color: ISER_COLORS.yellow
    });

    addEmptyLines(docx, 1);

    // Archivos raíz
    p = docx.createP();
    p.addText('Archivos en directorio raíz:', STYLES.heading3);

    const rootFiles = data.directory_structure.root_files;
    rootFiles.forEach(file => {
        p = docx.createP();
        p.addText(`  • ${file}`, STYLES.code);
    });

    addEmptyLines(docx, 1);

    // Directorios principales
    p = docx.createP();
    p.addText('Directorios principales:', STYLES.heading3);

    const dirs = data.directory_structure.directories;
    Object.entries(dirs).forEach(([name, info]) => {
        p = docx.createP();
        p.addText(`  ${name}/`, {
            font_face: 'Consolas',
            font_size: 11,
            bold: true,
            color: ISER_COLORS.green
        });
        p.addText(` - ${info.description}`, STYLES.normal);
    });

    addEmptyLines(docx, 1);

    // 2.2 Componentes principales
    p = docx.createP();
    p.addText('2.2 Componentes Principales', STYLES.heading2);

    addEmptyLines(docx, 1);

    p = docx.createP();
    p.addText('El plugin está organizado en las siguientes capas:', STYLES.normal);

    const layers = [
        ['Capa de Presentación', 'Archivos PHP de entrada, templates Mustache, módulos AMD'],
        ['Capa de Lógica de Negocio', 'Clases en classes/, helpers, forms'],
        ['Capa de Datos', 'Acceso a base de datos via Moodle DML'],
        ['Capa de Servicios', 'Web services, eventos, tareas programadas']
    ];

    layers.forEach(([layer, desc]) => {
        p = docx.createP();
        p.addText(`  • ${layer}: `, {
            font_face: 'Arial',
            font_size: 12,
            bold: true,
            color: ISER_COLORS.gray
        });
        p.addText(desc, STYLES.normal);
    });

    addPageBreak(docx);
}

/**
 * Genera la sección del modelo de datos
 * @param {Object} docx - Documento officegen
 * @param {Object} data - Datos técnicos
 */
function generateDataModelSection(docx, data) {
    let p = docx.createP();
    p.addText('3. MODELO DE DATOS', STYLES.heading1);

    addEmptyLines(docx, 1);

    // 3.1 Diagrama ER
    p = docx.createP();
    p.addText('3.1 Diagrama Entidad-Relación', STYLES.heading2);

    addEmptyLines(docx, 1);

    p = docx.createP();
    p.addText('[Insertar diagrama: diagrama_er.svg]', {
        font_face: 'Arial',
        font_size: 11,
        italic: true,
        color: ISER_COLORS.yellow
    });

    addEmptyLines(docx, 1);

    // 3.2 Descripción de tablas
    p = docx.createP();
    p.addText('3.2 Descripción de Tablas', STYLES.heading2);

    addEmptyLines(docx, 1);

    p = docx.createP();
    p.addText(`El plugin utiliza ${data.metadata.tables_count} tablas en la base de datos:`, STYLES.normal);

    addEmptyLines(docx, 1);

    // Listar tablas principales
    const tables = data.database_tables || [];
    tables.forEach(table => {
        p = docx.createP();
        p.addText(`${table.name}`, {
            font_face: 'Consolas',
            font_size: 11,
            bold: true,
            color: ISER_COLORS.green
        });

        p = docx.createP();
        p.addText(`  ${table.description}`, STYLES.normal);

        // Campos principales
        if (table.fields && table.fields.length > 0) {
            p = docx.createP();
            p.addText('  Campos principales: ', {
                font_face: 'Arial',
                font_size: 10,
                color: ISER_COLORS.gray
            });

            const fieldNames = table.fields.slice(0, 5).map(f => f.name).join(', ');
            p.addText(fieldNames + (table.fields.length > 5 ? '...' : ''), STYLES.code);
        }

        addEmptyLines(docx, 1);
    });

    addPageBreak(docx);
}

/**
 * Genera la sección de clases principales
 * @param {Object} docx - Documento officegen
 * @param {Object} data - Datos técnicos
 */
function generateClassesSection(docx, data) {
    let p = docx.createP();
    p.addText('4. CLASES PRINCIPALES', STYLES.heading1);

    addEmptyLines(docx, 1);

    const classes = data.classes || {};
    let sectionNum = 1;

    Object.entries(classes).forEach(([className, classInfo]) => {
        p = docx.createP();
        p.addText(`4.${sectionNum} Clase ${className}`, STYLES.heading2);

        addEmptyLines(docx, 1);

        // Información de la clase
        p = docx.createP();
        p.addText(`Namespace: `, STYLES.heading3);
        p.addText(classInfo.namespace, STYLES.code);

        p = docx.createP();
        p.addText(`Archivo: `, STYLES.heading3);
        p.addText(classInfo.file, STYLES.code);

        addEmptyLines(docx, 1);

        p = docx.createP();
        p.addText(classInfo.description, STYLES.normal);

        addEmptyLines(docx, 1);

        // Métodos
        if (classInfo.methods && classInfo.methods.length > 0) {
            p = docx.createP();
            p.addText('Métodos públicos:', STYLES.heading3);

            classInfo.methods.forEach(method => {
                p = docx.createP();
                p.addText(`  ${method.name}()`, {
                    font_face: 'Consolas',
                    font_size: 10,
                    bold: true,
                    color: ISER_COLORS.green
                });
                p.addText(` - ${method.description}`, {
                    font_face: 'Arial',
                    font_size: 10,
                    color: ISER_COLORS.gray
                });
            });
        }

        addEmptyLines(docx, 1);
        sectionNum++;
    });

    addPageBreak(docx);
}

/**
 * Genera la sección de flujos de trabajo
 * @param {Object} docx - Documento officegen
 * @param {Object} data - Datos técnicos
 */
function generateWorkflowsSection(docx, data) {
    let p = docx.createP();
    p.addText('5. FLUJOS DE TRABAJO', STYLES.heading1);

    addEmptyLines(docx, 1);

    // 5.1 Flujo de vacantes
    p = docx.createP();
    p.addText('5.1 Flujo de Vacantes', STYLES.heading2);

    addEmptyLines(docx, 1);

    p = docx.createP();
    p.addText('[Insertar diagrama: diagrama_flujo_vacante.svg]', {
        font_face: 'Arial',
        font_size: 11,
        italic: true,
        color: ISER_COLORS.yellow
    });

    addEmptyLines(docx, 1);

    const vacancyWorkflow = data.workflows?.vacancy_workflow;
    if (vacancyWorkflow) {
        p = docx.createP();
        p.addText(vacancyWorkflow.description, STYLES.normal);

        addEmptyLines(docx, 1);

        p = docx.createP();
        p.addText('Estados: ', STYLES.heading3);
        p.addText(vacancyWorkflow.states.join(' → '), STYLES.code);
    }

    addEmptyLines(docx, 2);

    // 5.2 Flujo de postulación
    p = docx.createP();
    p.addText('5.2 Flujo de Postulación', STYLES.heading2);

    addEmptyLines(docx, 1);

    p = docx.createP();
    p.addText('[Insertar diagrama: diagrama_flujo_postulacion.svg]', {
        font_face: 'Arial',
        font_size: 11,
        italic: true,
        color: ISER_COLORS.yellow
    });

    addEmptyLines(docx, 1);

    const appWorkflow = data.workflows?.application_workflow;
    if (appWorkflow) {
        p = docx.createP();
        p.addText(appWorkflow.description, STYLES.normal);

        addEmptyLines(docx, 1);

        p = docx.createP();
        p.addText('Estados:', STYLES.heading3);

        if (appWorkflow.states) {
            Object.entries(appWorkflow.states).forEach(([state, desc]) => {
                p = docx.createP();
                p.addText(`  • ${state}: `, {
                    font_face: 'Consolas',
                    font_size: 10,
                    bold: true,
                    color: ISER_COLORS.green
                });
                p.addText(desc, STYLES.normal);
            });
        }
    }

    addPageBreak(docx);
}

/**
 * Genera la sección de capabilities
 * @param {Object} docx - Documento officegen
 * @param {Object} data - Datos técnicos
 */
function generateCapabilitiesSection(docx, data) {
    let p = docx.createP();
    p.addText('6. CAPABILITIES Y PERMISOS', STYLES.heading1);

    addEmptyLines(docx, 1);

    p = docx.createP();
    p.addText(`El plugin define ${data.metadata.capabilities_count} capabilities para control de acceso:`, STYLES.normal);

    addEmptyLines(docx, 1);

    const capabilities = data.capabilities || [];
    let currentCategory = '';

    capabilities.forEach(cap => {
        if (cap.category !== currentCategory) {
            currentCategory = cap.category;
            addEmptyLines(docx, 1);
            p = docx.createP();
            p.addText(currentCategory, STYLES.heading3);
        }

        p = docx.createP();
        p.addText(`  ${cap.name}`, {
            font_face: 'Consolas',
            font_size: 10,
            color: ISER_COLORS.green
        });

        p = docx.createP();
        p.addText(`    ${cap.description}`, {
            font_face: 'Arial',
            font_size: 10,
            color: ISER_COLORS.gray
        });

        if (cap.archetypes && cap.archetypes.length > 0) {
            p = docx.createP();
            p.addText(`    Roles: ${cap.archetypes.join(', ')}`, {
                font_face: 'Arial',
                font_size: 9,
                italic: true,
                color: ISER_COLORS.gray
            });
        }
    });

    addPageBreak(docx);
}

/**
 * Genera la sección de servicios web
 * @param {Object} docx - Documento officegen
 * @param {Object} data - Datos técnicos
 */
function generateWebServicesSection(docx, data) {
    let p = docx.createP();
    p.addText('7. SERVICIOS WEB (API)', STYLES.heading1);

    addEmptyLines(docx, 1);

    const functions = data.webservices?.functions || [];

    p = docx.createP();
    p.addText(`El plugin expone ${functions.length} funciones de servicio web:`, STYLES.normal);

    addEmptyLines(docx, 1);

    functions.forEach(func => {
        p = docx.createP();
        p.addText(func.name, {
            font_face: 'Consolas',
            font_size: 11,
            bold: true,
            color: ISER_COLORS.green
        });

        p = docx.createP();
        p.addText(`  ${func.description}`, STYLES.normal);

        p = docx.createP();
        p.addText(`  Tipo: ${func.type} | AJAX: ${func.ajax ? 'Sí' : 'No'} | Capability: ${func.capabilities}`, {
            font_face: 'Arial',
            font_size: 9,
            color: ISER_COLORS.gray
        });

        addEmptyLines(docx, 1);
    });

    addPageBreak(docx);
}

/**
 * Genera la sección de eventos
 * @param {Object} docx - Documento officegen
 * @param {Object} data - Datos técnicos
 */
function generateEventsSection(docx, data) {
    let p = docx.createP();
    p.addText('8. EVENTOS DEL SISTEMA', STYLES.heading1);

    addEmptyLines(docx, 1);

    const events = data.events || [];

    p = docx.createP();
    p.addText(`El plugin dispara ${events.length} tipos de eventos:`, STYLES.normal);

    addEmptyLines(docx, 1);

    events.forEach(event => {
        p = docx.createP();
        p.addText(`  • local_jobboard\\event\\${event}`, STYLES.code);
    });

    addEmptyLines(docx, 2);

    p = docx.createP();
    p.addText('Estos eventos permiten:', STYLES.heading3);

    const uses = [
        'Integración con sistemas externos',
        'Logging y auditoría',
        'Triggers para notificaciones',
        'Extensibilidad del plugin'
    ];

    uses.forEach(use => {
        p = docx.createP();
        p.addText(`  • ${use}`, STYLES.normal);
    });

    addPageBreak(docx);
}

/**
 * Genera la sección de tareas programadas
 * @param {Object} docx - Documento officegen
 * @param {Object} data - Datos técnicos
 */
function generateScheduledTasksSection(docx, data) {
    let p = docx.createP();
    p.addText('9. TAREAS PROGRAMADAS', STYLES.heading1);

    addEmptyLines(docx, 1);

    const tasks = data.scheduled_tasks || [];

    p = docx.createP();
    p.addText(`El plugin registra ${tasks.length} tareas programadas:`, STYLES.normal);

    addEmptyLines(docx, 1);

    tasks.forEach(task => {
        const taskInfo = typeof task === 'string' ? { name: task } : task;

        p = docx.createP();
        p.addText(`\\local_jobboard\\task\\${taskInfo.name}`, {
            font_face: 'Consolas',
            font_size: 11,
            bold: true,
            color: ISER_COLORS.green
        });

        if (taskInfo.description) {
            p = docx.createP();
            p.addText(`  ${taskInfo.description}`, STYLES.normal);
        }

        addEmptyLines(docx, 1);
    });

    addPageBreak(docx);
}

/**
 * Genera la sección de funciones de lib.php
 * @param {Object} docx - Documento officegen
 * @param {Object} data - Datos técnicos
 */
function generateLibFunctionsSection(docx, data) {
    let p = docx.createP();
    p.addText('10. FUNCIONES DE lib.php', STYLES.heading1);

    addEmptyLines(docx, 1);

    const libFunctions = data.lib_functions || {};

    Object.entries(libFunctions).forEach(([category, functions]) => {
        p = docx.createP();
        p.addText(category.charAt(0).toUpperCase() + category.slice(1).replace(/_/g, ' '), STYLES.heading2);

        addEmptyLines(docx, 1);

        functions.forEach(func => {
            p = docx.createP();
            p.addText(`${func.name}()`, {
                font_face: 'Consolas',
                font_size: 10,
                bold: true,
                color: ISER_COLORS.green
            });

            p = docx.createP();
            p.addText(`  ${func.description}`, {
                font_face: 'Arial',
                font_size: 10,
                color: ISER_COLORS.gray
            });

            if (func.returns) {
                p = docx.createP();
                p.addText(`  Retorna: ${func.returns}`, {
                    font_face: 'Arial',
                    font_size: 9,
                    italic: true,
                    color: ISER_COLORS.gray
                });
            }
        });

        addEmptyLines(docx, 1);
    });

    addPageBreak(docx);
}

/**
 * Genera el anexo de diagramas
 * @param {Object} docx - Documento officegen
 */
function generateAnnexSection(docx) {
    let p = docx.createP();
    p.addText('ANEXO A: DIAGRAMAS SVG', STYLES.heading1);

    addEmptyLines(docx, 1);

    p = docx.createP();
    p.addText('Los siguientes diagramas están disponibles en formato SVG en el directorio docs/:', STYLES.normal);

    addEmptyLines(docx, 1);

    const diagrams = [
        {
            file: 'diagrama_arquitectura.svg',
            title: 'Arquitectura del Plugin',
            description: 'Estructura de directorios, clases core, capas del sistema'
        },
        {
            file: 'diagrama_er.svg',
            title: 'Modelo Entidad-Relación',
            description: 'Diagrama de las 24 tablas de base de datos con relaciones'
        },
        {
            file: 'diagrama_flujo_vacante.svg',
            title: 'Flujo de Estados de Vacante',
            description: 'Estados draft → published → closed → assigned'
        },
        {
            file: 'diagrama_flujo_postulacion.svg',
            title: 'Flujo de Postulación y Evaluación',
            description: 'Swimlane con roles: Postulante, Revisor, Decano, RR.HH.'
        }
    ];

    diagrams.forEach(diagram => {
        p = docx.createP();
        p.addText(`${diagram.file}`, {
            font_face: 'Consolas',
            font_size: 11,
            bold: true,
            color: ISER_COLORS.green
        });

        p = docx.createP();
        p.addText(`  ${diagram.title}`, STYLES.heading3);

        p = docx.createP();
        p.addText(`  ${diagram.description}`, STYLES.normal);

        addEmptyLines(docx, 1);
    });

    addEmptyLines(docx, 2);

    p = docx.createP();
    p.addText('Nota: Los diagramas SVG pueden abrirse directamente en navegadores web o insertarse en este documento.', {
        font_face: 'Arial',
        font_size: 10,
        italic: true,
        color: ISER_COLORS.gray
    });
}

// =============================================================================
// FUNCIÓN PRINCIPAL
// =============================================================================

/**
 * Genera el documento Word completo
 */
function generateReport() {
    console.log('='.repeat(60));
    console.log('  Generador de Informe Técnico - local_jobboard');
    console.log('='.repeat(60));
    console.log('');

    // Cargar datos
    console.log('[1/3] Cargando technical_data.json...');
    const data = loadTechnicalData();
    console.log(`      Plugin: ${data.plugin_info.name} v${data.plugin_info.release}`);

    // Crear documento
    console.log('[2/3] Generando documento Word...');

    const docx = officegen({
        type: 'docx',
        orientation: 'portrait',
        pageMargins: {
            top: 1440,      // 1 pulgada = 1440 twips
            right: 1440,
            bottom: 1440,
            left: 1440
        }
    });

    // Metadatos del documento
    docx.setDocSubject('Informe Técnico del Plugin local_jobboard');
    docx.setDocKeywords('jobboard, moodle, iomad, iser, vacantes, docentes');
    docx.setDescription('Documentación técnica del sistema de gestión de vacantes docentes');

    // Generar secciones
    console.log('      - Generando portada...');
    generateCoverPage(docx, data);

    console.log('      - Generando tabla de contenido...');
    generateTableOfContents(docx);

    console.log('      - Generando información del plugin...');
    generatePluginInfo(docx, data);

    console.log('      - Generando arquitectura...');
    generateArchitectureSection(docx, data);

    console.log('      - Generando modelo de datos...');
    generateDataModelSection(docx, data);

    console.log('      - Generando clases principales...');
    generateClassesSection(docx, data);

    console.log('      - Generando flujos de trabajo...');
    generateWorkflowsSection(docx, data);

    console.log('      - Generando capabilities...');
    generateCapabilitiesSection(docx, data);

    console.log('      - Generando servicios web...');
    generateWebServicesSection(docx, data);

    console.log('      - Generando eventos...');
    generateEventsSection(docx, data);

    console.log('      - Generando tareas programadas...');
    generateScheduledTasksSection(docx, data);

    console.log('      - Generando funciones lib.php...');
    generateLibFunctionsSection(docx, data);

    console.log('      - Generando anexo de diagramas...');
    generateAnnexSection(docx);

    // Guardar documento
    console.log('[3/3] Guardando documento...');

    const outputPath = path.join(__dirname, 'informe_tecnico_jobboard.docx');
    const out = fs.createWriteStream(outputPath);

    out.on('error', (err) => {
        console.error('Error al guardar el documento:', err);
        process.exit(1);
    });

    out.on('close', () => {
        console.log('');
        console.log('='.repeat(60));
        console.log('  Documento generado exitosamente!');
        console.log('='.repeat(60));
        console.log('');
        console.log(`  Archivo: ${outputPath}`);
        console.log('');
        console.log('  Notas:');
        console.log('  - Insertar manualmente los diagramas SVG');
        console.log('  - Revisar numeración de páginas');
        console.log('  - Actualizar tabla de contenido en Word');
        console.log('');
    });

    docx.generate(out);
}

// Ejecutar
generateReport();

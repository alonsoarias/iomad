/**
 * Generador de Informe Técnico - Plugins Moodle ISER
 *
 * Genera documento Word (.docx) con especificaciones ICONTEC
 *
 * Uso:
 *   node generate_report.js                    - Genera documento completo
 *   node generate_report.js --section=portada  - Solo portada
 *   node generate_report.js --section=jobboard - Solo sección jobboard
 */

import {
    Document,
    Packer,
    Paragraph,
    TextRun,
    PageBreak,
    Header,
    Footer,
    PageNumber,
    NumberFormat,
    AlignmentType,
    HeadingLevel
} from 'docx';
import { writeFile, mkdir } from 'fs/promises';
import { existsSync } from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Importar modulos
import { COLORS, MARGINS, DOCUMENT_STYLES } from './src/styles.js';
import generarPortada from './src/portada.js';
import generarTablaContenido from './src/tabla_contenido.js';
import generarTablaIlustraciones from './src/tabla_ilustraciones.js';
import generarTablaTablas from './src/tabla_tablas.js';
import generarSeccionesIntroductorias from './src/seccion_introduccion.js';
import generarSeccionesJobboard from './src/seccion_jobboard.js';
import generarSeccionesPlatformUsage from './src/seccion_platform_usage.js';
import generarSeccionesPlatformAccess from './src/seccion_platform_access.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Genera un salto de página
 */
function saltoPagina() {
    return new Paragraph({
        children: [new PageBreak()]
    });
}

/**
 * Genera el documento completo o una sección específica
 */
async function generarDocumento(seccion = null) {
    console.log('🔄 Iniciando generación del informe técnico...');

    const children = [];

    try {
        // Sección: Portada
        if (!seccion || seccion === 'portada' || seccion === 'completo') {
            console.log('  📄 Generando portada...');
            const portada = await generarPortada();
            children.push(...portada);
            if (!seccion || seccion === 'completo') children.push(saltoPagina());
        }

        // Seccion: Tabla de contenido
        if (!seccion || seccion === 'tablas' || seccion === 'completo') {
            console.log('  Generando tabla de contenido...');
            const toc = generarTablaContenido();
            children.push(...toc);
            children.push(saltoPagina());

            console.log('  Generando tabla de ilustraciones...');
            const ilustraciones = generarTablaIlustraciones();
            children.push(...ilustraciones);
            children.push(saltoPagina());

            console.log('  Generando tabla de tablas...');
            const tablas = generarTablaTablas();
            children.push(...tablas);
            if (!seccion || seccion === 'completo') children.push(saltoPagina());
        }

        // Seccion: Introduccion, Objetivos, Metodologia
        if (!seccion || seccion === 'introduccion' || seccion === 'completo') {
            console.log('  Generando secciones introductorias...');
            const intro = await generarSeccionesIntroductorias();
            children.push(...intro);
            if (!seccion || seccion === 'completo') children.push(saltoPagina());
        }

        // Seccion: local_jobboard
        if (!seccion || seccion === 'jobboard' || seccion === 'completo') {
            console.log('  🔧 Generando sección local_jobboard...');
            const jobboard = await generarSeccionesJobboard();
            children.push(...jobboard);
            if (!seccion || seccion === 'completo') children.push(saltoPagina());
        }

        // Sección: report_platform_usage
        if (!seccion || seccion === 'platform_usage' || seccion === 'completo') {
            console.log('  📊 Generando sección report_platform_usage...');
            const platformUsage = await generarSeccionesPlatformUsage();
            children.push(...platformUsage);
            if (!seccion || seccion === 'completo') children.push(saltoPagina());
        }

        // Sección: local_platform_access
        if (!seccion || seccion === 'platform_access' || seccion === 'completo') {
            console.log('  🔐 Generando sección local_platform_access...');
            const platformAccess = await generarSeccionesPlatformAccess();
            children.push(...platformAccess);
        }

    } catch (error) {
        console.error('❌ Error generando sección:', error.message);
        throw error;
    }

    // Crear documento
    console.log('  📝 Creando documento Word...');

    const doc = new Document({
        styles: DOCUMENT_STYLES,
        sections: [{
            properties: {
                page: {
                    margin: {
                        top: MARGINS.TOP,
                        bottom: MARGINS.BOTTOM,
                        left: MARGINS.LEFT,
                        right: MARGINS.RIGHT
                    }
                }
            },
            headers: {
                default: new Header({
                    children: [
                        new Paragraph({
                            children: [
                                new TextRun({
                                    text: 'Informe Técnico - Plugins Moodle ISER',
                                    font: 'Arial',
                                    size: 18,
                                    color: COLORS.GRIS
                                })
                            ],
                            alignment: AlignmentType.RIGHT
                        })
                    ]
                })
            },
            footers: {
                default: new Footer({
                    children: [
                        new Paragraph({
                            children: [
                                new TextRun({
                                    text: 'Página ',
                                    font: 'Arial',
                                    size: 18
                                }),
                                new TextRun({
                                    children: [PageNumber.CURRENT],
                                    font: 'Arial',
                                    size: 18
                                }),
                                new TextRun({
                                    text: ' de ',
                                    font: 'Arial',
                                    size: 18
                                }),
                                new TextRun({
                                    children: [PageNumber.TOTAL_PAGES],
                                    font: 'Arial',
                                    size: 18
                                })
                            ],
                            alignment: AlignmentType.CENTER
                        })
                    ]
                })
            },
            children: children
        }]
    });

    return doc;
}

/**
 * Guarda el documento en el sistema de archivos
 */
async function guardarDocumento(doc, nombreArchivo) {
    const outputDir = path.join(__dirname, 'output');

    // Crear directorio si no existe
    if (!existsSync(outputDir)) {
        await mkdir(outputDir, { recursive: true });
    }

    const outputPath = path.join(outputDir, nombreArchivo);

    console.log('  💾 Guardando documento...');
    const buffer = await Packer.toBuffer(doc);
    await writeFile(outputPath, buffer);

    return outputPath;
}

/**
 * Punto de entrada principal
 */
async function main() {
    const args = process.argv.slice(2);
    let seccion = null;

    // Parsear argumentos
    for (const arg of args) {
        if (arg.startsWith('--section=')) {
            seccion = arg.split('=')[1];
        }
    }

    console.log('═══════════════════════════════════════════════════════════');
    console.log('  GENERADOR DE INFORME TÉCNICO - PLUGINS MOODLE ISER');
    console.log('═══════════════════════════════════════════════════════════');
    console.log('');

    if (seccion) {
        console.log(`📌 Generando sección: ${seccion}`);
    } else {
        console.log('📌 Generando documento completo');
        seccion = 'completo';
    }
    console.log('');

    try {
        const doc = await generarDocumento(seccion);

        const nombreArchivo = seccion === 'completo'
            ? 'INFORME_TECNICO_PLUGINS_ISER_2025.docx'
            : `SECCION_${seccion.toUpperCase()}.docx`;

        const outputPath = await guardarDocumento(doc, nombreArchivo);

        console.log('');
        console.log('═══════════════════════════════════════════════════════════');
        console.log('✅ Documento generado exitosamente:');
        console.log(`   ${outputPath}`);
        console.log('═══════════════════════════════════════════════════════════');

    } catch (error) {
        console.error('');
        console.error('═══════════════════════════════════════════════════════════');
        console.error('❌ Error durante la generación:');
        console.error(`   ${error.message}`);
        console.error('═══════════════════════════════════════════════════════════');
        process.exit(1);
    }
}

// Ejecutar
main();

/**
 * Generador de Informe Técnico - Formato F-GCT-17 ISER
 *
 * Genera documento Word (.docx) usando el formato institucional F-GCT-17
 * con tablas de contenido, ilustraciones y tablas navegables
 *
 * Uso:
 *   node generate_report_fgct17.js
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
    AlignmentType,
    HeadingLevel,
    TableOfContents,
    StyleLevel,
    Table,
    TableRow,
    TableCell,
    WidthType,
    VerticalAlign,
    BorderStyle,
    ImageRun,
    SimpleField,
    convertInchesToTwip
} from 'docx';
import { writeFile, mkdir, readFile } from 'fs/promises';
import { existsSync, readFileSync } from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Importar modulos de contenido
import { COLORS, TABLE_STYLES } from './src/styles.js';
import generarSeccionesIntroductorias from './src/seccion_introduccion.js';
import generarSeccionesJobboard from './src/seccion_jobboard.js';
import generarSeccionesPlatformUsage from './src/seccion_platform_usage.js';
import generarSeccionesPlatformAccess from './src/seccion_platform_access.js';
import generarTablaIlustraciones from './src/tabla_ilustraciones.js';
import generarTablaTablas from './src/tabla_tablas.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Constantes del formato F-GCT-17
const FGCT17 = {
    CODIGO: 'F-GCT-17',
    VERSION: '02',
    FECHA: '01/08/2024',
    TITULO: 'INFORME TÉCNICO - PLUGINS MOODLE ISER',
    // Márgenes en twips (1 pulgada = 1440 twips)
    MARGINS: {
        TOP: 1135,
        BOTTOM: 993,
        LEFT: 1701,
        RIGHT: 1701,
        HEADER: 567,
        FOOTER: 567
    }
};

/**
 * Crea el encabezado en formato F-GCT-17 con tabla institucional
 */
async function crearEncabezadoFGCT17() {
    // Leer logo ISER
    const logoPath = path.join(__dirname, 'images/logo_iser.png');
    let logoImage = null;

    try {
        const imageBuffer = readFileSync(logoPath);
        logoImage = new ImageRun({
            data: imageBuffer,
            transformation: {
                width: 60,
                height: 73
            }
        });
    } catch (error) {
        console.warn('No se pudo cargar el logo ISER:', error.message);
    }

    // Tabla del encabezado (3 columnas, 4 filas)
    const headerTable = new Table({
        width: { size: 100, type: WidthType.PERCENTAGE },
        rows: [
            // Fila 1
            new TableRow({
                height: { value: 397, rule: 'atLeast' },
                children: [
                    // Celda Logo (span 4 filas)
                    new TableCell({
                        width: { size: 24, type: WidthType.PERCENTAGE },
                        rowSpan: 4,
                        verticalAlign: VerticalAlign.CENTER,
                        children: [
                            new Paragraph({
                                alignment: AlignmentType.CENTER,
                                children: logoImage ? [logoImage] : [new TextRun({ text: 'ISER', bold: true })]
                            })
                        ]
                    }),
                    // Celda Título (span 2 filas)
                    new TableCell({
                        width: { size: 50, type: WidthType.PERCENTAGE },
                        rowSpan: 2,
                        verticalAlign: VerticalAlign.CENTER,
                        children: [
                            new Paragraph({
                                alignment: AlignmentType.CENTER,
                                children: [
                                    new TextRun({
                                        text: 'INFORME TÉCNICO - PLUGINS MOODLE',
                                        bold: true,
                                        font: 'Arial',
                                        size: 22
                                    })
                                ]
                            })
                        ]
                    }),
                    // Celda Código
                    new TableCell({
                        width: { size: 26, type: WidthType.PERCENTAGE },
                        verticalAlign: VerticalAlign.CENTER,
                        children: [
                            new Paragraph({
                                children: [
                                    new TextRun({ text: 'Código: ', font: 'Arial', size: 20 }),
                                    new TextRun({ text: FGCT17.CODIGO, font: 'Arial', size: 20 })
                                ]
                            })
                        ]
                    })
                ]
            }),
            // Fila 2
            new TableRow({
                height: { value: 397, rule: 'atLeast' },
                children: [
                    // Celda Versión
                    new TableCell({
                        verticalAlign: VerticalAlign.CENTER,
                        children: [
                            new Paragraph({
                                children: [
                                    new TextRun({ text: 'Versión: ', font: 'Arial', size: 20 }),
                                    new TextRun({ text: FGCT17.VERSION, font: 'Arial', size: 20 })
                                ]
                            })
                        ]
                    })
                ]
            }),
            // Fila 3
            new TableRow({
                height: { value: 397, rule: 'atLeast' },
                children: [
                    // Celda FORMATO (span 2 filas)
                    new TableCell({
                        rowSpan: 2,
                        verticalAlign: VerticalAlign.CENTER,
                        children: [
                            new Paragraph({
                                alignment: AlignmentType.CENTER,
                                children: [
                                    new TextRun({
                                        text: 'FORMATO',
                                        bold: true,
                                        font: 'Arial',
                                        size: 22
                                    })
                                ]
                            })
                        ]
                    }),
                    // Celda Fecha
                    new TableCell({
                        verticalAlign: VerticalAlign.CENTER,
                        children: [
                            new Paragraph({
                                children: [
                                    new TextRun({ text: 'Fecha: ', font: 'Arial', size: 20 }),
                                    new TextRun({ text: FGCT17.FECHA, font: 'Arial', size: 20 })
                                ]
                            })
                        ]
                    })
                ]
            }),
            // Fila 4
            new TableRow({
                height: { value: 397, rule: 'atLeast' },
                children: [
                    // Celda Página
                    new TableCell({
                        verticalAlign: VerticalAlign.CENTER,
                        children: [
                            new Paragraph({
                                children: [
                                    new TextRun({ text: 'Página: ', font: 'Arial', size: 20 }),
                                    new SimpleField({ instruction: 'PAGE' }),
                                    new TextRun({ text: ' de ', font: 'Arial', size: 20 }),
                                    new SimpleField({ instruction: 'NUMPAGES' })
                                ]
                            })
                        ]
                    })
                ]
            })
        ],
        borders: {
            top: { style: BorderStyle.SINGLE, size: 4, color: '000000' },
            bottom: { style: BorderStyle.SINGLE, size: 4, color: '000000' },
            left: { style: BorderStyle.SINGLE, size: 4, color: '000000' },
            right: { style: BorderStyle.SINGLE, size: 4, color: '000000' },
            insideHorizontal: { style: BorderStyle.SINGLE, size: 4, color: '000000' },
            insideVertical: { style: BorderStyle.SINGLE, size: 4, color: '000000' }
        }
    });

    return new Header({
        children: [headerTable]
    });
}

/**
 * Crea el pie de página institucional
 */
function crearPiePaginaFGCT17() {
    return new Footer({
        children: [
            new Paragraph({
                alignment: AlignmentType.RIGHT,
                children: [
                    new TextRun({
                        text: '"Formamos profesionales de calidad para el desarrollo social y humano"',
                        font: 'Pristina',
                        size: 28,
                        color: '808080',
                        italics: true
                    })
                ]
            })
        ]
    });
}

/**
 * Genera un salto de página
 */
function saltoPagina() {
    return new Paragraph({
        children: [new PageBreak()]
    });
}

/**
 * Genera la portada del documento
 */
function generarPortadaFGCT17() {
    const elementos = [];

    // Espacio inicial
    elementos.push(new Paragraph({ spacing: { before: 2880 } }));

    // Título principal
    elementos.push(
        new Paragraph({
            alignment: AlignmentType.CENTER,
            children: [
                new TextRun({
                    text: 'INFORME TÉCNICO',
                    bold: true,
                    size: 48,
                    color: COLORS.VERDE,
                    font: 'Arial'
                })
            ],
            spacing: { after: 400 }
        })
    );

    // Subtítulo
    elementos.push(
        new Paragraph({
            alignment: AlignmentType.CENTER,
            children: [
                new TextRun({
                    text: 'Documentación de Plugins Moodle',
                    bold: true,
                    size: 32,
                    color: COLORS.GRIS,
                    font: 'Arial'
                })
            ],
            spacing: { after: 200 }
        })
    );

    // Plataforma Virtual ISER
    elementos.push(
        new Paragraph({
            alignment: AlignmentType.CENTER,
            children: [
                new TextRun({
                    text: 'Plataforma Virtual ISER',
                    size: 28,
                    color: COLORS.GRIS,
                    font: 'Arial'
                })
            ],
            spacing: { after: 1200 }
        })
    );

    // Plugins documentados
    const plugins = ['local_jobboard', 'report_platform_usage', 'local_platform_access'];
    for (const plugin of plugins) {
        elementos.push(
            new Paragraph({
                alignment: AlignmentType.CENTER,
                children: [
                    new TextRun({
                        text: `• ${plugin}`,
                        size: 24,
                        color: COLORS.VERDE,
                        font: 'Arial',
                        bold: true
                    })
                ],
                spacing: { after: 100 }
            })
        );
    }

    // Espacio
    elementos.push(new Paragraph({ spacing: { before: 1200 } }));

    // Año
    elementos.push(
        new Paragraph({
            alignment: AlignmentType.CENTER,
            children: [
                new TextRun({
                    text: '2025',
                    bold: true,
                    size: 36,
                    color: COLORS.ROJO,
                    font: 'Arial'
                })
            ]
        })
    );

    return elementos;
}

/**
 * Genera la tabla de contenido navegable
 */
function generarTablaContenidoNavegable() {
    const elementos = [];

    elementos.push(
        new Paragraph({
            text: 'TABLA DE CONTENIDO',
            heading: HeadingLevel.HEADING_1,
            spacing: { before: 400, after: 400 }
        })
    );

    // TOC automático de Word
    elementos.push(
        new TableOfContents('Tabla de Contenido', {
            hyperlink: true,
            headingStyleRange: '1-3',
            stylesWithLevels: [
                new StyleLevel('Heading1', 1),
                new StyleLevel('Heading2', 2),
                new StyleLevel('Heading3', 3)
            ]
        })
    );

    return elementos;
}

/**
 * Genera la tabla de ilustraciones con contenido
 * Usa la función generarTablaIlustraciones del módulo correspondiente
 */
function generarTablaIlustracionesCompleta() {
    return generarTablaIlustraciones();
}

/**
 * Genera la tabla de tablas con contenido
 * Usa la función generarTablaTablas del módulo correspondiente
 */
function generarTablaTablasCompleta() {
    return generarTablaTablas();
}

/**
 * Genera el documento completo en formato F-GCT-17
 */
async function generarDocumentoFGCT17() {
    console.log('🔄 Iniciando generación del informe en formato F-GCT-17...');

    const children = [];

    try {
        // Portada
        console.log('  📄 Generando portada...');
        children.push(...generarPortadaFGCT17());
        children.push(saltoPagina());

        // Tabla de contenido navegable
        console.log('  📑 Generando tabla de contenido navegable...');
        children.push(...generarTablaContenidoNavegable());
        children.push(saltoPagina());

        // Tabla de ilustraciones navegable
        console.log('  🖼️  Generando tabla de ilustraciones navegable...');
        children.push(...generarTablaIlustracionesCompleta());
        children.push(saltoPagina());

        // Tabla de tablas navegable
        console.log('  📊 Generando tabla de tablas navegable...');
        children.push(...generarTablaTablasCompleta());
        children.push(saltoPagina());

        // Secciones introductorias
        console.log('  📝 Generando secciones introductorias...');
        const intro = await generarSeccionesIntroductorias();
        children.push(...intro);
        children.push(saltoPagina());

        // Sección local_jobboard
        console.log('  🔧 Generando sección local_jobboard...');
        const jobboard = await generarSeccionesJobboard();
        children.push(...jobboard);
        children.push(saltoPagina());

        // Sección report_platform_usage
        console.log('  📊 Generando sección report_platform_usage...');
        const platformUsage = await generarSeccionesPlatformUsage();
        children.push(...platformUsage);
        children.push(saltoPagina());

        // Sección local_platform_access
        console.log('  🔐 Generando sección local_platform_access...');
        const platformAccess = await generarSeccionesPlatformAccess();
        children.push(...platformAccess);

    } catch (error) {
        console.error('❌ Error generando sección:', error.message);
        throw error;
    }

    // Crear encabezado y pie de página
    console.log('  📎 Creando encabezado y pie de página institucionales...');
    const headerFGCT17 = await crearEncabezadoFGCT17();
    const footerFGCT17 = crearPiePaginaFGCT17();

    // Estilos del documento
    const documentStyles = {
        default: {
            document: {
                run: {
                    font: 'Arial',
                    size: 24
                },
                paragraph: {
                    spacing: { line: 360 }
                }
            },
            heading1: {
                run: {
                    font: 'Arial',
                    size: 28,
                    bold: true,
                    color: COLORS.VERDE
                },
                paragraph: {
                    spacing: { before: 400, after: 200 }
                }
            },
            heading2: {
                run: {
                    font: 'Arial',
                    size: 24,
                    bold: true,
                    color: COLORS.VERDE
                },
                paragraph: {
                    spacing: { before: 300, after: 150 }
                }
            },
            heading3: {
                run: {
                    font: 'Arial',
                    size: 24,
                    bold: true,
                    color: COLORS.GRIS
                },
                paragraph: {
                    spacing: { before: 200, after: 100 }
                }
            }
        }
    };

    // Crear documento
    console.log('  📝 Creando documento Word...');

    const doc = new Document({
        styles: documentStyles,
        features: {
            updateFields: true
        },
        sections: [{
            properties: {
                page: {
                    margin: {
                        top: FGCT17.MARGINS.TOP,
                        bottom: FGCT17.MARGINS.BOTTOM,
                        left: FGCT17.MARGINS.LEFT,
                        right: FGCT17.MARGINS.RIGHT,
                        header: FGCT17.MARGINS.HEADER,
                        footer: FGCT17.MARGINS.FOOTER
                    }
                }
            },
            headers: {
                default: headerFGCT17
            },
            footers: {
                default: footerFGCT17
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
    console.log('═══════════════════════════════════════════════════════════');
    console.log('  GENERADOR DE INFORME TÉCNICO - FORMATO F-GCT-17');
    console.log('═══════════════════════════════════════════════════════════');
    console.log('');

    try {
        const doc = await generarDocumentoFGCT17();

        const nombreArchivo = 'INFORME_TECNICO_FGCT17_2025.docx';
        const outputPath = await guardarDocumento(doc, nombreArchivo);

        console.log('');
        console.log('═══════════════════════════════════════════════════════════');
        console.log('✅ Documento generado exitosamente:');
        console.log(`   ${outputPath}`);
        console.log('');
        console.log('📋 Instrucciones post-generación:');
        console.log('   1. Abra el documento en Microsoft Word');
        console.log('   2. Presione Ctrl+A para seleccionar todo');
        console.log('   3. Presione F9 para actualizar la tabla de contenido');
        console.log('');
        console.log('📊 Las listas de ilustraciones y tablas están pre-pobladas');
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

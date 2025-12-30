/**
 * Módulo para generar la sección de Platform Usage Report
 *
 * Lee data/report_platform_usage/CONSOLIDADO.json y genera
 * las secciones del informe técnico en formato docx
 */

import { Paragraph, TextRun, Table, TableCell, TableRow, HeadingLevel, AlignmentType, WidthType, BorderStyle } from 'docx';
import { readFile } from 'fs/promises';
import { COLORS } from './styles.js';
import { crearParrafoConImagen, existeSVG } from './image_utils.js';

// Ruta base de SVG para este plugin
const SVG_BASE = '/home/user/iomad/docs/informe_tecnico/svg/report_platform_usage';

/**
 * Genera la sección completa de Platform Usage Report
 * @returns {Promise<Array>} Array de elementos docx (Paragraph, Table, etc.)
 */
export async function generarSeccionPlatformUsage() {
    // Leer datos del plugin
    const datosRaw = await readFile(
        '/home/user/iomad/docs/informe_tecnico/data/report_platform_usage/CONSOLIDADO.json',
        'utf-8'
    );
    const datos = JSON.parse(datosRaw);

    const elementos = [];

    // ============================================================
    // 1. INTRODUCCIÓN
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'PLUGIN REPORT_PLATFORM_USAGE',
            heading: HeadingLevel.HEADING_1,
            spacing: { before: 400, after: 200 }
        })
    );

    elementos.push(
        new Paragraph({
            text: 'Introducción',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: `${datos.resumen_ejecutivo.nombre_plugin} es un ${datos.resumen_ejecutivo.tipo} diseñado para proporcionar ${datos.resumen_ejecutivo.proposito}.`,
                    size: 24
                })
            ],
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Este plugin se destaca por las siguientes características:',
                    size: 24
                })
            ],
            spacing: { after: 100 }
        })
    );

    datos.resumen_ejecutivo.caracteristicas_destacadas.forEach(caracteristica => {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `• ${caracteristica}`,
                        size: 24
                    })
                ],
                spacing: { after: 100 },
                indent: { left: 360 }
            })
        );
    });

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Audiencia objetivo: ',
                    bold: true,
                    size: 24
                }),
                new TextRun({
                    text: datos.resumen_ejecutivo.audiencia_objetivo.join(', ') + '.',
                    size: 24
                })
            ],
            spacing: { before: 200, after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    // ============================================================
    // 2. ESTRUCTURA DEL PLUGIN
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'Estructura del Plugin',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El plugin report_platform_usage sigue la estructura estándar de plugins de tipo report en Moodle, organizando sus archivos de manera modular para facilitar el mantenimiento.',
                    size: 24
                })
            ],
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    // Insertar diagrama de estructura
    const estructuraPath = `${SVG_BASE}/estructura_directorios.svg`;
    if (existeSVG(estructuraPath)) {
        try {
            const imagenParrafo = await crearParrafoConImagen(estructuraPath, { width: 480 });
            elementos.push(imagenParrafo);
        } catch (error) {
            console.warn(`No se pudo cargar imagen: ${estructuraPath}`, error.message);
        }
    }

    elementos.push(
        new Paragraph({
            text: 'Figura 10. Estructura de directorios del plugin report_platform_usage',
            style: 'PieIlustracion',
            spacing: { before: 100, after: 300 }
        })
    );

    // Tabla de información de versión
    elementos.push(
        new Paragraph({
            text: 'Información de Versión',
            heading: HeadingLevel.HEADING_3,
            spacing: { before: 200, after: 100 }
        })
    );

    const tablaVersion = new Table({
        width: { size: 100, type: WidthType.PERCENTAGE },
        rows: [
            new TableRow({
                tableHeader: true,
                children: [
                    new TableCell({
                        children: [new Paragraph({ children: [new TextRun({ text: 'Atributo', bold: true, color: 'FFFFFF' })] })],
                        shading: { fill: COLORS.VERDE },
                        margins: { top: 100, bottom: 100, left: 100, right: 100 }
                    }),
                    new TableCell({
                        children: [new Paragraph({ children: [new TextRun({ text: 'Valor', bold: true, color: 'FFFFFF' })] })],
                        shading: { fill: COLORS.VERDE },
                        margins: { top: 100, bottom: 100, left: 100, right: 100 }
                    })
                ]
            }),
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph('Plugin')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(datos.informacion_version.plugin)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            }),
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph('Componente')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(datos.informacion_version.component)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            }),
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph('Versión')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(datos.informacion_version.version_release)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            }),
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph('Moodle requerido')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(datos.informacion_version.moodle_requerido)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            }),
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph('Madurez')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(datos.informacion_version.madurez)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            }),
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph('Licencia')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(datos.informacion_version.licencia)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            })
        ]
    });

    elementos.push(tablaVersion);

    elementos.push(
        new Paragraph({
            text: 'Tabla 5. Información de versión - Platform Usage Report',
            style: 'PieTabla',
            spacing: { before: 100, after: 300 }
        })
    );

    // ============================================================
    // 3. ARQUITECTURA
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'Arquitectura del Sistema',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 400, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El plugin Platform Usage Report implementa un patrón arquitectónico de ',
                    size: 24
                }),
                new TextRun({
                    text: datos.arquitectura_clases.clases_principales[0].patron,
                    bold: true,
                    size: 24
                }),
                new TextRun({
                    text: ', proporcionando una clara separación entre la lógica de acceso a datos y la capa de servicios.',
                    size: 24
                })
            ],
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    // Insertar diagrama de arquitectura
    const arquitecturaPath = `${SVG_BASE}/arquitectura.svg`;
    if (existeSVG(arquitecturaPath)) {
        try {
            const imagenParrafo = await crearParrafoConImagen(arquitecturaPath, { width: 500 });
            elementos.push(imagenParrafo);
        } catch (error) {
            console.warn(`No se pudo cargar imagen: ${arquitecturaPath}`, error.message);
        }
    }

    elementos.push(
        new Paragraph({
            text: 'Figura 11. Diagrama de arquitectura - Platform Usage Report',
            style: 'PieIlustracion',
            spacing: { before: 100, after: 300 }
        })
    );

    // ============================================================
    // 4. BASE DE DATOS
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'Arquitectura de Base de Datos',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            text: 'Tablas Propias',
            heading: HeadingLevel.HEADING_3,
            spacing: { before: 200, after: 100 }
        })
    );

    // Tabla de tablas propias
    datos.arquitectura_base_datos.tablas_propias.forEach((tabla, index) => {
        const tablaDB = new Table({
            width: { size: 100, type: WidthType.PERCENTAGE },
            rows: [
                new TableRow({
                    tableHeader: true,
                    children: [
                        new TableCell({
                            children: [new Paragraph({ children: [new TextRun({ text: 'Campo', bold: true, color: 'FFFFFF' })] })],
                            shading: { fill: COLORS.VERDE },
                            margins: { top: 100, bottom: 100, left: 100, right: 100 }
                        }),
                        new TableCell({
                            children: [new Paragraph({ children: [new TextRun({ text: 'Descripción', bold: true, color: 'FFFFFF' })] })],
                            shading: { fill: COLORS.VERDE },
                            margins: { top: 100, bottom: 100, left: 100, right: 100 }
                        })
                    ]
                }),
                new TableRow({
                    children: [
                        new TableCell({ children: [new Paragraph('Nombre')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                        new TableCell({ children: [new Paragraph(tabla.nombre)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                    ]
                }),
                new TableRow({
                    children: [
                        new TableCell({ children: [new Paragraph('Propósito')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                        new TableCell({ children: [new Paragraph(tabla.proposito)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                    ]
                }),
                new TableRow({
                    children: [
                        new TableCell({ children: [new Paragraph('Poblada por')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                        new TableCell({ children: [new Paragraph(tabla.poblada_por)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                    ]
                }),
                new TableRow({
                    children: [
                        new TableCell({ children: [new Paragraph('Frecuencia')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                        new TableCell({ children: [new Paragraph(tabla.frecuencia_actualizacion)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                    ]
                })
            ]
        });

        elementos.push(tablaDB);

        elementos.push(
            new Paragraph({
                text: `Tabla ${6 + index}. Tabla ${tabla.nombre}`,
                style: 'PieTabla',
                spacing: { before: 100, after: 200 }
            })
        );
    });

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El plugin utiliza además las siguientes tablas estándar de Moodle:',
                    size: 24
                })
            ],
            spacing: { before: 200, after: 100 }
        })
    );

    datos.arquitectura_base_datos.tablas_externas_usadas.forEach(tabla => {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `• ${tabla}`,
                        size: 24,
                        font: 'Courier New'
                    })
                ],
                spacing: { after: 50 },
                indent: { left: 360 }
            })
        );
    });

    // ============================================================
    // 5. CLASES PRINCIPALES
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'Clases Principales',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    datos.arquitectura_clases.clases_principales.forEach((clase, index) => {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `${index + 1}. ${clase.nombre}`,
                        bold: true,
                        size: 24
                    })
                ],
                spacing: { before: 200, after: 100 }
            })
        );

        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: 'Responsabilidad: ',
                        bold: true,
                        size: 24
                    }),
                    new TextRun({
                        text: clase.responsabilidad,
                        size: 24
                    })
                ],
                spacing: { after: 100 },
                alignment: AlignmentType.JUSTIFIED
            })
        );

        if (clase.caracteristicas) {
            elementos.push(
                new Paragraph({
                    children: [
                        new TextRun({
                            text: 'Características:',
                            size: 24
                        })
                    ],
                    spacing: { before: 100, after: 50 }
                })
            );

            clase.caracteristicas.forEach(caracteristica => {
                elementos.push(
                    new Paragraph({
                        children: [
                            new TextRun({
                                text: `• ${caracteristica}`,
                                size: 24
                            })
                        ],
                        spacing: { after: 50 },
                        indent: { left: 360 }
                    })
                );
            });
        }

        if (clase.metricas_proporcionadas) {
            elementos.push(
                new Paragraph({
                    children: [
                        new TextRun({
                            text: 'Métricas proporcionadas:',
                            size: 24
                        })
                    ],
                    spacing: { before: 100, after: 50 }
                })
            );

            clase.metricas_proporcionadas.forEach(metrica => {
                elementos.push(
                    new Paragraph({
                        children: [
                            new TextRun({
                                text: `• ${metrica}`,
                                size: 24
                            })
                        ],
                        spacing: { after: 50 },
                        indent: { left: 360 }
                    })
                );
            });
        }
    });

    // ============================================================
    // 6. CAPABILITIES
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'Capabilities y Seguridad',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    const filasCapabilities = [
        new TableRow({
            tableHeader: true,
            children: [
                new TableCell({
                    children: [new Paragraph({ children: [new TextRun({ text: 'Capability', bold: true, color: 'FFFFFF' })] })],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                }),
                new TableCell({
                    children: [new Paragraph({ children: [new TextRun({ text: 'Tipo', bold: true, color: 'FFFFFF' })] })],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                }),
                new TableCell({
                    children: [new Paragraph({ children: [new TextRun({ text: 'Propósito', bold: true, color: 'FFFFFF' })] })],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                }),
                new TableCell({
                    children: [new Paragraph({ children: [new TextRun({ text: 'Roles por defecto', bold: true, color: 'FFFFFF' })] })],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                })
            ]
        })
    ];

    datos.seguridad_permisos.capabilities.forEach(cap => {
        filasCapabilities.push(
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph(cap.nombre)], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(cap.tipo)], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(cap.proposito)], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(cap.roles_defecto.join(', '))], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            })
        );
    });

    const tablaCapabilities = new Table({
        width: { size: 100, type: WidthType.PERCENTAGE },
        rows: filasCapabilities
    });

    elementos.push(tablaCapabilities);

    elementos.push(
        new Paragraph({
            text: `Tabla ${datos.arquitectura_base_datos.tablas_propias.length + 6}. Capabilities del plugin Platform Usage Report`,
            style: 'PieTabla',
            spacing: { before: 100, after: 300 }
        })
    );

    // ============================================================
    // 7. MÉTRICAS DISPONIBLES
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'Métricas Disponibles',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El plugin Platform Usage Report proporciona un conjunto completo de métricas organizadas en las siguientes categorías:',
                    size: 24
                })
            ],
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    datos.metricas_informes.categorias.forEach((categoria, index) => {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `${index + 1}. ${categoria.nombre}`,
                        bold: true,
                        size: 24
                    })
                ],
                spacing: { before: 200, after: 100 }
            })
        );

        categoria.metricas.forEach(metrica => {
            elementos.push(
                new Paragraph({
                    children: [
                        new TextRun({
                            text: `• ${metrica}`,
                            size: 24
                        })
                    ],
                    spacing: { after: 50 },
                    indent: { left: 360 }
                })
            );
        });

        if (categoria.contexto) {
            elementos.push(
                new Paragraph({
                    children: [
                        new TextRun({
                            text: 'Contexto: ',
                            italics: true,
                            color: COLORS.GRIS,
                            size: 22
                        }),
                        new TextRun({
                            text: categoria.contexto,
                            italics: true,
                            color: COLORS.GRIS,
                            size: 22
                        })
                    ],
                    spacing: { before: 100, after: 100 },
                    indent: { left: 360 }
                })
            );
        }

        if (categoria.fuente_datos) {
            elementos.push(
                new Paragraph({
                    children: [
                        new TextRun({
                            text: 'Fuente de datos: ',
                            italics: true,
                            color: COLORS.GRIS,
                            size: 22
                        }),
                        new TextRun({
                            text: categoria.fuente_datos,
                            italics: true,
                            color: COLORS.GRIS,
                            size: 22
                        })
                    ],
                    spacing: { after: 100 },
                    indent: { left: 360 }
                })
            );
        }
    });

    // ============================================================
    // 8. FLUJO DE DATOS
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'Flujo de Datos',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El plugin procesa datos de múltiples fuentes de Moodle para generar reportes consolidados de uso de la plataforma.',
                    size: 24
                })
            ],
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    // Insertar diagrama de flujo de datos
    const flujoDatosPath = `${SVG_BASE}/flujo_datos.svg`;
    if (existeSVG(flujoDatosPath)) {
        try {
            const imagenParrafo = await crearParrafoConImagen(flujoDatosPath, { width: 520 });
            elementos.push(imagenParrafo);
        } catch (error) {
            console.warn(`No se pudo cargar imagen: ${flujoDatosPath}`, error.message);
        }
    }

    elementos.push(
        new Paragraph({
            text: 'Figura 12. Diagrama de flujo de datos - Platform Usage Report',
            style: 'PieIlustracion',
            spacing: { before: 100, after: 300 }
        })
    );

    return elementos;
}

export default generarSeccionPlatformUsage;

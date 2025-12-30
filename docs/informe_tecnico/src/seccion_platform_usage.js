/**
 * Generador de sección de Platform Usage Report
 *
 * Lee data/report_platform_usage/CONSOLIDADO.json y genera
 * las secciones del informe técnico en formato docx
 */

import { Paragraph, TextRun, Table, TableCell, TableRow, HeadingLevel, AlignmentType, WidthType, BorderStyle } from 'docx';
import { readFile } from 'fs/promises';
import { COLORS } from './styles.js';

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
            text: 'PLATFORM USAGE REPORT',
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
                })
            ],
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    elementos.push(
        new Paragraph({
            text: 'Este plugin se destaca por las siguientes características:',
            spacing: { after: 100 }
        })
    );

    datos.resumen_ejecutivo.caracteristicas_destacadas.forEach(caracteristica => {
        elementos.push(
            new Paragraph({
                text: caracteristica,
                bullet: { level: 0 },
                spacing: { after: 100 }
            })
        );
    });

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Audiencia objetivo: ',
                    bold: true
                }),
                new TextRun({
                    text: datos.resumen_ejecutivo.audiencia_objetivo.join(', ') + '.'
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

    // Tabla de información de versión
    elementos.push(
        new Paragraph({
            text: 'Tabla 1. Información de versión - Platform Usage Report',
            style: 'PieTabla',
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
                        children: [new Paragraph({ text: 'Atributo', style: 'Normal' })],
                        shading: { fill: COLORS.VERDE },
                        margins: { top: 100, bottom: 100, left: 100, right: 100 }
                    }),
                    new TableCell({
                        children: [new Paragraph({ text: 'Valor', style: 'Normal' })],
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
                }),
                new TextRun({
                    text: datos.arquitectura_clases.clases_principales[0].patron,
                    bold: true
                }),
                new TextRun({
                    text: ', proporcionando una clara separación entre la lógica de acceso a datos y la capa de servicios.'
                })
            ],
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    // Referencia a diagrama SVG
    elementos.push(
        new Paragraph({
            text: 'Ilustración 1. Diagrama de arquitectura - Platform Usage Report',
            style: 'PieIlustracion',
            spacing: { before: 200, after: 200 }
        })
    );

    elementos.push(
        new Paragraph({
            text: '[Ver archivo: svg/report_platform_usage/architecture_diagram.svg]',
            italics: true,
            color: COLORS.GRIS,
            spacing: { after: 300 },
            alignment: AlignmentType.CENTER
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
            text: 'Tablas propias',
            heading: HeadingLevel.HEADING_3,
            spacing: { before: 200, after: 100 }
        })
    );

    // Tabla de tablas propias
    datos.arquitectura_base_datos.tablas_propias.forEach((tabla, index) => {
        elementos.push(
            new Paragraph({
                text: `Tabla ${index + 2}. Tabla ${tabla.nombre}`,
                style: 'PieTabla',
                spacing: { before: 200, after: 100 }
            })
        );

        const tablaDB = new Table({
            width: { size: 100, type: WidthType.PERCENTAGE },
            rows: [
                new TableRow({
                    tableHeader: true,
                    children: [
                        new TableCell({
                            children: [new Paragraph('Campo')],
                            shading: { fill: COLORS.VERDE },
                            margins: { top: 100, bottom: 100, left: 100, right: 100 }
                        }),
                        new TableCell({
                            children: [new Paragraph('Descripción')],
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
    });

    elementos.push(
        new Paragraph({
            text: 'El plugin utiliza además las siguientes tablas estándar de Moodle:',
            spacing: { before: 200, after: 100 }
        })
    );

    datos.arquitectura_base_datos.tablas_externas_usadas.forEach(tabla => {
        elementos.push(
            new Paragraph({
                text: tabla,
                bullet: { level: 0 },
                spacing: { after: 50 }
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
                        bold: true
                    }),
                    new TextRun({
                        text: clase.responsabilidad
                    })
                ],
                spacing: { after: 100 },
                alignment: AlignmentType.JUSTIFIED
            })
        );

        if (clase.caracteristicas) {
            elementos.push(
                new Paragraph({
                    text: 'Características:',
                    spacing: { before: 100, after: 50 }
                })
            );

            clase.caracteristicas.forEach(caracteristica => {
                elementos.push(
                    new Paragraph({
                        text: caracteristica,
                        bullet: { level: 0 },
                        spacing: { after: 50 }
                    })
                );
            });
        }

        if (clase.metricas_proporcionadas) {
            elementos.push(
                new Paragraph({
                    text: 'Métricas proporcionadas:',
                    spacing: { before: 100, after: 50 }
                })
            );

            clase.metricas_proporcionadas.forEach(metrica => {
                elementos.push(
                    new Paragraph({
                        text: metrica,
                        bullet: { level: 0 },
                        spacing: { after: 50 }
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

    elementos.push(
        new Paragraph({
            text: `Tabla ${datos.arquitectura_base_datos.tablas_propias.length + 2}. Capabilities del plugin Platform Usage Report`,
            style: 'PieTabla',
            spacing: { before: 200, after: 100 }
        })
    );

    const filasCapabilities = [
        new TableRow({
            tableHeader: true,
            children: [
                new TableCell({
                    children: [new Paragraph('Capability')],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                }),
                new TableCell({
                    children: [new Paragraph('Tipo')],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                }),
                new TableCell({
                    children: [new Paragraph('Propósito')],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                }),
                new TableCell({
                    children: [new Paragraph('Roles por defecto')],
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
            text: 'El plugin Platform Usage Report proporciona un conjunto completo de métricas organizadas en las siguientes categorías:',
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
                    text: metrica,
                    bullet: { level: 0 },
                    spacing: { after: 50 }
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
                            color: COLORS.GRIS
                        }),
                        new TextRun({
                            text: categoria.contexto,
                            italics: true,
                            color: COLORS.GRIS
                        })
                    ],
                    spacing: { before: 100, after: 100 }
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
                            color: COLORS.GRIS
                        }),
                        new TextRun({
                            text: categoria.fuente_datos,
                            italics: true,
                            color: COLORS.GRIS
                        })
                    ],
                    spacing: { after: 100 }
                })
            );
        }
    });

    // Referencia a diagrama de flujo
    elementos.push(
        new Paragraph({
            text: 'Ilustración 2. Diagrama de flujo de datos - Platform Usage Report',
            style: 'PieIlustracion',
            spacing: { before: 300, after: 200 }
        })
    );

    elementos.push(
        new Paragraph({
            text: '[Ver archivo: svg/report_platform_usage/data_flow_diagram.svg]',
            italics: true,
            color: COLORS.GRIS,
            spacing: { after: 300 },
            alignment: AlignmentType.CENTER
        })
    );

    return elementos;
}

export default generarSeccionPlatformUsage;

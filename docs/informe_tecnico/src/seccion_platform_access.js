/**
 * Generador de sección de Platform Access Generator
 *
 * Lee data/local_platform_access/CONSOLIDADO.json y genera
 * las secciones del informe técnico en formato docx
 */

import { Paragraph, TextRun, Table, TableCell, TableRow, HeadingLevel, AlignmentType, WidthType, BorderStyle } from 'docx';
import { readFile } from 'fs/promises';
import { COLORS } from './styles.js';

/**
 * Genera la sección completa de Platform Access Generator
 * @returns {Promise<Array>} Array de elementos docx (Paragraph, Table, etc.)
 */
export async function generarSeccionPlatformAccess() {
    // Leer datos del plugin
    const datosRaw = await readFile(
        '/home/user/iomad/docs/informe_tecnico/data/local_platform_access/CONSOLIDADO.json',
        'utf-8'
    );
    const datos = JSON.parse(datosRaw);

    const elementos = [];

    // ============================================================
    // 1. INTRODUCCIÓN
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'PLATFORM ACCESS GENERATOR',
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
                    text: `${datos.resumen_ejecutivo.nombre_completo} es un plugin de tipo `,
                }),
                new TextRun({
                    text: datos.tipo,
                    bold: true
                }),
                new TextRun({
                    text: ` cuyo propósito es ${datos.resumen_ejecutivo.proposito}`
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
                    text: 'Arquitectura: ',
                    bold: true
                }),
                new TextRun({
                    text: datos.resumen_ejecutivo.arquitectura
                })
            ],
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    elementos.push(
        new Paragraph({
            text: 'Características principales:',
            spacing: { before: 100, after: 100 }
        })
    );

    datos.resumen_ejecutivo.caracteristicas_principales.forEach(caracteristica => {
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
                    text: 'Estado de desarrollo: ',
                    bold: true
                }),
                new TextRun({
                    text: datos.resumen_ejecutivo.estado_desarrollo
                })
            ],
            spacing: { before: 200, after: 100 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Integración IOMAD: ',
                    bold: true
                }),
                new TextRun({
                    text: datos.resumen_ejecutivo.integracion_iomad
                })
            ],
            spacing: { after: 200 }
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
            text: 'Tabla 1. Información de versión - Platform Access Generator',
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
                    new TableCell({ children: [new Paragraph(datos.plugin)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            }),
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph('Nombre completo')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(datos.resumen_ejecutivo.nombre_completo)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            }),
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph('Tipo')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(datos.tipo)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            }),
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph('Versión')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(datos.version.release)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            }),
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph('Moodle requerido')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(datos.version.requires)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            }),
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph('Madurez')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(datos.version.maturity)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            }),
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph('Licencia')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(datos.version.licencia)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            })
        ]
    });

    elementos.push(tablaVersion);

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Nota importante: ',
                    bold: true
                }),
                new TextRun({
                    text: `Este plugin ${datos.arquitectura.sin_lib_php ? 'NO utiliza lib.php' : 'utiliza lib.php'}, ` +
                          `adoptando un diseño moderno basado completamente en clases con namespaces.`
                })
            ],
            spacing: { before: 200, after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    // ============================================================
    // 3. ARQUITECTURA
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'Arquitectura del Sistema',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El plugin implementa el patrón de diseño ',
                }),
                new TextRun({
                    text: datos.arquitectura.patron_diseno,
                    bold: true
                }),
                new TextRun({
                    text: ', optimizado para operaciones masivas de inserción en base de datos.'
                })
            ],
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    // Referencia a diagrama SVG
    elementos.push(
        new Paragraph({
            text: 'Ilustración 1. Diagrama de arquitectura - Platform Access Generator',
            style: 'PieIlustracion',
            spacing: { before: 200, after: 200 }
        })
    );

    elementos.push(
        new Paragraph({
            text: '[Ver archivo: svg/local_platform_access/architecture_diagram.svg]',
            italics: true,
            color: COLORS.GRIS,
            spacing: { after: 300 },
            alignment: AlignmentType.CENTER
        })
    );

    // ============================================================
    // 4. CLASES PRINCIPALES
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'Clases Principales',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    datos.arquitectura.clases.forEach((clase, index) => {
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
                        text: 'Namespace: ',
                        bold: true
                    }),
                    new TextRun({
                        text: clase.namespace,
                        font: 'Courier New'
                    })
                ],
                spacing: { after: 50 }
            })
        );

        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: 'Tipo: ',
                        bold: true
                    }),
                    new TextRun({
                        text: clase.tipo
                    })
                ],
                spacing: { after: 50 }
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

        if (clase.metodos_publicos_clave) {
            elementos.push(
                new Paragraph({
                    text: 'Métodos públicos clave:',
                    spacing: { before: 100, after: 50 }
                })
            );

            clase.metodos_publicos_clave.forEach(metodo => {
                elementos.push(
                    new Paragraph({
                        children: [
                            new TextRun({
                                text: metodo,
                                font: 'Courier New',
                                size: 20
                            })
                        ],
                        bullet: { level: 0 },
                        spacing: { after: 50 }
                    })
                );
            });
        }

        if (clase.optimizaciones) {
            elementos.push(
                new Paragraph({
                    text: 'Optimizaciones implementadas:',
                    spacing: { before: 100, after: 50 }
                })
            );

            clase.optimizaciones.forEach(opt => {
                elementos.push(
                    new Paragraph({
                        text: opt,
                        bullet: { level: 0 },
                        spacing: { after: 50 }
                    })
                );
            });
        }

        if (clase.secciones) {
            elementos.push(
                new Paragraph({
                    text: 'Secciones del formulario:',
                    spacing: { before: 100, after: 50 }
                })
            );

            clase.secciones.forEach(seccion => {
                elementos.push(
                    new Paragraph({
                        text: seccion,
                        bullet: { level: 0 },
                        spacing: { after: 50 }
                    })
                );
            });
        }
    });

    // ============================================================
    // 5. CAPABILITIES
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
            text: 'Tabla 2. Capabilities del plugin Platform Access Generator',
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
                    children: [new Paragraph('Riesgos')],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                }),
                new TableCell({
                    children: [new Paragraph('Propósito')],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                })
            ]
        })
    ];

    datos.capacidades.lista.forEach(cap => {
        filasCapabilities.push(
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph(cap.nombre)], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(cap.tipo)], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(cap.riesgos.join(', '))], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(cap.proposito)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
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
            text: 'Controles de seguridad implementados:',
            spacing: { before: 200, after: 100 }
        })
    );

    datos.seguridad_privacidad.controles_seguridad.forEach(control => {
        elementos.push(
            new Paragraph({
                text: control,
                bullet: { level: 0 },
                spacing: { after: 50 }
            })
        );
    });

    // ============================================================
    // 6. FLUJO DE GENERACIÓN
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'Flujo de Generación de Datos',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            text: 'El proceso completo de generación de registros de acceso sigue los siguientes pasos:',
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    // Extraer solo los números de paso y descripción
    const pasosFlujo = Object.entries(datos.flujo_completo)
        .filter(([key, _]) => key.startsWith('paso_'))
        .map(([_, descripcion]) => descripcion);

    pasosFlujo.forEach((paso, index) => {
        elementos.push(
            new Paragraph({
                text: `${index + 1}. ${paso}`,
                spacing: { after: 100 }
            })
        );
    });

    // Referencia a diagrama de flujo
    elementos.push(
        new Paragraph({
            text: 'Ilustración 2. Diagrama de flujo de generación - Platform Access Generator',
            style: 'PieIlustracion',
            spacing: { before: 300, after: 200 }
        })
    );

    elementos.push(
        new Paragraph({
            text: '[Ver archivo: svg/local_platform_access/generation_flow_diagram.svg]',
            italics: true,
            color: COLORS.GRIS,
            spacing: { after: 300 },
            alignment: AlignmentType.CENTER
        })
    );

    // ============================================================
    // 7. OPTIMIZACIONES DE RENDIMIENTO
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'Optimizaciones de Rendimiento',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            text: 'El plugin implementa múltiples técnicas de optimización para garantizar rendimiento óptimo incluso con grandes volúmenes de datos:',
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    elementos.push(
        new Paragraph({
            text: 'Tabla 3. Técnicas de optimización implementadas',
            style: 'PieTabla',
            spacing: { before: 200, after: 100 }
        })
    );

    const filasOptimizaciones = [
        new TableRow({
            tableHeader: true,
            children: [
                new TableCell({
                    children: [new Paragraph('Técnica')],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                }),
                new TableCell({
                    children: [new Paragraph('Descripción')],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                }),
                new TableCell({
                    children: [new Paragraph('Impacto')],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                })
            ]
        })
    ];

    datos.optimizaciones_rendimiento.tecnicas.forEach(tecnica => {
        filasOptimizaciones.push(
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph(tecnica.nombre)], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(tecnica.descripcion)], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(tecnica.impacto)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            })
        );
    });

    const tablaOptimizaciones = new Table({
        width: { size: 100, type: WidthType.PERCENTAGE },
        rows: filasOptimizaciones
    });

    elementos.push(tablaOptimizaciones);

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Constantes de configuración: ',
                    bold: true
                }),
                new TextRun({
                    text: `El plugin utiliza un tamaño de lote (BATCH_SIZE) de ${datos.optimizaciones_rendimiento.constantes.BATCH_SIZE} registros, ` +
                          `memoria configurada como ${datos.optimizaciones_rendimiento.constantes.memoria} y tiempo de ejecución ${datos.optimizaciones_rendimiento.constantes.tiempo}.`
                })
            ],
            spacing: { before: 200, after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    // ============================================================
    // 8. INTEGRACIÓN CON IOMAD
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'Integración con IOMAD',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            text: `El plugin está ${datos.integracion_iomad.compatibilidad}.`,
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    elementos.push(
        new Paragraph({
            text: 'Tablas IOMAD utilizadas:',
            spacing: { before: 100, after: 50 }
        })
    );

    datos.integracion_iomad.tablas_utilizadas.forEach(tabla => {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: tabla,
                        font: 'Courier New'
                    })
                ],
                bullet: { level: 0 },
                spacing: { after: 50 }
            })
        );
    });

    elementos.push(
        new Paragraph({
            text: 'Funcionalidades específicas de IOMAD:',
            spacing: { before: 200, after: 100 }
        })
    );

    datos.integracion_iomad.funcionalidades.forEach(func => {
        elementos.push(
            new Paragraph({
                text: func,
                bullet: { level: 0 },
                spacing: { after: 50 }
            })
        );
    });

    // ============================================================
    // 9. CASOS DE USO
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'Casos de Uso',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            text: 'El plugin Platform Access Generator está diseñado para los siguientes casos de uso:',
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    datos.casos_uso.forEach((caso, index) => {
        elementos.push(
            new Paragraph({
                text: `${index + 1}. ${caso}`,
                spacing: { after: 100 }
            })
        );
    });

    // ============================================================
    // 10. CONCLUSIÓN
    // ============================================================
    elementos.push(
        new Paragraph({
            text: 'Conclusión',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Calidad del código: ',
                    bold: true
                }),
                new TextRun({
                    text: datos.conclusion.calidad_codigo
                })
            ],
            spacing: { after: 100 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Rendimiento: ',
                    bold: true
                }),
                new TextRun({
                    text: datos.conclusion.rendimiento
                })
            ],
            spacing: { after: 100 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Integración IOMAD: ',
                    bold: true
                }),
                new TextRun({
                    text: datos.conclusion.integracion_iomad
                })
            ],
            spacing: { after: 100 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Este plugin representa una solución robusta y bien diseñada para la generación de datos de prueba en entornos Moodle/IOMAD, ' +
                          'con un enfoque especial en rendimiento y escalabilidad.'
                })
            ],
            spacing: { before: 200, after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    return elementos;
}

export default generarSeccionPlatformAccess;

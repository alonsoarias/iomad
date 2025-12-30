/**
 * Módulo para generar la sección de Platform Access Generator
 *
 * Lee data/local_platform_access/CONSOLIDADO.json y genera
 * las secciones del informe técnico en formato docx
 */

import { Paragraph, TextRun, Table, TableCell, TableRow, HeadingLevel, AlignmentType, WidthType, BorderStyle } from 'docx';
import { readFile } from 'fs/promises';
import { COLORS } from './styles.js';
import { crearParrafoConImagen, existeSVG, crearTituloFigura, crearTituloTabla } from './image_utils.js';

// Ruta base de SVG para este plugin
const SVG_BASE = '/home/user/iomad/docs/informe_tecnico/svg/local_platform_access';

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
            text: 'PLUGIN LOCAL_PLATFORM_ACCESS',
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
                    text: `${datos.nombre_completo} es un plugin de tipo `,
                    size: 24
                }),
                new TextRun({
                    text: datos.tipo,
                    bold: true,
                    size: 24
                }),
                new TextRun({
                    text: ` cuyo propósito es ${datos.resumen_ejecutivo?.proposito || 'generar registros de acceso para pruebas y demostraciones'}.`,
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
                    text: 'Arquitectura: ',
                    bold: true,
                    size: 24
                }),
                new TextRun({
                    text: datos.resumen_ejecutivo.arquitectura,
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
                    text: 'Características principales:',
                    size: 24
                })
            ],
            spacing: { before: 100, after: 100 }
        })
    );

    datos.resumen_ejecutivo.caracteristicas_principales.forEach(caracteristica => {
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
                    text: 'Estado de desarrollo: ',
                    bold: true,
                    size: 24
                }),
                new TextRun({
                    text: datos.resumen_ejecutivo.estado_desarrollo,
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
                    text: 'Integración IOMAD: ',
                    bold: true,
                    size: 24
                }),
                new TextRun({
                    text: datos.resumen_ejecutivo.integracion_iomad,
                    size: 24
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

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El plugin local_platform_access utiliza una estructura moderna basada en namespaces y clases, sin depender de lib.php para sus funcionalidades principales.',
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

    elementos.push(crearTituloFigura('Estructura de directorios del plugin local_platform_access'));

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
                    new TableCell({ children: [new Paragraph(datos.plugin)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
                ]
            }),
            new TableRow({
                children: [
                    new TableCell({ children: [new Paragraph('Nombre completo')], margins: { top: 100, bottom: 100, left: 100, right: 100 } }),
                    new TableCell({ children: [new Paragraph(datos.nombre_completo)], margins: { top: 100, bottom: 100, left: 100, right: 100 } })
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

    elementos.push(crearTituloTabla('Información de versión - Platform Access Generator'));

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Nota importante: ',
                    bold: true,
                    size: 24
                }),
                new TextRun({
                    text: `Este plugin ${datos.arquitectura.sin_lib_php ? 'NO utiliza lib.php' : 'utiliza lib.php'}, adoptando un diseño moderno basado completamente en clases con namespaces.`,
                    size: 24
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
                    size: 24
                }),
                new TextRun({
                    text: datos.arquitectura.patron_diseno,
                    bold: true,
                    size: 24
                }),
                new TextRun({
                    text: ', optimizado para operaciones masivas de inserción en base de datos.',
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

    elementos.push(crearTituloFigura('Diagrama de arquitectura - Platform Access Generator'));

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
                        bold: true,
                        size: 24
                    }),
                    new TextRun({
                        text: clase.namespace,
                        font: 'Courier New',
                        size: 24
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
                        bold: true,
                        size: 24
                    }),
                    new TextRun({
                        text: clase.tipo,
                        size: 24
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

        if (clase.metodos_publicos_clave) {
            elementos.push(
                new Paragraph({
                    children: [
                        new TextRun({
                            text: 'Métodos públicos clave:',
                            size: 24
                        })
                    ],
                    spacing: { before: 100, after: 50 }
                })
            );

            clase.metodos_publicos_clave.forEach(metodo => {
                elementos.push(
                    new Paragraph({
                        children: [
                            new TextRun({
                                text: `• ${metodo}`,
                                font: 'Courier New',
                                size: 20
                            })
                        ],
                        spacing: { after: 50 },
                        indent: { left: 360 }
                    })
                );
            });
        }

        if (clase.optimizaciones) {
            elementos.push(
                new Paragraph({
                    children: [
                        new TextRun({
                            text: 'Optimizaciones implementadas:',
                            size: 24
                        })
                    ],
                    spacing: { before: 100, after: 50 }
                })
            );

            clase.optimizaciones.forEach(opt => {
                elementos.push(
                    new Paragraph({
                        children: [
                            new TextRun({
                                text: `• ${opt}`,
                                size: 24
                            })
                        ],
                        spacing: { after: 50 },
                        indent: { left: 360 }
                    })
                );
            });
        }

        if (clase.secciones) {
            elementos.push(
                new Paragraph({
                    children: [
                        new TextRun({
                            text: 'Secciones del formulario:',
                            size: 24
                        })
                    ],
                    spacing: { before: 100, after: 50 }
                })
            );

            clase.secciones.forEach(seccion => {
                elementos.push(
                    new Paragraph({
                        children: [
                            new TextRun({
                                text: `• ${seccion}`,
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
    // 5. CAPABILITIES
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
                    children: [new Paragraph({ children: [new TextRun({ text: 'Riesgos', bold: true, color: 'FFFFFF' })] })],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                }),
                new TableCell({
                    children: [new Paragraph({ children: [new TextRun({ text: 'Propósito', bold: true, color: 'FFFFFF' })] })],
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

    elementos.push(crearTituloTabla('Capabilities del plugin Platform Access Generator'));

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Controles de seguridad implementados:',
                    size: 24
                })
            ],
            spacing: { before: 200, after: 100 }
        })
    );

    datos.seguridad_privacidad.controles_seguridad.forEach(control => {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `• ${control}`,
                        size: 24
                    })
                ],
                spacing: { after: 50 },
                indent: { left: 360 }
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
            children: [
                new TextRun({
                    text: 'El proceso completo de generación de registros de acceso sigue los siguientes pasos:',
                    size: 24
                })
            ],
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
                children: [
                    new TextRun({
                        text: `${index + 1}. ${paso}`,
                        size: 24
                    })
                ],
                spacing: { after: 100 }
            })
        );
    });

    // Insertar diagrama de generación
    const diagramaGeneracionPath = `${SVG_BASE}/diagrama_generacion.svg`;
    if (existeSVG(diagramaGeneracionPath)) {
        try {
            const imagenParrafo = await crearParrafoConImagen(diagramaGeneracionPath, { width: 520 });
            elementos.push(imagenParrafo);
        } catch (error) {
            console.warn(`No se pudo cargar imagen: ${diagramaGeneracionPath}`, error.message);
        }
    }

    elementos.push(crearTituloFigura('Diagrama de flujo de generación - Platform Access Generator'));

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
            children: [
                new TextRun({
                    text: 'El plugin implementa múltiples técnicas de optimización para garantizar rendimiento óptimo incluso con grandes volúmenes de datos:',
                    size: 24
                })
            ],
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    const filasOptimizaciones = [
        new TableRow({
            tableHeader: true,
            children: [
                new TableCell({
                    children: [new Paragraph({ children: [new TextRun({ text: 'Técnica', bold: true, color: 'FFFFFF' })] })],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                }),
                new TableCell({
                    children: [new Paragraph({ children: [new TextRun({ text: 'Descripción', bold: true, color: 'FFFFFF' })] })],
                    shading: { fill: COLORS.VERDE },
                    margins: { top: 100, bottom: 100, left: 100, right: 100 }
                }),
                new TableCell({
                    children: [new Paragraph({ children: [new TextRun({ text: 'Impacto', bold: true, color: 'FFFFFF' })] })],
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

    elementos.push(crearTituloTabla('Técnicas de optimización implementadas'));

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Constantes de configuración: ',
                    bold: true,
                    size: 24
                }),
                new TextRun({
                    text: `El plugin utiliza un tamaño de lote (BATCH_SIZE) de ${datos.optimizaciones_rendimiento.constantes.BATCH_SIZE} registros, memoria configurada como ${datos.optimizaciones_rendimiento.constantes.memoria} y tiempo de ejecución ${datos.optimizaciones_rendimiento.constantes.tiempo}.`,
                    size: 24
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
            children: [
                new TextRun({
                    text: `El plugin está ${datos.integracion_iomad.compatibilidad}.`,
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
                    text: 'Tablas IOMAD utilizadas:',
                    size: 24
                })
            ],
            spacing: { before: 100, after: 50 }
        })
    );

    datos.integracion_iomad.tablas_utilizadas.forEach(tabla => {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `• ${tabla}`,
                        font: 'Courier New',
                        size: 22
                    })
                ],
                spacing: { after: 50 },
                indent: { left: 360 }
            })
        );
    });

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Funcionalidades específicas de IOMAD:',
                    size: 24
                })
            ],
            spacing: { before: 200, after: 100 }
        })
    );

    datos.integracion_iomad.funcionalidades.forEach(func => {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `• ${func}`,
                        size: 24
                    })
                ],
                spacing: { after: 50 },
                indent: { left: 360 }
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
            children: [
                new TextRun({
                    text: 'El plugin Platform Access Generator está diseñado para los siguientes casos de uso:',
                    size: 24
                })
            ],
            spacing: { after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    datos.casos_uso.forEach((caso, index) => {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `${index + 1}. ${caso}`,
                        size: 24
                    })
                ],
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
                    bold: true,
                    size: 24
                }),
                new TextRun({
                    text: datos.conclusion.calidad_codigo,
                    size: 24
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
                    bold: true,
                    size: 24
                }),
                new TextRun({
                    text: datos.conclusion.rendimiento,
                    size: 24
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
                    bold: true,
                    size: 24
                }),
                new TextRun({
                    text: datos.conclusion.integracion_iomad,
                    size: 24
                })
            ],
            spacing: { after: 100 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Este plugin representa una solución robusta y bien diseñada para la generación de datos de prueba en entornos Moodle/IOMAD, con un enfoque especial en rendimiento y escalabilidad.',
                    size: 24
                })
            ],
            spacing: { before: 200, after: 200 },
            alignment: AlignmentType.JUSTIFIED
        })
    );

    return elementos;
}

export default generarSeccionPlatformAccess;

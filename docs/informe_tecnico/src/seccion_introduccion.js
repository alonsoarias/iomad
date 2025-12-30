/**
 * Modulo para generar las secciones introductorias del documento
 * Incluye: Introduccion, Objetivos, Metodologia, Alcance
 */

import {
    Paragraph,
    TextRun,
    Table,
    TableRow,
    TableCell,
    HeadingLevel,
    AlignmentType,
    WidthType,
    BorderStyle,
    VerticalAlign
} from 'docx';
import { COLORS } from './styles.js';

/**
 * Genera todas las secciones introductorias
 * @returns {Promise<Array>} Array de elementos docx
 */
export async function generarSeccionesIntroductorias() {
    const elementos = [];

    // 1. Introduccion
    elementos.push(...generarIntroduccion());

    // 2. Objetivos
    elementos.push(...generarObjetivos());

    // 3. Metodologia
    elementos.push(...generarMetodologia());

    // 4. Alcance
    elementos.push(...generarAlcance());

    // 5. Tecnologias utilizadas
    elementos.push(...generarTecnologias());

    return elementos;
}

/**
 * Genera la seccion de introduccion general
 */
function generarIntroduccion() {
    const elementos = [];

    elementos.push(
        new Paragraph({
            text: '1. INTRODUCCION',
            heading: HeadingLevel.HEADING_1,
            spacing: { before: 400, after: 200 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El Instituto Superior de Educacion Rural (ISER) ha desarrollado un conjunto de plugins personalizados para la plataforma Moodle con el objetivo de optimizar y automatizar procesos institucionales criticos. Estos desarrollos representan un esfuerzo significativo por adaptar la plataforma de aprendizaje a las necesidades especificas de la institucion, mejorando la experiencia tanto de administradores como de usuarios finales.',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El presente informe tecnico documenta de manera exhaustiva tres plugins desarrollados para el ecosistema Moodle de ISER:',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Lista de plugins
    const pluginsInfo = [
        {
            nombre: 'local_jobboard',
            descripcion: 'Sistema integral de gestion de vacantes academicas y procesos de seleccion docente. Permite la publicacion de convocatorias, recepcion de postulaciones, validacion de documentos y seguimiento del proceso de contratacion.'
        },
        {
            nombre: 'report_platform_usage',
            descripcion: 'Herramienta de analisis y reporteria que proporciona metricas detalladas sobre el uso de la plataforma, incluyendo tiempo de dedicacion, patrones de acceso y estadisticas de completitud de cursos.'
        },
        {
            nombre: 'local_platform_access',
            descripcion: 'Utilidad para la generacion de datos de acceso de prueba, facilitando los procesos de testing, capacitacion y demostraciones del sistema sin afectar datos reales.'
        }
    ];

    for (const plugin of pluginsInfo) {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `\u2022 ${plugin.nombre}: `,
                        size: 24,
                        bold: true,
                        color: COLORS.VERDE
                    }),
                    new TextRun({
                        text: plugin.descripcion,
                        size: 24
                    })
                ],
                alignment: AlignmentType.JUSTIFIED,
                spacing: { after: 150 },
                indent: { left: 360 }
            })
        );
    }

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Esta documentacion ha sido elaborada siguiendo las normas ICONTEC (NTC 1486) para la presentacion de trabajos escritos, garantizando una estructura clara, coherente y profesional que facilite la comprension y mantenimiento futuro de los desarrollos.',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { before: 200, after: 200 }
        })
    );

    return elementos;
}

/**
 * Genera la seccion de objetivos
 */
function generarObjetivos() {
    const elementos = [];

    elementos.push(
        new Paragraph({
            text: '2. OBJETIVOS',
            heading: HeadingLevel.HEADING_1,
            spacing: { before: 400, after: 200 }
        })
    );

    // Objetivo General
    elementos.push(
        new Paragraph({
            text: '2.1 Objetivo General',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Documentar de manera integral la arquitectura, funcionalidad y especificaciones tecnicas de los plugins Moodle desarrollados para ISER, proporcionando una referencia tecnica completa que facilite el mantenimiento, actualizacion y extension futura de estos componentes de software.',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Objetivos Especificos
    elementos.push(
        new Paragraph({
            text: '2.2 Objetivos Especificos',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    const objetivosEspecificos = [
        'Describir la estructura de directorios y organizacion de archivos de cada plugin, siguiendo las convenciones de desarrollo de Moodle.',
        'Documentar el modelo de datos, incluyendo tablas de base de datos, relaciones y campos relevantes.',
        'Especificar las clases PHP principales, sus metodos, propiedades y responsabilidades dentro del sistema.',
        'Detallar los servicios web (Web Services) expuestos por cada plugin, incluyendo parametros y respuestas.',
        'Explicar el sistema de permisos y capabilities implementado para el control de acceso.',
        'Ilustrar los flujos de trabajo y estados posibles de las entidades principales.',
        'Proporcionar diagramas tecnicos que faciliten la comprension visual de la arquitectura.',
        'Establecer recomendaciones para el mantenimiento y actualizacion de los plugins.'
    ];

    for (let i = 0; i < objetivosEspecificos.length; i++) {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `${i + 1}. `,
                        size: 24,
                        bold: true,
                        color: COLORS.VERDE
                    }),
                    new TextRun({
                        text: objetivosEspecificos[i],
                        size: 24
                    })
                ],
                alignment: AlignmentType.JUSTIFIED,
                spacing: { after: 100 },
                indent: { left: 360 }
            })
        );
    }

    return elementos;
}

/**
 * Genera la seccion de metodologia
 */
function generarMetodologia() {
    const elementos = [];

    elementos.push(
        new Paragraph({
            text: '3. METODOLOGIA',
            heading: HeadingLevel.HEADING_1,
            spacing: { before: 400, after: 200 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'La elaboracion de este informe tecnico siguio una metodologia sistematica de analisis de codigo y documentacion que garantiza la precision y completitud de la informacion presentada.',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Fases de la metodologia
    elementos.push(
        new Paragraph({
            text: '3.1 Fases del Proceso',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    const fases = [
        {
            nombre: 'Analisis de Codigo Fuente',
            descripcion: 'Revision sistematica de todos los archivos PHP, JavaScript, XML y CSS de cada plugin, extrayendo informacion sobre clases, funciones, constantes y configuraciones.',
            herramientas: 'Analisis estatico de codigo, parsing de archivos XML'
        },
        {
            nombre: 'Extraccion de Esquemas',
            descripcion: 'Analisis de archivos install.xml y access.php para documentar la estructura de base de datos y el sistema de permisos.',
            herramientas: 'Parsing XML, analisis de definiciones de capabilities'
        },
        {
            nombre: 'Generacion de Diagramas',
            descripcion: 'Creacion de diagramas tecnicos en formato SVG que ilustran la arquitectura, flujos de trabajo y relaciones entre componentes.',
            herramientas: 'Diseño vectorial SVG con colores corporativos ISER'
        },
        {
            nombre: 'Consolidacion de Datos',
            descripcion: 'Integracion de toda la informacion extraida en estructuras JSON que sirven como fuente de datos para el documento.',
            herramientas: 'Procesamiento JSON, validacion de datos'
        },
        {
            nombre: 'Generacion del Documento',
            descripcion: 'Produccion automatizada del documento Word (.docx) utilizando plantillas y estilos ICONTEC.',
            herramientas: 'Libreria docx para Node.js, Sharp para procesamiento de imagenes'
        }
    ];

    for (let i = 0; i < fases.length; i++) {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `Fase ${i + 1}: ${fases[i].nombre}`,
                        size: 24,
                        bold: true,
                        color: COLORS.VERDE
                    })
                ],
                spacing: { before: 200, after: 100 }
            })
        );

        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: fases[i].descripcion,
                        size: 24
                    })
                ],
                alignment: AlignmentType.JUSTIFIED,
                spacing: { after: 50 },
                indent: { left: 360 }
            })
        );

        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: 'Herramientas: ',
                        size: 22,
                        bold: true,
                        italics: true
                    }),
                    new TextRun({
                        text: fases[i].herramientas,
                        size: 22,
                        italics: true,
                        color: COLORS.GRIS
                    })
                ],
                spacing: { after: 100 },
                indent: { left: 360 }
            })
        );
    }

    return elementos;
}

/**
 * Genera la seccion de alcance
 */
function generarAlcance() {
    const elementos = [];

    elementos.push(
        new Paragraph({
            text: '4. ALCANCE',
            heading: HeadingLevel.HEADING_1,
            spacing: { before: 400, after: 200 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Este documento cubre la documentacion tecnica de los tres plugins mencionados en su version actual (2025). El alcance incluye:',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    const alcanceIncluido = [
        'Arquitectura y estructura de cada plugin',
        'Modelo de datos completo con todas las tablas y campos',
        'Clases PHP principales y sus metodos publicos',
        'Servicios web y APIs disponibles',
        'Sistema de permisos y capabilities',
        'Flujos de trabajo y maquinas de estado',
        'Diagramas tecnicos ilustrativos'
    ];

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Incluye:',
                    size: 24,
                    bold: true,
                    color: COLORS.VERDE
                })
            ],
            spacing: { after: 100 }
        })
    );

    for (const item of alcanceIncluido) {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `\u2713 ${item}`,
                        size: 24
                    })
                ],
                spacing: { after: 50 },
                indent: { left: 360 }
            })
        );
    }

    const alcanceExcluido = [
        'Manual de usuario final',
        'Guias de instalacion paso a paso',
        'Codigo fuente completo (solo extractos relevantes)',
        'Documentacion de la carpeta cli/ del plugin local_jobboard'
    ];

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'No incluye:',
                    size: 24,
                    bold: true,
                    color: COLORS.ROJO
                })
            ],
            spacing: { before: 200, after: 100 }
        })
    );

    for (const item of alcanceExcluido) {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `\u2717 ${item}`,
                        size: 24
                    })
                ],
                spacing: { after: 50 },
                indent: { left: 360 }
            })
        );
    }

    return elementos;
}

/**
 * Genera la seccion de tecnologias utilizadas
 */
function generarTecnologias() {
    const elementos = [];

    elementos.push(
        new Paragraph({
            text: '5. TECNOLOGIAS UTILIZADAS',
            heading: HeadingLevel.HEADING_1,
            spacing: { before: 400, after: 200 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Los plugins documentados utilizan las siguientes tecnologias y estandares:',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Tabla de tecnologias
    const tecnologias = [
        ['Categoria', 'Tecnologia', 'Version/Descripcion'],
        ['Plataforma Base', 'Moodle', '4.1+ (IOMAD)'],
        ['Lenguaje Backend', 'PHP', '8.0+'],
        ['Base de Datos', 'MariaDB/MySQL', '10.4+ / 8.0+'],
        ['Frontend', 'JavaScript (AMD)', 'Modulos RequireJS'],
        ['Plantillas', 'Mustache', 'Motor de plantillas Moodle'],
        ['Estilos', 'CSS/SCSS', 'Integrado con tema Moodle'],
        ['Web Services', 'REST/AJAX', 'API Moodle External'],
        ['Autenticacion', 'OAuth/Sesskey', 'Sistema nativo Moodle'],
        ['Cache', 'MUC', 'Moodle Universal Cache'],
        ['Tareas', 'Cron Tasks', 'Sistema de tareas Moodle']
    ];

    elementos.push(crearTabla(tecnologias, [25, 30, 45]));

    elementos.push(
        new Paragraph({
            text: 'Tabla 1. Tecnologias utilizadas en los plugins',
            style: 'PieTabla',
            spacing: { before: 100, after: 300 }
        })
    );

    return elementos;
}

/**
 * Funcion auxiliar para crear tablas
 */
function crearTabla(datos, anchosColumnas = null) {
    const filas = [];

    for (let i = 0; i < datos.length; i++) {
        const esEncabezado = i === 0;
        const celdas = datos[i].map((contenido, index) => {
            return new TableCell({
                children: [
                    new Paragraph({
                        children: [
                            new TextRun({
                                text: contenido,
                                size: 22,
                                bold: esEncabezado,
                                color: esEncabezado ? COLORS.BLANCO : COLORS.NEGRO
                            })
                        ],
                        alignment: AlignmentType.LEFT,
                        spacing: { before: 50, after: 50 }
                    })
                ],
                shading: {
                    fill: esEncabezado ? COLORS.VERDE : COLORS.BLANCO
                },
                width: anchosColumnas ? {
                    size: anchosColumnas[index],
                    type: WidthType.PERCENTAGE
                } : undefined,
                verticalAlign: VerticalAlign.CENTER,
                margins: {
                    top: 100,
                    bottom: 100,
                    left: 100,
                    right: 100
                }
            });
        });

        filas.push(new TableRow({ children: celdas }));
    }

    return new Table({
        rows: filas,
        width: {
            size: 100,
            type: WidthType.PERCENTAGE
        },
        borders: {
            top: { style: BorderStyle.SINGLE, size: 1, color: COLORS.GRIS },
            bottom: { style: BorderStyle.SINGLE, size: 1, color: COLORS.GRIS },
            left: { style: BorderStyle.SINGLE, size: 1, color: COLORS.GRIS },
            right: { style: BorderStyle.SINGLE, size: 1, color: COLORS.GRIS },
            insideHorizontal: { style: BorderStyle.SINGLE, size: 1, color: COLORS.GRIS },
            insideVertical: { style: BorderStyle.SINGLE, size: 1, color: COLORS.GRIS }
        }
    });
}

export default generarSeccionesIntroductorias;

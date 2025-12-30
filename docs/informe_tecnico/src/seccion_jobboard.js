/**
 * Modulo para generar las secciones del plugin local_jobboard
 * en el documento tecnico usando la biblioteca docx
 */

import { readFile } from 'fs/promises';
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
import { crearParrafoConImagen, existeSVG } from './image_utils.js';

// Rutas base de SVG
const SVG_BASE = '/home/user/iomad/docs/informe_tecnico/svg/local_jobboard';

/**
 * Genera todas las secciones del plugin local_jobboard
 * @returns {Promise<Array>} Array de elementos docx (Paragraph, Table, etc.)
 */
export async function generarSeccionesJobboard() {
    // Leer el archivo CONSOLIDADO.json
    const consolidadoPath = '/home/user/iomad/docs/informe_tecnico/data/local_jobboard/CONSOLIDADO.json';
    const data = JSON.parse(await readFile(consolidadoPath, 'utf-8'));

    const elementos = [];

    // 1. Introduccion del plugin
    elementos.push(...generarIntroduccion(data));

    // 2. Estructura del plugin (async - con imagenes)
    elementos.push(...await generarEstructura(data));

    // 3. Arquitectura (async - con imagenes)
    elementos.push(...await generarArquitectura(data));

    // 4. Base de datos (async - con imagenes)
    elementos.push(...await generarBaseDatos(data));

    // 5. Clases principales (async - con imagenes)
    elementos.push(...await generarClasesPrincipales(data));

    // 6. Servicios web (async - con imagenes)
    elementos.push(...await generarServiciosWeb(data));

    // 7. Capabilities y permisos
    elementos.push(...generarCapabilities(data));

    // 8. Flujos de trabajo (async - con imagenes)
    elementos.push(...await generarFlujosTrabalho(data));

    return elementos;
}

/**
 * Genera la sección de introducción del plugin
 */
function generarIntroduccion(data) {
    const elementos = [];
    const version = data.version;

    // Título principal
    elementos.push(
        new Paragraph({
            text: 'PLUGIN LOCAL_JOBBOARD',
            heading: HeadingLevel.HEADING_1,
            spacing: { before: 400, after: 200 }
        })
    );

    // Descripción
    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: version.description,
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Información de versión
    elementos.push(
        new Paragraph({
            text: 'Información de Versión',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    // Tabla de información de versión
    elementos.push(
        crearTabla(
            [
                ['Campo', 'Valor'],
                ['Versión', version.release],
                ['Build', version.version.toString()],
                ['Madurez', version.maturity],
                ['Moodle requerido', version.requires_description],
                ['Moodle soportado', version.supported_description],
                ['Autor', version.author],
                ['Email', version.author_email],
                ['Copyright', version.copyright],
                ['Licencia', version.license]
            ],
            [30, 70]
        )
    );

    elementos.push(
        new Paragraph({
            text: `Tabla 1. Información de versión del plugin local_jobboard`,
            style: 'PieTabla',
            spacing: { before: 100, after: 300 }
        })
    );

    return elementos;
}

/**
 * Genera la seccion de estructura del plugin
 */
async function generarEstructura(data) {
    const elementos = [];

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
                    text: 'El plugin local_jobboard sigue la estructura estandar de plugins locales de Moodle. La organizacion de directorios y archivos esta diseñada para facilitar el mantenimiento y cumplir con las convenciones de Moodle. El plugin cuenta con 11 carpetas principales y 23 subcarpetas, organizando codigo PHP, modulos JavaScript AMD, plantillas Mustache y archivos de configuracion.',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Insertar imagen SVG
    const svgPath = `${SVG_BASE}/estructura_directorios.svg`;
    if (existeSVG(svgPath)) {
        try {
            const imagenParrafo = await crearParrafoConImagen(svgPath, { width: 520 });
            elementos.push(imagenParrafo);
        } catch (error) {
            console.warn(`No se pudo cargar imagen: ${svgPath}`);
        }
    }

    elementos.push(
        new Paragraph({
            text: 'Figura 1. Estructura de directorios del plugin local_jobboard',
            style: 'PieIlustracion',
            spacing: { before: 100, after: 300 }
        })
    );

    return elementos;
}

/**
 * Genera la seccion de arquitectura
 */
async function generarArquitectura(data) {
    const elementos = [];

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
                    text: 'El plugin implementa una arquitectura modular que separa las responsabilidades en capas: presentacion (paginas y formularios), logica de negocio (clases), acceso a datos (base de datos), y servicios web (APIs externas). Esta arquitectura facilita el mantenimiento, testing y escalabilidad del sistema.',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Insertar imagen de arquitectura
    const svgPath = `${SVG_BASE}/arquitectura.svg`;
    if (existeSVG(svgPath)) {
        try {
            const imagenParrafo = await crearParrafoConImagen(svgPath, { width: 500 });
            elementos.push(imagenParrafo);
        } catch (error) {
            console.warn(`No se pudo cargar imagen: ${svgPath}`);
        }
    }

    elementos.push(
        new Paragraph({
            text: 'Figura 2. Arquitectura del plugin local_jobboard',
            style: 'PieIlustracion',
            spacing: { before: 100, after: 300 }
        })
    );

    // Casos de uso
    elementos.push(
        new Paragraph({
            text: 'Casos de Uso',
            heading: HeadingLevel.HEADING_3,
            spacing: { before: 200, after: 100 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El sistema define cinco tipos de actores principales: Docente/Aspirante, Revisor de Documentos, Administrador, Decano de Facultad y Recursos Humanos. Cada actor tiene acceso a un conjunto especifico de funcionalidades del sistema.',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    const casosUsoPath = `${SVG_BASE}/casos_uso.svg`;
    if (existeSVG(casosUsoPath)) {
        try {
            const imagenParrafo = await crearParrafoConImagen(casosUsoPath, { width: 520 });
            elementos.push(imagenParrafo);
        } catch (error) {
            console.warn(`No se pudo cargar imagen: ${casosUsoPath}`);
        }
    }

    elementos.push(
        new Paragraph({
            text: 'Figura 3. Diagrama de casos de uso',
            style: 'PieIlustracion',
            spacing: { before: 100, after: 300 }
        })
    );

    return elementos;
}

/**
 * Genera la seccion de base de datos
 */
async function generarBaseDatos(data) {
    const elementos = [];

    elementos.push(
        new Paragraph({
            text: 'Base de Datos',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El plugin utiliza ',
                    size: 24
                }),
                new TextRun({
                    text: data.database.tables.length.toString(),
                    size: 24,
                    bold: true,
                    color: COLORS.VERDE
                }),
                new TextRun({
                    text: ' tablas para almacenar la informacion de vacantes, postulaciones, documentos y configuraciones. Todas las tablas siguen las convenciones de nomenclatura de Moodle con el prefijo ',
                    size: 24
                }),
                new TextRun({
                    text: 'mdl_local_jobboard_',
                    size: 24,
                    font: 'Courier New',
                    color: COLORS.GRIS
                }),
                new TextRun({
                    text: '.',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Tabla de resumen de tablas
    elementos.push(
        new Paragraph({
            text: 'Tablas del Sistema',
            heading: HeadingLevel.HEADING_3,
            spacing: { before: 200, after: 100 }
        })
    );

    const tablasData = [['Tabla', 'Descripcion', 'Campos Clave']];

    for (const tabla of data.database.tables) {
        const camposClave = tabla.fields
            .filter(f => ['id', 'code', 'userid', 'vacancyid', 'applicationid', 'status'].includes(f.name))
            .map(f => f.name)
            .join(', ');

        tablasData.push([
            tabla.name.replace('local_jobboard_', ''),
            tabla.comment || 'Sin descripcion',
            camposClave
        ]);
    }

    elementos.push(
        crearTabla(tablasData, [25, 45, 30])
    );

    elementos.push(
        new Paragraph({
            text: `Tabla 2. Resumen de tablas de base de datos`,
            style: 'PieTabla',
            spacing: { before: 100, after: 300 }
        })
    );

    // Diagrama ER
    elementos.push(
        new Paragraph({
            text: 'Diagrama Entidad-Relacion',
            heading: HeadingLevel.HEADING_3,
            spacing: { before: 200, after: 100 }
        })
    );

    const erPath = `${SVG_BASE}/diagrama_er.svg`;
    if (existeSVG(erPath)) {
        try {
            const imagenParrafo = await crearParrafoConImagen(erPath, { width: 550 });
            elementos.push(imagenParrafo);
        } catch (error) {
            console.warn(`No se pudo cargar imagen: ${erPath}`);
        }
    }

    elementos.push(
        new Paragraph({
            text: 'Figura 4. Diagrama entidad-relacion del plugin local_jobboard',
            style: 'PieIlustracion',
            spacing: { before: 100, after: 300 }
        })
    );

    // Detalles de tablas principales
    elementos.push(
        new Paragraph({
            text: 'Detalles de Tablas Principales',
            heading: HeadingLevel.HEADING_3,
            spacing: { before: 200, after: 100 }
        })
    );

    // Tabla vacancy
    const tablaVacancy = data.database.tables.find(t => t.name === 'local_jobboard_vacancy');
    if (tablaVacancy) {
        elementos.push(...generarDetalleTabla('vacancy', tablaVacancy));
    }

    // Tabla application
    const tablaApplication = data.database.tables.find(t => t.name === 'local_jobboard_application');
    if (tablaApplication) {
        elementos.push(...generarDetalleTabla('application', tablaApplication));
    }

    return elementos;
}

/**
 * Genera el detalle de una tabla específica
 */
function generarDetalleTabla(nombre, tabla) {
    const elementos = [];

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: `Tabla: `,
                    size: 24,
                    bold: true
                }),
                new TextRun({
                    text: nombre,
                    size: 24,
                    bold: true,
                    color: COLORS.VERDE,
                    font: 'Courier New'
                })
            ],
            spacing: { before: 200, after: 100 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: tabla.comment || 'Sin descripción',
                    size: 24,
                    italics: true
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 100 }
        })
    );

    // Campos principales (primeros 10 o los más importantes)
    const camposData = [['Campo', 'Tipo', 'Descripción']];

    const camposPrincipales = tabla.fields
        .filter(f => !['timecreated', 'timemodified', 'modifiedby'].includes(f.name))
        .slice(0, 10);

    for (const campo of camposPrincipales) {
        const tipo = campo.length ? `${campo.type}(${campo.length})` : campo.type;
        camposData.push([
            campo.name,
            tipo,
            campo.comment || '-'
        ]);
    }

    elementos.push(
        crearTabla(camposData, [25, 20, 55], true)
    );

    return elementos;
}

/**
 * Genera la seccion de clases principales
 */
async function generarClasesPrincipales(data) {
    const elementos = [];

    elementos.push(
        new Paragraph({
            text: 'Clases Principales',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El plugin implementa un conjunto de clases PHP que encapsulan la logica de negocio del sistema. Las clases principales son ',
                    size: 24
                }),
                new TextRun({
                    text: 'vacancy',
                    size: 24,
                    bold: true,
                    font: 'Courier New',
                    color: COLORS.VERDE
                }),
                new TextRun({
                    text: ' y ',
                    size: 24
                }),
                new TextRun({
                    text: 'application',
                    size: 24,
                    bold: true,
                    font: 'Courier New',
                    color: COLORS.VERDE
                }),
                new TextRun({
                    text: ', que gestionan las vacantes y postulaciones respectivamente.',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Diagrama de clases
    const clasesPath = `${SVG_BASE}/diagrama_clases.svg`;
    if (existeSVG(clasesPath)) {
        try {
            const imagenParrafo = await crearParrafoConImagen(clasesPath, { width: 520 });
            elementos.push(imagenParrafo);
        } catch (error) {
            console.warn(`No se pudo cargar imagen: ${clasesPath}`);
        }
    }

    elementos.push(
        new Paragraph({
            text: 'Figura 5. Diagrama de clases principales',
            style: 'PieIlustracion',
            spacing: { before: 100, after: 300 }
        })
    );

    // Clase vacancy
    if (data.classes && data.classes.vacancy) {
        elementos.push(...generarDetalleClase('vacancy', data.classes.vacancy));
    }

    // Clase application
    if (data.classes && data.classes.application) {
        elementos.push(...generarDetalleClase('application', data.classes.application));
    }

    return elementos;
}

/**
 * Genera el detalle de una clase específica
 */
function generarDetalleClase(nombre, clase) {
    const elementos = [];

    elementos.push(
        new Paragraph({
            text: `Clase ${nombre}`,
            heading: HeadingLevel.HEADING_3,
            spacing: { before: 200, after: 100 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: `Namespace: `,
                    size: 24,
                    bold: true
                }),
                new TextRun({
                    text: clase.namespace,
                    size: 24,
                    font: 'Courier New',
                    color: COLORS.GRIS
                })
            ],
            spacing: { after: 50 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: `Archivo: `,
                    size: 24,
                    bold: true
                }),
                new TextRun({
                    text: clase.archivo,
                    size: 24,
                    font: 'Courier New',
                    color: COLORS.GRIS
                })
            ],
            spacing: { after: 100 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: clase.descripcion,
                    size: 24,
                    italics: true
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Constantes principales
    if (clase.constantes) {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: 'Constantes:',
                        size: 24,
                        bold: true,
                        underline: {}
                    })
                ],
                spacing: { before: 100, after: 100 }
            })
        );

        for (const [grupo, constantes] of Object.entries(clase.constantes)) {
            if (Array.isArray(constantes) && constantes.length > 0) {
                const constantesData = [['Constante', 'Valor', 'Descripción']];

                for (const constante of constantes.slice(0, 5)) {
                    constantesData.push([
                        constante.nombre,
                        constante.valor,
                        constante.descripcion
                    ]);
                }

                elementos.push(
                    crearTabla(constantesData, [30, 20, 50], true)
                );
            }
        }
    }

    // Métodos públicos principales
    if (clase.metodos_publicos && clase.metodos_publicos.length > 0) {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: 'Métodos Públicos Principales:',
                        size: 24,
                        bold: true,
                        underline: {}
                    })
                ],
                spacing: { before: 200, after: 100 }
            })
        );

        const metodosData = [['Método', 'Retorno', 'Descripción']];

        // Mostrar solo los métodos más importantes
        const metodosImportantes = clase.metodos_publicos
            .filter(m => !m.nombre.startsWith('get_') || ['get', 'get_list', 'get_statistics'].includes(m.nombre))
            .slice(0, 10);

        for (const metodo of metodosImportantes) {
            const nombreMetodo = metodo.estatico ? `static ${metodo.nombre}()` : `${metodo.nombre}()`;
            metodosData.push([
                nombreMetodo,
                metodo.retorno,
                metodo.descripcion
            ]);
        }

        elementos.push(
            crearTabla(metodosData, [30, 15, 55], true)
        );
    }

    return elementos;
}

/**
 * Genera la seccion de servicios web
 */
async function generarServiciosWeb(data) {
    const elementos = [];

    elementos.push(
        new Paragraph({
            text: 'Servicios Web',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    if (!data.services || data.services.length === 0) {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: 'El plugin no define servicios web externos.',
                        size: 24,
                        italics: true
                    })
                ],
                spacing: { after: 200 }
            })
        );
        return elementos;
    }

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El plugin expone ',
                    size: 24
                }),
                new TextRun({
                    text: data.services.length.toString(),
                    size: 24,
                    bold: true,
                    color: COLORS.VERDE
                }),
                new TextRun({
                    text: ' servicios web (Web Services) que permiten la interaccion con el sistema mediante AJAX y APIs externas. Estos servicios estan protegidos por capabilities y requieren autenticacion.',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Diagrama de servicios web
    const serviciosPath = `${SVG_BASE}/servicios_web.svg`;
    if (existeSVG(serviciosPath)) {
        try {
            const imagenParrafo = await crearParrafoConImagen(serviciosPath, { width: 480 });
            elementos.push(imagenParrafo);
        } catch (error) {
            console.warn(`No se pudo cargar imagen: ${serviciosPath}`);
        }
    }

    elementos.push(
        new Paragraph({
            text: 'Figura 6. Servicios web disponibles',
            style: 'PieIlustracion',
            spacing: { before: 100, after: 300 }
        })
    );

    // Tabla de servicios
    const serviciosData = [['Función', 'Tipo', 'Descripción', 'Capability Requerido']];

    for (const servicio of data.services) {
        serviciosData.push([
            servicio.function_name,
            servicio.type,
            servicio.description,
            servicio.capabilities || 'N/A'
        ]);
    }

    elementos.push(
        crearTabla(serviciosData, [30, 10, 40, 20])
    );

    elementos.push(
        new Paragraph({
            text: `Tabla 3. Servicios web disponibles`,
            style: 'PieTabla',
            spacing: { before: 100, after: 300 }
        })
    );

    return elementos;
}

/**
 * Genera la sección de capabilities y permisos
 */
function generarCapabilities(data) {
    const elementos = [];

    elementos.push(
        new Paragraph({
            text: 'Capabilities y Permisos',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    if (!data.capabilities || data.capabilities.length === 0) {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: 'No se encontró información de capabilities.',
                        size: 24,
                        italics: true
                    })
                ],
                spacing: { after: 200 }
            })
        );
        return elementos;
    }

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El plugin define un sistema de permisos basado en capabilities de Moodle que controlan el acceso a las diferentes funcionalidades. Estas capabilities se asignan a roles específicos (guest, user, teacher, manager, etc.) y permiten un control granular de accesos.',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Generar tabla de capabilities por categoría
    for (const categoria of data.capabilities) {
        if (!categoria.capabilities || categoria.capabilities.length === 0) continue;

        elementos.push(
            new Paragraph({
                text: categoria.name,
                heading: HeadingLevel.HEADING_3,
                spacing: { before: 200, after: 100 }
            })
        );

        const capabilitiesData = [['Capability', 'Tipo', 'Descripción']];

        for (const cap of categoria.capabilities) {
            capabilitiesData.push([
                cap.name,
                cap.type,
                cap.description
            ]);
        }

        elementos.push(
            crearTabla(capabilitiesData, [40, 15, 45])
        );
    }

    return elementos;
}

/**
 * Genera la sección de flujos de trabajo
 */
async function generarFlujosTrabalho(data) {
    const elementos = [];

    elementos.push(
        new Paragraph({
            text: 'Flujos de Trabajo',
            heading: HeadingLevel.HEADING_2,
            spacing: { before: 300, after: 150 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El plugin implementa varios flujos de trabajo que guían los procesos de gestión de vacantes y postulaciones. Estos flujos definen los estados posibles y las transiciones permitidas entre ellos.',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Flujo de estados de vacante
    elementos.push(
        new Paragraph({
            text: 'Flujo de Estados de Vacante',
            heading: HeadingLevel.HEADING_3,
            spacing: { before: 200, after: 100 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Las vacantes pasan por diferentes estados desde su creación hasta su finalización. El flujo incluye estados como borrador, publicada, cerrada y completada.',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Insertar imagen SVG de flujo de estados de vacante
    const flujoVacantePath = `${SVG_BASE}/flujo_estados_vacante.svg`;
    if (existeSVG(flujoVacantePath)) {
        try {
            const imagenParrafo = await crearParrafoConImagen(flujoVacantePath, { width: 480 });
            elementos.push(imagenParrafo);
        } catch (error) {
            console.warn(`No se pudo cargar imagen: ${flujoVacantePath}`, error.message);
        }
    }

    elementos.push(
        new Paragraph({
            text: 'Figura 6. Flujo de estados de vacante',
            style: 'PieIlustracion',
            spacing: { before: 100, after: 300 }
        })
    );

    // Flujo de postulación
    elementos.push(
        new Paragraph({
            text: 'Flujo de Postulación',
            heading: HeadingLevel.HEADING_3,
            spacing: { before: 200, after: 100 }
        })
    );

    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'El proceso de postulación incluye múltiples etapas desde el envío inicial hasta la selección final. Incluye revisión de documentos, entrevistas y decisión final.',
                    size: 24
                })
            ],
            alignment: AlignmentType.JUSTIFIED,
            spacing: { after: 200 }
        })
    );

    // Insertar imagen SVG de flujo de postulación
    const flujoPostulacionPath = `${SVG_BASE}/flujo_postulacion.svg`;
    if (existeSVG(flujoPostulacionPath)) {
        try {
            const imagenParrafo = await crearParrafoConImagen(flujoPostulacionPath, { width: 480 });
            elementos.push(imagenParrafo);
        } catch (error) {
            console.warn(`No se pudo cargar imagen: ${flujoPostulacionPath}`, error.message);
        }
    }

    elementos.push(
        new Paragraph({
            text: 'Figura 7. Flujo de postulación',
            style: 'PieIlustracion',
            spacing: { before: 100, after: 300 }
        })
    );

    return elementos;
}

/**
 * Función auxiliar para crear tablas con estilos consistentes
 */
function crearTabla(datos, anchosColumnas = null, estiloCompacto = false) {
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
                                size: estiloCompacto ? 20 : 22,
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

// Exportación por defecto
export default generarSeccionesJobboard;

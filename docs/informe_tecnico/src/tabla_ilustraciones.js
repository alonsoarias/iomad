/**
 * Generador de tabla de ilustraciones
 * Lista todas las figuras del documento con formato ICONTEC
 */

import { Paragraph, TextRun, AlignmentType, TabStopPosition, TabStopType } from 'docx';
import { COLORS } from './styles.js';

// Lista de todas las ilustraciones del documento
const ILUSTRACIONES = [
    { numero: 1, titulo: 'Estructura de directorios del plugin local_jobboard', pagina: 8 },
    { numero: 2, titulo: 'Arquitectura del plugin local_jobboard', pagina: 9 },
    { numero: 3, titulo: 'Diagrama de casos de uso', pagina: 10 },
    { numero: 4, titulo: 'Diagrama entidad-relación del plugin local_jobboard', pagina: 12 },
    { numero: 5, titulo: 'Diagrama de clases principales', pagina: 14 },
    { numero: 6, titulo: 'Servicios web disponibles', pagina: 16 },
    { numero: 7, titulo: 'Flujo de estados de vacante', pagina: 18 },
    { numero: 8, titulo: 'Flujo de postulación', pagina: 19 },
    { numero: 9, titulo: 'Ciclo de vida de la aplicación', pagina: 20 },
    { numero: 10, titulo: 'Estructura de directorios del plugin report_platform_usage', pagina: 22 },
    { numero: 11, titulo: 'Diagrama de arquitectura - Platform Usage Report', pagina: 24 },
    { numero: 12, titulo: 'Diagrama de flujo de datos - Platform Usage Report', pagina: 28 },
    { numero: 13, titulo: 'Estructura de directorios del plugin local_platform_access', pagina: 32 },
    { numero: 14, titulo: 'Diagrama de arquitectura - Platform Access Generator', pagina: 34 },
    { numero: 15, titulo: 'Diagrama de flujo de generación - Platform Access Generator', pagina: 38 }
];

/**
 * Genera la lista de ilustraciones del documento
 * @returns {Array<Paragraph>} Arreglo con la lista de ilustraciones
 */
export default function generarTablaIlustraciones() {
    const elementos = [];

    // Título de la sección
    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'LISTA DE ILUSTRACIONES',
                    bold: true,
                    size: 28,
                    color: COLORS.VERDE,
                    allCaps: true,
                    font: 'Arial'
                })
            ],
            alignment: AlignmentType.CENTER,
            spacing: { before: 400, after: 400 }
        })
    );

    // Encabezado de columnas
    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Figura',
                    bold: true,
                    size: 22,
                    font: 'Arial'
                }),
                new TextRun({
                    text: '\t',
                    size: 22
                }),
                new TextRun({
                    text: 'Título',
                    bold: true,
                    size: 22,
                    font: 'Arial'
                }),
                new TextRun({
                    text: '\t',
                    size: 22
                }),
                new TextRun({
                    text: 'Pág.',
                    bold: true,
                    size: 22,
                    font: 'Arial'
                })
            ],
            tabStops: [
                { type: TabStopType.LEFT, position: 1200 },
                { type: TabStopType.RIGHT, position: 9000 }
            ],
            spacing: { before: 200, after: 150 },
            border: {
                bottom: { color: COLORS.VERDE, space: 1, style: 'single', size: 8 }
            }
        })
    );

    // Lista de ilustraciones
    for (const ilustracion of ILUSTRACIONES) {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `Figura ${ilustracion.numero}`,
                        size: 22,
                        font: 'Arial',
                        color: COLORS.VERDE
                    }),
                    new TextRun({
                        text: '\t',
                        size: 22
                    }),
                    new TextRun({
                        text: ilustracion.titulo,
                        size: 22,
                        font: 'Arial'
                    }),
                    new TextRun({
                        text: '\t',
                        size: 22
                    }),
                    new TextRun({
                        text: ilustracion.pagina.toString(),
                        size: 22,
                        font: 'Arial'
                    })
                ],
                tabStops: [
                    { type: TabStopType.LEFT, position: 1200 },
                    { type: TabStopType.RIGHT, position: 9000, leader: 'dot' }
                ],
                spacing: { before: 80, after: 80 }
            })
        );
    }

    // Nota al pie
    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: `Total de ilustraciones: ${ILUSTRACIONES.length}`,
                    size: 20,
                    font: 'Arial',
                    italics: true,
                    color: COLORS.GRIS
                })
            ],
            alignment: AlignmentType.RIGHT,
            spacing: { before: 300, after: 200 }
        })
    );

    return elementos;
}

/**
 * Generador de tabla de tablas
 * Lista todas las tablas del documento con formato ICONTEC
 */

import { Paragraph, TextRun, AlignmentType, TabStopType } from 'docx';
import { COLORS } from './styles.js';

// Lista de todas las tablas del documento
const TABLAS = [
    { numero: 1, titulo: 'Tecnologías utilizadas en los plugins', pagina: 7 },
    { numero: 2, titulo: 'Información de versión del plugin local_jobboard', pagina: 8 },
    { numero: 3, titulo: 'Resumen de tablas de base de datos', pagina: 11 },
    { numero: 4, titulo: 'Servicios web disponibles', pagina: 17 },
    { numero: 5, titulo: 'Información de versión - Platform Usage Report', pagina: 23 },
    { numero: 6, titulo: 'Tabla report_platform_usage_summary', pagina: 25 },
    { numero: 7, titulo: 'Capabilities del plugin Platform Usage Report', pagina: 27 },
    { numero: 8, titulo: 'Información de versión - Platform Access Generator', pagina: 33 },
    { numero: 9, titulo: 'Capabilities del plugin Platform Access Generator', pagina: 36 },
    { numero: 10, titulo: 'Técnicas de optimización implementadas', pagina: 39 }
];

/**
 * Genera la lista de tablas del documento
 * @returns {Array<Paragraph>} Arreglo con la lista de tablas
 */
export default function generarTablaTablas() {
    const elementos = [];

    // Título de la sección
    elementos.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'LISTA DE TABLAS',
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
                    text: 'Tabla',
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

    // Lista de tablas
    for (const tabla of TABLAS) {
        elementos.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: `Tabla ${tabla.numero}`,
                        size: 22,
                        font: 'Arial',
                        color: COLORS.VERDE
                    }),
                    new TextRun({
                        text: '\t',
                        size: 22
                    }),
                    new TextRun({
                        text: tabla.titulo,
                        size: 22,
                        font: 'Arial'
                    }),
                    new TextRun({
                        text: '\t',
                        size: 22
                    }),
                    new TextRun({
                        text: tabla.pagina.toString(),
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
                    text: `Total de tablas: ${TABLAS.length}`,
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

/**
 * Generador de tabla de tablas
 * Placeholder - Será implementado cuando se agreguen las tablas al documento
 */

import { Paragraph, TextRun, AlignmentType } from 'docx';
import { COLORS } from './styles.js';

/**
 * Genera la lista de tablas del documento
 * @returns {Array<Paragraph>} Arreglo con el título de la lista de tablas
 */
export default function generarTablaTablas() {
    const tabla = [];

    // Título de la sección
    tabla.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'LISTA DE TABLAS',
                    bold: true,
                    size: 28, // 14pt
                    color: COLORS.ROJO,
                    allCaps: true,
                    font: 'Arial'
                })
            ],
            alignment: AlignmentType.CENTER,
            spacing: { before: 400, after: 400 }
        })
    );

    // Placeholder - Se implementará con las tablas reales
    tabla.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Las tablas serán listadas automáticamente en la versión final del documento.',
                    size: 22,
                    color: COLORS.GRIS,
                    italics: true,
                    font: 'Arial'
                })
            ],
            alignment: AlignmentType.CENTER,
            spacing: { before: 200, after: 400 }
        })
    );

    // TODO: Implementar lista automática de tablas
    // Se puede usar un índice de tablas similar a TableOfContents
    // o generar manualmente la lista con números de página

    return tabla;
}

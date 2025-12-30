/**
 * Generador de tabla de ilustraciones
 * Placeholder - Será implementado cuando se agreguen las ilustraciones al documento
 */

import { Paragraph, TextRun, AlignmentType } from 'docx';
import { COLORS } from './styles.js';

/**
 * Genera la lista de ilustraciones del documento
 * @returns {Array<Paragraph>} Arreglo con el título de la lista de ilustraciones
 */
export default function generarTablaIlustraciones() {
    const tabla = [];

    // Título de la sección
    tabla.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'LISTA DE ILUSTRACIONES',
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

    // Placeholder - Se implementará con las ilustraciones reales
    tabla.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Las ilustraciones serán listadas automáticamente en la versión final del documento.',
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

    // TODO: Implementar lista automática de figuras
    // Se puede usar un índice de figuras similar a TableOfContents
    // o generar manualmente la lista con números de página

    return tabla;
}

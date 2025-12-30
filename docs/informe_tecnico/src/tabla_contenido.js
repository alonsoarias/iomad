/**
 * Generador de tabla de contenido
 * Usa TableOfContents de docx para navegación automática en Word
 */

import { Paragraph, TextRun, TableOfContents, AlignmentType, PageBreak } from 'docx';
import { COLORS } from './styles.js';

/**
 * Genera la tabla de contenido del documento
 * @returns {Array<Paragraph>} Arreglo con el título y la tabla de contenido
 */
export default function generarTablaContenido() {
    const tabla = [];

    // Título de la sección
    tabla.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'TABLA DE CONTENIDO',
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

    // Tabla de contenido automática
    // Configura hasta 3 niveles de títulos (Heading1, Heading2, Heading3)
    tabla.push(
        new TableOfContents('Tabla de Contenido', {
            hyperlink: true,
            headingStyleRange: '1-3',
            stylesWithLevels: [
                { styleName: 'Titulo1', level: 1 },
                { styleName: 'Titulo2', level: 2 },
                { styleName: 'Titulo3', level: 3 }
            ]
        })
    );

    // Instrucción para actualizar en Word
    tabla.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: '',
                    size: 1
                })
            ],
            spacing: { after: 200 }
        })
    );

    tabla.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Nota: Para actualizar esta tabla de contenido en Word, haga clic derecho sobre ella y seleccione "Actualizar campos".',
                    size: 20, // 10pt
                    color: COLORS.GRIS,
                    italics: true,
                    font: 'Arial'
                })
            ],
            alignment: AlignmentType.LEFT,
            spacing: { before: 400, after: 200 }
        })
    );

    return tabla;
}

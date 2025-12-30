/**
 * Estilos ICONTEC para el informe técnico
 * Normas NTC 1486
 */

import { AlignmentType, HeadingLevel, convertInchesToTwip } from 'docx';

// Colores corporativos ISER
export const COLORS = {
    VERDE: '1B9E88',
    AMARILLO: 'FCBD05',
    ROJO: 'EB4335',
    GRIS: '646363',
    NEGRO: '000000',
    BLANCO: 'FFFFFF'
};

// Márgenes según ICONTEC (en twips: 1 pulgada = 1440 twips)
export const MARGINS = {
    TOP: convertInchesToTwip(1.97),      // 5cm
    BOTTOM: convertInchesToTwip(1.18),   // 3cm
    LEFT: convertInchesToTwip(1.18),     // 3cm
    RIGHT: convertInchesToTwip(1.18)     // 3cm
};

// Estilos de documento
export const DOCUMENT_STYLES = {
    default: {
        document: {
            run: {
                font: 'Arial',
                size: 24  // 12pt (size en half-points)
            },
            paragraph: {
                spacing: {
                    line: 360  // 1.5 líneas (240 = single, 360 = 1.5, 480 = double)
                }
            }
        }
    },
    paragraphStyles: [
        {
            id: 'Normal',
            name: 'Normal',
            run: {
                font: 'Arial',
                size: 24,
                color: COLORS.NEGRO
            },
            paragraph: {
                spacing: { line: 360, after: 200 },
                alignment: AlignmentType.JUSTIFIED
            }
        },
        {
            id: 'Titulo1',
            name: 'Título 1',
            basedOn: 'Normal',
            next: 'Normal',
            quickFormat: true,
            run: {
                font: 'Arial',
                size: 28,  // 14pt
                bold: true,
                color: COLORS.ROJO,
                allCaps: true
            },
            paragraph: {
                spacing: { before: 400, after: 200 },
                alignment: AlignmentType.LEFT
            }
        },
        {
            id: 'Titulo2',
            name: 'Título 2',
            basedOn: 'Normal',
            next: 'Normal',
            quickFormat: true,
            run: {
                font: 'Arial',
                size: 24,  // 12pt
                bold: true,
                color: COLORS.VERDE
            },
            paragraph: {
                spacing: { before: 300, after: 150 },
                alignment: AlignmentType.LEFT
            }
        },
        {
            id: 'Titulo3',
            name: 'Título 3',
            basedOn: 'Normal',
            next: 'Normal',
            quickFormat: true,
            run: {
                font: 'Arial',
                size: 24,  // 12pt
                bold: true,
                color: COLORS.GRIS
            },
            paragraph: {
                spacing: { before: 200, after: 100 },
                alignment: AlignmentType.LEFT
            }
        },
        {
            id: 'PieIlustracion',
            name: 'Pie de Ilustración',
            run: {
                font: 'Arial',
                size: 20,  // 10pt
                italics: true,
                color: COLORS.GRIS
            },
            paragraph: {
                spacing: { before: 100, after: 200 },
                alignment: AlignmentType.CENTER
            }
        },
        {
            id: 'PieTabla',
            name: 'Pie de Tabla',
            run: {
                font: 'Arial',
                size: 20,  // 10pt
                bold: true,
                color: COLORS.NEGRO
            },
            paragraph: {
                spacing: { before: 100, after: 100 },
                alignment: AlignmentType.LEFT
            }
        }
    ]
};

// Estilos para tablas
export const TABLE_STYLES = {
    header: {
        fill: COLORS.VERDE,
        color: COLORS.BLANCO,
        bold: true
    },
    cell: {
        color: COLORS.NEGRO
    },
    borders: {
        color: COLORS.GRIS,
        size: 1
    }
};

export default { COLORS, MARGINS, DOCUMENT_STYLES, TABLE_STYLES };

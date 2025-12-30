/**
 * Generador de portada institucional ISER
 * Informe Técnico - Plugins Moodle
 */

import { Paragraph, TextRun, ImageRun, AlignmentType, VerticalAlign } from 'docx';
import { readFileSync } from 'fs';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';
import { COLORS } from './styles.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

/**
 * Genera la portada institucional del informe técnico
 * @returns {Array<Paragraph>} Arreglo de párrafos que conforman la portada
 */
export default function generarPortada() {
    // Leer logo ISER
    const logoPath = join(__dirname, '../images/logo_iser.png');
    let logoImage = null;

    try {
        const imageBuffer = readFileSync(logoPath);
        logoImage = new ImageRun({
            data: imageBuffer,
            transformation: {
                width: 150,
                height: 150
            }
        });
    } catch (error) {
        console.warn('No se pudo cargar el logo ISER:', error.message);
    }

    const portada = [];

    // Logo institucional (centrado, arriba)
    if (logoImage) {
        portada.push(
            new Paragraph({
                children: [logoImage],
                alignment: AlignmentType.CENTER,
                spacing: { before: 1440, after: 400 } // 1 pulgada arriba, espacio abajo
            })
        );
    }

    // Nombre de la institución
    portada.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'INSTITUTO SUPERIOR DE EDUCACIÓN RURAL',
                    bold: true,
                    size: 28, // 14pt
                    color: COLORS.VERDE,
                    font: 'Arial'
                })
            ],
            alignment: AlignmentType.CENTER,
            spacing: { after: 100 }
        })
    );

    portada.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'ISER',
                    bold: true,
                    size: 28,
                    color: COLORS.VERDE,
                    font: 'Arial'
                })
            ],
            alignment: AlignmentType.CENTER,
            spacing: { after: 800 }
        })
    );

    // Título principal
    portada.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'INFORME TÉCNICO',
                    bold: true,
                    size: 36, // 18pt
                    color: COLORS.ROJO,
                    allCaps: true,
                    font: 'Arial'
                })
            ],
            alignment: AlignmentType.CENTER,
            spacing: { after: 400 }
        })
    );

    // Subtítulo
    portada.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Plugins Moodle - Plataforma Virtual ISER',
                    bold: true,
                    size: 28, // 14pt
                    color: COLORS.GRIS,
                    font: 'Arial'
                })
            ],
            alignment: AlignmentType.CENTER,
            spacing: { after: 200 }
        })
    );

    // URL de la plataforma
    portada.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'https://virtual.iser.edu.co/',
                    size: 24, // 12pt
                    color: COLORS.VERDE,
                    font: 'Arial',
                    italics: true
                })
            ],
            alignment: AlignmentType.CENTER,
            spacing: { after: 1200 } // Espacio considerable
        })
    );

    // Ubicación y año (parte inferior)
    portada.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: 'Pamplona, Norte de Santander',
                    size: 24,
                    color: COLORS.GRIS,
                    font: 'Arial'
                })
            ],
            alignment: AlignmentType.CENTER,
            spacing: { after: 100 }
        })
    );

    portada.push(
        new Paragraph({
            children: [
                new TextRun({
                    text: '2025',
                    bold: true,
                    size: 28,
                    color: COLORS.ROJO,
                    font: 'Arial'
                })
            ],
            alignment: AlignmentType.CENTER,
            spacing: { after: 400 }
        })
    );

    return portada;
}

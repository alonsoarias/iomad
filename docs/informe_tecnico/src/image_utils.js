/**
 * Utilidades para manejo de imagenes en el informe tecnico
 * Convierte SVG a PNG y genera elementos ImageRun para docx
 */

import sharp from 'sharp';
import { readFile, mkdir, writeFile } from 'fs/promises';
import { existsSync } from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import {
    Paragraph,
    ImageRun,
    AlignmentType,
    TextRun,
    SimpleField,
    BookmarkStart,
    BookmarkEnd
} from 'docx';
import { COLORS } from './styles.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Directorio para imagenes convertidas
const CACHE_DIR = path.join(__dirname, '..', 'cache_images');

/**
 * Convierte un archivo SVG a PNG
 * @param {string} svgPath - Ruta al archivo SVG
 * @param {number} width - Ancho deseado (opcional, default 600)
 * @returns {Promise<Buffer>} Buffer con la imagen PNG
 */
export async function svgToPng(svgPath, width = 600) {
    try {
        const svgBuffer = await readFile(svgPath);

        // Usar sharp para convertir SVG a PNG
        const pngBuffer = await sharp(svgBuffer)
            .resize({ width: width, fit: 'contain', background: { r: 255, g: 255, b: 255, alpha: 1 } })
            .png()
            .toBuffer();

        return pngBuffer;
    } catch (error) {
        console.error(`Error convirtiendo SVG a PNG: ${svgPath}`, error.message);
        throw error;
    }
}

/**
 * Crea un elemento ImageRun de docx desde un archivo SVG
 * @param {string} svgPath - Ruta al archivo SVG (relativa o absoluta)
 * @param {Object} options - Opciones de la imagen
 * @param {number} options.width - Ancho en puntos (default 550)
 * @param {number} options.height - Alto en puntos (opcional, se calcula automaticamente)
 * @returns {Promise<ImageRun>} Elemento ImageRun para docx
 */
export async function crearImagenDesdeSVG(svgPath, options = {}) {
    const {
        width = 550,
        height = null
    } = options;

    // Resolver ruta absoluta si es relativa
    let fullPath = svgPath;
    if (!path.isAbsolute(svgPath)) {
        fullPath = path.join(__dirname, '..', svgPath);
    }

    // Convertir SVG a PNG
    const pngBuffer = await svgToPng(fullPath, Math.round(width * 1.5)); // Mayor resolucion para calidad

    // Obtener dimensiones de la imagen
    const metadata = await sharp(pngBuffer).metadata();

    // Calcular alto proporcional si no se especifica
    const imgWidth = width;
    const imgHeight = height || Math.round((metadata.height / metadata.width) * width);

    return new ImageRun({
        data: pngBuffer,
        transformation: {
            width: imgWidth,
            height: imgHeight
        },
        type: 'png'
    });
}

/**
 * Crea un parrafo con una imagen centrada
 * @param {string} svgPath - Ruta al archivo SVG
 * @param {Object} options - Opciones
 * @param {number} options.width - Ancho de la imagen (default 550)
 * @returns {Promise<Paragraph>} Parrafo con la imagen
 */
export async function crearParrafoConImagen(svgPath, options = {}) {
    const imagen = await crearImagenDesdeSVG(svgPath, options);

    return new Paragraph({
        children: [imagen],
        alignment: AlignmentType.CENTER,
        spacing: { before: 200, after: 100 }
    });
}

/**
 * Crea multiples imagenes desde un array de rutas SVG
 * @param {Array<string>} svgPaths - Array de rutas SVG
 * @param {Object} options - Opciones para todas las imagenes
 * @returns {Promise<Array<Paragraph>>} Array de parrafos con imagenes
 */
export async function crearImagenesMultiples(svgPaths, options = {}) {
    const imagenes = [];

    for (const svgPath of svgPaths) {
        try {
            const parrafo = await crearParrafoConImagen(svgPath, options);
            imagenes.push(parrafo);
        } catch (error) {
            console.warn(`Advertencia: No se pudo cargar imagen ${svgPath}: ${error.message}`);
        }
    }

    return imagenes;
}

/**
 * Verifica si un archivo SVG existe
 * @param {string} svgPath - Ruta al archivo
 * @returns {boolean}
 */
export function existeSVG(svgPath) {
    let fullPath = svgPath;
    if (!path.isAbsolute(svgPath)) {
        fullPath = path.join(__dirname, '..', svgPath);
    }
    return existsSync(fullPath);
}

/**
 * Lista todos los SVG disponibles en un directorio
 * @param {string} plugin - Nombre del plugin (local_jobboard, report_platform_usage, local_platform_access)
 * @returns {Promise<Array<string>>} Array de rutas SVG
 */
export async function listarSVGsPlugin(plugin) {
    const { readdir } = await import('fs/promises');
    const svgDir = path.join(__dirname, '..', 'svg', plugin);

    try {
        const archivos = await readdir(svgDir);
        return archivos
            .filter(f => f.endsWith('.svg'))
            .map(f => path.join(svgDir, f));
    } catch (error) {
        console.warn(`No se pudo leer directorio SVG para ${plugin}: ${error.message}`);
        return [];
    }
}

// Contador global para bookmarks unicos
let bookmarkCounter = 0;

/**
 * Crea un parrafo de titulo de figura con campo SEQ para la tabla de ilustraciones
 * @param {string} titulo - Titulo de la figura (sin "Figura X. ")
 * @returns {Paragraph} Parrafo con el titulo de la figura
 */
export function crearTituloFigura(titulo) {
    bookmarkCounter++;
    const bookmarkId = `_Ref_Figura_${bookmarkCounter}`;

    return new Paragraph({
        children: [
            new BookmarkStart({ id: bookmarkId, name: bookmarkId }),
            new TextRun({
                text: 'Figura ',
                size: 20,
                italics: true,
                color: COLORS.GRIS,
                font: 'Arial'
            }),
            new SimpleField({ instruction: 'SEQ Figura \\* ARABIC' }),
            new TextRun({
                text: '. ',
                size: 20,
                italics: true,
                color: COLORS.GRIS,
                font: 'Arial'
            }),
            new TextRun({
                text: titulo,
                size: 20,
                italics: true,
                color: COLORS.GRIS,
                font: 'Arial'
            }),
            new BookmarkEnd({ id: bookmarkId })
        ],
        alignment: AlignmentType.CENTER,
        spacing: { before: 100, after: 300 }
    });
}

/**
 * Crea un parrafo de titulo de tabla con campo SEQ para la tabla de tablas
 * @param {string} titulo - Titulo de la tabla (sin "Tabla X. ")
 * @returns {Paragraph} Parrafo con el titulo de la tabla
 */
export function crearTituloTabla(titulo) {
    bookmarkCounter++;
    const bookmarkId = `_Ref_Tabla_${bookmarkCounter}`;

    return new Paragraph({
        children: [
            new BookmarkStart({ id: bookmarkId, name: bookmarkId }),
            new TextRun({
                text: 'Tabla ',
                size: 20,
                bold: true,
                color: COLORS.NEGRO,
                font: 'Arial'
            }),
            new SimpleField({ instruction: 'SEQ Tabla \\* ARABIC' }),
            new TextRun({
                text: '. ',
                size: 20,
                bold: true,
                color: COLORS.NEGRO,
                font: 'Arial'
            }),
            new TextRun({
                text: titulo,
                size: 20,
                bold: true,
                color: COLORS.NEGRO,
                font: 'Arial'
            }),
            new BookmarkEnd({ id: bookmarkId })
        ],
        alignment: AlignmentType.LEFT,
        spacing: { before: 100, after: 100 }
    });
}

/**
 * Crea un parrafo con imagen y su titulo con SEQ
 * @param {string} svgPath - Ruta al archivo SVG
 * @param {string} titulo - Titulo de la figura
 * @param {Object} options - Opciones de la imagen
 * @returns {Promise<Array<Paragraph>>} Array con parrafo de imagen y titulo
 */
export async function crearFiguraConTitulo(svgPath, titulo, options = {}) {
    const elementos = [];

    try {
        const imagenParrafo = await crearParrafoConImagen(svgPath, options);
        elementos.push(imagenParrafo);
    } catch (error) {
        console.warn(`No se pudo cargar imagen: ${svgPath}`, error.message);
        return elementos;
    }

    elementos.push(crearTituloFigura(titulo));

    return elementos;
}

export default {
    svgToPng,
    crearImagenDesdeSVG,
    crearParrafoConImagen,
    crearImagenesMultiples,
    existeSVG,
    listarSVGsPlugin,
    crearTituloFigura,
    crearTituloTabla,
    crearFiguraConTitulo
};

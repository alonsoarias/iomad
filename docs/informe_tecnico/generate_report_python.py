#!/usr/bin/env python3
"""
Generador de Informe Técnico - Formato F-GCT-17 ISER
Usa python-docx para editar el documento plantilla existente

Uso:
    python3 generate_report_python.py
"""

import json
import os
from pathlib import Path
from io import BytesIO

from docx import Document
from docx.shared import Inches, Pt, Cm, Twips
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.style import WD_STYLE_TYPE
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml.ns import qn, nsmap
from docx.oxml import OxmlElement
from PIL import Image
import cairosvg

# Rutas base
BASE_DIR = Path(__file__).parent
TEMPLATE_PATH = BASE_DIR / 'output' / 'F-GCT-17-Informe-Contratistas-v02-1.docx'
OUTPUT_PATH = BASE_DIR / 'output' / 'INFORME_TECNICO_FGCT17_2025.docx'
DATA_DIR = BASE_DIR / 'data'
SVG_DIR = BASE_DIR / 'svg'
IMAGES_DIR = BASE_DIR / 'images'

# Colores corporativos ISER (RGB)
COLORS = {
    'VERDE': (27, 158, 136),
    'AMARILLO': (252, 189, 5),
    'ROJO': (235, 67, 53),
    'GRIS': (100, 99, 99),
    'NEGRO': (0, 0, 0),
    'BLANCO': (255, 255, 255)
}

# Contadores globales para numeración
figura_counter = 0
tabla_counter = 0


def svg_to_png(svg_path: Path, width: int = 600) -> bytes:
    """Convierte SVG a PNG usando cairosvg."""
    try:
        png_data = cairosvg.svg2png(
            url=str(svg_path),
            output_width=width
        )
        return png_data
    except Exception as e:
        print(f"  ⚠️  Error convirtiendo SVG: {svg_path.name} - {e}")
        return None


def add_caption_field(paragraph, caption_type: str, title: str):
    """
    Agrega un campo de título con SEQ para Word.
    caption_type: 'Figura' o 'Tabla'
    """
    global figura_counter, tabla_counter

    # Incrementar contador
    if caption_type == 'Figura':
        figura_counter += 1
        num = figura_counter
    else:
        tabla_counter += 1
        num = tabla_counter

    # Crear el campo SEQ usando XML directo
    run = paragraph.add_run()

    # Texto inicial
    run.add_text(f"{caption_type} ")

    # Campo SEQ
    fld_char_begin = OxmlElement('w:fldChar')
    fld_char_begin.set(qn('w:fldCharType'), 'begin')

    instr_text = OxmlElement('w:instrText')
    instr_text.set(qn('xml:space'), 'preserve')
    instr_text.text = f' SEQ {caption_type} \\* ARABIC '

    fld_char_separate = OxmlElement('w:fldChar')
    fld_char_separate.set(qn('w:fldCharType'), 'separate')

    # Texto del número (valor actual)
    num_run = OxmlElement('w:r')
    num_text = OxmlElement('w:t')
    num_text.text = str(num)
    num_run.append(num_text)

    fld_char_end = OxmlElement('w:fldChar')
    fld_char_end.set(qn('w:fldCharType'), 'end')

    # Agregar elementos al run
    run._r.append(fld_char_begin)
    run._r.append(instr_text)
    run._r.append(fld_char_separate)
    run._r.append(num_run)
    run._r.append(fld_char_end)

    # Agregar punto y título
    run.add_text(f". {title}")


def add_toc_field(doc, toc_type: str):
    """
    Agrega un campo TOC para tabla de contenido, ilustraciones o tablas.
    toc_type: 'content', 'figure', 'table'
    """
    paragraph = doc.add_paragraph()
    run = paragraph.add_run()

    # Crear campo TOC
    fld_char_begin = OxmlElement('w:fldChar')
    fld_char_begin.set(qn('w:fldCharType'), 'begin')

    instr_text = OxmlElement('w:instrText')
    instr_text.set(qn('xml:space'), 'preserve')

    if toc_type == 'content':
        instr_text.text = ' TOC \\o "1-3" \\h \\z \\u '
    elif toc_type == 'figure':
        instr_text.text = ' TOC \\h \\z \\c "Figura" '
    elif toc_type == 'table':
        instr_text.text = ' TOC \\h \\z \\c "Tabla" '

    fld_char_separate = OxmlElement('w:fldChar')
    fld_char_separate.set(qn('w:fldCharType'), 'separate')

    # Texto placeholder
    placeholder = OxmlElement('w:r')
    placeholder_text = OxmlElement('w:t')
    placeholder_text.text = "Actualice este campo (Ctrl+A, F9)"
    placeholder.append(placeholder_text)

    fld_char_end = OxmlElement('w:fldChar')
    fld_char_end.set(qn('w:fldCharType'), 'end')

    run._r.append(fld_char_begin)
    run._r.append(instr_text)
    run._r.append(fld_char_separate)
    run._r.append(placeholder)
    run._r.append(fld_char_end)

    return paragraph


def add_figure(doc, svg_path: Path, title: str, width_inches: float = 5.5):
    """Agrega una figura con su título usando campos SEQ."""
    if not svg_path.exists():
        print(f"  ⚠️  SVG no encontrado: {svg_path}")
        return

    # Convertir SVG a PNG
    png_data = svg_to_png(svg_path, width=int(width_inches * 150))
    if png_data is None:
        return

    # Agregar imagen centrada
    paragraph = doc.add_paragraph()
    paragraph.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = paragraph.add_run()
    run.add_picture(BytesIO(png_data), width=Inches(width_inches))

    # Agregar título con campo SEQ
    caption = doc.add_paragraph()
    caption.alignment = WD_ALIGN_PARAGRAPH.CENTER
    # Formato de caption (italic, gris)
    add_caption_field(caption, 'Figura', title)
    for run in caption.runs:
        run.italic = True
        run.font.size = Pt(10)


def add_table_with_caption(doc, data: list, title: str, col_widths: list = None):
    """Agrega una tabla con su título usando campos SEQ."""
    # Título de la tabla (antes de la tabla)
    caption = doc.add_paragraph()
    add_caption_field(caption, 'Tabla', title)
    for run in caption.runs:
        run.bold = True
        run.font.size = Pt(10)

    # Crear tabla
    if not data:
        return

    table = doc.add_table(rows=len(data), cols=len(data[0]))
    table.style = 'Table Grid'
    table.alignment = WD_TABLE_ALIGNMENT.CENTER

    # Llenar datos
    for i, row_data in enumerate(data):
        row = table.rows[i]
        for j, cell_data in enumerate(row_data):
            cell = row.cells[j]
            cell.text = str(cell_data) if cell_data else ''

            # Estilo de encabezado
            if i == 0:
                for paragraph in cell.paragraphs:
                    for run in paragraph.runs:
                        run.bold = True

    # Aplicar anchos de columna si se especifican
    if col_widths:
        for i, width in enumerate(col_widths):
            for row in table.rows:
                row.cells[i].width = Inches(width)

    doc.add_paragraph()  # Espacio después de la tabla


def load_json_data(plugin_name: str) -> dict:
    """Carga los datos JSON consolidados de un plugin."""
    json_path = DATA_DIR / plugin_name / 'CONSOLIDADO.json'
    if not json_path.exists():
        print(f"  ⚠️  JSON no encontrado: {json_path}")
        return {}

    with open(json_path, 'r', encoding='utf-8') as f:
        return json.load(f)


def add_heading(doc, text: str, level: int = 1):
    """Agrega un encabezado con formato manual."""
    p = doc.add_paragraph()
    run = p.add_run(text)
    run.bold = True

    # Tamaños según nivel
    sizes = {1: 16, 2: 14, 3: 12}
    run.font.size = Pt(sizes.get(level, 12))

    # Color verde para títulos principales
    if level <= 2:
        run.font.color.rgb = None  # Reset to let Word handle it

    # Configurar como estilo de encabezado para TOC
    try:
        p.style = f'Heading {level}'
    except KeyError:
        # Si no existe el estilo, crear uno básico
        pass

    return p


def add_paragraph(doc, text: str, bold: bool = False, italic: bool = False):
    """Agrega un párrafo."""
    p = doc.add_paragraph()
    run = p.add_run(text)
    run.bold = bold
    run.italic = italic
    return p


def add_bullet_list(doc, items: list):
    """Agrega una lista con viñetas."""
    for item in items:
        p = doc.add_paragraph()
        p.add_run(f'• {item}')
        p.paragraph_format.left_indent = Inches(0.5)


def generate_cover_page(doc):
    """Genera la portada del documento."""
    # Espacio inicial
    for _ in range(3):
        doc.add_paragraph()

    # Título principal
    title = doc.add_paragraph()
    title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = title.add_run('INFORME TÉCNICO')
    run.bold = True
    run.font.size = Pt(24)

    # Subtítulo
    subtitle = doc.add_paragraph()
    subtitle.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = subtitle.add_run('Documentación de Plugins Moodle')
    run.bold = True
    run.font.size = Pt(16)

    # Plataforma
    platform = doc.add_paragraph()
    platform.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = platform.add_run('Plataforma Virtual ISER')
    run.font.size = Pt(14)

    doc.add_paragraph()

    # Plugins
    plugins = ['local_jobboard', 'report_platform_usage', 'local_platform_access']
    for plugin in plugins:
        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        run = p.add_run(f'• {plugin}')
        run.bold = True
        run.font.size = Pt(12)

    # Espacio
    for _ in range(3):
        doc.add_paragraph()

    # Año
    year = doc.add_paragraph()
    year.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = year.add_run('2025')
    run.bold = True
    run.font.size = Pt(18)

    doc.add_page_break()


def generate_toc_pages(doc):
    """Genera las páginas de tablas de contenido."""
    # Tabla de contenido
    add_heading(doc, 'TABLA DE CONTENIDO', 1)
    add_toc_field(doc, 'content')
    doc.add_page_break()

    # Lista de ilustraciones
    add_heading(doc, 'LISTA DE ILUSTRACIONES', 1)
    add_toc_field(doc, 'figure')
    doc.add_page_break()

    # Lista de tablas
    add_heading(doc, 'LISTA DE TABLAS', 1)
    add_toc_field(doc, 'table')
    doc.add_page_break()


def generate_introduction(doc):
    """Genera las secciones introductorias."""
    add_heading(doc, '1. INTRODUCCIÓN', 1)

    add_paragraph(doc,
        'El presente documento técnico describe la implementación, arquitectura y '
        'funcionamiento de tres plugins desarrollados para la plataforma Moodle del '
        'Instituto Superior de Educación Rural (ISER). Estos plugins extienden las '
        'funcionalidades de la plataforma virtual para satisfacer necesidades específicas '
        'de la institución.')

    add_heading(doc, '2. OBJETIVOS', 1)

    add_heading(doc, '2.1 Objetivo General', 2)
    add_paragraph(doc,
        'Documentar técnicamente los plugins desarrollados para la plataforma Moodle ISER, '
        'proporcionando información detallada sobre su arquitectura, funcionamiento e '
        'integración con el sistema.')

    add_heading(doc, '2.2 Objetivos Específicos', 2)
    objectives = [
        'Describir la estructura y organización de cada plugin',
        'Documentar la arquitectura de base de datos utilizada',
        'Explicar los flujos de trabajo implementados',
        'Detallar las APIs y servicios web disponibles',
        'Proporcionar guías de mantenimiento y extensión'
    ]
    add_bullet_list(doc, objectives)

    add_heading(doc, '3. ALCANCE', 1)
    add_paragraph(doc,
        'Este documento cubre la documentación técnica de los siguientes plugins:')

    plugins_info = [
        'local_jobboard: Sistema de gestión de vacantes y postulaciones docentes',
        'report_platform_usage: Generador de reportes de uso de la plataforma',
        'local_platform_access: Generador de registros de acceso para pruebas'
    ]
    add_bullet_list(doc, plugins_info)

    add_heading(doc, '4. TECNOLOGÍAS UTILIZADAS', 1)
    add_paragraph(doc,
        'Los plugins han sido desarrollados utilizando las siguientes tecnologías:')

    tech_data = [
        ['Categoría', 'Tecnología', 'Descripción'],
        ['Backend', 'PHP 8.x', 'Lenguaje principal de Moodle'],
        ['Base de datos', 'MySQL/MariaDB', 'DBAL de Moodle'],
        ['Frontend', 'JavaScript (AMD)', 'Módulos RequireJS'],
        ['Plantillas', 'Mustache', 'Motor de plantillas Moodle'],
        ['Web Services', 'REST/AJAX', 'API Moodle External'],
        ['Caché', 'MUC', 'Moodle Universal Cache']
    ]
    add_table_with_caption(doc, tech_data, 'Tecnologías utilizadas en los plugins', [1.5, 1.5, 3])

    doc.add_page_break()


def generate_jobboard_section(doc):
    """Genera la sección del plugin local_jobboard."""
    data = load_json_data('local_jobboard')
    if not data:
        return

    add_heading(doc, 'PLUGIN LOCAL_JOBBOARD', 1)

    # Descripción
    if 'version' in data:
        add_paragraph(doc, data['version'].get('description', ''))

    # Información de versión
    add_heading(doc, 'Información de Versión', 2)
    if 'version' in data:
        v = data['version']
        version_data = [
            ['Campo', 'Valor'],
            ['Versión', v.get('release', '')],
            ['Build', str(v.get('version', ''))],
            ['Madurez', v.get('maturity', '')],
            ['Moodle requerido', v.get('requires_description', '')],
            ['Autor', v.get('author', '')],
            ['Licencia', v.get('license', '')]
        ]
        add_table_with_caption(doc, version_data, 'Información de versión del plugin local_jobboard', [2, 4])

    # Estructura del plugin
    add_heading(doc, 'Estructura del Plugin', 2)
    add_paragraph(doc,
        'El plugin local_jobboard sigue la estructura estándar de plugins locales de Moodle. '
        'La organización de directorios y archivos está diseñada para facilitar el mantenimiento '
        'y cumplir con las convenciones de Moodle.')

    svg_path = SVG_DIR / 'local_jobboard' / 'estructura_directorios.svg'
    add_figure(doc, svg_path, 'Estructura de directorios del plugin local_jobboard')

    # Arquitectura
    add_heading(doc, 'Arquitectura del Sistema', 2)
    add_paragraph(doc,
        'El plugin implementa una arquitectura modular que separa las responsabilidades en capas: '
        'presentación, lógica de negocio, acceso a datos, y servicios web.')

    svg_path = SVG_DIR / 'local_jobboard' / 'arquitectura.svg'
    add_figure(doc, svg_path, 'Arquitectura del plugin local_jobboard')

    # Casos de uso
    add_heading(doc, 'Casos de Uso', 3)
    add_paragraph(doc,
        'El sistema define cinco tipos de actores principales: Docente/Aspirante, Revisor de '
        'Documentos, Administrador, Decano de Facultad y Recursos Humanos.')

    svg_path = SVG_DIR / 'local_jobboard' / 'casos_uso.svg'
    add_figure(doc, svg_path, 'Diagrama de casos de uso')

    # Base de datos
    add_heading(doc, 'Base de Datos', 2)
    if 'database' in data:
        tables = data['database'].get('tables', [])
        add_paragraph(doc,
            f'El plugin utiliza {len(tables)} tablas para almacenar la información de vacantes, '
            'postulaciones, documentos y configuraciones.')

        table_summary = [['Tabla', 'Descripción']]
        for t in tables[:6]:
            name = t.get('name', '').replace('local_jobboard_', '')
            comment = t.get('comment', 'Sin descripción')
            table_summary.append([name, comment])

        add_table_with_caption(doc, table_summary, 'Resumen de tablas de base de datos', [2, 4])

    # Diagrama ER
    svg_path = SVG_DIR / 'local_jobboard' / 'diagrama_er.svg'
    add_figure(doc, svg_path, 'Diagrama entidad-relación del plugin local_jobboard')

    # Diagrama de clases
    add_heading(doc, 'Clases Principales', 2)
    svg_path = SVG_DIR / 'local_jobboard' / 'diagrama_clases.svg'
    add_figure(doc, svg_path, 'Diagrama de clases principales')

    # Servicios web
    add_heading(doc, 'Servicios Web', 2)
    if 'services' in data and data['services']:
        add_paragraph(doc,
            f'El plugin expone {len(data["services"])} servicios web que permiten la interacción '
            'con el sistema mediante AJAX y APIs externas.')

        svg_path = SVG_DIR / 'local_jobboard' / 'servicios_web.svg'
        add_figure(doc, svg_path, 'Servicios web disponibles')

        services_data = [['Función', 'Tipo', 'Descripción']]
        for s in data['services'][:5]:
            services_data.append([
                s.get('function_name', ''),
                s.get('type', ''),
                s.get('description', '')
            ])
        add_table_with_caption(doc, services_data, 'Servicios web disponibles', [2.5, 1, 2.5])

    # Flujos de trabajo
    add_heading(doc, 'Flujos de Trabajo', 2)

    add_heading(doc, 'Flujo de Estados de Vacante', 3)
    svg_path = SVG_DIR / 'local_jobboard' / 'flujo_estados_vacante.svg'
    add_figure(doc, svg_path, 'Flujo de estados de vacante')

    add_heading(doc, 'Flujo de Postulación', 3)
    svg_path = SVG_DIR / 'local_jobboard' / 'flujo_postulacion.svg'
    add_figure(doc, svg_path, 'Flujo de postulación')

    add_heading(doc, 'Ciclo de Vida de la Aplicación', 3)
    svg_path = SVG_DIR / 'local_jobboard' / 'ciclo_vida_aplicacion.svg'
    add_figure(doc, svg_path, 'Ciclo de vida de la aplicación')

    doc.add_page_break()


def generate_platform_usage_section(doc):
    """Genera la sección del plugin report_platform_usage."""
    data = load_json_data('report_platform_usage')
    if not data:
        return

    add_heading(doc, 'PLUGIN REPORT_PLATFORM_USAGE', 1)

    # Introducción
    add_heading(doc, 'Introducción', 2)
    if 'resumen_ejecutivo' in data:
        resumen = data['resumen_ejecutivo']
        add_paragraph(doc,
            f'{resumen.get("nombre_plugin", "")} es un {resumen.get("tipo", "")} diseñado para '
            f'proporcionar {resumen.get("proposito", "")}.')

    # Estructura
    add_heading(doc, 'Estructura del Plugin', 2)
    svg_path = SVG_DIR / 'report_platform_usage' / 'estructura_directorios.svg'
    add_figure(doc, svg_path, 'Estructura de directorios del plugin report_platform_usage')

    # Información de versión
    add_heading(doc, 'Información de Versión', 3)
    if 'informacion_version' in data:
        v = data['informacion_version']
        version_data = [
            ['Atributo', 'Valor'],
            ['Plugin', v.get('plugin', '')],
            ['Versión', v.get('version_release', '')],
            ['Moodle requerido', v.get('moodle_requerido', '')],
            ['Madurez', v.get('madurez', '')],
            ['Licencia', v.get('licencia', '')]
        ]
        add_table_with_caption(doc, version_data, 'Información de versión - Platform Usage Report', [2, 4])

    # Arquitectura
    add_heading(doc, 'Arquitectura del Sistema', 2)
    svg_path = SVG_DIR / 'report_platform_usage' / 'arquitectura.svg'
    add_figure(doc, svg_path, 'Diagrama de arquitectura - Platform Usage Report')

    # Base de datos
    add_heading(doc, 'Arquitectura de Base de Datos', 2)
    if 'arquitectura_base_datos' in data:
        db = data['arquitectura_base_datos']
        if 'tablas_propias' in db:
            for tabla in db['tablas_propias']:
                tabla_data = [
                    ['Campo', 'Descripción'],
                    ['Nombre', tabla.get('nombre', '')],
                    ['Propósito', tabla.get('proposito', '')],
                    ['Poblada por', tabla.get('poblada_por', '')],
                    ['Frecuencia', tabla.get('frecuencia_actualizacion', '')]
                ]
                add_table_with_caption(doc, tabla_data, f'Tabla {tabla.get("nombre", "")}', [2, 4])

    # Capabilities
    add_heading(doc, 'Capabilities y Seguridad', 2)
    if 'seguridad_permisos' in data and 'capabilities' in data['seguridad_permisos']:
        caps = data['seguridad_permisos']['capabilities']
        caps_data = [['Capability', 'Tipo', 'Propósito']]
        for cap in caps:
            caps_data.append([
                cap.get('nombre', ''),
                cap.get('tipo', ''),
                cap.get('proposito', '')
            ])
        add_table_with_caption(doc, caps_data, 'Capabilities del plugin Platform Usage Report', [2.5, 1, 2.5])

    # Flujo de datos
    add_heading(doc, 'Flujo de Datos', 2)
    svg_path = SVG_DIR / 'report_platform_usage' / 'flujo_datos.svg'
    add_figure(doc, svg_path, 'Diagrama de flujo de datos - Platform Usage Report')

    doc.add_page_break()


def generate_platform_access_section(doc):
    """Genera la sección del plugin local_platform_access."""
    data = load_json_data('local_platform_access')
    if not data:
        return

    add_heading(doc, 'PLUGIN LOCAL_PLATFORM_ACCESS', 1)

    # Introducción
    add_heading(doc, 'Introducción', 2)
    add_paragraph(doc,
        f'{data.get("nombre_completo", "")} es un plugin de tipo {data.get("tipo", "")} '
        f'cuyo propósito es {data.get("resumen_ejecutivo", {}).get("proposito", "")}.')

    # Estructura
    add_heading(doc, 'Estructura del Plugin', 2)
    svg_path = SVG_DIR / 'local_platform_access' / 'estructura_directorios.svg'
    add_figure(doc, svg_path, 'Estructura de directorios del plugin local_platform_access')

    # Información de versión
    add_heading(doc, 'Información de Versión', 3)
    if 'version' in data:
        v = data['version']
        version_data = [
            ['Atributo', 'Valor'],
            ['Plugin', data.get('plugin', '')],
            ['Versión', v.get('release', '')],
            ['Moodle requerido', v.get('requires', '')],
            ['Madurez', v.get('maturity', '')],
            ['Licencia', v.get('licencia', '')]
        ]
        add_table_with_caption(doc, version_data, 'Información de versión - Platform Access Generator', [2, 4])

    # Arquitectura
    add_heading(doc, 'Arquitectura del Sistema', 2)
    if 'arquitectura' in data:
        arq = data['arquitectura']
        add_paragraph(doc,
            f'El plugin implementa el patrón de diseño {arq.get("patron_diseno", "")}, '
            'optimizado para operaciones masivas de inserción en base de datos.')

    svg_path = SVG_DIR / 'local_platform_access' / 'arquitectura.svg'
    add_figure(doc, svg_path, 'Diagrama de arquitectura - Platform Access Generator')

    # Capabilities
    add_heading(doc, 'Capabilities y Seguridad', 2)
    if 'capacidades' in data and 'lista' in data['capacidades']:
        caps = data['capacidades']['lista']
        caps_data = [['Capability', 'Tipo', 'Propósito']]
        for cap in caps:
            caps_data.append([
                cap.get('nombre', ''),
                cap.get('tipo', ''),
                cap.get('proposito', '')
            ])
        add_table_with_caption(doc, caps_data, 'Capabilities del plugin Platform Access Generator', [2.5, 1, 2.5])

    # Flujo de generación
    add_heading(doc, 'Flujo de Generación de Datos', 2)
    svg_path = SVG_DIR / 'local_platform_access' / 'diagrama_generacion.svg'
    add_figure(doc, svg_path, 'Diagrama de flujo de generación - Platform Access Generator')

    # Optimizaciones
    add_heading(doc, 'Optimizaciones de Rendimiento', 2)
    if 'optimizaciones_rendimiento' in data:
        opt = data['optimizaciones_rendimiento']
        if 'tecnicas' in opt:
            opt_data = [['Técnica', 'Descripción', 'Impacto']]
            for t in opt['tecnicas']:
                opt_data.append([
                    t.get('nombre', ''),
                    t.get('descripcion', ''),
                    t.get('impacto', '')
                ])
            add_table_with_caption(doc, opt_data, 'Técnicas de optimización implementadas', [2, 2.5, 1.5])


def main():
    """Función principal."""
    print('=' * 60)
    print('  GENERADOR DE INFORME TÉCNICO - FORMATO F-GCT-17')
    print('  (Versión Python - Edición de documento existente)')
    print('=' * 60)
    print()

    # Verificar que existe el template
    if not TEMPLATE_PATH.exists():
        print(f'❌ Error: No se encontró el template: {TEMPLATE_PATH}')
        return 1

    print(f'📄 Abriendo template: {TEMPLATE_PATH.name}')

    # Abrir documento template
    doc = Document(str(TEMPLATE_PATH))

    # Generar contenido
    print('  📝 Generando portada...')
    generate_cover_page(doc)

    print('  📑 Generando tablas de contenido...')
    generate_toc_pages(doc)

    print('  📝 Generando secciones introductorias...')
    generate_introduction(doc)

    print('  🔧 Generando sección local_jobboard...')
    generate_jobboard_section(doc)

    print('  📊 Generando sección report_platform_usage...')
    generate_platform_usage_section(doc)

    print('  🔐 Generando sección local_platform_access...')
    generate_platform_access_section(doc)

    # Guardar documento
    print(f'  💾 Guardando documento: {OUTPUT_PATH.name}')
    doc.save(str(OUTPUT_PATH))

    print()
    print('=' * 60)
    print('✅ Documento generado exitosamente:')
    print(f'   {OUTPUT_PATH}')
    print()
    print('📋 Instrucciones post-generación:')
    print('   1. Abra el documento en Microsoft Word')
    print('   2. Presione Ctrl+A para seleccionar todo')
    print('   3. Presione F9 para actualizar los campos')
    print('=' * 60)

    return 0


if __name__ == '__main__':
    exit(main())

# AGENTS.md - RECONSTRUCCIÓN INTEGRAL LOCAL_JOBBOARD

## Documento de Especificaciones para Agentes de Codificación IA

---

## 1. INFORMACIÓN DEL PROYECTO

| Campo | Valor |
|-------|-------|
| **Componente** | `local_jobboard` |
| **Tipo** | Plugin local de Moodle |
| **Institución** | ISER - Instituto Superior de Educación Rural |
| **Autor** | Alonso Arias `<soporteplataformas@iser.edu.co>` |
| **Supervisión** | Vicerrectoría Académica ISER |
| **Moodle Soportado** | 4.1 - 4.5 |
| **Licencia** | GNU GPL v3 or later |
| **Propósito** | Sistema de Bolsa de Empleo para reclutamiento de profesores de cátedra |

---

## 2. OBJETIVO DE ESTA RECONSTRUCCIÓN

Reconstruir desde cero el sistema visual completo del plugin, recreando:

1. Archivo `styles.css` (por fases, junto con cada template)
2. Todos los templates Mustache en `templates/`
3. Todas las cadenas de idiomas en `lang/en/` y `lang/es/`
4. Todos los módulos AMD en `amd/src/`
5. Todos los User Tours en `db/tours/`

---

## 3. PASO CERO OBLIGATORIO: ELIMINACIÓN DE ARCHIVOS

### 3.1 ANTES DE INICIAR CUALQUIER FASE

**ELIMINAR COMPLETAMENTE los siguientes archivos y carpetas:**

```bash
# EJECUTAR ESTOS COMANDOS ANTES DE EMPEZAR

# 1. Eliminar carpeta de templates
rm -rf local/jobboard/templates/

# 2. Eliminar carpeta de idiomas
rm -rf local/jobboard/lang/

# 3. Eliminar archivo de estilos
rm -f local/jobboard/styles.css

# 4. Eliminar builds de AMD (se recompilarán)
rm -rf local/jobboard/amd/build/
```

### 3.2 Crear Estructura Vacía

Después de eliminar, crear las carpetas vacías:

```bash
# Crear estructura de carpetas vacía
mkdir -p local/jobboard/templates/components
mkdir -p local/jobboard/templates/layouts
mkdir -p local/jobboard/templates/pages/admin
mkdir -p local/jobboard/templates/pages/applications
mkdir -p local/jobboard/templates/pages/convocatorias
mkdir -p local/jobboard/templates/pages/documents
mkdir -p local/jobboard/templates/pages/public
mkdir -p local/jobboard/templates/pages/reports
mkdir -p local/jobboard/templates/pages/review
mkdir -p local/jobboard/templates/pages/user
mkdir -p local/jobboard/templates/pages/vacancies

mkdir -p local/jobboard/lang/en
mkdir -p local/jobboard/lang/es

# Crear archivo styles.css vacío
touch local/jobboard/styles.css

# Crear archivos de idioma vacíos con cabecera PHP
```

### 3.3 Inicializar Archivos Base

**styles.css** - Crear con cabecera y variables CSS únicamente:

```css
/**
 * Styles for local_jobboard plugin.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* ==========================================================================
   VARIABLES CSS - BASE DEL SISTEMA
   ========================================================================== */
:root {
    /* Colores principales */
    --jb-primary: #0d6efd;
    --jb-primary-hover: #0b5ed7;
    --jb-primary-light: #e7f1ff;
    --jb-secondary: #6c757d;
    --jb-secondary-hover: #5c636a;
    --jb-success: #198754;
    --jb-success-hover: #157347;
    --jb-danger: #dc3545;
    --jb-danger-hover: #bb2d3b;
    --jb-warning: #ffc107;
    --jb-warning-hover: #ffca2c;
    --jb-info: #0dcaf0;
    --jb-info-hover: #31d2f2;
    --jb-light: #f8f9fa;
    --jb-dark: #212529;
    --jb-white: #ffffff;
    --jb-muted: #6c757d;
    --jb-body-bg: #f8f9fa;
    --jb-body-color: #212529;
    
    /* Espaciado */
    --jb-spacer-1: 0.25rem;
    --jb-spacer-2: 0.5rem;
    --jb-spacer-3: 1rem;
    --jb-spacer-4: 1.5rem;
    --jb-spacer-5: 3rem;
    
    /* Tipografía */
    --jb-font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    --jb-font-size-base: 1rem;
    --jb-font-size-sm: 0.875rem;
    --jb-font-size-lg: 1.25rem;
    --jb-font-size-xs: 0.75rem;
    --jb-line-height: 1.5;
    --jb-font-weight-normal: 400;
    --jb-font-weight-medium: 500;
    --jb-font-weight-bold: 700;
    
    /* Bordes */
    --jb-border-radius: 0.375rem;
    --jb-border-radius-sm: 0.25rem;
    --jb-border-radius-lg: 0.5rem;
    --jb-border-radius-pill: 50rem;
    --jb-border-color: #dee2e6;
    --jb-border-width: 1px;
    
    /* Sombras */
    --jb-shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    --jb-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    --jb-shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    
    /* Transiciones */
    --jb-transition-base: all 0.2s ease-in-out;
    --jb-transition-fast: all 0.15s ease-in-out;
    --jb-transition-slow: all 0.3s ease-in-out;
}

/* Los estilos se agregan por fases, junto con cada template creado */
```

**lang/en/local_jobboard.php** - Crear con cabecera:

```php
<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * English language strings for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin identification.
$string['pluginname'] = 'Job Board';

// Las cadenas se agregan por fases, junto con cada template creado.
```

**lang/es/local_jobboard.php** - Crear con cabecera:

```php
<?php
// This file is part of Moodle - http://moodle.org/
// ... (misma cabecera)

/**
 * Spanish language strings for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Identificación del plugin.
$string['pluginname'] = 'Bolsa de Empleo';

// Las cadenas se agregan por fases, junto con cada template creado.
```

---

## 4. REGLA FUNDAMENTAL: ANÁLISIS → CREACIÓN SINCRONIZADA

### 4.1 Principio Inviolable

**POR CADA TEMPLATE:**

```
VISTA ANALIZADA → MUSTACHE CREADO → ESTILOS CREADOS → CADENAS CREADAS
```

**NUNCA:**
- Crear un template sin antes analizar el renderer y la vista
- Crear un template sin agregar sus estilos a styles.css
- Crear un template sin agregar sus strings a lang/en/ y lang/es/
- Avanzar al siguiente template sin completar el anterior

### 4.2 Flujo Obligatorio por Cada Template

```
╔═══════════════════════════════════════════════════════════════════════════════╗
║                                                                               ║
║   PASO 1: ANALIZAR RENDERER Y VISTA                                           ║
║   ─────────────────────────────────                                           ║
║   • Abrir classes/output/renderer/[nombre]_renderer.php                       ║
║   • Abrir views/[nombre].php                                                  ║
║   • Identificar método render_*()                                             ║
║   • Identificar método prepare_*_data()                                       ║
║   • Documentar TODAS las variables del contexto                               ║
║   • Documentar TODAS las condiciones lógicas                                  ║
║   • Documentar TODAS las URLs y acciones                                      ║
║   • Listar TODOS los tooltips necesarios                                      ║
║                                                                               ║
║   ══════════════════════════════════════════════════════════════════════      ║
║                                    ↓                                          ║
║   ══════════════════════════════════════════════════════════════════════      ║
║                                                                               ║
║   PASO 2: CREAR TEMPLATE MUSTACHE                                             ║
║   ───────────────────────────────                                             ║
║   • Crear archivo en templates/pages/[categoria]/[nombre].mustache            ║
║   • Usar SOLO clases con prefijo jb-*                                         ║
║   • Incluir bloque de documentación con variables                             ║
║   • Implementar todas las condiciones del análisis                            ║
║   • Incluir tooltips en elementos interactivos                                ║
║   • Incluir estado de loading (skeleton)                                      ║
║   • Incluir estado vacío (empty state)                                        ║
║                                                                               ║
║   ══════════════════════════════════════════════════════════════════════      ║
║                                    ↓                                          ║
║   ══════════════════════════════════════════════════════════════════════      ║
║                                                                               ║
║   PASO 3: AGREGAR ESTILOS A styles.css                                        ║
║   ────────────────────────────────────                                        ║
║   • Agregar AL FINAL de styles.css los estilos del template                   ║
║   • Crear TODAS las clases jb-* usadas en el template                         ║
║   • Incluir estados: normal, hover, focus, active, disabled                   ║
║   • Incluir variantes responsive si aplica                                    ║
║   • Comentar la sección con el nombre del template                            ║
║                                                                               ║
║   ══════════════════════════════════════════════════════════════════════      ║
║                                    ↓                                          ║
║   ══════════════════════════════════════════════════════════════════════      ║
║                                                                               ║
║   PASO 4: AGREGAR STRINGS A ARCHIVOS DE IDIOMA                                ║
║   ────────────────────────────────────────────                                ║
║   • Agregar TODAS las strings {{#str}} a lang/en/local_jobboard.php           ║
║   • Agregar TODAS las traducciones a lang/es/local_jobboard.php               ║
║   • Incluir strings de tooltips                                               ║
║   • Incluir strings de estados vacíos                                         ║
║   • Incluir strings de errores/validación                                     ║
║   • Comentar la sección con el nombre del template                            ║
║                                                                               ║
║   ══════════════════════════════════════════════════════════════════════      ║
║                                    ↓                                          ║
║   ══════════════════════════════════════════════════════════════════════      ║
║                                                                               ║
║   PASO 5: VALIDAR                                                             ║
║   ──────────────                                                              ║
║   • Template renderiza sin errores                                            ║
║   • Estilos se aplican correctamente                                          ║
║   • Strings aparecen en ambos idiomas                                         ║
║   • Tooltips funcionan                                                        ║
║                                                                               ║
║   ══════════════════════════════════════════════════════════════════════      ║
║                                    ↓                                          ║
║   ══════════════════════════════════════════════════════════════════════      ║
║                                                                               ║
║   PASO 6: VERSIONAR                                                           ║
║   ─────────────────                                                           ║
║   • Incrementar $plugin->version en version.php                               ║
║   • Agregar entrada en CHANGELOG.md                                           ║
║                                                                               ║
║   ══════════════════════════════════════════════════════════════════════      ║
║                                    ↓                                          ║
║   ══════════════════════════════════════════════════════════════════════      ║
║                                                                               ║
║   → SIGUIENTE TEMPLATE (repetir desde Paso 1)                                 ║
║                                                                               ║
╚═══════════════════════════════════════════════════════════════════════════════╝
```

### 4.3 Ejemplo Completo: Crear Lista de Convocatorias

---

**PASO 1: ANALIZAR**

**Archivos a estudiar:**
- `classes/output/renderer/convocatoria_renderer.php`
- `views/convocatorias.php`

**Métodos identificados:**
- `render_convocatorias_list_page($data)`
- `prepare_convocatorias_list_data($page, $perpage, $filters)`

**Variables extraídas del método prepare:**
```
convocatorias       array    Lista de convocatorias
totalcount          int      Total de registros
page                int      Página actual
perpage             int      Registros por página
canmanage           bool     Puede gestionar
cancreate           bool     Puede crear
filters             object   Filtros aplicados
hasconvocatorias    bool     Hay datos para mostrar
createurl           string   URL crear nueva
statuses            array    Estados para filtro
```

**Condiciones lógicas:**
- `{{#canmanage}}` → Mostrar acciones de gestión
- `{{#cancreate}}` → Mostrar botón crear
- `{{#hasconvocatorias}}` → Mostrar tabla
- `{{^hasconvocatorias}}` → Mostrar empty state

**Tooltips necesarios:**
- Botón crear
- Botón ver detalle
- Botón editar
- Botón publicar
- Botón cerrar
- Filtro de estado
- Filtro de búsqueda

---

**PASO 2: CREAR TEMPLATE**

Archivo: `templates/pages/convocatorias/list.mustache`

Clases jb-* a usar:
- `jb-page-header`
- `jb-btn`, `jb-btn-primary`, `jb-btn-outline-secondary`
- `jb-filter-form`, `jb-filter-group`
- `jb-form-control`, `jb-form-select`
- `jb-table`, `jb-table-hover`, `jb-thead-light`
- `jb-badge`, `jb-badge-success`, `jb-badge-secondary`
- `jb-empty-state`, `jb-empty-state-icon`, `jb-empty-state-text`
- `jb-skeleton`, `jb-skeleton-text`
- `jb-tooltip`
- `jb-d-flex`, `jb-justify-content-between`, `jb-align-items-center`
- `jb-mb-3`, `jb-mb-4`, `jb-mt-2`

---

**PASO 3: AGREGAR A styles.css**

```css
/* ==========================================================================
   CONVOCATORIAS LIST - pages/convocatorias/list.mustache
   ========================================================================== */

/* Page header */
.jb-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--jb-spacer-4);
    flex-wrap: wrap;
    gap: var(--jb-spacer-2);
}

/* Buttons */
.jb-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--jb-spacer-2);
    padding: 0.5rem 1rem;
    font-size: var(--jb-font-size-base);
    font-weight: var(--jb-font-weight-medium);
    line-height: var(--jb-line-height);
    text-align: center;
    text-decoration: none;
    vertical-align: middle;
    cursor: pointer;
    user-select: none;
    border: var(--jb-border-width) solid transparent;
    border-radius: var(--jb-border-radius);
    transition: var(--jb-transition-base);
}

.jb-btn:focus {
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.jb-btn:disabled {
    opacity: 0.65;
    pointer-events: none;
}

.jb-btn-primary {
    color: var(--jb-white);
    background-color: var(--jb-primary);
    border-color: var(--jb-primary);
}

.jb-btn-primary:hover {
    color: var(--jb-white);
    background-color: var(--jb-primary-hover);
    border-color: var(--jb-primary-hover);
}

.jb-btn-outline-secondary {
    color: var(--jb-secondary);
    border-color: var(--jb-secondary);
    background-color: transparent;
}

.jb-btn-outline-secondary:hover {
    color: var(--jb-white);
    background-color: var(--jb-secondary);
    border-color: var(--jb-secondary);
}

/* Filter form */
.jb-filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: var(--jb-spacer-3);
    padding: var(--jb-spacer-3);
    background-color: var(--jb-light);
    border-radius: var(--jb-border-radius);
    margin-bottom: var(--jb-spacer-4);
}

.jb-filter-group {
    display: flex;
    flex-direction: column;
    gap: var(--jb-spacer-1);
}

/* Form controls */
.jb-form-control,
.jb-form-select {
    display: block;
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: var(--jb-font-size-base);
    font-weight: var(--jb-font-weight-normal);
    line-height: var(--jb-line-height);
    color: var(--jb-body-color);
    background-color: var(--jb-white);
    border: var(--jb-border-width) solid var(--jb-border-color);
    border-radius: var(--jb-border-radius);
    transition: var(--jb-transition-base);
}

.jb-form-control:focus,
.jb-form-select:focus {
    border-color: var(--jb-primary);
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Table */
.jb-table {
    width: 100%;
    margin-bottom: var(--jb-spacer-3);
    color: var(--jb-body-color);
    border-collapse: collapse;
}

.jb-table th,
.jb-table td {
    padding: 0.75rem;
    vertical-align: middle;
    border-bottom: var(--jb-border-width) solid var(--jb-border-color);
}

.jb-table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.jb-thead-light th {
    background-color: var(--jb-light);
    font-weight: var(--jb-font-weight-medium);
}

/* Badges */
.jb-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    font-size: var(--jb-font-size-xs);
    font-weight: var(--jb-font-weight-medium);
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: var(--jb-border-radius-pill);
}

.jb-badge-success {
    color: var(--jb-white);
    background-color: var(--jb-success);
}

.jb-badge-secondary {
    color: var(--jb-white);
    background-color: var(--jb-secondary);
}

/* Empty state */
.jb-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--jb-spacer-5);
    text-align: center;
}

.jb-empty-state-icon {
    font-size: 3rem;
    color: var(--jb-muted);
    margin-bottom: var(--jb-spacer-3);
}

.jb-empty-state-text {
    color: var(--jb-muted);
    max-width: 400px;
}

/* Skeleton loading */
.jb-skeleton {
    animation: jb-skeleton-pulse 1.5s ease-in-out infinite;
}

.jb-skeleton-text {
    height: 1rem;
    background-color: #e9ecef;
    border-radius: var(--jb-border-radius-sm);
}

@keyframes jb-skeleton-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Tooltip */
.jb-tooltip {
    position: relative;
}

.jb-tooltip::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    padding: 0.25rem 0.5rem;
    background-color: var(--jb-dark);
    color: var(--jb-white);
    font-size: var(--jb-font-size-xs);
    border-radius: var(--jb-border-radius-sm);
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: var(--jb-transition-fast);
    z-index: 1000;
}

.jb-tooltip:hover::after,
.jb-tooltip:focus::after {
    opacity: 1;
    visibility: visible;
}

/* Flexbox utilities */
.jb-d-flex { display: flex; }
.jb-justify-content-between { justify-content: space-between; }
.jb-align-items-center { align-items: center; }

/* Spacing utilities */
.jb-mb-3 { margin-bottom: var(--jb-spacer-3); }
.jb-mb-4 { margin-bottom: var(--jb-spacer-4); }
.jb-mt-2 { margin-top: var(--jb-spacer-2); }
```

---

**PASO 4: AGREGAR STRINGS**

Agregar a `lang/en/local_jobboard.php`:

```php
// ==========================================================================
// CONVOCATORIAS LIST - pages/convocatorias/list.mustache
// ==========================================================================
$string['convocatorias'] = 'Job Announcements';
$string['convocatorias_list'] = 'Job Announcements List';
$string['create_convocatoria'] = 'Create Announcement';
$string['edit_convocatoria'] = 'Edit';
$string['view_convocatoria'] = 'View';
$string['publish_convocatoria'] = 'Publish';
$string['close_convocatoria'] = 'Close';
$string['delete_convocatoria'] = 'Delete';

// Tooltips
$string['tooltip_create_convocatoria'] = 'Create a new job announcement';
$string['tooltip_view_convocatoria'] = 'View announcement details';
$string['tooltip_edit_convocatoria'] = 'Edit this announcement';
$string['tooltip_publish_convocatoria'] = 'Publish for applications';
$string['tooltip_close_convocatoria'] = 'Close this announcement';
$string['tooltip_filter_status'] = 'Filter by status';
$string['tooltip_filter_search'] = 'Search by name or code';

// Table headers
$string['convocatoria_name'] = 'Name';
$string['convocatoria_code'] = 'Code';
$string['convocatoria_status'] = 'Status';
$string['convocatoria_dates'] = 'Dates';
$string['convocatoria_vacancies'] = 'Vacancies';
$string['actions'] = 'Actions';

// Statuses
$string['status_draft'] = 'Draft';
$string['status_published'] = 'Published';
$string['status_closed'] = 'Closed';
$string['status_archived'] = 'Archived';

// Filters
$string['filter_by_status'] = 'Filter by status';
$string['all_statuses'] = 'All statuses';
$string['search'] = 'Search';
$string['search_placeholder'] = 'Search by name or code...';
$string['apply_filters'] = 'Apply';
$string['clear_filters'] = 'Clear';

// Empty state
$string['no_convocatorias'] = 'No announcements found';
$string['no_convocatorias_desc'] = 'There are no announcements matching your criteria.';
$string['create_first_convocatoria'] = 'Create your first announcement';

// Loading
$string['loading'] = 'Loading...';
```

Agregar a `lang/es/local_jobboard.php`:

```php
// ==========================================================================
// CONVOCATORIAS LIST - pages/convocatorias/list.mustache
// ==========================================================================
$string['convocatorias'] = 'Convocatorias';
$string['convocatorias_list'] = 'Listado de Convocatorias';
$string['create_convocatoria'] = 'Crear Convocatoria';
$string['edit_convocatoria'] = 'Editar';
$string['view_convocatoria'] = 'Ver';
$string['publish_convocatoria'] = 'Publicar';
$string['close_convocatoria'] = 'Cerrar';
$string['delete_convocatoria'] = 'Eliminar';

// Tooltips
$string['tooltip_create_convocatoria'] = 'Crear una nueva convocatoria';
$string['tooltip_view_convocatoria'] = 'Ver detalle de la convocatoria';
$string['tooltip_edit_convocatoria'] = 'Editar esta convocatoria';
$string['tooltip_publish_convocatoria'] = 'Publicar para postulaciones';
$string['tooltip_close_convocatoria'] = 'Cerrar esta convocatoria';
$string['tooltip_filter_status'] = 'Filtrar por estado';
$string['tooltip_filter_search'] = 'Buscar por nombre o código';

// Table headers
$string['convocatoria_name'] = 'Nombre';
$string['convocatoria_code'] = 'Código';
$string['convocatoria_status'] = 'Estado';
$string['convocatoria_dates'] = 'Fechas';
$string['convocatoria_vacancies'] = 'Vacantes';
$string['actions'] = 'Acciones';

// Statuses
$string['status_draft'] = 'Borrador';
$string['status_published'] = 'Publicada';
$string['status_closed'] = 'Cerrada';
$string['status_archived'] = 'Archivada';

// Filters
$string['filter_by_status'] = 'Filtrar por estado';
$string['all_statuses'] = 'Todos los estados';
$string['search'] = 'Buscar';
$string['search_placeholder'] = 'Buscar por nombre o código...';
$string['apply_filters'] = 'Aplicar';
$string['clear_filters'] = 'Limpiar';

// Empty state
$string['no_convocatorias'] = 'No se encontraron convocatorias';
$string['no_convocatorias_desc'] = 'No hay convocatorias que coincidan con los criterios.';
$string['create_first_convocatoria'] = 'Cree su primera convocatoria';

// Loading
$string['loading'] = 'Cargando...';
```

---

**PASO 5: VALIDAR**

- [ ] Acceder a la vista en el navegador
- [ ] Verificar estilos aplicados
- [ ] Cambiar idioma y verificar strings
- [ ] Probar tooltips
- [ ] Probar filtros
- [ ] Verificar empty state

---

**PASO 6: VERSIONAR**

```php
// version.php
$plugin->version = 2025121301; // Incrementar
```

```markdown
// CHANGELOG.md
## [4.0.1] - 2025-12-13
### Added
- Template `pages/convocatorias/list.mustache`
- Estilos CSS para lista de convocatorias (botones, tabla, filtros, badges, empty state, tooltips)
- 35 strings de idioma EN/ES para convocatorias
```

---

## 5. REGLAS ABSOLUTAS DE DESARROLLO

### 5.1 Reglas de Análisis

| Regla | Descripción |
|-------|-------------|
| **Análisis primero** | NUNCA crear template sin analizar renderer y vista |
| **Documentar variables** | Listar TODAS las variables antes de crear |
| **Entender condiciones** | Mapear TODAS las condiciones lógicas |
| **Identificar tooltips** | Listar TODOS los elementos que necesitan tooltip |

### 5.2 Reglas de CSS

| Regla | Descripción |
|-------|-------------|
| **Por fases** | CSS se crea junto con cada template, NO todo de una vez |
| **SOLO jb-*** | Nunca usar clases Bootstrap directamente |
| **Comentar secciones** | Cada bloque CSS indica qué template lo usa |
| **Estados completos** | normal, hover, focus, active, disabled |
| **Contraste WCAG AA** | Texto legible sobre fondo |

### 5.3 Reglas de Templates

| Regla | Descripción |
|-------|-------------|
| **Documentación** | Bloque de comentario con variables |
| **No hardcodear** | Usar `{{#str}}stringkey, local_jobboard{{/str}}` |
| **Tooltips obligatorios** | En botones, iconos, badges, campos especiales |
| **Loading state** | Skeleton mientras cargan datos |
| **Empty state** | Mensaje cuando no hay datos |

### 5.4 Reglas de Strings

| Regla | Descripción |
|-------|-------------|
| **Por fases** | Strings se crean junto con cada template |
| **Paridad EN/ES** | Toda string en ambos archivos |
| **Comentar secciones** | Cada bloque indica qué template lo usa |
| **Prefijos** | `tooltip_`, `error_`, `confirm_`, `empty_` |

### 5.5 Reglas de Versionado

| Tipo de Cambio | version.php |
|----------------|-------------|
| Cada template completado | +1 |

---

## 6. PRINCIPIOS UX: MINIMALISMO FUNCIONAL

### 6.1 Filosofía de Diseño

| Principio | Aplicación |
|-----------|------------|
| **Menos es más** | Solo elementos con valor funcional |
| **Espacios en blanco** | Padding y margin generosos |
| **Tipografía limpia** | Máximo 3 tamaños por vista |
| **Colores con propósito** | Solo para estado o acción |
| **Iconografía consistente** | Font Awesome 6, estilo solid |
| **Feedback inmediato** | Respuesta visual a cada acción |

### 6.2 Características Visuales

- Fondos blancos o grises claros
- Bordes sutiles (1px)
- Sombras mínimas
- Botones con todos los estados distintos
- Labels siempre visibles
- Estados vacíos con icono y mensaje
- Loading con skeletons

### 6.3 Tooltips Obligatorios

**Ubicación:**
- Botones de acción
- Iconos sin texto
- Campos especiales
- Badges
- Enlaces secundarios
- Acciones masivas

**Comportamiento:**
- Delay: 300ms
- Texto: máximo 10 palabras
- Accesible via teclado

---

## 7. INVENTARIO DE RENDERERS Y VISTAS

| Renderer | Vista PHP | Templates a Crear |
|----------|-----------|-------------------|
| `dashboard_renderer.php` | `index.php` | `pages/admin/dashboard` |
| `convocatoria_renderer.php` | `views/convocatorias.php` | `pages/convocatorias/*` (4) |
| `vacancy_renderer.php` | `views/vacancies.php` | `pages/vacancies/*` (7) |
| `application_renderer.php` | `views/apply.php` | `pages/applications/*` (4) |
| `public_renderer.php` | `public.php` | `pages/public/*` (4) |
| `review_renderer.php` | `views/review.php` | `pages/review/*` (6) |
| `committee_renderer.php` | `admin/manage_committee.php` | `pages/review/committee*` (3) |
| `admin_renderer.php` | `admin/*.php` | `pages/admin/*` (9) |
| `exemption_renderer.php` | `admin/manage_exemptions.php` | `pages/admin/exemption*` (3) |
| `reports_renderer.php` | `views/reports.php` | `pages/reports/*` (1) |

---

## 8. FASES DE IMPLEMENTACIÓN

### FASE 0: ELIMINACIÓN Y PREPARACIÓN

**OBLIGATORIO ANTES DE TODO:**
1. Eliminar `templates/`
2. Eliminar `lang/`
3. Eliminar `styles.css`
4. Eliminar `amd/build/`
5. Crear estructura de carpetas vacía
6. Crear archivos base (styles.css con variables, lang con cabecera)

---

### FASE 1: COMPONENTES BASE

**Por cada componente, seguir el flujo completo:**

Orden de creación:
1. `components/loading_skeleton.mustache`
2. `components/tooltip.mustache`
3. `components/alert.mustache`
4. `components/status_badge.mustache`
5. `components/empty_state.mustache`
6. `components/card.mustache`
7. `components/stat_card.mustache`
8. `components/table.mustache`
9. `components/pagination.mustache`
10. `components/filter_form.mustache`
11. `components/modal.mustache`
12. `components/breadcrumb.mustache`
13. `components/progress_bar.mustache`
14. `components/document_item.mustache`
15. `components/timeline_item.mustache`
16. `components/vacancy_card.mustache`

---

### FASE 2: LAYOUT BASE

Crear `layouts/base.mustache` con su CSS y strings

---

### FASE 3: DASHBOARD

1. Analizar `dashboard_renderer.php`
2. Crear `pages/admin/dashboard.mustache`
3. Agregar CSS del dashboard a styles.css
4. Agregar strings del dashboard a lang/

---

### FASE 4: PÁGINAS PÚBLICAS

Por cada una (análisis → template → CSS → strings):
1. `pages/public/index.mustache`
2. `pages/public/convocatoria.mustache`
3. `pages/public/vacancy.mustache`
4. `pages/public/apply_prompt.mustache`

---

### FASE 5: PÁGINAS DE CONVOCATORIAS

Por cada una:
1. `pages/convocatorias/list.mustache`
2. `pages/convocatorias/form.mustache`
3. `pages/convocatorias/detail.mustache`
4. `pages/convocatorias/documents.mustache`

---

### FASE 6: PÁGINAS DE VACANTES

Por cada una:
1. `pages/vacancies/list.mustache`
2. `pages/vacancies/manage.mustache`
3. `pages/vacancies/form.mustache`
4. `pages/vacancies/detail.mustache`
5. `pages/vacancies/applications.mustache`
6. `pages/vacancies/select_convocatoria.mustache`
7. `pages/vacancies/import.mustache`

---

### FASE 7: PÁGINAS DE POSTULACIONES

Por cada una:
1. `pages/applications/list.mustache`
2. `pages/applications/my.mustache`
3. `pages/applications/apply.mustache`
4. `pages/applications/detail.mustache`

---

### FASE 8: PÁGINAS DE DOCUMENTOS

Por cada una:
1. `pages/documents/list.mustache`
2. `pages/documents/upload.mustache`
3. `pages/documents/detail.mustache`

---

### FASE 9: PÁGINAS DE REVISIÓN

Por cada una:
1. `pages/review/list.mustache`
2. `pages/review/panel.mustache`
3. `pages/review/document.mustache`
4. `pages/review/assign_reviewer.mustache`
5. `pages/review/program_reviewers.mustache`
6. `pages/review/schedule_interview.mustache`

---

### FASE 10: PÁGINAS DE COMITÉ

Por cada una:
1. `pages/review/committee.mustache`
2. `pages/review/committee_members.mustache`
3. `pages/review/interview_complete.mustache`

---

### FASE 11: PÁGINAS DE ADMINISTRACIÓN

Por cada una:
1. `pages/admin/doctypes.mustache`
2. `pages/admin/doctype_form.mustache`
3. `pages/admin/templates.mustache`
4. `pages/admin/template_form.mustache`
5. `pages/admin/roles.mustache`
6. `pages/admin/audit.mustache`
7. `pages/admin/migrate.mustache`
8. `pages/admin/import_vacancies.mustache`
9. `pages/admin/settings.mustache`

---

### FASE 12: PÁGINAS DE EXCEPCIONES

Por cada una:
1. `pages/admin/exemptions.mustache`
2. `pages/admin/exemption_form.mustache`
3. `pages/admin/exemption_detail.mustache`

---

### FASE 13: PÁGINAS DE REPORTES

1. `pages/reports/index.mustache`

---

### FASE 14: PÁGINAS DE USUARIO

Por cada una:
1. `pages/user/profile.mustache`
2. `pages/user/edit_profile.mustache`
3. `pages/user/consent.mustache`
4. `pages/user/notifications.mustache`

---

### FASE 15: MÓDULOS AMD

Por cada módulo:
1. Analizar funcionalidad
2. Crear/modificar en `amd/src/`
3. Compilar: `grunt amd --root=local/jobboard`

---

### FASE 16: USER TOURS

Por cada tour:
1. Crear JSON en `db/tours/`
2. Agregar strings EN
3. Agregar strings ES

---

### FASE 17: VALIDACIÓN FINAL

Checklist completo de todo el plugin

---

## 9. RESUMEN DEL CICLO

```
╔═══════════════════════════════════════════════════════════════╗
║                                                               ║
║   1. VISTA ANALIZADA                                          ║
║      Renderer + Vista PHP estudiados                          ║
║      Variables y condiciones documentadas                     ║
║                         ↓                                     ║
║   2. MUSTACHE CREADO                                          ║
║      Template con clases jb-*, tooltips, estados              ║
║                         ↓                                     ║
║   3. ESTILOS CREADOS                                          ║
║      CSS agregado a styles.css para este template             ║
║                         ↓                                     ║
║   4. CADENAS CREADAS                                          ║
║      Strings EN + ES para este template                       ║
║                         ↓                                     ║
║   5. VALIDADO Y VERSIONADO                                    ║
║      Funciona → version.php + CHANGELOG                       ║
║                         ↓                                     ║
║   → SIGUIENTE TEMPLATE                                        ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
```

---

## 10. CONTACTO

| Rol | Nombre | Email |
|-----|--------|-------|
| Desarrollador | Alonso Arias | soporteplataformas@iser.edu.co |
| Supervisión | Vicerrectoría Académica | viceacademica@iser.edu.co |

---

*AGENTS.md para reconstrucción integral del plugin local_jobboard*
*Versión: 1.0*
*Fecha: 2025-12-13*
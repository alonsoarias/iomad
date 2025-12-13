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

Realizar una reconstrucción completa desde cero del sistema visual del plugin, eliminando y recreando:

1. Archivo `styles.css` completo
2. Todos los templates Mustache en `templates/`
3. Todos los módulos AMD en `amd/src/`
4. Todas las cadenas de idiomas en `lang/en/` y `lang/es/`
5. Todos los User Tours en `db/tours/`

---

## 3. REGLA CRÍTICA: ACTUALIZACIÓN SINCRONIZADA

### 3.1 Principio Fundamental

**CADA ARCHIVO MUSTACHE CREADO O MODIFICADO DEBE ACTUALIZAR SIMULTÁNEAMENTE:**

| Archivo | Actualización Requerida |
|---------|------------------------|
| `templates/*.mustache` | El template en sí |
| `styles.css` | Todas las clases CSS usadas en el template |
| `lang/en/local_jobboard.php` | Todas las strings usadas en el template |
| `lang/es/local_jobboard.php` | Traducción de todas las strings |

### 3.2 Flujo de Trabajo Obligatorio

```
┌─────────────────────────────────────────────────────────────────┐
│                    POR CADA TEMPLATE                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. ANALIZAR el renderer correspondiente                        │
│     ├── Identificar método render_*                             │
│     ├── Identificar método prepare_*_data                       │
│     └── Documentar TODAS las variables del contexto             │
│                                                                 │
│  2. CREAR/MODIFICAR el template .mustache                       │
│     ├── Usar SOLO clases jb-*                                   │
│     ├── Incluir tooltips en elementos interactivos              │
│     ├── Incluir estados de loading (skeleton)                   │
│     └── Incluir estados vacíos (empty state)                    │
│                                                                 │
│  3. ACTUALIZAR styles.css                                       │
│     ├── Agregar TODAS las clases jb-* usadas                    │
│     ├── Incluir estados hover, focus, active, disabled          │
│     ├── Incluir responsive (mobile-first)                       │
│     └── Incluir estilos de tooltip del template                 │
│                                                                 │
│  4. ACTUALIZAR lang/en/local_jobboard.php                       │
│     ├── Agregar TODAS las strings {{#str}}                      │
│     ├── Agregar strings de tooltips                             │
│     ├── Agregar strings de estados vacíos                       │
│     └── Agregar strings de errores/validación                   │
│                                                                 │
│  5. ACTUALIZAR lang/es/local_jobboard.php                       │
│     └── Traducir TODAS las strings agregadas en paso 4          │
│                                                                 │
│  6. VALIDAR                                                     │
│     ├── Template renderiza sin errores                          │
│     ├── Estilos aplicándose correctamente                       │
│     ├── Strings mostrándose en ambos idiomas                    │
│     └── Tooltips funcionando                                    │
│                                                                 │
│  7. VERSIONAR                                                   │
│     ├── Incrementar version en version.php                      │
│     └── Documentar en CHANGELOG.md                              │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 3.3 Ejemplo Práctico

**Al crear `templates/pages/admin/dashboard.mustache`:**

**Paso 1 - Analizar renderer:**
- Archivo: `classes/output/renderer/dashboard_renderer.php`
- Método: `render_dashboard_page()`
- Método: `prepare_dashboard_page_data()`
- Variables: `username`, `isadmin`, `iscoordinator`, `isreviewer`, `stats`, `quicklinks`, `notifications`, `timeline`

**Paso 2 - Crear template con clases jb-*:**
- Clases usadas: `jb-dashboard`, `jb-welcome-banner`, `jb-stat-card`, `jb-stat-card-primary`, `jb-quicklinks`, `jb-timeline`, `jb-notification-item`
- Tooltips: en cada stat-card, en cada quicklink, en cada acción de notificación
- Skeleton: para stats y timeline mientras cargan
- Empty state: cuando no hay notificaciones o actividad

**Paso 3 - Actualizar styles.css:**
```css
/* Agregar en styles.css */
.jb-dashboard { }
.jb-welcome-banner { }
.jb-stat-card { }
.jb-stat-card-primary { }
.jb-stat-card:hover { }
.jb-quicklinks { }
.jb-timeline { }
.jb-notification-item { }
/* ... todos los estados y variantes */
```

**Paso 4 - Actualizar lang/en/local_jobboard.php:**
```php
// Agregar en lang/en/local_jobboard.php
$string['dashboard'] = 'Dashboard';
$string['welcome_message'] = 'Welcome, {$a}';
$string['stats_applications'] = 'Applications';
$string['stats_applications_tooltip'] = 'Total applications received';
$string['quicklinks'] = 'Quick Actions';
$string['quicklink_convocatorias_tooltip'] = 'Manage job announcements';
$string['no_notifications'] = 'No pending notifications';
// ... todas las strings del template
```

**Paso 5 - Actualizar lang/es/local_jobboard.php:**
```php
// Agregar en lang/es/local_jobboard.php
$string['dashboard'] = 'Panel de Control';
$string['welcome_message'] = 'Bienvenido, {$a}';
$string['stats_applications'] = 'Postulaciones';
$string['stats_applications_tooltip'] = 'Total de postulaciones recibidas';
$string['quicklinks'] = 'Acciones Rápidas';
$string['quicklink_convocatorias_tooltip'] = 'Gestionar convocatorias';
$string['no_notifications'] = 'No hay notificaciones pendientes';
// ... todas las strings traducidas
```

**Paso 6 - Validar:**
- Acceder a `/local/jobboard/index.php`
- Verificar que renderiza sin errores
- Cambiar idioma y verificar strings
- Verificar tooltips con hover

**Paso 7 - Versionar:**
- Incrementar `$plugin->version` en version.php
- Agregar entrada en CHANGELOG.md

---

## 4. REGLAS ABSOLUTAS DE DESARROLLO

### 4.1 Reglas de CSS

| Regla | Descripción |
|-------|-------------|
| **SOLO jb-*** | Nunca usar clases Bootstrap directamente (btn, card, table, etc.) |
| **Variables CSS** | Usar variables de `:root` para colores, espaciados, tipografía |
| **Mobile-first** | Diseñar primero para móvil, expandir con media queries |
| **Estados completos** | Cada elemento interactivo debe tener: normal, hover, focus, active, disabled |
| **Contraste** | Cumplir WCAG AA para texto sobre fondo |
| **Sin !important** | Evitar !important excepto para sobrescribir estilos de theme |

### 4.2 Reglas de Templates

| Regla | Descripción |
|-------|-------------|
| **Documentación** | Cada template debe tener bloque de documentación con variables |
| **No hardcodear** | Usar `{{#str}}stringkey, local_jobboard{{/str}}` SIEMPRE |
| **Tooltips** | Incluir en botones, iconos, badges, campos de formulario |
| **Loading state** | Incluir skeleton para cuando se cargan datos |
| **Empty state** | Incluir mensaje cuando no hay datos |
| **Accesibilidad** | Incluir aria-labels, roles, skip-links |

### 4.3 Reglas de Strings

| Regla | Descripción |
|-------|-------------|
| **Paridad EN/ES** | Toda string DEBE existir en ambos archivos |
| **Prefijos** | Usar prefijos consistentes: `tooltip_`, `error_`, `confirm_`, `empty_` |
| **Placeholders** | Usar `{$a}` para valores dinámicos |
| **Sin HTML** | No incluir HTML en strings, usar plantillas |

### 4.4 Reglas de AMD

| Regla | Descripción |
|-------|-------------|
| **No editar build/** | NUNCA modificar archivos en `amd/build/` |
| **Compilar siempre** | Ejecutar `grunt amd --root=local/jobboard` después de cambios |
| **Selectores jb-*** | Usar clases `jb-*` o `data-region` en selectores |
| **Módulos core** | Usar core/ajax, core/notification, core/str, core/templates |
| **No Bootstrap JS** | No usar librerías JavaScript de Bootstrap |

### 4.5 Reglas de Versionado

| Tipo de Cambio | version.php | release |
|----------------|-------------|---------|
| Template nuevo/modificado | +1 | +0.0.1 |
| Fase completa | +1 | +0.1.0 |
| Bug fix | +1 | +0.0.1 |
| Nueva funcionalidad mayor | +1 | +1.0.0 |

---

## 5. PRINCIPIOS UX: MINIMALISMO FUNCIONAL

### 5.1 Filosofía de Diseño

| Principio | Aplicación |
|-----------|------------|
| **Menos es más** | Eliminar elementos que no aporten valor funcional |
| **Espacios en blanco** | Uso generoso de padding y margin para jerarquía |
| **Tipografía limpia** | Máximo 3 tamaños de fuente por vista |
| **Colores con propósito** | Color solo para estado o llamada a la acción |
| **Iconografía consistente** | Font Awesome 6, un solo estilo (solid) |
| **Microinteracciones** | Transiciones de 200-300ms, sutiles |
| **Feedback inmediato** | Respuesta visual instantánea a cada acción |

### 5.2 Características Visuales

- Fondos blancos o grises muy claros (#f8f9fa, #ffffff)
- Bordes sutiles (1px solid #dee2e6)
- Sombras mínimas solo en elementos flotantes
- Botones con todos los estados visualmente distintos
- Labels siempre visibles (no usar placeholder como label)
- Tablas con filas alternadas sutiles
- Estados vacíos con icono simple y mensaje claro
- Loading con skeletons, no spinners giratorios

### 5.3 Especificación de Tooltips

**Ubicación obligatoria:**
- Todos los botones de acción
- Iconos sin texto acompañante
- Campos de formulario con reglas especiales
- Badges y estados con abreviaciones
- Enlaces de navegación secundaria
- Acciones masivas (bulk actions)
- Atajos de teclado

**Comportamiento:**
- Delay de aparición: 300ms
- Desaparición: inmediata al mover cursor
- Texto: máximo 10 palabras
- Posición: automática, sin salir del viewport
- Accesible via teclado (focus)

---

## 6. ARQUITECTURA DE RENDERERS

### 6.1 Inventario de Renderer Traits

| Trait | Responsabilidad | Templates Asociados |
|-------|-----------------|---------------------|
| `dashboard_renderer` | Dashboard por rol | `pages/admin/dashboard` |
| `admin_renderer` | Páginas administrativas | `pages/admin/*` |
| `vacancy_renderer` | CRUD vacantes | `pages/vacancies/*` |
| `application_renderer` | Postulaciones | `pages/applications/*` |
| `convocatoria_renderer` | CRUD convocatorias | `pages/convocatorias/*` |
| `public_renderer` | Vista pública | `pages/public/*` |
| `review_renderer` | Panel revisión | `pages/review/*` |
| `committee_renderer` | Comités selección | `pages/review/committee*` |
| `exemption_renderer` | Excepciones | `pages/admin/exemption*` |
| `reports_renderer` | Reportes | `pages/reports/*` |

### 6.2 Proceso de Análisis por Renderer

Antes de crear cualquier template, analizar el renderer:

1. **Abrir archivo** `classes/output/renderer/[nombre]_renderer.php`
2. **Identificar métodos** `render_*` - determinan qué templates existen
3. **Identificar métodos** `prepare_*_data` - determinan variables del contexto
4. **Documentar variables** - listar TODAS las variables que pasa al template
5. **Identificar condiciones** - permisos, estados, datos vacíos
6. **Mapear navegación** - enlaces entre vistas relacionadas

---

## 7. ESTRUCTURA DE ARCHIVOS OBJETIVO

### 7.1 Templates

```
templates/
├── components/                    # Componentes reutilizables
│   ├── alert.mustache
│   ├── breadcrumb.mustache
│   ├── card.mustache
│   ├── document_item.mustache
│   ├── empty_state.mustache
│   ├── filter_form.mustache
│   ├── loading_skeleton.mustache
│   ├── modal.mustache
│   ├── pagination.mustache
│   ├── progress_bar.mustache
│   ├── stat_card.mustache
│   ├── status_badge.mustache
│   ├── table.mustache
│   ├── timeline_item.mustache
│   ├── tooltip.mustache
│   └── vacancy_card.mustache
├── layouts/
│   └── base.mustache              # Layout base común
└── pages/
    ├── admin/                     # Páginas administrativas
    │   ├── dashboard.mustache
    │   ├── doctypes.mustache
    │   ├── doctype_form.mustache
    │   ├── templates.mustache
    │   ├── template_form.mustache
    │   ├── roles.mustache
    │   ├── audit.mustache
    │   ├── migrate.mustache
    │   ├── import_vacancies.mustache
    │   ├── exemptions.mustache
    │   ├── exemption_form.mustache
    │   └── settings.mustache
    ├── applications/              # Postulaciones
    │   ├── list.mustache
    │   ├── my.mustache
    │   ├── apply.mustache
    │   └── detail.mustache
    ├── convocatorias/             # Convocatorias
    │   ├── list.mustache
    │   ├── form.mustache
    │   ├── detail.mustache
    │   └── documents.mustache
    ├── documents/                 # Documentos
    │   ├── list.mustache
    │   ├── upload.mustache
    │   └── detail.mustache
    ├── public/                    # Vista pública
    │   ├── index.mustache
    │   ├── convocatoria.mustache
    │   ├── vacancy.mustache
    │   └── apply_prompt.mustache
    ├── reports/                   # Reportes
    │   └── index.mustache
    ├── review/                    # Revisión
    │   ├── list.mustache
    │   ├── panel.mustache
    │   ├── document.mustache
    │   ├── assign_reviewer.mustache
    │   ├── program_reviewers.mustache
    │   ├── schedule_interview.mustache
    │   ├── committee.mustache
    │   └── interview_complete.mustache
    ├── user/                      # Usuario
    │   ├── profile.mustache
    │   ├── edit_profile.mustache
    │   ├── consent.mustache
    │   └── notifications.mustache
    └── vacancies/                 # Vacantes
        ├── list.mustache
        ├── manage.mustache
        ├── form.mustache
        ├── detail.mustache
        ├── applications.mustache
        ├── select_convocatoria.mustache
        └── import.mustache
```

### 7.2 Estructura de styles.css

```css
/* ==========================================================================
   SECCIÓN 1: VARIABLES CSS
   ========================================================================== */
:root {
    /* Colores */
    /* Espaciado */
    /* Tipografía */
    /* Bordes y sombras */
    /* Transiciones */
}

/* ==========================================================================
   SECCIÓN 2: RESET Y BASE
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 3: SISTEMA DE GRID
   jb-container, jb-row, jb-col-*
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 4: UTILIDADES DE ESPACIADO
   jb-m-*, jb-p-*, jb-mb-*, jb-mt-*, jb-me-*, jb-ms-*, jb-mx-*, jb-my-*
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 5: UTILIDADES FLEXBOX
   jb-d-flex, jb-d-none, jb-d-block, jb-d-inline-block
   jb-justify-content-*, jb-align-items-*, jb-flex-wrap, jb-flex-column
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 6: TIPOGRAFÍA
   jb-fs-*, jb-fw-*, jb-text-center, jb-text-left, jb-text-right
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 7: COLORES DE FONDO
   jb-bg-primary, jb-bg-secondary, jb-bg-success, jb-bg-danger, 
   jb-bg-warning, jb-bg-info, jb-bg-light, jb-bg-dark, jb-bg-white
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 8: COLORES DE TEXTO
   jb-text-primary, jb-text-secondary, jb-text-success, jb-text-danger,
   jb-text-warning, jb-text-info, jb-text-muted, jb-text-dark, jb-text-white
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 9: BOTONES
   jb-btn, jb-btn-primary, jb-btn-secondary, jb-btn-success, jb-btn-danger,
   jb-btn-warning, jb-btn-info, jb-btn-light, jb-btn-dark
   jb-btn-outline-*, jb-btn-sm, jb-btn-lg, jb-btn-block
   Estados: hover, focus, active, disabled
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 10: CARDS
   jb-card, jb-card-header, jb-card-body, jb-card-footer
   jb-card-shadow, jb-card-h100, jb-hover-lift
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 11: BADGES
   jb-badge, jb-badge-primary, jb-badge-secondary, etc.
   jb-badge-pill
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 12: ALERTAS
   jb-alert, jb-alert-primary, jb-alert-success, jb-alert-danger, etc.
   jb-alert-dismissible
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 13: TABLAS
   jb-table, jb-table-hover, jb-table-striped, jb-table-bordered
   jb-table-responsive, jb-thead-light, jb-table-sm
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 14: FORMULARIOS
   jb-form-control, jb-form-label, jb-form-group, jb-form-text
   jb-form-select, jb-form-check, jb-form-check-input, jb-form-check-label
   jb-is-valid, jb-is-invalid, jb-has-error
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 15: LIST GROUPS
   jb-list-group, jb-list-group-item, jb-list-group-item-action
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 16: NAVEGACIÓN
   jb-nav, jb-nav-tabs, jb-nav-pills, jb-nav-link, jb-nav-item
   jb-breadcrumb, jb-breadcrumb-item
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 17: MODALES
   jb-modal, jb-modal-dialog, jb-modal-content, jb-modal-header,
   jb-modal-body, jb-modal-footer, jb-modal-backdrop
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 18: PROGRESS BARS
   jb-progress, jb-progress-bar, jb-progress-striped
   jb-progress-steps (formularios multi-paso)
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 19: TOOLTIPS
   jb-tooltip, jb-tooltip-inner, jb-tooltip-arrow
   Posiciones: top, bottom, left, right
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 20: COMPONENTES ESPECÍFICOS DEL PLUGIN
   ========================================================================== */

/* 20.1 Stat Cards */
.jb-stat-card { }
.jb-stat-card-primary { }
.jb-stat-card-success { }
.jb-stat-card-warning { }
.jb-stat-card-danger { }
.jb-stat-card-info { }

/* 20.2 Vacancy Cards */
.jb-vacancy-card { }
.jb-vacancy-card-header { }
.jb-vacancy-card-body { }
.jb-vacancy-card-footer { }

/* 20.3 Application Cards */
.jb-application-card { }

/* 20.4 Convocatoria Cards */
.jb-convocatoria-card { }

/* 20.5 Document Items */
.jb-document-item { }
.jb-document-status { }

/* 20.6 Timeline */
.jb-timeline { }
.jb-timeline-item { }
.jb-timeline-marker { }
.jb-timeline-content { }

/* 20.7 Grading Panel (Revisión estilo mod_assign) */
.jb-grading-panel { }
.jb-grading-sidebar { }
.jb-grading-content { }
.jb-grading-preview { }

/* 20.8 Dashboard Sections */
.jb-dashboard { }
.jb-welcome-banner { }
.jb-quicklinks { }
.jb-notification-panel { }

/* 20.9 Filter Forms */
.jb-filter-form { }
.jb-filter-group { }

/* 20.10 Empty States */
.jb-empty-state { }
.jb-empty-state-icon { }
.jb-empty-state-message { }

/* ==========================================================================
   SECCIÓN 21: ANIMACIONES Y TRANSICIONES
   jb-fade-in, jb-fade-in-up, jb-slide-in
   jb-hover-lift, jb-hover-shadow
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 22: ESTADOS DE CARGA (SKELETONS)
   jb-skeleton, jb-skeleton-text, jb-skeleton-circle, jb-skeleton-rect
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 23: UTILIDADES DE ACCESIBILIDAD
   jb-sr-only, jb-skip-link, jb-focus-visible
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 24: UTILIDADES ADICIONALES
   jb-rounded, jb-rounded-circle, jb-border, jb-border-0
   jb-shadow, jb-shadow-sm, jb-shadow-lg
   jb-overflow-hidden, jb-position-relative, jb-position-absolute
   ========================================================================== */

/* ==========================================================================
   SECCIÓN 25: MEDIA QUERIES RESPONSIVAS
   ========================================================================== */
@media (max-width: 576px) { /* xs */ }
@media (min-width: 576px) { /* sm */ }
@media (min-width: 768px) { /* md */ }
@media (min-width: 992px) { /* lg */ }
@media (min-width: 1200px) { /* xl */ }

/* ==========================================================================
   SECCIÓN 26: COMPATIBILIDAD DE THEMES
   Boost, Classic, Remui, Flavor
   ========================================================================== */
```

### 7.3 Módulos AMD

| Módulo | Responsabilidad |
|--------|-----------------|
| `tooltips.js` | Sistema de tooltips personalizado |
| `public_filters.js` | Filtros AJAX vista pública |
| `review_ui.js` | Interfaz de revisión |
| `document_viewer.js` | Visor de documentos PDF |
| `application_form.js` | Formulario de postulación con tabs |
| `navigation.js` | Navegación general |
| `apply_progress.js` | Progreso del formulario |
| `progress_steps.js` | Indicador visual de pasos |
| `bulk_actions.js` | Acciones masivas |
| `grading_panel.js` | Panel revisión estilo mod_assign |
| `vacancy_manage.js` | Gestión de vacantes |
| `convocatoria_manage.js` | Gestión de convocatorias |
| `doctype_manage.js` | Gestión de doctypes |

---

## 8. FASES DE IMPLEMENTACIÓN

### FASE 0: PREPARACIÓN Y BACKUP

**Objetivo**: Respaldo completo antes de cualquier eliminación

**Tareas**:
1. Crear backup de `templates/`, `styles.css`, `amd/src/`, `lang/`
2. Documentar inventario actual
3. Verificar versión en `version.php`
4. Documentar inicio en CHANGELOG.md

**Entregables**:
- Carpeta de backup con fecha
- Documento de inventario
- Entrada en CHANGELOG

---

### FASE 1: SISTEMA CSS BASE

**Objetivo**: Crear `styles.css` completo con todas las clases `jb-*`

**Análisis Requerido**:
- Revisar clases CSS en templates actuales
- Identificar variantes de color, espaciado, tipografía
- Documentar componentes específicos del plugin

**Secciones a Crear** (en orden):
1. Variables CSS `:root`
2. Reset y base
3. Grid responsivo
4. Utilidades de espaciado
5. Utilidades flexbox
6. Tipografía
7. Colores de fondo
8. Colores de texto
9. Botones (TODOS los estados)
10. Cards
11. Badges
12. Alertas
13. Tablas
14. Formularios
15. List groups
16. Navegación
17. Modales
18. Progress bars
19. Tooltips
20. Componentes específicos
21. Animaciones
22. Skeletons
23. Accesibilidad
24. Utilidades adicionales
25. Media queries
26. Compatibilidad themes

**Problema a Resolver**: Botones con texto ilegible - asegurar contraste

**Actualización Sincronizada**:
- styles.css: Archivo completo
- lang/en: Strings de mensajes CSS (si aplica)
- lang/es: Traducción

**Criterio**: CSS funcionando, botones legibles, grid responsivo

---

### FASE 2: COMPONENTES BASE

**Objetivo**: Crear componentes reutilizables

**Componentes** (en orden de creación):

| # | Componente | Strings Requeridas |
|---|------------|-------------------|
| 1 | `loading_skeleton.mustache` | loading, loading_data |
| 2 | `tooltip.mustache` | (strings específicas por uso) |
| 3 | `alert.mustache` | alert_success, alert_error, alert_warning, alert_info, close |
| 4 | `status_badge.mustache` | status_*, badge_* |
| 5 | `breadcrumb.mustache` | home, breadcrumb_* |
| 6 | `empty_state.mustache` | no_data, empty_*, try_* |
| 7 | `card.mustache` | (strings específicas por uso) |
| 8 | `stat_card.mustache` | stats_*, tooltip_stats_* |
| 9 | `table.mustache` | no_records, actions, select_all |
| 10 | `pagination.mustache` | page, of, previous, next, first, last |
| 11 | `filter_form.mustache` | filter, search, clear_filters, apply_filters |
| 12 | `modal.mustache` | close, cancel, confirm |
| 13 | `progress_bar.mustache` | progress, complete |
| 14 | `document_item.mustache` | document_*, status_* |
| 15 | `timeline_item.mustache` | activity_*, time_ago_* |
| 16 | `vacancy_card.mustache` | vacancy_*, view_details, apply_now |

**Por cada componente**:
1. Crear template con documentación
2. Agregar clases a styles.css (si faltan)
3. Agregar strings EN
4. Agregar strings ES
5. Validar renderizado

**Criterio**: Todos los componentes funcionando con strings en ambos idiomas

---

### FASE 3: LAYOUT BASE

**Objetivo**: Layout común para todas las páginas

**Template**: `layouts/base.mustache`

**Elementos**:
- Skip link accesibilidad
- Área de alertas/mensajes
- Contenedor principal `[data-region="jobboard-*"]`
- Estructura breadcrumbs
- Área de contenido

**Actualización Sincronizada**:
- Template: `layouts/base.mustache`
- CSS: `.jb-page-wrapper`, `.jb-main-content`, `.jb-skip-link`
- Strings EN: `skiptocontent`, `mainnavigation`
- Strings ES: Traducción

**Criterio**: Layout accesible, responsivo, funcional

---

### FASE 4: DASHBOARD

**Objetivo**: Dashboard completo por rol

**Renderer**: `dashboard_renderer.php`
- Método: `prepare_dashboard_page_data()`
- Variables: `username`, `isadmin`, `iscoordinator`, `isreviewer`, `isapplicant`, `stats`, `quicklinks`, `notifications`, `timeline`, `convocatoriabanner`

**Template**: `pages/admin/dashboard.mustache`

**Secciones**:
1. Banner bienvenida
2. Stat cards según rol
3. Quicklinks según rol
4. Banner próxima convocatoria (si aplica)
5. Notificaciones pendientes
6. Timeline actividad reciente
7. Herramientas avanzadas (admin)

**Tooltips Requeridos**:
- Cada stat card
- Cada quicklink
- Acciones en notificaciones
- Items del timeline

**Actualización Sincronizada**:
- Template: `pages/admin/dashboard.mustache`
- CSS: `.jb-dashboard`, `.jb-welcome-banner`, `.jb-quicklinks`, etc.
- Strings EN: `dashboard`, `welcome_message`, `stats_*`, `quicklink_*`, `tooltip_*`, `no_notifications`, `recent_activity`
- Strings ES: Traducción completa

**Problema a Resolver**: Enlaces/funciones no visibles según rol

**Criterio**: Dashboard mostrando información correcta por rol, enlaces funcionando, tooltips presentes

---

### FASE 5: PÁGINAS PÚBLICAS

**Objetivo**: Vistas sin autenticación

**Renderer**: `public_renderer.php`

**Templates**:
1. `pages/public/index.mustache` - Listado convocatorias
2. `pages/public/convocatoria.mustache` - Detalle convocatoria
3. `pages/public/vacancy.mustache` - Detalle vacante
4. `pages/public/apply_prompt.mustache` - Prompt login/registro

**Tooltips Requeridos**:
- Filtros de búsqueda
- Botones ver detalle
- Botón postularse
- Badges de estado
- Enlace PDF convocatoria

**Actualización Sincronizada por template**:
- Crear template
- Agregar clases CSS faltantes
- Agregar strings EN
- Agregar strings ES
- Validar

**Criterio**: Navegación pública completa, diseño minimalista

---

### FASE 6: PÁGINAS DE CONVOCATORIAS

**Objetivo**: CRUD completo de convocatorias

**Renderer**: `convocatoria_renderer.php`

**Templates**:
1. `pages/convocatorias/list.mustache`
2. `pages/convocatorias/form.mustache`
3. `pages/convocatorias/detail.mustache`
4. `pages/convocatorias/documents.mustache`

**Tooltips Requeridos**:
- Acciones de tabla
- Campos del formulario
- Selector de documentos
- Estados de convocatoria

**Actualización Sincronizada por template**

**Criterio**: CRUD funcional, estados correctos

---

### FASE 7: PÁGINAS DE VACANTES

**Objetivo**: CRUD completo de vacantes

**Renderer**: `vacancy_renderer.php`

**Templates**:
1. `pages/vacancies/list.mustache`
2. `pages/vacancies/manage.mustache`
3. `pages/vacancies/form.mustache`
4. `pages/vacancies/detail.mustache`
5. `pages/vacancies/applications.mustache`
6. `pages/vacancies/select_convocatoria.mustache`
7. `pages/vacancies/import.mustache`

**Tooltips Requeridos**:
- Filtros
- Acciones por vacante
- Campos del formulario
- Requisitos

**Actualización Sincronizada por template**

**Criterio**: CRUD completo, filtros funcionando

---

### FASE 8: PÁGINAS DE POSTULACIONES

**Objetivo**: Flujo completo de postulación

**Renderer**: `application_renderer.php`

**Templates**:
1. `pages/applications/list.mustache`
2. `pages/applications/my.mustache`
3. `pages/applications/apply.mustache` (tabs)
4. `pages/applications/detail.mustache`

**Tabs del formulario apply**:
1. Información Personal
2. Formación Académica
3. Experiencia Laboral
4. Documentos
5. Carta de Intención (TEXTO, no archivo)
6. Revisión y Envío

**Tooltips Requeridos**:
- Cada pestaña
- Campos obligatorios
- Formatos permitidos
- Navegación entre pasos

**Actualización Sincronizada por template**

**Criterio**: Formulario con tabs funcional, validaciones correctas

---

### FASE 9: PÁGINAS DE DOCUMENTOS

**Objetivo**: Gestión de documentos del postulante

**Templates**:
1. `pages/documents/list.mustache`
2. `pages/documents/upload.mustache`
3. `pages/documents/detail.mustache`

**Tooltips Requeridos**:
- Estados de documento
- Botón subir/reemplazar
- Requisitos del doctype
- Checklist verificación

**Actualización Sincronizada por template**

**Criterio**: Gestión completa, estados visuales claros

---

### FASE 10: PÁGINAS DE REVISIÓN

**Objetivo**: Panel de revisión estilo mod_assign

**Renderer**: `review_renderer.php`

**Templates**:
1. `pages/review/list.mustache`
2. `pages/review/panel.mustache` (split-pane)
3. `pages/review/document.mustache`
4. `pages/review/assign_reviewer.mustache`
5. `pages/review/program_reviewers.mustache`
6. `pages/review/schedule_interview.mustache`

**Características del Panel**:
- Layout split-pane
- Navegación AJAX
- Visor PDF inline
- Atajos de teclado: J/K (navegar), A (aprobar), R (rechazar), D (descargar), F (fullscreen), S (sidebar), ? (ayuda)

**Tooltips Requeridos**:
- Atajos de teclado
- Botones aprobar/rechazar
- Estados de documento
- Navegación

**Actualización Sincronizada por template**

**Criterio**: Panel funcional con AJAX y atajos

---

### FASE 11: PÁGINAS DE COMITÉ

**Objetivo**: Gestión de comités por FACULTAD

**Renderer**: `committee_renderer.php`

**Templates**:
1. `pages/review/committee.mustache`
2. `pages/review/committee_members.mustache`
3. `pages/review/interview_complete.mustache`

**Tooltips Requeridos**:
- Roles del comité
- Búsqueda de usuarios
- Estados de entrevista
- Acciones de evaluación

**Actualización Sincronizada por template**

**Criterio**: Gestión de comités funcional

---

### FASE 12: PÁGINAS DE ADMINISTRACIÓN

**Objetivo**: Todas las páginas administrativas

**Renderer**: `admin_renderer.php`

**Templates**:
1. `pages/admin/doctypes.mustache`
2. `pages/admin/doctype_form.mustache`
3. `pages/admin/templates.mustache`
4. `pages/admin/template_form.mustache`
5. `pages/admin/roles.mustache`
6. `pages/admin/audit.mustache`
7. `pages/admin/migrate.mustache`
8. `pages/admin/import_vacancies.mustache`
9. `pages/admin/settings.mustache`

**Tooltips Requeridos**:
- Campos de configuración
- Acciones de tabla
- Placeholders de email
- Opciones de migración

**Actualización Sincronizada por template**

**Criterio**: Todas las funciones admin operativas

---

### FASE 13: PÁGINAS DE EXCEPCIONES

**Objetivo**: Gestión de excepciones documentales

**Renderer**: `exemption_renderer.php`

**Templates**:
1. `pages/admin/exemptions.mustache`
2. `pages/admin/exemption_form.mustache`
3. `pages/admin/exemption_detail.mustache`

**Tooltips Requeridos**:
- Tipos de excepción
- Documentos afectados
- Alcance

**Actualización Sincronizada por template**

**Criterio**: Sistema de excepciones funcional

---

### FASE 14: PÁGINAS DE REPORTES

**Objetivo**: Sistema de reportes con filtro obligatorio por convocatoria

**Renderer**: `reports_renderer.php`

**Templates**:
1. `pages/reports/index.mustache`

**Característica Especial**: Modal de selección de convocatoria OBLIGATORIO

**Tooltips Requeridos**:
- Selector de convocatoria
- Tipos de reporte
- Opciones de exportación

**Actualización Sincronizada**

**Criterio**: Reportes con filtro obligatorio funcional

---

### FASE 15: PÁGINAS DE USUARIO

**Objetivo**: Perfil y consentimientos

**Templates**:
1. `pages/user/profile.mustache`
2. `pages/user/edit_profile.mustache`
3. `pages/user/consent.mustache`
4. `pages/user/notifications.mustache`

**Tooltips Requeridos**:
- Campos del perfil
- Opciones de consentimiento
- Preferencias

**Actualización Sincronizada por template**

**Criterio**: Perfil completo con gestión de consentimiento

---

### FASE 16: MÓDULOS AMD

**Objetivo**: Recrear todos los módulos JavaScript

**Módulos**:
1. `tooltips.js`
2. `public_filters.js`
3. `review_ui.js`
4. `document_viewer.js`
5. `application_form.js`
6. `navigation.js`
7. `apply_progress.js`
8. `progress_steps.js`
9. `bulk_actions.js`
10. `grading_panel.js`
11. `vacancy_manage.js`
12. `convocatoria_manage.js`
13. `doctype_manage.js`

**Por cada módulo**:
1. Crear/modificar en `amd/src/`
2. Usar selectores `jb-*` o `data-region`
3. Compilar: `grunt amd --root=local/jobboard`
4. Validar funcionamiento

**Criterio**: Todos los módulos compilando y funcionando

---

### FASE 17: USER TOURS

**Objetivo**: 15 tours con selectores `jb-*`

**Tours**:
1. `tour_dashboard.json`
2. `tour_public.json`
3. `tour_convocatorias.json`
4. `tour_convocatoria_manage.json`
5. `tour_vacancies.json`
6. `tour_vacancy.json`
7. `tour_manage.json`
8. `tour_apply.json`
9. `tour_application.json`
10. `tour_myapplications.json`
11. `tour_documents.json`
12. `tour_review.json`
13. `tour_myreviews.json`
14. `tour_validate_document.json`
15. `tour_reports.json`

**Por cada tour**:
1. Crear JSON en `db/tours/`
2. Agregar strings EN para cada paso
3. Agregar strings ES para cada paso
4. Validar selectores

**Criterio**: Todos los tours ejecutándose con selectores estables

---

### FASE 18: VALIDACIÓN FINAL

**Checklist**:

**CSS**:
- [ ] Botones legibles todos los estados
- [ ] Grid responsivo
- [ ] Compatible Boost
- [ ] Compatible Classic
- [ ] Compatible Remui
- [ ] Compatible Flavor

**Templates**:
- [ ] Sin errores Mustache
- [ ] Variables renderizando
- [ ] Estados vacíos
- [ ] Loading skeletons
- [ ] Tooltips presentes

**Strings**:
- [ ] Paridad EN/ES completa
- [ ] Sin hardcodeos
- [ ] Tooltips traducidos

**AMD**:
- [ ] Módulos compilados
- [ ] Sin errores consola
- [ ] AJAX funcionando
- [ ] Atajos teclado

**Funcionalidad por Rol**:
- [ ] Admin completo
- [ ] Coordinador completo
- [ ] Revisor completo
- [ ] Postulante completo
- [ ] Visitante completo

**User Tours**:
- [ ] Todos ejecutándose
- [ ] Selectores encontrados
- [ ] Idioma correcto

---

## 9. PROBLEMAS CONOCIDOS A RESOLVER

| Problema | Ubicación | Solución |
|----------|-----------|----------|
| Botones con texto ilegible | styles.css | Asegurar contraste en `.jb-btn-*` |
| Dashboard sin enlaces según rol | dashboard_renderer + template | Verificar variables `isadmin`, `iscoordinator`, etc. |
| Tooltips no aparecen | tooltips.js + templates | Implementar sistema de tooltips consistente |

---

## 10. COMANDOS ÚTILES

```bash
# Compilar AMD
grunt amd --root=local/jobboard

# Purgar caché Moodle
php admin/cli/purge_caches.php

# Verificar sintaxis PHP
php -l local/jobboard/classes/output/renderer.php

# Contar líneas de archivo
wc -l local/jobboard/styles.css

# Buscar strings no traducidas
grep -r "get_string" local/jobboard/*.php | grep -v "local_jobboard"
```

---

## 11. CONTACTO

| Rol | Nombre | Email |
|-----|--------|-------|
| Desarrollador | Alonso Arias | soporteplataformas@iser.edu.co |
| Supervisión | Vicerrectoría Académica | viceacademica@iser.edu.co |

---

*Documento AGENTS.md para reconstrucción integral del plugin local_jobboard*
*Versión del documento: 1.0*
*Fecha: 2025-12-13*
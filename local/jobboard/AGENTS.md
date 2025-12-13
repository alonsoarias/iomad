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

## 3. REGLA FUNDAMENTAL: ANÁLISIS ANTES DE CREAR

### 3.1 Principio Inviolable

**NUNCA crear un template, estilo o string sin antes haber analizado completamente el renderer y la vista PHP correspondiente.**

El análisis previo determina:
- Qué variables están disponibles en el template
- Qué condiciones lógicas existen (permisos, estados, datos)
- Qué acciones y navegación debe soportar la vista
- Qué tooltips son necesarios
- Qué estados de carga y vacío se requieren

### 3.2 Flujo Obligatorio: ANALIZAR → CREAR → SINCRONIZAR

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                             │
│   ┌─────────────────────────────────────────────────────────────────────┐   │
│   │  PASO 1: ANALIZAR RENDERER Y VISTA                                  │   │
│   │                                                                     │   │
│   │  Archivos a estudiar:                                               │   │
│   │  • classes/output/renderer/[nombre]_renderer.php                    │   │
│   │  • views/[nombre].php (o index.php con parámetro view)              │   │
│   │                                                                     │   │
│   │  Información a extraer:                                             │   │
│   │  • Método render_*() → nombre del template                          │   │
│   │  • Método prepare_*_data() → variables del contexto                 │   │
│   │  • Condicionales de permisos → secciones por rol                    │   │
│   │  • URLs y navegación → enlaces y botones                            │   │
│   │  • Estados posibles → badges, alertas, empty states                 │   │
│   │                                                                     │   │
│   │  NO AVANZAR hasta tener documentadas TODAS las variables            │   │
│   └─────────────────────────────────────────────────────────────────────┘   │
│                                    │                                        │
│                                    ▼                                        │
│   ┌─────────────────────────────────────────────────────────────────────┐   │
│   │  PASO 2: CREAR TEMPLATE MUSTACHE                                    │   │
│   │                                                                     │   │
│   │  Con base en el análisis:                                           │   │
│   │  • Usar TODAS las variables identificadas                           │   │
│   │  • Implementar TODAS las condiciones lógicas                        │   │
│   │  • Usar SOLO clases con prefijo jb-*                                │   │
│   │  • Incluir tooltips en elementos interactivos                       │   │
│   │  • Incluir skeleton para estados de carga                           │   │
│   │  • Incluir empty state para cuando no hay datos                     │   │
│   │  • Documentar el template con bloque de comentario                  │   │
│   └─────────────────────────────────────────────────────────────────────┘   │
│                                    │                                        │
│                                    ▼                                        │
│   ┌─────────────────────────────────────────────────────────────────────┐   │
│   │  PASO 3: CREAR/ACTUALIZAR ESTILOS CSS                               │   │
│   │                                                                     │   │
│   │  Para CADA clase jb-* usada en el template:                         │   │
│   │  • Verificar si existe en styles.css                                │   │
│   │  • Si NO existe, CREARLA con todos sus estados                      │   │
│   │  • Estados requeridos: normal, hover, focus, active, disabled       │   │
│   │  • Incluir variantes responsive si aplica                           │   │
│   │  • Incluir estilos de tooltip del template                          │   │
│   └─────────────────────────────────────────────────────────────────────┘   │
│                                    │                                        │
│                                    ▼                                        │
│   ┌─────────────────────────────────────────────────────────────────────┐   │
│   │  PASO 4: CREAR CADENAS DE IDIOMAS                                   │   │
│   │                                                                     │   │
│   │  Para CADA {{#str}} usado en el template:                           │   │
│   │  • Agregar string en lang/en/local_jobboard.php                     │   │
│   │  • Agregar traducción en lang/es/local_jobboard.php                 │   │
│   │                                                                     │   │
│   │  Strings adicionales requeridas:                                    │   │
│   │  • Tooltips de todos los elementos interactivos                     │   │
│   │  • Mensajes de estado vacío                                         │   │
│   │  • Mensajes de error y validación                                   │   │
│   │  • Textos de confirmación                                           │   │
│   │  • Labels de accesibilidad (aria-label)                             │   │
│   └─────────────────────────────────────────────────────────────────────┘   │
│                                    │                                        │
│                                    ▼                                        │
│   ┌─────────────────────────────────────────────────────────────────────┐   │
│   │  PASO 5: VALIDAR Y VERSIONAR                                        │   │
│   │                                                                     │   │
│   │  Validaciones obligatorias:                                         │   │
│   │  • Template renderiza sin errores de sintaxis                       │   │
│   │  • Estilos CSS se aplican correctamente                             │   │
│   │  • Strings aparecen en español e inglés                             │   │
│   │  • Tooltips funcionan en hover y focus                              │   │
│   │  • Vista funciona en todos los roles aplicables                     │   │
│   │                                                                     │   │
│   │  Versionado:                                                        │   │
│   │  • Incrementar $plugin->version en version.php                      │   │
│   │  • Agregar entrada en CHANGELOG.md                                  │   │
│   └─────────────────────────────────────────────────────────────────────┘   │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 3.3 Checklist de Análisis de Renderer

Antes de crear cualquier template, completar este checklist:

```
ANÁLISIS DE: [nombre]_renderer.php + [nombre].php

□ Archivo renderer identificado: classes/output/renderer/_______________.php
□ Archivo vista identificado: views/_______________.php

MÉTODOS DEL RENDERER:
□ Método render: render_______________()
□ Método prepare: prepare_______________data()

VARIABLES DEL CONTEXTO (listar TODAS):
□ Variable 1: _______________ (tipo: _____) - Descripción: _______________
□ Variable 2: _______________ (tipo: _____) - Descripción: _______________
□ Variable 3: _______________ (tipo: _____) - Descripción: _______________
[continuar hasta listar todas]

CONDICIONES LÓGICAS:
□ Condición 1: {{#_______________}} - Cuándo se muestra: _______________
□ Condición 2: {{^_______________}} - Cuándo se oculta: _______________
[continuar]

NAVEGACIÓN Y ACCIONES:
□ URL 1: _______________ - Acción: _______________
□ URL 2: _______________ - Acción: _______________
[continuar]

PERMISOS REQUERIDOS:
□ Capability 1: _______________ - Para: _______________
□ Capability 2: _______________ - Para: _______________
[continuar]

TOOLTIPS NECESARIOS:
□ Elemento 1: _______________ - Tooltip: _______________
□ Elemento 2: _______________ - Tooltip: _______________
[continuar]

ESTADOS ESPECIALES:
□ Estado loading: Qué mostrar mientras carga: _______________
□ Estado vacío: Qué mostrar sin datos: _______________
□ Estado error: Qué mostrar en error: _______________
```

### 3.4 Ejemplo Completo de Análisis y Creación

**EJEMPLO: Crear página de listado de convocatorias**

---

**PASO 1: ANÁLISIS**

**Archivo renderer:** `classes/output/renderer/convocatoria_renderer.php`

**Archivo vista:** `views/convocatorias.php`

**Métodos identificados:**
- `render_convocatorias_list_page($data)`
- `prepare_convocatorias_list_data($page, $perpage, $filters)`

**Variables del contexto extraídas del método prepare:**
```
convocatorias       (array)   - Lista de convocatorias con datos
totalcount          (int)     - Total de registros
page                (int)     - Página actual
perpage             (int)     - Registros por página
canmanage           (bool)    - Si puede gestionar convocatorias
cancreate           (bool)    - Si puede crear nuevas
filters             (object)  - Filtros aplicados
  - status          (string)  - Filtro por estado
  - search          (string)  - Término de búsqueda
hasconvocatorias    (bool)    - Si hay convocatorias para mostrar
createurl           (string)  - URL para crear nueva
statuses            (array)   - Estados disponibles para filtro
```

**Condiciones lógicas identificadas:**
- `{{#canmanage}}` - Mostrar acciones de gestión
- `{{#cancreate}}` - Mostrar botón crear
- `{{#hasconvocatorias}}` - Mostrar tabla
- `{{^hasconvocatorias}}` - Mostrar empty state
- `{{#convocatoria.ispublished}}` - Badge publicada
- `{{#convocatoria.isclosed}}` - Badge cerrada

**URLs y acciones:**
- `createurl` - Crear nueva convocatoria
- `convocatoria.viewurl` - Ver detalle
- `convocatoria.editurl` - Editar
- `convocatoria.deleteurl` - Eliminar
- `convocatoria.publishurl` - Publicar
- `convocatoria.closeurl` - Cerrar

**Tooltips necesarios:**
- Botón crear: "Crear nueva convocatoria"
- Botón ver: "Ver detalle de la convocatoria"
- Botón editar: "Modificar convocatoria"
- Botón publicar: "Publicar convocatoria para postulaciones"
- Botón cerrar: "Cerrar convocatoria"
- Badge publicada: "Convocatoria visible para postulantes"
- Badge cerrada: "Convocatoria finalizada"
- Filtro estado: "Filtrar por estado de convocatoria"
- Filtro búsqueda: "Buscar por nombre o código"

---

**PASO 2: CREAR TEMPLATE**

Archivo: `templates/pages/convocatorias/list.mustache`

Clases jb-* a usar:
- `jb-convocatorias-list` (contenedor principal)
- `jb-page-header`
- `jb-filter-form`, `jb-filter-group`
- `jb-table`, `jb-table-hover`, `jb-thead-light`
- `jb-btn`, `jb-btn-primary`, `jb-btn-outline-secondary`
- `jb-badge`, `jb-badge-success`, `jb-badge-secondary`
- `jb-empty-state`, `jb-empty-state-icon`
- `jb-skeleton`, `jb-skeleton-text`
- `jb-tooltip`

---

**PASO 3: CREAR ESTILOS**

Agregar a `styles.css` (si no existen):

```css
/* Contenedor de lista de convocatorias */
.jb-convocatorias-list { }

/* Ya deben existir del sistema base: */
/* .jb-page-header, .jb-filter-form, .jb-table, .jb-btn-*, .jb-badge-*, .jb-empty-state */
```

---

**PASO 4: CREAR STRINGS**

Agregar a `lang/en/local_jobboard.php`:
```php
$string['convocatorias'] = 'Job Announcements';
$string['convocatorias_list'] = 'Job Announcements List';
$string['create_convocatoria'] = 'Create Announcement';
$string['tooltip_create_convocatoria'] = 'Create a new job announcement';
$string['tooltip_view_convocatoria'] = 'View announcement details';
$string['tooltip_edit_convocatoria'] = 'Edit announcement';
$string['tooltip_publish_convocatoria'] = 'Publish announcement for applications';
$string['tooltip_close_convocatoria'] = 'Close announcement';
$string['tooltip_badge_published'] = 'Announcement visible to applicants';
$string['tooltip_badge_closed'] = 'Announcement finalized';
$string['tooltip_filter_status'] = 'Filter by announcement status';
$string['tooltip_filter_search'] = 'Search by name or code';
$string['no_convocatorias'] = 'No job announcements found';
$string['no_convocatorias_desc'] = 'There are no announcements matching your criteria. Try adjusting your filters or create a new one.';
$string['filter_by_status'] = 'Filter by status';
$string['all_statuses'] = 'All statuses';
```

Agregar a `lang/es/local_jobboard.php`:
```php
$string['convocatorias'] = 'Convocatorias';
$string['convocatorias_list'] = 'Listado de Convocatorias';
$string['create_convocatoria'] = 'Crear Convocatoria';
$string['tooltip_create_convocatoria'] = 'Crear una nueva convocatoria';
$string['tooltip_view_convocatoria'] = 'Ver detalle de la convocatoria';
$string['tooltip_edit_convocatoria'] = 'Editar convocatoria';
$string['tooltip_publish_convocatoria'] = 'Publicar convocatoria para postulaciones';
$string['tooltip_close_convocatoria'] = 'Cerrar convocatoria';
$string['tooltip_badge_published'] = 'Convocatoria visible para postulantes';
$string['tooltip_badge_closed'] = 'Convocatoria finalizada';
$string['tooltip_filter_status'] = 'Filtrar por estado de convocatoria';
$string['tooltip_filter_search'] = 'Buscar por nombre o código';
$string['no_convocatorias'] = 'No se encontraron convocatorias';
$string['no_convocatorias_desc'] = 'No hay convocatorias que coincidan con los criterios. Intente ajustar los filtros o cree una nueva.';
$string['filter_by_status'] = 'Filtrar por estado';
$string['all_statuses'] = 'Todos los estados';
```

---

**PASO 5: VALIDAR Y VERSIONAR**

Validaciones:
- [ ] Acceder a `/local/jobboard/index.php?view=convocatorias`
- [ ] Verificar que la tabla muestra datos correctamente
- [ ] Verificar filtros funcionando
- [ ] Cambiar a español y verificar strings
- [ ] Probar tooltips con hover
- [ ] Probar como admin (todas las acciones)
- [ ] Probar como coordinador (acciones permitidas)
- [ ] Verificar empty state sin datos

Versionado:
- Incrementar version.php
- Agregar en CHANGELOG.md:
  ```
  ## [X.Y.Z] - YYYY-MM-DD
  ### Added
  - Template `pages/convocatorias/list.mustache` con filtros y acciones
  - Estilos CSS para lista de convocatorias
  - 16 strings de idioma EN/ES para convocatorias
  - Tooltips en todas las acciones y filtros
  ```

---

## 4. REGLAS ABSOLUTAS DE DESARROLLO

### 4.1 Reglas de Análisis

| Regla | Descripción |
|-------|-------------|
| **Análisis primero** | NUNCA crear template sin analizar renderer y vista |
| **Documentar variables** | Listar TODAS las variables antes de empezar |
| **Entender condiciones** | Mapear TODAS las condiciones lógicas |
| **Identificar tooltips** | Listar TODOS los elementos que necesitan tooltip |

### 4.2 Reglas de CSS

| Regla | Descripción |
|-------|-------------|
| **SOLO jb-*** | Nunca usar clases Bootstrap directamente |
| **Variables CSS** | Usar variables de `:root` para colores, espaciados |
| **Mobile-first** | Diseñar primero para móvil |
| **Estados completos** | Cada elemento: normal, hover, focus, active, disabled |
| **Contraste WCAG** | Cumplir AA para texto sobre fondo |

### 4.3 Reglas de Templates

| Regla | Descripción |
|-------|-------------|
| **Documentación** | Bloque de comentario con variables |
| **No hardcodear** | Usar `{{#str}}stringkey, local_jobboard{{/str}}` |
| **Tooltips** | En botones, iconos, badges, campos especiales |
| **Loading state** | Skeleton mientras cargan datos |
| **Empty state** | Mensaje cuando no hay datos |
| **Accesibilidad** | aria-labels, roles, skip-links |

### 4.4 Reglas de Strings

| Regla | Descripción |
|-------|-------------|
| **Paridad EN/ES** | Toda string en ambos archivos |
| **Prefijos** | `tooltip_`, `error_`, `confirm_`, `empty_`, `help_` |
| **Placeholders** | Usar `{$a}` para valores dinámicos |
| **Sin HTML** | No incluir HTML en strings |

### 4.5 Reglas de Versionado

| Tipo de Cambio | version.php | release |
|----------------|-------------|---------|
| Template + strings | +1 | +0.0.1 |
| Fase completa | +1 | +0.1.0 |
| Bug fix | +1 | +0.0.1 |

---

## 5. PRINCIPIOS UX: MINIMALISMO FUNCIONAL

### 5.1 Filosofía de Diseño

| Principio | Aplicación |
|-----------|------------|
| **Menos es más** | Solo elementos con valor funcional |
| **Espacios en blanco** | Padding y margin generosos |
| **Tipografía limpia** | Máximo 3 tamaños por vista |
| **Colores con propósito** | Solo para estado o acción |
| **Iconografía consistente** | Font Awesome 6, estilo solid |
| **Microinteracciones** | Transiciones 200-300ms |
| **Feedback inmediato** | Respuesta visual instantánea |

### 5.2 Características Visuales

- Fondos blancos o grises claros (#f8f9fa, #ffffff)
- Bordes sutiles (1px solid #dee2e6)
- Sombras mínimas solo en elementos flotantes
- Botones con todos los estados distintos
- Labels siempre visibles
- Tablas con filas alternadas sutiles
- Estados vacíos con icono y mensaje
- Loading con skeletons

### 5.3 Especificación de Tooltips

**Ubicación obligatoria:**
- Botones de acción
- Iconos sin texto
- Campos de formulario especiales
- Badges y estados
- Enlaces secundarios
- Acciones masivas
- Atajos de teclado

**Comportamiento:**
- Delay: 300ms
- Desaparición: inmediata
- Texto: máximo 10 palabras
- Posición: automática
- Accesible via teclado

---

## 6. INVENTARIO DE RENDERERS Y VISTAS

### 6.1 Mapeo Completo

| Renderer Trait | Vista PHP | Templates |
|----------------|-----------|-----------|
| `dashboard_renderer.php` | `index.php` (view=dashboard) | `pages/admin/dashboard` |
| `convocatoria_renderer.php` | `views/convocatorias.php` | `pages/convocatorias/*` |
| `vacancy_renderer.php` | `views/vacancies.php`, `views/vacancy.php` | `pages/vacancies/*` |
| `application_renderer.php` | `views/applications.php`, `views/apply.php` | `pages/applications/*` |
| `public_renderer.php` | `public.php` | `pages/public/*` |
| `review_renderer.php` | `views/review.php` | `pages/review/*` |
| `committee_renderer.php` | `admin/manage_committee.php` | `pages/review/committee*` |
| `admin_renderer.php` | `admin/*.php` | `pages/admin/*` |
| `exemption_renderer.php` | `admin/manage_exemptions.php` | `pages/admin/exemption*` |
| `reports_renderer.php` | `views/reports.php` | `pages/reports/*` |

### 6.2 Métodos por Renderer

**dashboard_renderer.php:**
- `render_dashboard_page()` → `prepare_dashboard_page_data()`

**convocatoria_renderer.php:**
- `render_convocatorias_list_page()` → `prepare_convocatorias_list_data()`
- `render_convocatoria_form_page()` → `prepare_convocatoria_form_data()`
- `render_convocatoria_detail_page()` → `prepare_convocatoria_detail_data()`
- `render_convocatoria_documents_page()` → `prepare_convocatoria_documents_data()`

**vacancy_renderer.php:**
- `render_vacancies_list_page()` → `prepare_vacancies_list_data()`
- `render_vacancy_manage_page()` → `prepare_vacancy_manage_data()`
- `render_vacancy_form_page()` → `prepare_vacancy_form_data()`
- `render_vacancy_detail_page()` → `prepare_vacancy_detail_data()`
- `render_vacancy_applications_page()` → `prepare_vacancy_applications_data()`
- `render_select_convocatoria_page()` → `prepare_select_convocatoria_data()`
- `render_import_vacancies_page()` → `prepare_import_vacancies_data()`

**application_renderer.php:**
- `render_applications_list_page()` → `prepare_applications_list_data()`
- `render_my_applications_page()` → `prepare_my_applications_data()`
- `render_apply_page()` → `prepare_apply_data()`
- `render_application_detail_page()` → `prepare_application_detail_data()`

**public_renderer.php:**
- `render_public_index_page()` → `prepare_public_index_data()`
- `render_public_convocatoria_page()` → `prepare_public_convocatoria_data()`
- `render_public_vacancy_page()` → `prepare_public_vacancy_data()`
- `render_apply_prompt_page()` → `prepare_apply_prompt_data()`

**review_renderer.php:**
- `render_review_list_page()` → `prepare_review_list_data()`
- `render_review_panel_page()` → `prepare_review_panel_data()`
- `render_review_document_page()` → `prepare_review_document_data()`
- `render_assign_reviewer_page()` → `prepare_assign_reviewer_data()`
- `render_program_reviewers_page()` → `prepare_program_reviewers_data()`
- `render_schedule_interview_page()` → `prepare_schedule_interview_data()`

**committee_renderer.php:**
- `render_committee_page()` → `prepare_committee_data()`
- `render_committee_members_page()` → `prepare_committee_members_data()`
- `render_interview_complete_page()` → `prepare_interview_complete_data()`

**admin_renderer.php:**
- `render_doctypes_page()` → `prepare_doctypes_data()`
- `render_doctype_form_page()` → `prepare_doctype_form_data()`
- `render_templates_page()` → `prepare_templates_data()`
- `render_template_form_page()` → `prepare_template_form_data()`
- `render_roles_page()` → `prepare_roles_data()`
- `render_audit_page()` → `prepare_audit_data()`
- `render_migrate_page()` → `prepare_migrate_data()`
- `render_import_vacancies_page()` → `prepare_import_vacancies_data()`
- `render_settings_page()` → `prepare_settings_data()`

**exemption_renderer.php:**
- `render_exemptions_page()` → `prepare_exemptions_data()`
- `render_exemption_form_page()` → `prepare_exemption_form_data()`
- `render_exemption_detail_page()` → `prepare_exemption_detail_data()`

**reports_renderer.php:**
- `render_reports_page()` → `prepare_reports_data()`

---

## 7. ESTRUCTURA DE ARCHIVOS OBJETIVO

### 7.1 Templates

```
templates/
├── components/                    # 16 componentes reutilizables
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
│   └── base.mustache
└── pages/
    ├── admin/                     # 12 páginas
    ├── applications/              # 4 páginas
    ├── convocatorias/             # 4 páginas
    ├── documents/                 # 3 páginas
    ├── public/                    # 4 páginas
    ├── reports/                   # 1 página
    ├── review/                    # 8 páginas
    ├── user/                      # 4 páginas
    └── vacancies/                 # 7 páginas
```

### 7.2 Estructura de styles.css

26 secciones organizadas:
1. Variables CSS
2. Reset y base
3. Grid responsivo
4. Utilidades espaciado
5. Utilidades flexbox
6. Tipografía
7. Colores de fondo
8. Colores de texto
9. Botones
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
20. Componentes específicos del plugin
21. Animaciones
22. Skeletons
23. Accesibilidad
24. Utilidades adicionales
25. Media queries
26. Compatibilidad themes

### 7.3 Módulos AMD

13 módulos:
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

---

## 8. FASES DE IMPLEMENTACIÓN

### FASE 0: PREPARACIÓN Y BACKUP

**Objetivo**: Respaldo completo

**Tareas**:
1. Backup de `templates/`, `styles.css`, `amd/src/`, `lang/`
2. Documentar inventario actual
3. Crear entrada en CHANGELOG.md

---

### FASE 1: SISTEMA CSS BASE

**Objetivo**: `styles.css` completo

**Análisis Requerido**:
- Revisar TODOS los templates actuales
- Identificar TODAS las clases usadas
- Documentar variantes necesarias

**Crear** 26 secciones de CSS

**Problema a Resolver**: Botones con texto ilegible

**Sincronización**: CSS completo, strings de errores CSS si aplica

---

### FASE 2: COMPONENTES BASE

**Objetivo**: 16 componentes reutilizables

**Por cada componente**:
1. Analizar dónde se usa
2. Crear template
3. Agregar clases a CSS (si faltan)
4. Crear strings EN
5. Crear strings ES
6. Validar

**Orden de creación**:
1. loading_skeleton
2. tooltip
3. alert
4. status_badge
5. breadcrumb
6. empty_state
7. card
8. stat_card
9. table
10. pagination
11. filter_form
12. modal
13. progress_bar
14. document_item
15. timeline_item
16. vacancy_card

---

### FASE 3: LAYOUT BASE

**Objetivo**: Layout común

**Análisis**: Estructura común de todas las páginas

**Crear**: `layouts/base.mustache`

**Sincronización**: Template + CSS + Strings

---

### FASE 4: DASHBOARD

**Análisis Obligatorio**:
- Renderer: `dashboard_renderer.php`
- Vista: `index.php`
- Método: `prepare_dashboard_page_data()`

**Documentar**: TODAS las variables del contexto

**Crear**:
- Template: `pages/admin/dashboard.mustache`
- CSS: Clases específicas del dashboard
- Strings EN: Todas las del template
- Strings ES: Traducciones

**Tooltips**: stat cards, quicklinks, notificaciones, timeline

---

### FASE 5: PÁGINAS PÚBLICAS

**Análisis Obligatorio**:
- Renderer: `public_renderer.php`
- Vista: `public.php`
- Métodos: `prepare_public_*_data()`

**Crear** (por cada una, análisis → template → CSS → strings):
1. `pages/public/index.mustache`
2. `pages/public/convocatoria.mustache`
3. `pages/public/vacancy.mustache`
4. `pages/public/apply_prompt.mustache`

---

### FASE 6: PÁGINAS DE CONVOCATORIAS

**Análisis Obligatorio**:
- Renderer: `convocatoria_renderer.php`
- Vista: `views/convocatorias.php`
- Métodos: `prepare_convocatoria*_data()`

**Crear**:
1. `pages/convocatorias/list.mustache`
2. `pages/convocatorias/form.mustache`
3. `pages/convocatorias/detail.mustache`
4. `pages/convocatorias/documents.mustache`

---

### FASE 7: PÁGINAS DE VACANTES

**Análisis Obligatorio**:
- Renderer: `vacancy_renderer.php`
- Vistas: `views/vacancies.php`, `views/vacancy.php`
- Métodos: `prepare_vacancy*_data()`

**Crear**:
1. `pages/vacancies/list.mustache`
2. `pages/vacancies/manage.mustache`
3. `pages/vacancies/form.mustache`
4. `pages/vacancies/detail.mustache`
5. `pages/vacancies/applications.mustache`
6. `pages/vacancies/select_convocatoria.mustache`
7. `pages/vacancies/import.mustache`

---

### FASE 8: PÁGINAS DE POSTULACIONES

**Análisis Obligatorio**:
- Renderer: `application_renderer.php`
- Vistas: `views/applications.php`, `views/apply.php`
- Métodos: `prepare_application*_data()`

**Crear**:
1. `pages/applications/list.mustache`
2. `pages/applications/my.mustache`
3. `pages/applications/apply.mustache` (6 tabs)
4. `pages/applications/detail.mustache`

---

### FASE 9: PÁGINAS DE DOCUMENTOS

**Análisis**: Identificar manejo de documentos en renderers

**Crear**:
1. `pages/documents/list.mustache`
2. `pages/documents/upload.mustache`
3. `pages/documents/detail.mustache`

---

### FASE 10: PÁGINAS DE REVISIÓN

**Análisis Obligatorio**:
- Renderer: `review_renderer.php`
- Vista: `views/review.php`
- Métodos: `prepare_review*_data()`

**Crear**:
1. `pages/review/list.mustache`
2. `pages/review/panel.mustache` (split-pane, atajos teclado)
3. `pages/review/document.mustache`
4. `pages/review/assign_reviewer.mustache`
5. `pages/review/program_reviewers.mustache`
6. `pages/review/schedule_interview.mustache`

---

### FASE 11: PÁGINAS DE COMITÉ

**Análisis Obligatorio**:
- Renderer: `committee_renderer.php`
- Vista: `admin/manage_committee.php`
- Métodos: `prepare_committee*_data()`

**Crear**:
1. `pages/review/committee.mustache`
2. `pages/review/committee_members.mustache`
3. `pages/review/interview_complete.mustache`

---

### FASE 12: PÁGINAS DE ADMINISTRACIÓN

**Análisis Obligatorio**:
- Renderer: `admin_renderer.php`
- Vistas: `admin/*.php`
- Métodos: `prepare_*_data()` de admin

**Crear**:
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

### FASE 13: PÁGINAS DE EXCEPCIONES

**Análisis Obligatorio**:
- Renderer: `exemption_renderer.php`
- Vista: `admin/manage_exemptions.php`
- Métodos: `prepare_exemption*_data()`

**Crear**:
1. `pages/admin/exemptions.mustache`
2. `pages/admin/exemption_form.mustache`
3. `pages/admin/exemption_detail.mustache`

---

### FASE 14: PÁGINAS DE REPORTES

**Análisis Obligatorio**:
- Renderer: `reports_renderer.php`
- Vista: `views/reports.php`
- Método: `prepare_reports_data()`

**Crear**:
1. `pages/reports/index.mustache` (filtro obligatorio por convocatoria)

---

### FASE 15: PÁGINAS DE USUARIO

**Análisis**: Identificar vistas de perfil

**Crear**:
1. `pages/user/profile.mustache`
2. `pages/user/edit_profile.mustache`
3. `pages/user/consent.mustache`
4. `pages/user/notifications.mustache`

---

### FASE 16: MÓDULOS AMD

**Por cada módulo**:
1. Analizar funcionalidad requerida
2. Crear/modificar en `amd/src/`
3. Usar selectores `jb-*`
4. Compilar: `grunt amd --root=local/jobboard`
5. Validar

---

### FASE 17: USER TOURS

**Por cada tour** (15 total):
1. Crear JSON en `db/tours/`
2. Usar selectores `jb-*`
3. Crear strings EN
4. Crear strings ES
5. Validar

---

### FASE 18: VALIDACIÓN FINAL

**Checklist completo** de CSS, templates, strings, AMD, funcionalidad por rol, tours

---

## 9. PROBLEMAS CONOCIDOS A RESOLVER

| Problema | Análisis Requerido | Solución |
|----------|-------------------|----------|
| Botones ilegibles | Revisar clases `.jb-btn-*` en CSS | Asegurar contraste texto/fondo |
| Dashboard sin enlaces | Analizar `prepare_dashboard_page_data()` | Verificar variables por rol |
| Tooltips no aparecen | Revisar `tooltips.js` y templates | Implementar sistema consistente |

---

## 10. COMANDOS ÚTILES

```bash
# Compilar AMD
grunt amd --root=local/jobboard

# Purgar caché
php admin/cli/purge_caches.php

# Buscar strings no traducidas
grep -r "get_string" local/jobboard/*.php | grep -v "local_jobboard"

# Contar líneas
wc -l local/jobboard/styles.css
wc -l local/jobboard/lang/en/local_jobboard.php
```

---

## 11. RESUMEN: EL CICLO POR CADA VISTA

```
╔═══════════════════════════════════════════════════════════════════╗
║                                                                   ║
║   1. VISTA ANALIZADA                                              ║
║      ↓                                                            ║
║      Renderer + Vista PHP → Variables, condiciones, acciones      ║
║                                                                   ║
║   2. MUSTACHE CREADO                                              ║
║      ↓                                                            ║
║      Template con clases jb-*, tooltips, loading, empty state     ║
║                                                                   ║
║   3. ESTILOS CREADOS                                              ║
║      ↓                                                            ║
║      CSS para TODAS las clases jb-* del template                  ║
║                                                                   ║
║   4. CADENAS CREADAS                                              ║
║      ↓                                                            ║
║      EN + ES para TODAS las strings del template                  ║
║                                                                   ║
║   5. VALIDADO Y VERSIONADO                                        ║
║      ↓                                                            ║
║      Funciona, tooltips, idiomas → version.php + CHANGELOG        ║
║                                                                   ║
╚═══════════════════════════════════════════════════════════════════╝
```

---

## 12. CONTACTO

| Rol | Nombre | Email |
|-----|--------|-------|
| Desarrollador | Alonso Arias | soporteplataformas@iser.edu.co |
| Supervisión | Vicerrectoría Académica | viceacademica@iser.edu.co |

---

*AGENTS.md para reconstrucción integral del plugin local_jobboard*
*Versión: 1.0*
*Fecha: 2025-12-13*
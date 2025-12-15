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

## 2. INVENTARIO ACTUAL DEL PLUGIN (ESTADO REAL)

### 2.1 Métricas del Estado Actual

| Elemento | Cantidad | Observación |
|----------|----------|-------------|
| **Capabilities** | 27 | En `db/access.php` |
| **Strings de idioma** | ~2589 | EN y ES con paridad |
| **Líneas CSS** | 3570 | CSS aislado, NO usa Bootstrap de Moodle |
| **Templates Mustache** | 52 | Organizados en components/layouts/pages |
| **Módulos AMD** | 13 | En `amd/src/`, falta `amd/build/` |
| **User Tours** | 15 | En `db/tours/` |
| **Eventos** | 8 | En `classes/event/` |
| **Tareas programadas** | 3 | En `db/tasks.php` |
| **Message providers** | 5 | En `db/messages.php` |
| **Formularios** | 8 | En `classes/forms/` |
| **Renderers** | 10 | En `classes/output/renderer/` |

### 2.2 Estructura de Directorios Actual

```
local/jobboard/
├── admin/                    # 18 archivos PHP de administración
├── ajax/                     # 3 archivos para llamadas AJAX
├── amd/src/                  # 13 módulos JavaScript AMD
├── classes/
│   ├── event/               # 8 eventos
│   ├── forms/               # 8 formularios
│   ├── output/renderer/     # 10 renderers
│   ├── privacy/             # Privacy API
│   ├── task/                # 3 tareas programadas
│   └── trait/               # 1 trait
├── cli/                      # 4 scripts CLI
├── db/
│   ├── tours/               # 15 User Tours
│   └── *.php                # access, install, upgrade, etc.
├── lang/
│   ├── en/                  # Strings en inglés
│   └── es/                  # Strings en español
├── templates/
│   ├── components/          # 16 componentes reutilizables
│   ├── layouts/             # 1 layout base
│   └── pages/               # 10 subdirectorios de vistas
├── tests/                    # 7 archivos de pruebas
├── views/                    # 18 archivos PHP de vistas
├── index.php                 # Punto de entrada principal
├── lib.php                   # 44KB - Funciones de librería
├── public.php                # Punto de entrada público
├── settings.php              # Configuración del plugin
├── signup.php                # Auto-registro
├── styles.css                # 114KB - CSS aislado
└── version.php               # Información de versión
```

---

## 3. OBJETIVO DE ESTA RECONSTRUCCIÓN

Realizar una **reconstrucción TOTAL desde cero** del sistema visual y de idiomas del plugin, garantizando que el resultado final cumpla con TODAS las funcionalidades, vistas, configuraciones y elementos que el plugin tiene planteados en su arquitectura.

**Elementos a reconstruir:**
1. Archivo `styles.css` (mínimo, aprovechando Bootstrap de Moodle 4.5)
2. Todos los templates Mustache en `templates/`
3. Todas las cadenas de idiomas en `lang/en/` y `lang/es/`
4. Todos los módulos AMD en `amd/src/` + compilación en `amd/build/`
5. Todos los User Tours en `db/tours/`

**Garantía de completitud:**
- La reconstrucción debe cubrir el 100% de las vistas del plugin
- TODOS los archivos PHP del plugin deben ser analizados para extraer strings
- Todas las capabilities deben tener strings
- Todas las configuraciones deben tener strings
- Todos los mensajes de error, validación y confirmación deben existir
- El resultado debe ser funcionalmente equivalente o superior al estado actual

---

## 4. PROBLEMAS IDENTIFICADOS Y REORGANIZACIONES PROPUESTAS

### 4.1 Problema: CSS Aislado vs Bootstrap de Moodle

**Estado actual:**
- `styles.css` tiene 3570 líneas (114KB)
- Define TODO desde cero: variables, resets, tipografía, componentes
- Usa prefijo `jb-` pero NO aprovecha Bootstrap de Moodle
- Comentario en archivo: "FULLY ISOLATED CSS - Independent from any Moodle theme"

**Problema:** Esto contradice la filosofía de usar Bootstrap de Moodle y crea:
- Mantenimiento duplicado
- Posibles conflictos con temas
- Tamaño innecesario

**Propuesta:**
- Reducir `styles.css` a <500 líneas
- Usar clases Bootstrap de Moodle 4.5 directamente
- Solo mantener estilos específicos con prefijo `jb-*`

### 4.2 Problema: Duplicación de Archivos

| Archivo 1 | Archivo 2 | Propuesta |
|-----------|-----------|-----------|
| `public.php` (raíz) | `views/public.php` | Unificar en `views/public.php`, `public.php` raíz solo como router |
| `admin/exemptions.php` | `admin/manage_exemptions.php` | Consolidar en `admin/manage_exemptions.php` |
| `views/view_convocatoria.php` | `views/public_convocatoria.php` | Analizar si ambos son necesarios |

### 4.3 Problema: Archivos Demasiado Grandes

| Archivo | Tamaño | Propuesta |
|---------|--------|-----------|
| `admin/migrate.php` | 41KB | Dividir en clases: `classes/migration/*.php` |
| `lib.php` | 44KB | Extraer funciones a clases específicas |
| `classes/email_template.php` | 47KB | Considerar dividir en traits o clases helper |
| `classes/output/renderer/vacancy_renderer.php` | 47KB | Evaluar si se puede modularizar |

### 4.4 Problema: Falta Compilación AMD

**Estado actual:**
- Existen 13 módulos en `amd/src/`
- NO existe directorio `amd/build/`

**Propuesta:**
- Crear `amd/build/` con módulos compilados
- Agregar script de compilación: `grunt amd` o similar

### 4.5 Propuesta: Reorganización de Vistas

**Vistas actuales en `views/` (18 archivos):**

| Archivo | Renderer | Propuesta |
|---------|----------|-----------|
| `application.php` | application_renderer | Mantener |
| `applications.php` | application_renderer | Mantener |
| `apply.php` | application_renderer | Mantener |
| `browse_convocatorias.php` | convocatoria_renderer | Consolidar con `convocatorias.php` |
| `convocatoria.php` | convocatoria_renderer | Mantener |
| `convocatorias.php` | convocatoria_renderer | Mantener |
| `dashboard.php` | dashboard_renderer | Mover lógica a `index.php` |
| `manage.php` | admin_renderer | Mantener |
| `myreviews.php` | review_renderer | Mantener |
| `public.php` | public_renderer | **Unificar con `public.php` raíz** |
| `public_convocatoria.php` | public_renderer | **Evaluar fusión con `view_convocatoria.php`** |
| `public_vacancy.php` | public_renderer | Mantener |
| `reports.php` | reports_renderer | Mantener |
| `review.php` | review_renderer | Mantener |
| `vacancies.php` | vacancy_renderer | Mantener |
| `vacancy.php` | vacancy_renderer | Mantener |
| `view_convocatoria.php` | convocatoria_renderer | **Evaluar fusión** |

---

## 5. ESTRATEGIA DE DESARROLLO SEGURA

### 5.1 Desarrollo en Rama Separada (NO Eliminar en Producción)

**CRÍTICO:** NO eliminar archivos directamente. Usar estrategia de desarrollo paralelo.

```
ESTRATEGIA SEGURA:
==================

1. Crear rama de desarrollo:
   git checkout -b feature/ui-reconstruction

2. Crear archivos nuevos con sufijo temporal:
   - styles_new.css
   - templates_new/
   - lang_new/

3. Desarrollar y probar en paralelo

4. Cuando esté listo:
   - Swap atómico de archivos
   - Eliminar archivos antiguos
   - Commit y merge

5. Si algo falla:
   - Revert inmediato posible
   - Archivos originales intactos
```

### 5.2 Backup Obligatorio

Antes de cualquier cambio:

```bash
# Crear backup con fecha
BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p backups/$BACKUP_DATE
cp -r lang/ backups/$BACKUP_DATE/
cp -r templates/ backups/$BACKUP_DATE/
cp styles.css backups/$BACKUP_DATE/
cp -r amd/src/ backups/$BACKUP_DATE/amd_src/
```

---

## 6. PRINCIPIOS DE DISEÑO

### 6.1 Uso de Bootstrap de Moodle 4.5

**El rediseño DEBE usar las clases de Bootstrap incluidas en Moodle 4.5.**

| Directriz | Descripción |
|-----------|-------------|
| **Bootstrap primero** | Usar clases Bootstrap de Moodle (btn, card, table, badge, alert, etc.) |
| **CSS mínimo** | `styles.css` solo para estilos específicos del plugin que Bootstrap no cubre |
| **Sin duplicar** | NO crear clases propias que ya existen en Bootstrap |
| **Prefijo jb-** | Solo usar prefijo `jb-` para componentes únicos del plugin |

**Clases Bootstrap disponibles en Moodle 4.5:**
- Botones: `btn btn-primary`, `btn btn-secondary`, `btn btn-success`, etc.
- Cards: `card`, `card-header`, `card-body`, `card-footer`
- Tablas: `table`, `table-striped`, `table-hover`, `table-bordered`
- Badges: `badge bg-primary`, `badge bg-success`, `badge bg-warning`
- Alerts: `alert alert-info`, `alert alert-success`, `alert alert-danger`
- Forms: `form-control`, `form-select`, `form-check`, `form-label`
- Grid: `container`, `row`, `col-*`, `d-flex`, `justify-content-*`
- Spacing: `m-*`, `p-*`, `mt-*`, `mb-*`, `ms-*`, `me-*`
- Text: `text-muted`, `text-primary`, `fw-bold`, `fs-*`

### 6.2 Diseño Limpio y Minimalista

**El diseño debe ser lo más limpio posible:**

| Elemento | Directriz |
|----------|-----------|
| **Sin hero sections** | NO usar banners hero, jumbotrons ni secciones destacadas grandes |
| **Sin decoración innecesaria** | NO agregar elementos visuales que no aporten funcionalidad |
| **Espacios en blanco** | Usar espaciado generoso pero sin exceso |
| **Tipografía simple** | Usar la tipografía por defecto de Moodle |
| **Colores del tema** | Usar los colores del tema de Moodle, no colores personalizados |
| **Iconos funcionales** | Iconos solo donde aporten valor (Font Awesome) |
| **Tablas simples** | Tablas con Bootstrap estándar, sin estilos elaborados |
| **Formularios estándar** | Formularios con clases de Moodle/Bootstrap |

### 6.3 Tooltips Obligatorios

Cada vista debe incluir tooltips en:
- Botones de acción
- Iconos sin texto
- Campos de formulario que requieran explicación
- Badges y estados
- Acciones masivas

---

## 7. ANÁLISIS EXHAUSTIVO DE ARCHIVOS

### 7.1 Principio Fundamental

**TODOS los archivos PHP del plugin deben ser analizados para extraer las cadenas de idiomas necesarias.**

### 7.2 Lista Completa de Archivos a Analizar

**Archivos de configuración y definición:**
```
version.php                           # pluginname
settings.php                          # ~40 strings de configuración
db/access.php                         # 27 capabilities
db/install.php                        # Roles personalizados
db/upgrade.php                        # Mensajes de upgrade
db/tasks.php                          # 3 tareas programadas
db/messages.php                       # 5 message providers
db/caches.php                         # Definiciones de cache
```

**Archivos de vistas principales:**
```
index.php                             # Punto de entrada
public.php                            # Vista pública
signup.php                            # Auto-registro
updateprofile.php                     # Actualización de perfil
reupload_document.php                 # Re-subida de documentos
ajax_conversion.php                   # Conversión AJAX
```

**Vistas en `views/` (18 archivos):**
```
views/application.php
views/applications.php
views/apply.php
views/browse_convocatorias.php
views/convocatoria.php
views/convocatorias.php
views/dashboard.php
views/manage.php
views/myreviews.php
views/public.php
views/public_convocatoria.php
views/public_vacancy.php
views/reports.php
views/review.php
views/vacancies.php
views/vacancy.php
views/view_convocatoria.php
```

**Administración en `admin/` (18 archivos):**
```
admin/assign_reviewer.php
admin/bulk_validate.php
admin/doctypes.php
admin/edit.php
admin/exemptions.php
admin/export_documents.php
admin/import_exemptions.php
admin/import_vacancies.php
admin/manage_applications.php
admin/manage_committee.php
admin/manage_exemptions.php
admin/manage_program_reviewers.php
admin/migrate.php
admin/roles.php
admin/schedule_interview.php
admin/templates.php
admin/validate_document.php
```

**Clases principales:**
```
classes/application.php
classes/audit.php
classes/bulk_validator.php
classes/committee.php
classes/convocatoria_exemption.php
classes/data_export.php
classes/document.php
classes/document_services.php
classes/email_template.php
classes/encryption.php
classes/exemption.php
classes/interview.php
classes/notification.php
classes/program_reviewer.php
classes/review_notifier.php
classes/reviewer.php
classes/vacancy.php
```

**Formularios (8 archivos):**
```
classes/forms/application_form.php
classes/forms/convocatoria_form.php
classes/forms/doctype_form.php
classes/forms/email_template_form.php
classes/forms/exemption_form.php
classes/forms/signup_form.php
classes/forms/updateprofile_form.php
classes/forms/vacancy_form.php
```

**Renderers (10 archivos):**
```
classes/output/renderer.php
classes/output/renderer_base.php
classes/output/ui_helper.php
classes/output/renderer/admin_renderer.php
classes/output/renderer/application_renderer.php
classes/output/renderer/committee_renderer.php
classes/output/renderer/convocatoria_renderer.php
classes/output/renderer/dashboard_renderer.php
classes/output/renderer/exemption_renderer.php
classes/output/renderer/public_renderer.php
classes/output/renderer/reports_renderer.php
classes/output/renderer/review_renderer.php
classes/output/renderer/vacancy_renderer.php
```

**Eventos (8 archivos):**
```
classes/event/application_created.php
classes/event/application_status_changed.php
classes/event/document_uploaded.php
classes/event/vacancy_closed.php
classes/event/vacancy_created.php
classes/event/vacancy_deleted.php
classes/event/vacancy_published.php
classes/event/vacancy_updated.php
```

**Tareas (3 archivos):**
```
classes/task/check_closing_vacancies.php
classes/task/cleanup_old_data.php
classes/task/send_notifications.php
```

**CLI (4 archivos):**
```
cli/cli.php
cli/import_vacancies.php
cli/parse_profiles.php
cli/parse_profiles_v2.php
```

**Privacy y otros:**
```
classes/privacy/provider.php
classes/trait/request_helper.php
lib.php
```

**AJAX (3 archivos):**
```
ajax/get_companies.php
ajax/get_convocatorias.php
ajax/get_departments.php
```

---

## 8. FLUJO OBLIGATORIO POR CADA VISTA (CON AMD INTEGRADO)

### 8.1 Secuencia Estricta

```
1. ANALIZAR RENDERER Y ARCHIVOS RELACIONADOS
   ↓
   Abrir classes/output/renderer/*_renderer.php
   Identificar métodos render_*() y prepare_*_data()
   Documentar TODAS las variables del contexto
   Identificar strings usadas en el renderer
   Identificar módulos AMD requeridos
   ↓
2. ANALIZAR VISTA PHP
   ↓
   Abrir views/*.php o admin/*.php
   Identificar strings usadas
   Identificar formularios relacionados
   Identificar interacciones JavaScript necesarias
   ↓
3. CREAR STRINGS DE ARCHIVOS PHP
   ↓
   Agregar a lang/en/ las strings del renderer
   Agregar a lang/en/ las strings de la vista
   Agregar a lang/en/ las strings de clases relacionadas
   Agregar a lang/es/ las traducciones
   ↓
4. CREAR TEMPLATE MUSTACHE
   ↓
   Usar clases Bootstrap de Moodle 4.5
   Diseño limpio sin elementos hero
   Incluir tooltips en elementos interactivos
   Incluir loading skeleton
   Incluir empty state
   Incluir data-attributes para JavaScript
   ↓
5. CREAR STRINGS DEL TEMPLATE
   ↓
   Agregar strings de tooltips
   Agregar strings de empty states
   Agregar strings de validación/error
   Paridad EN/ES
   ↓
6. CREAR/ACTUALIZAR MÓDULO AMD (SI NECESARIO)
   ↓
   Crear/modificar módulo en amd/src/
   Usar selectores data-region o data-action
   Compilar con grunt amd
   Verificar funcionalidad JavaScript
   ↓
7. ACTUALIZAR CSS (SOLO SI NECESARIO)
   ↓
   Agregar SOLO estilos que Bootstrap no cubre
   Usar prefijo jb-* para clases específicas
   Mantener CSS mínimo
   ↓
8. CREAR/ACTUALIZAR USER TOUR (SI APLICA)
   ↓
   Crear JSON en db/tours/
   Usar selectores existentes
   Crear strings del tour
   ↓
9. VALIDAR Y VERSIONAR
   ↓
   Ejecutar checklist de validación
   Verificar renderizado
   Verificar strings en ambos idiomas
   Verificar funcionalidad JavaScript
   Incrementar versión
   Documentar en CHANGELOG
```

### 8.2 Checklist de Validación por Vista

```markdown
## Validación de Vista: [NOMBRE_VISTA]

### Análisis
- [ ] Renderer analizado completamente
- [ ] Variables de contexto documentadas
- [ ] Vista PHP analizada
- [ ] Formularios relacionados analizados
- [ ] Dependencias JavaScript identificadas

### Strings
- [ ] Strings de archivos PHP creadas (EN)
- [ ] Strings de archivos PHP creadas (ES)
- [ ] Strings del template creadas (EN)
- [ ] Strings del template creadas (ES)
- [ ] Tooltips con strings
- [ ] Empty states con strings
- [ ] Mensajes de error con strings

### Template
- [ ] Usa clases Bootstrap de Moodle
- [ ] Diseño limpio sin hero sections
- [ ] Tooltips implementados
- [ ] Loading skeleton implementado
- [ ] Empty state implementado
- [ ] Data-attributes para JavaScript

### JavaScript
- [ ] Módulo AMD creado/actualizado (si necesario)
- [ ] Módulo compilado en amd/build/
- [ ] Funcionalidad verificada

### CSS
- [ ] Solo estilos necesarios agregados
- [ ] Prefijo jb-* usado correctamente

### User Tour
- [ ] Tour creado/actualizado (si aplica)
- [ ] Selectores válidos
- [ ] Strings del tour en EN y ES

### Funcionalidad
- [ ] Vista renderiza correctamente
- [ ] Acciones funcionan
- [ ] Formularios funcionan
- [ ] Sin errores en consola
- [ ] Sin errores PHP
```

---

## 9. FASES DE IMPLEMENTACIÓN (REORGANIZADAS)

### FASE 0: PREPARACIÓN

**Duración estimada:** Preparación inicial

**Tareas:**
1. Crear rama de desarrollo: `git checkout -b feature/ui-reconstruction`
2. Crear backup completo con fecha
3. Crear estructura de directorios temporal:
   - `templates_new/`
   - `lang_new/en/`
   - `lang_new/es/`
   - `styles_new.css`
4. Configurar ambiente de desarrollo
5. Documentar estado inicial en CHANGELOG.md

**Criterio de aceptación:**
- [ ] Rama creada
- [ ] Backup verificado
- [ ] Estructura temporal creada

---

### FASE 1: STRINGS DE BACKEND

**Objetivo:** Crear strings de configuración, capabilities y elementos no relacionados con vistas

**Archivos a analizar:**

| Prioridad | Archivo | Strings Esperadas |
|-----------|---------|-------------------|
| 1 | `version.php` | pluginname |
| 2 | `db/access.php` | 27 capabilities |
| 3 | `settings.php` | ~40 configuraciones |
| 4 | `db/install.php` | Roles del plugin |
| 5 | `db/tasks.php` | 3 tareas programadas |
| 6 | `db/messages.php` | 5 message providers |
| 7 | `classes/event/*.php` | 8 eventos |
| 8 | `cli/*.php` | Mensajes CLI |
| 9 | `classes/privacy/provider.php` | Privacy API |
| 10 | `lib.php` | Strings de funciones |

**Criterio de aceptación:**
- [ ] 27 capabilities con strings
- [ ] Todas las settings con strings y descripciones
- [ ] Todos los eventos con strings
- [ ] Tareas y messages con strings
- [ ] Paridad 100% EN/ES

---

### FASE 2: CSS BASE Y STRINGS COMUNES

**Objetivo:** Crear CSS base mínimo y strings de uso común

**CSS a crear (máximo 500 líneas):**
```css
/* Variables mínimas si necesarias */
:root {
    --jb-status-pending: #ffc107;
    --jb-status-approved: #198754;
    --jb-status-rejected: #dc3545;
}

/* Solo estilos que Bootstrap NO provee */
.jb-document-preview { ... }
.jb-timeline { ... }
.jb-grading-panel { ... }
```

**Strings comunes a crear:**

| Categoría | Ejemplos |
|-----------|----------|
| Acciones | save, cancel, delete, edit, view, create, back, close, confirm |
| Estados | active, inactive, pending, approved, rejected, draft, published |
| Mensajes | success, error, warning, loading, no_results |
| Paginación | page, of, next, previous, showing, results |
| Filtros | filter, search, clear, apply, all, none |
| Confirmación | confirm_delete, confirm_action, are_you_sure |
| Validación | required, invalid, too_long, too_short |

**Criterio de aceptación:**
- [ ] CSS < 500 líneas
- [ ] Sin duplicación de Bootstrap
- [ ] Strings comunes en EN y ES
- [ ] Prefijos usados correctamente (tooltip_, error_, confirm_, empty_, help_)

---

### FASE 3: COMPONENTES UI REUTILIZABLES

**Objetivo:** Crear/reconstruir templates de componentes compartidos

**Componentes en `templates/components/`:**

| Componente | Archivo | Propósito |
|------------|---------|-----------|
| empty_state | `empty_state.mustache` | Estado vacío genérico |
| skeleton | `skeleton.mustache` | Loading skeleton |
| stat_card | `stat_card.mustache` | Tarjeta de estadística |
| badge | `badge.mustache` | Badge de estado |
| alert | `alert.mustache` | Mensajes de alerta |
| button | `button.mustache` | Botón estándar |
| action_buttons | `action_buttons.mustache` | Grupo de acciones |
| card | `card.mustache` | Tarjeta genérica |
| table | `table.mustache` | Tabla con paginación |
| form_group | `form_group.mustache` | Grupo de formulario |
| nav_tabs | `nav_tabs.mustache` | Navegación por tabs |
| breadcrumb | `breadcrumb.mustache` | Migas de pan |
| progress | `progress.mustache` | Barra de progreso |
| timeline | `timeline.mustache` | Línea de tiempo |
| list_group | `list_group.mustache` | Lista de elementos |
| vacancy_card | `vacancy_card.mustache` | Tarjeta de vacante |

**Criterio de aceptación:**
- [ ] Todos los componentes usan Bootstrap
- [ ] Cada componente tiene strings documentadas
- [ ] Tooltips donde aplique
- [ ] Variables de contexto documentadas

---

### FASE 4: DASHBOARD Y NAVEGACIÓN

**Vista:** Dashboard principal (`index.php`, `views/dashboard.php`)
**Renderer:** `dashboard_renderer.php`
**Template:** `templates/pages/admin/dashboard.mustache`
**AMD:** N/A (si no requiere JS específico)
**Tour:** `db/tours/tour_dashboard.json`

**Dependencias:** Ninguna (es el punto de entrada)

**Funcionalidades:**
- Resumen de vacantes activas
- Postulaciones pendientes
- Accesos rápidos
- Estadísticas generales

**Criterio de aceptación:**
- [ ] Dashboard renderiza correctamente
- [ ] Estadísticas muestran datos reales
- [ ] Links de navegación funcionan
- [ ] Tour funcional

---

### FASE 5: VISTAS PÚBLICAS

**Vistas:**
- `public.php` (raíz) → router
- `views/public.php` → lista pública
- `views/public_vacancy.php` → detalle vacante pública
- `views/public_convocatoria.php` → detalle convocatoria pública

**Renderer:** `public_renderer.php`
**Templates:** `templates/pages/public/*.mustache`
**AMD:** `public_filters.js`
**Tour:** `db/tours/tour_public.json`

**Dependencias:** Componentes de Fase 3

**Propuesta de reorganización:**
1. `public.php` (raíz) → solo validar acceso y redirigir a `views/public.php`
2. Consolidar lógica en `views/public.php`

**Criterio de aceptación:**
- [ ] Lista pública de vacantes funciona
- [ ] Filtros funcionan (AMD)
- [ ] Detalle de vacante pública funciona
- [ ] Detalle de convocatoria pública funciona
- [ ] Accesible sin login
- [ ] Tour funcional

---

### FASE 6: CONVOCATORIAS

**Vistas:**
- `views/convocatorias.php` → lista
- `views/convocatoria.php` → detalle/edición
- `views/browse_convocatorias.php` → navegación
- `views/view_convocatoria.php` → vista

**Renderer:** `convocatoria_renderer.php`
**Form:** `convocatoria_form.php`
**Templates:** `templates/pages/convocatorias/*.mustache`
**AMD:** `convocatoria_form.js`
**Tour:** `db/tours/tour_convocatorias.json`, `tour_convocatoria_manage.json`

**Dependencias:** Fase 4 (Dashboard), Fase 3 (Componentes)

**Propuesta de reorganización:**
- Evaluar si `browse_convocatorias.php` y `view_convocatoria.php` pueden consolidarse

**Criterio de aceptación:**
- [ ] CRUD completo de convocatorias
- [ ] Formulario funcional
- [ ] Validaciones funcionan
- [ ] Publicación/despublicación funciona
- [ ] Tours funcionales

---

### FASE 7: VACANTES

**Vistas:**
- `views/vacancies.php` → lista
- `views/vacancy.php` → detalle
- `admin/edit.php` → edición

**Renderer:** `vacancy_renderer.php`
**Form:** `vacancy_form.php`
**Templates:** `templates/pages/vacancies/*.mustache`
**AMD:** `vacancy_form.js`, `card_actions.js`
**Tours:** `db/tours/tour_vacancies.json`, `tour_vacancy.json`

**Dependencias:** Fase 6 (Convocatorias)

**Criterio de aceptación:**
- [ ] CRUD completo de vacantes
- [ ] Asociación con convocatorias funciona
- [ ] Publicación funciona
- [ ] Cierre automático/manual funciona
- [ ] Tours funcionales

---

### FASE 8: POSTULACIONES

**Vistas:**
- `views/apply.php` → formulario de postulación
- `views/applications.php` → mis postulaciones
- `views/application.php` → detalle postulación
- `admin/manage_applications.php` → gestión admin

**Renderer:** `application_renderer.php`
**Form:** `application_form.php`
**Templates:** `templates/pages/applications/*.mustache`
**AMD:** `apply_progress.js`, `application_confirm.js`
**Tours:** `db/tours/tour_apply.json`, `tour_application.json`, `tour_myapplications.json`

**Dependencias:** Fase 7 (Vacantes), Fase 6 (Convocatorias)

**Criterio de aceptación:**
- [ ] Proceso de postulación completo
- [ ] Subida de documentos funciona
- [ ] Progreso visual funciona (AMD)
- [ ] Lista de mis postulaciones funciona
- [ ] Gestión administrativa funciona
- [ ] Tours funcionales

---

### FASE 9: DOCUMENTOS

**Vistas:**
- `reupload_document.php` → re-subida
- `admin/validate_document.php` → validación individual
- `admin/bulk_validate.php` → validación masiva
- `admin/export_documents.php` → exportación

**Renderer:** Parte de `application_renderer.php` y `admin_renderer.php`
**Templates:** `templates/pages/documents/*.mustache`
**AMD:** `document_preview.js`, `bulk_actions.js`
**Tours:** `db/tours/tour_documents.json`, `tour_validate_document.json`

**Dependencias:** Fase 8 (Postulaciones)

**Criterio de aceptación:**
- [ ] Re-subida de documentos funciona
- [ ] Validación individual funciona
- [ ] Validación masiva funciona (AMD)
- [ ] Vista previa funciona (AMD)
- [ ] Exportación funciona
- [ ] Tours funcionales

---

### FASE 10: REVISIÓN Y EVALUACIÓN

**Vistas:**
- `views/review.php` → revisión de postulación
- `views/myreviews.php` → mis revisiones asignadas
- `admin/assign_reviewer.php` → asignación de revisores

**Renderer:** `review_renderer.php`
**Templates:** `templates/pages/review/*.mustache`
**AMD:** `grading_panel.js`
**Tours:** `db/tours/tour_review.json`, `tour_myreviews.json`

**Dependencias:** Fase 8 (Postulaciones), Fase 9 (Documentos)

**Criterio de aceptación:**
- [ ] Panel de calificación funciona (AMD)
- [ ] Asignación de revisores funciona
- [ ] Lista de mis revisiones funciona
- [ ] Historial de revisiones visible
- [ ] Tours funcionales

---

### FASE 11: COMITÉ DE SELECCIÓN

**Vistas:**
- `admin/manage_committee.php` → gestión del comité
- `admin/manage_program_reviewers.php` → revisores por programa
- `admin/schedule_interview.php` → programación de entrevistas

**Renderer:** `committee_renderer.php`
**Templates:** `templates/pages/review/committee.mustache`, etc.
**AMD:** N/A
**Tours:** N/A (o crear si necesario)

**Dependencias:** Fase 10 (Revisión)

**Criterio de aceptación:**
- [ ] Gestión de comité funciona
- [ ] Asignación por programa funciona
- [ ] Programación de entrevistas funciona

---

### FASE 12: EXCEPCIONES Y EXENCIONES

**Vistas:**
- `admin/exemptions.php` → listado
- `admin/manage_exemptions.php` → gestión
- `admin/import_exemptions.php` → importación

**Renderer:** `exemption_renderer.php`
**Form:** `exemption_form.php`
**Templates:** `templates/pages/exemptions/*.mustache`
**AMD:** `exemption_form.js`
**Tours:** N/A

**Dependencias:** Fase 6 (Convocatorias)

**Propuesta de reorganización:**
- Consolidar `exemptions.php` y `manage_exemptions.php`

**Criterio de aceptación:**
- [ ] CRUD de excepciones funciona
- [ ] Importación masiva funciona
- [ ] Asociación con convocatorias funciona

---

### FASE 13: ADMINISTRACIÓN GENERAL

**Vistas:**
- `admin/doctypes.php` → tipos de documento
- `admin/templates.php` → plantillas de email
- `admin/roles.php` → gestión de roles
- `admin/import_vacancies.php` → importación de vacantes
- `admin/migrate.php` → migración de datos

**Renderer:** `admin_renderer.php`
**Forms:** `doctype_form.php`, `email_template_form.php`
**Templates:** `templates/pages/admin/*.mustache`
**AMD:** N/A
**Tours:** N/A

**Dependencias:** Fase 1 (Backend)

**Propuesta de reorganización:**
- `admin/migrate.php` (41KB) → dividir en `classes/migration/migrator.php`

**Criterio de aceptación:**
- [ ] Gestión de doctypes funciona
- [ ] Gestión de templates email funciona
- [ ] Gestión de roles funciona
- [ ] Importación de vacantes funciona
- [ ] Migración funciona (si aplica)

---

### FASE 14: REPORTES

**Vistas:**
- `views/reports.php` → reportes

**Renderer:** `reports_renderer.php`
**Templates:** `templates/pages/reports/index.mustache`
**AMD:** N/A (o crear si necesario)
**Tours:** `db/tours/tour_reports.json`

**Dependencias:** Todas las fases anteriores (necesita datos)

**Criterio de aceptación:**
- [ ] Reportes generan datos correctos
- [ ] Exportación funciona
- [ ] Filtros funcionan
- [ ] Tour funcional

---

### FASE 15: USUARIO Y AUTO-REGISTRO

**Vistas:**
- `signup.php` → auto-registro
- `updateprofile.php` → actualización de perfil

**Forms:** `signup_form.php`, `updateprofile_form.php`
**Templates:** `templates/pages/user/*.mustache`
**AMD:** `signup_form.js`, `progress_steps.js`
**Tours:** N/A

**Dependencias:** Fase 1 (Backend - settings de registro)

**Criterio de aceptación:**
- [ ] Auto-registro funciona
- [ ] reCAPTCHA funciona (si habilitado)
- [ ] Actualización de perfil funciona
- [ ] Validaciones funcionan

---

### FASE 16: VALIDACIÓN FINAL Y COMPILACIÓN

**Tareas:**

1. **Verificar completitud de strings:**
```bash
# Script de verificación
grep -h "get_string\|new lang_string" *.php **/*.php | \
  grep -oP "'[^']+'" | sort | uniq > strings_used.txt

grep -h "^\$string\[" lang/en/local_jobboard.php | \
  grep -oP "'[^']+'" | sort | uniq > strings_defined.txt

diff strings_used.txt strings_defined.txt
```

2. **Compilar módulos AMD:**
```bash
cd /path/to/moodle
grunt amd --plugin=local_jobboard
```

3. **Validar templates:**
```bash
php admin/cli/mustache_lint.php --plugin=local_jobboard
```

4. **Ejecutar tests:**
```bash
vendor/bin/phpunit --testsuite local_jobboard_testsuite
```

5. **Verificar CSS:**
- Confirmar que `styles.css` < 500 líneas
- Verificar que no duplica Bootstrap

6. **Swap de archivos:**
```bash
# Solo cuando todo esté validado
mv templates templates_old
mv templates_new templates
mv lang lang_old
mv lang_new lang
mv styles.css styles_old.css
mv styles_new.css styles.css
```

**Criterio de aceptación:**
- [ ] Sin strings faltantes
- [ ] AMD compilado sin errores
- [ ] Templates válidos
- [ ] Tests pasan
- [ ] CSS mínimo
- [ ] Swap completado
- [ ] Plugin funciona al 100%

---

## 10. MAPA DE DEPENDENCIAS ENTRE FASES

```
FASE 0: Preparación
    │
    ▼
FASE 1: Backend ◄──────────────────────────────────────┐
    │                                                   │
    ├──────────────────────────────────────────────────┼──► FASE 15: Usuario
    │                                                   │
    ▼                                                   │
FASE 2: CSS + Strings Comunes                          │
    │                                                   │
    ▼                                                   │
FASE 3: Componentes UI                                 │
    │                                                   │
    ▼                                                   │
FASE 4: Dashboard ◄────────────────────────────────────┤
    │                                                   │
    ├──► FASE 5: Vistas Públicas                       │
    │                                                   │
    ▼                                                   │
FASE 6: Convocatorias ◄────────────────────────────────┤
    │                                                   │
    ├──► FASE 12: Excepciones                          │
    │                                                   │
    ▼                                                   │
FASE 7: Vacantes                                       │
    │                                                   │
    ▼                                                   │
FASE 8: Postulaciones                                  │
    │                                                   │
    ▼                                                   │
FASE 9: Documentos                                     │
    │                                                   │
    ▼                                                   │
FASE 10: Revisión                                      │
    │                                                   │
    ▼                                                   │
FASE 11: Comité                                        │
    │                                                   │
    ▼                                                   │
FASE 13: Admin General ◄───────────────────────────────┘
    │
    ▼
FASE 14: Reportes
    │
    ▼
FASE 16: Validación Final
```

---

## 11. REGLAS ABSOLUTAS DE DESARROLLO

### 11.1 Reglas de Análisis

| Regla | Descripción |
|-------|-------------|
| **Analizar TODO** | Revisar CADA archivo PHP del plugin para strings |
| **Documentar variables** | Listar TODAS las variables del renderer antes de crear template |
| **No omitir archivos** | Incluir forms, helpers, exceptions, events, etc. |
| **Verificar existentes** | Revisar strings existentes antes de crear nuevas |

### 11.2 Reglas de CSS

| Regla | Descripción |
|-------|-------------|
| **Bootstrap primero** | Usar clases de Bootstrap de Moodle 4.5 |
| **CSS mínimo** | Máximo 500 líneas |
| **Prefijo jb-*** | Para componentes específicos del plugin |
| **Sin duplicar** | No crear clases que ya existen en Bootstrap |
| **Sin reset global** | No resetear estilos de Moodle |

### 11.3 Reglas de Templates

| Regla | Descripción |
|-------|-------------|
| **Diseño limpio** | Sin hero sections, sin decoración innecesaria |
| **Bootstrap** | Usar btn, card, table, badge, alert de Moodle |
| **Tooltips** | En botones, iconos, badges, campos especiales |
| **Loading state** | Skeleton mientras cargan datos |
| **Empty state** | Mensaje simple cuando no hay datos |
| **Sin hardcodear** | Usar strings de idioma SIEMPRE |
| **Data attributes** | Usar `data-region`, `data-action` para JavaScript |

### 11.4 Reglas de Strings

| Regla | Descripción |
|-------|-------------|
| **Backend primero** | Strings de configuración antes que de vistas |
| **PHP luego Mustache** | Strings de archivos PHP antes que del template |
| **Paridad EN/ES** | Toda string en ambos archivos simultáneamente |
| **Prefijos** | Usar: `tooltip_`, `error_`, `confirm_`, `empty_`, `help_` |
| **Sin duplicar** | Reusar strings existentes cuando aplique |

### 11.5 Reglas de AMD/JavaScript

| Regla | Descripción |
|-------|-------------|
| **Integrado en fase** | Crear AMD junto con la vista, NO al final |
| **Compilar siempre** | Ejecutar `grunt amd` después de cambios |
| **Data selectors** | Usar `data-region` y `data-action`, no clases CSS |
| **Sin jQuery directo** | Usar AMD y core/ajax de Moodle |

### 11.6 Reglas de Versionado

| Tipo de Cambio | version.php | release |
|----------------|-------------|---------|
| Fix de strings | +1 | +0.0.1 |
| Vista completa | +1 | +0.0.1 |
| Fase completa | +1 | +0.1.0 |
| Reconstrucción completa | +1 | +1.0.0 |

---

## 12. SCRIPTS DE UTILIDAD

### 12.1 Verificar Paridad de Strings

```bash
#!/bin/bash
# check_string_parity.sh

EN_FILE="lang/en/local_jobboard.php"
ES_FILE="lang/es/local_jobboard.php"

echo "=== Verificando paridad de strings ==="

EN_COUNT=$(grep -c "^\$string\[" $EN_FILE)
ES_COUNT=$(grep -c "^\$string\[" $ES_FILE)

echo "Strings EN: $EN_COUNT"
echo "Strings ES: $ES_COUNT"

if [ "$EN_COUNT" -ne "$ES_COUNT" ]; then
    echo "ERROR: Diferencia en cantidad de strings"

    # Encontrar diferencias
    grep -oP "^\\\$string\['\K[^']+" $EN_FILE | sort > /tmp/en_keys.txt
    grep -oP "^\\\$string\['\K[^']+" $ES_FILE | sort > /tmp/es_keys.txt

    echo "=== En EN pero no en ES ==="
    comm -23 /tmp/en_keys.txt /tmp/es_keys.txt

    echo "=== En ES pero no en EN ==="
    comm -13 /tmp/en_keys.txt /tmp/es_keys.txt
else
    echo "OK: Paridad correcta"
fi
```

### 12.2 Contar Líneas de CSS

```bash
#!/bin/bash
# check_css_size.sh

CSS_FILE="styles.css"
MAX_LINES=500

LINES=$(wc -l < $CSS_FILE)
echo "Líneas en $CSS_FILE: $LINES"

if [ "$LINES" -gt "$MAX_LINES" ]; then
    echo "WARNING: CSS excede $MAX_LINES líneas"
else
    echo "OK: CSS dentro del límite"
fi
```

### 12.3 Listar Strings Usadas vs Definidas

```bash
#!/bin/bash
# check_strings_usage.sh

echo "=== Extrayendo strings usadas ==="
grep -rhoP "get_string\s*\(\s*'[^']+'" --include="*.php" . | \
    grep -oP "'[^']+'" | tr -d "'" | sort | uniq > /tmp/used.txt

echo "=== Extrayendo strings definidas ==="
grep -oP "^\\\$string\['\K[^']+" lang/en/local_jobboard.php | \
    sort | uniq > /tmp/defined.txt

echo "=== Strings usadas pero no definidas ==="
comm -23 /tmp/used.txt /tmp/defined.txt

echo "=== Strings definidas pero no usadas ==="
comm -13 /tmp/used.txt /tmp/defined.txt
```

---

## 13. CHECKLIST FINAL DE COMPLETITUD

### 13.1 Backend

- [ ] `pluginname` definido
- [ ] 27 capabilities con strings
- [ ] ~40 settings con strings y descripciones
- [ ] Roles personalizados con strings
- [ ] 3 tareas programadas con strings
- [ ] 5 message providers con strings
- [ ] 8 eventos con strings
- [ ] Mensajes CLI con strings
- [ ] Privacy API con strings

### 13.2 Frontend

- [ ] 16 componentes reutilizables
- [ ] Dashboard funcional
- [ ] Vistas públicas funcionales
- [ ] Convocatorias CRUD completo
- [ ] Vacantes CRUD completo
- [ ] Postulaciones proceso completo
- [ ] Documentos gestión completa
- [ ] Revisión funcional
- [ ] Comité funcional
- [ ] Excepciones funcionales
- [ ] Administración completa
- [ ] Reportes funcionales
- [ ] Usuario/registro funcional

### 13.3 Técnico

- [ ] CSS < 500 líneas
- [ ] AMD compilado
- [ ] Templates válidos (mustache_lint)
- [ ] Tests pasan
- [ ] Sin errores PHP
- [ ] Sin errores JavaScript
- [ ] Paridad 100% EN/ES
- [ ] User Tours funcionales (15)

---

## 14. CONTACTO

| Rol | Nombre | Email |
|-----|--------|-------|
| Desarrollador | Alonso Arias | soporteplataformas@iser.edu.co |
| Supervisión | Vicerrectoría Académica | viceacademica@iser.edu.co |

---

*AGENTS.md para reconstrucción integral del plugin local_jobboard*
*Versión: 2.0*
*Fecha: 2025-12-15*
*Cambios: Reorganización de fases, integración AMD, métricas reales, dependencias explícitas, scripts de validación*

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

Realizar una **reconstrucción TOTAL desde cero** del sistema visual y de idiomas del plugin, garantizando que el resultado final cumpla con TODAS las funcionalidades, vistas, configuraciones y elementos que el plugin tiene planteados en su arquitectura.

**Elementos a reconstruir:**
1. Archivo `styles.css` completo (por fases, no de una vez)
2. Todos los templates Mustache en `templates/`
3. Todas las cadenas de idiomas en `lang/en/` y `lang/es/`
4. Todos los módulos AMD en `amd/src/`
5. Todos los User Tours en `db/tours/`

**Garantía de completitud:**
- La reconstrucción debe cubrir el 100% de las vistas del plugin
- Todas las capabilities deben tener strings
- Todas las configuraciones de settings.php deben tener strings
- Todos los mensajes de error, validación y confirmación deben existir
- Todos los elementos del backend (eventos, tareas, CLI) deben tener strings
- El resultado debe ser funcionalmente equivalente o superior al estado actual

---

## 3. ACCIÓN INICIAL OBLIGATORIA: ELIMINACIÓN

### 3.1 Antes de Cualquier Creación

**OBLIGATORIO: Eliminar los siguientes elementos antes de iniciar la reconstrucción:**

| Elemento a Eliminar | Ruta | Motivo |
|---------------------|------|--------|
| Carpeta de idiomas | `lang/` | Recrear desde cero con estructura correcta |
| Carpeta de templates | `templates/` | Recrear con nueva arquitectura |
| Archivo de estilos | `styles.css` | Recrear por fases con cada template |

### 3.2 Backup Previo

Antes de eliminar, crear respaldo completo con fecha:
- Copiar `lang/` a carpeta de backup
- Copiar `templates/` a carpeta de backup
- Copiar `styles.css` a carpeta de backup
- Copiar `amd/src/` a carpeta de backup

### 3.3 Verificación Post-Eliminación

Confirmar que las carpetas `lang/` y `templates/` están vacías y que `styles.css` no existe antes de proceder con la Fase 1.

---

## 4. REGLA FUNDAMENTAL: ANÁLISIS ANTES DE CREAR

### 4.1 Principio Inviolable

**NUNCA crear un template, estilo o string de frontend sin antes haber analizado completamente el renderer y la vista PHP correspondiente.**

El análisis previo determina:
- Qué variables están disponibles en el template
- Qué condiciones lógicas existen (permisos, estados, datos)
- Qué acciones y navegación debe soportar la vista
- Qué tooltips son necesarios
- Qué estados de carga y vacío se requieren
- Qué clases CSS serán necesarias

### 4.2 Flujo Obligatorio por Cada Vista

```
VISTA ANALIZADA
     ↓
Estudiar renderer (classes/output/renderer/*_renderer.php)
Estudiar vista PHP (views/*.php o admin/*.php)
Documentar TODAS las variables del método prepare_*_data()
Identificar condiciones, permisos, navegación
Listar tooltips necesarios
     ↓
MUSTACHE CREADO
     ↓
Crear template usando SOLO clases jb-*
Incluir tooltips en elementos interactivos
Incluir skeleton para estados de carga
Incluir empty state para cuando no hay datos
     ↓
ESTILOS CREADOS (solo los de este template)
     ↓
Agregar a styles.css SOLO las clases jb-* usadas en este template
Incluir estados: normal, hover, focus, active, disabled
Incluir variantes responsive si aplica
     ↓
CADENAS CREADAS (solo las de este template)
     ↓
Agregar a lang/en/ SOLO las strings de este template
Agregar a lang/es/ SOLO las traducciones de este template
Incluir strings de tooltips
Incluir strings de estados vacíos y errores
     ↓
VALIDADO Y VERSIONADO
     ↓
Verificar renderizado correcto
Verificar estilos aplicados
Verificar strings en ambos idiomas
Incrementar versión en version.php
Documentar en CHANGELOG.md
```

### 4.3 Regla de Sincronización Incremental

**El CSS y las strings NO se crean de una vez. Se construyen incrementalmente:**

- Por cada template Mustache creado, se agregan ÚNICAMENTE los estilos CSS que ese template necesita
- Por cada template Mustache creado, se agregan ÚNICAMENTE las strings que ese template usa
- El archivo `styles.css` crece con cada template
- Los archivos de idioma crecen con cada template

---

## 5. ORDEN DE CREACIÓN DE CADENAS DE IDIOMAS

### 5.1 Principio: Backend Primero, Frontend Después

Las cadenas de idiomas NO son solo las de las vistas. El plugin tiene strings en múltiples componentes del backend que deben crearse ANTES de las strings de templates.

### 5.2 Orden Obligatorio

**FASE A: Strings del Backend (crear primero)**

| Prioridad | Componente | Archivo Fuente a Analizar |
|-----------|------------|---------------------------|
| 1 | Identificación del plugin | `version.php` |
| 2 | Capabilities | `db/access.php` |
| 3 | Configuración | `settings.php` |
| 4 | Roles personalizados | `db/install.php` |
| 5 | Tareas programadas | `db/tasks.php` |
| 6 | Eventos | `classes/event/*.php` |
| 7 | CLI | `cli/*.php` |
| 8 | Notificaciones | `db/messages.php` |
| 9 | Privacy API | `classes/privacy/*.php` |
| 10 | Excepciones | `classes/exception/*.php` |
| 11 | Servicios externos | `db/services.php` (si aplica) |

**FASE B: Strings de Navegación y Comunes**

| Prioridad | Tipo de Strings |
|-----------|-----------------|
| 12 | Navegación principal (menús, breadcrumbs, tabs) |
| 13 | Acciones comunes (save, cancel, delete, edit, view, create, etc.) |
| 14 | Estados comunes (active, inactive, pending, approved, rejected, etc.) |
| 15 | Mensajes comunes (success, error, warning, info, confirm, loading) |
| 16 | Validaciones comunes (required, invalid, too_long, etc.) |
| 17 | Paginación (page, of, next, previous, first, last, etc.) |
| 18 | Filtros comunes (filter, search, clear, apply, all, none, select) |

**FASE C: Strings de Frontend (por cada template)**

| Prioridad | Tipo de Strings |
|-----------|-----------------|
| 19+ | Strings específicas de cada componente UI reutilizable |
| 20+ | Strings específicas de cada página/template |
| 21+ | Strings de tooltips |
| 22+ | Strings de empty states |
| 23+ | Strings de User Tours |

### 5.3 Verificación de Completitud de Idiomas

Antes de considerar completa la reconstrucción, verificar que existan strings para:

- Todas las capabilities en `db/access.php`
- Todas las settings en `settings.php` (nombre y descripción)
- Todos los roles en `db/install.php`
- Todas las tareas en `db/tasks.php`
- Todos los eventos del plugin
- Todos los mensajes de CLI
- Todos los metadatos de Privacy API
- Todos los templates
- Todos los tooltips
- Paridad completa EN/ES

---

## 6. REGLAS ABSOLUTAS DE DESARROLLO

### 6.1 Reglas de Análisis

| Regla | Descripción |
|-------|-------------|
| **Análisis primero** | NUNCA crear template sin analizar renderer y vista |
| **Documentar variables** | Listar TODAS las variables antes de crear template |
| **Entender condiciones** | Mapear TODAS las condiciones lógicas |
| **Identificar tooltips** | Listar TODOS los elementos que necesitan tooltip |

### 6.2 Reglas de CSS

| Regla | Descripción |
|-------|-------------|
| **SOLO jb-*** | Nunca usar clases Bootstrap directamente |
| **Por fases** | CSS crece con cada template, NO se crea todo de una vez |
| **Variables CSS** | Usar variables de `:root` para colores, espaciados |
| **Mobile-first** | Diseñar primero para móvil |
| **Estados completos** | Cada elemento: normal, hover, focus, active, disabled |
| **Contraste WCAG** | Cumplir AA para texto sobre fondo |

### 6.3 Reglas de Templates

| Regla | Descripción |
|-------|-------------|
| **Documentación** | Bloque de comentario con variables del contexto |
| **No hardcodear** | Usar strings de idioma SIEMPRE |
| **Tooltips** | En botones, iconos, badges, campos especiales |
| **Loading state** | Skeleton mientras cargan datos |
| **Empty state** | Mensaje cuando no hay datos |
| **Accesibilidad** | aria-labels, roles, skip-links |

### 6.4 Reglas de Strings

| Regla | Descripción |
|-------|-------------|
| **Backend primero** | Crear strings de backend antes que las de frontend |
| **Paridad EN/ES** | Toda string en ambos archivos simultáneamente |
| **Prefijos consistentes** | Usar prefijos: `tooltip_`, `error_`, `confirm_`, `empty_`, `help_` |
| **Placeholders** | Usar `{$a}` para valores dinámicos |
| **Sin HTML** | No incluir HTML en strings |

### 6.5 Reglas de Versionado

| Tipo de Cambio | version.php | release |
|----------------|-------------|---------|
| Template + strings | +1 | +0.0.1 |
| Fase completa | +1 | +0.1.0 |
| Bug fix | +1 | +0.0.1 |

---

## 7. PRINCIPIOS UX: MINIMALISMO FUNCIONAL

### 7.1 Filosofía de Diseño

| Principio | Aplicación |
|-----------|------------|
| **Menos es más** | Solo elementos con valor funcional |
| **Espacios en blanco** | Padding y margin generosos |
| **Tipografía limpia** | Máximo 3 tamaños por vista |
| **Colores con propósito** | Solo para estado o acción |
| **Iconografía consistente** | Font Awesome 6, estilo solid |
| **Microinteracciones** | Transiciones 200-300ms |
| **Feedback inmediato** | Respuesta visual instantánea |

### 7.2 Características Visuales

- Fondos blancos o grises claros
- Bordes sutiles
- Sombras mínimas solo en elementos flotantes
- Botones con todos los estados distintos
- Labels siempre visibles
- Tablas con filas alternadas sutiles
- Estados vacíos con icono y mensaje
- Loading con skeletons

### 7.3 Especificación de Tooltips

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

## 8. INVENTARIO DE RENDERERS Y VISTAS A ANALIZAR

### 8.1 Mapeo Renderer → Vista → Templates

| Renderer Trait | Vista PHP | Templates a Crear |
|----------------|-----------|-------------------|
| `dashboard_renderer.php` | `index.php` (view=dashboard) | `pages/admin/dashboard` |
| `convocatoria_renderer.php` | `views/convocatorias.php` | `pages/convocatorias/*` (4) |
| `vacancy_renderer.php` | `views/vacancies.php`, `views/vacancy.php` | `pages/vacancies/*` (7) |
| `application_renderer.php` | `views/applications.php`, `views/apply.php` | `pages/applications/*` (4) |
| `public_renderer.php` | `public.php` | `pages/public/*` (4) |
| `review_renderer.php` | `views/review.php` | `pages/review/*` (6) |
| `committee_renderer.php` | `admin/manage_committee.php` | `pages/review/committee*` (3) |
| `admin_renderer.php` | `admin/*.php` | `pages/admin/*` (9) |
| `exemption_renderer.php` | `admin/manage_exemptions.php` | `pages/admin/exemption*` (3) |
| `reports_renderer.php` | `views/reports.php` | `pages/reports/*` (1) |

### 8.2 Proceso de Análisis por Renderer

Para cada renderer, ANTES de crear cualquier template:

1. Abrir el archivo renderer trait
2. Identificar TODOS los métodos `render_*()`
3. Para cada método render, identificar el método `prepare_*_data()` correspondiente
4. Documentar TODAS las variables que retorna el prepare
5. Identificar condiciones de permisos
6. Identificar navegación y URLs
7. Listar tooltips necesarios
8. Definir estados de carga y vacío

---

## 9. ESTRUCTURA DE ARCHIVOS OBJETIVO

### 9.1 Templates

```
templates/
├── components/                    # 16 componentes reutilizables
├── layouts/                       # 1 layout base
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

### 9.2 Estructura de styles.css

El archivo se construye por fases. Secciones que irán apareciendo conforme se crean templates:

1. Variables CSS (`:root`)
2. Reset y base
3. Sistema de grid
4. Utilidades de espaciado
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

### 9.3 Módulos AMD

| Módulo | Responsabilidad |
|--------|-----------------|
| `tooltips.js` | Sistema de tooltips |
| `public_filters.js` | Filtros AJAX vista pública |
| `review_ui.js` | Interfaz de revisión |
| `document_viewer.js` | Visor de documentos PDF |
| `application_form.js` | Formulario de postulación |
| `navigation.js` | Navegación general |
| `apply_progress.js` | Progreso del formulario |
| `progress_steps.js` | Indicador visual de pasos |
| `bulk_actions.js` | Acciones masivas |
| `grading_panel.js` | Panel revisión estilo mod_assign |
| `vacancy_manage.js` | Gestión de vacantes |
| `convocatoria_manage.js` | Gestión de convocatorias |
| `doctype_manage.js` | Gestión de doctypes |

---

## 10. FASES DE IMPLEMENTACIÓN

### FASE 0: PREPARACIÓN Y ELIMINACIÓN

**Tareas:**
1. Crear backup completo de `lang/`, `templates/`, `styles.css`, `amd/src/`
2. **ELIMINAR** carpeta `lang/` completa
3. **ELIMINAR** carpeta `templates/` completa
4. **ELIMINAR** archivo `styles.css`
5. Crear carpetas vacías: `lang/en/`, `lang/es/`, `templates/`
6. Crear archivo `styles.css` vacío
7. Crear archivos vacíos `lang/en/local_jobboard.php` y `lang/es/local_jobboard.php` con estructura PHP básica
8. Documentar en CHANGELOG.md el inicio de la reconstrucción

---

### FASE 1: STRINGS DEL BACKEND

**Objetivo:** Crear todas las cadenas de idiomas del backend antes de cualquier template

**Archivos a Analizar:**
- `version.php` - identificación del plugin
- `db/access.php` - todas las capabilities (~34)
- `settings.php` - todas las configuraciones
- `db/install.php` - roles personalizados
- `db/tasks.php` - tareas programadas
- `classes/event/*.php` - eventos
- `cli/*.php` - mensajes CLI
- `db/messages.php` - notificaciones
- `classes/privacy/*.php` - Privacy API

**Resultado:** Archivos de idioma con todas las strings del backend en EN y ES

---

### FASE 2: STRINGS COMUNES Y CSS BASE

**Objetivo:** Crear strings de navegación/acciones comunes y variables CSS base

**Strings a crear:**
- Navegación principal
- Acciones comunes
- Estados comunes
- Mensajes comunes
- Validaciones comunes
- Paginación
- Filtros comunes

**CSS a crear:**
- Variables CSS en `:root`
- Reset y estilos base
- Sistema de grid responsivo
- Utilidades de espaciado
- Utilidades flexbox

---

### FASE 3: COMPONENTES UI

**Objetivo:** Crear los 16 componentes reutilizables

**Componentes (en orden de creación):**
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

**Por cada componente:**
1. Analizar dónde se usa
2. Crear template
3. Agregar clases CSS necesarias
4. Agregar strings EN
5. Agregar strings ES
6. Validar

---

### FASE 4: LAYOUT BASE

**Objetivo:** Crear layout común para todas las páginas

**Análisis:** Identificar estructura común de todas las páginas

**Crear:**
- Template de layout base
- Clases CSS del layout
- Strings del layout

---

### FASE 5: DASHBOARD

**Análisis Obligatorio:**
- Renderer: `dashboard_renderer.php`
- Vista: `index.php`
- Método: `prepare_dashboard_page_data()`

**Crear:**
- Template dashboard
- CSS específico del dashboard
- Strings EN del dashboard
- Strings ES del dashboard
- Tooltips en stat cards, quicklinks, notificaciones, timeline

---

### FASE 6: PÁGINAS PÚBLICAS

**Análisis Obligatorio:**
- Renderer: `public_renderer.php`
- Vista: `public.php`
- Métodos: todos los `prepare_public_*_data()`

**Crear (análisis → template → CSS → strings para cada una):**
1. Listado público de convocatorias
2. Detalle público de convocatoria
3. Detalle público de vacante
4. Prompt de login/registro

---

### FASE 7: PÁGINAS DE CONVOCATORIAS

**Análisis Obligatorio:**
- Renderer: `convocatoria_renderer.php`
- Vista: `views/convocatorias.php`
- Métodos: todos los `prepare_convocatoria*_data()`

**Crear:**
1. Listado de convocatorias
2. Formulario de convocatoria
3. Detalle de convocatoria
4. Configuración de documentos

---

### FASE 8: PÁGINAS DE VACANTES

**Análisis Obligatorio:**
- Renderer: `vacancy_renderer.php`
- Vistas: `views/vacancies.php`, `views/vacancy.php`
- Métodos: todos los `prepare_vacancy*_data()`

**Crear:**
1. Listado de vacantes
2. Gestión de vacantes
3. Formulario de vacante
4. Detalle de vacante
5. Postulaciones a vacante
6. Selector de convocatoria
7. Importación de vacantes

---

### FASE 9: PÁGINAS DE POSTULACIONES

**Análisis Obligatorio:**
- Renderer: `application_renderer.php`
- Vistas: `views/applications.php`, `views/apply.php`
- Métodos: todos los `prepare_application*_data()`

**Crear:**
1. Listado de postulaciones
2. Mis postulaciones
3. Formulario de postulación (6 tabs)
4. Detalle de postulación

---

### FASE 10: PÁGINAS DE DOCUMENTOS

**Análisis:** Identificar manejo de documentos en renderers

**Crear:**
1. Listado de documentos
2. Subir documento
3. Detalle de documento

---

### FASE 11: PÁGINAS DE REVISIÓN

**Análisis Obligatorio:**
- Renderer: `review_renderer.php`
- Vista: `views/review.php`
- Métodos: todos los `prepare_review*_data()`

**Crear:**
1. Cola de revisión
2. Panel de revisión (split-pane)
3. Vista de documento
4. Asignar revisor
5. Revisores por programa
6. Programar entrevista

---

### FASE 12: PÁGINAS DE COMITÉ

**Análisis Obligatorio:**
- Renderer: `committee_renderer.php`
- Vista: `admin/manage_committee.php`
- Métodos: todos los `prepare_committee*_data()`

**Crear:**
1. Gestión de comité
2. Miembros del comité
3. Completar entrevista

---

### FASE 13: PÁGINAS DE ADMINISTRACIÓN

**Análisis Obligatorio:**
- Renderer: `admin_renderer.php`
- Vistas: `admin/*.php`
- Métodos: todos los `prepare_*_data()` de admin

**Crear:**
1. Tipos de documento
2. Formulario de tipo de documento
3. Plantillas de email
4. Formulario de plantilla
5. Roles
6. Auditoría
7. Migración
8. Importar vacantes
9. Configuración

---

### FASE 14: PÁGINAS DE EXCEPCIONES

**Análisis Obligatorio:**
- Renderer: `exemption_renderer.php`
- Vista: `admin/manage_exemptions.php`
- Métodos: todos los `prepare_exemption*_data()`

**Crear:**
1. Listado de excepciones
2. Formulario de excepción
3. Detalle de excepción

---

### FASE 15: PÁGINAS DE REPORTES

**Análisis Obligatorio:**
- Renderer: `reports_renderer.php`
- Vista: `views/reports.php`
- Método: `prepare_reports_data()`

**Crear:**
1. Página de reportes (con filtro obligatorio por convocatoria)

---

### FASE 16: PÁGINAS DE USUARIO

**Análisis:** Identificar vistas de perfil

**Crear:**
1. Perfil de postulante
2. Editar perfil
3. Consentimientos
4. Preferencias de notificación

---

### FASE 17: MÓDULOS AMD

**Por cada módulo:**
1. Analizar funcionalidad requerida
2. Crear/modificar módulo
3. Usar selectores jb-*
4. Compilar con grunt
5. Validar

---

### FASE 18: USER TOURS

**Por cada tour (15 total):**
1. Crear JSON del tour
2. Usar selectores jb-*
3. Agregar strings EN de pasos
4. Agregar strings ES de pasos
5. Validar selectores

---

### FASE 19: VALIDACIÓN FINAL

**Verificar completitud total según checklist de Sección 11**

---

## 11. GARANTÍA DE COMPLETITUD

### 11.1 Checklist de Verificación Total

La reconstrucción se considera completa SOLO cuando:

**Backend:**
- [ ] Todas las ~34 capabilities tienen string EN y ES
- [ ] Todas las settings de `settings.php` tienen string y descripción
- [ ] Los 3 roles personalizados tienen nombre y descripción
- [ ] Todas las tareas programadas tienen string
- [ ] Todos los eventos tienen string
- [ ] Todos los mensajes CLI tienen string
- [ ] Privacy API tiene todos los metadatos traducidos

**Frontend:**
- [ ] Los 16 componentes UI están creados y funcionando
- [ ] Todas las páginas tienen template funcional
- [ ] Todos los tooltips implementados
- [ ] Todos los empty states implementados
- [ ] Todos los loading states implementados

**Estilos:**
- [ ] Todas las clases jb-* usadas en templates existen en CSS
- [ ] Todos los estados (hover, focus, active, disabled) implementados
- [ ] Responsive funcional en todas las vistas
- [ ] Compatible con themes: Boost, Classic, Remui, Flavor

**Idiomas:**
- [ ] Paridad 100% EN/ES
- [ ] Sin strings hardcodeadas en PHP
- [ ] Sin strings hardcodeadas en templates

**JavaScript:**
- [ ] 13 módulos AMD compilados y funcionando
- [ ] 15 User Tours funcionando

### 11.2 Métricas Objetivo

| Elemento | Cantidad Mínima |
|----------|-----------------|
| Templates Mustache | 47+ |
| Componentes UI | 16 |
| Strings de idioma | ~3000+ por idioma |
| Clases CSS | ~500+ |
| Módulos AMD | 13 |
| User Tours | 15 |

---

## 12. RESUMEN DEL CICLO

```
╔═══════════════════════════════════════════════════════════════════╗
║  ANTES DE EMPEZAR                                                 ║
║  → Backup completo                                                ║
║  → ELIMINAR /lang, /templates, styles.css                         ║
╠═══════════════════════════════════════════════════════════════════╣
║  FASE 1: STRINGS BACKEND                                          ║
║  → Analizar archivos PHP del backend                              ║
║  → Crear strings de capabilities, settings, roles, eventos, etc.  ║
╠═══════════════════════════════════════════════════════════════════╣
║  FASE 2: STRINGS COMUNES + CSS BASE                               ║
║  → Crear strings de navegación y acciones comunes                 ║
║  → Crear variables CSS y utilidades base                          ║
╠═══════════════════════════════════════════════════════════════════╣
║  FASES 3+: POR CADA VISTA                                         ║
║                                                                   ║
║  1. VISTA ANALIZADA                                               ║
║     → Renderer + Vista PHP → Variables, condiciones, acciones     ║
║                                                                   ║
║  2. MUSTACHE CREADO                                               ║
║     → Template con clases jb-*, tooltips, loading, empty state    ║
║                                                                   ║
║  3. ESTILOS CREADOS (solo los de este template)                   ║
║     → Agregar a CSS las clases jb-* de este template              ║
║                                                                   ║
║  4. CADENAS CREADAS (solo las de este template)                   ║
║     → Agregar a EN/ES las strings de este template                ║
║                                                                   ║
║  5. VALIDADO Y VERSIONADO                                         ║
║     → Funciona → version.php + CHANGELOG                          ║
╠═══════════════════════════════════════════════════════════════════╣
║  VALIDACIÓN FINAL                                                 ║
║  → Verificar completitud total                                    ║
║  → El plugin debe funcionar al 100%                               ║
║  → Cumplir con TODA la funcionalidad planteada                    ║
╚═══════════════════════════════════════════════════════════════════╝
```

---

## 13. CONTACTO

| Rol | Nombre | Email |
|-----|--------|-------|
| Desarrollador | Alonso Arias | soporteplataformas@iser.edu.co |
| Supervisión | Vicerrectoría Académica | viceacademica@iser.edu.co |

---

*AGENTS.md para reconstrucción integral del plugin local_jobboard*
*La reconstrucción debe ser TOTAL y garantizar el funcionamiento completo del plugin*
*Versión: 1.0*
*Fecha: 2025-12-13*
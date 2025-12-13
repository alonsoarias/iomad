# AGENTS.md - RECONSTRUCCIÓN INTEGRAL LOCAL_JOBBOARD

**Documento de instrucciones para agentes de codificación AI**
**Proyecto:** local_jobboard - Sistema de Bolsa de Empleo Docente
**Institución:** ISER - Instituto Superior de Educación Rural
**Autor:** Alonso Arias `<soporteplataformas@iser.edu.co>`
**Supervisión:** Vicerrectoría Académica ISER

---

## 1. INFORMACIÓN DEL PROYECTO

| Campo | Valor |
|-------|-------|
| **Componente** | `local_jobboard` |
| **Tipo** | Plugin local de Moodle |
| **Moodle Soportado** | 4.1 - 4.5 |
| **PHP Mínimo** | 7.4 (Recomendado 8.1+) |
| **Licencia** | GNU GPL v3 or later |
| **Propósito** | Sistema de Bolsa de Empleo para reclutamiento de profesores de cátedra |

### 1.1 Objetivo de Este Documento

Este AGENTS.md guía la **reconstrucción completa desde cero** del sistema visual del plugin, incluyendo:

1. Eliminación y recreación total del archivo `styles.css`
2. Eliminación y recreación total de todos los templates Mustache
3. Recreación completa de todos los módulos AMD con compilación
4. Recreación por fases de todas las cadenas de idiomas (EN/ES)
5. Validación integral de funcionalidad por cada fase completada

---

## 2. REGLAS ABSOLUTAS DE DESARROLLO

**ESTAS REGLAS SON INVIOLABLES. NO HAY EXCEPCIONES.**

| # | Regla |
|---|-------|
| 1 | **ANALIZAR** completamente cada renderer y su vista asociada antes de implementar |
| 2 | **SOLO CLASES jb-*** - Nunca usar clases Bootstrap directamente |
| 3 | **VALIDAR** en plataforma real antes de marcar fase como completada |
| 4 | **NO improvisar** - Seguir estrictamente la estructura documentada |
| 5 | **Paridad EN/ES** - Toda string debe existir en AMBOS idiomas simultáneamente |
| 6 | **NO hardcodear** strings - Usar `get_string()` en PHP y `{{#str}}` en Mustache SIEMPRE |
| 7 | **Documentar** TODO cambio en CHANGELOG.md |
| 8 | **Incrementar versión** con cada fase completada |
| 9 | **Compilar AMD** después de cada modificación de JavaScript |
| 10 | **Tooltips** en TODAS las vistas según especificación |
| 11 | **Template creado = Strings creadas** - Nunca crear template sin sus strings |
| 12 | **Mobile-first** - Diseñar primero para móvil, luego expandir |
| 13 | **Backup ANTES** de eliminar cualquier archivo |

---

## 3. PRINCIPIOS DE DISEÑO UX

### 3.1 Filosofía: Minimalismo Funcional

| Principio | Descripción |
|-----------|-------------|
| **Menos es más** | Eliminar todo elemento visual que no aporte valor funcional directo |
| **Espacios en blanco** | Uso generoso de espaciado para dar respiro visual y jerarquía |
| **Tipografía limpia** | Jerarquía clara con máximo 3 tamaños de fuente por vista |
| **Colores con propósito** | Usar color solo para comunicar estado o llamar a la acción |
| **Iconografía consistente** | Font Awesome 6, un solo estilo (solid o regular), nunca mezclados |
| **Microinteracciones sutiles** | Transiciones suaves de 200-300ms, nunca llamativas |
| **Feedback inmediato** | Cada acción del usuario debe tener respuesta visual instantánea |

### 3.2 Características Visuales Obligatorias

- Fondos predominantemente blancos o grises muy claros
- Bordes sutiles (1px, colores neutros)
- Sombras mínimas solo en elementos flotantes (cards elevadas, modales)
- Botones con estados claros: normal, hover, focus, disabled
- Formularios con labels siempre visibles (no placeholders como labels)
- Tablas con filas alternadas sutiles para legibilidad
- Estados vacíos con ilustración simple y mensaje claro
- Loading states con skeletons, nunca spinners giratorios intrusivos

### 3.3 Requisito de Tooltips

**OBLIGATORIO en cada vista:**

| Elemento | Tooltip Requerido |
|----------|-------------------|
| Botones de acción | Descripción de la acción |
| Iconos sin texto | Explicación del significado |
| Campos de formulario complejos | Instrucciones de llenado |
| Estados y badges | Significado del estado |
| Enlaces secundarios | Destino del enlace |
| Acciones masivas | Efecto de la acción |

**Comportamiento de Tooltips:**
- Aparecer tras 300ms de hover (no inmediatamente)
- Desaparecer inmediatamente al mover el cursor
- Texto conciso (máximo 10 palabras)
- Posicionamiento automático para no salir del viewport
- Accesibles via teclado (focus)

---

## 4. ARQUITECTURA DE RENDERERS

### 4.1 Inventario de Renderer Traits

| Trait | Archivo | Responsabilidad |
|-------|---------|-----------------|
| **dashboard_renderer** | `classes/output/renderer/dashboard_renderer.php` | Dashboard principal por rol |
| **admin_renderer** | `classes/output/renderer/admin_renderer.php` | Páginas admin, doctypes, templates email |
| **vacancy_renderer** | `classes/output/renderer/vacancy_renderer.php` | CRUD vacantes, listados, detalles |
| **application_renderer** | `classes/output/renderer/application_renderer.php` | Postulaciones, formulario apply |
| **convocatoria_renderer** | `classes/output/renderer/convocatoria_renderer.php` | CRUD convocatorias |
| **public_renderer** | `classes/output/renderer/public_renderer.php` | Vista pública sin login |
| **review_renderer** | `classes/output/renderer/review_renderer.php` | Panel revisión documentos |
| **committee_renderer** | `classes/output/renderer/committee_renderer.php` | Comités de selección |
| **exemption_renderer** | `classes/output/renderer/exemption_renderer.php` | Excepciones documentales |
| **reports_renderer** | `classes/output/renderer/reports_renderer.php` | Reportes por convocatoria |

### 4.2 Proceso de Análisis por Renderer

**ANTES de crear cualquier template, el agente DEBE:**

1. Leer completamente el archivo del renderer trait
2. Identificar TODOS los métodos `render_*` y `prepare_*_data`
3. Documentar las variables que cada método pasa al template
4. Identificar condiciones lógicas (permisos, estados, datos vacíos)
5. Mapear la navegación entre vistas relacionadas
6. Listar los tooltips necesarios para cada elemento interactivo
7. Definir los estados de loading/skeleton requeridos
8. Identificar componentes reutilizables

---

## 5. ESTRUCTURA DE TEMPLATES

### 5.1 Organización de Carpetas

```
templates/
├── components/          → Componentes UI reutilizables (16)
├── layouts/             → Layouts base (1)
├── pages/
│   ├── admin/           → Páginas administrativas (9)
│   ├── applications/    → Postulaciones (4)
│   ├── convocatorias/   → Convocatorias (4)
│   ├── documents/       → Documentos (3)
│   ├── public/          → Vista pública (4)
│   ├── reports/         → Reportes (1)
│   ├── review/          → Revisión de documentos (6)
│   ├── user/            → Páginas de usuario (4)
│   └── vacancies/       → Vacantes (7)
└── partials/            → Fragmentos parciales
```

### 5.2 Componentes Reutilizables

| Componente | Propósito |
|------------|-----------|
| `alert.mustache` | Mensajes de alerta (success, warning, danger, info) |
| `breadcrumb.mustache` | Navegación de migas de pan |
| `card.mustache` | Contenedor card genérico |
| `document_item.mustache` | Item de documento en listas |
| `empty_state.mustache` | Estado vacío con mensaje e icono |
| `filter_form.mustache` | Formulario de filtros |
| `loading_skeleton.mustache` | Skeleton para estados de carga |
| `modal.mustache` | Modal genérico |
| `pagination.mustache` | Paginación de resultados |
| `progress_bar.mustache` | Barra de progreso |
| `stat_card.mustache` | Tarjeta de estadística |
| `status_badge.mustache` | Badge de estado |
| `table.mustache` | Tabla genérica con opciones |
| `timeline_item.mustache` | Item de línea de tiempo |
| `tooltip.mustache` | Tooltip reutilizable |
| `vacancy_card.mustache` | Tarjeta de vacante |

### 5.3 Documentación de Template

**Cada template DEBE incluir este bloque de documentación:**

```mustache
{{!
    @template local_jobboard/[ruta]/[nombre]

    [Descripción del template]

    Context variables:
    * variable1 - tipo - Descripción
    * variable2 - tipo - Descripción
    * loading - bool - Estado de carga skeleton

    Example context (json):
    {
        "variable1": "valor",
        "loading": false
    }
}}
```

---

## 6. SISTEMA CSS

### 6.1 Principios del CSS

1. **Independencia Total de Bootstrap**: SOLO clases con prefijo `jb-*`
2. **Variables CSS**: Todas las configuraciones en `:root`
3. **Mobile-First**: Media queries de menor a mayor
4. **Accesibilidad**: Focus states, contraste WCAG AA
5. **Compatibilidad de Themes**: Boost, Classic, Remui, Flavor

### 6.2 Secciones del styles.css

| Sección | Contenido |
|---------|-----------|
| 1 | Variables CSS (`:root`) |
| 2 | Reset y estilos base |
| 3 | Sistema de grid (`jb-row`, `jb-col-*`) |
| 4 | Utilidades de espaciado (`jb-m-*`, `jb-p-*`) |
| 5 | Utilidades flexbox (`jb-d-flex`, `jb-justify-*`) |
| 6 | Tipografía (`jb-fs-*`, `jb-fw-*`, `jb-text-*`) |
| 7 | Colores de fondo (`jb-bg-*`) |
| 8 | Colores de texto (`jb-text-*`) |
| 9 | Botones (todos los estados y variantes) |
| 10 | Cards y contenedores |
| 11 | Badges y etiquetas |
| 12 | Alertas |
| 13 | Tablas |
| 14 | Formularios con validación |
| 15 | List groups |
| 16 | Navegación y tabs |
| 17 | Modales |
| 18 | Progress bars |
| 19 | Componentes específicos (stat-card, vacancy-card, grading-panel) |
| 20 | Sistema de tooltips |
| 21 | Animaciones y transiciones |
| 22 | Estados de carga (skeletons) |
| 23 | Utilidades de accesibilidad |
| 24 | Media queries responsivas |
| 25 | Compatibilidad con themes |

### 6.3 Problema Conocido: Botones Ilegibles

**El agente DEBE asegurar contraste adecuado:**

- Botones con fondo oscuro (primary, success, danger, info, dark): texto blanco
- Botones con fondo claro (warning, light): texto oscuro
- Estados hover/focus: mantener contraste
- Estados disabled: opacidad reducida pero legible

---

## 7. MÓDULOS AMD

### 7.1 Lista de Módulos

| Módulo | Responsabilidad |
|--------|-----------------|
| `tooltips.js` | Sistema de tooltips personalizado |
| `public_filters.js` | Filtros AJAX en vista pública |
| `review_ui.js` | Interfaz de revisión |
| `document_viewer.js` | Visor de documentos PDF |
| `application_form.js` | Formulario de postulación |
| `navigation.js` | Navegación general del plugin |
| `apply_progress.js` | Progreso del formulario con tabs |
| `progress_steps.js` | Indicador visual de pasos |
| `bulk_actions.js` | Acciones masivas con checkboxes |
| `grading_panel.js` | Panel de revisión estilo mod_assign |
| `vacancy_manage.js` | Gestión de vacantes |
| `convocatoria_manage.js` | Gestión de convocatorias |
| `doctype_manage.js` | Gestión de tipos de documento |

### 7.2 Reglas de Desarrollo AMD

| # | Regla |
|---|-------|
| 1 | **NUNCA** editar archivos en `amd/build/` |
| 2 | **SIEMPRE** compilar después de cambios: `grunt amd --root=local/jobboard` |
| 3 | **NO** usar jQuery directamente si existe equivalente en core |
| 4 | **NO** usar librerías Bootstrap JS |
| 5 | **USAR** módulos core: ajax, notification, str, templates, modal_factory |
| 6 | **Selectores** deben usar clases `jb-*` o `data-region` |

---

## 8. USER TOURS

### 8.1 Tours a Implementar (15)

| Tour | Archivo | Audiencia |
|------|---------|-----------|
| Dashboard | `tour_dashboard.json` | Todos |
| Vista pública | `tour_public.json` | Visitantes |
| Convocatorias | `tour_convocatorias.json` | Coordinadores |
| Gestión convocatoria | `tour_convocatoria_manage.json` | Coordinadores |
| Vacantes | `tour_vacancies.json` | Usuarios |
| Vacante detalle | `tour_vacancy.json` | Usuarios |
| Gestión vacantes | `tour_manage.json` | Coordinadores |
| Postulación | `tour_apply.json` | Postulantes |
| Detalle postulación | `tour_application.json` | Postulantes |
| Mis postulaciones | `tour_myapplications.json` | Postulantes |
| Documentos | `tour_documents.json` | Postulantes |
| Panel revisión | `tour_review.json` | Revisores |
| Mis revisiones | `tour_myreviews.json` | Revisores |
| Validar documento | `tour_validate_document.json` | Revisores |
| Reportes | `tour_reports.json` | Coordinadores |

### 8.2 Requisitos de Tours

- Selectores SOLO con clases `jb-*`
- Strings en ambos idiomas (EN/ES)
- Pasos concisos y útiles
- No más de 7 pasos por tour

---

## 9. FASES DE IMPLEMENTACIÓN

### FASE 0: PREPARACIÓN Y BACKUP

**Objetivo**: Crear respaldo completo y documentar estado actual

**Tareas**:
1. Crear backup de `templates/`, `styles.css`, `amd/src/`, `lang/`
2. Documentar inventario actual de archivos
3. Verificar versión actual en `version.php`
4. Documentar en CHANGELOG.md el inicio de reconstrucción

**Criterio de completitud**: Backups verificados, inventario documentado

---

### FASE 1: SISTEMA CSS BASE

**Objetivo**: Crear `styles.css` completo con sistema de clases `jb-*`

**Análisis Requerido**:
- Revisar todas las clases CSS usadas en templates actuales
- Identificar variantes de color, espaciado, tipografía necesarias
- Documentar componentes específicos que requieren estilos únicos

**Problema a Resolver**: Botones con texto ilegible - asegurar contraste

**Criterio de completitud**: CSS cargando, botones legibles, grid funcional, compatible con themes

---

### FASE 2: COMPONENTES BASE

**Objetivo**: Crear los 16 componentes reutilizables

**Orden de Creación**:
1. `loading_skeleton.mustache`
2. `tooltip.mustache`
3. `alert.mustache`
4. `status_badge.mustache`
5. `breadcrumb.mustache`
6. `empty_state.mustache`
7. `card.mustache`
8. `stat_card.mustache`
9. `table.mustache`
10. `pagination.mustache`
11. `filter_form.mustache`
12. `modal.mustache`
13. `progress_bar.mustache`
14. `document_item.mustache`
15. `timeline_item.mustache`
16. `vacancy_card.mustache`

**Por cada componente**:
- Crear template con documentación
- Crear strings EN
- Crear strings ES
- Validar renderizado

**Criterio de completitud**: Todos los componentes renderizando con strings en ambos idiomas

---

### FASE 3: LAYOUT BASE

**Objetivo**: Crear layout base para todas las páginas

**Elementos**:
- Skip link de accesibilidad
- Área de mensajes/alertas
- Contenedor principal con clase identificadora
- Estructura para breadcrumbs
- Área de contenido principal

**Criterio de completitud**: Layout funcional, accesible, responsivo

---

### FASE 4: PÁGINAS DE DASHBOARD

**Objetivo**: Recrear dashboard con vistas por rol

**Análisis Requerido**:
- Estudiar `dashboard_renderer.php`
- Documentar variables de `prepare_dashboard_page_data()`
- Identificar vistas por rol (admin, coordinator, reviewer, applicant)

**Templates**:
- `pages/admin/dashboard.mustache`

**Secciones**:
1. Banner de bienvenida
2. Estadísticas rápidas (stat cards)
3. Accesos rápidos según rol
4. Banner próxima convocatoria a cerrar
5. Notificaciones pendientes
6. Actividad reciente (timeline)
7. Herramientas avanzadas (solo admin)

**Tooltips Requeridos**:
- Cada stat card
- Cada enlace de acceso rápido
- Botones de notificaciones
- Items del timeline

**Problema a Resolver**: Dashboard no muestra enlaces/funciones por rol

**Criterio de completitud**: Dashboard correcto por rol, enlaces funcionando, tooltips presentes

---

### FASE 5: PÁGINAS PÚBLICAS

**Objetivo**: Recrear vistas públicas (sin autenticación)

**Análisis Requerido**:
- Estudiar `public_renderer.php`
- Documentar navegación: convocatorias → vacantes → detalle

**Templates**:
1. `pages/public/index.mustache`
2. `pages/public/convocatoria.mustache`
3. `pages/public/vacancy.mustache`
4. `pages/public/apply_prompt.mustache`

**Tooltips Requeridos**:
- Filtros de búsqueda
- Botones de ver detalle
- Botón postularse
- Badges de estado
- Enlace PDF convocatoria

**Criterio de completitud**: Navegación pública completa, filtros funcionando

---

### FASE 6: PÁGINAS DE CONVOCATORIAS

**Objetivo**: Recrear CRUD de convocatorias

**Análisis Requerido**:
- Estudiar `convocatoria_renderer.php`
- Documentar flujo: listar → crear/editar → configurar documentos → publicar

**Templates**:
1. `pages/convocatorias/list.mustache`
2. `pages/convocatorias/form.mustache`
3. `pages/convocatorias/detail.mustache`
4. `pages/convocatorias/documents.mustache`

**Tooltips Requeridos**:
- Acciones de tabla
- Campos de formulario
- Selector de documentos
- Estados de convocatoria

**Criterio de completitud**: CRUD completo, estados correctos, configuración documentos operativa

---

### FASE 7: PÁGINAS DE VACANTES

**Objetivo**: Recrear CRUD de vacantes

**Análisis Requerido**:
- Estudiar `vacancy_renderer.php`
- Documentar relación vacante-convocatoria-facultad-programa

**Templates**:
1. `pages/vacancies/list.mustache`
2. `pages/vacancies/manage.mustache`
3. `pages/vacancies/form.mustache`
4. `pages/vacancies/detail.mustache`
5. `pages/vacancies/applications.mustache`
6. `pages/vacancies/select_convocatoria.mustache`
7. `pages/vacancies/import.mustache`

**Tooltips Requeridos**:
- Filtros de búsqueda
- Acciones por vacante
- Campos de formulario
- Información de requisitos

**Criterio de completitud**: CRUD completo, filtros funcionando, relaciones correctas

---

### FASE 8: PÁGINAS DE POSTULACIONES

**Objetivo**: Recrear flujo completo de postulación

**Análisis Requerido**:
- Estudiar `application_renderer.php`
- Documentar formulario con pestañas
- Identificar estados y transiciones

**Templates**:
1. `pages/applications/list.mustache`
2. `pages/applications/my.mustache`
3. `pages/applications/apply.mustache`
4. `pages/applications/detail.mustache`

**Estructura del Formulario (Tabs)**:
1. Información Personal
2. Formación Académica
3. Experiencia Laboral
4. Documentos
5. Carta de Intención (texto, NO archivo)
6. Revisión y Envío

**Tooltips Requeridos**:
- Cada pestaña
- Campos obligatorios
- Formatos permitidos
- Botones de navegación

**Criterio de completitud**: Formulario tabs funcional, validaciones correctas

---

### FASE 9: PÁGINAS DE DOCUMENTOS

**Objetivo**: Recrear gestión de documentos

**Análisis Requerido**:
- Documentar estados (pendiente, aprobado, rechazado, requiere corrección)
- Entender relación documento-doctype-convocatoria

**Templates**:
1. `pages/documents/list.mustache`
2. `pages/documents/upload.mustache`
3. `pages/documents/detail.mustache`

**Tooltips Requeridos**:
- Estados de documento
- Botón subir/reemplazar
- Requisitos del documento
- Checklist de verificación

**Criterio de completitud**: Gestión completa, estados claros, preview funcional

---

### FASE 10: PÁGINAS DE REVISIÓN

**Objetivo**: Recrear panel de revisión estilo mod_assign

**Análisis Requerido**:
- Estudiar `review_renderer.php`
- Estudiar `mod/assign/` de Moodle core para patrones UX

**Templates**:
1. `pages/review/list.mustache`
2. `pages/review/panel.mustache`
3. `pages/review/document.mustache`
4. `pages/review/assign_reviewer.mustache`
5. `pages/review/program_reviewers.mustache`
6. `pages/review/schedule_interview.mustache`

**Características del Panel**:
- Layout split-pane
- Navegación AJAX
- Visor PDF inline
- Atajos: J/K (navegar), A (aprobar), R (rechazar), D (descargar), F (fullscreen), S (sidebar), ? (ayuda)

**Tooltips Requeridos**:
- Atajos de teclado
- Botones aprobar/rechazar
- Estados de documento
- Navegación entre documentos

**Criterio de completitud**: Panel funcional con AJAX, atajos operativos

---

### FASE 11: PÁGINAS DE COMITÉ

**Objetivo**: Recrear gestión de comités de selección

**Análisis Requerido**:
- Estudiar `committee_renderer.php`
- Comités son por FACULTAD, no por vacante

**Templates**:
1. `pages/review/committee.mustache`
2. `pages/review/committee_members.mustache`
3. `pages/review/interview_complete.mustache`

**Tooltips Requeridos**:
- Roles del comité
- Búsqueda de usuarios
- Estados de entrevista
- Acciones de evaluación

**Criterio de completitud**: Gestión comités funcional, búsqueda usuarios operativa

---

### FASE 12: PÁGINAS DE ADMINISTRACIÓN

**Objetivo**: Recrear páginas administrativas

**Análisis Requerido**:
- Estudiar `admin_renderer.php`
- Identificar permisos por página

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

**Criterio de completitud**: Todas las funciones administrativas operativas

---

### FASE 13: PÁGINAS DE EXCEPCIONES

**Objetivo**: Recrear gestión de excepciones documentales

**Análisis Requerido**:
- Estudiar `exemption_renderer.php`
- Tipos: excepciones globales vs por convocatoria

**Templates**:
1. `pages/admin/exemptions.mustache`
2. `pages/admin/exemption_form.mustache`
3. `pages/admin/exemption_detail.mustache`

**Tooltips Requeridos**:
- Tipos de excepción
- Documentos afectados
- Alcance de la excepción

**Criterio de completitud**: Sistema de excepciones funcional

---

### FASE 14: PÁGINAS DE REPORTES

**Objetivo**: Recrear sistema de reportes

**Análisis Requerido**:
- Estudiar `reports_renderer.php`
- TODOS los reportes requieren filtro por convocatoria

**Templates**:
1. `pages/reports/index.mustache`

**Tipos de Reportes**:
- Postulaciones por estado
- Documentos por estado
- Carga de trabajo de revisores
- Evaluaciones del comité
- Log de auditoría
- Estadísticas generales

**Tooltips Requeridos**:
- Selector de convocatoria
- Cada tipo de reporte
- Opciones de exportación

**Criterio de completitud**: Reportes funcionales con filtro obligatorio

---

### FASE 15: PÁGINAS DE USUARIO

**Objetivo**: Recrear páginas de perfil de postulante

**Templates**:
1. `pages/user/profile.mustache`
2. `pages/user/edit_profile.mustache`
3. `pages/user/consent.mustache`
4. `pages/user/notifications.mustache`

**Tooltips Requeridos**:
- Campos del perfil
- Opciones de consentimiento
- Impacto de preferencias

**Criterio de completitud**: Perfil completo con gestión de consentimiento

---

### FASE 16: MÓDULOS AMD

**Objetivo**: Recrear todos los módulos JavaScript

**Módulos** (13 total): Ver sección 7.1

**Criterio de completitud**: Todos compilando sin errores, funcionalidad verificada

---

### FASE 17: USER TOURS

**Objetivo**: Recrear los 15 User Tours

**Tours**: Ver sección 8.1

**Criterio de completitud**: Todos los tours funcionando con selectores estables

---

### FASE 18: VALIDACIÓN FINAL

**Objetivo**: Verificar funcionamiento completo

**Checklist**:

| Área | Verificaciones |
|------|----------------|
| CSS | Botones legibles, grid responsivo, compatible themes |
| Templates | Sin errores sintaxis, variables renderizando, tooltips presentes |
| Strings | Paridad EN/ES, sin hardcodeo |
| AMD | Compilados, sin errores consola, AJAX funcional |
| Roles | Admin, Coordinador, Revisor, Postulante, Visitante |
| Tours | Ejecutándose, selectores correctos |

**Criterio de completitud**: Todos los ítems verificados

---

## 10. VERSIONADO

### 10.1 Formato

```php
$plugin->version = YYYYMMDDXX;  // Ej: 2025121301
$plugin->release = 'X.Y.Z';     // Ej: '4.0.0'
```

### 10.2 Incrementos

| Tipo de Cambio | version | release |
|----------------|---------|---------|
| Fase completada | +1 | +0.1.0 |
| Bug fix menor | +1 | +0.0.1 |
| Corrección de string | +1 | Sin cambio |

### 10.3 Formato CHANGELOG

Cada fase completada debe documentarse en CHANGELOG.md con:
- Versión y fecha
- Descripción de la fase
- Templates creados/modificados
- Strings agregadas
- Correcciones realizadas
- Notas técnicas

---

## 11. PRIORIDAD DE EJECUCIÓN

### 11.1 Orden de Prioridad

| Prioridad | Fases | Descripción |
|-----------|-------|-------------|
| **CRÍTICO** | 0-4 | Backup, CSS, Componentes, Layout, Dashboard |
| **ALTO** | 5-8 | Público, Convocatorias, Vacantes, Postulaciones |
| **MEDIO** | 9-11 | Documentos, Revisión, Comité |
| **NORMAL** | 12-15 | Admin, Excepciones, Reportes, Usuario |
| **COMPLEMENTARIO** | 16-18 | AMD, Tours, Validación |

### 11.2 Dependencias

```
Fase 0 (Backup)
    ↓
Fase 1 (CSS)
    ↓
Fase 2 (Componentes)
    ↓
Fase 3 (Layout)
    ↓
Fases 4-15 (Páginas) ←→ Fase 16 (AMD) [paralelo]
    ↓
Fase 17 (Tours)
    ↓
Fase 18 (Validación)
```

---

## 12. RECORDATORIOS CRÍTICOS

### 12.1 Template = Strings

**NUNCA crear un template sin crear simultáneamente sus strings en:**
- `lang/en/local_jobboard.php`
- `lang/es/local_jobboard.php`

### 12.2 Vista = Tooltips

**NUNCA marcar un template como completado sin implementar tooltips en:**
- Todos los botones de acción
- Todos los iconos sin texto
- Todos los campos de formulario complejos
- Todos los badges de estado
- Todas las acciones de tabla

### 12.3 Cambio = Versión + CHANGELOG

**CADA cambio requiere:**
1. Incremento de versión en `version.php`
2. Entrada en `CHANGELOG.md`
3. Compilación AMD si hubo cambios JS

### 12.4 Análisis Antes de Implementación

**ANTES de escribir cualquier código:**
1. Leer el renderer completo
2. Documentar las variables
3. Identificar condiciones
4. Planificar tooltips
5. Definir skeletons

---

## 13. COMPATIBILIDAD

### 13.1 Moodle

- Mínimo: 4.1 (2022112800)
- Máximo probado: 4.5

### 13.2 Themes Compatibles

- Boost (default)
- Classic
- Remui
- Flavor

### 13.3 PHP

- Mínimo: 7.4
- Recomendado: 8.1+

---

*Documento AGENTS.md para reconstrucción integral*
*Proyecto: local_jobboard - ISER*
*Fecha: 2025-12-13*

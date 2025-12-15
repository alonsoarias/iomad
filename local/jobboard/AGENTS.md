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
1. Archivo `styles.css` (mínimo, aprovechando Bootstrap de Moodle 4.5)
2. Todos los templates Mustache en `templates/`
3. Todas las cadenas de idiomas en `lang/en/` y `lang/es/`
4. Todos los módulos AMD en `amd/src/`
5. Todos los User Tours en `db/tours/`

**Garantía de completitud:**
- La reconstrucción debe cubrir el 100% de las vistas del plugin
- TODOS los archivos PHP del plugin deben ser analizados para extraer strings
- Todas las capabilities deben tener strings
- Todas las configuraciones deben tener strings
- Todos los mensajes de error, validación y confirmación deben existir
- El resultado debe ser funcionalmente equivalente o superior al estado actual

---

## 3. ACCIÓN INICIAL OBLIGATORIA: ELIMINACIÓN

### 3.1 Antes de Cualquier Creación

**OBLIGATORIO: Eliminar los siguientes elementos antes de iniciar la reconstrucción:**

| Elemento a Eliminar | Ruta |
|---------------------|------|
| Carpeta de idiomas | `lang/` |
| Carpeta de templates | `templates/` |
| Archivo de estilos | `styles.css` |

### 3.2 Backup Previo

Antes de eliminar, crear respaldo completo con fecha:
- Copiar `lang/` a carpeta de backup
- Copiar `templates/` a carpeta de backup
- Copiar `styles.css` a carpeta de backup
- Copiar `amd/src/` a carpeta de backup

### 3.3 Verificación Post-Eliminación

Confirmar que las carpetas `lang/` y `templates/` están vacías y que `styles.css` no existe antes de proceder.

---

## 4. PRINCIPIOS DE DISEÑO

### 4.1 Uso de Bootstrap de Moodle 4.5

**El rediseño DEBE usar las clases de Bootstrap incluidas en Moodle 4.5.**

| Directriz | Descripción |
|-----------|-------------|
| **Bootstrap primero** | Usar clases Bootstrap de Moodle (btn, card, table, badge, alert, etc.) |
| **CSS mínimo** | `styles.css` solo para estilos específicos del plugin que Bootstrap no cubre |
| **Sin duplicar** | NO crear clases propias que ya existen en Bootstrap |
| **Prefijo jb-** | Solo usar prefijo `jb-` para componentes únicos del plugin |

Esto garantiza que `styles.css` sea pequeño y mantenible.

### 4.2 Diseño Limpio y Minimalista

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

### 4.3 Tooltips Obligatorios

Cada vista debe incluir tooltips en:
- Botones de acción
- Iconos sin texto
- Campos de formulario que requieran explicación
- Badges y estados
- Acciones masivas

---

## 5. ANÁLISIS EXHAUSTIVO DE ARCHIVOS

### 5.1 Principio Fundamental

**TODOS los archivos PHP del plugin deben ser analizados para extraer las cadenas de idiomas necesarias.**

El problema de reconstrucciones incompletas es que no se analizan todos los archivos. Para garantizar completitud:

### 5.2 Lista de Archivos a Analizar

**Archivos de configuración y definición:**
- `version.php`
- `settings.php`
- `lib.php`
- `db/access.php`
- `db/install.php`
- `db/upgrade.php`
- `db/tasks.php`
- `db/messages.php`
- `db/services.php`

**Archivos de vistas:**
- `index.php`
- `public.php`
- `views/*.php` (todos)
- `admin/*.php` (todos)

**Clases:**
- `classes/output/renderer.php`
- `classes/output/renderer/*.php` (todos los traits)
- `classes/form/*.php` (todos los formularios)
- `classes/event/*.php` (todos los eventos)
- `classes/task/*.php` (todas las tareas)
- `classes/privacy/*.php` (Privacy API)
- `classes/exception/*.php` (excepciones)
- `classes/*.php` (todas las demás clases)

**CLI:**
- `cli/*.php` (todos)

**Otros:**
- Cualquier otro archivo PHP que contenga `get_string()` o textos

### 5.3 Método de Análisis

Para CADA archivo PHP:
1. Abrir el archivo
2. Buscar todas las llamadas a `get_string()`
3. Buscar todos los textos que deberían ser strings (mensajes, labels, etc.)
4. Documentar las strings necesarias
5. Verificar que existan en los archivos de idioma

---

## 6. ORDEN DE CREACIÓN DE CADENAS DE IDIOMAS

### 6.1 Principio: Backend Primero, Vistas Después

**Las cadenas se crean en este orden estricto:**

```
FASE 1: STRINGS DE BACKEND (sin relación con vistas)
        ↓
        Analizar archivos de configuración y definición
        Crear strings de: pluginname, capabilities, settings, roles, 
        tareas, eventos, CLI, Privacy API, excepciones
        ↓
FASE 2: POR CADA VISTA
        ↓
        A) Analizar renderer y archivos relacionados
        B) Crear strings de esos archivos (renderer, clases, forms)
        C) Crear template(s) Mustache
        D) Crear strings del template (labels, tooltips, empty states)
```

### 6.2 Detalle del Orden

**PRIMERO - Strings de Backend (Fase 1):**

Estas strings NO tienen relación directa con las vistas del frontend:

| Prioridad | Archivo | Strings |
|-----------|---------|---------|
| 1 | `version.php` | pluginname |
| 2 | `db/access.php` | Todas las capabilities (~34) |
| 3 | `settings.php` | Todas las settings y descripciones |
| 4 | `db/install.php` | Roles personalizados |
| 5 | `db/tasks.php` | Nombres de tareas |
| 6 | `db/messages.php` | Tipos de notificación |
| 7 | `classes/event/*.php` | Nombres de eventos |
| 8 | `cli/*.php` | Mensajes de CLI |
| 9 | `classes/privacy/*.php` | Metadatos Privacy API |
| 10 | `classes/exception/*.php` | Mensajes de excepciones |
| 11 | `lib.php` | Strings de funciones de librería |

**DESPUÉS - Por cada vista (Fase 2+):**

| Paso | Acción |
|------|--------|
| A | Analizar el renderer trait correspondiente |
| B | Analizar la vista PHP correspondiente |
| C | Analizar clases relacionadas (forms, helpers) |
| D | Crear strings de esos archivos PHP |
| E | Crear el/los template(s) Mustache |
| F | Crear strings del template (tooltips, labels, empty states, errores) |

---

## 7. FLUJO OBLIGATORIO POR CADA VISTA

### 7.1 Secuencia Estricta

```
1. ANALIZAR RENDERER
   ↓
   Abrir classes/output/renderer/*_renderer.php
   Identificar métodos render_*() y prepare_*_data()
   Documentar TODAS las variables del contexto
   Identificar strings usadas en el renderer
   ↓
2. ANALIZAR VISTA PHP
   ↓
   Abrir views/*.php o admin/*.php
   Identificar strings usadas
   Identificar formularios relacionados
   ↓
3. ANALIZAR ARCHIVOS RELACIONADOS
   ↓
   Abrir clases de formularios si existen
   Abrir helpers o clases auxiliares
   Documentar strings de estos archivos
   ↓
4. CREAR STRINGS DE ARCHIVOS PHP
   ↓
   Agregar a lang/en/ las strings del renderer
   Agregar a lang/en/ las strings de la vista
   Agregar a lang/en/ las strings de clases relacionadas
   Agregar a lang/es/ las traducciones
   ↓
5. CREAR TEMPLATE MUSTACHE
   ↓
   Usar clases Bootstrap de Moodle 4.5
   Diseño limpio sin elementos hero
   Incluir tooltips en elementos interactivos
   Incluir loading skeleton
   Incluir empty state
   ↓
6. CREAR STRINGS DEL TEMPLATE
   ↓
   Agregar a lang/en/ las strings del template
   Incluir strings de tooltips
   Incluir strings de empty states
   Incluir strings de validación/error
   Agregar a lang/es/ las traducciones
   ↓
7. CREAR/ACTUALIZAR CSS (solo si necesario)
   ↓
   Agregar a styles.css SOLO estilos que Bootstrap no cubre
   Usar prefijo jb-* para clases específicas del plugin
   ↓
8. VALIDAR Y VERSIONAR
   ↓
   Verificar renderizado
   Verificar strings en ambos idiomas
   Incrementar versión
   Documentar en CHANGELOG
```

### 7.2 Verificación de Completitud por Vista

Antes de pasar a la siguiente vista, verificar:

- [ ] Renderer analizado completamente
- [ ] Vista PHP analizada completamente
- [ ] Clases relacionadas analizadas
- [ ] Strings de archivos PHP creadas (EN y ES)
- [ ] Template creado con Bootstrap de Moodle
- [ ] Strings del template creadas (EN y ES)
- [ ] Tooltips implementados
- [ ] CSS agregado (solo si necesario)
- [ ] Vista funciona correctamente

---

## 8. REGLAS ABSOLUTAS DE DESARROLLO

### 8.1 Reglas de Análisis

| Regla | Descripción |
|-------|-------------|
| **Analizar TODO** | Revisar CADA archivo PHP del plugin para strings |
| **Documentar variables** | Listar TODAS las variables del renderer antes de crear template |
| **No omitir archivos** | Incluir forms, helpers, exceptions, events, etc. |

### 8.2 Reglas de CSS

| Regla | Descripción |
|-------|-------------|
| **Bootstrap primero** | Usar clases de Bootstrap de Moodle 4.5 |
| **CSS mínimo** | Solo agregar lo que Bootstrap no provee |
| **Prefijo jb-*** | Para componentes específicos del plugin |
| **Sin duplicar** | No crear clases que ya existen en Bootstrap |

### 8.3 Reglas de Templates

| Regla | Descripción |
|-------|-------------|
| **Diseño limpio** | Sin hero sections, sin decoración innecesaria |
| **Bootstrap** | Usar btn, card, table, badge, alert de Moodle |
| **Tooltips** | En botones, iconos, badges, campos especiales |
| **Loading state** | Skeleton mientras cargan datos |
| **Empty state** | Mensaje simple cuando no hay datos |
| **Sin hardcodear** | Usar strings de idioma SIEMPRE |

### 8.4 Reglas de Strings

| Regla | Descripción |
|-------|-------------|
| **Backend primero** | Strings de configuración antes que de vistas |
| **PHP luego Mustache** | Strings de archivos PHP antes que del template |
| **Paridad EN/ES** | Toda string en ambos archivos simultáneamente |
| **Prefijos** | Usar: `tooltip_`, `error_`, `confirm_`, `empty_`, `help_` |

### 8.5 Reglas de Versionado

| Tipo de Cambio | version.php | release |
|----------------|-------------|---------|
| Vista completa | +1 | +0.0.1 |
| Fase completa | +1 | +0.1.0 |

---

## 9. INVENTARIO DE ARCHIVOS A ANALIZAR

### 9.1 Archivos de Backend (Fase 1)

| Archivo | Strings a Extraer |
|---------|-------------------|
| `version.php` | pluginname |
| `db/access.php` | ~34 capabilities |
| `settings.php` | Todas las configuraciones |
| `db/install.php` | Roles del plugin |
| `db/tasks.php` | Tareas programadas |
| `db/messages.php` | Tipos de mensaje |
| `lib.php` | Funciones de librería |
| `classes/event/*.php` | Todos los eventos |
| `cli/*.php` | Mensajes CLI |
| `classes/privacy/*.php` | Privacy API |
| `classes/exception/*.php` | Excepciones |

### 9.2 Renderers y Vistas (Fase 2+)

| Renderer | Vista PHP | Templates |
|----------|-----------|-----------|
| `dashboard_renderer.php` | `index.php` | dashboard |
| `convocatoria_renderer.php` | `views/convocatorias.php` | convocatorias/* |
| `vacancy_renderer.php` | `views/vacancies.php`, `views/vacancy.php` | vacancies/* |
| `application_renderer.php` | `views/applications.php`, `views/apply.php` | applications/* |
| `public_renderer.php` | `public.php` | public/* |
| `review_renderer.php` | `views/review.php` | review/* |
| `committee_renderer.php` | `admin/manage_committee.php` | review/committee* |
| `admin_renderer.php` | `admin/*.php` | admin/* |
| `exemption_renderer.php` | `admin/manage_exemptions.php` | admin/exemption* |
| `reports_renderer.php` | `views/reports.php` | reports/* |

### 9.3 Formularios

| Archivo | Strings |
|---------|---------|
| `classes/form/convocatoria_form.php` | Labels y validaciones |
| `classes/form/vacancy_form.php` | Labels y validaciones |
| `classes/form/application_form.php` | Labels y validaciones |
| `classes/form/doctype_form.php` | Labels y validaciones |
| `classes/form/exemption_form.php` | Labels y validaciones |
| `classes/form/*.php` | Todos los demás formularios |

---

## 10. FASES DE IMPLEMENTACIÓN

### FASE 0: PREPARACIÓN Y ELIMINACIÓN

**Tareas:**
1. Crear backup de `lang/`, `templates/`, `styles.css`, `amd/src/`
2. **ELIMINAR** carpeta `lang/` completa
3. **ELIMINAR** carpeta `templates/` completa
4. **ELIMINAR** archivo `styles.css`
5. Crear carpetas vacías: `lang/en/`, `lang/es/`, `templates/`
6. Crear archivos de idioma vacíos con estructura PHP básica
7. Crear archivo `styles.css` vacío
8. Documentar en CHANGELOG.md

---

### FASE 1: STRINGS DE BACKEND

**Objetivo:** Crear strings que NO están relacionadas con vistas

**Archivos a analizar:**
- `version.php`
- `db/access.php`
- `settings.php`
- `db/install.php`
- `db/tasks.php`
- `db/messages.php`
- `lib.php`
- `classes/event/*.php`
- `cli/*.php`
- `classes/privacy/*.php`
- `classes/exception/*.php`

**Resultado:** Archivos de idioma con strings de backend en EN y ES

---

### FASE 2: CSS BASE Y STRINGS COMUNES

**Objetivo:** Crear CSS base mínimo y strings de acciones comunes

**CSS a crear:**
- Variables CSS en `:root` (solo las necesarias)
- Estilos específicos del plugin que Bootstrap no cubre
- Usar prefijo `jb-` para clases propias

**Strings comunes:**
- Acciones: save, cancel, delete, edit, view, create, back, close
- Estados: active, inactive, pending, approved, rejected, draft, published
- Mensajes: success, error, warning, confirm, loading
- Paginación: page, of, next, previous, showing, results
- Filtros: filter, search, clear, apply, all

---

### FASE 3: COMPONENTES UI

**Por cada componente:**
1. Definir estructura usando Bootstrap de Moodle
2. Crear template
3. Crear strings del componente
4. Agregar CSS solo si Bootstrap no cubre

**Componentes:**
- loading_skeleton
- empty_state
- stat_card (si no usa card de Bootstrap)
- Otros específicos del plugin

---

### FASE 4 a 18: VISTAS DEL PLUGIN

**Por cada vista, seguir el flujo de la Sección 7:**

| Fase | Vista | Renderer | Archivos Relacionados |
|------|-------|----------|----------------------|
| 4 | Dashboard | dashboard_renderer | index.php |
| 5-8 | Públicas | public_renderer | public.php |
| 9-12 | Convocatorias | convocatoria_renderer | views/convocatorias.php, forms |
| 13-19 | Vacantes | vacancy_renderer | views/vacancies.php, forms |
| 20-23 | Postulaciones | application_renderer | views/apply.php, forms |
| 24-26 | Documentos | - | Análisis de manejo de docs |
| 27-32 | Revisión | review_renderer | views/review.php |
| 33-35 | Comité | committee_renderer | admin/manage_committee.php |
| 36-44 | Administración | admin_renderer | admin/*.php, forms |
| 45-47 | Excepciones | exemption_renderer | admin/manage_exemptions.php |
| 48 | Reportes | reports_renderer | views/reports.php |
| 49-52 | Usuario | - | Perfil, consentimientos |

**Para cada fase:**
1. Analizar renderer
2. Analizar vista PHP
3. Analizar formularios y clases relacionadas
4. Crear strings de archivos PHP
5. Crear template(s)
6. Crear strings del template
7. Agregar CSS si necesario
8. Validar y versionar

---

### FASE 53: MÓDULOS AMD

**Por cada módulo:**
1. Analizar funcionalidad
2. Crear/modificar módulo
3. Usar selectores de Bootstrap o `data-region`
4. Compilar
5. Validar

---

### FASE 54: USER TOURS

**Por cada tour:**
1. Crear JSON
2. Usar selectores de Bootstrap o clases existentes
3. Crear strings EN
4. Crear strings ES
5. Validar

---

### FASE 55: VALIDACIÓN FINAL

**Verificar completitud según Sección 11**

---

## 11. GARANTÍA DE COMPLETITUD

### 11.1 Checklist de Verificación

**Archivos analizados:**
- [ ] TODOS los archivos en `db/` analizados
- [ ] TODOS los archivos en `classes/` analizados
- [ ] TODOS los archivos en `views/` analizados
- [ ] TODOS los archivos en `admin/` analizados
- [ ] TODOS los archivos en `cli/` analizados
- [ ] `lib.php` analizado
- [ ] `index.php` analizado
- [ ] `public.php` analizado
- [ ] `settings.php` analizado

**Strings de backend:**
- [ ] Todas las capabilities tienen string
- [ ] Todas las settings tienen string y descripción
- [ ] Todos los roles tienen string
- [ ] Todas las tareas tienen string
- [ ] Todos los eventos tienen string
- [ ] Todos los mensajes CLI tienen string
- [ ] Privacy API completa

**Strings de frontend:**
- [ ] Todos los templates tienen strings
- [ ] Todos los tooltips tienen strings
- [ ] Todos los empty states tienen strings
- [ ] Todos los formularios tienen strings
- [ ] Paridad 100% EN/ES

**Templates:**
- [ ] Todos usan Bootstrap de Moodle
- [ ] Diseño limpio sin hero sections
- [ ] Tooltips implementados
- [ ] Loading states implementados
- [ ] Empty states implementados

**CSS:**
- [ ] Tamaño mínimo (Bootstrap hace el trabajo)
- [ ] Solo estilos específicos del plugin

---

## 12. RESUMEN DEL PROCESO

```
╔═══════════════════════════════════════════════════════════════════╗
║  PASO 0: PREPARACIÓN                                              ║
║  → Backup completo                                                ║
║  → ELIMINAR /lang, /templates, styles.css                         ║
╠═══════════════════════════════════════════════════════════════════╣
║  PASO 1: STRINGS DE BACKEND                                       ║
║  → Analizar: version, db/*, lib, classes/event, cli, privacy      ║
║  → Crear strings de backend (NO relacionadas con vistas)          ║
╠═══════════════════════════════════════════════════════════════════╣
║  PASO 2: CSS BASE + STRINGS COMUNES                               ║
║  → CSS mínimo (Bootstrap de Moodle hace el trabajo)               ║
║  → Strings de acciones y mensajes comunes                         ║
╠═══════════════════════════════════════════════════════════════════╣
║  PASOS 3+: POR CADA VISTA                                         ║
║                                                                   ║
║  A) ANALIZAR RENDERER Y ARCHIVOS RELACIONADOS                     ║
║     → Renderer, vista PHP, formularios, helpers                   ║
║                                                                   ║
║  B) CREAR STRINGS DE ARCHIVOS PHP                                 ║
║     → Strings del renderer, vista, formularios                    ║
║                                                                   ║
║  C) CREAR TEMPLATE MUSTACHE                                       ║
║     → Bootstrap de Moodle, diseño limpio, tooltips                ║
║                                                                   ║
║  D) CREAR STRINGS DEL TEMPLATE                                    ║
║     → Labels, tooltips, empty states, errores                     ║
║                                                                   ║
║  E) ACTUALIZAR CSS (solo si necesario)                            ║
║     → Solo lo que Bootstrap no cubre                              ║
║                                                                   ║
║  F) VALIDAR Y VERSIONAR                                           ║
╠═══════════════════════════════════════════════════════════════════╣
║  VALIDACIÓN FINAL                                                 ║
║  → Verificar que TODOS los archivos fueron analizados             ║
║  → Verificar completitud de strings                               ║
║  → El plugin debe funcionar al 100%                               ║
╚═══════════════════════════════════════════════════════════════════╝
```

---

## 13. NOTAS IMPORTANTES

### 13.1 Sobre el CSS

El uso de Bootstrap de Moodle 4.5 significa que `styles.css` debe ser **pequeño**. La mayoría de estilos vienen de Bootstrap. Solo agregar:
- Variables CSS si son necesarias
- Estilos para componentes únicos del plugin (con prefijo `jb-`)
- Ajustes menores que Bootstrap no provea

### 13.2 Sobre el Diseño

- **NO** usar hero sections
- **NO** usar jumbotrons
- **NO** usar decoración visual innecesaria
- **SÍ** usar las clases estándar de Bootstrap: `btn`, `card`, `table`, `badge`, `alert`, `form-control`, etc.
- **SÍ** mantener el diseño funcional y limpio

### 13.3 Sobre las Strings

El problema de reconstrucciones incompletas es que solo se analizan las vistas. Para evitarlo:
- Analizar CADA archivo PHP del plugin
- Las strings de backend se crean PRIMERO
- Las strings de cada vista se crean EN DOS MOMENTOS:
  1. Strings de los archivos PHP (renderer, vista, forms)
  2. Strings del template Mustache

---

## 14. CONTACTO

| Rol | Nombre | Email |
|-----|--------|-------|
| Desarrollador | Alonso Arias | soporteplataformas@iser.edu.co |
| Supervisión | Vicerrectoría Académica | viceacademica@iser.edu.co |

---

*AGENTS.md para reconstrucción integral del plugin local_jobboard*
*Usando Bootstrap de Moodle 4.5 - Diseño limpio sin elementos hero*
*Análisis exhaustivo de TODOS los archivos PHP para strings completas*
*Versión: 1.0*
*Fecha: 2025-12-14*
# AGENTS.md - local_jobboard

Plugin de Moodle para gestión de vacantes académicas y postulaciones docentes.
Sistema de Bolsa de Empleo para reclutamiento de profesores de cátedra.

## Información del Proyecto

| Campo | Valor |
|-------|-------|
| **Componente** | `local_jobboard` |
| **Versión actual** | 3.0.8 |
| **Moodle requerido** | 4.1+ (2022112800) |
| **Moodle soportado** | 4.1 - 4.5 |
| **Madurez** | MATURITY_STABLE |
| **Licencia** | GNU GPL v3 or later |
| **Institución** | ISER (Instituto Superior de Educación Rural) |
| **Autor** | Alonso Arias <soporteplataformas@iser.edu.co> |
| **Supervisión** | Vicerrectoría Académica ISER |

---

## Arquitectura IOMAD ISER

El plugin opera en un entorno IOMAD multi-tenant con estructura de 4 niveles:

### PARTE A: Estructura Organizacional (Multi-tenant)

```
NIVEL 1: INSTANCIA IOMAD
         virtual.iser.edu.co
              │
NIVEL 2: COMPANIES (16 Centros Tutoriales)
         ├── Pamplona (Sede Principal)
         ├── Cúcuta
         ├── Tibú
         ├── Ocaña
         ├── Toledo
         ├── El Tarra
         ├── Sardinata
         ├── San Vicente del Chucurí
         ├── Pueblo Bello
         ├── San Pablo
         ├── Santa Rosa del Sur
         ├── Fundación
         ├── Cimitarra
         ├── Salazar
         ├── Tame
         └── Saravena
              │
NIVEL 3: DEPARTMENTS (Modalidades por Centro)
         ├── Presencial
         ├── A Distancia
         ├── Virtual
         └── Híbrida
              │
NIVEL 4: SUB-DEPARTMENTS (Facultades por Modalidad)
         ├── Facultad de Ciencias Administrativas y Sociales (FCAS)
         └── Facultad de Ingenierías e Informática (FII)
```

### PARTE B: Estructura Académica

```
FACULTAD DE CIENCIAS ADMINISTRATIVAS Y SOCIALES (FCAS)
├── Tecnología en Gestión Empresarial
├── Tecnología en Gestión Comunitaria
├── Tecnología en Gestión de Mercadeo
└── Técnica Prof. en Seguridad y Salud en el Trabajo

FACULTAD DE INGENIERÍAS E INFORMÁTICA (FII)
├── Tecnología Agropecuaria
├── Tecnología en Procesos Agroindustriales
├── Tecnología en Gestión Industrial
├── Tecnología en Gestión de Redes y Sistemas Teleinformáticos
├── Tecnología en Gestión y Construcción de Obras Civiles
└── Técnica Prof. en Producción de Frutas y Hortalizas
```

---

## Estado Actual del Plugin (Análisis)

### Estructura de Archivos Existente

```
local/jobboard/
├── index.php                      # Router centralizado
├── lib.php                        # Funciones principales
├── settings.php                   # Configuración admin
├── version.php                    # Versión del plugin
├── styles.css                     # Estilos CSS
│
├── views/                         # 17 vistas PHP
│   ├── dashboard.php
│   ├── browse_convocatorias.php
│   ├── convocatorias.php
│   ├── convocatoria.php
│   ├── view_convocatoria.php
│   ├── vacancies.php
│   ├── vacancy.php
│   ├── apply.php
│   ├── applications.php
│   ├── application.php
│   ├── manage.php
│   ├── review.php
│   ├── myreviews.php
│   ├── reports.php
│   ├── public.php
│   ├── public_convocatoria.php
│   └── public_vacancy.php
│
├── templates/                     # ~39 plantillas Mustache
│   ├── dashboard.mustache
│   ├── dashboard_widget.mustache
│   ├── application_row.mustache
│   ├── components/
│   │   ├── page_header.mustache
│   │   ├── stat_card.mustache
│   │   └── filter_form.mustache
│   └── pages/
│       ├── dashboard.mustache
│       ├── manage.mustache
│       ├── apply.mustache
│       ├── application_detail.mustache
│       ├── vacancy_detail.mustache
│       └── review.mustache
│
├── amd/                           # ~15 módulos JavaScript
│   ├── src/
│   │   ├── public_filters.js
│   │   ├── department_loader.js
│   │   ├── company_loader.js
│   │   ├── convocatoria_loader.js
│   │   ├── tooltips.js
│   │   ├── signup_form.js
│   │   ├── apply_progress.js
│   │   ├── review_ui.js
│   │   ├── card_actions.js
│   │   ├── confirm_action.js
│   │   ├── review_shortcuts.js
│   │   └── loading_states.js
│   └── build/                     # JS compilado (NO EDITAR)
│
├── db/
│   ├── install.xml                # Esquema de BD
│   ├── install.php                # Instalación
│   ├── upgrade.php                # Migraciones
│   ├── access.php                 # ~30 capabilities
│   ├── services.php               # Web services
│   └── tours/                     # 15 User Tours JSON
│       ├── tour_dashboard.json
│       ├── tour_public.json
│       ├── tour_convocatorias.json
│       ├── tour_convocatoria_manage.json
│       ├── tour_vacancies.json
│       ├── tour_vacancy.json
│       ├── tour_manage.json
│       ├── tour_apply.json
│       ├── tour_application.json
│       ├── tour_myapplications.json
│       ├── tour_documents.json
│       ├── tour_review.json
│       ├── tour_myreviews.json
│       ├── tour_validate_document.json
│       └── tour_reports.json
│
├── classes/
│   ├── output/renderer.php
│   ├── audit.php
│   ├── document.php
│   ├── reviewer.php
│   └── external/api.php
│
├── cli/
│   ├── cli.php                    # Importador de perfiles v2.2
│   └── parse_profiles_v2.php
│
├── admin/                         # Páginas administrativas
│   ├── doctypes.php
│   ├── email_templates.php
│   └── exemptions.php
│
└── lang/
    ├── en/local_jobboard.php      # ~1860 strings
    └── es/local_jobboard.php      # ~1860 strings
```

### Roles Existentes (3)

| Shortname | Nombre | Capabilities Asignadas |
|-----------|--------|------------------------|
| `jobboard_reviewer` | Revisor de Documentos | view, viewinternal, review, validatedocuments, reviewdocuments, downloadanydocument |
| `jobboard_coordinator` | Coordinador de Selección | view, viewinternal, manage, createvacancy, editvacancy, publishvacancy, viewallvacancies, viewallapplications, changeapplicationstatus, assignreviewers, viewreports, viewevaluations, manageworkflow |
| `jobboard_committee` | Miembro del Comité | view, viewinternal, evaluate, viewevaluations, downloadanydocument |

### Capabilities Existentes (~30)

| Grupo | Capabilities |
|-------|--------------|
| **Vista general** | `view`, `viewinternal`, `viewpublicvacancies` |
| **Gestión vacantes** | `manage`, `createvacancy`, `editvacancy`, `deletevacancy`, `publishvacancy`, `viewallvacancies` |
| **Convocatorias** | `manageconvocatorias` |
| **Postulaciones** | `apply`, `viewownapplications`, `viewallapplications`, `changeapplicationstatus` |
| **Revisión** | `review`, `validatedocuments`, `reviewdocuments`, `assignreviewers`, `downloadanydocument` |
| **Evaluación** | `evaluate`, `viewevaluations` |
| **Workflow** | `manageworkflow` |
| **Reportes** | `viewreports`, `exportreports`, `exportdata` |
| **Administración** | `configure`, `managedoctypes`, `manageemailtemplates`, `manageexemptions` |

### Tablas de Base de Datos Existentes

| Tabla | Descripción |
|-------|-------------|
| `local_jobboard_convocatoria` | Convocatorias |
| `local_jobboard_vacancy` | Vacantes |
| `local_jobboard_application` | Postulaciones |
| `local_jobboard_document` | Documentos subidos |
| `local_jobboard_doc_validation` | Validaciones de documentos |
| `local_jobboard_doctype` | Tipos de documento |
| `local_jobboard_email_template` | Plantillas de email |
| `local_jobboard_email_strings` | Strings de email por idioma |
| `local_jobboard_exemption` | Excepciones por edad |
| `local_jobboard_config` | Configuración |
| `local_jobboard_audit` | Auditoría |
| `local_jobboard_applicant_profile` | Perfiles de postulantes |
| `local_jobboard_consent` | Consentimientos |

---

## ⚠️ PROBLEMAS IDENTIFICADOS Y REFACTORIZACIONES REQUERIDAS

### 1. MEZCLA DE CLASES CSS (Bootstrap + jb-*)

**PROBLEMA:** Los templates Mustache actuales mezclan clases Bootstrap con clases propias `jb-*`.

**Ejemplo del estado actual en `dashboard.mustache`:**
```html
<!-- PROBLEMA: Mezcla de Bootstrap y jb-* -->
<div class="card shadow-sm mb-4">           <!-- Bootstrap -->
    <div class="card-header bg-light">      <!-- Bootstrap -->
        <div class="jb-stat-card">          <!-- Propio -->
```

**Clases Bootstrap usadas actualmente (ELIMINAR):**
- Layout: `row`, `col-*`, `col-md-*`, `col-lg-*`, `mb-*`, `mt-*`, `p-*`
- Cards: `card`, `card-header`, `card-body`, `card-footer`, `shadow-sm`
- Botones: `btn`, `btn-primary`, `btn-secondary`, `btn-outline-*`, `btn-sm`, `btn-lg`, `btn-block`, `btn-group`
- Tablas: `table`, `table-hover`, `table-responsive`, `thead-light`
- Badges: `badge`, `badge-*`
- Alertas: `alert`, `alert-*`, `alert-dismissible`
- Formularios: `form-control`, `form-group`, `form-row`, `input-group`
- Texto: `text-muted`, `text-primary`, `text-*`, `font-weight-*`
- Utilidades: `d-flex`, `d-none`, `justify-content-*`, `align-items-*`
- Otros: `list-group`, `list-group-item`, `progress`, `progress-bar`

**ACCIÓN REQUERIDA:**
1. Auditar TODOS los templates Mustache
2. Crear clases `jb-*` equivalentes para cada clase Bootstrap
3. Reemplazar sistemáticamente en cada template
4. Actualizar `styles.css` con el sistema CSS personalizado completo

### 2. USER TOURS CON SELECTORES OBSOLETOS

**PROBLEMA:** Los 15 tours existentes usan selectores que mezclan Bootstrap y clases propias.

**Ejemplo de selectores problemáticos encontrados:**
```json
// tour_manage.json - Selectores actuales
".table.table-hover"        // Bootstrap
".thead-light"              // Bootstrap  
".badge"                    // Bootstrap
".btn-group.btn-group-sm"   // Bootstrap
".card.shadow-sm"           // Bootstrap
```

**ACCIÓN REQUERIDA:**
1. RECREAR COMPLETAMENTE los 15 tours
2. Usar SOLO selectores con clases `jb-*`
3. Validar cada selector con DevTools antes de implementar
4. Probar cada tour paso a paso en la interfaz

### 3. ROLES SIN CONTEXTO DE FACULTAD/PROGRAMA

**PROBLEMA:** Los roles actuales son globales (CONTEXT_SYSTEM) y no contemplan:
- Asignación de revisores por PROGRAMA
- Comités de selección por FACULTAD
- Filtrado de postulaciones según contexto del usuario

**ACCIÓN REQUERIDA:**
1. RECREAR los 3 roles existentes con nueva lógica
2. Crear tablas de asignación por facultad/programa
3. Implementar filtrado de datos según contexto
4. Actualizar capabilities en `db/access.php`

### 4. TABLAS FALTANTES PARA NUEVA LÓGICA

**PROBLEMA:** No existen tablas para soportar la estructura de facultades/programas.

**TABLAS A CREAR:**
```
local_jobboard_faculty           # Facultades (FCAS, FII)
local_jobboard_program           # Programas por facultad
local_jobboard_committee         # Comités por facultad y convocatoria
local_jobboard_committee_member  # Miembros del comité
local_jobboard_reviewer_program  # Asignación de revisores por programa
```

### 5. MÓDULOS AMD CON DEPENDENCIAS PROBLEMÁTICAS

**PROBLEMA:** Algunos módulos AMD dependen de jQuery y Bootstrap JS.

**Módulos a revisar:**
- `tooltips.js` - Usa `$(selector).tooltip()` de Bootstrap
- `public_filters.js` - Usa jQuery directamente
- `review_ui.js` - Inicializa tooltips Bootstrap

**ACCIÓN REQUERIDA:**
1. Auditar cada módulo AMD
2. Reemplazar `$(selector).tooltip()` por solución propia o core de Moodle
3. Minimizar uso de jQuery donde sea posible
4. Usar módulos core de Moodle: `core/ajax`, `core/notification`, `core/templates`

### 6. VISTAS PHP CON HTML DIRECTO

**PROBLEMA:** Algunas vistas PHP generan HTML directamente en lugar de usar templates.

**Ejemplo encontrado en `view_convocatoria.php`:**
```php
// HTML directo mezclado con lógica
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::tag('div', $content, ['class' => 'card-header bg-light']);
```

**ACCIÓN REQUERIDA:**
1. Identificar secciones con HTML directo en cada vista
2. Crear templates Mustache correspondientes
3. Usar renderer para pasar datos a templates
4. Eliminar `html_writer` con clases Bootstrap

---

## Reglas de Negocio Críticas

### Organización por Facultad (NUEVA LÓGICA)

1. **Vacantes separadas por facultad** - Las vacantes se organizan y filtran por facultad
2. **Comité de selección por FACULTAD** - NO por vacante. Cada facultad tiene su propio comité
3. **Revisores asignados por PROGRAMA** - Los revisores de documentos se asignan a nivel de programa académico

### Creación de Comité de Selección

- Al crear el comité, debe permitir **buscar/filtrar usuarios por username**
- El comité evalúa TODAS las vacantes de su facultad
- Un usuario puede pertenecer a comités de diferentes facultades

### Convocatorias

- **PDF adjunto obligatorio:** Al crear la convocatoria se debe cargar un PDF con el detalle completo
- **Descripción breve:** Campo de texto para resumen de la convocatoria
- **Botón de acceso al PDF:** Visible en la vista de la convocatoria

### Formulario de Postulación PERSONALIZABLE

El formulario de postulación debe ser completamente configurable desde la administración:

| Atributo | Descripción |
|----------|-------------|
| **Tipo** | `archivo` (documento a cargar) o `texto` (campo a diligenciar) |
| **Obligatoriedad** | `obligatorio` u `opcional` |
| **Estado** | `activo` o `inactivo` |
| **Orden** | Posición en el formulario |

**Nota:** La Carta de Intención es un campo de TEXTO, NO es un archivo a cargar.

### Postulaciones

- **Límite:** Un postulante solo puede aplicar a UNA vacante por convocatoria
- **Experiencia ocasional:** Docentes ocasionales requieren 2 años de experiencia laboral equivalente

### Excepciones por Edad (50+ años)

- Según legislación colombiana, personas ≥50 años están exentas de ciertos documentos
- Excepción principal: Libreta Militar
- Las excepciones son GLOBALES, definidas en admin y activadas por convocatoria

### Validación de Documentos

- La verificación es **100% MANUAL** - NO existe verificación automática
- Cada tipo de documento tiene su checklist de verificación
- Documentos rechazados pueden recargarse con observaciones enviadas por email

---

## Flujo de Trabajo: Postulación Completa

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        FLUJO DE POSTULACIÓN                             │
└─────────────────────────────────────────────────────────────────────────┘

[POSTULANTE]                    [REVISOR]                    [COMITÉ]
     │                              │                            │
     ▼                              │                            │
┌─────────┐                         │                            │
│ Aplica  │                         │                            │
│ vacante │                         │                            │
└────┬────┘                         │                            │
     │                              │                            │
     ▼                              │                            │
┌─────────────────┐                 │                            │
│ submitted       │                 │                            │
└────────┬────────┘                 │                            │
         │                          │                            │
         │ [Asigna revisor          │                            │
         │  por programa]           │                            │
         ▼                          │                            │
┌─────────────────┐                 │                            │
│ under_review    │◄────────────────┤                            │
└────────┬────────┘                 │                            │
         │                          ▼                            │
         │                   ┌─────────────┐                     │
         │                   │ Revisor     │                     │
         │                   │ evalúa docs │                     │
         │                   └──────┬──────┘                     │
         │                          │                            │
         │            ┌─────────────┴─────────────┐              │
         │            ▼                           ▼              │
         │     ┌─────────────────┐        ┌─────────────────┐    │
         │     │ docs_validated  │        │ docs_rejected   │    │
         │     └────────┬────────┘        └────────┬────────┘    │
         │              │                          │              │
         │              │                          ▼              │
         │              │                   ┌─────────────┐       │
         │              │                   │ Postulante  │       │
         │              │                   │ corrige     │       │
         │              │                   └──────┬──────┘       │
         │              │                          │              │
         │              │                          ▼              │
         │              │                  [Vuelve a under_review]
         │              │
         │              │ [Pasa a comité de la facultad]
         │              ▼                            │
         │       ┌─────────────┐                     │
         │       │ interview   │◄────────────────────┤
         │       └──────┬──────┘                     │
         │              │                            ▼
         │              │                     ┌─────────────┐
         │              │                     │ Comité      │
         │              │                     │ evalúa      │
         │              │                     └──────┬──────┘
         │              │                            │
         │              │              ┌─────────────┴─────────────┐
         │              │              ▼                           ▼
         │              │       ┌─────────────────┐        ┌─────────────────┐
         │              │       │ selected        │        │ rejected        │
         │              │       └─────────────────┘        └─────────────────┘
         ▼              ▼
   [Email de     [Email de
   notificación] notificación]
```

### Estados y Transiciones

| Estado Actual | Estados Siguientes | Quién Ejecuta |
|---------------|-------------------|---------------|
| `draft` | `submitted` | Postulante |
| `submitted` | `under_review`, `withdrawn` | Sistema, Postulante |
| `under_review` | `docs_validated`, `docs_rejected` | Revisor |
| `docs_validated` | `interview`, `rejected` | Comité, Coordinador |
| `docs_rejected` | `under_review`, `withdrawn` | Sistema, Postulante |
| `interview` | `selected`, `rejected` | Comité |
| `selected` | (estado final) | - |
| `rejected` | (estado final) | - |
| `withdrawn` | (estado final) | - |

---

## Sistema CSS Personalizado (IMPLEMENTAR)

### Regla Fundamental

**ELIMINAR TODA DEPENDENCIA DE BOOTSTRAP.** El plugin debe usar SOLO clases con prefijo `jb-*`.

### Categorías de Clases a Crear

| Categoría | Prefijo | Clases Ejemplo |
|-----------|---------|----------------|
| Variables | `--jb-*` | `--jb-primary`, `--jb-spacing-md` |
| Layout | `jb-container`, `jb-row`, `jb-col-*` | `jb-col-6`, `jb-col-lg-4` |
| Cards | `jb-card`, `jb-card-*` | `jb-card-header`, `jb-card-body` |
| Botones | `jb-btn`, `jb-btn-*` | `jb-btn-primary`, `jb-btn-sm` |
| Tablas | `jb-table`, `jb-table-*` | `jb-table-hover`, `jb-table-striped` |
| Badges | `jb-badge`, `jb-badge-*` | `jb-badge-success`, `jb-badge-warning` |
| Alertas | `jb-alert`, `jb-alert-*` | `jb-alert-info`, `jb-alert-danger` |
| Formularios | `jb-form-*` | `jb-form-control`, `jb-form-group` |
| Modal | `jb-modal`, `jb-modal-*` | `jb-modal-header`, `jb-modal-body` |
| Utilidades | `jb-d-*`, `jb-text-*`, `jb-mt-*` | `jb-d-flex`, `jb-text-muted` |

---

## Prioridades de Refactorización

### Fase 1: CSS y Templates
1. Crear sistema CSS completo con clases `jb-*` en `styles.css`
2. Auditar y listar TODAS las clases Bootstrap usadas en templates
3. Migrar templates uno por uno, empezando por componentes reutilizables
4. Probar en themes: Boost, Classic, Remui, Flavor

### Fase 2: Roles y Permisos
1. Crear tablas: `faculty`, `program`, `committee`, `committee_member`, `reviewer_program`
2. Recrear roles con nueva estructura
3. Implementar asignación por facultad/programa
4. Actualizar `db/upgrade.php` para migración

### Fase 3: Flujos de Trabajo
1. Crear clase `workflow.php` con máquina de estados
2. Implementar validadores para cada transición
3. Integrar con sistema de auditoría
4. Actualizar notificaciones por email

### Fase 4: Módulos AMD
1. Auditar dependencias de Bootstrap JS
2. Reemplazar `$(selector).tooltip()` por solución propia
3. Usar módulos core de Moodle
4. Compilar con `grunt amd --root=local/jobboard`

### Fase 5: User Tours
1. Eliminar todos los tours existentes
2. Crear nuevos tours con selectores `jb-*`
3. Validar selectores con DevTools
4. Probar cada tour completo

---

## Análisis del Repositorio (OBLIGATORIO)

Antes de implementar cualquier cambio, el agente DEBE analizar:

```
ANÁLISIS REQUERIDO
│
├── MOODLE CORE
│   ├── lib/amd/src/           → Patrones de módulos AMD
│   ├── lib/templates/         → Patrones de plantillas Mustache
│   ├── admin/tool/usertours/  → Estructura de User Tours
│   └── theme/boost/           → Clases CSS de referencia
│
├── PLUGINS DEL REPOSITORIO
│   ├── local/*/               → Plugins locales existentes
│   ├── mod/*/                 → Módulos de actividad
│   └── block/*/               → Bloques
│
└── IOMAD
    ├── local/iomad/           → Integración multi-tenant
    └── blocks/iomad_*/        → Bloques IOMAD
```

---

## Control de Versiones

### POLÍTICA OBLIGATORIA

**CADA cambio, por mínimo que sea, DEBE:**
1. Incrementar `$plugin->version` en version.php (formato YYYYMMDDXX)
2. Actualizar `$plugin->release`
3. Documentar en CHANGELOG.md
4. Validar en plataforma ANTES de commit

### Formato CHANGELOG.md

```markdown
## [X.Y.Z] - YYYY-MM-DD

### Added
- Nueva funcionalidad

### Changed
- Cambio de comportamiento

### Fixed
- Corrección de bug

### Removed
- Funcionalidad eliminada
```

---

## Comandos Útiles

| Comando | Propósito |
|---------|-----------|
| `php admin/cli/upgrade.php` | Ejecutar migraciones de BD |
| `php admin/cli/purge_caches.php` | Limpiar caché de Moodle |
| `grunt amd --root=local/jobboard` | Compilar JavaScript AMD |
| `php admin/tool/phpunit/cli/init.php` | Inicializar PHPUnit |
| `vendor/bin/phpunit --testsuite local_jobboard_testsuite` | Ejecutar tests |
| `php admin/tool/phpcs/cli/run.php --standard=moodle local/jobboard` | Validar código |

---

## Notas Críticas para Agentes

### Reglas Absolutas

1. **ANALIZAR** el repositorio completo antes de implementar
2. **NO USAR BOOTSTRAP** - Solo clases con prefijo `jb-*`
3. **RECREAR USER TOURS** - Con selectores actualizados
4. **RECREAR MÓDULOS AMD** - Sin dependencias de Bootstrap JS
5. **RECREAR ROLES** - Con contexto de facultad/programa
6. **VALIDAR SIEMPRE** en plataforma antes de commit
7. **NO improvisar** cambios directamente en producción
8. **Respetar** la arquitectura IOMAD de 4 niveles
9. **Mantener** paridad de strings EN/ES (~1860 strings)
10. **Documentar** TODO en CHANGELOG
11. **Comité de selección** es por FACULTAD, no por vacante
12. **Revisores** se asignan por PROGRAMA
13. **Formulario de postulación** es PERSONALIZABLE desde admin
14. **Carta de intención** es campo de TEXTO, no archivo
15. **Convocatoria** debe tener PDF adjunto con detalle completo
16. **Auditoría ROBUSTA** - registrar TODAS las acciones
17. Un postulante = UNA vacante por convocatoria
18. La validación de documentos es 100% MANUAL
19. **Búsqueda de usuarios** por username al crear comités

---

## Cumplimiento Normativo

### Protección de Datos

- **Ley 1581/2012** - Habeas Data (Colombia)
- **GDPR** - Privacy API de Moodle implementada

### Contratación

- Cumple normativa colombiana de contratación docente
- Excepciones de edad según legislación vigente

---

## Contacto

- **Autor:** Alonso Arias
- **Email:** soporteplataformas@iser.edu.co
- **Supervisión:** Vicerrectoría Académica ISER
- **Institución:** ISER (Instituto Superior de Educación Rural)
- **Sede Principal:** Pamplona, Norte de Santander, Colombia

---

*Última actualización: Diciembre 2025*
*Plugin local_jobboard v3.0.8 para Moodle 4.1-4.5 con IOMAD*
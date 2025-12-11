# AGENTS.md - local_jobboard

Plugin de Moodle para gestión de vacantes académicas y postulaciones docentes.
Sistema de Bolsa de Empleo para reclutamiento de profesores de cátedra.

---

## Información del Proyecto

| Campo | Valor |
|-------|-------|
| **Componente** | `local_jobboard` |
| **Versión actual** | 3.1.x |
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

## Estado Actual del Plugin

### Estructura de Archivos

```
local/jobboard/
├── index.php                      # Router centralizado
├── lib.php                        # Funciones principales
├── settings.php                   # Configuración admin
├── version.php                    # Versión 3.1.23 (2025121138)
├── styles.css                     # ⚠️ NO EXISTE - CREAR DESDE CERO
├── bulk_validate.php              # Validación masiva
├── assign_reviewer.php            # Asignación de revisores
├── migrate.php                    # Exportación de datos
│
├── views/                         # 17 vistas PHP ✅
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
├── templates/                     # ~50 plantillas Mustache ✅
│   ├── dashboard.mustache
│   ├── components/
│   │   ├── page_header.mustache
│   │   ├── stat_card.mustache
│   │   └── filter_form.mustache
│   └── pages/
│       ├── dashboard.mustache
│       ├── manage.mustache
│       ├── apply.mustache
│       ├── application_detail.mustache
│       ├── bulk_validate.mustache
│       ├── assign_reviewer.mustache
│       ├── committee.mustache
│       ├── public.mustache
│       ├── public_vacancy.mustache
│       ├── reports.mustache
│       ├── review.mustache
│       ├── vacancy_detail.mustache
│       └── ... (~50 templates total)
│
├── amd/                           # ⚠️ NO EXISTE - CREAR DESDE CERO
│   ├── src/                       # ~15 módulos JavaScript (PENDIENTE)
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
│   ├── install.xml                # Esquema de BD ✅
│   ├── install.php                # Instalación + doctypes predeterminados
│   ├── upgrade.php                # Migraciones
│   ├── access.php                 # 26 capabilities (de 34 especificadas)
│   ├── services.php               # Web services
│   └── tours/                     # ⚠️ NO EXISTE - 15 User Tours (PENDIENTE)
│
├── classes/                       # ~40 clases implementadas ✅
│   ├── output/
│   │   └── renderer.php           # ⚠️ 6,162 líneas - FRAGMENTAR
│   ├── audit.php
│   ├── document.php
│   ├── reviewer.php
│   ├── application.php
│   ├── bulk_validator.php
│   ├── exemption.php
│   ├── email_template.php
│   ├── privacy/provider.php       # GDPR implementado ✅
│   ├── forms/                     # 7 formularios ✅
│   ├── event/                     # 8 eventos ✅
│   ├── task/                      # 3 tareas programadas ✅
│   └── external/api.php
│
├── cli/
│   ├── cli.php                    # Importador de perfiles v2.2
│   ├── parse_profiles.php
│   ├── parse_profiles_v2.php
│   └── import_vacancies.php
│
├── admin/                         # Páginas administrativas
│   ├── doctypes.php
│   ├── email_templates.php
│   └── exemptions.php
│
├── lang/                          # ⚠️ NO EXISTE - CREAR DESDE CERO
│   ├── en/local_jobboard.php      # ~1860+ strings (PENDIENTE)
│   └── es/local_jobboard.php      # ~1860+ strings (PENDIENTE)
│
├── CHANGELOG.md                   # ⚠️ NO EXISTE - CREAR
└── README.md                      # ⚠️ NO EXISTE - CREAR
```

### Estructura Propuesta: Renderers Fragmentados

```
classes/output/                    # REFACTORIZACIÓN PENDIENTE
├── renderer.php                   # Renderer principal (delegador)
├── renderer_dashboard.php         # Dashboard y widgets
├── renderer_convocatoria.php      # Vistas de convocatorias
├── renderer_vacancy.php           # Vistas de vacantes
├── renderer_application.php       # Vistas de postulaciones
├── renderer_review.php            # Vistas de revisión
├── renderer_documents.php         # Validación de documentos
├── renderer_reports.php           # Reportes y exportaciones
├── renderer_admin.php             # Páginas administrativas
└── renderer_public.php            # Vistas públicas
```

### Roles Existentes (3)

| Shortname | Nombre | Capabilities Asignadas |
|-----------|--------|------------------------|
| `jobboard_reviewer` | Revisor de Documentos | view, viewinternal, review, validatedocuments, reviewdocuments, downloadanydocument |
| `jobboard_coordinator` | Coordinador de Selección | view, viewinternal, manage, createvacancy, editvacancy, publishvacancy, viewallvacancies, viewallapplications, changeapplicationstatus, assignreviewers, viewreports, viewevaluations, manageworkflow |
| `jobboard_committee` | Miembro del Comité | view, viewinternal, evaluate, viewevaluations, downloadanydocument |

### Capabilities Existentes (~34)

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

### Tablas de Base de Datos (~24)

| Tabla | Descripción | Estado |
|-------|-------------|--------|
| `local_jobboard_convocatoria` | Convocatorias | ✅ Implementada |
| `local_jobboard_vacancy` | Vacantes | ✅ Implementada |
| `local_jobboard_vacancy_field` | Campos custom de vacantes | ✅ Implementada |
| `local_jobboard_application` | Postulaciones | ✅ Implementada |
| `local_jobboard_document` | Documentos subidos | ✅ Implementada |
| `local_jobboard_doc_validation` | Validaciones de documentos | ✅ Implementada |
| `local_jobboard_doctype` | Tipos de documento | ✅ Implementada |
| `local_jobboard_email_template` | Plantillas de email | ✅ Implementada |
| `local_jobboard_email_strings` | Strings de email por idioma | ✅ Implementada |
| `local_jobboard_exemption` | Excepciones de documentos | ✅ Implementada |
| `local_jobboard_config` | Configuración | ✅ Implementada |
| `local_jobboard_audit` | Auditoría | ✅ Implementada |
| `local_jobboard_applicant_profile` | Perfiles de postulantes | ✅ Implementada |
| `local_jobboard_consent` | Consentimientos | ✅ Implementada |
| `local_jobboard_committee` | Comités de selección | ✅ Implementada |
| `local_jobboard_committee_member` | Miembros del comité | ✅ Implementada |
| `local_jobboard_faculty` | Facultades | ✅ Implementada |
| `local_jobboard_program` | Programas académicos | ✅ Implementada |
| `local_jobboard_program_reviewer` | Revisores por programa | ✅ Implementada |
| `local_jobboard_faculty_reviewer` | Revisores por facultad | ✅ Implementada |
| `local_jobboard_workflow_log` | Log de workflow | ✅ Implementada |
| `local_jobboard_notification` | Notificaciones | ✅ Implementada |
| `local_jobboard_interviewer` | Entrevistadores | ✅ Implementada |
| `local_jobboard_evaluation` | Evaluaciones | ✅ Implementada |

---

## ✅ FUNCIONALIDADES IMPLEMENTADAS

### Core del Sistema
- [x] Gestión completa de convocatorias (CRUD)
- [x] Gestión de vacantes con campos personalizados
- [x] Sistema de postulaciones
- [x] Carga y gestión de documentos
- [x] Validación manual de documentos con checklist
- [x] Validación masiva (bulk_validate.php)
- [x] Asignación de revisores (assign_reviewer.php)
- [x] Asignación automática por carga de trabajo
- [x] Sistema de estados de postulación
- [x] Notificaciones por email
- [x] Plantillas de email personalizables por company
- [x] Dashboard adaptativo por rol
- [x] Vista pública de convocatorias
- [x] Sistema de auditoría

### Estructura Organizacional
- [x] Integración IOMAD multi-tenant
- [x] Comités por facultad (companyid)
- [x] Revisores por programa (categoryid)
- [x] Tablas faculty y program
- [x] Estructura de 4 niveles IOMAD

### Documentos
- [x] 20+ tipos de documento predefinidos
- [x] Categorías: identification, academic, employment, legal, financial, health
- [x] Checklist de verificación por tipo
- [x] URLs externas para descarga
- [x] Excepciones ISER (historico_iser, documentos_recientes, traslado_interno, recontratacion)
- [x] Condiciones por género (libreta militar)
- [x] Excepciones por profesión
- [x] Fechas de vencimiento configurables

### CLI
- [x] Importador de perfiles desde texto (cli.php v2.2)
- [x] Creación automática de estructura IOMAD
- [x] Importación desde JSON y CSV
- [x] Parser de perfiles DOCX/texto
- [x] Exportación a JSON

### Cumplimiento Normativo
- [x] Privacy API (GDPR)
- [x] Ley 1581/2012 Habeas Data (Colombia)
- [x] Exportación de datos personales
- [x] Eliminación de datos personales
- [x] Anonimización de logs de auditoría

---

## ⚠️ PROBLEMAS IDENTIFICADOS Y PENDIENTES DE RESOLUCIÓN

### MÉTRICAS DEL DIAGNÓSTICO (Diciembre 2025)

| Métrica | Valor |
|---------|-------|
| Archivos PHP analizados | 62 |
| Templates Mustache | 50 |
| **Clases Bootstrap a migrar** | **1,224 ocurrencias** |
| Strings de idioma faltantes | ~1,860+ |
| Líneas de renderer.php | **6,162** |
| Renderers a crear | 10 |
| Módulos AMD faltantes | 15 |
| User Tours faltantes | 15 |
| Capabilities implementadas | 26 de 34 (77%) |

---

### 1. STYLES.CSS NO EXISTE

**ESTADO:** 🔴 NO EXISTE - Crear desde cero

**PROBLEMA:** El archivo `styles.css` con el sistema de clases `jb-*` NO EXISTE. No hay ningún CSS propio del plugin.

**ACCIÓN REQUERIDA:**
1. Crear `styles.css` en la raíz del plugin
2. Implementar sistema CSS completo con prefijo `jb-*`
3. Crear equivalentes para TODAS las 1,224 ocurrencias de clases Bootstrap
4. Asegurar compatibilidad con themes: Boost, Classic, Remui, Flavor

### 2. CLASES BOOTSTRAP EN TEMPLATES (1,224 ocurrencias)

**ESTADO:** 🔴 Crítico - Migración masiva requerida

**PROBLEMA:** Los templates Mustache usan clases Bootstrap directamente.

**TEMPLATES MÁS AFECTADOS:**

| Template | Ocurrencias |
|----------|-------------|
| pages/committee.mustache | 72 |
| pages/public.mustache | 65 |
| pages/reports.mustache | 64 |
| pages/review.mustache | 59 |
| pages/dashboard.mustache | 53 |
| pages/vacancy_detail.mustache | 48 |
| pages/application_detail.mustache | 48 |
| pages/bulk_validate.mustache | 47 |
| pages/assign_reviewer.mustache | 47 |
| pages/public_vacancy.mustache | 47 |

**CLASES BOOTSTRAP DETECTADAS:**
```
Layout: row, col-*, mb-*, mt-*, p-*, d-flex, d-none
Cards: card, card-header, card-body, card-footer, shadow-sm
Botones: btn, btn-primary, btn-secondary, btn-outline-*, btn-sm, btn-lg
Tablas: table, table-hover, table-responsive, thead-light
Badges: badge, badge-danger, badge-secondary, badge-*
Alertas: alert, alert-danger, alert-info, alert-*
Formularios: form-control, form-group, input-group
Texto: text-muted, text-primary, font-weight-bold
Flex: justify-content-*, align-items-*
```

**ACCIÓN REQUERIDA:**
1. Crear styles.css con equivalentes jb-* para cada clase Bootstrap
2. Migrar los 50 templates uno por uno
3. Comenzar por templates de pages/ (mayor impacto)
4. Probar en themes: Boost, Classic, Remui, Flavor

### 3. USER TOURS NO EXISTEN

**ESTADO:** 🔴 NO EXISTE - Crear desde cero

**PROBLEMA:** La carpeta `db/tours/` NO EXISTE. Los 15 User Tours especificados no han sido creados.

**TOURS A CREAR:**
```
tour_dashboard.json
tour_public.json
tour_convocatorias.json
tour_convocatoria_manage.json
tour_vacancies.json
tour_vacancy.json
tour_manage.json
tour_apply.json
tour_application.json
tour_myapplications.json
tour_documents.json
tour_review.json
tour_myreviews.json
tour_validate_document.json
tour_reports.json
```

**ACCIÓN REQUERIDA:**
1. Crear carpeta `db/tours/`
2. Crear los 15 tours con selectores `jb-*`
3. Validar selectores con DevTools
4. Probar cada tour paso a paso

### 4. MÓDULOS AMD NO EXISTEN

**ESTADO:** 🔴 NO EXISTE - Crear desde cero

**PROBLEMA:** La carpeta `amd/` NO EXISTE. Los 15 módulos JavaScript especificados no han sido creados.

**MÓDULOS A CREAR:**
```
amd/src/
├── public_filters.js
├── department_loader.js
├── company_loader.js
├── convocatoria_loader.js
├── tooltips.js
├── signup_form.js
├── apply_progress.js
├── review_ui.js
├── card_actions.js
├── confirm_action.js
├── review_shortcuts.js
└── loading_states.js
```

**ACCIÓN REQUERIDA:**
1. Crear carpeta `amd/src/`
2. Crear los 15 módulos JavaScript
3. NO usar jQuery ni Bootstrap JS
4. Usar módulos core: `core/ajax`, `core/notification`, `core/templates`
5. Compilar con `grunt amd --root=local/jobboard`

### 5. RENDERER.PHP DEMASIADO GRANDE (6,162 líneas)

**ESTADO:** 🟡 Requiere refactorización

**PROBLEMA:** El archivo `classes/output/renderer.php` está creciendo demasiado y se vuelve difícil de mantener. Contiene métodos para todas las vistas del plugin en un solo archivo.

**ESTRATEGIA DE FRAGMENTACIÓN:**

Dividir el renderer en múltiples clases especializadas por área funcional:

```
classes/output/
├── renderer.php                    # Renderer principal (delegador)
├── renderer_dashboard.php          # Dashboard y widgets
├── renderer_convocatoria.php       # Vistas de convocatorias
├── renderer_vacancy.php            # Vistas de vacantes
├── renderer_application.php        # Vistas de postulaciones
├── renderer_review.php             # Vistas de revisión
├── renderer_documents.php          # Validación de documentos
├── renderer_reports.php            # Reportes y exportaciones
├── renderer_admin.php              # Páginas administrativas
└── renderer_public.php             # Vistas públicas
```

**IMPLEMENTACIÓN PROPUESTA:**

1. **Renderer Principal (delegador):**
```php
class renderer extends plugin_renderer_base {
    
    protected function get_dashboard_renderer(): renderer_dashboard {
        return new renderer_dashboard($this->page, $this->target);
    }
    
    protected function get_review_renderer(): renderer_review {
        return new renderer_review($this->page, $this->target);
    }
    
    // Métodos públicos delegan a renderers especializados
    public function render_dashboard($data) {
        return $this->get_dashboard_renderer()->render($data);
    }
    
    public function render_review_page($data) {
        return $this->get_review_renderer()->render($data);
    }
}
```

2. **Renderer Especializado (ejemplo):**
```php
class renderer_dashboard extends plugin_renderer_base {
    
    public function render($data): string {
        return $this->render_from_template('local_jobboard/pages/dashboard', $data);
    }
    
    public function prepare_dashboard_data(int $userid, \context $context): array {
        // Toda la lógica de preparación de datos del dashboard
    }
    
    protected function prepare_admin_stats(): array { ... }
    protected function prepare_reviewer_stats(): array { ... }
    protected function prepare_applicant_stats(): array { ... }
}
```

**BENEFICIOS:**
- Archivos más pequeños y manejables (<500 líneas cada uno)
- Mejor organización por área funcional
- Facilita trabajo en paralelo
- Testing más sencillo por módulo
- Reducción de conflictos en control de versiones

**FASES DE MIGRACIÓN:**
1. Crear estructura de archivos vacíos
2. Extraer métodos de dashboard → renderer_dashboard.php
3. Extraer métodos de review → renderer_review.php
4. Continuar con cada área
5. Actualizar renderer.php para delegar
6. Probar cada vista afectada
7. Eliminar código duplicado del renderer principal

**ARCHIVOS AFECTADOS:**
- `classes/output/renderer.php` (refactorizar)
- Todas las vistas que usan `$PAGE->get_renderer('local_jobboard')`

### 6. VISTAS PHP CON HTML DIRECTO

**ESTADO:** 🟡 Parcialmente resuelto

**PROBLEMA:** Algunas vistas PHP generan HTML directamente con `html_writer`.

**VISTAS A REVISAR:**
- `view_convocatoria.php`
- `vacancy.php`
- `application.php`

**ACCIÓN REQUERIDA:**
1. Identificar secciones con HTML directo
2. Crear templates Mustache correspondientes
3. Usar renderer para pasar datos
4. Eliminar `html_writer` con clases Bootstrap

---

## 🔧 DESARROLLO PENDIENTE

### Prioridad Alta

#### 1. Interfaz de Revisión de Documentos (estilo mod_assign)

**DESCRIPCIÓN:** Crear interfaz de revisión similar a mod_assign para validar documentos.

**CARACTERÍSTICAS:**
- Panel lateral con lista de documentos
- Vista previa del documento (PDF viewer)
- Checklist de verificación interactivo
- Aprobación/rechazo con un clic
- Navegación entre documentos sin recargar
- Contador de progreso
- Atajos de teclado

**ARCHIVOS A CREAR:**
```
views/review_document.php
templates/pages/review_document.mustache
amd/src/review_document.js
classes/review_interface.php
```

#### 2. Excepciones Globales (no por usuario)

**DESCRIPCIÓN:** Rediseñar el sistema de excepciones para que sean globales y se activen por convocatoria.

**ESTADO ACTUAL:** Excepciones se asignan a usuarios individuales
**ESTADO DESEADO:** Excepciones definidas globalmente, activadas por convocatoria

**CAMPOS A AGREGAR:**
```sql
-- En local_jobboard_exemption
convocatoriaid INT(10) NULL -- NULL = todas las convocatorias
is_global INT(1) DEFAULT 0 -- 1 = aplica a todos los usuarios elegibles
criteria JSON -- criterios de elegibilidad (edad, etc.)
```

**LÓGICA:**
- Excepción edad 50+ años: Automática si fecha_nacimiento >= 50 años
- Excepción libreta militar: Solo hombres < 50 años la requieren
- Excepciones ISER: Por tipo (historico_iser, documentos_recientes, etc.)

#### 3. Plantillas de Email con Preview en Tiempo Real

**DESCRIPCIÓN:** Editor de plantillas de email con vista previa.

**CARACTERÍSTICAS:**
- Editor WYSIWYG para body
- Lista de variables disponibles con descripción
- Preview con datos de ejemplo
- Duplicar plantillas por company
- Historial de cambios

**ARCHIVOS A CREAR:**
```
templates/pages/email_template_editor.mustache
amd/src/email_template_editor.js
classes/forms/email_template_form.php
```

#### 4. Reportes Filtrados por Convocatoria

**DESCRIPCIÓN:** Todos los reportes deben filtrarse obligatoriamente por convocatoria.

**REPORTES A ACTUALIZAR:**
- Postulaciones por estado
- Documentos pendientes
- Carga de trabajo de revisores
- Estadísticas de vacantes
- Exportación de datos

**LÓGICA:**
- Selector de convocatoria obligatorio en cada reporte
- No mostrar datos sin convocatoria seleccionada
- Opción "Todas las convocatorias" solo para administradores

### Prioridad Media

#### 5. CLI para Procesar PDFs de PERFILESPROFESORES

**DESCRIPCIÓN:** Mejorar CLI para procesar PDFs grandes dividiéndolos.

**CARACTERÍSTICAS:**
- Dividir PDFs > 2 páginas en segmentos de 2 páginas
- Usar pdftotext o similar para extracción
- Guardar archivos intermedios
- Log detallado del proceso

**UBICACIÓN:** `/cli/process_pdfs.php`

#### 6. Búsqueda de Usuarios por Username en Comités

**DESCRIPCIÓN:** Al crear comités, permitir buscar usuarios por username además de nombre.

**IMPLEMENTACIÓN:**
- Autocomplete con búsqueda en: username, firstname, lastname, email
- Mostrar: "username - Nombre Completo (email)"
- Filtrar solo usuarios con capability `local/jobboard:evaluate`

#### 7. Widget de Dashboard para Revisores

**DESCRIPCIÓN:** Crear widget específico para el dashboard de revisores.

**CONTENIDO:**
- Documentos pendientes de revisar
- Tiempo promedio de revisión
- Documentos revisados hoy/semana
- Acceso rápido a mis revisiones

#### 8. Cadenas de Idiomas (Language Strings)

**ESTADO:** 🔴 NO EXISTEN - Crear desde cero

**DESCRIPCIÓN:** Los archivos de idioma del plugin NO EXISTEN. Se deben crear completamente desde cero para todas las funcionalidades del plugin.

**ARCHIVOS A CREAR:**
```
lang/en/local_jobboard.php    # Inglés (obligatorio)
lang/es/local_jobboard.php    # Español (obligatorio para ISER)
```

**ESTRUCTURA BASE DEL ARCHIVO:**
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
 * Language strings for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin name and general.
$string['pluginname'] = 'Job Board';
$string['pluginname_desc'] = 'Job board system for teacher recruitment';
// ... continuar con TODAS las strings
```

**CATEGORÍAS DE STRINGS A CREAR (~1860+ strings):**

| Categoría | Cantidad Estimada | Descripción |
|-----------|-------------------|-------------|
| General/Plugin | ~50 | pluginname, settings, navigation |
| Capabilities | ~34 | Una por cada capability |
| Roles | ~10 | Nombres y descripciones de roles |
| Convocatorias | ~80 | CRUD, estados, filtros |
| Vacantes | ~100 | CRUD, estados, campos |
| Postulaciones | ~120 | Estados, acciones, mensajes |
| Documentos | ~150 | Tipos, validación, checklist |
| Revisión | ~80 | Interfaz, acciones, estados |
| Comités | ~50 | Gestión, miembros |
| Reportes | ~60 | Títulos, filtros, exportación |
| Email Templates | ~100 | Plantillas, variables, preview |
| Excepciones | ~40 | Tipos, gestión |
| Auditoría | ~30 | Acciones, logs |
| Errores | ~150 | Mensajes de error y validación |
| Formularios | ~200 | Labels, placeholders, help |
| Dashboard | ~80 | Widgets, estadísticas |
| User Tours | ~200 | Títulos y contenido de tours |
| Privacy API | ~50 | Metadata GDPR |
| CLI | ~30 | Mensajes del importador |
| Misceláneos | ~200 | Botones, confirmaciones, etc. |

**STRINGS CRÍTICAS INICIALES (crear primero):**
```php
// Plugin identification
$string['pluginname'] = 'Job Board';
$string['jobboard:view'] = 'View job board';
$string['jobboard:manage'] = 'Manage job board';
$string['jobboard:apply'] = 'Apply to vacancies';
// ... todas las capabilities

// Navigation
$string['dashboard'] = 'Dashboard';
$string['convocatorias'] = 'Convocatorias';
$string['vacancies'] = 'Vacancies';
$string['applications'] = 'Applications';
$string['myapplications'] = 'My applications';
$string['review'] = 'Review';
$string['reports'] = 'Reports';

// Status strings
$string['status_draft'] = 'Draft';
$string['status_published'] = 'Published';
$string['status_closed'] = 'Closed';
$string['status_submitted'] = 'Submitted';
$string['status_under_review'] = 'Under review';
$string['status_docs_validated'] = 'Documents validated';
$string['status_docs_rejected'] = 'Documents rejected';
$string['status_selected'] = 'Selected';
$string['status_rejected'] = 'Rejected';
$string['status_waitlist'] = 'Waitlist';
```

**REGLA CRÍTICA:** 
- NINGUNA string hardcodeada en PHP o templates
- TODA funcionalidad requiere strings EN + ES
- Mantener paridad absoluta entre archivos de idioma
- Usar `get_string('key', 'local_jobboard')` SIEMPRE

#### 9. Documentación del Plugin

**ESTADO:** 🔴 Desactualizada

**DESCRIPCIÓN:** La documentación interna del plugin necesita actualizarse con la información de contacto correcta y reflejar el estado actual del desarrollo.

**ARCHIVOS A ACTUALIZAR:**
```
README.md
CHANGELOG.md
version.php (phpdoc header)
Todos los archivos PHP (phpdoc @author, @copyright)
```

**INFORMACIÓN DE CONTACTO A USAR:**
```
Autor: Alonso Arias
Email: soporteplataformas@iser.edu.co
Institución: ISER (Instituto Superior de Educación Rural)
Supervisión: Vicerrectoría Académica ISER
Ubicación: Pamplona, Norte de Santander, Colombia
```

**FORMATO PHPDOC ESTÁNDAR:**
```php
/**
 * [Descripción del archivo]
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
```

**ACCIÓN REQUERIDA:**
1. Actualizar headers en TODOS los archivos PHP
2. Crear/actualizar README.md con descripción completa
3. Mantener CHANGELOG.md actualizado con cada cambio
4. Documentar cada clase y método público

### Prioridad Baja

#### 10. Tests PHPUnit

**ESTADO:** 🔴 No implementados

**TESTS A CREAR:**
```
tests/application_test.php
tests/document_test.php
tests/vacancy_test.php
tests/exemption_test.php
tests/workflow_test.php
tests/privacy_provider_test.php
```

#### 11. Web Services API Completa

**ESTADO:** 🟡 Parcialmente implementada

**ENDPOINTS PENDIENTES:**
- `get_convocatorias`
- `get_vacancies`
- `get_application_status`
- `submit_application`
- `upload_document`
- `get_my_applications`

#### 12. Integración con Calendario Moodle

**DESCRIPCIÓN:** Crear eventos de calendario para:
- Fecha límite de postulación
- Fecha de entrevista
- Recordatorios de documentos pendientes

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
         │              ▼
         │       ┌─────────────────┐
         │       │ interview       │──────────────────────────────┤
         │       │ (si aplica)     │                              │
         │       └────────┬────────┘                              │
         │                │                                       │
         │                ▼                                       ▼
         │       ┌─────────────────┐                    ┌──────────────┐
         │       │ Comité evalúa   │◄───────────────────│ Comité de    │
         │       │ candidatos      │                    │ Facultad     │
         │       └────────┬────────┘                    └──────────────┘
         │                │
         │     ┌──────────┼──────────┐
         │     ▼          ▼          ▼
         │ ┌────────┐ ┌────────┐ ┌────────┐
         │ │selected│ │waitlist│ │rejected│
         │ └────────┘ └────────┘ └────────┘
```

---

## Reglas de Negocio Críticas

### Organización por Facultad

1. **Vacantes separadas por facultad** - Las vacantes se organizan y filtran por facultad
2. **Comité de selección por FACULTAD** - NO por vacante. Cada facultad tiene su propio comité
3. **Revisores asignados por PROGRAMA** - Los revisores de documentos se asignan a nivel de programa académico

### Convocatorias

- **PDF adjunto obligatorio:** Al crear la convocatoria se debe cargar un PDF con el detalle completo
- **Descripción breve:** Campo de texto para resumen de la convocatoria
- **Términos y condiciones:** HTML con condiciones legales

### Formulario de Postulación PERSONALIZABLE

| Atributo | Descripción |
|----------|-------------|
| **Tipo** | `file` (documento) o `text` (campo de texto) |
| **input_type** | file, text, textarea, select |
| **Obligatoriedad** | Campo `required` en doctype |
| **Estado** | `enabled` activo/inactivo |
| **Orden** | `sortorder` posición en formulario |

**Nota:** La Carta de Intención es un campo de TEXTO, NO es un archivo.

### Postulaciones

- **Límite:** Un postulante solo puede aplicar a UNA vacante por convocatoria
- **Experiencia ocasional:** Docentes ocasionales requieren 2 años de experiencia laboral equivalente

### Excepciones de Documentos

- **Tipos:** historico_iser, documentos_recientes, traslado_interno, recontratacion
- **Documentos eximibles:** Los marcados con `iserexempted = 1`
- **Excepciones por edad:** Personas ≥50 años exentas de libreta militar
- **Excepciones por género:** Libreta militar solo para hombres

### Validación de Documentos

- La verificación es **100% MANUAL** - NO existe verificación automática
- Cada tipo de documento tiene su checklist de verificación
- Documentos rechazados pueden recargarse con observaciones enviadas por email
- Razones de rechazo: illegible, expired, incomplete, wrongtype, mismatch

---

## Plan de Implementación por Fases

### Fase 1: Infraestructura Crítica (MÁXIMA PRIORIDAD)

**Objetivo:** Crear los archivos fundamentales que NO EXISTEN.

1. **Crear `styles.css`** con sistema CSS completo `jb-*`
   - Equivalentes para TODAS las clases Bootstrap usadas (1,224 ocurrencias)
   - Compatibilidad con themes: Boost, Classic, Remui, Flavor
   
2. **Crear archivos de idioma** (NO EXISTEN)
   - `lang/en/local_jobboard.php` (~1860+ strings)
   - `lang/es/local_jobboard.php` (~1860+ strings)
   
3. **Crear documentación básica**
   - `CHANGELOG.md`
   - `README.md`
   
4. **Actualizar `version.php`** con nueva versión

### Fase 2: Migración CSS (50 templates)

1. Migrar templates de `pages/` (mayor impacto - 10 archivos principales)
2. Migrar templates de `components/`
3. Migrar templates raíz
4. Migrar templates de `reports/`
5. Probar en themes: Boost, Classic, Remui, Flavor

### Fase 3: Refactorización del Renderer (6,162 líneas)

1. Analizar renderer.php actual y documentar todos los métodos
2. Crear estructura de archivos para renderers especializados
3. Extraer `renderer_dashboard.php` (dashboard y widgets)
4. Extraer `renderer_convocatoria.php` (vistas de convocatorias)
5. Extraer `renderer_vacancy.php` (vistas de vacantes)
6. Extraer `renderer_application.php` (vistas de postulaciones)
7. Extraer `renderer_review.php` (vistas de revisión)
8. Extraer `renderer_documents.php` (validación de documentos)
9. Extraer `renderer_reports.php` (reportes)
10. Extraer `renderer_admin.php` (páginas administrativas)
11. Extraer `renderer_public.php` (vistas públicas)
12. Actualizar renderer.php como delegador (~100 líneas)
13. Probar TODAS las vistas afectadas

### Fase 4: Módulos AMD (NO EXISTEN)

1. Crear carpeta `amd/src/`
2. Crear los 15 módulos JavaScript especificados
3. NO usar jQuery ni Bootstrap JS
4. Usar módulos core de Moodle
5. Compilar con `grunt amd --root=local/jobboard`

### Fase 5: User Tours (NO EXISTEN)

1. Crear carpeta `db/tours/`
2. Crear los 15 tours con selectores `jb-*`
3. Validar selectores con DevTools
4. Probar cada tour completo

### Fase 6: Interfaz de Revisión

1. Diseñar interfaz estilo mod_assign
2. Crear templates y AMD
3. Implementar navegación sin recarga
4. Agregar atajos de teclado

### Fase 7: Excepciones Globales

1. Modificar esquema de BD
2. Crear interfaz de gestión
3. Implementar lógica de elegibilidad automática
4. Migrar excepciones existentes

### Fase 8: Plantillas Email con Preview

1. Crear editor con variables
2. Implementar preview en tiempo real
3. Agregar historial de cambios

### Fase 9: Reportes por Convocatoria

1. Modificar todas las vistas de reportes
2. Agregar filtro obligatorio
3. Actualizar exportaciones

### Fase 10: Capabilities Faltantes

1. Implementar las 8 capabilities faltantes (de 34 especificadas)
2. Actualizar `db/access.php`
3. Actualizar roles

### Fase 11: Documentación Completa

1. Actualizar headers PHPDoc en TODOS los archivos PHP
2. Completar README.md con guía de instalación
3. Documentar clases y métodos públicos
4. Actualizar información de contacto en version.php

---

## Comandos Útiles

| Comando | Propósito |
|---------|-----------|
| `php admin/cli/upgrade.php` | Ejecutar migraciones de BD |
| `php admin/cli/purge_caches.php` | Limpiar caché de Moodle |
| `grunt amd --root=local/jobboard` | Compilar JavaScript AMD |
| `php local/jobboard/cli/cli.php --help` | Ver ayuda del importador |
| `php local/jobboard/cli/cli.php --create-structure --publish --public` | Importación completa |
| `vendor/bin/phpunit --testsuite local_jobboard_testsuite` | Ejecutar tests |
| `php admin/tool/phpcs/cli/run.php --standard=moodle local/jobboard` | Validar código |

### Comandos de Auditoría CSS

```bash
# Buscar clases Bootstrap en templates
grep -r "class=\"[^\"]*\b\(card\|btn\|alert\|badge\|table\|form-\)" templates/

# Buscar html_writer con clases Bootstrap
grep -r "html_writer" views/ | grep -i "card\|btn\|alert"

# Listar todos los selectores en tours
jq '.steps[].targetvalue' db/tours/*.json
```

---

## Notas Críticas para Agentes

### Reglas Absolutas

1. **ANALIZAR** el repositorio completo antes de implementar
2. **SOLO CLASES jb-*** - No usar clases Bootstrap directamente
3. **styles.css NO EXISTE** - Crear sistema CSS completo desde cero
4. **amd/ NO EXISTE** - Crear los 15 módulos JavaScript desde cero
5. **db/tours/ NO EXISTE** - Crear los 15 User Tours desde cero
6. **lang/ NO EXISTE** - Crear ~1860+ strings en EN y ES desde cero
7. **VALIDAR SIEMPRE** en plataforma antes de commit
8. **NO improvisar** cambios directamente en producción
9. **Respetar** la arquitectura IOMAD de 4 niveles
10. **Paridad EN/ES** - Toda string debe existir en AMBOS idiomas
11. **NO hardcodear** strings en PHP ni templates - usar get_string() SIEMPRE
12. **Documentar** TODO en CHANGELOG.md
13. **ACTUALIZAR DOCUMENTACIÓN** con información de contacto correcta
14. **FRAGMENTAR RENDERER** - 6,162 líneas es inaceptable, dividir en 10 renderers
15. **Comité de selección** es por FACULTAD, no por vacante
16. **Revisores** se asignan por PROGRAMA
17. **Formulario de postulación** es PERSONALIZABLE desde admin
18. **Carta de intención** es campo de TEXTO, no archivo
19. **Convocatoria** debe tener PDF adjunto con detalle completo
20. **Auditoría ROBUSTA** - registrar TODAS las acciones
21. Un postulante = UNA vacante por convocatoria
22. La validación de documentos es 100% MANUAL
23. **Búsqueda de usuarios** por username al crear comités
24. **Capabilities:** Solo 26 de 34 implementadas (77%) - completar las 8 faltantes

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

## Cumplimiento Normativo

### Protección de Datos

- **Ley 1581/2012** - Habeas Data (Colombia)
- **GDPR** - Privacy API de Moodle implementada
- **Exportación:** Implementada en privacy/provider.php
- **Eliminación:** Implementada con anonimización de auditoría

### Contratación

- Cumple normativa colombiana de contratación docente
- Excepciones de edad según legislación vigente (50+ años)
- Requisitos de libreta militar según género

---

## Contacto

- **Autor:** Alonso Arias
- **Email:** soporteplataformas@iser.edu.co
- **Supervisión:** Vicerrectoría Académica ISER
- **Institución:** ISER (Instituto Superior de Educación Rural)
- **Sede Principal:** Pamplona, Norte de Santander, Colombia

---

*Última actualización: Diciembre 2025*
*Plugin local_jobboard v3.1.x para Moodle 4.1-4.5 con IOMAD*
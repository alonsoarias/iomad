# REQUERIMIENTOS DEL PLUGIN LOCAL_JOBBOARD

**Documento consolidado de requerimientos extraídos de todas las conversaciones**
**Actualizado con el estado actual del plugin en la base de conocimientos**

**Fecha de generación:** 2025-12-12
**Plugin:** local_jobboard
**Institución:** ISER (Instituto Superior de Educación Rural)
**Autor:** Alonso Arias <soporteplataformas@iser.edu.co>
**Supervisión:** Vicerrectoría Académica ISER

---

## 1. INFORMACIÓN GENERAL DEL PROYECTO

| Campo | Valor |
|-------|-------|
| **Componente** | `local_jobboard` |
| **Versión Actual** | **3.4.0** (2025121251) |
| **Tipo** | Plugin local de Moodle |
| **Moodle requerido** | 4.1+ (2022112800) |
| **Moodle soportado** | 4.1 - 4.5 |
| **Madurez** | MATURITY_STABLE |
| **Licencia** | GNU GPL v3 or later |
| **Propósito** | Sistema de Bolsa de Empleo para reclutamiento de profesores de cátedra |

### 1.1 Estado de Implementación v3.4.0

| Componente | Estado | Observaciones |
|------------|:------:|---------------|
| Base de datos (28 tablas) | ✅ | Completo con upgrade.php |
| Capabilities (~31) | ✅ | Definidas en access.php |
| Roles (3 personalizados) | ✅ | jobboard_reviewer, jobboard_coordinator, jobboard_committee |
| Sistema CSS `jb-*` | ✅ 100% | Migrados en v3.2.3, Grading panel CSS v3.3.0 |
| Templates Mustache (116) | ✅ | Usando clases `jb-*`, +grading_panel.mustache v3.3.0 |
| Renderers especializados (11) | ✅ | renderer_base, dashboard_renderer, admin_renderer, etc. |
| AMD Modules (13) | ✅ | +grading_panel.js v3.3.0 con AJAX nav y atajos teclado |
| User Tours (15) | ✅ | 15 tours con selectores jb-* en db/tours/ (v3.4.0) |
| Strings de idioma | ✅ | ~2,990 líneas EN/ES cada uno |
| Email templates | ✅ | 6 plantillas predefinidas |
| Web Services | ✅ Eliminados | Removidos en v3.2.2 |
| Sistema de migración | ✅ | migrate.php (importación/exportación) |
| Privacy API | ⚠️ | Parcialmente implementada |
| Interfaz revisión mod_assign | ✅ | Implementada v3.3.0: split-pane, PDF preview, AJAX, shortcuts |
| Reportes filtrados por convocatoria | ⚠️ | Parcialmente implementado |
| Consolidación Dashboard | ⚠️ | Pendiente - Ver sección 22 |

---

## 2. ARQUITECTURA IOMAD

### 2.1 Estructura Organizacional Multi-tenant (4 Niveles)

```
NIVEL 1: INSTANCIA IOMAD
         virtual.iser.edu.co
              │
NIVEL 2: COMPANIES (16 Centros Tutoriales)
         ├── Cúcuta
         ├── Ocaña
         ├── El Tarra
         ├── Tibú
         ├── Toledo
         ├── Sardinata
         ├── San Vicente de Chucurí
         ├── Pueblo Bello
         ├── Salazar de las Palmas
         ├── San Pablo
         ├── Santa Rosa del Sur
         ├── Cimitarra
         ├── Saravena
         └── [3 adicionales]
              │
NIVEL 3: DEPARTMENTS (Modalidades por Centro)
         ├── Presencial (PRE)
         ├── A Distancia (DIS)
         ├── Virtual (VIR)
         └── Híbrida (HIB)
              │
NIVEL 4: SUB-DEPARTMENTS (Facultades por Modalidad)
         ├── Facultad de Ciencias Administrativas y Sociales
         └── Facultad de Ingenierías e Informática
```

### 2.2 Estructura Académica

#### Facultad de Ciencias Administrativas y Sociales
- Tecnología en Gestión Empresarial
- Tecnología en Gestión Comunitaria
- Tecnología en Gestión de Mercadeo
- Técnica Profesional en Seguridad y Salud en el Trabajo

#### Facultad de Ingenierías e Informática
- Tecnología Agropecuaria
- Tecnología en Procesos Agroindustriales
- Tecnología en Gestión Industrial
- Tecnología en Gestión de Redes y Sistemas Teleinformáticos
- Tecnología en Gestión y Construcción de Obras Civiles
- Técnica Profesional en Producción de Frutas y Hortalizas

---

## 3. REGLAS DE NEGOCIO FUNDAMENTALES

### 3.1 Organización por Facultad y Programa

| Elemento | Nivel de Organización |
|----------|----------------------|
| Vacantes | Separadas por FACULTAD |
| Comité de Selección | Por FACULTAD (NO por vacante) |
| Revisores de Documentos | Por PROGRAMA académico |
| Convocatorias | Globales con activación de excepciones |

### 3.2 Postulaciones

- **Límite:** Un postulante solo puede aplicar a **UNA vacante por convocatoria**
- **Experiencia ocasional:** Docentes ocasionales requieren 2 años de experiencia laboral equivalente a tiempo completo
- **Carta de intención:** Es un campo de **TEXTO** redactado en el formulario, NO un archivo a cargar

### 3.3 Convocatorias

- **PDF adjunto OBLIGATORIO:** Toda convocatoria debe tener un PDF con el detalle completo
- **Descripción breve:** Campo de texto para resumen
- **Términos y condiciones:** HTML con condiciones legales
- **Botón de acceso al PDF:** Visible en la vista de la convocatoria
- **Fechas de apertura/cierre:** Se gestionan SOLO desde la convocatoria (NO desde vacantes)

### 3.4 Vacantes

- **Sin fechas propias:** Las vacantes NO tienen fecha de apertura/cierre (heredan de la convocatoria)
- **Sin vacante extemporánea:** Eliminar esta opción
- **Organización:** Por facultad académica

### 3.5 Validación de Documentos

- La verificación es **100% MANUAL** - NO existe verificación automática en background
- Cada tipo de documento tiene su checklist de verificación
- Documentos rechazados pueden recargarse con observaciones enviadas por email
- **Razones de rechazo:** illegible, expired, incomplete, wrongtype, mismatch

### 3.6 Excepciones de Documentos

- **GLOBALES:** Se definen en administración, NO por usuario individual
- **Se activan por convocatoria:** Cada convocatoria puede activar/desactivar excepciones
- **Tipos de excepciones:**
  - `historico_iser` - Profesores previamente vinculados con ISER (eximidos de: títulos, cédula, tarjeta profesional, libreta militar, certificaciones laborales que ya reposen en Historia Laboral)
  - `documentos_recientes` - Documentos recientes válidos
  - `traslado_interno` - Traslado interno
  - `recontratacion` - Recontratación

### 3.7 Reglas Especiales de Libreta Militar

| Condición | Requisito |
|-----------|-----------|
| Hombres 18-28 años | Libreta militar obligatoria |
| Hombres declarados NO aptos | Certificado provisional de trámite |
| Hombres exentos | Certificado provisional de trámite |
| Hombres >28 años (superaron edad de incorporación) | Certificado provisional de trámite |
| Mujeres | No aplica |

---

## 4. FORMULARIO DE POSTULACIÓN PERSONALIZABLE

### 4.1 Arquitectura de Dos Niveles

El sistema de documentos funciona en **DOS NIVELES**:

| Nivel | Descripción | Quién configura |
|-------|-------------|-----------------|
| **CATÁLOGO GLOBAL** | Tipos de documento disponibles en el sistema | Administrador del sistema |
| **POR CONVOCATORIA** | Documentos requeridos para esa convocatoria específica | Administrador/Coordinador al crear convocatoria |

**Principio:** El catálogo global define QUÉ documentos PUEDEN existir. Cada convocatoria define QUÉ documentos SE REQUIEREN.

### 4.2 Nivel 1: Catálogo Global de Tipos de Documento

El administrador gestiona un **catálogo maestro** de tipos de documento desde `admin/doctypes.php`:

| Atributo | Descripción | Ejemplo |
|----------|-------------|---------|
| **code** | Código único identificador | `hoja_vida_sigep` |
| **name** | Nombre del documento | `Formato Único Hoja de Vida SIGEP II` |
| **type** | Tipo de campo | `file` (archivo) o `text` (texto) |
| **input_type** | Subtipo de entrada | file, text, textarea, select, url |
| **description** | Descripción general | Texto explicativo |
| **externalurl** | URL donde obtener el documento | `https://www.sigep.gov.co` |
| **acceptedformats** | Formatos aceptados | `pdf` (único formato admitido) |
| **maxsize** | Tamaño máximo (bytes) | 5242880 (5MB) |
| **checklistitems** | Ítems de verificación por defecto (JSON) | `["Legible","Firmado"]` |
| **gender_restricted** | Restricción por género | `M`, `F`, o `null` |
| **iserexempted** | Puede eximirse por historial ISER | `1` = sí, `0` = no |
| **enabled** | Disponible para usar en convocatorias | `1` = sí, `0` = no |

**Operaciones en el Catálogo Global:**
- **Crear:** Añadir nuevo tipo de documento al catálogo
- **Editar:** Modificar atributos base de un documento
- **Desactivar:** No disponible para nuevas convocatorias (no afecta convocatorias existentes)
- **Eliminar:** Solo si NO está usado en ninguna convocatoria

### 4.3 Nivel 2: Configuración por Convocatoria

Al **crear o editar una convocatoria**, el administrador/coordinador selecciona y configura los documentos requeridos:

#### Tabla: `local_jobboard_convocatoria_doctype`

| Campo | Descripción |
|-------|-------------|
| `id` | ID único |
| `convocatoriaid` | FK a la convocatoria |
| `doctypeid` | FK al tipo de documento del catálogo |
| `required` | `1` = obligatorio, `0` = opcional para ESTA convocatoria |
| `sortorder` | Orden de aparición en el formulario de ESTA convocatoria |
| `instructions` | Instrucciones específicas para ESTA convocatoria |
| `requirements` | Requisitos específicos para ESTA convocatoria |
| `maxagedays` | Antigüedad máxima en días (ej: 30 para certificados) |
| `customchecklistitems` | Checklist personalizado para ESTA convocatoria (JSON) |
| `enabled` | Activo/inactivo en ESTA convocatoria |
| `timecreated` | Timestamp de creación |
| `timemodified` | Timestamp de modificación |

#### Operaciones por Convocatoria:

| Acción | Descripción |
|--------|-------------|
| **Seleccionar** | Elegir qué documentos del catálogo aplican a esta convocatoria |
| **Configurar obligatoriedad** | Definir si es obligatorio u opcional |
| **Personalizar instrucciones** | Añadir instrucciones específicas para esta convocatoria |
| **Definir antigüedad** | Establecer máximo de días para certificados |
| **Reordenar** | Cambiar orden de aparición en el formulario |
| **Desactivar** | Quitar documento de esta convocatoria sin eliminarlo |
| **Copiar de otra convocatoria** | Usar configuración de convocatoria anterior como base |

### 4.4 Flujo de Configuración

```
ADMINISTRADOR DEL SISTEMA
         │
         ▼
┌─────────────────────────────────┐
│   CATÁLOGO GLOBAL DE DOCTYPES   │
│   (admin/doctypes.php)          │
│                                 │
│   • Hoja de Vida SIGEP II       │
│   • Declaración Bienes y Rentas │
│   • Cédula de Ciudadanía        │
│   • Títulos Académicos          │
│   • ... (todos los disponibles) │
└─────────────────────────────────┘
         │
         │ Al crear convocatoria
         ▼
┌─────────────────────────────────┐
│   CONVOCATORIA 2025-1           │
│                                 │
│   Documentos seleccionados:     │
│   ☑ Hoja de Vida (Obligatorio)  │
│   ☑ Cédula (Obligatorio)        │
│   ☑ Títulos (Obligatorio)       │
│   ☑ EPS (Oblig, máx 30 días)    │
│   ☐ Tarjeta Profesional (No)    │
│   ...                           │
└─────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────┐
│   CONVOCATORIA 2025-2           │
│   (puede ser diferente)         │
│                                 │
│   Documentos seleccionados:     │
│   ☑ Hoja de Vida (Obligatorio)  │
│   ☑ Cédula (Obligatorio)        │
│   ☑ Títulos (Obligatorio)       │
│   ☑ Tarjeta Profesional (Oblig) │  ← En esta sí se requiere
│   ☑ Nuevo documento XYZ         │  ← Añadido en catálogo
│   ...                           │
└─────────────────────────────────┘
```

### 4.5 Catálogo Inicial de Documentos (Pre-configurados)

El sistema viene con los siguientes tipos de documento **PRE-CARGADOS** en el catálogo global:

| # | Código | Documento | Tipo | URL Externa |
|---|--------|-----------|------|-------------|
| 1 | `hoja_vida_sigep` | Formato Único Hoja de Vida SIGEP II | file | sigep.gov.co |
| 2 | `declaracion_bienes` | Formato Declaración de Bienes y Rentas | file | - |
| 3 | `cedula` | Fotocopia Cédula de Ciudadanía | file | - |
| 4 | `titulos_academicos` | Títulos Académicos | file | - |
| 5 | `tarjeta_profesional` | Fotocopia Tarjeta Profesional | file | - |
| 6 | `libreta_militar` | Fotocopia Libreta Militar | file | - |
| 7 | `formacion_complementaria` | Certificados Formación Complementaria | file | - |
| 8 | `constancias_laborales` | Constancias Laborales | file | - |
| 9 | `rut` | Fotocopia RUT actualizado | file | - |
| 10 | `certificado_eps` | Certificado EPS | file | - |
| 11 | `certificado_pension` | Certificado Fondo de Pensión | file | - |
| 12 | `cuenta_bancaria` | Certificado Cuenta Bancaria | file | - |
| 13 | `antecedentes_disciplinarios` | Antecedentes Disciplinarios | file | procuraduria.gov.co |
| 14 | `antecedentes_fiscales` | Antecedentes Fiscales | file | contraloria.gov.co |
| 15 | `antecedentes_judiciales` | Antecedentes Judiciales | file | [Ver URL abajo] |
| 16 | `medidas_correctivas` | Registro Nacional Medidas Correctivas | file | [Ver URL abajo] |
| 17 | `inhabilidades_ley1918` | Consulta de Inhabilidades | file | [Ver URL abajo] |
| 18 | `redam` | REDAM | file | [Ver URL abajo] |
| 19 | `carta_intencion` | Carta de Intención | **text** | - |

**Notas importantes del catálogo:**
- **Formato único:** Todos los documentos tipo `file` solo aceptan **PDF**
- **Tarjeta Profesional:** Es **OPCIONAL** por defecto. Cada convocatoria decide si la requiere o no
- La obligatoriedad final de cada documento se define **por convocatoria**, no en el catálogo global

### 4.6 URLs de Descarga Pre-configuradas

| Documento | URL |
|-----------|-----|
| Antecedentes Judiciales | https://antecedentes.policia.gov.co:7005/WebJudicial |
| Medidas Correctivas | https://srvcnpc.policia.gov.co/PSC/frm_cnp_consulta.aspx |
| Inhabilidades Ley 1918 | https://inhabilidades.policia.gov.co:8080/ |
| REDAM | https://carpetaciudadana.and.gov.co/inicio-de-sesion |

### 4.7 Interfaz de Configuración de Convocatoria

Al crear/editar una convocatoria, la sección de documentos muestra:

```
┌─────────────────────────────────────────────────────────────────────────┐
│ DOCUMENTOS REQUERIDOS PARA ESTA CONVOCATORIA                            │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│ [Copiar de convocatoria anterior ▼]  [Seleccionar todos] [Limpiar]     │
│                                                                         │
│ ┌─────┬────────────────────────────┬───────────┬────────┬────────────┐ │
│ │ ☑/☐ │ Documento                  │ Obligat.  │ Orden  │ Acciones   │ │
│ ├─────┼────────────────────────────┼───────────┼────────┼────────────┤ │
│ │ ☑   │ Hoja de Vida SIGEP II      │ ● Sí ○ No │   1    │ ⚙ Config   │ │
│ │ ☑   │ Declaración Bienes/Rentas  │ ● Sí ○ No │   2    │ ⚙ Config   │ │
│ │ ☑   │ Cédula de Ciudadanía       │ ● Sí ○ No │   3    │ ⚙ Config   │ │
│ │ ☑   │ Títulos Académicos         │ ● Sí ○ No │   4    │ ⚙ Config   │ │
│ │ ☐   │ Tarjeta Profesional        │ ○ Sí ○ No │   -    │ ⚙ Config   │ │
│ │ ☑   │ Libreta Militar            │ ● Sí ○ No │   5    │ ⚙ Config   │ │
│ │ ...                                                                  │ │
│ └─────┴────────────────────────────┴───────────┴────────┴────────────┘ │
│                                                                         │
│ [+ Añadir documento del catálogo]                                       │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

Al hacer clic en **⚙ Config** se abre modal con:
- Instrucciones específicas para esta convocatoria
- Requisitos específicos
- Antigüedad máxima (días)
- Checklist de verificación personalizado

### 4.8 Excepciones por Convocatoria

Las excepciones también se configuran **POR CONVOCATORIA** (ver sección 3.6):

| Excepción | Documentos Eximidos |
|-----------|---------------------|
| `historico_iser` | Títulos, Cédula, Tarjeta profesional, Libreta militar, Constancias laborales |

Cada convocatoria activa/desactiva las excepciones que aplican.

### 4.9 Beneficios de Esta Arquitectura

| Beneficio | Descripción |
|-----------|-------------|
| **Flexibilidad** | Cada convocatoria puede tener requisitos diferentes |
| **Adaptabilidad** | Cambios normativos se aplican a nuevas convocatorias sin afectar las existentes |
| **Consistencia** | El catálogo global garantiza nombres y códigos uniformes |
| **Trazabilidad** | Se sabe exactamente qué se pidió en cada convocatoria |
| **Reutilización** | Se puede copiar configuración de convocatorias anteriores |
| **Auditoría** | Historial de cambios por convocatoria |

---

## 5. ROLES Y CAPABILITIES

### 5.1 Roles Personalizados del Plugin

| Rol | Shortname | Archetype | Alcance |
|-----|-----------|-----------|---------|
| Revisor de Documentos | `jobboard_reviewer` | teacher | Por PROGRAMA |
| Coordinador de Selección | `jobboard_coordinator` | editingteacher | Sistema |
| Miembro de Comité | `jobboard_committee` | teacher | Por FACULTAD |

### 5.2 Capabilities Requeridas (~34 total)

#### Acceso Básico
```php
local/jobboard:view              // Ver el plugin
local/jobboard:viewinternal      // Ver vacantes internas
local/jobboard:viewpublic        // Ver vacantes públicas
```

#### Postulante
```php
local/jobboard:apply             // Postularse a vacantes
local/jobboard:viewownapplications // Ver postulaciones propias
local/jobboard:uploaddocuments   // Subir documentos
local/jobboard:withdrawapplication // Retirar postulación
```

#### Revisor
```php
local/jobboard:reviewdocuments   // Revisar documentos
local/jobboard:approvedocument   // Aprobar documento
local/jobboard:rejectdocument    // Rechazar documento
local/jobboard:requestdocumentcorrection // Solicitar corrección
local/jobboard:viewassignedapplications // Ver postulaciones asignadas
```

#### Comité de Selección
```php
local/jobboard:evaluatecandidates // Evaluar candidatos
local/jobboard:selectcandidate   // Seleccionar candidato
local/jobboard:rejectcandidate   // Rechazar candidato
local/jobboard:viewfacultyapplications // Ver postulaciones de su facultad
local/jobboard:scheduleinterview // Programar entrevista
```

#### Coordinador
```php
local/jobboard:manageconvocatorias // Gestionar convocatorias
local/jobboard:managevacancies   // Gestionar vacantes
local/jobboard:createvacancy     // Crear vacantes
local/jobboard:editvacancy       // Editar vacantes
local/jobboard:deletevacancy     // Eliminar vacantes
local/jobboard:publishvacancy    // Publicar vacantes
local/jobboard:assignreviewers   // Asignar revisores
local/jobboard:managecommittee   // Gestionar comité
local/jobboard:viewallapplications // Ver todas las postulaciones
local/jobboard:exportdata        // Exportar datos
```

#### Administración
```php
local/jobboard:managedoctypes    // Gestionar tipos de documento
local/jobboard:manageexemptions  // Gestionar excepciones
local/jobboard:managetemplates   // Gestionar plantillas email
local/jobboard:viewaudit         // Ver auditoría
local/jobboard:configuresettings // Configurar ajustes
local/jobboard:manageapitokens   // Gestionar tokens API
```

### 5.3 Matriz de Permisos

| Capability | Postulante | Revisor | Coordinador | Comité | Admin |
|------------|:----------:|:-------:|:-----------:|:------:|:-----:|
| view | ✓ | ✓ | ✓ | ✓ | ✓ |
| viewinternal | ✓ | ✓ | ✓ | ✓ | ✓ |
| apply | ✓ | - | - | - | ✓ |
| viewownapplications | ✓ | - | - | - | ✓ |
| reviewdocuments | - | ✓ | ✓ | - | ✓ |
| validatedocuments | - | ✓ | ✓ | - | ✓ |
| managevacancies | - | - | ✓ | - | ✓ |
| createvacancy | - | - | ✓ | - | ✓ |
| editvacancy | - | - | ✓ | - | ✓ |
| deletevacancy | - | - | - | - | ✓ |
| publishvacancy | - | - | ✓ | - | ✓ |
| manageconvocatorias | - | - | - | - | ✓ |
| evaluate | - | - | - | ✓ | ✓ |
| viewevaluations | - | - | ✓ | ✓ | ✓ |
| viewreports | - | - | ✓ | - | ✓ |
| exportreports | - | - | - | - | ✓ |
| assignreviewers | - | - | ✓ | - | ✓ |
| configure | - | - | - | - | ✓ |
| viewallapplications | - | - | ✓ | - | ✓ |
| changeapplicationstatus | - | - | ✓ | - | ✓ |

---

## 6. BASE DE DATOS

### 6.1 Tablas Principales (28 tablas)

| Tabla | Descripción |
|-------|-------------|
| `local_jobboard_convocatoria` | Convocatorias con PDF adjunto |
| `local_jobboard_vacancy` | Vacantes académicas por facultad |
| `local_jobboard_vacancy_field` | Campos personalizados de vacantes |
| `local_jobboard_application` | Postulaciones |
| `local_jobboard_document` | Documentos subidos |
| `local_jobboard_doctype` | Tipos de documento CONFIGURABLES |
| `local_jobboard_docvalidation` | Validaciones de documentos |
| `local_jobboard_committee` | Comités de selección por facultad |
| `local_jobboard_committee_member` | Miembros del comité |
| `local_jobboard_evaluation` | Evaluaciones de candidatos |
| `local_jobboard_criteria` | Criterios de evaluación |
| `local_jobboard_decision` | Decisiones finales |
| `local_jobboard_exemption` | Excepciones globales |
| `local_jobboard_conv_docexempt` | Excepciones activadas por convocatoria |
| `local_jobboard_convocatoria_doctype` | **Documentos requeridos por convocatoria** |
| `local_jobboard_reviewer` | Revisores asignados |
| `local_jobboard_reviewer_program` | Asignación de revisores por programa |
| `local_jobboard_email_template` | Plantillas de email personalizables |
| `local_jobboard_email_log` | Log de emails enviados |
| `local_jobboard_audit` | Registro de auditoría |
| `local_jobboard_config` | Configuración del plugin |
| `local_jobboard_workflow_log` | Log de cambios de estado |
| `local_jobboard_consent` | Consentimientos de usuarios |
| `local_jobboard_applicant_profile` | Perfil extendido de postulantes |
| `local_jobboard_interview` | Entrevistas programadas |
| `local_jobboard_faculty` | Facultades académicas |
| `local_jobboard_program` | Programas por facultad |
| `local_jobboard_notification` | Notificaciones del sistema |

### 6.2 Convenciones de Tablas

- Prefijo obligatorio: `local_jobboard_`
- Campos de tiempo: `timecreated`, `timemodified` (Unix timestamp)
- Campos de usuario: `createdby`, `modifiedby` (FK a user.id)
- Status como `char` con valores definidos en constantes

---

## 7. SISTEMA CSS PERSONALIZADO

### 7.1 Estado de Implementación: ✅ COMPLETADO

El archivo `styles.css` ya existe con un sistema completo de clases `jb-*`:

**Características implementadas:**
- Variables CSS (`:root`) para colores, espaciado, tipografía
- Sistema de grid (`jb-row`, `jb-col-*`)
- Cards (`jb-card`, `jb-card-header`, `jb-card-body`, `jb-card-footer`)
- Botones (`jb-btn`, `jb-btn-primary`, `jb-btn-secondary`, etc.)
- Badges (`jb-badge`, `jb-badge-*`)
- Tablas (`jb-table`, `jb-table-hover`, `jb-thead-light`)
- Utilidades de espaciado (`jb-m-*`, `jb-p-*`)
- Utilidades de flexbox (`jb-d-flex`, `jb-justify-content-*`)
- Tipografía (`jb-fs-*`, `jb-fw-*`, `jb-text-*`)

### 7.2 Variables CSS Definidas

```css
:root {
    --jb-primary: #0d6efd;
    --jb-secondary: #6c757d;
    --jb-success: #198754;
    --jb-danger: #dc3545;
    --jb-warning: #ffc107;
    --jb-info: #0dcaf0;
    --jb-spacer-1 a --jb-spacer-5;
    --jb-border-radius: 0.375rem;
    --jb-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
```

### 7.3 Templates Actualizados

Los templates Mustache principales ya usan clases `jb-*`:
- `pages/public.mustache` ✅
- `pages/public_vacancy.mustache` ✅
- `pages/manage.mustache` ✅
- `pages/admin_roles.mustache` ✅
- `pages/reports.mustache` ✅
- `pages/schedule_interview.mustache` ✅
- `pages/review.mustache` ✅
- `pages/apply.mustache` ✅
- `pages/convocatorias.mustache` ✅
- `components/timeline.mustache` ✅
- `grading_panel.mustache` ✅ (v3.3.0)
- `admin_email_templates.mustache` ✅

---

## 8. USER TOURS

### 8.1 Estado de Implementación: ✅ IMPLEMENTADO v3.4.0

Los User Tours están definidos en `db/tours/` con selectores `jb-*` para independencia de theme.

### 8.2 Tours Implementados (15)

| Archivo | Descripción | Audiencia |
|---------|-------------|-----------|
| `tour_dashboard.json` | Dashboard principal | Todos |
| `tour_public.json` | Vista pública | Visitantes |
| `tour_convocatorias.json` | Lista de convocatorias | Coordinadores |
| `tour_convocatoria_manage.json` | Gestión de convocatoria | Coordinadores |
| `tour_vacancies.json` | Lista de vacantes | Usuarios |
| `tour_vacancy.json` | Detalle de vacante | Usuarios |
| `tour_manage.json` | Gestión de vacantes | Coordinadores |
| `tour_apply.json` | Proceso de postulación | Postulantes |
| `tour_application.json` | Detalle de postulación | Postulantes |
| `tour_myapplications.json` | Mis postulaciones | Postulantes |
| `tour_documents.json` | Gestión de documentos | Postulantes |
| `tour_review.json` | Panel de revisión | Revisores |
| `tour_myreviews.json` | Mis revisiones | Revisores |
| `tour_validate_document.json` | Validación de documento | Revisores |
| `tour_reports.json` | Reportes | Coordinadores |

### 8.3 Selectores Utilizados

Todos los tours usan selectores `jb-*` para estabilidad entre themes:
- `.jb-page-header`, `.jb-welcome-section`
- `.jb-card`, `.jb-card-shadow`, `.jb-card-body`
- `.jb-convocatoria-card`, `.jb-vacancy-card`, `.jb-application-card`
- `.jb-filter-form`, `.jb-progress-steps`
- `.jb-btn-primary`, `.jb-btn-group`
- `.jb-table`, `.jb-list-group`

---

## 9. MÓDULOS AMD (JavaScript)

### 9.1 Módulos Implementados (13)

| Módulo | Descripción |
|--------|-------------|
| `tooltips.js` | Sistema de tooltips (sin Bootstrap) |
| `public_filters.js` | Filtros de vista pública |
| `review_ui.js` | Interfaz de revisión |
| `document_viewer.js` | Visor de documentos |
| `application_form.js` | Formulario de postulación |
| `navigation.js` | Navegación del plugin |
| `apply_progress.js` | Progreso de postulación |
| `progress_steps.js` | Pasos de progreso |
| `bulk_actions.js` | Acciones masivas con checkboxes |
| `grading_panel.js` | **Panel de calificación mod_assign style** (v3.3.0) |
| `vacancy_manage.js` | Gestión de vacantes |
| `convocatoria_manage.js` | Gestión de convocatorias |
| `doctype_manage.js` | Gestión de tipos de documento |

### 9.2 Reglas de Desarrollo AMD

1. **NUNCA** editar archivos en `amd/build/`
2. **SIEMPRE** compilar después de cambios: `grunt amd --root=local/jobboard`
3. **NO** usar jQuery directamente si existe equivalente en core
4. **NO** usar librerías Bootstrap JS
5. **USAR** módulos core de Moodle para: AJAX, modales, notificaciones, templates

---

## 10. CADENAS DE IDIOMAS

### 10.1 Estado Actual: ✅ IMPLEMENTADO

Las cadenas de idiomas están implementadas con ~2,990 líneas en cada idioma:
- `lang/en/local_jobboard.php`
- `lang/es/local_jobboard.php`

### 10.2 Secciones de Strings

1. Plugin General
2. Navegación y Menús
3. Convocatorias
4. Vacantes
5. Postulaciones
6. Documentos
7. Validación
8. Excepciones
9. Comité de Selección
10. Notificaciones Email
11. Reportes
12. Roles Personalizados
13. Capabilities
14. Errores
15. Formularios
16. Estados de Workflow
17. Auditoría
18. Configuración
19. Grading Panel (v3.3.0)
20. User Tours (v3.4.0) - 236 strings

### 10.3 Reglas

- **Paridad EN/ES:** Toda string DEBE existir en AMBOS idiomas
- **NO hardcodear:** Usar `get_string()` SIEMPRE
- **Prefijo consistente:** Usar formato `componente:accion` para strings relacionadas

---

## 11. SISTEMA DE AUDITORÍA

### 11.1 Acciones a Registrar

| Componente | Acciones |
|------------|----------|
| Convocatoria | create, update, delete, publish, close, archive |
| Vacante | create, update, delete, publish, close |
| Postulación | create, submit, transition, withdraw |
| Documento | upload, download, approve, reject, request_correction |
| Comité | create, update, add_member, remove_member |
| Revisor | assign, revoke |
| Configuración | update_doctype, update_exemption, update_template |
| Email | sent |

### 11.2 Datos a Registrar

| Campo | Descripción |
|-------|-------------|
| `userid` | Usuario que realizó la acción |
| `action` | Acción realizada |
| `entitytype` | Tipo de entidad |
| `entityid` | ID de la entidad |
| `previousvalue` | Valor anterior (JSON) |
| `newvalue` | Valor nuevo (JSON) |
| `extradata` | Datos adicionales (JSON) |
| `ipaddress` | Dirección IP |
| `useragent` | User agent del navegador |
| `timecreated` | Timestamp |

---

## 12. PLANTILLAS DE EMAIL

### 12.1 Plantillas Implementadas (6)

| Template Key | Descripción |
|--------------|-------------|
| `application_received` | Confirmación de postulación |
| `application_status_changed` | Cambio de estado |
| `review_complete` | Revisión completada (email consolidado) |
| `document_rejected` | Documento rechazado |
| `interview_scheduled` | Citación a entrevista |
| `selected` | Notificación de selección |

### 12.2 Variables/Placeholders

```
{USER_FIRSTNAME}
{USER_LASTNAME}
{USER_EMAIL}
{VACANCY_TITLE}
{VACANCY_CODE}
{CONVOCATORIA_NAME}
{APPLICATION_DATE}
{APPLICATION_STATUS}
{DOCUMENT_NAME}
{REJECTION_REASON}
{INTERVIEW_DATE}
{INTERVIEW_LOCATION}
{SITE_NAME}
{SITE_URL}
```

---

## 13. INTERFAZ DE REVISIÓN DE DOCUMENTOS

### 13.1 Estado: ✅ IMPLEMENTADA v3.3.0

La interfaz de revisión estilo mod_assign fue implementada en v3.3.0:

**Características implementadas:**
- **Layout split-pane:** Panel izquierdo (lista documentos), Panel derecho (preview)
- **Visor PDF inline:** Sin necesidad de descargar
- **Navegación AJAX:** Cambiar entre documentos sin recarga de página
- **Atajos de teclado:**
  - `J/K` - Navegar entre documentos
  - `A` - Aprobar documento actual
  - `R` - Enfocar campo de rechazo
  - `D` - Descargar documento
  - `F` - Pantalla completa
  - `S` - Toggle sidebar
  - `?` - Mostrar ayuda

### 13.2 Archivos Relacionados

- `templates/grading_panel.mustache`
- `amd/src/grading_panel.js`
- `classes/output/grading_panel.php`

### 13.3 IMPORTANTE: Análisis de mod_assign

**Para futuras mejoras de la interfaz de revisión, se debe ANALIZAR el plugin mod_assign de Moodle core en lugar de crear funcionalidades desde cero.**

#### Archivos a estudiar en mod_assign:

```
mod/assign/
├── grading_panel.php           # Controlador del panel de calificación
├── grading_form.php            # Formulario de calificación
├── classes/
│   └── output/
│       └── grading_app.php     # Renderable del panel
├── templates/
│   └── grading_panel.mustache  # Template del panel
└── amd/src/
    └── grading_panel.js        # Módulo AMD del panel
```

#### Funcionalidades a replicar de mod_assign:

| Funcionalidad | Ubicación en mod_assign |
|---------------|------------------------|
| Navegación entre envíos | `grading_panel.js` → `handleNavigation()` |
| Actualización sin recarga | `grading_panel.js` → `refreshGradingPanel()` |
| Atajos de teclado | `grading_panel.js` → `registerEventListeners()` |
| Preview de archivos | `classes/output/grading_app.php` |
| Filtros de estudiantes | `grading_form.php` → método de filtrado |
| Guardado rápido | `grading_panel.js` → `saveGrade()` |

#### Beneficios de analizar mod_assign:

1. **Consistencia UX:** Los usuarios ya conocen la interfaz de calificación de Moodle
2. **Código probado:** mod_assign es estable y bien mantenido
3. **Accesibilidad:** Cumple estándares de accesibilidad de Moodle
4. **Compatibilidad:** Funciona en todos los themes soportados
5. **Mantenibilidad:** Patrones de código reconocidos

---

## 14. VISTA PÚBLICA

### 14.1 Funcionalidades

- Ver convocatorias vigentes
- Ver detalle de convocatoria (incluyendo PDF)
- Ver vacantes de la convocatoria
- Ver detalle de vacante
- Botones de acción abren en **nueva pestaña** (login, registro)

### 14.2 Navegación

```
Página pública (public.php)
    └── Convocatoria vigente (ver detalle)
            └── Vacantes disponibles
                    └── Detalle de vacante
                            └── Aplicar (requiere login)
```

---

## 15. FORMULARIO DE POSTULACIÓN CON PESTAÑAS

### 15.1 Estructura

La página `index.php?view=apply&vacancyid=xxx` debe tener **tabs/pestañas**:

| Pestaña | Contenido |
|---------|-----------|
| 1. Información Personal | Datos básicos del postulante |
| 2. Formación Académica | Títulos, certificaciones |
| 3. Experiencia Laboral | Historial laboral |
| 4. Documentos | Carga de archivos |
| 5. Carta de Intención | Campo de texto (NO archivo) |
| 6. Revisión y Envío | Resumen y consentimientos |

---

## 16. REPORTES

### 16.1 Filtro Obligatorio por Convocatoria

**TODOS** los reportes deben estar filtrados por convocatoria. El usuario debe seleccionar una convocatoria antes de ver cualquier reporte.

### 16.2 Reportes Requeridos

| Reporte | Descripción |
|---------|-------------|
| Postulaciones | Lista de postulaciones con estado |
| Documentos | Estado de documentos por postulante |
| Revisores | Carga de trabajo de revisores |
| Evaluaciones | Puntuaciones del comité |
| Auditoría | Log de acciones del sistema |
| Estadísticas | Métricas generales |

---

## 17. REGLAS ABSOLUTAS DE DESARROLLO

1. **ANALIZAR** el repositorio completo antes de implementar
2. **SOLO CLASES jb-*** - No usar clases Bootstrap directamente
3. **VALIDAR SIEMPRE** en plataforma antes de commit
4. **NO improvisar** cambios directamente en producción
5. **Respetar** la arquitectura IOMAD de 4 niveles
6. **Paridad EN/ES** - Toda string debe existir en AMBOS idiomas
7. **NO hardcodear** strings en PHP ni templates - usar `get_string()` SIEMPRE
8. **Documentar** TODO en CHANGELOG.md
9. **ACTUALIZAR DOCUMENTACIÓN** con información de contacto correcta
10. **Comité de selección** es por FACULTAD, no por vacante
11. **Revisores** se asignan por PROGRAMA
12. **Formulario de postulación** configurable **POR CONVOCATORIA** (no global)
13. **Carta de intención** es campo de TEXTO, no archivo
14. **Convocatoria** debe tener PDF adjunto con detalle completo
15. **Auditoría ROBUSTA** - registrar TODAS las acciones
16. Un postulante = UNA vacante por convocatoria
17. La validación de documentos es 100% MANUAL
18. **Búsqueda de usuarios** por username al crear comités
19. **Cada cambio** = incremento de versión + CHANGELOG
20. **Compilar AMD** después de modificaciones: `grunt amd --root=local/jobboard`
21. **Reportes** filtrados por convocatoria obligatoriamente
22. **Documentos:** Catálogo global + configuración específica por convocatoria
23. **Formato único de archivos:** Solo PDF admitido (no jpg, png, etc.)
24. **Tarjeta profesional:** OPCIONAL por defecto (cada convocatoria decide)
25. **Web Services:** NO se usan - fueron eliminados en v3.2.2
26. **User Tours:** Usar selectores `jb-*` para independencia de theme
27. **Analizar mod_assign** para funcionalidades de revisión (no reinventar la rueda)

---

## 18. VERSIONADO

### 18.1 Formato

```php
$plugin->version = YYYYMMDDXX;  // Ej: 2025121251
$plugin->release = 'X.Y.Z';     // Ej: '3.4.0'
```

### 18.2 Incrementos

| Tipo de Cambio | version | release |
|----------------|---------|---------|
| Corrección de typo | +1 | No cambia |
| Nueva string | +1 | No cambia |
| Bug fix | +1 | +0.0.1 |
| Nueva funcionalidad menor | +1 | +0.1.0 |
| Nueva funcionalidad mayor | +1 | +1.0.0 |
| Cambio de BD | +1 | +0.1.0 |

### 18.3 Historial de Versiones Recientes

| Versión | Fecha | Descripción |
|---------|-------|-------------|
| 3.4.0 | 2025-12-12 | User Tours con selectores jb-* (15 tours) |
| 3.3.0 | 2025-12-12 | Grading panel estilo mod_assign con AJAX y atajos |
| 3.2.4 | 2025-12-12 | Corrección doctype input_type comment |
| 3.2.3 | 2025-12-12 | Migración CSS completa a clases jb-* |
| 3.2.2 | 2025-12-12 | Eliminación de web services |

---

## 19. FASES DE IMPLEMENTACIÓN

### Fase 1: Infraestructura Crítica ✅ COMPLETADA
- [x] Crear `styles.css` con sistema de clases `jb-*`
- [x] Crear archivos de idioma completos (EN/ES)
- [x] Crear README.md y CHANGELOG.md

### Fase 2: Migración CSS ✅ COMPLETADA
- [x] Auditar templates Mustache
- [x] Reemplazar clases Bootstrap por `jb-*`
- [x] Probar en themes: Boost, Classic

### Fase 3: Refactorización Renderer ✅ COMPLETADA
- [x] Dividir renderer.php en renderers especializados
- [x] 11 renderers implementados

### Fase 4: Interfaz Revisión ✅ COMPLETADA v3.3.0
- [x] Diseñar layout mod_assign style
- [x] Implementar visor PDF inline
- [x] Agregar navegación AJAX
- [x] Implementar atajos de teclado

### Fase 5: User Tours ✅ COMPLETADA v3.4.0
- [x] Actualizar selectores a clases `jb-*`
- [x] Crear 15 archivos JSON en db/tours/
- [x] Agregar strings de idioma EN/ES (236 strings)

### Fase 6: Consolidación Dashboard ⚠️ PENDIENTE
- [ ] Ver sección 22 para detalles

---

## 20. COMPATIBILIDAD

### 20.1 Moodle

- Mínimo: 4.1 (2022112800)
- Máximo probado: 4.5

### 20.2 PHP

- Mínimo: 7.4
- Recomendado: 8.1+

### 20.3 Themes Compatibles

- Boost (default)
- Classic
- Remui
- Flavor

### 20.4 IOMAD

- Integración completa con arquitectura multi-tenant
- Filtrado por company/department

---

## 21. CUMPLIMIENTO NORMATIVO

### 21.1 GDPR / Habeas Data (Colombia)

- Consentimiento explícito para tratamiento de datos
- Derecho de acceso, rectificación y eliminación
- Privacy API de Moodle implementada

### 21.2 Normativa Colombiana

- Libreta militar: solo hombres 18-28 años (exentos/no aptos presentan certificado provisional)
- Documentos de antecedentes oficiales
- Requisitos MEN para docentes

---

## 22. CONSOLIDACIÓN DEL DASHBOARD

### 22.1 Estado: ⚠️ PENDIENTE

Se requiere una auditoría y reorganización del dashboard para asegurar que:
1. Las funcionalidades mostradas sean relevantes para cada rol
2. No haya vistas redundantes o innecesarias
3. Las funcionalidades faltantes se agreguen

### 22.2 Análisis Requerido

#### Vistas Actuales a Auditar

| Vista | Rol | ¿Necesaria? | Observaciones |
|-------|-----|:-----------:|---------------|
| Estadísticas generales | Admin | ✅ | Mantener |
| Convocatorias activas | Admin/Coord | ✅ | Mantener |
| Vacantes recientes | Admin/Coord | ? | Evaluar utilidad |
| Mis postulaciones | Postulante | ✅ | Mantener |
| Documentos pendientes | Revisor | ✅ | Mantener |
| Cola de revisión | Revisor | ✅ | Mantener |
| [Auditar otras...] | - | ? | - |

#### Funcionalidades Faltantes Potenciales

| Funcionalidad | Rol | Prioridad |
|---------------|-----|-----------|
| Acceso rápido a última convocatoria | Todos | Alta |
| Notificaciones pendientes | Todos | Alta |
| Resumen de actividad reciente | Admin | Media |
| Accesos directos por rol | Todos | Alta |
| [Identificar otras...] | - | - |

### 22.3 Principios de Diseño del Dashboard

1. **Por Rol:** Cada rol ve solo lo relevante para sus tareas
2. **Accionable:** Cada elemento debe llevar a una acción concreta
3. **Priorizado:** Lo más urgente/importante primero
4. **Sin Redundancia:** No duplicar información entre secciones
5. **Consistente:** Mismos patrones de interacción en todo el dashboard

### 22.4 Pasos para Consolidación

1. **Auditar** todas las vistas actuales del dashboard
2. **Documentar** qué muestra cada sección y para qué rol
3. **Identificar** redundancias y elementos innecesarios
4. **Listar** funcionalidades faltantes por rol
5. **Diseñar** nueva estructura del dashboard
6. **Implementar** cambios graduales
7. **Probar** con usuarios de cada rol

---

## 23. GUÍA DE ANÁLISIS DE MOD_ASSIGN

### 23.1 Propósito

Antes de implementar o mejorar funcionalidades de revisión/calificación, **SIEMPRE** analizar cómo lo hace mod_assign de Moodle core.

### 23.2 Estructura de mod_assign Relevante

```
mod/assign/
├── grading_panel.php           # Controlador del panel de calificación
├── grading_form.php            # Formulario de calificación
├── classes/output/grading_app.php     # Renderable del panel
├── templates/grading_panel.mustache   # Template del panel
└── amd/src/grading_panel.js           # Módulo AMD del panel
```

### 23.3 Patrones a Replicar

| Patrón | Descripción | Beneficio |
|--------|-------------|-----------|
| Split-pane layout | Lista izquierda, contenido derecha | UX familiar |
| Navegación con flechas | Anterior/Siguiente estudiante | Eficiencia |
| Guardar sin recargar | AJAX para persistir cambios | Fluidez |
| Atajos de teclado | Acciones rápidas | Productividad |
| Filtros de estado | Mostrar solo pendientes, etc. | Organización |
| Preview inline | Ver archivos sin descargar | Comodidad |

### 23.4 Cómo Analizar

1. **Leer código fuente** de los archivos listados
2. **Probar la interfaz** en una instalación de Moodle
3. **Inspeccionar DOM** para entender estructura HTML
4. **Ver Network tab** para entender llamadas AJAX
5. **Documentar** hallazgos antes de implementar

### 23.5 Adaptaciones para Job Board

| mod_assign | local_jobboard |
|------------|----------------|
| Estudiantes | Postulantes |
| Envíos | Documentos |
| Calificación | Validación (Aprobar/Rechazar) |
| Retroalimentación | Observaciones de rechazo |
| Grade | Status (approved/rejected/pending) |

---

*Documento actualizado: 2025-12-12*
*Versión del plugin: 3.4.0*
*Fuente: Consolidación de conversaciones del proyecto local_jobboard*

# Análisis Fase 1: Estructura del Plugin local_jobboard

## Fecha: 2026-01-08
## Versión Actual: 2025122301 (release 4.0.1)

---

## 1. Estructura de Archivos

### Directorio Principal
```
local/jobboard/
├── index.php                 # Punto de entrada principal (router)
├── version.php               # Versión del plugin (2025122301)
├── settings.php              # Configuración del plugin
├── lib.php                   # Funciones globales del plugin
├── support.php               # Página de soporte
├── updateprofile.php         # Actualización de perfil de postulante
├── download_documents.php    # Descarga de documentos
├── reupload_document.php     # Recarga de documentos
```

### Directorio db/
```
db/
├── install.xml               # Definición de tablas
├── install.php               # Script de instalación
├── upgrade.php               # Script de actualización
├── access.php                # Capabilities del plugin
├── services.php              # Servicios web externos
├── messages.php              # Proveedores de mensajes
├── caches.php                # Definiciones de caché
├── tasks.php                 # Tareas programadas
└── uninstall.php             # Script de desinstalación
```

### Directorio views/
```
views/
├── dashboard.php             # Dashboard principal
├── vacancies.php             # Listado de vacantes
├── vacancy.php               # Detalle de vacante
├── apply.php                 # Formulario de postulación
├── application.php           # Detalle de postulación
├── applications.php          # Listado de postulaciones
├── convocatorias.php         # Listado de convocatorias
├── convocatoria.php          # Detalle de convocatoria
├── review.php                # Revisión de documentos
├── myreviews.php             # Mis revisiones asignadas
├── reports.php               # Reportes
├── manage.php                # Gestión administrativa
└── public.php                # Vista pública
```

### Directorio classes/
```
classes/
├── application.php           # Clase de postulación (flujo principal)
├── vacancy.php               # Clase de vacante
├── document.php              # Clase de documento
├── convocatoria.php          # Clase de convocatoria (si existe)
├── audit.php                 # Sistema de auditoría
├── notification.php          # Sistema de notificaciones
├── reviewer.php              # Gestión de revisores
├── program_reviewer.php      # Revisores por programa
├── exemption.php             # Exenciones ISER
├── interview.php             # Entrevistas
├── forms/                    # Formularios Moodle
│   ├── application_form.php
│   ├── convocatoria_form.php
│   └── vacancy_form.php
├── helper/                   # Clases de ayuda
│   ├── status_helper.php
│   ├── role_access_helper.php
│   └── date_helper.php
└── external/                 # Servicios web externos
    ├── validate_document.php
    └── reject_document.php
```

---

## 2. Estructura de Base de Datos

### Tablas Principales

| Tabla | Propósito |
|-------|-----------|
| `local_jobboard_vacancy` | Vacantes publicadas |
| `local_jobboard_application` | Postulaciones de usuarios |
| `local_jobboard_document` | Documentos cargados |
| `local_jobboard_doc_validation` | Validaciones de documentos |
| `local_jobboard_convocatoria` | Convocatorias (agrupa vacantes) |
| `local_jobboard_faculty` | Facultades por empresa IOMAD |
| `local_jobboard_program` | Programas académicos |
| `local_jobboard_program_reviewer` | Revisores por programa |
| `local_jobboard_doctype` | Tipos de documentos requeridos |
| `local_jobboard_audit` | Log de auditoría |
| `local_jobboard_workflow_log` | Log de cambios de estado |
| `local_jobboard_notification` | Cola de notificaciones |
| `local_jobboard_email_template` | Plantillas de correo |
| `local_jobboard_exemption` | Exenciones ISER |

### Tabla: local_jobboard_application
Campos relevantes para el flujo:
- `id` - ID único
- `vacancyid` - FK a vacancy
- `userid` - FK a user
- `status` - Estado actual de la postulación
- `reviewerid` - Revisor asignado (si aplica)
- `statusnotes` - Notas del estado
- `timecreated`, `timemodified` - Timestamps

**Índice único:** `(vacancyid, userid)` - Impide múltiples postulaciones del mismo usuario a la misma vacante.

### Tabla: local_jobboard_convocatoria
Campos de fechas de revisión (agregados en v4.0.0):
- `dean_review_startdate` - Inicio revisión decano
- `dean_review_enddate` - Fin revisión decano
- `hr_review_startdate` - Inicio validación TH
- `hr_review_enddate` - Fin validación TH

---

## 3. Estados de Postulación (Flujo Actual)

```php
const STATUSES = [
    'draft',                    // Borrador (guardado parcial)
    'submitted',                // Enviada (espera cierre convocatoria)
    'pending_dean_review',      // Pendiente revisión decano
    'dean_approved',            // Preseleccionado por decano
    'dean_rejected',            // Rechazado por decano (FINAL)
    'pending_hr_validation',    // Pendiente validación TH
    'hr_validated',             // Validado por TH (FINAL)
    'hr_rejected',              // Rechazado por TH (FINAL)
    'withdrawn',                // Retirada por usuario (FINAL)
];
```

### Transiciones Permitidas
```php
const TRANSITIONS = [
    'draft' => ['submitted'],
    'submitted' => ['pending_dean_review', 'withdrawn'],
    'pending_dean_review' => ['dean_approved', 'dean_rejected'],
    'dean_approved' => ['pending_hr_validation'],
    'dean_rejected' => [],              // FINAL
    'pending_hr_validation' => ['hr_validated', 'hr_rejected'],
    'hr_validated' => [],               // FINAL
    'hr_rejected' => [],                // FINAL
    'withdrawn' => [],                  // FINAL
];
```

---

## 4. Roles del Plugin

### Roles Actuales (creados en install.php)

| Rol | Shortname | Propósito |
|-----|-----------|-----------|
| Decano/Revisor | `jobboard_dean` | Preselecciona perfiles |
| Talento Humano | `jobboard_hr` | Valida documentos |

### Capabilities del Rol Dean
- `local/jobboard:view`
- `local/jobboard:viewinternal`
- `local/jobboard:viewallapplications`
- `local/jobboard:downloadanydocument`
- `local/jobboard:reviewprofiles`
- `local/jobboard:approveprofile`

### Capabilities del Rol HR
- `local/jobboard:view`
- `local/jobboard:viewinternal`
- `local/jobboard:viewallapplications`
- `local/jobboard:downloadanydocument`
- `local/jobboard:validatedocuments`
- `local/jobboard:reviewdocuments`
- `local/jobboard:validatehr`

---

## 5. HALLAZGOS CRÍTICOS PARA FASE 1

### 5.1 Bug de Repostulación Después de Retiro

**Archivo afectado:** `classes/application.php:235-261`

**Problema identificado:**
El método `user_has_submitted_application()` verifica si un usuario ya tiene una postulación enviada (excluyendo borradores), pero **NO excluye postulaciones retiradas (`withdrawn`)**.

```php
// Líneas 235-250 - Código actual problemático
public static function user_has_applied(int $vacancyid, int $userid, bool $excludedrafts = false): bool {
    global $DB;

    if ($excludedrafts) {
        return $DB->record_exists_select(
            'local_jobboard_application',
            'vacancyid = :vacancyid AND userid = :userid AND status != :status',
            ['vacancyid' => $vacancyid, 'userid' => $userid, 'status' => 'draft']
        );
    }
    // ...
}
```

**Adicionalmente:** El índice único `(vacancyid, userid)` en la tabla `local_jobboard_application` impide crear un nuevo registro si ya existe uno previo.

**Solución propuesta:**
1. Modificar `user_has_submitted_application()` para excluir también `withdrawn`
2. Cuando se intente postular y exista una postulación retirada, reutilizar ese registro cambiando su estado a `draft`

### 5.2 Asignación de Revisores por Facultad

**Estado actual:** Existe la tabla `local_jobboard_program_reviewer` para asignar revisores por **programa académico**, no por facultad.

**Para convocatorias tipo DOCENTE se requiere:**
- Asignación de revisores (decanos) por **facultad**
- Cada facultad debe tener un revisor asignado
- El revisor solo puede ver postulaciones de su facultad

**Tablas existentes relacionadas:**
- `local_jobboard_faculty` - Facultades por empresa IOMAD
- `local_jobboard_program` - Programas (tienen FK a faculty)
- `local_jobboard_program_reviewer` - Revisores por programa (no por facultad)

**Solución propuesta:**
1. Crear nueva tabla `local_jobboard_faculty_reviewer` para asignar revisores a facultades
2. O bien, agregar campo `facultyid` a `local_jobboard_program_reviewer` y hacerlo opcional (program O faculty)
3. Modificar la lógica de asignación y filtrado para soportar revisores por facultad

---

## 6. PLAN DE CORRECCIONES FASE 1

### Corrección 1: Bug Repostulación (Prioridad ALTA)

**Archivos a modificar:**
- `classes/application.php` - Método `user_has_applied()` y `user_has_submitted_application()`
- `views/apply.php` - Lógica de detección de postulación anterior retirada

**Cambios:**
1. Modificar query en `user_has_applied()` para excluir `withdrawn`:
   ```php
   'vacancyid = :vacancyid AND userid = :userid AND status NOT IN (:draft, :withdrawn)',
   ```
2. Agregar lógica en `apply.php` para detectar postulación retirada y reutilizarla
3. Agregar método `reactivate_withdrawn()` para cambiar estado de `withdrawn` a `draft`

**Versión requerida:** Incrementar a `2025010800`

### Corrección 2: Revisores por Facultad (Prioridad ALTA)

**Archivos a modificar:**
- `db/install.xml` - Agregar tabla `local_jobboard_faculty_reviewer`
- `db/upgrade.php` - Migración para crear nueva tabla
- `classes/faculty_reviewer.php` - Nueva clase (o reutilizar reviewer.php)
- `admin/assign_reviewer.php` - Interfaz de asignación
- Lógica de filtrado en revisión

**Versión requerida:** Incrementar a `2025010801`

---

## 7. PRÓXIMOS PASOS

1. **Confirmar entendimiento** con el usuario
2. Aplicar corrección de bug de repostulación
3. Aplicar implementación de revisores por facultad
4. Documentar cambios en `fase1_correcciones_aplicadas.md`
5. Esperar aprobación antes de cambios grandes

---

**¿Continúo con la Fase 1 de Implementación (correcciones)?**

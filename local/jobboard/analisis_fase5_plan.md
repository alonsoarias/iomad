# FASE 5: Plan de Modificaciones

## 1. RESUMEN DEL PLAN

### Objetivo
Reestructurar el flujo de trabajo del plugin jobboard para:
1. Eliminar el Comite de Seleccion
2. Implementar flujo Decano -> Talento Humano
3. Agregar control de acceso por fechas
4. Mejorar sistema de auditoria

### Entregables
- Nuevos roles: `jobboard_dean`, `jobboard_hr`
- Nuevos estados de postulacion
- Nuevas capabilities
- Nuevos campos en convocatoria
- Sistema de auditoria mejorado

---

## 2. MODIFICACIONES EN BASE DE DATOS

### 2.1 Modificar tabla `local_jobboard_convocatoria`

Agregar campos para fechas de revision:

```xml
<!-- En db/install.xml -->
<FIELD NAME="dean_review_startdate" TYPE="int" LENGTH="10" NOTNULL="false"
       SEQUENCE="false" COMMENT="Start date for dean review period"/>
<FIELD NAME="dean_review_enddate" TYPE="int" LENGTH="10" NOTNULL="false"
       SEQUENCE="false" COMMENT="End date for dean review period"/>
<FIELD NAME="hr_review_startdate" TYPE="int" LENGTH="10" NOTNULL="false"
       SEQUENCE="false" COMMENT="Start date for HR validation period"/>
<FIELD NAME="hr_review_enddate" TYPE="int" LENGTH="10" NOTNULL="false"
       SEQUENCE="false" COMMENT="End date for HR validation period"/>
```

### 2.2 Modificar tabla `local_jobboard_audit`

Agregar campo para rol:

```xml
<FIELD NAME="userrole" TYPE="char" LENGTH="50" NOTNULL="false"
       SEQUENCE="false" COMMENT="Role shortname of user performing action"/>
```

### 2.3 Script de Upgrade

```php
// db/upgrade.php
if ($oldversion < 2025122300) {
    $table = new xmldb_table('local_jobboard_convocatoria');

    // Agregar campos de fechas
    $fields = [
        new xmldb_field('dean_review_startdate', XMLDB_TYPE_INTEGER, '10'),
        new xmldb_field('dean_review_enddate', XMLDB_TYPE_INTEGER, '10'),
        new xmldb_field('hr_review_startdate', XMLDB_TYPE_INTEGER, '10'),
        new xmldb_field('hr_review_enddate', XMLDB_TYPE_INTEGER, '10'),
    ];

    foreach ($fields as $field) {
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

    // Agregar campo rol a auditoria
    $audittable = new xmldb_table('local_jobboard_audit');
    $rolefield = new xmldb_field('userrole', XMLDB_TYPE_CHAR, '50');
    if (!$dbman->field_exists($audittable, $rolefield)) {
        $dbman->add_field($audittable, $rolefield);
    }

    upgrade_plugin_savepoint(true, 2025122300, 'local', 'jobboard');
}
```

---

## 3. NUEVAS CAPABILITIES

### 3.1 En db/access.php

```php
// Revision de perfiles por Decano
'local/jobboard:reviewprofiles' => [
    'captype' => 'read',
    'contextlevel' => CONTEXT_SYSTEM,
    'archetypes' => [],
],

// Aprobar/rechazar perfil completo
'local/jobboard:approveprofile' => [
    'captype' => 'write',
    'contextlevel' => CONTEXT_SYSTEM,
    'archetypes' => [],
],

// Validacion final de documentos por HR
'local/jobboard:validatehr' => [
    'captype' => 'write',
    'contextlevel' => CONTEXT_SYSTEM,
    'archetypes' => [],
],
```

---

## 4. NUEVOS ROLES

### 4.1 Rol Decano (jobboard_dean)

```php
// En db/install.php - funcion local_jobboard_create_roles()

// Role: Dean Reviewer
$deanrole = $DB->get_record('role', ['shortname' => 'jobboard_dean']);
if (!$deanrole) {
    $deanroleid = create_role(
        get_string('role_dean', 'local_jobboard'),
        'jobboard_dean',
        get_string('role_dean_desc', 'local_jobboard'),
        'teacher'
    );

    $deancaps = [
        'local/jobboard:view',
        'local/jobboard:viewinternal',
        'local/jobboard:viewallapplications',
        'local/jobboard:downloadanydocument',
        'local/jobboard:reviewprofiles',
        'local/jobboard:approveprofile',
    ];

    foreach ($deancaps as $cap) {
        assign_capability($cap, CAP_ALLOW, $deanroleid, $systemcontext->id);
    }

    set_role_contextlevels($deanroleid, [CONTEXT_SYSTEM]);
}
```

### 4.2 Rol Talento Humano (jobboard_hr)

```php
// Role: HR Validator
$hrrole = $DB->get_record('role', ['shortname' => 'jobboard_hr']);
if (!$hrrole) {
    $hrroleid = create_role(
        get_string('role_hr', 'local_jobboard'),
        'jobboard_hr',
        get_string('role_hr_desc', 'local_jobboard'),
        'teacher'
    );

    $hrcaps = [
        'local/jobboard:view',
        'local/jobboard:viewinternal',
        'local/jobboard:viewallapplications',
        'local/jobboard:downloadanydocument',
        'local/jobboard:validatedocuments',
        'local/jobboard:reviewdocuments',
        'local/jobboard:validatehr',
    ];

    foreach ($hrcaps as $cap) {
        assign_capability($cap, CAP_ALLOW, $hrroleid, $systemcontext->id);
    }

    set_role_contextlevels($hrroleid, [CONTEXT_SYSTEM]);
}
```

---

## 5. MODIFICAR CLASE APPLICATION

### 5.1 Nuevos Estados

```php
// classes/application.php

public const STATUSES = [
    'draft',
    'submitted',
    'pending_dean_review',
    'dean_approved',
    'dean_rejected',
    'pending_hr_validation',
    'hr_validated',
    'hr_rejected',
    'withdrawn',
];

public const TRANSITIONS = [
    'draft' => ['submitted'],
    'submitted' => ['pending_dean_review', 'withdrawn'],
    'pending_dean_review' => ['dean_approved', 'dean_rejected'],
    'dean_approved' => ['pending_hr_validation'],
    'dean_rejected' => [], // estado final
    'pending_hr_validation' => ['hr_validated', 'hr_rejected'],
    'hr_validated' => [], // estado final
    'hr_rejected' => [], // estado final
    'withdrawn' => [], // estado final
];

// Estados finales
public const FINAL_STATUSES = [
    'dean_rejected',
    'hr_validated',
    'hr_rejected',
    'withdrawn',
];
```

### 5.2 Nuevos Metodos

```php
/**
 * Avanzar postulacion a revision de Decano.
 * Se llama cuando cierra la convocatoria.
 */
public function advance_to_dean_review(): void {
    if ($this->status !== 'submitted') {
        throw new \moodle_exception('error:invalidtransition', 'local_jobboard');
    }

    $this->change_status('pending_dean_review',
        get_string('convocatoriaclosed', 'local_jobboard'));
}

/**
 * Aprobar perfil (solo Decano).
 */
public function approve_profile(string $comments = ''): void {
    $this->change_status('dean_approved', $comments);
    // Automaticamente avanzar a validacion HR
    $this->change_status('pending_hr_validation',
        get_string('profileapproved_advancing', 'local_jobboard'));
}

/**
 * Rechazar perfil (solo Decano).
 */
public function reject_profile(string $reason): void {
    $this->change_status('dean_rejected', $reason);
    notification::queue_profile_rejected($this, $reason);
}

/**
 * Validar por HR (estado final positivo).
 */
public function validate_hr(string $comments = ''): void {
    $this->change_status('hr_validated', $comments);
    notification::queue_hr_validated($this);
}

/**
 * Rechazar por HR (estado final negativo).
 */
public function reject_hr(string $reason): void {
    $this->change_status('hr_rejected', $reason);
    notification::queue_hr_rejected($this, $reason);
}

/**
 * Verificar si postulacion esta en estado final.
 */
public function is_final(): bool {
    return in_array($this->status, self::FINAL_STATUSES);
}
```

---

## 6. MODIFICAR CLASE AUDIT

### 6.1 Nuevo Metodo con Rol

```php
// classes/audit.php

/**
 * Log action with role information.
 */
public static function log_with_role(
    string $action,
    string $entitytype,
    ?int $entityid = null,
    array $extradata = [],
    ?array $previousvalue = null,
    ?array $newvalue = null
): void {
    global $USER, $DB;

    // Obtener rol del usuario en el contexto
    $role = self::get_user_role($USER->id);
    $extradata['user_role'] = $role;

    // Llamar al metodo original
    self::log($action, $entitytype, $entityid, $extradata, $previousvalue, $newvalue);

    // Tambien guardar en campo dedicado
    // ... actualizar ultimo registro con userrole
}

/**
 * Obtener rol principal del usuario para jobboard.
 */
protected static function get_user_role(int $userid): string {
    global $DB;

    $context = \context_system::instance();
    $roles = get_user_roles($context, $userid);

    $jobboard_roles = ['jobboard_dean', 'jobboard_hr', 'jobboard_coordinator',
                       'jobboard_reviewer', 'manager'];

    foreach ($roles as $role) {
        if (in_array($role->shortname, $jobboard_roles)) {
            return $role->shortname;
        }
    }

    return 'user';
}
```

---

## 7. NUEVO HELPER DE ACCESO POR ROL

### 7.1 Nueva Clase role_access_helper

```php
// classes/helper/role_access_helper.php

namespace local_jobboard\helper;

class role_access_helper {

    /**
     * Verificar si Decano puede acceder a revision.
     */
    public static function can_dean_access(\stdClass $convocatoria): bool {
        $now = time();

        if (empty($convocatoria->dean_review_startdate) ||
            empty($convocatoria->dean_review_enddate)) {
            return false;
        }

        return ($now >= $convocatoria->dean_review_startdate &&
                $now <= $convocatoria->dean_review_enddate);
    }

    /**
     * Verificar si HR puede acceder a validacion.
     */
    public static function can_hr_access(\stdClass $convocatoria): bool {
        $now = time();

        if (empty($convocatoria->hr_review_startdate) ||
            empty($convocatoria->hr_review_enddate)) {
            return false;
        }

        return ($now >= $convocatoria->hr_review_startdate &&
                $now <= $convocatoria->hr_review_enddate);
    }

    /**
     * Obtener postulaciones visibles segun rol.
     */
    public static function get_visible_applications(int $convocatoriaid, string $role): array {
        global $DB;

        switch ($role) {
            case 'jobboard_dean':
                // Solo postulaciones pendientes de revision Decano
                return $DB->get_records('local_jobboard_application', [
                    'convocatoriaid' => $convocatoriaid,
                    'status' => 'pending_dean_review',
                ]);

            case 'jobboard_hr':
                // Solo postulaciones pendientes de validacion HR
                return $DB->get_records('local_jobboard_application', [
                    'convocatoriaid' => $convocatoriaid,
                    'status' => 'pending_hr_validation',
                ]);

            default:
                return [];
        }
    }

    /**
     * Verificar si usuario tiene rol de Decano.
     */
    public static function is_dean(int $userid = 0): bool {
        return self::has_role($userid, 'jobboard_dean');
    }

    /**
     * Verificar si usuario tiene rol de HR.
     */
    public static function is_hr(int $userid = 0): bool {
        return self::has_role($userid, 'jobboard_hr');
    }

    /**
     * Verificar si usuario tiene un rol especifico.
     */
    protected static function has_role(int $userid, string $roleshortname): bool {
        global $USER, $DB;

        if (!$userid) {
            $userid = $USER->id;
        }

        $context = \context_system::instance();
        $role = $DB->get_record('role', ['shortname' => $roleshortname]);

        if (!$role) {
            return false;
        }

        return $DB->record_exists('role_assignments', [
            'roleid' => $role->id,
            'contextid' => $context->id,
            'userid' => $userid,
        ]);
    }
}
```

---

## 8. MODIFICAR FORMULARIO DE CONVOCATORIA

### 8.1 En classes/forms/convocatoria_form.php

Agregar campos de fechas:

```php
// Seccion: Fechas de Revision

$mform->addElement('header', 'reviewdates',
    get_string('reviewdates', 'local_jobboard'));

// Fechas Decano
$mform->addElement('date_time_selector', 'dean_review_startdate',
    get_string('dean_review_startdate', 'local_jobboard'));
$mform->addHelpButton('dean_review_startdate', 'dean_review_startdate', 'local_jobboard');

$mform->addElement('date_time_selector', 'dean_review_enddate',
    get_string('dean_review_enddate', 'local_jobboard'));
$mform->addHelpButton('dean_review_enddate', 'dean_review_enddate', 'local_jobboard');

// Fechas HR
$mform->addElement('date_time_selector', 'hr_review_startdate',
    get_string('hr_review_startdate', 'local_jobboard'));
$mform->addHelpButton('hr_review_startdate', 'hr_review_startdate', 'local_jobboard');

$mform->addElement('date_time_selector', 'hr_review_enddate',
    get_string('hr_review_enddate', 'local_jobboard'));
$mform->addHelpButton('hr_review_enddate', 'hr_review_enddate', 'local_jobboard');
```

---

## 9. MODIFICAR VISTA DE REVISION

### 9.1 En views/review.php

```php
// Control de acceso segun rol

use local_jobboard\helper\role_access_helper;

// Obtener rol del usuario
$is_dean = role_access_helper::is_dean();
$is_hr = role_access_helper::is_hr();

// Obtener convocatoria
$vacancy = vacancy::get($vacancyid);
$convocatoria = $DB->get_record('local_jobboard_convocatoria',
    ['id' => $vacancy->convocatoriaid]);

// Verificar acceso por fechas
if ($is_dean && !role_access_helper::can_dean_access($convocatoria)) {
    throw new \moodle_exception('error:dean_access_dates', 'local_jobboard');
}

if ($is_hr && !role_access_helper::can_hr_access($convocatoria)) {
    throw new \moodle_exception('error:hr_access_dates', 'local_jobboard');
}

// Filtrar postulaciones segun rol
if ($is_dean) {
    // Solo mostrar pendientes de revision Decano
    $applications = application::get_list(['status' => 'pending_dean_review']);
} elseif ($is_hr) {
    // Solo mostrar pendientes de validacion HR
    $applications = application::get_list(['status' => 'pending_hr_validation']);
}

// Configurar acciones disponibles
$data->can_approve_profile = $is_dean &&
    has_capability('local/jobboard:approveprofile', $context);
$data->can_validate_documents = $is_hr &&
    has_capability('local/jobboard:validatedocuments', $context);
$data->can_validate_hr = $is_hr &&
    has_capability('local/jobboard:validatehr', $context);
```

---

## 10. NUEVAS PLANTILLAS DE EMAIL

### 10.1 Plantillas a Agregar

```php
// En db/install.php - local_jobboard_get_default_email_templates()

// Perfil aprobado por Decano
[
    'code' => 'dean_approved',
    'name' => 'Perfil aprobado por Decano',
    'subject' => 'Su perfil ha sido aprobado - {VACANCY_TITLE}',
    'body' => '<p>Estimado/a {USER_FULLNAME},</p>
<p>Su perfil para la vacante <strong>{VACANCY_CODE} - {VACANCY_TITLE}</strong> ha sido aprobado.</p>
<p>Su postulacion avanzara a la etapa de validacion de documentos.</p>
<p>Atentamente,<br>Equipo de Recursos Humanos</p>',
    'bodyformat' => 1,
    'enabled' => 1,
],

// Perfil rechazado por Decano
[
    'code' => 'dean_rejected',
    'name' => 'Perfil rechazado',
    'subject' => 'Resultado de revision de perfil - {VACANCY_TITLE}',
    'body' => '<p>Estimado/a {USER_FULLNAME},</p>
<p>Despues de revisar su perfil para la vacante <strong>{VACANCY_CODE} - {VACANCY_TITLE}</strong>, lamentamos informarle que no cumple con los requisitos para continuar en el proceso.</p>
<p>Razon: {REJECT_REASON}</p>
<p>Le animamos a postularse a futuras convocatorias.</p>
<p>Atentamente,<br>Equipo de Recursos Humanos</p>',
    'bodyformat' => 1,
    'enabled' => 1,
],

// Validado por HR (final positivo)
[
    'code' => 'hr_validated',
    'name' => 'Postulacion validada',
    'subject' => 'Felicitaciones - Postulacion validada para {VACANCY_TITLE}',
    'body' => '<p>Estimado/a {USER_FULLNAME},</p>
<p>Nos complace informarle que su postulacion para la vacante <strong>{VACANCY_CODE} - {VACANCY_TITLE}</strong> ha sido validada exitosamente.</p>
<p>Proximamente recibira informacion sobre los siguientes pasos.</p>
<p>Felicitaciones!</p>
<p>Atentamente,<br>Equipo de Recursos Humanos</p>',
    'bodyformat' => 1,
    'enabled' => 1,
],

// Rechazado por HR (final negativo)
[
    'code' => 'hr_rejected',
    'name' => 'Postulacion rechazada por documentacion',
    'subject' => 'Resultado de validacion - {VACANCY_TITLE}',
    'body' => '<p>Estimado/a {USER_FULLNAME},</p>
<p>Despues de validar su documentacion para la vacante <strong>{VACANCY_CODE} - {VACANCY_TITLE}</strong>, lamentamos informarle que no podemos continuar con su postulacion.</p>
<p>Razon: {REJECT_REASON}</p>
<p>Atentamente,<br>Equipo de Recursos Humanos</p>',
    'bodyformat' => 1,
    'enabled' => 1,
],
```

---

## 11. CRONOGRAMA DE IMPLEMENTACION

### Sprint 1: Preparacion (Sin cambios visibles)
- [ ] Agregar campos a tabla convocatoria
- [ ] Agregar campo userrole a tabla audit
- [ ] Agregar nuevas capabilities
- [ ] Agregar nuevos roles
- [ ] Agregar cadenas de idioma

### Sprint 2: Nuevo Flujo
- [ ] Modificar STATUSES en application.php
- [ ] Modificar TRANSITIONS en application.php
- [ ] Crear role_access_helper.php
- [ ] Modificar audit.php para registrar rol
- [ ] Agregar campos a formulario convocatoria

### Sprint 3: Vistas y Templates
- [ ] Modificar views/review.php
- [ ] Modificar renderer de aplicaciones
- [ ] Crear/modificar templates para nuevo flujo
- [ ] Actualizar status_helper.php

### Sprint 4: Eliminacion
- [ ] Eliminar archivos del comite
- [ ] Eliminar tablas del comite
- [ ] Eliminar capabilities obsoletas
- [ ] Eliminar rol committee
- [ ] Limpiar referencias

### Sprint 5: Testing y Documentacion
- [ ] Actualizar tests unitarios
- [ ] Actualizar tests Behat
- [ ] Verificar migracion de datos existentes
- [ ] Documentar cambios

---

## 12. RIESGOS Y MITIGACIONES

| Riesgo | Impacto | Mitigacion |
|--------|---------|------------|
| Datos historicos del comite | Medio | Mantener tablas archivadas temporalmente |
| Usuarios con rol committee | Bajo | Script para reasignar a otros roles |
| Postulaciones en estados eliminados | Alto | Script de migracion de estados |
| Referencias rotas en codigo | Alto | Busqueda exhaustiva antes de eliminar |

### Script de Migracion de Estados

```php
// En upgrade.php
// Migrar postulaciones de estados antiguos a nuevos

$status_map = [
    'under_review' => 'pending_dean_review',
    'docs_validated' => 'dean_approved',
    'docs_rejected' => 'dean_rejected',
    'interview' => 'pending_hr_validation',
    'selected' => 'hr_validated',
    'rejected' => 'hr_rejected', // o dean_rejected segun caso
];

foreach ($status_map as $old => $new) {
    $DB->execute(
        "UPDATE {local_jobboard_application} SET status = :new WHERE status = :old",
        ['new' => $new, 'old' => $old]
    );
}
```

---

## 13. CHECKLIST FINAL

### Pre-implementacion
- [ ] Backup completo de base de datos
- [ ] Documentar usuarios con rol committee
- [ ] Documentar postulaciones en estados a eliminar
- [ ] Notificar a usuarios afectados

### Post-implementacion
- [ ] Verificar acceso de Decanos
- [ ] Verificar acceso de HR
- [ ] Verificar nuevas transiciones de estado
- [ ] Verificar notificaciones por email
- [ ] Verificar auditoria con roles
- [ ] Verificar que no hay errores en logs

---

*Documento generado: 2025-12-22*
*Fase 5 de 5 completada*

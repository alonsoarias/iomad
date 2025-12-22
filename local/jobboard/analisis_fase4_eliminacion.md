# FASE 4: Codigo a Eliminar

## 1. RESUMEN EJECUTIVO

### Estadisticas de Impacto

| Categoria | Cantidad |
|-----------|----------|
| Archivos a ELIMINAR completamente | 6 |
| Archivos a MODIFICAR | 35+ |
| Tablas de BD a ELIMINAR | 5 |
| Capabilities a ELIMINAR | 2 |
| Roles a ELIMINAR | 1 |
| Estados de aplicacion a ELIMINAR | 5 |
| Plantillas de email a MODIFICAR | 7 |

---

## 2. ARCHIVOS A ELIMINAR COMPLETAMENTE

### 2.1 Archivos PHP

| Archivo | Lineas | Funcion |
|---------|--------|---------|
| `admin/manage_committee.php` | ~200 | Pagina de gestion de comites |
| `classes/committee.php` | ~700 | Clase principal del comite |
| `classes/output/renderer/committee_renderer.php` | ~300 | Renderer del comite |

### 2.2 Templates

| Archivo | Funcion |
|---------|---------|
| `templates/pages/review/committee.mustache` | Vista del comite |
| `templates/pages/review/interview_complete.mustache` | Completar entrevista |
| `templates/pages/review/schedule_interview.mustache` | Programar entrevista |

### 2.3 Nota sobre Entrevistas

Los archivos de entrevistas se marcan para eliminacion porque el nuevo flujo no incluye la fase de entrevista. Sin embargo, la tabla `local_jobboard_interview` se mantendra por referencias historicas.

---

## 3. TABLAS DE BASE DE DATOS A ELIMINAR

### 3.1 Tablas a Eliminar en upgrade.php

| Tabla | Razon |
|-------|-------|
| `local_jobboard_committee` | Comite de seleccion eliminado |
| `local_jobboard_committee_member` | Miembros del comite |
| `local_jobboard_evaluation` | Evaluaciones del comite |
| `local_jobboard_criteria` | Criterios de evaluacion |
| `local_jobboard_decision` | Decisiones del comite |

### 3.2 Script de Eliminacion

```php
// En db/upgrade.php - Nueva version
function xmldb_local_jobboard_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < NUEVA_VERSION) {
        // Eliminar tablas del comite en orden correcto (dependencias)
        $tables_to_drop = [
            'local_jobboard_decision',      // FK a committee, application
            'local_jobboard_evaluation',    // FK a committee, application
            'local_jobboard_criteria',      // FK a vacancy
            'local_jobboard_committee_member', // FK a committee
            'local_jobboard_committee',     // Tabla principal
        ];

        foreach ($tables_to_drop as $tablename) {
            $table = new xmldb_table($tablename);
            if ($dbman->table_exists($table)) {
                $dbman->drop_table($table);
            }
        }
    }
}
```

---

## 4. CAPABILITIES A ELIMINAR

### 4.1 En db/access.php

```php
// ELIMINAR estos bloques completos:

'local/jobboard:evaluate' => [
    'captype' => 'write',
    'contextlevel' => CONTEXT_SYSTEM,
    'archetypes' => [
        'teacher' => CAP_ALLOW,
        'editingteacher' => CAP_ALLOW,
        'manager' => CAP_ALLOW,
    ],
],

'local/jobboard:viewevaluations' => [
    'captype' => 'read',
    'contextlevel' => CONTEXT_SYSTEM,
    'archetypes' => [
        'editingteacher' => CAP_ALLOW,
        'manager' => CAP_ALLOW,
    ],
],
```

### 4.2 Script de Eliminacion de Capabilities

```php
// En db/upgrade.php
$capabilities_to_remove = [
    'local/jobboard:evaluate',
    'local/jobboard:viewevaluations',
];

foreach ($capabilities_to_remove as $capability) {
    $DB->delete_records('capabilities', ['name' => $capability]);
    $DB->delete_records('role_capabilities', ['capability' => $capability]);
}
```

---

## 5. ROLES A ELIMINAR

### 5.1 Rol jobboard_committee

Ubicacion: `db/install.php` funcion `local_jobboard_create_roles()`

```php
// ELIMINAR este bloque completo (lineas ~785-808):
// Role: Selection Committee Member.
$committeerole = $DB->get_record('role', ['shortname' => 'jobboard_committee']);
if (!$committeerole) {
    $committeeroleid = create_role(
        get_string('role_committee', 'local_jobboard'),
        'jobboard_committee',
        get_string('role_committee_desc', 'local_jobboard'),
        'teacher'
    );
    // ... capabilities ...
}
```

### 5.2 En admin/roles.php

```php
// Linea 59 - MODIFICAR:
// ANTES:
$pluginroles = ['jobboard_reviewer', 'jobboard_coordinator', 'jobboard_committee'];

// DESPUES:
$pluginroles = ['jobboard_reviewer', 'jobboard_coordinator', 'jobboard_dean', 'jobboard_hr'];
```

### 5.3 Script de Eliminacion de Rol

```php
// En db/upgrade.php
$role = $DB->get_record('role', ['shortname' => 'jobboard_committee']);
if ($role) {
    // Eliminar asignaciones del rol
    $DB->delete_records('role_assignments', ['roleid' => $role->id]);
    // Eliminar capabilities del rol
    $DB->delete_records('role_capabilities', ['roleid' => $role->id]);
    // Eliminar el rol
    delete_role($role->id);
}
```

---

## 6. ARCHIVOS A MODIFICAR

### 6.1 Clases Principales

| Archivo | Modificaciones |
|---------|----------------|
| `classes/application.php` | Cambiar STATUSES y TRANSITIONS |
| `classes/helper/status_helper.php` | Actualizar constantes de estado |
| `classes/document.php` | Restringir validacion a rol HR |
| `classes/audit.php` | Agregar campo de rol |
| `classes/notification.php` | Actualizar para nuevos estados |
| `classes/email_template.php` | Actualizar plantillas |
| `classes/reviewer.php` | Adaptar para nuevo flujo |
| `classes/interview.php` | Deprecar (mantener para historico) |

### 6.2 Renderers

| Archivo | Modificaciones |
|---------|----------------|
| `classes/output/renderer.php` | Remover referencias a committee |
| `classes/output/renderer_base.php` | Remover referencias a committee |
| `classes/output/renderer/admin_renderer.php` | Remover menu de committee |
| `classes/output/renderer/application_renderer.php` | Adaptar estados |
| `classes/output/renderer/dashboard_renderer.php` | Remover widget committee |
| `classes/output/renderer/reports_renderer.php` | Remover reportes committee |
| `classes/output/renderer/review_renderer.php` | Adaptar para nuevo flujo |
| `classes/output/renderer/vacancy_renderer.php` | Remover references evaluaciones |
| `classes/output/ui_helper.php` | Actualizar badges de estado |

### 6.3 Vistas

| Archivo | Modificaciones |
|---------|----------------|
| `views/application.php` | Adaptar transiciones de estado |
| `views/applications.php` | Filtros de nuevo estados |
| `views/review.php` | Control acceso por rol/fechas |
| `views/vacancy.php` | Remover referencias evaluaciones |
| `views/dashboard.php` | Remover widget committee |

### 6.4 Admin

| Archivo | Modificaciones |
|---------|----------------|
| `admin/roles.php` | Agregar dean/hr, quitar committee |
| `admin/schedule_interview.php` | Deprecar o eliminar |

### 6.5 Base de Datos

| Archivo | Modificaciones |
|---------|----------------|
| `db/access.php` | Agregar nuevas capabilities, eliminar evaluate |
| `db/install.php` | Agregar roles dean/hr, eliminar committee |
| `db/install.xml` | Agregar campos a convocatoria |
| `db/upgrade.php` | Scripts de migracion |
| `db/uninstall.php` | Limpiar nuevas tablas |

### 6.6 Tests

| Archivo | Modificaciones |
|---------|----------------|
| `tests/application_test.php` | Actualizar para nuevos estados |
| `tests/behat/application_submission.feature` | Actualizar escenarios |
| `tests/behat/document_validation.feature` | Actualizar escenarios |
| `tests/generator/lib.php` | Actualizar generador |

### 6.7 Otros

| Archivo | Modificaciones |
|---------|----------------|
| `lib.php` | Remover funciones committee |
| `cli/cli.php` | Remover comandos committee |
| `reupload_document.php` | Verificar permisos HR |
| `styles.css` | Limpiar estilos committee |

---

## 7. IDIOMAS

### 7.1 Cadenas a Eliminar

Prefijos a buscar y eliminar en `lang/*/local_jobboard.php`:

```
committee_*
evaluation_*
criteria_*
role_committee*
evaluate*
```

### 7.2 Cadenas a Agregar

```php
// Nuevos estados
$string['appstatus:pending_dean_review'] = 'Pendiente revision Decano';
$string['appstatus:dean_approved'] = 'Aprobado por Decano';
$string['appstatus:dean_rejected'] = 'Rechazado por Decano';
$string['appstatus:pending_hr_validation'] = 'Pendiente validacion TH';
$string['appstatus:hr_validated'] = 'Validado por Talento Humano';
$string['appstatus:hr_rejected'] = 'Rechazado por Talento Humano';

// Nuevos roles
$string['role_dean'] = 'Decano Revisor';
$string['role_dean_desc'] = 'Revisa perfiles de postulantes y aprueba/rechaza perfiles completos';
$string['role_hr'] = 'Talento Humano';
$string['role_hr_desc'] = 'Valida documentos de postulantes preseleccionados';

// Nuevas capabilities
$string['jobboard:reviewprofiles'] = 'Revisar perfiles de postulantes';
$string['jobboard:approveprofile'] = 'Aprobar/rechazar perfiles completos';
$string['jobboard:validatehr'] = 'Validacion final de documentos HR';

// Campos convocatoria
$string['dean_review_startdate'] = 'Inicio revision Decano';
$string['dean_review_enddate'] = 'Fin revision Decano';
$string['hr_review_startdate'] = 'Inicio revision Talento Humano';
$string['hr_review_enddate'] = 'Fin revision Talento Humano';
```

---

## 8. PRIVACY PROVIDER

### 8.1 En classes/privacy/provider.php

Eliminar referencias a tablas del comite:

```php
// ELIMINAR exports de:
// - local_jobboard_committee_member
// - local_jobboard_evaluation

// ELIMINAR delete_data para:
// - local_jobboard_committee_member
// - local_jobboard_evaluation
```

---

## 9. MIGRACION Y EXPORTACION

### 9.1 En classes/migration/exporter.php

Eliminar export de:
- Comites
- Miembros de comite
- Evaluaciones
- Criterios
- Decisiones

### 9.2 En classes/migration/importer.php

Eliminar import de:
- Comites
- Miembros de comite
- Evaluaciones
- Criterios
- Decisiones

---

## 10. ORDEN DE ELIMINACION

### Fase 1: Preparacion (sin romper nada)
1. Agregar nuevas capabilities en access.php
2. Agregar nuevos roles en install.php
3. Agregar nuevos campos a convocatoria
4. Agregar nuevas cadenas de idioma

### Fase 2: Modificacion de Flujo
1. Modificar STATUSES en application.php
2. Modificar TRANSITIONS en application.php
3. Actualizar status_helper.php
4. Actualizar vistas y renderers

### Fase 3: Eliminacion de Codigo
1. Eliminar archivos del comite
2. Eliminar templates del comite
3. Eliminar renderer del comite
4. Limpiar referencias en otros archivos

### Fase 4: Limpieza de Base de Datos
1. Script upgrade.php para eliminar tablas
2. Eliminar capabilities
3. Eliminar rol committee

### Fase 5: Limpieza Final
1. Eliminar cadenas de idioma obsoletas
2. Limpiar tests
3. Actualizar version.php

---

## 11. DEPENDENCIAS CRITICAS

### 11.1 Tablas con Foreign Keys a Eliminar

```
local_jobboard_decision -> local_jobboard_committee
local_jobboard_decision -> local_jobboard_application
local_jobboard_evaluation -> local_jobboard_committee
local_jobboard_evaluation -> local_jobboard_application
local_jobboard_criteria -> local_jobboard_vacancy
local_jobboard_committee_member -> local_jobboard_committee
local_jobboard_committee -> local_jobboard_faculty
```

### 11.2 Orden de Eliminacion de Tablas

1. `local_jobboard_decision` (primero - FK a otros)
2. `local_jobboard_evaluation` (FK a committee)
3. `local_jobboard_criteria` (FK a vacancy)
4. `local_jobboard_committee_member` (FK a committee)
5. `local_jobboard_committee` (tabla principal)

---

## 12. VERIFICACION POST-ELIMINACION

### 12.1 Comandos de Verificacion

```bash
# Buscar referencias restantes
grep -r "committee" --include="*.php" .
grep -r "evaluate" --include="*.php" .
grep -r "criteria" --include="*.php" .

# Verificar tablas eliminadas
SELECT table_name FROM information_schema.tables
WHERE table_name LIKE 'mdl_local_jobboard_committee%';

# Verificar capabilities eliminadas
SELECT * FROM mdl_capabilities WHERE name LIKE '%evaluate%';

# Verificar rol eliminado
SELECT * FROM mdl_role WHERE shortname = 'jobboard_committee';
```

### 12.2 Tests a Ejecutar

```bash
# PHPUnit
vendor/bin/phpunit local/jobboard/tests/

# Behat
vendor/bin/behat --config local/jobboard/tests/behat/behat.yml

# Validacion de codigo
php admin/tool/check_plugins.php
```

---

## 13. PROXIMOS PASOS

La **Fase 5** documentara:
- Plan detallado de modificaciones
- Nuevas clases a crear
- Nuevos templates a crear
- Orden de implementacion
- Documento final consolidado

---

*Documento generado: 2025-12-22*
*Fase 4 de 5 completada*

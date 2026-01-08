# Fase 1: Correcciones Aplicadas

## Fecha: 2026-01-08
## Versión Anterior: 2025122301 (release 4.0.1)
## Versión Nueva: 2026010803 (release 4.1.0)

---

## Resumen de Correcciones

| # | Corrección | Estado | Tipo | Temporalidad |
|---|------------|--------|------|--------------|
| 1 | Bug de repostulación después de retiro | COMPLETADO | Bug fix | ⚠️ TEMPORAL |
| 2 | Asignación de revisores por facultad | COMPLETADO | Nueva funcionalidad + BD | Permanente |

---

## ⚠️ AVISO IMPORTANTE: Funcionalidad Temporal

### Corrección 1 - Repostulación Después de Retiro

**Esta funcionalidad es TEMPORAL y será removida en una versión futura.**

La corrección que permite a los usuarios repostularse a una vacante después de haber retirado su postulación es una solución provisional. En una versión futura del plugin, esta funcionalidad será eliminada y las postulaciones retiradas serán consideradas como finales (sin posibilidad de repostulación).

### Archivos a Modificar Cuando se Elimine Esta Funcionalidad

Al remover esta funcionalidad temporal, se deben realizar los siguientes cambios:

1. **`classes/application.php`**:
   - `user_has_applied()`: Remover exclusión de estado `withdrawn`
   - `user_has_submitted_application()`: Remover exclusión de estado `withdrawn`
   - `get_withdrawn()`: Eliminar método completo
   - `reactivate()`: Eliminar método completo

2. **`views/apply.php`**:
   - Eliminar bloque completo de reactivación (líneas 68-90)

3. **Renderers** (revertir a verificación simple con `record_exists`):
   - `classes/output/renderer/vacancy_renderer.php`
   - `classes/output/renderer/public_renderer.php`
   - `classes/output/renderer/convocatoria_renderer.php`
   - `ajax/filter_public_vacancies.php`

4. **Cadenas de idioma** (opcional, pueden mantenerse para histórico):
   - `applicationreactivated`
   - `applicationreactivated_notice`
   - `error:cannotreactivate`

---

## Corrección 1: Bug de Repostulación Después de Retiro

> ⚠️ **FUNCIONALIDAD TEMPORAL** - Ver aviso importante arriba.

### Problema Reportado
Los usuarios que retiraban una postulación no podían volver a postularse a la misma convocatoria.

### Causa Raíz Identificada
1. El método `user_has_submitted_application()` en `classes/application.php` solo excluía borradores (`draft`) pero **NO excluía postulaciones retiradas (`withdrawn`)**.
2. El índice único `(vacancyid, userid)` en la tabla `local_jobboard_application` impedía crear un nuevo registro.

### Solución Implementada
1. Modificar la query en `user_has_applied()` para excluir también el estado `withdrawn`
2. Agregar método `get_withdrawn()` para obtener postulaciones retiradas
3. Agregar método `reactivate()` para convertir una postulación retirada en borrador
4. Modificar `views/apply.php` para detectar y reutilizar postulaciones retiradas automáticamente

### Archivos Modificados

#### `classes/application.php`
- **Líneas 235-245**: Modificada query para excluir `withdrawn`
- **Líneas 265-289**: Nuevo método `get_withdrawn()`
- **Líneas 291-340**: Nuevo método `reactivate()`

```php
// Antes (líneas 238-244):
if ($excludedrafts) {
    return $DB->record_exists_select(
        'local_jobboard_application',
        'vacancyid = :vacancyid AND userid = :userid AND status != :status',
        ['vacancyid' => $vacancyid, 'userid' => $userid, 'status' => 'draft']
    );
}

// Después:
if ($excludedrafts) {
    // Exclude both draft and withdrawn applications.
    // Users with withdrawn applications can reapply.
    return $DB->record_exists_select(
        'local_jobboard_application',
        'vacancyid = :vacancyid AND userid = :userid AND status NOT IN (:draft, :withdrawn)',
        ['vacancyid' => $vacancyid, 'userid' => $userid, 'draft' => 'draft', 'withdrawn' => 'withdrawn']
    );
}
```

#### `views/apply.php`
- **Líneas 68-80**: Agregada lógica para detectar y reactivar postulaciones retiradas

```php
// Check for withdrawn application that can be reactivated.
$withdrawnapplication = null;
if (!$draftapplication) {
    $withdrawnapplication = application::get_withdrawn($vacancyid, $USER->id);
    if ($withdrawnapplication) {
        // Reactivate the withdrawn application as a draft.
        $withdrawnapplication->reactivate();
        $draftapplication = $withdrawnapplication;
        \core\notification::info(get_string('applicationreactivated_notice', 'local_jobboard'));
    }
}
```

#### `lang/en/local_jobboard.php`
- **Líneas 467-469**: Nuevas cadenas de texto

```php
$string['applicationreactivated'] = 'Application reactivated for resubmission';
$string['applicationreactivated_notice'] = 'Your previous withdrawn application has been reactivated...';
$string['error:cannotreactivate'] = 'Only withdrawn applications can be reactivated';
```

#### `lang/es/local_jobboard.php`
- **Líneas 451-453**: Nuevas cadenas de texto en español

### Archivos Adicionales Corregidos (v2026010802)

Se detectó que el botón "Aplicar" no aparecía en la UI porque múltiples vistas
verificaban si el usuario tenía postulación sin excluir `withdrawn`:

#### `views/vacancy.php`
- **Líneas 79-81**: Corregida verificación de postulación existente

```php
// Antes:
$hasapplied = $DB->record_exists('local_jobboard_application', [...]);

// Después:
$hasapplied = \local_jobboard\application::user_has_submitted_application($vacancy->id, $USER->id);
```

#### `classes/output/renderer/vacancy_renderer.php`
- **Líneas 159-161**: Corregida verificación en listado de vacantes

#### `classes/output/renderer/public_renderer.php`
- **Líneas 730-735**: Corregida verificación en página pública

#### `classes/output/renderer/convocatoria_renderer.php`
- **Líneas 561-563**: Corregida verificación en vista de convocatoria

#### `ajax/filter_public_vacancies.php`
- **Líneas 148-153**: Corregida verificación en filtrado AJAX

### Comportamiento Después de la Corrección Completa
1. Usuario retira su postulación → estado cambia a `withdrawn`
2. Usuario ve el botón "Aplicar" disponible (UI corregida)
3. Usuario hace clic en "Aplicar"
4. Sistema detecta la postulación retirada
5. Sistema reactiva la postulación anterior como `draft`
6. Usuario puede editar documentos y volver a enviar
7. Usuario recibe notificación de que su postulación fue reactivada

---

## Corrección 2: Asignación de Revisores por Facultad

### Problema Reportado
Para convocatorias tipo DOCENTE, los revisores (decanos) deben asignarse por facultad, no por programa.

### Solución Implementada
1. Crear nueva tabla `local_jobboard_faculty_reviewer`
2. Crear clase `faculty_reviewer.php` para gestionar asignaciones
3. Agregar script de upgrade para crear la tabla en instalaciones existentes

### Archivos Modificados/Creados

#### `db/install.xml`
- **Líneas 606-629**: Nueva definición de tabla `local_jobboard_faculty_reviewer`

```xml
<TABLE NAME="local_jobboard_faculty_reviewer"
       COMMENT="Reviewers assigned to faculties - for DOCENTE type convocatorias">
  <FIELDS>
    <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="true"/>
    <FIELD NAME="facultyid" TYPE="int" LENGTH="10" NOTNULL="true"/>
    <FIELD NAME="userid" TYPE="int" LENGTH="10" NOTNULL="true"/>
    <FIELD NAME="role" TYPE="char" LENGTH="20" NOTNULL="true" DEFAULT="reviewer"/>
    <FIELD NAME="status" TYPE="char" LENGTH="20" NOTNULL="true" DEFAULT="active"/>
    <FIELD NAME="addedby" TYPE="int" LENGTH="10" NOTNULL="true"/>
    <FIELD NAME="timecreated" TYPE="int" LENGTH="10" NOTNULL="true"/>
    <FIELD NAME="timemodified" TYPE="int" LENGTH="10" NOTNULL="false"/>
  </FIELDS>
  <KEYS>
    <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
    <KEY NAME="facultyid_fk" TYPE="foreign" FIELDS="facultyid"
         REFTABLE="local_jobboard_faculty" REFFIELDS="id"/>
    <KEY NAME="userid_fk" TYPE="foreign" FIELDS="userid" REFTABLE="user" REFFIELDS="id"/>
    <KEY NAME="addedby_fk" TYPE="foreign" FIELDS="addedby" REFTABLE="user" REFFIELDS="id"/>
  </KEYS>
  <INDEXES>
    <INDEX NAME="faculty_user_unique" UNIQUE="true" FIELDS="facultyid, userid"/>
    <INDEX NAME="status_idx" UNIQUE="false" FIELDS="status"/>
    <INDEX NAME="role_idx" UNIQUE="false" FIELDS="role"/>
  </INDEXES>
</TABLE>
```

#### `db/upgrade.php`
- **Líneas 811-844**: Nuevo bloque de upgrade para crear la tabla

#### `classes/faculty_reviewer.php` (NUEVO)
Clase completa para gestionar revisores por facultad:
- Métodos de consulta: `get()`, `get_by_faculty()`, `get_by_user()`, `get_faculty_ids_for_user()`
- Métodos de verificación: `is_reviewer()`, `is_dean()`
- Métodos de gestión: `assign()`, `reactivate()`, `deactivate()`, `delete()`, `update_role()`
- Soporte para roles: `dean`, `lead_reviewer`, `reviewer`
- Soporte para estados: `active`, `inactive`
- Integración con sistema de auditoría

#### `lang/en/local_jobboard.php`
- **Líneas 1753-1765**: Nuevas cadenas para faculty reviewers

#### `lang/es/local_jobboard.php`
- **Líneas 1748-1760**: Nuevas cadenas en español

### Estructura de la Nueva Tabla

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT | ID único autoincremental |
| `facultyid` | INT | FK a `local_jobboard_faculty` |
| `userid` | INT | FK a `user` (revisor) |
| `role` | VARCHAR(20) | Rol: dean, lead_reviewer, reviewer |
| `status` | VARCHAR(20) | Estado: active, inactive |
| `addedby` | INT | FK a `user` (quien asignó) |
| `timecreated` | INT | Timestamp de creación |
| `timemodified` | INT | Timestamp de modificación |

### Uso de la Clase faculty_reviewer

```php
use local_jobboard\faculty_reviewer;

// Asignar un decano a una facultad
$assignment = faculty_reviewer::assign($facultyid, $userid, 'dean');

// Verificar si usuario es revisor de una facultad
$isreviewer = faculty_reviewer::is_reviewer($facultyid, $userid);

// Obtener facultades que puede revisar un usuario
$facultyids = faculty_reviewer::get_faculty_ids_for_user($userid);

// Obtener todos los revisores de una facultad
$reviewers = faculty_reviewer::get_by_faculty($facultyid);
```

---

## Cambios en version.php

```php
// Antes:
$plugin->version = 2025122301;
$plugin->release = '4.0.1';

// Después:
$plugin->version = 2026010800;
$plugin->release = '4.1.0';
```

---

## Pruebas Recomendadas

### Para Bug de Repostulación
1. Crear postulación y enviarla
2. Retirar la postulación (estado → withdrawn)
3. Intentar postular de nuevo a la misma vacante
4. **Resultado esperado**: Sistema reactiva la postulación anterior y permite editarla

### Para Revisores por Facultad
1. Acceder a la administración del plugin
2. Asignar un usuario como revisor de una facultad
3. Verificar que el usuario aparece en la lista de revisores
4. Verificar que el usuario puede ver postulaciones de esa facultad (en convocatorias DOCENTE)

---

## Notas de Migración

- La nueva tabla `local_jobboard_faculty_reviewer` se crea automáticamente durante el upgrade
- No hay pérdida de datos existentes
- Los revisores por programa existentes NO se migran automáticamente a facultades
- Se debe configurar manualmente los revisores por facultad después del upgrade

---

## Próximos Pasos (Pendiente Aprobación)

Después de aprobar esta Fase 1, se procederá con el análisis exhaustivo para los **cambios grandes**:

1. Replantear dashboard completo
2. Implementar tipos de convocatoria (DOCENTE, OPS)
3. Crear rol de Talento Humano
4. Eliminar Comité de Selección
5. Implementar sistema de auditoría detallada
6. Configurar fechas de revisión por rol

---

**Estado de Fase 1: COMPLETADA**

**¿Se aprueba proceder con el análisis para cambios grandes?**

# FASE 2: Capabilities y Roles Actuales

## 1. ROLES PERSONALIZADOS ACTUALES

El plugin define 3 roles personalizados en `db/install.php`:

### 1.1 `jobboard_reviewer` - Revisor de Documentos

**Descripcion:** Revisa y valida documentos de postulantes
**Arquetipo base:** `teacher`

| Capability | Descripcion |
|------------|-------------|
| `local/jobboard:view` | Ver el job board |
| `local/jobboard:viewinternal` | Ver vacantes internas |
| `local/jobboard:review` | Revisar postulaciones |
| `local/jobboard:validatedocuments` | Validar documentos |
| `local/jobboard:reviewdocuments` | Revisar documentos |
| `local/jobboard:downloadanydocument` | Descargar cualquier documento |

### 1.2 `jobboard_coordinator` - Coordinador de Seleccion

**Descripcion:** Gestiona vacantes y coordina el proceso de seleccion
**Arquetipo base:** `editingteacher`

| Capability | Descripcion |
|------------|-------------|
| `local/jobboard:view` | Ver el job board |
| `local/jobboard:viewinternal` | Ver vacantes internas |
| `local/jobboard:manage` | Gestionar job board |
| `local/jobboard:createvacancy` | Crear vacantes |
| `local/jobboard:editvacancy` | Editar vacantes |
| `local/jobboard:publishvacancy` | Publicar vacantes |
| `local/jobboard:viewallvacancies` | Ver todas las vacantes |
| `local/jobboard:viewallapplications` | Ver todas las postulaciones |
| `local/jobboard:changeapplicationstatus` | Cambiar estado de postulacion |
| `local/jobboard:assignreviewers` | Asignar revisores |
| `local/jobboard:viewreports` | Ver reportes |
| `local/jobboard:viewevaluations` | Ver evaluaciones |
| `local/jobboard:manageworkflow` | Gestionar workflow |

### 1.3 `jobboard_committee` - Miembro del Comite de Seleccion **[A ELIMINAR]**

**Descripcion:** Evalua candidatos en el proceso de seleccion
**Arquetipo base:** `teacher`

| Capability | Descripcion |
|------------|-------------|
| `local/jobboard:view` | Ver el job board |
| `local/jobboard:viewinternal` | Ver vacantes internas |
| `local/jobboard:evaluate` | Evaluar candidatos |
| `local/jobboard:viewevaluations` | Ver evaluaciones |
| `local/jobboard:downloadanydocument` | Descargar cualquier documento |

---

## 2. CAPABILITIES ACTUALES COMPLETAS

### 2.1 Visualizacion General

| Capability | Tipo | Arquetipos por defecto |
|------------|------|----------------------|
| `local/jobboard:view` | read | guest, user, student, teacher, editingteacher, manager |
| `local/jobboard:viewinternal` | read | user, student, teacher, editingteacher, manager |
| `local/jobboard:viewpublicvacancies` | read | guest, user, student, teacher, editingteacher, manager |
| `local/jobboard:viewinternalvacancies` | read | user, student, teacher, editingteacher, manager |

### 2.2 Gestion de Vacantes

| Capability | Tipo | Arquetipos por defecto |
|------------|------|----------------------|
| `local/jobboard:manage` | write | editingteacher, manager |
| `local/jobboard:createvacancy` | write | editingteacher, manager |
| `local/jobboard:editvacancy` | write | editingteacher, manager |
| `local/jobboard:deletevacancy` | write | manager (RISK_DATALOSS) |
| `local/jobboard:publishvacancy` | write | editingteacher, manager |
| `local/jobboard:viewallvacancies` | read | editingteacher, manager |

### 2.3 Convocatorias

| Capability | Tipo | Arquetipos por defecto |
|------------|------|----------------------|
| `local/jobboard:manageconvocatorias` | write | manager |

### 2.4 Postulaciones

| Capability | Tipo | Arquetipos por defecto |
|------------|------|----------------------|
| `local/jobboard:apply` | write | user, student, teacher, editingteacher |
| `local/jobboard:viewownapplications` | read | user, student, teacher, editingteacher |
| `local/jobboard:viewallapplications` | read | editingteacher, manager |
| `local/jobboard:changeapplicationstatus` | write | editingteacher, manager |
| `local/jobboard:unlimitedapplications` | write | manager |

### 2.5 Revision de Documentos

| Capability | Tipo | Arquetipos por defecto |
|------------|------|----------------------|
| `local/jobboard:review` | write | teacher, editingteacher, manager |
| `local/jobboard:validatedocuments` | write | teacher, editingteacher, manager |
| `local/jobboard:reviewdocuments` | write | teacher, editingteacher, manager |
| `local/jobboard:assignreviewers` | write | editingteacher, manager |
| `local/jobboard:downloadanydocument` | read | teacher, editingteacher, manager |

### 2.6 Comite de Seleccion **[A ELIMINAR]**

| Capability | Tipo | Arquetipos por defecto |
|------------|------|----------------------|
| `local/jobboard:evaluate` | write | teacher, editingteacher, manager |
| `local/jobboard:viewevaluations` | read | editingteacher, manager |

### 2.7 Workflow

| Capability | Tipo | Arquetipos por defecto |
|------------|------|----------------------|
| `local/jobboard:manageworkflow` | write | manager |

### 2.8 Reportes

| Capability | Tipo | Arquetipos por defecto |
|------------|------|----------------------|
| `local/jobboard:viewreports` | read | editingteacher, manager |
| `local/jobboard:exportreports` | read | manager |
| `local/jobboard:exportdata` | read | manager |

### 2.9 Configuracion

| Capability | Tipo | Arquetipos por defecto |
|------------|------|----------------------|
| `local/jobboard:configure` | write | manager (RISK_CONFIG) |
| `local/jobboard:managedoctypes` | write | manager |
| `local/jobboard:manageemailtemplates` | write | manager |
| `local/jobboard:manageexemptions` | write | manager |

---

## 3. PROPUESTA DE NUEVOS ROLES

Segun los requerimientos, se deben crear/modificar los siguientes roles:

### 3.1 `jobboard_dean` - Decano (NUEVO)

**Descripcion:** Revisa perfiles de postulantes y aprueba/rechaza perfiles completos
**Arquetipo base:** `teacher`

| Capability | Descripcion | Accion |
|------------|-------------|--------|
| `local/jobboard:view` | Ver job board | ASIGNAR |
| `local/jobboard:viewinternal` | Ver vacantes internas | ASIGNAR |
| `local/jobboard:viewallapplications` | Ver todas las postulaciones | ASIGNAR |
| `local/jobboard:downloadanydocument` | Descargar documentos | ASIGNAR |
| **`local/jobboard:reviewprofiles`** | **Revisar perfiles** | **CREAR NUEVA** |
| **`local/jobboard:approveprofile`** | **Aprobar/rechazar perfil** | **CREAR NUEVA** |

**Restricciones:**
- Solo puede acceder durante fechas configuradas (`dean_review_startdate` - `dean_review_enddate`)
- NO puede aprobar/rechazar documentos individuales
- Solo puede aprobar o rechazar el PERFIL completo

### 3.2 `jobboard_hr` - Talento Humano (NUEVO)

**Descripcion:** Valida documentos de postulantes preseleccionados por el Decano
**Arquetipo base:** `teacher`

| Capability | Descripcion | Accion |
|------------|-------------|--------|
| `local/jobboard:view` | Ver job board | ASIGNAR |
| `local/jobboard:viewinternal` | Ver vacantes internas | ASIGNAR |
| `local/jobboard:viewallapplications` | Ver postulaciones | ASIGNAR |
| `local/jobboard:downloadanydocument` | Descargar documentos | ASIGNAR |
| `local/jobboard:validatedocuments` | Validar documentos | ASIGNAR |
| `local/jobboard:reviewdocuments` | Revisar documentos | ASIGNAR |
| **`local/jobboard:validatehr`** | **Validacion final HR** | **CREAR NUEVA** |

**Restricciones:**
- Solo puede acceder durante fechas configuradas (`hr_review_startdate` - `hr_review_enddate`)
- Solo ve postulantes con perfil aprobado por Decano (estado: `dean_approved`)
- Su validacion es el estado FINAL del sistema

---

## 4. CAPABILITIES A CREAR

### 4.1 Nuevas Capabilities para Decano

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
```

### 4.2 Nuevas Capabilities para Talento Humano

```php
// Validacion final de documentos por HR
'local/jobboard:validatehr' => [
    'captype' => 'write',
    'contextlevel' => CONTEXT_SYSTEM,
    'archetypes' => [],
],
```

---

## 5. CAPABILITIES A ELIMINAR

Las siguientes capabilities estan relacionadas con el Comite de Seleccion y deben ser **eliminadas**:

| Capability | Razon |
|------------|-------|
| `local/jobboard:evaluate` | Comite de Seleccion eliminado |
| `local/jobboard:viewevaluations` | Comite de Seleccion eliminado |

---

## 6. ROLES A ELIMINAR

| Rol | Shortname | Razon |
|-----|-----------|-------|
| Miembro del Comite de Seleccion | `jobboard_committee` | Comite de Seleccion eliminado del flujo |

---

## 7. MAPEO DE ROLES NUEVO vs ACTUAL

| Rol Actual | Rol Nuevo | Cambios |
|------------|-----------|---------|
| `jobboard_reviewer` | `jobboard_reviewer` | Mantener (puede ser usado internamente) |
| `jobboard_coordinator` | `jobboard_coordinator` | Mantener |
| `jobboard_committee` | **ELIMINAR** | Reemplazado por flujo Decano/HR |
| - | `jobboard_dean` | **CREAR** - Rol de Decano |
| - | `jobboard_hr` | **CREAR** - Rol de Talento Humano |

---

## 8. MATRIZ DE PERMISOS POR VISTA

### Vista: Lista de Postulantes

| Accion | Postulante | Decano | Talento Humano | Coordinador | Manager |
|--------|------------|--------|----------------|-------------|---------|
| Ver lista | Solo propias | Todas (en fechas) | Solo aprobadas por Decano | Todas | Todas |
| Ver documentos | Propios | Todos (solo lectura) | Todos | Todos | Todos |
| Aprobar perfil | - | SI | - | - | SI |
| Rechazar perfil | - | SI | - | - | SI |
| Aprobar documento | - | NO | SI | - | SI |
| Rechazar documento | - | NO | SI | - | SI |

### Vista: Detalle de Postulacion

| Accion | Postulante | Decano | Talento Humano | Coordinador | Manager |
|--------|------------|--------|----------------|-------------|---------|
| Ver estado | SI (sin preseleccion) | SI | SI | SI | SI |
| Ver documentos | SI | SI | SI | SI | SI |
| Descargar documentos | Propios | SI | SI | SI | SI |
| Ver contador revisados | NO | SI | SI | SI | SI |
| Botones aprobar/rechazar doc | - | NO | SI | - | SI |
| Boton aprobar/rechazar perfil | - | SI | - | - | SI |

---

## 9. ARCHIVOS A MODIFICAR PARA CAPABILITIES

| Archivo | Modificacion |
|---------|--------------|
| `db/access.php` | Agregar nuevas capabilities, eliminar evaluate/viewevaluations |
| `db/install.php` | Agregar creacion de roles dean/hr, eliminar committee |
| `db/upgrade.php` | Script de migracion para nuevos roles |
| `admin/roles.php` | Actualizar lista de roles del plugin |
| `lang/es/local_jobboard.php` | Agregar cadenas para nuevos roles |
| `lang/en/local_jobboard.php` | Agregar cadenas para nuevos roles |

---

## 10. PROXIMOS PASOS

La **Fase 3** documentara:
- Flujo de trabajo actual completo
- Estados de postulacion actuales
- Transiciones de estado
- Puntos de decision en el flujo

---

*Documento generado: 2025-12-22*
*Fase 2 de 5 completada*

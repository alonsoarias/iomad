# Resultados Fase 7 - Roles y Permisos

## Fecha: 2025-12-06
## Version: 1.9.38-beta (2025120617)

## Resumen Ejecutivo

Se definieron y verificaron todos los roles y capabilities necesarios para el plugin local_jobboard. Se agregaron 12 nuevas capabilities, se actualizaron los archetypes de las existentes, y se crearon 3 roles personalizados que se instalan automaticamente.

## Capabilities Definidas

### Capabilities de Visualizacion

| Capability | Tipo | Contexto | Archetypes |
|------------|------|----------|------------|
| local/jobboard:view | read | CONTEXT_SYSTEM | guest, user, student, teacher, editingteacher, manager |
| local/jobboard:viewinternal | read | CONTEXT_SYSTEM | user, student, teacher, editingteacher, manager |
| local/jobboard:viewpublicvacancies | read | CONTEXT_SYSTEM | guest, user, student, teacher, editingteacher, manager |
| local/jobboard:viewinternalvacancies | read | CONTEXT_SYSTEM | user, student, teacher, editingteacher, manager |

### Capabilities de Gestion de Vacantes

| Capability | Tipo | Contexto | Archetypes |
|------------|------|----------|------------|
| local/jobboard:manage | write | CONTEXT_SYSTEM | editingteacher, manager |
| local/jobboard:createvacancy | write | CONTEXT_SYSTEM | editingteacher, manager |
| local/jobboard:editvacancy | write | CONTEXT_SYSTEM | editingteacher, manager |
| local/jobboard:deletevacancy | write | CONTEXT_SYSTEM | manager (RISK_DATALOSS) |
| local/jobboard:publishvacancy | write | CONTEXT_SYSTEM | editingteacher, manager |
| local/jobboard:viewallvacancies | read | CONTEXT_SYSTEM | editingteacher, manager |
| local/jobboard:manageconvocatorias | write | CONTEXT_SYSTEM | manager |

### Capabilities de Postulacion

| Capability | Tipo | Contexto | Archetypes |
|------------|------|----------|------------|
| local/jobboard:apply | write | CONTEXT_SYSTEM | user, student, teacher, editingteacher |
| local/jobboard:viewownapplications | read | CONTEXT_SYSTEM | user, student, teacher, editingteacher |
| local/jobboard:viewallapplications | read | CONTEXT_SYSTEM | editingteacher, manager |
| local/jobboard:changeapplicationstatus | write | CONTEXT_SYSTEM | editingteacher, manager |
| local/jobboard:unlimitedapplications | write | CONTEXT_SYSTEM | manager |

### Capabilities de Revision de Documentos

| Capability | Tipo | Contexto | Archetypes |
|------------|------|----------|------------|
| local/jobboard:review | write | CONTEXT_SYSTEM | teacher, editingteacher, manager |
| local/jobboard:validatedocuments | write | CONTEXT_SYSTEM | teacher, editingteacher, manager |
| local/jobboard:reviewdocuments | write | CONTEXT_SYSTEM | teacher, editingteacher, manager |
| local/jobboard:assignreviewers | write | CONTEXT_SYSTEM | editingteacher, manager |
| local/jobboard:downloadanydocument | read | CONTEXT_SYSTEM | teacher, editingteacher, manager |

### Capabilities de Evaluacion

| Capability | Tipo | Contexto | Archetypes |
|------------|------|----------|------------|
| local/jobboard:evaluate | write | CONTEXT_SYSTEM | teacher, editingteacher, manager |
| local/jobboard:viewevaluations | read | CONTEXT_SYSTEM | editingteacher, manager |

### Capabilities de Reportes

| Capability | Tipo | Contexto | Archetypes |
|------------|------|----------|------------|
| local/jobboard:viewreports | read | CONTEXT_SYSTEM | editingteacher, manager |
| local/jobboard:exportreports | read | CONTEXT_SYSTEM | manager |
| local/jobboard:exportdata | read | CONTEXT_SYSTEM | manager |

### Capabilities de Administracion

| Capability | Tipo | Contexto | Archetypes |
|------------|------|----------|------------|
| local/jobboard:configure | write | CONTEXT_SYSTEM | manager (RISK_CONFIG) |
| local/jobboard:managedoctypes | write | CONTEXT_SYSTEM | manager |
| local/jobboard:manageemailtemplates | write | CONTEXT_SYSTEM | manager |
| local/jobboard:manageexemptions | write | CONTEXT_SYSTEM | manager |
| local/jobboard:manageworkflow | write | CONTEXT_SYSTEM | manager |
| local/jobboard:useapi | read | CONTEXT_SYSTEM | manager |
| local/jobboard:accessapi | read | CONTEXT_SYSTEM | manager |
| local/jobboard:manageapitokens | write | CONTEXT_SYSTEM | manager |

**Total Capabilities:** 34

## Roles Personalizados Creados

### 1. Revisor de Documentos (jobboard_reviewer)

| Campo | Valor |
|-------|-------|
| Shortname | jobboard_reviewer |
| Archetype | teacher |
| Descripcion | Puede revisar y validar documentos enviados por los postulantes |

**Capabilities asignadas:**
- local/jobboard:view
- local/jobboard:viewinternal
- local/jobboard:review
- local/jobboard:validatedocuments
- local/jobboard:reviewdocuments
- local/jobboard:downloadanydocument

### 2. Coordinador de Seleccion (jobboard_coordinator)

| Campo | Valor |
|-------|-------|
| Shortname | jobboard_coordinator |
| Archetype | editingteacher |
| Descripcion | Puede gestionar vacantes, convocatorias y coordinar el proceso de seleccion |

**Capabilities asignadas:**
- local/jobboard:view
- local/jobboard:viewinternal
- local/jobboard:manage
- local/jobboard:createvacancy
- local/jobboard:editvacancy
- local/jobboard:publishvacancy
- local/jobboard:viewallvacancies
- local/jobboard:viewallapplications
- local/jobboard:changeapplicationstatus
- local/jobboard:assignreviewers
- local/jobboard:viewreports
- local/jobboard:viewevaluations

### 3. Comite de Seleccion (jobboard_committee)

| Campo | Valor |
|-------|-------|
| Shortname | jobboard_committee |
| Archetype | teacher |
| Descripcion | Puede evaluar candidatos y participar en las decisiones finales de seleccion |

**Capabilities asignadas:**
- local/jobboard:view
- local/jobboard:viewinternal
- local/jobboard:evaluate
- local/jobboard:viewevaluations
- local/jobboard:downloadanydocument

## Matriz de Permisos por Rol

| Capability | Postulante | Revisor | Coordinador | Comite | Admin |
|------------|:----------:|:-------:|:-----------:|:------:|:-----:|
| view | X | X | X | X | X |
| viewinternal | X | X | X | X | X |
| apply | X | - | - | - | X |
| viewownapplications | X | - | - | - | X |
| review | - | X | X | - | X |
| validatedocuments | - | X | X | - | X |
| reviewdocuments | - | X | X | - | X |
| downloadanydocument | - | X | - | X | X |
| manage | - | - | X | - | X |
| createvacancy | - | - | X | - | X |
| editvacancy | - | - | X | - | X |
| deletevacancy | - | - | - | - | X |
| publishvacancy | - | - | X | - | X |
| viewallvacancies | - | - | X | - | X |
| viewallapplications | - | - | X | - | X |
| changeapplicationstatus | - | - | X | - | X |
| assignreviewers | - | - | X | - | X |
| evaluate | - | - | - | X | X |
| viewevaluations | - | - | X | X | X |
| viewreports | - | - | X | - | X |
| exportreports | - | - | - | - | X |
| manageconvocatorias | - | - | - | - | X |
| configure | - | - | - | - | X |

## Verificaciones Ejecutadas

| Verificacion | Resultado |
|--------------|-----------|
| PHP lint access.php | OK |
| PHP lint install.php | OK |
| PHP lint lang/en/local_jobboard.php | OK |
| PHP lint lang/es/local_jobboard.php | OK |
| Capabilities en access.php | 34 |
| Capability strings EN | 38 |
| Capability strings ES | 35 |
| Role strings EN | 6 |
| Role strings ES | 6 |
| Role creation functions | 3 |

## Archivos Modificados

```
local/jobboard/
├── db/
│   ├── access.php           (+12 capabilities, archetypes actualizados)
│   └── install.php          (+funcion local_jobboard_create_roles)
├── lang/
│   ├── en/local_jobboard.php (+22 strings)
│   └── es/local_jobboard.php (+22 strings, -3 duplicados)
└── version.php              (1.9.38-beta)
```

## Capabilities Agregadas en Esta Fase

### Nuevas capabilities:
1. local/jobboard:view
2. local/jobboard:viewinternal
3. local/jobboard:manage
4. local/jobboard:manageconvocatorias
5. local/jobboard:changeapplicationstatus
6. local/jobboard:review
7. local/jobboard:validatedocuments
8. local/jobboard:assignreviewers
9. local/jobboard:evaluate
10. local/jobboard:viewevaluations
11. local/jobboard:exportreports
12. local/jobboard:useapi

## Strings Agregadas

### Ingles (EN)
- 15 capability strings nuevas
- 6 role strings (3 nombres + 3 descripciones)
- 1 capability string actualizada

### Espanol (ES)
- 15 capability strings nuevas
- 6 role strings (3 nombres + 3 descripciones)
- 3 strings duplicadas eliminadas

## Flujo de Instalacion de Roles

1. Durante instalacion del plugin, se llama `xmldb_local_jobboard_install()`
2. Esta funcion llama a `local_jobboard_create_roles()`
3. Se verifican si los roles ya existen (por shortname)
4. Si no existen, se crean con `create_role()`
5. Se asignan las capabilities correspondientes
6. Se establecen los contextos donde se pueden asignar (CONTEXT_SYSTEM)

## Uso de Roles

### Asignar rol de Revisor
```
Administracion > Usuarios > Permisos > Asignar roles del sistema
Seleccionar: Job Board Document Reviewer
```

### Asignar rol de Coordinador
```
Administracion > Usuarios > Permisos > Asignar roles del sistema
Seleccionar: Job Board Coordinator
```

### Asignar rol de Comite
```
Administracion > Usuarios > Permisos > Asignar roles del sistema
Seleccionar: Job Board Selection Committee
```

---

*Fase completada: 2025-12-06*

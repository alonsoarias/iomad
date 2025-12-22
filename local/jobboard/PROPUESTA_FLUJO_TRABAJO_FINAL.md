# PROPUESTA DE REESTRUCTURACION DEL FLUJO DE TRABAJO
## Plugin local_jobboard - Documento para Aprobacion

---

## RESUMEN EJECUTIVO

### Objetivo
Reestructurar el flujo de trabajo del plugin jobboard eliminando el Comite de Seleccion e implementando un flujo simplificado con roles de Decano y Talento Humano.

### Cambios Principales
1. **Eliminar** el Comite de Seleccion (tablas, codigo, rol, capabilities)
2. **Crear** roles nuevos: `jobboard_dean` y `jobboard_hr`
3. **Implementar** control de acceso por fechas configurables
4. **Simplificar** estados de postulacion
5. **Mejorar** sistema de auditoria con registro de rol

---

## 1. ROLES DEL NUEVO FLUJO

### 1.1 DECANO (`jobboard_dean`)

| Aspecto | Detalle |
|---------|---------|
| **Funcion** | Revisar perfiles de postulantes |
| **Acciones** | Aprobar o rechazar PERFIL completo |
| **Restricciones** | NO puede aprobar/rechazar documentos individuales |
| **Acceso** | Solo durante fechas configuradas en convocatoria |
| **Capabilities** | `reviewprofiles`, `approveprofile` |

### 1.2 TALENTO HUMANO (`jobboard_hr`)

| Aspecto | Detalle |
|---------|---------|
| **Funcion** | Validar documentos de postulantes |
| **Acciones** | Aprobar/rechazar documentos, validacion final |
| **Restricciones** | Solo ve postulantes aprobados por Decano |
| **Acceso** | Solo durante fechas configuradas en convocatoria |
| **Capabilities** | `validatedocuments`, `reviewdocuments`, `validatehr` |

### 1.3 POSTULANTE

| Aspecto | Detalle |
|---------|---------|
| **Funcion** | Aplicar a vacantes |
| **Restricciones** | NO ve estado de preseleccion |
| **Restricciones** | NO ve contador de documentos revisados |
| **Notificaciones** | Recibe solo resultado final |

---

## 2. NUEVO FLUJO DE TRABAJO

```
CONVOCATORIA ABIERTA
        |
        v
   +---------+
   |  DRAFT  | (postulante guarda borrador)
   +----+----+
        |
        v
 +-----------+
 | SUBMITTED | (postulante envia)
 +-----+-----+
       |
       v (convocatoria cierra)
+--------------------+
| PENDING_DEAN_REVIEW| <-- Decano revisa en fechas configuradas
+---------+----------+
          |
    +-----+-----+
    |           |
    v           v
+-------------+ +---------------+
|DEAN_APPROVED| | DEAN_REJECTED |
+------+------+ +---------------+
       |               |
       v               v
+----------------------+ (ESTADO FINAL)
|PENDING_HR_VALIDATION | <-- HR valida en fechas configuradas
+----------+-----------+
           |
     +-----+-----+
     |           |
     v           v
+--------------+ +-------------+
| HR_VALIDATED | | HR_REJECTED |
+--------------+ +-------------+
       |               |
       v               v
 (ESTADO FINAL)  (ESTADO FINAL)
```

---

## 3. CONFIGURACION EN CONVOCATORIA

### Nuevos Campos

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| `dean_review_startdate` | timestamp | Inicio periodo revision Decano |
| `dean_review_enddate` | timestamp | Fin periodo revision Decano |
| `hr_review_startdate` | timestamp | Inicio periodo validacion HR |
| `hr_review_enddate` | timestamp | Fin periodo validacion HR |

### Ejemplo de Configuracion

```
Convocatoria: CONV-2025-001
- Fecha apertura: 01/01/2025
- Fecha cierre: 31/01/2025
- Revision Decano: 01/02/2025 - 15/02/2025
- Validacion HR: 16/02/2025 - 28/02/2025
```

---

## 4. MATRIZ DE PERMISOS

### Por Vista

| Accion | Postulante | Decano | HR | Manager |
|--------|------------|--------|----|---------|
| Ver lista postulaciones | Solo propias | Todas (en fechas) | Solo aprobadas | Todas |
| Ver documentos | Propios | Todos (lectura) | Todos | Todos |
| Descargar documentos | Propios | Si | Si | Si |
| Aprobar/Rechazar perfil | - | **SI** | - | Si |
| Aprobar/Rechazar documento | - | **NO** | **SI** | Si |
| Ver estado preseleccion | **NO** | Si | Si | Si |
| Ver contador revisados | **NO** | Si | Si | Si |

---

## 5. ELEMENTOS A ELIMINAR

### 5.1 Tablas de Base de Datos

| Tabla | Registros Estimados |
|-------|---------------------|
| `local_jobboard_committee` | Pocos |
| `local_jobboard_committee_member` | Pocos |
| `local_jobboard_evaluation` | Varios |
| `local_jobboard_criteria` | Varios |
| `local_jobboard_decision` | Pocos |

### 5.2 Archivos

- `admin/manage_committee.php`
- `classes/committee.php`
- `classes/output/renderer/committee_renderer.php`
- `templates/pages/review/committee.mustache`
- Templates de entrevista

### 5.3 Rol y Capabilities

- Rol: `jobboard_committee`
- Capability: `local/jobboard:evaluate`
- Capability: `local/jobboard:viewevaluations`

---

## 6. SISTEMA DE AUDITORIA MEJORADO

### Campos Adicionales

| Campo | Descripcion |
|-------|-------------|
| `userrole` | Rol del usuario que realiza la accion |

### Ejemplo de Registro

```json
{
    "action": "transition",
    "entitytype": "application",
    "entityid": 123,
    "userid": 456,
    "userrole": "jobboard_dean",
    "ipaddress": "192.168.1.100",
    "extradata": {
        "transition": "pending_dean_review -> dean_approved",
        "comments": "Perfil cumple requisitos"
    },
    "previousvalue": {"status": "pending_dean_review"},
    "newvalue": {"status": "dean_approved"},
    "timecreated": 1735900000
}
```

---

## 7. NOTIFICACIONES

### Nuevas Plantillas de Email

| Codigo | Evento | Destinatario |
|--------|--------|--------------|
| `dean_approved` | Perfil aprobado por Decano | Postulante |
| `dean_rejected` | Perfil rechazado por Decano | Postulante |
| `hr_validated` | Validacion final exitosa | Postulante |
| `hr_rejected` | Rechazo final por documentos | Postulante |

### Restricciones de Notificacion al Postulante

El postulante **NO recibe**:
- Estado de preseleccion durante revision
- Contador de documentos revisados
- Resultados parciales de revision

El postulante **SI recibe**:
- Confirmacion de postulacion enviada
- Resultado final (aprobado/rechazado)

---

## 8. IMPACTO EN CODIGO

### Archivos a Modificar (35+)

| Categoria | Cantidad |
|-----------|----------|
| Clases principales | 8 |
| Renderers | 9 |
| Vistas | 5 |
| Base de datos | 4 |
| Tests | 4+ |
| Otros | 5+ |

### Archivos a Eliminar

| Categoria | Cantidad |
|-----------|----------|
| PHP | 3 |
| Templates | 3 |
| **Total** | 6 |

---

## 9. PLAN DE IMPLEMENTACION

### Sprint 1: Preparacion (5 dias)
- Agregar campos a BD
- Agregar capabilities y roles
- Agregar cadenas de idioma

### Sprint 2: Nuevo Flujo (5 dias)
- Modificar estados y transiciones
- Crear helper de acceso por rol
- Modificar auditoria

### Sprint 3: Vistas (5 dias)
- Modificar vistas de revision
- Actualizar templates
- Modificar renderers

### Sprint 4: Eliminacion (3 dias)
- Eliminar codigo del comite
- Eliminar tablas
- Eliminar rol y capabilities

### Sprint 5: Testing (3 dias)
- Tests unitarios
- Tests de integracion
- Validacion con usuarios

---

## 10. RIESGOS IDENTIFICADOS

| Riesgo | Probabilidad | Impacto | Mitigacion |
|--------|--------------|---------|------------|
| Datos historicos perdidos | Baja | Alto | Backup antes de eliminar |
| Usuarios con rol eliminado | Media | Bajo | Script de migracion |
| Postulaciones en limbo | Media | Alto | Migracion de estados |
| Errores en produccion | Baja | Alto | Testing exhaustivo |

---

## 11. DOCUMENTOS DE REFERENCIA

| Fase | Documento | Contenido |
|------|-----------|-----------|
| 1 | `analisis_fase1_estructura.md` | Estructura de archivos y BD |
| 2 | `analisis_fase2_roles.md` | Capabilities y roles actuales |
| 3 | `analisis_fase3_flujo_actual.md` | Flujo de trabajo actual |
| 4 | `analisis_fase4_eliminacion.md` | Codigo a eliminar |
| 5 | `analisis_fase5_plan.md` | Plan de modificaciones |

---

## 12. APROBACION

### Condiciones para Implementar

- [ ] Aprobacion del flujo propuesto
- [ ] Aprobacion de eliminacion del Comite
- [ ] Confirmacion de fechas de implementacion
- [ ] Backup de datos existentes

### Firma de Aprobacion

| Rol | Nombre | Fecha | Firma |
|-----|--------|-------|-------|
| Product Owner | | | |
| Tech Lead | | | |
| QA Lead | | | |

---

## 13. PREGUNTAS PENDIENTES

1. **Datos historicos del Comite**: Se archivan o se eliminan permanentemente?
2. **Postulaciones existentes**: Como migrar las que estan en estados antiguos?
3. **Usuarios con rol committee**: Se reasignan a otro rol o se desactivan?
4. **Entrevistas**: Se mantiene la funcionalidad o se elimina completamente?

---

*Documento generado: 2025-12-22*
*Version: 1.0*
*Estado: PENDIENTE APROBACION*

---

## INSTRUCCIONES PARA EL REVISOR

1. Revisar cada seccion del documento
2. Marcar acuerdos/desacuerdos en cada punto
3. Responder preguntas pendientes (seccion 13)
4. Firmar aprobacion (seccion 12)
5. Notificar decision para proceder con implementacion

**IMPORTANTE**: No se realizara ninguna modificacion de codigo hasta recibir aprobacion explicita de este documento.

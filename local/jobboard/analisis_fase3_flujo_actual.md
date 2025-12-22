# FASE 3: Flujo de Trabajo Actual

## 1. ESTADOS DE POSTULACION (APPLICATION)

### 1.1 Estados Definidos Actualmente

Ubicacion: `classes/application.php` lineas 99-109

```php
public const STATUSES = [
    'draft',           // Borrador (guardado parcial)
    'submitted',       // Enviada (pendiente revision)
    'under_review',    // En revision de documentos
    'docs_validated',  // Documentos validados
    'docs_rejected',   // Documentos rechazados
    'interview',       // En entrevista
    'selected',        // Seleccionado (estado final)
    'rejected',        // Rechazado (estado final)
    'withdrawn',       // Retirada por postulante (estado final)
];
```

### 1.2 Transiciones Permitidas Actualmente

Ubicacion: `classes/application.php` lineas 112-119

```php
public const TRANSITIONS = [
    'draft' => ['submitted'],
    'submitted' => ['under_review', 'rejected'],
    'under_review' => ['docs_validated', 'docs_rejected'],
    'docs_rejected' => ['under_review'],
    'docs_validated' => ['interview', 'rejected'],
    'interview' => ['selected', 'rejected'],
];
```

### 1.3 Diagrama de Flujo Actual

```
                    +-----------+
                    |   DRAFT   |
                    +-----+-----+
                          |
                          v
                    +-----------+
                    | SUBMITTED |
                    +-----+-----+
                          |
            +-------------+-------------+
            |                           |
            v                           v
    +---------------+             +-----------+
    | UNDER_REVIEW  |             | REJECTED  |
    +-------+-------+             +-----------+
            |
    +-------+-------+
    |               |
    v               v
+---------------+  +---------------+
|DOCS_VALIDATED |  | DOCS_REJECTED |
+-------+-------+  +-------+-------+
        |                  |
        |                  +-----> (regresa a UNDER_REVIEW)
        |
+-------+-------+
|               |
v               v
+-----------+  +-----------+
| INTERVIEW |  | REJECTED  |
+-----+-----+  +-----------+
      |
+-----+-----+
|           |
v           v
+----------+ +----------+
| SELECTED | | REJECTED |
+----------+ +----------+
```

---

## 2. ESTADOS DE DOCUMENTOS

### 2.1 Estados de Validacion

Ubicacion: `db/install.xml` tabla `local_jobboard_doc_validation`

| Estado | Descripcion |
|--------|-------------|
| `pending` | Pendiente de revision |
| `approved` | Documento aprobado |
| `rejected` | Documento rechazado |

### 2.2 Flujo de Validacion de Documentos

```
+----------+
| PENDING  |
+----+-----+
     |
+----+----+
|         |
v         v
+--------+ +--------+
|APPROVED| |REJECTED|
+--------+ +--------+
              |
              v (resubir documento)
          +----------+
          | PENDING  | (nuevo documento)
          +----------+
```

**Metodos de validacion:**
- `document::validate()` - Aprobar documento
- `document::reject($reason)` - Rechazar con razon

---

## 3. ESTADOS DE VACANTES

### 3.1 Estados Definidos

Ubicacion: `classes/vacancy.php` linea 108

```php
public const STATUSES = ['draft', 'published', 'closed', 'assigned'];
```

### 3.2 Transiciones de Vacantes

```
+-------+
| DRAFT |
+---+---+
    |
    v (publicar)
+-----------+
| PUBLISHED |<-----+
+-----+-----+      |
      |            |
      v (cerrar)   | (reabrir)
+--------+         |
| CLOSED +---------+
+----+---+
     |
     v (asignar seleccionados)
+----------+
| ASSIGNED | (estado final)
+----------+
```

---

## 4. ESTADOS DE CONVOCATORIAS

### 4.1 Estados Definidos

Ubicacion: `db/install.xml` tabla `local_jobboard_convocatoria`

| Estado | Descripcion |
|--------|-------------|
| `draft` | Borrador, no visible |
| `open` | Abierta para postulaciones |
| `closed` | Cerrada, no acepta postulaciones |
| `archived` | Archivada (historico) |

### 4.2 Campos Actuales de la Convocatoria

```
- id
- code (unico)
- name
- description
- brief_description
- pdf_contenthash (documento PDF)
- pdf_filename
- startdate (inicio postulaciones)
- enddate (fin postulaciones)
- status
- companyid (IOMAD)
- departmentid (IOMAD)
- publicationtype (public/internal)
- terms
- allow_multiple_applications
- max_applications_per_user
- createdby, modifiedby
- timecreated, timemodified
```

---

## 5. FLUJO DEL COMITE DE SELECCION (A ELIMINAR)

### 5.1 Tablas Involucradas

| Tabla | Funcion |
|-------|---------|
| `local_jobboard_committee` | Comites por facultad |
| `local_jobboard_committee_member` | Miembros del comite |
| `local_jobboard_evaluation` | Evaluaciones por miembro |
| `local_jobboard_criteria` | Criterios de evaluacion |
| `local_jobboard_decision` | Decisiones finales |

### 5.2 Flujo Actual del Comite

```
1. Se crea comite para una facultad
2. Se asignan miembros con roles (chair, evaluator)
3. Cada miembro evalua postulaciones con:
   - Puntuacion (score)
   - Voto (approve/reject)
   - Comentarios
   - Ratings por criterio
4. El chair puede tomar decision final
5. La decision actualiza el estado de la postulacion
```

### 5.3 Clases Relacionadas

- `classes/committee.php` - Gestion del comite completo
- Metodos principales:
  - `create()` - Crear comite
  - `add_member()` - Agregar miembro
  - `submit_evaluation()` - Registrar evaluacion
  - `make_decision()` - Tomar decision final

---

## 6. FLUJO DE NOTIFICACIONES

### 6.1 Plantillas de Email Actuales

| Codigo | Evento |
|--------|--------|
| `application_submitted` | Postulacion recibida |
| `under_review` | Postulacion en revision |
| `docs_rejected` | Documentos rechazados |
| `docs_validated` | Documentos validados |
| `interview` | Citacion a entrevista |
| `selected` | Seleccionado |
| `rejected` | No seleccionado |

### 6.2 Flujo de Notificacion

```
1. Cambio de estado en postulacion
2. Se busca plantilla para el nuevo estado
3. Se reemplazan placeholders:
   - {USER_FULLNAME}
   - {VACANCY_CODE}
   - {VACANCY_TITLE}
   - {APPLICATION_DATE}
   - {CURRENT_STATUS}
   - {APPLICATION_URL}
4. Se encola notificacion
5. Tarea cron envia emails
```

---

## 7. SISTEMA DE AUDITORIA ACTUAL

### 7.1 Tabla de Auditoria

Ubicacion: `db/install.xml` tabla `local_jobboard_audit`

| Campo | Descripcion |
|-------|-------------|
| `userid` | Usuario que realiza accion |
| `action` | Tipo de accion |
| `entitytype` | Tipo de entidad |
| `entityid` | ID de entidad |
| `ipaddress` | IP del usuario |
| `useragent` | Navegador |
| `extradata` | Datos adicionales (JSON) |
| `previousvalue` | Valor anterior (JSON) |
| `newvalue` | Valor nuevo (JSON) |
| `timecreated` | Timestamp |

### 7.2 Tipos de Accion

```php
const ACTION_CREATE = 'create';
const ACTION_UPDATE = 'update';
const ACTION_DELETE = 'delete';
const ACTION_TRANSITION = 'transition';
```

### 7.3 Limitaciones Actuales del Sistema de Auditoria

- No registra el rol del usuario que realiza la accion
- No tiene campo dedicado para rol
- extradata no siempre incluye informacion completa
- No hay trazabilidad clara de quien (rol) hizo que

---

## 8. FLUJO DE TRABAJO PROPUESTO (NUEVO)

### 8.1 Nuevos Estados a Agregar

| Estado | Descripcion |
|--------|-------------|
| `pending_dean_review` | Pendiente revision Decano |
| `dean_approved` | Perfil aprobado por Decano |
| `dean_rejected` | Perfil rechazado por Decano |
| `pending_hr_validation` | Pendiente validacion TH |
| `hr_validated` | Validado por Talento Humano (FINAL) |
| `hr_rejected` | Rechazado por Talento Humano (FINAL) |

### 8.2 Nuevo Flujo Propuesto

```
                    +-----------+
                    |   DRAFT   |
                    +-----+-----+
                          |
                          v
                    +-----------+
                    | SUBMITTED |
                    +-----+-----+
                          |
                          v (convocatoria cierra)
              +---------------------+
              | PENDING_DEAN_REVIEW |
              +----------+----------+
                         |
            +------------+------------+
            |                         |
            v                         v
    +---------------+         +---------------+
    | DEAN_APPROVED |         | DEAN_REJECTED |
    +-------+-------+         +---------------+
            |                         |
            v                         v
+------------------------+      (estado final)
| PENDING_HR_VALIDATION  |
+-----------+------------+
            |
    +-------+-------+
    |               |
    v               v
+--------------+  +-------------+
| HR_VALIDATED |  | HR_REJECTED |
+--------------+  +-------------+
(estado final)    (estado final)
```

### 8.3 Nuevas Transiciones Propuestas

```php
public const TRANSITIONS = [
    'draft' => ['submitted'],
    'submitted' => ['pending_dean_review'], // cuando cierra convocatoria
    'pending_dean_review' => ['dean_approved', 'dean_rejected'],
    'dean_approved' => ['pending_hr_validation'],
    'dean_rejected' => [], // final
    'pending_hr_validation' => ['hr_validated', 'hr_rejected'],
    'hr_validated' => [], // final
    'hr_rejected' => [], // final
];
```

---

## 9. CAMPOS A AGREGAR EN CONVOCATORIA

Para controlar acceso por fechas de cada rol:

```xml
<FIELD NAME="dean_review_startdate" TYPE="int" LENGTH="10"/>
<FIELD NAME="dean_review_enddate" TYPE="int" LENGTH="10"/>
<FIELD NAME="hr_review_startdate" TYPE="int" LENGTH="10"/>
<FIELD NAME="hr_review_enddate" TYPE="int" LENGTH="10"/>
```

### 9.1 Logica de Acceso por Fechas

```php
// Decano puede acceder si:
$now = time();
$can_dean_access = ($now >= $conv->dean_review_startdate
                 && $now <= $conv->dean_review_enddate);

// Talento Humano puede acceder si:
$can_hr_access = ($now >= $conv->hr_review_startdate
               && $now <= $conv->hr_review_enddate);
```

---

## 10. COMPARATIVA: FLUJO ACTUAL vs PROPUESTO

| Aspecto | Actual | Propuesto |
|---------|--------|-----------|
| Revision documentos | Revisor generico | Talento Humano (exclusivo) |
| Aprobacion perfil | Comite de Seleccion | Decano |
| Validacion final | Comite + Entrevista | Talento Humano |
| Control de fechas | No | Si, por rol en convocatoria |
| Estados finales | selected/rejected | hr_validated/hr_rejected/dean_rejected |
| Complejidad | Alta (comite, criterios, votacion) | Baja (flujo lineal) |

---

## 11. ELEMENTOS A ELIMINAR DEL FLUJO

### 11.1 Estados a Eliminar

- `under_review` (reemplazado por `pending_dean_review`)
- `docs_validated` (reemplazado por `dean_approved`)
- `docs_rejected` (reemplazado por estados de rechazo por rol)
- `interview` (eliminado del flujo)
- `selected` (reemplazado por `hr_validated`)

### 11.2 Funcionalidad a Eliminar

- Sistema de comites
- Sistema de evaluaciones
- Sistema de criterios de evaluacion
- Sistema de decisiones por votacion
- Flujo de entrevistas (se mantiene la tabla para referencias pero no se usa)

---

## 12. ARCHIVOS QUE CONTIENEN LOGICA DE FLUJO

| Archivo | Modificacion Requerida |
|---------|----------------------|
| `classes/application.php` | Modificar STATUSES y TRANSITIONS |
| `classes/helper/status_helper.php` | Actualizar constantes y transiciones |
| `classes/committee.php` | ELIMINAR |
| `classes/document.php` | Modificar para restringir a rol HR |
| `views/application.php` | Adaptar para nuevo flujo |
| `views/review.php` | Adaptar para mostrar segun rol |
| `templates/pages/application/*.mustache` | Adaptar UI |

---

## 13. PROXIMOS PASOS

La **Fase 4** documentara:
- Lista completa de archivos a eliminar
- Lista completa de codigo a modificar
- Dependencias entre archivos
- Orden de eliminacion

---

*Documento generado: 2025-12-22*
*Fase 3 de 5 completada*

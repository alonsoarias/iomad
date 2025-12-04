# Job Board Plugin - Tutorial de Usuario

## Tabla de Contenidos

1. [Introduccion](#introduccion)
2. [Instalacion](#instalacion)
3. [Configuracion Inicial](#configuracion-inicial)
4. [Guia del Administrador](#guia-del-administrador)
5. [Guia del Revisor](#guia-del-revisor)
6. [Guia del Postulante](#guia-del-postulante)
7. [Gestion de Documentos](#gestion-de-documentos)
8. [Comites de Seleccion](#comites-de-seleccion)
9. [Entrevistas](#entrevistas)
10. [Reportes y Exportaciones](#reportes-y-exportaciones)
11. [API Externa](#api-externa)
12. [Preguntas Frecuentes](#preguntas-frecuentes)

---

## Introduccion

El plugin **Job Board** (local_jobboard) es una solucion completa para la gestion de convocatorias laborales en Moodle. Esta disenado especificamente para instituciones educativas que requieren gestionar vacantes para docentes de catedra u otros tipos de personal.

### Caracteristicas Principales

- **Gestion de Vacantes**: Crear, publicar y administrar convocatorias laborales
- **Sistema de Postulacion**: Formularios de postulacion con carga de documentos
- **Validacion de Documentos**: Workflow completo para revision y aprobacion
- **Comites de Seleccion**: Evaluacion colaborativa por multiples revisores
- **Programacion de Entrevistas**: Calendario integrado para entrevistas
- **Excepciones ISER**: Gestion de excepciones documentales para personal historico
- **Reportes**: Dashboard con estadisticas y exportacion de datos
- **API REST**: Integracion con sistemas externos
- **Multi-tenant**: Compatible con Iomad para multiples empresas/sedes

---

## Instalacion

### Requisitos

- Moodle 4.1 LTS o superior (hasta 4.5)
- PHP 8.0 o superior
- Extension OpenSSL (para encriptacion de documentos)

### Pasos de Instalacion

1. **Copiar el plugin**:
   ```bash
   cp -r local/jobboard /ruta/a/moodle/local/
   ```

2. **Instalar via Moodle**:
   - Acceder a `Administracion del sitio > Notificaciones`
   - Seguir el asistente de instalacion

3. **Configurar permisos**:
   - Ir a `Administracion del sitio > Usuarios > Permisos > Definir roles`
   - Asignar capacidades a los roles correspondientes

---

## Configuracion Inicial

### Acceso a Configuracion

Navegue a: `Administracion del sitio > Plugins > Plugins locales > Job Board`

### Configuraciones Generales

| Configuracion | Descripcion | Valor Recomendado |
|---------------|-------------|-------------------|
| Nombre de Institucion | Nombre que aparece en emails y formularios | Nombre de su institucion |
| Email de Contacto | Email para consultas de postulantes | rrhh@institucion.edu |
| Permitir multiples postulaciones | Permitir postularse a varias vacantes | Habilitado |
| Maximo postulaciones activas | Limite de postulaciones simultaneas | 3 |

### Configuracion de Documentos

| Configuracion | Descripcion | Valor Recomendado |
|---------------|-------------|-------------------|
| Tamano maximo de archivo | En MB, limite por archivo | 10 MB |
| Formatos permitidos | Extensiones aceptadas | pdf,jpg,png |
| Antiguedad EPS | Dias maximos para certificado EPS | 30 |
| Antiguedad Pension | Dias maximos para certificado pension | 30 |
| Antiguedad Antecedentes | Dias maximos para antecedentes | 90 |

### Configuracion de Seguridad

| Configuracion | Descripcion | Valor Recomendado |
|---------------|-------------|-------------------|
| Encriptar documentos | Cifrado AES-256 para archivos sensibles | Habilitado (produccion) |
| Dias de retencion | Tiempo de almacenamiento de datos | 1825 (5 anos) |
| Habilitar API | Activar API REST externa | Segun necesidad |

### Configuracion de Navegacion

| Configuracion | Descripcion | Valor Recomendado |
|---------------|-------------|-------------------|
| Mostrar en menu principal | Agregar acceso directo en navegacion | Habilitado |
| Titulo del menu | Texto del enlace de navegacion | "Vacantes" |
| Habilitar pagina publica | Acceso sin autenticacion | Segun politica |

---

## Guia del Administrador

### Dashboard Principal

Acceda al dashboard desde: `Menu principal > Vacantes > Dashboard`

El dashboard muestra:
- **Estadisticas generales**: Vacantes activas, postulaciones pendientes
- **Items urgentes**: Vacantes por cerrar, documentos sin revisar
- **Acciones rapidas**: Crear vacante, ver postulaciones

### Crear una Nueva Vacante

1. Ir a `Vacantes > Gestionar > Nueva vacante`
2. Completar informacion basica:
   - **Codigo**: Identificador unico (ej: VAC-2024-001)
   - **Titulo**: Nombre del cargo
   - **Descripcion**: Detalles de la posicion
   - **Tipo de contrato**: Catedra, Tiempo completo, etc.
   - **Duracion**: Periodo del contrato
   - **Salario**: Informacion de remuneracion (opcional)
   - **Ubicacion**: Sede o departamento
   - **Numero de vacantes**: Cantidad de posiciones

3. Configurar fechas:
   - **Fecha de apertura**: Cuando inicia recepcion de postulaciones
   - **Fecha de cierre**: Limite para postularse

4. Definir requisitos:
   - **Requisitos obligatorios**: Experiencia, formacion minima
   - **Requisitos deseables**: Competencias adicionales valoradas

5. Seleccionar documentos requeridos:
   - Marcar los tipos de documento obligatorios
   - Configurar instrucciones especificas por documento

6. **Guardar como borrador** o **Publicar** directamente

### Estados de una Vacante

| Estado | Descripcion | Acciones Disponibles |
|--------|-------------|---------------------|
| Borrador | En preparacion, no visible | Editar, Publicar |
| Publicada | Activa, recibiendo postulaciones | Cerrar, Editar fechas |
| Cerrada | No recibe mas postulaciones | Evaluar, Reabrir |
| Asignada | Proceso finalizado, vacante cubierta | Ver historial |

### Gestionar Postulaciones

1. Ir a `Vacantes > Postulaciones` o desde la vacante especifica
2. Filtrar por:
   - Estado de postulacion
   - Vacante
   - Fecha

3. Acciones sobre postulaciones:
   - **Ver detalle**: Informacion completa del postulante
   - **Revisar documentos**: Validar documentacion
   - **Cambiar estado**: Avanzar en el proceso
   - **Asignar revisor**: Delegar revision

### Tipos de Documento

Administre los tipos de documento en: `Vacantes > Administracion > Tipos de Documento`

Tipos predefinidos:
- **Hoja de vida** (curriculum)
- **Cedula de ciudadania** (identificacion)
- **Certificado EPS** (seguridad social salud)
- **Certificado Pension** (seguridad social pension)
- **Antecedentes disciplinarios**
- **Antecedentes fiscales**
- **Antecedentes judiciales**
- **Titulo profesional**
- **Experiencia laboral**

Para cada tipo puede configurar:
- Nombre y descripcion
- Si es requerido por defecto
- Formatos aceptados
- Antiguedad maxima (dias)
- Si aplica para excepcion ISER

---

## Guia del Revisor

### Acceso al Panel de Revision

Los revisores acceden desde: `Menu principal > Vacantes > Mis Revisiones`

### Proceso de Revision de Documentos

1. Seleccionar postulacion asignada
2. Para cada documento:
   - **Visualizar**: Abrir documento en visor
   - **Verificar**: Comprobar legibilidad, vigencia, autenticidad
   - **Aprobar** o **Rechazar**
   - **Agregar notas**: Comentarios para el postulante (si aplica)

3. Estados de documento:
   - **Pendiente**: Sin revisar
   - **Aprobado**: Documento valido
   - **Rechazado**: Documento invalido o incompleto
   - **Requiere resubida**: Solicitar nuevo documento

### Razones Comunes de Rechazo

- Documento ilegible
- Documento vencido
- Formato incorrecto
- Informacion incompleta
- Documento no corresponde al tipo solicitado

### Validacion Masiva

Para agilizar la revision: `Vacantes > Administracion > Validacion Masiva`

1. Seleccionar vacante
2. Filtrar por tipo de documento
3. Aprobar/rechazar multiples documentos simultaneamente

---

## Guia del Postulante

### Buscar Vacantes

1. Acceder a `Vacantes` desde el menu principal
2. Ver listado de vacantes disponibles
3. Filtrar por:
   - Tipo de contrato
   - Ubicacion/sede
   - Fecha de cierre

### Postularse a una Vacante

1. Hacer clic en la vacante de interes
2. Revisar requisitos y documentos solicitados
3. Clic en "Postularse"
4. Completar formulario de postulacion:
   - Informacion de contacto (prellenada de perfil)
   - Carta de presentacion (opcional)
5. Cargar documentos requeridos:
   - Arrastrar archivos o usar selector
   - Indicar fecha de emision para cada documento
   - Verificar que el formato sea correcto
6. Revisar y enviar postulacion

### Seguimiento de Postulacion

Acceda a: `Vacantes > Mis Postulaciones`

Estados posibles de su postulacion:
| Estado | Significado |
|--------|-------------|
| Enviada | Postulacion recibida, pendiente revision |
| En Revision | Sus documentos estan siendo evaluados |
| Documentos Validados | Toda la documentacion aprobada |
| Documentos Rechazados | Algun documento requiere correccion |
| Entrevista Programada | Tiene entrevista agendada |
| Seleccionado | Fue elegido para la vacante |
| No Seleccionado | No continua en el proceso |

### Resubir Documentos

Si un documento fue rechazado:
1. Ir a `Mis Postulaciones`
2. Seleccionar la postulacion
3. Ver documentos rechazados
4. Clic en "Resubir" junto al documento
5. Cargar nuevo archivo
6. El documento vuelve a estado "Pendiente"

---

## Gestion de Documentos

### Subida de Documentos

**Formatos aceptados**: PDF, JPG, PNG
**Tamano maximo**: Configurable (default 10 MB)

Recomendaciones:
- Escanear documentos en alta resolucion
- Verificar legibilidad antes de subir
- Usar PDF para documentos de multiples paginas
- Verificar que el documento no este vencido

### Encriptacion

Cuando la encriptacion esta habilitada:
- Los archivos se cifran con AES-256-GCM
- Solo usuarios autorizados pueden descargar
- Impacto minimo en rendimiento (~20%)
- Requiere respaldo de clave de encriptacion

### Retencion de Datos

Los documentos se eliminan automaticamente despues del periodo configurado:
- Default: 1825 dias (5 anos)
- Cumple con GDPR y Ley 1581/2012 (Habeas Data Colombia)

---

## Comites de Seleccion

### Crear un Comite

1. Ir a `Vacantes > Gestionar > [Seleccionar vacante] > Comite`
2. Clic en "Crear Comite"
3. Configurar:
   - Nombre del comite
   - Miembros (seleccionar usuarios con capacidad de revision)
   - Criterios de evaluacion (opcional)

### Evaluacion por Comite

1. Cada miembro del comite evalua postulantes asignados
2. Puede votar: Aprobar, Rechazar, Abstener
3. Agregar observaciones
4. Sistema calcula recomendacion agregada

### Toma de Decisiones

1. Ver resultados consolidados del comite
2. Seleccionar decision final:
   - **Seleccionado**: Candidato elegido
   - **Lista de espera**: Candidato alternativo
   - **No seleccionado**: No continua
3. Sistema notifica automaticamente al postulante

---

## Entrevistas

### Programar una Entrevista

1. Ir a la postulacion del candidato
2. Clic en "Programar Entrevista"
3. Configurar:
   - Fecha y hora
   - Tipo: Presencial, Virtual
   - Ubicacion o enlace de reunion
   - Entrevistadores asignados
   - Notas para el candidato
4. Sistema envia notificacion al postulante

### Durante la Entrevista

El entrevistador puede:
- Ver perfil del candidato
- Consultar documentos aprobados
- Registrar observaciones
- Calificar segun criterios definidos

### Despues de la Entrevista

1. Registrar resultado:
   - Completada satisfactoriamente
   - No se presento (No Show)
   - Reprogramar
2. Agregar comentarios finales
3. Avanzar postulacion al siguiente estado

---

## Reportes y Exportaciones

### Dashboard de Reportes

Acceso: `Vacantes > Reportes`

Reportes disponibles:
- **General**: Estadisticas globales del sistema
- **Por Vacante**: Metricas especificas por convocatoria
- **Por Revisor**: Carga de trabajo y productividad
- **Timeline**: Linea de tiempo de actividad

### Exportar Datos

Formatos disponibles:
- **CSV**: Para analisis en Excel
- **Excel**: Formato nativo con formato
- **PDF**: Reportes formales

Datos exportables:
- Lista de postulantes por vacante
- Estado de documentos
- Historial de workflow
- Resultados de evaluacion

---

## API Externa

### Activar la API

1. Ir a `Administracion > Plugins > Job Board > Seguridad`
2. Habilitar "API REST"

### Crear Token de Acceso

1. Ir a `Vacantes > Administracion > Tokens API`
2. Clic en "Nuevo Token"
3. Configurar:
   - Nombre descriptivo
   - Permisos (lectura, escritura, administracion)
   - IP permitidas (opcional)
   - Fecha de expiracion (opcional)
4. Guardar y copiar el token generado

### Endpoints Disponibles

Base URL: `https://su-sitio.com/local/jobboard/api/v1/`

| Metodo | Endpoint | Descripcion |
|--------|----------|-------------|
| GET | /vacancies | Listar vacantes |
| GET | /vacancies/{id} | Detalle de vacante |
| GET | /applications | Listar postulaciones |
| GET | /applications/{id} | Detalle de postulacion |
| GET | /documents/{id} | Obtener documento |

### Ejemplo de Uso

```bash
curl -X GET \
  "https://su-sitio.com/local/jobboard/api/v1/vacancies" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

### Rate Limiting

- Maximo 100 solicitudes por hora por token
- Headers de respuesta incluyen limite restante

---

## Preguntas Frecuentes

### Para Administradores

**P: Como puedo ver quien reviso un documento?**
R: En el detalle del documento, la seccion "Historial de Revision" muestra todas las acciones realizadas con usuario, fecha y notas.

**P: Puedo reabrir una vacante cerrada?**
R: Si, mientras no este en estado "Asignada". Vaya a la vacante, clic en "Reabrir" y defina nueva fecha de cierre.

**P: Como configuro notificaciones automaticas?**
R: En `Administracion > Plugins > Job Board > Notificaciones`, puede personalizar las plantillas de email para cada evento del sistema.

### Para Revisores

**P: Cuantas postulaciones puedo revisar simultaneamente?**
R: No hay limite tecnico. El sistema balancea carga automaticamente entre revisores disponibles.

**P: Que hago si un documento es ilegible?**
R: Rechace el documento seleccionando "Documento ilegible" como razon. El sistema notificara al postulante para que suba una nueva version.

### Para Postulantes

**P: Puedo modificar mi postulacion despues de enviarla?**
R: No puede modificar la postulacion, pero si puede resubir documentos si son rechazados.

**P: Recibre notificaciones del proceso?**
R: Si, recibira emails en cada cambio de estado de su postulacion. Revise su carpeta de spam.

**P: Puedo postularme desde el celular?**
R: Si, la plataforma es responsive. Sin embargo, recomendamos usar computador para la carga de documentos.

---

## Soporte

Para asistencia tecnica, contacte:
- Email: soporte@su-institucion.edu
- Telefono: +57 (X) XXX-XXXX
- Horario: Lunes a Viernes, 8:00 AM - 5:00 PM

---

*Plugin Version: 1.7.5-beta*
*Ultima actualizacion: Diciembre 2025*
*Compatible con: Moodle 4.1 - 4.5*

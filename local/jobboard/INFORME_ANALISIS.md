# INFORME DE ANÁLISIS - Plugin local_jobboard

**Fecha de Análisis:** 2025-12-08
**Versión Analizada:** 2.0.32 (2025120850)
**Analista:** Claude Code

---

## 1. RESUMEN EJECUTIVO

El plugin `local_jobboard` es un sistema completo de gestión de convocatorias y vacantes para profesores catedráticos, diseñado para Moodle 4.1+ con compatibilidad IOMAD multi-tenant.

### Estado General: FUNCIONAL CON MEJORAS MENORES NECESARIAS

**Puntos Fuertes:**
- Arquitectura bien diseñada con 24 tablas de base de datos
- Sistema de clases robusto con separación de responsabilidades
- 15 vistas con templates Mustache
- 15 User Tours configurados
- 1,921 cadenas de idioma en EN y ES (paridad completa)
- CLI funcional con múltiples modos de operación

**Áreas de Mejora Identificadas:**
1. JSON de perfiles incompleto (167 de 197 sin tipo de contrato)
2. Badge de versión en README desactualizado (2.0.16 vs 2.0.32)
3. CLI tiene 1,851 líneas - podría beneficiarse de modularización

---

## 2. INVENTARIO DE COMPONENTES

### 2.1 Estructura de Directorios

```
local/jobboard/
├── admin/                  # Páginas de administración (4 archivos)
├── ajax/                   # Endpoints AJAX (1 archivo)
├── amd/                    # JavaScript AMD (src/ y build/)
├── api/                    # API REST v1
├── classes/                # Clases PHP (19 clases principales)
│   ├── api/                # Endpoints API (4 archivos)
│   ├── event/              # Eventos Moodle (8 eventos)
│   ├── forms/              # Formularios (7 formularios)
│   ├── output/             # Renderizadores (2 archivos)
│   ├── privacy/            # Proveedor privacidad
│   ├── task/               # Tareas programadas (3 tareas)
│   └── trait/              # Traits PHP (1 archivo)
├── cli/                    # Scripts CLI (4 archivos)
├── db/                     # Definiciones base de datos
│   └── tours/              # User Tours (15 archivos JSON)
├── lang/                   # Cadenas de idioma (EN y ES)
├── PERFILESPROFESORES/     # Documentos fuente (PDF y DOCX)
├── templates/              # Plantillas Mustache
│   ├── components/         # Componentes reutilizables (13 archivos)
│   ├── pages/              # Páginas completas (15 archivos)
│   └── reports/            # Reportes (5 archivos)
├── tests/                  # Tests (behat y generator)
└── views/                  # Controladores de vista (16 archivos)
```

### 2.2 Archivos Principales

| Archivo | Propósito | Líneas |
|---------|-----------|--------|
| lib.php | Funciones helper globales | ~1,000 |
| cli/cli.php | CLI unificado de importación | 1,851 |
| classes/vacancy.php | Clase de vacantes | ~1,000 |
| classes/application.php | Clase de postulaciones | ~700 |
| version.php | Metadatos del plugin | 36 |
| styles.css | Estilos CSS | ~1,100 |

### 2.3 Base de Datos (24 tablas)

| # | Tabla | Propósito |
|---|-------|-----------|
| 1 | local_jobboard_vacancy | Vacantes |
| 2 | local_jobboard_vacancy_field | Campos personalizados de vacantes |
| 3 | local_jobboard_application | Postulaciones |
| 4 | local_jobboard_document | Documentos subidos |
| 5 | local_jobboard_doc_validation | Validaciones de documentos |
| 6 | local_jobboard_doc_requirement | Requisitos de documentos por vacante |
| 7 | local_jobboard_workflow_log | Log de cambios de estado |
| 8 | local_jobboard_audit | Log de auditoría general |
| 9 | local_jobboard_exemption | Exenciones ISER |
| 10 | local_jobboard_notification | Cola de notificaciones |
| 11 | local_jobboard_config | Configuración del plugin |
| 12 | local_jobboard_api_token | Tokens API |
| 13 | local_jobboard_doctype | Tipos de documentos predefinidos |
| 14 | local_jobboard_email_template | Plantillas de email |
| 15 | local_jobboard_interview | Entrevistas programadas |
| 16 | local_jobboard_interviewer | Miembros del panel de entrevistas |
| 17 | local_jobboard_committee | Comités de selección |
| 18 | local_jobboard_committee_member | Miembros de comités |
| 19 | local_jobboard_evaluation | Evaluaciones de comité |
| 20 | local_jobboard_criteria | Criterios de evaluación |
| 21 | local_jobboard_decision | Decisiones finales |
| 22 | local_jobboard_consent | Registros de consentimiento |
| 23 | local_jobboard_applicant_profile | Perfiles extendidos de postulantes |
| 24 | local_jobboard_convocatoria | Convocatorias (agrupan vacantes) |

### 2.4 Clases Principales

| Clase | Métodos Públicos | Propósito |
|-------|------------------|-----------|
| vacancy | 35+ | Gestión de vacantes (CRUD, estados, publicación) |
| application | 20+ | Gestión de postulaciones |
| document | ~15 | Gestión de documentos |
| committee | ~15 | Gestión de comités de selección |
| interview | ~12 | Programación de entrevistas |
| reviewer | ~10 | Asignación de revisores |
| exemption | ~12 | Gestión de exenciones ISER |
| notification | ~8 | Sistema de notificaciones |
| audit | ~5 | Registro de auditoría |

### 2.5 Formularios (Moodleforms)

| Formulario | Propósito |
|------------|-----------|
| vacancy_form | Crear/editar vacantes |
| convocatoria_form | Crear/editar convocatorias |
| application_form | Formulario de postulación |
| signup_form | Registro de postulantes |
| updateprofile_form | Actualización de perfil |
| exemption_form | Gestión de exenciones |
| api_token_form | Gestión de tokens API |

### 2.6 Tareas Programadas

| Tarea | Frecuencia | Propósito |
|-------|------------|-----------|
| send_notifications | Cada 5 min | Enviar notificaciones pendientes |
| check_closing_vacancies | 8:00 AM diario | Verificar vacantes por cerrar |
| cleanup_old_data | 3:00 AM día 1 | Limpiar datos antiguos |

### 2.7 Eventos Moodle

| Evento | Trigger |
|--------|---------|
| application_created | Al crear postulación |
| application_status_changed | Al cambiar estado |
| document_uploaded | Al subir documento |
| vacancy_created | Al crear vacante |
| vacancy_updated | Al actualizar vacante |
| vacancy_published | Al publicar vacante |
| vacancy_closed | Al cerrar vacante |
| vacancy_deleted | Al eliminar vacante |

---

## 3. ANÁLISIS DE VISTAS

### 3.1 Vistas Disponibles (16 archivos en views/)

| Vista | Archivo | Propósito | Template |
|-------|---------|-----------|----------|
| Dashboard | dashboard.php | Panel principal por rol | pages/dashboard.mustache |
| Public | public.php | Portal público de vacantes | pages/public.mustache |
| Vacancies | vacancies.php | Listado de vacantes | pages/vacancies.mustache |
| Vacancy | vacancy.php | Detalle de vacante | pages/vacancy_detail.mustache |
| Apply | apply.php | Formulario de postulación | pages/apply.mustache |
| Application | application.php | Detalle de postulación | pages/application_detail.mustache |
| Applications | applications.php | Mis postulaciones | pages/applications.mustache |
| Convocatorias | convocatorias.php | Listado de convocatorias | pages/convocatorias.mustache |
| Convocatoria | convocatoria.php | Detalle de convocatoria | pages/convocatoria.mustache |
| View Convocatoria | view_convocatoria.php | Ver convocatoria | pages/convocatoria.mustache |
| Browse Convocatorias | browse_convocatorias.php | Explorar convocatorias | - |
| Manage | manage.php | Gestión de vacantes | pages/manage.mustache |
| Review | review.php | Revisión de postulación | pages/review.mustache |
| My Reviews | myreviews.php | Mis revisiones pendientes | pages/myreviews.mustache |
| Reports | reports.php | Reportes y estadísticas | pages/reports.mustache |

### 3.2 Templates Mustache

**Components (13):** action_card, data_table, empty_state, filter_form, info_card, list_group, page_header, progress_bar, stat_card, status_badge, timeline, vacancy_card

**Pages (15):** application_detail, applications, apply, convocatoria, convocatorias, dashboard, manage, myreviews, public, public_detail, reports, review, vacancies, vacancy_detail

**Reports (5):** applications, documents, overview, reviewers, timeline

---

## 4. ANÁLISIS DEL CLI

### 4.1 Archivos CLI

| Archivo | Líneas | Propósito |
|---------|--------|-----------|
| cli.php | 1,851 | Script unificado de importación |
| import_vacancies.php | 387 | Importación desde CSV |
| parse_profiles.php | 339 | Parser de perfiles v1 |
| parse_profiles_v2.php | 291 | Parser de perfiles v2 |

### 4.2 Opciones del CLI Principal (cli.php)

**Opciones Básicas:**
- `-h, --help` - Mostrar ayuda
- `-i, --input=DIR` - Directorio de entrada
- `-x, --csv=FILE` - Importar desde CSV
- `-T, --export-csv-template` - Generar template CSV
- `-j, --export-json=FILE` - Exportar a JSON
- `-v, --verbose` - Salida detallada

**Opciones Moodle:**
- `-C, --create-structure` - Crear empresas/departamentos IOMAD
- `-p, --publish` - Publicar vacantes
- `-P, --public` - Hacer vacantes públicas
- `-r, --reset` - Eliminar vacantes existentes
- `--reset-convocatorias` - Eliminar convocatorias
- `-c, --convocatoria=ID` - Usar convocatoria existente
- `--convocatoria-name=NAME` - Nombre para nueva convocatoria
- `-o, --opendate=DATE` - Fecha de apertura
- `-e, --closedate=DATE` - Fecha de cierre
- `-d, --dryrun` - Modo simulación
- `-u, --update` - Actualizar existentes

### 4.3 Modos de Operación

1. **Standalone (sin Moodle):** Solo parseo y exportación a JSON
2. **Moodle Mode:** Importación completa con creación de registros

---

## 5. ANÁLISIS DE USER TOURS

### 5.1 Tours Configurados (15 tours)

| Tour | URL Pattern | Pasos |
|------|-------------|-------|
| dashboard | /local/jobboard/index.php%view=dashboard% | 8 |
| public | /local/jobboard/public.php% | 10 |
| vacancies | /local/jobboard/index.php%view=vacancies% | 7 |
| vacancy | /local/jobboard/index.php%view=vacancy% | 7 |
| apply | /local/jobboard/index.php%view=apply% | 9 |
| application | /local/jobboard/index.php%view=application% | 7 |
| myapplications | /local/jobboard/index.php%view=applications% | 8 |
| convocatorias | /local/jobboard/index.php%view=convocatorias% | 8 |
| convocatoria_manage | /local/jobboard/index.php%view=convocatoria% | 7 |
| manage | /local/jobboard/index.php%view=manage% | 10 |
| review | /local/jobboard/index.php%view=review% | 9 |
| myreviews | /local/jobboard/index.php%view=myreviews% | 6 |
| documents | /local/jobboard/admin/doctypes.php% | 6 |
| validate_document | /local/jobboard/validate_document.php% | 8 |
| reports | /local/jobboard/index.php%view=reports% | 8 |

### 5.2 Selectores CSS Utilizados

Los tours usan selectores robustos basados en clases del plugin:
- `.local-jobboard-*` - Contenedores principales
- `.jb-*` - Componentes específicos (jb-stat-card, jb-filter-form, etc.)
- `#*-heading` - IDs de accesibilidad para secciones
- `.card`, `.table`, `.btn-*` - Clases Bootstrap estándar

---

## 6. ANÁLISIS DE CADENAS DE IDIOMA

### 6.1 Estadísticas

| Idioma | Archivo | Líneas | Cadenas |
|--------|---------|--------|---------|
| Inglés | lang/en/local_jobboard.php | 2,388 | 1,921 |
| Español | lang/es/local_jobboard.php | 2,779 | 1,921 |

**Paridad:** 100% - Ambos idiomas tienen exactamente las mismas cadenas.

### 6.2 Cadenas de Tours

- Cadenas para tours: 267 en cada idioma
- Formato: `tour_NOMBRE_stepN_title/content`

---

## 7. ANÁLISIS DE PERFILESPROFESORES

### 7.1 Documentos Fuente

| Archivo | Formato | Tamaño |
|---------|---------|--------|
| FCAS-PERFILES_PROFESORES_2026.pdf | PDF | 373 KB |
| FCAS-PERFILES_PROFESORES_2026.docx | DOCX | 405 KB |
| PERFILES_PROFESORES MODALIDAD PRESENCIAL_FII_2026.pdf | PDF | 125 KB |
| PERFILES_PROFESORES MODALIDAD PRESENCIAL_FII_2026.docx | DOCX | 316 KB |
| PERFILES_PROFESORES MODALIDAD A DISTANCIA_FII_2026.pdf | PDF | 135 KB |
| PERFILES_PROFESORES MODALIDAD A DISTANCIA_FII_2026.docx | DOCX | 301 KB |

### 7.2 JSON Parseado (perfiles_2026.json)

| Métrica | Valor |
|---------|-------|
| Tamaño | 79 KB |
| Total de perfiles | 197 |
| Perfiles FCAS | 156 |
| Perfiles FII | 41 |

### 7.3 Calidad de Datos del JSON

| Campo | Completo | Vacío | Porcentaje |
|-------|----------|-------|------------|
| code | 197 | 0 | 100% |
| faculty | 197 | 0 | 100% |
| modality | 197 | 0 | 100% |
| location | 197 | 0 | 100% |
| contracttype | 30 | 167 | 15% |
| courses | 131 | 66 | 66% |

**Problema Identificado:** 167 vacantes (85%) no tienen tipo de contrato definido.

### 7.4 Distribución de Datos

**Por Facultad:**
- FCAS: 156 (79%)
- FII: 41 (21%)

**Por Modalidad:**
- PRESENCIAL: 189 (96%)
- A DISTANCIA: 8 (4%)

**Por Tipo de Contrato (solo los definidos):**
- OCASIONAL TIEMPO COMPLETO: 21
- CATEDRA: 9
- Sin definir: 167

---

## 8. PROBLEMAS IDENTIFICADOS

### 8.1 Críticos (Ninguno)

No se identificaron problemas críticos que impidan el funcionamiento.

### 8.2 Importantes

| # | Problema | Impacto | Solución |
|---|----------|---------|----------|
| 1 | JSON de perfiles incompleto | 85% sin tipo de contrato | Reprocesar documentos o asignar valor por defecto |
| 2 | Badge de versión en README | Inconsistencia visual | Actualizar a 2.0.32 |

### 8.3 Menores

| # | Problema | Impacto | Solución |
|---|----------|---------|----------|
| 1 | CLI muy grande (1,851 líneas) | Mantenibilidad | Modularizar en funciones/clases |
| 2 | Datos solo para PAMPLONA | Limitación de datos | Agregar otras sedes si es necesario |

---

## 9. RECOMENDACIONES

### 9.1 Prioridad Alta

1. **Corregir datos del JSON de perfiles**
   - Asignar tipo de contrato por defecto ("CATEDRA") a los 167 perfiles sin definir
   - O reprocesar los documentos fuente para extraer la información

2. **Actualizar badge de versión en README.md**
   - Cambiar `Version-2.0.16` a `Version-2.0.32`

### 9.2 Prioridad Media

3. **Verificar User Tours después de cambios de interfaz**
   - Los selectores actuales son robustos pero deben validarse

4. **Considerar modularización del CLI**
   - Separar funciones de parseo, importación y estructura IOMAD

### 9.3 Prioridad Baja

5. **Agregar más ubicaciones al JSON**
   - Actualmente solo PAMPLONA
   - Considerar: CÚCUTA, TIBÚ, SAN VICENTE, EL TARRA, OCAÑA

---

## 10. CONCLUSIÓN

El plugin `local_jobboard` está en un estado maduro y funcional. La arquitectura es sólida, con una separación clara de responsabilidades entre clases, vistas y templates. Los User Tours están bien configurados con selectores CSS robustos, y las cadenas de idioma tienen paridad perfecta entre inglés y español.

Los problemas identificados son menores y no afectan la funcionalidad principal. La recomendación principal es corregir los datos incompletos en el JSON de perfiles y mantener la documentación sincronizada con la versión actual.

---

*Informe generado automáticamente el 2025-12-08*

# Changelog - local_jobboard

Todos los cambios notables de este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Versionado Semántico](https://semver.org/lang/es/).

---

## [2.0.5] - 2025-12-06

### Eliminado
- **Estilos de navegación personalizados**
  - Removidas las reglas CSS para `.nav`, `.nav-link`, `.nav-tabs`
  - La navegación ahora usa exclusivamente los estilos nativos del tema Moodle
  - El plugin ya utiliza `local_jobboard_extend_navigation()` para integración nativa

### Verificado
- **Navegación nativa de Moodle**
  - El plugin usa `local_jobboard_extend_navigation()` en lib.php
  - Añade items al navigation drawer (barra lateral)
  - Añade items al custom menu (menú superior)
  - Soporta submenús basados en capabilities del usuario
  - Integración completa con Moodle sin estilos personalizados

### Modificado
- **styles.css**: Removidos ~50 líneas de estilos de navegación
- **version.php**: Incrementado a 2.0.5 (2025120623)

---

## [2.0.4] - 2025-12-06

### Corregido
- **Breadcrumbs duplicados eliminados**
  - `ui_helper::page_header()` ya no renderiza título ni breadcrumbs
  - Template `page_header.mustache` solo muestra botones de acción
  - El título se maneja con `$PAGE->set_heading()` de Moodle
  - Los breadcrumbs se manejan con `$PAGE->navbar` de Moodle
  - Evita duplicación de título y navegación en todas las vistas

- **Formulario de registro (signup_form.php)**
  - Nombres y apellidos se convierten automáticamente a MAYÚSCULAS
  - Email se convierte automáticamente a minúsculas
  - Validación de email case-insensitive para evitar duplicados

### Modificado
- **ui_helper::page_header()**: Parámetros `$title` y `$breadcrumbs` ahora son ignorados (deprecated)
- **page_header.mustache**: Simplificado para mostrar solo botones de acción
- **version.php**: Incrementado a 2.0.4 (2025120622)

---

## [2.0.3] - 2025-12-06

### Añadido
- **Creación de roles en actualizaciones**
  - Los roles personalizados ahora se crean también al actualizar el plugin
  - Instalaciones existentes obtendrán los 3 roles automáticamente
  - Añadido paso de upgrade 2025120621 en `db/upgrade.php`

- **Estilos base para todos los elementos HTML**
  - Tipografía completa: h1-h6, p, span, strong, em, small
  - Enlaces (a) con estados hover y focus
  - Listas (ul, ol, li, dl, dt, dd)
  - Elementos de formulario: label, input, select, textarea, button
  - Elementos de código: code, pre
  - Otros: blockquote, hr, img, figure, figcaption, address
  - Utilidades de texto: colores, pesos, alineación, transformación
  - Todos con prefijo `.path-local-jobboard` para aislamiento de temas

### Modificado
- **styles.css**: Ampliado con ~250 líneas de estilos base de elementos
- **version.php**: Incrementado a 2.0.3 (2025120621)

### Corregido
- Roles no se creaban en instalaciones existentes que actualizaban desde versiones anteriores

---

## [2.0.2] - 2025-12-06

### Añadido
- **Aislamiento completo de estilos de temas**
  - Nuevos overrides para: btn-secondary, btn-warning, btn-info, btn-light, btn-dark, btn-link
  - Overrides de outline buttons: success, danger, warning, info
  - List groups con estilos completos
  - Nav tabs y pills
  - Progress bars con variantes de color
  - Input groups y custom form elements
  - Tooltips base
  - Todas las utilidades Bootstrap: spacing, display, flex, position, overflow, float

### Modificado
- **README.md**: Reescrito completamente
  - Nueva estructura con tabla de contenidos
  - Documentación detallada de características por rol
  - Guía de uso con flujo de trabajo típico
  - Documentación de API REST con ejemplos
  - Descripción de todos los estados del workflow
  - Información de cumplimiento normativo (Colombia + GDPR)

- **CHANGELOG.md**: Reescrito completamente
  - Historial completo desde v1.0.0 hasta v2.0.2
  - 86 versiones documentadas
  - Roadmap de versiones futuras

---

## [2.0.1] - 2025-12-06

### Añadido
- **Estilos de override para temas remui/inteb**
  - Prefijo `.path-local-jobboard` para especificidad CSS
  - Override completo de: cards, tablas, botones, badges, formularios
  - Override de: alerts, pagination, modals, dropdowns, list-groups
  - Override de: navs, progress bars, input-groups, tooltips
  - Utilidades de: backgrounds, borders, shadows, typography
  - Utilidades de: spacing (margins/paddings), display, flex, positions
  - Colores de texto y espaciado garantizados

### Modificado
- **Breadcrumbs removidos**: Uso de navegación nativa de Moodle
  - Removidos de: assign_reviewer.php, import_exemptions.php, edit.php
  - Removidos de todos los archivos en views/
  - Removidos de: signup.php, validate_document.php, manage_exemptions.php
  - Removidos de: schedule_interview.php, reupload_document.php, updateprofile.php
  - Removidos de: manage_committee.php, manage_applications.php, bulk_validate.php

- **Estilos inline migrados a styles.css**
  - `ui_helper::get_inline_styles()` ahora retorna string vacío
  - Migrados: jb-page-header, jb-stat-card, jb-filter-form
  - Migrados: jb-stat-value, jb-stat-label, jb-empty-state, jb-actions
  - Migradas utilidades: border-left-*, cursor-pointer, badge-purple

### Corregido
- Conflictos de navegación y visualización con temas remui/inteb
- Estilos del plugin ahora tienen precedencia sobre estilos del tema

---

## [2.0.0] - 2025-12-06

### Añadido
- **Sistema de Convocatorias**
  - Gestión jerárquica: Convocatorias contienen múltiples vacantes
  - Vista pública de convocatorias abiertas
  - Navegación browse_convocatorias.php y view_convocatoria.php
  - Clase `convocatoria.php` con gestión completa

- **Roles Personalizados (3 nuevos roles)**
  - `jobboard_reviewer`: Revisor de documentos (archetype: user)
  - `jobboard_coordinator`: Coordinador de selección (archetype: editingteacher)
  - `jobboard_committee`: Miembro de comité de selección (archetype: teacher)
  - Creación automática en db/install.php

- **15 User Tours Completos**
  - tour_dashboard.json: Panel principal (5 pasos)
  - tour_public.json: Vacantes públicas (9 pasos)
  - tour_apply.json: Formulario de postulación (8 pasos)
  - tour_myapplications.json: Mis postulaciones (7 pasos)
  - tour_review.json: Revisión de documentos (9 pasos)
  - tour_manage.json: Gestión de vacantes (9 pasos)
  - tour_reports.json: Reportes (8 pasos)
  - tour_validate_document.json: Validación de documentos (8 pasos)
  - Tours adicionales para convocatorias y excepciones
  - Disponibles en español e inglés
  - Instalación automática desde db/tours/

- **34 Capabilities**
  - Permisos granulares para todas las funcionalidades
  - Nuevos permisos para convocatorias y API
  - Permisos para comité de selección

- **Privacy API Completa**
  - Cobertura de 10 tablas con datos de usuario
  - Cumplimiento GDPR y Ley Habeas Data (Colombia)
  - Exportación y eliminación de datos personales

### Modificado
- **Versión estable**: Maturity cambiada de BETA a STABLE
- **styles.css**: Reducido de 1,310 a 176 líneas (87% de reducción)
- **Templates core**: Removidos estilos inline, uso de Bootstrap nativo
- **signup_page.mustache**: Iconos Font Awesome reemplazados por pix_icon

### Mejorado
- **Accesibilidad**
  - Focus indicators para navegación por teclado
  - Soporte para `prefers-reduced-motion`
  - Soporte para `prefers-contrast: high`

---

## [1.9.42] - 2025-12-05

### Corregido
- Error de sesskey en manage.php: Solo acciones que modifican datos requieren sesskey

---

## [1.9.41] - 2025-12-05

### Corregido
- TypeError en vacancy.php: Conversión explícita de userid a int

---

## [1.9.40] - 2025-12-05

### Añadido
- Método `is_open_for_applications()` como alias de `is_open()` en vacancy.php

---

## [1.9.39] - 2025-12-05

### Corregido
- Warning undefined property en doctypes.php: Campo `isrequired` en lugar de `required`

---

## [1.9.38] - 2025-12-05

### Añadido
- Método `get_record()` como alias de `to_record()` en vacancy.php

---

## [1.9.37] - 2025-12-05

### Añadido
- Clase `document_services.php` para conversión de documentos

---

## [1.9.36] - 2025-12-05

### Añadido
- Integración con `\core_files\converter` de Moodle (unoconv/LibreOffice)

---

## [1.9.35] - 2025-12-05

### Añadido
- Soporte para formatos: DOC, DOCX, XLS, XLSX, PPT, PPTX, ODT, ODS, ODP, RTF, TXT, HTML

---

## [1.9.34] - 2025-12-05

### Añadido
- Endpoint AJAX `ajax_conversion.php` para polling de estado de conversión

---

## [1.9.33] - 2025-12-05

### Añadido
- Indicador visual de estado de conversión (badge) en tiempo real

---

## [1.9.32] - 2025-12-05

### Añadido
- Previsualización automática de documentos convertidos a PDF

---

## [1.9.31] - 2025-12-05

### Modificado
- `validate_document.php`: Integración completa con sistema de conversión

---

## [1.9.30] - 2025-12-05

### Añadido
- Strings de idioma para estados de conversión (EN/ES)

---

## [1.9.29] - 2025-12-05

### Añadido
- `tour_dashboard.json`: Tour del panel principal (5 pasos)

---

## [1.9.28] - 2025-12-05

### Añadido
- `tour_public.json`: Tour de vacantes públicas (9 pasos)

---

## [1.9.27] - 2025-12-05

### Añadido
- `tour_apply.json`: Tour del formulario de postulación (8 pasos)

---

## [1.9.26] - 2025-12-05

### Añadido
- `tour_myapplications.json`: Tour de mis postulaciones (7 pasos)

---

## [1.9.25] - 2025-12-05

### Añadido
- `tour_review.json`: Tour de revisión de documentos (9 pasos)

---

## [1.9.24] - 2025-12-05

### Añadido
- `tour_manage.json`: Tour de gestión de vacantes (9 pasos)

---

## [1.9.23] - 2025-12-05

### Añadido
- `tour_reports.json`: Tour de reportes (8 pasos)

---

## [1.9.22] - 2025-12-05

### Añadido
- `tour_validate_document.json`: Tour de validación de documentos (8 pasos)

---

## [1.9.21] - 2025-12-05

### Añadido
- Módulo AMD `document_preview.js` con visor PDF.js modal

---

## [1.9.20] - 2025-12-05

### Añadido
- Soporte para imágenes (JPEG, PNG, GIF, WebP) en previsualizador

---

## [1.9.19] - 2025-12-05

### Añadido
- Carga dinámica de PDF.js desde CDN

---

## [1.9.18] - 2025-12-05

### Añadido
- Controles de navegación de páginas en visor PDF

---

## [1.9.17] - 2025-12-05

### Añadido
- `export_documents.php`: Exportación de documentos como archivo ZIP

---

## [1.9.16] - 2025-12-05

### Añadido
- Exportación individual de documentos por aplicación

---

## [1.9.15] - 2025-12-05

### Añadido
- Exportación masiva de documentos por vacante (todos los aplicantes)

---

## [1.9.14] - 2025-12-05

### Añadido
- Estructura de carpetas ZIP: APPLICANT/DOCTYPE/filename

---

## [1.9.13] - 2025-12-05

### Añadido
- Botón de exportación PDF en vista de reportes

---

## [1.9.12] - 2025-12-05

### Añadido
- Generación de PDF usando TCPDF (pdflib.php de Moodle)

---

## [1.9.11] - 2025-12-05

### Eliminado
- `jobboard_admin_tour.json`: Tour genérico reemplazado por tours específicos

---

## [1.9.10] - 2025-12-05

### Eliminado
- `jobboard_applicant_tour.json`: Tour genérico reemplazado por tours específicos

---

## [1.9.9] - 2025-12-05

### Añadido
- Compilación de módulos AMD con Terser

---

## [1.9.8] - 2025-12-05

### Añadido
- Cobertura completa de strings de idioma para EN

---

## [1.9.7] - 2025-12-05

### Añadido
- Cobertura completa de strings de idioma para ES

---

## [1.9.6] - 2025-12-05

### Corregido
- Errores de ESLint en módulos AMD

---

## [1.9.5] - 2025-12-05

### Corregido
- Warnings de sintaxis en archivos JavaScript

---

## [1.9.4] - 2025-12-04

### Añadido
- User Tours iniciales para el plugin (versión genérica)

---

## [1.9.3] - 2025-12-04

### Añadido
- Integración básica con tool_usertours

---

## [1.9.2] - 2025-12-01

### Añadido
- Endpoints REST para vacantes

---

## [1.9.1] - 2025-12-01

### Añadido
- Endpoints REST para aplicaciones

---

## [1.9.0] - 2025-12-01

### Añadido
- Endpoints REST para documentos

---

## [1.8.9] - 2025-12-01

### Añadido
- Sistema de tokens API con gestión de permisos

---

## [1.8.8] - 2025-12-01

### Añadido
- Rate limiting configurable para API

---

## [1.8.7] - 2025-12-01

### Añadido
- Documentación OpenAPI (Swagger)

---

## [1.8.6] - 2025-12-01

### Añadido
- Sistema de auditoría: Registro de acciones

---

## [1.8.5] - 2025-12-01

### Añadido
- Exportación de logs de auditoría

---

## [1.8.4] - 2025-12-01

### Añadido
- Filtros de auditoría por usuario, acción y fecha

---

## [1.8.3] - 2025-12-01

### Añadido
- Plantillas de email personalizables

---

## [1.8.2] - 2025-12-01

### Añadido
- Notificaciones automáticas por cambio de estado

---

## [1.8.1] - 2025-12-01

### Añadido
- Cola de mensajes con tareas programadas (adhoc tasks)

---

## [1.8.0] - 2025-11-25

### Añadido
- Gestión de excepciones ISER: Importación masiva desde CSV

---

## [1.7.9] - 2025-11-25

### Añadido
- Tipos de excepción configurables

---

## [1.7.8] - 2025-11-25

### Añadido
- Validación automática de documentos exceptuados

---

## [1.7.7] - 2025-11-25

### Añadido
- Comité de selección: Asignación de revisores a vacantes

---

## [1.7.6] - 2025-11-25

### Añadido
- Sistema de calificación de candidatos

---

## [1.7.5] - 2025-11-25

### Añadido
- Consenso de evaluación en comité

---

## [1.7.4] - 2025-11-20

### Añadido
- Sistema de entrevistas: Programación

---

## [1.7.3] - 2025-11-20

### Añadido
- Notificaciones a candidatos para entrevistas

---

## [1.7.2] - 2025-11-20

### Añadido
- Registro de resultados de entrevistas

---

## [1.7.1] - 2025-11-20

### Añadido
- Workflow de 8 estados: draft, submitted, under_review, shortlisted

---

## [1.7.0] - 2025-11-20

### Añadido
- Estados adicionales: interview, selected, hired, rejected

---

## [1.6.9] - 2025-11-15

### Añadido
- Validación de documentos: Checklist específico por tipo

---

## [1.6.8] - 2025-11-15

### Añadido
- Estados de documentos: pending, valid, rejected

---

## [1.6.7] - 2025-11-15

### Añadido
- Razones de rechazo predefinidas para documentos

---

## [1.6.6] - 2025-11-15

### Añadido
- Notas de validación en documentos

---

## [1.6.5] - 2025-11-15

### Añadido
- Privacy API (GDPR): Proveedor de privacidad

---

## [1.6.4] - 2025-11-15

### Añadido
- Exportación de datos personales (Privacy API)

---

## [1.6.3] - 2025-11-15

### Añadido
- Eliminación de datos bajo solicitud (Privacy API)

---

## [1.6.2] - 2025-11-10

### Añadido
- Dashboard con estadísticas de vacantes

---

## [1.6.1] - 2025-11-10

### Añadido
- Reportes por vacante

---

## [1.6.0] - 2025-11-10

### Añadido
- Reportes por período

---

## [1.5.9] - 2025-11-10

### Añadido
- Exportación de reportes a CSV

---

## [1.5.8] - 2025-11-10

### Añadido
- Exportación de reportes a Excel

---

## [1.5.7] - 2025-11-10

### Añadido
- Encriptación AES-256 para documentos sensibles

---

## [1.5.6] - 2025-11-10

### Añadido
- Backup de claves de encriptación

---

## [1.5.5] - 2025-11-10

### Añadido
- Configuración de encriptación por tipo de documento

---

## [1.5.4] - 2025-11-05

### Añadido
- Carga múltiple de documentos

---

## [1.5.3] - 2025-11-05

### Añadido
- Validación de tipos MIME en documentos

---

## [1.5.2] - 2025-11-05

### Añadido
- Límites de tamaño configurables para documentos

---

## [1.5.1] - 2025-11-05

### Añadido
- Preview de imágenes en documentos

---

## [1.5.0] - 2025-11-05

### Añadido
- 18 tipos de documentos predefinidos (contexto colombiano)
  - sigep, bienes_rentas, cedula
  - titulo_pregrado, titulo_postgrado, titulo_especializacion
  - titulo_maestria, titulo_doctorado, acta_grado
  - tarjeta_profesional, libreta_militar, rut
  - eps, pension, certificado_medico
  - antecedentes_procuraduria, antecedentes_contraloria, antecedentes_policia

---

## [1.4.9] - 2025-11-05

### Añadido
- Configuración de documentos requeridos/opcionales por vacante

---

## [1.4.8] - 2025-11-05

### Añadido
- Fechas de vencimiento para documentos

---

## [1.4.7] - 2025-11-01

### Añadido
- Formulario de postulación

---

## [1.4.6] - 2025-11-01

### Añadido
- Vista "Mis postulaciones"

---

## [1.4.5] - 2025-11-01

### Añadido
- Estados de postulación básicos

---

## [1.4.4] - 2025-11-01

### Añadido
- Historial de cambios en postulaciones

---

## [1.4.3] - 2025-11-01

### Añadido
- Multi-tenant (IOMAD): Filtrado por companyid

---

## [1.4.2] - 2025-11-01

### Añadido
- Permisos por empresa (IOMAD)

---

## [1.4.1] - 2025-11-01

### Añadido
- Vacantes por sede (IOMAD)

---

## [1.4.0] - 2025-10-30

### Añadido
- Gestión de vacantes: Crear vacantes

---

## [1.3.0] - 2025-10-30

### Añadido
- Gestión de vacantes: Editar vacantes

---

## [1.2.0] - 2025-10-30

### Añadido
- Gestión de vacantes: Eliminar vacantes

---

## [1.1.0] - 2025-10-28

### Añadido
- Estados de vacantes: draft, open, closed, cancelled

---

## [1.0.9] - 2025-10-28

### Añadido
- Fechas de apertura y cierre en vacantes

---

## [1.0.8] - 2025-10-28

### Añadido
- Requisitos y descripción en vacantes

---

## [1.0.7] - 2025-10-27

### Añadido
- Estructura de base de datos: 21 tablas
  - local_jobboard_vacancy
  - local_jobboard_application
  - local_jobboard_document
  - local_jobboard_doctype
  - local_jobboard_vacancy_doctype
  - local_jobboard_log
  - local_jobboard_reviewer
  - local_jobboard_exemption
  - local_jobboard_interview
  - local_jobboard_convocatoria
  - (y 11 tablas adicionales)

---

## [1.0.6] - 2025-10-27

### Añadido
- Índices optimizados en base de datos

---

## [1.0.5] - 2025-10-27

### Añadido
- Claves foráneas en base de datos

---

## [1.0.4] - 2025-10-26

### Añadido
- 15 capabilities iniciales

---

## [1.0.3] - 2025-10-26

### Añadido
- Roles predefinidos sugeridos

---

## [1.0.2] - 2025-10-25

### Añadido
- Idioma español (es) - idioma principal

---

## [1.0.1] - 2025-10-25

### Añadido
- Idioma inglés (en) - traducción completa

---

## [1.0.0] - 2025-10-25

### Añadido
- **Versión inicial del plugin local_jobboard**
- Estructura básica del plugin compatible con Moodle 4.1+
- Archivo version.php con metadatos del plugin
- Integración base con IOMAD para multi-tenancy
- lib.php con callbacks básicos de Moodle
- index.php como punto de entrada
- settings.php para configuración administrativa

---

## Leyenda

| Tipo | Descripción |
|------|-------------|
| **Añadido** | Nuevas funcionalidades |
| **Modificado** | Cambios en funcionalidades existentes |
| **Obsoleto** | Funcionalidades que serán eliminadas próximamente |
| **Eliminado** | Funcionalidades eliminadas |
| **Corregido** | Corrección de errores |
| **Seguridad** | Vulnerabilidades corregidas |

---

## Versiones Futuras (Roadmap)

### [2.2.0] - Planificado

- [ ] Migración completa de iconos Font Awesome a pix_icon
- [ ] Integración con sistemas de nómina
- [ ] Firmas digitales de documentos

### [2.3.0] - Planificado

- [ ] App móvil para postulantes
- [ ] Dashboard analítico avanzado
- [ ] Inteligencia artificial para matching de candidatos

# Changelog

Todos los cambios notables de este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Versionado Semántico](https://semver.org/lang/es/).

## [1.9.2-beta] - 2025-12-05

### Corregido
- **Error de sesskey en manage.php**
  - Corregido: `confirm_sesskey()` se llamaba incluso al cargar la página sin acciones
  - Ahora usa `require_sesskey()` solo cuando hay una acción que modificar datos
- **TypeError en vacancy.php**
  - Corregido: `local_jobboard_get_user_companyid()` recibía string en lugar de int
  - Ahora convierte explícitamente `$filters['userid']` a int antes de pasarlo

## [1.9.1-beta] - 2025-12-05

### Añadido
- **Conversión de documentos para previsualización**
  - Nueva clase `document_services.php` para conversión de documentos
  - Integración con `\core_files\converter` de Moodle (unoconv/LibreOffice)
  - Soporte para formatos: DOC, DOCX, XLS, XLSX, PPT, PPTX, ODT, ODS, ODP, RTF, TXT, HTML
  - Endpoint AJAX `ajax_conversion.php` para polling de estado de conversión
  - Indicador visual de estado de conversión (badge) en tiempo real
  - Previsualización automática de documentos convertidos a PDF

### Modificado
- `validate_document.php`: Integración completa con sistema de conversión
- Strings de idioma: Nuevos mensajes para estados de conversión (EN/ES)

## [1.9.0-beta] - 2025-12-05

### Añadido
- **8 User Tours específicos por vista**
  - `tour_dashboard.json`: Tour del panel principal (5 pasos)
  - `tour_public.json`: Tour de vacantes públicas (9 pasos)
  - `tour_apply.json`: Tour del formulario de postulación (8 pasos)
  - `tour_myapplications.json`: Tour de mis postulaciones (7 pasos)
  - `tour_review.json`: Tour de revisión de documentos (9 pasos)
  - `tour_manage.json`: Tour de gestión de vacantes (9 pasos)
  - `tour_reports.json`: Tour de reportes (8 pasos)
  - `tour_validate_document.json`: Tour de validación de documentos (8 pasos)

- **Módulo AMD document_preview.js**
  - Visor PDF.js modal con zoom, rotación y paginación
  - Soporte para imágenes (JPEG, PNG, GIF, WebP)
  - Carga dinámica de PDF.js desde CDN
  - Controles de navegación de páginas

- **Exportación ZIP de documentos**
  - `export_documents.php`: Exportación de documentos como archivo ZIP
  - Exportación individual por aplicación
  - Exportación masiva por vacante (todos los aplicantes)
  - Estructura de carpetas: APPLICANT/DOCTYPE/filename

- **Exportación PDF de reportes**
  - Nuevo botón de exportación PDF en vista de reportes
  - Generación usando TCPDF (pdflib.php de Moodle)
  - Formatos disponibles: CSV, Excel, PDF

### Eliminado
- `jobboard_admin_tour.json`: Tour genérico reemplazado por tours específicos
- `jobboard_applicant_tour.json`: Tour genérico reemplazado por tours específicos

## [1.7.8-beta] - 2025-12-05

### Añadido
- Compilación de módulos AMD con Terser
- Cobertura completa de strings de idioma para EN y ES

### Corregido
- Errores de ESLint en módulos AMD
- Warnings de sintaxis en archivos JavaScript

## [1.7.7-beta] - 2025-12-04

### Añadido
- User Tours iniciales para el plugin (versión genérica)
- Integración básica con tool_usertours

## [1.7.0-beta] - 2025-12-01

### Añadido
- **API REST completa**
  - Endpoints para vacantes, aplicaciones y documentos
  - Sistema de tokens API con gestión de permisos
  - Rate limiting configurable
  - Documentación OpenAPI

- **Sistema de auditoría**
  - Registro de todas las acciones del sistema
  - Exportación de logs de auditoría
  - Filtros por usuario, acción y fecha

- **Notificaciones por email**
  - Plantillas personalizables
  - Notificaciones automáticas por cambio de estado
  - Cola de mensajes con tareas programadas

## [1.6.0-beta] - 2025-11-25

### Añadido
- **Gestión de excepciones ISER**
  - Importación masiva desde CSV
  - Tipos de excepción configurables
  - Validación automática de documentos exceptuados

- **Comité de selección**
  - Asignación de revisores a vacantes
  - Sistema de calificación de candidatos
  - Consenso de evaluación

## [1.5.0-beta] - 2025-11-20

### Añadido
- **Sistema de entrevistas**
  - Programación de entrevistas
  - Notificaciones a candidatos
  - Registro de resultados

- **Workflow de 8 estados**
  - draft → submitted → under_review → shortlisted
  - interview → selected → hired
  - rejected (en cualquier punto)

## [1.4.0-beta] - 2025-11-15

### Añadido
- **Validación de documentos**
  - Checklist específico por tipo de documento
  - Estados: pending, valid, rejected
  - Razones de rechazo predefinidas
  - Notas de validación

- **Privacy API (GDPR)**
  - Proveedor de privacidad completo
  - Exportación de datos personales
  - Eliminación de datos bajo solicitud

## [1.3.0-beta] - 2025-11-10

### Añadido
- **Reportes y métricas**
  - Dashboard con estadísticas
  - Reportes por vacante
  - Reportes por período
  - Exportación CSV y Excel

- **Encriptación de documentos**
  - Encriptación AES-256 para documentos sensibles
  - Backup de claves de encriptación
  - Configuración por tipo de documento

## [1.2.0-beta] - 2025-11-05

### Añadido
- **Gestión de documentos**
  - Carga múltiple de documentos
  - Validación de tipos MIME
  - Límites de tamaño configurables
  - Preview de imágenes

- **18 tipos de documentos predefinidos**
  - Documentos colombianos específicos
  - Configuración de requeridos/opcionales
  - Fechas de vencimiento

## [1.1.0-beta] - 2025-11-01

### Añadido
- **Sistema de postulaciones**
  - Formulario de postulación
  - Vista "Mis postulaciones"
  - Estados de postulación
  - Historial de cambios

- **Multi-tenant (IOMAD)**
  - Filtrado por companyid
  - Permisos por empresa
  - Vacantes por sede

## [1.0.0-beta] - 2025-10-25

### Añadido
- **Versión inicial del plugin**
- Gestión básica de vacantes
  - Crear, editar, eliminar vacantes
  - Estados: draft, open, closed, cancelled
  - Fechas de apertura y cierre
  - Requisitos y descripción

- **Estructura de base de datos**
  - 21 tablas para el sistema completo
  - Índices optimizados
  - Claves foráneas

- **Capabilities**
  - 15 permisos granulares
  - Roles predefinidos sugeridos

- **Idiomas**
  - Español (es) - idioma principal
  - Inglés (en) - traducción completa

---

## Tipos de cambios

- **Añadido** para nuevas funcionalidades.
- **Modificado** para cambios en funcionalidades existentes.
- **Obsoleto** para funcionalidades que serán eliminadas próximamente.
- **Eliminado** para funcionalidades eliminadas.
- **Corregido** para corrección de errores.
- **Seguridad** para vulnerabilidades corregidas.

## Versiones futuras planificadas

### [2.0.0] - Planificado
- [ ] Integración con sistemas de nómina
- [ ] Firmas digitales de documentos
- [ ] Verificación automática de antecedentes
- [ ] App móvil para postulantes
- [ ] Dashboard analítico avanzado

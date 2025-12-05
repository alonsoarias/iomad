# Bolsa de Empleo - Moodle Job Board Plugin

[![Moodle 4.1+](https://img.shields.io/badge/Moodle-4.1%2B-orange.svg)](https://moodle.org)
[![PHP 7.4+](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net)
[![License GPL-3.0](https://img.shields.io/badge/License-GPL%20v3-green.svg)](http://www.gnu.org/copyleft/gpl.html)
[![Version 1.9.1](https://img.shields.io/badge/Version-1.9.1--beta-brightgreen.svg)]()

Plugin para Moodle 4.1+ que implementa un sistema completo de gestión de vacantes y recepción de hojas de vida para profesores cátedra de instituciones educativas. Compatible con IOMAD multi-tenant.

## Descripción

El plugin **local_jobboard** proporciona un sistema integral para:

- **Gestión de vacantes**: Crear, editar, publicar y cerrar ofertas laborales
- **Postulaciones**: Recepción y seguimiento de aplicaciones de candidatos
- **Documentación**: Carga, validación y previsualización de documentos
- **Workflow**: Flujo de trabajo de 8 estados desde postulación hasta selección
- **Reportes**: Informes con exportación a CSV, Excel y PDF
- **Multi-tenant**: Soporte completo para Iomad (múltiples empresas/sedes)

## Características Principales

### Para Postulantes
- Ver vacantes publicadas disponibles
- Postularse con documentos requeridos
- Consultar estado de postulaciones
- Reenviar documentos rechazados
- Soporte para excepciones ISER (personal histórico)

### Para Gestores
- Crear y administrar vacantes
- Configurar documentos requeridos por vacante
- Revisar postulaciones y documentos
- **Previsualización inline de documentos** (PDF, imágenes, Office)
- Validar documentos con checklist predefinido
- Gestionar workflow de selección
- **Exportar documentos como ZIP**
- Notificaciones automáticas por email

### Para Administradores
- Configuración global del sistema
- Gestión de tipos de documentos (18 tipos predefinidos)
- Plantillas de correo personalizables
- Gestión de excepciones ISER
- API REST para integraciones
- **Reportes con exportación PDF/Excel/CSV**
- Auditoría completa de acciones

## Novedades v1.9.1

### Conversión de Documentos para Previsualización
- Integración con convertidores de Moodle (unoconv/LibreOffice)
- Conversión automática de documentos Office a PDF
- Formatos soportados: DOC, DOCX, XLS, XLSX, PPT, PPTX, ODT, ODS, ODP, RTF, TXT, HTML
- Indicador de estado de conversión en tiempo real

### User Tours Interactivos
- 8 tours guiados para cada vista del plugin
- Selectores CSS específicos para elementos de UI
- Disponibles en español e inglés

### Exportación de Documentos
- Exportación ZIP de documentos por aplicación
- Exportación masiva de todos los documentos de una vacante
- Exportación de reportes en PDF

## Compatibilidad

| Componente | Versión |
|------------|---------|
| Moodle | 4.1 LTS, 4.2, 4.3, 4.4, 4.5 |
| Iomad | 4.1+ |
| PHP | 7.4+ (8.0+ recomendado) |
| Base de datos | PostgreSQL 12+ o MySQL 8.0+ |

## Instalación

Ver [INSTALL.md](INSTALL.md) para instrucciones detalladas.

### Instalación Rápida

```bash
# Clonar en el directorio de plugins locales
cd /path/to/moodle/local
git clone https://github.com/tu-repo/jobboard.git

# Acceder a Moodle como administrador
# Seguir el proceso de actualización de plugins
# Configurar permisos según sea necesario
```

### Requisitos para Conversión de Documentos

Para habilitar la previsualización de documentos Office:

1. Instalar unoconv en el servidor:
   ```bash
   # Ubuntu/Debian
   sudo apt-get install unoconv libreoffice

   # CentOS/RHEL
   sudo yum install unoconv libreoffice
   ```

2. Configurar en Moodle:
   - Ir a: Site administration > Plugins > Document converters
   - Habilitar "Unoconv" y configurar la ruta

## Documentos Predefinidos

El sistema incluye 18 tipos de documentos específicos para profesores cátedra en Colombia:

| Documento | Código |
|-----------|--------|
| Formato Único Hoja de Vida SIGEP II | `sigep` |
| Declaración de Bienes y Rentas | `bienes_rentas` |
| Cédula de Ciudadanía | `cedula` |
| Título Pregrado | `titulo_pregrado` |
| Título Postgrado | `titulo_postgrado` |
| Título Especialización | `titulo_especializacion` |
| Título Maestría | `titulo_maestria` |
| Título Doctorado | `titulo_doctorado` |
| Acta de Grado | `acta_grado` |
| Tarjeta Profesional | `tarjeta_profesional` |
| Libreta Militar | `libreta_militar` |
| RUT | `rut` |
| Certificado EPS | `eps` |
| Certificado Pensión | `pension` |
| Certificado Médico | `certificado_medico` |
| Antecedentes Procuraduría | `antecedentes_procuraduria` |
| Antecedentes Contraloría | `antecedentes_contraloria` |
| Antecedentes Policía | `antecedentes_policia` |

## Estados del Workflow

```
┌─────────────┐     ┌─────────────┐     ┌─────────────────┐
│   draft     │────▶│  submitted  │────▶│  under_review   │
└─────────────┘     └─────────────┘     └─────────────────┘
                                                │
                          ┌─────────────────────┼─────────────────────┐
                          ▼                     ▼                     ▼
                  ┌───────────────┐    ┌───────────────┐    ┌───────────────┐
                  │   shortlisted │    │   interview   │    │   rejected    │
                  └───────────────┘    └───────────────┘    └───────────────┘
                          │                     │
                          └──────────┬──────────┘
                                     ▼
                            ┌───────────────┐
                            │   selected    │
                            └───────────────┘
                                     │
                                     ▼
                            ┌───────────────┐
                            │   hired       │
                            └───────────────┘
```

## Estructura del Plugin

```
local/jobboard/
├── classes/
│   ├── application.php      # Gestión de postulaciones
│   ├── vacancy.php          # Gestión de vacantes
│   ├── document.php         # Gestión de documentos
│   ├── document_services.php # Conversión de documentos
│   ├── audit.php            # Sistema de auditoría
│   ├── api/                 # Endpoints REST API
│   ├── event/               # Eventos del sistema
│   ├── forms/               # Formularios Moodle
│   ├── privacy/             # Proveedor GDPR
│   └── task/                # Tareas programadas
├── views/
│   ├── dashboard.php        # Panel principal
│   ├── public.php           # Vacantes públicas
│   ├── apply.php            # Formulario postulación
│   ├── applications.php     # Mis postulaciones
│   ├── manage.php           # Gestión de vacantes
│   ├── review.php           # Revisión de documentos
│   └── reports.php          # Reportes y métricas
├── db/
│   ├── install.xml          # Esquema 21 tablas
│   ├── access.php           # Capabilities
│   ├── tours/               # 8 User Tours JSON
│   └── services.php         # Web services
├── amd/
│   └── src/
│       ├── document_preview.js  # Preview PDF.js
│       ├── document_upload.js   # Upload con drag&drop
│       └── vacancy_filter.js    # Filtros dinámicos
├── lang/
│   ├── en/                  # 1332 strings
│   └── es/                  # 1357 strings
├── templates/               # Plantillas Mustache
├── tests/                   # PHPUnit tests
├── export_documents.php     # Exportación ZIP
├── ajax_conversion.php      # AJAX conversión
└── validate_document.php    # Validación documentos
```

## API REST

El plugin incluye una API REST completa:

```http
# Autenticación via token
Authorization: Bearer {api_token}

# Endpoints
GET  /local/jobboard/api/v1/vacancies
GET  /local/jobboard/api/v1/vacancies/{id}
POST /local/jobboard/api/v1/applications
GET  /local/jobboard/api/v1/applications/{id}
POST /local/jobboard/api/v1/applications/{id}/documents
GET  /local/jobboard/api/v1/documents/{id}
```

Ver [API.md](API.md) para documentación completa.

## Seguridad

- Encriptación de documentos sensibles
- Validación de tipos MIME
- Control de acceso basado en capabilities
- Auditoría de todas las acciones
- Cumplimiento GDPR/Privacy API

Ver [SECURITY.md](SECURITY.md) para más detalles.

## Cumplimiento Normativo

- **Ley 1581/2012** (Habeas Data - Colombia)
- **GDPR** (Privacy API de Moodle)
- Consentimiento informado obligatorio
- Registro de auditoría completo
- Política de retención de datos configurable

## Contribuir

Las contribuciones son bienvenidas:

1. Fork del repositorio
2. Crear rama feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit de cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

## Testing

```bash
# Ejecutar tests unitarios
vendor/bin/phpunit local/jobboard/tests/

# Ejecutar con cobertura
vendor/bin/phpunit --coverage-html coverage local/jobboard/tests/
```

## Licencia

Este plugin está licenciado bajo [GNU GPL v3](http://www.gnu.org/copyleft/gpl.html).

## Soporte

Para reportar problemas o solicitar funcionalidades, crear un issue en el repositorio.

## Créditos

Desarrollado para **ISER** - Instituto Superior de Educación Rural.

Sistema de gestión de vacantes para profesores cátedra.

---

**Versión actual**: 1.9.1-beta
**Última actualización**: Diciembre 2025

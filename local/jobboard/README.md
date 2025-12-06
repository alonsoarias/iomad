# local_jobboard - Sistema de Bolsa de Empleo para Moodle

[![Moodle 4.1+](https://img.shields.io/badge/Moodle-4.1%2B-orange.svg)](https://moodle.org)
[![PHP 7.4+](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net)
[![License GPL-3.0](https://img.shields.io/badge/License-GPL%20v3-green.svg)](http://www.gnu.org/copyleft/gpl.html)
[![Version 2.0.15](https://img.shields.io/badge/Version-2.0.15-brightgreen.svg)]()
[![IOMAD Compatible](https://img.shields.io/badge/IOMAD-Compatible-blue.svg)]()

Plugin para Moodle que implementa un sistema completo de gestión de convocatorias, vacantes y postulaciones para profesores cátedra. Diseñado específicamente para instituciones educativas en Colombia, compatible con IOMAD multi-tenant.

---

## Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Características](#características)
3. [Requisitos del Sistema](#requisitos-del-sistema)
4. [Instalación](#instalación)
5. [Configuración](#configuración)
6. [Guía de Uso](#guía-de-uso)
7. [Tipos de Documentos](#tipos-de-documentos)
8. [Estados del Workflow](#estados-del-workflow)
9. [API REST](#api-rest)
10. [Roles y Permisos](#roles-y-permisos)
11. [Cumplimiento Normativo](#cumplimiento-normativo)
12. [Estructura del Plugin](#estructura-del-plugin)
13. [Desarrollo y Testing](#desarrollo-y-testing)
14. [Changelog](#changelog)
15. [Licencia](#licencia)
16. [Créditos](#créditos)

---

## Descripción General

**local_jobboard** es un plugin de tipo local para Moodle 4.1+ que proporciona:

- **Sistema de Convocatorias**: Organización jerárquica donde cada convocatoria agrupa múltiples vacantes
- **Gestión de Vacantes**: Creación, publicación y cierre de ofertas laborales
- **Portal de Postulaciones**: Interfaz pública para que candidatos apliquen a vacantes
- **Validación de Documentos**: Revisión manual con checklist predefinido por tipo de documento
- **Workflow de Selección**: 8 estados desde postulación hasta contratación
- **Reportes y Exportación**: Informes en CSV, Excel y PDF
- **Compatibilidad IOMAD**: Soporte multi-tenant para múltiples empresas/sedes

---

## Características

### Para Postulantes

| Funcionalidad | Descripción |
|---------------|-------------|
| Vista pública de convocatorias | Explorar convocatorias y vacantes abiertas |
| Registro y postulación | Aplicar con carga de documentos requeridos |
| Seguimiento de estado | Consultar progreso de postulaciones |
| Reenvío de documentos | Cargar nuevamente documentos rechazados |
| Soporte de excepciones | Personal histórico (ISER) con requisitos reducidos |

### Para Revisores de Documentos

| Funcionalidad | Descripción |
|---------------|-------------|
| Cola de validación | Documentos pendientes organizados por tipo |
| Previsualización inline | Visor PDF/imágenes sin descargar |
| Checklist de validación | Criterios predefinidos por tipo de documento |
| Validación masiva | Aprobar/rechazar múltiples documentos |
| Notas y razones de rechazo | Feedback estructurado para candidatos |

### Para Coordinadores

| Funcionalidad | Descripción |
|---------------|-------------|
| Gestión de convocatorias | Crear, editar y cerrar convocatorias |
| Gestión de vacantes | Configurar requisitos y documentos por vacante |
| Asignación de revisores | Distribuir trabajo entre equipo |
| Programación de entrevistas | Agendar y notificar candidatos |
| Workflow de selección | Mover candidatos entre estados |

### Para Administradores

| Funcionalidad | Descripción |
|---------------|-------------|
| Configuración global | Tipos de documentos, plantillas de email |
| Gestión de excepciones | Importación masiva desde CSV |
| API REST | Integración con sistemas externos |
| Auditoría | Registro completo de acciones |
| Reportes | Estadísticas con exportación múltiple |

---

## Requisitos del Sistema

| Componente | Versión Mínima | Recomendada |
|------------|----------------|-------------|
| Moodle | 4.1 LTS | 4.3+ |
| PHP | 7.4 | 8.1+ |
| PostgreSQL | 12 | 14+ |
| MySQL/MariaDB | 8.0 / 10.4 | 8.0 / 10.6 |

### Dependencias Opcionales

| Componente | Propósito |
|------------|-----------|
| LibreOffice + unoconv | Conversión de documentos Office a PDF |
| IOMAD 4.1+ | Multi-tenancy (empresas/sedes) |

---

## Instalación

### Instalación Manual

```bash
# 1. Clonar el repositorio
cd /path/to/moodle/local
git clone https://github.com/tu-org/jobboard.git

# 2. Acceder como administrador a Moodle
# 3. Ir a: Site administration > Notifications
# 4. Seguir el proceso de instalación del plugin
```

### Post-Instalación

1. **Configurar permisos**: Asignar capabilities a roles existentes o usar los roles predefinidos
2. **Configurar documentos**: Revisar tipos de documentos en settings
3. **Configurar conversión**: Si usa documentos Office, configurar unoconv

### Configuración de unoconv (Opcional)

```bash
# Ubuntu/Debian
sudo apt-get install unoconv libreoffice

# CentOS/RHEL
sudo yum install unoconv libreoffice

# Configurar en Moodle:
# Site administration > Plugins > Document converters > Enable "Unoconv"
```

---

## Configuración

### Configuración General

Acceder a: **Site administration > Plugins > Local plugins > Job Board**

| Opción | Descripción |
|--------|-------------|
| Tamaño máximo de archivos | Límite para documentos (default: 10MB) |
| Tipos MIME permitidos | Formatos aceptados para documentos |
| Días de retención | Tiempo para mantener documentos rechazados |
| Notificaciones por email | Habilitar/deshabilitar emails automáticos |

### Tipos de Documentos

El plugin incluye 18 tipos de documentos predefinidos para el contexto colombiano. Se pueden agregar más desde la configuración.

---

## Guía de Uso

### Flujo de Trabajo Típico

```
1. Administrador crea Convocatoria
   └── 2. Agrega Vacantes a la convocatoria
       └── 3. Configura documentos requeridos por vacante
           └── 4. Publica convocatoria/vacantes

5. Postulante encuentra vacante pública
   └── 6. Se registra o inicia sesión
       └── 7. Aplica cargando documentos requeridos

8. Revisor valida documentos
   └── 9. Aprueba o rechaza con feedback
       └── 10. Sistema actualiza estado de postulación

11. Coordinador revisa candidatos aptos
    └── 12. Agenda entrevistas
        └── 13. Marca candidatos seleccionados/rechazados
```

### Vistas Principales

| Vista | URL | Propósito |
|-------|-----|-----------|
| Dashboard | /local/jobboard/ | Panel principal según rol |
| Convocatorias públicas | /local/jobboard/index.php?view=public | Portal público |
| Mis postulaciones | /local/jobboard/index.php?view=applications | Para postulantes |
| Gestión | /local/jobboard/index.php?view=manage | Para coordinadores |
| Reportes | /local/jobboard/index.php?view=reports | Estadísticas |

---

## Tipos de Documentos

### Documentos Predefinidos (Colombia)

| Código | Documento | Requerido |
|--------|-----------|-----------|
| `sigep` | Formato Único Hoja de Vida SIGEP II | Sí |
| `bienes_rentas` | Declaración de Bienes y Rentas | Sí |
| `cedula` | Cédula de Ciudadanía | Sí |
| `titulo_pregrado` | Título de Pregrado | Sí |
| `titulo_postgrado` | Título de Postgrado | Según vacante |
| `titulo_especializacion` | Título de Especialización | Según vacante |
| `titulo_maestria` | Título de Maestría | Según vacante |
| `titulo_doctorado` | Título de Doctorado | Según vacante |
| `acta_grado` | Acta de Grado | Sí |
| `tarjeta_profesional` | Tarjeta Profesional | Según área |
| `libreta_militar` | Libreta Militar | Solo hombres |
| `rut` | RUT | Sí |
| `eps` | Certificado de EPS | Sí |
| `pension` | Certificado de Pensión | Sí |
| `certificado_medico` | Certificado Médico Ocupacional | Sí |
| `antecedentes_procuraduria` | Antecedentes Procuraduría | Sí |
| `antecedentes_contraloria` | Antecedentes Contraloría | Sí |
| `antecedentes_policia` | Antecedentes Policía | Sí |

---

## Estados del Workflow

### Diagrama de Estados

```
┌──────────────────────────────────────────────────────────────────┐
│                         POSTULACIÓN                              │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│   ┌─────────┐    ┌───────────┐    ┌──────────────┐              │
│   │  draft  │───▶│ submitted │───▶│ under_review │              │
│   └─────────┘    └───────────┘    └──────────────┘              │
│                                          │                       │
│                    ┌─────────────────────┼─────────────────────┐ │
│                    ▼                     ▼                     ▼ │
│            ┌──────────────┐     ┌──────────────┐     ┌─────────┐ │
│            │docs_validated│     │ docs_rejected│     │withdrawn│ │
│            └──────────────┘     └──────────────┘     └─────────┘ │
│                    │                                             │
│                    ▼                                             │
│            ┌──────────────┐                                      │
│            │  interview   │                                      │
│            └──────────────┘                                      │
│                    │                                             │
│          ┌─────────┴─────────┐                                   │
│          ▼                   ▼                                   │
│   ┌──────────────┐   ┌──────────────┐                           │
│   │   selected   │   │   rejected   │                           │
│   └──────────────┘   └──────────────┘                           │
│          │                                                       │
│          ▼                                                       │
│   ┌──────────────┐                                              │
│   │    hired     │                                              │
│   └──────────────┘                                              │
└──────────────────────────────────────────────────────────────────┘
```

### Descripción de Estados

| Estado | Descripción |
|--------|-------------|
| `draft` | Borrador, postulación no enviada |
| `submitted` | Enviada, pendiente de revisión |
| `under_review` | En proceso de revisión de documentos |
| `docs_validated` | Todos los documentos aprobados |
| `docs_rejected` | Uno o más documentos rechazados |
| `interview` | Programada para entrevista |
| `selected` | Seleccionado para el cargo |
| `rejected` | Rechazado del proceso |
| `withdrawn` | Retirado por el postulante |
| `hired` | Contratado |

---

## API REST

### Autenticación

```http
Authorization: Bearer {token}
Content-Type: application/json
```

### Endpoints Disponibles

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/local/jobboard/api/v1/vacancies` | Listar vacantes |
| GET | `/local/jobboard/api/v1/vacancies/{id}` | Detalle de vacante |
| POST | `/local/jobboard/api/v1/applications` | Crear postulación |
| GET | `/local/jobboard/api/v1/applications/{id}` | Detalle de postulación |
| POST | `/local/jobboard/api/v1/applications/{id}/documents` | Subir documento |
| GET | `/local/jobboard/api/v1/documents/{id}` | Descargar documento |

### Ejemplo de Uso

```bash
# Listar vacantes publicadas
curl -H "Authorization: Bearer $TOKEN" \
     https://moodle.example.com/local/jobboard/api/v1/vacancies

# Crear postulación
curl -X POST \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     -d '{"vacancyid": 123}' \
     https://moodle.example.com/local/jobboard/api/v1/applications
```

---

## Roles y Permisos

### Roles Predefinidos

| Rol | Archetype | Descripción |
|-----|-----------|-------------|
| `jobboard_reviewer` | user | Revisor de documentos |
| `jobboard_coordinator` | editingteacher | Coordinador de selección |
| `jobboard_committee` | teacher | Miembro de comité |

### Capabilities (34 totales)

| Capability | Descripción |
|------------|-------------|
| `local/jobboard:view` | Ver el plugin |
| `local/jobboard:apply` | Postularse a vacantes |
| `local/jobboard:viewownapplications` | Ver propias postulaciones |
| `local/jobboard:createvacancy` | Crear vacantes |
| `local/jobboard:editvacancy` | Editar vacantes |
| `local/jobboard:deletevacancy` | Eliminar vacantes |
| `local/jobboard:reviewdocuments` | Revisar documentos |
| `local/jobboard:validatedocuments` | Validar/rechazar documentos |
| `local/jobboard:manageworkflow` | Gestionar estados de postulación |
| `local/jobboard:viewreports` | Ver reportes |
| `local/jobboard:exportdata` | Exportar datos |
| `local/jobboard:manageexemptions` | Gestionar excepciones |
| `local/jobboard:scheduleinterview` | Programar entrevistas |
| `local/jobboard:manageconvocatorias` | Gestionar convocatorias |
| ... | (ver db/access.php para lista completa) |

---

## Cumplimiento Normativo

### Colombia

| Normativa | Cumplimiento |
|-----------|--------------|
| Ley 1581 de 2012 (Habeas Data) | Consentimiento informado, acceso a datos |
| Decreto 1377 de 2013 | Tratamiento de datos personales |
| Ley 1712 de 2014 (Transparencia) | Acceso a información pública |

### Internacional

| Normativa | Cumplimiento |
|-----------|--------------|
| GDPR (UE) | Privacy API de Moodle implementada |
| CCPA (California) | Exportación y eliminación de datos |

### Privacy API

El plugin implementa completamente la Privacy API de Moodle:

- **Exportación de datos**: Todas las postulaciones, documentos y estados
- **Eliminación de datos**: Bajo solicitud del usuario
- **Tablas cubiertas**: 10 tablas con datos personales

---

## Estructura del Plugin

```
local/jobboard/
├── classes/
│   ├── api/                    # Endpoints REST
│   ├── event/                  # Eventos del sistema
│   ├── forms/                  # Formularios Moodle
│   ├── output/                 # Renderizadores y UI
│   ├── privacy/                # Privacy API provider
│   ├── task/                   # Tareas programadas
│   ├── application.php         # Clase de postulación
│   ├── audit.php               # Sistema de auditoría
│   ├── bulk_validator.php      # Validación masiva
│   ├── convocatoria.php        # Clase de convocatoria
│   ├── document.php            # Clase de documento
│   ├── document_services.php   # Conversión de documentos
│   ├── exemption.php           # Excepciones ISER
│   ├── reviewer.php            # Gestión de revisores
│   └── vacancy.php             # Clase de vacante
├── db/
│   ├── access.php              # 34 capabilities
│   ├── install.php             # Script de instalación (roles)
│   ├── install.xml             # Esquema de BD (21 tablas)
│   ├── services.php            # Definición de web services
│   ├── upgrade.php             # Migraciones de BD
│   └── tours/                  # 15 User Tours JSON
├── views/
│   ├── application.php         # Detalle de postulación
│   ├── applications.php        # Mis postulaciones
│   ├── apply.php               # Formulario de postulación
│   ├── convocatoria.php        # Detalle de convocatoria
│   ├── dashboard.php           # Panel principal
│   ├── manage.php              # Gestión de vacantes
│   ├── myreviews.php           # Mis revisiones
│   ├── public.php              # Vista pública
│   ├── reports.php             # Reportes
│   ├── review.php              # Revisión de documentos
│   └── vacancy.php             # Detalle de vacante
├── amd/src/                    # Módulos JavaScript AMD
├── templates/                  # Plantillas Mustache
├── lang/
│   ├── en/                     # Inglés
│   └── es/                     # Español
├── tests/                      # PHPUnit tests
├── styles.css                  # Estilos del plugin
├── index.php                   # Punto de entrada
├── lib.php                     # Callbacks de Moodle
├── settings.php                # Configuración admin
└── version.php                 # Metadatos del plugin
```

---

## Desarrollo y Testing

### Ejecutar Tests

```bash
# Tests unitarios
vendor/bin/phpunit local/jobboard/tests/

# Con cobertura
vendor/bin/phpunit --coverage-html coverage local/jobboard/tests/
```

### Compilar JavaScript AMD

```bash
cd local/jobboard
npx grunt amd
```

### Code Style

```bash
# PHP CodeSniffer
vendor/bin/phpcs --standard=moodle local/jobboard

# ESLint
npx eslint amd/src/
```

---

## Changelog

Ver [CHANGELOG.md](CHANGELOG.md) para historial completo de versiones.

### Versión Actual: 2.0.2 (2025-12-06)

- Aislamiento completo de estilos de temas remui/inteb
- Overrides CSS con prefijo `.path-local-jobboard`
- README.md y CHANGELOG.md reescritos completamente

---

## Licencia

Este plugin está licenciado bajo la [GNU General Public License v3](http://www.gnu.org/copyleft/gpl.html).

```
Copyright (C) 2024-2025 ISER - Instituto Superior de Educación Rural

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
```

---

## Créditos

### Desarrollado para

**ISER** - Instituto Superior de Educación Rural

### Equipo de Desarrollo

- Desarrollo: Equipo ISER
- Diseño UX/UI: Basado en Bootstrap 4 (Moodle)

### Agradecimientos

- Comunidad Moodle
- Equipo IOMAD
- Contribuidores de código abierto

---

**Versión**: 2.0.2
**Última actualización**: Diciembre 2025
**Moodle**: 4.1 - 4.5
**Estado**: Estable

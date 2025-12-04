# Bolsa de Empleo - Moodle Job Board Plugin

Plugin para Moodle 4.1+ que implementa un sistema completo de gestión de vacantes y recepción de hojas de vida para profesores cátedra de instituciones educativas.

## Descripción

El plugin **local_jobboard** proporciona un sistema integral para:

- **Gestión de vacantes**: Crear, editar, publicar y cerrar ofertas laborales
- **Postulaciones**: Recepción y seguimiento de aplicaciones de candidatos
- **Documentación**: Carga y validación de documentos requeridos
- **Workflow**: Flujo de trabajo completo desde la postulación hasta la selección
- **Reportes**: Informes y métricas del proceso de selección
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
- Validar documentos con checklist predefinido
- Gestionar workflow de selección
- Notificaciones automáticas por email

### Para Administradores
- Configuración global del sistema
- Gestión de tipos de documentos
- Plantillas de correo personalizables
- Gestión de excepciones ISER
- API REST para integraciones
- Reportes y auditoría completa

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

1. Descargar y extraer en `/local/jobboard`
2. Acceder a Moodle como administrador
3. Seguir el proceso de actualización de plugins
4. Configurar permisos según sea necesario

## Documentos Predefinidos

El sistema incluye tipos de documentos específicos para profesores cátedra en Colombia:

- Formato Único Hoja de Vida SIGEP II
- Declaración de Bienes y Rentas
- Cédula de Ciudadanía
- Títulos Académicos
- Tarjeta Profesional
- Libreta Militar
- Certificaciones Laborales
- RUT
- Certificado EPS
- Certificado Pensión
- Certificado Cuenta Bancaria
- Antecedentes (Disciplinarios, Fiscales, Judiciales)
- Registro de Medidas Correctivas
- Consulta de Inhabilidades
- REDAM

## Excepciones ISER

Para personal histórico ISER, los siguientes documentos están exceptuados:
- Títulos Académicos
- Cédula de Ciudadanía
- Tarjeta Profesional
- Libreta Militar
- Certificaciones Laborales

## Cumplimiento Normativo

- **Ley 1581/2012** (Habeas Data - Colombia)
- Consentimiento informado obligatorio
- Registro de auditoría completo
- Política de retención de datos configurable

## Estructura del Plugin

```
local/jobboard/
├── classes/           # Clases PHP (vacancy, audit, events, forms)
├── db/                # Esquema de BD y capabilities
├── lang/              # Archivos de idioma (es, en)
├── templates/         # Plantillas Mustache
├── tests/             # PHPUnit y Behat tests
├── lib.php            # Funciones principales
├── version.php        # Información de versión
└── index.php          # Página principal
```

## API REST

El plugin incluye una API REST para integraciones externas:

- `GET /vacancies` - Listar vacantes
- `GET /vacancies/{id}` - Detalle de vacante
- `POST /applications` - Crear postulación
- `POST /applications/{id}/documents` - Subir documento
- `GET /applications/{id}` - Estado de postulación

## Contribuir

Las contribuciones son bienvenidas. Por favor:

1. Fork del repositorio
2. Crear rama feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit de cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

## Licencia

Este plugin está licenciado bajo [GNU GPL v3](http://www.gnu.org/copyleft/gpl.html).

## Soporte

Para reportar problemas o solicitar funcionalidades, por favor crear un issue en el repositorio.

## Créditos

Desarrollado para ISER - Sistema de gestión de vacantes para profesores cátedra.

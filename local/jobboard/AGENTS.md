# AGENTS.md - local_jobboard

Plugin de Moodle para gestión de vacantes académicas y postulaciones docentes.
Sistema de Bolsa de Empleo para reclutamiento de profesores de cátedra.

## Información del Proyecto

| Campo | Valor |
|-------|-------|
| **Componente** | `local_jobboard` |
| **Versión actual** | 3.0.8 |
| **Moodle requerido** | 4.1+ (2022112800) |
| **Moodle soportado** | 4.1 - 4.5 |
| **Madurez** | MATURITY_STABLE |
| **Licencia** | GNU GPL v3 or later |
| **Institución** | ISER (Instituto Superior de Educación Rural) |
| **Autor** | Alonso Arias <soporteplataformas@iser.edu.co> |
| **Supervisión** | Vicerrectoría Académica ISER |

---

## Arquitectura IOMAD ISER

El plugin opera en un entorno IOMAD multi-tenant con estructura de 4 niveles:

### PARTE A: Estructura Organizacional (Multi-tenant)

```
NIVEL 1: INSTANCIA IOMAD
         virtual.iser.edu.co
              │
NIVEL 2: COMPANIES (13 Centros Tutoriales)
         ├── Cúcuta
         ├── Ocaña
         ├── El Tarra
         ├── Tibú
         ├── Toledo
         ├── Sardinata
         ├── San Vicente de Chucurí
         ├── Pueblo Bello
         ├── Salazar de las Palmas
         ├── San Pablo
         ├── Santa Rosa del Sur
         ├── Cimitarra
         └── Saravena
              │
NIVEL 3: DEPARTMENTS (Modalidades por Centro)
         ├── Presencial
         ├── Distancia
         ├── Virtual
         └── Híbrida
              │
NIVEL 4: SUB-DEPARTMENTS (Facultades por Modalidad)
         ├── Facultad de Ciencias Administrativas y Sociales
         └── Facultad de Ingenierías e Informática
```

### PARTE B: Estructura Académica (Contenido Compartido)

```
CATEGORÍAS DE CURSOS (Course Categories)
    │
    ├── FACULTAD DE CIENCIAS ADMINISTRATIVAS Y SOCIALES
    │       ├── Tecnología en Gestión Empresarial
    │       ├── Tecnología en Gestión Comunitaria
    │       ├── Tecnología en Gestión de Mercadeo
    │       └── Técnica Prof. en Seguridad y Salud en el Trabajo
    │
    └── FACULTAD DE INGENIERÍAS E INFORMÁTICA
            ├── Tecnología Agropecuaria
            ├── Tecnología en Procesos Agroindustriales
            ├── Tecnología en Gestión Industrial
            ├── Tecnología en Gestión de Redes y Sistemas Teleinformáticos
            ├── Tecnología en Gestión y Construcción de Obras Civiles
            └── Técnica Prof. en Producción de Frutas y Hortalizas
```

### PARTE C: Mecanismo de Conexión

| Mecanismo | Función |
|-----------|---------|
| **SHARED COURSES** | Comparte asignaturas a Companies/Departments |
| **LICENSES** | Controla acceso y cupos por centro/modalidad/periodo |
| **COHORTS** | Agrupa estudiantes: `[CENTRO]-[MOD]-[PROG]-[SEM]-[PERIODO]` |

Ejemplo de cohorte: `CUCU-DIS-TECGES-3SEM-2025-1`

---

## ⚠️ REFACTORIZACIONES OBLIGATORIAS

### 1. RECREACIÓN DE ROLES Y PERMISOS

**ESTADO:** Los roles actuales deben ser **COMPLETAMENTE RECREADOS** para garantizar la correcta asignación de permisos según la nueva lógica de negocio.

**RAZÓN:** La lógica actual no contempla:
- Comités por FACULTAD (no por vacante)
- Revisores por PROGRAMA (no globales)
- Jerarquía de permisos basada en la estructura IOMAD de 4 niveles

**ACCIÓN REQUERIDA:**
1. Eliminar roles existentes del plugin
2. Recrear roles con nueva estructura de capabilities
3. Implementar asignación contextual (por facultad/programa)
4. Migrar asignaciones existentes (si las hay)
5. Actualizar `db/access.php` con capabilities reorganizadas
6. Crear upgrade en `db/upgrade.php` para la migración

### 2. RECREACIÓN DE FLUJOS DE TRABAJO

**ESTADO:** Los flujos de trabajo actuales deben ser **RECREADOS** para garantizar:
- Separación clara de responsabilidades (revisor vs comité)
- Flujo secuencial obligatorio
- Validaciones en cada transición de estado
- Auditoría completa de cada paso

**ACCIÓN REQUERIDA:**
1. Documentar flujo actual (si existe)
2. Diseñar nuevo flujo según lógica de negocio
3. Implementar máquina de estados en `classes/workflow.php`
4. Crear validadores para cada transición
5. Integrar con sistema de auditoría
6. Actualizar notificaciones por email

### 3. MIGRACIÓN A CSS PERSONALIZADO

**ESTADO:** Se debe **ELIMINAR TODA DEPENDENCIA DE BOOTSTRAP** y crear un sistema de clases CSS propias para garantizar independencia gráfica total del plugin.

**ACCIÓN REQUERIDA:**
1. Auditar todos los templates Mustache existentes
2. Crear sistema de clases CSS con prefijo `jb-*`
3. Reemplazar clases Bootstrap por clases propias
4. Crear `styles.css` completo y autocontenido
5. Probar en todos los themes (Boost, Classic, Remui, Flavor)
6. Documentar sistema de clases en este archivo

### 4. MIGRACIÓN DE VISTAS A MUSTACHE

**ESTADO:** Todas las vistas PHP que generen HTML directamente deben ser **MIGRADAS A PLANTILLAS MUSTACHE**.

**RAZÓN:**
- Separación de lógica y presentación
- Reutilización de componentes
- Mantenibilidad del código
- Compatibilidad con themes de Moodle

**ACCIÓN REQUERIDA:**
1. Identificar todas las vistas PHP con HTML embebido
2. Crear plantillas Mustache correspondientes
3. Crear renderers en `classes/output/`
4. Migrar datos a contexto para plantillas
5. Eliminar HTML directo de archivos PHP
6. Verificar renderizado en diferentes themes

### 5. RECREACIÓN DE USER TOURS

**ESTADO:** Los User Tours actuales deben ser **COMPLETAMENTE RECREADOS** debido a:
- Cambios en la interfaz de usuario
- Selectores CSS obsoletos o incorrectos
- Nueva estructura de vistas
- Nuevo sistema de clases CSS

**ACCIÓN REQUERIDA:**
1. Eliminar todos los tours existentes en `db/tours/`
2. Documentar nuevos flujos de usuario
3. Crear nuevos tours con selectores actualizados
4. Probar cada tour paso a paso en la interfaz
5. Validar selectores con DevTools del navegador
6. Verificar en diferentes themes

**REFERENCIA:** Analizar implementación de tours en el repositorio de Moodle core y otros plugins del mismo repositorio donde se encuentra el plugin.

### 6. RECREACIÓN DE MÓDULOS AMD

**ESTADO:** Los módulos JavaScript AMD deben ser **RECREADOS** para:
- Eliminar dependencias de Bootstrap JS
- Usar módulos core de Moodle
- Implementar nueva lógica de UI
- Soportar nuevos componentes CSS personalizados

**ACCIÓN REQUERIDA:**
1. Auditar módulos AMD existentes en `amd/src/`
2. Identificar dependencias de Bootstrap
3. Reemplazar con módulos core de Moodle
4. Implementar lógica para componentes `jb-*`
5. Compilar con `grunt amd --root=local/jobboard`
6. Probar funcionalidad en todos los navegadores

**REFERENCIA:** Analizar implementación de módulos AMD en Moodle core y otros plugins del repositorio para seguir patrones establecidos.

---

## Análisis del Repositorio

### OBLIGATORIO ANTES DE IMPLEMENTAR

Antes de realizar cualquier implementación, el agente DEBE analizar:

```
ANÁLISIS REQUERIDO
│
├── MOODLE CORE
│   ├── lib/amd/src/           → Patrones de módulos AMD
│   ├── lib/templates/         → Patrones de plantillas Mustache
│   ├── admin/tool/usertours/  → Estructura de User Tours
│   └── theme/boost/           → Clases CSS de referencia
│
├── PLUGINS DEL REPOSITORIO
│   ├── local/*/               → Plugins locales existentes
│   ├── mod/*/                 → Módulos de actividad
│   └── block/*/               → Bloques
│
└── IOMAD
    ├── local/iomad/           → Integración multi-tenant
    └── blocks/iomad_*/        → Bloques IOMAD
```

**PROPÓSITO DEL ANÁLISIS:**
- Identificar patrones de código reutilizables
- Seguir convenciones establecidas en el repositorio
- Evitar reinventar soluciones existentes
- Garantizar compatibilidad con IOMAD

---

## Sistema CSS Personalizado

### Política de Estilos

**REGLA FUNDAMENTAL:** El plugin NO debe usar clases de Bootstrap ni de ningún framework CSS externo. Debe tener su propio sistema de clases para garantizar independencia gráfica.

### Prefijo de Clases

Todas las clases CSS del plugin deben usar el prefijo `jb-` (jobboard).

### Categorías de Componentes CSS

| Categoría | Prefijo | Descripción |
|-----------|---------|-------------|
| Variables | `--jb-*` | Custom properties (colores, espaciado, etc.) |
| Layout | `jb-container`, `jb-row`, `jb-col-*` | Sistema de grid |
| Cards | `jb-card`, `jb-card-header`, `jb-card-body` | Tarjetas |
| Botones | `jb-btn`, `jb-btn-primary`, `jb-btn-*` | Botones |
| Formularios | `jb-form-*` | Campos de formulario |
| Tablas | `jb-table`, `jb-table-*` | Tablas de datos |
| Badges | `jb-badge`, `jb-badge-*` | Etiquetas de estado |
| Alertas | `jb-alert`, `jb-alert-*` | Mensajes de alerta |
| Tabs | `jb-tabs`, `jb-tab-*` | Pestañas |
| Modal | `jb-modal`, `jb-modal-*` | Ventanas modales |
| Paginación | `jb-pagination`, `jb-page-*` | Paginación |
| Timeline | `jb-timeline`, `jb-timeline-*` | Historial/timeline |
| Estados | `jb-status`, `jb-status-*` | Indicadores de estado |
| Utilidades | `jb-text-*`, `jb-mt-*`, `jb-d-*` | Helpers |

### Variables CSS Requeridas

| Tipo | Variables |
|------|-----------|
| Colores primarios | `--jb-primary`, `--jb-primary-hover`, `--jb-primary-light` |
| Colores secundarios | `--jb-secondary`, `--jb-secondary-hover` |
| Colores de estado | `--jb-success`, `--jb-warning`, `--jb-danger`, `--jb-info` |
| Colores neutros | `--jb-gray-50` a `--jb-gray-900`, `--jb-white` |
| Tipografía | `--jb-font-family`, `--jb-font-size-*` |
| Espaciado | `--jb-spacing-xs` a `--jb-spacing-2xl` |
| Bordes | `--jb-border-radius-*`, `--jb-border-color` |
| Sombras | `--jb-shadow-sm`, `--jb-shadow`, `--jb-shadow-md`, `--jb-shadow-lg` |
| Transiciones | `--jb-transition` |

---

## Lógica de Negocio: Comité de Selección

### Estructura del Comité

```
COMITÉ DE SELECCIÓN
│
├── Ámbito: Por FACULTAD (no por vacante)
│   ├── Comité Facultad de Ciencias Administrativas y Sociales
│   └── Comité Facultad de Ingenierías e Informática
│
├── Composición:
│   ├── Presidente del Comité (1)
│   ├── Secretario (1)
│   └── Miembros evaluadores (N)
│
├── Funciones:
│   ├── Evaluar candidatos con documentos VALIDADOS
│   ├── Realizar entrevistas
│   ├── Calificar según criterios establecidos
│   ├── Emitir concepto de selección
│   └── Firmar actas de selección
│
└── Restricciones:
    ├── NO puede validar documentos (eso es del revisor)
    ├── Solo ve postulaciones con docs_validated = true
    └── Un miembro puede pertenecer a múltiples comités
```

### Flujo del Comité

```
1. RECEPCIÓN
   └── El comité recibe postulaciones con documentos validados
   
2. EVALUACIÓN INDIVIDUAL
   ├── Cada miembro evalúa al candidato
   ├── Califica según criterios predefinidos
   └── Registra observaciones

3. DELIBERACIÓN
   ├── Se consolidan evaluaciones
   ├── Se discuten casos
   └── Se toman decisiones

4. DECISIÓN
   ├── Seleccionado → Estado: selected
   ├── Rechazado → Estado: rejected
   └── En espera → Estado: waitlisted

5. NOTIFICACIÓN
   └── Sistema envía email al postulante
```

### Vista: Crear Comité de Selección

```
┌─────────────────────────────────────────────────────────────┐
│ CREAR COMITÉ DE SELECCIÓN                                   │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ Facultad: [Dropdown con facultades]                         │
│                                                             │
│ Convocatoria: [Dropdown con convocatorias activas]          │
│                                                             │
│ ─────────────────────────────────────────────────────────── │
│                                                             │
│ AGREGAR MIEMBROS                                            │
│                                                             │
│ Buscar usuario: [________________] [🔍 Buscar]              │
│                 (por username, nombre o email)              │
│                                                             │
│ Resultados:                                                 │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ ☐ jperez - Juan Pérez - jperez@iser.edu.co             │ │
│ │ ☐ mgarcia - María García - mgarcia@iser.edu.co         │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                             │
│ Miembros seleccionados:                                     │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ • Juan Pérez (jperez) - Rol: [Presidente ▼] [Eliminar] │ │
│ │ • María García (mgarcia) - Rol: [Miembro ▼] [Eliminar] │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                             │
│                            [Cancelar] [Guardar Comité]      │
└─────────────────────────────────────────────────────────────┘
```

---

## Lógica de Negocio: Revisores

### Estructura de Revisores

```
REVISORES DE DOCUMENTOS
│
├── Ámbito: Por PROGRAMA ACADÉMICO
│   ├── Revisor de Tecnología en Gestión Empresarial
│   ├── Revisor de Tecnología Agropecuaria
│   └── ... (uno o más por programa)
│
├── Funciones:
│   ├── Revisar documentos de postulantes
│   ├── Verificar autenticidad y vigencia
│   ├── Aprobar o rechazar documentos
│   ├── Escribir observaciones
│   └── Solicitar correcciones
│
├── Restricciones:
│   ├── NO puede evaluar candidatos (eso es del comité)
│   ├── NO puede ver postulaciones de otros programas
│   └── Solo trabaja con postulaciones en estado under_review
│
└── Asignación:
    ├── Manual: Coordinador asigna revisor
    └── Automática: Por programa de la vacante
```

### Flujo del Revisor

```
1. ASIGNACIÓN
   └── Revisor es asignado a programa(s) académico(s)

2. RECEPCIÓN
   ├── Ve postulaciones de SUS programas
   └── Solo en estado under_review

3. REVISIÓN DE DOCUMENTOS
   ├── Abre cada documento
   ├── Verifica checklist según tipo de documento
   ├── Marca como: aprobado / rechazado / pendiente corrección
   └── Escribe observaciones si es necesario

4. FINALIZACIÓN
   ├── Si TODOS aprobados → Estado: docs_validated
   ├── Si alguno rechazado → Estado: docs_rejected
   └── Sistema notifica al postulante

5. CORRECCIONES
   ├── Postulante sube documento corregido
   └── Revisor vuelve a evaluar
```

### Vista: Asignar Revisores por Programa

```
┌─────────────────────────────────────────────────────────────┐
│ ASIGNAR REVISORES POR PROGRAMA                              │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ Facultad: [Ciencias Administrativas y Sociales ▼]           │
│                                                             │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ PROGRAMA                     │ REVISORES ASIGNADOS      │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Tecnología en Gestión        │ • jperez (Juan Pérez)   │ │
│ │ Empresarial                  │ • mrodriguez            │ │
│ │                              │ [+ Agregar revisor]      │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Tecnología en Gestión        │ (Sin revisores)          │ │
│ │ Comunitaria                  │ [+ Agregar revisor]      │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Tecnología en Gestión        │ • agarcia               │ │
│ │ de Mercadeo                  │ [+ Agregar revisor]      │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                             │
│ Modal: Agregar Revisor                                      │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Buscar: [________________] (username)                   │ │
│ │                                                         │ │
│ │ Resultados:                                             │ │
│ │ ○ lmartinez - Luis Martínez                            │ │
│ │ ○ clopez - Carlos López                                │ │
│ │                                                         │ │
│ │                         [Cancelar] [Asignar]           │ │
│ └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

---

## Flujo de Trabajo: Postulación Completa

### Diagrama de Estados

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        FLUJO DE POSTULACIÓN                             │
└─────────────────────────────────────────────────────────────────────────┘

[POSTULANTE]                    [REVISOR]                    [COMITÉ]
     │                              │                            │
     ▼                              │                            │
┌─────────┐                         │                            │
│ Aplica  │                         │                            │
│ vacante │                         │                            │
└────┬────┘                         │                            │
     │                              │                            │
     ▼                              │                            │
┌─────────┐                         │                            │
│ Carga   │                         │                            │
│ docs    │                         │                            │
└────┬────┘                         │                            │
     │                              │                            │
     ▼                              │                            │
┌─────────────────┐                 │                            │
│ submitted       │                 │                            │
│ (Postulación    │                 │                            │
│ enviada)        │                 │                            │
└────────┬────────┘                 │                            │
         │                          │                            │
         │ [Sistema asigna          │                            │
         │  revisor por programa]   │                            │
         ▼                          │                            │
┌─────────────────┐                 │                            │
│ under_review    │◄────────────────┤                            │
│ (En revisión)   │                 │                            │
└────────┬────────┘                 │                            │
         │                          │                            │
         │                          ▼                            │
         │                   ┌─────────────┐                     │
         │                   │ Revisor     │                     │
         │                   │ evalúa docs │                     │
         │                   └──────┬──────┘                     │
         │                          │                            │
         │            ┌─────────────┴─────────────┐              │
         │            ▼                           ▼              │
         │     ┌─────────────┐             ┌─────────────┐       │
         │     │ Todos       │             │ Alguno      │       │
         │     │ aprobados   │             │ rechazado   │       │
         │     └──────┬──────┘             └──────┬──────┘       │
         │            │                           │              │
         │            ▼                           ▼              │
         │     ┌─────────────────┐        ┌─────────────────┐    │
         │     │ docs_validated  │        │ docs_rejected   │    │
         │     │ (Docs válidos)  │        │ (Docs rechaz.)  │    │
         │     └────────┬────────┘        └────────┬────────┘    │
         │              │                          │              │
         │              │                          ▼              │
         │              │                   ┌─────────────┐       │
         │              │                   │ Postulante  │       │
         │              │                   │ corrige     │       │
         │              │                   └──────┬──────┘       │
         │              │                          │              │
         │              │                          │ [Sube        │
         │              │                          │  nuevos      │
         │              │                          │  docs]       │
         │              │                          │              │
         │              │                          ▼              │
         │              │            ┌─────────────────────┐      │
         │              │            │ Vuelve a            │      │
         │              │            │ under_review        │──────┘
         │              │            └─────────────────────┘
         │              │
         │              │ [Pasa a comité de la facultad]
         │              │
         │              ▼                            │
         │       ┌─────────────┐                     │
         │       │ interview   │◄────────────────────┤
         │       │ (Entrevista)│                     │
         │       └──────┬──────┘                     │
         │              │                            │
         │              │                            ▼
         │              │                     ┌─────────────┐
         │              │                     │ Comité      │
         │              │                     │ evalúa      │
         │              │                     └──────┬──────┘
         │              │                            │
         │              │              ┌─────────────┴─────────────┐
         │              │              ▼                           ▼
         │              │       ┌─────────────┐             ┌─────────────┐
         │              │       │ Seleccionado│             │ Rechazado   │
         │              │       └──────┬──────┘             └──────┬──────┘
         │              │              │                           │
         │              │              ▼                           ▼
         │              │       ┌─────────────────┐        ┌─────────────────┐
         │              │       │ selected        │        │ rejected        │
         │              │       │ (Seleccionado)  │        │ (No seleccion.) │
         │              │       └─────────────────┘        └─────────────────┘
         │              │
         ▼              ▼
   [Email de     [Email de
   notificación] notificación]
```

### Estados y Transiciones

| Estado Actual | Estados Siguientes Permitidos | Quién Ejecuta |
|---------------|------------------------------|---------------|
| `draft` | `submitted` | Postulante |
| `submitted` | `under_review`, `withdrawn` | Sistema, Postulante |
| `under_review` | `docs_validated`, `docs_rejected` | Revisor |
| `docs_validated` | `interview`, `rejected` | Comité, Coordinador |
| `docs_rejected` | `under_review`, `withdrawn` | Sistema, Postulante |
| `interview` | `selected`, `rejected` | Comité |
| `selected` | (estado final) | - |
| `rejected` | (estado final) | - |
| `withdrawn` | (estado final) | - |

---

## Reglas de Negocio Críticas

### Organización por Facultad

1. **Vacantes separadas por facultad** - Las vacantes se organizan y filtran por facultad
2. **Comité de selección por FACULTAD** - NO por vacante. Cada facultad tiene su propio comité
3. **Revisores asignados por PROGRAMA** - Los revisores de documentos se asignan a nivel de programa académico

### Creación de Comité de Selección

- Al crear el comité, debe permitir **buscar/filtrar usuarios por username**
- El comité evalúa TODAS las vacantes de su facultad
- Un usuario puede pertenecer a comités de diferentes facultades

### Convocatorias

- **PDF adjunto obligatorio:** Al crear la convocatoria se debe cargar un PDF con el detalle completo
- **Descripción breve:** Campo de texto para resumen de la convocatoria
- **Botón de acceso al PDF:** Visible en la vista de la convocatoria

### Formulario de Postulación PERSONALIZABLE

El formulario de postulación debe ser completamente configurable desde la administración:

| Atributo | Descripción |
|----------|-------------|
| **Tipo** | `archivo` (documento a cargar) o `texto` (campo a diligenciar) |
| **Nombre** | Identificador del documento/campo |
| **Etiqueta** | Texto visible para el usuario |
| **Obligatoriedad** | `obligatorio` u `opcional` |
| **Estado** | `activo` o `inactivo` |
| **Orden** | Posición en el formulario |
| **Instrucciones** | Texto de ayuda para el usuario |

**Nota:** La Carta de Intención es un campo de TEXTO que se redacta directamente en el formulario, NO es un archivo a cargar.

### Postulaciones

- **Límite:** Un postulante solo puede aplicar a UNA vacante por convocatoria
- **Experiencia ocasional:** Docentes ocasionales requieren 2 años de experiencia laboral equivalente a tiempo completo

### Excepciones por Edad (50+ años)

- Según legislación colombiana, personas ≥50 años están exentas de ciertos documentos
- Excepción principal: Libreta Militar
- Las excepciones son GLOBALES, definidas en admin y activadas por convocatoria

### Validación de Documentos

- La verificación es **100% MANUAL** - NO existe verificación automática
- Cada tipo de documento tiene su checklist de verificación
- Documentos rechazados pueden recargarse con observaciones enviadas por email

---

## Estructura de Vistas (REFACTORIZAR)

### Vistas Actuales vs Propuestas

| Vista Actual | Acción | Vista Propuesta |
|--------------|--------|-----------------|
| `dashboard.php` | Migrar a Mustache | `views/dashboard.php` + `templates/pages/dashboard.mustache` |
| `browse_convocatorias.php` | Refactorizar | `views/convocatorias/index.php` |
| `convocatoria_detail.php` | Refactorizar | `views/convocatorias/view.php` |
| `vacancies.php` | Refactorizar | `views/vacancies/index.php` |
| `vacancy_detail.php` | Refactorizar | `views/vacancies/view.php` |
| `applications.php` | Refactorizar | `views/applications/index.php` |
| `application_detail.php` | Refactorizar | `views/applications/view.php` |
| `review.php` | **RECREAR** | `views/review/index.php` (panel revisor) |
| `myreviews.php` | Consolidar | `views/review/my.php` |
| `validate_document.php` | Consolidar | `views/review/document.php` |
| N/A | **CREAR** | `views/committee/index.php` (panel comité) |
| N/A | **CREAR** | `views/committee/evaluate.php` |
| `admin/exemptions.php` | Migrar a Mustache | Gestión de excepciones |
| N/A | **CREAR** | `admin/doctypes.php` (config documentos) |
| N/A | **CREAR** | `admin/committee.php` (gestión comités) |
| N/A | **CREAR** | `admin/reviewers.php` (asignación revisores) |

### Estructura de Carpetas Propuesta

```
views/
├── dashboard.php              # Dashboard principal
│
├── convocatorias/
│   ├── index.php             # Lista de convocatorias
│   ├── view.php              # Detalle de convocatoria
│   └── create.php            # Crear/editar convocatoria
│
├── vacancies/
│   ├── index.php             # Lista de vacantes (por facultad)
│   ├── view.php              # Detalle de vacante
│   └── create.php            # Crear/editar vacante
│
├── applications/
│   ├── index.php             # Mis postulaciones (postulante)
│   ├── view.php              # Detalle de postulación
│   ├── create.php            # Formulario de postulación
│   └── documents.php         # Gestión de documentos
│
├── review/                   # PANEL DE REVISOR
│   ├── index.php             # Postulaciones asignadas
│   ├── my.php                # Mis revisiones completadas
│   └── document.php          # Validar documento individual
│
├── committee/                # PANEL DE COMITÉ
│   ├── index.php             # Postulaciones para evaluar
│   ├── evaluate.php          # Evaluar candidato
│   └── results.php           # Resultados de evaluación
│
├── reports/
│   ├── index.php             # Índice de reportes
│   ├── applications.php      # Reporte de postulaciones
│   ├── documents.php         # Reporte de documentos
│   └── audit.php             # Consulta de auditoría
│
└── public/
    ├── index.php             # Vista pública de convocatorias
    └── vacancy.php           # Detalle público de vacante
```

### Plantillas Mustache Requeridas

```
templates/
├── layouts/
│   └── main.mustache                    # Layout principal con jb-app
│
├── components/
│   ├── card.mustache                    # Componente card
│   ├── table.mustache                   # Componente tabla
│   ├── pagination.mustache              # Paginación
│   ├── modal.mustache                   # Modal
│   ├── alert.mustache                   # Alertas
│   ├── badge.mustache                   # Badges de estado
│   ├── timeline.mustache                # Timeline de historial
│   ├── status_badge.mustache            # Badge de estado
│   ├── user_search.mustache             # Buscador de usuarios
│   └── document_item.mustache           # Item de documento
│
├── pages/
│   ├── dashboard.mustache               # Dashboard
│   ├── convocatorias_list.mustache      # Lista convocatorias
│   ├── convocatoria_detail.mustache     # Detalle convocatoria
│   ├── vacancies_list.mustache          # Lista vacantes por facultad
│   ├── vacancy_detail.mustache          # Detalle vacante
│   ├── applications_list.mustache       # Lista postulaciones
│   ├── application_detail.mustache      # Detalle postulación
│   ├── application_form.mustache        # Formulario postulación
│   ├── review_panel.mustache            # Panel del revisor
│   ├── review_document.mustache         # Revisar documento
│   ├── committee_panel.mustache         # Panel del comité
│   ├── committee_evaluate.mustache      # Evaluar candidato
│   └── public_convocatorias.mustache    # Vista pública
│
└── admin/
    ├── doctypes_list.mustache           # Config tipos documento
    ├── doctype_form.mustache            # Formulario tipo doc
    ├── committee_form.mustache          # Crear/editar comité
    ├── committee_members.mustache       # Miembros del comité
    ├── reviewers_assignment.mustache    # Asignar revisores
    └── audit_log.mustache               # Log de auditoría
```

---

## User Tours (RECREAR)

### Tours a Crear

| Tour ID | Nombre | Descripción | Audiencia |
|---------|--------|-------------|-----------|
| `jb_tour_applicant_first` | Primer inicio postulante | Guía inicial para postulantes | Postulantes nuevos |
| `jb_tour_apply_vacancy` | Aplicar a vacante | Proceso de postulación paso a paso | Postulantes |
| `jb_tour_upload_documents` | Subir documentos | Cómo cargar documentos correctamente | Postulantes |
| `jb_tour_reviewer_panel` | Panel del revisor | Navegación del panel de revisión | Revisores |
| `jb_tour_review_document` | Revisar documento | Proceso de validación de documentos | Revisores |
| `jb_tour_committee_panel` | Panel del comité | Navegación del panel de evaluación | Comité |
| `jb_tour_evaluate_candidate` | Evaluar candidato | Proceso de evaluación | Comité |
| `jb_tour_admin_doctypes` | Configurar documentos | Gestión de tipos de documento | Admin |
| `jb_tour_admin_committee` | Gestionar comités | Crear y administrar comités | Admin |
| `jb_tour_admin_reviewers` | Asignar revisores | Asignación de revisores por programa | Admin |

### Estructura de Tour JSON

Cada tour debe guardarse en `db/tours/` con estructura:

```
db/tours/
├── jb_tour_applicant_first.json
├── jb_tour_apply_vacancy.json
├── jb_tour_upload_documents.json
├── jb_tour_reviewer_panel.json
├── jb_tour_review_document.json
├── jb_tour_committee_panel.json
├── jb_tour_evaluate_candidate.json
├── jb_tour_admin_doctypes.json
├── jb_tour_admin_committee.json
└── jb_tour_admin_reviewers.json
```

### Consideraciones para Tours

1. **Selectores CSS:** Usar clases `jb-*` propias, NO clases de Bootstrap
2. **Validación:** Verificar cada selector con DevTools antes de implementar
3. **Orden de pasos:** Seguir flujo lógico de la tarea
4. **Textos:** Definir en strings de idioma (EN/ES)
5. **Condiciones:** Configurar audiencia correctamente (roles/capabilities)

---

## Módulos AMD (RECREAR)

### Módulos Requeridos

| Módulo | Propósito | Dependencias Core |
|--------|-----------|-------------------|
| `local_jobboard/main` | Inicialización principal | `core/ajax`, `core/notification` |
| `local_jobboard/modal` | Gestión de modales `jb-modal` | `core/modal_factory`, `core/templates` |
| `local_jobboard/tabs` | Gestión de pestañas `jb-tabs` | (ninguna externa) |
| `local_jobboard/form` | Validación de formularios | `core/form-autocomplete` |
| `local_jobboard/user_search` | Búsqueda de usuarios por username | `core/ajax`, `core/templates` |
| `local_jobboard/document_upload` | Carga de documentos | `core/ajax`, `core/notification` |
| `local_jobboard/document_review` | Revisión de documentos | `core/ajax`, `core/modal_factory` |
| `local_jobboard/committee_evaluate` | Evaluación de candidatos | `core/ajax`, `core/templates` |
| `local_jobboard/status_update` | Actualización de estados | `core/ajax`, `core/notification` |
| `local_jobboard/timeline` | Renderizado de timeline | `core/templates` |
| `local_jobboard/pagination` | Paginación AJAX | `core/ajax`, `core/templates` |

### Estructura de Carpetas AMD

```
amd/
├── src/                      # Fuentes (EDITAR AQUÍ)
│   ├── main.js
│   ├── modal.js
│   ├── tabs.js
│   ├── form.js
│   ├── user_search.js
│   ├── document_upload.js
│   ├── document_review.js
│   ├── committee_evaluate.js
│   ├── status_update.js
│   ├── timeline.js
│   └── pagination.js
│
└── build/                    # Compilados (NO EDITAR)
    ├── main.min.js
    ├── modal.min.js
    └── ...
```

### Reglas para Módulos AMD

1. **NUNCA** editar archivos en `amd/build/`
2. **SIEMPRE** compilar después de cambios: `grunt amd --root=local/jobboard`
3. **NO** usar jQuery directamente si existe equivalente en core
4. **NO** usar librerías Bootstrap JS
5. **USAR** módulos core de Moodle para: AJAX, modales, notificaciones, templates

---

## Roles y Capabilities

### Roles del Plugin

| Rol | Shortname | Ámbito | Descripción |
|-----|-----------|--------|-------------|
| Revisor de Documentos | `jb_reviewer` | Por PROGRAMA | Revisa y valida documentos |
| Miembro de Comité | `jb_committee` | Por FACULTAD | Evalúa candidatos |
| Coordinador de Selección | `jb_coordinator` | Sistema | Gestiona todo el proceso |

### Grupos de Capabilities

| Grupo | Capabilities |
|-------|--------------|
| **Acceso básico** | `view`, `viewpublic` |
| **Postulante** | `apply`, `viewownapplications`, `uploaddocuments`, `withdrawapplication` |
| **Revisor** | `reviewdocuments`, `approvedocument`, `rejectdocument`, `requestdocumentcorrection`, `viewassignedapplications` |
| **Comité** | `evaluatecandidates`, `selectcandidate`, `rejectcandidate`, `viewfacultyapplications`, `scheduleinterview` |
| **Coordinador** | `manageconvocatorias`, `managevacancies`, `assignreviewers`, `managecommittee`, `viewallapplications`, `exportdata` |
| **Administración** | `managedoctypes`, `manageexemptions`, `managetemplates`, `viewaudit`, `configuresettings` |

---

## Base de Datos

### Tablas Principales

| Tabla | Descripción |
|-------|-------------|
| `local_jobboard_convocatoria` | Convocatorias con PDF adjunto |
| `local_jobboard_vacancy` | Vacantes académicas por facultad |
| `local_jobboard_application` | Postulaciones |
| `local_jobboard_document` | Documentos subidos |
| `local_jobboard_doctype` | Tipos de documento CONFIGURABLES |
| `local_jobboard_docvalidation` | Validaciones de documentos |
| `local_jobboard_audit` | Registro de auditoría |

### Tablas Nuevas Requeridas

| Tabla | Descripción |
|-------|-------------|
| `local_jobboard_faculty` | Facultades académicas |
| `local_jobboard_program` | Programas por facultad |
| `local_jobboard_committee` | Comités de selección por facultad |
| `local_jobboard_committee_member` | Miembros del comité |
| `local_jobboard_reviewer_program` | Asignación de revisores por programa |

---

## Sistema de Auditoría

### Acciones a Registrar

| Componente | Acciones |
|------------|----------|
| Convocatoria | create, update, delete, publish, close, archive |
| Vacante | create, update, delete, publish, close |
| Postulación | create, submit, transition, withdraw |
| Documento | upload, download, approve, reject, request_correction |
| Comité | create, update, add_member, remove_member |
| Revisor | assign, revoke |
| Configuración | update_doctype, update_exemption, update_template |
| Email | sent |

### Datos a Registrar

| Campo | Descripción |
|-------|-------------|
| `userid` | Usuario que realizó la acción |
| `action` | Tipo de acción |
| `component` | Entidad afectada |
| `itemid` | ID del registro afectado |
| `previousvalue` | Valor anterior (JSON) |
| `newvalue` | Valor nuevo (JSON) |
| `ipaddress` | Dirección IP |
| `useragent` | Navegador |
| `timecreated` | Timestamp |
| `extradata` | Datos adicionales (JSON) |

---

## Plantillas de Email

### Templates Requeridos

| Template Key | Descripción |
|--------------|-------------|
| `application_received` | Confirmación de postulación |
| `application_status_changed` | Cambio de estado |
| `review_complete` | Revisión completada (consolidado) |
| `document_approved` | Documento aprobado |
| `document_rejected` | Documento rechazado |
| `interview_scheduled` | Citación a entrevista |
| `selected` | Notificación de selección |
| `rejected` | Notificación de no selección |
| `vacancy_closing_soon` | Vacante próxima a cerrar |

### Placeholders Disponibles

```
{USER_NAME}, {USER_EMAIL}, {SITE_NAME}, {SITE_URL}
{VACANCY_TITLE}, {VACANCY_CODE}, {APPLICATION_DATE}
{FACULTY_NAME}, {PROGRAM_NAME}
{OLD_STATUS}, {NEW_STATUS}, {DOCUMENT_TYPE}
{REJECTION_REASON}, {OBSERVATIONS}, {REUPLOAD_URL}
{INTERVIEW_DATE}, {INTERVIEW_TIME}, {INTERVIEW_LOCATION}
{CONVOCATORIA_NAME}, {CONVOCATORIA_PDF_URL}
```

---

## Control de Versiones

### POLÍTICA OBLIGATORIA

**CADA cambio, por mínimo que sea, DEBE:**
1. Incrementar `$plugin->version` en version.php (formato YYYYMMDDXX)
2. Actualizar `$plugin->release`
3. Documentar en CHANGELOG.md
4. Validar en plataforma ANTES de commit

### Formato CHANGELOG.md

```
## [X.Y.Z] - YYYY-MM-DD

### Added
- Nueva funcionalidad

### Changed
- Cambio de comportamiento

### Fixed
- Corrección de bug

### Removed
- Funcionalidad eliminada
```

---

## Comandos Útiles

| Comando | Propósito |
|---------|-----------|
| `php admin/cli/upgrade.php` | Ejecutar migraciones de BD |
| `php admin/cli/purge_caches.php` | Limpiar caché de Moodle |
| `grunt amd --root=local/jobboard` | Compilar JavaScript AMD |
| `php admin/tool/phpunit/cli/init.php` | Inicializar PHPUnit |
| `vendor/bin/phpunit --testsuite local_jobboard_testsuite` | Ejecutar tests |

---

## Elementos Eliminados/Obsoletos

- ❌ Campo `salary`/`remuneration` en vacantes
- ❌ Tarjeta de Identidad como tipo de documento
- ❌ Vacante extemporánea
- ❌ Fechas de apertura/cierre en vacantes (solo en convocatoria)
- ❌ Breadcrumb personalizado (usar nativo de Moodle)
- ❌ Font Awesome (usar pix_icon)
- ❌ CSS de navegación personalizado
- ❌ Comité por vacante (ahora es por FACULTAD)
- ❌ Clases Bootstrap (usar clases `jb-*`)

---

## Notas Críticas para Agentes

### Prioridades de Refactorización

1. **PRIMERO:** Analizar repositorio completo (Moodle core, IOMAD, otros plugins)
2. **SEGUNDO:** Migrar a CSS personalizado (independencia gráfica)
3. **TERCERO:** Migrar vistas a Mustache
4. **CUARTO:** Recrear roles y capabilities
5. **QUINTO:** Recrear flujos de trabajo
6. **SEXTO:** Recrear módulos AMD
7. **SÉPTIMO:** Recrear User Tours

### Reglas Absolutas

1. **ANALIZAR** el repositorio antes de implementar cualquier cosa
2. **NO USAR BOOTSTRAP** - Solo clases con prefijo `jb-*`
3. **MIGRAR A MUSTACHE** - Todas las vistas deben usar plantillas
4. **RECREAR USER TOURS** - Con selectores actualizados
5. **RECREAR MÓDULOS AMD** - Sin dependencias de Bootstrap JS
6. **VALIDAR SIEMPRE** en plataforma antes de commit
7. **NO improvisar** cambios directamente en producción
8. **Respetar** la arquitectura IOMAD de 4 niveles
9. **Mantener** paridad de strings EN/ES
10. **Documentar** TODO en CHANGELOG
11. **Comité de selección** es por FACULTAD, no por vacante
12. **Revisores** se asignan por PROGRAMA
13. **Formulario de postulación** es PERSONALIZABLE desde admin
14. **Carta de intención** es campo de TEXTO, no archivo
15. **Convocatoria** debe tener PDF adjunto con detalle completo
16. **Auditoría ROBUSTA** - registrar TODAS las acciones
17. Un postulante = UNA vacante por convocatoria
18. La validación de documentos es 100% MANUAL
19. **Búsqueda de usuarios** por username al crear comités

---

## Cumplimiento Normativo

### Protección de Datos

- **Ley 1581/2012** - Habeas Data (Colombia)
- **GDPR** - Privacy API de Moodle implementada
- Privacy Provider para tablas con datos personales

### Contratación

- Cumple normativa colombiana de contratación docente
- Excepciones de edad según legislación vigente

---

## Contacto

- **Autor:** Alonso Arias
- **Email:** soporteplataformas@iser.edu.co
- **Supervisión:** Vicerrectoría Académica ISER
- **Institución:** ISER (Instituto Superior de Educación Rural)
- **Sede Principal:** Pamplona, Norte de Santander, Colombia

---

*Última actualización: Diciembre 2025*
*Plugin local_jobboard para Moodle 4.1-4.5 con IOMAD*

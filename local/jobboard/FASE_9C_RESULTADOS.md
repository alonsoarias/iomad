# Resultados Fase 9C: Mapeo y Creacion de Script CLI de Importacion

## Fecha: 2025-12-08

## Resumen Ejecutivo

Se analizo la estructura de IOMAD (companies/departments) y del plugin local_jobboard
para crear scripts CLI que permitan importar los perfiles profesionales como vacantes.
Se genero un **CLI unificado** (`cli.php`) que automaticamente:
1. Parsea los archivos de texto extraidos de los PDFs
2. Genera JSON con los perfiles estructurados
3. Importa las vacantes a la base de datos de Moodle

Se extrajeron **197 perfiles** listos para importacion.

---

## 1. Analisis de Estructura IOMAD

### Jerarquia Multi-tenant

```
IOMAD ISER (virtual.iser.edu.co)
├── COMPANY: Cucuta (Centro Tutorial)
│   ├── DEPARTMENT: Presencial
│   ├── DEPARTMENT: A Distancia
│   ├── DEPARTMENT: Virtual
│   └── DEPARTMENT: Hibrida
├── COMPANY: Ocana
│   └── [4 departments...]
├── COMPANY: El Tarra
│   └── [4 departments...]
└── [... 13 centros tutoriales total]
```

### Tablas IOMAD Relevantes

| Tabla | Proposito |
|-------|-----------|
| `company` | Centros tutoriales (id, name, shortname, parentid) |
| `department` | Modalidades educativas (id, name, company, parent) |
| `company_users` | Usuarios asignados a companias |

---

## 2. Estructura de Vacantes (local_jobboard)

### Tabla `local_jobboard_vacancy`

| Campo | Tipo | Mapeo desde Perfiles |
|-------|------|---------------------|
| `code` | varchar(50) | CODIGO del perfil (FCAS-01, FII-15) |
| `title` | varchar(255) | PROGRAMA + PERFIL |
| `description` | text | Lista de cursos a orientar |
| `contracttype` | varchar(50) | TIPO DE VINCULACION |
| `location` | varchar(255) | Sede (PAMPLONA, etc.) |
| `department` | varchar(255) | PROGRAMA ACADEMICO (texto) |
| `companyid` | int | ID de company IOMAD (Centro Tutorial) |
| `departmentid` | int | ID de department IOMAD (Modalidad) |
| `requirements` | text | PERFIL PROFESIONAL ESPECIFICO |
| `positions` | int | Numero de plazas (default: 1) |
| `status` | varchar(20) | draft/published/closed |

---

## 3. CLI Unificado

### 3.1 cli.php (Principal)

**Ubicacion:** `local/jobboard/cli/cli.php`

**Funcion:** Script unificado que automaticamente:
1. Lee los archivos de texto de `PERFILESPROFESORES_TEXT`
2. Parsea y extrae los perfiles profesionales
3. Importa las vacantes a la base de datos (si Moodle esta disponible)

**Modos de operacion:**

- **Modo Moodle:** Ejecuta parsing + importacion a base de datos
- **Modo Standalone:** Solo parsing (cuando no hay config.php de Moodle)

**Uso basico:**
```bash
# Importacion completa (desde directorio de Moodle)
php local/jobboard/cli/cli.php

# Con opciones
php local/jobboard/cli/cli.php \
    --input=local/jobboard/PERFILESPROFESORES_TEXT \
    --convocatoria=1 \
    --company=2 \
    --opendate=2026-01-15 \
    --closedate=2026-02-15 \
    --status=draft

# Solo parsing (modo standalone o con opcion)
php local/jobboard/cli/cli.php --export-json=perfiles.json

# Simulacion sin cambios en DB
php local/jobboard/cli/cli.php --dryrun
```

**Opciones:**
| Opcion | Descripcion |
|--------|-------------|
| `--input=DIR` | Directorio con archivos .txt (default: PERFILESPROFESORES_TEXT) |
| `--export-json=FILE` | Exportar perfiles a archivo JSON |
| `--convocatoria=ID` | ID de convocatoria para asociar |
| `--company=ID` | ID de company IOMAD por defecto |
| `--department=ID` | ID de department IOMAD por defecto |
| `--opendate=DATE` | Fecha apertura (YYYY-MM-DD), default: ahora |
| `--closedate=DATE` | Fecha cierre (YYYY-MM-DD), default: +30 dias |
| `--dryrun` | Simular sin crear registros |
| `--update` | Actualizar vacantes existentes (por codigo) |
| `--status=STATUS` | Estado inicial: draft/published (default: draft) |
| `--verbose` | Mostrar informacion detallada |
| `--help` | Mostrar ayuda |

**Salida:**
```
==============================================
Phase 1: Parsing Profile Text Files
==============================================
Input directory: /path/to/PERFILESPROFESORES_TEXT
Found 33 text files

Processing: FCAS-PERFILES_PROFESORES_2026_p01.txt ... found 9 profiles
Processing: FCAS-PERFILES_PROFESORES_2026_p02.txt ... found 13 profiles
...

Parsing complete:
  Files processed: 33
  Profiles found: 197
    - FCAS: 156
    - FII: 41

==============================================
Phase 2: Importing Vacancies to Database
==============================================
[1/197] CREATED: FCAS-01 (ID: 1)
[2/197] CREATED: FCAS-02 (ID: 2)
...

Import Summary
==============
Total processed: 197
Created: 197
Updated: 0
Skipped: 0
Errors: 0
```

**Estructura JSON generada:**
```json
{
  "generated": "2025-12-08 15:38:22",
  "source": "PERFILES PROFESORES ISER 2026",
  "stats": {
    "total_profiles": 197,
    "fcas_profiles": 156,
    "fii_profiles": 41
  },
  "vacancies": [
    {
      "code": "FII-01",
      "faculty": "FII",
      "modality": "PRESENCIAL",
      "location": "PAMPLONA",
      "contracttype": "OCASIONAL TIEMPO COMPLETO",
      "program": "TECNOLOGIA EN PROCESOS AGROINDUSTRIALES",
      "profile": "INGENIERO DE ALIMENTOS...",
      "courses": ["INTRODUCCION A LA AGROINDUSTRIA", ...]
    }
  ]
}
```

---

### 3.2 Scripts auxiliares (Deprecados)

Los siguientes scripts fueron reemplazados por `cli.php`:

| Script | Funcion | Estado |
|--------|---------|--------|
| `parse_profiles.php` | Parser v1 | Deprecado |
| `parse_profiles_v2.php` | Parser v2 mejorado | Integrado en cli.php |
| `import_vacancies.php` | Importacion desde JSON | Integrado en cli.php |

> **Nota:** Estos scripts siguen funcionando de forma independiente pero se recomienda usar el CLI unificado.

---

## 4. Perfiles Extraidos - Estadisticas

### Totales

| Metrica | Valor |
|---------|-------|
| Total perfiles parseados | 197 |
| Perfiles FCAS | 156 |
| Perfiles FII | 41 |

### Calidad de Datos Extraidos

| Campo | Completitud |
|-------|-------------|
| Codigo | 100% |
| Programa academico | 81.7% |
| Cursos a orientar | 66.5% |
| Tipo de contrato | 15.2% |
| Perfil profesional | 6.1% |

### Tipos de Contrato

| Tipo | Cantidad |
|------|----------|
| Sin especificar | 167 |
| Ocasional tiempo completo | 21 |
| Catedra | 9 |

### Programas Academicos (Top 5)

| Programa | Perfiles |
|----------|----------|
| Tecnologia en Gestion de Mercadeo | 80 |
| Tecnologia en Gestion Comunitaria | 23 |
| Tecnologia en Gestion Empresarial | 12 |
| Tecnologia en Procesos Agroindustriales | 7 |
| Otros programas | 39 |
| Sin programa identificado | 36 |

---

## 5. Mapeo IOMAD Sugerido

### Companies (Centros Tutoriales)

| shortname | Nombre | Descripcion |
|-----------|--------|-------------|
| PAMPLONA | Sede Principal Pamplona | Sede central academica |
| CUCUTA | Centro Tutorial Cucuta | |
| OCANA | Centro Tutorial Ocana | |
| TIBU | Centro Tutorial Tibu | |
| TOLEDO | Centro Tutorial Toledo | |
| SARDINATA | Centro Tutorial Sardinata | |
| ELTARRA | Centro Tutorial El Tarra | |
| SANVICENTE | Centro Tutorial San Vicente | |
| PUEBLOBELLO | Centro Tutorial Pueblo Bello | |
| SALAZAR | Centro Tutorial Salazar | |
| SANPABLO | Centro Tutorial San Pablo | |
| SANTAROSA | Centro Tutorial Santa Rosa | |
| CIMITARRA | Centro Tutorial Cimitarra | |
| SARAVENA | Centro Tutorial Saravena | |

### Departments (Modalidades)

| name | Descripcion |
|------|-------------|
| Presencial | Modalidad presencial |
| A Distancia | Modalidad a distancia |
| Virtual | Modalidad virtual |
| Hibrida | Modalidad hibrida |

---

## 6. Flujo de Importacion Recomendado

### Paso 1: Configurar IOMAD

```sql
-- Verificar companies existentes
SELECT id, shortname, name FROM mdl_company;

-- Verificar departments por company
SELECT d.id, d.name, c.shortname
FROM mdl_department d
JOIN mdl_company c ON d.company = c.id;
```

### Paso 2: Crear Convocatoria

Crear una convocatoria en el plugin local_jobboard para agrupar las vacantes:
- Nombre: "Convocatoria Docentes 2026-1"
- Fechas: Enero 15 - Febrero 15, 2026
- Estado: draft

### Paso 3: Importar con CLI Unificado (Dry Run)

```bash
# Simulacion - no crea registros
php local/jobboard/cli/cli.php \
    --convocatoria=1 \
    --opendate=2026-01-15 \
    --closedate=2026-02-15 \
    --dryrun
```

### Paso 4: Importar Vacantes (Real)

```bash
# Importacion completa automatica
php local/jobboard/cli/cli.php \
    --convocatoria=1 \
    --company=1 \
    --opendate=2026-01-15 \
    --closedate=2026-02-15 \
    --status=draft
```

### Paso 5: Revisar y Publicar

1. Acceder a Admin > Local Plugins > Job Board > Manage Vacancies
2. Revisar cada vacante importada
3. Completar datos faltantes si es necesario
4. Cambiar estado a "published" cuando esten listas

---

## 7. Archivos Generados

| Archivo | Descripcion |
|---------|-------------|
| `cli/cli.php` | **CLI Unificado** - script principal |
| `cli/parse_profiles.php` | Parser v1 (deprecado) |
| `cli/parse_profiles_v2.php` | Parser v2 (integrado en cli.php) |
| `cli/import_vacancies.php` | Importador (integrado en cli.php) |
| `perfiles_2026.json` | JSON con 197 perfiles parseados |

---

## 8. Notas Tecnicas

### Limitaciones del Parsing

1. El texto extraido de PDFs pierde la estructura tabular original
2. Algunos campos como "perfil profesional" son dificiles de delimitar
3. Se recomienda revision manual post-importacion

### Consideraciones de Seguridad

1. Los scripts CLI requieren permisos de administrador
2. Usar `--dryrun` antes de importaciones reales
3. Hacer backup de la base de datos antes de importar

### Integracion con IOMAD

- El campo `companyid` permite asociar vacantes a centros tutoriales
- El campo `departmentid` permite filtrar por modalidad
- Los usuarios solo ven vacantes de su company asignada

---

## 9. Estado

- [x] **COMPLETADO** - CLI unificado (cli.php) creado y funcional
- [x] **COMPLETADO** - Parsing de 197 perfiles verificado
- [x] **COMPLETADO** - Documentacion actualizada

---

## 10. Proximos Pasos

1. Configurar companies y departments en IOMAD segun arquitectura
2. Crear convocatoria para agrupar vacantes
3. Ejecutar importacion con: `php local/jobboard/cli/cli.php --dryrun`
4. Validar datos importados
5. Ajustar y completar informacion faltante
6. Publicar convocatoria

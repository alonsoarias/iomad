# Resultados Fase 9C: Mapeo y Creacion de Script CLI de Importacion

## Fecha: 2025-12-08

## Resumen Ejecutivo

Se analizo la estructura de IOMAD (companies/departments) y del plugin local_jobboard
para crear scripts CLI que permitan importar los perfiles profesionales como vacantes.
Se generaron **197 perfiles** parseados y listos para importacion.

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

## 3. Scripts CLI Creados

### 3.1 parse_profiles_v2.php

**Ubicacion:** `local/jobboard/cli/parse_profiles_v2.php`

**Funcion:** Parsea los archivos de texto extraidos de los PDFs y genera un archivo JSON
estructurado con todos los perfiles.

**Uso:**
```bash
php local/jobboard/cli/parse_profiles_v2.php \
    --input=local/jobboard/PERFILESPROFESORES_TEXT \
    --output=local/jobboard/perfiles_2026.json \
    --verbose
```

**Salida:** Archivo JSON con estructura:
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

### 3.2 import_vacancies.php

**Ubicacion:** `local/jobboard/cli/import_vacancies.php`

**Funcion:** Importa las vacantes desde el archivo JSON a la base de datos de Moodle,
creando registros en la tabla `local_jobboard_vacancy`.

**Uso:**
```bash
# Simulacion (no crea registros)
php local/jobboard/cli/import_vacancies.php \
    --file=local/jobboard/perfiles_2026.json \
    --opendate=2026-01-15 \
    --closedate=2026-02-15 \
    --dryrun

# Importacion real
php local/jobboard/cli/import_vacancies.php \
    --file=local/jobboard/perfiles_2026.json \
    --convocatoria=1 \
    --company=2 \
    --opendate=2026-01-15 \
    --closedate=2026-02-15 \
    --status=draft
```

**Opciones:**
| Opcion | Descripcion |
|--------|-------------|
| `--file` | Archivo JSON de entrada (requerido) |
| `--convocatoria` | ID de convocatoria para asociar |
| `--company` | ID de company IOMAD por defecto |
| `--department` | ID de department IOMAD por defecto |
| `--opendate` | Fecha apertura (YYYY-MM-DD) |
| `--closedate` | Fecha cierre (YYYY-MM-DD) |
| `--dryrun` | Simular sin crear registros |
| `--update` | Actualizar vacantes existentes |
| `--status` | Estado inicial (draft/published) |

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

### Paso 3: Importar Vacantes (Dry Run)

```bash
php local/jobboard/cli/import_vacancies.php \
    --file=local/jobboard/perfiles_2026.json \
    --convocatoria=1 \
    --opendate=2026-01-15 \
    --closedate=2026-02-15 \
    --dryrun
```

### Paso 4: Importar Vacantes (Real)

```bash
php local/jobboard/cli/import_vacancies.php \
    --file=local/jobboard/perfiles_2026.json \
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
| `cli/parse_profiles.php` | Parser v1 (deprecado) |
| `cli/parse_profiles_v2.php` | Parser v2 mejorado |
| `cli/import_vacancies.php` | Script de importacion |
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

- [x] **COMPLETADO** - Scripts CLI creados y funcionales

---

## 10. Proximos Pasos

1. Configurar companies y departments en IOMAD segun arquitectura
2. Crear convocatoria para agrupar vacantes
3. Ejecutar importacion en entorno de pruebas
4. Validar datos importados
5. Ajustar y completar informacion faltante
6. Publicar convocatoria

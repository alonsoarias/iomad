# Auditoría 1C: APIs Externas y Formularios

**Fecha:** 2025-12-06
**Plugin:** local_jobboard
**Versión auditada:** 1.9.30-beta

## Resumen

| Métrica | Valor |
|---------|-------|
| Archivos API analizados | 1 |
| Funciones API | 10 |
| Formularios analizados | 7 |
| Hallazgos Críticos | 0 |
| Hallazgos Mayores | 1 |
| Hallazgos Menores | 6 |
| Observaciones | 4 |

---

## APIs Externas

### classes/external/api.php (1238 líneas)

#### Información General

| Verificación | Estado |
|--------------|--------|
| Licencia GPL | ✅ OK |
| MOODLE_INTERNAL | ✅ OK |
| declare(strict_types=1) | ✅ OK |
| Extiende \external_api | ✅ OK |
| Namespace correcto | ✅ OK (local_jobboard\external) |
| require_once externallib.php | ✅ OK |

#### Funciones Registradas

| Función | Línea | En services.php | Tipo | login_required |
|---------|-------|-----------------|------|----------------|
| get_vacancies | 80 | ✅ | read | false |
| get_vacancy | 226 | ✅ | read | false |
| filter_vacancies | 333 | ✅ | read | false |
| get_applications | 481 | ✅ | read | true |
| get_application | 592 | ✅ | read | true |
| check_application_limit | 713 | ✅ | read | false |
| revoke_token | 887 | ✅ | write | true |
| enable_token | 939 | ✅ | write | true |
| delete_token | 991 | ✅ | write | true |
| get_departments | 1043 | ✅ | read | true |

#### Verificaciones de Seguridad por Función

| Función | validate_parameters | validate_context | Capabilities |
|---------|---------------------|------------------|--------------|
| get_vacancies | ✅ L91 | ✅ L102 | ✅ has_capability |
| get_vacancy | ✅ L230 | ✅ L234 | ✅ has_capability |
| filter_vacancies | ✅ L342 | ✅ L351 | ✅ has_capability |
| get_applications | ✅ L490 | ✅ L499 | ✅ require_capability L509 |
| get_application | ✅ L596 | ✅ L600 | ✅ has_capability |
| check_application_limit | ✅ L717 | ✅ L723 | ✅ has_capability |
| revoke_token | ✅ L888 | ✅ L890 | ✅ require_capability L891 |
| enable_token | ✅ L940 | ✅ L942 | ✅ require_capability L943 |
| delete_token | ✅ L992 | ✅ L994 | ✅ require_capability L995 |
| get_departments | ✅ L1045 | ✅ L1051 | ✅ require_capability L1054 |

#### Hallazgos API

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| 146-147 | Menor | Uso de `function_exists()` para verificar función - patrón frágil | Usar class_exists() o verificar mediante configuración |
| 352 | Observación | `$PAGE->set_context()` dentro de función API - puede causar efectos secundarios | Documentar o mover a controlador |
| 1205 | Menor | Función `local_jobboard_get_application_statuses()` llamada sin namespace - podría fallar si no está cargada | Verificar que lib.php esté incluido |

#### Estructura _parameters / _returns

| Función | _parameters() | _returns() | Tipos correctos |
|---------|---------------|------------|-----------------|
| get_vacancies | ✅ | ✅ | ✅ |
| get_vacancy | ✅ | ✅ | ✅ |
| filter_vacancies | ✅ | ✅ | ✅ |
| get_applications | ✅ | ✅ | ✅ |
| get_application | ✅ | ✅ | ✅ |
| check_application_limit | ✅ | ✅ | ✅ |
| revoke_token | ✅ | ✅ | ✅ |
| enable_token | ✅ | ✅ | ✅ |
| delete_token | ✅ | ✅ | ✅ |
| get_departments | ✅ | ✅ | ✅ |

---

## Formularios

### vacancy_form.php (367 líneas)

| Verificación | Estado |
|--------------|--------|
| Licencia GPL | ✅ OK |
| MOODLE_INTERNAL | ✅ OK |
| declare(strict_types=1) | ✅ OK |
| Extiende \moodleform | ✅ OK |
| definition() | ✅ OK |
| validation() | ✅ OK |
| Uso addElement() | ✅ OK |
| Uso addRule() | ✅ OK |
| Uso setType() | ✅ OK |
| Labels get_string() | ✅ OK |
| Help buttons | ✅ OK |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| - | - | Sin hallazgos negativos | - |

**Campos implementados:** code, title, description (editor), contracttype, duration, salary, location, department, opendate, closedate, positions, requirements (editor), desirable (editor), convocatoriaid, companyid, departmentid, publicationtype.

---

### application_form.php (336 líneas)

| Verificación | Estado |
|--------------|--------|
| Licencia GPL | ✅ OK (líneas 5-18) |
| MOODLE_INTERNAL | ✅ OK |
| declare(strict_types=1) | ⚠️ Línea 3 (antes de GPL) |
| Extiende moodleform | ✅ OK |
| definition() | ✅ OK |
| validation() | ✅ OK |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| 1-3 | Menor | Encabezado duplicado: `declare(strict_types=1)` antes del comentario GPL | Mover declare() después del bloque GPL |
| 244 | Menor | Typo: `signaturetoooshort` (tres 'o') | Corregir a `signaturetooshort` en lang file |

**Funcionalidad:**
- ✅ Captura consentimiento digital con IP y timestamp
- ✅ Validación de documentos requeridos
- ✅ Verificación de fechas de emisión de documentos
- ✅ Declaración jurada

---

### convocatoria_form.php (188 líneas)

| Verificación | Estado |
|--------------|--------|
| Licencia GPL | ✅ OK |
| MOODLE_INTERNAL | ✅ OK |
| declare(strict_types=1) | ✅ OK |
| Extiende \moodleform | ✅ OK |
| definition() | ✅ OK |
| validation() | ✅ OK |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| 83 | Observación | Usa date_selector en lugar de date_time_selector para fechas | Considerar date_time_selector para mayor precisión |

**Campos implementados:** code, name, description (editor), startdate, enddate, publicationtype, companyid, departmentid, terms (editor).

---

### api_token_form.php (172 líneas)

| Verificación | Estado |
|--------------|--------|
| Licencia GPL | ✅ OK (líneas 5-18) |
| MOODLE_INTERNAL | ✅ OK |
| declare(strict_types=1) | ⚠️ Línea 3 (antes de GPL) |
| Extiende \moodleform | ✅ OK |
| definition() | ✅ OK |
| validation() | ✅ OK |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| 1-3 | Menor | Encabezado duplicado: `declare(strict_types=1)` antes del comentario GPL | Mover declare() después del bloque GPL |

**Funcionalidad:**
- ✅ Selector de usuario con autocompletar
- ✅ Permisos granulares por checkbox
- ✅ Validación de IP con soporte CIDR
- ✅ Periodo de validez opcional

---

### exemption_form.php (222 líneas)

| Verificación | Estado |
|--------------|--------|
| Licencia GPL | ✅ OK (líneas 5-18) |
| MOODLE_INTERNAL | ✅ OK |
| declare(strict_types=1) | ⚠️ Implícito (falta) |
| Extiende \moodleform | ✅ OK |
| definition() | ✅ OK |
| validation() | ✅ OK |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| 1-2 | Menor | `declare(strict_types=1)` antes del comentario GPL | Mover declare() después del bloque GPL |
| 134-156 | **Mayor** | JavaScript inline dentro del formulario - viola CSP y buenas prácticas Moodle | Mover a archivo AMD (amd/src/exemption_form.js) |
| 117-157 | Menor | HTML crudo con botones onclick - problema de seguridad potencial | Usar `$PAGE->requires->js_call_amd()` |

**Código problemático (líneas 134-156):**
```php
$mform->addElement('html', '
    <script>
    function selectAllDocs() { ... }
    function selectIdentityDocs() { ... }
    function selectBackgroundDocs() { ... }
    </script>
');
```

**Corrección sugerida:**
```php
global $PAGE;
$PAGE->requires->js_call_amd('local_jobboard/exemption_form', 'init');
```

---

### signup_form.php (464 líneas)

| Verificación | Estado |
|--------------|--------|
| Licencia GPL | ✅ OK |
| MOODLE_INTERNAL | ✅ OK |
| declare(strict_types=1) | ❌ Falta |
| Extiende moodleform | ✅ OK |
| definition() | ✅ OK |
| validation() | ✅ OK (muy completo) |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| - | Menor | Falta `declare(strict_types=1)` | Agregar después del bloque GPL |

**Puntos positivos:**
- ✅ Validación exhaustiva de todos los campos
- ✅ Verificación de edad mínima (18 años)
- ✅ Verificación de políticas de contraseña
- ✅ Validación de email único
- ✅ Soporte reCAPTCHA
- ✅ Consentimiento de datos y términos

**Campos implementados:** password, email, email2, firstname, lastname, doctype, idnumber, birthdate, gender, phone1, phone2, address, city, department_region, country, education_level, degree_title, institution, expertise_area, experience_years, description, companyid, departmentid, policyagreed, datatreatmentagreed, dataaccuracy.

---

### updateprofile_form.php (345 líneas)

| Verificación | Estado |
|--------------|--------|
| Licencia GPL | ✅ OK |
| MOODLE_INTERNAL | ✅ OK |
| declare(strict_types=1) | ❌ Falta |
| Extiende moodleform | ✅ OK |
| definition() | ✅ OK |
| validation() | ✅ OK |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| - | Menor | Falta `declare(strict_types=1)` | Agregar después del bloque GPL |
| 250 | Observación | Llamada a `local_jobboard_get_departments()` sin prefijo namespace | Verificar que la función esté disponible |

---

## Verificación de Strings en lang/

### Strings usados en formularios (verificación de existencia)

| Formulario | Strings verificados | Estado |
|------------|---------------------|--------|
| vacancy_form | vacancycode, vacancytitle, vacancydescription, contracttype, etc. | ✅ OK |
| application_form | consentheader, consentaccepttext, digitalsignature, etc. | ✅ OK |
| convocatoria_form | convocatoriacode, convocatorianame, convocatoriadescription, etc. | ✅ OK |
| api_token_form | permissions, api:token:*, etc. | ✅ OK |
| exemption_form | exemptiontype, exempteddocs, doctype_*, etc. | ✅ OK |
| signup_form | signup_*, etc. | ✅ OK |

---

## Correspondencia API - services.php

| Función en api.php | Registrada en services.php | Coincidencia |
|--------------------|---------------------------|--------------|
| get_vacancies | ✅ local_jobboard_get_vacancies | ✅ |
| get_vacancy | ✅ local_jobboard_get_vacancy | ✅ |
| filter_vacancies | ✅ local_jobboard_filter_vacancies | ✅ |
| get_applications | ✅ local_jobboard_get_applications | ✅ |
| get_application | ✅ local_jobboard_get_application | ✅ |
| check_application_limit | ✅ local_jobboard_check_application_limit | ✅ |
| revoke_token | ✅ local_jobboard_revoke_token | ✅ |
| enable_token | ✅ local_jobboard_enable_token | ✅ |
| delete_token | ✅ local_jobboard_delete_token | ✅ |
| get_departments | ✅ local_jobboard_get_departments | ✅ |

**Servicio web definido:** `local_jobboard_ws` (Job Board Web Services)

---

## Lista de Correcciones Prioritarias

### Críticos (0)
*Ninguno*

### Mayores (1)

| # | Archivo | Línea | Descripción |
|---|---------|-------|-------------|
| 1 | exemption_form.php | 134-156 | JavaScript inline viola CSP. Mover a módulo AMD |

### Menores (6)

| # | Archivo | Línea | Descripción |
|---|---------|-------|-------------|
| 1 | application_form.php | 1-3 | declare(strict_types) antes del bloque GPL |
| 2 | api_token_form.php | 1-3 | declare(strict_types) antes del bloque GPL |
| 3 | exemption_form.php | 1-2 | declare(strict_types) antes del bloque GPL |
| 4 | signup_form.php | - | Falta declare(strict_types=1) |
| 5 | updateprofile_form.php | - | Falta declare(strict_types=1) |
| 6 | api.php | 1205 | Función sin namespace podría fallar |

---

## Observaciones Generales

### Aspectos Positivos

1. **API bien estructurada**: Todas las funciones siguen el patrón correcto de Moodle 4.1+.

2. **Seguridad API sólida**:
   - validate_parameters() en todas las funciones
   - validate_context() en todas las funciones
   - Verificación de capabilities apropiada

3. **Formularios completos**: Validación exhaustiva del lado servidor y cliente.

4. **Documentación PHPDoc**: Completa en todos los archivos.

5. **Soporte multi-tenant**: Integración correcta con IOMAD en formularios y API.

### Áreas de Mejora

1. **JavaScript inline**: exemption_form.php tiene JS inline que debería moverse a AMD.

2. **Consistencia declare(strict_types)**: Algunos archivos tienen el declare antes del bloque GPL.

3. **Dependencias implícitas**: Algunas funciones de lib.php se llaman sin verificar que estén cargadas.

---

## Próximos Pasos Recomendados

1. **Corregir hallazgo Mayor**: Crear `amd/src/exemption_form.js` y mover JavaScript inline.

2. **Estandarizar encabezados**: Mover declare(strict_types=1) después del bloque GPL en todos los archivos.

3. **Agregar strict_types**: signup_form.php y updateprofile_form.php.

4. **Verificar typo**: Corregir `signaturetoooshort` en archivo de idioma.

---

*Auditoría generada automáticamente - Fase 1C*

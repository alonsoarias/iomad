# Auditoría 1D: Vistas PHP y Seguridad

**Fecha:** 2025-12-06
**Plugin:** local_jobboard
**Versión auditada:** 1.9.30-beta

## Resumen Ejecutivo

| Métrica | Valor |
|---------|-------|
| Entry points analizados | 7 |
| Views analizados | 6 |
| Hallazgos Críticos | 0 |
| Hallazgos Mayores | 2 |
| Hallazgos Menores | 4 |
| Observaciones | 3 |

### Resumen de Seguridad

| Verificación | Cumple | Archivos con problemas |
|--------------|--------|------------------------|
| require_login() | ✅ | 0 |
| require_capability() | ✅ | 0 |
| Parámetros sanitizados | ⚠️ | 1 (signup.php) |
| Protección CSRF | ✅ | 0 |
| Output escapado | ✅ | 0 |

---

## PROBLEMAS DE SEGURIDAD

### Mayores

| # | Archivo | Línea | Vulnerabilidad | Riesgo | Corrección |
|---|---------|-------|----------------|--------|------------|
| 1 | signup.php | 393 | Acceso directo a `$_SERVER['HTTP_USER_AGENT']` | Medio - Potencial XSS en logs | Usar `$_SERVER` con sanitización o función auxiliar |
| 2 | signup.php | 514 | Acceso directo a `$_SERVER['HTTP_USER_AGENT']` en audit log | Medio - Potencial XSS en logs | Sanitizar antes de almacenar |

**Código problemático (línea 393):**
```php
$useragent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512);
```

**Corrección sugerida:**
```php
$useragent = substr(clean_param($_SERVER['HTTP_USER_AGENT'] ?? '', PARAM_TEXT), 0, 512);
```

---

## Entry Points Principales

### index.php (150 líneas) - Router Principal

| Verificación | Estado | Línea |
|--------------|--------|-------|
| require_once config.php | ✅ | 45 |
| require_login() | ✅ Condicional | 60-62 |
| context_system::instance() | ✅ | 54 |
| $PAGE->set_url() | ✅ | 76 |
| $PAGE->set_context() | ✅ | 65 |
| optional_param() | ✅ | 49-51 |
| Vistas públicas definidas | ✅ | 57 |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| 57 | Observación | Solo 'public' definida como vista sin auth | Documentar cuáles son públicas |

**Flujo de autenticación:**
- Vistas que NO requieren login: `public`
- Todas las demás vistas requieren login
- Capabilities se verifican dentro de cada vista

---

### edit.php (337 líneas) - Crear/Editar Vacantes

| Verificación | Estado | Línea |
|--------------|--------|-------|
| require_once config.php | ✅ | 27 |
| require_login() | ✅ | 32 |
| require_capability() | ✅ | 40, 54 |
| context_system::instance() | ✅ | 37 |
| $PAGE->set_url() | ✅ | 60 |
| $PAGE->set_context() | ✅ | 59 |
| optional_param() | ✅ | 34-35 |
| Formulario moodleform | ✅ | 211 |
| CSRF (via moodleform) | ✅ | Implícito |
| Output escapado (s(), format_string) | ✅ | Múltiples |

**Sin hallazgos de seguridad.**

---

### public.php (39 líneas) - Redirect

| Verificación | Estado | Línea |
|--------------|--------|-------|
| require_once config.php | ✅ | 28 |
| optional_param() | ✅ | 31 |
| redirect() | ✅ | 38 |

**Solo redirect, sin lógica de negocio.**

---

### signup.php (524 líneas) - Registro de Usuarios

| Verificación | Estado | Línea |
|--------------|--------|-------|
| require_once config.php | ✅ | 34 |
| NO_MOODLE_COOKIES | ✅ | 31 |
| isloggedin() check | ✅ | 42-49 |
| context_system::instance() | ✅ | 74 |
| $PAGE->set_url() | ✅ | 76 |
| $PAGE->set_context() | ✅ | 75 |
| optional_param() | ✅ | 62 |
| Formulario moodleform | ✅ | 109 |
| Transacción DB | ✅ | 285 |
| hash_internal_user_password() | ✅ | 263 |
| Validación email | ✅ | Via form |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| 393 | **Mayor** | `$_SERVER['HTTP_USER_AGENT']` sin sanitización | Usar clean_param() |
| 514 | **Mayor** | `$_SERVER['HTTP_USER_AGENT']` sin sanitización | Usar clean_param() |
| 260 | Observación | Username derivado de idnumber con regex - podría colisionar | Documentar comportamiento |

**Aspectos positivos de seguridad:**
- ✅ Verificación de self-registration habilitado
- ✅ Transacciones para integridad de datos
- ✅ Hash seguro de contraseñas
- ✅ Validación de edad mínima
- ✅ Verificación de email único
- ✅ reCAPTCHA soportado

---

### validate_document.php (390 líneas) - Validación de Documentos

| Verificación | Estado | Línea |
|--------------|--------|-------|
| require_once config.php | ✅ | 25 |
| require_login() | ✅ | 34 |
| require_capability() | ✅ | 37 |
| required_param() | ✅ | 31 |
| optional_param() | ✅ | 32 |
| require_sesskey() | ✅ | 67 |
| context_system::instance() | ✅ | 36 |
| $PAGE->set_url() | ✅ | 52 |
| sesskey en forms | ✅ | 249, 264 |
| Output escapado | ✅ | format_string() |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| - | - | Sin hallazgos | - |

**Excelente implementación de seguridad:**
- ✅ CSRF protection completa
- ✅ Capability check antes de cualquier acción
- ✅ Validación de documento y aplicación existen
- ✅ Audit logging

---

### export_documents.php (299 líneas) - Exportación de Documentos

| Verificación | Estado | Línea |
|--------------|--------|-------|
| require_once config.php | ✅ | 25 |
| require_login() | ✅ | 36 |
| has_capability() | ✅ | 41-43 |
| require_capability() | ✅ | 77 |
| optional_param() | ✅ | 33-34 |
| context_system::instance() | ✅ | 38 |
| send_file() | ✅ | 181, 280 |
| Audit logging | ✅ | 175, 274 |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| - | - | Sin hallazgos | - |

**Aspectos positivos:**
- ✅ Doble verificación de capabilities
- ✅ Audit log de exportaciones
- ✅ Limpieza de archivos temporales

---

### updateprofile.php (275 líneas) - Actualización de Perfil

| Verificación | Estado | Línea |
|--------------|--------|-------|
| require_once config.php | ✅ | 28 |
| require_login() | ✅ | 33 |
| context_system::instance() | ✅ | 44 |
| $PAGE->set_url() | ✅ | 46 |
| $PAGE->set_context() | ✅ | 45 |
| optional_param() | ✅ | 36-37 |
| Formulario moodleform | ✅ | 90 |
| Verificación usuario actual | ✅ | 41 |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| - | - | Sin hallazgos | - |

---

## Views (Incluidas desde index.php)

### views/dashboard.php (394 líneas)

| Verificación | Estado | Línea |
|--------------|--------|-------|
| defined('MOODLE_INTERNAL') | ✅ | 27 |
| has_capability() | ✅ | 38-41 |
| $PAGE setup | ✅ | 32-35 |
| Output escapado | ✅ | html_writer, s() |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| 361-391 | Menor | CSS inline mediante html_writer::tag('style') | Mover a styles.css |

---

### views/vacancies.php (372 líneas)

| Verificación | Estado | Línea |
|--------------|--------|-------|
| defined('MOODLE_INTERNAL') | ✅ | 27 |
| has_capability() | ✅ | 60, 153, 163 |
| optional_param() | ✅ | 34-39 |
| Output escapado | ✅ | s(), format_string() |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| 338-368 | Menor | CSS inline | Mover a styles.css |

---

### views/vacancy.php (440 líneas)

| Verificación | Estado | Línea |
|--------------|--------|-------|
| defined('MOODLE_INTERNAL') | ✅ | 27 |
| required_param() | ✅ | 34 |
| Verificación acceso | ✅ | 42-44 |
| has_capability() | ✅ | 59-60 |
| Audit logging | ✅ | 56 |
| Output escapado | ✅ | s(), format_string(), format_text() |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| - | - | Sin hallazgos | - |

---

### views/applications.php (380 líneas)

| Verificación | Estado | Línea |
|--------------|--------|-------|
| defined('MOODLE_INTERNAL') | ✅ | 27 |
| require_capability() | ✅ | 36 |
| optional_param() | ✅ | 39-41 |
| Filtro userid forzado | ✅ | 54 |
| Output escapado | ✅ | format_string() |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| 363-376 | Menor | CSS inline | Mover a styles.css |

**Excelente práctica de seguridad:**
- Línea 54: `$filters = ['userid' => $USER->id]` - Fuerza filtro por usuario actual

---

### views/public.php (758 líneas)

| Verificación | Estado | Línea |
|--------------|--------|-------|
| defined('MOODLE_INTERNAL') | ✅ | 27 |
| Verificación enable_public_page | ✅ | 34-37 |
| has_capability() | ✅ | 66-67 |
| optional_param() | ✅ | 40-47 |
| SQL con placeholders | ✅ | 88-136 |
| sql_like_escape() | ✅ | 117, 135 |
| Output escapado | ✅ | s(), format_string(), format_text() |

| Línea | Severidad | Descripción | Corrección |
|-------|-----------|-------------|------------|
| 473-483 | Menor | CSS inline | Mover a styles.css |

**Aspectos positivos:**
- ✅ Verificación de configuración antes de mostrar
- ✅ Filtrado por publicationtype
- ✅ SQL injection prevention con placeholders
- ✅ Escape correcto de búsqueda con sql_like_escape()

---

## Matriz de Seguridad por Archivo

| Archivo | require_login | require_cap | sesskey | CSRF | Params | Output |
|---------|--------------|-------------|---------|------|--------|--------|
| index.php | ✅ Cond. | Delegado | N/A | N/A | ✅ | ✅ |
| edit.php | ✅ | ✅ | ✅ Form | ✅ | ✅ | ✅ |
| public.php | N/A | N/A | N/A | N/A | ✅ | N/A |
| signup.php | N/A (público) | N/A | ✅ Form | ✅ | ⚠️ | ✅ |
| validate_document.php | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| export_documents.php | ✅ | ✅ | N/A | N/A | ✅ | ✅ |
| updateprofile.php | ✅ | N/A | ✅ Form | ✅ | ✅ | ✅ |
| views/dashboard.php | Heredado | ✅ | N/A | N/A | N/A | ✅ |
| views/vacancies.php | Heredado | ✅ | N/A | N/A | ✅ | ✅ |
| views/vacancy.php | Heredado | ✅ | N/A | N/A | ✅ | ✅ |
| views/applications.php | Heredado | ✅ | N/A | N/A | ✅ | ✅ |
| views/public.php | N/A (público) | ✅ Cond. | N/A | N/A | ✅ | ✅ |

---

## Lista de Correcciones Prioritarias

### Críticos (0)
*Ninguno*

### Mayores (2)

| # | Archivo | Línea | Descripción |
|---|---------|-------|-------------|
| 1 | signup.php | 393 | Sanitizar `$_SERVER['HTTP_USER_AGENT']` antes de almacenar |
| 2 | signup.php | 514 | Sanitizar `$_SERVER['HTTP_USER_AGENT']` antes de almacenar |

### Menores (4)

| # | Archivo | Línea | Descripción |
|---|---------|-------|-------------|
| 1 | views/dashboard.php | 361-391 | CSS inline debería estar en styles.css |
| 2 | views/vacancies.php | 338-368 | CSS inline debería estar en styles.css |
| 3 | views/applications.php | 363-376 | CSS inline debería estar en styles.css |
| 4 | views/public.php | 473-483 | CSS inline debería estar en styles.css |

---

## Observaciones Generales

### Aspectos Positivos

1. **Arquitectura de router centralizado**: index.php como punto de entrada único facilita control de seguridad.

2. **Uso consistente de moodleform**: Todos los formularios usan la clase moodleform que incluye CSRF protection automática.

3. **Verificación de capabilities**: Todas las vistas verifican capabilities apropiadamente.

4. **Output escaping**: Uso consistente de s(), format_string(), format_text() para prevenir XSS.

5. **SQL injection prevention**: Uso correcto de placeholders y sql_like_escape().

6. **Audit logging**: Operaciones sensibles se registran en tabla de auditoría.

7. **Transacciones**: Operaciones críticas usan transacciones DB para integridad.

### Áreas de Mejora

1. **CSS inline**: Múltiples vistas tienen CSS inline que debería consolidarse en styles.css.

2. **Documentación de vistas públicas**: Documentar explícitamente cuáles vistas son accesibles sin autenticación.

3. **Sanitización de headers HTTP**: Sanitizar User-Agent antes de almacenar.

---

## Próximos Pasos Recomendados

1. **Corregir hallazgos Mayores**: Sanitizar `$_SERVER['HTTP_USER_AGENT']` en signup.php líneas 393 y 514.

2. **Consolidar CSS**: Mover estilos inline a styles.css para mejor mantenibilidad.

3. **Documentar flujo de autenticación**: Crear diagrama de qué vistas requieren qué nivel de acceso.

---

*Auditoría generada automáticamente - Fase 1D*

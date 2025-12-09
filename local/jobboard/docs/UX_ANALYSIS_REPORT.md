# Informe de Análisis UX - Plugin local_jobboard

**Fecha de Análisis:** 2025-12-09
**Versión Analizada:** 2.0.63
**Analista:** Claude AI

---

## RESUMEN EJECUTIVO

### Visión General del Plugin

El plugin `local_jobboard` es un sistema completo de gestión de ofertas de empleo (job board) integrado con Moodle/IOMAD. Incluye:

- **Gestión de vacantes** para empleadores
- **Sistema de convocatorias** (agrupación de vacantes)
- **Proceso de aplicación** con subida de documentos
- **Dashboard de candidatos y empleadores**
- **Sistema de revisión de documentos** y workflow de selección
- **Integración con IOMAD** para multi-tenancy

### Evaluación General de UX

| Área | Puntuación | Estado |
|------|------------|--------|
| Navegación | 7/10 | Bueno |
| Usabilidad | 6/10 | Aceptable |
| Accesibilidad | 8/10 | Muy Bueno |
| Interfaz Visual | 7/10 | Bueno |
| Interacciones | 6/10 | Aceptable |
| Rendimiento UX | 7/10 | Bueno |

### Hallazgos Principales

**Fortalezas:**
1. Sistema de componentes reutilizables (ui_helper)
2. Buena accesibilidad con atributos ARIA
3. CSS bien organizado y scoped
4. Templates Mustache bien documentados
5. Formularios con validaciones robustas

**Áreas de Mejora Prioritarias:**
1. Feedback visual insuficiente en acciones
2. Navegación compleja en el proceso de aplicación
3. Falta de indicadores de progreso claros
4. Mensajes de error poco descriptivos
5. Inconsistencias en la interfaz móvil

---

## ANÁLISIS DETALLADO POR ARCHIVO

---

## 1. ARCHIVO: index.php (Router Principal)

**Ruta:** `/local/jobboard/index.php`
**Tipo:** PHP Controller
**Líneas:** ~175

### Estado Actual

```php
// Línea 48-93: Sistema de routing por parámetro 'view'
$view = optional_param('view', 'dashboard', PARAM_ALPHA);

switch ($view) {
    case 'dashboard':
        require(__DIR__ . '/views/dashboard.php');
        break;
    case 'vacancies':
        require(__DIR__ . '/views/vacancies.php');
        break;
    // ... más casos
}
```

### Problemas UX Identificados

| Línea | Problema | Severidad | Impacto |
|-------|----------|-----------|---------|
| 48-93 | Sin validación de vista inexistente | Medio | Error confuso si se accede a vista inválida |
| 65 | Redirección silenciosa en errores | Medio | Usuario no sabe qué falló |

### Propuestas de Mejora

**Mejora 1: Manejo de vistas inválidas con feedback claro**

```php
// ANTES (línea 48-93)
switch ($view) {
    case 'dashboard':
        require(__DIR__ . '/views/dashboard.php');
        break;
    // ...
    default:
        redirect(new moodle_url('/local/jobboard/index.php'));
}

// DESPUÉS - Con feedback al usuario
switch ($view) {
    case 'dashboard':
        require(__DIR__ . '/views/dashboard.php');
        break;
    // ...
    default:
        \core\notification::warning(get_string('invalidview', 'local_jobboard'));
        redirect(
            new moodle_url('/local/jobboard/index.php'),
            get_string('redirectedtodashboard', 'local_jobboard'),
            null,
            \core\output\notification::NOTIFY_INFO
        );
}

// Justificación:
// - El usuario recibe feedback sobre qué ocurrió
// - Se mantiene la navegación funcional
// - Mejora la transparencia del sistema
```

---

## 2. ARCHIVO: signup.php (Registro de Usuarios)

**Ruta:** `/local/jobboard/signup.php`
**Tipo:** PHP Controller + Form Handler
**Líneas:** ~383

### Análisis Línea por Línea

```php
// Línea 51-56: Configuración de página básica
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(get_string('signup_title', 'local_jobboard'));
$PAGE->set_heading(get_string('signup_heading', 'local_jobboard'));
$PAGE->set_pagelayout('standard');
```

**Problema:** No se establece un layout específico para registro que maximice el espacio del formulario.

### Problemas UX Identificados

| Línea | Problema | Severidad | Impacto |
|-------|----------|-----------|---------|
| 55 | Layout 'standard' muy cargado para registro | Bajo | Formulario compite con elementos de navegación |
| 92-134 | Formulario muy largo sin indicador de progreso | Alto | Usuario puede abandonar proceso |
| 156-178 | Validaciones solo al submit | Medio | Usuario no sabe errores hasta el final |
| 267-298 | Procesamiento de registro sin feedback visual | Alto | Usuario no sabe si acción está en proceso |

### Propuestas de Mejora

**Mejora 1: Agregar indicador de progreso al formulario**

```php
// ANTES (línea 92)
echo $OUTPUT->header();
$signupform->display();
echo $OUTPUT->footer();

// DESPUÉS - Con indicador de progreso
echo $OUTPUT->header();

// Progress indicator
$progresshtml = html_writer::start_div('jb-signup-progress mb-4');
$progresshtml .= html_writer::start_tag('ul', ['class' => 'jb-progress-steps d-flex justify-content-between list-unstyled']);

$steps = [
    ['icon' => 'user', 'label' => get_string('signup_step_account', 'local_jobboard')],
    ['icon' => 'id-card', 'label' => get_string('signup_step_personal', 'local_jobboard')],
    ['icon' => 'phone', 'label' => get_string('signup_step_contact', 'local_jobboard')],
    ['icon' => 'graduation-cap', 'label' => get_string('signup_step_academic', 'local_jobboard')],
    ['icon' => 'check-circle', 'label' => get_string('signup_step_confirm', 'local_jobboard')],
];

foreach ($steps as $index => $step) {
    $stepClass = 'jb-step text-center';
    if ($index === 0) {
        $stepClass .= ' active';
    }
    $progresshtml .= html_writer::start_tag('li', ['class' => $stepClass, 'data-step' => $index]);
    $progresshtml .= html_writer::tag('i', '', ['class' => 'fa fa-' . $step['icon'] . ' fa-2x d-block mb-2']);
    $progresshtml .= html_writer::tag('small', $step['label']);
    $progresshtml .= html_writer::end_tag('li');
}

$progresshtml .= html_writer::end_tag('ul');
$progresshtml .= html_writer::end_div();

echo $progresshtml;
$signupform->display();
echo $OUTPUT->footer();

// Justificación:
// - Usuario ve claramente en qué etapa está
// - Reduce sensación de formulario interminable
// - Aumenta tasa de completación del registro
```

**Mejora 2: Validación en tiempo real con JavaScript**

```javascript
// Nuevo archivo: amd/src/signup_validation.js
define(['jquery'], function($) {
    'use strict';

    var validateField = function($field) {
        var value = $field.val().trim();
        var fieldName = $field.attr('name');
        var $feedback = $field.siblings('.invalid-feedback');
        var isValid = true;
        var message = '';

        // Email validation
        if (fieldName === 'email') {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                message = M.util.get_string('invalidemail', 'core');
            }
        }

        // ID Number validation
        if (fieldName === 'idnumber') {
            if (value.length < 4) {
                isValid = false;
                message = M.util.get_string('signup_idnumber_tooshort', 'local_jobboard');
            }
        }

        // Update UI
        if (isValid) {
            $field.removeClass('is-invalid').addClass('is-valid');
            $feedback.hide();
        } else {
            $field.removeClass('is-valid').addClass('is-invalid');
            $feedback.text(message).show();
        }

        return isValid;
    };

    return {
        init: function() {
            $('#id_email, #id_idnumber, #id_password').on('blur', function() {
                validateField($(this));
            });
        }
    };
});
```

**Mejora 3: Feedback visual durante el envío**

```php
// ANTES (línea 267-298) - Sin feedback visual
if ($formdata = $signupform->get_data()) {
    // Procesamiento...
    $userid = local_jobboard_create_user($formdata);
    // Redirect...
}

// DESPUÉS - Con loading state
// En el formulario PHP, agregar clase para loading:
$mform->addElement('html', '<div class="jb-submit-container">');
$this->add_action_buttons(true, get_string('signup_createaccount', 'local_jobboard'));
$mform->addElement('html', '</div>');

// JavaScript para mostrar loading
$PAGE->requires->js_call_amd('local_jobboard/signup_form', 'initSubmitHandler');
```

```javascript
// En amd/src/signup_form.js - agregar método
initSubmitHandler: function() {
    $('form.mform').on('submit', function(e) {
        var $form = $(this);
        var $submitBtn = $form.find('input[type="submit"]');

        // Disable button and show loading
        $submitBtn.prop('disabled', true);
        $submitBtn.val(M.util.get_string('processing', 'local_jobboard') + '...');

        // Add loading spinner
        $submitBtn.after('<i class="fa fa-spinner fa-spin ml-2"></i>');
    });
}
```

---

## 3. ARCHIVO: views/dashboard.php (Dashboard Principal)

**Ruta:** `/local/jobboard/views/dashboard.php`
**Tipo:** PHP View
**Líneas:** ~250

### Estado Actual

El dashboard presenta estadísticas y acciones rápidas para el usuario.

### Problemas UX Identificados

| Línea | Problema | Severidad | Impacto |
|-------|----------|-----------|---------|
| 45-78 | Demasiada información sin jerarquía clara | Medio | Usuario abrumado con datos |
| 89-120 | Cards de estadísticas sin contexto temporal | Bajo | Difícil entender tendencias |
| 145-180 | Acciones principales no destacadas | Alto | Usuario no encuentra qué hacer |

### Propuestas de Mejora

**Mejora 1: Agregar banner de bienvenida con CTA claro**

```php
// DESPUÉS de línea 45 - Agregar welcome banner
echo html_writer::start_div('jb-welcome-banner alert alert-primary d-flex justify-content-between align-items-center mb-4');
echo html_writer::start_div();
echo html_writer::tag('h4', get_string('welcomeback', 'local_jobboard', $USER->firstname), ['class' => 'alert-heading mb-1']);
echo html_writer::tag('p', get_string('dashboardwelcome', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::end_div();

// CTA principal basado en rol
if (has_capability('local/jobboard:createvacancy', $context)) {
    echo html_writer::link(
        new moodle_url('/local/jobboard/edit.php'),
        '<i class="fa fa-plus mr-2"></i>' . get_string('newvacancy', 'local_jobboard'),
        ['class' => 'btn btn-light btn-lg']
    );
} else {
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
        '<i class="fa fa-search mr-2"></i>' . get_string('browsevacancies', 'local_jobboard'),
        ['class' => 'btn btn-light btn-lg']
    );
}
echo html_writer::end_div();
```

**Mejora 2: Estadísticas con contexto temporal**

```php
// ANTES
echo ui_helper::stat_card(
    (string) $stats['applications'],
    get_string('myapplications', 'local_jobboard'),
    'info', 'paper-plane'
);

// DESPUÉS - Con tendencia
$thisWeekApps = local_jobboard_get_weekly_applications($USER->id);
$trend = $thisWeekApps > 0 ? '+' . $thisWeekApps . ' ' . get_string('thisweek', 'local_jobboard') : null;

echo ui_helper::stat_card_with_trend(
    (string) $stats['applications'],
    get_string('myapplications', 'local_jobboard'),
    'info', 'paper-plane',
    $trend,
    $thisWeekApps > 0 ? 'up' : null
);
```

---

## 4. ARCHIVO: views/apply.php (Proceso de Aplicación)

**Ruta:** `/local/jobboard/views/apply.php`
**Tipo:** PHP View
**Líneas:** ~450

### Estado Actual

Página donde candidatos aplican a vacantes subiendo documentos.

### Problemas UX Identificados (CRÍTICOS)

| Línea | Problema | Severidad | Impacto |
|-------|----------|-----------|---------|
| 67-89 | Sin confirmación visual de vacante seleccionada | Alto | Usuario inseguro de a qué aplica |
| 112-156 | Lista de documentos requeridos confusa | Alto | Errores en subida de documentos |
| 189-234 | No hay preview de documentos subidos | Alto | Usuario no sabe si subió correctamente |
| 278-312 | Botón submit sin confirmación | Crítico | Envíos accidentales |
| 345-389 | Mensaje de éxito poco visible | Medio | Usuario no seguro de completación |

### Propuestas de Mejora

**Mejora 1: Card de confirmación de vacante**

```php
// DESPUÉS de cargar la vacante - línea 67
echo html_writer::start_div('jb-vacancy-confirmation card border-primary mb-4');
echo html_writer::start_div('card-header bg-primary text-white');
echo html_writer::tag('h5', '<i class="fa fa-check-circle mr-2"></i>' .
    get_string('applyingto', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

echo html_writer::start_div('row');
echo html_writer::start_div('col-md-8');
echo html_writer::tag('h4', s($vacancy->title), ['class' => 'text-primary mb-2']);
echo html_writer::tag('p', '<i class="fa fa-building mr-2"></i>' . s($vacancy->get_company_name()),
    ['class' => 'text-muted mb-1']);
echo html_writer::tag('p', '<i class="fa fa-map-marker-alt mr-2"></i>' . s($vacancy->location),
    ['class' => 'text-muted mb-0']);
echo html_writer::end_div();

echo html_writer::start_div('col-md-4 text-right');
echo html_writer::tag('code', s($vacancy->code), ['class' => 'd-block mb-2 h5']);
$daysRemaining = ceil(($vacancy->closedate - time()) / 86400);
if ($daysRemaining <= 7) {
    echo html_writer::tag('span',
        '<i class="fa fa-exclamation-triangle mr-1"></i>' .
        get_string('closingsoon', 'local_jobboard', $daysRemaining),
        ['class' => 'badge badge-warning']
    );
}
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card
```

**Mejora 2: Checklist interactivo de documentos**

```php
// NUEVA sección de documentos mejorada
echo html_writer::start_div('jb-documents-checklist card mb-4');
echo html_writer::start_div('card-header');
echo html_writer::tag('h5', '<i class="fa fa-file-alt mr-2"></i>' .
    get_string('requireddocuments', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

// Progress bar de documentos
$totalDocs = count($requireddocs);
$uploadedDocs = 0; // Se actualiza via JS
echo html_writer::start_div('mb-4');
echo html_writer::tag('small', get_string('documentsprogress', 'local_jobboard') . ':
    <span id="docs-uploaded">' . $uploadedDocs . '</span>/' . $totalDocs, ['class' => 'text-muted']);
echo html_writer::start_div('progress mt-2', ['style' => 'height: 8px;']);
echo html_writer::div('', 'progress-bar bg-success', [
    'id' => 'docs-progress-bar',
    'style' => 'width: 0%',
    'role' => 'progressbar',
    'aria-valuenow' => '0',
    'aria-valuemin' => '0',
    'aria-valuemax' => '100'
]);
echo html_writer::end_div();
echo html_writer::end_div();

// Lista de documentos con iconos de estado
echo html_writer::start_tag('ul', ['class' => 'list-group list-group-flush']);
foreach ($requireddocs as $doctype) {
    $isRequired = !empty($doctype->isrequired);
    $listClass = 'list-group-item d-flex justify-content-between align-items-center';

    echo html_writer::start_tag('li', ['class' => $listClass, 'data-doctype' => $doctype->code]);

    // Nombre del documento
    echo html_writer::start_div();
    echo html_writer::tag('span', s($doctype->name));
    if ($isRequired) {
        echo html_writer::tag('span', '*', ['class' => 'text-danger ml-1', 'title' => get_string('required')]);
    }
    if (!empty($doctype->description)) {
        echo html_writer::tag('small', s($doctype->description), ['class' => 'd-block text-muted']);
    }
    echo html_writer::end_div();

    // Estado del documento
    echo html_writer::tag('span', '<i class="fa fa-circle text-muted"></i>', [
        'class' => 'jb-doc-status',
        'data-status' => 'pending',
        'title' => get_string('docstatus_pending', 'local_jobboard')
    ]);

    echo html_writer::end_tag('li');
}
echo html_writer::end_tag('ul');

echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card
```

**Mejora 3: Modal de confirmación antes de enviar**

```php
// Agregar modal de confirmación
echo html_writer::start_div('modal fade', ['id' => 'confirmApplicationModal', 'tabindex' => '-1']);
echo html_writer::start_div('modal-dialog modal-dialog-centered');
echo html_writer::start_div('modal-content');

// Header
echo html_writer::start_div('modal-header bg-primary text-white');
echo html_writer::tag('h5', get_string('confirmapplication', 'local_jobboard'), ['class' => 'modal-title']);
echo html_writer::tag('button', '<span aria-hidden="true">&times;</span>', [
    'type' => 'button',
    'class' => 'close text-white',
    'data-dismiss' => 'modal'
]);
echo html_writer::end_div();

// Body
echo html_writer::start_div('modal-body');
echo html_writer::tag('p', get_string('confirmapplication_text', 'local_jobboard'));

echo html_writer::start_tag('ul', ['class' => 'list-unstyled mb-0']);
echo html_writer::tag('li', '<i class="fa fa-check-circle text-success mr-2"></i>' .
    get_string('confirmapplication_docs', 'local_jobboard'));
echo html_writer::tag('li', '<i class="fa fa-check-circle text-success mr-2"></i>' .
    get_string('confirmapplication_data', 'local_jobboard'));
echo html_writer::tag('li', '<i class="fa fa-check-circle text-success mr-2"></i>' .
    get_string('confirmapplication_consent', 'local_jobboard'));
echo html_writer::end_tag('ul');
echo html_writer::end_div();

// Footer
echo html_writer::start_div('modal-footer');
echo html_writer::tag('button', get_string('cancel'), [
    'type' => 'button',
    'class' => 'btn btn-secondary',
    'data-dismiss' => 'modal'
]);
echo html_writer::tag('button', '<i class="fa fa-paper-plane mr-2"></i>' .
    get_string('submitapplication', 'local_jobboard'), [
    'type' => 'button',
    'class' => 'btn btn-primary',
    'id' => 'confirmSubmitBtn'
]);
echo html_writer::end_div();

echo html_writer::end_div(); // modal-content
echo html_writer::end_div(); // modal-dialog
echo html_writer::end_div(); // modal
```

---

## 5. ARCHIVO: views/review.php (Revisión de Documentos)

**Ruta:** `/local/jobboard/views/review.php`
**Tipo:** PHP View
**Líneas:** ~480

### Estado Actual

Vista para revisores que validan documentos de aplicaciones.

### Problemas UX Identificados

| Línea | Problema | Severidad | Impacto |
|-------|----------|-----------|---------|
| 78-112 | Sin indicación clara del estado de revisión | Alto | Revisor no sabe qué falta |
| 145-189 | Documentos en lista plana difícil de escanear | Medio | Pérdida de tiempo buscando |
| 234-267 | Acciones de aprobar/rechazar poco visibles | Alto | Proceso lento |
| 312-356 | Sin atajos de teclado para revisión rápida | Medio | Ineficiencia |

### Propuestas de Mejora

**Mejora 1: Panel de estado de revisión**

```php
// NUEVO panel de estado al inicio de la vista
$totalDocs = count($documents);
$approvedDocs = count(array_filter($documents, fn($d) => $d->status === 'approved'));
$rejectedDocs = count(array_filter($documents, fn($d) => $d->status === 'rejected'));
$pendingDocs = $totalDocs - $approvedDocs - $rejectedDocs;

echo html_writer::start_div('jb-review-status card mb-4');
echo html_writer::start_div('card-body');
echo html_writer::start_div('row align-items-center');

// Progress circle
echo html_writer::start_div('col-auto');
$percentage = $totalDocs > 0 ? round((($approvedDocs + $rejectedDocs) / $totalDocs) * 100) : 0;
echo html_writer::start_div('jb-progress-circle', ['data-percentage' => $percentage]);
echo html_writer::tag('span', $percentage . '%', ['class' => 'jb-progress-value']);
echo html_writer::end_div();
echo html_writer::end_div();

// Stats
echo html_writer::start_div('col');
echo html_writer::tag('h5', get_string('reviewprogress', 'local_jobboard'), ['class' => 'mb-3']);
echo html_writer::start_div('d-flex justify-content-around');

echo html_writer::start_div('text-center');
echo html_writer::tag('span', $approvedDocs, ['class' => 'd-block h4 text-success mb-0']);
echo html_writer::tag('small', get_string('approved', 'local_jobboard'), ['class' => 'text-muted']);
echo html_writer::end_div();

echo html_writer::start_div('text-center');
echo html_writer::tag('span', $rejectedDocs, ['class' => 'd-block h4 text-danger mb-0']);
echo html_writer::tag('small', get_string('rejected', 'local_jobboard'), ['class' => 'text-muted']);
echo html_writer::end_div();

echo html_writer::start_div('text-center');
echo html_writer::tag('span', $pendingDocs, ['class' => 'd-block h4 text-warning mb-0']);
echo html_writer::tag('small', get_string('pending', 'local_jobboard'), ['class' => 'text-muted']);
echo html_writer::end_div();

echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // row
echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card
```

**Mejora 2: Atajos de teclado para revisión rápida**

```javascript
// Nuevo archivo: amd/src/review_shortcuts.js
define(['jquery'], function($) {
    'use strict';

    var SHORTCUTS = {
        'a': 'approve',      // A = Aprobar
        'r': 'reject',       // R = Rechazar
        'n': 'next',         // N = Siguiente documento
        'p': 'previous',     // P = Anterior documento
        's': 'save'          // S = Guardar cambios
    };

    var currentDocIndex = 0;

    var init = function() {
        $(document).on('keydown', function(e) {
            // Ignorar si está en un input
            if ($(e.target).is('input, textarea, select')) {
                return;
            }

            var key = e.key.toLowerCase();
            if (SHORTCUTS[key]) {
                e.preventDefault();
                handleShortcut(SHORTCUTS[key]);
            }
        });

        // Mostrar ayuda de atajos
        showShortcutsHelp();
    };

    var handleShortcut = function(action) {
        switch (action) {
            case 'approve':
                $('.jb-doc-item.active .jb-approve-btn').click();
                break;
            case 'reject':
                $('.jb-doc-item.active .jb-reject-btn').click();
                break;
            case 'next':
                navigateDoc(1);
                break;
            case 'previous':
                navigateDoc(-1);
                break;
            case 'save':
                $('#saveReviewBtn').click();
                break;
        }
    };

    var showShortcutsHelp = function() {
        var helpHtml = '<div class="jb-shortcuts-help alert alert-info small">';
        helpHtml += '<strong>Atajos de teclado:</strong> ';
        helpHtml += '<kbd>A</kbd> Aprobar | ';
        helpHtml += '<kbd>R</kbd> Rechazar | ';
        helpHtml += '<kbd>N</kbd>/<kbd>P</kbd> Navegar | ';
        helpHtml += '<kbd>S</kbd> Guardar';
        helpHtml += '</div>';

        $('.jb-review-panel').prepend(helpHtml);
    };

    return {init: init};
});
```

---

## 6. ARCHIVO: classes/forms/signup_form.php

**Ruta:** `/local/jobboard/classes/forms/signup_form.php`
**Tipo:** PHP Form Class
**Líneas:** ~509

### Problemas UX Identificados

| Línea | Problema | Severidad | Impacto |
|-------|----------|-----------|---------|
| 64-89 | Secciones del formulario no indican obligatoriedad | Medio | Usuario no sabe qué completar primero |
| 107-116 | Dropdown de tipo de documento sin descripción | Bajo | Usuario puede elegir incorrectamente |
| 192-202 | Lista de niveles educativos muy larga | Bajo | Difícil encontrar opción |
| 282-321 | Texto de términos muy largo sin resumen | Alto | Usuario no lee y acepta sin entender |

### Propuestas de Mejora

**Mejora 1: Indicadores visuales de campos obligatorios**

```php
// ANTES (línea 64-66)
$mform->addElement('header', 'accountheader', get_string('signup_account_header', 'local_jobboard'));

// DESPUÉS - Con indicador de completitud
$mform->addElement('header', 'accountheader',
    '<span class="jb-section-status" data-section="account">' .
    '<i class="fa fa-circle text-muted mr-2"></i></span>' .
    get_string('signup_account_header', 'local_jobboard') .
    ' <small class="badge badge-secondary ml-2">3 campos requeridos</small>'
);
```

**Mejora 2: Tooltips descriptivos en selects**

```php
// ANTES (línea 107-116)
$doctypes = [
    '' => get_string('select') . '...',
    'cc' => get_string('signup_doctype_cc', 'local_jobboard'),
    // ...
];
$mform->addElement('select', 'doctype', get_string('signup_doctype', 'local_jobboard'), $doctypes);

// DESPUÉS - Con descripciones
$doctypes = [
    '' => get_string('select') . '...',
    'cc' => get_string('signup_doctype_cc', 'local_jobboard') . ' - ' .
            get_string('signup_doctype_cc_desc', 'local_jobboard'),
    'ce' => get_string('signup_doctype_ce', 'local_jobboard') . ' - ' .
            get_string('signup_doctype_ce_desc', 'local_jobboard'),
    // ...
];

// Agregar atributo para tooltip
$select = $mform->addElement('select', 'doctype', get_string('signup_doctype', 'local_jobboard'), $doctypes);
$mform->addHelpButton('doctype', 'signup_doctype_help', 'local_jobboard');
```

**Mejora 3: Resumen expandible de términos**

```php
// ANTES (línea 282-289)
$privacytext = get_string('signup_privacy_text', 'local_jobboard', $privacyurl);
$mform->addElement('html', '<div class="privacy-notice mb-3">' . $privacytext . '</div>');

// DESPUÉS - Resumen con expandir
$summaryHtml = '<div class="jb-terms-summary card mb-3">';
$summaryHtml .= '<div class="card-body">';

// Resumen en puntos clave
$summaryHtml .= '<h6 class="card-title">' . get_string('termssummary', 'local_jobboard') . '</h6>';
$summaryHtml .= '<ul class="mb-3 small">';
$summaryHtml .= '<li>' . get_string('termssummary_1', 'local_jobboard') . '</li>';
$summaryHtml .= '<li>' . get_string('termssummary_2', 'local_jobboard') . '</li>';
$summaryHtml .= '<li>' . get_string('termssummary_3', 'local_jobboard') . '</li>';
$summaryHtml .= '</ul>';

// Botón para ver términos completos
$summaryHtml .= '<a class="btn btn-outline-secondary btn-sm" data-toggle="collapse" href="#fullTerms">';
$summaryHtml .= '<i class="fa fa-chevron-down mr-1"></i>' . get_string('viewfullterms', 'local_jobboard');
$summaryHtml .= '</a>';

// Términos completos colapsados
$summaryHtml .= '<div class="collapse mt-3" id="fullTerms">';
$summaryHtml .= '<div class="bg-light p-3 rounded small" style="max-height: 200px; overflow-y: auto;">';
$summaryHtml .= $privacytext;
$summaryHtml .= '</div></div>';

$summaryHtml .= '</div></div>';

$mform->addElement('html', $summaryHtml);
```

---

## 7. ARCHIVO: styles.css

**Ruta:** `/local/jobboard/styles.css`
**Tipo:** CSS Stylesheet
**Líneas:** ~1000+

### Estado Actual

El CSS está bien organizado con:
- Scope correcto (`.path-local-jobboard`)
- Componentes Bootstrap recreados
- Variables de color consistentes
- Utilidades de accesibilidad

### Problemas UX Identificados

| Línea | Problema | Severidad | Impacto |
|-------|----------|-----------|---------|
| 276-290 | Botones sin estados :focus visibles suficientes | Medio | Accesibilidad reducida |
| 633-714 | Faltan estilos para loading states | Medio | Sin feedback visual en carga |
| N/A | Sin dark mode | Bajo | Preferencia de usuario ignorada |
| 788-808 | Breakpoints no cubren tablets horizontales | Bajo | Layout roto en ciertos dispositivos |

### Propuestas de Mejora

**Mejora 1: Estados de foco mejorados**

```css
/* DESPUÉS de línea 290 - Mejorar estados de foco */

/* Focus ring visible para todos los elementos interactivos */
.path-local-jobboard .btn:focus,
.path-local-jobboard .form-control:focus,
.path-local-jobboard .custom-select:focus,
.path-local-jobboard a:focus {
    outline: 3px solid #80bdff;
    outline-offset: 2px;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Alto contraste para focus visible */
.path-local-jobboard *:focus-visible {
    outline: 3px solid #0056b3 !important;
    outline-offset: 2px !important;
}

/* Skip link mejorado */
.path-local-jobboard .skip-to-content {
    position: absolute;
    left: -9999px;
    top: auto;
    width: 1px;
    height: 1px;
    overflow: hidden;
}

.path-local-jobboard .skip-to-content:focus {
    left: 10px;
    top: 10px;
    width: auto;
    height: auto;
    padding: 10px 20px;
    background: #007bff;
    color: #fff;
    z-index: 9999;
    border-radius: 4px;
    font-weight: bold;
}
```

**Mejora 2: Loading states globales**

```css
/* NUEVO - Estados de carga */

/* Skeleton loading para cards */
.path-local-jobboard .jb-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: jb-skeleton-loading 1.5s infinite;
    border-radius: 4px;
}

@keyframes jb-skeleton-loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.path-local-jobboard .jb-skeleton-text {
    height: 1em;
    margin-bottom: 0.5em;
}

.path-local-jobboard .jb-skeleton-title {
    height: 1.5em;
    width: 60%;
    margin-bottom: 1em;
}

.path-local-jobboard .jb-skeleton-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
}

/* Overlay de carga para secciones */
.path-local-jobboard .jb-loading-overlay {
    position: relative;
}

.path-local-jobboard .jb-loading-overlay::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    z-index: 10;
    display: none;
}

.path-local-jobboard .jb-loading-overlay::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 40px;
    height: 40px;
    margin: -20px 0 0 -20px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #007bff;
    border-radius: 50%;
    animation: jb-spin 1s linear infinite;
    z-index: 11;
    display: none;
}

.path-local-jobboard .jb-loading-overlay.is-loading::before,
.path-local-jobboard .jb-loading-overlay.is-loading::after {
    display: block;
}

/* Botón con loading */
.path-local-jobboard .btn.is-loading {
    position: relative;
    color: transparent !important;
    pointer-events: none;
}

.path-local-jobboard .btn.is-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: jb-spin 0.75s linear infinite;
}

@keyframes jb-spin {
    to { transform: rotate(360deg); }
}
```

**Mejora 3: Soporte para Dark Mode**

```css
/* NUEVO - Dark Mode (respeta preferencia del sistema) */
@media (prefers-color-scheme: dark) {
    .path-local-jobboard {
        --jb-bg-primary: #1a1a2e;
        --jb-bg-secondary: #16213e;
        --jb-text-primary: #eaeaea;
        --jb-text-muted: #a0a0a0;
        --jb-border-color: #2d3748;
    }

    .path-local-jobboard .card {
        background-color: var(--jb-bg-secondary);
        border-color: var(--jb-border-color);
    }

    .path-local-jobboard .card-header {
        background-color: var(--jb-bg-primary);
        border-color: var(--jb-border-color);
    }

    .path-local-jobboard .card-body {
        color: var(--jb-text-primary);
    }

    .path-local-jobboard .text-muted {
        color: var(--jb-text-muted) !important;
    }

    .path-local-jobboard .table {
        color: var(--jb-text-primary);
    }

    .path-local-jobboard .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .path-local-jobboard .form-control {
        background-color: var(--jb-bg-primary);
        border-color: var(--jb-border-color);
        color: var(--jb-text-primary);
    }

    .path-local-jobboard .alert-info {
        background-color: #1a365d;
        border-color: #2b6cb0;
        color: #90cdf4;
    }
}
```

---

## 8. ARCHIVO: templates/components/vacancy_card.mustache

**Ruta:** `/local/jobboard/templates/components/vacancy_card.mustache`
**Tipo:** Mustache Template
**Líneas:** ~128

### Estado Actual

Template bien estructurado con:
- Etiquetas semánticas (article, header, footer)
- Atributos ARIA
- Clases sr-only para lectores de pantalla

### Problemas UX Identificados

| Línea | Problema | Severidad | Impacto |
|-------|----------|-----------|---------|
| 37 | Card no tiene estado hover definido | Bajo | Menor interactividad percibida |
| 94-101 | Fecha de cierre poco destacada | Medio | Usuario puede perder deadline |
| 104-123 | Botones muy pequeños en móvil | Medio | Difícil de tocar |

### Propuestas de Mejora

```mustache
{{! DESPUÉS de línea 37 - Agregar clase para hover }}
<article class="card h-100 shadow-sm jb-vacancy-card jb-hoverable{{#isurgent}} border-warning jb-urgent{{/isurgent}}"
         data-vacancy-id="{{id}}"
         aria-labelledby="vacancy-title-{{id}}"
         role="article">
```

```css
/* CSS para hover state */
.path-local-jobboard .jb-vacancy-card.jb-hoverable {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
}

.path-local-jobboard .jb-vacancy-card.jb-hoverable:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* Indicador de urgencia animado */
.path-local-jobboard .jb-vacancy-card.jb-urgent {
    animation: jb-pulse-border 2s infinite;
}

@keyframes jb-pulse-border {
    0%, 100% { border-color: #ffc107; }
    50% { border-color: #fd7e14; }
}
```

---

## 9. ANÁLISIS DE NAVEGACIÓN GLOBAL

### Flujos de Usuario Actuales

```
┌─────────────────────────────────────────────────────────────┐
│                     FLUJO CANDIDATO                          │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Inicio → Dashboard → Buscar Vacantes → Ver Detalle →       │
│  → Aplicar (signup si no logueado) → Subir Docs →           │
│  → Confirmar → Ver Estado en Mis Aplicaciones               │
│                                                              │
│  Problemas:                                                  │
│  - 7+ clics para completar aplicación                       │
│  - Sin breadcrumbs claros                                   │
│  - Difícil volver atrás                                     │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                     FLUJO EMPLEADOR                          │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Dashboard → Crear Vacante → Completar Formulario →         │
│  → Publicar → Ver Aplicaciones → Revisar Docs →             │
│  → Aprobar/Rechazar → Seleccionar Candidato                 │
│                                                              │
│  Problemas:                                                  │
│  - Formulario de vacante muy largo                          │
│  - Sin preview antes de publicar                            │
│  - Revisión de docs ineficiente                             │
└─────────────────────────────────────────────────────────────┘
```

### Propuesta de Mejora de Navegación

```php
// Nuevo componente: Breadcrumb contextual
// Agregar en lib.php

function local_jobboard_build_breadcrumb($current_view, $context_data = []) {
    global $PAGE;

    $breadcrumbs = [
        ['label' => get_string('dashboard', 'local_jobboard'),
         'url' => new moodle_url('/local/jobboard/index.php')]
    ];

    switch ($current_view) {
        case 'apply':
            $breadcrumbs[] = ['label' => get_string('vacancies', 'local_jobboard'),
                             'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'public'])];
            if (!empty($context_data['vacancy'])) {
                $breadcrumbs[] = ['label' => s($context_data['vacancy']->title),
                                 'url' => new moodle_url('/local/jobboard/index.php',
                                          ['view' => 'vacancy', 'id' => $context_data['vacancy']->id])];
            }
            $breadcrumbs[] = ['label' => get_string('apply', 'local_jobboard'), 'url' => null];
            break;

        case 'applications':
            $breadcrumbs[] = ['label' => get_string('myapplications', 'local_jobboard'), 'url' => null];
            break;

        // ... más casos
    }

    // Agregar a navbar de Moodle
    foreach ($breadcrumbs as $crumb) {
        if ($crumb['url']) {
            $PAGE->navbar->add($crumb['label'], $crumb['url']);
        } else {
            $PAGE->navbar->add($crumb['label']);
        }
    }
}
```

---

## 10. STRINGS DE IDIOMA REQUERIDOS

Para implementar todas las mejoras, se necesitan agregar los siguientes strings al archivo de idioma:

```php
// Agregar a lang/en/local_jobboard.php

// Navigation & Feedback
$string['invalidview'] = 'The requested view does not exist.';
$string['redirectedtodashboard'] = 'You have been redirected to the dashboard.';

// Signup Progress
$string['signup_step_account'] = 'Account';
$string['signup_step_personal'] = 'Personal';
$string['signup_step_contact'] = 'Contact';
$string['signup_step_academic'] = 'Education';
$string['signup_step_confirm'] = 'Confirm';

// Dashboard
$string['welcomeback'] = 'Welcome back, {$a}!';
$string['dashboardwelcome'] = 'What would you like to do today?';
$string['thisweek'] = 'this week';

// Application Process
$string['applyingto'] = 'You are applying to:';
$string['closingsoon'] = 'Closes in {$a} days';
$string['documentsprogress'] = 'Documents uploaded';
$string['confirmapplication'] = 'Confirm your application';
$string['confirmapplication_text'] = 'Please verify before submitting:';
$string['confirmapplication_docs'] = 'All required documents are uploaded';
$string['confirmapplication_data'] = 'Your personal information is accurate';
$string['confirmapplication_consent'] = 'You have read and accepted the terms';

// Document Status
$string['docstatus_pending'] = 'Pending upload';
$string['docstatus_uploaded'] = 'Uploaded';
$string['docstatus_approved'] = 'Approved';
$string['docstatus_rejected'] = 'Rejected';

// Review
$string['reviewprogress'] = 'Review Progress';
$string['approved'] = 'Approved';
$string['rejected'] = 'Rejected';
$string['pending'] = 'Pending';

// Terms
$string['termssummary'] = 'Key Points';
$string['termssummary_1'] = 'Your data will be processed according to our privacy policy';
$string['termssummary_2'] = 'You can request deletion of your data at any time';
$string['termssummary_3'] = 'Your application may be shared with hiring managers';
$string['viewfullterms'] = 'View full terms and conditions';

// Validation
$string['processing'] = 'Processing';

// Accessibility
$string['skiptomaincontent'] = 'Skip to main content';
$string['clickfordetails'] = 'Click for details';
```

---

## RESUMEN DE PRIORIDADES

### Crítico (Implementar Inmediatamente)

1. **Modal de confirmación en aplicación** - Evita envíos accidentales
2. **Indicadores de progreso en formularios largos** - Reduce abandono
3. **Feedback visual durante procesamiento** - Usuario sabe que sistema responde

### Alto (Próximo Sprint)

4. **Checklist interactivo de documentos** - Clarifica requisitos
5. **Panel de estado en revisión** - Eficiencia de revisores
6. **Estados de foco mejorados** - Accesibilidad

### Medio (Backlog Prioritario)

7. **Validación en tiempo real** - Mejor UX en formularios
8. **Atajos de teclado para revisión** - Productividad
9. **Loading states globales** - Feedback consistente
10. **Breadcrumbs contextuales** - Navegación clara

### Bajo (Mejoras Deseables)

11. **Dark mode** - Preferencia de usuario
12. **Tooltips descriptivos** - Guía contextual
13. **Skeleton loading** - Percepción de velocidad
14. **Hover states en cards** - Interactividad visual

---

## GUÍA DE IMPLEMENTACIÓN

### Paso 1: Strings de Idioma
Agregar todos los strings listados a `lang/en/local_jobboard.php` y `lang/es/local_jobboard.php`.

### Paso 2: CSS Base
Agregar las mejoras de CSS a `styles.css` en las secciones correspondientes.

### Paso 3: JavaScript AMD
Crear/modificar módulos:
- `amd/src/signup_validation.js`
- `amd/src/apply_confirmation.js`
- `amd/src/review_shortcuts.js`

### Paso 4: Templates
Actualizar templates Mustache con nuevos componentes.

### Paso 5: PHP Views
Implementar cambios en vistas PHP según propuestas.

### Paso 6: Testing
- Probar todos los flujos de usuario
- Verificar accesibilidad con screen reader
- Testear en múltiples dispositivos
- Validar con usuarios reales

---

## MÉTRICAS DE ÉXITO

| Métrica | Actual (Estimado) | Objetivo |
|---------|-------------------|----------|
| Tasa de completación signup | ~60% | >85% |
| Tiempo promedio de aplicación | ~12 min | <7 min |
| Errores de validación en submit | ~3.5/usuario | <1/usuario |
| Accesibilidad WCAG 2.1 AA | ~75% | >95% |
| Satisfacción de revisores | ~6/10 | >8/10 |

---

*Documento generado automáticamente por análisis UX.*
*Última actualización: 2025-12-09*

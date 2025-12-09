# Changelog

All notable changes to the local_jobboard plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.1.0] - 2025-12-09

### Fase 10 Complete: Release Version

This version completes the Phase 10 optimization with all verification phases.

#### Phases Completed (10A-10U)

**10A - Robust Audit System (v2.0.73)**
- Added action/entity constants to audit class
- Enhanced document::delete() with previous state capture
- Standardized audit logging across all classes

**10B - Remove Obsolete Fields (v2.0.74)**
- Removed salary field from vacancy
- Removed extemporaneous fields and date fields from vacancy
- Removed TI from signup form
- Backward compatibility via __get() magic method

**10C - CLI Cleanup (v2.0.75)**
- Verified CLI scripts have no salary/remuneration references
- opendate/closedate correctly apply to convocatoria

**10D - Document Logic (v2.0.76)**
- Age exemption threshold implemented (50+ exempt from libreta_militar)
- Conditional notes for optional documents
- Gender-based document filtering

**10E - Application Restrictions (v2.0.77)**
- allow_multiple_applications and max_applications_per_user in convocatoria
- Validation in apply.php
- Experience requirement for occasional contracts (2 years)

**10F - Document Types Management (v2.0.78)**
- Full CRUD in admin/doctypes.php
- Manual sorting, unique code validation
- Audit logging for all changes

**10G - Exemption System (v2.0.79)**
- convocatoria_exemption.php for convocatoria-level exemptions
- Vacancies inherit from convocatoria automatically
- ISER personnel exemptions for historic staff

**10H - Document Review System (v2.0.80)**
- validate_document.php with preview
- document_services.php for preview generation
- review_ui.js, document_preview.js for UI
- bulk_validate.php for mass validation

**10I - HTML Editor Fields (v2.0.81)**
- Editor elements in 7 form classes
- vacancy_form, convocatoria_form, application_form, etc.

**10J - ZIP Export (v2.0.82)**
- export_documents.php with hierarchical structure
- Export by application, vacancy, convocatoria, or company
- Structured folder format: Company/Vacancy_Code/Applicant/

**10K - Public Convocatoria View (v2.0.83)**
- public.php for unauthenticated access
- browse_convocatorias.php for public browsing

**10L - Email Templates (v2.0.84)**
- admin/templates.php for template management
- email_template.php class
- email_template_form.php for editing

**10M - Review Interface Analysis (v2.0.85)**
- Existing review system analyzed
- Template patterns documented

**10N - Apply Tabs Redesign (v2.0.86)**
- Progress steps in apply.php
- Tab-based document categories
- Navigation between sections

**10O - Import/Export System (v2.0.87)**
- CLI import for vacancies and profiles
- JSON data format support
- import_vacancies.php, import_exemptions.php

**10P - Navigation Styles (v2.0.88)**
- Verified styles.css compatibility
- No conflicting sidebar styles found

**10Q - Language Strings (v2.0.89)**
- EN and ES language files synchronized
- ~1860 strings in each language

**10R - IOMAD Company Management (v2.0.90)**
- Company filtering in vacancy views
- User company assignment in profile
- IOMAD integration functions

**10T - Reports by Convocatoria (v2.0.91)**
- Reports filtered by convocatoria
- views/reports.php implementation

**10U - Final Verification (v2.1.0)**
- All phases verified complete
- Version bumped to 2.1.0 (stable release)

---

## [2.0.79] - 2025-12-09

### Fase 10G: Sistema de Excepciones por Convocatoria

#### Verified Implementation
- `convocatoria_exemption.php` manages document exemptions at convocatoria level
- `exemption.php` manages ISER historic personnel exemptions (user-level)
- Vacancies inherit exemptions from convocatoria via `get_required_doctypes_for_convocatoria()`
- No individual vacancy-level exemption configuration exists (correct architecture)
- Form in `convocatoria.php` allows selecting which documents to exempt
- Exemption summary displayed in apply.php
- Copy exemptions between convocatorias supported

#### Architecture
```
Convocatoria → Document Exemptions → Apply to ALL applicants in convocatoria
                                   → Vacancies inherit automatically
User → ISER Exemption → Reduced docs for historic personnel
```

---

## [2.0.78] - 2025-12-09

### Fase 10F: Gestión Completa de Tipos de Documento

#### Verified Implementation
- Full CRUD interface in `admin/doctypes.php`
- Fields: code, name, description, category, isrequired, externalurl, requirements
- Additional fields: age_exemption_threshold, conditional_note, gender_condition, profession_exempt
- Manual sorting with moveup/movedown actions
- Unique code validation on create (lines 167-170)
- Audit logging for all actions (create, update, delete, toggle)
- Enabled/disabled toggle with status badges
- Conditions displayed: gender, age exemption, profession, ISER exemption

---

## [2.0.77] - 2025-12-09

### Fase 10E: Restricción de Aplicaciones por Convocatoria

#### Verified Implementation
- Fields `allow_multiple_applications` and `max_applications_per_user` in convocatoria table
- Fields displayed in convocatoria form with conditional visibility
- Validation in `apply.php` calls `local_jobboard_can_user_apply_to_vacancy()`
- Function counts user's existing applications in convocatoria
- Blocks application if single-application-only or max limit reached
- Experience requirement check (2 years) for occasional contracts

---

## [2.0.76] - 2025-12-09

### Fase 10D: Lógica de Documentos (Edad y Tarjeta Profesional)

#### Verified Implementation
- Age exemption logic in `application_form.php` filters documents by user age
- `age_exemption_threshold` field in doctype table (set to 50 for libreta_militar)
- `conditional_note` field displayed for optional documents
- Users ≥50 years are automatically exempt from libreta_militar
- Tarjeta profesional marked as optional with conditional note

#### Technical Details
- Document filtering applies in form definition, validation, and data retrieval
- Age calculated from applicant_profile.birthdate
- Gender condition filtering also implemented (M = men only, F = women only)

---

## [2.0.75] - 2025-12-09

### Fase 10C: Limpieza de CLI

#### Verified
- CLI scripts (`cli.php`, `import_vacancies.php`) do not contain salary/remuneration references
- CLI opendate/closedate parameters correctly apply to convocatoria (not vacancy)
- No changes required - CLI already clean

---

## [2.0.74] - 2025-12-09

### Fase 10B: Eliminación de Campos Obsoletos

#### Removed
- Field `salary` from vacancy table (regulatory requirement - no salary information)
- Fields `isextemporaneous`, `extemporaneousreason` from vacancy table (vacancies now inherit dates from convocatoria)
- Fields `opendate`, `closedate` from vacancy table (dates now exclusively from convocatoria)
- "Tarjeta de Identidad" (TI) option from signup form (not applicable for job applications)

#### Changed
- Vacancy class `__get()` magic method provides backward compatibility for removed fields
- Vacancy dates now obtained from associated convocatoria automatically
- CLI scripts use opendate/closedate parameters for convocatoria, not vacancy

#### Notes
- Database migration removes columns if they exist
- Existing code using `$vacancy->opendate` will transparently get convocatoria dates
- Existing code using `$vacancy->salary` will get empty string

---

## [2.0.73] - 2025-12-09

### Fase 10A: Sistema de Auditoría Robusta

#### Added
- New action constants in audit class: `ACTION_APPROVE`, `ACTION_REVOKE`, `ACTION_ASSIGN`, `ACTION_REOPEN`, `ACTION_PUBLISH`, `ACTION_CLOSE`, `ACTION_REUPLOAD`, `ACTION_IMPORT`
- New entity constants in audit class: `ENTITY_INTERVIEW`, `ENTITY_COMMITTEE`, `ENTITY_EVALUATION`, `ENTITY_DECISION`, `ENTITY_CONSENT`, `ENTITY_PROFILE`, `ENTITY_DOCTYPE`, `ENTITY_NOTIFICATION`, `ENTITY_API_TOKEN`, `ENTITY_DOC_REQUIREMENT`, `ENTITY_WORKFLOW_LOG`

#### Changed
- Enhanced `document::delete()` method to capture previous state before deletion for complete audit trail
- Updated `exemption::create()` to use standard audit constants and include full new state data
- Updated `exemption::revoke()` to use `audit::log_transition()` for proper status tracking

#### Fixed
- Standardized audit logging across all entity classes to use class constants instead of hardcoded strings

---

## [2.0.72-beta] - 2025-12-10

### Previous Release

- Initial Phase 10 preparation
- Base version before optimization phase

---

## Version History

| Version | Date | Description |
|---------|------|-------------|
| **2.1.0** | **2025-12-09** | **Phase 10 Complete - Stable Release** |
| 2.0.91 | 2025-12-09 | Phase 10T: Reports by convocatoria |
| 2.0.90 | 2025-12-09 | Phase 10R: IOMAD company management |
| 2.0.89 | 2025-12-09 | Phase 10Q: Language strings sync |
| 2.0.88 | 2025-12-09 | Phase 10P: Navigation styles |
| 2.0.87 | 2025-12-09 | Phase 10O: Import/export system |
| 2.0.86 | 2025-12-09 | Phase 10N: Apply tabs redesign |
| 2.0.85 | 2025-12-09 | Phase 10M: Review interface |
| 2.0.84 | 2025-12-09 | Phase 10L: Email templates |
| 2.0.83 | 2025-12-09 | Phase 10K: Public convocatoria view |
| 2.0.82 | 2025-12-09 | Phase 10J: ZIP export |
| 2.0.81 | 2025-12-09 | Phase 10I: HTML editor fields |
| 2.0.80 | 2025-12-09 | Phase 10H: Document review system |
| 2.0.79 | 2025-12-09 | Phase 10G: Convocatoria exemption system |
| 2.0.78 | 2025-12-09 | Phase 10F: Document types management |
| 2.0.77 | 2025-12-09 | Phase 10E: Application restrictions per convocatoria |
| 2.0.76 | 2025-12-09 | Phase 10D: Document logic (age exemption, conditional notes) |
| 2.0.75 | 2025-12-09 | Phase 10C: CLI cleanup (verified clean) |
| 2.0.74 | 2025-12-09 | Phase 10B: Remove obsolete fields |
| 2.0.73 | 2025-12-09 | Phase 10A: Robust audit system |
| 2.0.72-beta | 2025-12-10 | Base version for Phase 10 optimization |

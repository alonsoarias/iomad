# Changelog

All notable changes to the local_jobboard plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
| 2.0.76 | 2025-12-09 | Phase 10D: Document logic (age exemption, conditional notes) |
| 2.0.75 | 2025-12-09 | Phase 10C: CLI cleanup (verified clean) |
| 2.0.74 | 2025-12-09 | Phase 10B: Remove obsolete fields |
| 2.0.73 | 2025-12-09 | Phase 10A: Robust audit system |
| 2.0.72-beta | 2025-12-10 | Base version for Phase 10 optimization |

# Changelog

All notable changes to the local_jobboard plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
| 2.0.73 | 2025-12-09 | Phase 10A: Robust audit system |
| 2.0.72-beta | 2025-12-10 | Base version for Phase 10 optimization |

# Changelog

All notable changes to the local_jobboard plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.1.3] - 2025-12-11

### Added
- **Complete language files**: English (`lang/en/local_jobboard.php`) and Spanish (`lang/es/local_jobboard.php`)
  - ~200 language strings for full plugin internationalization
  - Dashboard strings, role labels, welcome messages
  - Capability strings, status labels, action buttons
  - Error messages, notifications, privacy metadata
  - IOMAD multi-tenant labels (Centro, Facultad, Programa)

### Technical Notes
- All hardcoded strings replaced with language string calls
- Supports Moodle language switching
- Spanish translation complete for ISER deployment

## [3.1.2] - 2025-12-11

### Added
- **NEW TABLE**: `local_jobboard_faculty` - Faculties within each IOMAD company (FCAS, FII)
  - Supports ISER architecture: 2 faculties per centro
  - Fields: companyid, code, name, shortname, description, enabled, sortorder
- **NEW TABLE**: `local_jobboard_program` - Academic programs within faculties
  - Linked to course categories for Moodle integration
  - Fields: facultyid, categoryid, code, name, modality, level, enabled

### Changed
- `local_jobboard_committee`: Added `facultyid` field for faculty-level committees
- `local_jobboard_committee`: Added `description` field
- `local_jobboard_program_reviewer`: Added `programid` field for program-level reviewers
- `local_jobboard_program_reviewer`: `categoryid` is now optional (legacy support)

### Technical Notes
- Per AGENTS.md specification: "Committees are per FACULTY, Reviewers are per PROGRAM"
- Database schema now supports complete IOMAD 4-level hierarchy:
  - ISER Universidad (global)
  - 16 Centros (IOMAD companies)
  - 2 Facultades per centro (FCAS, FII) - NEW
  - ~10 Programas per faculty - NEW
- Upgrade script migrates existing data while maintaining backwards compatibility

## [3.1.1] - 2025-12-11

### Added
- AMD JavaScript modules (`amd/src/dashboard.js`, `common.js`, `notifications.js`)
- Dashboard AMD module with animations and stat card interactions
- Common utilities module with CSS class constants and AJAX helpers
- Notifications module for toast notifications (no Bootstrap dependencies)
- Additional CSS utility classes (`jb-rounded-lg`, `jb-rounded-xl`)

### Changed
- **MAJOR**: Refactored `views/dashboard.php` from 910 lines to ~100 lines
- Dashboard now uses renderer pattern with Mustache templates
- Updated `pages/dashboard.mustache` template with complete dashboard structure
- Added welcome header, workflow sections, reports sections, config sections
- Renderer now includes `prepare_dashboard_data()`, `prepare_workflow_sections()`,
  `prepare_report_sections()`, `prepare_config_sections()` methods

### Technical Notes
- Dashboard view now follows MVC pattern (Model-View-Controller separation)
- All HTML output delegated to Mustache templates
- Business logic moved to renderer class
- Zero Bootstrap dependencies in AMD modules

## [3.1.0] - 2025-12-11

### Added
- Complete custom CSS system with `jb-*` prefix classes in `styles.css`
- CSS custom properties (variables) for consistent theming
- Full grid system with responsive breakpoints (`jb-col-*`, `jb-row`)
- Card components (`jb-card`, `jb-stat-card`, `jb-action-card`, `jb-section-card`)
- Button system (`jb-btn`, `jb-btn-*`, `jb-btn-outline-*`)
- Badge system (`jb-badge`, `jb-badge-*`)
- Alert system (`jb-alert`, `jb-alert-*`)
- Table system (`jb-table`, `jb-table-*`)
- Form components (`jb-form-control`, `jb-form-group`, `jb-form-select`)
- List group components (`jb-list-group`, `jb-list-group-item`)
- Progress bar components (`jb-progress`, `jb-progress-bar`)
- Modal components (`jb-modal`, `jb-modal-*`)
- Dropdown components (`jb-dropdown`, `jb-dropdown-*`)
- Display utilities (`jb-d-*`)
- Flexbox utilities (`jb-flex-*`, `jb-justify-content-*`, `jb-align-items-*`)
- Spacing utilities (`jb-m-*`, `jb-p-*`, `jb-mt-*`, `jb-mb-*`, etc.)
- Text utilities (`jb-text-*`, `jb-fw-*`, `jb-fs-*`)
- Background utilities (`jb-bg-*`)
- Border utilities (`jb-border-*`, `jb-rounded-*`)
- Shadow utilities (`jb-shadow-*`)
- Position utilities (`jb-position-*`)
- Visibility utilities (`jb-visible`, `jb-invisible`)
- Special jobboard sections for User Tours (`jb-admin-section`, `jb-reviewer-section`, `jb-applicant-section`)
- Vacancy card component (`jb-vacancy-card`)
- Timeline component (`jb-timeline`)
- Status badges for application states (`jb-status-*`)
- Empty state component (`jb-empty-state`)
- Loading states and spinner (`jb-loading`, `jb-spinner`)
- Document validation components (`jb-document-item`)
- Filter form components (`jb-filter-form`)
- Print styles (`jb-d-print-*`)
- Reduced motion accessibility support
- CHANGELOG.md for version tracking

### Changed
- Initiated migration away from Bootstrap classes to custom `jb-*` system
- Updated CSS architecture for theme independence

### Technical Notes
- CSS file: ~2800 lines of custom styles
- No Bootstrap dependencies
- Full responsive design support
- Accessibility compliant (WCAG 2.1)

## [3.0.8] - 2025-12-10

### Fixed
- Replaced hardcoded Spanish text with language strings (v3.0.7)

### Removed
- Unused REST API directory (v3.0.8)
- Obsolete jobboard language and stylesheet files

## [3.0.7] - 2025-12-09

### Fixed
- Internationalization improvements
- Language string corrections

## [3.0.6] - 2025-12-08

### Added
- Program reviewers table (`local_jobboard_program_reviewer`)
- Email template language strings table (`local_jobboard_email_strings`)

### Changed
- Committee structure now supports faculty-level assignment

## [3.0.5] - 2025-12-07

### Added
- Convocatoria document exemptions table (`local_jobboard_conv_docexempt`)
- PDF attachment support for convocatorias
- Brief description field for convocatorias

### Changed
- Enhanced convocatoria management interface

## [3.0.4] - 2025-12-06

### Added
- Interview scheduling functionality
- Selection committee management
- Application evaluation system

### Changed
- Improved workflow state machine

## [3.0.3] - 2025-12-05

### Added
- User consent tracking
- Applicant profile extended fields
- Age-based document exemptions

### Fixed
- Document validation workflow issues

## [3.0.2] - 2025-12-04

### Added
- Email template system with multi-tenant support
- Notification queue and history

### Changed
- Enhanced audit logging

## [3.0.1] - 2025-12-03

### Added
- Document type definitions with checklist support
- Document requirements per vacancy
- ISER exemptions management

### Fixed
- Various bug fixes in application workflow

## [3.0.0] - 2025-12-01

### Added
- Complete plugin rewrite for Moodle 4.1+
- IOMAD multi-tenant support
- Convocatoria (call) management system
- Vacancy management with custom fields
- Application workflow with multiple states
- Document upload and validation
- Reviewer assignment system
- Selection committee support
- Audit logging
- Privacy API implementation (GDPR)
- Behat tests for core functionality
- PHPUnit tests

### Changed
- Database schema completely redesigned
- User interface modernized
- Role and capability structure updated

### Technical Notes
- Requires Moodle 4.1+ (2022112800)
- Supports Moodle 4.1 to 4.5
- IOMAD compatible

---

## Version Numbering

- **Major version (X.0.0)**: Breaking changes or major rewrites
- **Minor version (0.X.0)**: New features, backwards compatible
- **Patch version (0.0.X)**: Bug fixes and minor improvements

## Upgrade Notes

### Upgrading to 3.1.0
1. Run `php admin/cli/purge_caches.php` to clear CSS cache
2. The new CSS system is backwards compatible during migration
3. Templates will be progressively updated to use `jb-*` classes

### Upgrading to 3.0.0
1. Backup your database before upgrading
2. Run `php admin/cli/upgrade.php`
3. Review and configure new settings
4. Assign new capabilities to existing roles

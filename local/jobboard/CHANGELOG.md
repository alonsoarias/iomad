# Changelog

All notable changes to the local_jobboard plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.1.17] - 2025-12-11

### Changed
- **MAJOR**: Refactored `manage_program_reviewers.php` from 434 lines to ~113 lines (74% reduction)
- Program reviewers management page now uses renderer pattern with Mustache template
- Created `templates/pages/program_reviewers.mustache` with complete jb-* CSS classes
- All reviewer management functionality preserved: add, remove, change role, toggle status

### Added
- `render_program_reviewers_page()` method in renderer for program reviewers view
- `prepare_program_reviewers_page_data()` method in renderer with full data preparation
- Two view modes: list view (all programs overview) and program view (single program management)
- Statistics cards: total reviewers, active reviewers, lead reviewers, programs with reviewers
- Programs with reviewers table with reviewer and lead counts
- Category hierarchy list for adding reviewers to any program
- Add reviewer form with user selection and role dropdown
- Assigned reviewers table with change role, toggle status, and remove actions

### Technical Notes
- Supports IOMAD category/program hierarchy
- Reviewer roles: reviewer, lead_reviewer
- Reviewer status: active, inactive with toggle functionality
- Available users filtered by jobboard_reviewer, manager, or admin roles
- Session key validation for all actions preserved
- Zero Bootstrap dependencies - uses jb-* CSS classes only
- **Continues Phase 2 (Roles/Permissions) per AGENTS.md**

## [3.1.16] - 2025-12-11

### Changed
- **MAJOR**: Refactored `admin/roles.php` from 505 lines to ~119 lines (76% reduction)
- Role management page now uses renderer pattern with Mustache template
- Created `templates/pages/admin_roles.mustache` with complete jb-* CSS classes
- All role management functionality preserved: assign, unassign, role selection

### Added
- `render_admin_roles_page()` method in renderer for admin roles view
- `prepare_admin_roles_page_data()` method in renderer with full role data preparation
- Statistics cards showing total users and users per role
- Role cards with capabilities preview
- User assignment form with search filtering
- Assigned users table with unassign actions

### Technical Notes
- Supports all three plugin roles: reviewer, coordinator, committee
- Role assignment actions handled in PHP, display delegated to template
- JavaScript for user search filtering via `js_init_code()`
- **Begins Phase 2 (Roles/Permissions) per AGENTS.md**
- Zero Bootstrap dependencies - uses jb-* CSS classes only

## [3.1.15] - 2025-12-11

### Changed
- **MAJOR**: Refactored `views/reports.php` from 833 lines to ~317 lines (62% reduction)
- Reports page now uses renderer pattern with Mustache template
- Updated `templates/pages/reports.mustache` with complete jb-* CSS classes
- All 5 report types preserved: overview, applications, documents, reviewers, timeline
- Export functionality preserved: CSV, Excel, PDF formats

### Added
- `prepare_reports_page_data()` method in renderer for reports view
- `prepare_overview_report_data()` protected method for summary statistics
- `prepare_applications_report_data()` protected method for vacancy breakdown
- `prepare_documents_report_data()` protected method for document validation stats
- `prepare_reviewers_report_data()` protected method for reviewer performance
- `prepare_timeline_report_data()` protected method for daily application trends
- `get_application_status_color()` helper method for status badge colors
- Report type tabs with icons and active state
- Filter form with vacancy selector and date range pickers
- Statistics cards for each report type
- Progress bars for status percentages in overview
- Navigation footer with dashboard and quick action links

### Technical Notes
- All report data generation moved to renderer methods
- Template uses report type flags (isoverview, isapplications, etc.) for conditional sections
- Export function kept in view file for simplicity
- Document stats fetched via bulk_validator class
- Reviewer stats fetched via reviewer class
- Zero Bootstrap dependencies - uses jb-* CSS classes only
- **COMPLETES Mustache migration for all major views**

## [3.1.14] - 2025-12-11

### Changed
- **MAJOR**: Refactored `views/convocatoria.php` from 468 lines to ~271 lines (42% reduction)
- Convocatoria create/edit page now uses renderer pattern with Mustache template
- Updated `templates/pages/convocatoria.mustache` with complete jb-* CSS classes
- Form handling and business logic preserved in view file

### Added
- `prepare_convocatoria_edit_page_data()` method in renderer for convocatoria form page
- Breadcrumb navigation with proper hierarchy
- Statistics cards when editing (vacancies, applications, status)
- Convocatoria info card with dates and action buttons
- Vacancies list (first 5) with status badges and application counts
- Navigation footer with back and dashboard links

### Technical Notes
- Moodle form HTML captured via output buffering and passed to template
- Create/update logic with audit logging preserved
- IOMAD company/department selection support preserved
- Document exemptions handling preserved
- AMD module initialization for IOMAD form preserved
- Zero Bootstrap dependencies - uses jb-* CSS classes only

## [3.1.13] - 2025-12-11

### Changed
- **MAJOR**: Refactored `views/public.php` from 902 lines to ~253 lines (72% reduction)
- Public listing page now uses renderer pattern with Mustache template
- Updated `templates/pages/public.mustache` with complete jb-* CSS classes
- Dual-mode support preserved: convocatorias list + vacancies for specific convocatoria

### Added
- `prepare_public_convocatorias_data()` method in renderer for convocatorias list mode
- `prepare_public_vacancies_data()` method in renderer for vacancies mode
- Convocatorias grid with cards, stats (vacancy count, positions), urgent indicators
- Vacancies grid with filter form (search, contract type, location)
- Quick access buttons for logged-in users (dashboard, my reviews, my applications)
- Statistics row: active convocatorias, open vacancies, total positions, closing soon
- Social sharing links (Facebook, Twitter, LinkedIn, WhatsApp)
- CTA section for non-logged-in users

### Technical Notes
- Template handles both modes via `showconvocatorias` and `showvacancies` flags
- All filter logic and SQL queries preserved in view file
- Vacancy application status checked per card for logged-in users
- Internal/public vacancy visibility based on capabilities
- Pagination support for vacancies mode
- Zero Bootstrap dependencies - uses jb-* CSS classes only

## [3.1.12] - 2025-12-11

### Changed
- **MAJOR**: Refactored `views/apply.php` from 490 lines to ~272 lines
- Application form page now uses renderer pattern with Mustache template
- Updated `templates/pages/apply.mustache` with jb-* CSS classes (no Bootstrap)
- Business logic preserved: capability checks, exemption handling, form processing

### Added
- `prepare_apply_page_data()` method in renderer for apply view
- Progress steps navigation in template (5-step wizard UI)
- Vacancy summary sidebar with deadline countdown
- Document checklist sidebar
- Quick tips and help section
- Exemption info display when applicable

### Technical Notes
- Form HTML captured via output buffering and passed to template
- All pre-submission checks preserved (vacancy open, not already applied, profile complete)
- Document upload and application creation workflow unchanged
- Convocatoria and user-level document exemptions preserved
- AMD module initialization for progress steps and confirmation modal
- Zero Bootstrap dependencies - uses jb-* CSS classes only

## [3.1.11] - 2025-12-11

### Changed
- **MAJOR**: Refactored `views/review.php` from 1066 lines to ~268 lines
- Document review page now uses renderer pattern with Mustache template
- Business logic preserved: validate, reject, validateall, markreviewed actions
- Multi-tenant (IOMAD) filtering preserved for review queue

### Added
- `prepare_review_page_data()` method in renderer for review view
- `prepare_review_list_data()` protected method for applications queue
- `prepare_review_single_application_data()` protected method for document review
- Review list mode: filter by vacancy, stats cards, pagination
- Single app mode: document list, validation actions, progress tracking
- Previous/next navigation between applications in review queue

### Technical Notes
- All rendering delegated to `templates/pages/review.mustache` (already migrated in v3.1.9)
- Stats cards: pending review count, pending documents, urgent deadlines
- Document validation workflow fully functional via renderer data
- Modal forms for rejection with reason preserved
- Review completion with observations and email notification preserved
- Zero Bootstrap dependencies - uses jb-* CSS classes only

## [3.1.10] - 2025-12-11

### Changed
- **MAJOR**: Refactored 2 public views to renderer + template pattern:
  - `views/public_convocatoria.php`: 400 lines → ~96 lines
  - `views/public_vacancy.php`: 503 lines → ~98 lines
- Both public views now use clean renderer pattern with Mustache templates

### Added
- New `templates/pages/public_convocatoria.mustache` for public convocatoria details
- New `templates/pages/public_vacancy.mustache` for public vacancy details
- `prepare_public_convocatoria_page_data()` method in renderer
- `render_public_convocatoria_page()` method in renderer
- `prepare_public_vacancy_page_data()` method in renderer
- `render_public_vacancy_page()` method in renderer
- `get_convocatoria_status_class()` helper method in renderer
- Social sharing links (Facebook, Twitter, LinkedIn, WhatsApp) with jb-* styling
- Deadline progress bars for both public views

### Technical Notes
- Public pages accessible to anonymous users
- Login CTA with return URL for non-authenticated users
- Share links properly encoded for social networks
- All templates use jb-* CSS classes only (no Bootstrap)
- Consistent breadcrumb navigation across public views

## [3.1.9] - 2025-12-11

### Changed
- **MAJOR**: Refactored `views/view_convocatoria.php` from 375 lines to ~110 lines
- View convocatoria page now uses renderer pattern with Mustache template
- Updated `templates/pages/review.mustache` with complete jb-* CSS classes

### Added
- New `templates/pages/view_convocatoria.mustache` for applicant convocatoria browsing
- `prepare_view_convocatoria_page_data()` method in renderer
- `render_view_convocatoria_page()` method in renderer
- Convocatoria detail with vacancy cards, apply buttons, and status indicators
- Previous/next navigation in review template for reviewer workflow

### Technical Notes
- View convocatoria shows vacancy cards with urgency indicators
- Apply status shown per vacancy (applied/not applied)
- Review template fully migrated to jb-* CSS classes
- AMD module initialization preserved for card actions
- Zero Bootstrap dependencies in templates

## [3.1.8] - 2025-12-11

### Changed
- **MAJOR**: Refactored 2 detail views to renderer + template pattern:
  - `views/vacancy.php`: 461 lines → ~105 lines (clean separation of concerns)
  - `views/application.php`: 515 lines → ~145 lines (workflow actions preserved)
- Updated `templates/pages/vacancy_detail.mustache` with complete jb-* CSS classes
- Updated `templates/pages/application_detail.mustache` with complete jb-* CSS classes

### Added
- `prepare_vacancy_detail_page_data()` method in renderer
- `render_vacancy_detail_page()` method in renderer
- `prepare_application_detail_page_data()` method in renderer
- `render_application_detail_page()` method in renderer
- Vacancy detail page with breadcrumbs, apply CTA, and progress bar
- Application detail page with document table, status history, and workflow actions
- New language strings for detail pages (EN/ES):
  - vacancyopen, backtoconvocatoria, backtovacancies, deadlineprogress, daysremaining
  - currentstatus, vacancyinfo, uploadeddocuments, applicationdetails, workflowactions
  - And more (see lang files for complete list)

### Technical Notes
- Vacancy detail shows deadline progress and urgent warnings
- Application detail preserves withdraw and status change workflows
- Exemption info displayed for reviewers when applicable
- All templates use jb-* CSS classes only (no Bootstrap)
- Status history timeline with color-coded entries

## [3.1.7] - 2025-12-11

### Changed
- **MAJOR**: Refactored 3 views to renderer + template pattern:
  - `views/browse_convocatorias.php`: 335 lines → ~100 lines
  - `views/myreviews.php`: 440 lines → ~145 lines
  - `views/manage.php`: 663 lines → ~310 lines (business logic preserved)
- Updated `templates/pages/myreviews.mustache` with complete jb-* CSS classes
- Updated `templates/pages/manage.mustache` with complete jb-* CSS classes

### Added
- New `templates/pages/browse_convocatorias.mustache` for applicant convocatoria browsing
- `prepare_browse_convocatorias_page_data()` method in renderer
- `prepare_myreviews_page_data()` method in renderer
- `prepare_manage_page_data()` method in renderer
- Reviewer assignment cards with document progress indicators
- Convocatoria browsing with status tabs and cards

### Technical Notes
- Manage view preserves business logic (actions, bulk actions) in view file
- Display logic fully migrated to renderer + template
- All templates use jb-* CSS classes only (no Bootstrap)
- IOMAD department filter AJAX support maintained

## [3.1.6] - 2025-12-11

### Changed
- **MAJOR**: Refactored `views/vacancies.php` from 420 lines to ~130 lines
- Vacancies page now uses renderer pattern with Mustache template
- Updated `templates/pages/vacancies.mustache` with complete jb-* CSS classes
- Added `prepare_vacancies_page_data()` method to renderer class

### Added
- Vacancy card component with urgency indicators
- Welcome banner with search emphasis
- Filter form with search, contract type, and status filters
- Navigation footer with back and browse links
- Additional vacancies language strings (EN/ES)

### Technical Notes
- AMD module initialization preserved for card actions
- IOMAD department filter AJAX support maintained
- Zero Bootstrap dependencies in template

## [3.1.5] - 2025-12-11

### Changed
- **MAJOR**: Refactored `views/convocatorias.php` from 512 lines to ~250 lines
- Convocatorias page now uses renderer pattern with Mustache template
- Updated `templates/pages/convocatorias.mustache` with complete jb-* CSS classes
- Added `prepare_convocatorias_page_data()` method to renderer class

### Added
- Convocatoria card component with status badges
- Status action buttons (open, close, reopen, archive, delete)
- Vacancy and application counts per convocatoria
- Additional convocatoria language strings (EN/ES)

### Technical Notes
- Business logic for actions (delete, open, close, etc.) remains in view
- Display logic moved to renderer + template
- All confirmation dialogs via JavaScript confirm()

## [3.1.4] - 2025-12-11

### Changed
- **MAJOR**: Refactored `views/applications.php` from 360 lines to ~100 lines
- Applications page now uses renderer pattern with Mustache template
- Updated `templates/pages/applications.mustache` with complete jb-* CSS classes
- Added `prepare_applications_page_data()` method to renderer class

### Added
- Application card component with progress tracking
- Exemption notice display
- Filter form with status dropdown
- Document status counters (approved/pending/rejected)

### Technical Notes
- Zero Bootstrap dependencies in applications template
- Follows MVC pattern with clean separation of concerns
- All strings internationalized (EN/ES)

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

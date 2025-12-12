# Changelog

All notable changes to the local_jobboard plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.6.11] - 2025-12-12

### Fixed
- **Missing CSS Classes**: Added ~300 lines of missing CSS class definitions to styles.css
  - Background color variants (jb-bg-primary, jb-bg-success, etc.)
  - Text color variants (jb-text-primary, jb-text-muted, etc.)
  - Border color variants (jb-border-primary, jb-border-left, etc.)
  - Button variants (jb-btn-primary through jb-btn-dark)
  - Button outline variants (jb-btn-outline-primary through jb-btn-outline-dark)
  - Button group styles (jb-btn-group, jb-btn-group-vertical)
  - Badge variants (jb-badge-primary through jb-badge-dark)
  - Alert variants (jb-alert-primary through jb-alert-dark, jb-alert-dismissible)
  - Stat card variants (jb-stat-card-primary through jb-stat-card-info)
  - Bootstrap 5 margin utilities (jb-me-*, jb-ms-*)
  - Shadow utilities (jb-shadow-sm, jb-shadow, jb-hover-shadow)
  - Form validation classes (jb-is-valid, jb-is-invalid, jb-has-error)
  - Nav tabs (jb-nav, jb-nav-tabs, jb-nav-link)
  - Table variants (jb-table-sm, jb-table-bordered)
  - Additional utilities (jb-avatar-circle, jb-timeline-*, jb-code-block, etc.)

### Technical Notes
- Styles.css now contains ~3000 lines of jb-* CSS classes
- All templates should now render correctly with proper styling
- Requires Moodle cache purge to see CSS changes

## [3.6.10] - 2025-12-12

### Fixed
- **Template Path Mismatches**: Fixed PHP renderer paths that didn't match actual template file locations
  - `exemption_renderer.php`: Fixed 6 paths (exemptions/* instead of admin/*)
  - `admin_renderer.php`: Fixed 1 path (doctype_delete vs doctype_confirm_delete)
  - `vacancy_renderer.php`: Fixed 2 paths (select_convocatoria, edit vs edit_form)

### Technical Notes
- All 46 PHP template paths now correctly resolve to existing mustache files
- This fixes "filenotfound" errors when accessing exemptions, vacancy edit, and doctype delete pages

## [3.6.9] - 2025-12-12

### Changed
- **Complete Mustache Templates Reorganization**: Entirely recreated and reorganized the templates folder
  - New hierarchical folder structure for better maintainability
  - **62 total templates** organized into logical directories

### New Directory Structure
```
templates/
├── components/          (14 reusable UI components)
│   ├── alert.mustache
│   ├── breadcrumb.mustache
│   ├── card.mustache
│   ├── document_item.mustache
│   ├── empty_state.mustache
│   ├── filter_form.mustache
│   ├── modal.mustache
│   ├── pagination.mustache
│   ├── progress_bar.mustache
│   ├── stat_card.mustache
│   ├── status_badge.mustache
│   ├── table.mustache
│   ├── timeline_item.mustache
│   └── vacancy_card.mustache
├── layouts/             (1 layout template)
│   └── base.mustache
├── pages/               (47 page templates in 10 subfolders)
│   ├── admin/           (14 admin pages)
│   ├── applications/    (4 application pages)
│   ├── convocatorias/   (4 convocatoria pages)
│   ├── documents/       (3 document pages)
│   ├── exemptions/      (0 - uses admin/exemptions*)
│   ├── public/          (4 public pages)
│   ├── reports/         (1 reports page)
│   ├── review/          (6 review pages)
│   ├── user/            (4 user pages)
│   └── vacancies/       (7 vacancy pages)
└── partials/            (0 - reserved for future use)
```

### PHP Renderer Updates
- Updated all 10 renderer trait files with new template paths:
  - `admin_renderer.php`: 9 template path updates
  - `application_renderer.php`: 3 template path updates
  - `committee_renderer.php`: 5 template path updates
  - `convocatoria_renderer.php`: 3 template path updates
  - `dashboard_renderer.php`: 1 template path update
  - `exemption_renderer.php`: 6 template path updates
  - `public_renderer.php`: 7 template path updates
  - `reports_renderer.php`: 1 template path update
  - `review_renderer.php`: 5 template path updates
  - `vacancy_renderer.php`: 6 template path updates

### Template Path Mapping (Old → New)
- `pages/dashboard` → `pages/admin/dashboard`
- `pages/vacancies` → `pages/vacancies/list`
- `pages/manage` → `pages/vacancies/manage`
- `pages/vacancy_detail` → `pages/vacancies/detail`
- `pages/manage_applications` → `pages/applications/manage`
- `pages/applications` → `pages/applications/list`
- `pages/apply` → `pages/applications/apply`
- `pages/application_detail` → `pages/applications/detail`
- `pages/convocatoria` → `pages/convocatorias/form`
- `pages/convocatorias` → `pages/convocatorias/list`
- `pages/view_convocatoria` → `pages/convocatorias/detail`
- `pages/admin_*` → `pages/admin/*`
- `pages/manage_exemptions` → `pages/admin/exemptions`
- And many more...

### Technical Notes
- All templates use consistent `jb-*` CSS class prefix
- Components are designed for reuse across multiple pages
- Page templates are organized by feature/module
- Clean separation between UI components, layouts, and page content

## [3.6.8] - 2025-12-12

### Fixed
- **Mustache Template Syntax Errors**: Fixed comment block syntax in 8 templates
  - Changed `{{! comment }}}` to `{{! comment }}` (triple to double closing braces)
  - Affected files:
    - `pages/admin_roles.mustache`
    - `pages/import_exemptions.mustache`
    - `pages/import_exemptions_results.mustache`
    - `pages/interview_complete_form.mustache`
    - `pages/program_reviewers.mustache`
    - `pages/reports.mustache`
    - `pages/reupload_document.mustache`
    - `pages/signup_success.mustache`
  - Triple braces `{{{var}}}` for unescaped HTML output remain intact

### Technical Notes
- Mustache comments should end with `}}` not `}}}`
- Triple braces `{{{...}}}` are only valid for variable output (unescaped HTML), not for comments

## [3.6.7] - 2025-12-12

### Changed
- **File Reorganization**: Moved 6 admin/management files from root to `admin/` folder:
  - `assign_reviewer.php` → `admin/assign_reviewer.php`
  - `bulk_validate.php` → `admin/bulk_validate.php`
  - `edit.php` → `admin/edit.php`
  - `manage_applications.php` → `admin/manage_applications.php`
  - `schedule_interview.php` → `admin/schedule_interview.php`
  - `validate_document.php` → `admin/validate_document.php`
- Updated all internal URL references across renderer traits and views
- Updated `require_once` paths in moved files

### Technical Notes
- Root directory now only contains essential entry points: `index.php`, `public.php`, `signup.php`, `updateprofile.php`, `reupload_document.php`
- All admin/management functionality consolidated under `admin/` folder
- Total files in `admin/`: 17 (was 11)

## [3.6.6] - 2025-12-12

### Fixed
- **Namespace Import Error**: Added missing `use moodle_url;` statement to 9 renderer traits
  - Fixed "Class not found" error when traits used `moodle_url` within their namespace
  - Affected files: `admin_renderer.php`, `application_renderer.php`, `committee_renderer.php`,
    `convocatoria_renderer.php`, `exemption_renderer.php`, `public_renderer.php`,
    `reports_renderer.php`, `review_renderer.php`, `vacancy_renderer.php`
  - `dashboard_renderer.php` already had the correct import

## [3.6.5] - 2025-12-12

### Removed
- **81 Unused Legacy Templates** (~6,431 lines removed):
  - 62 ROOT templates: `admin_*.mustache` (13), `application_*.mustache` (8),
    `convocatoria_*.mustache` (6), `dashboard*.mustache` (5), `vacancy_*.mustache` (6),
    `report_*.mustache` (5), and 19 other legacy templates
  - 14 unused components: `action_card`, `activity_timeline`, `badge`, `breadcrumbs`,
    `data_table`, `filter_form`, `info_card`, `list_group`, `page_header`, `progress_bar`,
    `stats_grid`, `status_badge`, `timeline`, `vacancy_card`
  - 5 reports/ templates: Entire `reports/` subfolder removed (handled inline by `pages/reports.mustache`)

### Technical Notes
- Template count reduced from ~130 to 49 (62% reduction)
- Kept templates: `signup_page.mustache`, `components/{alert,empty_state,stat_card}.mustache`,
  and all 45 `pages/*.mustache` files

## [3.6.4] - 2025-12-12

### Removed
- **8 Redundant Standalone Renderer Classes** (~3,593 lines removed):
  - `classes/output/admin_renderer.php` (~750 lines)
  - `classes/output/application_renderer.php` (~400 lines)
  - `classes/output/convocatoria_renderer.php` (~375 lines)
  - `classes/output/dashboard_renderer.php` (~570 lines)
  - `classes/output/public_renderer.php` (~560 lines)
  - `classes/output/reports_renderer.php` (~680 lines)
  - `classes/output/review_renderer.php` (~375 lines)
  - `classes/output/vacancy_renderer.php` (~325 lines)

### Technical Notes
- These standalone classes duplicated functionality already in `classes/output/renderer/*.php` traits
- Kept in `classes/output/`: `renderer.php` (main), `renderer_base.php` (base class), `ui_helper.php` (utility)

## [3.6.3] - 2025-12-12

### Fixed
- **Duplicate Method Collision**: Removed duplicate `render_reports_page` from `admin_renderer.php` trait
  - Method existed in both `admin_renderer.php` and `reports_renderer.php` traits
  - Would cause PHP fatal error when both traits are used in the same class
- **Method Relocation**: Moved `prepare_reports_page_data` from `admin_renderer.php` to `reports_renderer.php`
  - Keeps all reports-related methods together in the appropriate trait

## [3.6.2] - 2025-12-12

### Added
- **New Reports Renderer Trait** (`reports_renderer.php`):
  - Created dedicated trait for reporting functionality
  - Contains 6 methods: `prepare_overview_report_data`, `prepare_applications_report_data`,
    `prepare_documents_report_data`, `prepare_reviewers_report_data`, `prepare_timeline_report_data`,
    `get_application_status_color`
  - Added `render_reports_page` method

### Changed
- **Final Renderer Modularization**:
  - `renderer.php` reduced from 1,123 lines to **86 lines** (now only trait includes)
  - Moved 3 review methods to `review_renderer.php`:
    - `prepare_review_list_data`
    - `prepare_review_single_application_data`
    - `get_validation_checklist`
  - Moved 3 committee methods to `committee_renderer.php`:
    - `prepare_committee_list_data`
    - `prepare_committee_company_data`
    - `prepare_committee_vacancy_data`

### Technical Notes
- Total 10 renderer traits now fully contain all rendering logic
- `renderer.php` is now a clean shell that only imports and uses traits
- Trait file sizes (approximate):
  - `review_renderer.php`: ~1,070 lines
  - `committee_renderer.php`: ~850 lines
  - `vacancy_renderer.php`: ~1,100 lines
  - `admin_renderer.php`: ~1,035 lines
  - `dashboard_renderer.php`: ~789 lines
  - `convocatoria_renderer.php`: ~728 lines
  - `public_renderer.php`: ~702 lines
  - `application_renderer.php`: ~671 lines
  - `exemption_renderer.php`: ~504 lines
  - `reports_renderer.php`: ~315 lines

## [3.6.1] - 2025-12-12

### Changed
- **Complete Renderer Modularization**:
  - Moved 48 `prepare_*` methods from `renderer.php` to their corresponding traits
  - Distribution across traits:
    - `vacancy_renderer`: 7 methods (~920 lines)
    - `admin_renderer`: 10 methods (~783 lines)
    - `convocatoria_renderer`: 5 methods (~604 lines)
    - `application_renderer`: 4 methods (~553 lines)
    - `public_renderer`: 6 methods (~530 lines)
    - `review_renderer`: 5 methods (~496 lines)
    - `committee_renderer`: 5 methods (~467 lines)
    - `exemption_renderer`: 6 methods (~353 lines)

### Technical Notes
- `renderer.php` reduced from 6,354 lines to **1,123 lines** (~82% reduction)
- Total trait distribution now ~6,767 lines across 9 specialized files
- Each trait is self-contained with both `render_*` and `prepare_*` methods
- Traits now follow Moodle coding standards for modular renderer organization

## [3.6.0] - 2025-12-12

### Changed
- **Major Plugin Reorganization**:
  - Moved 7 admin files from root to `admin/` folder:
    - `migrate.php` → `admin/migrate.php`
    - `import_vacancies.php` → `admin/import_vacancies.php`
    - `import_exemptions.php` → `admin/import_exemptions.php`
    - `export_documents.php` → `admin/export_documents.php`
    - `manage_exemptions.php` → `admin/manage_exemptions.php`
    - `manage_committee.php` → `admin/manage_committee.php`
    - `manage_program_reviewers.php` → `admin/manage_program_reviewers.php`
  - Updated all internal URL references to new locations
  - Updated all `require_once` paths in moved files

- **Renderer Traits Activation**:
  - Activated all 9 renderer traits in `renderer.php` (previously commented out)
  - Removed 46 duplicate `render_*_page` methods from `renderer.php` (~430 lines)
  - Removed duplicate dashboard methods from `renderer.php` (~740 lines)
  - Added missing methods to traits:
    - `render_signup_success_page()` to `public_renderer`
    - `render_interview_complete_form_page()` to `committee_renderer`
    - `render_reupload_document_page()` to `review_renderer`
    - `render_import_exemptions_page()` and `render_import_exemptions_results_page()` to `exemption_renderer`
    - `render_admin_template_edit_page()`, `render_admin_doctype_form_page()`, `render_admin_doctype_confirm_delete_page()` to `admin_renderer`

### Removed
- **Unused Templates** (5 files):
  - `templates/convocatoria_stats.mustache`
  - `templates/review_comments.mustache`
  - `templates/reviewer_selector.mustache`
  - `templates/document_upload.mustache`
  - `templates/report_export_buttons.mustache`

### Technical Notes
- `renderer.php` reduced from 7,520 lines to 6,354 lines (~1,166 lines removed)
- All render methods now provided by traits in `classes/output/renderer/`
- Root templates serve as components/partials, `pages/` templates are full page layouts
- Improved code organization following Moodle plugin best practices

## [3.5.9] - 2025-12-12

### Added
- **Mustache Templates**:
  - `pages/interview_complete_form.mustache` for interview completion form
  - `pages/reupload_document.mustache` for document reupload page
- **Renderer Methods**:
  - `render_interview_complete_form_page()` and `prepare_interview_complete_form_data()`
  - `render_reupload_document_page()` and `prepare_reupload_document_data()`

### Changed
- Migrated `schedule_interview.php` complete interview view to renderer + template pattern
- Migrated `reupload_document.php` to renderer + template pattern
- Completed mustache migration - all significant inline HTML now uses templates

### Technical Notes
- `import_vacancies.php` HTML within moodleform `addElement()` is standard Moodle pattern (not migrated)
- `migrate.php` single html_writer usage for message building is acceptable
- All page-level rendering now uses renderer + template pattern

## [3.5.8] - 2025-12-12

### Added
- **Mustache Templates**:
  - `pages/admin_template_edit.mustache` for email template edit form
  - `pages/admin_doctype_form.mustache` for document type add/edit form
  - `pages/admin_doctype_confirm_delete.mustache` for doctype delete confirmation
  - `pages/signup_success.mustache` for registration success page
  - `pages/import_exemptions.mustache` for exemption import form
  - `pages/import_exemptions_results.mustache` for import results
- **Renderer Methods**:
  - `render_admin_template_edit_page()` and `prepare_admin_template_edit_data()`
  - `render_admin_doctype_form_page()` and `prepare_admin_doctype_form_data()`
  - `render_admin_doctype_confirm_delete_page()` and `prepare_admin_doctype_confirm_delete_data()`
  - `render_signup_success_page()` and `prepare_signup_success_data()`
  - `render_import_exemptions_page()` and `prepare_import_exemptions_data()`
  - `render_import_exemptions_results_page()` and `prepare_import_exemptions_results_data()`

### Changed
- Migrated `admin/templates.php` edit view to use renderer + template pattern
- Migrated `admin/doctypes.php` add/edit and confirm delete views to use renderer + template pattern
- Migrated `signup.php` success view to use renderer + template pattern
- Migrated `import_exemptions.php` form and results views to use renderer + template pattern
- Removed helper functions from admin pages (now in templates)
- Removed ~200 lines of inline HTML from PHP files

## [3.5.7] - 2025-12-12

### Added
- **Renderer Traits Architecture** (`classes/output/renderer/`):
  - `dashboard_renderer.php` - Dashboard page and widgets (~750 lines)
  - `public_renderer.php` - Public-facing pages (browse, vacancy, convocatoria)
  - `vacancy_renderer.php` - Vacancy management pages
  - `convocatoria_renderer.php` - Convocatoria management pages
  - `application_renderer.php` - Application pages
  - `review_renderer.php` - Review and validation pages
  - `admin_renderer.php` - Admin settings and tools pages
  - `exemption_renderer.php` - Exemption management pages
  - `committee_renderer.php` - Committee and reviewer assignment pages

### Changed
- Refactored `renderer.php` to use modular trait-based architecture
- Created `classes/output/renderer/` directory with 9 trait files
- Added documentation and structure for future `prepare_*` method migration
- Updated renderer.php header with trait loading and usage documentation

### Technical Notes
- Traits contain `render_*` methods grouped by functional area
- Main renderer.php still contains `prepare_*` methods for backward compatibility
- Future versions will move `prepare_*` methods to corresponding traits
- This reduces the 7,136-line renderer.php into manageable modules

## [3.5.6] - 2025-12-12

### Added
- **Mustache Templates**:
  - `pages/exemption_form.mustache` for exemption add/edit
  - `pages/exemption_revoke.mustache` for exemption revoke confirmation
  - `pages/exemption_view.mustache` for exemption details view
  - `pages/updateprofile.mustache` for profile update page
- **Renderer Methods**:
  - `render_exemption_form_page()` and `prepare_exemption_form_data()`
  - `render_exemption_revoke_page()` and `prepare_exemption_revoke_data()`
  - `render_exemption_view_page()` and `prepare_exemption_view_data()`
  - `render_updateprofile_page()` and `prepare_updateprofile_data()`

### Changed
- Migrated `manage_exemptions.php` sub-views (add/edit/view/revoke) to use renderer + template pattern
- Migrated `updateprofile.php` to use renderer + template pattern
- Removed ~250 lines of inline HTML from PHP files

## [3.5.5] - 2025-12-12

### Added
- **Mustache Templates**:
  - `pages/manage_applications.mustache` for application management
  - `pages/edit_select_convocatoria.mustache` for vacancy creation flow
  - `pages/edit_vacancy_form.mustache` for vacancy edit/create form
- **Renderer Methods**:
  - `render_manage_applications_page()` and `prepare_manage_applications_page_data()`
  - `render_edit_select_convocatoria_page()` and `prepare_edit_select_convocatoria_data()`
  - `render_edit_vacancy_form_page()` and `prepare_edit_vacancy_form_data()`

### Changed
- Migrated `manage_applications.php` to use renderer + template pattern
- Migrated `edit.php` (both selection and form views) to use renderer + template pattern
- Removed ~300 lines of inline HTML from PHP files

## [3.5.4] - 2025-12-12

### Added
- **Mustache Templates**:
  - `pages/admin_doctypes.mustache` for document types admin
  - `pages/admin_templates.mustache` for email templates admin
  - `pages/import_vacancies.mustache` for vacancy import form
  - `pages/import_vacancies_results.mustache` for import results
- **Renderer Methods**:
  - `render_admin_doctypes_page()` and `prepare_admin_doctypes_page_data()`
  - `render_admin_templates_page()` and `prepare_admin_templates_page_data()`
  - `render_import_vacancies_page()` and `prepare_import_vacancies_page_data()`
  - `render_import_vacancies_results_page()` and `prepare_import_vacancies_results_data()`

### Changed
- Migrated `admin/doctypes.php` listing view to use renderer + template pattern
- Migrated `admin/templates.php` listing view to use renderer + template pattern
- Migrated `import_vacancies.php` form and results to use renderer + template pattern
- Removed ~400 lines of inline HTML from admin pages

## [3.5.3] - 2025-12-12

### Added
- **Mustache Template**: `pages/migrate.mustache` for migration tool
- **Renderer Methods**: `render_migrate_page()` and `prepare_migrate_page_data()`
- **Dashboard Link**: Migration tool accessible from dashboard config section
- **Language Strings**: Added `access` string in EN/ES

### Changed
- Migrated `migrate.php` to use renderer + template pattern
- Removed inline HTML from migration page display section

## [3.5.2] - 2025-12-12

### Added
- **Language Strings**:
  - `features` / `Características` - for dashboard template
  - `allcommittees` / `Todos los comités` - for committee management
  - `createcompanies_help` - help text for import form
  - `updateexisting_help` - help text for import form

### Fixed
- `str_repeat()` type error in `renderer.php:4820` - cast `$cat->depth` to int

## [3.5.1] - 2025-12-12

### Added
- **Dynamic Status Strings** (EN/ES):
  - Application statuses: `status_submitted`, `status_under_review`, `status_docs_validated`, `status_docs_rejected`, `status_interview`, `status_selected`, `status_rejected`, `status_withdrawn`, `status_waitlist`
  - Vacancy statuses: `status_draft`, `status_published`, `status_closed`, `status_archived`, `status_assigned`
  - Vacancy status labels: `vacancystatus:draft`, `vacancystatus:published`, `vacancystatus:closed`, `vacancystatus:archived`, `vacancystatus:assigned`
  - Convocatoria status labels: `convocatoriastatus:draft`, `convocatoriastatus:open`, `convocatoriastatus:closed`, `convocatoriastatus:archived`

### Fixed
- **SQL LIMIT Parameter Error**: Changed from named parameter `LIMIT :limit` to Moodle's `get_records_sql()` limitfrom/limitnum parameters in `renderer.php`

## [3.5.0] - 2025-12-12

### Added
- **Phase 6: Dashboard Consolidation** (AGENTS.md)
  - Next convocatoria banner with countdown and urgency indicators
  - Pending notifications panel for reviewers/coordinators
  - Recent activity timeline with icons and relative timestamps
- **Privacy API**: Completed implementation with `consent` and `applicant_profile` tables
- **Reports Filter**: Mandatory convocatoria selection with blocking modal
- **Language Strings**: ~127 new strings for dashboard, Privacy API, and reports (EN/ES)

### Changed
- Updated `views/reports.php` with convocatoria filter requirement
- Enhanced `templates/pages/reports.mustache` with selection modal
- Enhanced `templates/pages/dashboard.mustache` with consolidated features

## [3.4.0] - 2025-12-12

### Added
- **User Tours**: Interactive onboarding tours with `jb-*` selectors
  - Dashboard tour for new users
  - Application process tour
  - Review workflow tour
  - Reports tour

## [3.2.0] - 2025-12-12

### Added
- **Phase 2: AMD JavaScript Modules** - 12 modules in `amd/src/`
  - `signup_form.js`: IOMAD company/department AJAX loading
  - `progress_steps.js`: Multi-step form navigation indicator
  - `loading_states.js`: Form submission feedback and spinners
  - `document_preview.js`: Document preview with modal viewer
  - `card_actions.js`: Vacancy/convocatoria card interactions
  - `public_filters.js`: Dynamic filter form with AJAX results
  - `apply_progress.js`: Tab-based application form navigation
  - `application_confirm.js`: Submission confirmation modal
  - `vacancy_form.js`: Dynamic company/dept/convocatoria selects
  - `convocatoria_form.js`: Dynamic selectors with date validation
  - `bulk_actions.js`: Checkbox selection and batch operations
  - `exemption_form.js`: Quick select document type groups

- **Language strings**: Added ~1,300 additional strings
  - Complete coverage of all plugin features
  - Both English and Spanish (Colombian) translations

### Removed
- Dark mode CSS support (prefers-color-scheme media query)

## [3.1.24] - 2025-12-12

### Added
- **styles.css**: Complete CSS system with `jb-*` prefix (~2,000 lines)
  - CSS custom properties for colors, spacing, and typography
  - Responsive grid system (`jb-row`, `jb-col-*`)
  - Spacing utilities (`jb-m-*`, `jb-p-*`, `jb-mb-*`, etc.)
  - Flexbox utilities (`jb-d-flex`, `jb-justify-*`, `jb-align-*`)
  - Card components (`jb-card`, `jb-card-header`, `jb-card-body`, etc.)
  - Button styles (`jb-btn`, `jb-btn-primary`, `jb-btn-outline-*`)
  - Badge styles (`jb-badge`, all color variants)
  - Table, form, alert, and list-group components
  - Progress bars, timeline, stat cards
  - Theme compatibility (Boost, Classic, Remui, Flavor)

- **lang/en/local_jobboard.php**: English language strings (~1,000 strings)
  - Plugin identification and all 31 capabilities
  - Navigation, actions, and common labels
  - Status strings for vacancies, applications, and documents
  - Convocatorias, vacancies, applications management
  - Document validation and reviewer management
  - Committee and interview management
  - Email templates with placeholder descriptions
  - Signup/profile fields and validation messages
  - Reports and dashboard strings
  - Error, success, and confirmation messages
  - Privacy API metadata strings
  - CLI and import/export strings
  - Accessibility strings

- **lang/es/local_jobboard.php**: Spanish (Colombian) translation
  - Complete translation of all English strings
  - Colombian Spanish terminology appropriate for ISER context
  - Localized document type names (Cédula, RUT, etc.)
  - Localized contract types (Docente de Cátedra, etc.)

- **CHANGELOG.md**: This changelog file
- **README.md**: Complete plugin documentation

### Changed
- Updated `version.php` with correct author information
  - Author: Alonso Arias <soporteplataformas@iser.edu.co>
  - Institution: ISER - Instituto Superior de Educación Rural
  - Version bumped to 3.1.24 (2025121239)

- Migrated root templates to `jb-*` CSS classes:
  - `templates/dashboard.mustache`
  - `templates/dashboard_widget.mustache`
  - `templates/vacancy_card.mustache`
  - `templates/vacancy_list.mustache`

## [3.1.23] - 2025-12-11

### Added
- Phase 1 critical infrastructure files implementation started
- Initial convocatoria workflow migrations

### Fixed
- Removed duplicate `get_convocatoria_status_class` method

## [3.1.21-23] - 2025-12-10

### Changed
- Complete Phase 3 workflow migrations
- Refactored convocatoria management

## [3.1.x] - Previous versions

### Features implemented
- Complete convocatoria management (CRUD)
- Vacancy management with custom fields
- Application submission system
- Document upload and management
- Manual document validation with checklist
- Bulk document validation
- Reviewer assignment (manual and automatic)
- Application status workflow
- Email notifications with customizable templates
- Role-based dashboard
- Public vacancy view
- Audit logging system
- IOMAD multi-tenant integration
- Committees by faculty
- Reviewers by program
- Privacy API (GDPR) compliance
- Colombian data protection (Ley 1581/2012) compliance

### Database tables (24)
- `local_jobboard_convocatoria`
- `local_jobboard_vacancy`
- `local_jobboard_vacancy_field`
- `local_jobboard_application`
- `local_jobboard_document`
- `local_jobboard_doc_validation`
- `local_jobboard_doctype`
- `local_jobboard_email_template`
- `local_jobboard_email_strings`
- `local_jobboard_exemption`
- `local_jobboard_config`
- `local_jobboard_audit`
- `local_jobboard_applicant_profile`
- `local_jobboard_consent`
- `local_jobboard_committee`
- `local_jobboard_committee_member`
- `local_jobboard_faculty`
- `local_jobboard_program`
- `local_jobboard_program_reviewer`
- `local_jobboard_faculty_reviewer`
- `local_jobboard_workflow_log`
- `local_jobboard_notification`
- `local_jobboard_interviewer`
- `local_jobboard_evaluation`

---

## Roadmap

### Phase 2: AMD Modules ✅ COMPLETED (v3.2.0)
- Created `amd/src/` folder structure
- Implemented 12 JavaScript modules
- Uses Moodle core AMD modules (no jQuery/Bootstrap JS)

### Phase 3: Renderer Refactoring (Planned)
- Split `renderer.php` (6,162 lines) into specialized renderers
- Create 10 renderer classes by functional area

### Phase 4: User Tours (Planned)
- Create `db/tours/` folder
- Implement 15 guided tours

### Phase 5-11: Additional Features (Planned)
- Review interface (mod_assign style)
- Global exemptions system
- Email template preview
- Reports by convocatoria
- PHPUnit tests
- Web Services API
- Calendar integration

---

## Support

- **Author**: Alonso Arias
- **Email**: soporteplataformas@iser.edu.co
- **Institution**: ISER - Instituto Superior de Educación Rural
- **Location**: Pamplona, Norte de Santander, Colombia

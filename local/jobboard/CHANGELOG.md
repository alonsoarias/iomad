# Changelog

All notable changes to the local_jobboard plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

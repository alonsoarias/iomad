# Changelog

All notable changes to the local_jobboard plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
  - Dark mode support via CSS media query

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

### Phase 2: AMD Modules (Planned)
- Create `amd/src/` folder structure
- Implement 15 JavaScript modules
- Use Moodle core AMD modules (no jQuery/Bootstrap JS)

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

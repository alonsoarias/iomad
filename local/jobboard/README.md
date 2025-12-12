# Job Board (local_jobboard)

A comprehensive Moodle plugin for managing academic job vacancies and teacher applications. Designed specifically for adjunct professor recruitment in higher education institutions.

## Overview

The Job Board plugin provides a complete recruitment workflow including:

- **Convocatorias**: Group multiple vacancies into recruitment campaigns
- **Vacancies**: Detailed job postings with custom fields
- **Applications**: Online submission with document upload
- **Document Validation**: Manual review with checklists
- **Reviewer Assignment**: Automatic or manual assignment by program
- **Committee Evaluation**: Faculty-based selection committees
- **Notifications**: Customizable email templates
- **Reports**: Comprehensive analytics and data export

## Requirements

| Requirement | Version |
|-------------|---------|
| Moodle | 4.1 - 4.5 |
| PHP | 7.4+ |
| IOMAD | Optional (for multi-tenant) |

## Installation

1. Download the plugin
2. Extract to `/local/jobboard/`
3. Visit Site Administration → Notifications
4. Complete the installation wizard

```bash
cd /path/to/moodle
git clone https://github.com/your-repo/local_jobboard.git local/jobboard
php admin/cli/upgrade.php
```

## Features

### For Applicants
- Browse public vacancies
- Create profile and upload documents
- Track application status
- Receive email notifications

### For Reviewers
- Review assigned applications
- Validate documents with checklist
- Approve or reject with comments
- Track workload and progress

### For Coordinators
- Create and manage convocatorias
- Publish vacancies
- Assign reviewers
- Change application status
- Generate reports

### For Administrators
- Configure document types
- Customize email templates
- Manage exemptions
- View audit logs

## Architecture

### IOMAD Multi-tenant Structure

```
LEVEL 1: IOMAD Instance
         └── virtual.iser.edu.co
              │
LEVEL 2: Companies (16 Tutorial Centers)
         ├── Pamplona (Main Campus)
         ├── Cúcuta
         ├── Tibú
         └── ... (13 more)
              │
LEVEL 3: Departments (Modalities)
         ├── On-site
         ├── Distance
         ├── Virtual
         └── Hybrid
              │
LEVEL 4: Sub-departments (Faculties)
         ├── Faculty of Administrative and Social Sciences (FCAS)
         └── Faculty of Engineering and Informatics (FII)
```

### Key Business Rules

1. **One application per convocatoria**: An applicant can only apply to ONE vacancy per convocatoria
2. **Committees by faculty**: Selection committees are organized by faculty, not by vacancy
3. **Reviewers by program**: Document reviewers are assigned at the program level
4. **Manual validation**: All document validation is 100% manual with checklists
5. **Customizable form**: Application form fields are configurable from admin

## Capabilities

| Capability | Description |
|------------|-------------|
| `view` | View job board |
| `viewinternal` | View internal vacancies |
| `manage` | Manage vacancies |
| `createvacancy` | Create new vacancies |
| `editvacancy` | Edit existing vacancies |
| `deletevacancy` | Delete vacancies |
| `publishvacancy` | Publish/unpublish vacancies |
| `apply` | Submit applications |
| `review` | Review applications |
| `validatedocuments` | Validate uploaded documents |
| `assignreviewers` | Assign reviewers to applications |
| `evaluate` | Evaluate candidates (committee) |
| `viewreports` | Access reports |
| `exportdata` | Export data |
| `configure` | Configure plugin settings |
| `managedoctypes` | Manage document types |
| `manageemailtemplates` | Manage email templates |
| `manageexemptions` | Manage document exemptions |

## Roles

### Reviewer (jobboard_reviewer)
- View and review assigned applications
- Validate documents
- Download any document

### Coordinator (jobboard_coordinator)
- Full vacancy management
- View all applications
- Assign reviewers
- Change application status
- Access reports

### Committee Member (jobboard_committee)
- Evaluate candidates
- View evaluations
- Download documents

## Document Types

The plugin includes 20+ predefined document types:

| Category | Documents |
|----------|-----------|
| Identification | ID Card (Cédula), Tax ID (RUT) |
| Academic | Undergraduate degree, Graduate degree, Graduation certificate |
| Employment | Work certificates, Resume/CV |
| Legal | Criminal background, Disciplinary background, Fiscal background |
| Health | Medical certificate, Health insurance (EPS), Pension |
| Financial | Bank account certificate |

## Exemptions

Support for document exemptions based on:

- **Historical ISER**: Documents already on file
- **Recent documents**: Submitted within 6 months
- **Internal transfer**: Between campuses
- **Rehire**: Previous employees
- **Age**: 50+ years exempt from military card
- **Gender**: Military card only for males

## Application Workflow

```
[Applicant]              [Reviewer]              [Committee]
     │                        │                       │
     ▼                        │                       │
┌─────────┐                   │                       │
│ Submits │                   │                       │
│ Application                 │                       │
└────┬────┘                   │                       │
     │                        │                       │
     ▼                        │                       │
┌─────────────────┐           │                       │
│ SUBMITTED       │           │                       │
└────────┬────────┘           │                       │
         │ [Assign reviewer]  │                       │
         ▼                    │                       │
┌─────────────────┐           │                       │
│ UNDER_REVIEW    │◄──────────┤                       │
└────────┬────────┘           │                       │
         │                    ▼                       │
         │             ┌─────────────┐                │
         │             │ Reviews     │                │
         │             │ documents   │                │
         │             └──────┬──────┘                │
         │                    │                       │
         │      ┌─────────────┴─────────────┐        │
         │      ▼                           ▼        │
         │ ┌─────────────────┐    ┌─────────────────┐│
         │ │ DOCS_VALIDATED  │    │ DOCS_REJECTED   ││
         │ └────────┬────────┘    └────────┬────────┘│
         │          │                      │         │
         │          ▼                      ▼         │
         │   ┌─────────────────┐    [Applicant      │
         │   │ INTERVIEW       │     corrects]      │
         │   └────────┬────────┘                    │
         │            │                             │
         │            ▼                             ▼
         │     ┌─────────────┐            ┌──────────────┐
         │     │ Committee   │◄───────────│ Selection    │
         │     │ evaluates   │            │ Committee    │
         │     └──────┬──────┘            └──────────────┘
         │            │
         │  ┌─────────┼─────────┐
         │  ▼         ▼         ▼
         │ SELECTED  WAITLIST  REJECTED
```

## CLI Tools

```bash
# Import profiles from text file
php local/jobboard/cli/cli.php --create-structure --publish --public

# View help
php local/jobboard/cli/cli.php --help
```

## Development

### CSS Classes

All CSS classes use the `jb-` prefix for namespace isolation:

```html
<div class="jb-card jb-card-shadow jb-mb-3">
    <div class="jb-card-header">
        <h5 class="jb-mb-0">Title</h5>
    </div>
    <div class="jb-card-body">
        <p class="jb-text-muted">Content</p>
    </div>
</div>
```

### Language Strings

Always use `get_string()` - never hardcode strings:

```php
$title = get_string('vacancies', 'local_jobboard');
```

### Templates

Use Mustache templates with documentation:

```mustache
{{!
    @template local_jobboard/component_name

    Description of the template.

    Context variables required:
    * variable1 - Description
    * variable2 - Description
}}
```

## Compliance

- **GDPR**: Privacy API implemented for data export/deletion
- **Ley 1581/2012**: Colombian personal data protection compliance
- **Accessibility**: WCAG 2.1 guidelines followed

## Support

- **Author**: Alonso Arias
- **Email**: soporteplataformas@iser.edu.co
- **Institution**: ISER - Instituto Superior de Educación Rural
- **Supervision**: Vicerrectoría Académica ISER
- **Location**: Pamplona, Norte de Santander, Colombia

## License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

See [GNU GPL v3](http://www.gnu.org/copyleft/gpl.html) for details.

---

*Last updated: December 2025*
*Plugin version: 3.1.24*

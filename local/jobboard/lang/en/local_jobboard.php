<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * English language strings for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// General strings.
$string['pluginname'] = 'Job Board';
$string['jobboard'] = 'Job Board';
$string['jobboard:desc'] = 'Vacancy and application management system for adjunct professors';

// Navigation.
$string['vacancies'] = 'Vacancies';
$string['myvacancies'] = 'My Vacancies';
$string['myapplications'] = 'My Applications';
$string['newvacancy'] = 'New Vacancy';
$string['addvacancy'] = 'Add Vacancy';
$string['viewvacancies'] = 'View Vacancies';
$string['managevacancies'] = 'Manage Vacancies';
$string['reviewapplications'] = 'Review Applications';
$string['reports'] = 'Reports';
$string['settings'] = 'Settings';
$string['dashboard'] = 'Dashboard';
$string['exemptions'] = 'ISER Exemptions';

// Capabilities.
$string['jobboard:view'] = 'View job board';
$string['jobboard:viewinternal'] = 'View internal vacancies';
$string['jobboard:manage'] = 'Manage job board';
$string['jobboard:createvacancy'] = 'Create vacancies';
$string['jobboard:editvacancy'] = 'Edit vacancies';
$string['jobboard:deletevacancy'] = 'Delete vacancies';
$string['jobboard:publishvacancy'] = 'Publish vacancies';
$string['jobboard:viewallvacancies'] = 'View all vacancies';
$string['jobboard:manageconvocatorias'] = 'Manage calls for applications';
$string['jobboard:apply'] = 'Apply for vacancies';
$string['jobboard:viewownapplications'] = 'View own applications';
$string['jobboard:viewallapplications'] = 'View all applications';
$string['jobboard:changeapplicationstatus'] = 'Change application status';
$string['jobboard:review'] = 'Review applications';
$string['jobboard:validatedocuments'] = 'Validate documents';
$string['jobboard:reviewdocuments'] = 'Review documents';
$string['jobboard:assignreviewers'] = 'Assign reviewers';
$string['jobboard:downloadanydocument'] = 'Download any document';
$string['jobboard:evaluate'] = 'Evaluate candidates';
$string['jobboard:viewevaluations'] = 'View evaluations';
$string['jobboard:manageworkflow'] = 'Manage workflow';
$string['jobboard:viewreports'] = 'View reports';
$string['jobboard:exportreports'] = 'Export reports';
$string['jobboard:exportdata'] = 'Export data';
$string['jobboard:manageexemptions'] = 'Manage ISER exemptions';
$string['jobboard:useapi'] = 'Use REST API';
$string['jobboard:accessapi'] = 'Access REST API';
$string['jobboard:manageapitokens'] = 'Manage API tokens';
$string['jobboard:configure'] = 'Configure job board';
$string['jobboard:managedoctypes'] = 'Manage document types';
$string['jobboard:manageemailtemplates'] = 'Manage email templates';
$string['jobboard:viewpublicvacancies'] = 'View public vacancies';
$string['jobboard:viewinternalvacancies'] = 'View internal vacancies';
$string['jobboard:unlimitedapplications'] = 'Unlimited applications';

// Custom roles.
$string['role_reviewer'] = 'Job Board Document Reviewer';
$string['role_reviewer_desc'] = 'Can review and validate documents submitted by applicants in the job board system.';
$string['role_coordinator'] = 'Job Board Coordinator';
$string['role_coordinator_desc'] = 'Can manage vacancies, calls for applications, and coordinate the selection process.';
$string['role_committee'] = 'Job Board Selection Committee';
$string['role_committee_desc'] = 'Can evaluate candidates and participate in the final selection decisions.';

// Vacancy fields.
$string['vacancycode'] = 'Vacancy code';
$string['vacancycode_help'] = 'A unique internal code to identify this vacancy. This code will be used in reports, communications, and as a reference throughout the selection process. Use a consistent format such as VAC-2025-001 or PROF-MAT-001.';
$string['vacancytitle'] = 'Vacancy title';
$string['vacancytitle_help'] = 'Enter a clear and descriptive title for the position. This will be displayed in vacancy listings and search results. Example: "Full-time Mathematics Professor" or "Part-time English Instructor". Keep it concise but informative.';
$string['vacancydescription'] = 'Description';
$string['vacancydescription_help'] = 'Provide a comprehensive description of the position including: main responsibilities and duties, working hours and schedule, location or campus, department or faculty. This description will be visible to all applicants and helps them understand the role.';
$string['contracttype'] = 'Contract type';
$string['contracttype_help'] = 'Select the employment contract type for this position. Options typically include: Full-time (dedicated), Part-time (hourly), Temporary, Fixed-term. The contract type affects benefits, schedule expectations, and compensation.';
$string['duration'] = 'Duration';
$string['duration_help'] = 'Specify the expected duration of the contract. For permanent positions, you can indicate "Indefinite". For temporary or fixed-term contracts, specify the period (e.g., "6 months", "1 academic year", "Until December 2025").';
// Salary field removed in Phase 10 - compensation handled externally.
$string['location'] = 'Location';
$string['location_help'] = 'Specify where the position will be performed. Include campus name, city, or indicate if remote work is possible. Example: "Main Campus - Bogota", "Virtual/Remote", or "Hybrid - 3 days on-site".';
$string['modality'] = 'Modality';
$string['modality_help'] = 'Educational modality for this position: Presencial (on-site), A Distancia (distance learning), Virtual (online), or Híbrida (hybrid).';
$string['department'] = 'Department/Unit';
$string['department_help'] = 'Select or enter the department, faculty, or organizational unit where this position belongs. This helps applicants understand the organizational context and who they would report to.';
$string['category'] = 'Category';
$string['company'] = 'Company/Site';
$string['company_help'] = 'In multi-tenant environments (IOMAD), select the company or site this vacancy belongs to. The vacancy will only be visible to users associated with this company unless published as public.';
$string['opendate'] = 'Opening date';
$string['opendate_help'] = 'The date when this vacancy will become visible and applications will be accepted. The vacancy will not appear in listings before this date. Set this to a future date if you want to prepare the vacancy in advance.';
$string['closedate'] = 'Closing date';
$string['closedate_help'] = 'The deadline for receiving applications. After this date: the vacancy will show as "Closed", no new applications will be accepted, existing applications will proceed to review. Plan sufficient time for the review process.';
$string['positions'] = 'Number of positions';
$string['positions_help'] = 'The total number of positions available for this vacancy. This affects: how many candidates can be selected, reporting and statistics, and helps applicants understand competition level.';
$string['requirements'] = 'Minimum requirements';
$string['requirements_help'] = 'List all mandatory requirements for this position: academic qualifications (degrees, certifications), years of experience required, specific skills or competencies, language requirements. Be specific to help candidates assess their eligibility. Candidates not meeting these requirements may be automatically disqualified.';
$string['desirable'] = 'Desirable requirements';
$string['desirable_help'] = 'List qualifications that are valued but not mandatory: additional certifications, extra experience, special skills, publications, awards. Meeting these requirements can give candidates an advantage during the selection process but will not disqualify those who do not have them.';
$string['status'] = 'Status';
$string['createdby'] = 'Created by';
$string['modifiedby'] = 'Modified by';
$string['timecreated'] = 'Created date';
$string['timemodified'] = 'Modified date';

// Vacancy statuses.
$string['status:draft'] = 'Draft';
$string['status:published'] = 'Published';
$string['status:closed'] = 'Closed';
$string['status:assigned'] = 'Assigned';

// Application statuses.
$string['appstatus:submitted'] = 'Submitted';
$string['appstatus:under_review'] = 'Under review';
$string['appstatus:docs_validated'] = 'Documents validated';
$string['appstatus:docs_rejected'] = 'Documents rejected';
$string['appstatus:interview'] = 'Interview scheduled';
$string['appstatus:selected'] = 'Selected';
$string['appstatus:rejected'] = 'Not selected';
$string['appstatus:withdrawn'] = 'Withdrawn';

// Document types.
$string['doctype:sigep'] = 'SIGEP II Resume Format';
$string['doctype:bienes_rentas'] = 'Assets and Income Declaration';
$string['doctype:cedula'] = 'National ID';
$string['doctype:titulo_academico'] = 'Academic Degrees';
$string['doctype:tarjeta_profesional'] = 'Professional License';
$string['doctype:libreta_militar'] = 'Military Service Card';
$string['doctype:formacion_complementaria'] = 'Complementary Training Certificates';
$string['doctype:certificacion_laboral'] = 'Employment Certificates';
$string['doctype:rut'] = 'Tax ID (RUT)';
$string['doctype:eps'] = 'Health Insurance Certificate';
$string['doctype:pension'] = 'Pension Certificate';
$string['doctype:cuenta_bancaria'] = 'Bank Account Certificate';
$string['doctype:antecedentes_disciplinarios'] = 'Disciplinary Background (Attorney General)';
$string['doctype:antecedentes_fiscales'] = 'Fiscal Background (Comptroller)';
$string['doctype:antecedentes_judiciales'] = 'Criminal Background (Police)';
$string['doctype:medidas_correctivas'] = 'National Registry of Corrective Measures';
$string['doctype:inhabilidades'] = 'Disqualifications Check (Sexual Offenses)';
$string['doctype:redam'] = 'REDAM (Child Support Debtors Registry)';
$string['doctype:otro'] = 'Other document';

// Document validation statuses.
$string['docstatus:pending'] = 'Pending review';
$string['docstatus:approved'] = 'Approved';
$string['docstatus:rejected'] = 'Rejected';

// Form labels and placeholders.
$string['entercode'] = 'Enter vacancy code';
$string['entertitle'] = 'Enter vacancy title';
$string['enterdescription'] = 'Describe the position and its functions';
$string['selectcontracttype'] = 'Select contract type';
$string['selectcategory'] = 'Select a category';
$string['selectcompany'] = 'Select a company/site';
$string['selectdepartment'] = 'Select a department';
$string['selectstatus'] = 'Select status';
$string['uploadfile'] = 'Upload file';
$string['choosefiles'] = 'Choose files';
$string['nodocuments'] = 'No documents uploaded';

// Contract types.
$string['contract:catedra'] = 'Adjunct Professor';
$string['contract:temporal'] = 'Temporary';
$string['contract:termino_fijo'] = 'Fixed Term';
$string['contract:prestacion_servicios'] = 'Service Contract';
$string['contract:planta'] = 'Permanent';

// Educational modalities.
$string['modality'] = 'Modality';
$string['modality_help'] = 'Educational modality of the academic program.';
$string['modality:presencial'] = 'In-person';
$string['modality:distancia'] = 'Distance';
$string['modality:virtual'] = 'Virtual';
$string['modality:hibrida'] = 'Hybrid';
$string['selectmodality'] = 'Select modality...';

// Actions.
$string['create'] = 'Create';
$string['edit'] = 'Edit';
$string['delete'] = 'Delete';
$string['view'] = 'View';
$string['publish'] = 'Publish';
$string['unpublish'] = 'Unpublish';
$string['close'] = 'Close';
$string['reopen'] = 'Reopen';
$string['assign'] = 'Assign';
$string['apply'] = 'Apply';
$string['withdraw'] = 'Withdraw application';
$string['save'] = 'Save';
$string['saveandcontinue'] = 'Save and continue';
$string['cancel'] = 'Cancel';
$string['confirm'] = 'Confirm';
$string['back'] = 'Back';
$string['next'] = 'Next';
$string['previous'] = 'Previous';
$string['search'] = 'Search';
$string['filter'] = 'Filter';
$string['clearfilters'] = 'Clear filters';
$string['export'] = 'Export';
$string['import'] = 'Import';
$string['download'] = 'Download';
$string['upload'] = 'Upload';
$string['preview'] = 'Preview';
$string['validate'] = 'Validate';
$string['reject'] = 'Reject';
$string['approve'] = 'Approve';
$string['resubmit'] = 'Resubmit';
$string['manage'] = 'Manage';

// Migration tools.
$string['migrationtools'] = 'Migration Tools';
$string['importvacancies'] = 'Import Vacancies';
$string['importvacancies_desc'] = 'Import vacancies from CSV file';
$string['importexemptions'] = 'Import Exemptions';
$string['importexemptions_desc'] = 'Import ISER exemptions from CSV';
$string['exportdocuments'] = 'Export Documents';
$string['exportdocuments_desc'] = 'Export application documents as ZIP';
$string['manageexemptions'] = 'Manage Exemptions';
$string['manageexemptions_desc'] = 'Manage user document exemptions';

// Migration strings.
$string['migrateplugin'] = 'Migrate Plugin';
$string['migrateplugin_desc'] = 'Export or import plugin configuration and data between Moodle instances';
$string['exportdata'] = 'Export Data';
$string['exportdata_desc'] = 'Export plugin configuration to a JSON file that can be imported in another instance.';
$string['importdata'] = 'Import Data';
$string['importdata_desc'] = 'Import plugin configuration from a previously exported JSON file.';
$string['migrationfile'] = 'Migration file (JSON)';
$string['overwriteexisting'] = 'Overwrite existing records';
$string['dryrunmode'] = 'Dry run (preview without saving)';
$string['exportdownload'] = 'Download Export';
$string['importupload'] = 'Upload and Import';
$string['importwarning'] = 'Warning: Import will modify your database. Use dry run first to preview changes.';
$string['invalidmigrationfile'] = 'Invalid migration file. Please upload a valid JobBoard export file.';
$string['dryrunresults'] = 'Dry Run Results (no changes made):';
$string['importerror'] = 'Import error';
$string['pluginsettings'] = 'Plugin settings';
$string['exemptions'] = 'User exemptions';
$string['importeddoctypes'] = 'Document types: {$a->inserted} inserted, {$a->updated} updated, {$a->skipped} skipped';
$string['importedemails'] = 'Email templates: {$a->inserted} inserted, {$a->updated} updated, {$a->skipped} skipped';
$string['importedconvocatorias'] = 'Convocatorias: {$a->inserted} inserted, {$a->updated} updated, {$a->skipped} skipped';
$string['importedvacancies'] = 'Vacancies: {$a->inserted} inserted, {$a->updated} updated, {$a->skipped} skipped';
$string['importedsettings'] = 'Settings: {$a} updated';
$string['importedexemptions'] = 'Exemptions: {$a->inserted} inserted, {$a->updated} updated, {$a->skipped} skipped';
$string['importedapplications'] = 'Applications: {$a->inserted} inserted, {$a->skipped} skipped';
$string['importeddocuments'] = 'Documents: {$a->inserted} inserted, {$a->skipped} skipped';
$string['importedfiles'] = 'Files: {$a->inserted} inserted, {$a->skipped} skipped';
$string['importingfrom'] = 'Importing from {$a->site} (v{$a->version}) exported on {$a->date}';
$string['fullexport'] = 'Complete Export';
$string['fullexport_info'] = 'This will export ALL plugin data including applications, documents, files, and configurations. The ZIP file can be imported into another Moodle IOMAD instance with JobBoard installed.';
$string['datatorexport'] = 'Data to export';
$string['exportwarning_files'] = 'The export includes files and may take some time to generate. Please wait...';
$string['documents'] = 'Documents';
$string['files'] = 'Files';
$string['migrationinfo_title'] = 'About Migration';
$string['migrationinfo_desc'] = 'This tool allows you to transfer ALL JobBoard data between Moodle instances. Export creates a ZIP file with all database records and files. Import reads the ZIP and restores the data with ID mapping for related records. No data is optional - everything is exported.';
$string['exporterror'] = 'Export error';
$string['applications'] = 'Applications';
$string['auditlogs'] = 'Audit logs';

// Dashboard sections.
$string['datatools'] = 'Data Import/Export';
$string['systemmigration'] = 'System Migration';
$string['reports_desc'] = 'View statistics and reports';
$string['pluginsettings_desc'] = 'Configure plugin options';
$string['doctypes_desc'] = 'Manage required document types';
$string['migrateplugin_full_desc'] = 'Transfer ALL plugin data to another Moodle IOMAD instance. Creates a complete backup that can be restored on a new installation.';
$string['migrate_includes_doctypes'] = 'Document types and configurations';
$string['migrate_includes_convocatorias'] = 'Convocatorias with exemptions';
$string['migrate_includes_vacancies'] = 'All vacancies and settings';
$string['migrate_includes_applications'] = 'Applications with documents';
$string['migrate_includes_files'] = 'All uploaded files';
$string['openmigrationtool'] = 'Open Migration Tool';

// Public view strings.
$string['totalpositions'] = 'Total Positions';
$string['closingsoon'] = 'Closing Soon';
$string['closesindays'] = 'Closes in {$a} days';
$string['noconvocatorias'] = 'No active convocatorias at this time';
$string['startdate'] = 'Start Date';
$string['enddate'] = 'End Date';
$string['vacancy'] = 'Vacancy';
$string['public'] = 'Public';
$string['internal'] = 'Internal';
$string['type'] = 'Type';
$string['convocatoria_footer_info'] = 'This convocatoria has {$a->vacancies} vacancies with {$a->positions} total positions available.';

// Messages.
$string['vacancycreated'] = 'Vacancy created successfully';
$string['vacancyupdated'] = 'Vacancy updated successfully';
$string['vacancydeleted'] = 'Vacancy deleted successfully';
$string['vacancypublished'] = 'Vacancy published successfully';
$string['vacancyclosed'] = 'Vacancy closed successfully';
$string['vacancyreopened'] = 'Vacancy reopened successfully';
$string['vacancyunpublished'] = 'Vacancy unpublished successfully';
$string['applicationsubmitted'] = 'Application submitted successfully';
$string['applicationwithdrawn'] = 'Application withdrawn successfully';
$string['documentuploaded'] = 'Document uploaded successfully';
$string['documentvalidated'] = 'Document validated successfully';
$string['documentrejected'] = 'Document rejected';
$string['changesaved'] = 'Changes saved successfully';

// Errors.
$string['error:vacancynotfound'] = 'Vacancy not found';
$string['error:vacancynotpublic'] = 'This vacancy is not publicly available';
$string['error:applicationnotfound'] = 'Application not found';
$string['error:documentnotfound'] = 'Document not found';
$string['error:codeexists'] = 'A vacancy with this code already exists';
$string['error:invaliddates'] = 'Closing date must be after opening date';
$string['error:closedateexpired'] = 'Closing date has passed';
$string['error:cannotdelete'] = 'Cannot delete vacancy because it has applications';
$string['error:cannotedit'] = 'Cannot edit this vacancy';
$string['error:cannotpublish'] = 'Cannot publish: complete all required fields';
$string['error:alreadyapplied'] = 'You already have an active application for this vacancy';
$string['error:vacancyclosed'] = 'The vacancy is closed and not accepting applications';
$string['error:invalidfile'] = 'The file is not valid';
$string['error:filetoobig'] = 'File exceeds maximum allowed size';
$string['error:invalidformat'] = 'File format is not allowed';
$string['error:invalidmimetype'] = 'File type is not valid';
$string['error:documentexpired'] = 'Document issue date is too old';
$string['error:consentrequired'] = 'You must accept the terms and conditions';
$string['error:permissiondenied'] = 'You do not have permission to perform this action';
$string['error:invalidtransition'] = 'Status transition not allowed';
$string['error:noaccess'] = 'You do not have access to this resource';
$string['error:requiredfield'] = 'This field is required';
$string['error:cannotdelete_hasapplications'] = 'Cannot delete vacancy: there are {$a} application(s) associated';
$string['error:cannotunpublish'] = 'Cannot unpublish vacancy: there are applications associated';
$string['error:cannotclose'] = 'Cannot close vacancy: must be in published status';
$string['error:cannotreopen'] = 'Cannot reopen vacancy: must be in closed status';

// Consent and privacy.
$string['consent'] = 'Informed consent';
$string['consenttext'] = 'Data processing policy text';
$string['consentagree'] = 'I have read and accept the personal data processing policy';
$string['consentverify'] = 'I authorize verification of the information provided';
$string['consenttruth'] = 'I declare that the information provided is true and complete';
$string['digitalsignature'] = 'Digital signature (full name)';
$string['privacypolicy'] = 'Privacy Policy';
$string['habeasconsent'] = 'Habeas Data Consent';

// ISER Exemptions.
$string['iserexemption'] = 'ISER Exemption';
$string['iserhistoric'] = 'ISER historic personnel';
$string['isernewpersonnel'] = 'New personnel';
$string['iserexemptionmessage'] = 'You are ISER historic personnel. The following documents are not required because they are already in your Employment History:';
$string['exempteddocs'] = 'Exempted documents';
$string['exemptionvalid'] = 'Valid exemption';
$string['exemptionexpired'] = 'Expired exemption';
$string['manageexemptions'] = 'Manage ISER exemptions';
$string['addexemption'] = 'Add exemption';
$string['editexemption'] = 'Edit exemption';
$string['deleteexemption'] = 'Delete exemption';
$string['importexemptions'] = 'Import exemptions from CSV';
$string['exemptionimported'] = 'Exemptions imported successfully';

// Convocatorias (Calls/Campaigns).
$string['convocatoria'] = 'Call';
$string['convocatorias'] = 'Calls';
$string['manageconvocatorias'] = 'Manage Calls';
$string['addconvocatoria'] = 'Add call';
$string['editconvocatoria'] = 'Edit call';
$string['deleteconvocatoria'] = 'Delete call';
$string['convocatoriacode'] = 'Call code';
$string['convocatorianame'] = 'Call name';
$string['convocatoriadescription'] = 'Description';
$string['convocatoriastartdate'] = 'Start date';
$string['convocatoriaenddate'] = 'End date';
$string['convocatoriastatus'] = 'Status';
$string['convocatoriaterms'] = 'Terms and conditions';
$string['convocatoriavacancies'] = 'Vacancies in this call';
$string['convocatoria_status_draft'] = 'Draft';
$string['convocatoria_status_open'] = 'Open';
$string['convocatoria_status_closed'] = 'Closed';
$string['convocatoria_status_archived'] = 'Archived';
$string['convocatoriacreated'] = 'Call created successfully';
$string['convocatoriaupdated'] = 'Call updated successfully';
$string['convocatoriadeleted'] = 'Call deleted successfully';
$string['noconvocatorias'] = 'No calls found';
$string['convocatorianotfound'] = 'Call not found';
$string['convocatoriaactive'] = 'Active calls';
$string['convocatoriaclosed'] = 'Closed calls';
$string['viewconvocatoria'] = 'View call';
$string['convocatoriadetails'] = 'Call details';
$string['selectconvocatoria'] = 'Select a call';
$string['convocatoriahelp'] = 'A call groups related vacancies and sets the period during which applications are accepted.';
$string['convocatoriavacancycount'] = '{$a} vacancies';
$string['createvacancyinconvocatoria'] = 'Add vacancy to this call';
$string['confirmdeletevconvocatoria'] = 'Are you sure you want to delete this call? This will not delete the associated vacancies.';
$string['convocatoriawithvacancies'] = 'This call has {$a} vacancies. They will be unlinked but not deleted.';
$string['openconvocatoria'] = 'Open call';
$string['closeconvocatoria'] = 'Close call';
$string['archiveconvocatoria'] = 'Archive call';
$string['reopenconvocatoria'] = 'Reopen call';
$string['confirmopenconvocatoria'] = 'Are you sure you want to open this call? All draft vacancies will be published.';
$string['confirmcloseconvocatoria'] = 'Are you sure you want to close this call? All vacancies will be closed.';
$string['confirmreopenconvocatoria'] = 'Are you sure you want to reopen this call? Closed vacancies will be published again.';
$string['confirmarchiveconvocatoria'] = 'Are you sure you want to archive this call? This action is for completed calls.';
$string['convocatoriaopened'] = 'Call opened successfully';
$string['convocatoriaclosedmsg'] = 'Call closed successfully';
$string['convocatoriaarchived'] = 'Call archived successfully';
$string['convocatoriareopened'] = 'Call reopened successfully';
$string['error:convocatoriahasnovacancies'] = 'Cannot open a call without vacancies';
$string['error:cannotreopenconvocatoria'] = 'Cannot reopen call: must be in closed status';
$string['error:convocatoriadatesinvalid'] = 'End date must be after start date';
$string['error:convocatoriacodeexists'] = 'A call with this code already exists';
$string['error:cannotdeleteconvocatoria'] = 'Cannot delete this call. Only draft or archived calls can be deleted';
$string['dates'] = 'Dates';
$string['vacanciesforconvocatoria'] = 'Vacancies for call';
$string['backtoconvocatorias'] = 'Back to calls';
$string['backtoconvocatoria'] = 'Back to call';
$string['period'] = 'Period';
$string['totalvacancies'] = 'Total Vacancies';
$string['totalconvocatorias'] = 'Total Calls';
$string['browseconvocatorias'] = 'Browse Calls';
$string['viewconvocatorias'] = 'View calls';
$string['activeconvocatorias'] = 'Active Calls';
$string['activeconvocatorias_alert'] = 'There are {$a} active calls available';
$string['novacancies'] = 'No vacancies available';
$string['daysleft'] = '{$a} days left';
$string['convocatorias_dashboard_desc'] = 'Create and manage job calls that group related vacancies.';

// Convocatoria document exemptions.
$string['convocatoriadocexemptions'] = 'Document Exemptions';
$string['exempteddoctypes'] = 'Exempted Document Types';
$string['exempteddoctypes_help'] = 'Select document types that are NOT required for applicants in this call. All selected documents will be exempted for ALL applicants in this convocatoria. This is a global exemption at the call level.';
$string['exemptionreason'] = 'Exemption Reason';
$string['exemptionreason_help'] = 'Provide a reason for exempting the selected documents. This will be recorded for audit purposes and may be displayed to applicants.';
$string['error:convocatorianotfound'] = 'Call not found';
$string['error:doctypenotfound'] = 'Document type not found';
$string['error:alreadyexempted'] = 'This document type is already exempted for this call';
$string['docexemptioncreated'] = 'Document exemption added successfully';
$string['docexemptiondeleted'] = 'Document exemption removed successfully';
$string['nodocexemptions'] = 'No document exemptions configured for this call';
$string['alldocumentsrequired'] = 'All document types are required';
$string['exempteddocumentscount'] = '{$a} document type(s) exempted';

// Extemporaneous vacancies removed in Phase 10 - dates now managed at convocatoria level.
$string['convocatoriadates'] = 'Call dates';
$string['usingconvocatoriadates'] = 'Using call dates';

// External links.
$string['externallinkinfo'] = 'You must download this certificate from the official website and upload it here';
$string['linkantecedentesjudiciales'] = 'Criminal Background (National Police)';
$string['linkmedidascorrectivas'] = 'Corrective Measures (National Police)';
$string['linkinhabilidades'] = 'Disqualifications for Sexual Offenses';
$string['linkredam'] = 'REDAM - Citizen Folder';
$string['linksigep'] = 'SIGEP II';

// Document validation checklist.
$string['checklist'] = 'Verification checklist';
$string['checklistitem'] = 'Verification item';
$string['checklistcomplete'] = 'All items verified';
$string['checklistincomplete'] = 'Items pending verification';
$string['reviewernotes'] = 'Reviewer notes';
$string['rejectreason'] = 'Rejection reason';
$string['enterrejectreason'] = 'Enter the reason for rejection';

// Workflow.
$string['workflow'] = 'Workflow';
$string['workflowlog'] = 'Change history';
$string['statuschange'] = 'Status change';
$string['previousstatus'] = 'Previous status';
$string['newstatus'] = 'New status';
$string['changedby'] = 'Changed by';
$string['changedate'] = 'Change date';
$string['comments'] = 'Comments';

// Notifications.
$string['notification'] = 'Notification';
$string['notifications'] = 'Notifications';
$string['emailsent'] = 'Email sent';
$string['emailfailed'] = 'Failed to send email';
$string['notificationsettings'] = 'Notification settings';
$string['emailtemplates'] = 'Email templates';
$string['edittemplate'] = 'Edit template';

// Reports.
$string['report:applications'] = 'Applications report';
$string['report:documents'] = 'Documents report';
$string['report:metrics'] = 'Time metrics';
$string['report:vacancies'] = 'Vacancies report';
$string['report:audit'] = 'Audit';
$string['exportcsv'] = 'Export CSV';
$string['exportexcel'] = 'Export Excel';
$string['exportpdf'] = 'Export PDF';
$string['daterange'] = 'Date range';
$string['datefrom'] = 'From';
$string['dateto'] = 'To';

// Dashboard.
$string['activevacancies'] = 'Active vacancies';
$string['applicationstoday'] = 'Applications today';
$string['pendingdocuments'] = 'Pending documents';
$string['totalapplicants'] = 'Total applicants';
$string['selectedthismonth'] = 'Selected this month';
$string['averagereviewtime'] = 'Average review time';
$string['recentactivity'] = 'Recent activity';
$string['alerts'] = 'Alerts';
$string['quickactions'] = 'Quick actions';

// Configuration.
$string['generalsettings'] = 'General settings';
$string['enableselfregistration'] = 'Enable plugin self-registration';
$string['enableselfregistration_desc'] = 'Allow users to register through the Job Board even when Moodle\'s global self-registration is disabled. Users will register using email confirmation.';
$string['documentsettings'] = 'Document settings';
$string['workflowsettings'] = 'Workflow settings';
$string['securitysettings'] = 'Security settings';
$string['multitenentsettings'] = 'Multi-tenant settings';
$string['institutionname'] = 'Institution name';
$string['institutionlogo'] = 'Institution logo';
$string['contactemail'] = 'Contact email';
$string['maxfilesize'] = 'Maximum file size (MB)';
$string['allowedformats'] = 'Allowed formats';
$string['epsmaxdays'] = 'Maximum days for EPS certificate';
$string['pensionmaxdays'] = 'Maximum days for pension certificate';
$string['antecedentesmaxdays'] = 'Maximum days for background checks';
$string['enableencryption'] = 'Enable file encryption';
$string['dataretentiondays'] = 'Data retention days';
$string['enableapi'] = 'Enable REST API';

// API.
$string['apitokens'] = 'API Tokens';
$string['createtoken'] = 'Create token';
$string['tokenname'] = 'Token name';
$string['tokenpermissions'] = 'Permissions';
$string['tokenipwhitelist'] = 'IP whitelist';
$string['tokenexpiry'] = 'Expiry date';
$string['tokencreated'] = 'Token created successfully';
$string['tokenrevoked'] = 'Token revoked';
$string['ratelimit'] = 'Rate limit';
$string['ratelimitexceeded'] = 'Rate limit exceeded';

// Audit.
$string['auditlog'] = 'Audit log';
$string['action'] = 'Action';
$string['entity'] = 'Entity';
$string['ipaddress'] = 'IP address';
$string['useragent'] = 'User agent';
$string['timestamp'] = 'Timestamp';
$string['details'] = 'Details';

// Table headers.
$string['thcode'] = 'Code';
$string['thtitle'] = 'Title';
$string['thstatus'] = 'Status';
$string['thapplicant'] = 'Applicant';
$string['thdate'] = 'Date';
$string['thactions'] = 'Actions';
$string['thdocument'] = 'Document';
$string['thvalidation'] = 'Validation';
$string['threviewer'] = 'Reviewer';

// Pagination.
$string['showing'] = 'Showing {$a->from} to {$a->to} of {$a->total} records';
$string['perpage'] = 'Per page';
$string['page'] = 'Page';
$string['first'] = 'First';
$string['last'] = 'Last';

// Confirmations.
$string['confirmdeletevacancy'] = 'Are you sure you want to delete this vacancy?';
$string['confirmwithdraw'] = 'Are you sure you want to withdraw your application?';
$string['confirmstatuschange'] = 'Confirm status change?';
$string['confirmpublish'] = 'Are you sure you want to publish this vacancy?';
$string['confirmunpublish'] = 'Are you sure you want to unpublish this vacancy? It will revert to draft status.';
$string['confirmclose'] = 'Are you sure you want to close this vacancy? No more applications will be accepted.';
$string['confirmreopen'] = 'Are you sure you want to reopen this vacancy?';

// Help strings.
$string['help:vacancy'] = 'Complete all required fields to create a vacancy';
$string['help:documents'] = 'Upload required documents in PDF, JPG or PNG format';
$string['help:review'] = 'Review each document against the verification checklist';
$string['help:iser'] = 'If you are ISER historic personnel, some documents will not be required';

// Events.
$string['event:vacancycreated'] = 'Vacancy created';
$string['event:vacancyupdated'] = 'Vacancy updated';
$string['event:vacancydeleted'] = 'Vacancy deleted';
$string['event:vacancypublished'] = 'Vacancy published';
$string['event:vacancyclosed'] = 'Vacancy closed';
$string['event:vacancyreopened'] = 'Vacancy reopened';
$string['event:applicationcreated'] = 'Application created';
$string['event:applicationupdated'] = 'Application updated';
$string['event:documentuploaded'] = 'Document uploaded';
$string['event:documentvalidated'] = 'Document validated';
$string['event:statuschanged'] = 'Status changed';

// Task names.
$string['task:sendnotifications'] = 'Send pending notifications';
$string['task:cleanupdata'] = 'Clean up old data';
$string['task:updatemetrics'] = 'Update dashboard metrics';

// Privacy.
$string['privacy:metadata:application'] = 'Information about user applications';
$string['privacy:metadata:document'] = 'Documents uploaded by the user';
$string['privacy:metadata:audit'] = 'User action log';

// Miscellaneous.
$string['noresults'] = 'No results found';
$string['loading'] = 'Loading...';
$string['processing'] = 'Processing...';
$string['allstatuses'] = 'All statuses';
$string['allcompanies'] = 'All companies';
$string['alldepartments'] = 'All departments';
$string['alldates'] = 'All dates';
$string['today'] = 'Today';
$string['thisweek'] = 'This week';
$string['thismonth'] = 'This month';
$string['days'] = 'days';
$string['hours'] = 'hours';
$string['minutes'] = 'minutes';
$string['yes'] = 'Yes';
$string['no'] = 'No';
$string['required'] = 'Required';
$string['optional'] = 'Optional';
$string['active'] = 'Active';
$string['inactive'] = 'Inactive';
$string['enabled'] = 'Enabled';
$string['disabled'] = 'Disabled';

// Phase 5: API Token Management.
$string['managetokens'] = 'Manage API Tokens';
$string['api:manageapitokens'] = 'Manage API Tokens';
$string['api:token:create'] = 'Create Token';
$string['api:token:created'] = 'Token created successfully. Make sure to copy the token now as it will not be shown again.';
$string['api:token:description'] = 'Token description';
$string['api:token:description_help'] = 'A descriptive name to identify this token and its purpose';
$string['api:token:validity'] = 'Validity Period';
$string['api:token:validfrom'] = 'Valid from';
$string['api:token:validfrom_help'] = 'The date from which the token becomes valid. Leave empty for immediate validity.';
$string['api:token:validuntil'] = 'Valid until';
$string['api:token:validuntil_help'] = 'The date until which the token remains valid. Leave empty for no expiration.';
$string['api:token:ipwhitelist'] = 'IP Whitelist';
$string['api:token:ipwhitelist_help'] = 'Enter one IP address or CIDR range per line. Leave empty to allow all IPs. Example: 192.168.1.0/24';
$string['api:token:revoke'] = 'Revoke Token';
$string['api:token:delete'] = 'Delete Token';
$string['api:token:enable'] = 'Enable Token';
$string['api:token:disable'] = 'Disable Token';
$string['api:token:confirmrevoke'] = 'Are you sure you want to revoke this API token? This action will disable the token immediately.';
$string['api:token:confirmdelete'] = 'Are you sure you want to permanently delete this API token? This action cannot be undone.';
$string['api:token:revoked'] = 'Token has been revoked';
$string['api:token:deleted'] = 'Token has been deleted';
$string['api:token:notfound'] = 'Token not found';
$string['api:token:lastused'] = 'Last used';
$string['api:token:never'] = 'Never';
$string['api:token:copytoclipboard'] = 'Copy to clipboard';
$string['api:token:copied'] = 'Token copied to clipboard';
$string['api:token:yourtoken'] = 'Your new API token';
$string['api:token:warning'] = 'Warning: This is the only time this token will be displayed. Make sure to copy it now.';
$string['api:token:notoken'] = 'No API tokens have been created yet.';

// Token statistics.
$string['totaltokens'] = 'Total Tokens';
$string['activetokens'] = 'Active Tokens';
$string['revokedtokens'] = 'Revoked Tokens';
$string['usedtoday'] = 'Used Today';
$string['tokenslist'] = 'API Tokens List';

// API Token Statuses.
$string['api:token:status:active'] = 'Active';
$string['api:token:status:disabled'] = 'Disabled';
$string['api:token:status:expired'] = 'Expired';
$string['api:token:status:not_yet_valid'] = 'Not Yet Valid';

// API Permissions.
$string['permissions'] = 'Permissions';
$string['api:permission:view_vacancies'] = 'View vacancy listings';
$string['api:permission:view_vacancy_details'] = 'View vacancy details';
$string['api:permission:create_application'] = 'Create applications';
$string['api:permission:view_applications'] = 'View applications';
$string['api:permission:view_application_details'] = 'View application details';
$string['api:permission:upload_documents'] = 'Upload documents';
$string['api:permission:view_documents'] = 'View documents';

// API Errors.
$string['api:error:unauthorized'] = 'Unauthorized: Invalid or missing API token';
$string['api:error:forbidden'] = 'Forbidden: You do not have permission to access this resource';
$string['api:error:notfound'] = 'Not found: The requested resource does not exist';
$string['api:error:ratelimit'] = 'Rate limit exceeded. Please try again later.';
$string['api:error:invalidrequest'] = 'Invalid request';
$string['api:error:ipnotallowed'] = 'Access denied: Your IP address is not in the whitelist';
$string['api:error:tokendisabled'] = 'Token is disabled';
$string['api:error:tokenexpired'] = 'Token has expired';
$string['api:error:tokennotyetvalid'] = 'Token is not yet valid';

// API Response headers.
$string['api:ratelimit:limit'] = 'Rate limit';
$string['api:ratelimit:remaining'] = 'Remaining requests';
$string['api:ratelimit:reset'] = 'Reset time';

// Encryption.
$string['encryption:enabled'] = 'File encryption is enabled';
$string['encryption:disabled'] = 'File encryption is disabled';
$string['encryption:keygenerated'] = 'Encryption key generated successfully';
$string['encryption:keyimported'] = 'Encryption key imported successfully';
$string['encryption:invalidkey'] = 'Invalid encryption key format';
$string['encryption:error'] = 'Encryption/decryption error occurred';
$string['encryption:nokey'] = 'Encryption key not configured';
$string['encryption:settings'] = 'Encryption Settings';
$string['encryption:generatekey'] = 'Generate New Key';
$string['encryption:importkey'] = 'Import Key';
$string['encryption:exportkey'] = 'Export Key';
$string['encryption:warning'] = 'Warning: Changing the encryption key will make previously encrypted files unreadable.';

// Security.
$string['security'] = 'Security';
$string['security:settings'] = 'Security Settings';
$string['security:apiconfig'] = 'API Configuration';
$string['security:ratelimiting'] = 'Rate Limiting';

// Data Export (GDPR/Habeas Data).
$string['dataexport'] = 'Export My Data';
$string['dataexport:personal'] = 'Personal Data Export';
$string['dataexport:title'] = 'Personal Data Export Report';
$string['dataexport:userinfo'] = 'User Information';
$string['dataexport:exportdate'] = 'Export date';
$string['dataexport:consent'] = 'Consent Records';
$string['dataexport:json'] = 'Export as JSON';
$string['dataexport:pdf'] = 'Export as PDF';
$string['dataexport:requested'] = 'Your data export has been requested';
$string['dataexport:ready'] = 'Your data export is ready for download';
$string['dataexport:description'] = 'Download a copy of your personal data stored in the Job Board system';

// Data Deletion.
$string['datadeletion'] = 'Delete My Data';
$string['datadeletion:request'] = 'Request Data Deletion';
$string['datadeletion:confirm'] = 'Are you sure you want to request deletion of your personal data? This action cannot be undone.';
$string['datadeletion:requested'] = 'Your data deletion request has been submitted';
$string['datadeletion:completed'] = 'Your personal data has been deleted';
$string['datadeletion:pending'] = 'Data deletion pending';

// Privacy provider extended.
$string['privacy:metadata:local_jobboard_application'] = 'Information about job applications submitted by the user';
$string['privacy:metadata:local_jobboard_application:userid'] = 'The ID of the user who submitted the application';
$string['privacy:metadata:local_jobboard_application:vacancyid'] = 'The ID of the vacancy applied for';
$string['privacy:metadata:local_jobboard_application:status'] = 'The current status of the application';
$string['privacy:metadata:local_jobboard_application:coverletter'] = 'Cover letter text submitted with the application';
$string['privacy:metadata:local_jobboard_application:digitalsignature'] = 'Digital signature provided by the applicant';
$string['privacy:metadata:local_jobboard_application:consentgiven'] = 'Whether consent was given for data processing';
$string['privacy:metadata:local_jobboard_application:consenttimestamp'] = 'When consent was given';
$string['privacy:metadata:local_jobboard_application:consentip'] = 'IP address from which consent was given';
$string['privacy:metadata:local_jobboard_application:timecreated'] = 'When the application was created';

$string['privacy:metadata:local_jobboard_document'] = 'Documents uploaded by users as part of their applications';
$string['privacy:metadata:local_jobboard_document:userid'] = 'The ID of the user who uploaded the document';
$string['privacy:metadata:local_jobboard_document:documenttype'] = 'The type of document uploaded';
$string['privacy:metadata:local_jobboard_document:filename'] = 'The name of the uploaded file';
$string['privacy:metadata:local_jobboard_document:timecreated'] = 'When the document was uploaded';

$string['privacy:metadata:local_jobboard_exemption'] = 'ISER exemption records for users';
$string['privacy:metadata:local_jobboard_exemption:userid'] = 'The ID of the user with the exemption';
$string['privacy:metadata:local_jobboard_exemption:exemptiontype'] = 'The type of exemption granted';
$string['privacy:metadata:local_jobboard_exemption:validfrom'] = 'When the exemption becomes valid';
$string['privacy:metadata:local_jobboard_exemption:validuntil'] = 'When the exemption expires';

$string['privacy:metadata:local_jobboard_audit'] = 'Audit log of user actions';
$string['privacy:metadata:local_jobboard_audit:userid'] = 'The ID of the user who performed the action';
$string['privacy:metadata:local_jobboard_audit:action'] = 'The action performed';
$string['privacy:metadata:local_jobboard_audit:ipaddress'] = 'The IP address from which the action was performed';
$string['privacy:metadata:local_jobboard_audit:timecreated'] = 'When the action was performed';

$string['privacy:metadata:local_jobboard_api_token'] = 'API tokens created by users';
$string['privacy:metadata:local_jobboard_api_token:userid'] = 'The ID of the user who owns the token';
$string['privacy:metadata:local_jobboard_api_token:description'] = 'Description of the token purpose';
$string['privacy:metadata:local_jobboard_api_token:timecreated'] = 'When the token was created';

$string['privacy:metadata:local_jobboard_notification'] = 'Notification records for users';
$string['privacy:metadata:local_jobboard_notification:userid'] = 'The ID of the user who received the notification';
$string['privacy:metadata:local_jobboard_notification:templatecode'] = 'The notification template used';
$string['privacy:metadata:local_jobboard_notification:timecreated'] = 'When the notification was created';

$string['privacy:metadata:interviewer'] = 'Information about users assigned as interview panel members';
$string['privacy:metadata:interviewer:userid'] = 'The ID of the user assigned as interviewer';
$string['privacy:metadata:interviewer:interviewid'] = 'The interview to which they are assigned';
$string['privacy:metadata:interviewer:timecreated'] = 'When the assignment was made';

$string['privacy:metadata:committeemember'] = 'Information about users assigned to selection committees';
$string['privacy:metadata:committeemember:userid'] = 'The ID of the user assigned to the committee';
$string['privacy:metadata:committeemember:committeeid'] = 'The committee to which they are assigned';
$string['privacy:metadata:committeemember:role'] = 'The role of the user in the committee';
$string['privacy:metadata:committeemember:addedby'] = 'The user who made the assignment';
$string['privacy:metadata:committeemember:timecreated'] = 'When the assignment was made';

$string['privacy:metadata:evaluation'] = 'Evaluation scores and votes submitted by committee members';
$string['privacy:metadata:evaluation:userid'] = 'The ID of the user who submitted the evaluation';
$string['privacy:metadata:evaluation:applicationid'] = 'The application being evaluated';
$string['privacy:metadata:evaluation:score'] = 'The numeric score given';
$string['privacy:metadata:evaluation:vote'] = 'The vote decision (approve/reject)';
$string['privacy:metadata:evaluation:comments'] = 'Comments provided with the evaluation';
$string['privacy:metadata:evaluation:timecreated'] = 'When the evaluation was submitted';

// Additional validation errors.
$string['error:usernotfound'] = 'User not found';
$string['error:nopermission'] = 'At least one permission must be selected';
$string['error:invalidip'] = 'Invalid IP address or CIDR notation: {$a}';
$string['error:invalidtoken'] = 'Invalid or expired token';
$string['error:tokenlimit'] = 'Maximum number of tokens reached';

// Cleanup task.
$string['task:cleanupolddata'] = 'Clean up old job board data';
$string['cleanup:applicationsdeleted'] = '{$a} old applications deleted';
$string['cleanup:tokensdeleted'] = '{$a} expired tokens deleted';
$string['cleanup:auditlogsdeleted'] = '{$a} old audit logs deleted';
$string['cleanup:notificationsdeleted'] = '{$a} old notifications deleted';

// Data retention.
$string['dataretention'] = 'Data Retention';
$string['dataretention:days'] = 'Retention period (days)';
$string['dataretention:days_help'] = 'Number of days to retain rejected or withdrawn applications before automatic deletion. Set to 0 to disable automatic deletion.';
$string['dataretention:policy'] = 'Data retention policy';
$string['dataretention:auditdays'] = 'Audit log retention (days)';
$string['dataretention:notificationdays'] = 'Notification retention (days)';

// Validity dates.
$string['validfrom'] = 'Valid from';
$string['validuntil'] = 'Valid until';
$string['validfrom_help'] = 'The date from which this item becomes valid';
$string['validuntil_help'] = 'The date until which this item remains valid';

// User/application documents.
$string['applications'] = 'Applications';
$string['documents'] = 'Documents';
$string['nodocumentsrequired'] = 'No documents required for this vacancy';
$string['alldocumentssubmitted'] = 'All required documents have been submitted';
$string['documentsmissing'] = 'Some required documents are missing';

// Application form.
$string['applyfor'] = 'Apply for: {$a}';
$string['coverletter'] = 'Cover Letter';
$string['coverletter_help'] = 'Write a personalized cover letter explaining: why you are interested in this position, how your experience matches the requirements, what you can contribute to the organization. Keep it concise (300-500 words) and professional. This is optional but highly recommended as it helps reviewers understand your motivation.';
$string['submitapplication'] = 'Submit Application';
$string['applicationpreview'] = 'Application Preview';

// Token table headers.
$string['th:token'] = 'Token';
$string['th:description'] = 'Description';
$string['th:user'] = 'User';
$string['th:permissions'] = 'Permissions';
$string['th:status'] = 'Status';
$string['th:lastused'] = 'Last Used';
$string['th:created'] = 'Created';
$string['th:actions'] = 'Actions';

// ==========================================================================
// Phase 7: Public Page and Application Limits.
// ==========================================================================

// Publication types.
$string['publicationtype'] = 'Publication type';
$string['publicationtype_help'] = 'Choose how this vacancy will be published. Public: visible to everyone including non-registered users, ideal for open positions. Internal: only visible to authenticated users within the organization, used for promotions, internal mobility, or positions reserved for current staff.';
$string['publicationtype:public'] = 'Public';
$string['publicationtype:internal'] = 'Internal';

// IOMAD multi-tenant strings.
$string['iomadsettings'] = 'Company & Department';
$string['iomad_department'] = 'IOMAD Department';
$string['iomad_department_help'] = 'Select the department within the company for this vacancy. Departments are managed in IOMAD.';

// Public page.
$string['publicvacancies'] = 'Job Opportunities';
$string['publicpagetitle'] = 'Job Opportunities';
$string['publicpagetitle_default'] = 'Job Opportunities';
$string['vacanciesfound'] = '{$a} vacancies found';
$string['novacanciesfound'] = 'No vacancies found matching your criteria.';
$string['searchplaceholder'] = 'Search by title, code, or description...';
$string['viewdetails'] = 'View Details';
$string['loginandapply'] = 'Login to Apply';
$string['opensnewwindow'] = '(opens in new window)';
$string['closesin'] = 'Closes in {$a} days';
$string['closeson'] = 'Closes on';
$string['wanttoapply'] = 'Want to apply?';
$string['createaccounttoapply'] = 'Create an account or log in to apply for vacancies.';
$string['loginprompt_public'] = 'Sign in or create an account to apply for vacancies.';
$string['loginrequired_apply'] = 'You need to be logged in to apply for this position.';
$string['backtovacancies'] = 'Back to Vacancies';
$string['requireddocuments'] = 'Required Documents';
$string['importantdates'] = 'Important Dates';
$string['sharethisvacancy'] = 'Share this Vacancy';
$string['copylink'] = 'Copy link';
$string['applynow'] = 'Apply Now';
$string['alreadyapplied'] = 'You have already applied for this vacancy.';
$string['applicationstatus'] = 'Application status';
$string['viewmyapplications'] = 'View My Applications';
$string['loginrequiredtoapply'] = 'You must be logged in to apply for this vacancy.';
$string['noapplypermission'] = 'You do not have permission to apply for vacancies.';
$string['youhaveapplied'] = 'You have applied';
$string['viewmyapplication'] = 'View my application';
$string['viewallapplications'] = 'View all my applications';
$string['applyforposition'] = 'Apply for this position';
$string['applynow_desc'] = 'Ready to apply? Submit your application now and take the next step in your career.';
$string['noapplycapability'] = 'You do not have permission to apply for vacancies.';
$string['quicklinks'] = 'Quick links';
$string['othervacancies'] = 'Other vacancies in this convocatoria';
$string['allconvocatorias'] = 'All convocatorias';
$string['share'] = 'Share';
$string['searchvacancies'] = 'Search vacancies...';
$string['all'] = 'All';

// Public page settings.
$string['publicpagesettings'] = 'Public Page Settings';
$string['publicpagesettings_desc'] = 'Configure the public vacancies page accessible without authentication.';
$string['enablepublicpage'] = 'Enable public page';
$string['enablepublicpage_desc'] = 'Allow public access to view public vacancies without requiring authentication.';
$string['publicpagedescription'] = 'Page description';
$string['publicpagedescription_desc'] = 'Introduction text shown at the top of the public vacancies page.';
$string['publicpagetitle_desc'] = 'Custom title for the public vacancies page. Leave empty to use the default.';
$string['showpublicnavlink'] = 'Show in navigation';
$string['showpublicnavlink_desc'] = 'Show a link to the public vacancies page in the main navigation for non-authenticated users.';

// Application limits settings.
$string['applicationlimits'] = 'Application Limits';
$string['applicationlimits_desc'] = 'Configure how many applications users can submit.';
$string['applicationlimits_perconvocatoria_desc'] = 'Application limits (allow multiple applications, maximum per user) are now configured per convocatoria. Edit each convocatoria to set these restrictions.';
$string['allowmultipleapplications'] = 'Allow multiple applications';
$string['allowmultipleapplications_desc'] = 'Allow users to apply for multiple vacancies simultaneously.';
$string['maxactiveapplications'] = 'Maximum active applications';
$string['maxactiveapplications_desc'] = 'Maximum number of active applications per user (0 = unlimited). Only applies when multiple applications are allowed.';

// Application limit errors.
$string['error:multipleapplicationsnotallowed'] = 'You can only have one active application at a time. Please withdraw your current application before applying for a new vacancy.';
$string['error:applicationlimitreached'] = 'You have reached the maximum number of active applications ({$a}). Please wait for your current applications to be processed or withdraw one before applying for a new vacancy.';
$string['error:publicpagedisabled'] = 'The public vacancies page is disabled.';
$string['error:loginrequiredforinternal'] = 'You must be logged in to view internal vacancies.';

// ==========================================================================
// Additional form strings - Exemption Form.
// ==========================================================================

$string['exemptiondetails'] = 'Exemption Details';
$string['exemptiontype'] = 'Exemption Type';
$string['exemptiontype_help'] = 'Select the type of exemption applicable to this user';
$string['exemptiontype_historico_iser'] = 'ISER Historic Personnel';
$string['exemptiontype_documentos_recientes'] = 'Recent Documents on File';
$string['exemptiontype_traslado_interno'] = 'Internal Transfer';
$string['exemptiontype_recontratacion'] = 'Rehiring';
$string['documentref'] = 'Document Reference';
$string['documentref_help'] = 'Reference number or identifier of the supporting document';
$string['validityperiod'] = 'Validity Period';
$string['selectall'] = 'Select All';
$string['selectidentitydocs'] = 'Select Identity Documents';
$string['selectbackgrounddocs'] = 'Select Background Checks';
$string['selectatleastone'] = 'Please select at least one option';
$string['usernotfound'] = 'User not found';
$string['additionalinfo'] = 'Additional Information';

// Document types for exemption form.
$string['doctype_cedula'] = 'National ID (Cédula)';
$string['doctype_rut'] = 'Tax ID (RUT)';
$string['doctype_eps'] = 'Health Insurance (EPS)';
$string['doctype_pension'] = 'Pension Certificate';
$string['doctype_cuenta_bancaria'] = 'Bank Account Certificate';
$string['doctype_libreta_militar'] = 'Military Service Card';
$string['doctype_titulo_pregrado'] = 'Undergraduate Degree';
$string['doctype_titulo_postgrado'] = 'Graduate Degree';
$string['doctype_tarjeta_profesional'] = 'Professional License';
$string['doctype_sigep'] = 'SIGEP II Registration';
$string['doctype_antecedentes_procuraduria'] = 'Disciplinary Background (Attorney General)';
$string['doctype_antecedentes_contraloria'] = 'Fiscal Background (Comptroller)';
$string['doctype_antecedentes_policia'] = 'Criminal Background (National Police)';
$string['doctype_rnmc'] = 'National Registry of Corrective Measures';
$string['doctype_certificado_medico'] = 'Medical Certificate';

// Document categories.
$string['doccategory_identification'] = 'Identification Documents';
$string['doccategory_academic'] = 'Academic Documents';
$string['doccategory_employment'] = 'Employment Documents';
$string['doccategory_financial'] = 'Financial Documents';
$string['doccategory_health'] = 'Health and Social Security';
$string['doccategory_legal'] = 'Legal Background Checks';
$string['doccategory_other'] = 'Other Documents';

// Document conditional messages.
$string['conditions'] = 'Conditions';
$string['doc_condition_men_only'] = 'Required only for men';
$string['doc_condition_women_only'] = 'Required only for women';
$string['doc_condition_profession_exempt'] = 'Not required for: {$a}';
$string['doc_condition_iser_exempt'] = 'Not required for previous ISER employees';

// ==========================================================================
// Additional form strings - Application Form.
// ==========================================================================

$string['vacancyinfo'] = 'Vacancy Information';
$string['code'] = 'Code';
$string['title'] = 'Title';
$string['exemptionnotice'] = 'Exemption Notice';
$string['exemptionapplied'] = 'An exemption applies to your application';
$string['exemptionreduceddocs'] = 'Some documents are not required due to your exemption status.';
$string['consentheader'] = 'Consent and Authorization';
$string['datatreatmentpolicytitle'] = 'Personal Data Treatment Policy';
$string['defaultdatatreatmentpolicy'] = 'By submitting this application, you authorize the processing of your personal data in accordance with applicable data protection laws. Your data will be used exclusively for the selection process and will be handled with strict confidentiality.';
$string['consentaccepttext'] = 'I have read and accept the personal data treatment policy';
$string['consentrequired'] = 'You must accept the consent to proceed';
$string['documentshelp'] = 'Please upload all required documents in PDF, JPG, or PNG format. Maximum file size varies by document type.';
$string['documentrequired'] = 'The document "{$a}" is required';
$string['documentissuedate'] = 'Document Issue Date';
$string['documentexpired'] = 'Document has expired. Maximum age allowed: {$a}';
$string['declaration'] = 'Declaration';
$string['declarationtext'] = 'I solemnly declare that all information provided in this application is true, complete, and accurate to the best of my knowledge. I understand that any false statements or misrepresentations may result in disqualification from the selection process or termination of employment if discovered after hiring.';
$string['declarationaccept'] = 'I accept and confirm this declaration';
$string['declarationrequired'] = 'You must accept the declaration to proceed';
$string['signaturetoooshort'] = 'Digital signature is too short. Please enter your full name.';
$string['digitalsignature_help'] = 'Type your full legal name exactly as it appears on your identification documents. This serves as your digital signature and legally confirms that: all information provided is accurate and truthful, you authorize the verification of your documents, you accept the terms and conditions of the application process. Using a false name may result in disqualification.';

// Document upload help strings.
$string['documenttype_help'] = 'Select the type of document you are uploading. Each vacancy requires specific documents. Common types include: academic degrees and diplomas, professional certifications, identification documents, reference letters. Make sure to select the correct type to avoid delays in the review process.';
$string['documentfile_help'] = 'Upload your document in PDF format. Requirements: maximum file size 10MB, supported formats PDF only, the document must be legible and complete. For multi-page documents, combine all pages into a single PDF. Scanned documents should have a minimum resolution of 150 DPI for readability.';
$string['documentissuedate_help'] = 'Enter the date when this document was issued or certified. For academic degrees, use the graduation date. For certifications, use the certification date. Some documents have validity periods and may be rejected if expired.';

// Document review help strings.
$string['validationstatus_help'] = 'Current status of document validation: Pending (not yet reviewed), Approved (document meets all requirements), Rejected (document has issues - see rejection reason), Needs Clarification (additional information required from applicant).';
$string['rejectionreason_help'] = 'If rejecting the document, select the reason from the list or provide a custom explanation. Common reasons include: document is illegible, document is incomplete, document is expired, wrong document type uploaded, name does not match application. The applicant will be notified and can upload a corrected version.';
$string['reviewcomments_help'] = 'Add any internal comments about this document review. These comments are visible only to reviewers and administrators, not to the applicant. Use this field to note any concerns, verifications performed, or recommendations.';

// ==========================================================================
// Additional strings for applications.php.
// ==========================================================================

// Application status strings (used with status_ prefix).
$string['status_submitted'] = 'Submitted';
$string['status_under_review'] = 'Under Review';
$string['status_docs_validated'] = 'Documents Validated';
$string['status_docs_rejected'] = 'Documents Rejected';
$string['status_interview'] = 'Interview Scheduled';
$string['status_selected'] = 'Selected';
$string['status_rejected'] = 'Not Selected';
$string['status_withdrawn'] = 'Withdrawn';

// Applications page strings.
$string['filterbystatus'] = 'Filter by status';
$string['noapplicationsfound'] = 'No applications found';
$string['browsevacancies'] = 'Browse Vacancies';
$string['vacancy'] = 'Vacancy';
$string['dateapplied'] = 'Date Applied';
$string['unknownvacancy'] = 'Unknown Vacancy';

// ==========================================================================
// Reports page strings.
// ==========================================================================

$string['reportoverview'] = 'Overview';
$string['reportapplications'] = 'Applications';
$string['reportdocuments'] = 'Documents';
$string['reportreviewers'] = 'Reviewers';
$string['reporttimeline'] = 'Timeline';
$string['allvacancies'] = 'All Vacancies';
$string['totalapplications'] = 'Total Applications';
$string['selected'] = 'Selected';
$string['rejected'] = 'Rejected';
$string['selectionrate'] = 'Selection Rate';
$string['applicationsbystatus'] = 'Applications by Status';
$string['count'] = 'Count';
$string['documentsbytype'] = 'Documents by Type';
$string['validationrate'] = 'Validation Rate';
$string['avgprocessingtime'] = 'Average Processing Time';
$string['reviewerperformance'] = 'Reviewer Performance';
$string['reviewerworkload'] = 'Reviewer Workload';
$string['applicationtrends'] = 'Application Trends';
$string['dailyapplications'] = 'Daily Applications';
$string['noreportdata'] = 'No data available for this report';

// ==========================================================================
// Document review page strings.
// ==========================================================================

$string['reviewdocuments'] = 'Review Documents';
$string['applicationdetails'] = 'Application Details';
$string['applicantinfo'] = 'Applicant Information';
$string['documentlist'] = 'Document List';
$string['documentpreview'] = 'Document Preview';
$string['previewdocument'] = 'Preview Document';
$string['togglepreview'] = 'Toggle Preview';
$string['validateall'] = 'Validate All';
$string['rejectall'] = 'Reject All';
$string['validatedocument'] = 'Validate Document';
$string['rejectdocument'] = 'Reject Document';
$string['addnote'] = 'Add Note';
$string['reviewnotes'] = 'Review Notes';
$string['markasreviewed'] = 'Mark as Reviewed';
$string['nextapplication'] = 'Next Application';
$string['previousapplication'] = 'Previous Application';
$string['applicationof'] = 'Application {$a->current} of {$a->total}';
$string['nodocumentstoreview'] = 'No documents to review';
$string['alldocumentsreviewed'] = 'All documents have been reviewed';
$string['reviewedby'] = 'Reviewed by';
$string['reviewedon'] = 'Reviewed on';
$string['documentstatus'] = 'Document Status';
$string['reviewhistory'] = 'Review History';
$string['submitreview'] = 'Submit Review';
$string['reviewsubmitted'] = 'Review submitted successfully';
$string['error:invalidapplication'] = 'Invalid application';
$string['error:invaliddocument'] = 'Invalid document';
$string['error:reviewfailed'] = 'Failed to submit review';
$string['applicant'] = 'Applicant';
$string['name'] = 'Name';

// ==========================================================================
// Phase 2-4: Additional strings from Spanish localization.
// ==========================================================================

// Application system.
$string['applytovacancy'] = 'Apply for vacancy';
$string['application'] = 'Application';
$string['currentstatus'] = 'Current status';
$string['consentgiven'] = 'Consent given';
$string['uploadeddocuments'] = 'Uploaded documents';
$string['statushistory'] = 'Status history';
$string['workflowactions'] = 'Workflow actions';
$string['changestatus'] = 'Change status';
$string['updatestatus'] = 'Update status';
$string['notes'] = 'Notes';
$string['backtoapplications'] = 'Back to applications';
$string['cannotwithdraw'] = 'Cannot withdraw this application in its current status';
$string['statuschanged'] = 'Status updated successfully';
$string['invalidtransition'] = 'Invalid status transition';
$string['viewvacancy'] = 'View vacancy';
$string['noaccess'] = 'You do not have access to this resource';
$string['vacancynotopen'] = 'Vacancy is not open for applications';
$string['applicationcreatefailed'] = 'Failed to create application';
$string['applicationerror'] = 'Application error';
$string['deadlinewarning'] = 'Attention: Vacancy closes in {$a} day(s)';
$string['applicationguidelines'] = 'Application guidelines';
$string['guideline1'] = 'Complete all required fields marked with asterisk (*)';
$string['guideline2'] = 'Upload legible and complete documents';
$string['guideline3'] = 'Verify that the information provided is correct before submitting';
$string['guideline4'] = 'Once submitted, you will receive a confirmation email';
$string['documentsuploaded'] = 'documents uploaded';
$string['exemptionactive'] = 'ISER exemption active';
$string['exemption'] = 'Exemption';

// Document validation.
$string['documentinfo'] = 'Document information';
$string['documenttype'] = 'Document type';
$string['filename'] = 'File name';
$string['uploaded'] = 'Uploaded';
$string['viewdocument'] = 'View document';
$string['validationchecklist'] = 'Validation checklist';
$string['validationdecision'] = 'Validation decision';
$string['selectreason'] = 'Select a reason';
$string['additionalnotes'] = 'Additional notes';
$string['rejectreason_illegible'] = 'Illegible document';
$string['rejectreason_expired'] = 'Expired document';
$string['rejectreason_incomplete'] = 'Incomplete document';
$string['rejectreason_wrongtype'] = 'Wrong document type';
$string['rejectreason_mismatch'] = 'Information mismatch';
$string['validated'] = 'Validated';
$string['pendingvalidation'] = 'Pending Validation';
$string['documenttypes'] = 'Document Types';
$string['pending'] = 'Pending';

// Validation checklist items.
$string['checklist_legible'] = 'Document is legible';
$string['checklist_complete'] = 'Document is complete';
$string['checklist_namematch'] = 'Name matches applicant';
$string['checklist_cedula_number'] = 'ID number is visible and legible';
$string['checklist_cedula_photo'] = 'Photo is clear';
$string['checklist_background_date'] = 'Issue date is recent (max. 3 months)';
$string['checklist_background_status'] = 'No records found';
$string['checklist_title_institution'] = 'Educational institution is recognized';
$string['checklist_title_date'] = 'Graduation date is visible';
$string['checklist_title_program'] = 'Academic program is clear';
$string['checklist_acta_number'] = 'Certificate number is visible';
$string['checklist_acta_date'] = 'Certificate date is visible';
$string['checklist_tarjeta_number'] = 'License number is visible';
$string['checklist_tarjeta_profession'] = 'Profession is specified';
$string['checklist_rut_nit'] = 'Tax ID is visible';
$string['checklist_rut_updated'] = 'Tax document is updated';
$string['checklist_eps_active'] = 'Affiliation is active';
$string['checklist_eps_entity'] = 'Health insurance entity is clear';
$string['checklist_pension_fund'] = 'Pension fund is clear';
$string['checklist_pension_active'] = 'Affiliation is active';
$string['checklist_medical_date'] = 'Certificate date is recent';
$string['checklist_medical_aptitude'] = 'Fitness assessment is favorable';
$string['checklist_military_class'] = 'Military card class is visible';
$string['checklist_military_number'] = 'Military card number is visible';

// Additional document types.
$string['doctype_titulo_especializacion'] = 'Specialization Degree';
$string['doctype_titulo_maestria'] = 'Master\'s Degree';
$string['doctype_titulo_doctorado'] = 'Doctoral Degree';
$string['doctype_acta_grado'] = 'Graduation Certificate';
$string['doctype_sijin'] = 'SIJIN Certificate';

// Manage applications.
$string['manageapplications'] = 'Manage applications';
$string['backtomanage'] = 'Back to management';
$string['searchapplicant'] = 'Search applicant...';

// Reviewer management.
$string['myreviews'] = 'My reviews';
$string['assignreviewers'] = 'Assign reviewers';
$string['assignreviewer'] = 'Assign reviewer';
$string['reviewer'] = 'Reviewer';
$string['reviewers'] = 'Reviewers';
$string['selectreviewer'] = 'Select reviewer';
$string['workload'] = 'Workload';
$string['currentworkload'] = 'Current workload';
$string['maxworkload'] = 'Maximum workload';
$string['availablereviewers'] = 'Available Reviewers';
$string['unassignedapplications'] = 'Unassigned Applications';
$string['totalassigned'] = 'Total Assigned';
$string['avgworkload'] = 'Avg. Workload';
$string['pendingassignment'] = 'pending assignment';
$string['noassignments'] = 'No pending assignments';
$string['pendingassignments'] = 'Pending assignments';
$string['assignedto'] = 'Assigned to';
$string['assignedby'] = 'Assigned by';
$string['assignmentdate'] = 'Assignment date';
$string['unassigned'] = 'Unassigned';
$string['reassign'] = 'Reassign';
$string['autoassign'] = 'Auto-assign';
$string['autoassigndesc'] = 'Automatically assign applications to available reviewers with load balancing';
$string['assignmentscompleted'] = '{$a} assignments completed';
$string['assignmenterror'] = 'Error assigning reviewer';
$string['reviewerassigned'] = 'Reviewer assigned successfully';
$string['reviewersassigned'] = '{$a} reviewers assigned successfully';
$string['selectapplications'] = 'Select applications to assign';
$string['noapplicationsselected'] = 'No applications selected';
$string['allapplicationsassigned'] = 'All applications already have an assigned reviewer';
$string['myassignments'] = 'My assignments';

// Bulk validation.
$string['bulkvalidation'] = 'Bulk validation';
$string['bulkvalidate'] = 'Bulk validate';
$string['selectdocuments'] = 'Select documents';
$string['selectnone'] = 'Deselect all';
$string['approveselected'] = 'Approve selected';
$string['rejectselected'] = 'Reject selected';
$string['documentssummary'] = 'Documents summary';
$string['documentsvalidated'] = 'Documents validated';
$string['documentsrejected'] = 'Documents rejected';
$string['documentspending'] = 'Documents pending';
$string['validationcomplete'] = 'Validation complete';
$string['validationresults'] = 'Validation results';
$string['validationsuccess'] = '{$a} documents processed successfully';
$string['validationfailed'] = '{$a} documents failed';
$string['bydocumenttype'] = 'By document type';
$string['byapplication'] = 'By application';
$string['autovalidate'] = 'Auto-validate';
$string['autovalidatedesc'] = 'Automatically validate documents that meet predefined rules';
$string['autovalidationrules'] = 'Auto-validation rules';
$string['documentsautovalidated'] = '{$a} documents auto-validated';
$string['noautovalidationdocs'] = 'No documents meet auto-validation rules';

// Document re-upload.
$string['reupload'] = 'Re-upload';
$string['reuploaddocument'] = 'Re-upload document';
$string['reuploaddesc'] = 'Your document was rejected. Please upload a corrected version.';
$string['previousversion'] = 'Previous version';
$string['newversion'] = 'New version';
$string['superseded'] = 'Superseded';
$string['documenthistory'] = 'Document history';
$string['reuploadsuccess'] = 'Document re-uploaded successfully';
$string['waitingforreupload'] = 'Waiting for new version';

// Dashboard.
$string['applicationpipeline'] = 'Application pipeline';
$string['validationstats'] = 'Validation statistics';
$string['recentapplications'] = 'Recent applications';
$string['norecentapplications'] = 'No recent applications';
$string['viewreports'] = 'View reports';
$string['createvacancy'] = 'Create vacancy';

// Reports.
$string['generatereport'] = 'Generate report';
$string['fromdate'] = 'From date';
$string['todate'] = 'To date';
$string['allreviewers'] = 'All reviewers';
$string['totaldocuments'] = 'Total documents';
$string['totalreviewed'] = 'Total reviewed';
$string['rejectionrate'] = 'Rejection rate';
$string['validationbytype'] = 'Validation by type';
$string['commonrejections'] = 'Common rejections';
$string['toprejectionreasons'] = 'Top rejection reasons';
$string['timetovalidation'] = 'Time to validation';
$string['timetoselection'] = 'Time to selection';

// My reviews page.
$string['sortby'] = 'Sort by';
$string['datesubmitted'] = 'Date submitted';
$string['closingdate'] = 'Closing date';
$string['progress'] = 'Progress';
$string['closingsoon'] = 'Closes in {$a} day(s)';

// Rejection reasons.
$string['rejectreason_ilegible'] = 'Illegible document';
$string['rejectreason_vencido'] = 'Expired document';
$string['rejectreason_incompleto'] = 'Incomplete document';
$string['rejectreason_formato_incorrecto'] = 'Incorrect format';
$string['rejectreason_datos_erroneos'] = 'Erroneous data';
$string['rejectreason_no_coincide'] = 'Does not match applicant information';
$string['rejectreason_sin_firma'] = 'Missing signature or seal';
$string['rejectreason_otro'] = 'Other reason';

// Export.
$string['exportformat'] = 'Export format';
$string['downloading'] = 'Downloading...';

// Validation workflow.
$string['validationworkflow'] = 'Validation workflow';
$string['nextdocument'] = 'Next document';
$string['previousdocument'] = 'Previous document';
$string['validateandnext'] = 'Validate and next';
$string['rejectandnext'] = 'Reject and next';
$string['skipfornow'] = 'Skip for now';
$string['allvalidated'] = 'All documents validated';
$string['somerejected'] = 'Some documents rejected';
$string['completereview'] = 'Complete review';
$string['reviewcomplete'] = 'Review complete';

// Filters.
$string['filterbytype'] = 'Filter by type';
$string['filterbyvacancy'] = 'Filter by vacancy';
$string['filterbyreviewer'] = 'Filter by reviewer';
$string['filterbydate'] = 'Filter by date';
$string['applyfilters'] = 'Apply filters';

// Statistics.
$string['statistics'] = 'Statistics';
$string['overallstats'] = 'Overall statistics';
$string['mystats'] = 'My statistics';
$string['todaystats'] = 'Today\'s statistics';
$string['weekstats'] = 'This week\'s statistics';
$string['monthstats'] = 'This month\'s statistics';
$string['validationstoday'] = 'Validations today';
$string['averagetime'] = 'Average time';
$string['fastesttime'] = 'Fastest time';
$string['slowesttime'] = 'Slowest time';

// Alerts and warnings.
$string['closingvacanciesalert'] = '{$a} vacancy(ies) closing in the next 3 days';
$string['pendingvalidationsalert'] = 'You have {$a} documents pending validation';
$string['overdueassignmentsalert'] = 'You have {$a} overdue assignments';
$string['urgentattentionneeded'] = 'Urgent attention needed';
$string['nourgentitems'] = 'No urgent items';

// ISER Exemptions extended.
$string['totalexemptions'] = 'Total Exemptions';
$string['activeexemptions'] = 'Active Exemptions';
$string['expiredexemptions'] = 'Expired Exemptions';
$string['revokedexemptions'] = 'Revoked Exemptions';
$string['exemptionlist'] = 'Exemption List';
$string['revoked'] = 'Revoked';
$string['expired'] = 'Expired';
$string['revoke'] = 'Revoke';
$string['revokedby'] = 'Revoked by';
$string['revokeexemption'] = 'Revoke exemption';
$string['confirmrevokeexemption'] = 'Are you sure you want to revoke the exemption for {$a}?';
$string['exemptionupdated'] = 'Exemption updated successfully';
$string['exemptioncreated'] = 'Exemption created successfully';
$string['exemptionrevoked'] = 'Exemption revoked successfully';
$string['exemptionerror'] = 'Error processing exemption';
$string['exemptionrevokeerror'] = 'Error revoking exemption';
$string['noexemptions'] = 'No exemptions registered';
$string['exemptionusagehistory'] = 'Exemption usage history';
$string['noexemptionusage'] = 'This exemption has not been used in any application';
$string['doctypes'] = 'document types';
$string['searchuser'] = 'Search user...';

// Interview scheduling.
$string['scheduleinterview'] = 'Schedule interview';
$string['schedulenewinterview'] = 'Schedule new interview';
$string['interviewdetails'] = 'Interview details';
$string['interviewdate'] = 'Interview date';
$string['interviewtime'] = 'Interview time';
$string['interviewlocation'] = 'Interview location';
$string['interviewtype'] = 'Interview type';
$string['interviewtype_presencial'] = 'In-person';
$string['interviewtype_virtual'] = 'Virtual';
$string['interviewtype_telefonica'] = 'Phone';
$string['interviewlink'] = 'Meeting link';
$string['interviewers'] = 'Interviewers';
$string['selectinterviewers'] = 'Select interviewers';
$string['interviewscheduled'] = 'Interview scheduled successfully';
$string['interviewupdated'] = 'Interview updated successfully';
$string['interviewcancelled'] = 'Interview cancelled';
$string['confirmcancelinterview'] = 'Are you sure you want to cancel this interview?';
$string['rescheduleinterview'] = 'Reschedule interview';
$string['rescheduledby'] = 'Rescheduled by';
$string['reschedulednote'] = 'Reschedule note';
$string['cancelledby'] = 'Cancelled by';
$string['cancelnote'] = 'Cancellation note';
$string['scheduledinterviews'] = 'Scheduled interviews';
$string['pastinterviews'] = 'Past interviews';
$string['upcominginterviews'] = 'Upcoming interviews';
$string['nointerviewsscheduled'] = 'No interviews scheduled';
$string['recordresults'] = 'Record results';
$string['interviewresults'] = 'Interview results';
$string['attended'] = 'Attended';
$string['noshow'] = 'No show';
$string['interviewresult'] = 'Interview result';
$string['result_favorable'] = 'Favorable';
$string['result_no_favorable'] = 'Not favorable';
$string['result_pendiente'] = 'Pending evaluation';
$string['interviewscore'] = 'Interview score';
$string['interviewobservations'] = 'Interview observations';
$string['resultrecorded'] = 'Result recorded successfully';
$string['completeinterview'] = 'Complete interview';
$string['confirmnoshow'] = 'Are you sure you want to mark this applicant as no show?';
$string['markednoshow'] = 'Marked as no show by {$a->user} on {$a->time}. Notes: {$a->notes}';

// Selection committee.
$string['selectioncommittee'] = 'Selection committee';
$string['createcommittee'] = 'Create committee';
$string['editcommittee'] = 'Edit committee';
$string['committeedetails'] = 'Committee details';
$string['committeename'] = 'Committee name';
$string['committeemembers'] = 'Committee members';
$string['addmember'] = 'Add member';
$string['removemember'] = 'Remove member';
$string['confirmremovemember'] = 'Are you sure you want to remove this member?';
$string['role'] = 'Role';
$string['role_chair'] = 'Chair';
$string['role_secretary'] = 'Secretary';
$string['role_evaluator'] = 'Evaluator';
$string['role_observer'] = 'Observer';
$string['rolechanged'] = 'Role changed successfully';
$string['committeecreated'] = 'Committee created successfully';
$string['committeeupdated'] = 'Committee updated successfully';
$string['committeedeleted'] = 'Committee deleted';
$string['nocommittees'] = 'No committees created';
$string['committeechair'] = 'Committee chair';
$string['quorum'] = 'Quorum';
$string['quorummet'] = 'Quorum met';
$string['quorumnotmet'] = 'Quorum not met';

// Evaluation.
$string['evaluateapplicant'] = 'Evaluate applicant';
$string['evaluationcriteria'] = 'Evaluation criteria';
$string['criterion'] = 'Criterion';
$string['weight'] = 'Weight';
$string['score'] = 'Score';
$string['avgscore'] = 'Average score';
$string['totalscore'] = 'Total score';
$string['evaluationnotes'] = 'Evaluation notes';
$string['submitevaluation'] = 'Submit evaluation';
$string['evaluationsubmitted'] = 'Evaluation submitted successfully';
$string['allevaluations'] = 'All evaluations';
$string['pendingevaluations'] = 'Pending evaluations';
$string['completedevaluations'] = 'Completed evaluations';
$string['myevaluations'] = 'My evaluations';
$string['viewevaluations'] = 'View evaluations';
$string['applicantranking'] = 'Applicant ranking';
$string['aggregateresults'] = 'Aggregate results';
$string['totalevaluators'] = 'Total evaluators';
$string['saveresults'] = 'Save results';

// Voting.
$string['submitvote'] = 'Submit vote';
$string['vote_approve'] = 'Approve';
$string['vote_reject'] = 'Reject';
$string['vote_abstain'] = 'Abstain';
$string['votes'] = 'Votes';
$string['approvalvotes'] = 'Approval votes';
$string['rejectionvotes'] = 'Rejection votes';
$string['abstentions'] = 'Abstentions';
$string['strong_approve'] = 'Strongly approve';
$string['recommendation_approve'] = 'Recommended';
$string['recommendation_reject'] = 'Not recommended';

// Decision.
$string['makedecision'] = 'Make decision';
$string['finaldecision'] = 'Final decision';
$string['decisionreason'] = 'Decision justification';
$string['confirmdecision'] = 'Are you sure about this decision? This action will notify the applicant.';
$string['decisionrecorded'] = 'Decision recorded successfully';
$string['applicantselected'] = 'Applicant selected';
$string['applicantrejected'] = 'Applicant not selected';
$string['committeeRecommendation'] = 'Committee recommendation';

// Notifications.
$string['notification_application_received_subject'] = 'Application confirmation - {VACANCY_TITLE}';
$string['notification_application_received_body'] = '<p>Dear {USER_NAME},</p><p>We have received your application for the vacancy <strong>{VACANCY_TITLE}</strong> (Code: {VACANCY_CODE}).</p><p>You can check the status of your application at any time through the following link: <a href="{APPLICATION_URL}">{APPLICATION_URL}</a></p><p>Sincerely,<br>{SITE_NAME}</p>';
$string['notification_under_review_subject'] = 'Your application is being reviewed - {VACANCY_TITLE}';
$string['notification_under_review_body'] = '<p>Dear {USER_NAME},</p><p>Your application for the vacancy <strong>{VACANCY_TITLE}</strong> is now under review.</p><p>We will inform you when there are updates.</p><p>Sincerely,<br>{SITE_NAME}</p>';
$string['notification_docs_validated_subject'] = 'Documents validated - {VACANCY_TITLE}';
$string['notification_docs_validated_body'] = '<p>Dear {USER_NAME},</p><p>Your documents for the vacancy <strong>{VACANCY_TITLE}</strong> have been successfully validated.</p><p>Sincerely,<br>{SITE_NAME}</p>';
$string['notification_docs_rejected_subject'] = 'Documents require correction - {VACANCY_TITLE}';
$string['notification_docs_rejected_body'] = '<p>Dear {USER_NAME},</p><p>Some documents from your application for the vacancy <strong>{VACANCY_TITLE}</strong> require correction.</p><p>Please log in to see the details: <a href="{APPLICATION_URL}">{APPLICATION_URL}</a></p><p>Sincerely,<br>{SITE_NAME}</p>';
$string['notification_interview_subject'] = 'Interview invitation - {VACANCY_TITLE}';
$string['notification_interview_body'] = '<p>Dear {USER_NAME},</p><p>You have been invited to an interview for the vacancy <strong>{VACANCY_TITLE}</strong>.</p><p>{NOTES}</p><p>Sincerely,<br>{SITE_NAME}</p>';
$string['notification_selected_subject'] = 'Congratulations! You have been selected - {VACANCY_TITLE}';
$string['notification_selected_body'] = '<p>Dear {USER_NAME},</p><p>We are pleased to inform you that you have been selected for the vacancy <strong>{VACANCY_TITLE}</strong>.</p><p>You will soon receive information about the next steps in the hiring process.</p><p>Sincerely,<br>{SITE_NAME}</p>';
$string['notification_rejected_subject'] = 'Selection process result - {VACANCY_TITLE}';
$string['notification_rejected_body'] = '<p>Dear {USER_NAME},</p><p>Thank you for participating in the selection process for the vacancy <strong>{VACANCY_TITLE}</strong>.</p><p>On this occasion, we have selected another candidate whose profile better fits the specific requirements of the position.</p><p>We encourage you to continue participating in future calls.</p><p>Sincerely,<br>{SITE_NAME}</p>';
$string['notification_closing_soon_subject'] = 'Vacancy closing soon - {VACANCY_TITLE}';
$string['notification_closing_soon_body'] = '<p>Dear {USER_NAME},</p><p>The vacancy <strong>{VACANCY_TITLE}</strong> closes in {DAYS_LEFT} day(s).</p><p>If you are interested, you can apply before {CLOSE_DATE} at: <a href="{VACANCY_URL}">{VACANCY_URL}</a></p><p>Sincerely,<br>{SITE_NAME}</p>';

// Tasks.
$string['task:checkclosingvacancies'] = 'Check closing vacancies';

// Import.
$string['importfromcsv'] = 'Import from CSV';
$string['csvfile'] = 'CSV file';
$string['csvformat'] = 'CSV format';
$string['requiredcolumns'] = 'Required columns';
$string['samplecsv'] = 'Sample CSV';
$string['importresults'] = 'Import results';
$string['rowsprocessed'] = 'Rows processed';
$string['rowsimported'] = 'Rows imported';
$string['rowsfailed'] = 'Rows failed';
$string['row'] = 'Row';
$string['useridentifier'] = 'User identifier';
$string['andmore'] = 'and {$a} more...';

// Table headers additional.
$string['result'] = 'Result';

// ==========================================================================
// Admin pages strings.
// ==========================================================================

// Document types management.
$string['managedoctypes'] = 'Manage Document Types';
$string['doctypecode'] = 'Document Type Code';
$string['doctypename'] = 'Document Type Name';
$string['nodoctypes'] = 'No document types configured';
$string['doctypecreated'] = 'Document type created successfully';
$string['doctypeupdated'] = 'Document type updated successfully';
$string['doctypedeleted'] = 'Document type deleted successfully';
$string['enabledoctype'] = 'Enable document type';
$string['disabledoctype'] = 'Disable document type';

// Email templates management.
$string['subject'] = 'Subject';
$string['body'] = 'Body';
$string['emailtemplateshelp'] = 'Email templates use placeholders like {USER_NAME}, {VACANCY_TITLE}, {APPLICATION_URL} that are replaced with actual values when the email is sent.';
$string['notemplates'] = 'No email templates configured. Templates are created automatically when the plugin is installed.';

// Document re-upload.
$string['newdocument'] = 'New document';
$string['uploaddocument'] = 'Upload document';
$string['reuploadhelp'] = 'Upload a new version of the document that was rejected. Make sure the new document addresses the rejection reason.';
$string['documentreuploaded'] = 'Document uploaded successfully. Your application is now under review again.';
$string['uploadfailed'] = 'Failed to upload document. Please try again.';
$string['cannotreupload'] = 'Cannot re-upload documents for this application in its current status.';

// Navigation settings.
$string['navigationsettings'] = 'Navigation Settings';
$string['navigationsettings_desc'] = 'Configure how Job Board appears in the site navigation.';
$string['showinmainmenu'] = 'Show in main navigation menu';
$string['showinmainmenu_desc'] = 'If enabled, Job Board will appear in the main navigation menu (top bar) with dropdown submenus.';
$string['mainmenutitle'] = 'Menu title';
$string['mainmenutitle_desc'] = 'Custom title for the Job Board menu item. Leave empty to use the default plugin name.';

// ==========================================================================
// Additional strings for completeness (Phase 8.3).
// ==========================================================================

// Application view.
$string['viewapplication'] = 'View application';
$string['withdrawapplication'] = 'Withdraw application';

// Exemption additional.
$string['viewexemption'] = 'View exemption';
$string['noexpiry'] = 'No expiry';
$string['exemptiontype_desc'] = 'historico_iser, documentos_recientes, traslado_interno, or recontratacion';
$string['exempteddocs_desc'] = 'List of document codes separated by | (e.g. cedula|rut|eps)';
$string['exempteddocs_help'] = 'Select the document types that will not be required';
$string['revokereason'] = 'Revoke reason';
$string['approvedocument'] = 'Approve document';
$string['avgvalidationtime'] = 'Average validation time';

// Import exemptions.
$string['csvdelimiter'] = 'CSV delimiter';
$string['encoding'] = 'Encoding';
$string['defaultexemptiontype'] = 'Default exemption type';
$string['defaultexemptiontype_help'] = 'This type will be used when the exemptiontype column is empty';
$string['defaultvalidfrom'] = 'Default valid from';
$string['defaultvaliduntil'] = 'Default valid until';
$string['previewonly'] = 'Preview only (do not import)';
$string['importinstructions'] = 'Import instructions';
$string['importinstructionstext'] = 'Prepare a CSV file with the data of users who will receive ISER exemptions.';
$string['optionalcolumns'] = 'Optional columns';
$string['previewmodenotice'] = 'Preview mode: no changes were made. Review the results and run again without the "Preview only" option.';
$string['previewtotal'] = '{$a} exemptions will be created';
$string['previewconfirm'] = 'To import these exemptions, go back and uncheck the "Preview only" option.';
$string['importcomplete'] = 'Import complete';
$string['importedsuccess'] = '{$a} exemptions imported successfully';
$string['importedskipped'] = '{$a} users skipped (already have an active exemption)';
$string['importerrors'] = 'Errors found';
$string['importednote'] = 'Imported via CSV on {$a}';
$string['importerror_usernotfound'] = 'Row {$a}: User not found';
$string['importerror_alreadyexempt'] = 'Row {$a->row}: {$a->user} already has an active exemption';
$string['importerror_createfailed'] = 'Row {$a->row}: Error creating exemption for {$a->user}';
$string['numdocs'] = 'Num. documents';
$string['documentref_desc'] = 'Supporting document reference (optional)';
$string['notes_desc'] = 'Additional notes (optional)';

// Interview scheduling extended.
$string['dateandtime'] = 'Date and time';
$string['interviewtype_inperson'] = 'In-person';
$string['interviewtype_video'] = 'Video call';
$string['interviewtype_phone'] = 'Phone';
$string['locationorurl'] = 'Location or URL';
$string['locationorurl_help'] = 'For in-person interviews, enter the address. For video calls, enter the meeting link.';
$string['interviewinstructions'] = 'Interview instructions';
$string['interviewscheduleerror'] = 'Error scheduling interview';
$string['interviewcompleted'] = 'Interview completed';
$string['interviewstatus_scheduled'] = 'Scheduled';
$string['interviewstatus_confirmed'] = 'Confirmed';
$string['interviewstatus_completed'] = 'Completed';
$string['interviewstatus_cancelled'] = 'Cancelled';
$string['interviewstatus_noshow'] = 'No show';
$string['interviewstatus_rescheduled'] = 'Rescheduled';
$string['confirmcancel'] = 'Are you sure you want to cancel this interview?';
$string['markedasnoshow'] = 'Interview marked as no show';
$string['overallrating'] = 'Overall rating';
$string['rating_poor'] = 'Poor';
$string['rating_fair'] = 'Fair';
$string['rating_good'] = 'Good';
$string['rating_verygood'] = 'Very good';
$string['rating_excellent'] = 'Excellent';
$string['recommendation'] = 'Recommendation';
$string['recommend_hire'] = 'Hire';
$string['recommend_furtherreview'] = 'Needs further review';
$string['recommend_reject'] = 'Do not hire';
$string['interviewfeedback'] = 'Interview feedback';
$string['error:pastdate'] = 'Date must be in the future';
$string['error:schedulingconflict'] = 'One or more interviewers have a scheduling conflict';

// Committee management extended.
$string['managecommittee'] = 'Manage selection committee';
$string['defaultcommitteename'] = 'Selection Committee - {$a}';
$string['initialmembers'] = 'Initial members';
$string['initialmembers_help'] = 'Select the members that will initially form the committee (besides the chair)';
$string['memberadded'] = 'Member added successfully';
$string['memberremoved'] = 'Member removed successfully';
$string['nomembers'] = 'No members in the committee';
$string['maxscore'] = 'Maximum score';
$string['nocriteria'] = 'No evaluation criteria defined';
$string['editcriteria'] = 'Edit criteria';
$string['rank'] = 'Rank';
$string['evaluate'] = 'Evaluate';
$string['decide'] = 'Decide';
$string['evaluateapplication'] = 'Evaluate application';
$string['evaluationscore'] = 'Evaluation score';
$string['evaluationvote'] = 'Vote';
$string['evaluationcomments'] = 'Evaluation comments';
$string['marginal'] = 'Marginal';

// =============================================================================
// USER TOURS - Complete Guided Tours for all plugin views
// =============================================================================

// Common tour strings.
$string['tour_endlabel'] = 'Got it!';

// Tour: Dashboard.
$string['tour_dashboard_name'] = 'Job Board Dashboard Tour';
$string['tour_dashboard_description'] = 'Learn how to navigate the Job Board dashboard';
$string['tour_dashboard_step1_title'] = 'Welcome to Job Board';
$string['tour_dashboard_step1_content'] = 'Welcome to the Job Board plugin! This guided tour will show you how to navigate and use the main features. The Job Board allows you to manage job vacancies, applications, and the complete hiring process.';
$string['tour_dashboard_step2_title'] = 'Dashboard Overview';
$string['tour_dashboard_step2_content'] = 'This is your main dashboard. From here you can access all the key features based on your role: browse vacancies, manage applications, review documents, and generate reports.';
$string['tour_dashboard_step3_title'] = 'Quick Actions';
$string['tour_dashboard_step3_content'] = 'Each card provides quick access to a specific area of the system. Click on any card to navigate to that section.';
$string['tour_dashboard_step4_title'] = 'Available Features';
$string['tour_dashboard_step4_content'] = 'Depending on your permissions, you can: view vacancies, track your applications, manage vacancies as an administrator, review applicant documents, and access reports and analytics.';
$string['tour_dashboard_step5_title'] = 'Action Cards';
$string['tour_dashboard_step5_content'] = 'These cards provide quick access to different sections: manage vacancies, view applications, and access reports. Click on any card to navigate to that section.';
$string['tour_dashboard_step6_title'] = 'Reviewer Section';
$string['tour_dashboard_step6_content'] = 'If you are a document reviewer, this section shows your assigned reviews and pending documents. Keep track of your review tasks here.';
$string['tour_dashboard_step7_title'] = 'My Applications';
$string['tour_dashboard_step7_content'] = 'As an applicant, this section shows your application status and quick access to view all your applications. Track your progress here.';
$string['tour_dashboard_step8_title'] = 'Ready to Start!';
$string['tour_dashboard_step8_content'] = 'You are now ready to use the Job Board! Explore the available options and don\'t hesitate to use the help resources if you need assistance.';

// Tour: Public Vacancies.
$string['tour_public_name'] = 'Public Vacancies Tour';
$string['tour_public_description'] = 'Learn how to browse and search for available vacancies';
$string['tour_public_step1_title'] = 'Public Vacancies';
$string['tour_public_step1_content'] = 'Welcome to the public vacancies page! Here you can find all available job opportunities. This guided tour will show you how to search and filter vacancies effectively.';
$string['tour_public_step2_title'] = 'Page Header';
$string['tour_public_step2_content'] = 'This section displays the page title and any welcome message configured by the administrator. Read it to understand more about the organization\'s hiring process.';
$string['tour_public_step3_title'] = 'Search and Filters';
$string['tour_public_step3_content'] = 'Use these controls to filter vacancies. You can search by keyword, filter by contract type, location, and more to find opportunities that match your profile.';
$string['tour_public_step4_title'] = 'Search Box';
$string['tour_public_step4_content'] = 'Type keywords here to search in vacancy titles, codes, and descriptions. The search is case-insensitive and looks for partial matches.';
$string['tour_public_step5_title'] = 'Filter Dropdowns';
$string['tour_public_step5_content'] = 'Use these dropdown menus to filter by contract type (full-time, part-time, etc.) and location. Combine multiple filters to narrow down your search.';
$string['tour_public_step6_title'] = 'Vacancy Cards';
$string['tour_public_step6_content'] = 'Each vacancy is displayed as a card with key information: title, location, contract type, and closing date. The card also shows how many days remain until the application deadline.';
$string['tour_public_step7_title'] = 'Vacancy Type Badges';
$string['tour_public_step7_content'] = 'Vacancies are marked as "Public" (open to everyone) or "Internal" (only for authenticated users from the organization). Make sure you meet the eligibility criteria before applying.';
$string['tour_public_step8_title'] = 'View Details and Apply';
$string['tour_public_step8_content'] = 'Click "View Details" to see the complete vacancy information, or click "Apply" to start your application. You may need to log in first to apply.';
$string['tour_public_step9_title'] = 'Call to Action';
$string['tour_public_step9_content'] = 'If you are not logged in, you will see a prompt to create an account or log in. This allows you to apply for vacancies and track your applications.';
$string['tour_public_step10_title'] = 'Start Exploring!';
$string['tour_public_step10_content'] = 'You are now ready to browse available vacancies! Use the search and filters to find opportunities that match your skills and interests. Good luck with your applications!';

// Tour: Application Form.
$string['tour_apply_name'] = 'Application Form Tour';
$string['tour_apply_description'] = 'Learn how to complete and submit your job application';
$string['tour_apply_step1_title'] = 'Apply for a Vacancy';
$string['tour_apply_step1_content'] = 'Welcome to the application form! This guided tour will help you understand each section and successfully submit your application.';
$string['tour_apply_step2_title'] = 'Your Progress';
$string['tour_apply_step2_content'] = 'These progress steps show where you are in the application process. Complete each section in order: Consent, Documents, Cover Letter, and Submit. The active step is highlighted in blue.';
$string['tour_apply_step3_title'] = 'Application Guidelines';
$string['tour_apply_step3_content'] = 'Read these guidelines carefully before starting your application. They contain important information about required documents, accepted formats, and the application process. Click the header to collapse or expand.';
$string['tour_apply_step4_title'] = 'Consent and Terms';
$string['tour_apply_step4_content'] = 'You must accept the terms and conditions and give consent for processing your personal data according to data protection laws. Enter your digital signature and check the consent box.';
$string['tour_apply_step5_title'] = 'Required Documents';
$string['tour_apply_step5_content'] = 'Upload all required documents here. Make sure each document is legible, complete, and in an accepted format (usually PDF, JPG, or PNG). Required documents are marked with an asterisk (*).';
$string['tour_apply_step6_title'] = 'Cover Letter';
$string['tour_apply_step6_content'] = 'Optionally, you can add a cover letter to explain why you are interested in this position and highlight your relevant qualifications and experience.';
$string['tour_apply_step7_title'] = 'Declaration';
$string['tour_apply_step7_content'] = 'Read and accept the final declaration to confirm that all information you provided is accurate and complete. This is required before submitting.';
$string['tour_apply_step8_title'] = 'Submit Application';
$string['tour_apply_step8_content'] = 'Once you have filled in all required fields and uploaded your documents, click this button to submit your application. Make sure everything is correct before submitting!';
$string['tour_apply_step9_title'] = 'Vacancy Summary';
$string['tour_apply_step9_content'] = 'This sidebar shows the vacancy details, closing date, and time remaining. The document checklist helps you keep track of required uploads. Need help? Click "Start Guided Tour" to restart this tour anytime.';

// Tour: My Applications.
$string['tour_myapplications_name'] = 'My Applications Tour';
$string['tour_myapplications_description'] = 'Learn how to track and manage your job applications';
$string['tour_myapplications_step1_title'] = 'My Applications';
$string['tour_myapplications_step1_content'] = 'Welcome to your applications dashboard! Here you can track the status of all your job applications and see any updates or required actions.';
$string['tour_myapplications_step2_title'] = 'Exemption Status';
$string['tour_myapplications_step2_content'] = 'If you have an active exemption (for example, as a current or former employee), it will be displayed here. This affects which documents you need to submit with your applications.';
$string['tour_myapplications_step3_title'] = 'Status Filter';
$string['tour_myapplications_step3_content'] = 'Use this filter to show only applications with a specific status. This helps you focus on applications that need attention or review.';
$string['tour_myapplications_step4_title'] = 'Applications Table';
$string['tour_myapplications_step4_content'] = 'This table shows all your applications with key information: vacancy name, date applied, current status, and document count. Click on any row for more details.';
$string['tour_myapplications_step5_title'] = 'Application Status';
$string['tour_myapplications_step5_content'] = 'The status badge shows where your application is in the review process: Submitted, Under Review, Documents Validated, Interview, Selected, or Rejected. Watch for status changes!';
$string['tour_myapplications_step6_title'] = 'Available Actions';
$string['tour_myapplications_step6_content'] = 'Click "View" to see your full application details and uploaded documents. If your application is still under review, you may also have the option to withdraw it.';
$string['tour_myapplications_step7_title'] = 'View Application Details';
$string['tour_myapplications_step7_content'] = 'Click "View" to see your full application details and uploaded documents. You can also withdraw your application if it is still under review.';
$string['tour_myapplications_step8_title'] = 'Stay Updated!';
$string['tour_myapplications_step8_content'] = 'Check this page regularly for updates on your applications. You will also receive email notifications for important status changes. Good luck with your applications!';

// Tour: Document Review.
$string['tour_review_name'] = 'Document Review Tour';
$string['tour_review_description'] = 'Learn how to review and validate applicant documents';
$string['tour_review_step1_title'] = 'Document Review Center';
$string['tour_review_step1_content'] = 'Welcome to the document review center! As a reviewer, you are responsible for validating applicant documents to ensure they meet the requirements.';
$string['tour_review_step2_title'] = 'Review Progress';
$string['tour_review_step2_content'] = 'These steps show your progress: Examine (download and check documents), Validate (approve or reject each), and Complete (finalize the review). Completed steps turn green.';
$string['tour_review_step3_title'] = 'Statistics Overview';
$string['tour_review_step3_content'] = 'Quick statistics show total documents, how many are approved, rejected, and still pending. Use these to track your progress at a glance.';
$string['tour_review_step4_title'] = 'Review Guidelines';
$string['tour_review_step4_content'] = 'Read these guidelines before reviewing. They remind you to download documents, check legibility, provide clear rejection reasons, and complete all reviews before finalizing.';
$string['tour_review_step5_title'] = 'Document List';
$string['tour_review_step5_content'] = 'All uploaded documents are listed here. Pending documents have a yellow border, approved ones green, and rejected ones red. Each shows type, filename, and current status.';
$string['tour_review_step6_title'] = 'Document Status';
$string['tour_review_step6_content'] = 'The colored indicator shows each document\'s status. Pending documents need your attention. After validation, you\'ll see who reviewed it and when.';
$string['tour_review_step7_title'] = 'Validation Actions';
$string['tour_review_step7_content'] = 'For each document: Download to examine, click the green check to Approve, or the red X to Reject. When rejecting, provide a clear reason so the applicant can resubmit.';
$string['tour_review_step8_title'] = 'Review Tips';
$string['tour_review_step8_content'] = 'This sidebar provides helpful reminders: download documents, check legibility, verify completeness, and authenticate when possible. Follow these tips for thorough reviews.';
$string['tour_review_step9_title'] = 'Happy Reviewing!';
$string['tour_review_step9_content'] = 'When all documents are reviewed, a completion button will appear. Remember: careful validation is crucial for the integrity of the selection process. Take your time!';

// Tour: Vacancy Management.
$string['tour_manage_name'] = 'Vacancy Management Tour';
$string['tour_manage_description'] = 'Learn how to create and manage job vacancies';
$string['tour_manage_step1_title'] = 'Vacancy Management';
$string['tour_manage_step1_content'] = 'Welcome to the vacancy management center! Here you can create, edit, publish, and manage all job vacancies. Bulk actions and pagination controls make managing large numbers of vacancies easy.';
$string['tour_manage_step2_title'] = 'Management Dashboard';
$string['tour_manage_step2_content'] = 'This is your vacancy management dashboard. From here you can view statistics, filter vacancies, perform bulk actions, and navigate through your vacancies efficiently.';
$string['tour_manage_step3_title'] = 'Statistics Cards';
$string['tour_manage_step3_content'] = 'These cards show at-a-glance statistics: total vacancies, published vacancies, applications received, and positions available. Use them to monitor your recruitment activity.';
$string['tour_manage_step4_title'] = 'Filter Options';
$string['tour_manage_step4_content'] = 'Filter vacancies by status (Draft, Published, Closed) and company (in multi-tenant setups). Use the search box to find specific vacancies by code or title.';
$string['tour_manage_step5_title'] = 'Bulk Selection';
$string['tour_manage_step5_content'] = 'Use these checkboxes to select multiple vacancies at once. When items are selected, a bulk actions toolbar appears allowing you to publish, unpublish, close, or delete multiple vacancies simultaneously.';
$string['tour_manage_step6_title'] = 'Vacancies Table';
$string['tour_manage_step6_content'] = 'This table displays all vacancies with their code, title, status, dates, and application count. Click on a vacancy title to view its full details.';
$string['tour_manage_step7_title'] = 'Status Badges';
$string['tour_manage_step7_content'] = 'The status badge shows the current state: Draft (not visible), Published (accepting applications), Closed (no longer accepting), or Assigned (positions filled).';
$string['tour_manage_step8_title'] = 'Action Buttons';
$string['tour_manage_step8_content'] = 'Use these buttons for individual actions: Edit the vacancy, Publish a draft, Close an active vacancy, View applications, or Delete (only if no applications exist).';
$string['tour_manage_step9_title'] = 'Pagination Controls';
$string['tour_manage_step9_content'] = 'Use the pagination bar to navigate through pages of vacancies. You can also select how many records to display per page (10, 25, 50, or 100).';
$string['tour_manage_step10_title'] = 'You\'re All Set!';
$string['tour_manage_step10_content'] = 'You now know how to manage vacancies! Use bulk actions to efficiently manage multiple vacancies, and remember to publish vacancies to make them visible to applicants.';

// Tour: Reports.
$string['tour_reports_name'] = 'Reports and Analytics Tour';
$string['tour_reports_description'] = 'Learn how to generate reports and analyze recruitment data';
$string['tour_reports_step1_title'] = 'Reports Dashboard';
$string['tour_reports_step1_content'] = 'Welcome to the reports section! Here you can analyze recruitment data, track performance metrics, and export reports for further analysis.';
$string['tour_reports_step2_title'] = 'Report Types';
$string['tour_reports_step2_content'] = 'Use these tabs to switch between different report types: Overview (summary statistics), Applications, Documents, Reviewers (performance), and Timeline (trends).';
$string['tour_reports_step3_title'] = 'Vacancy Filter';
$string['tour_reports_step3_content'] = 'Filter reports by a specific vacancy or view data for all vacancies combined. This helps you analyze performance for individual positions.';
$string['tour_reports_step4_title'] = 'Date Range';
$string['tour_reports_step4_content'] = 'Set the date range for your report. By default, reports show the last 30 days, but you can adjust this to analyze any time period.';
$string['tour_reports_step5_title'] = 'Export Options';
$string['tour_reports_step5_content'] = 'Export your report data in CSV or Excel format for further analysis, sharing with stakeholders, or archiving. PDF export is also available.';
$string['tour_reports_step6_title'] = 'Report Content';
$string['tour_reports_step6_content'] = 'The main report area displays statistics, tables, and charts based on the selected report type. Data updates automatically when you change filters.';
$string['tour_reports_step7_title'] = 'Visual Indicators';
$string['tour_reports_step7_content'] = 'Progress bars and charts help you quickly understand the data. Green indicates positive metrics, red indicates areas that may need attention.';
$string['tour_reports_step8_title'] = 'Make Data-Driven Decisions!';
$string['tour_reports_step8_content'] = 'Use these reports to identify trends, optimize your recruitment process, and make informed decisions. Regular analysis leads to continuous improvement!';

// Tour: Document Validation Page.
$string['tour_validate_name'] = 'Document Validation Tour';
$string['tour_validate_description'] = 'Learn how to properly validate applicant documents';
$string['tour_validate_step1_title'] = 'Document Validation';
$string['tour_validate_step1_content'] = 'Welcome to the document validation page! Here you will review a specific document and decide whether to approve or reject it.';
$string['tour_validate_step2_title'] = 'Document Information';
$string['tour_validate_step2_content'] = 'This section shows details about the document: type, filename, upload date, and issue date (if applicable). Review this information carefully.';
$string['tour_validate_step3_title'] = 'View Document';
$string['tour_validate_step3_content'] = 'Click this button to open the document in a new tab or use the inline preview. Examine the document carefully before making a decision.';
$string['tour_validate_step4_title'] = 'Validation Checklist';
$string['tour_validate_step4_content'] = 'Use this checklist to verify the document meets all requirements. Each document type has specific criteria you should verify.';
$string['tour_validate_step5_title'] = 'Check Each Item';
$string['tour_validate_step5_content'] = 'Go through each checklist item: Is the document legible? Is it complete? Does the name match the applicant? Are dates current (for time-sensitive documents)?';
$string['tour_validate_step6_title'] = 'Approve Document';
$string['tour_validate_step6_content'] = 'If the document meets all requirements, click "Approve". You can optionally add notes for your records.';
$string['tour_validate_step7_title'] = 'Reject Document';
$string['tour_validate_step7_content'] = 'If the document has issues, select a rejection reason from the dropdown and click "Reject". The applicant will be notified and can upload a corrected document.';
$string['tour_validate_step8_title'] = 'Validation Complete!';
$string['tour_validate_step8_content'] = 'After making your decision, you\'ll return to the application view. Continue reviewing other documents until all are validated.';

// Tour: Vacancies List.
$string['tour_vacancies_name'] = 'Vacancies List Tour';
$string['tour_vacancies_description'] = 'Learn how to browse and filter available vacancies';
$string['tour_vacancies_step1_title'] = 'Vacancies Overview';
$string['tour_vacancies_step1_content'] = 'Welcome to the vacancies list! This page shows all vacancies you have access to view. Use filters and search to find specific opportunities.';
$string['tour_vacancies_step2_title'] = 'Search and Filter Panel';
$string['tour_vacancies_step2_content'] = 'Use this panel to search and filter vacancies. You can combine multiple filters to narrow down results.';
$string['tour_vacancies_step3_title'] = 'Search Box';
$string['tour_vacancies_step3_content'] = 'Type keywords to search in vacancy titles, codes, and descriptions. Press Enter or click Search to apply.';
$string['tour_vacancies_step4_title'] = 'Status Filter';
$string['tour_vacancies_step4_content'] = 'Filter vacancies by their status: Draft (not published), Published (accepting applications), Closed (no longer accepting), or Assigned (positions filled).';
$string['tour_vacancies_step5_title'] = 'Vacancies Table';
$string['tour_vacancies_step5_content'] = 'The table shows all matching vacancies with key information: code, title, status, dates, and available positions.';
$string['tour_vacancies_step6_title'] = 'Status Badges';
$string['tour_vacancies_step6_content'] = 'Status badges help you quickly identify vacancy states: green for published, yellow for draft, gray for closed.';
$string['tour_vacancies_step7_title'] = 'Apply for Vacancies';
$string['tour_vacancies_step7_content'] = 'Click the Apply button to submit your application for any open vacancy that interests you. Good luck!';

// Tour: Single Vacancy Detail.
$string['tour_vacancy_name'] = 'Vacancy Detail Tour';
$string['tour_vacancy_description'] = 'Learn about all the information available in a vacancy detail page';
$string['tour_vacancy_step1_title'] = 'Vacancy Details';
$string['tour_vacancy_step1_content'] = 'This page shows complete information about a specific vacancy. Review all details before applying.';
$string['tour_vacancy_step2_title'] = 'Vacancy Header';
$string['tour_vacancy_step2_content'] = 'The header shows the vacancy code and publication type badge. Public vacancies are open to everyone; internal ones are for organization members only.';
$string['tour_vacancy_step3_title'] = 'Vacancy Title';
$string['tour_vacancy_step3_content'] = 'The vacancy title and main details are shown here, including company name (if applicable), location, and contract type.';
$string['tour_vacancy_step4_title'] = 'Closing Date Alert';
$string['tour_vacancy_step4_content'] = 'Pay attention to the closing date! If it shows as a warning, the deadline is approaching soon. Make sure to submit your application on time.';
$string['tour_vacancy_step5_title'] = 'Apply Button';
$string['tour_vacancy_step5_content'] = 'Click this button to start your application. You may need to log in first if you haven\'t already.';
$string['tour_vacancy_step6_title'] = 'Additional Details';
$string['tour_vacancy_step6_content'] = 'Review additional details like duration, contract type, department, and important dates before applying.';
$string['tour_vacancy_step7_title'] = 'Navigation';
$string['tour_vacancy_step7_content'] = 'Use this back button to return to the previous page - either the convocatoria list or the vacancies list, depending on how you navigated here.';
$string['tour_vacancy_step8_title'] = 'Ready to Apply!';
$string['tour_vacancy_step8_content'] = 'You now have all the information you need. If this vacancy matches your profile, go ahead and apply!';

// Tour: Application Detail.
$string['tour_application_name'] = 'Application Detail Tour';
$string['tour_application_description'] = 'Learn how to track your application status and manage documents';
$string['tour_application_step1_title'] = 'Your Application';
$string['tour_application_step1_content'] = 'This page shows the complete details of your application. Track your progress and manage your documents here.';
$string['tour_application_step2_title'] = 'Application Status';
$string['tour_application_step2_content'] = 'The status badge shows where your application is in the process: Submitted, Under Review, Documents Validated, Interview, Selected, or Rejected.';
$string['tour_application_step3_title'] = 'Progress Indicator';
$string['tour_application_step3_content'] = 'This progress bar shows how far along you are in the application process. Watch it advance as your documents are reviewed.';
$string['tour_application_step4_title'] = 'Document List';
$string['tour_application_step4_content'] = 'All your uploaded documents are listed here with their validation status. Green means approved, red means rejected, and yellow means pending review.';
$string['tour_application_step5_title'] = 'Document Actions';
$string['tour_application_step5_content'] = 'For each document, you can view or download it. If a document was rejected, you\'ll see an option to upload a corrected version.';
$string['tour_application_step6_title'] = 'Application History';
$string['tour_application_step6_content'] = 'The history section shows all status changes and actions taken on your application. This helps you track the review process.';
$string['tour_application_step7_title'] = 'Stay Informed!';
$string['tour_application_step7_content'] = 'Check back regularly for updates, or enable email notifications to be informed of status changes. Good luck with your application!';

// Tour: My Reviews.
$string['tour_myreviews_name'] = 'My Reviews Tour';
$string['tour_myreviews_description'] = 'Learn how to manage your assigned document reviews';
$string['tour_myreviews_step1_title'] = 'Your Review Queue';
$string['tour_myreviews_step1_content'] = 'Welcome to your review queue! This page shows all applications and documents assigned to you for review.';
$string['tour_myreviews_step2_title'] = 'Queue Overview';
$string['tour_myreviews_step2_content'] = 'The overview cards show your pending reviews, completed reviews, and any urgent items requiring immediate attention.';
$string['tour_myreviews_step3_title'] = 'Pending Items';
$string['tour_myreviews_step3_content'] = 'Items marked with a warning badge require your attention. Prioritize these to keep the review process moving.';
$string['tour_myreviews_step4_title'] = 'Review Table';
$string['tour_myreviews_step4_content'] = 'The table lists all your assigned reviews with applicant information, vacancy details, and current status.';
$string['tour_myreviews_step5_title'] = 'Review Actions';
$string['tour_myreviews_step5_content'] = 'Click the Review button to start reviewing documents for any application. You\'ll be taken to the detailed review page.';
$string['tour_myreviews_step6_title'] = 'Start Reviewing!';
$string['tour_myreviews_step6_content'] = 'Your reviews help applicants move forward in the process. Try to complete reviews promptly to maintain an efficient workflow.';

// Missing strings - Phase 8.8: Complete language string coverage.
// Assignment and reviewer related.
$string['activeassignments'] = 'Active assignments';
$string['assigned'] = 'Assigned';
$string['assignselected'] = 'Assign selected';
$string['assignto'] = 'Assign to';
$string['autoassignall'] = 'Auto-assign all';
$string['autoassigncomplete'] = 'Auto-assignment complete. {$a->assigned} applications assigned to {$a->reviewers} reviewers.';
$string['autoassignhelp'] = 'Automatically distribute pending applications among available reviewers based on current workload.';
$string['manualassign'] = 'Manual assignment';
$string['maxperreviewer'] = 'Maximum per reviewer';
$string['nodocumentspending'] = 'No documents pending review';
$string['norejections'] = 'No rejections';
$string['noreviewers'] = 'No reviewers available';
$string['nounassignedapplications'] = 'No unassigned applications';
$string['reviewed'] = 'Reviewed';
$string['reviewerunassigned'] = 'Reviewer unassigned successfully';

// Validation and documents.
$string['alreadyvalidated'] = 'This document has already been validated';
$string['autovalidated'] = 'Auto-validated';
$string['bulkactions'] = 'Bulk actions';
$string['bulkvalidationcomplete'] = 'Bulk validation complete. {$a->approved} approved, {$a->rejected} rejected.';
$string['documentnotfound'] = 'Document not found';
$string['doctypeshelp'] = 'Configure the document types that applicants can upload. Each type can be marked as required or optional.';
$string['optionalnotes'] = 'Optional notes';
$string['rejectionreason'] = 'Rejection reason';
$string['rejectionreasons'] = 'Rejection reasons';
$string['validationsummary'] = 'Validation summary';

// API related.
$string['api:authheader'] = 'Authorization header';
$string['api:baseurl'] = 'Base URL';
$string['api:info'] = 'API information';
$string['api:ratelimit'] = 'Rate limit';
$string['api:requestsperhour'] = 'requests per hour';
$string['api:token:copywarning'] = 'This token will only be shown once. Copy it now and store it securely.';
$string['api:token:deleteconfirm'] = 'Are you sure you want to permanently delete this API token? This action cannot be undone.';
$string['api:token:none'] = 'No API tokens found';
$string['api:token:revokeconfirm'] = 'Are you sure you want to revoke this API token? It will no longer work for authentication.';
$string['api:token:usage'] = 'Token usage';

// Reports and statistics.
$string['applicationsbyvacancy'] = 'Applications by vacancy';
$string['avgtime'] = 'Average time';

// CSV import.
$string['csvimporterror'] = 'Error importing CSV file';
$string['csvinvalidtype'] = 'Invalid exemption type: {$a}';
$string['csvlineerror'] = 'Error on line {$a->line}: {$a->error}';
$string['csvusernotfound'] = 'User not found: {$a}';

// Encryption.
$string['encryption:backupinstructions'] = 'Download and securely store this encryption key. You will need it to decrypt documents if you restore from backup.';
$string['encryption:nokeytobackup'] = 'No encryption key to backup. Enable encryption first.';

// Error messages.
$string['error:invalidpublicationtype'] = 'Invalid publication type';
$string['error:invalidstatus'] = 'Invalid status';
$string['vacancynotfound'] = 'Vacancy not found';

// Miscellaneous.
$string['lastused'] = 'Last used';
$string['selecttype'] = 'Select type';
$string['type'] = 'Type';
$string['share'] = 'Share';

// Export functionality.
$string['exportdocumentszip'] = 'Export Documents (ZIP)';
$string['exportalldocuments'] = 'Export All Documents';
$string['exportapplicationdocs'] = 'Export Application Documents';
$string['zipexportfailed'] = 'Failed to create ZIP archive';
$string['nodocumentstoexport'] = 'No documents to export';
$string['invalidparameters'] = 'Invalid parameters';
$string['exportingpdf'] = 'Exporting PDF...';
$string['pdfreportgenerated'] = 'PDF report generated successfully';
$string['pdfexportfailed'] = 'Failed to generate PDF report';
$string['generatedon'] = 'Generated on';

// Document conversion.
$string['conversionready'] = 'Preview ready';
$string['conversioninprogress'] = 'Converting document...';
$string['conversionpending'] = 'Conversion pending';
$string['conversionfailed'] = 'Conversion failed';
$string['conversionwait'] = 'The document is being converted for preview. This may take a few moments.';
$string['previewunavailable'] = 'Preview not available for this file type';
$string['downloadtoview'] = 'Download to view';
$string['convertersavailable'] = 'Document converters available';
$string['noconvertersavailable'] = 'No document converters configured';
$string['supportedformats'] = 'Supported formats for conversion';
$string['documentconverted'] = 'Document converted successfully';
$string['refreshpreview'] = 'Refresh preview';

// ==========================================================================
// Alternative Signup Form strings.
// ==========================================================================

// Signup page titles and intro.
$string['signup_title'] = 'Create Your Account';
$string['signup_intro'] = 'Register to apply for vacancies and track your applications. Complete the form below with your information.';
$string['signup_success_title'] = 'Registration Successful!';
$string['signup_success_message'] = 'A confirmation email has been sent to {$a}. Please check your inbox and click the confirmation link to activate your account.';
$string['signup_success_instructions'] = 'Once you confirm your email, you will be able to log in and apply for vacancies.';

// Signup form sections.
$string['signup_personalinfo'] = 'Personal Information';
$string['signup_contactinfo'] = 'Contact Information';
$string['signup_companyinfo'] = 'Company Selection';
$string['signup_termsheader'] = 'Terms and Conditions';

// Signup form fields.
$string['signup_username'] = 'Username';
$string['signup_username_help'] = 'Choose a unique username that you will use to log in. It should contain only lowercase letters, numbers, underscores, and hyphens.';
$string['signup_password'] = 'Password';
$string['signup_password_help'] = 'Create a strong password with at least 8 characters, including uppercase and lowercase letters, numbers, and symbols.';
$string['signup_idnumber'] = 'Identification Number';
$string['signup_idnumber_help'] = 'Enter your national identification number (e.g., ID card, passport number). This will be used to verify your identity.';
$string['signup_company_help'] = 'Select the company or organization you wish to apply to. This helps us route your application to the appropriate department.';

// Signup form actions.
$string['signup_createaccount'] = 'Create Account';
$string['signup_already_account'] = 'Already have an account?';
$string['signup_applying_for'] = 'You are registering to apply for:';

// Signup form validations and errors.
$string['signup_terms_accept'] = 'I have read and accept the terms of service and privacy policy';
$string['signup_terms_required'] = 'You must accept the terms and conditions to create an account';
$string['signup_datatreatment_accept'] = 'I consent to the processing of my personal data as described above';
$string['signup_datatreatment_required'] = 'You must consent to the data treatment policy to create an account';
$string['signup_privacy_text'] = 'By creating an account, you agree to our <a href="{$a}" target="_blank">Privacy Policy</a>. Your personal data will be processed in accordance with applicable data protection regulations.';
$string['signup_email_error'] = 'Failed to send confirmation email. Please try again or contact support.';
$string['emailnotmatch'] = 'The email addresses do not match';

// Password strength indicators.
$string['password_strength_weak'] = 'Weak password';
$string['password_strength_medium'] = 'Medium strength';
$string['password_strength_strong'] = 'Strong password';

// Registration disabled.
$string['registrationdisabled'] = 'Self-registration is currently disabled. Please contact the administrator for assistance.';

// ==========================================================================
// Enhanced Signup Form strings (extended profile fields).
// ==========================================================================

// Form sections.
$string['signup_account_header'] = 'Account Credentials';
$string['signup_academic_header'] = 'Academic and Professional Profile';
$string['signup_required_fields'] = 'Fields marked with an asterisk are required';

// Document types.
$string['signup_doctype'] = 'Document Type';
$string['signup_doctype_cc'] = 'Citizenship ID (Cédula de Ciudadanía)';
$string['signup_doctype_ce'] = 'Foreign ID (Cédula de Extranjería)';
$string['signup_doctype_passport'] = 'Passport';
$string['signup_doctype_ti'] = 'Identity Card (Tarjeta de Identidad)';
$string['signup_doctype_pep'] = 'Special Stay Permit (PEP)';
$string['signup_doctype_ppt'] = 'Temporary Protection Permit (PPT)';

// Personal fields.
$string['signup_birthdate'] = 'Date of Birth';
$string['signup_gender'] = 'Gender';
$string['signup_gender_male'] = 'Male';
$string['signup_gender_female'] = 'Female';
$string['signup_gender_other'] = 'Other';
$string['signup_gender_prefer_not'] = 'Prefer not to say';

// Contact fields.
$string['signup_phone_mobile'] = 'Mobile Phone';
$string['signup_phone_home'] = 'Home/Alternative Phone';
$string['signup_department_region'] = 'State/Province/Department';

// Academic fields.
$string['signup_education_level'] = 'Highest Education Level';
$string['signup_edu_highschool'] = 'High School';
$string['signup_edu_technical'] = 'Technical Degree';
$string['signup_edu_technological'] = 'Technological Degree';
$string['signup_edu_undergraduate'] = 'Undergraduate Degree (Bachelor\'s)';
$string['signup_edu_specialization'] = 'Specialization';
$string['signup_edu_masters'] = 'Master\'s Degree';
$string['signup_edu_doctorate'] = 'Doctorate (PhD)';
$string['signup_edu_postdoctorate'] = 'Post-Doctorate';

$string['signup_degree_title'] = 'Degree/Title Obtained';
$string['signup_degree_title_help'] = 'Enter the exact name of your highest degree or professional title. For example: "Bachelor of Science in Computer Engineering"';
$string['signup_institution'] = 'Institution';
$string['signup_institution_help'] = 'Enter the name of the educational institution where you obtained your highest degree';
$string['signup_expertise_area'] = 'Area of Expertise/Specialization';
$string['signup_expertise_area_help'] = 'Enter your main area of professional expertise or academic specialization';

// Experience.
$string['signup_experience_years'] = 'Years of Professional Experience';
$string['signup_exp_none'] = 'No experience';
$string['signup_exp_less_1'] = 'Less than 1 year';
$string['signup_exp_1_3'] = '1 to 3 years';
$string['signup_exp_3_5'] = '3 to 5 years';
$string['signup_exp_5_10'] = '5 to 10 years';
$string['signup_exp_more_10'] = 'More than 10 years';

// Professional profile.
$string['signup_professional_profile'] = 'Professional Profile';
$string['signup_professional_profile_help'] = 'Write a brief description of your professional profile, including your key skills, experience, and career objectives (maximum 1000 characters)';

// Validation messages.
$string['signup_username_tooshort'] = 'Username must be at least 4 characters long';
$string['signup_idnumber_exists'] = 'This identification number is already registered in the system';
$string['signup_birthdate_minage'] = 'You must be at least 18 years old to register';
$string['signup_dataaccuracy_accept'] = 'I declare that all information provided is accurate and truthful';
$string['signup_dataaccuracy_required'] = 'You must confirm that the information provided is accurate';
$string['signup_error_creating'] = 'An error occurred while creating your account';

// Email confirmation instructions.
$string['signup_email_instructions_title'] = 'Next Steps';
$string['signup_email_instruction_1'] = 'Check your email inbox for the confirmation message';
$string['signup_email_instruction_2'] = 'Click the confirmation link in the email to activate your account';
$string['signup_email_instruction_3'] = 'Once confirmed, you can log in and apply for vacancies';
$string['signup_check_spam'] = 'If you don\'t see the email, please check your spam or junk folder';

// Username = ID Number strings.
$string['signup_username_is_idnumber'] = 'Your identification number will be your username to access the platform.';
$string['signup_idnumber_username'] = 'Identification Number (Username)';
$string['signup_idnumber_username_help'] = 'Enter your national identification number. This will be used as your username to log into the platform. For example: 1234567890';
$string['signup_idnumber_tooshort'] = 'The identification number must have at least 4 characters';
$string['signup_idnumber_exists_as_user'] = 'A user with this identification number already exists. Please log in instead.';

// Apply modal strings.
$string['apply_modal_title'] = 'Apply for this Vacancy';
$string['apply_modal_question'] = 'Do you already have an account on our platform?';
$string['apply_modal_registered'] = 'Yes, I have an account';
$string['apply_modal_not_registered'] = 'No, I need to register';
$string['apply_modal_registered_desc'] = 'Log in with your credentials and update your profile to apply.';
$string['apply_modal_not_registered_desc'] = 'Create a new account using your ID number as username.';

// Profile update strings.
$string['updateprofile_title'] = 'Update Your Profile';
$string['updateprofile_intro'] = 'Please complete or update your profile information before applying for vacancies.';
$string['updateprofile_required'] = 'You must complete your profile before applying for vacancies.';
$string['updateprofile_success'] = 'Your profile has been updated successfully.';
$string['updateprofile_company_required'] = 'Please select a company/department to continue with your application.';
$string['updateprofile_continue_apply'] = 'Continue to Application';
$string['updateprofile_submit'] = 'Update Profile and Continue';
$string['completeprofile_required'] = 'You must complete your profile information before applying for this vacancy. Please fill in the required fields below.';

// Tour: Convocatorias.
$string['tour_convocatorias_name'] = 'Calls/Campaigns Management Tour';
$string['tour_convocatorias_description'] = 'Learn how to create and manage job calls that group related vacancies';
$string['tour_convocatorias_step1_title'] = 'Welcome to Calls Management';
$string['tour_convocatorias_step1_content'] = 'This is the calls (convocatorias) management center. A call groups related vacancies and defines the period during which applications are accepted. You can create calls with specific start and end dates, and then add multiple vacancies to each call.';
$string['tour_convocatorias_step2_title'] = 'Create New Call';
$string['tour_convocatorias_step2_content'] = 'Click this button to create a new call. You will need to provide a unique code, name, description, start date, end date, and terms and conditions. The call can be set as public (visible to everyone) or internal (only for authenticated users).';
$string['tour_convocatorias_step3_title'] = 'Calls Table';
$string['tour_convocatorias_step3_content'] = 'This table shows all existing calls with their code, name, dates, status, and number of associated vacancies. Each call has a lifecycle: Draft (being prepared), Open (accepting applications), Closed (applications finished), and Archived (historical record).';
$string['tour_convocatorias_step4_title'] = 'Call Status';
$string['tour_convocatorias_step4_content'] = 'The status badge indicates the current state of the call. Draft calls are not visible to applicants. When you are ready, open the call to start receiving applications. The call can be manually closed or will auto-close on the end date.';
$string['tour_convocatorias_step5_title'] = 'Call Status';
$string['tour_convocatorias_step5_content'] = 'Badges show the current status of each call: Draft (being prepared), Open (accepting applications), Closed (applications finished), or Archived. The status determines whether applicants can view and apply to vacancies.';
$string['tour_convocatorias_step6_title'] = 'Import Vacancies from CSV';
$string['tour_convocatorias_step6_content'] = 'Use this button to bulk import multiple vacancies from a CSV file. You can download a template with the correct format and upload the professor profiles defined in FCAS and FII documents.';
$string['tour_convocatorias_step7_title'] = 'Create New Call';
$string['tour_convocatorias_step7_content'] = 'Use this button to create a new call. Define the name, code, start and end dates, and terms and conditions. Once created, you can add vacancies individually or via CSV import.';
$string['tour_convocatorias_step8_title'] = 'Ready to Go!';
$string['tour_convocatorias_step8_content'] = 'You now understand how to manage calls in the Job Board. Remember: first create the call with its dates, then add the vacancies (manually or via CSV according to professional profiles). Once everything is set up, open the call to start receiving applications.';

// Tour: Document Types Management.
$string['tour_documents_name'] = 'Document Types Configuration Tour';
$string['tour_documents_description'] = 'Learn how to configure and manage document types that applicants must upload';
$string['tour_documents_step1_title'] = 'Document Types Management';
$string['tour_documents_step1_content'] = 'Welcome to the document types configuration page! Here you define what documents applicants must upload when applying for positions. Each document type specifies requirements, validation rules, and whether it is mandatory.';
$string['tour_documents_step2_title'] = 'Document Types Table';
$string['tour_documents_step2_content'] = 'This table shows all configured document types. Each type has a unique code, name, category, and status. You can see which documents are required versus optional, and any special conditions that apply.';
$string['tour_documents_step3_title'] = 'Document Categories';
$string['tour_documents_step3_content'] = 'Documents are organized into categories: identification (ID cards, military service), academic (degrees, certifications), employment (work certificates, CV), health (EPS, pension), financial (bank account, RUT), and legal (background checks).';
$string['tour_documents_step4_title'] = 'Special Conditions';
$string['tour_documents_step4_content'] = 'Some documents have special conditions: gender-specific requirements (e.g., military service for males only), profession exemptions (e.g., no professional card for education graduates), or maximum document age limits.';
$string['tour_documents_step5_title'] = 'Validation Checklist';
$string['tour_documents_step5_content'] = 'Each document type has a validation checklist that reviewers use to verify documents. This ensures consistent review criteria across all applications and helps maintain quality standards.';
$string['tour_documents_step6_title'] = 'Enable/Disable Documents';
$string['tour_documents_step6_content'] = 'You can enable or disable document types as needed. Disabled document types will not be required from applicants. Use this to adapt requirements for different recruitment campaigns.';

// Tour: Single Convocatoria Management.
$string['tour_convocatoria_manage_name'] = 'Call Management Detail Tour';
$string['tour_convocatoria_manage_description'] = 'Learn how to create and edit a job call with all its details and vacancies';
$string['tour_convocatoria_manage_step1_title'] = 'Call Detail Page';
$string['tour_convocatoria_manage_step1_content'] = 'This is the call (convocatoria) management page. From here you can configure all aspects of a job call: basic information, dates, terms, and the vacancies that belong to this call.';
$string['tour_convocatoria_manage_step2_title'] = 'Basic Information';
$string['tour_convocatoria_manage_step2_content'] = 'Enter the call code (unique identifier), name, and description. The code should follow your organization\'s naming convention (e.g., CONV-2024-001). The description helps applicants understand the purpose of this recruitment campaign.';
$string['tour_convocatoria_manage_step3_title'] = 'Call Dates';
$string['tour_convocatoria_manage_step3_content'] = 'Set the start and end dates for the call. Applications will only be accepted during this period. Make sure to leave enough time for applicants to gather required documents and submit their applications.';
$string['tour_convocatoria_manage_step4_title'] = 'Publication Type';
$string['tour_convocatoria_manage_step4_content'] = 'Choose whether the call is public (visible to everyone without login) or internal (only visible to authenticated users). Internal calls are useful for employee-only recruitment opportunities.';
$string['tour_convocatoria_manage_step5_title'] = 'Terms and Conditions';
$string['tour_convocatoria_manage_step5_content'] = 'Define the terms and conditions applicants must accept. This typically includes data privacy consent, accuracy of information declaration, and acceptance of the selection process rules.';
$string['tour_convocatoria_manage_step6_title'] = 'Associated Vacancies';
$string['tour_convocatoria_manage_step6_content'] = 'After creating the call, you can add vacancies to it. Vacancies inherit the call\'s date constraints. Each vacancy represents a specific position with its own requirements, based on the PERFILES PROFESORES documentation.';
$string['tour_convocatoria_manage_step7_title'] = 'Save and Publish';
$string['tour_convocatoria_manage_step7_content'] = 'Save your changes and when ready, change the call status from Draft to Open to start accepting applications. Remember: you can add or modify vacancies even after the call is open.';

// Convocatoria help strings.
$string['convocatoria_help'] = 'A call groups related job vacancies under a single campaign with defined start and end dates. When you create a vacancy, you can associate it with a call to organize your recruitment process. The call dates determine when applications can be submitted.';
$string['convocatoria_profile_help'] = 'Vacancies within a call should be created according to the professional profiles defined in the PERFILES PROFESORES documents. Each profile specifies the required qualifications, experience, and documents needed for that position.';
$string['convocatoriaid_help'] = 'Select the call to which this vacancy will belong. The vacancy will inherit the dates and conditions of the selected call.';
$string['convocatoriacode_help'] = 'Unique code to identify this call. Use a consistent format like CALL-2024-001.';
$string['convocatoriaterms_help'] = 'Enter the terms and conditions that applicants must accept when applying to vacancies in this call.';
$string['departmentid_help'] = 'Select the IOMAD department this vacancy will belong to for organizational filtering.';
$string['companyid_help'] = 'Select the company or site where this vacancy will be published. In multi-tenant environments, this determines visibility.';
$string['terms_help'] = 'You must accept the terms and conditions to continue with the process.';
$string['recaptcha_help'] = 'Complete the security verification to prove you are not a robot.';

// ============================================================================
// Dashboard redesign - Hierarchical navigation (Calls → Vacancies)
// ============================================================================

// Welcome messages.
$string['dashboard_admin_welcome'] = 'Welcome to the Job Board administration panel. From here you can manage calls, vacancies, applications and review documents.';
$string['dashboard_applicant_welcome'] = 'Welcome to the Job Board. Explore available vacancies and manage your applications from this panel.';

// Section titles.
$string['administracion'] = 'Administration';
$string['reviewertasks'] = 'Review Tasks';

// Statistics cards.
$string['publishedvacancies'] = 'Published vacancies';
$string['pendingreviews'] = 'Pending reviews';
$string['vacancies_dashboard_desc'] = 'Create, edit and manage vacancies. Control publication and assignment status of positions.';
$string['workflow_flow'] = 'Workflow';
$string['selection'] = 'Selection';
$string['gotoconvocatorias'] = 'Go to Calls';

// Review card.
$string['review_dashboard_desc'] = 'Review and validate applicant documents. Verify that documentation meets the established requirements.';
$string['pending_reviews_alert'] = 'You have {$a} pending document reviews that require your attention.';
$string['gotoreview'] = 'Go to Reviews';

// Reports card.
$string['reports_dashboard_desc'] = 'View statistics and reports on calls, vacancies, applications and processing times.';

// Configuration card.
$string['configuration'] = 'Configuration';
$string['config_dashboard_desc'] = 'Configure document types, email templates, workflows and other system options.';
$string['configure'] = 'Configure';

// Exemptions card.
$string['exemptions_dashboard_desc'] = 'Manage document exemptions for historical ISER personnel and other special categories.';

// Applicant section statistics.
$string['myapplicationcount'] = 'My applications';
$string['availablevacancies'] = 'Available vacancies';
$string['pendingdocs'] = 'Pending documents';

// Browse vacancies card.
$string['browservacancies'] = 'Browse Vacancies';
$string['browse_vacancies_desc'] = 'View vacancies published by the institution. Review requirements and apply to those that match your professional profile.';
$string['available_vacancies_alert'] = 'There are {$a} available vacancies that match your profile. Don\'t miss the opportunity to apply!';
$string['explorevacancias'] = 'View available vacancies';

// My applications card.
$string['myapplications_desc'] = 'Check the status of your applications, upload pending documents and track your selection processes.';
$string['pending_docs_alert'] = 'You have {$a} pending documents to upload or correct in your applications.';

// Reviewer section.
$string['myreviews_desc'] = 'View applications assigned for review and validate applicant documentation.';
$string['viewmyreviews'] = 'View my reviews';

// ============================================================================
// Additional help strings for tooltips
// ============================================================================

// Signup form help strings - titles.
$string['signup_email'] = 'Email Address';
$string['signup_phone'] = 'Phone Number';
$string['companyid'] = 'Company Selection';
$string['departmentid'] = 'Department Selection';

// Signup form help strings - content.
$string['signup_email_help'] = 'Enter a valid email address. It will be used to send your account confirmation and important notifications about the selection process.';
$string['signup_doctype_help'] = 'Select the type of identification document you will use for registration. This document must match the identification number you enter.';
$string['signup_birthdate_help'] = 'Select your date of birth. You must be at least 18 years old to apply for vacancies.';
$string['signup_phone_help'] = 'Enter your primary mobile phone number where we can contact you for notifications and interviews.';
$string['signup_education_level_help'] = 'Select the highest education level you have completed. This helps filter vacancies that match your academic profile.';

// Convocatoria form help strings.
$string['convocatorianame_help'] = 'Enter a descriptive name for the call. This name will be visible to applicants and should reflect the purpose of the call.';
$string['convocatoriadescription_help'] = 'Provide a detailed description of the call, including general information about vacancies, common requirements and the selection process.';
$string['convocatoriastartdate_help'] = 'Select the start date of the call. From this date, associated vacancies will be available to receive applications.';
$string['convocatoriaenddate_help'] = 'Select the closing date of the call. After this date, no more applications will be received for associated vacancies.';
$string['convocatoria_companyid'] = 'Company';
$string['convocatoria_companyid_help'] = 'Select the company or headquarters to which this call belongs. Select "All companies" if the call is institutional.';
$string['convocatoria_departmentid'] = 'Department';
$string['convocatoria_departmentid_help'] = 'Select the IOMAD department associated with this call. This helps organize vacancies by organizational structure.';

// Application form help strings.
$string['consentaccepted_help'] = 'By checking this box, you agree that your personal data will be processed in accordance with the institution\'s data protection policy for the purposes of the selection process.';
$string['declarationaccepted_help'] = 'By checking this box, you declare under oath that all information provided is true and that the attached documents are authentic.';

// ============================================================================
// Modern UI Redesign strings
// ============================================================================

// Error messages.
$string['error:convocatoriarequired'] = 'You must select a call (convocatoria). Vacancies must belong to a call.';

// Vacancy search and filters.
$string['searchvacancies'] = 'Search vacancies';
$string['allcontracttypes'] = 'All contract types';

// Vacancy detail page.
$string['closingsoondays'] = 'Closing in {$a} days! Apply now.';
$string['vacancyopen'] = 'This vacancy is open for applications';
$string['deadlineprogress'] = 'Application deadline progress';
$string['readytoapply'] = 'Ready to apply?';
$string['applynowdesc'] = 'Review the requirements and submit your application before the deadline.';
$string['applicationstats'] = 'Application Statistics';
$string['applied'] = 'Applied';
$string['total'] = 'Total';

// Navigation and flow.
$string['selectconvocatoriafirst'] = 'Please select a call first';
$string['createvacancyinconvocatoriadesc'] = 'Vacancies must belong to a call. Select or create a call first, then add vacancies.';
$string['noconvocatoriasavailable'] = 'No calls available. Create a call first to add vacancies.';
$string['gotocreateconvocatoria'] = 'Create a call';

// Dashboard cards.
$string['quickstats'] = 'Quick Statistics';
$string['pendingactions'] = 'Pending Actions';

// Review interface.
$string['reviewqueue'] = 'Review Queue';
$string['assignedtome'] = 'Assigned to me';
$string['pendingmyreview'] = 'Pending my review';
$string['completedreviews'] = 'Completed reviews';
$string['applicationprogress'] = 'Application Progress';

// Status flow.
$string['workflowstatus'] = 'Workflow Status';
$string['nextsteps'] = 'Next Steps';
$string['statusupdated'] = 'Status updated successfully';

// Accessibility.
$string['expandsection'] = 'Expand section';
$string['collapsesection'] = 'Collapse section';
$string['loadingcontent'] = 'Loading content...';
$string['actionrequired'] = 'Action required';

// Tooltips for redesigned interface.
$string['tooltip_viewdetails'] = 'Click to view full details';
$string['tooltip_quickapply'] = 'Start your application';
$string['tooltip_trackstatus'] = 'Track your application status';
$string['tooltip_downloadall'] = 'Download all documents';
$string['tooltip_filterresults'] = 'Filter results by criteria';

// ============================================================================
// Review interface redesign strings
// ============================================================================
$string['pendingreview'] = 'Pending Review';
$string['reviewapplication'] = 'Review Application';
$string['reviewprogress'] = 'Review Progress';
$string['alldocsreviewed'] = 'All documents have been reviewed';
$string['rejectreason_placeholder'] = 'Describe the reason for rejection...';

// Public page redesign strings.
$string['alllocations'] = 'All locations';
$string['allconvocatorias'] = 'All calls';
$string['openvacancies'] = 'Open Vacancies';
$string['logintoapply'] = 'Log in to apply for this position';
$string['allmodalities'] = 'All modalities';
$string['filtervacancies'] = 'Filter vacancies';
$string['publicpagedesc'] = 'Explore our available positions and find your next career opportunity.';
$string['closingsoon'] = 'closing soon';

// General.
$string['viewall'] = 'View all';
$string['urgent'] = 'Urgent';

// ============================================================================
// Accessibility and UX enhancement strings (v2.0.11)
// ============================================================================

// Screen reader and ARIA labels.
$string['clickfordetails'] = 'Click for details';
$string['trending_up'] = 'Trending up';
$string['trending_down'] = 'Trending down';
$string['progressindicator'] = 'Progress indicator';
$string['complete'] = 'complete';
$string['datatable'] = 'Data table';
$string['filterform'] = 'Filter form';
$string['resetfilters'] = 'Reset filters';
$string['uploadform'] = 'Upload form';
$string['requireddocument'] = 'Required document';
$string['issuedatehelp'] = 'Date the document was issued';
$string['uploading'] = 'Uploading...';
$string['select'] = 'Select';
$string['date'] = 'Date';

// Additional card and component labels.
$string['applyto'] = 'Apply to';
$string['editvacancy'] = 'Edit vacancy';
$string['logintoviewmore'] = 'Log in to view all available vacancies';

// Navigation and page structure.
$string['navigation'] = 'Navigation';
$string['vacancydetails'] = 'Vacancy details';
$string['pagination'] = 'Pagination';

// Social sharing.
$string['shareonlinkedin'] = 'Share on LinkedIn';
$string['shareonfacebook'] = 'Share on Facebook';
$string['shareontwitter'] = 'Share on Twitter';

// Accessibility and ARIA labels (v2.0.13).
$string['closealert'] = 'Close alert';
$string['adminstatistics'] = 'Administration statistics';
$string['applicantstatistics'] = 'Applicant statistics';
$string['features'] = 'Features';
$string['applicationstatistics'] = 'Application statistics';
$string['applicationlist'] = 'Application list';
$string['documentactions'] = 'Document actions';
$string['reviewstatistics'] = 'Review statistics';
$string['applicationsqueue'] = 'Applications queue';
$string['documentstoreview'] = 'Documents to review';
$string['at'] = 'at';
$string['performedby'] = 'Performed by';
$string['noteshelptext'] = 'Add any notes about the status change (visible to applicant)';
$string['warning'] = 'Warning';

// Missing strings identified in v2.0.13 audit.
$string['actions'] = 'Actions';
$string['applicationform'] = 'Application Form';
$string['applicationhelptext'] = 'If you need assistance with your application, please contact us.';
$string['applicationinfo'] = 'Application Information';
$string['approvalrate'] = 'Approval Rate';
$string['approved'] = 'Approved';
$string['cannotapply'] = 'You cannot apply for this vacancy.';
$string['changessaved'] = 'Changes saved successfully';
$string['contactus'] = 'Contact Us';
$string['daysremaining'] = 'Days remaining';
$string['inprogress'] = 'In Progress';
$string['issuedate'] = 'Issue Date';
$string['lastupdated'] = 'Last Updated';
$string['needhelp'] = 'Need Help?';
$string['noapplications'] = 'No applications';
$string['noapplicationsdesc'] = 'You have not submitted any applications yet. Browse available vacancies to get started.';
$string['noconvocatoriasdesc'] = 'There are no calls for applications at this time. Check back later for new opportunities.';
$string['nodata'] = 'No data available';
$string['nohistory'] = 'No history available';
$string['novacanciesyet'] = 'There are no vacancies yet';
$string['pendingdocsalert'] = 'You have pending documents that require attention.';
$string['pendingbytype'] = 'Pending by Type';
$string['percentage'] = 'Percentage';
$string['performance'] = 'Performance';
$string['showingresults'] = 'Showing results';
$string['trend'] = 'Trend';
$string['trydifferentfilters'] = 'Try adjusting your filters to see more results.';
$string['vacancysummary'] = 'Vacancy Summary';

// Pagination and bulk actions (v2.0.25).
$string['recordsperpage'] = 'Records per page';
$string['entries'] = 'entries';
$string['showingxofy'] = 'Showing {$a->start} to {$a->end} of {$a->total} entries';
$string['selectall'] = 'Select all';
$string['selected'] = 'selected';
$string['bulkactions'] = 'Bulk actions';
$string['bulkdelete'] = 'Delete selected';
$string['bulkpublish'] = 'Publish selected';
$string['bulkunpublish'] = 'Unpublish selected';
$string['bulkclose'] = 'Close selected';
$string['confirmdelete'] = 'Are you sure you want to delete the selected items? This action cannot be undone.';
$string['confirmpublish'] = 'Are you sure you want to publish the selected items?';
$string['confirmunpublish'] = 'Are you sure you want to unpublish the selected items?';
$string['confirmclose'] = 'Are you sure you want to close the selected items?';
$string['itemsdeleted'] = '{$a} item(s) deleted successfully';
$string['itemspublished'] = '{$a} item(s) published successfully';
$string['itemsunpublished'] = '{$a} item(s) unpublished successfully';
$string['itemsclosed'] = '{$a} item(s) closed successfully';
$string['noitemsselected'] = 'No items selected';
$string['confirmaction'] = 'Confirm action';
$string['bulkactionerrors'] = '{$a} item(s) could not be processed due to errors';

// CSV Import for vacancies.
$string['importvacancies'] = 'Import Vacancies from CSV';
$string['importvacancies_help'] = 'Upload a CSV file with the vacancies to import. Use the template to ensure correct format.';
$string['downloadcsvtemplate'] = 'Download CSV template';
$string['selectconvocatoria'] = '-- Select a convocatoria --';
$string['iomadoptions'] = 'IOMAD Options';
$string['createcompanies'] = 'Create companies automatically';
$string['createcompanies_help'] = 'If enabled, IOMAD companies will be automatically created based on locations in the CSV.';
$string['importoptions'] = 'Import options';
$string['defaultstatus'] = 'Default status';
$string['vacancy_status_draft'] = 'Draft';
$string['vacancy_status_published'] = 'Published';
$string['updateexisting'] = 'Update existing vacancies';
$string['updateexisting_help'] = 'If enabled, vacancies with the same code will be updated. Otherwise, they will be skipped.';
$string['previewmode'] = 'Preview mode - No changes have been made. Review the data and upload again without the preview option to import.';
$string['previewconfirm'] = 'Found {$a} vacancies to import. Upload the file again without "Preview only" to confirm the import.';
$string['uploadnewfile'] = 'Upload new file';
$string['vacancies_created'] = 'Vacancies created';
$string['vacancies_updated'] = 'Vacancies updated';
$string['vacancies_skipped'] = 'Vacancies skipped';
$string['importerror_vacancyexists'] = 'Row {$a->row}: Vacancy {$a->code} already exists';
$string['backtoconvocatorias'] = 'Back to Convocatorias';
$string['csvformat_desc'] = 'The CSV file must contain a header row with column names. Courses should be separated by the | (pipe) character.';
$string['csvcolumn_code'] = 'Unique vacancy code';
$string['csvcolumn_contracttype'] = 'Contract type (OCASIONAL TIEMPO COMPLETO or CATEDRA)';
$string['csvcolumn_program'] = 'Academic program';
$string['csvcolumn_profile'] = 'Required professional profile';
$string['csvcolumn_courses'] = 'Courses to teach (separated by |)';
$string['csvcolumn_location'] = 'Location (PAMPLONA, CUCUTA, TIBU, etc.)';
$string['csvcolumn_modality'] = 'Modality (PRESENCIAL or A DISTANCIA)';
$string['csvcolumn_faculty'] = 'Faculty (FCAS or FII)';
$string['csvexample'] = 'CSV Example';
$string['csvexample_desc'] = 'You can copy and paste this example as a base for creating your CSV file:';
$string['csvexample_tip'] = 'Courses are separated by the | (pipe) character. Valid locations are: PAMPLONA, CUCUTA, TIBU, SANVICENTE, ELTARRA, OCANA, PUEBLOBELLO, SANPABLO, SANTAROSA, TAME, FUNDACION, CIMITARRA, SALAZAR, TOLEDO.';
$string['column'] = 'Column';
$string['required'] = 'Required';
$string['example'] = 'Example';
$string['row'] = 'Row';
$string['andmore'] = 'And {$a} more...';

// ============================================================================
// Strings for improved apply view - 2025-12-09
// ============================================================================
$string['step_profile'] = 'Profile';
$string['step_consent'] = 'Consent';
$string['step_documents'] = 'Documents';
$string['step_coverletter'] = 'Letter';
$string['step_submit'] = 'Submit';
$string['profilereview'] = 'Review Profile';
$string['profilereview_info'] = 'Please verify your personal information below is correct before starting your application. If you need to make changes, click the "Edit Profile" button.';
$string['editprofile'] = 'Edit Profile';
$string['personalinfo'] = 'Personal Information';
$string['fullname'] = 'Full Name';
$string['education'] = 'Education';
$string['educationlevel'] = 'Education Level';
$string['applicationsteps_tooltip'] = 'Application progress. Complete each section in order.';
$string['deadlinewarning_title'] = 'Deadline approaching!';
$string['applyhelp_text'] = 'If you have questions about completing your application, start the guided tour or contact support.';
$string['restarttour'] = 'Start Guided Tour';
$string['documentchecklist'] = 'Document Checklist';

// ============================================================================
// Strings for improved review view - 2025-12-09
// ============================================================================
$string['reviewsteps_tooltip'] = 'Review progress: examine each document and approve or reject it.';
$string['reviewhelp_text'] = 'For each document, download and examine it carefully. Then approve if it meets requirements or reject with a clear explanation.';
$string['step_examine'] = 'Examine';
$string['step_validate'] = 'Validate';
$string['step_complete'] = 'Complete';
$string['reviewguidelines'] = 'Review Guidelines';
$string['guideline_review1'] = 'Download and open each document to verify its content and authenticity.';
$string['guideline_review2'] = 'Check that documents are legible, complete, and match the required type.';
$string['guideline_review3'] = 'When rejecting a document, provide a clear reason so the applicant knows what to fix.';
$string['guideline_review4'] = 'Complete all document reviews before marking the application as reviewed.';
$string['quickactions'] = 'Quick Actions';
$string['approveall_confirm'] = 'Are you sure you want to approve all pending documents? This action cannot be undone.';
$string['documentchecklist_reviewer'] = 'Documents to Review';
$string['reviewtips'] = 'Review Tips';
$string['tip_download'] = 'Download each document to verify its content';
$string['tip_legible'] = 'Ensure documents are clear and legible';
$string['tip_complete'] = 'Check that all required information is present';
$string['tip_authentic'] = 'Verify document authenticity when possible';
$string['needsattention'] = 'Needs Attention';
$string['allclear'] = 'All Clear';
$string['documentsremaining'] = '{$a} document(s) remaining';
$string['reviewcompletetooltip'] = 'All documents reviewed. Click to complete the review process.';

// ============================================================================
// UX Improvement Strings - 2025-12-09
// Navigation & Feedback
// ============================================================================
$string['invalidview'] = 'The requested view does not exist.';
$string['redirectedtodashboard'] = 'You have been redirected to the dashboard.';
$string['actioncompleted'] = 'Action completed successfully.';
$string['actionfailed'] = 'The action could not be completed. Please try again.';
$string['loadinginprogress'] = 'Loading...';
$string['processingrequest'] = 'Processing your request...';
$string['pleasewait'] = 'Please wait...';

// Signup Progress Steps
$string['signup_step_account'] = 'Account';
$string['signup_step_personal'] = 'Personal Info';
$string['signup_step_contact'] = 'Contact';
$string['signup_step_academic'] = 'Education';
$string['signup_step_company'] = 'Company';
$string['signup_step_confirm'] = 'Confirm';
$string['signup_progress'] = 'Registration Progress';
$string['signup_fields_required'] = '{$a} required fields';
$string['signup_section_complete'] = 'Section complete';
$string['signup_section_incomplete'] = 'Section incomplete';

// Dashboard Welcome
$string['welcomeback'] = 'Welcome back, {$a}!';
$string['dashboardwelcome'] = 'What would you like to do today?';
$string['dashboardwelcome_candidate'] = 'Find and apply for job opportunities that match your profile.';
$string['dashboardwelcome_employer'] = 'Manage your vacancies and review candidate applications.';
$string['thisweek'] = 'this week';
$string['thismonth'] = 'this month';
$string['noactivity'] = 'No recent activity';
$string['quickactions_title'] = 'Quick Actions';
$string['recentactivity'] = 'Recent Activity';
$string['pendingitems'] = 'Pending Items';

// Application Process
$string['applyingto'] = 'You are applying to:';
$string['applicationfor'] = 'Application for: {$a}';
$string['closingsoon'] = 'Closes in {$a} days';
$string['closingtoday'] = 'Closes today!';
$string['closingtomorrow'] = 'Closes tomorrow';
$string['alreadyclosed'] = 'This vacancy is closed';
$string['documentsprogress'] = 'Documents uploaded';
$string['documentsprogress_detail'] = '{$a->uploaded} of {$a->total} documents';
$string['alldocumentsuploaded'] = 'All required documents uploaded';
$string['missingdocuments'] = '{$a} required document(s) missing';

// Application Confirmation Modal
$string['confirmapplication'] = 'Confirm your application';
$string['confirmapplication_title'] = 'Ready to submit?';
$string['confirmapplication_text'] = 'Please verify the following before submitting your application:';
$string['confirmapplication_docs'] = 'All required documents have been uploaded';
$string['confirmapplication_data'] = 'Your personal information is accurate and up-to-date';
$string['confirmapplication_consent'] = 'You have read and accepted the terms and conditions';
$string['confirmapplication_final'] = 'Once submitted, you cannot modify your application. Are you sure you want to proceed?';
$string['confirmsubmit'] = 'Yes, submit my application';
$string['cancelsubmit'] = 'Cancel, let me review';
$string['applicationsubmitting'] = 'Submitting your application...';
$string['applicationsubmitted_success'] = 'Your application has been submitted successfully!';
$string['applicationsubmitted_next'] = 'You will receive a confirmation email shortly. You can track your application status in "My Applications".';

// Document Status
$string['docstatus_pending'] = 'Pending upload';
$string['docstatus_uploading'] = 'Uploading...';
$string['docstatus_uploaded'] = 'Uploaded';
$string['docstatus_validating'] = 'Under review';
$string['docstatus_approved'] = 'Approved';
$string['docstatus_rejected'] = 'Rejected';
$string['docstatus_expired'] = 'Expired';
$string['docupload_success'] = 'Document uploaded successfully';
$string['docupload_error'] = 'Error uploading document';
$string['docremove_confirm'] = 'Are you sure you want to remove this document?';

// Review Panel
$string['reviewprogress'] = 'Review Progress';
$string['reviewprogress_detail'] = '{$a->reviewed} of {$a->total} documents reviewed';
$string['documentsapproved'] = 'Approved';
$string['documentsrejected'] = 'Rejected';
$string['documentspending'] = 'Pending';
$string['reviewcomplete'] = 'Review Complete';
$string['reviewincomplete'] = 'Review Incomplete';
$string['startreview'] = 'Start Review';
$string['continuereview'] = 'Continue Review';
$string['reviewsummary'] = 'Review Summary';

// Keyboard Shortcuts
$string['keyboardshortcuts'] = 'Keyboard Shortcuts';
$string['shortcut_approve'] = 'Approve document';
$string['shortcut_reject'] = 'Reject document';
$string['shortcut_next'] = 'Next document';
$string['shortcut_previous'] = 'Previous document';
$string['shortcut_save'] = 'Save changes';
$string['shortcut_help'] = 'Show shortcuts help';
$string['shortcutshelp_title'] = 'Available Keyboard Shortcuts';

// Terms Summary
$string['termssummary'] = 'Key Points';
$string['termssummary_intro'] = 'By submitting this application, you agree to:';
$string['termssummary_1'] = 'Your data will be processed according to our privacy policy';
$string['termssummary_2'] = 'You can request deletion of your data at any time';
$string['termssummary_3'] = 'Your application may be shared with authorized hiring personnel';
$string['termssummary_4'] = 'All information provided must be accurate and truthful';
$string['viewfullterms'] = 'View full terms and conditions';
$string['hidefullterms'] = 'Hide full terms';

// Form Validation
$string['validating'] = 'Validating...';
$string['fieldvalid'] = 'This field is valid';
$string['fieldinvalid'] = 'Please check this field';
$string['formhasserrors'] = 'Please correct the errors before continuing';
$string['allfieldsvalid'] = 'All fields are valid';

// Accessibility
$string['skiptomaincontent'] = 'Skip to main content';
$string['skiptoform'] = 'Skip to form';
$string['clickfordetails'] = 'Click for details';
$string['expandsection'] = 'Expand section';
$string['collapsesection'] = 'Collapse section';
$string['opensinnewwindow'] = 'Opens in new window';
$string['requiredfield'] = 'Required field';
$string['optionalfield'] = 'Optional field';
$string['currentstep'] = 'Current step';
$string['completedstep'] = 'Completed step';
$string['pendingstep'] = 'Pending step';

// Loading States
$string['loading_vacancies'] = 'Loading vacancies...';
$string['loading_applications'] = 'Loading applications...';
$string['loading_documents'] = 'Loading documents...';
$string['loading_data'] = 'Loading data...';
$string['savingchanges'] = 'Saving changes...';
$string['uploadingfile'] = 'Uploading file...';

// Empty States
$string['novacancies_candidate'] = 'No vacancies available at the moment. Check back later for new opportunities.';
$string['noapplications_candidate'] = 'You haven\'t applied to any vacancies yet. Browse available positions to get started.';
$string['noapplications_employer'] = 'No applications received yet for this vacancy.';
$string['nodocuments_review'] = 'No documents pending review.';

// Success Messages
$string['changesaved'] = 'Your changes have been saved.';
$string['documentsaved'] = 'Document saved successfully.';
$string['applicationsaved'] = 'Application saved as draft.';
$string['reviewsaved'] = 'Review saved successfully.';

// ============================================================================
// Navigation UX improvements (v2.0.64)
// ============================================================================
$string['backtovacancy'] = 'Back to vacancy';
$string['backtodashboard'] = 'Back to Dashboard';
$string['backtoreviewlist'] = 'Back to review list';

// Unsaved changes warning.
$string['unsavedchanges'] = 'Unsaved changes';
$string['unsavedchangeswarning'] = 'You have unsaved changes. Are you sure you want to leave this page? Your changes will be lost.';
$string['leave'] = 'Leave';
$string['stay'] = 'Stay';

// Application help sidebar enhancements.
$string['quicktips'] = 'Quick Tips';
$string['tip_saveoften'] = 'Complete all required fields before submitting';
$string['tip_checkdocs'] = 'Ensure documents are clear and legible';
$string['tip_deadline'] = 'Submit before the deadline expires';
$string['viewfaq'] = 'Frequently Asked Questions';
$string['contactsupport'] = 'Contact Support';
$string['viewvacancydetails'] = 'View vacancy details';

// Document categories.
$string['doccat_employment'] = 'Employment';
$string['doccat_employment_desc'] = 'Work history and employment-related documents';
$string['doccat_identification'] = 'Identification';
$string['doccat_identification_desc'] = 'Personal identification documents';
$string['doccat_academic'] = 'Academic';
$string['doccat_academic_desc'] = 'Degrees, certifications and academic records';
$string['doccat_financial'] = 'Financial';
$string['doccat_financial_desc'] = 'Tax and banking documents';
$string['doccat_health'] = 'Health & Social Security';
$string['doccat_health_desc'] = 'Health insurance and pension affiliation';
$string['doccat_legal'] = 'Legal Background';
$string['doccat_legal_desc'] = 'Background checks and legal clearances';

// Document field labels.
$string['docrequirements'] = 'View requirements';
$string['optional'] = 'Optional';

// Multiple documents notice.
$string['multipledocs_notice'] = 'Multiple certificates in one file';
$string['multipledocs_titulo_academico'] = 'If you have multiple degrees (undergraduate, postgraduate, specialization), combine all certificates into a single PDF file.';
$string['multipledocs_formacion_complementaria'] = 'If you have multiple complementary training certificates, combine all into a single PDF file.';
$string['multipledocs_certificacion_laboral'] = 'If you have multiple employment certificates, combine all into a single PDF file ordered by date (most recent first).';

// ============================================================================
// reCAPTCHA and Security Settings (v2.0.71)
// ============================================================================
$string['recaptchasettings'] = 'reCAPTCHA Settings';
$string['recaptchasettings_desc'] = 'Configure reCAPTCHA to prevent spam and abuse on registration and profile update forms.';
$string['recaptcha_enabled'] = 'Enable reCAPTCHA';
$string['recaptcha_enabled_desc'] = 'Enable reCAPTCHA verification on signup and profile update forms.';
$string['recaptcha_version'] = 'reCAPTCHA Version';
$string['recaptcha_version_desc'] = 'Select which version of reCAPTCHA to use.';
$string['recaptcha_v2'] = 'reCAPTCHA v2 (Checkbox)';
$string['recaptcha_v3'] = 'reCAPTCHA v3 (Invisible)';
$string['recaptcha_sitekey'] = 'Site Key';
$string['recaptcha_sitekey_desc'] = 'Enter the reCAPTCHA site key from Google reCAPTCHA console.';
$string['recaptcha_secretkey'] = 'Secret Key';
$string['recaptcha_secretkey_desc'] = 'Enter the reCAPTCHA secret key from Google reCAPTCHA console.';
$string['recaptcha_v3_threshold'] = 'v3 Score Threshold';
$string['recaptcha_v3_threshold_desc'] = 'Minimum score (0.0-1.0) required to pass verification. Default: 0.5';
$string['recaptcha_required'] = 'Please complete the security verification.';
$string['recaptcha_failed'] = 'Security verification failed. Please try again.';
$string['verification'] = 'Security Verification';

// ============================================================================
// Account Credentials Management (v2.0.71)
// ============================================================================
$string['username_differs_idnumber'] = 'Your username is different from your ID number. You can update it to match your identification number for easier login.';
$string['update_username'] = 'Update username';
$string['update_username_desc'] = 'Change my username to match my ID number';
$string['password_change_optional'] = 'Leave the password fields empty if you don\'t want to change your password. Fill in all fields only if you want to set a new password.';
$string['currentpassword'] = 'Current password';
$string['currentpassword_help'] = 'Enter your current password to verify your identity. This is required if you want to change your email or password.';
$string['confirmpassword'] = 'Confirm new password';
$string['currentpassword_required'] = 'Current password is required to change email or password.';
$string['currentpassword_invalid'] = 'The current password you entered is incorrect.';
$string['passwordsdiffer'] = 'The passwords do not match.';
$string['email_updated'] = 'Your email address has been updated.';
$string['password_updated'] = 'Your password has been updated.';
$string['username_updated'] = 'Your username has been updated to: {$a}';
$string['completeprofile_required'] = 'Please complete your profile information before applying for this vacancy.';

// ============================================================================
// Phase 10 - Version 2.1.0: Major Refactoring
// ============================================================================

// Vacancy dates now come from convocatoria.
$string['vacancy_inherits_dates'] = 'Vacancy dates are inherited from the selected convocatoria. To modify dates, edit the convocatoria.';
$string['legacyconvocatoria'] = 'Legacy Convocatoria';
$string['legacyconvocatoria_desc'] = 'Auto-created convocatoria for vacancies without an assigned convocatoria during the upgrade to v2.1.0.';

// Application limits per convocatoria.
$string['applicationlimits'] = 'Application Limits';
$string['allowmultipleapplications_convocatoria'] = 'Allow Multiple Applications';
$string['allowmultipleapplications_convocatoria_desc'] = 'Allow applicants to apply for multiple vacancies within this call';
$string['allowmultipleapplications_convocatoria_help'] = 'If enabled, applicants can submit applications for more than one vacancy in this call. You can set a maximum limit below.';
$string['maxapplicationsperuser'] = 'Maximum applications per user';
$string['maxapplicationsperuser_help'] = 'Maximum number of applications a single user can submit in this call. Set to 0 for unlimited. Only applies when multiple applications are allowed.';
$string['error:singleapplicationonly'] = 'The call "{$a}" only allows one application per person. Your current application must be finalized (selected, rejected, or withdrawn) before you can apply for another vacancy in this call.';
$string['error:applicationlimitreached'] = 'You have reached the maximum number of applications ({$a->max}) for the call "{$a->convocatoria}". You currently have {$a->current} active application(s).';
$string['singleapplication_notice'] = 'This call only allows one application per person. Choose carefully which vacancy to apply for.';
$string['maxapplications_notice'] = 'This call allows a maximum of {$a} applications per person.';

// Experience requirement for occasional contracts.
$string['error:occasionalrequiresexperience'] = 'Occasional contracts require at least 2 years of full-time equivalent work experience.';

// Tarjeta profesional conditional note.
$string['tarjeta_profesional_note'] = 'The professional license is optional. However, it is MANDATORY for: Engineering, Architecture, Law, Medicine, Nursing, Accounting, Psychology, and other regulated professions according to Colombian law.';

// Conditional document notes.
$string['conditional_document_note'] = 'Note: {$a}';
$string['document_age_exemption'] = 'Exempt due to age';

// Age exemption.
$string['age_exempt_notice'] = 'This document is not required for applicants aged {$a} years or older.';

// Audit actions.
$string['audit_action_create'] = 'Create';
$string['audit_action_update'] = 'Update';
$string['audit_action_delete'] = 'Delete';
$string['audit_action_view'] = 'View';
$string['audit_action_download'] = 'Download';
$string['audit_action_validate'] = 'Validate';
$string['audit_action_reject'] = 'Reject';
$string['audit_action_submit'] = 'Submit';
$string['audit_action_transition'] = 'Status change';
$string['audit_action_email_sent'] = 'Email sent';
$string['audit_action_login'] = 'Login';
$string['audit_action_export'] = 'Export';
$string['audit_action_upload'] = 'Upload';

// Audit entities.
$string['audit_entity_vacancy'] = 'Vacancy';
$string['audit_entity_application'] = 'Application';
$string['audit_entity_document'] = 'Document';
$string['audit_entity_exemption'] = 'Exemption';
$string['audit_entity_convocatoria'] = 'Call';
$string['audit_entity_config'] = 'Configuration';
$string['audit_entity_user'] = 'User';
$string['audit_entity_email_template'] = 'Email Template';

// Email templates.
$string['emailtemplates'] = 'Email Templates';
$string['emailtemplates_desc'] = 'Customize the email notifications sent by the Job Board system.';
$string['emailtemplates_help'] = 'Customize the email notifications sent to applicants. Use placeholders to include dynamic content. Changes apply immediately.';
$string['emailtemplate'] = 'Email Template';
$string['templatekey'] = 'Template Key';
$string['templatesubject'] = 'Subject';
$string['templatebody'] = 'Message Body';
$string['availableplaceholders'] = 'Available Placeholders';
$string['placeholders_help'] = 'Insert these placeholders in the subject or body. They will be replaced with actual values when the email is sent.';
$string['templateenabled'] = 'Template enabled';
$string['resettodefault'] = 'Reset to default';
$string['restoreddefault'] = 'Restore Default';
$string['templateupdated'] = 'Email template updated successfully';
$string['emailtemplate_saved'] = 'Email template saved successfully.';
$string['emailtemplate_restored'] = 'Template restored to default values.';
$string['templatereset'] = 'Template has been reset to default values';
$string['previewemail'] = 'Preview Email';
$string['managetemplates'] = 'Manage email templates';
$string['notemplates'] = 'No email templates found. Templates will be created automatically when needed.';
$string['customized'] = 'Customized';
$string['customize'] = 'Customize';
$string['default'] = 'Default';
$string['backtotemplates'] = 'Back to templates';
$string['confirmreset'] = 'Are you sure you want to reset this template to its default values? This action cannot be undone.';
$string['quickhelp'] = 'Quick Help';
$string['templatehelp_placeholders'] = 'Use placeholders like {fullname} or {vacancy_title} to insert dynamic content.';
$string['templatehelp_html'] = 'You can use HTML formatting in the email body for rich text emails.';
$string['templatehelp_reset'] = 'Click "Reset" on a customized template to restore the default text.';
$string['preview'] = 'Preview';
$string['preview_hint'] = 'The preview shows how your email will look with sample data. Edit the template and see changes in real-time.';
$string['livepreview'] = 'Live Preview';
$string['preview_loading'] = 'Loading preview...';
$string['templatesubject_help'] = 'Enter the email subject line. You can use placeholders which will be replaced with actual values.';
$string['templatebody_help'] = 'Enter the email body content. Use the HTML editor for formatting and include placeholders for dynamic content.';

// Template names.
$string['template_application_received'] = 'Application Received';
$string['template_docs_validated'] = 'Documents Validated';
$string['template_docs_rejected'] = 'Documents Rejected';
$string['template_review_complete'] = 'Review Complete';
$string['template_interview'] = 'Interview Scheduled';
$string['template_selected'] = 'Application Selected';
$string['template_rejected'] = 'Application Rejected';

// Default email template strings (used as fallback).
$string['email_application_received_subject'] = 'Application Received - {vacancy_code}';
$string['email_application_received_body'] = 'Dear {fullname},

Your application for "{vacancy_title}" (Code: {vacancy_code}) has been received.

You can track your application status at:
{application_url}

Best regards,
{sitename}';

$string['email_docs_validated_subject'] = 'Documents Validated - {vacancy_code}';
$string['email_docs_validated_body'] = 'Dear {fullname},

Your documents for the application to "{vacancy_title}" have been validated.

You can view your application at:
{application_url}

Best regards,
{sitename}';

$string['email_docs_rejected_subject'] = 'Document Review - Action Required - {vacancy_code}';
$string['email_docs_rejected_body'] = 'Dear {fullname},

Some documents for your application to "{vacancy_title}" require attention.

Rejected documents:
{rejected_docs}

Observations:
{observations}

Please review and reupload at:
{application_url}

Best regards,
{sitename}';

$string['email_review_complete_subject'] = 'Document Review Complete - {vacancy_code}';
$string['email_review_complete_body'] = 'Dear {fullname},

The review of your documents for "{vacancy_title}" has been completed.

{summary}

{action_required}

View details at:
{application_url}

Best regards,
{sitename}';

$string['email_interview_subject'] = 'Interview Scheduled - {vacancy_code}';
$string['email_interview_body'] = 'Dear {fullname},

You have been scheduled for an interview for "{vacancy_title}".

Date: {interview_date}
Location: {interview_location}

{interview_notes}

Best regards,
{sitename}';

$string['email_selected_subject'] = 'Congratulations! - {vacancy_code}';
$string['email_selected_body'] = 'Dear {fullname},

Congratulations! You have been selected for "{vacancy_title}".

{notes}

Best regards,
{sitename}';

$string['email_rejected_subject'] = 'Application Update - {vacancy_code}';
$string['email_rejected_body'] = 'Dear {fullname},

Thank you for your interest in "{vacancy_title}".

After careful consideration, we regret to inform you that your application was not successful this time.

{notes}

We encourage you to apply for future opportunities.

Best regards,
{sitename}';

// Review complete notification.
$string['notification_review_complete_subject'] = 'Document Review Complete - {$a->vacancy}';
$string['notification_review_complete_body'] = '<p>Dear {$a->user_name},</p>

<p>The review of your documents for the vacancy <strong>{$a->vacancy_title}</strong> has been completed.</p>

<p><strong>The following documents require corrections:</strong></p>
<ul>
{$a->documents_list}
</ul>

<p>Please upload corrected versions of these documents at:<br>
<a href="{$a->reupload_url}">{$a->reupload_url}</a></p>

<p>Once you have uploaded the corrected documents, they will be reviewed again.</p>

<p>Sincerely,<br>
{$a->site_name}</p>';

// Review notification strings.
$string['reviewsubmitted_with_notification'] = 'Review submitted and applicant notified.';
$string['reviewhasrejected'] = '{$a} document(s) rejected. The applicant will need to resubmit.';
$string['reviewallapproved'] = 'All documents approved. The applicant will proceed to the next step.';
$string['reviewobservations'] = 'Observations (optional)';
$string['reviewobservations_placeholder'] = 'Enter any additional comments for the applicant...';
$string['applicantwillbenotified'] = 'The applicant will receive an email notification.';
$string['noobservations'] = 'No additional observations.';
$string['noreason'] = 'No reason provided.';
$string['none'] = 'None';
$string['documentsreviewed'] = 'Documents reviewed';
$string['documentsapproved'] = 'Documents approved';
$string['documentsrejected'] = 'Documents rejected';
$string['rejecteddocuments'] = 'Rejected documents';
$string['email_action_reupload'] = 'Please review the rejected documents and upload corrected versions.';
$string['viewapplication'] = 'View Application';

// Message provider for document review.
$string['messageprovider:documentreview'] = 'Document review notifications';

// Export strings.
$string['novacanciesinconvocatoria'] = 'No vacancies found in this call for applications.';
$string['novacanciesforcompany'] = 'No vacancies found for this company.';
$string['companynotfound'] = 'Company not found.';
$string['exportdocuments'] = 'Export Documents';
$string['exportbyconvocatoria'] = 'Export by Call';
$string['exportbycompany'] = 'Export by Company';
$string['exportmanifest'] = 'Export Manifest';

// Apply page enhancements.
$string['clicktojump'] = 'Click to jump to this section';

// Email template editor (live preview).
$string['backtotemplates'] = 'Back to templates';

// Tab-based application form.
$string['submit'] = 'Submit Application';
$string['step'] = 'Step';
$string['of'] = 'of';
$string['completerequiredfields'] = 'Please complete all required fields before continuing.';

// ============================================================================
// Document Types CRUD (Phase 10F)
// ============================================================================
$string['adddoctype'] = 'Add Document Type';
$string['editdoctype'] = 'Edit Document Type';
$string['confirmdeletedoctype'] = 'Confirm Delete Document Type';
$string['confirmdeletedoctype_msg'] = 'Are you sure you want to delete the document type "{$a}"? This action cannot be undone.';
$string['error:doctypeinuse'] = 'Cannot delete: this document type is used by {$a} document(s).';
$string['error:codealreadyexists'] = 'A document type with this code already exists.';
$string['error:invalidcode'] = 'Invalid code format. Use only letters, numbers, and underscores, starting with a letter.';
$string['error:invalidage'] = 'Age must be between 18 and 100 years.';
$string['error:invalidurl'] = 'Please enter a valid URL.';
$string['basicinfo'] = 'Basic Information';
$string['sortorder'] = 'Sort Order';
$string['externalurl'] = 'External URL';
$string['externalurl_help'] = 'URL where applicants can obtain this document (e.g., government website).';
$string['validationrequirements'] = 'Validation Requirements';
$string['defaultmaxagedays'] = 'Maximum Document Age (days)';
$string['defaultmaxagedays_help'] = 'Maximum number of days since the document issue date. Leave empty for no limit.';
$string['exemptions'] = 'Exemptions & Conditions';
$string['iserexempted'] = 'ISER Staff Exempt';
$string['iserexempted_help'] = 'Historic ISER staff are exempt from providing this document.';
$string['gendercondition'] = 'Gender Condition';
$string['gendercondition_help'] = 'Restrict this document requirement to a specific gender.';
$string['allapplicants'] = 'All Applicants';
$string['menonly'] = 'Men Only';
$string['womenonly'] = 'Women Only';
$string['ageexemptionthreshold'] = 'Age Exemption Threshold';
$string['ageexemptionthreshold_help'] = 'Applicants at or above this age are exempt from this document. Example: 50 for military service card.';
$string['professionexempt'] = 'Education Levels Exempt';
$string['professionexempt_help'] = 'Education levels that are exempt from this document requirement.';
$string['conditionalnote'] = 'Conditional Note';
$string['conditionalnote_help'] = 'Explanatory note shown when this document is conditional (e.g., "Only required if you have a professional title").';
$string['configuration'] = 'Configuration';
$string['checklistitems'] = 'Validation Checklist Items';
$string['checklistitems_help'] = 'Enter checklist items for reviewers, one per line. These items will be shown when validating documents of this type.';
$string['doctype_isrequired_help'] = 'This document is required for all standard applications.';
$string['hasnote'] = 'Has Note';
$string['doctypeshelp'] = 'Document types define what documents applicants need to upload. Configure requirements, exemptions, and validation rules here.';
$string['totaldoctypes'] = 'Total Document Types';
$string['enableddoctypes'] = 'Enabled Types';
$string['requireddoctypes'] = 'Required Types';
$string['conditionaldoctypes'] = 'Conditional Types';
$string['aboutdoctypes'] = 'About Document Types';
$string['doctypelist'] = 'Document Type List';
$string['items'] = 'items';

// Document categories.
$string['doccategory_identity'] = 'Identity';
$string['doccategory_academic'] = 'Academic';
$string['doccategory_professional'] = 'Professional';
$string['doccategory_background'] = 'Background Checks';
$string['doccategory_financial'] = 'Financial';
$string['doccategory_health'] = 'Health';

// ============================================================================
// Public Convocatoria View (Phase 10K)
// ============================================================================
$string['loginrequired'] = 'Login Required';
$string['loginrequired_desc'] = 'To apply for positions in this call, you need to create an account or log in with your existing credentials.';
$string['error:convocatorianotfound'] = 'The requested call for applications was not found.';
$string['error:convocatoriaclosed'] = 'This call for applications is no longer accepting submissions.';
$string['nopublicvacancies'] = 'No public vacancies available in this call at the moment.';
$string['viewdetails'] = 'View Details';
$string['vacanciesavailable'] = 'There are {$a} vacancies available in this call';

// Dashboard strings.
$string['role_administrator'] = 'Administrator';
$string['role_manager'] = 'Manager';
$string['role_applicant'] = 'Applicant';
$string['dashboard_manager_welcome'] = 'Manage calls, vacancies and review applications.';
$string['dashboard_reviewer_welcome'] = 'Review documents and evaluate assigned candidates.';
$string['viewpublicpage'] = 'View public page';
$string['overview'] = 'Overview';
$string['contentmanagement'] = 'Content Management';
$string['active'] = 'Active';
$string['draft'] = 'Draft';
$string['viewall'] = 'View All';
$string['addnew'] = 'Add New';
$string['pending'] = 'Pending';
$string['total'] = 'Total';
$string['reviewall'] = 'Review All';
$string['reportsanddata'] = 'Reports & Data';
$string['exportdata'] = 'Export Data';
$string['exportdata_desc'] = 'Export applications and documents data';
$string['systemconfiguration'] = 'System Configuration';
$string['emailtemplates'] = 'Email Templates';
$string['emailtemplates_desc'] = 'Configure email notification templates';
$string['exemptions'] = 'Exemptions';
$string['adminonly'] = 'Admin Only';
$string['reviewoverview'] = 'Review Overview';
$string['mypendingreviews'] = 'My Pending Reviews';
$string['completedreviews'] = 'Completed Reviews';
$string['pendingreviews_alert'] = 'You have {$a} pending reviews';
$string['allapplications'] = 'All Applications';
$string['allapplications_desc'] = 'View and search all applications in the system';
$string['explore'] = 'Explore';
$string['welcometojobboard'] = 'Welcome to the Job Board';
$string['vieweronly_desc'] = 'You currently only have access to view public vacancies available.';
$string['viewpublicvacancies'] = 'View Public Vacancies';

// Dashboard - Workflow Management.
$string['workflowmanagement'] = 'Workflow Management';
$string['assignreviewers_desc'] = 'Assign reviewers to applications and vacancies';
$string['bulkvalidation_desc'] = 'Validate multiple documents at once';
$string['committees'] = 'Selection Committees';
$string['committees_desc'] = 'Manage selection committee members';
$string['committees_access_hint'] = 'Access from vacancy management';
$string['apitokens_desc'] = 'Manage API access tokens for external integrations';

// Public view - Role-based quick access.
$string['sharepage'] = 'Share this page';

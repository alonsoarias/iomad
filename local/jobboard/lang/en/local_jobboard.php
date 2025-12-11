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
 * Language strings for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin general.
$string['pluginname'] = 'Job Board';
$string['jobboard'] = 'Job Board';
$string['jobboard:view'] = 'View job board';
$string['jobboard:apply'] = 'Apply to vacancies';
$string['jobboard:configure'] = 'Configure job board';
$string['jobboard:createvacancy'] = 'Create vacancies';
$string['jobboard:editvacancy'] = 'Edit vacancies';
$string['jobboard:publishvacancy'] = 'Publish vacancies';
$string['jobboard:viewallvacancies'] = 'View all vacancies';
$string['jobboard:manageconvocatorias'] = 'Manage convocatorias';
$string['jobboard:reviewdocuments'] = 'Review documents';
$string['jobboard:validatedocuments'] = 'Validate documents';
$string['jobboard:assignreviewers'] = 'Assign reviewers';
$string['jobboard:evaluate'] = 'Evaluate applications';
$string['jobboard:viewevaluations'] = 'View evaluations';
$string['jobboard:viewallapplications'] = 'View all applications';
$string['jobboard:changeapplicationstatus'] = 'Change application status';
$string['jobboard:viewreports'] = 'View reports';
$string['jobboard:exportreports'] = 'Export reports';
$string['jobboard:exportdata'] = 'Export data';
$string['jobboard:viewownapplications'] = 'View own applications';
$string['jobboard:viewinternalvacancies'] = 'View internal vacancies';
$string['jobboard:managedoctypes'] = 'Manage document types';
$string['jobboard:manageemailtemplates'] = 'Manage email templates';
$string['jobboard:manageexemptions'] = 'Manage exemptions';
$string['jobboard:manageworkflow'] = 'Manage workflow';

// Dashboard.
$string['dashboard'] = 'Dashboard';
$string['administracion'] = 'Administration';
$string['adminstatistics'] = 'Administration statistics';
$string['applicantstatistics'] = 'Your statistics';
$string['notifications'] = 'Notifications';
$string['features'] = 'Features';

// Role labels.
$string['role_administrator'] = 'Administrator';
$string['role_manager'] = 'Manager';
$string['role_reviewer'] = 'Reviewer';
$string['role_applicant'] = 'Applicant';

// Dashboard welcome messages.
$string['dashboard_admin_welcome'] = 'Full access to manage convocatorias, vacancies, and system configuration.';
$string['dashboard_manager_welcome'] = 'Manage convocatorias, vacancies and review applications.';
$string['dashboard_reviewer_welcome'] = 'Review and validate documents from applicants assigned to you.';
$string['dashboard_applicant_welcome'] = 'Browse available vacancies and manage your applications.';

// Dashboard sections.
$string['workflowmanagement'] = 'Workflow Management';
$string['reportsanddata'] = 'Reports and Data';
$string['systemconfiguration'] = 'System Configuration';
$string['reviewertasks'] = 'Reviewer Tasks';

// Statistics labels.
$string['activeconvocatorias'] = 'Active Calls';
$string['publishedvacancies'] = 'Published Vacancies';
$string['totalapplications'] = 'Total Applications';
$string['pendingreviews'] = 'Pending Reviews';
$string['availablevacancies'] = 'Available Vacancies';
$string['myapplicationcount'] = 'My Applications';
$string['pendingdocs'] = 'Pending Documents';

// Convocatorias.
$string['convocatorias'] = 'Calls for Applications';
$string['convocatoria'] = 'Call for Applications';
$string['convocatorias_dashboard_desc'] = 'Create and manage calls for applications with multiple vacancies.';
$string['newconvocatoria'] = 'New Call';
$string['editconvocatoria'] = 'Edit Call';
$string['deleteconvocatoria'] = 'Delete Call';
$string['convocatoria_status_draft'] = 'Draft';
$string['convocatoria_status_open'] = 'Open';
$string['convocatoria_status_closed'] = 'Closed';
$string['convocatoria_status_archived'] = 'Archived';
$string['browseconvocatorias'] = 'Browse Calls';
$string['browseconvocatorias_desc'] = 'Explore available calls for applications and find vacancies that match your profile.';

// Vacancies.
$string['vacancies'] = 'Vacancies';
$string['vacancy'] = 'Vacancy';
$string['vacancies_dashboard_desc'] = 'Create, edit and publish job vacancies within calls.';
$string['newvacancy'] = 'New Vacancy';
$string['editvacancy'] = 'Edit Vacancy';
$string['deletevacancy'] = 'Delete Vacancy';
$string['status:draft'] = 'Draft';
$string['status:published'] = 'Published';
$string['status:closed'] = 'Closed';
$string['status:assigned'] = 'Assigned';

// Applications.
$string['applications'] = 'Applications';
$string['application'] = 'Application';
$string['myapplications'] = 'My Applications';
$string['myapplications_desc'] = 'View and manage your submitted applications and required documents.';
$string['viewmyapplications'] = 'View My Applications';
$string['review_dashboard_desc'] = 'Review submitted applications, validate documents, and manage the selection process.';
$string['reviewall'] = 'Review All';

// Application statuses.
$string['appstatus:submitted'] = 'Submitted';
$string['appstatus:under_review'] = 'Under Review';
$string['appstatus:docs_validated'] = 'Documents Validated';
$string['appstatus:docs_rejected'] = 'Documents Rejected';
$string['appstatus:interview'] = 'Interview';
$string['appstatus:selected'] = 'Selected';
$string['appstatus:rejected'] = 'Rejected';
$string['appstatus:withdrawn'] = 'Withdrawn';

// Contract types.
$string['contract:fulltime'] = 'Full Time';
$string['contract:parttime'] = 'Part Time';
$string['contract:temporary'] = 'Temporary';
$string['contract:permanent'] = 'Permanent';
$string['contract:adjunct'] = 'Adjunct';
$string['contract:hourly'] = 'Hourly';

// Publication types.
$string['publicationtype:internal'] = 'Internal';
$string['publicationtype:public'] = 'Public';

// Reviews.
$string['myreviews'] = 'My Reviews';
$string['myreviews_desc'] = 'View and complete document reviews assigned to you.';
$string['viewmyreviews'] = 'View My Reviews';
$string['completedreviews'] = 'Completed Reviews';
$string['pending'] = 'Pending';
$string['pending_reviews_alert'] = 'You have {$a} pending reviews to complete.';
$string['pending_docs_alert'] = 'You have {$a} pending documents to upload.';
$string['reviewerstatistics'] = 'Reviewer Statistics';
$string['pendingassignments'] = 'Pending Assignments';
$string['documentsvalidated'] = 'Documents Validated';
$string['documentsrejected'] = 'Documents Rejected';
$string['avgvalidationtime'] = 'Avg Validation Time';
$string['noassignments'] = 'No assignments';
$string['noassignments_desc'] = 'You have no applications assigned to review at this time.';

// Workflow.
$string['assignreviewers'] = 'Assign Reviewers';
$string['assignreviewers_desc'] = 'Assign reviewers to applications for document validation.';
$string['bulkvalidation'] = 'Bulk Validation';
$string['bulkvalidation_desc'] = 'Validate multiple documents at once for faster processing.';
$string['committees'] = 'Selection Committees';
$string['committees_desc'] = 'Manage selection committees for each faculty.';
$string['managecommittees'] = 'Manage Committees';
$string['program_reviewers'] = 'Program Reviewers';
$string['program_reviewers_desc'] = 'Assign default reviewers for each academic program.';

// Reports.
$string['reports'] = 'Reports';
$string['reports_desc'] = 'View statistics, charts, and detailed reports of the selection process.';
$string['viewreports'] = 'View Reports';
$string['importvacancies'] = 'Import Vacancies';
$string['importvacancies_desc'] = 'Import vacancies from CSV or Excel files.';
$string['import'] = 'Import';
$string['exportdata'] = 'Export Data';
$string['exportdata_desc'] = 'Export applications, evaluations, and selection data.';
$string['export'] = 'Export';

// Configuration.
$string['pluginsettings'] = 'Plugin Settings';
$string['pluginsettings_desc'] = 'Configure general options for the Job Board plugin.';
$string['configure'] = 'Configure';
$string['doctypes'] = 'Document Types';
$string['doctypes_desc'] = 'Define required documents and validation rules.';
$string['manage'] = 'Manage';
$string['emailtemplates'] = 'Email Templates';
$string['emailtemplates_desc'] = 'Customize notification emails sent to applicants.';
$string['exemptions'] = 'User Exemptions';
$string['manageexemptions_desc'] = 'Manage document exemptions for specific users (ISER members, age).';
$string['manageroles'] = 'Role Management';
$string['manageroles_desc'] = 'Configure roles and permissions for the Job Board.';

// Public page.
$string['viewpublicpage'] = 'View Public Page';
$string['viewpublicvacancies'] = 'View Public Vacancies';
$string['welcometojobboard'] = 'Welcome to the Job Board';
$string['vieweronly_desc'] = 'You currently have view-only access. Browse available public vacancies to learn more about opportunities.';

// Feature descriptions.
$string['feature_create_convocatorias'] = 'Create and configure calls';
$string['feature_manage_vacancies'] = 'Manage vacancies within calls';
$string['feature_track_applications'] = 'Track application progress';
$string['feature_create_vacancies'] = 'Create new job postings';
$string['feature_publish_vacancies'] = 'Publish and close vacancies';
$string['feature_import_export'] = 'Import/export data';
$string['feature_review_documents'] = 'Review submitted documents';
$string['feature_validate_applications'] = 'Validate applications';
$string['feature_assign_reviewers'] = 'Assign reviewers to applications';

// Actions.
$string['apply'] = 'Apply';
$string['view'] = 'View';
$string['edit'] = 'Edit';
$string['delete'] = 'Delete';
$string['save'] = 'Save';
$string['cancel'] = 'Cancel';
$string['submit'] = 'Submit';
$string['approve'] = 'Approve';
$string['reject'] = 'Reject';
$string['explore'] = 'Explore';
$string['search'] = 'Search';
$string['filter'] = 'Filter';
$string['reset'] = 'Reset';
$string['back'] = 'Back';
$string['next'] = 'Next';
$string['previous'] = 'Previous';
$string['close'] = 'Close';
$string['create'] = 'Create';
$string['update'] = 'Update';

// Messages.
$string['noconvocatorias'] = 'No calls for applications available.';
$string['novacancies'] = 'No vacancies available.';
$string['noapplications'] = 'No applications found.';
$string['noreviews'] = 'No reviews assigned.';
$string['confirmdeletion'] = 'Are you sure you want to delete this item?';
$string['deletionsuccess'] = 'Item deleted successfully.';
$string['savesuccess'] = 'Changes saved successfully.';
$string['error'] = 'An error occurred.';

// Document validation.
$string['documentvalidation'] = 'Document Validation';
$string['validationstatus'] = 'Validation Status';
$string['validationpending'] = 'Pending Validation';
$string['validationapproved'] = 'Approved';
$string['validationrejected'] = 'Rejected';
$string['validationcomments'] = 'Validation Comments';
$string['requireddocuments'] = 'Required Documents';
$string['uploaddocument'] = 'Upload Document';
$string['reupload'] = 'Re-upload';

// Privacy.
$string['privacy:metadata:local_jobboard_application'] = 'Information about user applications to job vacancies.';
$string['privacy:metadata:local_jobboard_document'] = 'Documents uploaded by users for their applications.';
$string['privacy:metadata:local_jobboard_application:userid'] = 'The ID of the user who submitted the application.';
$string['privacy:metadata:local_jobboard_application:timecreated'] = 'The time when the application was submitted.';
$string['privacy:metadata:local_jobboard_document:userid'] = 'The ID of the user who uploaded the document.';
$string['privacy:metadata:local_jobboard_document:filename'] = 'The name of the uploaded file.';

// Tables and data.
$string['code'] = 'Code';
$string['name'] = 'Name';
$string['description'] = 'Description';
$string['status'] = 'Status';
$string['startdate'] = 'Start Date';
$string['enddate'] = 'End Date';
$string['opendate'] = 'Open Date';
$string['closedate'] = 'Close Date';
$string['createdby'] = 'Created By';
$string['timecreated'] = 'Date Created';
$string['timemodified'] = 'Last Modified';
$string['actions'] = 'Actions';
$string['positions'] = 'Positions';
$string['location'] = 'Location';
$string['department'] = 'Department';
$string['contracttype'] = 'Contract Type';
$string['duration'] = 'Duration';
$string['requirements'] = 'Requirements';
$string['desirable'] = 'Desirable';
$string['applicant'] = 'Applicant';
$string['vacancy_title'] = 'Vacancy';
$string['documents'] = 'Documents';
$string['progress'] = 'Progress';

// IOMAD/Multi-tenant.
$string['company'] = 'Centro';
$string['faculty'] = 'Faculty';
$string['program'] = 'Program';
$string['selectcompany'] = 'Select Centro';
$string['selectfaculty'] = 'Select Faculty';
$string['selectprogram'] = 'Select Program';
$string['allcompanies'] = 'All Centros';
$string['allfaculties'] = 'All Faculties';
$string['allprograms'] = 'All Programs';

// Settings.
$string['settings'] = 'Settings';
$string['generalsettings'] = 'General Settings';
$string['enable_public_page'] = 'Enable Public Page';
$string['enable_public_page_desc'] = 'Allow anonymous users to view public vacancies.';
$string['require_consent'] = 'Require Consent';
$string['require_consent_desc'] = 'Require applicants to accept terms before applying.';
$string['consent_text'] = 'Consent Text';
$string['consent_text_desc'] = 'Text shown to applicants for consent acceptance.';
$string['max_file_size'] = 'Maximum File Size';
$string['max_file_size_desc'] = 'Maximum size for uploaded documents (in MB).';
$string['allowed_file_types'] = 'Allowed File Types';
$string['allowed_file_types_desc'] = 'Comma-separated list of allowed file extensions.';

// Notifications.
$string['notification_application_submitted'] = 'Application Submitted';
$string['notification_documents_approved'] = 'Documents Approved';
$string['notification_documents_rejected'] = 'Documents Rejected';
$string['notification_interview_scheduled'] = 'Interview Scheduled';
$string['notification_application_selected'] = 'Application Selected';
$string['notification_application_rejected'] = 'Application Not Selected';

// Errors.
$string['error:noaccess'] = 'You do not have permission to access this page.';
$string['error:invalidid'] = 'Invalid ID provided.';
$string['error:notfound'] = 'The requested item was not found.';
$string['error:vacancyclosed'] = 'This vacancy is no longer accepting applications.';
$string['error:alreadyapplied'] = 'You have already applied to this vacancy.';
$string['error:uploadfailed'] = 'File upload failed. Please try again.';
$string['error:invalidfiletype'] = 'Invalid file type. Allowed types: {$a}';
$string['error:filetoobig'] = 'File is too large. Maximum size: {$a} MB';

// Schedule interview.
$string['scheduleinterview'] = 'Schedule Interview';
$string['interviewdate'] = 'Interview Date';
$string['interviewtime'] = 'Interview Time';
$string['interviewlocation'] = 'Interview Location';
$string['interviewtype'] = 'Interview Type';
$string['interviewtype:inperson'] = 'In Person';
$string['interviewtype:online'] = 'Online';
$string['interviewtype:phone'] = 'Phone';
$string['interviewlink'] = 'Interview Link';
$string['interviewers'] = 'Interviewers';

// User profile.
$string['applicantprofile'] = 'Applicant Profile';
$string['updateprofile'] = 'Update Profile';
$string['personalinfo'] = 'Personal Information';
$string['contactinfo'] = 'Contact Information';
$string['academicinfo'] = 'Academic Information';
$string['workexperience'] = 'Work Experience';

// Audit.
$string['auditlog'] = 'Audit Log';
$string['auditaction'] = 'Action';
$string['audituser'] = 'User';
$string['audittime'] = 'Time';
$string['auditdetails'] = 'Details';

// Applications page additional strings.
$string['browsevacancies'] = 'Browse Vacancies';
$string['allstatuses'] = 'All statuses';
$string['inprogress'] = 'In Progress';
$string['showingxtoy'] = 'Showing {$a->from} to {$a->to} of {$a->total}';
$string['noapplicationsdesc'] = 'You haven\'t applied to any vacancies yet. Start by browsing available opportunities.';
$string['unknownvacancy'] = 'Unknown Vacancy';
$string['exemptionactive'] = 'Exemption Active';
$string['exemptiontype_iser'] = 'ISER Member';
$string['exemptiontype_age'] = 'Age-based Exemption';
$string['exemptiontype_disability'] = 'Disability Exemption';
$string['documentstatus'] = 'Document Status';
$string['approved'] = 'Approved';
$string['rejected'] = 'Rejected';
$string['uploaddocsreminder'] = 'Please upload required documents to continue.';
$string['viewdetails'] = 'View Details';
$string['withdraw'] = 'Withdraw';
$string['confirmwithdraw'] = 'Are you sure you want to withdraw this application? This action cannot be undone.';
$string['breadcrumb'] = 'Breadcrumb';
$string['pagination'] = 'Page navigation';

// Convocatorias page additional strings.
$string['manageconvocatorias'] = 'Manage Calls';
$string['addconvocatoria'] = 'Add Call';
$string['addvacancy'] = 'Add Vacancy';
$string['viewvacancies'] = 'View Vacancies';
$string['totalconvocatorias'] = 'Total Calls';
$string['convocatoriahelp'] = 'Convocatorias group multiple vacancies together under a single call. Create a convocatoria, add vacancies, then open it to start accepting applications.';
$string['noconvocatoriasdesc'] = 'No calls have been created yet. Create your first call to start managing vacancies.';
$string['openconvocatoria'] = 'Open Call';
$string['closeconvocatoria'] = 'Close Call';
$string['reopenconvocatoria'] = 'Reopen Call';
$string['archiveconvocatoria'] = 'Archive Call';
$string['confirmopenconvocatoria'] = 'Are you sure you want to open this call? All draft vacancies will be published.';
$string['confirmcloseconvocatoria'] = 'Are you sure you want to close this call? All vacancies will be closed.';
$string['confirmreopenconvocatoria'] = 'Are you sure you want to reopen this call? All closed vacancies will be reopened.';
$string['confirmarchiveconvocatoria'] = 'Are you sure you want to archive this call?';
$string['confirmdeletevconvocatoria'] = 'Are you sure you want to delete this call? This action cannot be undone.';
$string['convocatoriadeleted'] = 'Call deleted successfully.';
$string['convocatoriaopened'] = 'Call opened successfully. All vacancies are now published.';
$string['convocatoriaclosedmsg'] = 'Call closed successfully. All vacancies are now closed.';
$string['convocatoriaarchived'] = 'Call archived successfully.';
$string['convocatoriareopened'] = 'Call reopened successfully. All vacancies are now published.';
$string['error:cannotdeleteconvocatoria'] = 'Cannot delete this call. Only draft or archived calls can be deleted.';
$string['error:convocatoriahasnovacancies'] = 'Cannot open this call. Please add at least one vacancy first.';
$string['error:cannotreopenconvocatoria'] = 'Cannot reopen this call. Only closed calls can be reopened.';

// Vacancies page additional strings.
$string['explorevacancias'] = 'Explore Vacancies';
$string['browse_vacancies_desc'] = 'Find your next opportunity from our available positions.';
$string['closingsoon'] = 'Closing Soon';
$string['searchvacancies'] = 'Search vacancies';
$string['allcontracttypes'] = 'All contract types';
$string['alldepartments'] = 'All departments';
$string['allvacancies'] = 'All vacancies';
$string['daysleft'] = '{$a} days left';
$string['vacancystatistics'] = 'Vacancy Statistics';
$string['convocatoriaactive'] = 'Active Calls';
$string['convocatoriaclosed'] = 'Closed Calls';
$string['convocatoriastatistics'] = 'Convocatoria Statistics';
$string['noconvocatorias'] = 'No convocatorias';
$string['noconvocatorias_desc'] = 'There are no convocatorias available at this time.';
$string['datesubmitted'] = 'Date Submitted';
$string['closingdate'] = 'Closing Date';
$string['pendingdocuments'] = 'Pending Documents';
$string['sortby'] = 'Sort by';
$string['statustabs'] = 'Status tabs';
$string['convocatoriavacancycount'] = '{$a} vacancies';
$string['noresults'] = 'No results found';
$string['applied'] = 'Applied';
$string['backtodashboard'] = 'Back to Dashboard';

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

// =============================================================================
// PLUGIN METADATA
// =============================================================================
$string['pluginname'] = 'Job Board';
$string['plugindescription'] = 'Job vacancy management for adjunct professors';

// =============================================================================
// CAPABILITIES
// =============================================================================
$string['jobboard:view'] = 'View job board';
$string['jobboard:apply'] = 'Apply to vacancies';
$string['jobboard:manage'] = 'Manage vacancies';
$string['jobboard:review'] = 'Review applications';
$string['jobboard:managecommittee'] = 'Manage selection committees';
$string['jobboard:managetemplates'] = 'Manage email templates';
$string['jobboard:viewreports'] = 'View reports';
$string['jobboard:managesettings'] = 'Manage settings';

// =============================================================================
// NAVIGATION
// =============================================================================
$string['nav_dashboard'] = 'Dashboard';
$string['nav_convocatorias'] = 'Calls';
$string['nav_vacancies'] = 'Vacancies';
$string['nav_applications'] = 'Applications';
$string['nav_myapplications'] = 'My Applications';
$string['nav_documents'] = 'Documents';
$string['nav_reviewers'] = 'Reviewers';
$string['nav_committees'] = 'Committees';
$string['nav_templates'] = 'Email Templates';
$string['nav_doctypes'] = 'Document Types';
$string['nav_settings'] = 'Settings';
$string['nav_reports'] = 'Reports';

// =============================================================================
// GENERAL UI
// =============================================================================
$string['actions'] = 'Actions';
$string['add'] = 'Add';
$string['edit'] = 'Edit';
$string['delete'] = 'Delete';
$string['save'] = 'Save';
$string['cancel'] = 'Cancel';
$string['confirm'] = 'Confirm';
$string['back'] = 'Back';
$string['search'] = 'Search';
$string['filter'] = 'Filter';
$string['reset'] = 'Reset';
$string['view'] = 'View';
$string['download'] = 'Download';
$string['upload'] = 'Upload';
$string['status'] = 'Status';
$string['date'] = 'Date';
$string['name'] = 'Name';
$string['description'] = 'Description';
$string['details'] = 'Details';
$string['active'] = 'Active';
$string['inactive'] = 'Inactive';
$string['yes'] = 'Yes';
$string['no'] = 'No';
$string['all'] = 'All';
$string['none'] = 'None';
$string['required'] = 'Required';
$string['optional'] = 'Optional';
$string['loading'] = 'Loading...';
$string['noresults'] = 'No results found';
$string['error'] = 'Error';
$string['success'] = 'Success';
$string['warning'] = 'Warning';
$string['info'] = 'Information';

// =============================================================================
// DASHBOARD
// =============================================================================
$string['dashboard'] = 'Dashboard';
$string['dashboard_welcome'] = 'Welcome to Job Board';
$string['dashboard_stats'] = 'Statistics';
$string['dashboard_recent'] = 'Recent Activity';
$string['stat_vacancies'] = 'Vacancies';
$string['stat_applications'] = 'Applications';
$string['stat_pending'] = 'Pending';
$string['stat_approved'] = 'Approved';
$string['stat_rejected'] = 'Rejected';

// =============================================================================
// CONVOCATORIAS (CALLS)
// =============================================================================
$string['convocatoria'] = 'Call';
$string['convocatorias'] = 'Calls';
$string['convocatoria_add'] = 'Add Call';
$string['convocatoria_edit'] = 'Edit Call';
$string['convocatoria_delete'] = 'Delete Call';
$string['convocatoria_view'] = 'View Call';
$string['convocatoria_title'] = 'Title';
$string['convocatoria_code'] = 'Code';
$string['convocatoria_description'] = 'Description';
$string['convocatoria_startdate'] = 'Start Date';
$string['convocatoria_enddate'] = 'End Date';
$string['convocatoria_status'] = 'Status';
$string['convocatoria_vacancies'] = 'Vacancies';
$string['convocatoria_applications'] = 'Applications';
$string['convocatoria_document'] = 'Call Document (PDF)';
$string['convocatoria_document_help'] = 'Upload the official call document in PDF format';
$string['convocatoria_faculty'] = 'Faculty';
$string['convocatoria_company'] = 'Tutorial Center';

// Convocatoria statuses
$string['convocatoria_status_draft'] = 'Draft';
$string['convocatoria_status_open'] = 'Open';
$string['convocatoria_status_closed'] = 'Closed';
$string['convocatoria_status_cancelled'] = 'Cancelled';
$string['convocatoria_status_completed'] = 'Completed';

// Convocatoria messages
$string['convocatoria_created'] = 'Call created successfully';
$string['convocatoria_updated'] = 'Call updated successfully';
$string['convocatoria_deleted'] = 'Call deleted successfully';
$string['convocatoria_delete_confirm'] = 'Are you sure you want to delete this call?';
$string['convocatoria_not_found'] = 'Call not found';
$string['convocatoria_has_applications'] = 'Cannot delete call with existing applications';

// =============================================================================
// VACANCIES
// =============================================================================
$string['vacancy'] = 'Vacancy';
$string['vacancies'] = 'Vacancies';
$string['vacancy_add'] = 'Add Vacancy';
$string['vacancy_edit'] = 'Edit Vacancy';
$string['vacancy_delete'] = 'Delete Vacancy';
$string['vacancy_view'] = 'View Vacancy';
$string['vacancy_title'] = 'Title';
$string['vacancy_description'] = 'Description';
$string['vacancy_requirements'] = 'Requirements';
$string['vacancy_positions'] = 'Positions';
$string['vacancy_program'] = 'Academic Program';
$string['vacancy_course'] = 'Course';
$string['vacancy_modality'] = 'Modality';
$string['vacancy_hours'] = 'Hours per Week';
$string['vacancy_salary'] = 'Salary';
$string['vacancy_deadline'] = 'Application Deadline';

// Vacancy statuses
$string['vacancy_status_draft'] = 'Draft';
$string['vacancy_status_open'] = 'Open';
$string['vacancy_status_closed'] = 'Closed';
$string['vacancy_status_filled'] = 'Filled';
$string['vacancy_status_cancelled'] = 'Cancelled';

// Vacancy messages
$string['vacancy_created'] = 'Vacancy created successfully';
$string['vacancy_updated'] = 'Vacancy updated successfully';
$string['vacancy_deleted'] = 'Vacancy deleted successfully';
$string['vacancy_delete_confirm'] = 'Are you sure you want to delete this vacancy?';
$string['vacancy_not_found'] = 'Vacancy not found';
$string['vacancy_has_applications'] = 'Cannot delete vacancy with existing applications';
$string['vacancy_no_vacancies'] = 'No vacancies available';

// =============================================================================
// APPLICATIONS
// =============================================================================
$string['application'] = 'Application';
$string['applications'] = 'Applications';
$string['application_submit'] = 'Submit Application';
$string['application_view'] = 'View Application';
$string['application_review'] = 'Review Application';
$string['application_withdraw'] = 'Withdraw Application';
$string['myapplications'] = 'My Applications';
$string['application_date'] = 'Application Date';
$string['application_vacancy'] = 'Vacancy';
$string['application_applicant'] = 'Applicant';
$string['application_documents'] = 'Documents';
$string['application_notes'] = 'Notes';
$string['application_reviewer'] = 'Assigned Reviewer';
$string['application_assign_reviewer'] = 'Assign Reviewer';

// Application statuses
$string['application_status_draft'] = 'Draft';
$string['application_status_submitted'] = 'Submitted';
$string['application_status_under_review'] = 'Under Review';
$string['application_status_docs_pending'] = 'Documents Pending';
$string['application_status_docs_validated'] = 'Documents Validated';
$string['application_status_docs_rejected'] = 'Documents Rejected';
$string['application_status_shortlisted'] = 'Shortlisted';
$string['application_status_interview'] = 'Interview Scheduled';
$string['application_status_selected'] = 'Selected';
$string['application_status_rejected'] = 'Rejected';
$string['application_status_withdrawn'] = 'Withdrawn';

// Application messages
$string['application_submitted'] = 'Application submitted successfully';
$string['application_updated'] = 'Application updated successfully';
$string['application_withdrawn'] = 'Application withdrawn successfully';
$string['application_withdraw_confirm'] = 'Are you sure you want to withdraw this application?';
$string['application_not_found'] = 'Application not found';
$string['application_already_exists'] = 'You have already applied to this vacancy';
$string['application_deadline_passed'] = 'Application deadline has passed';
$string['application_no_applications'] = 'No applications found';

// =============================================================================
// DOCUMENTS
// =============================================================================
$string['document'] = 'Document';
$string['documents'] = 'Documents';
$string['document_upload'] = 'Upload Document';
$string['document_download'] = 'Download Document';
$string['document_view'] = 'View Document';
$string['document_delete'] = 'Delete Document';
$string['document_type'] = 'Document Type';
$string['document_file'] = 'File';
$string['document_filename'] = 'File Name';
$string['document_filesize'] = 'File Size';
$string['document_uploaded'] = 'Upload Date';
$string['document_validation'] = 'Validation';
$string['document_validate'] = 'Validate Document';
$string['document_reject'] = 'Reject Document';
$string['document_comments'] = 'Comments';

// Document statuses
$string['document_status_pending'] = 'Pending Review';
$string['document_status_approved'] = 'Approved';
$string['document_status_rejected'] = 'Rejected';
$string['document_status_expired'] = 'Expired';

// Document messages
$string['document_uploaded_success'] = 'Document uploaded successfully';
$string['document_deleted'] = 'Document deleted successfully';
$string['document_validated'] = 'Document validated successfully';
$string['document_rejected'] = 'Document rejected';
$string['document_not_found'] = 'Document not found';
$string['document_invalid_type'] = 'Invalid document type';
$string['document_too_large'] = 'File size exceeds the maximum allowed';
$string['document_required_missing'] = 'Required documents are missing';
$string['document_only_pdf'] = 'Only PDF files are allowed';

// =============================================================================
// DOCUMENT TYPES
// =============================================================================
$string['doctype'] = 'Document Type';
$string['doctypes'] = 'Document Types';
$string['doctype_add'] = 'Add Document Type';
$string['doctype_edit'] = 'Edit Document Type';
$string['doctype_delete'] = 'Delete Document Type';
$string['doctype_name'] = 'Name';
$string['doctype_code'] = 'Code';
$string['doctype_description'] = 'Description';
$string['doctype_required'] = 'Required';
$string['doctype_validitydays'] = 'Validity Days';
$string['doctype_validitydays_help'] = 'Number of days the document is valid (0 = no expiry)';
$string['doctype_maxsize'] = 'Maximum File Size (MB)';
$string['doctype_input_type'] = 'Input Type';
$string['doctype_input_file'] = 'File Upload';
$string['doctype_input_text'] = 'Text Input';
$string['doctype_input_date'] = 'Date Input';
$string['doctype_input_number'] = 'Number Input';
$string['doctype_active'] = 'Active';
$string['doctype_order'] = 'Display Order';

// Document type messages
$string['doctype_created'] = 'Document type created successfully';
$string['doctype_updated'] = 'Document type updated successfully';
$string['doctype_deleted'] = 'Document type deleted successfully';
$string['doctype_delete_confirm'] = 'Are you sure you want to delete this document type?';
$string['doctype_has_documents'] = 'Cannot delete document type with existing documents';

// =============================================================================
// PROGRAM REVIEWERS
// =============================================================================
$string['program_reviewers'] = 'Program Reviewers';
$string['program_reviewer'] = 'Program Reviewer';
$string['manage_program_reviewers'] = 'Manage Program Reviewers';
$string['reviewer'] = 'Reviewer';
$string['reviewers'] = 'Reviewers';
$string['reviewer_add'] = 'Add Reviewer';
$string['reviewer_remove'] = 'Remove Reviewer';
$string['reviewer_role'] = 'Role';
$string['reviewer_status'] = 'Status';
$string['reviewer_program'] = 'Program';
$string['reviewer_user'] = 'User';
$string['reviewer_assigned'] = 'Assigned Date';
$string['reviewer_workload'] = 'Workload';

// Reviewer roles
$string['role_lead_reviewer'] = 'Lead Reviewer';
$string['role_reviewer'] = 'Reviewer';

// Reviewer statuses
$string['reviewer_status_active'] = 'Active';
$string['reviewer_status_inactive'] = 'Inactive';

// Reviewer statistics
$string['reviewer_stats_total'] = 'Total Assignments';
$string['reviewer_stats_active'] = 'Active Reviewers';
$string['reviewer_stats_leads'] = 'Lead Reviewers';
$string['reviewer_stats_programs'] = 'Programs with Reviewers';
$string['reviewer_stats_users'] = 'Unique Reviewers';

// Reviewer messages
$string['reviewer_added'] = 'Reviewer added successfully';
$string['reviewer_removed'] = 'Reviewer removed successfully';
$string['reviewer_role_updated'] = 'Reviewer role updated successfully';
$string['reviewer_status_updated'] = 'Reviewer status updated successfully';
$string['reviewer_already_assigned'] = 'User is already a reviewer for this program';
$string['reviewer_not_found'] = 'Reviewer not found';
$string['reviewer_cannot_remove_last_lead'] = 'Cannot remove the last active lead reviewer';
$string['reviewer_no_capability'] = 'User does not have reviewer capability';
$string['reviewer_select_program'] = 'Select Program';
$string['reviewer_select_user'] = 'Select User';
$string['reviewer_no_reviewers'] = 'No reviewers assigned';
$string['reviewer_programs_without'] = 'Programs without reviewers';

// =============================================================================
// SELECTION COMMITTEES
// =============================================================================
$string['committee'] = 'Selection Committee';
$string['committees'] = 'Selection Committees';
$string['committee_manage'] = 'Manage Committees';
$string['committee_add'] = 'Add Committee';
$string['committee_edit'] = 'Edit Committee';
$string['committee_delete'] = 'Delete Committee';
$string['committee_view'] = 'View Committee';
$string['committee_faculty'] = 'Faculty';
$string['committee_members'] = 'Members';
$string['committee_add_member'] = 'Add Member';
$string['committee_remove_member'] = 'Remove Member';

// Committee roles
$string['committee_role_president'] = 'President';
$string['committee_role_secretary'] = 'Secretary';
$string['committee_role_member'] = 'Member';

// Committee messages
$string['committee_created'] = 'Committee created successfully';
$string['committee_updated'] = 'Committee updated successfully';
$string['committee_deleted'] = 'Committee deleted successfully';
$string['committee_member_added'] = 'Member added to committee';
$string['committee_member_removed'] = 'Member removed from committee';
$string['committee_not_found'] = 'Committee not found';
$string['committee_no_committees'] = 'No committees found';

// =============================================================================
// EMAIL TEMPLATES
// =============================================================================
$string['email_templates'] = 'Email Templates';
$string['email_template'] = 'Email Template';
$string['template_manage'] = 'Manage Templates';
$string['template_add'] = 'Add Template';
$string['template_edit'] = 'Edit Template';
$string['template_delete'] = 'Delete Template';
$string['template_duplicate'] = 'Duplicate Template';
$string['template_preview'] = 'Preview';
$string['template_send_test'] = 'Send Test Email';
$string['template_name'] = 'Template Name';
$string['template_code'] = 'Template Code';
$string['template_subject'] = 'Subject';
$string['template_body'] = 'Body';
$string['template_signature'] = 'Signature';
$string['template_language'] = 'Language';
$string['template_active'] = 'Active';
$string['template_event'] = 'Trigger Event';

// Template events
$string['template_event_application_submitted'] = 'Application Submitted';
$string['template_event_application_received'] = 'Application Received (Admin)';
$string['template_event_documents_validated'] = 'Documents Validated';
$string['template_event_documents_rejected'] = 'Documents Rejected';
$string['template_event_application_shortlisted'] = 'Application Shortlisted';
$string['template_event_interview_scheduled'] = 'Interview Scheduled';
$string['template_event_application_selected'] = 'Application Selected';
$string['template_event_application_rejected'] = 'Application Rejected';
$string['template_event_reviewer_assigned'] = 'Reviewer Assigned';
$string['template_event_deadline_reminder'] = 'Deadline Reminder';

// Template placeholders
$string['template_placeholders'] = 'Available Placeholders';
$string['placeholder_user_firstname'] = 'User first name';
$string['placeholder_user_lastname'] = 'User last name';
$string['placeholder_user_fullname'] = 'User full name';
$string['placeholder_user_email'] = 'User email';
$string['placeholder_vacancy_title'] = 'Vacancy title';
$string['placeholder_vacancy_program'] = 'Academic program';
$string['placeholder_convocatoria_title'] = 'Call title';
$string['placeholder_convocatoria_code'] = 'Call code';
$string['placeholder_application_date'] = 'Application date';
$string['placeholder_application_status'] = 'Application status';
$string['placeholder_company_name'] = 'Tutorial center name';
$string['placeholder_site_name'] = 'Site name';
$string['placeholder_site_url'] = 'Site URL';
$string['placeholder_deadline'] = 'Deadline date';
$string['placeholder_reviewer_name'] = 'Reviewer name';
$string['placeholder_documents_list'] = 'Documents list';
$string['placeholder_rejection_reason'] = 'Rejection reason';
$string['placeholder_interview_date'] = 'Interview date';
$string['placeholder_interview_location'] = 'Interview location';

// Template messages
$string['template_created'] = 'Template created successfully';
$string['template_updated'] = 'Template updated successfully';
$string['template_deleted'] = 'Template deleted successfully';
$string['template_duplicated'] = 'Template duplicated successfully';
$string['template_test_sent'] = 'Test email sent successfully';
$string['template_not_found'] = 'Template not found';
$string['template_code_exists'] = 'Template code already exists';
$string['template_preview_title'] = 'Email Preview';
$string['template_preview_note'] = 'This is a preview with sample data';

// =============================================================================
// REPORTS
// =============================================================================
$string['reports'] = 'Reports';
$string['report_applications'] = 'Applications Report';
$string['report_vacancies'] = 'Vacancies Report';
$string['report_reviewers'] = 'Reviewers Report';
$string['report_documents'] = 'Documents Report';
$string['report_export'] = 'Export';
$string['report_export_csv'] = 'Export to CSV';
$string['report_export_excel'] = 'Export to Excel';
$string['report_export_pdf'] = 'Export to PDF';
$string['report_date_from'] = 'From Date';
$string['report_date_to'] = 'To Date';
$string['report_generate'] = 'Generate Report';

// =============================================================================
// SETTINGS
// =============================================================================
$string['settings'] = 'Settings';
$string['settings_general'] = 'General Settings';
$string['settings_notifications'] = 'Notification Settings';
$string['settings_documents'] = 'Document Settings';
$string['settings_emails'] = 'Email Settings';

// General settings
$string['setting_enabled'] = 'Enable Job Board';
$string['setting_enabled_desc'] = 'Enable or disable the job board functionality';
$string['setting_allow_applications'] = 'Allow Applications';
$string['setting_allow_applications_desc'] = 'Allow users to submit applications';
$string['setting_require_login'] = 'Require Login';
$string['setting_require_login_desc'] = 'Require users to be logged in to view vacancies';

// Document settings
$string['setting_max_filesize'] = 'Maximum File Size';
$string['setting_max_filesize_desc'] = 'Maximum file size for document uploads (in MB)';
$string['setting_allowed_types'] = 'Allowed File Types';
$string['setting_allowed_types_desc'] = 'Comma-separated list of allowed file extensions';
$string['setting_pdf_only'] = 'PDF Only';
$string['setting_pdf_only_desc'] = 'Only allow PDF file uploads';

// Email settings
$string['setting_email_from'] = 'From Email Address';
$string['setting_email_from_desc'] = 'Email address used as sender';
$string['setting_email_replyto'] = 'Reply-To Address';
$string['setting_email_replyto_desc'] = 'Email address for replies';
$string['setting_email_copy_admin'] = 'Copy Admin on Emails';
$string['setting_email_copy_admin_desc'] = 'Send a copy of all emails to administrators';

// =============================================================================
// ERRORS AND VALIDATION
// =============================================================================
$string['error_required_field'] = 'This field is required';
$string['error_invalid_date'] = 'Invalid date format';
$string['error_invalid_email'] = 'Invalid email address';
$string['error_date_past'] = 'Date cannot be in the past';
$string['error_date_order'] = 'End date must be after start date';
$string['error_file_upload'] = 'Error uploading file';
$string['error_file_type'] = 'Invalid file type';
$string['error_file_size'] = 'File size exceeds maximum allowed';
$string['error_permission_denied'] = 'Permission denied';
$string['error_not_found'] = 'Record not found';
$string['error_already_exists'] = 'Record already exists';
$string['error_cannot_delete'] = 'Cannot delete this record';
$string['error_invalid_action'] = 'Invalid action';
$string['error_session_expired'] = 'Session expired. Please log in again.';
$string['error_unknown'] = 'An unknown error occurred';

// =============================================================================
// CONFIRMATIONS
// =============================================================================
$string['confirm_delete'] = 'Are you sure you want to delete this item?';
$string['confirm_action'] = 'Are you sure you want to perform this action?';
$string['confirm_withdraw'] = 'Are you sure you want to withdraw your application?';
$string['confirm_submit'] = 'Are you sure you want to submit your application?';

// =============================================================================
// ACCESSIBILITY
// =============================================================================
$string['aria_close'] = 'Close';
$string['aria_expand'] = 'Expand';
$string['aria_collapse'] = 'Collapse';
$string['aria_menu'] = 'Menu';
$string['aria_loading'] = 'Loading content';
$string['aria_required'] = 'Required field';

// =============================================================================
// MODALITIES
// =============================================================================
$string['modality'] = 'Modality';
$string['modality_presencial'] = 'In-Person';
$string['modality_distancia'] = 'Distance';
$string['modality_virtual'] = 'Virtual';
$string['modality_hibrida'] = 'Hybrid';

// =============================================================================
// FACULTIES AND PROGRAMS
// =============================================================================
$string['faculty'] = 'Faculty';
$string['faculties'] = 'Faculties';
$string['program'] = 'Academic Program';
$string['programs'] = 'Academic Programs';
$string['select_faculty'] = 'Select Faculty';
$string['select_program'] = 'Select Program';

// =============================================================================
// AUDIT LOG
// =============================================================================
$string['audit_log'] = 'Audit Log';
$string['audit_action'] = 'Action';
$string['audit_user'] = 'User';
$string['audit_date'] = 'Date';
$string['audit_details'] = 'Details';
$string['audit_ip'] = 'IP Address';

// Audit actions
$string['audit_application_submitted'] = 'Application submitted';
$string['audit_application_updated'] = 'Application updated';
$string['audit_application_withdrawn'] = 'Application withdrawn';
$string['audit_document_uploaded'] = 'Document uploaded';
$string['audit_document_validated'] = 'Document validated';
$string['audit_document_rejected'] = 'Document rejected';
$string['audit_status_changed'] = 'Status changed';
$string['audit_reviewer_assigned'] = 'Reviewer assigned';
$string['audit_email_sent'] = 'Email sent';
$string['audit_program_reviewer_added'] = 'Program reviewer added';
$string['audit_program_reviewer_removed'] = 'Program reviewer removed';
$string['audit_program_reviewer_role_changed'] = 'Program reviewer role changed';

// =============================================================================
// CRON AND TASKS
// =============================================================================
$string['task_send_notifications'] = 'Send pending email notifications';
$string['task_cleanup_drafts'] = 'Cleanup abandoned draft applications';
$string['task_send_reminders'] = 'Send deadline reminders';
$string['task_expire_documents'] = 'Mark expired documents';

// =============================================================================
// PRIVACY
// =============================================================================
$string['privacy:metadata:applications'] = 'Information about job applications';
$string['privacy:metadata:applications:userid'] = 'The ID of the user who submitted the application';
$string['privacy:metadata:applications:status'] = 'The status of the application';
$string['privacy:metadata:documents'] = 'Documents uploaded with applications';
$string['privacy:metadata:documents:userid'] = 'The ID of the user who uploaded the document';

// =============================================================================
// HELP STRINGS
// =============================================================================
$string['help_convocatoria'] = 'A call is a formal announcement for hiring adjunct professors';
$string['help_vacancy'] = 'A vacancy represents a specific teaching position within a call';
$string['help_application'] = 'An application is submitted by a candidate for a specific vacancy';
$string['help_reviewer'] = 'A reviewer evaluates and validates application documents';
$string['help_committee'] = 'A selection committee makes final hiring decisions';
$string['help_program_reviewer'] = 'Program reviewers are assigned to specific academic programs to review applications';

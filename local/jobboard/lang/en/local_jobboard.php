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

// =============================================================================
// PROGRAM REVIEWERS MANAGEMENT PAGE
// =============================================================================
$string['programreviewers'] = 'Program Reviewers';
$string['program_reviewers_desc'] = 'Manage reviewers assigned to academic programs';
$string['programreviewerhelp'] = 'Assign reviewers to specific academic programs to review applications';
$string['totalreviewers'] = 'Total Reviewers';
$string['activereviewers'] = 'Active Reviewers';
$string['leadreviewers'] = 'Lead Reviewers';
$string['programswithreviewers'] = 'Programs with Reviewers';
$string['noprogramswithreviewers'] = 'No programs have reviewers assigned yet';
$string['addreviewer'] = 'Add Reviewer';
$string['addreviewerstoprogram'] = 'Add reviewers to this program';
$string['assignedreviewers'] = 'Assigned Reviewers';
$string['noreviewersforprogram'] = 'No reviewers assigned to this program';
$string['nousersavailable'] = 'No users available with reviewer capability';
$string['selectuser'] = 'Select user...';
$string['revieweradded'] = 'Reviewer added successfully';
$string['revieweradderror'] = 'Error adding reviewer';
$string['reviewerremoved'] = 'Reviewer removed successfully';
$string['reviewerremoveerror'] = 'Error removing reviewer';
$string['rolechanged'] = 'Role changed successfully';
$string['rolechangeerror'] = 'Error changing role';
$string['statuschanged'] = 'Status changed successfully';
$string['statuschangeerror'] = 'Error changing status';
$string['confirmremovereviewer'] = 'Are you sure you want to remove this reviewer?';
$string['changerole'] = 'Change Role';
$string['backtolist'] = 'Back to list';
$string['activate'] = 'Activate';
$string['deactivate'] = 'Deactivate';
$string['manage'] = 'Manage';
$string['remove'] = 'Remove';
$string['role'] = 'Role';
$string['user'] = 'User';
$string['email'] = 'Email';
$string['help'] = 'Help';

// Bulk validation and document review.
$string['alreadyvalidated'] = 'Document already validated';
$string['bulkrejected'] = 'Rejected during bulk validation';
$string['interviewscheduled'] = 'Interview has been scheduled';

// =============================================================================
// MISSING STRINGS - Auto-generated list
// =============================================================================

// Dashboard and navigation.
$string['aboutdoctypes'] = 'About document types';
$string['activeassignments'] = 'Active assignments';
$string['activecommittees'] = 'Active committees';
$string['activeconvocatorias'] = 'Active calls';
$string['activeconvocatorias_alert'] = 'There are active calls open for applications';
$string['activeexemptions'] = 'Active exemptions';
$string['addconvocatoria'] = 'Add call';
$string['adddoctype'] = 'Add document type';
$string['addexemption'] = 'Add exemption';
$string['additionalinfo'] = 'Additional information';
$string['additionalnotes'] = 'Additional notes';
$string['addmember'] = 'Add member';
$string['addnew'] = 'Add new';
$string['addvacancy'] = 'Add vacancy';
$string['adminonly'] = 'Admin only';
$string['age_exempt_notice'] = 'Age exemption applies';
$string['ageexemptionthreshold'] = 'Age exemption threshold';

// Applicant and applications.
$string['allapplicants'] = 'All applicants';
$string['allapplications'] = 'All applications';
$string['allapplications_desc'] = 'View and manage all applications';
$string['allcommittees'] = 'All committees';
$string['allcompanies'] = 'All companies';
$string['allcontracttypes'] = 'All contract types';
$string['alldepartments'] = 'All departments';
$string['alldocsreviewed'] = 'All documents reviewed';
$string['allowedformats'] = 'Allowed formats';
$string['allowedformats_desc'] = 'Allowed file formats for uploads';
$string['allowmultipleapplications_convocatoria'] = 'Allow multiple applications';
$string['allowmultipleapplications_convocatoria_desc'] = 'Allow users to apply to multiple vacancies';
$string['allstatuses'] = 'All statuses';
$string['allvacancies'] = 'All vacancies';
$string['allvalidated'] = 'All validated';
$string['alreadyapplied'] = 'You have already applied to this vacancy';
$string['andmore'] = 'and {$a} more';
$string['antecedentesmaxdays'] = 'Maximum age for background checks (days)';
$string['applicant'] = 'Applicant';
$string['applicantinfo'] = 'Applicant information';
$string['applicantwillbenotified'] = 'Applicant will be notified';
$string['applicationdetails'] = 'Application details';
$string['applicationerror'] = 'Error processing application';
$string['applicationguidelines'] = 'Application guidelines';
$string['applicationlimits'] = 'Application limits';
$string['applicationlimits_perconvocatoria_desc'] = 'Maximum applications per call';
$string['applicationof'] = 'Application {$a->current} of {$a->total}';
$string['applicationsbystatus'] = 'Applications by status';
$string['applicationsbyvacancy'] = 'Applications by vacancy';
$string['applicationstats'] = 'Application statistics';
$string['applicationsubmitted'] = 'Application submitted successfully';
$string['applicationwithdrawn'] = 'Application withdrawn';
$string['applied'] = 'Applied';
$string['apply'] = 'Apply';
$string['applyhelp_text'] = 'Complete all required fields and upload documents';
$string['applynow_desc'] = 'Start your application now';
$string['applynowdesc'] = 'Apply now';
$string['applytovacancy'] = 'Apply to vacancy';
$string['approve'] = 'Approve';
$string['approveall_confirm'] = 'Approve all documents?';
$string['approvedocument'] = 'Approve document';
$string['approveselected'] = 'Approve selected';

// Application status strings.
$string['appstatus:docs_rejected'] = 'Documents rejected';
$string['appstatus:docs_validated'] = 'Documents validated';
$string['appstatus:interview'] = 'Interview scheduled';
$string['appstatus:rejected'] = 'Rejected';
$string['appstatus:selected'] = 'Selected';
$string['appstatus:submitted'] = 'Submitted';
$string['appstatus:under_review'] = 'Under review';
$string['appstatus:withdrawn'] = 'Withdrawn';

// Archive and assign.
$string['archiveconvocatoria'] = 'Archive call';
$string['assigned'] = 'Assigned';
$string['assignedusers'] = 'Assigned users';
$string['assignnewusers'] = 'Assign new users';
$string['assignreviewer'] = 'Assign reviewer';
$string['assignreviewers'] = 'Assign reviewers';
$string['assignreviewers_desc'] = 'Manage reviewer assignments';
$string['assignselected'] = 'Assign selected';
$string['assignto'] = 'Assign to';
$string['auditlog'] = 'Audit log';
$string['autoassign'] = 'Auto-assign';
$string['autoassignall'] = 'Auto-assign all';
$string['autoassigncomplete'] = 'Auto-assignment complete';
$string['autoassignhelp'] = 'Automatically distribute applications';
$string['autovalidated'] = 'Auto-validated';
$string['available_placeholders'] = 'Available placeholders';
$string['available_vacancies_alert'] = 'Vacancies available for applications';
$string['availablereviewers'] = 'Available reviewers';
$string['availablevacancies'] = 'Available vacancies';
$string['avgtime'] = 'Average time';
$string['avgvalidationtime'] = 'Average validation time';
$string['avgworkload'] = 'Average workload';

// Back navigation.
$string['back_to_templates'] = 'Back to templates';
$string['backtoapplications'] = 'Back to applications';
$string['backtoconvocatoria'] = 'Back to call';
$string['backtoconvocatorias'] = 'Back to calls';
$string['backtodashboard'] = 'Back to dashboard';
$string['backtomanage'] = 'Back to management';
$string['backtoreviewlist'] = 'Back to review list';
$string['backtorolelist'] = 'Back to roles';
$string['backtovacancies'] = 'Back to vacancies';
$string['backtovacancy'] = 'Back to vacancy';
$string['basicinfo'] = 'Basic information';
$string['briefdescription'] = 'Brief description';
$string['browse_vacancies_desc'] = 'Browse available vacancies';
$string['browseconvocatorias'] = 'Browse calls';
$string['browservacancies'] = 'Browse vacancies';
$string['browsevacancies'] = 'Browse vacancies';

// Bulk actions.
$string['bulkactionerrors'] = 'Errors during bulk action';
$string['bulkactions'] = 'Bulk actions';
$string['bulkclose'] = 'Close selected';
$string['bulkdelete'] = 'Delete selected';
$string['bulkpublish'] = 'Publish selected';
$string['bulkunpublish'] = 'Unpublish selected';
$string['bulkvalidation'] = 'Bulk validation';
$string['bulkvalidation_desc'] = 'Validate multiple documents at once';
$string['bulkvalidationcomplete'] = 'Bulk validation complete';
$string['bydocumenttype'] = 'By document type';

// Cancelled and capabilities.
$string['cancelledby'] = 'Cancelled by';
$string['cap_assignreviewers'] = 'Assign reviewers';
$string['cap_createvacancy'] = 'Create vacancies';
$string['cap_download'] = 'Download documents';
$string['cap_evaluate'] = 'Evaluate candidates';
$string['cap_manage'] = 'Manage job board';
$string['cap_review'] = 'Review documents';
$string['cap_validate'] = 'Validate documents';
$string['cap_viewevaluations'] = 'View evaluations';
$string['cap_viewreports'] = 'View reports';
$string['capabilities'] = 'Capabilities';
$string['category'] = 'Category';
$string['chairhelp'] = 'Committee chair information';
$string['changestatus'] = 'Change status';

// Checklist items.
$string['checklist_acta_date'] = 'Graduation date is visible';
$string['checklist_acta_number'] = 'Act number is visible';
$string['checklist_background_date'] = 'Issue date is recent';
$string['checklist_background_status'] = 'Status is clear';
$string['checklist_cedula_number'] = 'ID number is visible';
$string['checklist_cedula_photo'] = 'Photo is visible';
$string['checklist_complete'] = 'Document is complete';
$string['checklist_eps_active'] = 'Affiliation is active';
$string['checklist_eps_entity'] = 'EPS name is visible';
$string['checklist_legible'] = 'Document is legible';
$string['checklist_medical_aptitude'] = 'Aptitude status is clear';
$string['checklist_medical_date'] = 'Exam date is valid';
$string['checklist_military_class'] = 'Military class is specified';
$string['checklist_military_number'] = 'Booklet number is visible';
$string['checklist_namematch'] = 'Name matches application';
$string['checklist_pension_active'] = 'Affiliation is active';
$string['checklist_pension_fund'] = 'Fund name is visible';
$string['checklist_rut_nit'] = 'NIT/RUT number is visible';
$string['checklist_rut_updated'] = 'Update date is recent';
$string['checklist_tarjeta_number'] = 'Professional card number is visible';
$string['checklist_tarjeta_profession'] = 'Profession matches requirements';
$string['checklist_title_date'] = 'Graduation date is visible';
$string['checklist_title_institution'] = 'Institution name is visible';
$string['checklist_title_program'] = 'Program name is visible';
$string['checklistitems'] = 'Checklist items';
$string['clearfilters'] = 'Clear filters';
$string['close'] = 'Close';
$string['closeconvocatoria'] = 'Close call';
$string['closedate'] = 'Close date';
$string['closesindays'] = 'Closes in {$a} days';
$string['closingdate'] = 'Closing date';
$string['closingsoon'] = 'Closing soon';
$string['closingsoondays'] = 'Days before closing warning';
$string['code'] = 'Code';
$string['column'] = 'Column';

// Committee.
$string['committeeautoroleassign'] = 'Auto-assign committee roles';
$string['committeecreated'] = 'Committee created successfully';
$string['committeecreateerror'] = 'Error creating committee';
$string['committeename'] = 'Committee name';
$string['committees_desc'] = 'Manage selection committees';
$string['company'] = 'Company';
$string['completedreviews'] = 'Completed reviews';
$string['completeinterview'] = 'Complete interview';
$string['completeprofile_required'] = 'Please complete your profile before applying';
$string['completerequiredfields'] = 'Complete all required fields';
$string['conditional_document_note'] = 'This document has conditions';
$string['conditionaldoctypes'] = 'Conditional document types';
$string['conditionalnote'] = 'Conditional note';
$string['conditions'] = 'Conditions';
$string['configuration'] = 'Configuration';
$string['configure'] = 'Configure';
$string['confirm_reset'] = 'Confirm reset';

// Confirm actions.
$string['confirmaction'] = 'Confirm action';
$string['confirmarchiveconvocatoria'] = 'Confirm archive call';
$string['confirmcancel'] = 'Confirm cancellation';
$string['confirmclose'] = 'Confirm close';
$string['confirmcloseconvocatoria'] = 'Confirm close call';
$string['confirmdelete'] = 'Confirm delete';
$string['confirmdeletedoctype'] = 'Confirm delete document type';
$string['confirmdeletedoctype_msg'] = 'Are you sure you want to delete this document type?';
$string['confirmdeletevacancy'] = 'Confirm delete vacancy';
$string['confirmdeletevconvocatoria'] = 'Confirm delete call';
$string['confirmnoshow'] = 'Confirm no-show';
$string['confirmopenconvocatoria'] = 'Confirm open call';
$string['confirmpassword'] = 'Confirm password';
$string['confirmpublish'] = 'Confirm publish';
$string['confirmremovemember'] = 'Confirm remove member';
$string['confirmreopen'] = 'Confirm reopen';
$string['confirmreopenconvocatoria'] = 'Confirm reopen call';
$string['confirmrevokeexemption'] = 'Confirm revoke exemption';
$string['confirmunassign'] = 'Confirm unassign';
$string['confirmunpublish'] = 'Confirm unpublish';
$string['confirmwithdraw'] = 'Confirm withdraw application';

// Consent.
$string['consentaccepttext'] = 'I accept the terms and conditions';
$string['consentgiven'] = 'Consent given';
$string['consentheader'] = 'Terms and consent';
$string['consentrequired'] = 'Consent is required';
$string['contactemail'] = 'Contact email';
$string['contentmanagement'] = 'Content management';

// Contract types.
$string['contract:catedra'] = 'Adjunct';
$string['contract:planta'] = 'Full-time';
$string['contract:prestacion_servicios'] = 'Service contract';
$string['contract:temporal'] = 'Temporary';
$string['contract:termino_fijo'] = 'Fixed term';
$string['contracttype'] = 'Contract type';

// Conversion.
$string['conversionfailed'] = 'Conversion failed';
$string['conversioninprogress'] = 'Conversion in progress';
$string['conversionpending'] = 'Conversion pending';
$string['conversionready'] = 'Conversion ready';
$string['conversionwait'] = 'Please wait while the document is converted';

// Convocatoria strings.
$string['convocatoria_status_archived'] = 'Archived';
$string['convocatoriaactive'] = 'Call is active';
$string['convocatoriaarchived'] = 'Call archived';
$string['convocatoriaclosed'] = 'Call closed';
$string['convocatoriaclosedmsg'] = 'This call has been closed';
$string['convocatoriacode'] = 'Call code';
$string['convocatoriacreated'] = 'Call created successfully';
$string['convocatoriadates'] = 'Call dates';
$string['convocatoriadeleted'] = 'Call deleted';
$string['convocatoriadescription'] = 'Call description';
$string['convocatoriadetails'] = 'Call details';
$string['convocatoriadocexemptions'] = 'Document exemptions';
$string['convocatoriaenddate'] = 'End date';
$string['convocatoriahelp'] = 'Help for calls';
$string['convocatorianame'] = 'Call name';
$string['convocatoriaopened'] = 'Call opened';
$string['convocatoriapdf'] = 'Call PDF';
$string['convocatoriareopened'] = 'Call reopened';
$string['convocatorias_dashboard_desc'] = 'Manage all calls';
$string['convocatoriastartdate'] = 'Start date';
$string['convocatoriastatus'] = 'Call status';
$string['convocatoriaterms'] = 'Call terms';
$string['convocatoriaupdated'] = 'Call updated';
$string['convocatoriavacancies'] = 'Call vacancies';
$string['convocatoriavacancycount'] = 'Number of vacancies';
$string['copy_placeholder'] = 'Copy placeholder';
$string['count'] = 'Count';
$string['courses'] = 'Courses';
$string['coverletter'] = 'Cover letter';
$string['create'] = 'Create';
$string['createaccounttoapply'] = 'Create account to apply';
$string['createcommittee'] = 'Create committee';
$string['createcompanies'] = 'Create companies';
$string['createdby'] = 'Created by';
$string['createvacancyinconvocatoriadesc'] = 'Create vacancy in this call';

// CSV import.
$string['csvdelimiter'] = 'CSV delimiter';
$string['csvexample'] = 'CSV example';
$string['csvexample_desc'] = 'Example CSV format';
$string['csvexample_tip'] = 'Use this format for imports';
$string['csvfile'] = 'CSV file';
$string['csvformat'] = 'CSV format';
$string['csvformat_desc'] = 'Expected CSV format';
$string['csvimporterror'] = 'CSV import error';
$string['csvinvalidtype'] = 'Invalid type in CSV';
$string['csvlineerror'] = 'Error in line {$a}';
$string['csvusernotfound'] = 'User not found';
$string['currentpassword'] = 'Current password';
$string['currentpassword_invalid'] = 'Current password is invalid';
$string['currentpassword_required'] = 'Current password is required';
$string['currentstatus'] = 'Current status';
$string['currentworkload'] = 'Current workload';
$string['dailyapplications'] = 'Daily applications';

// Dashboard strings.
$string['dashboard_admin_welcome'] = 'Welcome, Administrator';
$string['dashboard_applicant_welcome'] = 'Welcome to Job Board';
$string['dashboard_manager_welcome'] = 'Welcome, Manager';
$string['dashboard_reviewer_welcome'] = 'Welcome, Reviewer';

// Data export.
$string['dataexport'] = 'Data export';
$string['dataexport:consent'] = 'Consent information';
$string['dataexport:exportdate'] = 'Export date';
$string['dataexport:personal'] = 'Personal data';
$string['dataexport:title'] = 'Data export report';
$string['dataexport:userinfo'] = 'User information';
$string['dataretentiondays'] = 'Data retention days';
$string['datatorexport'] = 'Data to export';
$string['datatreatmentpolicytitle'] = 'Data treatment policy';

// Date and time.
$string['dateandtime'] = 'Date and time';
$string['dateapplied'] = 'Date applied';
$string['datefrom'] = 'From date';
$string['dates'] = 'Dates';
$string['datesubmitted'] = 'Date submitted';
$string['dateto'] = 'To date';
$string['days'] = 'days';
$string['daysleft'] = 'Days left';
$string['daysremaining'] = '{$a} days remaining';
$string['deadlineprogress'] = 'Deadline progress';
$string['deadlinewarning'] = 'Only {$a} days left to apply';
$string['deadlinewarning_title'] = 'Deadline approaching!';

// Declaration.
$string['declaration'] = 'Declaration';
$string['declarationaccept'] = 'I accept the declaration';
$string['declarationrequired'] = 'Declaration is required';
$string['declarationtext'] = 'Declaration text';
$string['defaultdatatreatmentpolicy'] = 'Default data treatment policy';
$string['defaultexemptiontype'] = 'Default exemption type';
$string['defaultmaxagedays'] = 'Default maximum document age (days)';
$string['defaultstatus'] = 'Default status';
$string['defaultvalidfrom'] = 'Default valid from';
$string['defaultvaliduntil'] = 'Default valid until';
$string['department'] = 'Department';
$string['desirable'] = 'Desirable';
$string['digitalsignature'] = 'Digital signature';
$string['disabled'] = 'Disabled';

// Document conditions.
$string['doc_condition_iser_exempt'] = 'ISER exempt';
$string['doc_condition_men_only'] = 'Men only';
$string['doc_condition_profession_exempt'] = 'Profession exempt';
$string['doc_condition_women_only'] = 'Women only';

// Document categories.
$string['doccategory_academic'] = 'Academic';
$string['doccategory_background'] = 'Background checks';
$string['doccategory_financial'] = 'Financial';
$string['doccategory_health'] = 'Health';
$string['doccategory_identity'] = 'Identity';
$string['doccategory_professional'] = 'Professional';
$string['docrequirements'] = 'Document requirements';

// Document status.
$string['docstatus:approved'] = 'Approved';
$string['docstatus:pending'] = 'Pending';
$string['docstatus:rejected'] = 'Rejected';

// Document type names.
$string['doctype_antecedentes_contraloria'] = 'Comptroller background check';
$string['doctype_antecedentes_policia'] = 'Police background check';
$string['doctype_antecedentes_procuraduria'] = 'Attorney general background check';
$string['doctype_cedula'] = 'ID card';
$string['doctype_certificado_medico'] = 'Medical certificate';
$string['doctype_cuenta_bancaria'] = 'Bank account certificate';
$string['doctype_eps'] = 'EPS certificate';
$string['doctype_isrequired_help'] = 'This document is mandatory';
$string['doctype_libreta_militar'] = 'Military booklet';
$string['doctype_pension'] = 'Pension certificate';
$string['doctype_rnmc'] = 'RNMC certificate';
$string['doctype_rut'] = 'RUT';
$string['doctype_sigep'] = 'SIGEP form';
$string['doctype_tarjeta_profesional'] = 'Professional card';
$string['doctype_titulo_postgrado'] = 'Postgraduate degree';
$string['doctype_titulo_pregrado'] = 'Undergraduate degree';
$string['doctypecreated'] = 'Document type created';
$string['doctypedeleted'] = 'Document type deleted';
$string['doctypelist'] = 'Document types';
$string['doctypes_desc'] = 'Manage document types';
$string['doctypeshelp'] = 'Configure required documents';
$string['doctypeupdated'] = 'Document type updated';

// Document actions.
$string['documentchecklist'] = 'Document checklist';
$string['documentexpired'] = 'Document has expired';
$string['documentinfo'] = 'Document information';
$string['documentissuedate'] = 'Issue date';
$string['documentlist'] = 'Document list';
$string['documentnotfound'] = 'Document not found';
$string['documentnumber'] = 'Document number';
$string['documentpreview'] = 'Document preview';
$string['documentref'] = 'Document reference';
$string['documentref_desc'] = 'External document reference';
$string['documentrejected'] = 'Document rejected';
$string['documentrequired'] = 'This document is required';
$string['documentreuploaded'] = 'Document reuploaded';
$string['documentsapproved'] = 'Documents approved';
$string['documentsettings'] = 'Document settings';
$string['documentshelp'] = 'Document help';
$string['documentsrejected'] = 'Documents rejected';
$string['documentsremaining'] = '{$a} documents remaining';
$string['documentsreviewed'] = 'Documents reviewed';
$string['documentsvalidated'] = 'Documents validated';
$string['documenttype'] = 'Document type';
$string['documenttypes'] = 'Document types';
$string['documentvalidated'] = 'Document validated';
$string['downloadcsvtemplate'] = 'Download CSV template';
$string['downloadtoview'] = 'Download to view';
$string['draft'] = 'Draft';
$string['dryrunmode'] = 'Dry run mode';
$string['dryrunresults'] = 'Dry run results';
$string['duration'] = 'Duration';
$string['edit_template'] = 'Edit template';
$string['editconvocatoria'] = 'Edit call';
$string['editprofile'] = 'Edit profile';
$string['education'] = 'Education';
$string['educationlevel'] = 'Education level';
$string['email_action_reupload'] = 'Please reupload the document';
$string['email_updated'] = 'Email updated';
$string['emailnotmatch'] = 'Emails do not match';
$string['emailtemplates'] = 'Email templates';
$string['emailtemplates_desc'] = 'Configure email notifications';
$string['enableapi'] = 'Enable API';
$string['enabled'] = 'Enabled';
$string['enableddoctypes'] = 'Enabled document types';
$string['enableencryption'] = 'Enable encryption';
$string['enablepublicpage'] = 'Enable public page';
$string['enablepublicpage_desc'] = 'Show public vacancies page';
$string['enableselfregistration'] = 'Enable self-registration';
$string['enableselfregistration_desc'] = 'Allow users to create accounts';
$string['encoding'] = 'Encoding';
$string['encryption:backupinstructions'] = 'Backup encryption key';
$string['encryption:nokeytobackup'] = 'No encryption key to backup';
$string['enddate'] = 'End date';
$string['entries'] = 'entries';
$string['epsmaxdays'] = 'EPS certificate max age (days)';

// Error messages.
$string['error:alreadyapplied'] = 'You have already applied';
$string['error:applicationlimitreached'] = 'Application limit reached';
$string['error:cannotdelete_hasapplications'] = 'Cannot delete: has applications';
$string['error:cannotdeleteconvocatoria'] = 'Cannot delete call';
$string['error:cannotreopenconvocatoria'] = 'Cannot reopen call';
$string['error:codealreadyexists'] = 'Code already exists';
$string['error:codeexists'] = 'This code is already in use';
$string['error:consentrequired'] = 'Consent is required';
$string['error:convocatoriacodeexists'] = 'Call code exists';
$string['error:convocatoriadatesinvalid'] = 'Invalid call dates';
$string['error:convocatoriahasnovacancies'] = 'Call has no vacancies';
$string['error:convocatoriarequired'] = 'Call is required';
$string['error:doctypeinuse'] = 'Document type is in use';
$string['error:invalidage'] = 'Invalid age';
$string['error:invalidcode'] = 'Invalid code';
$string['error:invaliddates'] = 'Invalid dates';
$string['error:invalidpublicationtype'] = 'Invalid publication type';
$string['error:invalidstatus'] = 'Invalid status';
$string['error:invalidurl'] = 'Invalid URL';
$string['error:occasionalrequiresexperience'] = 'Occasional position requires experience';
$string['error:pastdate'] = 'Date cannot be in the past';
$string['error:requiredfield'] = 'This field is required';
$string['error:schedulingconflict'] = 'Scheduling conflict';
$string['error:singleapplicationonly'] = 'Only one application allowed';
$string['error:vacancyclosed'] = 'Vacancy is closed';
$string['error:vacancynotfound'] = 'Vacancy not found';
$string['evaluations'] = 'Evaluations';
$string['evaluatorshelp'] = 'Evaluators help';

// Event strings.
$string['event:applicationcreated'] = 'Application created';
$string['event:documentuploaded'] = 'Document uploaded';
$string['event:statuschanged'] = 'Status changed';
$string['event:vacancyclosed'] = 'Vacancy closed';
$string['event:vacancycreated'] = 'Vacancy created';
$string['event:vacancydeleted'] = 'Vacancy deleted';
$string['event:vacancypublished'] = 'Vacancy published';
$string['event:vacancyupdated'] = 'Vacancy updated';
$string['example'] = 'Example';

// Exemption strings.
$string['exempteddocs'] = 'Exempted documents';
$string['exempteddocs_desc'] = 'Documents exempted from requirements';
$string['exempteddoctypes'] = 'Exempted document types';
$string['exemption'] = 'Exemption';
$string['exemptionactive'] = 'Exemption is active';
$string['exemptionapplied'] = 'Exemption applied';
$string['exemptioncreated'] = 'Exemption created';
$string['exemptiondetails'] = 'Exemption details';
$string['exemptionerror'] = 'Exemption error';
$string['exemptionlist'] = 'Exemption list';
$string['exemptionnotice'] = 'Exemption notice';
$string['exemptionreason'] = 'Exemption reason';
$string['exemptionreduceddocs'] = 'Reduced document requirements';
$string['exemptionrevoked'] = 'Exemption revoked';
$string['exemptionrevokeerror'] = 'Error revoking exemption';
$string['exemptions'] = 'Exemptions';
$string['exemptiontype'] = 'Exemption type';
$string['exemptiontype_desc'] = 'Type of exemption';
$string['exemptiontype_documentos_recientes'] = 'Recent documents';
$string['exemptiontype_historico_iser'] = 'ISER history';
$string['exemptiontype_recontratacion'] = 'Rehiring';
$string['exemptiontype_traslado_interno'] = 'Internal transfer';
$string['exemptionupdated'] = 'Exemption updated';
$string['exemptionusagehistory'] = 'Exemption usage history';
$string['existingvacancycommittee'] = 'Existing vacancy committee';
$string['expired'] = 'Expired';
$string['expiredexemptions'] = 'Expired exemptions';
$string['explore'] = 'Explore';
$string['explorevacancias'] = 'Explore vacancies';
$string['export'] = 'Export';
$string['exportcsv'] = 'Export to CSV';
$string['exportdata'] = 'Export data';
$string['exportdata_desc'] = 'Export application data';
$string['exportdownload'] = 'Download export';
$string['exporterror'] = 'Export error';
$string['exportexcel'] = 'Export to Excel';
$string['exportwarning_files'] = 'Warning: file attachments not included';
$string['externalurl'] = 'External URL';

// Faculty and files.
$string['facultieswithoutcommittee'] = 'Faculties without committee';
$string['facultycommitteedefaultname'] = 'Faculty committee';
$string['facultyvacancies'] = 'Faculty vacancies';
$string['filename'] = 'Filename';
$string['files'] = 'Files';
$string['fullexport'] = 'Full export';
$string['fullexport_info'] = 'Export all data';
$string['fullname'] = 'Full name';
$string['gendercondition'] = 'Gender condition';
$string['generalsettings'] = 'General settings';
$string['generatedon'] = 'Generated on';
$string['gotocreateconvocatoria'] = 'Create new call';

// Guidelines.
$string['guideline1'] = 'Review all document requirements carefully';
$string['guideline2'] = 'Upload legible documents in PDF format';
$string['guideline3'] = 'Ensure all personal information is accurate';
$string['guideline4'] = 'Submit before the deadline';
$string['guideline_review1'] = 'Verify document authenticity';
$string['guideline_review2'] = 'Check all checklist items';
$string['guideline_review3'] = 'Provide clear reasons for rejection';
$string['guideline_review4'] = 'Notify applicant of any issues';
$string['hasnote'] = 'Has notes';
$string['html_support'] = 'HTML supported';

// Import.
$string['import'] = 'Import';
$string['importcomplete'] = 'Import complete';
$string['importdata'] = 'Import data';
$string['importdata_desc'] = 'Import data from file';
$string['importedapplications'] = 'Imported applications';
$string['importedconvocatorias'] = 'Imported calls';
$string['importeddoctypes'] = 'Imported document types';
$string['importeddocuments'] = 'Imported documents';
$string['importedemails'] = 'Imported emails';
$string['importedexemptions'] = 'Imported exemptions';
$string['importedfiles'] = 'Imported files';
$string['importednote'] = 'Import note';
$string['importedsettings'] = 'Imported settings';
$string['importedskipped'] = 'Skipped';
$string['importedsuccess'] = 'Successfully imported';
$string['importedvacancies'] = 'Imported vacancies';
$string['importerror'] = 'Import error';
$string['importerror_alreadyexempt'] = 'User already has exemption';
$string['importerror_createfailed'] = 'Failed to create record';
$string['importerror_usernotfound'] = 'User not found';
$string['importerror_vacancyexists'] = 'Vacancy already exists';
$string['importerrors'] = 'Import errors';
$string['importexemptions'] = 'Import exemptions';
$string['importingfrom'] = 'Importing from';
$string['importinstructions'] = 'Import instructions';
$string['importinstructionstext'] = 'Follow the CSV format below';
$string['importoptions'] = 'Import options';
$string['importresults'] = 'Import results';
$string['importupload'] = 'Upload file';
$string['importvacancies'] = 'Import vacancies';
$string['importvacancies_desc'] = 'Import vacancies from CSV';
$string['importvacancies_help'] = 'Help for importing vacancies';
$string['importwarning'] = 'Import warning';
$string['inprogress'] = 'In progress';
$string['inputtype'] = 'Input type';
$string['inputtype_file'] = 'File upload';
$string['inputtype_number'] = 'Number';
$string['inputtype_text'] = 'Text';
$string['inputtype_url'] = 'URL';
$string['install_defaults'] = 'Install defaults';
$string['institutionname'] = 'Institution name';
$string['internal'] = 'Internal';

// Interview.
$string['interviewcancelled'] = 'Interview cancelled';
$string['interviewcompleted'] = 'Interview completed';
$string['interviewdate'] = 'Interview date';
$string['interviewers'] = 'Interviewers';
$string['interviewfeedback'] = 'Interview feedback';
$string['interviewinstructions'] = 'Interview instructions';
$string['interviews'] = 'Interviews';
$string['interviewscheduleerror'] = 'Error scheduling interview';
$string['interviewtype'] = 'Interview type';
$string['interviewtype_inperson'] = 'In person';
$string['interviewtype_phone'] = 'Phone';
$string['interviewtype_video'] = 'Video call';
$string['invalidmigrationfile'] = 'Invalid migration file';
$string['invalidrole'] = 'Invalid role';
$string['iomad_department'] = 'IOMAD Department';
$string['iomadoptions'] = 'IOMAD options';
$string['iomadsettings'] = 'IOMAD settings';
$string['iserexempted'] = 'ISER exempted';
$string['iserexempted_help'] = 'This document is exempt for ISER users';
$string['items'] = 'items';
$string['jobboard'] = 'Job Board';
$string['legacyvacancycommittee'] = 'Legacy vacancy committee';
$string['location'] = 'Location';
$string['locationorurl'] = 'Location or URL';
$string['loginrequiredtoapply'] = 'Please log in to apply';
$string['mainmenutitle'] = 'Job Board';
$string['mainmenutitle_desc'] = 'Title shown in main menu';

// Manage strings.
$string['manageapplications'] = 'Manage applications';
$string['managecommittees'] = 'Manage committees';
$string['manageconvocatorias'] = 'Manage calls';
$string['managedoctypes'] = 'Manage document types';
$string['manageexemptions'] = 'Manage exemptions';
$string['manageexemptions_desc'] = 'Add and manage exemptions';
$string['manageroles'] = 'Manage roles';
$string['manageroles_desc'] = 'Configure committee roles';
$string['manageusers'] = 'Manage users';
$string['managevacancies'] = 'Manage vacancies';
$string['manualassign'] = 'Manual assignment';
$string['markedasnoshow'] = 'Marked as no-show';
$string['markednoshow'] = 'No-show';
$string['maxapplicationsperuser'] = 'Maximum applications per user';
$string['maxfilesize'] = 'Maximum file size';
$string['maxperreviewer'] = 'Maximum per reviewer';
$string['memberadded'] = 'Member added';
$string['memberadderror'] = 'Error adding member';
$string['membercount'] = 'Member count';
$string['memberremoved'] = 'Member removed';
$string['memberremoveerror'] = 'Error removing member';
$string['members'] = 'Members';
$string['menonly'] = 'Men only';

// Migration.
$string['migrate_includes_applications'] = 'Includes applications';
$string['migrate_includes_convocatorias'] = 'Includes calls';
$string['migrate_includes_doctypes'] = 'Includes document types';
$string['migrate_includes_vacancies'] = 'Includes vacancies';
$string['migrateplugin'] = 'Migrate plugin';
$string['migrateplugin_desc'] = 'Migrate from previous version';
$string['migrateplugin_full_desc'] = 'Full plugin migration';
$string['migrationfile'] = 'Migration file';
$string['migrationinfo_desc'] = 'Migration information';
$string['migrationinfo_title'] = 'Migration';

// Modality.
$string['modality:distancia'] = 'Distance';
$string['modality:hibrida'] = 'Hybrid';
$string['modality:presencial'] = 'In-person';
$string['modality:virtual'] = 'Virtual';
$string['modifiedby'] = 'Modified by';
$string['multi_tenant'] = 'Multi-tenant';
$string['multipledocs_notice'] = 'Multiple documents allowed';
$string['myapplicationcount'] = 'My applications';
$string['myapplications_desc'] = 'View your applications';
$string['mypendingreviews'] = 'My pending reviews';
$string['myreviews'] = 'My reviews';
$string['myreviews_desc'] = 'View your review assignments';

// Navigation.
$string['navigationsettings'] = 'Navigation settings';
$string['navigationsettings_desc'] = 'Configure navigation options';
$string['needhelp'] = 'Need help?';
$string['needsattention'] = 'Needs attention';
$string['newdocument'] = 'New document';
$string['newvacancy'] = 'New vacancy';
$string['nextapplication'] = 'Next application';
$string['no_templates'] = 'No templates found';
$string['noapplicationsfound'] = 'No applications found';
$string['noassignments'] = 'No assignments';
$string['nocommitteeforthisvacancy'] = 'No committee for this vacancy';
$string['nocommittees'] = 'No committees';
$string['noconvocatorias'] = 'No calls';
$string['noconvocatoriasavailable'] = 'No calls available';
$string['nodata'] = 'No data';
$string['nodoctypes'] = 'No document types';
$string['nodocuments'] = 'No documents';
$string['nodocumentspending'] = 'No documents pending';
$string['nodocumentstoreview'] = 'No documents to review';
$string['noexemptions'] = 'No exemptions';
$string['noexemptionusage'] = 'No exemption usage';
$string['noexpiry'] = 'No expiry';
$string['noobservations'] = 'No observations';
$string['noreason'] = 'No reason provided';
$string['norejections'] = 'No rejections';
$string['noreviewers'] = 'No reviewers';
$string['nosecretaryoptional'] = 'Secretary optional';
$string['noshow'] = 'No-show';
$string['notes'] = 'Notes';
$string['notes_desc'] = 'Additional notes';
$string['notifications'] = 'Notifications';
$string['nounassignedapplications'] = 'No unassigned applications';
$string['nousersassigned'] = 'No users assigned';
$string['novacancies'] = 'No vacancies';
$string['novacanciesfound'] = 'No vacancies found';
$string['numdocs'] = 'Number of documents';
$string['of'] = 'of';

// Open and overview.
$string['openconvocatoria'] = 'Open call';
$string['opendate'] = 'Open date';
$string['openmigrationtool'] = 'Open migration tool';
$string['openvacancies'] = 'Open vacancies';
$string['optionalcolumns'] = 'Optional columns';
$string['optionalnotes'] = 'Optional notes';
$string['overallrating'] = 'Overall rating';
$string['overview'] = 'Overview';
$string['overwriteexisting'] = 'Overwrite existing';
$string['password_change_optional'] = 'Leave blank to keep current password';
$string['password_updated'] = 'Password updated';
$string['passwordsdiffer'] = 'Passwords do not match';

// Pending.
$string['pending'] = 'Pending';
$string['pending_docs_alert'] = 'Documents pending review';
$string['pendingassignment'] = 'Pending assignment';
$string['pendingassignments'] = 'Pending assignments';
$string['pendingbytype'] = 'Pending by type';
$string['pendingdocs'] = 'Pending documents';
$string['pendingdocuments'] = 'Pending documents';
$string['pendingreview'] = 'Pending review';
$string['pendingreviews'] = 'Pending reviews';
$string['pendingreviews_alert'] = 'You have pending reviews';
$string['pendingvalidation'] = 'Pending validation';
$string['pensionmaxdays'] = 'Pension certificate max age (days)';
$string['percentage'] = 'Percentage';
$string['period'] = 'Period';
$string['personalinfo'] = 'Personal information';
$string['placeholders'] = 'Placeholders';
$string['placeholders_help'] = 'Available placeholders for templates';
$string['pluginsettings'] = 'Plugin settings';
$string['pluginsettings_desc'] = 'Configure plugin settings';
$string['positions'] = 'Positions';
$string['previewconfirm'] = 'Confirm preview';
$string['previewdocument'] = 'Preview document';
$string['previewmode'] = 'Preview mode';
$string['previewmodenotice'] = 'Preview mode - changes not saved';
$string['previewonly'] = 'Preview only';
$string['previewtotal'] = 'Total to preview';
$string['previewunavailable'] = 'Preview unavailable';
$string['previousapplication'] = 'Previous application';
$string['professionexempt'] = 'Profession exempt';
$string['profilereview'] = 'Profile review';
$string['profilereview_info'] = 'Review and update your profile';
$string['public'] = 'Public';
$string['publicationtype'] = 'Publication type';
$string['publicationtype:internal'] = 'Internal';
$string['publicationtype:public'] = 'Public';
$string['publicpagedesc'] = 'Public page description';
$string['publicpagedescription'] = 'Public page description';
$string['publicpagedescription_desc'] = 'Description for public page';
$string['publicpagesettings'] = 'Public page settings';
$string['publicpagesettings_desc'] = 'Configure public page';
$string['publicpagetitle'] = 'Public page title';
$string['publicpagetitle_desc'] = 'Title for public page';
$string['publicvacancies'] = 'Public vacancies';
$string['publish'] = 'Publish';
$string['published'] = 'Published';
$string['publishedvacancies'] = 'Published vacancies';

// Quick actions.
$string['quickactions'] = 'Quick actions';
$string['quicktips'] = 'Quick tips';

// Ratings.
$string['rating_excellent'] = 'Excellent';
$string['rating_fair'] = 'Fair';
$string['rating_good'] = 'Good';
$string['rating_poor'] = 'Poor';
$string['rating_verygood'] = 'Very good';
$string['readytoapply'] = 'Ready to apply';

// reCAPTCHA.
$string['recaptcha_enabled'] = 'Enable reCAPTCHA';
$string['recaptcha_enabled_desc'] = 'Require reCAPTCHA for registration';
$string['recaptcha_failed'] = 'reCAPTCHA verification failed';
$string['recaptcha_required'] = 'Please complete the reCAPTCHA';
$string['recaptcha_secretkey'] = 'Secret key';
$string['recaptcha_secretkey_desc'] = 'reCAPTCHA secret key';
$string['recaptcha_sitekey'] = 'Site key';
$string['recaptcha_sitekey_desc'] = 'reCAPTCHA site key';
$string['recaptcha_v2'] = 'reCAPTCHA v2';
$string['recaptcha_v3'] = 'reCAPTCHA v3';
$string['recaptcha_v3_threshold'] = 'Score threshold';
$string['recaptcha_v3_threshold_desc'] = 'Minimum score for v3';
$string['recaptcha_version'] = 'reCAPTCHA version';
$string['recaptcha_version_desc'] = 'Select reCAPTCHA version';
$string['recaptchasettings'] = 'reCAPTCHA settings';
$string['recaptchasettings_desc'] = 'Configure reCAPTCHA';

// Recommendations.
$string['recommend_furtherreview'] = 'Recommend further review';
$string['recommend_hire'] = 'Recommend for hire';
$string['recommend_reject'] = 'Recommend rejection';
$string['recommendation'] = 'Recommendation';
$string['recordsperpage'] = 'Records per page';
$string['refresh'] = 'Refresh';

// Reject.
$string['reject'] = 'Reject';
$string['rejectdocument'] = 'Reject document';
$string['rejected'] = 'Rejected';
$string['rejecteddocuments'] = 'Rejected documents';
$string['rejectionreason'] = 'Rejection reason';
$string['rejectionreasons'] = 'Rejection reasons';
$string['rejectreason'] = 'Reason for rejection';
$string['rejectreason_expired'] = 'Document has expired';
$string['rejectreason_illegible'] = 'Document is illegible';
$string['rejectreason_incomplete'] = 'Document is incomplete';
$string['rejectreason_mismatch'] = 'Information does not match';
$string['rejectreason_placeholder'] = 'Enter rejection reason';
$string['rejectreason_wrongtype'] = 'Wrong document type';
$string['rejectselected'] = 'Reject selected';
$string['removemember'] = 'Remove member';
$string['reopen'] = 'Reopen';
$string['reopenconvocatoria'] = 'Reopen call';

// Reports.
$string['reportapplications'] = 'Applications report';
$string['reportdocuments'] = 'Documents report';
$string['reportoverview'] = 'Overview report';
$string['reportreviewers'] = 'Reviewers report';
$string['reports_desc'] = 'View and export reports';
$string['reportsanddata'] = 'Reports and data';
$string['reporttimeline'] = 'Timeline report';
$string['requiredcolumns'] = 'Required columns';
$string['requireddoctypes'] = 'Required document types';
$string['requirements'] = 'Requirements';
$string['rescheduledby'] = 'Rescheduled by';
$string['reschedulednote'] = 'Rescheduled note';
$string['reset_to_default'] = 'Reset to default';
$string['restarttour'] = 'Restart tour';
$string['result'] = 'Result';
$string['reuploaddocument'] = 'Reupload document';
$string['reuploadhelp'] = 'Upload a corrected version';

// Review.
$string['review_dashboard_desc'] = 'Review dashboard';
$string['reviewall'] = 'Review all';
$string['reviewallapproved'] = 'All reviews approved';
$string['reviewapplication'] = 'Review application';
$string['reviewapplications'] = 'Review applications';
$string['reviewdocuments'] = 'Review documents';
$string['reviewed'] = 'Reviewed';
$string['reviewedby'] = 'Reviewed by';
$string['reviewerassigned'] = 'Reviewer assigned';
$string['reviewerperformance'] = 'Reviewer performance';
$string['reviewertasks'] = 'Reviewer tasks';
$string['reviewerunassigned'] = 'Reviewer unassigned';
$string['reviewerworkload'] = 'Reviewer workload';
$string['reviewguidelines'] = 'Review guidelines';
$string['reviewhasrejected'] = 'Review has rejections';
$string['reviewhelp_text'] = 'Review help text';
$string['reviewobservations'] = 'Review observations';
$string['reviewobservations_placeholder'] = 'Enter observations';
$string['reviewoverview'] = 'Review overview';
$string['reviewprogress'] = 'Review progress';
$string['reviewsteps_tooltip'] = 'Review steps';
$string['reviewsubmitted'] = 'Review submitted';
$string['reviewsubmitted_with_notification'] = 'Review submitted and applicant notified';
$string['reviewtips'] = 'Review tips';

// Revoke.
$string['revoke'] = 'Revoke';
$string['revoked'] = 'Revoked';
$string['revokedby'] = 'Revoked by';
$string['revokedexemptions'] = 'Revoked exemptions';
$string['revokeexemption'] = 'Revoke exemption';
$string['revokereason'] = 'Revoke reason';

// Roles.
$string['role_administrator'] = 'Administrator';
$string['role_applicant'] = 'Applicant';
$string['role_chair'] = 'Committee chair';
$string['role_committee'] = 'Committee member';
$string['role_committee_desc'] = 'Can evaluate candidates';
$string['role_coordinator'] = 'Coordinator';
$string['role_coordinator_desc'] = 'Coordinates the selection process';
$string['role_evaluator'] = 'Evaluator';
$string['role_manager'] = 'Manager';
$string['role_observer'] = 'Observer';
$string['role_reviewer_desc'] = 'Can review and validate documents';
$string['role_secretary'] = 'Committee secretary';
$string['rolenotcreated'] = 'Role not created';
$string['row'] = 'Row';
$string['samplecsv'] = 'Sample CSV';
$string['saveresults'] = 'Save results';
$string['scheduledinterviews'] = 'Scheduled interviews';
$string['scheduleinterview'] = 'Schedule interview';
$string['schedulenewinterview'] = 'Schedule new interview';

// Search.
$string['searchapplicant'] = 'Search applicant';
$string['searchbyusername'] = 'Search by username';
$string['searchuser'] = 'Search user';
$string['searchusers'] = 'Search users';
$string['searchusersplaceholder'] = 'Type to search users';
$string['searchvacancies'] = 'Search vacancies';
$string['securitysettings'] = 'Security settings';
$string['selectall'] = 'Select all';
$string['selectatleastone'] = 'Select at least one';
$string['selectbackgrounddocs'] = 'Select background documents';
$string['selectcompany'] = 'Select company';
$string['selectcontracttype'] = 'Select contract type';
$string['selectconvocatoria'] = 'Select call';
$string['selectconvocatoriafirst'] = 'Select a call first';
$string['selectdepartment'] = 'Select department';
$string['selected'] = 'Selected';
$string['selectfaculty'] = 'Select faculty';
$string['selectidentitydocs'] = 'Select identity documents';
$string['selectinterviewers'] = 'Select interviewers';
$string['selectionrate'] = 'Selection rate';
$string['selectmodality'] = 'Select modality';
$string['selectmultiplehelp'] = 'Hold Ctrl to select multiple';
$string['selectreason'] = 'Select reason';
$string['selectreviewer'] = 'Select reviewer';
$string['selectroletoassign'] = 'Select role to assign';
$string['selecttype'] = 'Select type';
$string['selectusers'] = 'Select users';

// Share.
$string['share'] = 'Share';
$string['shareonfacebook'] = 'Share on Facebook';
$string['shareonlinkedin'] = 'Share on LinkedIn';
$string['shareontwitter'] = 'Share on Twitter';
$string['sharepage'] = 'Share page';
$string['sharethisvacancy'] = 'Share this vacancy';
$string['showing'] = 'Showing';
$string['showingxofy'] = 'Showing {$a->from} to {$a->to} of {$a->total}';
$string['showinmainmenu'] = 'Show in main menu';
$string['showinmainmenu_desc'] = 'Display job board in main menu';
$string['showpublicnavlink'] = 'Show public navigation link';
$string['showpublicnavlink_desc'] = 'Show link to public vacancies';
$string['signaturetoooshort'] = 'Signature too short';

// Signup strings.
$string['signup_academic_header'] = 'Academic information';
$string['signup_account_header'] = 'Account information';
$string['signup_already_account'] = 'Already have an account?';
$string['signup_applying_for'] = 'Applying for';
$string['signup_birthdate'] = 'Date of birth';
$string['signup_birthdate_minage'] = 'Minimum age required';
$string['signup_check_spam'] = 'Check your spam folder';
$string['signup_company_help'] = 'Select your location';
$string['signup_companyinfo'] = 'Location information';
$string['signup_contactinfo'] = 'Contact information';
$string['signup_createaccount'] = 'Create account';
$string['signup_dataaccuracy_accept'] = 'I confirm my information is accurate';
$string['signup_dataaccuracy_required'] = 'Data accuracy confirmation required';
$string['signup_datatreatment_accept'] = 'I accept the data treatment policy';
$string['signup_datatreatment_required'] = 'Data treatment acceptance required';
$string['signup_degree_title'] = 'Degree title';
$string['signup_department_region'] = 'Department/Region';
$string['signup_doctype'] = 'Document type';
$string['signup_doctype_cc'] = 'Citizenship card';
$string['signup_doctype_ce'] = 'Foreign ID';
$string['signup_doctype_passport'] = 'Passport';
$string['signup_doctype_pep'] = 'Special permit';
$string['signup_doctype_ppt'] = 'Temporary permit';
$string['signup_edu_doctor'] = 'Doctorate';
$string['signup_edu_doctorate'] = 'Doctorate';
$string['signup_edu_especialista'] = 'Specialist';
$string['signup_edu_highschool'] = 'High school';
$string['signup_edu_magister'] = 'Masters';
$string['signup_edu_masters'] = 'Masters';
$string['signup_edu_postdoctorate'] = 'Postdoctorate';
$string['signup_edu_profesional'] = 'Professional';
$string['signup_edu_specialization'] = 'Specialization';
$string['signup_edu_technical'] = 'Technical';
$string['signup_edu_technological'] = 'Technological';
$string['signup_edu_tecnico'] = 'Technical';
$string['signup_edu_tecnologo'] = 'Technologist';
$string['signup_edu_undergraduate'] = 'Undergraduate';
$string['signup_education_level'] = 'Education level';
$string['signup_email_instruction_1'] = 'Check your email inbox';
$string['signup_email_instruction_2'] = 'Click the confirmation link';
$string['signup_email_instruction_3'] = 'Complete your profile';
$string['signup_email_instructions_title'] = 'Next steps';
$string['signup_error_creating'] = 'Error creating account';
$string['signup_exp_1_3'] = '1-3 years';
$string['signup_exp_3_5'] = '3-5 years';
$string['signup_exp_5_10'] = '5-10 years';
$string['signup_exp_less_1'] = 'Less than 1 year';
$string['signup_exp_more_10'] = 'More than 10 years';
$string['signup_exp_none'] = 'No experience';
$string['signup_experience_years'] = 'Years of experience';
$string['signup_expertise_area'] = 'Area of expertise';
$string['signup_gender'] = 'Gender';
$string['signup_gender_female'] = 'Female';
$string['signup_gender_male'] = 'Male';
$string['signup_gender_other'] = 'Other';
$string['signup_gender_prefer_not'] = 'Prefer not to say';
$string['signup_idnumber'] = 'ID number';
$string['signup_idnumber_exists'] = 'This ID number is already registered';
$string['signup_idnumber_exists_as_user'] = 'User already exists with this ID';
$string['signup_idnumber_tooshort'] = 'ID number is too short';
$string['signup_intro'] = 'Create your account to apply';
$string['signup_personalinfo'] = 'Personal information';
$string['signup_phone_home'] = 'Home phone';
$string['signup_phone_mobile'] = 'Mobile phone';
$string['signup_privacy_text'] = 'Privacy policy text';
$string['signup_professional_profile'] = 'Professional profile';
$string['signup_required_fields'] = 'Required fields';
$string['signup_step_academic'] = 'Academic';
$string['signup_step_account'] = 'Account';
$string['signup_step_confirm'] = 'Confirm';
$string['signup_step_contact'] = 'Contact';
$string['signup_step_personal'] = 'Personal';
$string['signup_success_message'] = 'Account created. Check {$a} for confirmation.';
$string['signup_success_title'] = 'Registration successful';
$string['signup_terms_accept'] = 'I accept the terms and conditions';
$string['signup_terms_required'] = 'Terms acceptance required';
$string['signup_termsheader'] = 'Terms and conditions';
$string['signup_title'] = 'Create account';
$string['signup_username_is_idnumber'] = 'Username will be your ID number';

// Sort and start.
$string['sortby'] = 'Sort by';
$string['sortorder'] = 'Sort order';
$string['startdate'] = 'Start date';

// Status strings.
$string['status:assigned'] = 'Assigned';
$string['status:closed'] = 'Closed';
$string['status:draft'] = 'Draft';
$string['status:published'] = 'Published';
$string['statushistory'] = 'Status history';

// Steps.
$string['step'] = 'Step';
$string['step_complete'] = 'Complete';
$string['step_consent'] = 'Consent';
$string['step_coverletter'] = 'Cover letter';
$string['step_documents'] = 'Documents';
$string['step_examine'] = 'Examine';
$string['step_profile'] = 'Profile';
$string['step_submit'] = 'Submit';
$string['step_validate'] = 'Validate';

// Submit.
$string['submit'] = 'Submit';
$string['submitapplication'] = 'Submit application';
$string['submitreview'] = 'Submit review';
$string['systemconfiguration'] = 'System configuration';
$string['systemmigration'] = 'System migration';

// Tasks.
$string['task:checkclosingvacancies'] = 'Check closing vacancies';
$string['task:cleanupolddata'] = 'Clean up old data';
$string['task:sendnotifications'] = 'Send notifications';

// Template strings.
$string['template_categories'] = 'Template categories';
$string['template_category'] = 'Template category';
$string['template_content'] = 'Template content';
$string['template_delete_failed'] = 'Failed to delete template';
$string['template_deleted_success'] = 'Template deleted';
$string['template_description'] = 'Template description';
$string['template_disabled_success'] = 'Template disabled';
$string['template_enabled'] = 'Template enabled';
$string['template_enabled_desc'] = 'Enable this template';
$string['template_enabled_success'] = 'Template enabled';
$string['template_help_html'] = 'HTML is supported';
$string['template_help_placeholders'] = 'Use placeholders for dynamic content';
$string['template_help_tenant'] = 'Templates can be company-specific';
$string['template_help_title'] = 'Template help';
$string['template_info'] = 'Template information';
$string['template_preview_hint'] = 'Preview will appear here';
$string['template_priority'] = 'Template priority';
$string['template_reset_success'] = 'Template reset';
$string['template_saved_success'] = 'Template saved';
$string['template_settings'] = 'Template settings';
$string['templates_disabled'] = 'Templates disabled';
$string['templates_enabled'] = 'Templates enabled';
$string['templates_installed'] = 'Templates installed';

// Table headers.
$string['thactions'] = 'Actions';
$string['thcode'] = 'Code';
$string['thstatus'] = 'Status';
$string['thtitle'] = 'Title';

// Tips.
$string['tip_authentic'] = 'Ensure documents are authentic';
$string['tip_checkdocs'] = 'Check all documents carefully';
$string['tip_complete'] = 'Complete all fields';
$string['tip_deadline'] = 'Submit before deadline';
$string['tip_download'] = 'Download for offline review';
$string['tip_legible'] = 'Documents must be legible';
$string['tip_saveoften'] = 'Save your work frequently';
$string['title'] = 'Title';
$string['toggle_status'] = 'Toggle status';
$string['togglepreview'] = 'Toggle preview';

// Totals.
$string['total'] = 'Total';
$string['total_templates'] = 'Total templates';
$string['totalapplications'] = 'Total applications';
$string['totalassigned'] = 'Total assigned';
$string['totalassignedusers'] = 'Total assigned users';
$string['totalcommittees'] = 'Total committees';
$string['totalcommmembers'] = 'Total committee members';
$string['totalconvocatorias'] = 'Total calls';
$string['totaldoctypes'] = 'Total document types';
$string['totaldocuments'] = 'Total documents';
$string['totalexemptions'] = 'Total exemptions';
$string['totalpositions'] = 'Total positions';
$string['totalvacancies'] = 'Total vacancies';
$string['type'] = 'Type';

// Unassign.
$string['unassign'] = 'Unassign';
$string['unassignedapplications'] = 'Unassigned applications';
$string['unknownvacancy'] = 'Unknown vacancy';
$string['unpublish'] = 'Unpublish';

// Update.
$string['update_username'] = 'Update username';
$string['update_username_desc'] = 'Allow username updates';
$string['updateexisting'] = 'Update existing';
$string['updateprofile_intro'] = 'Update your profile information';
$string['updateprofile_submit'] = 'Update profile';
$string['updateprofile_success'] = 'Profile updated successfully';
$string['updateprofile_title'] = 'Update profile';
$string['updatestatus'] = 'Update status';

// Upload.
$string['uploaddocument'] = 'Upload document';
$string['uploaded'] = 'Uploaded';
$string['uploadeddocuments'] = 'Uploaded documents';
$string['uploadfailed'] = 'Upload failed';
$string['uploadnewfile'] = 'Upload new file';
$string['urgent'] = 'Urgent';
$string['useridentifier'] = 'User identifier';
$string['username_differs_idnumber'] = 'Username differs from ID number';
$string['username_updated'] = 'Username updated';
$string['usernotfound'] = 'User not found';
$string['usersassigned'] = 'Users assigned';
$string['usersassignedcount'] = '{$a} users assigned';
$string['userunassigned'] = 'User unassigned';

// Vacancies.
$string['vacancies_created'] = 'Vacancies created';
$string['vacancies_dashboard_desc'] = 'Manage all vacancies';
$string['vacancies_skipped'] = 'Vacancies skipped';
$string['vacancies_updated'] = 'Vacancies updated';
$string['vacanciesavailable'] = 'Vacancies available';
$string['vacanciesforconvocatoria'] = 'Vacancies for this call';
$string['vacanciesfound'] = '{$a} vacancies found';
$string['vacancy_inherits_dates'] = 'Vacancy inherits call dates';
$string['vacancy_status_published'] = 'Published';
$string['vacancyclosed'] = 'Vacancy closed';
$string['vacancycode'] = 'Vacancy code';
$string['vacancycreated'] = 'Vacancy created';
$string['vacancydeleted'] = 'Vacancy deleted';
$string['vacancydescription'] = 'Vacancy description';
$string['vacancyinfo'] = 'Vacancy information';
$string['vacancynotfound'] = 'Vacancy not found';
$string['vacancyopen'] = 'Vacancy open';
$string['vacancypublished'] = 'Vacancy published';
$string['vacancyreopened'] = 'Vacancy reopened';
$string['vacancysummary'] = 'Vacancy summary';
$string['vacancytitle'] = 'Vacancy title';
$string['vacancyunpublished'] = 'Vacancy unpublished';
$string['vacancyupdated'] = 'Vacancy updated';

// Validate.
$string['validate'] = 'Validate';
$string['validateall'] = 'Validate all';
$string['validated'] = 'Validated';
$string['validatedocument'] = 'Validate document';
$string['validationchecklist'] = 'Validation checklist';
$string['validationdecision'] = 'Validation decision';
$string['validationrequirements'] = 'Validation requirements';
$string['validfrom'] = 'Valid from';
$string['validityperiod'] = 'Validity period';
$string['validuntil'] = 'Valid until';
$string['verification'] = 'Verification';

// View.
$string['viewall'] = 'View all';
$string['viewapplication'] = 'View application';
$string['viewconvocatoria'] = 'View call';
$string['viewdetails'] = 'View details';
$string['vieweronly_desc'] = 'View only access';
$string['viewmyapplication'] = 'View my application';
$string['viewmyapplications'] = 'View my applications';
$string['viewmyreviews'] = 'View my reviews';
$string['viewpublicpage'] = 'View public page';
$string['viewpublicvacancies'] = 'View public vacancies';
$string['viewreports'] = 'View reports';
$string['viewvacancies'] = 'View vacancies';
$string['viewvacancy'] = 'View vacancy';
$string['viewvacancydetails'] = 'View vacancy details';
$string['wanttoapply'] = 'Want to apply?';
$string['welcometojobboard'] = 'Welcome to Job Board';
$string['withdraw'] = 'Withdraw';
$string['withdrawapplication'] = 'Withdraw application';
$string['womenonly'] = 'Women only';
$string['workflowactions'] = 'Workflow actions';
$string['workflowmanagement'] = 'Workflow management';

// CSV column names.
$string['csvcolumn_code'] = 'Code';
$string['csvcolumn_contracttype'] = 'Contract type';
$string['csvcolumn_courses'] = 'Courses';
$string['csvcolumn_faculty'] = 'Faculty';
$string['csvcolumn_location'] = 'Location';
$string['csvcolumn_modality'] = 'Modality';
$string['csvcolumn_profile'] = 'Profile';
$string['csvcolumn_program'] = 'Program';

// Edit actions.
$string['editdoctype'] = 'Edit document type';
$string['editexemption'] = 'Edit exemption';

// Email placeholder descriptions.
$string['ph_user_fullname'] = 'User full name';
$string['ph_user_firstname'] = 'User first name';
$string['ph_user_lastname'] = 'User last name';
$string['ph_user_email'] = 'User email';
$string['ph_site_name'] = 'Site name';
$string['ph_site_url'] = 'Site URL';
$string['ph_current_date'] = 'Current date';
$string['ph_company_name'] = 'Tutorial center name';
$string['ph_vacancy_code'] = 'Vacancy code';
$string['ph_vacancy_title'] = 'Vacancy title';
$string['ph_application_id'] = 'Application ID';
$string['ph_application_url'] = 'Application URL';
$string['ph_submit_date'] = 'Submit date';
$string['ph_reviewer_name'] = 'Reviewer name';
$string['ph_documents_count'] = 'Documents count';
$string['ph_rejected_docs'] = 'Rejected documents list';
$string['ph_observations'] = 'Reviewer observations';
$string['ph_resubmit_deadline'] = 'Resubmit deadline';
$string['ph_review_summary'] = 'Review summary';
$string['ph_approved_count'] = 'Approved documents';
$string['ph_rejected_count'] = 'Rejected documents';
$string['ph_action_required'] = 'Required actions';
$string['ph_interview_date'] = 'Interview date';
$string['ph_interview_time'] = 'Interview time';
$string['ph_interview_location'] = 'Interview location';
$string['ph_interview_type'] = 'Interview type';
$string['ph_interview_duration'] = 'Interview duration';
$string['ph_interview_notes'] = 'Additional notes';
$string['ph_interviewer_name'] = 'Interviewer name';
$string['ph_hours_until'] = 'Hours until interview';
$string['ph_interview_feedback'] = 'Interview feedback';
$string['ph_next_steps'] = 'Next steps';
$string['ph_selection_notes'] = 'Selection notes';
$string['ph_contact_info'] = 'Contact information';
$string['ph_rejection_reason'] = 'Rejection reason';
$string['ph_feedback'] = 'Feedback';
$string['ph_waitlist_position'] = 'Waitlist position';
$string['ph_notification_note'] = 'Information note';
$string['ph_days_remaining'] = 'Days remaining';
$string['ph_close_date'] = 'Close date';
$string['ph_vacancy_url'] = 'Vacancy URL';
$string['ph_vacancy_description'] = 'Vacancy description';
$string['ph_open_date'] = 'Open date';
$string['ph_faculty_name'] = 'Faculty name';
$string['ph_applicant_name'] = 'Applicant name';
$string['ph_deadline'] = 'Review deadline';

// JavaScript strings.
$string['js_select'] = 'Select...';
$string['js_selectconvocatoria'] = 'Select call...';
$string['js_selectmodality'] = 'Select modality...';
$string['js_loading'] = 'Loading...';
$string['js_internalerror'] = 'An internal error occurred';

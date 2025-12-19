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
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// =============================================================================
// PLUGIN IDENTIFICATION
// =============================================================================

$string['pluginname'] = 'Job Board';
$string['pluginname_desc'] = 'Job board system for teacher recruitment and vacancy management';
$string['jobboard'] = 'Job Board';
$string['jobboard:view'] = 'View job board';
$string['jobboard:manage'] = 'Manage job board';

// =============================================================================
// CAPABILITIES
// =============================================================================

$string['jobboard:view'] = 'View job board';
$string['jobboard:viewinternal'] = 'View internal vacancies';
$string['jobboard:viewpublicvacancies'] = 'View public vacancies';
$string['jobboard:viewinternalvacancies'] = 'View internal vacancies';
$string['jobboard:manage'] = 'Manage vacancies';
$string['jobboard:createvacancy'] = 'Create vacancies';
$string['jobboard:editvacancy'] = 'Edit vacancies';
$string['jobboard:deletevacancy'] = 'Delete vacancies';
$string['jobboard:publishvacancy'] = 'Publish vacancies';
$string['jobboard:viewallvacancies'] = 'View all vacancies';
$string['jobboard:manageconvocatorias'] = 'Manage convocatorias';
$string['jobboard:apply'] = 'Apply to vacancies';
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
$string['jobboard:configure'] = 'Configure plugin';
$string['jobboard:managedoctypes'] = 'Manage document types';
$string['jobboard:manageemailtemplates'] = 'Manage email templates';
$string['jobboard:manageexemptions'] = 'Manage exemptions';
$string['jobboard:unlimitedapplications'] = 'Unlimited applications';

// =============================================================================
// NAVIGATION & MENU
// =============================================================================

$string['dashboard'] = 'Dashboard';
$string['convocatorias'] = 'Convocatorias';
$string['vacancies'] = 'Vacancies';
$string['applications'] = 'Applications';
$string['myapplications'] = 'My applications';
$string['review'] = 'Review';
$string['myreviews'] = 'My reviews';
$string['reports'] = 'Reports';
$string['settings'] = 'Settings';
$string['administration'] = 'Administration';
$string['manage'] = 'Manage';
$string['public'] = 'Public';
$string['browse'] = 'Browse';
$string['home'] = 'Home';
$string['back'] = 'Back';
$string['backtodashboard'] = 'Back to dashboard';
$string['backtolist'] = 'Back to list';

// =============================================================================
// COMMON ACTIONS
// =============================================================================

$string['view'] = 'View';
$string['edit'] = 'Edit';
$string['delete'] = 'Delete';
$string['save'] = 'Save';
$string['cancel'] = 'Cancel';
$string['create'] = 'Create';
$string['update'] = 'Update';
$string['submit'] = 'Submit';
$string['confirm'] = 'Confirm';
$string['close'] = 'Close';
$string['open'] = 'Open';
$string['access'] = 'Access';
$string['search'] = 'Search';
$string['filter'] = 'Filter';
$string['reset'] = 'Reset';
$string['clear'] = 'Clear';
$string['apply'] = 'Apply';
$string['download'] = 'Download';
$string['upload'] = 'Upload';
$string['export'] = 'Export';
$string['import'] = 'Import';
$string['print'] = 'Print';
$string['preview'] = 'Preview';
$string['refresh'] = 'Refresh';
$string['duplicate'] = 'Duplicate';
$string['archive'] = 'Archive';
$string['restore'] = 'Restore';
$string['approve'] = 'Approve';
$string['reject'] = 'Reject';
$string['select'] = 'Select';
$string['selectall'] = 'Select all';
$string['deselectall'] = 'Deselect all';
$string['actions'] = 'Actions';
$string['options'] = 'Options';
$string['more'] = 'More';
$string['less'] = 'Less';
$string['showmore'] = 'Show more';
$string['showless'] = 'Show less';
$string['viewall'] = 'View all';
$string['viewdetails'] = 'View details';
$string['viewprofile'] = 'View profile';
$string['continue'] = 'Continue';
$string['finish'] = 'Finish';
$string['next'] = 'Next';
$string['previous'] = 'Previous';
$string['assign'] = 'Assign';
$string['unassign'] = 'Unassign';
$string['enable'] = 'Enable';
$string['disable'] = 'Disable';
$string['activate'] = 'Activate';
$string['deactivate'] = 'Deactivate';

// =============================================================================
// COMMON LABELS
// =============================================================================

$string['name'] = 'Name';
$string['title'] = 'Title';
$string['description'] = 'Description';
$string['code'] = 'Code';
$string['status'] = 'Status';
$string['type'] = 'Type';
$string['category'] = 'Category';
$string['date'] = 'Date';
$string['time'] = 'Time';
$string['datetime'] = 'Date and time';
$string['startdate'] = 'Start date';
$string['enddate'] = 'End date';
$string['closedate'] = 'Close date';
$string['opendate'] = 'Open date';
$string['deadline'] = 'Deadline';
$string['created'] = 'Created';
$string['modified'] = 'Modified';
$string['createdby'] = 'Created by';
$string['modifiedby'] = 'Modified by';
$string['timecreated'] = 'Time created';
$string['timemodified'] = 'Time modified';
$string['location'] = 'Location';
$string['department'] = 'Department';
$string['company'] = 'Site / Tutorial Center';
$string['faculty'] = 'Faculty';
$string['program'] = 'Program';
$string['positions'] = 'Positions';
$string['salary'] = 'Salary';
$string['contracttype'] = 'Contract type';
$string['modality'] = 'Modality';
$string['schedule'] = 'Schedule';
$string['requirements'] = 'Requirements';
$string['benefits'] = 'Benefits';
$string['notes'] = 'Notes';
$string['comments'] = 'Comments';
$string['observations'] = 'Observations';
$string['reason'] = 'Reason';
$string['priority'] = 'Priority';
$string['order'] = 'Order';
$string['total'] = 'Total';
$string['count'] = 'Count';
$string['quantity'] = 'Quantity';
$string['percentage'] = 'Percentage';
$string['average'] = 'Average';
$string['yes'] = 'Yes';
$string['no'] = 'No';
$string['all'] = 'All';
$string['none'] = 'None';
$string['any'] = 'Any';
$string['other'] = 'Other';
$string['unknown'] = 'Unknown';
$string['notavailable'] = 'Not available';
$string['notapplicable'] = 'Not applicable';
$string['days'] = 'days';
$string['day'] = 'day';
$string['hours'] = 'hours';
$string['hour'] = 'hour';
$string['minutes'] = 'minutes';
$string['minute'] = 'minute';
$string['ago'] = 'ago';
$string['remaining'] = 'remaining';
$string['daysremaining'] = 'Days remaining';
$string['page'] = 'Page';
$string['of'] = 'of';
$string['to'] = 'to';
$string['from'] = 'from';
$string['for'] = 'for';
$string['by'] = 'by';
$string['at'] = 'at';
$string['in'] = 'in';
$string['on'] = 'on';

// =============================================================================
// STATUS STRINGS
// =============================================================================

// General status
$string['active'] = 'Active';
$string['inactive'] = 'Inactive';
$string['enabled'] = 'Enabled';
$string['disabled'] = 'Disabled';
$string['pending'] = 'Pending';
$string['approved'] = 'Approved';
$string['rejected'] = 'Rejected';
$string['completed'] = 'Completed';
$string['cancelled'] = 'Cancelled';
$string['expired'] = 'Expired';

// Vacancy status
$string['status:draft'] = 'Draft';
$string['status:published'] = 'Published';
$string['status:closed'] = 'Closed';
$string['status:archived'] = 'Archived';

// Convocatoria status
$string['convocatoria_status_draft'] = 'Draft';
$string['convocatoria_status_open'] = 'Open';
$string['convocatoria_status_closed'] = 'Closed';
$string['convocatoria_status_archived'] = 'Archived';

// Application status
$string['appstatus:submitted'] = 'Submitted';
$string['appstatus:under_review'] = 'Under review';
$string['appstatus:docs_validated'] = 'Documents validated';
$string['appstatus:docs_rejected'] = 'Documents rejected';
$string['appstatus:interview'] = 'Interview';
$string['appstatus:selected'] = 'Selected';
$string['appstatus:rejected'] = 'Rejected';
$string['appstatus:waitlist'] = 'Waitlist';
$string['appstatus:withdrawn'] = 'Withdrawn';

// Document status
$string['docstatus:pending'] = 'Pending';
$string['docstatus:approved'] = 'Approved';
$string['docstatus:rejected'] = 'Rejected';
$string['docstatus:resubmitted'] = 'Resubmitted';

// Dynamic status strings (used with get_string('status_' . $status))
// Application statuses
$string['status_submitted'] = 'Submitted';
$string['status_under_review'] = 'Under review';
$string['status_docs_validated'] = 'Documents validated';
$string['status_docs_rejected'] = 'Documents rejected';
$string['status_interview'] = 'Interview';
$string['status_selected'] = 'Selected';
$string['status_rejected'] = 'Rejected';
$string['status_withdrawn'] = 'Withdrawn';
$string['status_waitlist'] = 'Waitlist';
// Vacancy statuses
$string['status_draft'] = 'Draft';
$string['status_published'] = 'Published';
$string['status_closed'] = 'Closed';
$string['status_cancelled'] = 'Cancelled';
$string['status_archived'] = 'Archived';
$string['status_assigned'] = 'Assigned';
// Document statuses
$string['status_pending'] = 'Pending';
$string['status_validated'] = 'Validated';
$string['status_approved'] = 'Approved';
$string['status_reupload'] = 'Reupload Required';

// Vacancy status labels (used with get_string('vacancystatus:' . $status))
$string['vacancystatus:draft'] = 'Draft';
$string['vacancystatus:published'] = 'Published';
$string['vacancystatus:closed'] = 'Closed';
$string['vacancystatus:archived'] = 'Archived';
$string['vacancystatus:assigned'] = 'Assigned';

// Convocatoria status labels (used with get_string('convocatoriastatus:' . $status))
$string['convocatoriastatus:draft'] = 'Draft';
$string['convocatoriastatus:open'] = 'Open';
$string['convocatoriastatus:closed'] = 'Closed';
$string['convocatoriastatus:archived'] = 'Archived';

// =============================================================================
// CONVOCATORIAS
// =============================================================================

$string['convocatoria'] = 'Convocatoria';
$string['newconvocatoria'] = 'New convocatoria';
$string['editconvocatoria'] = 'Edit convocatoria';
$string['deleteconvocatoria'] = 'Delete convocatoria';
$string['viewconvocatoria'] = 'View convocatoria';
$string['convocatorialist'] = 'Convocatoria list';
$string['convocatoriadetails'] = 'Convocatoria details';
$string['convocatoriacode'] = 'Convocatoria code';
$string['convocatorianame'] = 'Convocatoria name';
$string['convocatoriadescription'] = 'Description';
$string['convocatoriaterms'] = 'Terms and conditions';
$string['convocatoriapdf'] = 'Agreement/Resolution PDF';
$string['convocatoriapdf_help'] = 'Upload the official agreement or resolution document (e.g., Acuerdo 050) in PDF format';
$string['convocatoriaterms_pdf'] = 'Terms of Call PDF';
$string['convocatoriaterms_pdf_help'] = 'Upload the terms and conditions document for this call in PDF format';
$string['convocatoriastartdate'] = 'Start date';
$string['convocatoriaenddate'] = 'End date';
$string['convocatoriastatus'] = 'Status';
$string['publishconvocatoria'] = 'Publish convocatoria';
$string['closeconvocatoria'] = 'Close convocatoria';
$string['archiveconvocatoria'] = 'Archive convocatoria';
$string['reopenconvocatoria'] = 'Reopen convocatoria';
$string['noconvocatorias'] = 'No convocatorias found';
$string['selectconvocatoria'] = 'Select convocatoria';
$string['allconvocatorias'] = 'All convocatorias';
$string['activeconvocatorias'] = 'Active convocatorias';
$string['convocatoriavacancies'] = 'Vacancies in this convocatoria';
$string['convocatoriaapplications'] = 'Applications in this convocatoria';
$string['convocatoriarequired'] = 'Please select a convocatoria';
$string['convocatoriacreated'] = 'Convocatoria created successfully';
$string['convocatoriaupdated'] = 'Convocatoria updated successfully';
$string['convocatoriadeleted'] = 'Convocatoria deleted successfully';
$string['convocatoriapublished'] = 'Convocatoria published successfully';
$string['convocatoriaclosed'] = 'Convocatoria closed successfully';
$string['confirmdeleteconvocatoria'] = 'Are you sure you want to delete this convocatoria? This action cannot be undone.';

// =============================================================================
// VACANCIES
// =============================================================================

$string['vacancy'] = 'Vacancy';
$string['newvacancy'] = 'New vacancy';
$string['editvacancy'] = 'Edit vacancy';
$string['deletevacancy'] = 'Delete vacancy';
$string['viewvacancy'] = 'View vacancy';
$string['vacancylist'] = 'Vacancy list';
$string['vacancydetails'] = 'Vacancy details';
$string['vacancycode'] = 'Vacancy code';
$string['vacancytitle'] = 'Vacancy title';
$string['vacancydescription'] = 'Description';
$string['vacancyrequirements'] = 'Requirements';
$string['vacancybenefits'] = 'Benefits';
$string['vacancylocation'] = 'Location';
$string['vacancydepartment'] = 'Department';
$string['vacancypositions'] = 'Number of positions';
$string['vacancycontracttype'] = 'Contract type';
$string['vacancymodality'] = 'Modality';
$string['vacancyschedule'] = 'Schedule';
$string['vacancysalary'] = 'Salary';
$string['vacancystartdate'] = 'Start date';
$string['vacancyenddate'] = 'End date';
$string['vacancyclosedate'] = 'Application deadline';
$string['vacancystatus'] = 'Status';
$string['publishvacancy'] = 'Publish vacancy';
$string['unpublishvacancy'] = 'Unpublish vacancy';
$string['closevacancy'] = 'Close vacancy';
$string['reopenvacancy'] = 'Reopen vacancy';
$string['duplicatevacancy'] = 'Duplicate vacancy';
$string['novacancies'] = 'No vacancies found';
$string['selectvacancy'] = 'Select vacancy';
$string['allvacancies'] = 'All vacancies';
$string['activevacancies'] = 'Active vacancies';
$string['closedvacancies'] = 'Closed vacancies';
$string['internalvacancies'] = 'Internal vacancies';
$string['publicvacancies'] = 'Public vacancies';
$string['vacancycreated'] = 'Vacancy created successfully';
$string['vacancyupdated'] = 'Vacancy updated successfully';
$string['vacancydeleted'] = 'Vacancy deleted successfully';
$string['vacancypublished'] = 'Vacancy published successfully';
$string['vacancyclosed'] = 'Vacancy closed successfully';
$string['vacancyclosed_desc'] = 'This vacancy is no longer accepting applications';
$string['confirmdeletevacancy'] = 'Are you sure you want to delete this vacancy? This action cannot be undone.';
$string['vacancyhasapplications'] = 'This vacancy has applications and cannot be deleted';
$string['vacancyactions'] = 'Actions';
$string['vacancyoverview'] = 'Overview';
$string['metadata'] = 'Metadata';
$string['closingsoon'] = 'Closing soon';
$string['urgent'] = 'Urgent';
$string['newapplicants'] = 'New applicants';
$string['totalapplicants'] = 'Total applicants';
$string['pagination'] = 'Page navigation';
$string['applicationcount'] = 'Applications';

// Contract types
$string['contract:catedra'] = 'Adjunct Professor';
$string['contract:planta'] = 'Full-time Professor';
$string['contract:temporal'] = 'Temporary';
$string['contract:ocasional'] = 'Occasional';
$string['contract:ocasional_tc'] = 'Full-time Occasional';
$string['contract:ocasional_mt'] = 'Part-time Occasional';
$string['contract:hora_catedra'] = 'Hourly';

// Modalities
$string['modality:presencial'] = 'On-site';
$string['modality:virtual'] = 'Virtual';
$string['modality:hibrida'] = 'Hybrid';
$string['modality:distancia'] = 'Distance';

// =============================================================================
// APPLICATIONS
// =============================================================================

$string['application'] = 'Application';
$string['newapplication'] = 'New application';
$string['editapplication'] = 'Edit application';
$string['deleteapplication'] = 'Delete application';
$string['viewapplication'] = 'View application';
$string['applicationlist'] = 'Application list';
$string['applicationdetails'] = 'Application details';
$string['applicationcode'] = 'Application code';
$string['applicationdate'] = 'Application date';
$string['applicationstatus'] = 'Application status';
$string['applicant'] = 'Applicant';
$string['applicantinfo'] = 'Applicant information';
$string['applynow'] = 'Apply now';
$string['applyforvacancy'] = 'Apply for vacancy';
$string['submitapplication'] = 'Submit application';
$string['withdrawapplication'] = 'Withdraw application';
$string['applicationsubmitted'] = 'Application submitted successfully';
$string['applicationupdated'] = 'Application updated successfully';
$string['applicationwithdrawn'] = 'Application withdrawn successfully';
$string['applicationdeleted'] = 'Application deleted successfully';
$string['noapplications'] = 'No applications found';
$string['noapplications_desc'] = 'You haven\'t applied to any vacancies yet. Browse available positions to get started.';
$string['yourapplications'] = 'Your applications';
$string['allapplications'] = 'All applications';
$string['pendingapplications'] = 'Pending applications';
$string['reviewedapplications'] = 'Reviewed applications';
$string['approvedapplications'] = 'Approved applications';
$string['rejectedapplications'] = 'Rejected applications';
$string['applicationhistory'] = 'Application history';
$string['applicationtimeline'] = 'Application timeline';
$string['applicationprogress'] = 'Application progress';
$string['confirmwithdrawapplication'] = 'Are you sure you want to withdraw this application?';
$string['confirmdeleteapplication'] = 'Are you sure you want to delete this application?';
$string['alreadyapplied'] = 'You have already applied to this vacancy';
$string['applicationlimit'] = 'You can only apply to one vacancy per convocatoria';
$string['cannotapply'] = 'You cannot apply to this vacancy';
$string['applicationclosed'] = 'Applications are closed for this vacancy';
$string['reviewapplications'] = 'Review applications';
$string['manageapplications'] = 'Manage applications';
$string['changeapplicationstatus'] = 'Change application status';
$string['applicationstatuschanged'] = 'Application status changed successfully';
$string['letterofintent'] = 'Letter of intent';
$string['letterofintent_help'] = 'Write a brief letter explaining why you are interested in this position and what makes you a good candidate';
$string['coveringletter'] = 'Covering letter';

// =============================================================================
// DOCUMENTS
// =============================================================================

$string['document'] = 'Document';
$string['documents'] = 'Documents';
$string['uploaddocument'] = 'Upload document';
$string['reupload'] = 'Re-upload';
$string['reuploaddocument'] = 'Re-upload document';
$string['viewdocument'] = 'View document';
$string['downloaddocument'] = 'Download document';
$string['deletedocument'] = 'Delete document';
$string['attachments'] = 'Attachments';
$string['viewpdf'] = 'View PDF';
$string['officialdocument'] = 'Official document';
$string['documentlist'] = 'Document list';
$string['documentdetails'] = 'Document details';
$string['documenttype'] = 'Document type';
$string['documentname'] = 'Document name';
$string['documentfile'] = 'File';
$string['documentstatus'] = 'Document status';
$string['documentuploaded'] = 'Document uploaded successfully';
$string['documentdeleted'] = 'Document deleted successfully';
$string['documentupdated'] = 'Document updated successfully';
$string['nodocuments'] = 'No documents found';
$string['requireddocuments'] = 'Required documents';
$string['optionaldocuments'] = 'Optional documents';
$string['uploadeddocuments'] = 'Uploaded documents';
$string['pendingdocuments'] = 'Pending documents';
$string['approveddocuments'] = 'Approved documents';
$string['rejecteddocuments'] = 'Rejected documents';
$string['documentprogress'] = 'Document progress';
$string['documentsvalidated'] = 'Documents validated';
$string['documentsrejected'] = 'Documents rejected';
$string['documentspending'] = 'Documents pending';
$string['alldocumentsapproved'] = 'All documents approved';
$string['somedocumentsrejected'] = 'Some documents were rejected';
$string['documentrequired'] = 'This document is required';
$string['documentoptional'] = 'This document is optional';
$string['maxfilesize'] = 'Maximum file size';
$string['allowedfiletypes'] = 'Allowed file types';
$string['invalidfiletype'] = 'Invalid file type';
$string['filetoobig'] = 'File is too large';

// Document types
$string['doctype_cedula'] = 'ID Card (Cédula)';
$string['doctype_rut'] = 'Tax ID (RUT)';
$string['doctype_titulo_pregrado'] = 'Undergraduate degree';
$string['doctype_titulo_posgrado'] = 'Graduate degree';
$string['doctype_acta_grado'] = 'Graduation certificate';
$string['doctype_certificado_laboral'] = 'Work certificate';
$string['doctype_hoja_vida'] = 'Resume/CV';
$string['doctype_foto'] = 'Photo';
$string['doctype_libreta_militar'] = 'Military card';
$string['doctype_antecedentes_penales'] = 'Criminal background check';
$string['doctype_antecedentes_disciplinarios'] = 'Disciplinary background';
$string['doctype_antecedentes_fiscales'] = 'Fiscal background';
$string['doctype_certificado_medico'] = 'Medical certificate';
$string['doctype_eps'] = 'Health insurance certificate';
$string['doctype_pension'] = 'Pension certificate';
$string['doctype_arl'] = 'Work risk insurance';
$string['doctype_cuenta_bancaria'] = 'Bank account certificate';
$string['doctype_contrato_firmado'] = 'Signed contract';
$string['doctype_resolucion_convalidacion'] = 'Degree validation resolution';
$string['doctype_carta_intencion'] = 'Letter of intent';

// Document categories
$string['doccat_identification'] = 'Identification';
$string['doccat_academic'] = 'Academic';
$string['doccat_employment'] = 'Employment';
$string['doccat_legal'] = 'Legal';
$string['doccat_financial'] = 'Financial';
$string['doccat_health'] = 'Health';
$string['doccat_other'] = 'Other';

// =============================================================================
// DOCUMENT VALIDATION
// =============================================================================

$string['validatedocument'] = 'Validate document';
$string['validatedocuments'] = 'Validate documents';
$string['bulkvalidate'] = 'Bulk validate';
$string['bulkvalidation'] = 'Bulk validation';
$string['documentvalidation'] = 'Document validation';
$string['validationchecklist'] = 'Validation checklist';
$string['validationresult'] = 'Validation result';
$string['validationdate'] = 'Validation date';
$string['validatedby'] = 'Validated by';
$string['approvedocument'] = 'Approve document';
$string['rejectdocument'] = 'Reject document';
$string['documentapproved'] = 'Document approved successfully';
$string['documentrejected'] = 'Document rejected';
$string['rejectionreason'] = 'Rejection reason';
$string['rejectionreasons'] = 'Rejection reasons';
$string['selectrejectionreason'] = 'Select rejection reason';
$string['additionalcomments'] = 'Additional comments';
$string['validationcomments'] = 'Validation comments';

// Rejection reasons
$string['rejection:illegible'] = 'Document is illegible';
$string['rejection:expired'] = 'Document has expired';
$string['rejection:incomplete'] = 'Document is incomplete';
$string['rejection:wrongtype'] = 'Wrong document type';
$string['rejection:mismatch'] = 'Information does not match';
$string['rejection:other'] = 'Other reason';

// Validation checklist items
$string['check_legible'] = 'Document is legible';
$string['check_complete'] = 'Document is complete';
$string['check_notexpired'] = 'Document is not expired';
$string['check_correcttype'] = 'Correct document type';
$string['check_matchesinfo'] = 'Information matches applicant data';
$string['check_originaldocument'] = 'Appears to be original document';
$string['check_propersignatures'] = 'Contains proper signatures';
$string['check_notampered'] = 'No signs of tampering';

// =============================================================================
// REVIEWERS
// =============================================================================

$string['reviewer'] = 'Reviewer';
$string['reviewers'] = 'Reviewers';
$string['assignreviewer'] = 'Assign reviewer';
$string['assignreviewers'] = 'Assign reviewers';
$string['unassignreviewer'] = 'Unassign reviewer';
$string['reviewerassigned'] = 'Reviewer assigned successfully';
$string['reviewerunassigned'] = 'Reviewer unassigned successfully';
$string['noreviewer'] = 'No reviewer assigned';
$string['selectreviewer'] = 'Select reviewer';
$string['availablereviewers'] = 'Available reviewers';
$string['assignedreviewers'] = 'Assigned reviewers';
$string['reviewerworkload'] = 'Reviewer workload';
$string['reviewerprogress'] = 'Reviewer progress';
$string['reviewerstatistics'] = 'Reviewer statistics';
$string['programreviewers'] = 'Program reviewers';
$string['facultyreviewers'] = 'Faculty reviewers';
$string['manageprogramreviewers'] = 'Manage program reviewers';
$string['assignedapplications'] = 'Assigned applications';
$string['completedreviews'] = 'Completed reviews';
$string['pendingreviews'] = 'Pending reviews';
$string['averagereviewtime'] = 'Average review time';
$string['autoassign'] = 'Auto-assign';
$string['autoassignreviewer'] = 'Auto-assign reviewer';
$string['autoassignbased'] = 'Auto-assign based on';
$string['workload'] = 'Workload';
$string['expertise'] = 'Expertise';

// =============================================================================
// COMMITTEES
// =============================================================================

$string['committee'] = 'Committee';
$string['committees'] = 'Committees';
$string['selectioncommittee'] = 'Selection committee';
$string['createcommittee'] = 'Create committee';
$string['editcommittee'] = 'Edit committee';
$string['deletecommittee'] = 'Delete committee';
$string['managecommittee'] = 'Manage committee';
$string['committeemembers'] = 'Committee members';
$string['addmember'] = 'Add member';
$string['removemember'] = 'Remove member';
$string['memberadded'] = 'Member added successfully';
$string['memberremoved'] = 'Member removed successfully';
$string['nocommittees'] = 'No committees found';
$string['allcommittees'] = 'All committees';
$string['selectcommittee'] = 'Select committee';
$string['facultycommittee'] = 'Faculty committee';
$string['committeerole'] = 'Role in committee';
$string['committeechair'] = 'Committee chair';
$string['committeemember'] = 'Committee member';
$string['committeesecretary'] = 'Committee secretary';

// =============================================================================
// INTERVIEWS
// =============================================================================

$string['interview'] = 'Interview';
$string['interviews'] = 'Interviews';
$string['scheduleinterview'] = 'Schedule interview';
$string['rescheduleinterview'] = 'Reschedule interview';
$string['cancelinterview'] = 'Cancel interview';
$string['interviewscheduled'] = 'Interview scheduled successfully';
$string['interviewrescheduled'] = 'Interview rescheduled successfully';
$string['interviewcancelled'] = 'Interview cancelled';
$string['interviewdate'] = 'Interview date';
$string['interviewtime'] = 'Interview time';
$string['interviewlocation'] = 'Interview location';
$string['interviewtype'] = 'Interview type';
$string['interviewers'] = 'Interviewers';
$string['interviewnotes'] = 'Interview notes';
$string['interviewresult'] = 'Interview result';
$string['upcominginterviews'] = 'Upcoming interviews';
$string['pastinterviews'] = 'Past interviews';
$string['nointerviews'] = 'No interviews scheduled';
$string['interviewonsite'] = 'On-site interview';
$string['interviewvirtual'] = 'Virtual interview';
$string['interviewphone'] = 'Phone interview';

// =============================================================================
// EVALUATIONS
// =============================================================================

$string['evaluation'] = 'Evaluation';
$string['evaluations'] = 'Evaluations';
$string['evaluate'] = 'Evaluate';
$string['evaluatecandidate'] = 'Evaluate candidate';
$string['submiteval'] = 'Submit evaluation';
$string['evaluationsubmitted'] = 'Evaluation submitted successfully';
$string['evaluationcriteria'] = 'Evaluation criteria';
$string['evaluationscore'] = 'Score';
$string['evaluationcomments'] = 'Comments';
$string['overallscore'] = 'Overall score';
$string['recommendation'] = 'Recommendation';
$string['stronglyrecommend'] = 'Strongly recommend';
$string['recommend'] = 'Recommend';
$string['neutral'] = 'Neutral';
$string['notrecommend'] = 'Do not recommend';
$string['stronglynotrecommend'] = 'Strongly do not recommend';

// =============================================================================
// EXEMPTIONS
// =============================================================================

$string['exemption'] = 'Exemption';
$string['exemptions'] = 'Exemptions';
$string['createexemption'] = 'Create exemption';
$string['editexemption'] = 'Edit exemption';
$string['deleteexemption'] = 'Delete exemption';
$string['manageexemptions'] = 'Manage exemptions';
$string['exemptiontype'] = 'Exemption type';
$string['exemptionapplied'] = 'Exemption applied';
$string['exemptionreason'] = 'Exemption reason';
$string['noexemptions'] = 'No exemptions found';
$string['exemptionsaved'] = 'Exemption saved successfully';
$string['exemptiondeleted'] = 'Exemption deleted successfully';
$string['importexemptions'] = 'Import exemptions';
$string['exportexemptions'] = 'Export exemptions';

// Exemption types
$string['exemptiontype_historico_iser'] = 'Historical ISER';
$string['exemptiontype_documentos_recientes'] = 'Recent documents';
$string['exemptiontype_traslado_interno'] = 'Internal transfer';
$string['exemptiontype_recontratacion'] = 'Rehire';
$string['exemptiontype_age'] = 'Age exemption';
$string['exemptiontype_gender'] = 'Gender exemption';
$string['exemptiontype_profession'] = 'Profession exemption';

$string['exemptiontype_historico_iser_desc'] = 'Employee with historical documents on file at ISER';
$string['exemptiontype_documentos_recientes_desc'] = 'Documents submitted within the last 6 months';
$string['exemptiontype_traslado_interno_desc'] = 'Internal transfer between campuses';
$string['exemptiontype_recontratacion_desc'] = 'Previous employee being rehired';

// =============================================================================
// EMAIL TEMPLATES
// =============================================================================

$string['emailtemplate'] = 'Email template';
$string['emailtemplates'] = 'Email templates';
$string['createemailtemplate'] = 'Create email template';
$string['editemailtemplate'] = 'Edit email template';
$string['deleteemailtemplate'] = 'Delete email template';
$string['manageemailtemplates'] = 'Manage email templates';
$string['templatename'] = 'Template name';
$string['templatesubject'] = 'Subject';
$string['templatebody'] = 'Body';
$string['templatevariables'] = 'Available variables';
$string['previewtemplate'] = 'Preview template';
$string['testtemplate'] = 'Test template';
$string['templatesaved'] = 'Template saved successfully';
$string['templatedeleted'] = 'Template deleted successfully';
$string['notemplates'] = 'No email templates found';
$string['duplicatetemplate'] = 'Duplicate template';
$string['resettodefault'] = 'Reset to default';

// Email template placeholders
$string['ph_firstname'] = 'First name';
$string['ph_lastname'] = 'Last name';
$string['ph_fullname'] = 'Full name';
$string['ph_email'] = 'Email address';
$string['ph_username'] = 'Username';
$string['ph_vacancytitle'] = 'Vacancy title';
$string['ph_vacancycode'] = 'Vacancy code';
$string['ph_convocatorianame'] = 'Convocatoria name';
$string['ph_applicationcode'] = 'Application code';
$string['ph_applicationstatus'] = 'Application status';
$string['ph_documentname'] = 'Document name';
$string['ph_rejectionreason'] = 'Rejection reason';
$string['ph_interviewdate'] = 'Interview date';
$string['ph_interviewtime'] = 'Interview time';
$string['ph_interviewlocation'] = 'Interview location';
$string['ph_siteurl'] = 'Site URL';
$string['ph_sitename'] = 'Site name';
$string['ph_supportemail'] = 'Support email';

// =============================================================================
// SIGNUP & PROFILE
// =============================================================================

$string['signup'] = 'Sign up';
$string['register'] = 'Register';
$string['createaccount'] = 'Create account';
$string['alreadyhaveaccount'] = 'Already have an account?';
$string['donthaveaccount'] = 'Don\'t have an account?';
$string['login'] = 'Log in';
$string['logout'] = 'Log out';
$string['profile'] = 'Profile';
$string['myprofile'] = 'My profile';
$string['editprofile'] = 'Edit profile';
$string['updateprofile'] = 'Update profile';
$string['profileupdated'] = 'Profile updated successfully';
$string['personalinformation'] = 'Personal information';
$string['contactinformation'] = 'Contact information';
$string['academicinfo'] = 'Academic information';
$string['workexperience'] = 'Work experience';
$string['additionalinfo'] = 'Additional information';

// Personal fields
$string['firstname'] = 'First name';
$string['lastname'] = 'Last name';
$string['middlename'] = 'Middle name';
$string['fullname'] = 'Full name';
$string['idnumber'] = 'ID number';
$string['idtype'] = 'ID type';
$string['dateofbirth'] = 'Date of birth';
$string['gender'] = 'Gender';
$string['gender_male'] = 'Male';
$string['gender_female'] = 'Female';
$string['gender_other'] = 'Other';
$string['gender_prefernotsay'] = 'Prefer not to say';
$string['nationality'] = 'Nationality';
$string['maritalstatus'] = 'Marital status';
$string['marital_single'] = 'Single';
$string['marital_married'] = 'Married';
$string['marital_divorced'] = 'Divorced';
$string['marital_widowed'] = 'Widowed';
$string['marital_other'] = 'Other';

// Contact fields
$string['email'] = 'Email';
$string['phone'] = 'Phone';
$string['mobilephone'] = 'Mobile phone';
$string['address'] = 'Address';
$string['city'] = 'City';
$string['state'] = 'State/Province';
$string['country'] = 'Country';
$string['postalcode'] = 'Postal code';

// Academic fields
$string['highestdegree'] = 'Highest degree';
$string['degreefield'] = 'Field of study';
$string['institution'] = 'Institution';
$string['graduationyear'] = 'Graduation year';
$string['degree_technical'] = 'Technical';
$string['degree_technology'] = 'Technology';
$string['degree_undergraduate'] = 'Undergraduate';
$string['degree_specialization'] = 'Specialization';
$string['degree_masters'] = 'Master\'s';
$string['degree_doctorate'] = 'Doctorate';

// Work experience
$string['yearsexperience'] = 'Years of experience';
$string['currentposition'] = 'Current position';
$string['currentemployer'] = 'Current employer';
$string['teachingexperience'] = 'Teaching experience';

// Signup specific
$string['signupsuccess'] = 'Registration successful';
$string['signupsuccess_message'] = 'Your account has been created. Please check your email ({$a->email}) for further instructions.';
$string['acceptterms'] = 'I accept the terms and conditions';
$string['acceptprivacy'] = 'I accept the privacy policy';
$string['termsandconditions'] = 'Terms and conditions';
$string['privacypolicy'] = 'Privacy policy';
$string['dataprotection'] = 'Data protection';
$string['consent'] = 'Consent';
$string['consenttext'] = 'I consent to the processing of my personal data in accordance with the privacy policy';

// =============================================================================
// REPORTS
// =============================================================================

$string['report'] = 'Report';
$string['generatereport'] = 'Generate report';
$string['downloadreport'] = 'Download report';
$string['exportreport'] = 'Export report';
$string['reporttype'] = 'Report type';
$string['reportformat'] = 'Report format';
$string['reportperiod'] = 'Report period';
$string['reportfilters'] = 'Report filters';
$string['noreportdata'] = 'No data available for this report';

// Report types
$string['report_overview'] = 'Overview';
$string['report_applications'] = 'Applications report';
$string['report_documents'] = 'Documents report';
$string['report_reviewers'] = 'Reviewers report';
$string['report_timeline'] = 'Timeline report';
$string['report_statistics'] = 'Statistics';
$string['report_export'] = 'Data export';

// Statistics
$string['statistics'] = 'Statistics';
$string['totalvacancies'] = 'Total vacancies';
$string['totalapplications'] = 'Total applications';
$string['totaldocuments'] = 'Total documents';
$string['totalreviewers'] = 'Total reviewers';
$string['applicationspervacancy'] = 'Applications per vacancy';
$string['documentsperapp'] = 'Documents per application';
$string['approvalrate'] = 'Approval rate';
$string['rejectionrate'] = 'Rejection rate';
$string['averageprocessingtime'] = 'Average processing time';

// =============================================================================
// DATA EXPORT
// =============================================================================

$string['dataexport'] = 'Data export';
$string['exportdata'] = 'Export data';
$string['exportformat'] = 'Export format';
$string['exportcsv'] = 'Export as CSV';
$string['exportexcel'] = 'Export as Excel';
$string['exportpdf'] = 'Export as PDF';
$string['exportjson'] = 'Export as JSON';
$string['exportzip'] = 'Export documents as ZIP';
$string['selectfieldstoexport'] = 'Select fields to export';
$string['exportstarted'] = 'Export started';
$string['exportcompleted'] = 'Export completed';
$string['exportfailed'] = 'Export failed';
$string['downloadexport'] = 'Download export';

// =============================================================================
// DASHBOARD
// =============================================================================

$string['welcomeback'] = 'Welcome back, {$a}';
$string['dashboardoverview'] = 'Dashboard overview';
$string['quickactions'] = 'Quick actions';
$string['recentactivity'] = 'Recent activity';
$string['alerts'] = 'Alerts';
$string['notifications'] = 'Notifications';
$string['pendingactions'] = 'Pending actions';
$string['todaystasks'] = 'Today\'s tasks';
$string['upcomingdeadlines'] = 'Upcoming deadlines';
$string['recentapplications'] = 'Recent applications';
$string['recentdocuments'] = 'Recent documents';
$string['recentreviews'] = 'Recent reviews';
$string['performancemetrics'] = 'Performance metrics';
$string['noalerts'] = 'No alerts';
$string['nonotifications'] = 'No notifications';
$string['norecentactivity'] = 'No recent activity';

// Dashboard widgets
$string['widget_vacancies'] = 'Vacancies';
$string['widget_applications'] = 'Applications';
$string['widget_documents'] = 'Documents';
$string['widget_reviews'] = 'Reviews';
$string['widget_pending'] = 'Pending';
$string['widget_approved'] = 'Approved';
$string['widget_rejected'] = 'Rejected';

// =============================================================================
// NOTIFICATIONS & ALERTS
// =============================================================================

$string['notification'] = 'Notification';
$string['markasread'] = 'Mark as read';
$string['markallread'] = 'Mark all as read';
$string['clearnotifications'] = 'Clear notifications';
$string['newnotification'] = 'New notification';
$string['unreadnotifications'] = 'Unread notifications';

// Notification messages
$string['notify_application_submitted'] = 'New application submitted for {$a->vacancy}';
$string['notify_application_status_changed'] = 'Your application status has been updated to {$a->status}';
$string['notify_document_approved'] = 'Your document {$a->document} has been approved';
$string['notify_document_rejected'] = 'Your document {$a->document} has been rejected';
$string['notify_review_assigned'] = 'You have been assigned to review application {$a->application}';
$string['notify_interview_scheduled'] = 'Interview scheduled for {$a->date} at {$a->time}';
$string['notify_vacancy_closing'] = 'Vacancy {$a->vacancy} is closing soon';
$string['notify_deadline_approaching'] = 'Deadline approaching for {$a->item}';

// =============================================================================
// AUDIT & LOGGING
// =============================================================================

$string['audit'] = 'Audit';
$string['auditlog'] = 'Audit log';
$string['audittrail'] = 'Audit trail';
$string['action'] = 'Action';
$string['actor'] = 'User';
$string['target'] = 'Target';
$string['timestamp'] = 'Timestamp';
$string['ipaddress'] = 'IP address';
$string['details'] = 'Details';
$string['viewauditlog'] = 'View audit log';

// Audit actions
$string['audit_create'] = 'Create';
$string['audit_update'] = 'Update';
$string['audit_delete'] = 'Delete';
$string['audit_view'] = 'View';
$string['audit_login'] = 'Login';
$string['audit_logout'] = 'Logout';
$string['audit_export'] = 'Export';
$string['audit_statuschange'] = 'Status change';

// =============================================================================
// SETTINGS & CONFIGURATION
// =============================================================================

$string['generalsettings'] = 'General settings';
$string['pluginsettings'] = 'Plugin settings';
$string['configureplugin'] = 'Configure plugin';
$string['defaultsettings'] = 'Default settings';
$string['advancedsettings'] = 'Advanced settings';

// Setting labels
$string['setting_enablepublic'] = 'Enable public view';
$string['setting_enablepublic_desc'] = 'Allow unauthenticated users to view public vacancies';
$string['setting_requirelogin'] = 'Require login to apply';
$string['setting_requirelogin_desc'] = 'Users must be logged in to submit applications';
$string['setting_maxfilesize'] = 'Maximum file size';
$string['setting_maxfilesize_desc'] = 'Maximum file size for document uploads (in MB)';
$string['setting_allowedfiletypes'] = 'Allowed file types';
$string['setting_allowedfiletypes_desc'] = 'Comma-separated list of allowed file extensions';
$string['setting_defaultcontracttype'] = 'Default contract type';
$string['setting_defaultmodality'] = 'Default modality';
$string['setting_notifyreviewer'] = 'Notify reviewers';
$string['setting_notifyreviewer_desc'] = 'Send email notification when reviewer is assigned';
$string['setting_notifyapplicant'] = 'Notify applicants';
$string['setting_notifyapplicant_desc'] = 'Send email notification on status changes';
$string['setting_autoassignreviewers'] = 'Auto-assign reviewers';
$string['setting_autoassignreviewers_desc'] = 'Automatically assign reviewers based on workload';
$string['setting_applicationlimit'] = 'Application limit per convocatoria';
$string['setting_applicationlimit_desc'] = 'Maximum number of applications per user per convocatoria (0 = unlimited)';

// =============================================================================
// DOCUMENT TYPES MANAGEMENT
// =============================================================================

$string['doctypes'] = 'Document types';
$string['managedoctypes'] = 'Manage document types';
$string['createdoctype'] = 'Create document type';
$string['editdoctype'] = 'Edit document type';
$string['deletedoctype'] = 'Delete document type';
$string['doctypename'] = 'Document type name';
$string['doctypecode'] = 'Code';
$string['doctypecategory'] = 'Category';
$string['doctyperequired'] = 'Required';
$string['doctypeenabled'] = 'Enabled';
$string['doctypesortorder'] = 'Sort order';
$string['doctypechecklist'] = 'Validation checklist';
$string['doctypeexpirationdays'] = 'Expiration days';
$string['doctypegender'] = 'Gender specific';
$string['doctypeiserexempted'] = 'ISER exempted';
$string['doctypesaved'] = 'Document type saved successfully';
$string['doctypedeleted'] = 'Document type deleted successfully';
$string['nodoctypes'] = 'No document types found';
$string['confirmdeletedoctype'] = 'Are you sure you want to delete this document type?';

// =============================================================================
// ERROR MESSAGES
// =============================================================================

$string['error'] = 'Error';
$string['error_general'] = 'An error occurred. Please try again.';
$string['error_notfound'] = 'The requested item was not found';
$string['error_permission'] = 'You do not have permission to perform this action';
$string['error_invalid'] = 'Invalid request';
$string['error_required'] = 'This field is required';
$string['error_invalidformat'] = 'Invalid format';
$string['error_invaliddate'] = 'Invalid date';
$string['error_invalidemail'] = 'Invalid email address';
$string['error_invalidphone'] = 'Invalid phone number';
$string['error_invalidfile'] = 'Invalid file';
$string['error_fileupload'] = 'Error uploading file';
$string['error_filetoobig'] = 'File exceeds maximum allowed size';
$string['error_invalidfiletype'] = 'File type not allowed';
$string['error_missingfield'] = 'Missing required field: {$a}';
$string['error_duplicate'] = 'A record with this information already exists';
$string['error_cannotdelete'] = 'This item cannot be deleted';
$string['error_cannotupdate'] = 'This item cannot be updated';
$string['error_databaseerror'] = 'Database error occurred';
$string['error_sessionexpired'] = 'Your session has expired. Please log in again.';
$string['error_accessdenied'] = 'Access denied';
$string['error_notloggedin'] = 'You must be logged in to access this page';
$string['error_convocatoriaclosed'] = 'This convocatoria is closed';
$string['error_vacancyclosed'] = 'This vacancy is closed';
$string['error_applicationlimit'] = 'You have reached the application limit for this convocatoria';
$string['error_alreadyapplied'] = 'You have already applied to this vacancy';
$string['error_documentrequired'] = 'Required document is missing: {$a}';
$string['error_invaliddocument'] = 'Invalid document submitted';
$string['error_reviewernotfound'] = 'Reviewer not found';
$string['error_noreviewers'] = 'No reviewers available';
$string['error_exportfailed'] = 'Export failed. Please try again.';
$string['error_importfailed'] = 'Import failed. Please check the file format.';

// =============================================================================
// SUCCESS MESSAGES
// =============================================================================

$string['success'] = 'Success';
$string['success_saved'] = 'Changes saved successfully';
$string['success_created'] = 'Item created successfully';
$string['success_updated'] = 'Item updated successfully';
$string['success_deleted'] = 'Item deleted successfully';
$string['success_uploaded'] = 'File uploaded successfully';
$string['success_submitted'] = 'Form submitted successfully';
$string['success_sent'] = 'Message sent successfully';
$string['success_exported'] = 'Data exported successfully';
$string['success_imported'] = 'Data imported successfully';

// =============================================================================
// CONFIRMATION MESSAGES
// =============================================================================

$string['confirm'] = 'Confirm';
$string['confirm_delete'] = 'Are you sure you want to delete this item?';
$string['confirm_cancel'] = 'Are you sure you want to cancel?';
$string['confirm_submit'] = 'Are you sure you want to submit?';
$string['confirm_withdraw'] = 'Are you sure you want to withdraw?';
$string['confirm_approve'] = 'Are you sure you want to approve?';
$string['confirm_reject'] = 'Are you sure you want to reject?';
$string['confirm_publish'] = 'Are you sure you want to publish?';
$string['confirm_close'] = 'Are you sure you want to close?';
$string['confirm_archive'] = 'Are you sure you want to archive?';
$string['confirm_action'] = 'Are you sure you want to perform this action?';
$string['actioncannnotbeundone'] = 'This action cannot be undone.';

// Email confirmation page strings.
$string['confirm_success_title'] = 'Email Confirmed';
$string['confirm_success_message'] = 'Your email address has been verified successfully. You can now log in to your account.';
$string['confirm_failed_title'] = 'Confirmation Failed';
$string['confirm_failed_message'] = 'We could not verify your email address. The confirmation link may have expired or is invalid. Please try registering again or contact support.';
$string['confirm_pending_vacancy'] = 'You have a pending job application. After logging in, you will be redirected to complete your application.';
$string['confirm_next_steps'] = 'Next Steps';
$string['confirm_step_login'] = 'Log in with your username and password';
$string['confirm_step_application'] = 'Complete your pending job application';
$string['confirm_step_browse'] = 'Browse available job vacancies';
$string['confirm_step_apply'] = 'Submit your applications';
$string['confirm_username_reminder'] = 'Your username is:';
$string['confirm_possible_reasons'] = 'Possible Reasons';
$string['confirm_reason_expired'] = 'The confirmation link has expired';
$string['confirm_reason_invalid'] = 'The link was incomplete or modified';
$string['confirm_reason_already'] = 'Your account was already confirmed';
$string['confirm_reason_notexist'] = 'The account does not exist';
$string['confirm_what_to_do'] = 'What You Can Do';
$string['confirm_todo_trylogin'] = 'Try logging in - your account may already be active';
$string['confirm_todo_register'] = 'Register again if you haven\'t completed the process';
$string['confirm_todo_contact'] = 'Contact support if the problem persists';
$string['confirm_need_help'] = 'Need help?';

// =============================================================================
// HELP STRINGS
// =============================================================================

$string['help'] = 'Help';
$string['help_convocatoria'] = 'A convocatoria is a call for applications that groups multiple vacancies';
$string['help_vacancy'] = 'A vacancy represents a job position that applicants can apply for';
$string['help_application'] = 'An application is a submission from a candidate for a specific vacancy';
$string['help_documents'] = 'Documents are the files that applicants must submit as part of their application';
$string['help_reviewer'] = 'Reviewers are responsible for validating applicant documents';
$string['help_committee'] = 'The selection committee evaluates candidates and makes hiring decisions';
$string['help_exemption'] = 'Exemptions allow certain applicants to skip specific document requirements';

// =============================================================================
// EMPTY STATES
// =============================================================================

$string['noresults'] = 'No results found';
$string['nodata'] = 'No data available';
$string['noitems'] = 'No items to display';
$string['emptylist'] = 'The list is empty';
$string['getstarted'] = 'Get started';
$string['createfirst'] = 'Create your first {$a}';

// =============================================================================
// FORM VALIDATION
// =============================================================================

$string['required'] = 'Required';
$string['optional'] = 'Optional';
$string['fieldrequired'] = 'This field is required';
$string['invalidvalue'] = 'Invalid value';
$string['minlength'] = 'Minimum length is {$a} characters';
$string['maxlength'] = 'Maximum length is {$a} characters';
$string['minvalue'] = 'Minimum value is {$a}';
$string['maxvalue'] = 'Maximum value is {$a}';
$string['dateformat'] = 'Date format: YYYY-MM-DD';
$string['selectoption'] = 'Please select an option';
$string['entervalue'] = 'Please enter a value';
$string['uploadorselect'] = 'Upload or select a file';

// =============================================================================
// PRIVACY API
// =============================================================================

$string['privacy:metadata:local_jobboard_application'] = 'Information about user applications';
$string['privacy:metadata:local_jobboard_application:userid'] = 'The ID of the user who submitted the application';
$string['privacy:metadata:local_jobboard_application:vacancyid'] = 'The ID of the vacancy applied for';
$string['privacy:metadata:local_jobboard_application:status'] = 'The status of the application';
$string['privacy:metadata:local_jobboard_application:timecreated'] = 'The time when the application was created';

$string['privacy:metadata:local_jobboard_document'] = 'Information about user documents';
$string['privacy:metadata:local_jobboard_document:userid'] = 'The ID of the user who uploaded the document';
$string['privacy:metadata:local_jobboard_document:filename'] = 'The name of the uploaded file';
$string['privacy:metadata:local_jobboard_document:timecreated'] = 'The time when the document was uploaded';

$string['privacy:metadata:local_jobboard_applicant_profile'] = 'Information about applicant profiles';
$string['privacy:metadata:local_jobboard_applicant_profile:userid'] = 'The ID of the user';
$string['privacy:metadata:local_jobboard_applicant_profile:personaldata'] = 'Personal information provided by the applicant';

$string['privacy:metadata:local_jobboard_audit'] = 'Audit log of user actions';
$string['privacy:metadata:local_jobboard_audit:userid'] = 'The ID of the user who performed the action';
$string['privacy:metadata:local_jobboard_audit:action'] = 'The action performed';
$string['privacy:metadata:local_jobboard_audit:timecreated'] = 'The time when the action was performed';

// Privacy metadata for all plugin tables (AGENTS.md compliance)
$string['privacy:metadata:application'] = 'Job application records submitted by users';
$string['privacy:metadata:application:userid'] = 'User ID of the applicant';
$string['privacy:metadata:application:vacancyid'] = 'Vacancy ID applied for';
$string['privacy:metadata:application:status'] = 'Application status';
$string['privacy:metadata:application:digitalsignature'] = 'Digital signature provided by applicant';
$string['privacy:metadata:application:coverletter'] = 'Cover letter text';
$string['privacy:metadata:application:applicationdata'] = 'Additional application form data (JSON)';
$string['privacy:metadata:application:consentgiven'] = 'Whether consent was given';
$string['privacy:metadata:application:consenttimestamp'] = 'Time when consent was given';
$string['privacy:metadata:application:consentip'] = 'IP address when consent was given';
$string['privacy:metadata:application:consentuseragent'] = 'Browser user agent when consent was given';
$string['privacy:metadata:application:timecreated'] = 'Time application was submitted';

$string['privacy:metadata:document'] = 'Documents uploaded by applicants';
$string['privacy:metadata:document:applicationid'] = 'Application ID the document belongs to';
$string['privacy:metadata:document:documenttype'] = 'Type of document';
$string['privacy:metadata:document:filename'] = 'Original filename';
$string['privacy:metadata:document:uploadedby'] = 'User who uploaded the document';
$string['privacy:metadata:document:issuedate'] = 'Issue date of the document';
$string['privacy:metadata:document:timecreated'] = 'Upload timestamp';

$string['privacy:metadata:exemption'] = 'ISER exemption records for historic personnel';
$string['privacy:metadata:exemption:userid'] = 'User ID with exemption';
$string['privacy:metadata:exemption:exemptiontype'] = 'Type of exemption';
$string['privacy:metadata:exemption:exempteddocs'] = 'Documents exempted';
$string['privacy:metadata:exemption:validfrom'] = 'Start date of exemption';
$string['privacy:metadata:exemption:validuntil'] = 'End date of exemption';
$string['privacy:metadata:exemption:notes'] = 'Admin notes about exemption';
$string['privacy:metadata:exemption:timecreated'] = 'Time exemption was created';

$string['privacy:metadata:workflowlog'] = 'Application status change history';
$string['privacy:metadata:workflowlog:applicationid'] = 'Application ID';
$string['privacy:metadata:workflowlog:previousstatus'] = 'Previous application status';
$string['privacy:metadata:workflowlog:newstatus'] = 'New application status';
$string['privacy:metadata:workflowlog:changedby'] = 'User who changed the status';
$string['privacy:metadata:workflowlog:comments'] = 'Comments about the status change';
$string['privacy:metadata:workflowlog:timecreated'] = 'Time of status change';

$string['privacy:metadata:audit'] = 'Audit log entries';
$string['privacy:metadata:audit:userid'] = 'User who performed the action';
$string['privacy:metadata:audit:action'] = 'Action performed';
$string['privacy:metadata:audit:entitytype'] = 'Type of entity affected';
$string['privacy:metadata:audit:entityid'] = 'ID of entity affected';
$string['privacy:metadata:audit:ipaddress'] = 'IP address of user';
$string['privacy:metadata:audit:useragent'] = 'Browser user agent';
$string['privacy:metadata:audit:extradata'] = 'Additional action data';
$string['privacy:metadata:audit:timecreated'] = 'Time of action';

$string['privacy:metadata:notification'] = 'Email notification records';
$string['privacy:metadata:notification:userid'] = 'Recipient user ID';
$string['privacy:metadata:notification:templatecode'] = 'Email template used';
$string['privacy:metadata:notification:data'] = 'Notification placeholder data';
$string['privacy:metadata:notification:status'] = 'Notification status';
$string['privacy:metadata:notification:timecreated'] = 'Time notification was queued';

$string['privacy:metadata:interviewer'] = 'Interview panel member assignments';
$string['privacy:metadata:interviewer:userid'] = 'User assigned as interviewer';
$string['privacy:metadata:interviewer:interviewid'] = 'Interview ID';
$string['privacy:metadata:interviewer:timecreated'] = 'Time of assignment';

$string['privacy:metadata:committeemember'] = 'Selection committee memberships';
$string['privacy:metadata:committeemember:userid'] = 'User ID of committee member';
$string['privacy:metadata:committeemember:committeeid'] = 'Committee ID';
$string['privacy:metadata:committeemember:role'] = 'Role in committee';
$string['privacy:metadata:committeemember:addedby'] = 'User who added the member';
$string['privacy:metadata:committeemember:timecreated'] = 'Time member was added';

$string['privacy:metadata:evaluation'] = 'Application evaluations by committee members';
$string['privacy:metadata:evaluation:userid'] = 'User who submitted evaluation';
$string['privacy:metadata:evaluation:applicationid'] = 'Application being evaluated';
$string['privacy:metadata:evaluation:score'] = 'Evaluation score';
$string['privacy:metadata:evaluation:vote'] = 'Evaluator vote';
$string['privacy:metadata:evaluation:comments'] = 'Evaluation comments';
$string['privacy:metadata:evaluation:timecreated'] = 'Time evaluation was submitted';

$string['privacy:metadata:consent'] = 'User consent records for data processing';
$string['privacy:metadata:consent:userid'] = 'User who gave consent';
$string['privacy:metadata:consent:consenttype'] = 'Type of consent (data treatment, terms, privacy)';
$string['privacy:metadata:consent:consentgiven'] = 'Whether consent was given';
$string['privacy:metadata:consent:consentversion'] = 'Version of policy consented to';
$string['privacy:metadata:consent:ipaddress'] = 'IP address at consent time';
$string['privacy:metadata:consent:useragent'] = 'Browser user agent at consent time';
$string['privacy:metadata:consent:timecreated'] = 'Time consent was recorded';

$string['privacy:metadata:applicantprofile'] = 'Extended profile data for job applicants';
$string['privacy:metadata:applicantprofile:userid'] = 'User ID of applicant';
$string['privacy:metadata:applicantprofile:doctype'] = 'Document type (CC, CE, etc.)';
$string['privacy:metadata:applicantprofile:birthdate'] = 'Date of birth';
$string['privacy:metadata:applicantprofile:gender'] = 'Gender';
$string['privacy:metadata:applicantprofile:education_level'] = 'Highest education level';
$string['privacy:metadata:applicantprofile:degree_title'] = 'Degree or title obtained';
$string['privacy:metadata:applicantprofile:expertise_area'] = 'Area of expertise';
$string['privacy:metadata:applicantprofile:experience_years'] = 'Years of experience';
$string['privacy:metadata:applicantprofile:timecreated'] = 'Profile creation time';

$string['privacy:metadata:files'] = 'Files uploaded by users (application documents)';

// Additional strings for privacy export
$string['consent'] = 'Consent';
$string['applicantprofile'] = 'Applicant Profile';

// =============================================================================
// DASHBOARD CONSOLIDATED FEATURES (AGENTS.md 22.2)
// =============================================================================

// Time ago strings
$string['timeago_justnow'] = 'Just now';
$string['timeago_minutes'] = '{$a} minutes ago';
$string['timeago_hours'] = '{$a} hours ago';
$string['timeago_days'] = '{$a} days ago';

// Next convocatoria banner
$string['critical'] = 'CRITICAL';
$string['closingsoon'] = 'Closing Soon';
$string['closingon'] = 'Closes on';
$string['daysremaining'] = 'days remaining';
$string['viewdetails'] = 'View Details';

// Pending notifications
$string['pendingnotifications'] = 'Pending Notifications';
$string['notification_application_received'] = 'Application received';
$string['notification_application_received_subject'] = 'Application Confirmation - {$a->vacancy}';
$string['notification_application_received_body'] = 'Dear {$a->fullname},

We have received your application for the vacancy "{$a->vacancy}" (Code: {$a->code}).

Your application has been successfully registered and is under review. You will receive notifications about your application status via this email.

Application date: {$a->date}

You can check the status of your application at any time by logging into the platform.

Best regards,
{$a->sitename}';
$string['notification_docs_validated'] = 'Documents validated';
$string['notification_docs_rejected'] = 'Documents need attention';
$string['notification_interview_scheduled'] = 'Interview scheduled';
$string['notification_status_changed'] = 'Application status changed';
$string['notification_application_selected'] = 'Congratulations! You have been selected';
$string['notification_application_rejected'] = 'Application not selected';

// Recent activity
$string['recentactivity'] = 'Recent Activity';
$string['activity_application_submitted'] = 'New application submitted';
$string['activity_document_uploaded'] = 'Document uploaded';
$string['activity_vacancy_published'] = 'Vacancy published';
$string['activity_convocatoria_opened'] = 'Convocatoria opened';
$string['activity_status_viewed'] = 'Application status viewed';

// =============================================================================
// CLI & IMPORT
// =============================================================================

$string['cli_usage'] = 'Usage: php cli.php [options]';
$string['cli_help'] = 'Display this help message';
$string['cli_version'] = 'Display version information';
$string['cli_import'] = 'Import data from file';
$string['cli_export'] = 'Export data to file';
$string['cli_processing'] = 'Processing...';
$string['cli_complete'] = 'Complete';
$string['cli_error'] = 'Error: {$a}';
$string['cli_success'] = 'Success: {$a}';
$string['cli_recordsprocessed'] = '{$a} records processed';
$string['cli_recordsimported'] = '{$a} records imported';
$string['cli_recordsexported'] = '{$a} records exported';
$string['cli_recordsfailed'] = '{$a} records failed';

$string['import'] = 'Import';
$string['importfile'] = 'Import file';
$string['importformat'] = 'Import format';
$string['importoptions'] = 'Import options';
$string['importpreview'] = 'Import preview';
$string['importconfirm'] = 'Confirm import';
$string['importstarted'] = 'Import started';
$string['importcompleted'] = 'Import completed';
$string['importfailed'] = 'Import failed';
$string['importresults'] = 'Import results';
$string['rowsimported'] = '{$a} rows imported';
$string['rowsskipped'] = '{$a} rows skipped';
$string['rowsfailed'] = '{$a} rows failed';

// =============================================================================
// EVENTS
// =============================================================================

$string['event_application_created'] = 'Application created';
$string['event_application_status_changed'] = 'Application status changed';
$string['event_document_uploaded'] = 'Document uploaded';
$string['event_vacancy_created'] = 'Vacancy created';
$string['event_vacancy_updated'] = 'Vacancy updated';
$string['event_vacancy_published'] = 'Vacancy published';
$string['event_vacancy_closed'] = 'Vacancy closed';
$string['event_vacancy_deleted'] = 'Vacancy deleted';

// =============================================================================
// MISCELLANEOUS
// =============================================================================

$string['loading'] = 'Loading...';
$string['pleasewait'] = 'Please wait...';
$string['processing'] = 'Processing...';
$string['saving'] = 'Saving...';
$string['uploading'] = 'Uploading...';
$string['searching'] = 'Searching...';
$string['nochanges'] = 'No changes detected';
$string['unsavedchanges'] = 'You have unsaved changes';
$string['confirmleavepage'] = 'Are you sure you want to leave this page? Any unsaved changes will be lost.';
$string['sessiontimeout'] = 'Your session has timed out';
$string['connectionerror'] = 'Connection error. Please check your internet connection.';
$string['tryagain'] = 'Try again';
$string['contactsupport'] = 'Contact support';
$string['reporterror'] = 'Report an error';
$string['needhelp'] = 'Need help?';
$string['supporthelptext'] = 'If you encounter any issues or need assistance, please contact our support team.';
$string['supportemail'] = 'soporteplataformas@iser.edu.co';
$string['supportemailsubject'] = 'Job Board Support Request';
$string['supportemailbody'] = 'Hello Support Team,%0D%0A%0D%0AI need assistance with the Job Board system.%0D%0A%0D%0ADescription of the issue:%0D%0A[Please describe your issue here]%0D%0A%0D%0APage URL: {$a->url}%0D%0AUser: {$a->username}%0D%0ADate: {$a->date}%0D%0A%0D%0AThank you.';

// Support form page strings.
$string['support_page_title'] = 'Report Technical Issue';
$string['support_warning_title'] = 'Technical Support Only';
$string['support_warning_message'] = 'This form is ONLY for reporting technical errors with the Job Board system (bugs, errors, problems with document uploads, etc.). This is NOT for questions about selection processes, application status, or general inquiries. For those questions, please contact the Human Resources department directly.';
$string['support_info_title'] = 'What to Report';
$string['support_info_description'] = 'Use this form to report technical problems you have encountered while using the Job Board system. Please provide as much detail as possible to help us identify and fix the issue.';
$string['support_examples_title'] = 'Examples of issues to report:';
$string['support_example_1'] = 'Problems uploading documents (error messages, files not saving)';
$string['support_example_2'] = 'Page errors or unexpected behavior';
$string['support_example_3'] = 'Forms not submitting correctly';
$string['support_example_4'] = 'Display problems or broken layouts';
$string['support_error_details'] = 'Error Details';
$string['support_error_type'] = 'Type of Error';
$string['support_select_error_type'] = 'Select the type of error...';
$string['support_type_document_upload'] = 'Document upload problem';
$string['support_type_form_submission'] = 'Form submission error';
$string['support_type_page_error'] = 'Page error or crash';
$string['support_type_login_issue'] = 'Login or access problem';
$string['support_type_display_issue'] = 'Display or layout problem';
$string['support_type_notification_issue'] = 'Notification or email problem';
$string['support_type_other'] = 'Other technical issue';
$string['support_error_description'] = 'Error Description';
$string['support_error_description_help'] = 'Describe the error you encountered in detail. Include what you were trying to do and what happened instead.';
$string['support_description_placeholder'] = 'Describe the error in detail. What were you trying to do? What error message did you see?';
$string['support_description_too_short'] = 'Please provide a more detailed description (at least 20 characters)';
$string['support_steps_too_short'] = 'Please provide more detailed steps to reproduce the problem (at least 20 characters)';
$string['support_instructions_title'] = 'Important instructions';
$string['support_instructions_detail'] = 'To help you effectively, it is essential that you be as detailed as possible in your report. A clear and complete description allows us to resolve your problem more quickly.';
$string['support_instructions_tip1'] = '<strong>Be specific:</strong> Describe exactly what you were doing when the problem occurred.';
$string['support_instructions_tip2'] = '<strong>Include screenshots:</strong> Images help us better understand the problem. Use the image button in the editor to attach them.';
$string['support_instructions_tip3'] = '<strong>Detail the steps:</strong> List step by step how to reproduce the error so we can verify it.';
$string['support_steps_to_reproduce'] = 'Steps to Reproduce (Required)';
$string['support_steps_to_reproduce_help'] = 'Describe step by step the actions you took before encountering the error. This is crucial for us to reproduce and fix the problem.';
$string['support_steps_placeholder'] = '1. Go to page...\n2. Click on...\n3. Fill in...\n4. Error appears...';
$string['support_expected_behavior'] = 'Expected Behavior';
$string['support_expected_placeholder'] = 'What did you expect to happen?';
$string['support_page_url'] = 'Page URL';
$string['support_contact_info'] = 'Your Contact Information';
$string['support_reporter_name'] = 'Your Name';
$string['support_reporter_email'] = 'Your Email';
$string['support_browser'] = 'Browser';
$string['support_timestamp'] = 'Date/Time';
$string['support_user_info'] = 'User Information';
$string['support_submit'] = 'Submit Report';
$string['support_success_message'] = 'Your error report has been submitted successfully. Our technical team will review it and may contact you if additional information is needed.';
$string['support_email_failed'] = 'There was a problem sending your report. Please try again later or contact support directly at the email shown.';
$string['support_email_subject'] = 'Job Board - Technical Support Request';
$string['support_email_header'] = 'NEW TECHNICAL SUPPORT REQUEST - JOB BOARD';
$string['technicaldetails'] = 'Technical details';
$string['debuginfo'] = 'Debug information';
$string['version'] = 'Version';
$string['copyright'] = 'Copyright';
$string['allrightsreserved'] = 'All rights reserved';
$string['poweredby'] = 'Powered by';
$string['lastupdate'] = 'Last update';
$string['timezone'] = 'Timezone';
$string['language'] = 'Language';
$string['selectlanguage'] = 'Select language';

// Support settings.
$string['supportsettings'] = 'Support Settings';
$string['supportsettings_desc'] = 'Configure the support system for technical issue reporting.';
$string['support_emails'] = 'Support Email Addresses';
$string['support_emails_desc'] = 'Enter email addresses (one per line or comma-separated) that will receive technical support requests from users. These addresses receive notifications when users report bugs or errors through the support form.';

// Accessibility
$string['skiptomaincontent'] = 'Skip to main content';
$string['opensinnewwindow'] = 'Opens in new window';
$string['expandcollapse'] = 'Expand/Collapse';
$string['sortascending'] = 'Sort ascending';
$string['sortdescending'] = 'Sort descending';
$string['sortby'] = 'Sort by';
$string['filterby'] = 'Filter by';
$string['showing'] = 'Showing';
$string['entries'] = 'entries';
$string['firstpage'] = 'First page';
$string['lastpage'] = 'Last page';
$string['previouspage'] = 'Previous page';
$string['nextpage'] = 'Next page';

// =============================================================================
// ADDITIONAL STRINGS - CONVOCATORIA MANAGEMENT
// =============================================================================

$string['addconvocatoria'] = 'Add convocatoria';
$string['editconvocatoria'] = 'Edit convocatoria';
$string['deleteconvocatoria'] = 'Delete convocatoria';
$string['convocatoriaactive'] = 'Active';
$string['convocatoriaarchived'] = 'Archived';
$string['convocatoriaclosedmsg'] = 'This convocatoria is closed';
$string['convocatoriadates'] = 'Convocatoria dates';
$string['convocatoriadocexemptions'] = 'Document exemptions for this convocatoria';
$string['convocatoriahelp'] = 'Help on convocatorias';
$string['convocatoriaopened'] = 'Convocatoria opened successfully';
$string['convocatoriareopened'] = 'Convocatoria reopened successfully';
$string['convocatorias_dashboard_desc'] = 'Manage all convocatorias from this dashboard';
$string['openconvocatoria'] = 'Open convocatoria';
$string['manageconvocatorias'] = 'Manage convocatorias';
$string['browseconvocatorias'] = 'Browse convocatorias';
$string['browseconvocatorias_desc'] = 'View all available convocatorias';
$string['backtoconvocatoria'] = 'Back to convocatoria';
$string['backtoconvocatorias'] = 'Back to convocatorias';
$string['totalconvocatorias'] = 'Total convocatorias';
$string['confirmarchiveconvocatoria'] = 'Are you sure you want to archive this convocatoria?';
$string['confirmcloseconvocatoria'] = 'Are you sure you want to close this convocatoria?';
$string['confirmopenconvocatoria'] = 'Are you sure you want to open this convocatoria?';
$string['confirmreopenconvocatoria'] = 'Are you sure you want to reopen this convocatoria?';
$string['confirmdeletevconvocatoria'] = 'Are you sure you want to delete this convocatoria? This action cannot be undone.';
$string['gotocreateconvocatoria'] = 'Create new convocatoria';
$string['createvacancyinconvocatoriadesc'] = 'Create a new vacancy within this convocatoria';

// =============================================================================
// ADDITIONAL STRINGS - VACANCY MANAGEMENT
// =============================================================================

$string['addvacancy'] = 'Add vacancy';
$string['managevacancies'] = 'Manage vacancies';
$string['vacancies_dashboard_desc'] = 'View and manage all vacancies';
$string['backtovacancies'] = 'Back to vacancies';
$string['availablevacancies'] = 'Available vacancies';
$string['openvacancies'] = 'Open vacancies';
$string['publishedvacancies'] = 'Published vacancies';
$string['vacanciesfound'] = 'Vacancies found';
$string['searchvacancies'] = 'Search vacancies';
$string['explorevacancias'] = 'Explore vacancies';
$string['explore'] = 'Explore';
$string['browse_vacancies_desc'] = 'Browse all available vacancies';
$string['applytovacancy'] = 'Apply to vacancy';
$string['vacancyinfo'] = 'Vacancy information';
$string['vacancyopen'] = 'Vacancy opened';
$string['vacancyreopened'] = 'Vacancy reopened';
$string['vacancyunpublished'] = 'Vacancy unpublished';
$string['vacancy_inherits_dates'] = 'This vacancy inherits dates from the convocatoria';
$string['vacancy_status_draft'] = 'Draft';
$string['vacancy_status_published'] = 'Published';
$string['unknownvacancy'] = 'Unknown vacancy';
$string['totalpositions'] = 'Total positions';
$string['closingdate'] = 'Closing date';
$string['closesindays'] = 'Closes in {$a} days';
$string['closingsoondays'] = 'Closing soon (within {$a} days)';
$string['publish'] = 'Publish';
$string['unpublish'] = 'Unpublish';
$string['reopen'] = 'Reopen';
$string['confirmpublish'] = 'Are you sure you want to publish this vacancy?';
$string['confirmunpublish'] = 'Are you sure you want to unpublish this vacancy?';
$string['confirmreopen'] = 'Are you sure you want to reopen this vacancy?';
$string['confirmclose'] = 'Are you sure you want to close this vacancy?';
$string['sharethisvacancy'] = 'Share this vacancy';

// =============================================================================
// ADDITIONAL STRINGS - APPLICATION MANAGEMENT
// =============================================================================

$string['applicationerror'] = 'Error processing application';
$string['applicationlimits'] = 'Application limits';
$string['applicationlimits_perconvocatoria_desc'] = 'Maximum applications per user per convocatoria';
$string['backtoapplications'] = 'Back to applications';
$string['noapplicationsfound'] = 'No applications found';
$string['allapplicants'] = 'All applicants';
$string['myapplicationcount'] = 'My applications: {$a}';
$string['myapplications_desc'] = 'View and manage your applications';
$string['dateapplied'] = 'Date applied';
$string['datesubmitted'] = 'Date submitted';
$string['viewmyapplications'] = 'View my applications';
$string['confirmwithdraw'] = 'Are you sure you want to withdraw this application?';
$string['loginrequiredtoapply'] = 'You must be logged in to apply';
$string['completeprofile_required'] = 'Please complete your profile before applying';
$string['maxapplicationsperuser'] = 'Maximum applications per user';

// =============================================================================
// ADDITIONAL STRINGS - DOCUMENT TYPES & MANAGEMENT
// =============================================================================

$string['adddoctype'] = 'Add document type';
$string['doctypelist'] = 'Document type list';
$string['doctypes_desc'] = 'Configure document types required for applications';
$string['doctypeshelp'] = 'Document types help';
$string['doctypecreated'] = 'Document type created successfully';
$string['doctypeupdated'] = 'Document type updated successfully';
$string['confirmdeletedoctype_msg'] = 'Are you sure you want to delete this document type?';
$string['aboutdoctypes'] = 'About document types';
$string['totaldoctypes'] = 'Total document types';
$string['enableddoctypes'] = 'Enabled document types';
$string['requireddoctypes'] = 'Required document types';
$string['conditionaldoctypes'] = 'Conditional document types';
$string['conditionalnote'] = 'This document is conditionally required';
$string['conditional_document_note'] = 'This document may be required based on certain conditions';
$string['docrequirements'] = 'Document requirements';
$string['documentsettings'] = 'Document settings';
$string['documentshelp'] = 'Help on documents';
$string['documentnotfound'] = 'Document not found';
$string['documentexpired'] = 'This document has expired';
$string['documentissuedate'] = 'Issue date';
$string['documentnumber'] = 'Document number';
$string['documentref'] = 'Document reference';
$string['documentref_desc'] = 'Reference number for this document';
$string['documentreuploaded'] = 'Document re-uploaded successfully';
$string['documentsapproved'] = 'Documents approved';
$string['documentsreviewed'] = 'Documents reviewed';
$string['documentvalidated'] = 'Document validated successfully';
$string['newdocument'] = 'New document';
$string['numdocs'] = 'Number of documents';
$string['pendingdocs'] = 'Pending documents';
$string['pending_docs_alert'] = 'You have {$a} pending documents to upload';
$string['missing_docs_alert'] = 'You have {$a} required documents pending to upload';
$string['pending_review_alert'] = 'You have {$a} documents awaiting review';
$string['pendingreviews'] = 'Pending reviews';
$string['actionrequired'] = 'Action required';
$string['missing_documents_list'] = 'Missing documents';
$string['missing_documents'] = 'Documents not uploaded';
$string['rejected_documents_reupload'] = 'Rejected documents';
$string['rejected_documents_list'] = 'Need re-upload';
$string['alreadyvalidated'] = 'Already validated';
$string['autovalidated'] = 'Auto-validated';
$string['validated'] = 'Validated';
$string['validationapproved'] = 'Validation approved';
$string['validationrequirements'] = 'Validation requirements';
$string['reuploadhelp'] = 'Upload a new version of this document';
$string['uploadnewfile'] = 'Upload new file';
$string['uploadfailed'] = 'Upload failed';

// =============================================================================
// ADDITIONAL STRINGS - DOCUMENT CONDITIONS & CATEGORIES
// =============================================================================

$string['gendercondition'] = 'Gender condition';
$string['menonly'] = 'Men only';
$string['womenonly'] = 'Women only';
$string['doc_condition_men_only'] = 'Required for men only';
$string['doc_condition_women_only'] = 'Required for women only';
$string['doc_condition_iser_exempt'] = 'Exempt for ISER employees';
$string['doc_condition_profession_exempt'] = 'Exempt based on profession';
$string['professionexempt'] = 'Profession exempt';
$string['iserexempted'] = 'ISER exempted';
$string['iserexempted_help'] = 'Documents exempted for ISER employees';
$string['multipledocs_'] = 'Multiple documents';
$string['multipledocs_notice'] = 'You can upload multiple documents of this type';

// =============================================================================
// ADDITIONAL STRINGS - VALIDITY & EXPIRATION
// =============================================================================

$string['validfrom'] = 'Valid from';
$string['validuntil'] = 'Valid until';
$string['validityperiod'] = 'Validity period';
$string['defaultvalidfrom'] = 'Default valid from';
$string['defaultvaliduntil'] = 'Default valid until';
$string['noexpiry'] = 'No expiry';
$string['antecedentesmaxdays'] = 'Background check validity (days)';
$string['epsmaxdays'] = 'EPS certificate validity (days)';
$string['pensionmaxdays'] = 'Pension certificate validity (days)';
$string['defaultmaxagedays'] = 'Default document validity (days)';

// =============================================================================
// ADDITIONAL STRINGS - EXEMPTIONS
// =============================================================================

$string['addexemption'] = 'Add exemption';
$string['exempteddocs'] = 'Exempted documents';
$string['exempteddocs_desc'] = 'Documents exempted from requirements';
$string['exempteddoctypes'] = 'Exempted document types';
$string['exemptioncreated'] = 'Exemption created successfully';
$string['exemptiondetails'] = 'Exemption details';
$string['exemptionerror'] = 'Error creating exemption';
$string['exemptionnotice'] = 'Exemption notice';
$string['exemptionreduceddocs'] = 'Reduced document requirements due to exemption';
$string['exemptionrevoked'] = 'Exemption revoked successfully';
$string['exemptionrevokeerror'] = 'Error revoking exemption';
$string['exemptionupdated'] = 'Exemption updated successfully';
$string['exemptionusagehistory'] = 'Exemption usage history';
$string['noexemptionusage'] = 'No exemption usage recorded';
$string['confirmrevokeexemption'] = 'Are you sure you want to revoke this exemption?';
$string['revokeexemption'] = 'Revoke exemption';
$string['revoke'] = 'Revoke';
$string['revoked'] = 'Revoked';
$string['revokedby'] = 'Revoked by';
$string['revokereason'] = 'Revoke reason';
$string['manageexemptions_desc'] = 'Configure and manage document exemptions';
$string['defaultexemptiontype'] = 'Default exemption type';
$string['age_exempt_notice'] = 'Age-based exemption notice';
$string['ageexemptionthreshold'] = 'Age exemption threshold';

// =============================================================================
// ADDITIONAL STRINGS - REVIEW & VALIDATION
// =============================================================================

$string['reviewall'] = 'Review all';
$string['reviewapplication'] = 'Review application';
$string['reviewdocuments'] = 'Review documents';
$string['reviewsubmitted'] = 'Review submitted successfully';
$string['reviewsubmitted_with_notification'] = 'Review submitted and notification sent';
$string['review_dashboard_desc'] = 'Review applications and documents';
$string['backtoreviewlist'] = 'Back to review list';
$string['pendingreview'] = 'Pending review';
$string['pendingassignments'] = 'Pending assignments';
$string['bulkvalidation_desc'] = 'Validate multiple documents at once';
$string['bulkvalidationcomplete'] = 'Bulk validation complete';
$string['bulkrejected'] = 'Bulk rejected';
$string['bulkactionerrors'] = 'Some bulk actions failed';
$string['avgvalidationtime'] = 'Average validation time';
$string['checklistitems'] = 'Checklist items';

// =============================================================================
// ADDITIONAL STRINGS - REVIEWERS
// =============================================================================

$string['revieweradded'] = 'Reviewer added successfully';
$string['revieweradderror'] = 'Error adding reviewer';
$string['reviewerremoved'] = 'Reviewer removed successfully';
$string['reviewerremoveerror'] = 'Error removing reviewer';
$string['assignreviewers_desc'] = 'Assign reviewers to applications';
$string['autoassigncomplete'] = 'Auto-assignment complete';
$string['program_reviewers'] = 'Program reviewers';
$string['program_reviewers_desc'] = 'Reviewers assigned by program';

// =============================================================================
// ADDITIONAL STRINGS - COMMITTEES
// =============================================================================

$string['committeecreated'] = 'Committee created successfully';
$string['committeecreateerror'] = 'Error creating committee';
$string['committees_desc'] = 'Manage evaluation committees';
$string['managecommittees'] = 'Manage committees';
$string['facultycommitteedefaultname'] = 'Faculty Committee';
$string['memberadderror'] = 'Error adding member';
$string['memberremoveerror'] = 'Error removing member';

// =============================================================================
// ADDITIONAL STRINGS - INTERVIEWS
// =============================================================================

$string['completeinterview'] = 'Complete interview';
$string['interviewcompleted'] = 'Interview completed';
$string['interviewfeedback'] = 'Interview feedback';
$string['interviewinstructions'] = 'Interview instructions';
$string['interviewscheduleerror'] = 'Error scheduling interview';
$string['interviewstatus_'] = 'Interview status';
$string['interviewtype_'] = 'Interview type';
$string['interviewtype_inperson'] = 'In person';
$string['interviewtype_phone'] = 'Phone';
$string['interviewtype_video'] = 'Video call';
$string['selectinterviewers'] = 'Select interviewers';
$string['rescheduledby'] = 'Rescheduled by';
$string['reschedulednote'] = 'Reschedule note';
$string['markedasnoshow'] = 'Marked as no-show';
$string['markednoshow'] = 'No-show';

// =============================================================================
// ADDITIONAL STRINGS - RATINGS & RECOMMENDATIONS
// =============================================================================

$string['overallrating'] = 'Overall rating';
$string['rating_excellent'] = 'Excellent';
$string['rating_verygood'] = 'Very good';
$string['rating_good'] = 'Good';
$string['rating_fair'] = 'Fair';
$string['rating_poor'] = 'Poor';
$string['recommend_'] = 'Recommendation';
$string['recommend_hire'] = 'Recommend to hire';
$string['recommend_reject'] = 'Do not recommend';
$string['recommend_furtherreview'] = 'Needs further review';

// =============================================================================
// ADDITIONAL STRINGS - EMAIL TEMPLATES
// =============================================================================

$string['email_templates'] = 'Email templates';
$string['emailtemplates_desc'] = 'Manage email notification templates';
$string['edit_template'] = 'Edit template';
$string['back_to_templates'] = 'Back to templates';
$string['no_templates'] = 'No templates found';
$string['total_templates'] = 'Total templates';
$string['templates_enabled'] = 'Templates enabled';
$string['templates_disabled'] = 'Templates disabled';
$string['templates_installed'] = 'Templates installed';
$string['template_name'] = 'Template name';
$string['template_code'] = 'Template code';
$string['template_subject'] = 'Subject';
$string['template_body'] = 'Body';
$string['template_content'] = 'Content';
$string['template_description'] = 'Description';
$string['template_enabled'] = 'Enabled';
$string['template_enabled_desc'] = 'Enable or disable this template';
$string['template_priority'] = 'Priority';
$string['template_category'] = 'Category';
$string['template_categories'] = 'Categories';
$string['template_preview'] = 'Preview';
$string['template_preview_hint'] = 'Preview how the email will look';
$string['template_settings'] = 'Template settings';
$string['template_info'] = 'Template information';
$string['template_not_found'] = 'Template not found';
$string['template_saved_success'] = 'Template saved successfully';
$string['template_deleted_success'] = 'Template deleted successfully';
$string['template_enabled_success'] = 'Template enabled successfully';
$string['template_disabled_success'] = 'Template disabled successfully';
$string['template_delete_failed'] = 'Failed to delete template';
$string['template_reset_success'] = 'Template reset to default';
$string['template_help_title'] = 'Email template help';
$string['template_help_placeholders'] = 'Use placeholders to insert dynamic content';
$string['template_help_html'] = 'HTML formatting is supported';
$string['template_help_tenant'] = 'Templates can be customized per tenant';
$string['email_updated'] = 'Email template updated';
$string['email_action_reupload'] = 'Request document re-upload';
$string['toggle_status'] = 'Toggle status';
$string['reset_to_default'] = 'Reset to default';

// =============================================================================
// ADDITIONAL STRINGS - PLACEHOLDERS
// =============================================================================

$string['placeholders'] = 'Placeholders';
$string['placeholders_help'] = 'Available placeholders for this template';
$string['available_placeholders'] = 'Available placeholders';
$string['copy_placeholder'] = 'Copy placeholder';
$string['html_support'] = 'HTML supported';

// =============================================================================
// ADDITIONAL STRINGS - SIGNUP & PROFILE
// =============================================================================

$string['basicinfo'] = 'Basic information';
$string['personalinfo'] = 'Personal information';
$string['education'] = 'Education';
$string['educationlevel'] = 'Education level';
$string['coverletter'] = 'Cover letter';
$string['declaration'] = 'Declaration';
$string['declarationtext'] = 'I declare that all information provided is accurate and complete';
$string['declarationaccept'] = 'I accept the declaration';
$string['declarationrequired'] = 'You must accept the declaration to continue';
$string['verification'] = 'Verification';
$string['profilereview'] = 'Profile review';
$string['profilereview_info'] = 'Please review your profile information';
$string['updateprofile_intro'] = 'Update your profile information';
$string['updateprofile_submit'] = 'Update profile';
$string['updateprofile_success'] = 'Profile updated successfully';
$string['updateprofile_title'] = 'Update profile';
$string['updateprofile_button'] = 'Update profile';
$string['updateprofile_dashboard_desc'] = 'Keep your personal information, contact details, and professional profile up to date for better application outcomes.';
$string['update_username'] = 'Update username';
$string['update_username_desc'] = 'Allow users to update their username';

// =============================================================================
// ADDITIONAL STRINGS - PASSWORD
// =============================================================================

$string['password'] = 'Password';
$string['currentpassword'] = 'Current password';
$string['newpassword'] = 'New password';
$string['confirmpassword'] = 'Confirm password';
$string['currentpassword_invalid'] = 'Current password is incorrect';
$string['currentpassword_required'] = 'Current password is required';
$string['passwordsdiffer'] = 'Passwords do not match';
$string['password_change_optional'] = 'Leave blank to keep current password';
$string['password_updated'] = 'Password updated successfully';

// =============================================================================
// ADDITIONAL STRINGS - CONSENT & GDPR
// =============================================================================

$string['consentaccepttext'] = 'I accept the data treatment policy';
$string['consentheader'] = 'Data consent';
$string['consentrequired'] = 'You must accept the data treatment policy';
$string['datatreatmentpolicytitle'] = 'Data treatment policy';
$string['defaultdatatreatmentpolicy'] = 'Default data treatment policy';
$string['dataretentiondays'] = 'Data retention period (days)';
$string['dataexport:consent'] = 'Consent records';
$string['dataexport:exportdate'] = 'Export date';
$string['dataexport:personal'] = 'Personal data';
$string['dataexport:title'] = 'Data export';
$string['dataexport:userinfo'] = 'User information';
$string['datatorexport'] = 'Data to export';

// =============================================================================
// ADDITIONAL STRINGS - REPORTS
// =============================================================================

$string['reports_desc'] = 'View application and vacancy reports';
$string['viewreports'] = 'View reports';
$string['reportapplications'] = 'Applications report';
$string['reportdocuments'] = 'Documents report';
$string['reportoverview'] = 'Overview report';
$string['reportreviewers'] = 'Reviewers report';
$string['reporttimeline'] = 'Timeline report';
$string['generatedon'] = 'Generated on';
$string['selectionrate'] = 'Selection rate';

// =============================================================================
// ADDITIONAL STRINGS - IMPORT/EXPORT
// =============================================================================

$string['importvacancies'] = 'Import vacancies';
$string['importvacancies_desc'] = 'Import vacancies from CSV file';
$string['importvacancies_help'] = 'Upload a CSV file with vacancy data';
$string['importdata'] = 'Import data';
$string['importdata_desc'] = 'Import data from external sources';
$string['importupload'] = 'Upload file';
$string['importinstructions'] = 'Import instructions';
$string['importinstructionstext'] = 'Follow these instructions to import data correctly';
$string['importcomplete'] = 'Import complete';
$string['importerror'] = 'Import error';
$string['importerrors'] = 'Import errors';
$string['importwarning'] = 'Import warning';
$string['importingfrom'] = 'Importing from';
$string['importednote'] = 'Import note';
$string['importedapplications'] = 'Applications imported';
$string['importedconvocatorias'] = 'Convocatorias imported';
$string['importeddoctypes'] = 'Document types imported';
$string['importeddocuments'] = 'Documents imported';
$string['importedemails'] = 'Email templates imported';
$string['importedexemptions'] = 'Exemptions imported';
$string['importedfiles'] = 'Files imported';
$string['importedsettings'] = 'Settings imported';
$string['importedskipped'] = 'Skipped';
$string['importedsuccess'] = 'Successfully imported';
$string['importedvacancies'] = 'Vacancies imported';
$string['importerror_alreadyexempt'] = 'User already has exemption';
$string['importerror_createfailed'] = 'Failed to create record';
$string['importerror_usernotfound'] = 'User not found';
$string['importerror_vacancyexists'] = 'Vacancy already exists';
$string['exportdata_desc'] = 'Export application and vacancy data';
$string['exportdownload'] = 'Download export';
$string['exporterror'] = 'Export error';
$string['exportwarning_files'] = 'Warning: Files will not be included in export';
$string['fullexport'] = 'Full export';
$string['fullexport_info'] = 'Export all data including files';

// =============================================================================
// ADDITIONAL STRINGS - CSV IMPORT
// =============================================================================

$string['csvfile'] = 'CSV file';
$string['csvformat'] = 'CSV format';
$string['csvformat_desc'] = 'Expected format for CSV import';
$string['csvdelimiter'] = 'CSV delimiter';
$string['csvexample'] = 'CSV example';
$string['csvexample_desc'] = 'Example of expected CSV format';
$string['csvexample_tip'] = 'Download the example CSV file as a template';
$string['csvimporterror'] = 'CSV import error';
$string['csvinvalidtype'] = 'Invalid file type. Please upload a CSV file.';
$string['csvlineerror'] = 'Error on line {$a}';
$string['csvusernotfound'] = 'User not found in CSV line {$a}';
$string['csvcolumn_code'] = 'Code column';
$string['csvcolumn_contracttype'] = 'Contract type column';
$string['csvcolumn_courses'] = 'Courses column';
$string['csvcolumn_faculty'] = 'Faculty column';
$string['csvcolumn_location'] = 'Location column';
$string['csvcolumn_modality'] = 'Modality column';
$string['csvcolumn_profile'] = 'Profile column';
$string['csvcolumn_program'] = 'Program column';
$string['requiredcolumns'] = 'Required columns';
$string['optionalcolumns'] = 'Optional columns';
$string['samplecsv'] = 'Sample CSV';
$string['downloadcsvtemplate'] = 'Download CSV template';
$string['encoding'] = 'Encoding';
$string['dryrunmode'] = 'Dry run mode';
$string['dryrunresults'] = 'Dry run results';
$string['overwriteexisting'] = 'Overwrite existing';
$string['updateexisting'] = 'Update existing records';
$string['updateexisting_help'] = 'If enabled, existing vacancies with the same code will be updated with the imported data. If disabled, existing vacancies will be skipped.';
$string['vacancies_created'] = 'Vacancies created';
$string['vacancies_skipped'] = 'Vacancies skipped';
$string['vacancies_updated'] = 'Vacancies updated';

// =============================================================================
// ADDITIONAL STRINGS - MIGRATION
// =============================================================================

$string['migrateplugin'] = 'Migrate plugin';
$string['migrateplugin_desc'] = 'Migrate data from another system';
$string['migrationfile'] = 'Migration file';
$string['migrationinfo_desc'] = 'Migration information';
$string['migrationinfo_title'] = 'Migration';
$string['invalidmigrationfile'] = 'Invalid migration file';
$string['advancedtools'] = 'Advanced Tools';
$string['migration'] = 'Migration';
$string['migration_desc'] = 'Import and export data from external systems';

// =============================================================================
// ADDITIONAL STRINGS - SETTINGS & CONFIGURATION
// =============================================================================

$string['configuration'] = 'Configuration';
$string['configure'] = 'Configure';
$string['pluginsettings_desc'] = 'Configure plugin settings';
$string['navigationsettings'] = 'Navigation settings';
$string['navigationsettings_desc'] = 'Configure navigation options';
$string['mainmenutitle'] = 'Main menu title';
$string['mainmenutitle_desc'] = 'Title displayed in the main menu';
$string['showinmainmenu'] = 'Show in main menu';
$string['showinmainmenu_desc'] = 'Display link in the main navigation menu';
$string['showpublicnavlink'] = 'Show public navigation link';
$string['showpublicnavlink_desc'] = 'Show link to public vacancies page in navigation';
$string['publicpagesettings'] = 'Public page settings';
$string['publicpagesettings_desc'] = 'Configure public vacancy page';
$string['publicpagetitle'] = 'Public page title';
$string['publicpagetitle_desc'] = 'Title for the public vacancies page';
$string['publicpagedesc'] = 'Public page description';
$string['publicpagedescription'] = 'Public page description';
$string['publicpagedescription_desc'] = 'Description shown on the public vacancies page';
$string['enablepublicpage'] = 'Enable public page';
$string['enablepublicpage_desc'] = 'Allow public access to vacancy listings';
$string['enableselfregistration'] = 'Enable self-registration';
$string['enableselfregistration_desc'] = 'Allow users to register themselves';
$string['enableapi'] = 'Enable API';
$string['enableencryption'] = 'Enable encryption';
$string['securitysettings'] = 'Security settings';
$string['allowedformats'] = 'Allowed file formats';
$string['allowedformats_desc'] = 'File formats allowed for document uploads';
$string['allowmultipleapplications_convocatoria'] = 'Allow multiple applications per convocatoria';
$string['allowmultipleapplications_convocatoria_desc'] = 'Allow users to apply to multiple vacancies in the same convocatoria';
$string['recordsperpage'] = 'Records per page';

// =============================================================================
// ADDITIONAL STRINGS - RECAPTCHA ENTERPRISE
// =============================================================================

$string['recaptchasettings'] = 'reCAPTCHA settings';
$string['recaptchasettings_desc'] = 'Configure Google reCAPTCHA Enterprise or standard for forms';
$string['recaptcha_enabled'] = 'Enable reCAPTCHA';
$string['recaptcha_enabled_desc'] = 'Enable reCAPTCHA verification on forms';
$string['recaptcha_type'] = 'reCAPTCHA type';
$string['recaptcha_type_desc'] = 'Select Enterprise (recommended) or standard';
$string['recaptcha_type_enterprise'] = 'reCAPTCHA Enterprise';
$string['recaptcha_type_standard'] = 'reCAPTCHA Standard';
$string['recaptcha_sitekey'] = 'Site key';
$string['recaptcha_sitekey_desc'] = 'Google reCAPTCHA site key';
$string['recaptcha_project_id'] = 'Project ID';
$string['recaptcha_project_id_desc'] = 'Google Cloud project ID (Enterprise only)';
$string['recaptcha_api_key'] = 'API key';
$string['recaptcha_api_key_desc'] = 'Google Cloud API key (Enterprise only)';
$string['recaptcha_secretkey'] = 'Secret key';
$string['recaptcha_secretkey_desc'] = 'Google reCAPTCHA secret key (standard version only)';
$string['recaptcha_threshold'] = 'Score threshold';
$string['recaptcha_threshold_desc'] = 'Minimum score to pass verification (0.0 to 1.0). Higher values are stricter.';
$string['recaptcha_failed'] = 'reCAPTCHA verification failed';
$string['recaptcha_required'] = 'Please complete the reCAPTCHA verification';
$string['recaptcha_config_error'] = 'reCAPTCHA configuration error. Contact administrator.';
$string['recaptcha_network_error'] = 'Network error verifying reCAPTCHA. Please try again.';
$string['recaptcha_api_error'] = 'reCAPTCHA API error. Please try again.';
$string['recaptcha_invalid_response'] = 'Invalid response from reCAPTCHA server';
$string['recaptcha_invalid_token'] = 'Invalid or expired reCAPTCHA token';
$string['recaptcha_action_mismatch'] = 'reCAPTCHA action mismatch';
$string['recaptcha_score_too_low'] = 'reCAPTCHA score too low. Please try again.';

// =============================================================================
// ADDITIONAL STRINGS - IOMAD INTEGRATION
// =============================================================================

$string['iomadsettings'] = 'IOMAD settings';
$string['iomadoptions'] = 'IOMAD options';
$string['iomad_department'] = 'Department';
$string['multi_tenant'] = 'Multi-site';
$string['allcompanies'] = 'All sites';
$string['alldepartments'] = 'All departments';
$string['selectcompany'] = 'Select site';
$string['selectdepartment'] = 'Select department';
$string['selectconvocatoriafirst'] = 'Please select a convocatoria first';
$string['createcompanies'] = 'Create sites';
$string['createcompanies_help'] = 'If enabled, sites referenced in the import file that do not exist in IOMAD will be automatically created. If disabled, vacancies referencing non-existent sites will be skipped.';

// =============================================================================
// ADDITIONAL STRINGS - ROLES & CAPABILITIES
// =============================================================================

$string['manageroles'] = 'Manage roles';
$string['manageroles_desc'] = 'Configure role assignments';
$string['rolechanged'] = 'Role changed successfully';
$string['rolechangeerror'] = 'Error changing role';
$string['cap_assignreviewers'] = 'Assign reviewers';
$string['cap_createvacancy'] = 'Create vacancy';
$string['cap_download'] = 'Download';
$string['cap_evaluate'] = 'Evaluate';
$string['cap_manage'] = 'Manage';
$string['cap_review'] = 'Review';
$string['cap_validate'] = 'Validate';
$string['cap_viewevaluations'] = 'View evaluations';
$string['cap_viewreports'] = 'View reports';

// =============================================================================
// ADDITIONAL STRINGS - FEATURES & DESCRIPTIONS
// =============================================================================

$string['features'] = 'Features';
$string['feature_assign_reviewers'] = 'Assign reviewers to applications';
$string['feature_create_convocatorias'] = 'Create and manage convocatorias';
$string['feature_create_vacancies'] = 'Create new vacancy postings';
$string['feature_import_export'] = 'Import and export data';
$string['feature_manage_vacancies'] = 'Manage existing vacancies';
$string['feature_publish_vacancies'] = 'Publish vacancies to applicants';
$string['feature_review_documents'] = 'Review submitted documents';
$string['feature_track_applications'] = 'Track application status';
$string['feature_validate_applications'] = 'Validate application documents';

// =============================================================================
// ADDITIONAL STRINGS - TASKS & EVENTS
// =============================================================================

$string['task:checkclosingvacancies'] = 'Check for closing vacancies';
$string['task:cleanupolddata'] = 'Clean up old data';
$string['task:sendnotifications'] = 'Send pending notifications';
$string['event:applicationcreated'] = 'Application created';
$string['event:documentuploaded'] = 'Document uploaded';
$string['event:statuschanged'] = 'Status changed';
$string['event:vacancyclosed'] = 'Vacancy closed';
$string['event:vacancycreated'] = 'Vacancy created';
$string['event:vacancydeleted'] = 'Vacancy deleted';
$string['event:vacancypublished'] = 'Vacancy published';
$string['event:vacancyupdated'] = 'Vacancy updated';

// =============================================================================
// ADDITIONAL STRINGS - ERRORS & VALIDATION
// =============================================================================

$string['error:alreadyapplied'] = 'You have already applied to this vacancy';
$string['error:applicationlimitreached'] = 'Application limit reached';
$string['error:cannotdelete_hasapplications'] = 'Cannot delete: vacancy has applications';
$string['error:cannotdeleteconvocatoria'] = 'Cannot delete convocatoria';
$string['error:cannotreopenconvocatoria'] = 'Cannot reopen convocatoria';
$string['error:codealreadyexists'] = 'Code already exists';
$string['error:codeexists'] = 'This code is already in use';
$string['error:consentrequired'] = 'You must accept the data consent';
$string['error:convocatoriacodeexists'] = 'Convocatoria code already exists';
$string['error:convocatoriadatesinvalid'] = 'Invalid convocatoria dates';
$string['error:convocatoriahasnovacancies'] = 'Convocatoria has no vacancies';
$string['error:convocatoriarequired'] = 'Convocatoria is required';
$string['error:doctypeinuse'] = 'Document type is in use and cannot be deleted';
$string['error:invalidage'] = 'Invalid age';
$string['error:invalidcode'] = 'Invalid code format';
$string['error:invaliddates'] = 'Invalid dates';
$string['error:invalidpublicationtype'] = 'Invalid publication type';
$string['error:invalidstatus'] = 'Invalid status';
$string['error:invalidurl'] = 'Invalid URL format';
$string['error:invalidapplication'] = 'Invalid application';
$string['error:invaliduser'] = 'Invalid user';
$string['error:nodocuments'] = 'No documents found for this application';
$string['error:nodocumentstodownload'] = 'No documents available to download';
$string['error:cannotcreatezip'] = 'Cannot create ZIP file';
$string['error:occasionalrequiresexperience'] = 'Occasional contract requires experience';
$string['error:pastdate'] = 'Date cannot be in the past';
$string['error:requiredfield'] = 'This field is required';
$string['error:schedulingconflict'] = 'Scheduling conflict detected';
$string['error:singleapplicationonly'] = 'Only one application allowed';
$string['error:vacancyclosed'] = 'This vacancy is closed';
$string['error:vacancynotfound'] = 'Vacancy not found';
$string['error:publicpagedisabled'] = 'The public convocatorias page is not available at this time';
$string['error:publicpagedisabled_title'] = 'Page not available';
$string['error:publicpagedisabled_desc'] = 'Public access to convocatorias and vacancies has been temporarily disabled by the system administrator.';
$string['error:publicpagedisabled_action'] = 'If you are a registered user, please log in to access available convocatorias.';
$string['error:convocatorianotfound'] = 'Convocatoria not found';
$string['invalidemail'] = 'Invalid email address';
$string['emailexists'] = 'Email already exists';
$string['emailagain'] = 'Email (again)';
$string['emailnotmatch'] = 'Emails do not match';
$string['completerequiredfields'] = 'Please complete all required fields';
$string['signaturetoooshort'] = 'Signature is too short';
$string['maximumchars'] = 'Maximum {$a} characters';

// =============================================================================
// ADDITIONAL STRINGS - STATUS MESSAGES
// =============================================================================

$string['statuschanged'] = 'Status changed successfully';
$string['statuschangeerror'] = 'Error changing status';
$string['changessaved'] = 'Changes saved successfully';
$string['savechanges'] = 'Save changes';
$string['saveresults'] = 'Save results';
$string['inprogress'] = 'In progress';
$string['cancelledby'] = 'Cancelled by';
$string['defaultstatus'] = 'Default status';
$string['allstatuses'] = 'All statuses';
$string['allcontracttypes'] = 'All contract types';
$string['selectcontracttype'] = 'Select contract type';
$string['selectmodality'] = 'Select modality';
$string['selectatleastone'] = 'Please select at least one option';
$string['selectbackgrounddocs'] = 'Select background documents';
$string['selectidentitydocs'] = 'Select identity documents';
$string['selected'] = 'Selected';
$string['selectacountry'] = 'Select a country';
$string['confirm_reset'] = 'Confirm reset';

// =============================================================================
// ADDITIONAL STRINGS - DASHBOARD & WELCOME
// =============================================================================

$string['dashboard_admin_welcome'] = 'Welcome, Administrator';
$string['dashboard_applicant_welcome'] = 'Welcome to your applicant dashboard';
$string['dashboard_manager_welcome'] = 'Welcome to the management dashboard';
$string['dashboard_reviewer_welcome'] = 'Welcome to the reviewer dashboard';

// =============================================================================
// ADDITIONAL STRINGS - NOTIFICATIONS
// =============================================================================

$string['notification_'] = 'Notification';
$string['deadlinewarning'] = 'Deadline approaching';

// =============================================================================
// ADDITIONAL STRINGS - DATES & TIME
// =============================================================================

$string['dates'] = 'Dates';
$string['dateandtime'] = 'Date and time';
$string['datefrom'] = 'Date from';
$string['dateto'] = 'Date to';
$string['strftimedate'] = '%d %B %Y';
$string['strftimedateshort'] = '%d/%m/%Y';
$string['strftimedatetime'] = '%d %B %Y, %H:%M';
$string['duration'] = 'Duration';

// =============================================================================
// ADDITIONAL STRINGS - TABLES & LISTS
// =============================================================================

$string['row'] = 'Row';
$string['items'] = 'Items';
$string['show'] = 'Show';
$string['perpage'] = 'Per page';
$string['showingxofy'] = 'Showing {$a->from} to {$a->to} of {$a->total}';
$string['showingxtoy'] = 'Showing {$a->from} to {$a->to} of {$a->total}';
$string['andmore'] = 'and {$a} more';
$string['moveup'] = 'Move up';
$string['movedown'] = 'Move down';
$string['sortorder'] = 'Sort order';
$string['dragtoorder'] = 'Drag to reorder';
$string['reorder_success'] = 'Order saved successfully';

// =============================================================================
// ADDITIONAL STRINGS - CONVERSION & FILES
// =============================================================================

$string['conversionfailed'] = 'Conversion failed';
$string['conversioninprogress'] = 'Conversion in progress';
$string['conversionpending'] = 'Conversion pending';
$string['conversionready'] = 'Conversion ready';
$string['files'] = 'Files';
$string['digitalsignature'] = 'Digital signature';
$string['externalurl'] = 'External URL';
$string['locationorurl'] = 'Location or URL';
$string['courses'] = 'Courses';

// =============================================================================
// ADDITIONAL STRINGS - INPUT TYPES
// =============================================================================

$string['inputtype'] = 'Input type';
$string['inputtype_file'] = 'File upload';
$string['inputtype_number'] = 'Number';
$string['inputtype_text'] = 'Text';
$string['inputtype_url'] = 'URL';

// =============================================================================
// ADDITIONAL STRINGS - FORM ELEMENTS
// =============================================================================

$string['step'] = 'Step';
$string['conditions'] = 'Conditions';
$string['default'] = 'Default';
$string['desirable'] = 'Desirable';
$string['internal'] = 'Internal';
$string['publicationtype'] = 'Publication type';
$string['briefdescription'] = 'Brief description';
$string['contactemail'] = 'Contact email';
$string['institutionname'] = 'Institution name';
$string['subject'] = 'Subject';
$string['example'] = 'Example';
$string['noobservations'] = 'No observations';
$string['noreason'] = 'No reason provided';
$string['notes_desc'] = 'Additional notes';
$string['hasnote'] = 'Has note';

// =============================================================================
// ADDITIONAL STRINGS - TIPS & GUIDELINES
// =============================================================================

$string['guideline1'] = 'Complete all required fields';
$string['guideline2'] = 'Upload documents in PDF format';
$string['guideline3'] = 'Check all information before submitting';
$string['guideline4'] = 'Save your progress frequently';
$string['tip_checkdocs'] = 'Check that all documents are readable';
$string['tip_deadline'] = 'Submit before the deadline';
$string['tip_saveoften'] = 'Save your progress often';

// =============================================================================
// ADDITIONAL STRINGS - PREVIEW
// =============================================================================

$string['previewconfirm'] = 'Preview confirmation';
$string['previewmode'] = 'Preview mode';
$string['previewmodenotice'] = 'You are in preview mode. Changes will not be saved.';
$string['previewonly'] = 'Preview only';
$string['previewtotal'] = 'Preview total';
$string['previewunavailable'] = 'Preview unavailable';

// =============================================================================
// ADDITIONAL STRINGS - ENCRYPTION
// =============================================================================

$string['encryption:backupinstructions'] = 'Backup encryption key instructions';
$string['encryption:nokeytobackup'] = 'No encryption key to backup';

// =============================================================================
// ADDITIONAL STRINGS - INSTALLATION
// =============================================================================

$string['install_defaults'] = 'Install default settings';

// =============================================================================
// ADDITIONAL STRINGS - DOCUMENT STATUS PREFIXES (Dynamic)
// =============================================================================

$string['docstatus_'] = 'Document status';
$string['appstatus:'] = 'Application status';

// =============================================================================
// DOCUMENT CATEGORIES
// =============================================================================

$string['doccategory_identity'] = 'Identity documents';
$string['doccategory_academic'] = 'Academic documents';
$string['doccategory_professional'] = 'Professional documents';
$string['doccategory_background'] = 'Background check documents';
$string['doccategory_financial'] = 'Financial documents';
$string['doccategory_health'] = 'Health documents';

// =============================================================================
// DOCUMENT VALIDATION CHECKLIST
// =============================================================================

$string['checklist_legible'] = 'Document is legible';
$string['checklist_complete'] = 'Document is complete';
$string['checklist_namematch'] = 'Name matches application';
$string['checklist_cedula_number'] = 'ID number is visible';
$string['checklist_cedula_photo'] = 'Photo is visible';
$string['checklist_background_date'] = 'Issue date is recent';
$string['checklist_background_status'] = 'Clean background status';
$string['checklist_title_institution'] = 'Institution is recognized';
$string['checklist_title_date'] = 'Graduation date is verified';
$string['checklist_title_program'] = 'Program name matches';
$string['checklist_acta_number'] = 'Diploma number is present';
$string['checklist_acta_date'] = 'Diploma date is verified';
$string['checklist_tarjeta_number'] = 'Professional card number is present';
$string['checklist_tarjeta_profession'] = 'Profession matches position';
$string['checklist_rut_nit'] = 'Tax ID (NIT) is verified';
$string['checklist_rut_updated'] = 'Tax document is up to date';
$string['checklist_eps_active'] = 'Health insurance is active';
$string['checklist_eps_entity'] = 'Health insurance entity is verified';
$string['checklist_pension_fund'] = 'Pension fund is identified';
$string['checklist_pension_active'] = 'Pension contributions are active';
$string['checklist_medical_date'] = 'Medical exam date is recent';
$string['checklist_medical_aptitude'] = 'Aptitude status is favorable';
$string['checklist_military_class'] = 'Military class is verified';
$string['checklist_military_number'] = 'Military ID number is present';

// =============================================================================
// REPORTS - EMPTY STATE MESSAGES
// =============================================================================

$string['noapplicationsreport'] = 'No applications found for the selected criteria';
$string['novacanciesreport'] = 'No vacancies found for the selected criteria';
$string['nodocumentsreport'] = 'No documents found for the selected criteria';
$string['noconvocatoriasreport'] = 'No convocatorias found for the selected criteria';

// =============================================================================
// NAVIGATION - ADDITIONAL
// =============================================================================

$string['browsevacancies'] = 'Browse vacancies';
$string['browsevacancies_desc'] = 'Explore all available vacancies in our active calls';
$string['createconvocatoria'] = 'Create convocatoria';
$string['backtomanage'] = 'Back to management';
$string['reviewdashboard'] = 'Review dashboard';
$string['featuredvacancies'] = 'Featured vacancies';
$string['noconvocatoriasavailable'] = 'No convocatorias available';
$string['novacanciesavailable'] = 'No vacancies available';
$string['noapplicationsavailable'] = 'No applications available';

// =============================================================================
// USER ROLES
// =============================================================================

$string['role_reviewer'] = 'Reviewer';
$string['role_coordinator'] = 'Coordinator';
$string['role_administrator'] = 'Administrator';
$string['role_manager'] = 'Manager';
$string['role_applicant'] = 'Applicant';
$string['role_evaluator'] = 'Evaluator';
$string['role_secretary'] = 'Secretary';
$string['role_director'] = 'Director';

// =============================================================================
// REJECTION REASONS
// =============================================================================

$string['rejectreason_expired'] = 'Document has expired';
$string['rejectreason_illegible'] = 'Document is illegible';
$string['rejectreason_mismatch'] = 'Information does not match';
$string['rejectreason_incomplete'] = 'Document is incomplete';
$string['rejectreason_invalid'] = 'Document is invalid';
$string['rejectreason_wrong_format'] = 'Document is in wrong format';
$string['rejectreason_other'] = 'Other reason';

// =============================================================================
// WORKFLOW STEPS
// =============================================================================

$string['step_consent'] = 'Consent';
$string['step_documents'] = 'Documents';
$string['step_profile'] = 'Profile';
$string['step_coverletter'] = 'Cover letter';
$string['step_submit'] = 'Submit';
$string['step_review'] = 'Review';
$string['step_evaluation'] = 'Evaluation';
$string['step_interview'] = 'Interview';
$string['step_selection'] = 'Selection';

// =============================================================================
// DOCUMENT TYPES - SPECIFIC
// =============================================================================

$string['doctype_tarjeta_profesional'] = 'Professional card';
$string['doctype_sigep'] = 'SIGEP registration';
$string['doctype_antecedentes_policia'] = 'Police background check';
$string['doctype_antecedentes_fiscalia'] = 'Attorney general background check';
$string['doctype_antecedentes_contraloria'] = 'Comptroller background check';
$string['doctype_antecedentes_procuraduria'] = 'Inspector general background check';
$string['doctype_libreta_militar'] = 'Military service card';
$string['doctype_certificado_eps'] = 'Health insurance certificate';
$string['doctype_certificado_pension'] = 'Pension certificate';
$string['doctype_rut'] = 'Tax registration (RUT)';
$string['doctype_examen_medico'] = 'Medical examination';
$string['doctype_titulo'] = 'Degree certificate';
$string['doctype_acta_grado'] = 'Graduation diploma';

// =============================================================================
// PUBLICATION TYPES
// =============================================================================

$string['publicationtype:public'] = 'Public';
$string['publicationtype:internal'] = 'Internal';
$string['publicationtype:both'] = 'Public and internal';

// =============================================================================
// MISCELLANEOUS - ADDITIONAL
// =============================================================================

$string['user'] = 'User';
$string['useridentifier'] = 'User identifier';
$string['workflowsettings'] = 'Workflow settings';
$string['column'] = 'Column';
$string['doctype_isrequired_help'] = 'If checked, this document type will be mandatory for applications';

// =============================================================================
// SIGNUP FORM STRINGS
// =============================================================================

$string['signup_title'] = 'Create your account';
$string['signup_intro'] = 'Fill out the form below to create your account and start applying for positions.';
$string['signup_account_header'] = 'Account information';
$string['signup_personalinfo'] = 'Personal information';
$string['signup_contactinfo'] = 'Contact information';
$string['signup_academic_header'] = 'Academic information';
$string['signup_professional_profile'] = 'Professional profile';
$string['signup_termsheader'] = 'Terms and conditions';
$string['signup_companyinfo'] = 'Site information';
$string['signup_company_help'] = 'Select the site you belong to';
$string['signup_progress'] = 'Registration progress';
$string['signup_required_fields'] = 'Fields marked with * are required';
$string['signup_username_is_idnumber'] = 'Your username will be your ID number';
$string['signup_doctype'] = 'ID document type';
$string['signup_doctype_cc'] = 'Citizenship card (CC)';
$string['signup_doctype_ce'] = 'Foreign ID card (CE)';
$string['signup_doctype_passport'] = 'Passport';
$string['signup_doctype_pep'] = 'Special permit (PEP)';
$string['signup_doctype_ppt'] = 'Temporary protection permit (PPT)';
$string['signup_idnumber'] = 'ID number';
$string['signup_idnumber_exists'] = 'This ID number is already registered';
$string['signup_idnumber_exists_as_user'] = 'This ID number is already registered as a user';
$string['signup_idnumber_tooshort'] = 'ID number is too short';
$string['signup_birthdate'] = 'Date of birth';
$string['signup_birthdate_minage'] = 'You must be at least 18 years old';
$string['signup_gender'] = 'Gender';
$string['signup_gender_male'] = 'Male';
$string['signup_gender_female'] = 'Female';
$string['signup_gender_other'] = 'Other';
$string['signup_gender_prefer_not'] = 'Prefer not to say';
$string['signup_phone_mobile'] = 'Mobile phone';
$string['signup_phone_home'] = 'Home phone';
$string['signup_department_region'] = 'Department/Region';
$string['signup_education_level'] = 'Education level';
$string['signup_edu_highschool'] = 'High school';
$string['signup_edu_technical'] = 'Technical';
$string['signup_edu_tecnico'] = 'Technical degree';
$string['signup_edu_technological'] = 'Technological';
$string['signup_edu_tecnologo'] = 'Technologist degree';
$string['signup_edu_undergraduate'] = 'Undergraduate';
$string['signup_edu_profesional'] = 'Professional degree';
$string['signup_edu_specialization'] = 'Specialization';
$string['signup_edu_especialista'] = 'Specialist degree';
$string['signup_edu_masters'] = 'Master\'s degree';
$string['signup_edu_magister'] = 'Master\'s degree';
$string['signup_edu_doctorate'] = 'Doctorate';
$string['signup_edu_doctor'] = 'Doctoral degree';
$string['signup_edu_postdoctorate'] = 'Post-doctorate';
$string['signup_degree_title'] = 'Degree title';
$string['signup_expertise_area'] = 'Area of expertise';
$string['signup_experience_years'] = 'Years of experience';
$string['signup_exp_none'] = 'No experience';
$string['signup_exp_less_1'] = 'Less than 1 year';
$string['signup_exp_1_3'] = '1-3 years';
$string['signup_exp_3_5'] = '3-5 years';
$string['signup_exp_5_10'] = '5-10 years';
$string['signup_exp_more_10'] = 'More than 10 years';
$string['signup_terms_accept'] = 'I accept the terms and conditions';
$string['signup_terms_required'] = 'You must accept the terms and conditions';
$string['signup_datatreatment_accept'] = 'I accept the data treatment policy';
$string['signup_datatreatment_required'] = 'You must accept the data treatment policy';
$string['signup_dataaccuracy_accept'] = 'I certify that the information provided is accurate';
$string['signup_dataaccuracy_required'] = 'You must certify the accuracy of the information';
$string['signup_privacy_text'] = 'Your personal data will be processed according to our privacy policy';
$string['signup_createaccount'] = 'Create account';
$string['signup_already_account'] = 'Already have an account?';
$string['signup_applying_for'] = 'Applying for';
$string['signup_success_title'] = 'Registration successful';
$string['signup_success_message'] = 'Your account has been created successfully. Please check your email to confirm.';
$string['signup_error_creating'] = 'Error creating account';
$string['signup_check_spam'] = 'Check your spam folder if you don\'t receive the email';
$string['signup_email_instructions_title'] = 'Email verification';
$string['signup_email_instruction_1'] = 'Check your email inbox';
$string['signup_email_instruction_2'] = 'Click the verification link';
$string['signup_email_instruction_3'] = 'Complete your profile';
$string['signup_step_account'] = 'Account';
$string['signup_step_personal'] = 'Personal';
$string['signup_step_contact'] = 'Contact';
$string['signup_step_academic'] = 'Academic';
$string['signup_step_confirm'] = 'Confirm';

// =============================================================================
// DASHBOARD STRINGS
// =============================================================================

$string['admindashboard'] = 'Admin dashboard';
$string['managerdashboard'] = 'Manager dashboard';
$string['companydashboard'] = 'Site dashboard';
$string['applicantdashboarddesc'] = 'View your applications and track their progress';
$string['adminstatistics'] = 'Admin statistics';
$string['applicantstatistics'] = 'Applicant statistics';
$string['convocatoriastatistics'] = 'Convocatoria statistics';
$string['vacancystatistics'] = 'Vacancy statistics';
$string['reviewerperformance'] = 'Reviewer performance';
$string['applicationstats'] = 'Application statistics';
$string['documentstats'] = 'Document statistics';
$string['systemhealth'] = 'System health';
$string['systemconfiguration'] = 'System configuration';
$string['quicklinks'] = 'Quick links';
$string['quicktips'] = 'Quick tips';
$string['recentsnapshots'] = 'Recent snapshots';
$string['recentvacancies'] = 'Recent vacancies';
$string['activecommittees'] = 'Active committees';
$string['activeassignments'] = 'Active assignments';
$string['activeexemptions'] = 'Active exemptions';
$string['activereviewers'] = 'Active reviewers';
$string['totalcommittees'] = 'Total committees';
$string['totalcommmembers'] = 'Total committee members';
$string['totalexemptions'] = 'Total exemptions';
$string['totalusers'] = 'Total users';
$string['totalassigned'] = 'Total assigned';
$string['totalassignedusers'] = 'Total assigned users';
$string['avgtime'] = 'Average time';
$string['avgworkload'] = 'Average workload';
$string['currentworkload'] = 'Current workload';
$string['trend'] = 'Trend';
$string['trending_up'] = 'Trending up';
$string['trending_down'] = 'Trending down';

// =============================================================================
// EMAIL TEMPLATE PLACEHOLDERS
// =============================================================================

$string['availableplaceholders'] = 'Available placeholders';
$string['placeholder'] = 'Placeholder';
$string['ph_user_fullname'] = 'User full name';
$string['ph_user_firstname'] = 'User first name';
$string['ph_user_lastname'] = 'User last name';
$string['ph_user_email'] = 'User email';
$string['ph_applicant_name'] = 'Applicant name';
$string['ph_vacancy_code'] = 'Vacancy code';
$string['ph_vacancy_title'] = 'Vacancy title';
$string['ph_vacancy_description'] = 'Vacancy description';
$string['ph_vacancy_url'] = 'Vacancy URL';
$string['ph_application_id'] = 'Application ID';
$string['ph_application_url'] = 'Application URL';
$string['ph_submit_date'] = 'Submission date';
$string['ph_current_date'] = 'Current date';
$string['ph_deadline'] = 'Deadline';
$string['ph_days_remaining'] = 'Days remaining';
$string['ph_hours_until'] = 'Hours until';
$string['ph_close_date'] = 'Close date';
$string['ph_open_date'] = 'Open date';
$string['ph_company_name'] = 'Site name';
$string['ph_faculty_name'] = 'Faculty name';
$string['ph_contact_info'] = 'Contact info';
$string['ph_site_name'] = 'Site name';
$string['ph_site_url'] = 'Site URL';
$string['ph_interview_date'] = 'Interview date';
$string['ph_interview_time'] = 'Interview time';
$string['ph_interview_location'] = 'Interview location';
$string['ph_interview_type'] = 'Interview type';
$string['ph_interview_duration'] = 'Interview duration';
$string['ph_interview_notes'] = 'Interview notes';
$string['ph_interview_feedback'] = 'Interview feedback';
$string['ph_interviewer_name'] = 'Interviewer name';
$string['ph_reviewer_name'] = 'Reviewer name';
$string['ph_feedback'] = 'Feedback';
$string['ph_observations'] = 'Observations';
$string['ph_next_steps'] = 'Next steps';
$string['ph_action_required'] = 'Action required';
$string['ph_rejection_reason'] = 'Rejection reason';
$string['ph_rejected_docs'] = 'Rejected documents';
$string['ph_rejected_count'] = 'Rejected count';
$string['ph_approved_count'] = 'Approved count';
$string['ph_documents_count'] = 'Documents count';
$string['ph_review_summary'] = 'Review summary';
$string['ph_selection_notes'] = 'Selection notes';
$string['ph_notification_note'] = 'Notification note';
$string['ph_resubmit_deadline'] = 'Resubmit deadline';
$string['ph_waitlist_position'] = 'Waitlist position';

// =============================================================================
// UI AND NAVIGATION STRINGS
// =============================================================================

$string['navigation'] = 'Navigation';
$string['breadcrumb'] = 'Breadcrumb';
$string['pagination'] = 'Pagination';
$string['filterform'] = 'Filter form';
$string['filters'] = 'Filters';
$string['applyfilters'] = 'Apply filters';
$string['clearfilters'] = 'Clear filters';
$string['resetfilters'] = 'Reset filters';
$string['searchplaceholder'] = 'Search...';
$string['searchjobs'] = 'Search jobs';
$string['searchuser'] = 'Search user';
$string['searchusers'] = 'Search users';
$string['searchusersplaceholder'] = 'Search by name or email...';
$string['searchapplicant'] = 'Search applicant';
$string['searchbyusername'] = 'Search by username';
$string['searchagain'] = 'Search again';
$string['searchresultsfor'] = 'Search results for';
$string['showingresults'] = 'Showing results';
$string['noresultsforsearch'] = 'No results found for your search';
$string['trydifferentsearch'] = 'Try a different search term';
$string['trydifferentfilters'] = 'Try different filters';
$string['sortby:newest'] = 'Newest first';
$string['sortby:closedate'] = 'Close date';
$string['sortby:title'] = 'Title';
$string['sortby:positions'] = 'Positions';
$string['ascending'] = 'Ascending';
$string['descending'] = 'Descending';
$string['allcategories'] = 'All categories';
$string['alllocations'] = 'All locations';
$string['selectaction'] = 'Select action';
$string['selectuser'] = 'Select user';
$string['selectusers'] = 'Select users';
$string['selecttype'] = 'Select type';
$string['selectreason'] = 'Select reason';
$string['selectfaculty'] = 'Select faculty';
$string['selectroletoassign'] = 'Select role to assign';
$string['selectmultiplehelp'] = 'Hold Ctrl/Cmd to select multiple';

// =============================================================================
// APPLICATION WORKFLOW STRINGS
// =============================================================================

$string['applicationid'] = 'Application ID';
$string['applicationguidelines'] = 'Application guidelines';
$string['applicationsubmitteddesc'] = 'Your application has been submitted successfully';
$string['applicationsqueue'] = 'Applications queue';
$string['applicationsbystatus'] = 'Applications by status';
$string['applicationsbyvacancy'] = 'Applications by vacancy';
$string['applicationstopreview'] = 'Applications to preview';
$string['applied'] = 'Applied';
$string['applyfor'] = 'Apply for';
$string['applyto'] = 'Apply to';
$string['applynow_desc'] = 'Submit your application now';
$string['applynowdesc'] = 'Submit your application now';
$string['applyhelp_text'] = 'Follow the steps to complete your application';
$string['readytoapply'] = 'Ready to apply';
$string['wanttoapply'] = 'I want to apply';
$string['createaccounttoapply'] = 'Create an account to apply';
$string['logintoapply'] = 'Login to apply';
$string['loginandapply'] = 'Login and apply';
$string['submitted'] = 'Submitted';
$string['reviewing'] = 'Reviewing';
$string['assigned'] = 'Assigned';
$string['currentstatus'] = 'Current status';
$string['statushistory'] = 'Status history';
$string['updatestatus'] = 'Update status';
$string['changestatus'] = 'Change status';
$string['submitreview'] = 'Submit review';
$string['nextsteps'] = 'Next steps';
$string['nextapplication'] = 'Next application';
$string['previousapplication'] = 'Previous application';
$string['viewmyapplication'] = 'View my application';
$string['withdraw'] = 'Withdraw';
$string['resubmit'] = 'Resubmit';

// =============================================================================
// DOCUMENT VALIDATION STRINGS
// =============================================================================

$string['documentchecklist'] = 'Document checklist';
$string['documentinfo'] = 'Document information';
$string['documentpreview'] = 'Document preview';
$string['previewdocument'] = 'Preview document';
$string['documentactions'] = 'Document actions';
$string['documentstoreview'] = 'Documents to review';
$string['documentsremaining'] = 'Documents remaining';
$string['documenttypes'] = 'Document types';
$string['requireddocument'] = 'Required document';
$string['uploadfile'] = 'Upload file';
$string['uploadform'] = 'Upload form';
$string['uploaded'] = 'Uploaded';
$string['uploaddocsreminder'] = 'Remember to upload all required documents';
$string['downloadtoview'] = 'Download to view';
$string['validate'] = 'Validate';
$string['validateall'] = 'Validate all';
$string['validateindividual'] = 'Validate individual';
$string['validateddate'] = 'Validated date';
$string['validation'] = 'Validation';
$string['validationdecision'] = 'Validation decision';
$string['validationsummary'] = 'Validation summary';
$string['nochecklist'] = 'No checklist available';
$string['nodocumentspending'] = 'No documents pending';
$string['nodocumentstoreview'] = 'No documents to review';
$string['alldocsreviewed'] = 'All documents reviewed';
$string['allvalidated'] = 'All validated';
$string['approveselected'] = 'Approve selected';
$string['rejectselected'] = 'Reject selected';
$string['rejectreason'] = 'Rejection reason';
$string['rejectreason_placeholder'] = 'Enter reason for rejection...';
$string['rejectreason_wrongtype'] = 'Wrong document type';
$string['rejectreason_help'] = 'Indicate the reason why the document is rejected';
$string['unknowndoctype'] = 'Unknown document type';

// Document observations (for reviewer feedback per document)
$string['documentobservation'] = 'Document observations';
$string['documentobservation_placeholder'] = 'Enter observations about this document...';
$string['documentobservation_help'] = 'Observations will be compiled into an email for the applicant in case of corrections';
$string['required_for_rejection'] = 'Required for rejection';
$string['observation_required_for_rejection'] = 'Observation is required to reject the document';
$string['observation_required_title'] = 'Observation required';
$string['compiledocservations'] = 'Compile observations';
$string['sendobservationsemail'] = 'Send observations email';
$string['observationsemailsent'] = 'Observations email sent successfully';
$string['noobservationstosend'] = 'No observations to send';
$string['saving'] = 'Saving...';
$string['sending'] = 'Sending...';
$string['saveandsend'] = 'Save & Send';
$string['sent'] = 'Sent!';
$string['emailerror'] = 'Email error';
$string['downloadzip'] = 'Download ZIP';
$string['downloadallzip'] = 'Download all documents as ZIP';

// Sequential review strings
$string['sequentialreview'] = 'Sequential Review';
$string['reviewingdocument'] = 'Reviewing document';
$string['of'] = 'of';
$string['current'] = 'Current';
$string['locked'] = 'Locked';

// Application and consent strings
$string['consentgiven'] = 'Consent given';
$string['selectstatus'] = 'Select status';
$string['optionalnotes'] = 'Optional notes';
$string['optionalnotes_placeholder'] = 'Enter optional notes...';

// =============================================================================
// REVIEW AND COMMITTEE STRINGS
// =============================================================================

$string['reviewcomments'] = 'Review comments';
$string['reviewdecision'] = 'Review decision';
$string['reviewdocuments_desc'] = 'Review and validate applicant documents';
$string['reviewed'] = 'Reviewed';
$string['reviewedby'] = 'Reviewed by';
$string['reviewertasks'] = 'Reviewer tasks';
$string['reviewobservations'] = 'Review observations';
$string['reviewobservations_placeholder'] = 'Enter your observations...';
$string['reviewprogress'] = 'Review progress';
$string['reviewstatistics'] = 'Review statistics';
$string['myreviews_desc'] = 'View your assigned reviews';
$string['addreviewer'] = 'Add reviewer';
$string['addreviewerstoprogram'] = 'Add reviewers to program';
$string['currentreviewers'] = 'Current reviewers';
$string['assignedusers'] = 'Assigned users';
$string['assignnewusers'] = 'Assign new users';
$string['assignselected'] = 'Assign selected';
$string['assignto'] = 'Assign to';
$string['autoassignall'] = 'Auto-assign all';
$string['autoassignhelp'] = 'Automatically distribute applications among reviewers';
$string['manualassign'] = 'Manual assign';
$string['maxperreviewer'] = 'Max per reviewer';
$string['noreviewers'] = 'No reviewers';
$string['noreviewersavailable'] = 'No reviewers available';
$string['noreviewersforprogram'] = 'No reviewers for this program';
$string['programreviewerhelp'] = 'Assign reviewers to specific programs';
$string['programswithreviewers'] = 'Programs with reviewers';
$string['noprogramswithreviewers'] = 'No programs with reviewers';
$string['leadreviewers'] = 'Lead reviewers';
$string['committeename'] = 'Committee name';
$string['committeeautoroleassign'] = 'Auto-assign committee roles';
$string['members'] = 'Members';
$string['membercount'] = 'Member count';
$string['managemembers'] = 'Manage members';
$string['nomembers'] = 'No members';
$string['nocommitteeforthisvacancy'] = 'No committee for this vacancy';
$string['existingvacancycommittee'] = 'Existing vacancy committee';
$string['legacyvacancycommittee'] = 'Legacy vacancy committee';
$string['chairhelp'] = 'The chair leads the committee';
$string['evaluatorshelp'] = 'Evaluators assess candidates';
$string['confirmremovemember'] = 'Remove this member from the committee?';
$string['confirmremovereviewer'] = 'Remove this reviewer?';
$string['confirmunassign'] = 'Unassign this user?';

// =============================================================================
// INTERVIEW STRINGS
// =============================================================================

$string['scheduledinterviews'] = 'Scheduled interviews';
$string['schedulenewinterview'] = 'Schedule new interview';
$string['pendinginterviews'] = 'Pending interviews';
$string['noupcominginterviews'] = 'No upcoming interviews';
$string['virtualinterview'] = 'Virtual interview';
$string['confirmnoshow'] = 'Confirm no-show';
$string['noshow'] = 'No show';

// =============================================================================
// ROLE STRINGS
// =============================================================================

$string['role_chair'] = 'Chair';
$string['role_committee'] = 'Committee member';
$string['role_committee_desc'] = 'Member of evaluation committee';
$string['role_coordinator_desc'] = 'Coordinates the recruitment process';
$string['role_reviewer_desc'] = 'Reviews applications and documents';
$string['role_lead_reviewer'] = 'Lead reviewer';
$string['role_observer'] = 'Observer';
$string['changerole'] = 'Change role';
$string['rolenotcreated'] = 'Role not created';
$string['backtorolelist'] = 'Back to role list';

// =============================================================================
// EXEMPTION STRINGS
// =============================================================================

$string['exemptiontype_desc'] = 'Type of exemption';
$string['exemptionactive'] = 'Exemption active';
$string['exemptionlist'] = 'Exemption list';
$string['expiredexemptions'] = 'Expired exemptions';
$string['revokedexemptions'] = 'Revoked exemptions';
$string['grantedby'] = 'Granted by';
$string['expiry'] = 'Expiry';

// =============================================================================
// REPORT STRINGS
// =============================================================================

$string['report:applications'] = 'Applications report';
$string['report:vacancies'] = 'Vacancies report';
$string['report:documents'] = 'Documents report';
$string['report:convocatorias'] = 'Convocatorias report';
$string['reporttypes'] = 'Report types';
$string['reportsanddata'] = 'Reports and data';
$string['filterreport'] = 'Filter report';
$string['exportas'] = 'Export as';
$string['exportoptions'] = 'Export options';
$string['generatedby'] = 'Generated by';
$string['generated'] = 'Generated';

// =============================================================================
// VACANCY DISPLAY STRINGS
// =============================================================================

$string['vacanciesavailable'] = 'Vacancies available';
$string['vacanciesforconvocatoria'] = 'Vacancies for convocatoria';
$string['vacancysummary'] = 'Vacancy summary';
$string['convocatoriavacancycount'] = 'Vacancies in convocatoria';
$string['availablepositions'] = 'Available positions';
$string['daysleft'] = 'Days left';
$string['deadlineprogress'] = 'Deadline progress';
$string['deadlinewarning_title'] = 'Deadline warning';
$string['closes'] = 'Closes';
$string['starts'] = 'Starts';
$string['ends'] = 'Ends';
$string['viewallvacancies'] = 'View all vacancies';
$string['viewvacancies'] = 'View vacancies';
$string['viewvacancydetails'] = 'View vacancy details';
$string['backtovacancy'] = 'Back to vacancy';
$string['novacanciesfound'] = 'No vacancies found';
$string['novacanciesyet'] = 'No vacancies yet';
$string['createfirstvacancy'] = 'Create your first vacancy';
$string['suggestedvacancies'] = 'Suggested vacancies';
$string['nosuggestedvacancies'] = 'No suggested vacancies';
$string['featuredvacancies'] = 'Featured vacancies';
$string['nofeaturedvacancies'] = 'No featured vacancies';
$string['companyvacancies'] = 'Site vacancies';
$string['nocompanvacancies'] = 'No site vacancies';
$string['facultyvacancies'] = 'Faculty vacancies';
$string['facultieswithoutcommittee'] = 'Faculties without committee';

// =============================================================================
// CONVOCATORIA STRINGS
// =============================================================================

$string['noactiveconvocatorias'] = 'No active convocatorias';
$string['noconvocatorias_desc'] = 'No convocatorias found';
$string['noconvocatoriasdesc'] = 'No convocatorias found';

// =============================================================================
// PROGRESS AND STATS STRINGS
// =============================================================================

$string['progress'] = 'Progress';
$string['progressindicator'] = 'Progress indicator';
$string['complete'] = 'Complete';
$string['result'] = 'Result';
$string['results'] = 'Results';
$string['found'] = 'Found';
$string['rating'] = 'Rating';
$string['performance'] = 'Performance';
$string['performedby'] = 'Performed by';
$string['nostatsavailable'] = 'No statistics available';
$string['dailyapplications'] = 'Daily applications';
$string['pendingvalidation'] = 'Pending validation';
$string['pendingassignment'] = 'Pending assignment';
$string['pendingbytype'] = 'Pending by type';
$string['bydocumenttype'] = 'By document type';
$string['unassignedapplications'] = 'Unassigned applications';
$string['nounassignedapplications'] = 'No unassigned applications';

// =============================================================================
// USER MANAGEMENT STRINGS
// =============================================================================

$string['manageall'] = 'Manage all';
$string['manageusers'] = 'Manage users';
$string['usersassigned'] = 'Users assigned';
$string['usersassignedcount'] = 'Users assigned count';
$string['nousersassigned'] = 'No users assigned';
$string['nousersavailable'] = 'No users available';
$string['userunassigned'] = 'User unassigned';
$string['usernotfound'] = 'User not found';
$string['username_differs_idnumber'] = 'Username differs from ID number';
$string['username_matches_idnumber'] = 'Username matches your ID number';
$string['username_updated'] = 'Username updated';

// =============================================================================
// TABLE AND LIST STRINGS
// =============================================================================

$string['datatable'] = 'Data table';
$string['thcode'] = 'Code';
$string['thtitle'] = 'Title';
$string['thstatus'] = 'Status';
$string['thactions'] = 'Actions';
$string['statustabs'] = 'Status tabs';
$string['sortby'] = 'Sort by';

// =============================================================================
// TIMELINE AND HISTORY STRINGS
// =============================================================================

$string['timeline'] = 'Timeline';
$string['notimeline'] = 'No timeline available';
$string['nohistory'] = 'No history';
$string['noactivity'] = 'No activity';
$string['nocomments'] = 'No comments';
$string['addcomment'] = 'Add comment';
$string['additionalnotes'] = 'Additional notes';
$string['optionalcomment'] = 'Optional comment';
$string['optionalnotes'] = 'Optional notes';
$string['lastmodified'] = 'Last modified';
$string['timeago:justnow'] = 'just now';
$string['timeago:minutes'] = '{$a} minutes ago';
$string['timeago:hours'] = '{$a} hours ago';
$string['timeago:days'] = '{$a} days ago';

// =============================================================================
// SHARE STRINGS
// =============================================================================

$string['share'] = 'Share';
$string['sharepage'] = 'Share this page';
$string['sharebyemail'] = 'Share by email';
$string['shareonfacebook'] = 'Share on Facebook';
$string['shareontwitter'] = 'Share on Twitter';
$string['shareonlinkedin'] = 'Share on LinkedIn';
$string['shareonwhatsapp'] = 'Share on WhatsApp';

// =============================================================================
// JOB ALERTS STRINGS
// =============================================================================

$string['jobalerts'] = 'Job alerts';
$string['jobalertsdesc'] = 'Subscribe to receive alerts for new vacancies';
$string['subscribe'] = 'Subscribe';

// =============================================================================
// MISC UI STRINGS
// =============================================================================

$string['draft'] = 'Draft';
$string['private'] = 'Private';
$string['markprivate'] = 'Mark as private';
$string['entity'] = 'Entity';
$string['companies'] = 'Companies';
$string['departments'] = 'Departments';
$string['filename'] = 'File name';
$string['issuedate'] = 'Issue date';
$string['issuedatehelp'] = 'Date the document was issued';
$string['choosefiles'] = 'Choose files';
$string['clickfordetails'] = 'Click for details';
$string['opennewtab'] = 'Open in new tab';
$string['opensnewwindow'] = 'Opens in new window';
$string['togglepreview'] = 'Toggle preview';
$string['vieweronly_desc'] = 'View only, no editing allowed';
$string['viewpublicpage'] = 'View public page';
$string['viewpublicvacancies'] = 'View public vacancies';
$string['viewmyreviews'] = 'View my reviews';
$string['gotodashboard'] = 'Go to dashboard';
$string['needhelp'] = 'Need help?';
$string['interestedinposition'] = 'Interested in this position?';
$string['welcome'] = 'Welcome';
$string['welcometojobboard'] = 'Welcome to Job Board';
$string['landingdescription'] = 'Find your next opportunity';
$string['findyournextjob'] = 'Find your next job';
$string['browsealljobs'] = 'Browse all jobs';
$string['browsebycategory'] = 'Browse by category';
$string['administracion'] = 'Administration';
$string['iomadintegration'] = 'IOMAD integration';
$string['workflowconfiguration'] = 'Workflow configuration';
$string['workflowmanagement'] = 'Workflow management';
$string['workflowactions'] = 'Workflow actions';
$string['capabilities'] = 'Capabilities';
$string['bulkactions'] = 'Bulk actions';
$string['bulkoperations'] = 'Bulk operations';
$string['bulkcomment'] = 'Bulk comment';
$string['confirmcancel'] = 'Confirm cancel';
$string['confirmdelete'] = 'Confirm delete';
$string['conversionwait'] = 'Please wait while the document is being converted';
$string['nextpossible'] = 'Next possible';
$string['finalstage'] = 'Final stage';
$string['needsattention'] = 'Needs attention';
$string['norejections'] = 'No rejections';
$string['noassignments'] = 'No assignments';
$string['noassignments_desc'] = 'No assignments found';
$string['noauditlogs'] = 'No audit logs';
$string['nopendingapplications'] = 'No pending applications';
$string['nopendingdocuments'] = 'No pending documents';
$string['noapplicationsdesc'] = 'No applications found';
$string['noapplicationsyet'] = 'No applications yet';
$string['nosecretaryoptional'] = 'Secretary is optional';
$string['myrecentapplications'] = 'My recent applications';
$string['contract:prestacion_servicios'] = 'Service contract';
$string['contract:termino_fijo'] = 'Fixed-term contract';
$string['doctype_rnmc'] = 'National teacher registry (RNMC)';
$string['doctype_titulo_postgrado'] = 'Postgraduate degree';
$string['status:assigned'] = 'Assigned';

// =============================================================================
// GRADING PANEL STRINGS (mod_assign style interface)
// =============================================================================

$string['gradingpanel'] = 'Review panel';
$string['applicationlist'] = 'Application list';
$string['applicationnavigation'] = 'Application navigation';
$string['selectapplication'] = 'Select an application';
$string['selectdocument'] = 'Select a document';
$string['selectdocumenttopreview'] = 'Select a document to preview';
$string['selectdocumenthelp'] = 'Click on a document from the list on the left to preview it';
$string['previewnotavailable'] = 'Preview not available for this file type';
$string['filterbyname'] = 'Filter by name...';
$string['togglesidebar'] = 'Toggle sidebar';
$string['pendingdocs'] = 'Pending documents';
$string['exitgrading'] = 'Exit review panel';
$string['exit'] = 'Exit';
$string['fullscreen'] = 'Fullscreen';
$string['approveall'] = 'Approve all';
$string['confirmapproveall'] = 'Are you sure you want to approve all pending documents for this application?';
$string['previewunavailable'] = 'Preview not available for this file type';
$string['previewnotavailable'] = 'Preview not available';
$string['downloadtoview'] = 'Download to view';
$string['withdrawwarning'] = 'This action cannot be undone. You will need to re-apply if you want to be considered for this position.';
$string['selectrejectreason'] = 'Select rejection reason...';

// Keyboard shortcuts
$string['keyboardshortcuts'] = 'Keyboard shortcuts';
$string['shortcut_next'] = 'Next document';
$string['shortcut_prev'] = 'Previous document';
$string['shortcut_approve'] = 'Approve current document';
$string['shortcut_reject'] = 'Focus rejection reason';
$string['shortcut_download'] = 'Download document';
$string['shortcut_fullscreen'] = 'Toggle fullscreen';
$string['shortcut_sidebar'] = 'Toggle sidebar';
$string['shortcut_navigate_docs'] = 'Navigate documents';
$string['shortcut_approve_all'] = 'Approve all pending';
$string['shortcut_show_help'] = 'Show this help';
$string['shortcut_exit'] = 'Exit review panel';

// Validation feedback
$string['document_approved'] = 'Document approved successfully';
$string['document_rejected'] = 'Document rejected';
$string['validatedby'] = 'Validated by';
$string['validatedat'] = 'Validated at';

// =============================================================================
// USER TOURS - TOUR NAMES AND DESCRIPTIONS
// =============================================================================

$string['tour_endlabel'] = 'End tour';

// Dashboard Tour
$string['tour_dashboard_name'] = 'Job Board Dashboard Tour';
$string['tour_dashboard_desc'] = 'Learn how to navigate the Job Board dashboard';
$string['tour_dashboard_welcome_title'] = 'Welcome to Job Board';
$string['tour_dashboard_welcome_content'] = 'This is the main dashboard of the Job Board plugin. Here you can access all features based on your role.';
$string['tour_dashboard_stats_title'] = 'Statistics Overview';
$string['tour_dashboard_stats_content'] = 'These cards show key statistics like active convocatorias, vacancies, and applications.';
$string['tour_dashboard_admin_title'] = 'Administration Sections';
$string['tour_dashboard_admin_content'] = 'Each card provides access to different management areas. Click on any card to manage that section.';
$string['tour_dashboard_reviewer_title'] = 'Reviewer Tasks';
$string['tour_dashboard_reviewer_content'] = 'As a reviewer, you can see pending documents and your completed reviews here.';
$string['tour_dashboard_applicant_title'] = 'Applicant Section';
$string['tour_dashboard_applicant_content'] = 'View your applications and browse available vacancies from this section.';

// Public Page Tour
$string['tour_public_name'] = 'Public Page Tour';
$string['tour_public_desc'] = 'Discover how to browse public job opportunities';
$string['tour_public_welcome_title'] = 'Job Opportunities';
$string['tour_public_welcome_content'] = 'Welcome to the public job board. Here you can browse all available job opportunities.';
$string['tour_public_stats_title'] = 'Quick Statistics';
$string['tour_public_stats_content'] = 'These cards show the number of open convocatorias, vacancies, and positions available.';
$string['tour_public_convocatorias_title'] = 'Convocatoria Cards';
$string['tour_public_convocatorias_content'] = 'Each card represents a convocatoria (call for applications). Click to view details and available vacancies.';
$string['tour_public_vacancies_title'] = 'Vacancy Cards';
$string['tour_public_vacancies_content'] = 'Browse available positions. Each card shows key information about the vacancy.';
$string['tour_public_filters_title'] = 'Filter Options';
$string['tour_public_filters_content'] = 'Use these filters to narrow down your search by location, contract type, or keywords.';
$string['tour_public_apply_title'] = 'Apply Button';
$string['tour_public_apply_content'] = 'Click Apply to start your application. You\'ll need to log in or create an account first.';

// Convocatorias Tour
$string['tour_convocatorias_name'] = 'Convocatorias Management Tour';
$string['tour_convocatorias_desc'] = 'Learn how to manage convocatorias';
$string['tour_convocatorias_header_title'] = 'Convocatorias Management';
$string['tour_convocatorias_header_content'] = 'This page allows you to manage all convocatorias (calls for applications).';
$string['tour_convocatorias_create_title'] = 'Create New';
$string['tour_convocatorias_create_content'] = 'Click here to create a new convocatoria. You\'ll need to provide dates, description, and a PDF document.';
$string['tour_convocatorias_stats_title'] = 'Statistics';
$string['tour_convocatorias_stats_content'] = 'These cards show the current status of all convocatorias.';
$string['tour_convocatorias_filter_title'] = 'Filter Options';
$string['tour_convocatorias_filter_content'] = 'Filter convocatorias by status, date range, or other criteria.';
$string['tour_convocatorias_card_title'] = 'Convocatoria Card';
$string['tour_convocatorias_card_content'] = 'Each card shows the convocatoria details, including dates, vacancy count, and application statistics.';
$string['tour_convocatorias_actions_title'] = 'Quick Actions';
$string['tour_convocatorias_actions_content'] = 'Use these buttons to edit, view vacancies, or manage the convocatoria.';

// Convocatoria Manage Tour
$string['tour_convocatoria_manage_name'] = 'Convocatoria Form Tour';
$string['tour_convocatoria_manage_desc'] = 'Learn how to create and edit convocatorias';
$string['tour_convocatoria_manage_header_title'] = 'Convocatoria Form';
$string['tour_convocatoria_manage_header_content'] = 'Use this form to create or edit a convocatoria.';
$string['tour_convocatoria_manage_form_title'] = 'Form Fields';
$string['tour_convocatoria_manage_form_content'] = 'Fill in all required fields. The name, code, and description are essential.';
$string['tour_convocatoria_manage_dates_title'] = 'Date Settings';
$string['tour_convocatoria_manage_dates_content'] = 'Set the start and end dates for the convocatoria. Vacancies inherit these dates.';
$string['tour_convocatoria_manage_pdf_title'] = 'PDF Document';
$string['tour_convocatoria_manage_pdf_content'] = 'Upload the official PDF document with the complete convocatoria details. This is required.';
$string['tour_convocatoria_manage_submit_title'] = 'Save';
$string['tour_convocatoria_manage_submit_content'] = 'Click Save to create or update the convocatoria.';

// Vacancies Tour
$string['tour_vacancies_name'] = 'Vacancies List Tour';
$string['tour_vacancies_desc'] = 'Learn how to browse and manage vacancies';
$string['tour_vacancies_header_title'] = 'Vacancies';
$string['tour_vacancies_header_content'] = 'This page shows all vacancies for the selected convocatoria.';
$string['tour_vacancies_selector_title'] = 'Convocatoria Selector';
$string['tour_vacancies_selector_content'] = 'Select a convocatoria to view its vacancies.';
$string['tour_vacancies_card_title'] = 'Vacancy Card';
$string['tour_vacancies_card_content'] = 'Each card shows vacancy details including title, location, and number of positions.';
$string['tour_vacancies_status_title'] = 'Status Badge';
$string['tour_vacancies_status_content'] = 'The badge indicates the current status: draft, published, or closed.';
$string['tour_vacancies_actions_title'] = 'Actions';
$string['tour_vacancies_actions_content'] = 'Use these buttons to view details or apply to the vacancy.';

// Vacancy Detail Tour
$string['tour_vacancy_name'] = 'Vacancy Detail Tour';
$string['tour_vacancy_desc'] = 'Learn about vacancy details';
$string['tour_vacancy_header_title'] = 'Vacancy Details';
$string['tour_vacancy_header_content'] = 'This page shows complete information about the vacancy.';
$string['tour_vacancy_details_title'] = 'Description';
$string['tour_vacancy_details_content'] = 'Read the full description, responsibilities, and requirements for this position.';
$string['tour_vacancy_requirements_title'] = 'Requirements';
$string['tour_vacancy_requirements_content'] = 'Review the required qualifications and documents before applying.';
$string['tour_vacancy_apply_title'] = 'Apply Now';
$string['tour_vacancy_apply_content'] = 'Click Apply to start your application process.';

// Manage Tour
$string['tour_manage_name'] = 'Vacancy Management Tour';
$string['tour_manage_desc'] = 'Learn how to manage vacancies';
$string['tour_manage_header_title'] = 'Vacancy Management';
$string['tour_manage_header_content'] = 'Manage all vacancies from this page.';
$string['tour_manage_create_title'] = 'Create Vacancy';
$string['tour_manage_create_content'] = 'Click here to create a new vacancy within a convocatoria.';
$string['tour_manage_table_title'] = 'Vacancy Table';
$string['tour_manage_table_content'] = 'This table lists all vacancies with their details and status.';
$string['tour_manage_bulk_title'] = 'Bulk Selection';
$string['tour_manage_bulk_content'] = 'Select multiple vacancies to perform bulk actions.';
$string['tour_manage_actions_title'] = 'Row Actions';
$string['tour_manage_actions_content'] = 'Use these buttons to edit, view, or delete individual vacancies.';

// Apply Tour
$string['tour_apply_name'] = 'Application Process Tour';
$string['tour_apply_desc'] = 'Learn how to submit an application';
$string['tour_apply_header_title'] = 'Apply to Vacancy';
$string['tour_apply_header_content'] = 'Complete this form to submit your application.';
$string['tour_apply_progress_title'] = 'Progress Steps';
$string['tour_apply_progress_content'] = 'Follow these steps to complete your application. All steps must be completed.';
$string['tour_apply_guidelines_title'] = 'Guidelines';
$string['tour_apply_guidelines_content'] = 'Read these guidelines carefully before starting your application.';
$string['tour_apply_form_title'] = 'Application Form';
$string['tour_apply_form_content'] = 'Fill in all required fields and upload the necessary documents.';
$string['tour_apply_sidebar_title'] = 'Vacancy Summary';
$string['tour_apply_sidebar_content'] = 'This sidebar shows the vacancy details and deadline.';
$string['tour_apply_checklist_title'] = 'Document Checklist';
$string['tour_apply_checklist_content'] = 'Make sure you have all required documents ready before submitting.';
$string['tour_apply_submit_title'] = 'Submit Application';
$string['tour_apply_submit_content'] = 'Click Submit when you have completed all sections and uploaded all documents.';

// Application Detail Tour
$string['tour_application_name'] = 'Application Detail Tour';
$string['tour_application_desc'] = 'Learn about your application details';
$string['tour_application_header_title'] = 'Application Details';
$string['tour_application_header_content'] = 'View all details about your application here.';
$string['tour_application_status_title'] = 'Status';
$string['tour_application_status_content'] = 'This badge shows your current application status.';
$string['tour_application_timeline_title'] = 'Timeline';
$string['tour_application_timeline_content'] = 'Track the progress of your application through each stage.';
$string['tour_application_documents_title'] = 'Documents';
$string['tour_application_documents_content'] = 'View and manage your uploaded documents here.';
$string['tour_application_actions_title'] = 'Actions';
$string['tour_application_actions_content'] = 'Use these buttons to withdraw or modify your application (if allowed).';

// My Applications Tour
$string['tour_myapplications_name'] = 'My Applications Tour';
$string['tour_myapplications_desc'] = 'Learn how to track your applications';
$string['tour_myapplications_header_title'] = 'My Applications';
$string['tour_myapplications_header_content'] = 'View all your submitted applications here.';
$string['tour_myapplications_stats_title'] = 'Application Statistics';
$string['tour_myapplications_stats_content'] = 'These counters show how many applications you have in each status.';
$string['tour_myapplications_card_title'] = 'Application Card';
$string['tour_myapplications_card_content'] = 'Each card represents one application with its current status and details.';
$string['tour_myapplications_status_title'] = 'Status Badge';
$string['tour_myapplications_status_content'] = 'Colors indicate status: blue for submitted, yellow for under review, green for approved.';
$string['tour_myapplications_browse_title'] = 'Browse Vacancies';
$string['tour_myapplications_browse_content'] = 'Click here to browse more vacancies and apply to new positions.';

// Documents Tour
$string['tour_documents_name'] = 'Documents Management Tour';
$string['tour_documents_desc'] = 'Learn how to manage your documents';
$string['tour_documents_header_title'] = 'Document Management';
$string['tour_documents_header_content'] = 'Manage all your application documents from here.';
$string['tour_documents_list_title'] = 'Document List';
$string['tour_documents_list_content'] = 'Each row shows a document type with its current status.';
$string['tour_documents_status_title'] = 'Document Status';
$string['tour_documents_status_content'] = 'Green means approved, yellow means pending review, red means rejected.';
$string['tour_documents_upload_title'] = 'Upload Document';
$string['tour_documents_upload_content'] = 'Click to upload a new document or replace an existing one.';
$string['tour_documents_preview_title'] = 'Preview';
$string['tour_documents_preview_content'] = 'Click to preview your uploaded document without downloading.';

// Review Tour
$string['tour_review_name'] = 'Document Review Tour';
$string['tour_review_desc'] = 'Learn how to review applicant documents';
$string['tour_review_header_title'] = 'Document Review';
$string['tour_review_header_content'] = 'Review and validate applicant documents from this page.';
$string['tour_review_stats_title'] = 'Review Statistics';
$string['tour_review_stats_content'] = 'These cards show pending, approved, and rejected document counts.';
$string['tour_review_navigation_title'] = 'Navigation';
$string['tour_review_navigation_content'] = 'Navigate between applications using these buttons.';
$string['tour_review_documents_title'] = 'Document List';
$string['tour_review_documents_content'] = 'All documents for this application are listed here.';
$string['tour_review_actions_title'] = 'Quick Actions';
$string['tour_review_actions_content'] = 'Use these buttons to approve, reject, or preview each document.';
$string['tour_review_applicant_title'] = 'Applicant Info';
$string['tour_review_applicant_content'] = 'View applicant details and vacancy information in the sidebar.';
$string['tour_review_progress_title'] = 'Review Progress';
$string['tour_review_progress_content'] = 'This bar shows how many documents have been reviewed.';
$string['tour_review_submit_title'] = 'Complete Review';
$string['tour_review_submit_content'] = 'Click here when you have reviewed all documents to submit your review.';

// My Reviews Tour
$string['tour_myreviews_name'] = 'My Reviews Tour';
$string['tour_myreviews_desc'] = 'Learn how to track your review assignments';
$string['tour_myreviews_header_title'] = 'My Reviews';
$string['tour_myreviews_header_content'] = 'View all applications assigned to you for review.';
$string['tour_myreviews_stats_title'] = 'Review Statistics';
$string['tour_myreviews_stats_content'] = 'These cards show your pending and completed reviews.';
$string['tour_myreviews_filter_title'] = 'Filters';
$string['tour_myreviews_filter_content'] = 'Filter applications by status, convocatoria, or date.';
$string['tour_myreviews_queue_title'] = 'Review Queue';
$string['tour_myreviews_queue_content'] = 'Applications waiting for your review are listed here.';
$string['tour_myreviews_pending_title'] = 'Pending Count';
$string['tour_myreviews_pending_content'] = 'This badge shows how many documents need review in each application.';
$string['tour_myreviews_action_title'] = 'Start Review';
$string['tour_myreviews_action_content'] = 'Click to open the document review panel for this application.';

// Validate Document Tour
$string['tour_validate_document_name'] = 'Document Validation Tour';
$string['tour_validate_document_desc'] = 'Learn how to validate individual documents';
$string['tour_validate_header_title'] = 'Document Validation';
$string['tour_validate_header_content'] = 'Review and validate this document against the requirements.';
$string['tour_validate_preview_title'] = 'Document Preview';
$string['tour_validate_preview_content'] = 'The document is displayed here. You can zoom and scroll to review it.';
$string['tour_validate_checklist_title'] = 'Validation Checklist';
$string['tour_validate_checklist_content'] = 'Check each item to verify the document meets all requirements.';
$string['tour_validate_approve_title'] = 'Approve';
$string['tour_validate_approve_content'] = 'Click Approve if the document meets all requirements.';
$string['tour_validate_reject_title'] = 'Reject';
$string['tour_validate_reject_content'] = 'Click Reject if the document has issues and needs to be resubmitted.';
$string['tour_validate_reason_title'] = 'Rejection Reason';
$string['tour_validate_reason_content'] = 'If rejecting, provide a clear reason so the applicant can correct the document.';

// Reports Tour
$string['tour_reports_name'] = 'Reports Tour';
$string['tour_reports_desc'] = 'Learn how to use the reporting features';
$string['tour_reports_header_title'] = 'Reports';
$string['tour_reports_header_content'] = 'Generate and export reports from this page.';
$string['tour_reports_export_title'] = 'Export Options';
$string['tour_reports_export_content'] = 'Export reports in CSV, Excel, or PDF format.';
$string['tour_reports_tabs_title'] = 'Report Types';
$string['tour_reports_tabs_content'] = 'Choose from different report types: overview, applications, documents, or reviewers.';
$string['tour_reports_filter_title'] = 'Filters';
$string['tour_reports_filter_content'] = 'Filter reports by convocatoria, date range, or status.';
$string['tour_reports_stats_title'] = 'Summary Statistics';
$string['tour_reports_stats_content'] = 'These cards show key metrics for the selected filters.';
$string['tour_reports_table_title'] = 'Data Table';
$string['tour_reports_table_content'] = 'Detailed data is displayed in this table. Click headers to sort.';
$string['tour_reports_progress_title'] = 'Progress Bars';
$string['tour_reports_progress_content'] = 'Visual representation of percentages and progress.';

// Reports Convocatoria Filter (AGENTS.md 16.1)
$string['selectconvocatoria_required'] = 'Convocatoria Selection Required';
$string['selectconvocatoria_required_desc'] = 'Please select a convocatoria to view the reports. All reports must be filtered by convocatoria as per institutional policy.';
$string['currentconvocatoria'] = 'Current Convocatoria';
$string['changeconvocatoria'] = 'Change Convocatoria';
$string['noconvocatorias_forreports'] = 'No convocatorias available. You must create at least one convocatoria before viewing reports.';
$string['continue'] = 'Continue';

// =============================================================================
// ACCESSIBILITY STRINGS
// =============================================================================

$string['skiptocontent'] = 'Skip to content';
$string['skiptomain'] = 'Skip to main content';
$string['skiptonavigation'] = 'Skip to navigation';

// =============================================================================
// TEMPLATE PLACEHOLDERS
// =============================================================================

$string['placeholder_username'] = 'User\'s full name';
$string['placeholder_vacancytitle'] = 'Vacancy title';
$string['placeholder_sitename'] = 'Site name';
$string['placeholder_applicationid'] = 'Application ID';
$string['placeholder_convocatorianame'] = 'Convocatoria name';
$string['placeholder_deadline'] = 'Application deadline';

// =============================================================================
// UPLOAD & DOCUMENT STRINGS
// =============================================================================

$string['uploadtips'] = 'Upload Tips';
$string['uploadtip1'] = 'Ensure the document is clear and readable';
$string['uploadtip2'] = 'File size must not exceed the maximum allowed';
$string['uploadtip3'] = 'Supported formats: PDF, JPG, PNG';
$string['reuploadinstructions'] = 'Reupload Instructions';
$string['uploadnewversion'] = 'Upload New Version';
$string['documentrejected'] = 'Document Rejected';
$string['documentinfo'] = 'Document Information';
$string['documentname'] = 'Document Name';
$string['documenttype'] = 'Document Type';

// =============================================================================
// IMPORT/EXPORT STRINGS
// =============================================================================

$string['importwarningtitle'] = 'Important Warning';
$string['previewdata'] = 'Preview Data';
$string['nopreviewdata'] = 'No data available for preview';
$string['previewsummary'] = 'Preview Summary';
$string['recordstoprocess'] = 'Records to process';
$string['confirmimport'] = 'Confirm Import';
$string['exemptionsimported'] = 'Exemptions imported';
$string['exemptionsskipped'] = 'Exemptions skipped';
$string['datatorexport'] = 'Data to export';
$string['fullexport_info'] = 'All configuration data will be included';

// =============================================================================
// REVIEWER ASSIGNMENT STRINGS
// =============================================================================

$string['unassignedapplications'] = 'Unassigned applications';
$string['availablereviewers'] = 'Available reviewers';
$string['totalassigned'] = 'Total assigned';
$string['avgworkload'] = 'Avg. workload';
$string['reviewerworkload'] = 'Reviewer Workload';
$string['activeassignments'] = 'active assignments';
$string['reviewed'] = 'Reviewed';
$string['avgtime'] = 'Avg. time';
$string['autoassign'] = 'Auto-Assign';
$string['autoassignall'] = 'Auto-assign All';
$string['autoassignhelp'] = 'Automatically distributes unassigned applications evenly among available reviewers';
$string['maxperreviewer'] = 'Max per reviewer';
$string['manualassign'] = 'Manual Assignment';
$string['pendingassignment'] = 'pending assignment';
$string['assignto'] = 'Assign to';
$string['selectreviewer'] = 'Select a reviewer';
$string['assignselected'] = 'Assign Selected';
$string['alldone'] = 'All Done!';
$string['nounassignedapplications'] = 'There are no unassigned applications at this time';
$string['noreviewers'] = 'No reviewers available';
$string['allvacancies'] = 'All vacancies';
$string['bulkvalidation'] = 'Bulk Validation';
$string['reviewapplications'] = 'Review Applications';

// =============================================================================
// PROGRAM REVIEWERS STRINGS
// =============================================================================

$string['totalreviewers'] = 'Total Reviewers';
$string['activereviewers'] = 'Active Reviewers';
$string['leadreviewers'] = 'Lead Reviewers';
$string['programswithreviewers'] = 'Programs with Reviewers';
$string['noprogramswithreviewers'] = 'No programs have reviewers assigned yet';
$string['addreviewerstoprogram'] = 'Add Reviewers to Program';
$string['addreviewer'] = 'Add Reviewer';
$string['assignedreviewers'] = 'Assigned Reviewers';
$string['noreviewersforprogram'] = 'No reviewers assigned to this program';
$string['selectuser'] = 'Select user';
$string['role_reviewer'] = 'Reviewer';
$string['role_lead_reviewer'] = 'Lead Reviewer';
$string['nousersavailable'] = 'No users available for assignment';
$string['changerole'] = 'Change role';
$string['confirmremovereviewer'] = 'Are you sure you want to remove this reviewer?';
$string['programreviewerhelp'] = 'Reviewers assigned to a program can review applications for all vacancies within that program category.';

// =============================================================================
// VACANCY SELECTION STRINGS
// =============================================================================

$string['selectconvocatoriafirst'] = 'Select a Convocatoria First';
$string['createvacancyinconvocatoriadesc'] = 'To create a new vacancy, you must first select which convocatoria it belongs to.';
$string['noconvocatoriasavailable'] = 'No convocatorias available';
$string['gotocreateconvocatoria'] = 'Create a Convocatoria';
$string['selectconvocatoria'] = 'Select Convocatoria';
$string['addvacancy'] = 'Add Vacancy';
$string['addconvocatoria'] = 'Create New Convocatoria';
$string['or'] = 'or';

// =============================================================================
// MIGRATION STRINGS
// =============================================================================

$string['dryrunresults'] = 'Dry Run Results';

// =============================================================================
// STATUS STRINGS
// =============================================================================

$string['inactive'] = 'Inactive';
$string['active'] = 'Active';
$string['assigned'] = 'Assigned';

// =============================================================================
// MISC UI STRINGS
// =============================================================================

$string['pagination'] = 'Pagination';
$string['browservacancies'] = 'Browse Vacancies';
$string['novacancies'] = 'No vacancies';
$string['viewvacancies'] = 'View Vacancies';
$string['viewconvocatoria'] = 'View Convocatoria';
$string['noconvocatorias'] = 'No convocatorias found';
$string['noconvocatorias_desc'] = 'There are no convocatorias available at this time. Please check back later.';
$string['convocatoria_status_open'] = 'Open';
$string['convocatoria_status_closed'] = 'Closed';
$string['daysremaining'] = 'days remaining';

// =============================================================================
// DASHBOARD STRINGS
// =============================================================================

$string['myreviews_desc'] = 'Review applications assigned to you';
$string['pending_reviews_alert'] = 'You have {$a} applications pending review';
$string['attentionrequired'] = 'Attention Required';
$string['nopendingreview'] = 'No pending reviews. Great job!';
$string['viewmyreviews'] = 'View My Reviews';
$string['completedreviews'] = 'Completed Reviews';
$string['critical'] = 'Critical';
$string['closingon'] = 'Closing on';
$string['viewpublicpage'] = 'View Public Page';
$string['managevacancies'] = 'Manage Vacancies';
$string['notifications'] = 'Notifications';
$string['mystats'] = 'My Statistics';
$string['reviewertasks'] = 'Reviewer Tasks';
$string['recentactivity'] = 'Recent Activity';
$string['statistics'] = 'Statistics';

// Error messages
$string['error:usernotfound'] = 'User not found';
$string['unknownuser'] = 'Unknown user';

// =============================================================================
// UI FILTER AND NAVIGATION STRINGS
// =============================================================================

$string['openconvocatorias'] = 'Open Convocatorias';
$string['convocatoriaactions'] = 'Convocatoria Actions';
$string['filterconvocatorias'] = 'Filter Convocatorias';
$string['pagenavigation'] = 'Page Navigation';
$string['filterapplications'] = 'Filter Applications';
$string['allcaughtup'] = 'All caught up!';
$string['filterdocuments'] = 'Filter Documents';
$string['doctypeactions'] = 'Document Type Actions';
$string['exemptionactions'] = 'Exemption Actions';
$string['filterexemptions'] = 'Filter Exemptions';
$string['filtervacancies'] = 'Filter Vacancies';
$string['noexemptionsdesc'] = 'No exemptions have been created yet.';

// =============================================================================
// MISSING STRINGS - SHARING AND SOCIAL
// =============================================================================

$string['available'] = 'Available';
$string['shareon'] = 'Share on {$a}';
$string['shareconvocatoria'] = 'Share this convocatoria';

// =============================================================================
// MISSING STRINGS - SIGNUP/UPDATE PROFILE FORM
// =============================================================================

$string['signup_email'] = 'Email address';
$string['signup_email_help'] = 'Enter a valid email address. This will be used for communications regarding your applications and account recovery.';
$string['currentpassword_help'] = 'Enter your current password to confirm any changes to your account settings.';
$string['signup_password'] = 'Password';
$string['signup_password_help'] = 'Create a strong password with at least 8 characters, including uppercase, lowercase letters and numbers.';
$string['signup_doctype_help'] = 'Select the type of identification document you are using.';
$string['signup_birthdate_help'] = 'Enter your date of birth. This information is required for some verification processes.';
$string['signup_phone'] = 'Phone number';
$string['signup_phone_help'] = 'Enter your phone number including country code. This will be used for urgent communications.';
$string['signup_education_level_help'] = 'Select your highest completed education level.';
$string['signup_degree_title_help'] = 'Enter the official title of your highest degree or certification.';
$string['signup_institution'] = 'Educational institution';
$string['signup_institution_help'] = 'Enter the name of the institution where you obtained your highest qualification.';
$string['signup_expertise_area_help'] = 'Describe your main areas of professional expertise and specialization.';
$string['signup_professional_profile_help'] = 'Provide a brief professional summary highlighting your experience and key skills.';

// =============================================================================
// MISSING STRINGS - APPLICATION FORM
// =============================================================================

$string['skiptoform'] = 'Skip to application form';
$string['indicatesrequired'] = 'indicates required field';
$string['pending'] = 'Pending';
$string['uploaded'] = 'Uploaded';
$string['alldocumentsuploaded'] = 'All documents have been uploaded!';
$string['textcontent'] = 'Text content';
$string['rejectreason_prompt'] = 'Enter the rejection reason:';
$string['rejectreason_required'] = 'You must enter a reason to reject the document.';
$string['consentaccepted'] = 'Data processing consent';
$string['consentaccepted_help'] = 'By checking this box, you consent to the processing of your personal data for recruitment purposes in accordance with data protection regulations.';
$string['digitalsignature_help'] = 'Type your full name exactly as it appears on your official documents. This serves as your electronic signature.';
$string['coverletter_help'] = 'Write a brief cover letter explaining your interest in the position and why you are a suitable candidate.';
$string['declarationaccepted'] = 'Declaration of truthfulness';
$string['declarationaccepted_help'] = 'By checking this box, you declare that all information provided in this application is true and accurate to the best of your knowledge.';

// =============================================================================
// MISSING STRINGS - DOCUMENT CATEGORIES
// =============================================================================

$string['doccat_employment_desc'] = 'Employment certificates and work-related documents';
$string['doccat_identification_desc'] = 'Official identification documents and personal certificates';
$string['doccat_academic_desc'] = 'Academic credentials, degrees and educational certificates';
$string['doccat_financial_desc'] = 'Financial documents and bank certificates';
$string['doccat_health_desc'] = 'Health certificates and medical documentation';
$string['doccat_legal_desc'] = 'Legal documents, background checks and judicial certificates';

// =============================================================================
// MISSING STRINGS - MULTIPLE DOCUMENTS
// =============================================================================

$string['multipledocs_certificacion_laboral'] = 'You can upload multiple employment certificates. Each certificate should be from a different employer or position.';
$string['multipledocs_titulo_academico'] = 'You can upload multiple academic titles. Include all relevant degrees and certifications.';
$string['multipledocs_formacion_complementaria'] = 'You can upload multiple complementary training certificates. Include all relevant courses and workshops.';

// =============================================================================
// CHECKLIST STRINGS
// =============================================================================

$string['allcomplete'] = 'All complete!';

// =============================================================================
// APPLICATION FORM STRINGS (Rebuilt)
// =============================================================================

$string['applicationform'] = 'Application Form';
$string['applicationsubmitted'] = 'Your application has been submitted successfully.';
$string['applicationsubmitfailed'] = 'Failed to submit application. Please try again.';
$string['applicationcreatefailed'] = 'Failed to create application record.';
$string['declarationtitle'] = 'Declaration of Accuracy';
$string['obtaindocument'] = 'Obtain this document';
$string['missingvacancy'] = 'Vacancy information is missing.';

// =============================================================================
// PROFILE PAGE STRINGS
// =============================================================================

$string['profilerequired_title'] = 'Profile completion required';
$string['profilerequired_desc'] = 'Please complete all required fields in your profile before proceeding with your application.';
$string['profilechecklist'] = 'Profile checklist';
$string['profile_field_names'] = 'First and last name';
$string['profile_field_email'] = 'Email address';
$string['profile_field_phone'] = 'Phone number';
$string['profile_field_idnumber'] = 'ID number';
$string['profile_required_note'] = 'Fields marked with yellow need to be completed.';
$string['profile_help_text'] = 'If you need assistance completing your profile, please contact support.';
$string['completeprofiletoapply'] = 'Complete your profile to continue with your application.';

// =============================================================================
// REVIEW PAGE STRINGS
// =============================================================================

$string['reviewqueue_desc'] = 'Review and validate applicant documents submitted for vacancies.';
$string['reviewapplication'] = 'Review application';
$string['selectdocumenttopreview'] = 'Select a document from the list to preview it here.';
$string['sequentialreview'] = 'Sequential review';
$string['reviewingdocument'] = 'Reviewing document';
$string['readytosubmit'] = 'All documents reviewed. You can submit your review now.';
$string['alldocsreviewed'] = 'All documents reviewed';
$string['submitreview'] = 'Submit review';
$string['reviewobservations_placeholder'] = 'Add any final observations about this application...';
$string['fullscreen'] = 'Fullscreen';

// =============================================================================
// MANAGE APPLICATIONS PAGE STRINGS
// =============================================================================

$string['searchapplicant'] = 'Search by name or email...';
$string['allstatuses'] = 'All statuses';
$string['applicantslist'] = 'Applicants list';
$string['noapplicationsfound_desc'] = 'No applications match your current filters. Try adjusting your search criteria.';
$string['exportdesc'] = 'Download the applications data in your preferred format for offline analysis.';

// =============================================================================
// FILTER AND UX STRINGS
// =============================================================================

$string['filterbyvacancy'] = 'Filter by vacancy';
$string['selectvacancytofilter'] = 'Select a vacancy to filter applications';
$string['applyfilters'] = 'Apply filters';
$string['totaldocuments'] = 'Total documents';
$string['allreviewed'] = 'All reviewed';
$string['urgent'] = 'Urgent';

// =============================================================================
// DOCUMENT TYPES - v3.6.38
// =============================================================================

$string['doctype_experiencia_docente'] = 'Teaching Experience';
$string['doctype_experiencia_profesional'] = 'Professional Experience';
$string['doctype_formacion_pedagogia'] = 'Pedagogy Training';
$string['doctype_formacion_tic'] = 'ICT Training';
$string['doctype_formacion_complementaria'] = 'Complementary Certifications';
$string['doctype_titulo_academico'] = 'Undergraduate and Graduate Degrees';
$string['doctype_bienes_rentas'] = 'Assets and Income Declaration Form';
$string['doctype_antecedentes_judiciales'] = 'Criminal Record';
$string['doctype_medidas_correctivas'] = 'National Corrective Measures Registry';
$string['doctype_inhabilidades'] = 'Disqualifications Check';
$string['doctype_redam'] = 'REDAM (Child Support Debtors Registry)';

// =============================================================================
// VACANCY FILTERS - v3.6.38
// =============================================================================

$string['filterbycode'] = 'Search by code...';
$string['filtering'] = 'Filtering...';
$string['modality'] = 'Modality';
$string['modality_presencial'] = 'On-site';
$string['modality_virtual'] = 'Virtual';
$string['modality_hibrida'] = 'Hybrid';
$string['modality_distancia'] = 'Distance';
$string['department'] = 'Academic Program';

// =============================================================================
// USER TOURS - GENERAL
// =============================================================================

$string['tour_endlabel'] = 'End tour';

// =============================================================================
// USER TOUR: DASHBOARD
// =============================================================================

$string['tour_dashboard_name'] = 'Job Board Dashboard Tour';
$string['tour_dashboard_desc'] = 'Learn how to navigate the Job Board dashboard and discover its main features.';
$string['tour_dashboard_welcome_title'] = 'Welcome to the Job Board';
$string['tour_dashboard_welcome_content'] = 'This is your main dashboard where you can access all Job Board features based on your role. Here you\'ll find quick actions and important information at a glance.';
$string['tour_dashboard_stats_title'] = 'Statistics Overview';
$string['tour_dashboard_stats_content'] = 'These cards show key statistics about vacancies, applications, and the review process. Monitor the status of your recruitment activities in real-time.';
$string['tour_dashboard_admin_title'] = 'Administration Sections';
$string['tour_dashboard_admin_content'] = 'As an administrator, you can manage convocatorias, vacancies, applications, and system configuration from these sections. Each card provides quick access to specific management areas.';
$string['tour_dashboard_reviewer_title'] = 'Reviewer Tasks';
$string['tour_dashboard_reviewer_content'] = 'If you are a document reviewer, this section shows your pending reviews and completed work. Click "View My Reviews" to access applications assigned to you.';
$string['tour_dashboard_applicant_title'] = 'My Applications';
$string['tour_dashboard_applicant_content'] = 'As an applicant, you can view your submitted applications, track their status, and browse available vacancies from this section.';

// =============================================================================
// USER TOUR: PUBLIC PAGE
// =============================================================================

$string['tour_public_name'] = 'Public Job Board Tour';
$string['tour_public_desc'] = 'Discover how to browse and apply for available vacancies on the public job board.';
$string['tour_public_welcome_title'] = 'Welcome to the Job Board';
$string['tour_public_welcome_content'] = 'This is the public page where all available job opportunities are listed. You can browse vacancies without logging in, but you\'ll need to register or sign in to apply.';
$string['tour_public_stats_title'] = 'Available Opportunities';
$string['tour_public_stats_content'] = 'These statistics show the current number of open convocatorias and available vacancies. Keep an eye on these numbers for new opportunities.';
$string['tour_public_convocatorias_title'] = 'Active Convocatorias';
$string['tour_public_convocatorias_content'] = 'Convocatorias are job calls that group related vacancies. Click on a convocatoria card to see all its associated positions and requirements.';
$string['tour_public_vacancies_title'] = 'Job Vacancies';
$string['tour_public_vacancies_content'] = 'Each vacancy card shows the position title, location, modality, and key requirements. Click on a card to view full details and application instructions.';
$string['tour_public_filters_title'] = 'Filter Options';
$string['tour_public_filters_content'] = 'Use these filters to narrow down vacancies by convocatoria, modality, location, or keyword. This helps you find positions that match your profile.';
$string['tour_public_apply_title'] = 'Apply Now';
$string['tour_public_apply_content'] = 'When you find a vacancy that interests you, click the "Apply" or "View Details" button. You\'ll need to create an account or log in to submit your application.';

// =============================================================================
// USER TOUR: APPLY FOR VACANCY
// =============================================================================

$string['tour_apply_name'] = 'Application Process Tour';
$string['tour_apply_desc'] = 'Learn how to complete and submit your job application step by step.';
$string['tour_apply_header_title'] = 'Application Form';
$string['tour_apply_header_content'] = 'This is the application form for the selected vacancy. Follow the steps carefully to ensure your application is complete and accurate.';
$string['tour_apply_progress_title'] = 'Progress Steps';
$string['tour_apply_progress_content'] = 'These steps show your progress through the application process. Complete each section before moving to the next one.';
$string['tour_apply_guidelines_title'] = 'Application Guidelines';
$string['tour_apply_guidelines_content'] = 'Read these guidelines carefully before starting your application. They contain important information about required documents and deadlines.';
$string['tour_apply_form_title'] = 'Document Upload';
$string['tour_apply_form_content'] = 'Upload all required documents here. Make sure each file meets the format and size requirements. Required documents are marked with an asterisk (*).';
$string['tour_apply_sidebar_title'] = 'Vacancy Information';
$string['tour_apply_sidebar_content'] = 'This sidebar shows details about the vacancy you\'re applying for, including position requirements and important dates.';
$string['tour_apply_checklist_title'] = 'Document Checklist';
$string['tour_apply_checklist_content'] = 'Track your document submission progress with this checklist. Green checkmarks indicate uploaded documents, while empty circles show pending items.';
$string['tour_apply_submit_title'] = 'Submit Application';
$string['tour_apply_submit_content'] = 'Once you\'ve uploaded all required documents and reviewed your information, click this button to submit your application. You cannot modify it after submission.';

// =============================================================================
// USER TOUR: VACANCIES LIST
// =============================================================================

$string['tour_vacancies_name'] = 'Vacancies Management Tour';
$string['tour_vacancies_desc'] = 'Learn how to browse and manage job vacancies in the system.';
$string['tour_vacancies_header_title'] = 'Vacancies Page';
$string['tour_vacancies_header_content'] = 'This page displays all vacancies in the system. As an administrator, you can create, edit, and manage vacancy statuses from here.';
$string['tour_vacancies_selector_title'] = 'Filter by Convocatoria';
$string['tour_vacancies_selector_content'] = 'Use this dropdown to filter vacancies by their parent convocatoria. This helps you manage vacancies for specific job calls.';
$string['tour_vacancies_card_title'] = 'Vacancy Cards';
$string['tour_vacancies_card_content'] = 'Each card represents a vacancy with its code, title, and key information. Cards are color-coded by status for quick identification.';
$string['tour_vacancies_status_title'] = 'Status Badges';
$string['tour_vacancies_status_content'] = 'The badge shows the current status of each vacancy: Draft (not published), Published (accepting applications), Closed (no longer accepting), or Assigned (position filled).';
$string['tour_vacancies_actions_title'] = 'Quick Actions';
$string['tour_vacancies_actions_content'] = 'Use these buttons to view, edit, publish, or close a vacancy. Available actions depend on the vacancy\'s current status and your permissions.';

// =============================================================================
// USER TOUR: CONVOCATORIAS
// =============================================================================

$string['tour_convocatorias_name'] = 'Convocatorias Management Tour';
$string['tour_convocatorias_desc'] = 'Learn how to create and manage job call convocatorias.';
$string['tour_convocatorias_header_title'] = 'Convocatorias Page';
$string['tour_convocatorias_header_content'] = 'This page shows all convocatorias (job calls) in the system. Convocatorias group related vacancies under a single application period.';
$string['tour_convocatorias_create_title'] = 'Create New Convocatoria';
$string['tour_convocatorias_create_content'] = 'Click this button to create a new convocatoria. You\'ll define the application period, terms, and can then add vacancies to it.';
$string['tour_convocatorias_stats_title'] = 'Statistics';
$string['tour_convocatorias_stats_content'] = 'These cards show the count of convocatorias by status: Open (accepting applications), Closed (ended), and Draft (not yet published).';
$string['tour_convocatorias_filter_title'] = 'Filter Options';
$string['tour_convocatorias_filter_content'] = 'Use these filters to find specific convocatorias by status, date range, or keyword search.';
$string['tour_convocatorias_card_title'] = 'Convocatoria Cards';
$string['tour_convocatorias_card_content'] = 'Each card displays a convocatoria with its code, name, dates, and vacancy count. Click on a card to view its details and associated vacancies.';
$string['tour_convocatorias_actions_title'] = 'Actions Menu';
$string['tour_convocatorias_actions_content'] = 'Use these buttons to view details, edit, open, close, or delete a convocatoria. Available actions depend on the current status.';

// =============================================================================
// USER TOUR: DOCUMENT REVIEW
// =============================================================================

$string['tour_review_name'] = 'Document Review Tour';
$string['tour_review_desc'] = 'Learn how to review and validate applicant documents effectively.';
$string['tour_review_header_title'] = 'Document Review Interface';
$string['tour_review_header_content'] = 'This is the document review interface where you validate applicant submissions. Each application contains documents that need to be verified.';
$string['tour_review_stats_title'] = 'Review Statistics';
$string['tour_review_stats_content'] = 'These cards show the review progress: total documents, validated, rejected, and pending review. Track your workload at a glance.';
$string['tour_review_navigation_title'] = 'Navigation';
$string['tour_review_navigation_content'] = 'Navigate between applications using these controls. You can move to the next pending review or jump to a specific application.';
$string['tour_review_documents_title'] = 'Document List';
$string['tour_review_documents_content'] = 'Each item in this list represents a document submitted by the applicant. Click on a document to preview it and perform validation.';
$string['tour_review_actions_title'] = 'Validation Actions';
$string['tour_review_actions_content'] = 'Use these buttons to approve or reject documents. You can also add notes and select specific reasons for rejection.';
$string['tour_review_applicant_title'] = 'Applicant Information';
$string['tour_review_applicant_content'] = 'This panel shows the applicant\'s profile information including name, contact details, and application history.';
$string['tour_review_progress_title'] = 'Review Progress';
$string['tour_review_progress_content'] = 'This progress bar shows how many documents you\'ve reviewed for the current application. Complete all reviews to finalize the validation.';
$string['tour_review_submit_title'] = 'Complete Review';
$string['tour_review_submit_content'] = 'After reviewing all documents, click this button to finalize the application review. This will update the application status accordingly.';

// =============================================================================
// USER TOUR: MY APPLICATIONS
// =============================================================================

$string['tour_myapplications_name'] = 'My Applications Tour';
$string['tour_myapplications_desc'] = 'Learn how to track and manage your job applications.';
$string['tour_myapplications_header_title'] = 'My Applications';
$string['tour_myapplications_header_content'] = 'This page shows all your submitted job applications. Track their status and view feedback from reviewers.';
$string['tour_myapplications_filters_title'] = 'Filter Applications';
$string['tour_myapplications_filters_content'] = 'Use these filters to find specific applications by status, vacancy, or date submitted.';
$string['tour_myapplications_card_title'] = 'Application Cards';
$string['tour_myapplications_card_content'] = 'Each card represents one of your applications showing the vacancy name, submission date, and current status.';
$string['tour_myapplications_status_title'] = 'Application Status';
$string['tour_myapplications_status_content'] = 'The status badge shows where your application is in the review process: Submitted, Under Review, Documents Validated, Interview Scheduled, Selected, or Rejected.';
$string['tour_myapplications_actions_title'] = 'View Details';
$string['tour_myapplications_actions_content'] = 'Click this button to view your full application including uploaded documents, reviewer feedback, and any required actions.';

// =============================================================================
// USER TOUR: MY REVIEWS (REVIEWER)
// =============================================================================

$string['tour_myreviews_name'] = 'My Reviews Tour';
$string['tour_myreviews_desc'] = 'Learn how to manage your assigned document reviews.';
$string['tour_myreviews_header_title'] = 'My Review Queue';
$string['tour_myreviews_header_content'] = 'This page shows all applications assigned to you for document review. Prioritize pending reviews to keep the hiring process moving.';
$string['tour_myreviews_stats_title'] = 'Review Statistics';
$string['tour_myreviews_stats_content'] = 'Track your review workload with these statistics: pending reviews, completed today, and total reviewed this month.';
$string['tour_myreviews_pending_title'] = 'Pending Reviews';
$string['tour_myreviews_pending_content'] = 'These applications are waiting for your review. Click on any card to start the document validation process.';
$string['tour_myreviews_completed_title'] = 'Completed Reviews';
$string['tour_myreviews_completed_content'] = 'Here you can see applications you\'ve already reviewed. You can revisit them if needed.';
$string['tour_myreviews_start_title'] = 'Start Review';
$string['tour_myreviews_start_content'] = 'Click this button to begin reviewing the application documents. You\'ll be taken to the document validation interface.';

// =============================================================================
// USER TOUR: APPLICATION DETAIL
// =============================================================================

$string['tour_application_name'] = 'Application Detail Tour';
$string['tour_application_desc'] = 'Learn how to view and understand your application details.';
$string['tour_application_header_title'] = 'Application Details';
$string['tour_application_header_content'] = 'This page shows the complete details of your application including status, submitted documents, and reviewer feedback.';
$string['tour_application_status_title'] = 'Current Status';
$string['tour_application_status_content'] = 'This section shows your application\'s current status and any important messages from the review team.';
$string['tour_application_documents_title'] = 'Submitted Documents';
$string['tour_application_documents_content'] = 'View all documents you submitted with this application. Each document shows its validation status and any reviewer comments.';
$string['tour_application_timeline_title'] = 'Application Timeline';
$string['tour_application_timeline_content'] = 'This timeline shows the history of your application from submission through each review stage.';
$string['tour_application_actions_title'] = 'Available Actions';
$string['tour_application_actions_content'] = 'Depending on your application status, you may be able to reupload documents, withdraw your application, or complete additional steps.';

// =============================================================================
// USER TOUR: VACANCY DETAIL
// =============================================================================

$string['tour_vacancy_name'] = 'Vacancy Detail Tour';
$string['tour_vacancy_desc'] = 'Learn how to view vacancy details and requirements.';
$string['tour_vacancy_header_title'] = 'Vacancy Details';
$string['tour_vacancy_header_content'] = 'This page shows complete information about the job vacancy including requirements, responsibilities, and application instructions.';
$string['tour_vacancy_info_title'] = 'Position Information';
$string['tour_vacancy_info_content'] = 'Here you\'ll find the job title, department, location, modality, and contract details.';
$string['tour_vacancy_requirements_title'] = 'Requirements';
$string['tour_vacancy_requirements_content'] = 'This section lists the required qualifications, experience, and skills for the position. Make sure you meet these before applying.';
$string['tour_vacancy_documents_title'] = 'Required Documents';
$string['tour_vacancy_documents_content'] = 'Review the list of documents you\'ll need to submit with your application. Prepare these before starting your application.';
$string['tour_vacancy_apply_title'] = 'Apply Now';
$string['tour_vacancy_apply_content'] = 'If you meet the requirements, click this button to start your application. You\'ll be guided through the document upload process.';

// =============================================================================
// USER TOUR: MANAGE VACANCIES
// =============================================================================

$string['tour_manage_name'] = 'Vacancy Management Tour';
$string['tour_manage_desc'] = 'Learn how to manage and administer job vacancies.';
$string['tour_manage_header_title'] = 'Vacancy Management';
$string['tour_manage_header_content'] = 'This page provides comprehensive tools for managing all vacancies in the system including creation, editing, and status management.';
$string['tour_manage_create_title'] = 'Create Vacancy';
$string['tour_manage_create_content'] = 'Click this button to create a new vacancy. You\'ll select a convocatoria first, then fill in the position details.';
$string['tour_manage_filters_title'] = 'Advanced Filters';
$string['tour_manage_filters_content'] = 'Use these filters to find specific vacancies by status, convocatoria, date range, or keyword.';
$string['tour_manage_bulk_title'] = 'Bulk Actions';
$string['tour_manage_bulk_content'] = 'Select multiple vacancies to perform bulk actions like publish, close, or export. Use the checkboxes to select items.';
$string['tour_manage_table_title'] = 'Vacancy Table';
$string['tour_manage_table_content'] = 'This table shows all vacancies with sortable columns. Click column headers to sort, or use the action buttons for individual management.';
$string['tour_manage_actions_title'] = 'Quick Actions';
$string['tour_manage_actions_content'] = 'Each row has action buttons for viewing, editing, publishing, or managing applications for that vacancy.';

// =============================================================================
// USER TOUR: DOCUMENTS
// =============================================================================

$string['tour_documents_name'] = 'Document Management Tour';
$string['tour_documents_desc'] = 'Learn how to manage and review application documents.';
$string['tour_documents_header_title'] = 'Document Management';
$string['tour_documents_header_content'] = 'This page allows you to view and manage all documents submitted with applications.';
$string['tour_documents_filters_title'] = 'Filter Documents';
$string['tour_documents_filters_content'] = 'Use these filters to find documents by type, status, or applicant name.';
$string['tour_documents_list_title'] = 'Document List';
$string['tour_documents_list_content'] = 'Each row shows a document with its type, applicant, upload date, and validation status.';
$string['tour_documents_preview_title'] = 'Preview Documents';
$string['tour_documents_preview_content'] = 'Click the preview button to view documents without downloading. This helps speed up the review process.';
$string['tour_documents_validate_title'] = 'Validate Documents';
$string['tour_documents_validate_content'] = 'Use the validation buttons to approve or reject documents. Add notes to provide feedback to applicants.';

// =============================================================================
// USER TOUR: VALIDATE DOCUMENT
// =============================================================================

$string['tour_validate_document_name'] = 'Document Validation Tour';
$string['tour_validate_document_desc'] = 'Learn the step-by-step process for validating applicant documents.';
$string['tour_validate_document_header_title'] = 'Document Validation';
$string['tour_validate_document_header_content'] = 'This interface allows you to thoroughly review and validate individual documents from applicants.';
$string['tour_validate_document_preview_title'] = 'Document Preview';
$string['tour_validate_document_preview_content'] = 'The document is displayed here for your review. You can zoom in, scroll through pages, and check all details.';
$string['tour_validate_document_checklist_title'] = 'Validation Checklist';
$string['tour_validate_document_checklist_content'] = 'Use this checklist to verify that the document meets all requirements. Check each item as you verify it.';
$string['tour_validate_document_notes_title'] = 'Reviewer Notes';
$string['tour_validate_document_notes_content'] = 'Add notes to explain your validation decision. These notes help the applicant understand what needs to be corrected if rejected.';
$string['tour_validate_document_decision_title'] = 'Make Decision';
$string['tour_validate_document_decision_content'] = 'Select Approve if the document is valid, or Reject if it needs corrections. Be sure to add notes explaining any rejection.';
$string['tour_validate_document_submit_title'] = 'Submit Validation';
$string['tour_validate_document_submit_content'] = 'Click this button to submit your validation decision. The applicant will be notified of the result.';

// =============================================================================
// USER TOUR: REPORTS
// =============================================================================

$string['tour_reports_name'] = 'Reports Dashboard Tour';
$string['tour_reports_desc'] = 'Learn how to access and generate reports from the job board data.';
$string['tour_reports_header_title'] = 'Reports Dashboard';
$string['tour_reports_header_content'] = 'This page provides access to various reports and analytics about the recruitment process.';
$string['tour_reports_overview_title'] = 'Overview Statistics';
$string['tour_reports_overview_content'] = 'These charts and numbers give you a quick overview of recruitment activity, application volumes, and processing times.';
$string['tour_reports_filters_title'] = 'Report Filters';
$string['tour_reports_filters_content'] = 'Use these filters to customize the date range, convocatoria, or other parameters for your reports.';
$string['tour_reports_types_title'] = 'Report Types';
$string['tour_reports_types_content'] = 'Select from different report types: Applications by Status, Documents by Type, Processing Times, and more.';
$string['tour_reports_export_title'] = 'Export Data';
$string['tour_reports_export_content'] = 'Download reports in various formats (PDF, Excel, CSV) for offline analysis or sharing with stakeholders.';

// =============================================================================
// USER TOUR: CONVOCATORIA MANAGE (CREATE/EDIT)
// =============================================================================

$string['tour_convocatoria_manage_name'] = 'Convocatoria Form Tour';
$string['tour_convocatoria_manage_desc'] = 'Learn how to create and configure a job call convocatoria.';
$string['tour_convocatoria_manage_header_title'] = 'Convocatoria Form';
$string['tour_convocatoria_manage_header_content'] = 'Use this form to create or edit a convocatoria. Fill in all required fields to define your job call.';
$string['tour_convocatoria_manage_basic_title'] = 'Basic Information';
$string['tour_convocatoria_manage_basic_content'] = 'Enter the convocatoria code, name, and description. The code should be unique and help identify this job call.';
$string['tour_convocatoria_manage_dates_title'] = 'Date Configuration';
$string['tour_convocatoria_manage_dates_content'] = 'Set the start and end dates for accepting applications. Make sure to allow enough time for applicants to submit their documents.';
$string['tour_convocatoria_manage_settings_title'] = 'Application Settings';
$string['tour_convocatoria_manage_settings_content'] = 'Configure whether applicants can apply to multiple vacancies within this convocatoria and set any application limits.';
$string['tour_convocatoria_manage_terms_title'] = 'Terms and Conditions';
$string['tour_convocatoria_manage_terms_content'] = 'Define the terms applicants must accept when applying. This can include privacy policies and data handling agreements.';
$string['tour_convocatoria_manage_save_title'] = 'Save Convocatoria';
$string['tour_convocatoria_manage_save_content'] = 'Click Save to create or update the convocatoria. You can add vacancies after saving.';

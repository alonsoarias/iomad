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
$string['company'] = 'Company';
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
$string['convocatoriapdf'] = 'Convocatoria PDF';
$string['convocatoriapdf_help'] = 'Upload the official convocatoria document in PDF format';
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
$string['confirmdeletevacancy'] = 'Are you sure you want to delete this vacancy? This action cannot be undone.';
$string['vacancyhasapplications'] = 'This vacancy has applications and cannot be deleted';
$string['closingsoon'] = 'Closing soon';
$string['urgent'] = 'Urgent';
$string['newapplicants'] = 'New applicants';
$string['totalapplicants'] = 'Total applicants';

// Contract types
$string['contract:catedra'] = 'Adjunct Professor';
$string['contract:planta'] = 'Full-time Professor';
$string['contract:temporal'] = 'Temporary';
$string['contract:ocasional'] = 'Occasional';
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
// ADDITIONAL STRINGS - RECAPTCHA
// =============================================================================

$string['recaptchasettings'] = 'reCAPTCHA settings';
$string['recaptchasettings_desc'] = 'Configure Google reCAPTCHA for forms';
$string['recaptcha_enabled'] = 'Enable reCAPTCHA';
$string['recaptcha_enabled_desc'] = 'Enable reCAPTCHA verification on forms';
$string['recaptcha_sitekey'] = 'Site key';
$string['recaptcha_sitekey_desc'] = 'Google reCAPTCHA site key';
$string['recaptcha_secretkey'] = 'Secret key';
$string['recaptcha_secretkey_desc'] = 'Google reCAPTCHA secret key';
$string['recaptcha_version'] = 'reCAPTCHA version';
$string['recaptcha_version_desc'] = 'Select reCAPTCHA version to use';
$string['recaptcha_v2'] = 'reCAPTCHA v2';
$string['recaptcha_v3'] = 'reCAPTCHA v3';
$string['recaptcha_v3_threshold'] = 'reCAPTCHA v3 threshold';
$string['recaptcha_v3_threshold_desc'] = 'Minimum score for reCAPTCHA v3 (0.0 to 1.0)';
$string['recaptcha_failed'] = 'reCAPTCHA verification failed';
$string['recaptcha_required'] = 'Please complete the reCAPTCHA verification';

// =============================================================================
// ADDITIONAL STRINGS - IOMAD INTEGRATION
// =============================================================================

$string['iomadsettings'] = 'IOMAD settings';
$string['iomadoptions'] = 'IOMAD options';
$string['iomad_department'] = 'Department';
$string['multi_tenant'] = 'Multi-tenant';
$string['allcompanies'] = 'All companies';
$string['alldepartments'] = 'All departments';
$string['selectcompany'] = 'Select company';
$string['selectdepartment'] = 'Select department';
$string['selectconvocatoriafirst'] = 'Please select a convocatoria first';
$string['createcompanies'] = 'Create companies';

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
$string['error:occasionalrequiresexperience'] = 'Occasional contract requires experience';
$string['error:pastdate'] = 'Date cannot be in the past';
$string['error:requiredfield'] = 'This field is required';
$string['error:schedulingconflict'] = 'Scheduling conflict detected';
$string['error:singleapplicationonly'] = 'Only one application allowed';
$string['error:vacancyclosed'] = 'This vacancy is closed';
$string['error:vacancynotfound'] = 'Vacancy not found';
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
$string['showingxofy'] = 'Showing {$a->from} to {$a->to} of {$a->total}';
$string['showingxtoy'] = 'Showing {$a->start} to {$a->end}';
$string['andmore'] = 'and {$a} more';
$string['moveup'] = 'Move up';
$string['movedown'] = 'Move down';
$string['sortorder'] = 'Sort order';

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
$string['signup_companyinfo'] = 'Company information';
$string['signup_company_help'] = 'Select the company you belong to';
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
$string['companydashboard'] = 'Company dashboard';
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
$string['ph_company_name'] = 'Company name';
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
$string['unknowndoctype'] = 'Unknown document type';

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
$string['companyvacancies'] = 'Company vacancies';
$string['nocompanvacancies'] = 'No company vacancies';
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
$string['filterbyname'] = 'Filter by name...';
$string['togglesidebar'] = 'Toggle sidebar';
$string['pendingdocs'] = 'Pending documents';
$string['exitgrading'] = 'Exit review panel';
$string['exit'] = 'Exit';
$string['fullscreen'] = 'Fullscreen';
$string['approveall'] = 'Approve all';
$string['confirmapproveall'] = 'Are you sure you want to approve all pending documents for this application?';
$string['previewunavailable'] = 'Preview not available for this file type';
$string['downloadtoview'] = 'Download to view';
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

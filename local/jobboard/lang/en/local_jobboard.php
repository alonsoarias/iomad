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

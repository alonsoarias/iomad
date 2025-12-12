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
$string['opensnewwindow'] = 'Opens in new window';
$string['expandcollapse'] = 'Expand/Collapse';
$string['sortascending'] = 'Sort ascending';
$string['sortdescending'] = 'Sort descending';
$string['sortby'] = 'Sort by';
$string['sortorder'] = 'Sort order';
$string['filterby'] = 'Filter by';
$string['showing'] = 'Showing';
$string['entries'] = 'entries';
$string['firstpage'] = 'First page';
$string['lastpage'] = 'Last page';
$string['previouspage'] = 'Previous page';
$string['nextpage'] = 'Next page';

// =============================================================================
// ADDITIONAL NAVIGATION & MENU
// =============================================================================

$string['mainmenutitle'] = 'Job Board';
$string['mainmenutitle_desc'] = 'Title displayed in the main navigation menu';
$string['navigationsettings'] = 'Navigation settings';
$string['navigationsettings_desc'] = 'Configure navigation menu options';
$string['showinmainmenu'] = 'Show in main menu';
$string['showinmainmenu_desc'] = 'Display link in the main navigation menu';
$string['showpublicnavlink'] = 'Show public navigation link';
$string['showpublicnavlink_desc'] = 'Show link to public vacancies in navigation';
$string['administracion'] = 'Administration';
$string['breadcrumb'] = 'Breadcrumb';
$string['navigation'] = 'Navigation';
$string['backtoapplications'] = 'Back to applications';
$string['backtoconvocatoria'] = 'Back to convocatoria';
$string['backtoconvocatorias'] = 'Back to convocatorias';
$string['backtovacancies'] = 'Back to vacancies';
$string['backtovacancy'] = 'Back to vacancy';
$string['backtomanage'] = 'Back to manage';
$string['backtoreviewlist'] = 'Back to review list';
$string['backtorolelist'] = 'Back to role list';
$string['back_to_templates'] = 'Back to templates';
$string['gotocreateconvocatoria'] = 'Create a new convocatoria';

// =============================================================================
// ADDITIONAL COMMON LABELS
// =============================================================================

$string['add'] = 'Add';
$string['active'] = 'Active';
$string['inactive'] = 'Inactive';
$string['dateandtime'] = 'Date and time';
$string['datefrom'] = 'Date from';
$string['dateto'] = 'Date to';
$string['dates'] = 'Dates';
$string['datesubmitted'] = 'Date submitted';
$string['dateapplied'] = 'Date applied';
$string['daysleft'] = 'Days left';
$string['items'] = 'Items';
$string['row'] = 'Row';
$string['column'] = 'Column';
$string['result'] = 'Result';
$string['step'] = 'Step';
$string['applied'] = 'Applied';
$string['assigned'] = 'Assigned';
$string['reviewed'] = 'Reviewed';
$string['validated'] = 'Validated';
$string['uploaded'] = 'Uploaded';
$string['closed'] = 'Closed';
$string['default'] = 'Default';
$string['revoked'] = 'Revoked';
$string['show'] = 'Show';
$string['inprogress'] = 'In progress';
$string['complete'] = 'Complete';
$string['internal'] = 'Internal';
$string['or'] = 'or';
$string['andmore'] = 'and more';
$string['trend'] = 'Trend';
$string['trending_up'] = 'Trending up';
$string['trending_down'] = 'Trending down';
$string['performance'] = 'Performance';
$string['performedby'] = 'Performed by';
$string['rating'] = 'Rating';
$string['rating_excellent'] = 'Excellent';
$string['rating_verygood'] = 'Very good';
$string['rating_good'] = 'Good';
$string['rating_fair'] = 'Fair';
$string['rating_poor'] = 'Poor';
$string['overallrating'] = 'Overall rating';

// =============================================================================
// ADDITIONAL VACANCY FIELDS
// =============================================================================

$string['vacancy_status_draft'] = 'Draft';
$string['vacancy_status_published'] = 'Published';
$string['vacancyinfo'] = 'Vacancy information';
$string['vacancysummary'] = 'Vacancy summary';
$string['vacancyopen'] = 'Vacancy is open';
$string['vacancyreopened'] = 'Vacancy reopened';
$string['vacancyunpublished'] = 'Vacancy unpublished';
$string['vacanciesfound'] = 'Vacancies found';
$string['vacanciesavailable'] = 'Vacancies available';
$string['vacanciesforconvocatoria'] = 'Vacancies for this convocatoria';
$string['vacancies_created'] = 'Vacancies created';
$string['vacancies_updated'] = 'Vacancies updated';
$string['vacancies_skipped'] = 'Vacancies skipped';
$string['vacancies_dashboard_desc'] = 'Manage and view all vacancies';
$string['vacancy_inherits_dates'] = 'Vacancy inherits dates from convocatoria';
$string['unknownvacancy'] = 'Unknown vacancy';
$string['novacanciesyet'] = 'No vacancies yet';
$string['novacanciesfound'] = 'No vacancies found';
$string['openvacancies'] = 'Open vacancies';
$string['publishedvacancies'] = 'Published vacancies';
$string['availablevacancies'] = 'Available vacancies';
$string['totalvacancies'] = 'Total vacancies';
$string['totalpositions'] = 'Total positions';
$string['facultyvacancies'] = 'Faculty vacancies';
$string['viewvacancies'] = 'View vacancies';
$string['viewvacancydetails'] = 'View vacancy details';
$string['searchvacancies'] = 'Search vacancies';
$string['addvacancy'] = 'Add vacancy';
$string['applytovacancy'] = 'Apply to this vacancy';
$string['sharethisvacancy'] = 'Share this vacancy';
$string['createvacancyinconvocatoriadesc'] = 'Create a new vacancy in this convocatoria';
$string['explorevacancias'] = 'Explore vacancies';
$string['browservacancies'] = 'Browse vacancies';
$string['browsevacancies'] = 'Browse vacancies';
$string['browse_vacancies_desc'] = 'Search and browse available vacancies';
$string['closesindays'] = 'Closes in {$a} days';
$string['closesin'] = 'Closes in';
$string['closingdate'] = 'Closing date';
$string['closingsoondays'] = 'Closing within {$a} days';

// =============================================================================
// ADDITIONAL CONVOCATORIA FIELDS
// =============================================================================

$string['convocatoria_status_'] = 'Status';
$string['convocatoriavacancycount'] = 'Number of vacancies';
$string['convocatoriaactive'] = 'Convocatoria is active';
$string['convocatoriaopened'] = 'Convocatoria opened';
$string['convocatoriareopened'] = 'Convocatoria reopened';
$string['convocatoriaarchived'] = 'Convocatoria archived';
$string['convocatoriaclosedmsg'] = 'This convocatoria is closed for applications';
$string['convocatoriadates'] = 'Convocatoria dates';
$string['convocatoriadocexemptions'] = 'Document exemptions for this convocatoria';
$string['convocatoriahelp'] = 'Help with convocatorias';
$string['convocatoriastatistics'] = 'Convocatoria statistics';
$string['totalconvocatorias'] = 'Total convocatorias';
$string['addconvocatoria'] = 'Add convocatoria';
$string['manageconvocatorias'] = 'Manage convocatorias';
$string['browseconvocatorias'] = 'Browse convocatorias';
$string['browseconvocatorias_desc'] = 'View all available convocatorias';
$string['convocatorias_dashboard_desc'] = 'Manage and view all convocatorias';
$string['noconvocatoriasdesc'] = 'No convocatorias available at this time';
$string['noconvocatorias_desc'] = 'No convocatorias have been created yet';
$string['noconvocatoriasavailable'] = 'No convocatorias available';
$string['selectconvocatoriafirst'] = 'Please select a convocatoria first';
$string['openconvocatoria'] = 'Open convocatoria';
$string['confirmcloseconvocatoria'] = 'Are you sure you want to close this convocatoria?';
$string['confirmopenconvocatoria'] = 'Are you sure you want to open this convocatoria?';
$string['confirmreopenconvocatoria'] = 'Are you sure you want to reopen this convocatoria?';
$string['confirmdeletevconvocatoria'] = 'Are you sure you want to delete this convocatoria?';
$string['confirmarchiveconvocatoria'] = 'Are you sure you want to archive this convocatoria?';

// =============================================================================
// ADDITIONAL APPLICATION FIELDS
// =============================================================================

$string['applicationguidelines'] = 'Application guidelines';
$string['applicationlimits'] = 'Application limits';
$string['applicationlimits_perconvocatoria_desc'] = 'Limit applications per user per convocatoria';
$string['applicationstats'] = 'Application statistics';
$string['applicationerror'] = 'Application error';
$string['applicationsbystatus'] = 'Applications by status';
$string['applicationsbyvacancy'] = 'Applications by vacancy';
$string['applicationsqueue'] = 'Applications queue';
$string['applicantstatistics'] = 'Applicant statistics';
$string['myapplicationcount'] = 'My applications';
$string['myapplications_desc'] = 'View and manage your applications';
$string['previousapplication'] = 'Previous application';
$string['nextapplication'] = 'Next application';
$string['viewmyapplication'] = 'View my application';
$string['viewmyapplications'] = 'View my applications';
$string['noapplicationsdesc'] = 'No applications have been submitted yet';
$string['noapplicationsfound'] = 'No applications found';
$string['allapplicants'] = 'All applicants';
$string['unassignedapplications'] = 'Unassigned applications';
$string['nounassignedapplications'] = 'No unassigned applications';
$string['pendingassignment'] = 'Pending assignment';
$string['pendingassignments'] = 'Pending assignments';
$string['activeassignments'] = 'Active assignments';
$string['totalassigned'] = 'Total assigned';
$string['totalassignedusers'] = 'Total assigned users';
$string['wanttoapply'] = 'Want to apply?';
$string['readytoapply'] = 'Ready to apply';
$string['createaccounttoapply'] = 'Create an account to apply';
$string['loginandapply'] = 'Log in and apply';
$string['loginrequiredtoapply'] = 'You must be logged in to apply';
$string['logintoapply'] = 'Log in to apply';
$string['cannotapply'] = 'You cannot apply to this vacancy';
$string['applyhelp_text'] = 'Follow the instructions to complete your application';
$string['applynow_desc'] = 'Submit your application now';
$string['applynowdesc'] = 'Start your application process';
$string['applyto'] = 'Apply to';
$string['withdraw'] = 'Withdraw';
$string['confirmwithdraw'] = 'Are you sure you want to withdraw your application?';
$string['coverletter'] = 'Cover letter';
$string['allowmultipleapplications_convocatoria'] = 'Allow multiple applications per convocatoria';
$string['allowmultipleapplications_convocatoria_desc'] = 'Allow users to apply to multiple vacancies in the same convocatoria';

// =============================================================================
// ADDITIONAL STATUS STRINGS
// =============================================================================

$string['status:'] = 'Status';
$string['status:assigned'] = 'Assigned';
$string['status_'] = 'Status';
$string['docstatus_'] = 'Document status';
$string['docstatus:'] = 'Document status';
$string['appstatus:'] = 'Application status';
$string['currentstatus'] = 'Current status';
$string['changestatus'] = 'Change status';
$string['statuschanged'] = 'Status changed successfully';
$string['statuschangeerror'] = 'Error changing status';
$string['statushistory'] = 'Status history';
$string['statustabs'] = 'Status tabs';

// =============================================================================
// ADDITIONAL DOCUMENT FIELDS
// =============================================================================

$string['documentactions'] = 'Document actions';
$string['documentchecklist'] = 'Document checklist';
$string['documentexpired'] = 'Document has expired';
$string['documentinfo'] = 'Document information';
$string['documentnotfound'] = 'Document not found';
$string['documentnumber'] = 'Document number';
$string['documentpreview'] = 'Document preview';
$string['documentref'] = 'Document reference';
$string['documentref_desc'] = 'Reference number for this document';
$string['documentrejected'] = 'Document was rejected';
$string['documentreuploaded'] = 'Document re-uploaded successfully';
$string['documentsapproved'] = 'Documents approved';
$string['documentsettings'] = 'Document settings';
$string['documentshelp'] = 'Help with documents';
$string['documentsrejected'] = 'Documents rejected';
$string['documentsremaining'] = 'Documents remaining';
$string['documentsreviewed'] = 'Documents reviewed';
$string['documentstats'] = 'Document statistics';
$string['documentstoreview'] = 'Documents to review';
$string['documentvalidated'] = 'Document validated';
$string['totaldocuments'] = 'Total documents';
$string['totaldoctypes'] = 'Total document types';
$string['nodocumentspending'] = 'No documents pending';
$string['nodocumentstoreview'] = 'No documents to review';
$string['documentissuedate'] = 'Document issue date';
$string['issuedate'] = 'Issue date';
$string['issuedatehelp'] = 'Date when the document was issued';
$string['previewdocument'] = 'Preview document';
$string['previewonly'] = 'Preview only';
$string['previewunavailable'] = 'Preview unavailable';
$string['previewconfirm'] = 'Confirm preview';
$string['previewtotal'] = 'Preview total';
$string['previewmode'] = 'Preview mode';
$string['previewmodenotice'] = 'This is a preview. No changes will be saved.';
$string['downloadtoview'] = 'Download to view';

// =============================================================================
// ADDITIONAL DOCUMENT TYPES
// =============================================================================

$string['doctype_'] = 'Document type';
$string['doctype_antecedentes_contraloria'] = 'Comptroller background check';
$string['doctype_antecedentes_policia'] = 'Police background check';
$string['doctype_antecedentes_procuraduria'] = 'Attorney General background check';
$string['doctype_titulo_postgrado'] = 'Graduate degree';
$string['doctype_rnmc'] = 'National criminal background (RNMC)';
$string['doctype_sigep'] = 'SIGEP registration';
$string['doctype_tarjeta_profesional'] = 'Professional license card';
$string['doctype_isrequired_help'] = 'Indicates if this document is mandatory';
$string['aboutdoctypes'] = 'About document types';
$string['doctypes_desc'] = 'Configure document types required for applications';
$string['doctypeshelp'] = 'Help with document types';
$string['doctypelist'] = 'Document type list';
$string['doctypecreated'] = 'Document type created successfully';
$string['doctypedeleted'] = 'Document type deleted successfully';
$string['doctypeupdated'] = 'Document type updated successfully';
$string['adddoctype'] = 'Add document type';
$string['enableddoctypes'] = 'Enabled document types';
$string['requireddoctypes'] = 'Required document types';
$string['documenttypes'] = 'Document types';
$string['conditionaldoctypes'] = 'Conditional document types';
$string['conditionalnote'] = 'Conditional requirements note';
$string['conditional_document_note'] = 'This document may be required based on specific conditions';

// =============================================================================
// ADDITIONAL DOCUMENT CATEGORIES
// =============================================================================

$string['doccat_'] = 'Document category';
$string['doccategory_'] = 'Document category';
$string['doccategory_academic'] = 'Academic documents';
$string['doccategory_background'] = 'Background checks';
$string['doccategory_financial'] = 'Financial documents';
$string['doccategory_health'] = 'Health documents';
$string['doccategory_identity'] = 'Identity documents';
$string['doccategory_professional'] = 'Professional documents';
$string['docrequirements'] = 'Document requirements';

// =============================================================================
// ADDITIONAL VALIDATION STRINGS
// =============================================================================

$string['bulkvalidation_desc'] = 'Validate multiple documents at once';
$string['bulkvalidationcomplete'] = 'Bulk validation complete';
$string['validateall'] = 'Validate all';
$string['validationdecision'] = 'Validation decision';
$string['validationrequirements'] = 'Validation requirements';
$string['validationsummary'] = 'Validation summary';
$string['validationapproved'] = 'Validation approved';
$string['validfrom'] = 'Valid from';
$string['validuntil'] = 'Valid until';
$string['validityperiod'] = 'Validity period';
$string['noexpiry'] = 'No expiry';
$string['bulkvalidation'] = 'Bulk validation';
$string['bulkactions'] = 'Bulk actions';
$string['bulkactionerrors'] = 'Bulk action errors';
$string['bulkrejected'] = 'Bulk rejected';
$string['approveselected'] = 'Approve selected';
$string['rejectselected'] = 'Reject selected';
$string['assignselected'] = 'Assign selected';
$string['verification'] = 'Verification';
$string['checklistitems'] = 'Checklist items';

// =============================================================================
// ADDITIONAL CHECKLIST ITEMS
// =============================================================================

$string['checklist_acta_date'] = 'Check graduation certificate date';
$string['checklist_acta_number'] = 'Check graduation certificate number';
$string['checklist_background_date'] = 'Check background check date';
$string['checklist_background_status'] = 'Check background status';
$string['checklist_cedula_number'] = 'Check ID card number';
$string['checklist_cedula_photo'] = 'Check ID card photo';
$string['checklist_complete'] = 'Document is complete';
$string['checklist_eps_active'] = 'Check EPS is active';
$string['checklist_eps_entity'] = 'Check EPS entity';
$string['checklist_legible'] = 'Document is legible';
$string['checklist_medical_aptitude'] = 'Check medical aptitude';
$string['checklist_medical_date'] = 'Check medical certificate date';
$string['checklist_military_class'] = 'Check military card class';
$string['checklist_military_number'] = 'Check military card number';
$string['checklist_namematch'] = 'Name matches applicant';
$string['checklist_pension_active'] = 'Check pension is active';
$string['checklist_pension_fund'] = 'Check pension fund';
$string['checklist_rut_nit'] = 'Check RUT NIT number';
$string['checklist_rut_updated'] = 'Check RUT is updated';
$string['checklist_tarjeta_number'] = 'Check professional card number';
$string['checklist_tarjeta_profession'] = 'Check profession on card';
$string['checklist_title_date'] = 'Check degree date';
$string['checklist_title_institution'] = 'Check issuing institution';
$string['checklist_title_program'] = 'Check program name';

// =============================================================================
// ADDITIONAL REJECTION REASONS
// =============================================================================

$string['rejectreason'] = 'Rejection reason';
$string['rejectreason_'] = 'Rejection reason';
$string['rejectreason_expired'] = 'Document has expired';
$string['rejectreason_illegible'] = 'Document is illegible';
$string['rejectreason_incomplete'] = 'Document is incomplete';
$string['rejectreason_mismatch'] = 'Information does not match';
$string['rejectreason_placeholder'] = 'Enter rejection reason...';
$string['rejectreason_wrongtype'] = 'Wrong document type';
$string['norejections'] = 'No rejections';
$string['selectreason'] = 'Select a reason';
$string['noreason'] = 'No reason specified';

// =============================================================================
// ADDITIONAL REVIEWER STRINGS
// =============================================================================

$string['revieweradded'] = 'Reviewer added successfully';
$string['revieweradderror'] = 'Error adding reviewer';
$string['reviewerremoved'] = 'Reviewer removed successfully';
$string['reviewerremoveerror'] = 'Error removing reviewer';
$string['reviewerperformance'] = 'Reviewer performance';
$string['reviewertasks'] = 'Reviewer tasks';
$string['addreviewer'] = 'Add reviewer';
$string['addreviewerstoprogram'] = 'Add reviewers to program';
$string['noreviewers'] = 'No reviewers';
$string['noreviewersforprogram'] = 'No reviewers for this program';
$string['totalreviewers'] = 'Total reviewers';
$string['activereviewers'] = 'Active reviewers';
$string['leadreviewers'] = 'Lead reviewers';
$string['confirmremovereviewer'] = 'Are you sure you want to remove this reviewer?';
$string['confirmunassign'] = 'Are you sure you want to unassign this reviewer?';
$string['autoassignall'] = 'Auto-assign all';
$string['autoassigncomplete'] = 'Auto-assignment complete';
$string['autoassignhelp'] = 'Automatically assign reviewers based on workload';
$string['autovalidated'] = 'Auto-validated';
$string['manualassign'] = 'Manual assignment';
$string['assignnewusers'] = 'Assign new users';
$string['assignto'] = 'Assign to';
$string['assignedusers'] = 'Assigned users';
$string['usersassigned'] = 'Users assigned';
$string['usersassignedcount'] = '{$a} users assigned';
$string['userunassigned'] = 'User unassigned';
$string['nousersassigned'] = 'No users assigned';
$string['nousersavailable'] = 'No users available';
$string['currentworkload'] = 'Current workload';
$string['avgworkload'] = 'Average workload';
$string['avgtime'] = 'Average time';
$string['avgvalidationtime'] = 'Average validation time';
$string['maxperreviewer'] = 'Maximum per reviewer';
$string['reviewall'] = 'Review all';
$string['reviewprogress'] = 'Review progress';
$string['reviewstatistics'] = 'Review statistics';
$string['reviewobservations'] = 'Review observations';
$string['reviewobservations_placeholder'] = 'Enter your observations...';
$string['reviewsubmitted'] = 'Review submitted';
$string['reviewsubmitted_with_notification'] = 'Review submitted and notification sent';
$string['submitreview'] = 'Submit review';
$string['reviewedby'] = 'Reviewed by';
$string['noassignments'] = 'No assignments';
$string['noassignments_desc'] = 'No assignments have been made yet';
$string['myreviews_desc'] = 'View and manage your assigned reviews';
$string['reviewdocuments_desc'] = 'Review and validate applicant documents';
$string['review_dashboard_desc'] = 'Overview of review activities';
$string['assignreviewers_desc'] = 'Assign reviewers to applications';

// =============================================================================
// PROGRAM REVIEWERS
// =============================================================================

$string['program_reviewers'] = 'Program reviewers';
$string['program_reviewers_desc'] = 'Manage reviewers assigned to programs';
$string['programreviewerhelp'] = 'Help with program reviewers';
$string['programswithreviewers'] = 'Programs with reviewers';
$string['noprogramswithreviewers'] = 'No programs have reviewers assigned';

// =============================================================================
// ADDITIONAL COMMITTEE STRINGS
// =============================================================================

$string['committeecreated'] = 'Committee created successfully';
$string['committeecreateerror'] = 'Error creating committee';
$string['committeename'] = 'Committee name';
$string['committeeautoroleassign'] = 'Auto-assign committee roles';
$string['committees_desc'] = 'Manage selection committees';
$string['allcommittees'] = 'All committees';
$string['activecommittees'] = 'Active committees';
$string['totalcommittees'] = 'Total committees';
$string['totalcommmembers'] = 'Total committee members';
$string['facultieswithoutcommittee'] = 'Faculties without committee';
$string['existingvacancycommittee'] = 'Existing vacancy committee';
$string['legacyvacancycommittee'] = 'Legacy vacancy committee';
$string['nocommitteeforthisvacancy'] = 'No committee for this vacancy';
$string['facultycommitteedefaultname'] = '{$a} Selection Committee';
$string['members'] = 'Members';
$string['membercount'] = 'Member count';
$string['memberadderror'] = 'Error adding member';
$string['memberremoveerror'] = 'Error removing member';
$string['nomembers'] = 'No members';
$string['confirmremovemember'] = 'Are you sure you want to remove this member?';
$string['chairhelp'] = 'Help with committee chair';
$string['evaluatorshelp'] = 'Help with evaluators';
$string['nosecretaryoptional'] = 'Secretary is optional';

// =============================================================================
// ADDITIONAL INTERVIEW STRINGS
// =============================================================================

$string['interviewcompleted'] = 'Interview completed';
$string['interviewfeedback'] = 'Interview feedback';
$string['interviewinstructions'] = 'Interview instructions';
$string['interviewscheduleerror'] = 'Error scheduling interview';
$string['interviewstatus_'] = 'Interview status';
$string['interviewtype_'] = 'Interview type';
$string['interviewtype_inperson'] = 'In person';
$string['interviewtype_phone'] = 'Phone';
$string['interviewtype_video'] = 'Video call';
$string['scheduledinterviews'] = 'Scheduled interviews';
$string['schedulenewinterview'] = 'Schedule new interview';
$string['completeinterview'] = 'Complete interview';
$string['reschedulednote'] = 'Interview was rescheduled';
$string['rescheduledby'] = 'Rescheduled by';
$string['duration'] = 'Duration';
$string['locationorurl'] = 'Location or URL';
$string['noshow'] = 'No show';
$string['markedasnoshow'] = 'Marked as no show';
$string['markednoshow'] = 'Marked as no show';
$string['confirmnoshow'] = 'Confirm no show';
$string['cancelledby'] = 'Cancelled by';
$string['selectinterviewers'] = 'Select interviewers';

// =============================================================================
// ADDITIONAL EXEMPTION STRINGS
// =============================================================================

$string['exemptionactive'] = 'Exemption is active';
$string['exemptioncreated'] = 'Exemption created successfully';
$string['exemptiondetails'] = 'Exemption details';
$string['exemptionerror'] = 'Exemption error';
$string['exemptionlist'] = 'Exemption list';
$string['exemptionnotice'] = 'Exemption notice';
$string['exemptionreduceddocs'] = 'Reduced documents due to exemption';
$string['exemptionrevoked'] = 'Exemption revoked';
$string['exemptionrevokeerror'] = 'Error revoking exemption';
$string['exemptionupdated'] = 'Exemption updated successfully';
$string['exemptionusagehistory'] = 'Exemption usage history';
$string['exemptiontype_'] = 'Exemption type';
$string['exemptiontype_desc'] = 'Type of exemption to apply';
$string['exempteddocs'] = 'Exempted documents';
$string['exempteddocs_desc'] = 'Documents exempted from requirements';
$string['exempteddoctypes'] = 'Exempted document types';
$string['addexemption'] = 'Add exemption';
$string['revokeexemption'] = 'Revoke exemption';
$string['confirmrevokeexemption'] = 'Are you sure you want to revoke this exemption?';
$string['revokereason'] = 'Revoke reason';
$string['revokedby'] = 'Revoked by';
$string['revokedexemptions'] = 'Revoked exemptions';
$string['expiredexemptions'] = 'Expired exemptions';
$string['activeexemptions'] = 'Active exemptions';
$string['totalexemptions'] = 'Total exemptions';
$string['noexemptionusage'] = 'No exemption usage';
$string['iserexempted'] = 'ISER exempted';
$string['iserexempted_help'] = 'Document is exempted for ISER employees';
$string['professionexempt'] = 'Profession exempt';
$string['defaultexemptiontype'] = 'Default exemption type';
$string['defaultmaxagedays'] = 'Default maximum age in days';
$string['defaultvalidfrom'] = 'Default valid from';
$string['defaultvaliduntil'] = 'Default valid until';
$string['manageexemptions_desc'] = 'Create and manage document exemptions';

// =============================================================================
// ADDITIONAL CONTRACT TYPES
// =============================================================================

$string['contract:'] = 'Contract type';
$string['contract:prestacion_servicios'] = 'Service contract';
$string['contract:termino_fijo'] = 'Fixed-term contract';
$string['allcontracttypes'] = 'All contract types';
$string['selectcontracttype'] = 'Select contract type';

// =============================================================================
// EMAIL TEMPLATE STRINGS
// =============================================================================

$string['email_templates'] = 'Email templates';
$string['email_updated'] = 'Email updated successfully';
$string['email_action_reupload'] = 'Re-upload document action';
$string['emailtemplates_desc'] = 'Configure email notification templates';
$string['edit_template'] = 'Edit template';
$string['template_body'] = 'Template body';
$string['template_categories'] = 'Template categories';
$string['template_category'] = 'Template category';
$string['template_code'] = 'Template code';
$string['template_content'] = 'Template content';
$string['template_delete_failed'] = 'Failed to delete template';
$string['template_deleted_success'] = 'Template deleted successfully';
$string['template_description'] = 'Template description';
$string['template_disabled_success'] = 'Template disabled successfully';
$string['template_enabled'] = 'Template enabled';
$string['template_enabled_desc'] = 'Enable or disable this template';
$string['template_enabled_success'] = 'Template enabled successfully';
$string['template_help_html'] = 'HTML is supported in template content';
$string['template_help_placeholders'] = 'Available placeholders for this template';
$string['template_help_tenant'] = 'Template can be customized per tenant';
$string['template_help_title'] = 'Template help';
$string['template_info'] = 'Template information';
$string['template_name'] = 'Template name';
$string['template_not_found'] = 'Template not found';
$string['template_preview'] = 'Template preview';
$string['template_preview_hint'] = 'Preview how the email will look';
$string['template_priority'] = 'Template priority';
$string['template_reset_success'] = 'Template reset to default';
$string['template_saved_success'] = 'Template saved successfully';
$string['template_settings'] = 'Template settings';
$string['template_subject'] = 'Email subject';
$string['templates_disabled'] = 'Templates disabled';
$string['templates_enabled'] = 'Templates enabled';
$string['templates_installed'] = 'Templates installed';
$string['total_templates'] = 'Total templates';
$string['no_templates'] = 'No templates found';
$string['reset_to_default'] = 'Reset to default';
$string['confirm_reset'] = 'Are you sure you want to reset to default?';
$string['toggle_status'] = 'Toggle status';
$string['togglepreview'] = 'Toggle preview';
$string['html_support'] = 'HTML support';
$string['copy_placeholder'] = 'Copy placeholder';
$string['subject'] = 'Subject';

// =============================================================================
// ADDITIONAL PLACEHOLDERS
// =============================================================================

$string['placeholders'] = 'Placeholders';
$string['placeholders_help'] = 'Use these placeholders in your templates';
$string['available_placeholders'] = 'Available placeholders';
$string['ph_action_required'] = 'Action required description';
$string['ph_applicant_name'] = 'Applicant full name';
$string['ph_application_id'] = 'Application ID';
$string['ph_application_url'] = 'Application URL';
$string['ph_approved_count'] = 'Number of approved items';
$string['ph_close_date'] = 'Closing date';
$string['ph_company_name'] = 'Company name';
$string['ph_contact_info'] = 'Contact information';
$string['ph_current_date'] = 'Current date';
$string['ph_days_remaining'] = 'Days remaining';
$string['ph_deadline'] = 'Deadline';
$string['ph_documents_count'] = 'Number of documents';
$string['ph_faculty_name'] = 'Faculty name';
$string['ph_feedback'] = 'Feedback';
$string['ph_hours_until'] = 'Hours until deadline';
$string['ph_interview_date'] = 'Interview date';
$string['ph_interview_duration'] = 'Interview duration';
$string['ph_interview_feedback'] = 'Interview feedback';
$string['ph_interview_location'] = 'Interview location';
$string['ph_interview_notes'] = 'Interview notes';
$string['ph_interview_time'] = 'Interview time';
$string['ph_interview_type'] = 'Interview type';
$string['ph_interviewer_name'] = 'Interviewer name';
$string['ph_next_steps'] = 'Next steps';
$string['ph_notification_note'] = 'Notification note';
$string['ph_observations'] = 'Observations';
$string['ph_open_date'] = 'Opening date';
$string['ph_rejected_count'] = 'Number of rejected items';
$string['ph_rejected_docs'] = 'Rejected documents list';
$string['ph_rejection_reason'] = 'Rejection reason';
$string['ph_resubmit_deadline'] = 'Resubmit deadline';
$string['ph_review_summary'] = 'Review summary';
$string['ph_reviewer_name'] = 'Reviewer name';
$string['ph_selection_notes'] = 'Selection notes';
$string['ph_site_name'] = 'Site name';
$string['ph_site_url'] = 'Site URL';
$string['ph_submit_date'] = 'Submit date';
$string['ph_user_email'] = 'User email';
$string['ph_user_firstname'] = 'User first name';
$string['ph_user_fullname'] = 'User full name';
$string['ph_user_lastname'] = 'User last name';
$string['ph_vacancy_code'] = 'Vacancy code';
$string['ph_vacancy_description'] = 'Vacancy description';
$string['ph_vacancy_title'] = 'Vacancy title';
$string['ph_vacancy_url'] = 'Vacancy URL';
$string['ph_waitlist_position'] = 'Waitlist position';

// =============================================================================
// SIGNUP STRINGS
// =============================================================================

$string['signup_academic_header'] = 'Academic Information';
$string['signup_account_header'] = 'Account Information';
$string['signup_already_account'] = 'Already have an account?';
$string['signup_applying_for'] = 'Applying for';
$string['signup_birthdate'] = 'Date of birth';
$string['signup_birthdate_minage'] = 'You must be at least 18 years old';
$string['signup_check_spam'] = 'Please check your spam folder if you don\'t receive the email';
$string['signup_company_help'] = 'Select the company or campus you are applying to';
$string['signup_companyinfo'] = 'Company information';
$string['signup_contactinfo'] = 'Contact information';
$string['signup_createaccount'] = 'Create account';
$string['signup_dataaccuracy_accept'] = 'I certify that all information provided is accurate';
$string['signup_dataaccuracy_required'] = 'You must certify the accuracy of your information';
$string['signup_datatreatment_accept'] = 'I accept the data treatment policy';
$string['signup_datatreatment_required'] = 'You must accept the data treatment policy';
$string['signup_degree_title'] = 'Degree title';
$string['signup_department_region'] = 'Department/Region';
$string['signup_doctype'] = 'Document type';
$string['signup_doctype_cc'] = 'Colombian ID (CC)';
$string['signup_doctype_ce'] = 'Foreign ID (CE)';
$string['signup_doctype_passport'] = 'Passport';
$string['signup_doctype_pep'] = 'Special Stay Permit (PEP)';
$string['signup_doctype_ppt'] = 'Temporary Protection Permit (PPT)';
$string['signup_edu_doctor'] = 'Doctor';
$string['signup_edu_doctorate'] = 'Doctorate';
$string['signup_edu_especialista'] = 'Specialist';
$string['signup_edu_highschool'] = 'High School';
$string['signup_edu_magister'] = 'Magister';
$string['signup_edu_masters'] = 'Masters';
$string['signup_edu_postdoctorate'] = 'Post-doctorate';
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
$string['signup_email_instructions_title'] = 'Email confirmation instructions';
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
$string['signup_idnumber_exists_as_user'] = 'This ID number already exists as a user';
$string['signup_idnumber_tooshort'] = 'ID number is too short';
$string['signup_intro'] = 'Create your account to start applying for vacancies';
$string['signup_personalinfo'] = 'Personal information';
$string['signup_phone_home'] = 'Home phone';
$string['signup_phone_mobile'] = 'Mobile phone';
$string['signup_privacy_text'] = 'Privacy policy text';
$string['signup_professional_profile'] = 'Professional profile';
$string['signup_progress'] = 'Registration progress';
$string['signup_required_fields'] = 'Required fields';
$string['signup_step_academic'] = 'Academic';
$string['signup_step_account'] = 'Account';
$string['signup_step_confirm'] = 'Confirm';
$string['signup_step_contact'] = 'Contact';
$string['signup_step_personal'] = 'Personal';
$string['signup_success_message'] = 'Your account has been created successfully';
$string['signup_success_title'] = 'Registration successful';
$string['signup_terms_accept'] = 'I accept the terms and conditions';
$string['signup_terms_required'] = 'You must accept the terms and conditions';
$string['signup_termsheader'] = 'Terms and conditions';
$string['signup_title'] = 'Sign up';
$string['signup_username_is_idnumber'] = 'Your username will be your ID number';

// =============================================================================
// PROFILE STRINGS
// =============================================================================

$string['personalinfo'] = 'Personal information';
$string['basicinfo'] = 'Basic information';
$string['education'] = 'Education';
$string['educationlevel'] = 'Education level';
$string['completeprofile_required'] = 'Please complete your profile to continue';
$string['completerequiredfields'] = 'Please complete all required fields';
$string['profilereview'] = 'Profile review';
$string['profilereview_info'] = 'Review and update your profile information';
$string['updateprofile_intro'] = 'Update your profile information';
$string['updateprofile_submit'] = 'Update profile';
$string['updateprofile_success'] = 'Profile updated successfully';
$string['updateprofile_title'] = 'Update profile';

// =============================================================================
// PASSWORD STRINGS
// =============================================================================

$string['password'] = 'Password';
$string['currentpassword'] = 'Current password';
$string['newpassword'] = 'New password';
$string['confirmpassword'] = 'Confirm password';
$string['passwordsdiffer'] = 'Passwords do not match';
$string['currentpassword_invalid'] = 'Current password is invalid';
$string['currentpassword_required'] = 'Current password is required';
$string['password_change_optional'] = 'Password change is optional';
$string['password_updated'] = 'Password updated successfully';
$string['update_username'] = 'Update username';
$string['update_username_desc'] = 'Change username to match ID number';
$string['username_differs_idnumber'] = 'Username differs from ID number';
$string['username_updated'] = 'Username updated successfully';

// =============================================================================
// ADDITIONAL REPORT STRINGS
// =============================================================================

$string['reports_desc'] = 'View reports and analytics';
$string['reportsanddata'] = 'Reports and data';
$string['reporttypes'] = 'Report types';
$string['reportoverview'] = 'Report overview';
$string['reportapplications'] = 'Applications report';
$string['reportdocuments'] = 'Documents report';
$string['reportreviewers'] = 'Reviewers report';
$string['reporttimeline'] = 'Timeline report';
$string['viewreports'] = 'View reports';
$string['dailyapplications'] = 'Daily applications';
$string['bydocumenttype'] = 'By document type';
$string['pendingbytype'] = 'Pending by type';
$string['selectionrate'] = 'Selection rate';
$string['approvalrate'] = 'Approval rate';

// =============================================================================
// DATA EXPORT STRINGS
// =============================================================================

$string['exportdata_desc'] = 'Export application and vacancy data';
$string['exportoptions'] = 'Export options';
$string['exportdownload'] = 'Download export';
$string['exporterror'] = 'Export error';
$string['exportwarning_files'] = 'Warning: File exports may take some time';
$string['fullexport'] = 'Full export';
$string['fullexport_info'] = 'Export all data including documents';
$string['datatorexport'] = 'Data to export';
$string['selectfieldstoexport'] = 'Select fields to export';
$string['encoding'] = 'Encoding';

// =============================================================================
// DASHBOARD STRINGS
// =============================================================================

$string['dashboard_admin_welcome'] = 'Welcome to the administration dashboard';
$string['dashboard_applicant_welcome'] = 'Welcome to your applicant dashboard';
$string['dashboard_manager_welcome'] = 'Welcome to the management dashboard';
$string['dashboard_reviewer_welcome'] = 'Welcome to your reviewer dashboard';
$string['welcometojobboard'] = 'Welcome to Job Board';
$string['quicktips'] = 'Quick tips';
$string['tip_checkdocs'] = 'Check your documents regularly';
$string['tip_deadline'] = 'Pay attention to deadlines';
$string['tip_saveoften'] = 'Save your progress often';
$string['needhelp'] = 'Need help?';
$string['needsattention'] = 'Needs attention';
$string['pending_docs_alert'] = 'You have pending documents to upload';
$string['pending_reviews_alert'] = 'You have pending reviews';
$string['deadlinewarning'] = 'Deadline warning';
$string['deadlinewarning_title'] = 'Approaching deadline';
$string['deadlineprogress'] = 'Deadline progress';
$string['progress'] = 'Progress';
$string['progressindicator'] = 'Progress indicator';

// =============================================================================
// NOTIFICATION STRINGS
// =============================================================================

$string['notification_'] = 'Notification';
$string['alerts'] = 'Alerts';

// =============================================================================
// FILTER & SEARCH STRINGS
// =============================================================================

$string['filterform'] = 'Filter form';
$string['clearfilters'] = 'Clear filters';
$string['resetfilters'] = 'Reset filters';
$string['trydifferentfilters'] = 'Try different filters';
$string['searchapplicant'] = 'Search applicant';
$string['searchbyusername'] = 'Search by username';
$string['searchuser'] = 'Search user';
$string['searchusers'] = 'Search users';
$string['searchusersplaceholder'] = 'Search by name, email, or ID...';
$string['clickfordetails'] = 'Click for details';

// =============================================================================
// TABLE STRINGS
// =============================================================================

$string['datatable'] = 'Data table';
$string['recordsperpage'] = 'Records per page';
$string['showingxofy'] = 'Showing {$a->from} to {$a->to} of {$a->total}';
$string['showingxtoy'] = 'Showing {$a->x} to {$a->y}';
$string['pagination'] = 'Pagination';
$string['thactions'] = 'Actions';
$string['thcode'] = 'Code';
$string['thstatus'] = 'Status';
$string['thtitle'] = 'Title';

// =============================================================================
// SETTINGS STRINGS
// =============================================================================

$string['pluginsettings_desc'] = 'Configure Job Board plugin settings';
$string['generalsettings'] = 'General settings';
$string['securitysettings'] = 'Security settings';
$string['systemconfiguration'] = 'System configuration';
$string['configuration'] = 'Configure';
$string['configure'] = 'Configure';
$string['enablepublicpage'] = 'Enable public page';
$string['enablepublicpage_desc'] = 'Allow public access to vacancy listing';
$string['enableselfregistration'] = 'Enable self-registration';
$string['enableselfregistration_desc'] = 'Allow users to register themselves';
$string['publicpagesettings'] = 'Public page settings';
$string['publicpagesettings_desc'] = 'Configure the public vacancy page';
$string['publicpagetitle'] = 'Public page title';
$string['publicpagetitle_desc'] = 'Title displayed on the public vacancy page';
$string['publicpagedescription'] = 'Public page description';
$string['publicpagedescription_desc'] = 'Description shown on the public vacancy page';
$string['publicpagedesc'] = 'View public vacancies';
$string['viewpublicpage'] = 'View public page';
$string['viewpublicvacancies'] = 'View public vacancies';
$string['dataretentiondays'] = 'Data retention days';
$string['enableapi'] = 'Enable API';
$string['enableencryption'] = 'Enable encryption';
$string['datatreatmentpolicytitle'] = 'Data treatment policy';
$string['defaultdatatreatmentpolicy'] = 'Default data treatment policy';

// =============================================================================
// RECAPTCHA SETTINGS
// =============================================================================

$string['recaptchasettings'] = 'reCAPTCHA settings';
$string['recaptchasettings_desc'] = 'Configure reCAPTCHA for form protection';
$string['recaptcha_enabled'] = 'Enable reCAPTCHA';
$string['recaptcha_enabled_desc'] = 'Enable reCAPTCHA verification on forms';
$string['recaptcha_failed'] = 'reCAPTCHA verification failed';
$string['recaptcha_required'] = 'reCAPTCHA is required';
$string['recaptcha_secretkey'] = 'reCAPTCHA secret key';
$string['recaptcha_secretkey_desc'] = 'Your reCAPTCHA secret key';
$string['recaptcha_sitekey'] = 'reCAPTCHA site key';
$string['recaptcha_sitekey_desc'] = 'Your reCAPTCHA site key';
$string['recaptcha_v2'] = 'reCAPTCHA v2';
$string['recaptcha_v3'] = 'reCAPTCHA v3';
$string['recaptcha_v3_threshold'] = 'reCAPTCHA v3 threshold';
$string['recaptcha_v3_threshold_desc'] = 'Score threshold for reCAPTCHA v3 (0.0-1.0)';
$string['recaptcha_version'] = 'reCAPTCHA version';
$string['recaptcha_version_desc'] = 'Select reCAPTCHA version to use';

// =============================================================================
// DOCUMENT VALIDITY SETTINGS
// =============================================================================

$string['antecedentesmaxdays'] = 'Maximum age for background checks (days)';
$string['epsmaxdays'] = 'Maximum age for EPS certificate (days)';
$string['pensionmaxdays'] = 'Maximum age for pension certificate (days)';

// =============================================================================
// CONSENT STRINGS
// =============================================================================

$string['consentaccepttext'] = 'I accept the terms and consent to data processing';
$string['consentgiven'] = 'Consent given';
$string['consentheader'] = 'Data Processing Consent';
$string['consentrequired'] = 'Consent is required';
$string['declaration'] = 'Declaration';
$string['declarationaccept'] = 'I accept the declaration';
$string['declarationrequired'] = 'Declaration is required';
$string['declarationtext'] = 'I declare that all information provided is accurate and complete';
$string['step_consent'] = 'Consent';
$string['step_coverletter'] = 'Cover letter';
$string['step_documents'] = 'Documents';
$string['step_profile'] = 'Profile';
$string['step_submit'] = 'Submit';

// =============================================================================
// ADDITIONAL CAPABILITY STRINGS
// =============================================================================

$string['capabilities'] = 'Capabilities';
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
// ROLE STRINGS
// =============================================================================

$string['role'] = 'Role';
$string['role_administrator'] = 'Administrator';
$string['role_applicant'] = 'Applicant';
$string['role_chair'] = 'Committee chair';
$string['role_committee'] = 'Committee member';
$string['role_committee_desc'] = 'Member of selection committee';
$string['role_coordinator'] = 'Coordinator';
$string['role_coordinator_desc'] = 'Vacancy coordinator';
$string['role_evaluator'] = 'Evaluator';
$string['role_lead_reviewer'] = 'Lead reviewer';
$string['role_manager'] = 'Manager';
$string['role_observer'] = 'Observer';
$string['role_reviewer'] = 'Reviewer';
$string['role_reviewer_desc'] = 'Document reviewer';
$string['role_secretary'] = 'Secretary';
$string['rolechanged'] = 'Role changed successfully';
$string['rolechangeerror'] = 'Error changing role';
$string['rolenotcreated'] = 'Role not created';
$string['changerole'] = 'Change role';
$string['selectroletoassign'] = 'Select role to assign';
$string['manageroles'] = 'Manage roles';
$string['manageroles_desc'] = 'Configure user roles and permissions';
$string['manageusers'] = 'Manage users';

// =============================================================================
// ADMIN STATISTICS
// =============================================================================

$string['adminstatistics'] = 'Administration statistics';
$string['vacancystatistics'] = 'Vacancy statistics';

// =============================================================================
// FEATURE DESCRIPTIONS
// =============================================================================

$string['features'] = 'Features';
$string['feature_assign_reviewers'] = 'Assign reviewers to applications';
$string['feature_create_convocatorias'] = 'Create and manage convocatorias';
$string['feature_create_vacancies'] = 'Create and publish vacancies';
$string['feature_import_export'] = 'Import and export data';
$string['feature_manage_vacancies'] = 'Manage all vacancies';
$string['feature_publish_vacancies'] = 'Publish vacancies';
$string['feature_review_documents'] = 'Review applicant documents';
$string['feature_track_applications'] = 'Track application status';
$string['feature_validate_applications'] = 'Validate applications';

// =============================================================================
// RECOMMENDATION STRINGS
// =============================================================================

$string['recommend_'] = 'Recommendation';
$string['recommend_furtherreview'] = 'Recommend for further review';
$string['recommend_hire'] = 'Recommend to hire';
$string['recommend_reject'] = 'Recommend to reject';

// =============================================================================
// IOMAD STRINGS
// =============================================================================

$string['iomad_department'] = 'IOMAD Department';
$string['iomadoptions'] = 'IOMAD options';
$string['iomadsettings'] = 'IOMAD settings';
$string['multi_tenant'] = 'Multi-tenant';
$string['allcompanies'] = 'All companies';
$string['alldepartments'] = 'All departments';
$string['selectcompany'] = 'Select company';
$string['selectdepartment'] = 'Select department';
$string['selectfaculty'] = 'Select faculty';
$string['selectmodality'] = 'Select modality';
$string['selecttype'] = 'Select type';
$string['selectuser'] = 'Select user';
$string['selectusers'] = 'Select users';
$string['selectatleastone'] = 'Select at least one';
$string['selectbackgrounddocs'] = 'Select background documents';
$string['selectidentitydocs'] = 'Select identity documents';
$string['selectmultiplehelp'] = 'Hold Ctrl/Cmd to select multiple';
$string['selectacountry'] = 'Select a country';

// =============================================================================
// IMPORT STRINGS
// =============================================================================

$string['importcomplete'] = 'Import complete';
$string['importdata'] = 'Import data';
$string['importdata_desc'] = 'Import vacancies and applications from file';
$string['importedapplications'] = 'Applications imported';
$string['importedconvocatorias'] = 'Convocatorias imported';
$string['importeddoctypes'] = 'Document types imported';
$string['importeddocuments'] = 'Documents imported';
$string['importedemails'] = 'Email templates imported';
$string['importedexemptions'] = 'Exemptions imported';
$string['importedfiles'] = 'Files imported';
$string['importednote'] = 'Import note';
$string['importedsettings'] = 'Settings imported';
$string['importedskipped'] = 'Records skipped';
$string['importedsuccess'] = 'Records imported successfully';
$string['importedvacancies'] = 'Vacancies imported';
$string['importerror'] = 'Import error';
$string['importerror_alreadyexempt'] = 'User already has exemption';
$string['importerror_createfailed'] = 'Failed to create record';
$string['importerror_usernotfound'] = 'User not found';
$string['importerror_vacancyexists'] = 'Vacancy already exists';
$string['importerrors'] = 'Import errors';
$string['importingfrom'] = 'Importing from';
$string['importinstructions'] = 'Import instructions';
$string['importinstructionstext'] = 'Follow these instructions to import data';
$string['importoptions'] = 'Import options';
$string['importresults'] = 'Import results';
$string['importupload'] = 'Upload import file';
$string['importvacancies'] = 'Import vacancies';
$string['importvacancies_desc'] = 'Import vacancies from CSV file';
$string['importvacancies_help'] = 'Help with vacancy import';
$string['importwarning'] = 'Import warning';

// =============================================================================
// CSV IMPORT STRINGS
// =============================================================================

$string['csvcolumn_code'] = 'Code column';
$string['csvcolumn_contracttype'] = 'Contract type column';
$string['csvcolumn_courses'] = 'Courses column';
$string['csvcolumn_faculty'] = 'Faculty column';
$string['csvcolumn_location'] = 'Location column';
$string['csvcolumn_modality'] = 'Modality column';
$string['csvcolumn_profile'] = 'Profile column';
$string['csvcolumn_program'] = 'Program column';
$string['csvdelimiter'] = 'CSV delimiter';
$string['csvexample'] = 'CSV example';
$string['csvexample_desc'] = 'Example of CSV file format';
$string['csvexample_tip'] = 'Tip for CSV format';
$string['csvfile'] = 'CSV file';
$string['csvformat'] = 'CSV format';
$string['csvformat_desc'] = 'Description of CSV format requirements';
$string['csvimporterror'] = 'CSV import error';
$string['csvinvalidtype'] = 'Invalid type in CSV';
$string['csvlineerror'] = 'Error on line {$a}';
$string['csvusernotfound'] = 'User not found in CSV';
$string['downloadcsvtemplate'] = 'Download CSV template';
$string['requiredcolumns'] = 'Required columns';
$string['optionalcolumns'] = 'Optional columns';
$string['samplecsv'] = 'Sample CSV';

// =============================================================================
// MIGRATION STRINGS
// =============================================================================

$string['migrateplugin'] = 'Migrate plugin';
$string['migrateplugin_desc'] = 'Migrate data from previous version';
$string['migrationfile'] = 'Migration file';
$string['migrationinfo_desc'] = 'Migration information';
$string['migrationinfo_title'] = 'Migration information';
$string['invalidmigrationfile'] = 'Invalid migration file';
$string['dryrunmode'] = 'Dry run mode';
$string['dryrunresults'] = 'Dry run results';
$string['overwriteexisting'] = 'Overwrite existing';
$string['updateexisting'] = 'Update existing';

// =============================================================================
// TASK STRINGS
// =============================================================================

$string['task:checkclosingvacancies'] = 'Check closing vacancies';
$string['task:cleanupolddata'] = 'Clean up old data';
$string['task:sendnotifications'] = 'Send notifications';

// =============================================================================
// EVENT STRINGS
// =============================================================================

$string['event:applicationcreated'] = 'Application created';
$string['event:documentuploaded'] = 'Document uploaded';
$string['event:statuschanged'] = 'Status changed';
$string['event:vacancyclosed'] = 'Vacancy closed';
$string['event:vacancycreated'] = 'Vacancy created';
$string['event:vacancydeleted'] = 'Vacancy deleted';
$string['event:vacancypublished'] = 'Vacancy published';
$string['event:vacancyupdated'] = 'Vacancy updated';

// =============================================================================
// FILE UPLOAD STRINGS
// =============================================================================

$string['allowedformats'] = 'Allowed formats';
$string['allowedformats_desc'] = 'File formats allowed for upload';
$string['choosefiles'] = 'Choose files';
$string['filename'] = 'File name';
$string['files'] = 'Files';
$string['uploadfile'] = 'Upload file';
$string['uploadform'] = 'Upload form';
$string['uploading'] = 'Uploading...';
$string['uploadnewfile'] = 'Upload new file';
$string['uploadfailed'] = 'Upload failed';
$string['uploaddocsreminder'] = 'Remember to upload all required documents';
$string['resubmit'] = 'Resubmit';
$string['reuploadhelp'] = 'Re-upload your document to fix issues';

// =============================================================================
// CONVERSION STRINGS
// =============================================================================

$string['conversionfailed'] = 'Conversion failed';
$string['conversioninprogress'] = 'Conversion in progress';
$string['conversionpending'] = 'Conversion pending';
$string['conversionready'] = 'Conversion ready';
$string['conversionwait'] = 'Please wait for conversion';

// =============================================================================
// USER STRINGS
// =============================================================================

$string['user'] = 'User';
$string['users'] = 'Users';
$string['useridentifier'] = 'User identifier';
$string['usernotfound'] = 'User not found';

// =============================================================================
// DATE FORMAT STRINGS
// =============================================================================

$string['strftimedate'] = '%d %B %Y';
$string['strftimedateshort'] = '%d/%m/%Y';
$string['strftimedatetime'] = '%d %B %Y, %H:%M';

// =============================================================================
// ERROR STRINGS
// =============================================================================

$string['error:alreadyapplied'] = 'You have already applied to this vacancy';
$string['error:applicationlimitreached'] = 'Application limit reached';
$string['error:cannotdelete_hasapplications'] = 'Cannot delete: has applications';
$string['error:cannotdeleteconvocatoria'] = 'Cannot delete convocatoria';
$string['error:cannotreopenconvocatoria'] = 'Cannot reopen convocatoria';
$string['error:codealreadyexists'] = 'Code already exists';
$string['error:codeexists'] = 'Code already exists';
$string['error:consentrequired'] = 'Consent is required';
$string['error:convocatoriacodeexists'] = 'Convocatoria code already exists';
$string['error:convocatoriadatesinvalid'] = 'Convocatoria dates are invalid';
$string['error:convocatoriahasnovacancies'] = 'Convocatoria has no vacancies';
$string['error:convocatoriarequired'] = 'Convocatoria is required';
$string['error:doctypeinuse'] = 'Document type is in use';
$string['error:invalidage'] = 'Invalid age';
$string['error:invalidcode'] = 'Invalid code';
$string['error:invaliddates'] = 'Invalid dates';
$string['error:invalidpublicationtype'] = 'Invalid publication type';
$string['error:invalidstatus'] = 'Invalid status';
$string['error:invalidurl'] = 'Invalid URL';
$string['error:occasionalrequiresexperience'] = 'Occasional contract requires experience';
$string['error:pastdate'] = 'Date is in the past';
$string['error:requiredfield'] = 'Required field';
$string['error:schedulingconflict'] = 'Scheduling conflict';
$string['error:singleapplicationonly'] = 'Only one application allowed';
$string['error:vacancyclosed'] = 'Vacancy is closed';
$string['error:vacancynotfound'] = 'Vacancy not found';
$string['errors'] = 'Errors';
$string['noobservations'] = 'No observations';
$string['nohistory'] = 'No history';
$string['hasnote'] = 'Has note';
$string['optionalnotes'] = 'Optional notes';
$string['additionalnotes'] = 'Additional notes';
$string['notes_desc'] = 'Additional notes or comments';

// =============================================================================
// VALIDATION STRINGS
// =============================================================================

$string['invalidemail'] = 'Invalid email address';
$string['emailexists'] = 'Email already exists';
$string['emailnotmatch'] = 'Emails do not match';
$string['emailagain'] = 'Enter email again';
$string['signaturetoooshort'] = 'Signature is too short';
$string['maximumchars'] = 'Maximum {$a} characters';
$string['digitalsignature'] = 'Digital signature';

// =============================================================================
// SHARE STRINGS
// =============================================================================

$string['share'] = 'Share';
$string['sharepage'] = 'Share page';
$string['shareonfacebook'] = 'Share on Facebook';
$string['shareonlinkedin'] = 'Share on LinkedIn';
$string['shareontwitter'] = 'Share on Twitter';

// =============================================================================
// WORK EXPERIENCE STRINGS
// =============================================================================

$string['courses'] = 'Courses';
$string['institutionname'] = 'Institution name';

// =============================================================================
// ADDITIONAL MISC STRINGS
// =============================================================================

$string['cliunknowoption'] = 'Unknown CLI option';
$string['install_defaults'] = 'Install defaults';
$string['createcompanies'] = 'Create companies';
$string['savechanges'] = 'Save changes';
$string['saveresults'] = 'Save results';
$string['changessaved'] = 'Changes saved';
$string['confirmcancel'] = 'Are you sure you want to cancel?';
$string['confirmpublish'] = 'Are you sure you want to publish?';
$string['confirmclose'] = 'Are you sure you want to close?';
$string['reopen'] = 'Reopen';
$string['confirmreopen'] = 'Are you sure you want to reopen?';
$string['publish'] = 'Publish';
$string['unpublish'] = 'Unpublish';
$string['confirmunpublish'] = 'Are you sure you want to unpublish?';
$string['explore'] = 'Explore';
$string['remove'] = 'Remove';
$string['revoke'] = 'Revoke';
$string['desirable'] = 'Desirable';
$string['example'] = 'Example';
$string['help'] = 'Help';
$string['externalurl'] = 'External URL';
$string['generatedon'] = 'Generated on';
$string['workflowactions'] = 'Workflow actions';
$string['workflowmanagement'] = 'Workflow management';
$string['allvalidated'] = 'All validated';
$string['alldocsreviewed'] = 'All documents reviewed';
$string['gendercondition'] = 'Gender condition';
$string['menonly'] = 'Men only';
$string['womenonly'] = 'Women only';
$string['age_exempt_notice'] = 'Age exemption notice';
$string['ageexemptionthreshold'] = 'Age exemption threshold';
$string['doc_condition_iser_exempt'] = 'ISER exempt condition';
$string['doc_condition_men_only'] = 'Men only condition';
$string['doc_condition_profession_exempt'] = 'Profession exempt condition';
$string['doc_condition_women_only'] = 'Women only condition';
$string['multipledocs_'] = 'Multiple documents';
$string['multipledocs_notice'] = 'Multiple documents can be uploaded';
$string['inputtype'] = 'Input type';
$string['inputtype_file'] = 'File upload';
$string['inputtype_number'] = 'Number';
$string['inputtype_text'] = 'Text';
$string['inputtype_url'] = 'URL';
$string['defaultstatus'] = 'Default status';
$string['publicationtype'] = 'Publication type';
$string['publicationtype:'] = 'Publication type';
$string['publicationtype:internal'] = 'Internal';
$string['publicationtype:public'] = 'Public';
$string['allstatuses'] = 'All statuses';

// =============================================================================
// DATA EXPORT PRIVACY
// =============================================================================

$string['dataexport:consent'] = 'Data export consent';
$string['dataexport:exportdate'] = 'Export date';
$string['dataexport:personal'] = 'Personal data';
$string['dataexport:title'] = 'Data export';
$string['dataexport:userinfo'] = 'User information';

// =============================================================================
// ENCRYPTION STRINGS
// =============================================================================

$string['encryption:backupinstructions'] = 'Backup encryption instructions';
$string['encryption:nokeytobackup'] = 'No encryption key to backup';

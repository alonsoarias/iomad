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
$string['managevacancies'] = 'Manage Vacancies';
$string['reviewapplications'] = 'Review Applications';
$string['reports'] = 'Reports';
$string['settings'] = 'Settings';
$string['dashboard'] = 'Dashboard';
$string['exemptions'] = 'ISER Exemptions';

// Capabilities.
$string['jobboard:createvacancy'] = 'Create vacancies';
$string['jobboard:editvacancy'] = 'Edit vacancies';
$string['jobboard:deletevacancy'] = 'Delete vacancies';
$string['jobboard:publishvacancy'] = 'Publish vacancies';
$string['jobboard:viewallvacancies'] = 'View all vacancies';
$string['jobboard:apply'] = 'Apply for vacancies';
$string['jobboard:viewownapplications'] = 'View own applications';
$string['jobboard:viewallapplications'] = 'View all applications';
$string['jobboard:reviewdocuments'] = 'Review documents';
$string['jobboard:downloadanydocument'] = 'Download any document';
$string['jobboard:manageworkflow'] = 'Manage workflow';
$string['jobboard:viewreports'] = 'View reports';
$string['jobboard:exportdata'] = 'Export data';
$string['jobboard:manageexemptions'] = 'Manage ISER exemptions';
$string['jobboard:accessapi'] = 'Access REST API';
$string['jobboard:manageapitokens'] = 'Manage API tokens';
$string['jobboard:configure'] = 'Configure system';
$string['jobboard:managedoctypes'] = 'Manage document types';
$string['jobboard:manageemailtemplates'] = 'Manage email templates';

// Vacancy fields.
$string['vacancycode'] = 'Vacancy code';
$string['vacancycode_help'] = 'Unique internal code to identify the vacancy';
$string['vacancytitle'] = 'Vacancy title';
$string['vacancytitle_help'] = 'Name of the position';
$string['vacancydescription'] = 'Description';
$string['vacancydescription_help'] = 'Detailed description of the position and its functions';
$string['contracttype'] = 'Contract type';
$string['contracttype_help'] = 'Contract modality';
$string['duration'] = 'Duration';
$string['duration_help'] = 'Estimated contract duration';
$string['salary'] = 'Salary';
$string['salary_help'] = 'Compensation information (optional)';
$string['location'] = 'Location';
$string['location_help'] = 'Place where the position will be performed';
$string['department'] = 'Department/Unit';
$string['department_help'] = 'Area or department of the institution';
$string['course'] = 'Associated course';
$string['course_help'] = 'Moodle course related to this vacancy';
$string['category'] = 'Category';
$string['category_help'] = 'Related course category';
$string['company'] = 'Company/Site';
$string['company_help'] = 'Company or site (in multi-tenant environments)';
$string['opendate'] = 'Opening date';
$string['opendate_help'] = 'Date from which applications can be received';
$string['closedate'] = 'Closing date';
$string['closedate_help'] = 'Deadline for receiving applications';
$string['positions'] = 'Number of positions';
$string['positions_help'] = 'Number of available positions';
$string['requirements'] = 'Minimum requirements';
$string['requirements_help'] = 'Indispensable requirements for the position';
$string['desirable'] = 'Desirable requirements';
$string['desirable_help'] = 'Requirements that add points but are not mandatory';
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
$string['selectcourse'] = 'Select a course';
$string['selectcategory'] = 'Select a category';
$string['selectcompany'] = 'Select a company/site';
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

// Actions.
$string['create'] = 'Create';
$string['edit'] = 'Edit';
$string['delete'] = 'Delete';
$string['view'] = 'View';
$string['publish'] = 'Publish';
$string['unpublish'] = 'Unpublish';
$string['close'] = 'Close';
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

// Messages.
$string['vacancycreated'] = 'Vacancy created successfully';
$string['vacancyupdated'] = 'Vacancy updated successfully';
$string['vacancydeleted'] = 'Vacancy deleted successfully';
$string['vacancypublished'] = 'Vacancy published successfully';
$string['vacancyclosed'] = 'Vacancy closed successfully';
$string['applicationsubmitted'] = 'Application submitted successfully';
$string['applicationwithdrawn'] = 'Application withdrawn successfully';
$string['documentuploaded'] = 'Document uploaded successfully';
$string['documentvalidated'] = 'Document validated successfully';
$string['documentrejected'] = 'Document rejected';
$string['changesaved'] = 'Changes saved successfully';

// Errors.
$string['error:vacancynotfound'] = 'Vacancy not found';
$string['error:applicationnotfound'] = 'Application not found';
$string['error:documentnotfound'] = 'Document not found';
$string['error:codeexists'] = 'A vacancy with this code already exists';
$string['error:invaliddates'] = 'Closing date must be after opening date';
$string['error:closedateexpired'] = 'Closing date has passed';
$string['error:cannotdelete'] = 'Cannot delete vacancy because it has applications';
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
$string['documentsettings'] = 'Document settings';
$string['notificationsettings'] = 'Notification settings';
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

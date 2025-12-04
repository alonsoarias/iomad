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
$string['coverletter_help'] = 'Optional cover letter to accompany your application';
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
$string['publicationtype_help'] = 'Public vacancies are visible to everyone, including unauthenticated users. Internal vacancies are only visible to authenticated users of the organization.';
$string['publicationtype:public'] = 'Public';
$string['publicationtype:internal'] = 'Internal';

// Public page.
$string['publicvacancies'] = 'Job Opportunities';
$string['publicpagetitle'] = 'Job Opportunities';
$string['publicpagetitle_default'] = 'Job Opportunities';
$string['vacanciesfound'] = '{$a} vacancies found';
$string['novacanciesfound'] = 'No vacancies found matching your criteria.';
$string['searchplaceholder'] = 'Search by title, code, or description...';
$string['viewdetails'] = 'View Details';
$string['loginandapply'] = 'Login to Apply';
$string['closesin'] = 'Closes in {$a} days';
$string['closeson'] = 'Closes on';
$string['wanttoapply'] = 'Want to apply?';
$string['createaccounttoapply'] = 'Create an account or log in to apply for vacancies.';
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
$string['allowmultipleapplications'] = 'Allow multiple applications';
$string['allowmultipleapplications_desc'] = 'Allow users to apply for multiple vacancies simultaneously.';
$string['maxactiveapplications'] = 'Maximum active applications';
$string['maxactiveapplications_desc'] = 'Maximum number of active applications per user (0 = unlimited). Only applies when multiple applications are allowed.';

// Application limit errors.
$string['error:multipleapplicationsnotallowed'] = 'You can only have one active application at a time. Please withdraw your current application before applying for a new vacancy.';
$string['error:applicationlimitreached'] = 'You have reached the maximum number of active applications ({$a}). Please wait for your current applications to be processed or withdraw one before applying for a new vacancy.';
$string['error:publicpagedisabled'] = 'The public vacancies page is disabled.';
$string['error:loginrequiredforinternal'] = 'You must be logged in to view internal vacancies.';

// New capabilities.
$string['jobboard:viewpublicvacancies'] = 'View public vacancies';
$string['jobboard:viewinternalvacancies'] = 'View internal vacancies';
$string['jobboard:unlimitedapplications'] = 'Bypass application limits';

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
$string['digitalsignature_help'] = 'Enter your full legal name as it appears on your official documents. This serves as your digital signature for this application.';

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
$string['datefrom'] = 'From';
$string['dateto'] = 'To';
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
$string['pendingdocuments'] = 'Pending Documents';
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
$string['pendingvalidation'] = 'Pending validation';
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
$string['availablereviewers'] = 'Available reviewers';
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
$string['activeexemptions'] = 'Active exemptions';
$string['expiredexemptions'] = 'Expired exemptions';
$string['revokedexemptions'] = 'Revoked exemptions';
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
$string['row'] = 'Row';
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
$string['loginrequiredtoapply'] = 'You must log in to apply for vacancies.';

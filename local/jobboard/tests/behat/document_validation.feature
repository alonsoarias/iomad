@local @local_jobboard
Feature: Document validation
  In order to process job applications
  As a document reviewer
  I need to validate or reject uploaded documents

  Background:
    Given the following "users" exist:
      | username   | firstname | lastname | email                  |
      | applicant1 | John      | Smith    | applicant1@example.com |
      | reviewer1  | Review    | User     | reviewer1@example.com  |
    And the following "roles" exist:
      | shortname   | name        |
      | jobreviewer | Job Reviewer|
    And the following "role assigns" exist:
      | user      | role        | contextlevel | reference |
      | reviewer1 | jobreviewer | System       |           |
    And the following "permission overrides" exist:
      | capability                      | permission | role        | contextlevel | reference |
      | local/jobboard:reviewdocuments  | Allow      | jobreviewer | System       |           |
      | local/jobboard:manageworkflow   | Allow      | jobreviewer | System       |           |
    And the following "local_jobboard_vacancies" exist:
      | code   | title                | status    | opendate  | closedate    |
      | VAC001 | Professor of Physics | published | ##today## | ##+30 days## |
    And the following "local_jobboard_applications" exist:
      | vacancy | user       | status       |
      | VAC001  | applicant1 | under_review |
    And the following "local_jobboard_documents" exist:
      | application | documenttype | filename    |
      | 1           | cedula       | cedula.pdf  |
      | 1           | rut          | rut.pdf     |

  @javascript
  Scenario: View application documents
    Given I log in as "reviewer1"
    And I navigate to "Job Board > Manage Vacancies" in site administration
    When I click on "Applications" "link" in the "VAC001" "table_row"
    And I click on "View" "link" in the "John Smith" "table_row"
    Then I should see "Uploaded documents"
    And I should see "Cédula de Ciudadanía"
    And I should see "cedula.pdf"
    And I should see "RUT"
    And I should see "rut.pdf"
    And I should see "Pending validation" in the "cedula.pdf" "table_row"

  @javascript
  Scenario: Validate a document
    Given I log in as "reviewer1"
    And I navigate to "Job Board > Manage Vacancies" in site administration
    When I click on "Applications" "link" in the "VAC001" "table_row"
    And I click on "View" "link" in the "John Smith" "table_row"
    And I click on "Validate" "link" in the "cedula.pdf" "table_row"
    Then I should see "Validate document"
    And I should see "Document type"
    And I should see "Cédula de Ciudadanía"
    # Complete validation checklist items
    And I should see "Validation checklist"
    And I set the field "notes_approve" to "Document verified successfully"
    When I click on "Approve document" "button"
    Then I should see "Document validated successfully"
    And I should see "Validated" in the "cedula.pdf" "table_row"

  @javascript
  Scenario: Reject a document
    Given I log in as "reviewer1"
    And I navigate to "Job Board > Manage Vacancies" in site administration
    When I click on "Applications" "link" in the "VAC001" "table_row"
    And I click on "View" "link" in the "John Smith" "table_row"
    And I click on "Validate" "link" in the "rut.pdf" "table_row"
    And I set the field "rejectreason" to "expired"
    And I set the field "notes_reject" to "Document is more than 6 months old"
    When I click on "Reject document" "button"
    Then I should see "Document rejected"
    And I should see "Rejected" in the "rut.pdf" "table_row"
    And I should see "Document expired"

  @javascript
  Scenario: Change application status after all documents validated
    Given the following "local_jobboard_doc_validations" exist:
      | document | isvalid | notes          |
      | 1        | 1       | Cedula OK      |
      | 2        | 1       | RUT OK         |
    And I log in as "reviewer1"
    And I navigate to "Job Board > Manage Vacancies" in site administration
    When I click on "Applications" "link" in the "VAC001" "table_row"
    And I click on "View" "link" in the "John Smith" "table_row"
    Then I should see "Validated" in the "cedula.pdf" "table_row"
    And I should see "Validated" in the "rut.pdf" "table_row"
    When I set the field "Change status" to "Documents validated"
    And I set the field "Notes" to "All documents have been validated"
    And I press "Update status"
    Then I should see "Status updated successfully"
    And I should see "Documents validated"

  @javascript
  Scenario: Change application status to documents rejected
    Given the following "local_jobboard_doc_validations" exist:
      | document | isvalid | rejectreason | notes             |
      | 1        | 1       |              | Cedula OK         |
      | 2        | 0       | expired      | RUT is expired    |
    And I log in as "reviewer1"
    And I navigate to "Job Board > Manage Vacancies" in site administration
    When I click on "Applications" "link" in the "VAC001" "table_row"
    And I click on "View" "link" in the "John Smith" "table_row"
    When I set the field "Change status" to "Documents rejected"
    And I set the field "Notes" to "Please upload a current RUT"
    And I press "Update status"
    Then I should see "Status updated successfully"
    And I should see "Documents rejected"

  @javascript
  Scenario: Applicant sees document validation status
    Given the following "local_jobboard_doc_validations" exist:
      | document | isvalid | rejectreason | notes                  |
      | 1        | 1       |              | Verified               |
      | 2        | 0       | expired      | Please upload new copy |
    And I log in as "applicant1"
    And I navigate to "Job Board > My Applications" in site administration
    When I click on "View" "link" in the "Professor of Physics" "table_row"
    Then I should see "Validated" in the "cedula.pdf" "table_row"
    And I should see "Rejected" in the "rut.pdf" "table_row"
    And I should see "Document expired"

  @javascript
  Scenario: Document pending count shown in applications list
    Given I log in as "reviewer1"
    And I navigate to "Job Board > Manage Vacancies" in site administration
    When I click on "Applications" "link" in the "VAC001" "table_row"
    Then I should see "2 Pending" in the "John Smith" "table_row"

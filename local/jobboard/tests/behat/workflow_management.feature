@local @local_jobboard
Feature: Workflow management
  In order to manage the application review process
  As a manager or reviewer
  I need to assign reviewers and validate documents efficiently

  Background:
    Given the following "users" exist:
      | username   | firstname | lastname | email                  |
      | applicant1 | John      | Smith    | applicant1@example.com |
      | applicant2 | Jane      | Doe      | applicant2@example.com |
      | applicant3 | Bob       | Johnson  | applicant3@example.com |
      | reviewer1  | Alice     | Reviewer | reviewer1@example.com  |
      | reviewer2  | Carlos    | Reviewer | reviewer2@example.com  |
      | manager1   | Maria     | Manager  | manager1@example.com   |
    And the following "roles" exist:
      | shortname   | name         |
      | jobreviewer | Job Reviewer |
      | jobmanager  | Job Manager  |
    And the following "role assigns" exist:
      | user      | role        | contextlevel | reference |
      | reviewer1 | jobreviewer | System       |           |
      | reviewer2 | jobreviewer | System       |           |
      | manager1  | jobmanager  | System       |           |
    And the following "permission overrides" exist:
      | capability                      | permission | role        | contextlevel | reference |
      | local/jobboard:reviewdocuments  | Allow      | jobreviewer | System       |           |
      | local/jobboard:manageworkflow   | Allow      | jobmanager  | System       |           |
      | local/jobboard:reviewdocuments  | Allow      | jobmanager  | System       |           |
    And the following "local_jobboard_vacancies" exist:
      | code   | title                | status    | opendate  | closedate    |
      | VAC001 | Professor of Physics | published | ##today## | ##+30 days## |
    And the following "local_jobboard_applications" exist:
      | vacancy | user       | status    |
      | VAC001  | applicant1 | submitted |
      | VAC001  | applicant2 | submitted |
      | VAC001  | applicant3 | submitted |
    And the following "local_jobboard_documents" exist:
      | application | type   | filename    |
      | 1           | cedula | cedula1.pdf |
      | 1           | rut    | rut1.pdf    |
      | 2           | cedula | cedula2.pdf |
      | 2           | rut    | rut2.pdf    |
      | 3           | cedula | cedula3.pdf |

  @javascript
  Scenario: Manager assigns reviewer to applications
    Given I log in as "manager1"
    And I navigate to "Job Board > Assign Reviewers" in site administration
    Then I should see "John Smith"
    And I should see "Jane Doe"
    And I should see "Bob Johnson"
    When I click on "input[type='checkbox']" "css_element" in the "John Smith" "table_row"
    And I click on "input[type='checkbox']" "css_element" in the "Jane Doe" "table_row"
    And I set the field "reviewer" to "Alice Reviewer"
    And I press "Assign Selected"
    Then I should see "2 assigned successfully"
    And I should see "Alice Reviewer" in the "John Smith" "table_row"
    And I should see "Alice Reviewer" in the "Jane Doe" "table_row"
    And I should see "Unassigned" in the "Bob Johnson" "table_row"

  @javascript
  Scenario: Manager uses auto-assign feature
    Given I log in as "manager1"
    And I navigate to "Job Board > Assign Reviewers" in site administration
    When I press "Auto-Assign"
    Then I should see "3 assignments completed"
    # Both reviewers should have assignments
    And I should not see "Unassigned"

  @javascript
  Scenario: Reviewer sees only their assigned applications
    Given the following "local_jobboard_reviewer_assignments" exist:
      | application | reviewer  |
      | 1           | reviewer1 |
      | 2           | reviewer1 |
      | 3           | reviewer2 |
    And I log in as "reviewer1"
    When I navigate to "Job Board > My Reviews" in site administration
    Then I should see "John Smith"
    And I should see "Jane Doe"
    And I should not see "Bob Johnson"

  @javascript
  Scenario: Reviewer validates documents in bulk
    Given the following "local_jobboard_reviewer_assignments" exist:
      | application | reviewer  |
      | 1           | reviewer1 |
    And I log in as "reviewer1"
    And I navigate to "Job Board > Bulk Validation" in site administration
    Then I should see "cedula1.pdf"
    And I should see "rut1.pdf"
    When I click on "Select All" "button"
    And I press "Approve Selected"
    Then I should see "2 documents processed successfully"
    And I should see "Validated" in the "cedula1.pdf" "table_row"
    And I should see "Validated" in the "rut1.pdf" "table_row"

  @javascript
  Scenario: Reviewer rejects documents with reason
    Given the following "local_jobboard_reviewer_assignments" exist:
      | application | reviewer  |
      | 1           | reviewer1 |
    And I log in as "reviewer1"
    And I navigate to "Job Board > Bulk Validation" in site administration
    When I click on "input[type='checkbox']" "css_element" in the "cedula1.pdf" "table_row"
    And I set the field "rejectreason" to "ilegible"
    And I press "Reject Selected"
    Then I should see "1 documents processed"
    And I should see "Rejected" in the "cedula1.pdf" "table_row"

  @javascript
  Scenario: View workflow dashboard statistics
    Given the following "local_jobboard_reviewer_assignments" exist:
      | application | reviewer  |
      | 1           | reviewer1 |
      | 2           | reviewer1 |
    And the following "local_jobboard_doc_validations" exist:
      | document | isvalid | reviewer  |
      | 1        | 1       | reviewer1 |
      | 2        | 0       | reviewer1 |
    And I log in as "manager1"
    When I navigate to "Job Board > Dashboard" in site administration
    Then I should see "3" in the ".card.bg-info h2" "css_element"
    And I should see "Total Applications"
    And I should see "Application Pipeline"
    And I should see "Validation Statistics"

  @javascript
  Scenario: Reassign applications to different reviewer
    Given the following "local_jobboard_reviewer_assignments" exist:
      | application | reviewer  |
      | 1           | reviewer1 |
      | 2           | reviewer1 |
      | 3           | reviewer1 |
    And I log in as "manager1"
    And I navigate to "Job Board > Assign Reviewers" in site administration
    # Filter to show only reviewer1's assignments
    When I set the field "currentreviewer" to "Alice Reviewer"
    And I press "Filter"
    Then I should see "John Smith"
    When I click on "input[type='checkbox']" "css_element" in the "John Smith" "table_row"
    And I set the field "reviewer" to "Carlos Reviewer"
    And I press "Reassign Selected"
    Then I should see "1 reassigned successfully"

  @javascript
  Scenario: View reviewer workload
    Given the following "local_jobboard_reviewer_assignments" exist:
      | application | reviewer  |
      | 1           | reviewer1 |
      | 2           | reviewer1 |
      | 3           | reviewer2 |
    And I log in as "manager1"
    And I navigate to "Job Board > Assign Reviewers" in site administration
    Then I should see "Alice Reviewer" in the "#reviewer-workload" "css_element"
    And I should see "2" in the "Alice Reviewer" "table_row"
    And I should see "Carlos Reviewer" in the "#reviewer-workload" "css_element"
    And I should see "1" in the "Carlos Reviewer" "table_row"

  @javascript
  Scenario: Reviewer stats are tracked
    Given the following "local_jobboard_reviewer_assignments" exist:
      | application | reviewer  |
      | 1           | reviewer1 |
    And I log in as "reviewer1"
    And I navigate to "Job Board > Bulk Validation" in site administration
    When I click on "Select All" "button"
    And I press "Approve Selected"
    Then I should see "2 documents processed successfully"
    When I navigate to "Job Board > My Reviews" in site administration
    Then I should see "2" in the ".card.bg-success h2" "css_element"
    And I should see "Documents Validated"

  @javascript
  Scenario: Filter bulk validation by document type
    Given the following "local_jobboard_reviewer_assignments" exist:
      | application | reviewer  |
      | 1           | reviewer1 |
      | 2           | reviewer1 |
    And I log in as "reviewer1"
    And I navigate to "Job Board > Bulk Validation" in site administration
    Then I should see "cedula1.pdf"
    And I should see "rut1.pdf"
    And I should see "cedula2.pdf"
    And I should see "rut2.pdf"
    When I set the field "documenttype" to "cedula"
    And I press "Filter"
    Then I should see "cedula1.pdf"
    And I should see "cedula2.pdf"
    And I should not see "rut1.pdf"
    And I should not see "rut2.pdf"

  @javascript
  Scenario: View validation reports
    Given the following "local_jobboard_doc_validations" exist:
      | document | isvalid | reviewer  | rejectreason |
      | 1        | 1       | reviewer1 |              |
      | 2        | 0       | reviewer1 | ilegible     |
      | 3        | 1       | reviewer2 |              |
      | 4        | 0       | reviewer2 | vencido      |
    And I log in as "manager1"
    When I navigate to "Job Board > Reports" in site administration
    Then I should see "Reports"
    When I click on "Documents" "link"
    Then I should see "Documents by Type"
    And I should see "Top Rejection Reasons"
    When I click on "Reviewers" "link"
    Then I should see "Reviewer Performance"
    And I should see "Alice Reviewer"
    And I should see "Carlos Reviewer"

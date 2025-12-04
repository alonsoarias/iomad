@local @local_jobboard
Feature: Application submission
  In order to apply for a job vacancy
  As a user
  I need to submit an application with required documents

  Background:
    Given the following "users" exist:
      | username   | firstname | lastname | email                  |
      | applicant1 | John      | Smith    | applicant1@example.com |
      | applicant2 | Jane      | Doe      | applicant2@example.com |
      | reviewer1  | Review    | User     | reviewer1@example.com  |
    And the following "roles" exist:
      | shortname   | name        |
      | jobreviewer | Job Reviewer|
    And the following "role assigns" exist:
      | user      | role        | contextlevel | reference |
      | reviewer1 | jobreviewer | System       |           |
    And the following "permission overrides" exist:
      | capability                      | permission | role        | contextlevel | reference |
      | local/jobboard:apply            | Allow      | user        | System       |           |
      | local/jobboard:reviewdocuments  | Allow      | jobreviewer | System       |           |
      | local/jobboard:manageworkflow   | Allow      | jobreviewer | System       |           |
    And the following "local_jobboard_vacancies" exist:
      | code   | title                | status    | opendate  | closedate    |
      | VAC001 | Professor of Physics | published | ##today## | ##+30 days## |

  @javascript
  Scenario: Apply for a vacancy with all required documents
    Given I log in as "applicant1"
    And I navigate to "Job Board > Vacancies" in site administration
    When I click on "View" "link" in the "Professor of Physics" "table_row"
    And I click on "Apply" "button"
    Then I should see "Apply for vacancy"
    And I should see "Professor of Physics"
    # Fill consent section
    And I set the field "consentaccepted" to "1"
    And I set the field "Digital signature (full name)" to "John Smith"
    # Accept declaration
    And I set the field "declarationaccepted" to "1"
    When I press "Submit Application"
    Then I should see "Application submitted successfully"
    And I should see "My Applications"
    And I should see "Professor of Physics"
    And I should see "Submitted"

  @javascript
  Scenario: Cannot apply without accepting consent
    Given I log in as "applicant1"
    And I navigate to "Job Board > Vacancies" in site administration
    When I click on "View" "link" in the "Professor of Physics" "table_row"
    And I click on "Apply" "button"
    # Try to submit without consent
    And I set the field "Digital signature (full name)" to "John Smith"
    And I set the field "declarationaccepted" to "1"
    When I press "Submit Application"
    Then I should see "You must accept the data treatment policy"

  @javascript
  Scenario: Cannot apply to the same vacancy twice
    Given the following "local_jobboard_applications" exist:
      | vacancy | user       | status    |
      | VAC001  | applicant1 | submitted |
    And I log in as "applicant1"
    And I navigate to "Job Board > Vacancies" in site administration
    When I click on "View" "link" in the "Professor of Physics" "table_row"
    Then I should not see "Apply" in the ".vacancy-actions" "css_element"
    And I should see "You have already applied"

  @javascript
  Scenario: View my applications list
    Given the following "local_jobboard_vacancies" exist:
      | code   | title                  | status    | opendate  | closedate    |
      | VAC002 | Professor of Chemistry | published | ##today## | ##+30 days## |
    And the following "local_jobboard_applications" exist:
      | vacancy | user       | status       |
      | VAC001  | applicant1 | submitted    |
      | VAC002  | applicant1 | under_review |
    And I log in as "applicant1"
    When I navigate to "Job Board > My Applications" in site administration
    Then I should see "Professor of Physics"
    And I should see "Submitted"
    And I should see "Professor of Chemistry"
    And I should see "Under review"

  @javascript
  Scenario: Withdraw an application
    Given the following "local_jobboard_applications" exist:
      | vacancy | user       | status    |
      | VAC001  | applicant1 | submitted |
    And I log in as "applicant1"
    And I navigate to "Job Board > My Applications" in site administration
    When I click on "View" "link" in the "Professor of Physics" "table_row"
    And I click on "Withdraw application" "button"
    And I click on "OK" "button" in the "Confirm" "dialogue"
    Then I should see "Application withdrawn successfully"
    And I should see "Withdrawn"

  @javascript
  Scenario: Reviewer changes application status
    Given the following "local_jobboard_applications" exist:
      | vacancy | user       | status    |
      | VAC001  | applicant1 | submitted |
    And I log in as "reviewer1"
    And I navigate to "Job Board > Manage Vacancies" in site administration
    When I click on "Applications" "link" in the "VAC001" "table_row"
    Then I should see "John Smith"
    And I should see "Submitted"
    When I click on "View" "link" in the "John Smith" "table_row"
    And I set the field "Change status" to "Under review"
    And I set the field "Notes" to "Starting document review"
    And I press "Update status"
    Then I should see "Status updated successfully"
    And I should see "Under review"

  @javascript
  Scenario: Filter applications by status
    Given the following "local_jobboard_vacancies" exist:
      | code   | title                  | status    | opendate  | closedate    |
      | VAC002 | Professor of Chemistry | published | ##today## | ##+30 days## |
    And the following "local_jobboard_applications" exist:
      | vacancy | user       | status         |
      | VAC001  | applicant1 | submitted      |
      | VAC001  | applicant2 | under_review   |
    And I log in as "reviewer1"
    And I navigate to "Job Board > Manage Vacancies" in site administration
    When I click on "Applications" "link" in the "VAC001" "table_row"
    Then I should see "John Smith"
    And I should see "Jane Doe"
    When I set the field "status" to "submitted"
    And I press "Filter"
    Then I should see "John Smith"
    And I should not see "Jane Doe"

  @javascript
  Scenario: View application with ISER exemption
    Given the following "local_jobboard_exemptions" exist:
      | user       | exemptiontype   | documentref   |
      | applicant1 | historico_iser  | RES-2024-001  |
    And the following "local_jobboard_applications" exist:
      | vacancy | user       | status    | isexemption |
      | VAC001  | applicant1 | submitted | 1           |
    And I log in as "reviewer1"
    And I navigate to "Job Board > Manage Vacancies" in site administration
    When I click on "Applications" "link" in the "VAC001" "table_row"
    Then I should see "Exemption" in the "John Smith" "table_row"
    When I click on "View" "link" in the "John Smith" "table_row"
    Then I should see "ISER Exemption Applied"
    And I should see "Historic ISER Personnel"

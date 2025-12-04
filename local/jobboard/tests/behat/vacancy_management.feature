@local @local_jobboard
Feature: Vacancy management
  In order to manage job vacancies
  As a manager
  I need to create, edit, and publish vacancies

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | manager1 | Manager   | One      | manager1@example.com |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And the following "roles" exist:
      | shortname    | name          |
      | jobmanager   | Job Manager   |
    And the following "role assigns" exist:
      | user     | role       | contextlevel | reference |
      | manager1 | jobmanager | System       |           |
    And the following "permission overrides" exist:
      | capability                      | permission | role       | contextlevel | reference |
      | local/jobboard:createvacancy    | Allow      | jobmanager | System       |           |
      | local/jobboard:editvacancy      | Allow      | jobmanager | System       |           |
      | local/jobboard:deletevacancy    | Allow      | jobmanager | System       |           |
      | local/jobboard:publishvacancy   | Allow      | jobmanager | System       |           |
      | local/jobboard:viewallvacancies | Allow      | jobmanager | System       |           |

  @javascript
  Scenario: Create a new vacancy
    Given I log in as "manager1"
    And I navigate to "Job Board > Manage Vacancies" in site administration
    When I click on "New Vacancy" "link"
    And I set the following fields to these values:
      | Vacancy code  | VAC001               |
      | Vacancy title | Professor of Physics |
      | Location      | Main Campus          |
      | positions     | 2                    |
    And I press "Create"
    Then I should see "Vacancy created successfully"
    And I should see "VAC001"
    And I should see "Professor of Physics"

  @javascript
  Scenario: Edit an existing vacancy
    Given the following "local_jobboard_vacancies" exist:
      | code   | title              | status | createdby |
      | VAC002 | Professor of Math  | draft  | manager1  |
    And I log in as "manager1"
    And I navigate to "Job Board > Manage Vacancies" in site administration
    When I click on "Edit" "link" in the "VAC002" "table_row"
    And I set the field "Vacancy title" to "Senior Professor of Mathematics"
    And I press "Save"
    Then I should see "Vacancy updated successfully"
    And I should see "Senior Professor of Mathematics"

  @javascript
  Scenario: Publish a vacancy
    Given the following "local_jobboard_vacancies" exist:
      | code   | title               | status | createdby | opendate  | closedate |
      | VAC003 | Professor of History| draft  | manager1  | ##today## | ##+30 days## |
    And I log in as "manager1"
    And I navigate to "Job Board > Manage Vacancies" in site administration
    When I click on "Publish" "link" in the "VAC003" "table_row"
    And I click on "OK" "button" in the "Confirm" "dialogue"
    Then I should see "Vacancy published successfully"
    And I should see "Published" in the "VAC003" "table_row"

  @javascript
  Scenario: View vacancy list as applicant
    Given the following "local_jobboard_vacancies" exist:
      | code   | title                | status    | opendate  | closedate    |
      | VAC004 | Published Vacancy    | published | ##today## | ##+30 days## |
      | VAC005 | Draft Vacancy        | draft     | ##today## | ##+30 days## |
    And I log in as "teacher1"
    When I navigate to "Job Board > Vacancies" in site administration
    Then I should see "Published Vacancy"
    And I should not see "Draft Vacancy"

  @javascript
  Scenario: Search vacancies by title
    Given the following "local_jobboard_vacancies" exist:
      | code   | title               | status    | opendate  | closedate    |
      | VAC006 | Chemistry Professor | published | ##today## | ##+30 days## |
      | VAC007 | Biology Professor   | published | ##today## | ##+30 days## |
    And I log in as "teacher1"
    And I navigate to "Job Board > Vacancies" in site administration
    When I set the field "search" to "Chemistry"
    And I press "Filter"
    Then I should see "Chemistry Professor"
    And I should not see "Biology Professor"

  @javascript
  Scenario: Cannot delete vacancy with applications
    Given the following "local_jobboard_vacancies" exist:
      | code   | title              | status    | createdby | opendate  | closedate    |
      | VAC008 | Cannot Delete This | published | manager1  | ##today## | ##+30 days## |
    And the following "local_jobboard_applications" exist:
      | vacancy | user     | status    |
      | VAC008  | teacher1 | submitted |
    And I log in as "manager1"
    And I navigate to "Job Board > Manage Vacancies" in site administration
    Then I should not see "Delete" in the "VAC008" "table_row"

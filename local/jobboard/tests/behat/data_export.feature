@local @local_jobboard @javascript
Feature: Personal Data Export
  As a user with job applications
  I need to export my personal data
  So that I can review what information is stored about me

  Background:
    Given the following "users" exist:
      | username   | firstname | lastname | email                  |
      | applicant1 | John      | Doe      | applicant1@example.com |
      | applicant2 | Jane      | Smith    | applicant2@example.com |
    And the following "local_jobboard_vacancy" exist:
      | code    | title              | status    | companyid |
      | VAC001  | Software Developer | published | 0         |
      | VAC002  | Data Analyst       | published | 0         |
    And the following "local_jobboard_application" exist:
      | vacancyid | userid | status    | consentgiven |
      | 1         | 3      | submitted | 1            |
      | 2         | 3      | submitted | 1            |
      | 1         | 4      | submitted | 1            |

  Scenario: User can see data export option
    Given I log in as "applicant1"
    When I follow "My Applications"
    Then I should see "Export My Data"

  Scenario: User can export data as JSON
    Given I log in as "applicant1"
    And I follow "My Applications"
    When I click on "Export My Data" "link"
    Then I should see "Personal Data Export"
    And I should see "Export as JSON"
    And I should see "Export as PDF"
    When I click on "Export as JSON" "button"
    # The download should start - we verify the link exists
    Then I should not see "Error"

  Scenario: User can export data as PDF
    Given I log in as "applicant1"
    And I follow "My Applications"
    When I click on "Export My Data" "link"
    And I click on "Export as PDF" "button"
    Then I should not see "Error"

  Scenario: Exported data includes all applications
    Given I log in as "applicant1"
    And I follow "My Applications"
    When I click on "Export My Data" "link"
    Then I should see "2 applications"

  Scenario: User only sees their own data
    Given I log in as "applicant2"
    And I follow "My Applications"
    When I click on "Export My Data" "link"
    Then I should see "1 applications"

  Scenario: Export includes consent information
    Given I log in as "applicant1"
    And I follow "My Applications"
    When I click on "Export My Data" "link"
    Then I should see "Consent Records"

  Scenario: User can request data deletion
    Given I log in as "applicant1"
    And I follow "My Applications"
    When I click on "Delete My Data" "link"
    Then I should see "Request Data Deletion"
    And I should see "Are you sure you want to request deletion"
    When I click on "Confirm" "button"
    Then I should see "Your data deletion request has been submitted"

@local @local_jobboard @javascript
Feature: API Token Management
  As an administrator
  I need to manage API tokens
  So that external systems can access the Job Board API

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | admin1   | Admin     | User     | admin1@example.com   |
      | manager1 | Manager   | User     | manager1@example.com |
    And the following "roles" exist:
      | shortname     | name          | archetype |
      | tokenmanager  | Token Manager |           |
    And the following "role assigns" exist:
      | user     | role         | contextlevel | reference |
      | manager1 | tokenmanager | System       |           |
    And the following "permission overrides" exist:
      | capability                       | permission | role         | contextlevel | reference |
      | local/jobboard:manageapitokens   | Allow      | tokenmanager | System       |           |

  Scenario: Admin can access API token management page
    Given I log in as "admin"
    When I navigate to "Plugins > Local plugins > Job Board > Manage API Tokens" in site administration
    Then I should see "Manage API Tokens"
    And I should see "Create Token"
    And I should see "No API tokens have been created yet."

  Scenario: Admin can create a new API token
    Given I log in as "admin"
    And I navigate to "Plugins > Local plugins > Job Board > Manage API Tokens" in site administration
    When I click on "Create Token" "button"
    Then I should see "Token description"
    And I should see "Permissions"
    And I should see "Validity Period"
    And I should see "IP Whitelist"
    When I set the following fields to these values:
      | Token description | Test Integration Token |
    And I set the field "View vacancy listings" to "1"
    And I set the field "View vacancy details" to "1"
    And I press "Create Token"
    Then I should see "Token created successfully"
    And I should see "Your new API token"
    And I should see "Warning: This is the only time this token will be displayed"

  Scenario: Admin can view existing API tokens
    Given I log in as "admin"
    And the following "local_jobboard_api_token" exist:
      | description        | userid | permissions                            | enabled |
      | Production Token   | 2      | ["view_vacancies","view_applications"] | 1       |
      | Development Token  | 2      | ["view_vacancies"]                     | 1       |
    And I navigate to "Plugins > Local plugins > Job Board > Manage API Tokens" in site administration
    Then I should see "Production Token"
    And I should see "Development Token"
    And I should see "Active" in the "Production Token" "table_row"

  Scenario: Admin can revoke an API token
    Given I log in as "admin"
    And the following "local_jobboard_api_token" exist:
      | description    | userid | permissions          | enabled |
      | Active Token   | 2      | ["view_vacancies"]   | 1       |
    And I navigate to "Plugins > Local plugins > Job Board > Manage API Tokens" in site administration
    When I click on "Revoke" "link" in the "Active Token" "table_row"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    Then I should see "Token has been revoked"
    And I should see "Disabled" in the "Active Token" "table_row"

  Scenario: Admin can delete an API token
    Given I log in as "admin"
    And the following "local_jobboard_api_token" exist:
      | description      | userid | permissions          | enabled |
      | Token to Delete  | 2      | ["view_vacancies"]   | 1       |
    And I navigate to "Plugins > Local plugins > Job Board > Manage API Tokens" in site administration
    When I click on "Delete" "link" in the "Token to Delete" "table_row"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    Then I should see "Token has been deleted"
    And I should not see "Token to Delete"

  Scenario: User without permission cannot access token management
    Given I log in as "manager1"
    When I am on site homepage
    Then I should not see "Manage API Tokens" in the "Administration" "block"

  Scenario: Token with IP whitelist restricts access
    Given I log in as "admin"
    And I navigate to "Plugins > Local plugins > Job Board > Manage API Tokens" in site administration
    When I click on "Create Token" "button"
    And I set the following fields to these values:
      | Token description | IP Restricted Token |
      | IP Whitelist      | 192.168.1.0/24      |
    And I set the field "View vacancy listings" to "1"
    And I press "Create Token"
    Then I should see "Token created successfully"

  Scenario: Token with validity period shows correct status
    Given I log in as "admin"
    And the following "local_jobboard_api_token" exist:
      | description    | userid | permissions          | enabled | validuntil |
      | Expired Token  | 2      | ["view_vacancies"]   | 1       | 1609459200 |
    And I navigate to "Plugins > Local plugins > Job Board > Manage API Tokens" in site administration
    Then I should see "Expired" in the "Expired Token" "table_row"

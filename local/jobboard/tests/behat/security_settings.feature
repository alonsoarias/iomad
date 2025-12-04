@local @local_jobboard @javascript
Feature: Security Settings
  As an administrator
  I need to configure security settings
  So that the Job Board is properly secured

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | admin1   | Admin     | User     | admin1@example.com |

  Scenario: Admin can access security settings
    Given I log in as "admin"
    When I navigate to "Plugins > Local plugins > Job Board > Settings" in site administration
    Then I should see "Security settings"
    And I should see "Enable file encryption"
    And I should see "Enable REST API"
    And I should see "Data retention days"

  Scenario: Admin can enable file encryption
    Given I log in as "admin"
    And I navigate to "Plugins > Local plugins > Job Board > Settings" in site administration
    When I set the field "Enable file encryption" to "1"
    And I press "Save changes"
    Then I should see "Changes saved"

  Scenario: Admin can configure data retention period
    Given I log in as "admin"
    And I navigate to "Plugins > Local plugins > Job Board > Settings" in site administration
    When I set the field "Data retention days" to "730"
    And I press "Save changes"
    Then I should see "Changes saved"

  Scenario: Admin can enable REST API
    Given I log in as "admin"
    And I navigate to "Plugins > Local plugins > Job Board > Settings" in site administration
    When I set the field "Enable REST API" to "1"
    And I press "Save changes"
    Then I should see "Changes saved"

  Scenario: Admin can configure rate limiting
    Given I log in as "admin"
    And I navigate to "Plugins > Local plugins > Job Board > Settings" in site administration
    Then I should see "Rate limit"
    When I set the field "Rate limit" to "200"
    And I press "Save changes"
    Then I should see "Changes saved"

  Scenario: Admin can view audit log
    Given I log in as "admin"
    And the following "local_jobboard_audit" exist:
      | action              | entitytype  | entityid | userid | ipaddress |
      | vacancy_created     | vacancy     | 1        | 2      | 127.0.0.1 |
      | application_created | application | 1        | 3      | 127.0.0.1 |
    When I navigate to "Plugins > Local plugins > Job Board > Audit Log" in site administration
    Then I should see "vacancy_created"
    And I should see "application_created"
    And I should see "127.0.0.1"

  Scenario: Encryption settings show warning
    Given I log in as "admin"
    And I navigate to "Plugins > Local plugins > Job Board > Settings" in site administration
    When I click on "Encryption Settings" "link"
    Then I should see "Warning: Changing the encryption key will make previously encrypted files unreadable"

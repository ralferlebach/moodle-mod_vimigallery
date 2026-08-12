@mod @mod_vimigallery
Feature: Configure a ViMi Gallery activity
  In order to publish and moderate maps
  As a teacher
  I need to create a gallery and choose its comment and compare options

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | One      |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Teacher creates a gallery with comments and comparison enabled
    Given I am on the "Course 1" course page logged in as teacher1
    And I turn editing mode on
    When I add a "ViMi Gallery" activity to course "Course 1" section "1" and I fill the form with:
      | Name                                  | Shared maps |
      | Allow comments on maps                | 1           |
      | Enable side-by-side comparison        | 1           |
    Then I should see "Shared maps"

  Scenario: Comment and compare settings are available on the form
    Given I am on the "Course 1" course page logged in as teacher1
    And I turn editing mode on
    When I add a "ViMi Gallery" activity to course "Course 1" section "1"
    Then I should see "Allow comments on maps"
    And I should see "Enable side-by-side comparison"
    And I should see "Require comments"

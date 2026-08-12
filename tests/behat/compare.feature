@mod @mod_vimigallery
Feature: Compare two maps side by side
  In order to reflect on differences between maps
  As a user
  I need a compare view when the teacher enables it

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname |
      | student1 | Student   | One      |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following "activities" exist:
      | activity    | name       | course | idnumber | enablecompare |
      | vimigallery | Gallery A  | C1     | vga      | 1             |

  Scenario: The compare button is shown when comparison is enabled
    When I am on the "Gallery A" "vimigallery activity" page logged in as student1
    Then I should see "Compare"

  @javascript
  Scenario: The compare view offers two map selectors
    Given I am on the "Gallery A" "vimigallery activity" page logged in as student1
    When I follow "Compare"
    Then I should see "Left"
    And I should see "Right"

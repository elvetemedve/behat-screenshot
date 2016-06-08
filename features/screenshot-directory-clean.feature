Feature: Cleanup screenshot folder before scanerio
  Background:
    Given I have the file "index.html" in document root:
      """
      <!DOCTYPE html>
      <html>
          <head>
              <meta charset="UTF-8">
              <title>Test page</title>
              <style>
                  body {background-color: #a9a9a9;}
              </style>
          </head>

          <body>
              <h1>Lorem ipsum dolor amet.</h1>
          </body>
      </html>
      """
    And I have a web server running on host "localhost" and port "8080"
    And I have the feature:
      """
      Feature: Multi-step feature
      Scenario:
        Given I have a step
        When I have a failing step
        Then I should have a skipped step
      """
    And I have the context:
      """
      <?php
      use Behat\MinkExtension\Context\RawMinkContext;
      class FeatureContext extends RawMinkContext
      {
          /**
           * @Given I have a step
           */
          function passingStep()
          {
            $this->visitPath('index.html');
          }
          /**
           * @When I have a failing step
           */
          function failingStep()
          {
            throw new Exception('Error');
          }
          /**
           * @Then I should have a skipped step
           */
          function skippedStep()
          {}
      }
      """

  # fileinfo only needs to be installed separately on windows
  @windows
  Scenario: It reports an error when fileinfo is not installed
    Given I have the configuration:
      """
      default:
        extensions:
          Behat\MinkExtension:
            base_url: 'http://localhost:8080'
            sessions:
              default:
                selenium2:
                  wd_host: http://localhost:4444/wd/hub
                  browser: phantomjs

          Bex\Behat\ScreenshotExtension:
            image_drivers:
              local:
                screenshot_directory: /tmp/behat-screenshot-custom/
                clear_screenshot_directory: true
      """
    When I run Behat
    Then I should see the message "The fileinfo PHP extension is required, but not installed."

  Scenario: It doesn't clear local screenshot directory before running the tests if clear directory feature is disabled
    Given I have the configuration:
      """
      default:
        extensions:
          Behat\MinkExtension:
            base_url: 'http://localhost:8080'
            sessions:
              default:
                selenium2:
                  wd_host: http://localhost:4444/wd/hub
                  browser: phantomjs

          Bex\Behat\ScreenshotExtension:
            image_drivers:
              local:
                screenshot_directory: /tmp/behat-screenshot-custom/
      """
    And I have an image "dummy.png" file in "/tmp/behat-screenshot-custom/" directory
    When I run Behat with PHP CLI arguments "-d extension=fileinfo.so"
    Then I should see a failing test
    And I should have the image file "/tmp/behat-screenshot-custom/features_feature_feature_2.png"
    And I should have the image file "/tmp/behat-screenshot-custom/dummy.png"

  Scenario: Clear local screenshot directory before running the tests
    Given I have the configuration:
      """
      default:
        extensions:
          Behat\MinkExtension:
            base_url: 'http://localhost:8080'
            sessions:
              default:
                selenium2:
                  wd_host: http://localhost:4444/wd/hub
                  browser: phantomjs

          Bex\Behat\ScreenshotExtension:
            image_drivers:
              local:
                screenshot_directory: /tmp/behat-screenshot-custom/
                clear_screenshot_directory: true
      """
    And I have an image "dummy.png" file in "/tmp/behat-screenshot-custom/" directory
    When I run Behat
    Then I should see a failing test
    And the only file in "/tmp/behat-screenshot-custom/" directory should be "/tmp/behat-screenshot-custom/features_feature_feature_2.png"

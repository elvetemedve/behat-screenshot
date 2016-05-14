Feature: Taking screenshot
  In order to debug failing scenarios more easily
  As a developer
  I should see a screenshot of the browser window of the failing step

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

  Scenario: Save screenshot to local filesystem
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

          Bex\Behat\ScreenshotExtension: ~
      """
    When I run Behat
    Then I should see a failing test
    And I should see the message "Screenshot has been taken. Open image at %temp-dir%/behat-screenshot/failure_features_feature_feature_2.png"
    And I should have the image file "%temp-dir%/behat-screenshot/failure_features_feature_feature_2.png"

  Scenario: Save screenshot into a custom local directory
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
    When I run Behat
    Then I should see a failing test
    And I should see the message "Screenshot has been taken. Open image at /tmp/behat-screenshot-custom/failure_features_feature_feature_2.png"
    And I should have the image file "/tmp/behat-screenshot-custom/failure_features_feature_feature_2.png"

  Scenario: Save screenshot using external driver
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
            active_image_drivers: dummy
      """
    When I run Behat
    Then I should see a failing test
    And I should see the message "Screenshot has been taken. Open image at http://docs.behat.org/en/v2.5/_static/img/logo.png"

  Scenario: Disable the extension
    Given I have the configuration:
      """
      default:
        extensions:
          Bex\Behat\ScreenshotExtension:
            enabled: false
      """
    When I run Behat
    Then I should not see the message "Screenshot has been taken."

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
    And the only file in "/tmp/behat-screenshot-custom/" directory should be "/tmp/behat-screenshot-custom/failure_features_feature_feature_2.png"

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
    And I should see the message "Screenshot has been taken. Open image at %temp-dir%/behat-screenshot/features_feature_feature_2.png"
    And I should have the image file "%temp-dir%/behat-screenshot/features_feature_feature_2.png"

  Scenario: Don't save screenshot to local filesystem if there isn't any screenshot taken
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
          /**
           * @beforeStep
           */
          function doSomethingWrong()
          {
            throw new Exception('Error');
          }
      }
      """
    When I run Behat
    Then I should see a failing test
    And I should not see the message "Screenshot has been taken"

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
    And I should see the message "Screenshot has been taken. Open image at /tmp/behat-screenshot-custom/features_feature_feature_2.png"
    And I should have the image file "/tmp/behat-screenshot-custom/features_feature_feature_2.png"

  Scenario: Save screenshot into a custom local directory using base path
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
                screenshot_directory: %paths.base%/behat-screenshot-custom/
      """
    When I run Behat
    Then I should see a failing test
    And I should see the message "Screenshot has been taken. Open image at %working-dir%/behat-screenshot-custom/features_feature_feature_2.png"
    And I should have the image file "%working-dir%/behat-screenshot-custom/features_feature_feature_2.png"

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

  Scenario: Save screenshot to local filesystem if example fails
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
    And I have the feature:
      """
      Feature: Multi-step feature
      Scenario Outline: hopp
        Given I have a <first> step
        When I have a <second> step
        Then I should have a <third> step

        Examples:
          | first | second | third |
          | normal | failing | skipped |
      """
    And I have the context:
      """
      <?php
      use Behat\MinkExtension\Context\RawMinkContext;
      class FeatureContext extends RawMinkContext
      {
          /**
           * @Given I have a normal step
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
    When I run Behat
    Then I should see a failing test
    And I should see the message "Screenshot has been taken. Open image at %temp-dir%/behat-screenshot/features_feature_feature_9.png"
    And I should have the image file "%temp-dir%/behat-screenshot/features_feature_feature_9.png"

Feature: Taking screenshot
  In order to debug failing scenarios more easily
  As a developer
  I should see a screenshot of the browser window of the failing step


  Scenario: It reports an error when ImageMagick is not installed
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
            record_all_steps: true
      """
    When I run Behat
    Then I should see the message "Imagemagick PHP extension is required, but not installed."

  Scenario: It creates a combined screenshot of multiple steps
    Given I have a web server running on host "localhost" and port "8080"
    And I have the file "index.html" in document root:
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
    And I have the feature:
      """
      Feature: Multi-step feature
      Scenario:
        Given I have a failing step
      Scenario:
        Given I have a step
        When I have another failing step
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
           * @When I have another failing step
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
    And I have the configuration:
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
            record_all_steps: true
      """
    When I run Behat with PHP CLI arguments "-d extension=imagick.so"
    Then I should have "%temp-dir%/behat-screenshot/i_have_a_failing_step.png" image containing 1 step
    And I should have "%temp-dir%/behat-screenshot/i_have_another_failing_step.png" image containing 2 steps

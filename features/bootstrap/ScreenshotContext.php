<?php

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Bex\Behat\Context\TestRunnerContext;

/**
 * Class ScreenshotContext
 *
 * This class provides screenshot taking related scenario steps.
 *
 * @author Geza Buza <bghome@gmail.com>
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class ScreenshotContext implements SnippetAcceptingContext
{
    /** @var TestRunnerContext $testRunnerContext */
    private $testRunnerContext;

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->testRunnerContext = $environment->getContext('Bex\Behat\Context\TestRunnerContext');
    }


    /**
     * @Given I should have the image file :filePath
     */
    public function iShouldHaveTheImageFile($filePath)
    {
        $filePath = $this->substituteParameters($filePath);
        if (!file_exists($filePath)) {
            throw new RuntimeException('File does not exist: ' . $filePath);
        }

        $fileInfo = new finfo(FILEINFO_MIME);
        $mimeType = $fileInfo->file($filePath);
        if (mb_strpos($mimeType, 'image/') !== 0) {
            throw new RuntimeException(
                sprintf('File "%s" expected to be an image, but it is a "%s".', $filePath, $mimeType)
            );
        }
    }

    /**
     * @Given I should see the message :message
     */
    public function iShouldSeeTheMessage($message)
    {
        $message = $this->substituteParameters($message);
        $output = $this->testRunnerContext->getStandardOutputMessage() .
            $this->testRunnerContext->getStandardErrorMessage();
        $this->assertOutputContainsMessage($output, $message);
    }

    /**
     * @param $text
     * @return mixed
     */
    private function substituteParameters($text)
    {
        return str_replace('%temp-dir%', sys_get_temp_dir(), $text);
    }

    /**
     * @param $message
     */
    private function assertOutputContainsMessage($output, $message)
    {
        if (mb_strpos($output, $message) === false) {
            throw new RuntimeException('Behat output did not contain the given message.');
        }
    }
}
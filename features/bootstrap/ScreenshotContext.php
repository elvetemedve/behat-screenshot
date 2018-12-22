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

    /** @var array $imageFileSizes */
    private $imageFileSizes;

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
     * @Then I should not see the message :message
     */
    public function iShouldNotSeeTheMessage($message)
    {
        try {
            $this->iShouldSeeTheMessage($message);
        } catch (RuntimeException $e) {
            return;
        }

        throw new RuntimeException('Behat output contained the given message.');
    }

    /**
     * @Given I have an image :image file in :directory directory
     */
    public function iHaveAnImageFileInDirectory($image, $directory)
    {
        $filename = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $image;
        $this->createDirectory($directory);
        $this->createDummyImage($filename);
    }

    /**
     * @Given the only file in :directory directory should be :filename
     */
    public function theOnlyFileInDirectoryShouldBe($directory, $filename)
    {
        $files = glob(rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*');

        if (count($files) > 1 || array_search($filename, $files) === false) {
            throw new RuntimeException(
                sprintf(
                    'Only file "%s" is expected to be in "%s" directory, but %d files found.',
                    $filename,
                    $directory,
                    count($files)
                )
            );
        }
    }

    /**
     * @AfterSuite
     */
    public static function cleanUp()
    {
        self::removeTempDirs(['/tmp/behat-screenshot/', '/tmp/behat-screenshot-custom/']);
    }

    /**
     * @param $text
     * @return mixed
     */
    private function substituteParameters($text)
    {
        $text = str_replace('%temp-dir%', sys_get_temp_dir(), $text);
        $text = str_replace('%working-dir%', $this->testRunnerContext->getWorkingDirectory(), $text);

        return $text;
    }

    /**
     * @Then /^I should have "([^"]*)" image containing (\d+) step[s]?$/
     */
    public function iShouldHaveImageContainingStep($imageFilename, $stepCount)
    {
        $imageFilename = $this->substituteParameters($imageFilename);
        $this->iShouldHaveTheImageFile($imageFilename);
        list($width, $height) = getimagesize($imageFilename);
        $this->imageFileSizes[$stepCount] = $height;
        if ($this->imageFileSizes[1] * $stepCount !== $this->imageFileSizes[$stepCount]) {
            throw new RuntimeException(sprintf('The image %s does not contain %d steps.', $imageFilename, $stepCount));
        }
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

    /**
     * @param array $directories
     */
    private static function removeTempDirs(array $directories)
    {
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            $fileIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $directory,
                    FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS
                ),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($fileIterator as $file) {
                $file->isFile() ? unlink($file) : rmdir($file);
            }

            rmdir($directory);
        }
    }

    private function createDirectory($directory)
    {
        $filesystem = new \Symfony\Component\Filesystem\Filesystem();
        if (!$filesystem->exists($directory)) {
            $filesystem->mkdir($directory);
        }
    }

    private function createDummyImage($saveAsFile)
    {
        $base64Image = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVQIW2P4//8/AAX+Av6hN/6/AAAAAElFTkSuQmCC';
        file_put_contents($saveAsFile, base64_decode($base64Image));
    }
}

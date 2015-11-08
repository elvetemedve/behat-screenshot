<?php

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Class TestRunnerContext
 *
 * This class provides steps to execute a secondary Behat process in order to test Behat extension under development.
 * Selenium2 driver is in use and expected to have a running browser which will be controlled by tests. Define the
 * executable file of the browser in behat.yml by passing on the "browserCommand" parameter to this context class.
 *
 * @author Geza Buza <bghome@gmail.com>
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class TestRunnerContext implements SnippetAcceptingContext
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var string $workingDirectory Place for generated tests */
    private $workingDirectory;

    /** @var string $documentRoot Root directory of the web server */
    private $documentRoot;

    /** @var Process $behatProcess */
    private $behatProcess;

    /** @var Process $webServerProcess */
    private $webServerProcess;

    /** @var Process $browserProcess */
    private $browserProcess;

    /** @var string $browserCommand */
    private $browserCommand;

    /**
     * TestRunnerContext constructor.
     *
     * @param string $browserCommand Shell command which executes the tester browser
     */
    public function __construct($browserCommand)
    {
        $this->filesystem = new Filesystem();
        $this->browserCommand = $browserCommand;
    }

    /**
     * @BeforeScenario
     */
    public function createWorkingDirectory()
    {
        $this->workingDirectory = tempnam(sys_get_temp_dir(), 'behat-screenshot');
        $this->filesystem->remove($this->workingDirectory);
        $this->filesystem->mkdir($this->workingDirectory . '/features/bootstrap', 0777);

        $this->documentRoot = $this->workingDirectory .'/document_root';
        $this->filesystem->mkdir($this->documentRoot, 0777);
    }
    /**
     * @AfterScenario
     */
    public function clearWorkingDirectory()
    {
        $this->filesystem->remove($this->workingDirectory);
    }

    /**
     * @BeforeScenario
     */
    public function createProcesses()
    {
        $this->behatProcess = new Process(null);
        $this->webServerProcess = new Process(null);
        $this->browserProcess = new Process(null);
    }

    /**
     * @AfterScenario
     */
    public function stopProcessesIfRunning()
    {
        /** @var Process $process */
        foreach ([$this->behatProcess, $this->webServerProcess, $this->browserProcess] as $process) {
            if ($process->isRunning()) {
                $process->stop(10);
            }
        }
    }

    /**
     * @AfterScenario
     */
    public function printTesterOutputOnFailure(AfterScenarioScope $scope)
    {
        if (!$scope->getTestResult()->isPassed()) {
            $outputFile = sys_get_temp_dir() . '/behat-screenshot.out';
            $this->filesystem->dumpFile(
                $outputFile,
                $this->behatProcess->getOutput() . $this->behatProcess->getErrorOutput()
            );
            throw new RuntimeException("Output of secondary Behat process has been saved to $outputFile");
        }
    }

    /**
     * @Given I have the configuration:
     */
    public function iHaveTheConfiguration(PyStringNode $config)
    {
        $this->filesystem->dumpFile(
            $this->workingDirectory.'/behat.yml',
            $config->getRaw()
        );
    }

    /**
     * @Given I have the feature:
     */
    public function iHaveTheFeature(PyStringNode $content)
    {
        $this->filesystem->dumpFile(
            $this->workingDirectory.'/features/feature.feature',
            $content->getRaw()
        );
    }
    /**
     * @Given I have the context:
     */
    public function iHaveTheContext(PyStringNode $definition)
    {
        $this->filesystem->dumpFile(
            $this->workingDirectory.'/features/bootstrap/FeatureContext.php',
            $definition->getRaw()
        );
    }

    /**
     * @When I run Behat
     */
    public function iRunBehat()
    {
        $this->runBehat();
    }

    /**
     * @Given I have the file :filename in document root:
     */
    public function iHaveTheFileInDocumentRoot($filename, PyStringNode $content)
    {
        $this->filesystem->dumpFile($this->documentRoot .'/'. $filename, $content);
    }

    /**
     * @Given I have a web server running on host :hostname and port :port
     */
    public function iHaveAWebServerRunningOnAddressAndPort($hostname, $port)
    {
        $this->runWebServer($hostname, $port);
        $this->runBrowser();
    }

    /**
     * @Then I should see a failing test
     */
    public function iShouldSeeAFailingTest()
    {
        if ($this->behatProcess->getExitCode() == 0) {
            throw new RuntimeException('Behat did not find any failing scenario.');
        }
    }

    /**
     * Returns the output of Behat command
     *
     * @return string
     */
    public function getStandardOutputMessage()
    {
        return $this->behatProcess->getOutput();
    }

    /**
     * Returns the error output of Behat command
     *
     * @return string
     */
    public function getStandardErrorMessage()
    {
        return $this->behatProcess->getErrorOutput();
    }

    private function runBehat()
    {
        $phpFinder = new PhpExecutableFinder();
        $phpBin = $phpFinder->find();
        $this->behatProcess->setWorkingDirectory($this->workingDirectory);
        $this->behatProcess->setCommandLine(
            sprintf(
                '%s %s --no-colors',
                $phpBin,
                escapeshellarg(BEHAT_BIN_PATH)
            )
        );
        $this->behatProcess->run();
    }

    private function runWebServer($hostname, $port)
    {
        $phpFinder = new PhpExecutableFinder();
        $phpBin = $phpFinder->find();
        $this->webServerProcess->setWorkingDirectory($this->documentRoot);
        $this->webServerProcess->setCommandLine(
            sprintf(
                '%s -S %s:%s -t %s',
                $phpBin,
                escapeshellarg($hostname),
                escapeshellarg($port),
                escapeshellarg($this->documentRoot)
            )
        );
        $this->webServerProcess->start();
    }

    private function runBrowser()
    {
        $this->browserProcess->setWorkingDirectory($this->workingDirectory);
        $this->browserProcess->setCommandLine(escapeshellcmd($this->browserCommand));
        $this->browserProcess->start();
    }
}
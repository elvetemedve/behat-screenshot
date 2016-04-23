<?php

namespace Bex\Behat\ScreenshotExtension\Output;

use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class IndentedOutput
 *
 * @package Bex\Behat\ScreenshotExtension\Output
 *
 * @author Geza Buza <bghome@gmail.com>
 */
class IndentedOutput implements OutputInterface
{
    const INDENT_WIDTH = 2;

    const DEFAULT_INDENT_LEVEL = 3;
    const DEFAULT_INDENT_CHARACTER = ' ';

    /**
     * @var string String being appended to the message
     */
    private $indentation;

    /**
     * @var OutputInterface
     */
    private $decoratedOutput;

    /**
     * IndentedOutput constructor.
     *
     * @param OutputInterface $decoratedOutput
     * @param int $indentLevel
     * @param string $indentCharacter
     */
    public function __construct(
        OutputInterface $decoratedOutput,
        $indentLevel = self::DEFAULT_INDENT_LEVEL,
        $indentCharacter = self::DEFAULT_INDENT_CHARACTER
    ) {
        $this->indentation = str_repeat($indentCharacter, $indentLevel * self::INDENT_WIDTH);
        $this->decoratedOutput = $decoratedOutput;
    }

    /**
     * Writes a message to the output.
     *
     * @param string|array $messages The message as an array of lines or a single string
     * @param bool $newline Whether to add a newline
     * @param int $options A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     */
    public function write($messages, $newline = false, $options = 0)
    {
        $messages = $this->addIndentationToMessages($messages);

        $this->decoratedOutput->write($messages, $newline, $options);
    }

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|array $messages The message as an array of lines of a single string
     * @param int $options A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     */
    public function writeln($messages, $options = 0)
    {
        $messages = $this->addIndentationToMessages($messages);

        $this->decoratedOutput->writeln($messages, $options);
    }

    /**
     * Sets the verbosity of the output.
     *
     * @param int $level The level of verbosity (one of the VERBOSITY constants)
     */
    public function setVerbosity($level)
    {
        $this->decoratedOutput->setVerbosity($level);
    }

    /**
     * Gets the current verbosity of the output.
     *
     * @return int The current level of verbosity (one of the VERBOSITY constants)
     */
    public function getVerbosity()
    {
        return $this->decoratedOutput->getVerbosity();
    }

    /**
     * Returns whether verbosity is quiet (-q).
     *
     * @return bool true if verbosity is set to VERBOSITY_QUIET, false otherwise
     */
    public function isQuiet()
    {
        return $this->decoratedOutput->isQuiet();
    }

    /**
     * Returns whether verbosity is verbose (-v).
     *
     * @return bool true if verbosity is set to VERBOSITY_VERBOSE, false otherwise
     */
    public function isVerbose()
    {
        return $this->decoratedOutput->isVerbose();
    }

    /**
     * Returns whether verbosity is very verbose (-vv).
     *
     * @return bool true if verbosity is set to VERBOSITY_VERY_VERBOSE, false otherwise
     */
    public function isVeryVerbose()
    {
        return $this->decoratedOutput->isVeryVerbose();
    }

    /**
     * Returns whether verbosity is debug (-vvv).
     *
     * @return bool true if verbosity is set to VERBOSITY_DEBUG, false otherwise
     */
    public function isDebug()
    {
        return $this->decoratedOutput->isDebug();
    }

    /**
     * Sets the decorated flag.
     *
     * @param bool $decorated Whether to decorate the messages
     */
    public function setDecorated($decorated)
    {
        $this->decoratedOutput->setDecorated($decorated);
    }

    /**
     * Gets the decorated flag.
     *
     * @return bool true if the output will decorate messages, false otherwise
     */
    public function isDecorated()
    {
        return $this->decoratedOutput->isDecorated();
    }

    /**
     * Sets output formatter.
     *
     * @param \Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter
     */
    public function setFormatter(\Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter)
    {
        $this->decoratedOutput->setFormatter($formatter);
    }

    /**
     * Returns current output formatter instance.
     *
     * @return \Symfony\Component\Console\Formatter\OutputFormatterInterface
     */
    public function getFormatter()
    {
        return $this->decoratedOutput->getFormatter();
    }

    /**
     * Prepend message with padding
     * 
     * @param $messages
     *
     * @return array|string
     */
    private function addIndentationToMessages($messages)
    {
        if (is_array($messages)) {
            array_walk(
                $messages,
                function ($message) {
                    return $this->indentation . $message;
                }
            );
            return $messages;
        } else {
            $messages = $this->indentation . $messages;
            return $messages;
        }
    }
}
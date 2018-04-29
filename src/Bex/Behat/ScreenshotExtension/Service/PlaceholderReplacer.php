<?php

namespace Bex\Behat\ScreenshotExtension\Service;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;

class PlaceholderReplacer
{
    /**
     * @var array
     */
    private $placeholders;
    
    /**
     * @param array $placeholders
     */
    public function __construct(array $placeholders = [])
    {
        $this->placeholders = $placeholders;
    }

    /**
     * @param string $text
     * @param AfterScenarioTested $event
     *
     * @return string
     */
    public function replaceAll($text, AfterScenarioTested $event)
    {
        foreach ($this->placeholders as $placeholder) {
            if (strpos($text, $placeholder->getKey()) !== false) {
                $text = str_replace($placeholder->getKey(), $placeholder->getValue($event), $text);
            }
        }

        return $text;
    }
}

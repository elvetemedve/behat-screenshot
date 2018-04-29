<?php

namespace Bex\Behat\ScreenshotExtension\Placeholder;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Bex\Behat\ScreenshotExtension\Placeholder\PlaceholderInterface;

class ScenarioLineNumber implements PlaceholderInterface
{
    const KEY = '%SCENARIO_LINE_NUMBER%';

    public function getKey()
    {
        return self::KEY;   
    }

    public function getValue(AfterScenarioTested $event)
    {
        return (string) $event->getScenario()->getLine();
    }
}

<?php

namespace Bex\Behat\ScreenshotExtension\Placeholder;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Bex\Behat\ScreenshotExtension\Placeholder\PlaceholderInterface;

class SuiteName implements PlaceholderInterface
{
    const KEY = '%SUITE_NAME%';

    public function getKey()
    {
        return self::KEY;   
    }

    public function getValue(AfterScenarioTested $event)
    {
        return $event->getSuite()->getName();
    }
}

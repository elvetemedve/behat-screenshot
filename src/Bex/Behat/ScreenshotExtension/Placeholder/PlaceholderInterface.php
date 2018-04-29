<?php

namespace Bex\Behat\ScreenshotExtension\Placeholder;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;

interface PlaceholderInterface
{
    public function getKey();

    public function getValue(AfterScenarioTested $event);
}
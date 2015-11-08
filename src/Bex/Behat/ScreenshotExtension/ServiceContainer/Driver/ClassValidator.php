<?php

namespace Bex\Behat\ScreenshotExtension\ServiceContainer\Driver;

use Bex\Behat\ScreenshotExtension\Driver\DriverInterface;


class ClassValidator
{
    /**
     * @var string
     */
    private $parent;

    /**
     * @param string $parent
     */
    public function __construct($parent = '')
    {
        $this->parent = $parent;
    }

    /**
     * @param  string  $className
     *
     * @return boolean
     */
    public function isValidDriverClass($className)
    {
        if (!class_exists($className)) {
            return false;
        }

        if (!is_subclass_of($className, DriverInterface::class)) {
            return false;
        }

        if (!empty($this->parent) && !is_subclass_of($className, $this->parent)) {
            return false;
        }

        return true;
    }
}
<?php
namespace Bex\Behat\ScreenshotExtension\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatches an event when screenshots are uploaded.
 */
class ScreenshotUploaderEvent extends Event
{
    public const UPLOAD = 'screenshot-uploader-upload';

    protected $filename = '';

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function getFilename()
    {
        return $this->filename;
    }
}

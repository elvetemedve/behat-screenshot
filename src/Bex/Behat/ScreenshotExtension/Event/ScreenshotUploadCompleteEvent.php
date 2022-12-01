<?php
namespace Bex\Behat\ScreenshotExtension\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatches an event when screenshots are uploaded.
 */
class ScreenshotUploadCompleteEvent extends Event
{
    const NAME = 'screenshot.uploader.upload_complete';

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

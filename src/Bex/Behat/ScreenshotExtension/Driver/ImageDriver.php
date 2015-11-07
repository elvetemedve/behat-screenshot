<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

interface ImageDriver
{
    /**
     * @param string $binaryImage
     * @param string $filename
     *
     * @return string URL to the image
     */
    public function upload($binaryImage, $filename);
}
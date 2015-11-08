<?php

use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Class ScreenshotContext
 *
 * This class provides screenshot taking related scenario steps.
 *
 * @author Geza Buza <bghome@gmail.com>
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class ScreenshotContext implements SnippetAcceptingContext
{

    /**
     * @Given I should have the image file :filePath
     */
    public function iShouldHaveTheImageFile($filePath)
    {
        $filePath = str_replace('%temp-dir%', sys_get_temp_dir(), $filePath);
        if (!file_exists($filePath)) {
            throw new RuntimeException('File does not exist: ' . $filePath);
        }

        $fileInfo = new finfo(FILEINFO_MIME);
        $mimeType = $fileInfo->file($filePath);
        if (strpos($mimeType, 'image/') !== 0) {
            throw new RuntimeException(
                sprintf('File "%s" expected to be an image, but it is a "%s".', $filePath, $mimeType)
            );
        }
    }
}
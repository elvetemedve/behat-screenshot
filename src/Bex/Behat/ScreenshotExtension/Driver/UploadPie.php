<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Bex\Behat\ScreenshotExtension\Config\Parameters;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class UploadPie implements ImageDriver
{
    /**
     * @var Parameters
     */
    private $parameters;

    /**
     * @param Parameters $parameters
     */
    public function __construct(Parameters $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param string $binaryImage Content
     * @param string $filename
     *
     * @return string URL to the image
     */
    public function upload($binaryImage, $filename)
    {
        $tmpFile = "/tmp/$filename";
        file_put_contents($tmpFile, $binaryImage);

        $url = 'http://uploadpie.com/';
        $data = [
            'uploadedfile' => new \CURLFile($tmpFile),
            'expire' => $this->parameters->getExpiryDate(),
            'upload' => 1
        ];

        $ch = curl_init();  
             
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);    

        $output = curl_exec($ch);

        curl_close($ch);

        preg_match('/<input.*value="(.*)"/U', $output, $matches);

        if (isset($matches[1])) {
            unlink($tmpFile);
            return $matches[1];
        }
        
        return $tmpFile;
    }
}
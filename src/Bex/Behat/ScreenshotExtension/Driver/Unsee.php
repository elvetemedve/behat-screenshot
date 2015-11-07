<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Bex\Behat\ScreenshotExtension\Config\Parameters;
use Buzz\Client\Curl;
use Buzz\Message\Form\FormRequest;
use Buzz\Message\Form\FormUpload;
use Buzz\Message\Response;

class Unsee implements ImageDriver
{
    const REQUEST_URL = 'https://unsee.cc/upload/';
    const IMAGE_BASE_URL= 'https://unsee.cc/';

    /**
     * @var Curl
     */
    private $client;

    /**
     * @var Parameters
     */
    private $parameters;

    /**
     * @param Curl       $client
     * @param Parameters $parameters
     */
    public function __construct(Curl $client, Parameters $parameters)
    {
        $this->client = $client;
        $this->parameters = $parameters;
    }

    /**
     * @param string $binaryImage
     * @param string $filename
     *
     * @return string URL to the image
     */
    public function upload($binaryImage, $filename)
    {
        $response = $this->callApi($binaryImage, $filename);
        return $this->processResponse($response);
    }

    /**
     * @param  string $binaryImage
     * @param  string $filename
     *
     * @return Response
     */
    private function callApi($binaryImage, $filename)
    {
        $response = new Response();

        $image = new FormUpload();
        $image->setFilename($filename);
        $image->setContent($binaryImage);
        $expire = 600; //TODO get expire time from config (possible values: 0 , 600, 1800, 3600)

        $request = $this->buildRequest($image, $expire);
        $this->client->send($request, $response);

        return $response;
    }

    /**
     * @param  Response $response
     *
     * @return string
     */
    private function processResponse(Response $response)
    {
        $responseData = json_decode($response->getContent(), true);

        if (!isset($responseData['hash'])) {
            throw new \RuntimeException('Screenshot upload failed');
        }

        return self::IMAGE_BASE_URL . $responseData['hash'];
    }

    /**
     * @param  FormUpload $image
     * @param  int        $expire
     *
     * @return FormRequest
     */
    private function buildRequest($image, $expire)
    {
        $request = new FormRequest();
        
        $request->fromUrl(self::REQUEST_URL);
        $request->setField('image', $image);
        $request->setField('time', $expire);

        return $request;
    }
}
<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Bex\Behat\ScreenshotExtension\Config\Parameters;
use Buzz\Client\Curl;
use Buzz\Message\Form\FormRequest;
use Buzz\Message\Form\FormUpload;
use Buzz\Message\Request;
use Buzz\Message\RequestInterface;
use Buzz\Message\Response;
use Buzz\Util\Url;

/**
 * All uploaded image will live for 10 minutes
 */
class Img42 implements ImageDriver
{
    const REQUEST_URL = 'https://img42.com';
    const IMAGE_BASE_URL= 'https://img42.com/';

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

        $request = $this->buildRequest($binaryImage);
        $this->client->setOption(CURLOPT_BINARYTRANSFER, true);
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

        if (!isset($responseData['id'])) {
            throw new \RuntimeException('Screenshot upload failed');
        }

        return self::IMAGE_BASE_URL . $responseData['id'];
    }

    /**
     * @param  string $binaryImage
     *
     * @return Request
     */
    private function buildRequest($binaryImage)
    {
        $request = new Request(RequestInterface::METHOD_POST);

        $request->fromUrl(self::REQUEST_URL);
        $request->setContent($binaryImage);

        return $request;
    }
}
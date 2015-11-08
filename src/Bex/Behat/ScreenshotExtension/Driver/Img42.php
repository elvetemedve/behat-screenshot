<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Bex\Behat\ScreenshotExtension\Driver\ImageDriverInterface;
use Buzz\Client\Curl;
use Buzz\Message\Form\FormRequest;
use Buzz\Message\Form\FormUpload;
use Buzz\Message\Request;
use Buzz\Message\RequestInterface;
use Buzz\Message\Response;
use Buzz\Util\Url;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Img42 implements ImageDriverInterface
{
    const REQUEST_URL = 'https://img42.com';
    const IMAGE_BASE_URL= 'https://img42.com/';

    /**
     * @var Curl
     */
    private $client;

    /**
     * @param Curl       $client
     */
    public function __construct(Curl $client = null)
    {
        $this->client = $client ?: new Curl();
    }

    /**
     * @param  ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        // no additional configuration required
        // all uploaded image will live for 10 minutes
        // it can't be configured during the image upload
    }

    /**
     * @param  ContainerBuilder $container
     * @param  array            $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        // there isn't any configuration for this image upload driver
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
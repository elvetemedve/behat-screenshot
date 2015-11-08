<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Bex\Behat\ScreenshotExtension\Driver\ImageDriverInterface;
use Buzz\Client\Curl;
use Buzz\Message\Form\FormRequest;
use Buzz\Message\Form\FormUpload;
use Buzz\Message\Response;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class UploadPie implements ImageDriverInterface
{
    const CONFIG_PARAM_EXPIRE = 'expire';

    const REQUEST_URL = 'http://uploadpie.com/';

    /**
     * @var Curl
     */
    private $client;

    /**
     * @var string
     */
    private $expire;

    /**
     * @param Curl       $client
     * @param Parameters $parameters
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
        $builder
            ->children()
                ->scalarNode(self::CONFIG_PARAM_EXPIRE)
                    ->defaultValue(30)
                ->end()
            ->end();
    }

    /**
     * @param  ContainerBuilder $container
     * @param  array            $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->expire = $config[self::CONFIG_PARAM_EXPIRE];
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
        $expire = 1; // TODO expire value should be between 1 and 5

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
        $matches = [];

        preg_match('/<input.*value="(.*)"/U', $response->getContent(), $matches);

        if (!isset($matches[1])) {
            throw new \RuntimeException('Screenshot upload failed');
        }

        return $matches[1];
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
        $request->setField('uploadedfile', $image);
        $request->setField('expire', $expire);
        $request->setField('upload', 1);

        return $request;
    }
}
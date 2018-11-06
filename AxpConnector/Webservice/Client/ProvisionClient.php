<?php

namespace Adobe\AxpConnector\Webservice\Client;

use Magento\Framework\HTTP\ZendClient;
use Adobe\AxpConnector\Helper\Data;
use \Psr\Log\LoggerInterface;

class ProvisionClient
{
    /**
     * @var ZendClient
     */
    private $zendClient;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ZendClient $zendClient,
        Data $helper,
        LoggerInterface $logger
    ) {
        $this->zendClient = $zendClient;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * Make request to Launch API.
     *
     * @param string $uri
     * @param string $method
     * @param array $headers
     * @param integer $code
     * @param array $body
     * @param string $enctype
     * @return array
     */
    public function request($uri, $method, $headers, $code, $body = null, $enctype = null)
    {
        try {
            $this->logger->debug('DebugProvisionRequestStart', ['request' => ['uri' => $uri, 'body' => $body,
                'headers' => $headers, 'method' => $method]]);
            $this->zendClient->resetParameters();
            if ($body !== null) {
                if ($enctype === 'multipart/form-data') {
                    foreach ($body as $key => $val) {
                        $this->zendClient->setParameterPost($key, $val);
                    }
                } elseif ($enctype === 'application/vnd.api+json') {
                    $this->zendClient->setRawData($body, $enctype);
                }
            }
            $this->zendClient->setUri($uri);
            $this->zendClient->setHeaders($headers);
            $this->zendClient->setMethod($method);
            $this->zendClient->setConfig([
                'timeout' => 60,
                'keepalive' => true,
            ]);
        } catch (\Exception $argumentException) {
            $this->logger->critical($argumentException);
            return ['error' => $argumentException->getMessage()];
        }
        try {
            // send the request
            $response = $this->zendClient->request();
            $this->logger->debug('DebugProvisionRequestEnd', ['response' => $response]);
            if ($response->getStatus() !== $code) {
                return ['error' => 'The request failed with code: '.$response->getStatus()];
            }
            $body = $response->getBody();
            if ($body !== '') {
                return $this->helper->jsonDecode($body);
            } else {
                return [];
            }
        } catch (\Exception $runtimeException) {
            $this->logger->critical($runtimeException);
            return ['error' => $runtimeException->getMessage()];
        }
    }
}

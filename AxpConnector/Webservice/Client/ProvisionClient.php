<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Webservice\Client;

use Magento\Framework\Serialize\Serializer\Json;
use Zend\Http\Client as ZendClient;
use Psr\Log\LoggerInterface;

/**
 * Class ProvisionClient
 *
 * @package Adobe\AxpConnector\Webservice\Client
 */
class ProvisionClient
{
    /**
     * @var ZendClient
     */
    private $zendClient;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ProvisionClient constructor.
     * @param ZendClient $zendClient
     * @param Json $jsonSerializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        ZendClient $zendClient,
        Json $jsonSerializer,
        LoggerInterface $logger
    ) {
        $this->zendClient = $zendClient;
        $this->jsonSerializer = $jsonSerializer;
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
            $this->logger->debug(
                'DebugProvisionRequestStart', [
                    'request' => [
                        'uri' => $uri,
                        'body' => $body,
                        'headers' => $headers,
                        'method' => $method
                    ]
                ]
            );

            $this->zendClient->resetParameters();
            $this->makeBody($body, $enctype);
            $this->zendClient->setUri($uri);
            $this->zendClient->setHeaders($headers);
            $this->zendClient->setMethod($method);
        } catch (\Exception $argumentException) {
            $this->logger->critical($argumentException);
            return ['error' => $argumentException->getMessage()];
        }
        try {
            $response = $this->zendClient->send();

            $this->logger->debug('DebugProvisionRequestEnd', ['response' => $response]);
            $body = $response->getBody();
            try {
                $respObj = $this->jsonSerializer->unserialize($body);
            } catch (\Exception $e) {
                $respObj = [];
            }
            if ($response->getStatusCode() !== $code) {
                $message = '';
                if (array_key_exists('error', $respObj)) {
                    $message = $respObj['error'];
                }
                if (array_key_exists('error_description', $respObj)) {
                    $message = $respObj['error_description'];
                }
                return ['error' => 'The request failed with code: ' . $response->getStatusCode() . ' ' . $message];
            }
            return $respObj;
        } catch (\Exception $runtimeException) {
            $this->logger->critical($runtimeException);
            return ['error' => $runtimeException->getMessage()];
        }
    }

    /**
     * Make request to Launch API.
     *
     * @param array $body
     * @param string $enctype
     * @return void
     */
    private function makeBody($body, $enctype)
    {
        if ($body === null) {
            return;
        }

        if ($enctype === 'application/x-www-form-urlencoded') {
            $this->zendClient->setParameterPost($body);
        } elseif ($enctype === 'application/vnd.api+json') {
            $this->zendClient->setEncType($enctype);
            $this->zendClient->setRawBody($body);
        }
    }
}

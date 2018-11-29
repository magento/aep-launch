<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

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
            $this->makeBody($body, $enctype);
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
            $body = $response->getBody();
            try {
                $respObj = $this->helper->jsonDecode($body);
            } catch (\Exception $e) {
                $respObj = [];
            }
            if ($response->getStatus() !== $code) {
                $message = '';
                if (array_key_exists('error', $respObj)) {
                    $message = $respObj['error'];
                }
                if (array_key_exists('error_description', $respObj)) {
                    $message = $respObj['error_description'];
                }
                return ['error' => 'The request failed with code: '.$response->getStatus().' '.$message];
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
     * @return array
     */
    private function makeBody($body, $enctype)
    {
        if ($body !== null) {
            if ($enctype === 'multipart/form-data') {
                foreach ($body as $key => $val) {
                    $this->zendClient->setParameterPost($key, $val);
                }
            } elseif ($enctype === 'application/vnd.api+json') {
                $this->zendClient->setRawData($body, $enctype);
            }
        }
    }
}

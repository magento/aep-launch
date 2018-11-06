<?php

namespace Adobe\AxpConnector\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Exception\FileSystemException;
use Adobe\AxpConnector\Helper\ProvisionHelper;
use Adobe\AxpConnector\Helper\Data;

class Provision extends Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var File
     */
    private $file;

    /**
     * @var ProvisionHelper
     */
    private $provisionHelper;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Reader $moduleReader
     * @param File $file
     * @param ProvisionHelper $provisionHelper
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Reader $moduleReader,
        File $file,
        ProvisionHelper $provisionHelper,
        Data $helper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->moduleReader = $moduleReader;
        $this->file = $file;
        $this->provisionHelper = $provisionHelper;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Execute Provisioning
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();
        $config = $this->_getJsonConfig();
        $requestResponse = $this->_sendAPIRequests($config);

        return $result->setData($requestResponse);
    }

    /**
     * Get the JSON file
     *
     * @return mixed
     */
    private function _getJsonConfig()
    {
        $etcDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR,
            'Adobe_AxpConnector'
        );
        $file = $etcDir . '/adminhtml/provision_config.json';
        try {
            $string = $this->file->fileGetContents($file);
            return $this->helper->jsonDecode($string);
        } catch (FileSystemException $e) {
            return ["error"=>$e->getMessage()];
        }
    }

    /**
     * Make the API calls
     *
     * @param array $config
     * @return array
     */
    private function _sendAPIRequests($config)
    {
        return $this->provisionHelper->makeRequests($config);
    }
}

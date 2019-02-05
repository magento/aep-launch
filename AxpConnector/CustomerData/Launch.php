<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Adobe\AxpConnector\Model\LaunchConfigProvider;

/**
 * Launch private data section
 */
class Launch extends \Magento\Framework\DataObject implements SectionSourceInterface
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     * @deprecated
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param LaunchConfigProvider $launchConfigProvider
     * @param \Magento\Checkout\Model\Session $_checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        LaunchConfigProvider $launchConfigProvider,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($data);
        $this->jsonHelper = $jsonHelper;
        $this->_checkoutSession = $_checkoutSession;
        $this->customerSession = $customerSession;
        $this->launchConfigProvider = $launchConfigProvider;
    }

    /**
     * Get section data.
     *
     * @return array
     */
    public function getSectionData()
    {
        $data = [];

        if ($this->_checkoutSession->getAddToCartDatalayerContent()) {
            $data[] = $this->_checkoutSession->getAddToCartDatalayerContent();
        }
        $this->_checkoutSession->setAddToCartDatalayerContent(null);

        if ($this->_checkoutSession->getRemoveFromCartDatalayerContent()) {
            $data[] = $this->_checkoutSession->getRemoveFromCartDatalayerContent();
        }
        $this->_checkoutSession->setRemoveFromCartDatalayerContent(null);

        // Get rid of nulls and empties
        $data = array_filter($data);

        $script = "";
        if (count($data) > 0) {
            $jsonData = $this->jsonHelper->jsonEncode($data);

            // So awful...
            $script = '<script type="text/javascript">'
                . "window.{$this->launchConfigProvider->getDatalayerName()} = window.{$this->launchConfigProvider->getDatalayerName()} || [];";

            foreach ($data as $event) {
                $jsonData = $this->jsonHelper->jsonEncode($event);
                $script = $script . "window.{$this->launchConfigProvider->getDatalayerName()}.push(${jsonData});\n";
            }
            $script = $script . '</script>';
        }

        return [
            'datalayerScript' => $script
        ];
    }
}

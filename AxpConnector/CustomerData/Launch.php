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
class Launch implements SectionSourceInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
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

        if ($this->checkoutSession->getAddToCartDatalayerContent()) {
            $data[] = $this->checkoutSession->getAddToCartDatalayerContent();
        }
        $this->checkoutSession->setAddToCartDatalayerContent(null);

        if ($this->checkoutSession->getRemoveFromCartDatalayerContent()) {
            $data[] = $this->checkoutSession->getRemoveFromCartDatalayerContent();
        }
        $this->checkoutSession->setRemoveFromCartDatalayerContent(null);

        // Get rid of nulls and empties
        $data = array_filter($data);

        $script = '<script type="text/javascript">';
        if (count($data) > 0) {
            foreach ($data as $event) {
                $script .= sprintf("window.%s.push(%s)", $this->launchConfigProvider->getDatalayerName(), $event);
            }
            $script = $script . '</script>';
        }

        return [
            'datalayerScript' => $script
        ];
    }
}

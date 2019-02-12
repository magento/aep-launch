<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Adobe\AxpConnector\Model\LaunchConfigProvider;
use Magento\Checkout\Model\Session;

/**
 * Launch private data section
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Launch implements SectionSourceInterface
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     * @param Session $checkoutSession
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        Session $checkoutSession
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

        $data[] = $this->checkoutSession->getData('add_to_cart_datalayer_content', true);
        $data[] = $this->checkoutSession->getData('remove_from_cart_datalayer_content', true);
        $data[] = $this->checkoutSession->getData('order_placed_datalayer_content', true);
        $data = array_values(array_filter($data));

        return [
            'datalayerEvents' => $data
        ];
    }
}

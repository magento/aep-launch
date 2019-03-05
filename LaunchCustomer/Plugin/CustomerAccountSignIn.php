<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCustomer\Plugin;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Adobe\LaunchCustomer\Model\FormatCustomerEvent;
use Adobe\AxpConnector\Api\AddPrivateDatalayerEventInterface;
use Adobe\AxpConnector\Model\LaunchConfigProvider;

/**
 * Plugin for Customer Login event.
 */
class CustomerAccountSignIn
{
    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @var AddPrivateDatalayerEventInterface
     */
    private $addPrivateDatalayerEvent;

    /**
     * @var FormatCustomerEvent
     */
    private $formatCustomerEvent;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     * @param AddPrivateDatalayerEventInterface $addPrivateDatalayerEvent
     * @param FormatCustomerEvent $formatCustomerEvent
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        AddPrivateDatalayerEventInterface $addPrivateDatalayerEvent,
        FormatCustomerEvent $formatCustomerEvent
    ) {
        $this->launchConfigProvider = $launchConfigProvider;
        $this->addPrivateDatalayerEvent = $addPrivateDatalayerEvent;
        $this->formatCustomerEvent = $formatCustomerEvent;
    }

    /**
     * Process "User Sign In" datalayer event.
     *
     * @param AccountManagementInterface $subject
     * @param CustomerInterface $customer
     * @return CustomerInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterAuthenticate(AccountManagementInterface $subject, CustomerInterface $customer)
    {
        if (!$this->launchConfigProvider->isEnabled()) {
            return $customer;
        }

        $datalayerContent = $this->formatCustomerEvent->execute($customer);
        $datalayerContent['event'] = 'User Signed In';
        $this->addPrivateDatalayerEvent->execute('CustomerSignInDatalayerContent', $datalayerContent);
        return $customer;
    }
}

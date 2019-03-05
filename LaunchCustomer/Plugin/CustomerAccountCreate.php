<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCustomer\Plugin;

use Adobe\LaunchCustomer\Model\FormatCustomerEvent;
use Magento\Customer\Api\Data\CustomerInterface;
use Adobe\AxpConnector\Api\AddPrivateDatalayerEventInterface;
use Adobe\AxpConnector\Model\LaunchConfigProvider;
use Magento\Customer\Api\AccountManagementInterface;

/**
 * Plugin for Customer Account Created event.
 */
class CustomerAccountCreate
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
     * Process "User Registered" datalayer event.
     *
     * @param AccountManagementInterface $subject
     * @param CustomerInterface $customer
     * @return CustomerInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreateAccount(AccountManagementInterface $subject, CustomerInterface $customer)
    {
        if (!$this->launchConfigProvider->isEnabled()) {
            return $customer;
        }

        $datalayerContent = $this->formatCustomerEvent->execute($customer);
        $datalayerContent['event'] = 'User Registered';
        $this->addPrivateDatalayerEvent->execute('CustomerAccountCreateDatalayerContent', $datalayerContent);
        return $customer;
    }
}

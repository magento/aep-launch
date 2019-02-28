<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCustomer\Model;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Format Customer Registered datalayer event data.
 */
class FormatCustomerEvent
{
    /**
     * Format Customer Registered event.
     *
     * @param CustomerInterface $customer
     * @return array
     */
    public function execute(CustomerInterface $customer): array
    {
        $result = [];

        $result['event'] = 'User Registered';
        $result['user']['userType'] = $customer->getGroupId();
        $result['user']['custKey'] = $customer->getId();

        return $result;
    }
}

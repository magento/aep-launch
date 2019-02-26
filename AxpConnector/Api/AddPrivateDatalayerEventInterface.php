<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Api;

/**
 * Add event to the datalayer.
 */
interface AddPrivateDatalayerEventInterface
{
    /**
     * Append an event to the Datalayer.
     *
     * @param string $eventName
     * @param array $eventData
     */
    public function execute(string $eventName, array $eventData): void;
}

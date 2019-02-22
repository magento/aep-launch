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
interface AddDatalayerEventInterface
{
    /**
     * Append an event to the Datalayer.
     *
     * @param array $eventData
     */
    public function execute(array $eventData): void;
}

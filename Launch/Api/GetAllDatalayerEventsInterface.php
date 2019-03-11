<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\Launch\Api;

/**
 * Get all events form the datalayer.
 */
interface GetAllDatalayerEventsInterface
{
    /**
     * Return all events stored in the datalayer.
     *
     * @return array
     */
    public function execute(): array;
}

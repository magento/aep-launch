<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\Launch\Model\Command;

use Adobe\Launch\Api\GetAllDatalayerEventsInterface;
use Adobe\Launch\Model\Datalayer;

/**
 * Get all events form the datalayer.
 */
class GetAllDatalayerEvents implements GetAllDatalayerEventsInterface
{
    /**
     * @var Datalayer
     */
    private $datalayer;

    /**
     * @param Datalayer $datalayer
     */
    public function __construct(Datalayer $datalayer)
    {
        $this->datalayer = $datalayer;
    }

    /**
     * Return all events stored in the datalayer.
     *
     * @return array
     */
    public function execute(): array
    {
        return $this->datalayer->emit();
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Model\Command;

use Adobe\AxpConnector\Model\Datalayer;

/**
 * Get all events form the datalayer.
 */
class GetAllDatalayerEvents
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
     * @return array
     */
    public function execute()
    {
        return $this->datalayer->emit();
    }
}

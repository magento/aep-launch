<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Model\Command;

use Adobe\AxpConnector\Model\Datalayer;

/**
 * Add event to the datalayer.
 */
class AddDatalayerEvent
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

    public function execute(array $eventData)
    {
        $this->datalayer->push($eventData);
    }
}

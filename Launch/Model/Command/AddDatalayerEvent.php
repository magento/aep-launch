<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\Launch\Model\Command;

use Adobe\Launch\Api\AddDatalayerEventInterface;
use Adobe\Launch\Model\Datalayer;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Add event to the datalayer.
 */
class AddDatalayerEvent implements AddDatalayerEventInterface
{
    /**
     * @var Datalayer
     */
    private $datalayer;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @param Datalayer $datalayer
     * @param Json $jsonSerializer
     */
    public function __construct(
        Datalayer $datalayer,
        Json $jsonSerializer
    ) {
        $this->datalayer = $datalayer;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Append an event to the Datalayer.
     *
     * @param array $eventData
     */
    public function execute(array $eventData): void
    {
        $this->datalayer->push($this->jsonSerializer->serialize($eventData));
    }
}

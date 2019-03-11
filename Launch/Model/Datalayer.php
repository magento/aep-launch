<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\Launch\Model;

use Magento\Framework\Serialize\Serializer\Json;

/**
 * This is a prototype class, temporary introduced for refactoring purposes.
 */
class Datalayer
{
    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var array
     */
    private $datalayerEvents = [];

    /**
     * @param Json $jsonSerializer
     */
    public function __construct(Json $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Push event data entry into the datalayer.
     *
     * @param string $eventData
     */
    public function push(string $eventData): void
    {
        $this->datalayerEvents[] = $eventData;
    }

    /**
     * Return all events stored in the datalayer.
     *
     * @return array
     */
    public function emit(): array
    {
        return $this->datalayerEvents;
    }
}

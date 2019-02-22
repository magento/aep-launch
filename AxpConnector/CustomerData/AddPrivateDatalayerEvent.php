<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\CustomerData;

use Adobe\AxpConnector\Api\AddPrivateDatalayerEventInterface;
use Magento\Framework\Session\Generic as Session;

/**
 * Add event to the datalayer.
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class AddPrivateDatalayerEvent implements AddPrivateDatalayerEventInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(
        Session $session
    ) {
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $eventName, array $eventData): void
    {
        $eventName = 'set' . ucfirst($eventName);
        $this->session->$eventName($eventData);
    }
}

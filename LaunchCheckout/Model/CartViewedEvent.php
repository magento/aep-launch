<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCheckout\Model;

use Magento\Framework\View\LayoutInterface;
use Magento\Checkout\Block\Cart as CartBlock;
use Adobe\AxpConnector\Api\AddDatalayerEventInterface;

/**
 * Add datalayer events to the Cart View page.
 */
class CartViewedEvent
{
    /**
     * @var AddDatalayerEventInterface
     */
    private $addDatalayerEvent;

    /**
     * @var FormatCartViewedEvent
     */
    private $formatCartViewedEvent;

    /**
     * @param AddDatalayerEventInterface $addDatalayerEvent
     * @param FormatCartViewedEvent $formatCartViewedEvent
     */
    public function __construct(
        AddDatalayerEventInterface $addDatalayerEvent,
        FormatCartViewedEvent $formatCartViewedEvent
    ) {
        $this->addDatalayerEvent = $addDatalayerEvent;
        $this->formatCartViewedEvent = $formatCartViewedEvent;
    }

    /**
     * Add datalayer events to the Shopping Cart View page.
     *
     * @param LayoutInterface $layout
     * @return void
     */
    public function execute(LayoutInterface $layout)
    {
        /** @var CartBlock $cartBlock */
        $cartBlock = $layout->getBlock("checkout.cart");
        if ($cartBlock) {
            $cartItems = $cartBlock->getItems();
            $this->addDatalayerEvent->execute($this->formatCartViewedEvent->execute($cartItems));
        }
    }
}

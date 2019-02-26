<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCheckout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Adobe\AxpConnector\Api\AddPrivateDatalayerEventInterface;
use Adobe\AxpConnector\Model\Datalayer;
use Adobe\AxpConnector\Model\LaunchConfigProvider;
use Adobe\LaunchCheckout\Model\FormatAddToCartEvent;

/**
 * Observer for quote item remove.
 */
class CheckoutCartRemoveProductObserver implements ObserverInterface
{
    /**
     * @var Datalayer
     */
    private $datalayer;

    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @var AddPrivateDatalayerEventInterface
     */
    private $addPrivateDatalayerEvent;

    /**
     * @var FormatAddToCartEvent
     */
    private $formatAddToCartEvent;

    /**
     * @param Datalayer $datalayer
     * @param LaunchConfigProvider $launchConfigProvider
     * @param AddPrivateDatalayerEventInterface $addPrivateDatalayerEvent
     * @param FormatAddToCartEvent $formatAddToCartEvent
     */
    public function __construct(
        Datalayer $datalayer,
        LaunchConfigProvider $launchConfigProvider,
        AddPrivateDatalayerEventInterface $addPrivateDatalayerEvent,
        FormatAddToCartEvent $formatAddToCartEvent
    ) {
        $this->datalayer = $datalayer;
        $this->launchConfigProvider = $launchConfigProvider;
        $this->addPrivateDatalayerEvent = $addPrivateDatalayerEvent;
        $this->formatAddToCartEvent = $formatAddToCartEvent;
    }

    /**
     * @inheritdoc
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->launchConfigProvider->isEnabled()) {
            return;
        }

        $quoteItem = $observer->getData('quote_item');
        $qty = $quoteItem->getData('qty');

        $eventData = $this->formatAddToCartEvent->execute($qty, $quoteItem);
        $eventData['event'] = 'Product Removed';

        $this->addPrivateDatalayerEvent->execute('removeFromCartDatalayerContent', $eventData);
    }
}

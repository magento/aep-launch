<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCheckout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Adobe\Launch\Api\AddPrivateDatalayerEventInterface;
use Adobe\Launch\Model\LaunchConfigProvider;
use Adobe\LaunchCheckout\Model\FormatOrderPlacedEvent;

/**
 * Observer for checkout success event
 */
class CheckoutOnepageControllerSuccessActionObserver implements ObserverInterface
{
    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @var FormatOrderPlacedEvent
     */
    private $formatOrderPlacedEvent;

    /**
     * @var AddPrivateDatalayerEventInterface
     */
    private $addPrivateDatalayerEvent;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     * @param AddPrivateDatalayerEventInterface $addPrivateDatalayerEvent
     * @param FormatOrderPlacedEvent $formatOrderPlacedEvent
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        AddPrivateDatalayerEventInterface $addPrivateDatalayerEvent,
        FormatOrderPlacedEvent $formatOrderPlacedEvent
    ) {
        $this->addPrivateDatalayerEvent = $addPrivateDatalayerEvent;
        $this->launchConfigProvider = $launchConfigProvider;
        $this->formatOrderPlacedEvent = $formatOrderPlacedEvent;
    }

    /**
     * @inheritdoc
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->launchConfigProvider->isEnabled()) {
            return;
        }

        $orders = [];
        $singleOrder = $observer->getEvent()->getData('order');
        $multipleOrders = $observer->getEvent()->getData('orders');

        if ($singleOrder) {
            $orders = array_merge($orders, [$singleOrder]);
        }
        if ($multipleOrders) {
            $orders = array_merge($orders, $multipleOrders);
        }
        if (empty($orders)) {
            return;
        }

        $datalayerContent = $this->formatOrderPlacedEvent->execute($orders);
        $this->addPrivateDatalayerEvent->execute('OrderPlacedDatalayerContent', $datalayerContent);
    }
}

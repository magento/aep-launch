<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Observer;

use Adobe\AxpConnector\Model\Datalayer;
use Magento\Framework\Event\ObserverInterface;
use Adobe\AxpConnector\Model\LaunchConfigProvider;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Checkout\Model\Session;

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
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var Datalayer
     */
    private $datalayer;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     * @param Session $session
     * @param OrderRepositoryInterface $orderRepository
     * @param Datalayer $datalayer
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        Session $session,
        OrderRepositoryInterface $orderRepository,
        Datalayer $datalayer
    ) {
        $this->session = $session;
        $this->launchConfigProvider = $launchConfigProvider;
        $this->orderRepository = $orderRepository;
        $this->datalayer = $datalayer;
    }

    /**
     * @inheritdoc
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orders = [];

        $checkoutOrder = $observer->getEvent()->getOrder();
        $multishippingOrders = $observer->getEvent()->getOrders();

        if ($checkoutOrder) {
            $orders = array_merge($orders, [$checkoutOrder]);
        }
        if ($multishippingOrders) {
            $orders = array_merge($orders, $multishippingOrders);
        }
        if (empty($orders)) {
            return;
        }

        $datalayerContent = $this->datalayer->orderPlacedPushData($orders);
        $this->session->setOrderPlacedDatalayerContent($datalayerContent);
    }
}

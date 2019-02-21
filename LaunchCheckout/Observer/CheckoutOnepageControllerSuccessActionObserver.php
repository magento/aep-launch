<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCheckout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Session\Generic as Session;
use Adobe\AxpConnector\Model\LaunchConfigProvider;
use Adobe\AxpConnector\Model\Datalayer;

/**
 * Observer for checkout success event
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
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

        $datalayerContent = $this->datalayer->orderPlacedPushData($orders);
        $this->session->setOrderPlacedDatalayerContent($datalayerContent);
    }
}

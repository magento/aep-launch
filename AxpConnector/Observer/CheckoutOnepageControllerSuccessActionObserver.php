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

/**
 * Observer for checkout success event
 */
class CheckoutOnepageControllerSuccessActionObserver implements ObserverInterface
{
    /**
     * @var string
     * @deprecated Cookies should not be manipulated here.
     */
    const COOKIE_NAME = 'axpconnector_checkout_success';

    /**
     * Short duration, it just has to survive a page load
     * @deprecated Cookies should not be manipulated here.
     */
    const COOKIE_DURATION_SECS = 180;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     * @deprecated Cookies should not be manipulated here.
     */
    private $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     * @deprecated Cookies should not be manipulated here.
     */
    private $cookieMetadataFactory;

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
     * @param LaunchConfigProvider $launchConfigProvider
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param Datalayer $datalayer
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        OrderRepositoryInterface $orderRepository,
        Datalayer $datalayer
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->launchConfigProvider = $launchConfigProvider;
        $this->orderRepository = $orderRepository;
        $this->datalayer = $datalayer;
    }

    /**
     * @inheritdoc
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orders = [];
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        foreach ($orderIds as $orderId) {
            $orders[$orderId] = $this->orderRepository->get($orderId);
        }

        $datalayerContent = $this->datalayer->orderPlacedPushData($orders);
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(self::COOKIE_DURATION_SECS);
        $this->cookieManager->setPublicCookie(self::COOKIE_NAME, $datalayerContent, $metadata);
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCheckout\Model;

use Adobe\AxpConnector\Api\AddDatalayerEventInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\LayoutInterface;
use Magento\Checkout\Block\Onepage as CheckoutBlock;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\GuestCartRepositoryInterface;

/**
 * Add datalayer events to the Checkout start page.
 */
class CheckoutStartedEvent
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
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $guestCartRepository;

    /**
     * @param AddDatalayerEventInterface $addDatalayerEvent
     * @param FormatCartViewedEvent $formatCartViewedEvent
     * @param CartRepositoryInterface $cartRepository
     * @param GuestCartRepositoryInterface $guestCartRepository
     */
    public function __construct(
        AddDatalayerEventInterface $addDatalayerEvent,
        FormatCartViewedEvent $formatCartViewedEvent,
        CartRepositoryInterface $cartRepository,
        GuestCartRepositoryInterface $guestCartRepository
    ) {
        $this->addDatalayerEvent = $addDatalayerEvent;
        $this->formatCartViewedEvent = $formatCartViewedEvent;
        $this->cartRepository = $cartRepository;
        $this->guestCartRepository = $guestCartRepository;
    }

    /**
     * Add datalayer events to the Checkout page.
     *
     * @param LayoutInterface $layout
     * @return void
     */
    public function execute(LayoutInterface $layout)
    {
        /** @var CheckoutBlock $checkoutBlock */
        $checkoutBlock = $layout->getBlock("checkout.root");

        if (!$checkoutBlock) {
            return;
        }

        $checkoutConfig = $checkoutBlock->getCheckoutConfig();
        $cartId = $checkoutConfig['quoteData']['entity_id'];
        $isLoggedIn = $checkoutConfig['isCustomerLoggedIn'];
        try {
            $cart = $isLoggedIn ? $this->cartRepository->get($cartId) : $this->guestCartRepository->get($cartId);
        } catch (NoSuchEntityException $exception) {
            return;
        }

        $cartItems = $cart->getItems();
        $eventData = $this->formatCartViewedEvent->execute($cartItems);
        $eventData['event'] = 'Checkout Started';
        $this->addDatalayerEvent->execute($eventData);
    }
}

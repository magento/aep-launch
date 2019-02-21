<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCheckout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Session\Generic as Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Adobe\AxpConnector\Model\Datalayer;
use Adobe\AxpConnector\Model\LaunchConfigProvider;

/**
 * Observer for quote item remove.
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class SalesQuoteRemoveItemObserver implements ObserverInterface
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
     * @var Session
     */
    private $session;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param Datalayer $datalayer
     * @param LaunchConfigProvider $launchConfigProvider
     * @param ProductRepositoryInterface $productRepository
     * @param Session $session
     */
    public function __construct(
        Datalayer $datalayer,
        LaunchConfigProvider $launchConfigProvider,
        ProductRepositoryInterface $productRepository,
        Session $session
    ) {
        $this->datalayer = $datalayer;
        $this->launchConfigProvider = $launchConfigProvider;
        $this->session = $session;
        $this->productRepository = $productRepository;
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
        $productId = $quoteItem->getData('product_id');

        if (!$productId) {
            return;
        }

        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $exception) {
            return;
        }

        $qty = $quoteItem->getData('qty');

        $this->session->setRemoveFromCartDatalayerContent(
            $this->datalayer->removeFromCartPushData($qty, $product)
        );
    }
}

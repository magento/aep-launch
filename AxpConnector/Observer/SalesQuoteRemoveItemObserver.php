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
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Session;

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
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->launchConfigProvider->isEnabled()) {
            return;
        }

        $quoteItem = $observer->getData('quote_item');
        $productId = $quoteItem->getData('product_id');

        if (!$productId) {
            return;
        }

        $product = $this->productRepository->getById($productId);
        $qty = $quoteItem->getData('qty');

        $this->session->setRemoveFromCartDatalayerContent(
            $this->datalayer->removeFromCartPushData($qty, $product)
        );
    }
}

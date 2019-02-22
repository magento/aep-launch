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
use Magento\Framework\Exception\NoSuchEntityException;
use Adobe\AxpConnector\Api\AddPrivateDatalayerEventInterface;
use Adobe\AxpConnector\Model\Datalayer;
use Adobe\AxpConnector\Model\LaunchConfigProvider;
use Adobe\LaunchCheckout\Model\FormatAddToCartEvent;

/**
 * Observer for quote item remove.
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
     * @var AddPrivateDatalayerEventInterface
     */
    private $addPrivateDatalayerEvent;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var FormatAddToCartEvent
     */
    private $formatAddToCartEvent;

    /**
     * @param Datalayer $datalayer
     * @param LaunchConfigProvider $launchConfigProvider
     * @param ProductRepositoryInterface $productRepository
     * @param AddPrivateDatalayerEventInterface $addPrivateDatalayerEvent
     * @param FormatAddToCartEvent $formatAddToCartEvent
     */
    public function __construct(
        Datalayer $datalayer,
        LaunchConfigProvider $launchConfigProvider,
        ProductRepositoryInterface $productRepository,
        AddPrivateDatalayerEventInterface $addPrivateDatalayerEvent,
        FormatAddToCartEvent $formatAddToCartEvent
    ) {
        $this->datalayer = $datalayer;
        $this->launchConfigProvider = $launchConfigProvider;
        $this->addPrivateDatalayerEvent = $addPrivateDatalayerEvent;
        $this->productRepository = $productRepository;
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

        $eventData = $this->formatAddToCartEvent->execute($qty, $product);
        $eventData['event'] = 'Product Removed';

        $this->addPrivateDatalayerEvent->execute('removeFromCartDatalayerContent', $eventData);
    }
}

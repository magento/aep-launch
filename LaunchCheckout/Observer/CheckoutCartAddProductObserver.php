<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCheckout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Filter\LocalizedToNormalized;
use Adobe\AxpConnector\Api\AddPrivateDatalayerEventInterface;
use Adobe\AxpConnector\Model\LaunchConfigProvider;
use Adobe\LaunchCheckout\Model\FormatAddToCartEvent;

/**
 * Observer for Product Add to Cart.
 */
class CheckoutCartAddProductObserver implements ObserverInterface
{
    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * @var AddPrivateDatalayerEventInterface
     */
    private $addPrivateDatalayerEvent;

    /**
     * @var LocalizedToNormalized
     */
    private $localizedToNormalized;

    /**
     * @var FormatAddToCartEvent
     */
    private $formatAddToCartEvent;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     * @param ResolverInterface $localeResolver
     * @param AddPrivateDatalayerEventInterface $addPrivateDatalayerEvent
     * @param LocalizedToNormalized $localizedToNormalized
     * @param FormatAddToCartEvent $formatAddToCartEvent
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        ResolverInterface $localeResolver,
        AddPrivateDatalayerEventInterface $addPrivateDatalayerEvent,
        LocalizedToNormalized $localizedToNormalized,
        FormatAddToCartEvent $formatAddToCartEvent
    ) {
        $this->launchConfigProvider = $launchConfigProvider;
        $this->localeResolver = $localeResolver;
        $this->addPrivateDatalayerEvent = $addPrivateDatalayerEvent;
        $this->localizedToNormalized = $localizedToNormalized;
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

        $product = $observer->getData('product');
        $request = $observer->getData('request');

        $params = $request->getParams();
        if (isset($params['qty'])) {
            $this->localizedToNormalized->setOptions(['locale' => $this->localeResolver->getLocale()]);
            $qty = $this->localizedToNormalized->filter($params['qty']);
        } else {
            $qty = 1;
        }

        $datalayerContent = $this->formatAddToCartEvent->execute($qty, $product);
        $this->addPrivateDatalayerEvent->execute('AddToCartDatalayerContent', $datalayerContent);
    }
}

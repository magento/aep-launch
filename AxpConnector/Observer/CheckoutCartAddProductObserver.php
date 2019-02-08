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

/**
 * Observer for Product Add to Cart.
 */
class CheckoutCartAddProductObserver implements ObserverInterface
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
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $resolverInterface;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Framework\Filter\LocalizedToNormalized
     */
    private $localizedToNormalized;

    /**
     * @param Datalayer $datalayer
     * @param LaunchConfigProvider $launchConfigProvider
     * @param \Magento\Framework\Locale\ResolverInterface $resolverInterface
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Filter\LocalizedToNormalized $localizedToNormalized
     */
    public function __construct(
        Datalayer $datalayer,
        LaunchConfigProvider $launchConfigProvider,
        \Magento\Framework\Locale\ResolverInterface $resolverInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Filter\LocalizedToNormalized $localizedToNormalized
    ) {
        $this->datalayer = $datalayer;
        $this->launchConfigProvider = $launchConfigProvider;
        $this->resolverInterface = $resolverInterface;
        $this->checkoutSession = $checkoutSession;
        $this->localizedToNormalized = $localizedToNormalized;
    }

    /**
     * @inheritdoc
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->launchConfigProvider->isEnabled()) {
            return $this;
        }

        $product = $observer->getData('product');
        $request = $observer->getData('request');

        $params = $request->getParams();

        if (isset($params['qty'])) {
            $this->localizedToNormalized->setOptions(['locale' => $this->resolverInterface->getLocale()]);
            $qty = $this->localizedToNormalized->filter($params['qty']);
        } else {
            $qty = 1;
        }

        $datalayerContent = $this->datalayer->addToCartPushData($qty, $product);
        $this->checkoutSession->setAddToCartDatalayerContent($datalayerContent);

        return $this;
    }
}

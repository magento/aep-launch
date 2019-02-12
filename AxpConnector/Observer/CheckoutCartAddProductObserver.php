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
use Magento\Framework\Locale\ResolverInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Filter\LocalizedToNormalized;

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
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var LocalizedToNormalized
     */
    private $localizedToNormalized;

    /**
     * @param Datalayer $datalayer
     * @param LaunchConfigProvider $launchConfigProvider
     * @param ResolverInterface $localeResolver
     * @param Session $session
     * @param LocalizedToNormalized $localizedToNormalized
     */
    public function __construct(
        Datalayer $datalayer,
        LaunchConfigProvider $launchConfigProvider,
        ResolverInterface $localeResolver,
        Session $session,
        LocalizedToNormalized $localizedToNormalized
    ) {
        $this->datalayer = $datalayer;
        $this->launchConfigProvider = $launchConfigProvider;
        $this->localeResolver = $localeResolver;
        $this->session = $session;
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
            $this->localizedToNormalized->setOptions(['locale' => $this->localeResolver->getLocale()]);
            $qty = $this->localizedToNormalized->filter($params['qty']);
        } else {
            $qty = 1;
        }

        $datalayerContent = $this->datalayer->addToCartPushData($qty, $product);
        $this->session->setAddToCartDatalayerContent($datalayerContent);

        return $this;
    }
}

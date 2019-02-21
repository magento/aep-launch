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
use Magento\Framework\Session\Generic as Session;
use Magento\Framework\Filter\LocalizedToNormalized;
use Adobe\AxpConnector\Model\Datalayer;
use Adobe\AxpConnector\Model\LaunchConfigProvider;

/**
 * Observer for Product Add to Cart.
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
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

        $datalayerContent = $this->datalayer->addToCartPushData($qty, $product);
        $this->session->setAddToCartDatalayerContent($datalayerContent);
    }
}

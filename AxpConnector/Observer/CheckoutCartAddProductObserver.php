<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Observer;

use Magento\Framework\Event\ObserverInterface;
use Adobe\AxpConnector\Model\LaunchConfigProvider;

/**
 * Observer for Product Add to Cart.
 */
class CheckoutCartAddProductObserver implements ObserverInterface
{
    /**
     * @var \Adobe\AxpConnector\Helper\Data
     * @deprecated
     */
    private $helper;

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
     * @param \Adobe\AxpConnector\Helper\Data $helper
     * @param LaunchConfigProvider $launchConfigProvider
     * @param \Magento\Framework\Locale\ResolverInterface $resolverInterface
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Adobe\AxpConnector\Helper\Data $helper,
        LaunchConfigProvider $launchConfigProvider,
        \Magento\Framework\Locale\ResolverInterface $resolverInterface,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->helper = $helper;
        $this->launchConfigProvider = $launchConfigProvider;
        $this->resolverInterface = $resolverInterface;
        $this->checkoutSession = $checkoutSession;
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
            $filter = new \Zend_Filter_LocalizedToNormalized(
                ['locale' => $this->resolverInterface->getLocale()]
            );
            $qty = $filter->filter($params['qty']);
        } else {
            $qty = 1;
        }

        $datalayerContent = $this->helper->addToCartPushData($qty, $product);
        $this->checkoutSession->setAddToCartDatalayerContent($datalayerContent);

        return $this;
    }
}

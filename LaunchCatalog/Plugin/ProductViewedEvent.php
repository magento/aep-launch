<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCatalog\Plugin;

use Magento\Catalog\Block\Product\View as ProductViewBlock;
use Adobe\AxpConnector\Model\Command\AddDatalayerEvent;
use Adobe\AxpConnector\Model\LaunchConfigProvider;
use Adobe\LaunchCatalog\Model\FormatProductViewedEvent;

/**
 * Add datalayer information to the Product View page.
 */
class ProductViewedEvent
{
    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @var AddDatalayerEvent
     */
    private $addDatalayerEvent;

    /**
     * @var FormatProductViewedEvent
     */
    private $formatProductViewedEvent;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     * @param FormatProductViewedEvent $formatProductViewedEvent
     * @param AddDatalayerEvent $addDatalayerEvent
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        FormatProductViewedEvent $formatProductViewedEvent,
        AddDatalayerEvent $addDatalayerEvent
    ) {
        $this->launchConfigProvider = $launchConfigProvider;
        $this->formatProductViewedEvent = $formatProductViewedEvent;
        $this->addDatalayerEvent = $addDatalayerEvent;
    }

    /**
     * Add datalayer information to the Product View page.
     *
     * @param ProductViewBlock $subject
     * @param string $html
     * @return string
     */
    public function afterToHtml(ProductViewBlock $subject, string $html)
    {
        if (!$this->launchConfigProvider->isEnabled()) {
            return $html;
        }

        $product = $subject->getProduct();
        $this->addDatalayerEvent->execute($this->formatProductViewedEvent->execute($product));

        return $html;
    }
}

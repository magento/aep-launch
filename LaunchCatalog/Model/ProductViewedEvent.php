<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCatalog\Model;

use Magento\Catalog\Block\Product\View as ProductViewBlock;
use Adobe\Launch\Model\Command\AddDatalayerEvent;
use Magento\Framework\View\LayoutInterface;

/**
 * Add datalayer information to the Product View page.
 */
class ProductViewedEvent
{
    /**
     * @var AddDatalayerEvent
     */
    private $addDatalayerEvent;

    /**
     * @var FormatProductViewedEvent
     */
    private $formatProductViewedEvent;

    /**
     * @param FormatProductViewedEvent $formatProductViewedEvent
     * @param AddDatalayerEvent $addDatalayerEvent
     */
    public function __construct(
        FormatProductViewedEvent $formatProductViewedEvent,
        AddDatalayerEvent $addDatalayerEvent
    ) {
        $this->formatProductViewedEvent = $formatProductViewedEvent;
        $this->addDatalayerEvent = $addDatalayerEvent;
    }

    /**
     * Add product viewed event to the Datalayer.
     *
     * @param LayoutInterface $layout
     */
    public function execute(LayoutInterface $layout)
    {
        /** @var ProductViewBlock $productViewBlock */
        $productViewBlock = $layout->getBlock('product.info');
        $product = $productViewBlock->getProduct();
        $this->addDatalayerEvent->execute($this->formatProductViewedEvent->execute($product));
    }
}

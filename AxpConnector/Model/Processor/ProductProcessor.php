<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\AxpConnector\Model\Processor;

use Adobe\AxpConnector\Model\Datalayer;

class ProductProcessor
{
    /**
     * @var \Adobe\AxpConnector\ViewModel\Js
     */
    private $viewModel;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    private $blockFactory;

    /**
     * @var Datalayer
     */
    private $datalayer;

    public function __construct(
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Adobe\AxpConnector\ViewModel\Js $viewModel,
        Datalayer $datalayer
    ) {
        $this->blockFactory = $blockFactory;
        $this->viewModel = $viewModel;
        $this->datalayer = $datalayer;
    }

    /**
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function process(\Magento\Framework\View\LayoutInterface $layout) {
        /** @var \Magento\Catalog\Block\Product\View $productInfo */
        $productInfo = $layout->getBlock('product.info.review');

        $event = $this->datalayer->productViewedPushData($productInfo->getProduct());
        $this->datalayer->push($event);
    }
}

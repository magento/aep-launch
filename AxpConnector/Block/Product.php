<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Block;

use Magento\Framework\View\Element\Template;
use Adobe\AxpConnector\Model\Datalayer;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;

/**
 * Product block.
 *
 * @api
 */
class Product extends Template
{
    /**
     * @var Registry
     * @deprecated Registry is deprecated
     */
    private $registry;

    /**
     * @var Datalayer
     */
    private $datalayer;

    /**
     * @param Context $context
     * @param Datalayer $datalayer
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Datalayer $datalayer,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->datalayer = $datalayer;
    }

    /**
     * Product datalayer.
     *
     * @return string
     * @deprecated Due to redundancy
     */
    private function datalayerProduct(): string
    {
        return $this->datalayer->productViewedPushData($this->getCurrentProduct());
    }

    /**
     * Json product data layer.
     *
     * @return string
     */
    public function datalayerProductJson(): string
    {
        return $this->datalayerProduct();
    }

    /**
     * Getter for current product.
     *
     * @return mixed
     * @deprecated Due to usage of deprecated APIs
     */
    private function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }
}

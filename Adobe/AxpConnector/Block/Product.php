<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\AxpConnector\Block;

/**
 * Product block.
 */
class Product extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Adobe\AxpConnector\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Adobe\AxpConnector\Helper\Data $helper
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Adobe\AxpConnector\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->helper = $helper;
    }

    /**
     * Product datalayer.
     *
     * @return array
     */
    public function datalayerProduct()
    {
        return $this->helper->productViewedPushData($this->getCurrentProduct());
    }

    /**
     * Json product data layer.
     *
     * @return string
     */
    public function datalayerProductJson()
    {
        $datalayerProd = $this->datalayerProduct();
        return $this->helper->jsonify($datalayerProd);
    }

    /**
     * Getter for current product.
     *
     * @return mixed
     */
    protected function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }
}

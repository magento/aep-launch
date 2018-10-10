<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\AxpConnector\Block;

/**
 * Class Product
 * @package Adobe\AxpConnector\Block
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
     * Product constructor.
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
     * @return array
     */
    public function datalayerProduct()
    {
        return $this->helper->productViewedPushData($this->getCurrentProduct());
    }

    /**
     * @return string
     */
    public function datalayerProductJson()
    {
        $datalayerProd = $this->datalayerProduct();
        return $this->helper->jsonify($datalayerProd);
    }

    /**
     * @return mixed
     */
    protected function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

}
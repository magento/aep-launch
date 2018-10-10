<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\AxpConnector\Block;

/**
 * Class Checkout
 * @package Adobe\AxpConnector\Block
 */
class Checkout extends \Magento\Framework\View\Element\Template
{
    /**
     * Checkout constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Adobe\AxpConnector\Helper\Data $helper
     * @param \Magento\Checkout\Model\Cart $cartModel
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Adobe\AxpConnector\Helper\Data $helper,
        \Magento\Checkout\Model\Cart $cartModel,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->cartModel = $cartModel;
        $this->helper = $helper;
    }

    /**
     * @return array
     */
    public function datalayer()
    {
        return $this->helper->checkoutStartedPushData($this->cartModel);
    }

    /**
     * @return string
     */
    public function datalayerJson()
    {
        $datalayer = $this->datalayer();
        return $this->helper->jsonify($datalayer);
    }
}

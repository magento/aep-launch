<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Block;

/**
 * Cart Block.
 *
 * @api
 */
class Cart extends Base
{
    /**
     * @var \Magento\Checkout\Model\Cart $cartModel
     */
    protected $cartModel;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Adobe\AxpConnector\Helper\Data $helper
     * @param array $data
     * @param \Magento\Checkout\Model\Cart $cartModel
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Adobe\AxpConnector\Helper\Data $helper,
        array $data,
        \Magento\Checkout\Model\Cart $cartModel
    ) {
        parent::__construct($context, $helper, $data);
        $this->cartModel = $cartModel;
    }

    /**
     * Datalayer.
     *
     * @return array
     */
    public function datalayer()
    {
        return $this->helper->cartViewedPushData($this->cartModel);
    }

    /**
     * Json Datalayer.
     *
     * @return string
     */
    public function datalayerJson()
    {
        $datalayer = $this->datalayer();
        return $this->helper->jsonify($datalayer);
    }
}

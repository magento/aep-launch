<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Block;

use Adobe\AxpConnector\Model\Datalayer;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Cart as CartModel;

/**
 * Checkout block.
 *
 * @api
 */
class Checkout extends Template
{
    /**
     * @var CartModel $cartModel
     * @deprecated Model should not be used directly
     */
    private $cartModel;

    /**
     * @var Datalayer
     */
    private $datalayer;

    /**
     * @param Context $context
     * @param CartModel $cartModel
     * @param Datalayer $datalayer
     * @param array $data
     */
    public function __construct(
        Context $context,
        CartModel $cartModel,
        Datalayer $datalayer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cartModel = $cartModel;
        $this->datalayer = $datalayer;
    }

    /**
     * Json Datalayer.
     *
     * @depracated This method is only temporarily used as a part of refactoring routine.
     */
    public function datalayerJson()
    {
        return $this->datalayer->checkoutStartedPushData($this->cartModel);
    }
}

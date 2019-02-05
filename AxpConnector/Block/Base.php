<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Block;

use Adobe\AxpConnector\Model\LaunchConfigProvider;

/**
 * Base Block.
 *
 * @api
 */
class Base extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Adobe\AxpConnector\Helper\Data
     * @deprecated
     */
    protected $helper;

    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Adobe\AxpConnector\Helper\Data $helper
     * @param LaunchConfigProvider $launchConfigProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Adobe\AxpConnector\Helper\Data $helper,
        LaunchConfigProvider $launchConfigProvider,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->launchConfigProvider = $launchConfigProvider;
    }

    /**
     * AxpConnector Helper
     *
     * @return \Adobe\AxpConnector\Helper\Data
     * @deprecated
     */
    public function helper()
    {
        return $this->helper;
    }

    /**
     * Datalayer name
     *
     * @return string
     */
    public function datalayerName()
    {
        return $this->launchConfigProvider->getDatalayerName();
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

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Block;

use Adobe\AxpConnector\Model\LaunchConfigProvider;

/**
 * Block for AXP connector Datalayer initialization in page head.
 *
 * @api
 */
class Js extends \Magento\Framework\View\Element\Template
{
    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param LaunchConfigProvider $launchConfigProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        LaunchConfigProvider $launchConfigProvider,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->launchConfigProvider = $launchConfigProvider;
    }

    /**
     * Get name of the JS Datalayer object.
     *
     * @return string
     */
    public function getDatalayerName(): string
    {
        return $this->escapeJs($this->launchConfigProvider->getDatalayerName());
    }

    /**
     * Get src for the Adobe Launch script.
     *
     * @return string
     */
    public function getScriptUrl(): string
    {
        return $this->escapeUrl($this->launchConfigProvider->getScriptUrl());
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Adobe\AxpConnector\Model\LaunchConfigProvider;

/**
 * View Model for the Launch related blocks. Provides module configuration.
 */
class Js implements ArgumentInterface
{
    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     */
    public function __construct(LaunchConfigProvider $launchConfigProvider)
    {
        $this->launchConfigProvider = $launchConfigProvider;
    }

    /**
     * Get Launch script url.
     *
     * @return string
     */
    public function getScriptUrl(): ?string
    {
        return $this->launchConfigProvider->getScriptUrl();
    }

    /**
     * Get JS Datalayer object name.
     *
     * @return string
     */
    public function getDatalayerName(): ?string
    {
        return $this->launchConfigProvider->getDatalayerName();
    }
}

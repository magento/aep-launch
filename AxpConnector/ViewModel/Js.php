<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\ViewModel;

use Adobe\AxpConnector\Model\Command\GetAllDatalayerEvents;
use Adobe\AxpConnector\Model\Datalayer;
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
     * @var GetAllDatalayerEvents
     */
    private $getAllDatalayerEvents;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     * @param Datalayer $datalayer
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        \Adobe\AxpConnector\Model\Command\GetAllDatalayerEvents $getAllDatalayerEvents
    ) {
        $this->launchConfigProvider = $launchConfigProvider;
        $this->getAllDatalayerEvents = $getAllDatalayerEvents;
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

    public function getDatalayerEvents()
    {
        return $this->getAllDatalayerEvents->execute();
    }
}

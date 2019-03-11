<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\Launch\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Adobe\Launch\Api\GetAllDatalayerEventsInterface;
use Adobe\Launch\Model\LaunchConfigProvider;

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
     * @var GetAllDatalayerEventsInterface
     */
    private $getAllDatalayerEvents;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     * @param GetAllDatalayerEventsInterface $getAllDatalayerEvents
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        GetAllDatalayerEventsInterface $getAllDatalayerEvents
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

    /**
     * Return all events stored in the datalayer.
     *
     * @return array
     */
    public function getDatalayerEvents(): array
    {
        return $this->getAllDatalayerEvents->execute();
    }
}

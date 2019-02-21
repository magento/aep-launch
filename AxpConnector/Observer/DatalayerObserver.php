<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Observer;

use Adobe\AxpConnector\Model\LaunchConfigProvider;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Observer for tracking current layout.
 */
class DatalayerObserver implements ObserverInterface
{
    /**
     * @var array
     */
    private $datalayerProcessors;

    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     * @param array $datalayerProcessors
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        array $datalayerProcessors = []
    ) {
        $this->launchConfigProvider = $launchConfigProvider;
        $this->datalayerProcessors = $datalayerProcessors;
    }

    /**
     * Fires all registered layout event processors.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->launchConfigProvider->isEnabled()) {
            return;
        }

        $fullActionName = $observer->getEvent()->getData('full_action_name');
        /** @var \Magento\Framework\View\LayoutInterface $layout */
        $layout = $observer->getEvent()->getData('layout');

        if (isset($this->datalayerProcessors[$fullActionName])) {
            foreach ($this->datalayerProcessors[$fullActionName] as $processor) {
                $processor->execute($layout);
            }
        }
    }
}

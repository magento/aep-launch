<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Observer;

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
     * @param array $datalayerProcessors
     */
    public function __construct(array $datalayerProcessors = [])
    {
        $this->datalayerProcessors = $datalayerProcessors;
    }

    /**
     * @inheritdoc
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $fullActionName = $observer->getEvent()->getData('full_action_name');
        /** @var \Magento\Framework\View\LayoutInterface $layout */
        $layout = $observer->getEvent()->getData('layout');

        if (isset($this->datalayerProcessors[$fullActionName])) {
            $this->datalayerProcessors[$fullActionName]->process($layout);
        }
    }
}

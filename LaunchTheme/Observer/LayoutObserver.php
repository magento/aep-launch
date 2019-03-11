<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchTheme\Observer;

use Adobe\LaunchTheme\Model\RenderedBreadcrumbs;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Page\Title as PageTitle;
use Adobe\Launch\Model\LaunchConfigProvider;
use Adobe\Launch\Api\AddDatalayerEventInterface;
use Adobe\LaunchTheme\Model\FormatPageLoadedEvent;

/**
 * Observer tracking page load events.
 */
class LayoutObserver implements ObserverInterface
{
    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @var PageTitle
     */
    private $pageTitle;

    /**
     * @var AddDatalayerEventInterface
     */
    private $addDatalayerEvent;

    /**
     * @var FormatPageLoadedEvent
     */
    private $pageLoadedEvent;

    /**
     * @var RenderedBreadcrumbs
     */
    private $renderedBreadcrumbs;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     * @param PageTitle $pageTitle
     * @param FormatPageLoadedEvent $pageLoadedEvent
     * @param AddDatalayerEventInterface $addDatalayerEvent
     * @param RenderedBreadcrumbs $renderedBreadcrumbs
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        PageTitle $pageTitle,
        FormatPageLoadedEvent $pageLoadedEvent,
        AddDatalayerEventInterface $addDatalayerEvent,
        RenderedBreadcrumbs $renderedBreadcrumbs
    ) {
        $this->launchConfigProvider = $launchConfigProvider;
        $this->pageTitle = $pageTitle;
        $this->pageLoadedEvent = $pageLoadedEvent;
        $this->addDatalayerEvent = $addDatalayerEvent;
        $this->renderedBreadcrumbs = $renderedBreadcrumbs;
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
        $pageTitle = $this->pageTitle->getShort();
        $breadcrumbs = $this->renderedBreadcrumbs->getAllRenderedBreadcrumbs();

        $pageLoadedData = $this->pageLoadedEvent->execute($pageTitle, $fullActionName, $breadcrumbs);
        $this->addDatalayerEvent->execute($pageLoadedData);
    }
}

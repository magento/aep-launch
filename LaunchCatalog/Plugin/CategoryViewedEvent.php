<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCatalog\Plugin;

use Magento\Catalog\Block\Product\ListProduct as CategoryViewBlock;
use Adobe\Launch\Api\AddDatalayerEventInterface;
use Adobe\Launch\Model\LaunchConfigProvider;
use Adobe\LaunchCatalog\Model\FormatCategoryViewedEvent;

/**
 * Add datalayer events to the Category View page.
 */
class CategoryViewedEvent
{
    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @var AddDatalayerEventInterface
     */
    private $addDatalayerEvent;

    /**
     * @var FormatCategoryViewedEvent
     */
    private $formatCategoryViewedEvent;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     * @param AddDatalayerEventInterface $addDatalayerEvent
     * @param FormatCategoryViewedEvent $formatCategoryViewedEvent
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        AddDatalayerEventInterface $addDatalayerEvent,
        FormatCategoryViewedEvent $formatCategoryViewedEvent
    ) {
        $this->launchConfigProvider = $launchConfigProvider;
        $this->addDatalayerEvent = $addDatalayerEvent;
        $this->formatCategoryViewedEvent = $formatCategoryViewedEvent;
    }

    /**
     * Add datalayer events to the Category View page.
     *
     * @param CategoryViewBlock $subject
     * @param string $html
     * @return string
     */
    public function afterToHtml(CategoryViewBlock $subject, string $html)
    {
        if (!$this->launchConfigProvider->isEnabled()) {
            return $html;
        }

        $productCollection = $subject->getLoadedProductCollection();
        $toolbar = $subject->getToolbarBlock();

        $eventData = $this->formatCategoryViewedEvent->execute(
            $productCollection->count(),
            $productCollection->getSize(),
            $toolbar->getCurrentOrder(),
            $toolbar->getCurrentDirection()
        );

        $this->addDatalayerEvent->execute($eventData);

        return $html;
    }
}

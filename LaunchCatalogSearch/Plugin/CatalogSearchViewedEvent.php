<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCatalogSearch\Plugin;

use Magento\CatalogSearch\Block\Result as SearchResultBlock;
use Magento\Search\Helper\Data as SearchData;
use Adobe\AxpConnector\Model\Command\AddDatalayerEvent;
use Adobe\AxpConnector\Model\LaunchConfigProvider;
use Adobe\LaunchCatalog\Model\FormatCategoryViewedEvent;
use Adobe\LaunchCatalogSearch\Model\FormatCatalogSearchViewedEvent;

/**
 * Add datalayer events to the Category View page.
 */
class CatalogSearchViewedEvent
{
    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @var AddDatalayerEvent
     */
    private $addDatalayerEvent;

    /**
     * @var FormatCategoryViewedEvent
     */
    private $formatCategoryViewedEvent;

    /**
     * @var FormatCatalogSearchViewedEvent
     */
    private $formatCatalogSearchViewedEvent;

    /**
     * @var SearchData
     */
    private $searchData;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     * @param AddDatalayerEvent $addDatalayerEvent
     * @param FormatCategoryViewedEvent $formatCategoryViewedEvent
     * @param FormatCatalogSearchViewedEvent $formatCatalogSearchViewedEvent
     * @param SearchData $searchData
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        AddDatalayerEvent $addDatalayerEvent,
        FormatCategoryViewedEvent $formatCategoryViewedEvent,
        FormatCatalogSearchViewedEvent $formatCatalogSearchViewedEvent,
        SearchData $searchData
    ) {
        $this->launchConfigProvider = $launchConfigProvider;
        $this->addDatalayerEvent = $addDatalayerEvent;
        $this->formatCategoryViewedEvent = $formatCategoryViewedEvent;
        $this->formatCatalogSearchViewedEvent = $formatCatalogSearchViewedEvent;
        $this->searchData = $searchData;
    }

    /**
     * Add datalayer events to the Category View page.
     *
     * @param SearchResultBlock $subject
     * @param string $html
     * @return string
     */
    public function afterToHtml(SearchResultBlock $subject, string $html)
    {
        if (!$this->launchConfigProvider->isEnabled()) {
            return $html;
        }

        $productListBlock = $subject->getListBlock();
        $productCollection = $productListBlock->getLoadedProductCollection();
        $toolbar = $productListBlock->getToolbarBlock();

        $catalogEventData = $this->formatCategoryViewedEvent->execute(
            $productCollection->count(),
            $productCollection->getSize(),
            $toolbar->getCurrentOrder(),
            $toolbar->getCurrentDirection()
        );
        $searchEventData = $this->formatCatalogSearchViewedEvent->execute($this->searchData->getEscapedQueryText());

        $this->addDatalayerEvent->execute(array_merge_recursive($catalogEventData, $searchEventData));

        return $html;
    }
}

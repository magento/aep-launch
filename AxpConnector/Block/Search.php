<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Block;

use Magento\Framework\View\Element\Template;
use Adobe\AxpConnector\Model\Datalayer;
use Magento\Framework\View\Element\Template\Context;
use Magento\CatalogSearch\Helper\Data as CatalogSearchHelper;
use Magento\Catalog\Block\Product\ListProduct;

/**
 * Search Block.
 *
 * @api
 */
class Search extends Template
{
    /**
     * @var \Magento\CatalogSearch\Helper\Data
     * @deprecated Public APIs should be used instead of helpers where possible
     */
    private $catalogSearchHelper;

    /**
     * @var Datalayer
     */
    private $datalayer;

    /**
     * @param Context $context
     * @param Datalayer $datalayer
     * @param CatalogSearchHelper $catalogSearchHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Datalayer $datalayer,
        CatalogSearchHelper $catalogSearchHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->datalayer = $datalayer;
        $this->catalogSearchHelper = $catalogSearchHelper;
    }

    /**
     * User query text
     *
     * @return string
     */
    private function getQueryText(): string
    {
        return $this->catalogSearchHelper->getEscapedQueryText();
    }

    /**
     * Product listing sort direction
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     * @deprecated Request should not be used directly
     */
    private function getListDirection(): string
    {
        $sortOrder = $this->getRequest()->getParam('product_list_dir');
        if ($sortOrder) {
            return $sortOrder;
        } else {
            return 'desc';
        }
    }

    /**
     * Product listing order
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     * @deprecated Request should not be used directly
     */
    private function getListOrder(): string
    {
        $listOrder = $this->getRequest()->getParam('product_list_order');
        if ($listOrder) {
            return $listOrder;
        } else {
            return 'relevance';
        }
    }

    /**
     * Search results datalayer.
     *
     * @return string|null
     * @deprecaed Due to redundancy
     */
    private function datalayer(): ?string
    {
        /** @var ListProduct $searchResultListBlock */
        $searchResultListBlock = $this->_layout->getBlock('search_result_list');

        if (empty($searchResultListBlock)) {
            return null;
        }

        $collection = $searchResultListBlock->getLoadedProductCollection();
        $resultsCount = $collection->getSize();
        $resultsShown = count($collection);
        $queryText = $this->getQueryText();
        $listOrder = $this->getListOrder();
        $listDirection = $this->getListDirection();

        return $this->datalayer->searchResultsPushData(
            $resultsShown,
            $resultsCount,
            $listOrder,
            $listDirection,
            $queryText
        );
    }

    /**
     * Json search results datalayer.
     *
     * @return string|null
     */
    public function datalayerJson(): ?string
    {
        return $this->datalayer();
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Block;

use Adobe\AxpConnector\Model\LaunchConfigProvider;

/**
 * Search Block.
 *
 * @api
 */
class Search extends Base
{
    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $catalogSearchHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Adobe\AxpConnector\Helper\Data $helper
     * @param LaunchConfigProvider $launchConfigProvider
     * @param array $data
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Adobe\AxpConnector\Helper\Data $helper,
        LaunchConfigProvider $launchConfigProvider,
        array $data,
        \Magento\CatalogSearch\Helper\Data $catalogSearchHelper
    ) {
        parent::__construct($context, $helper, $launchConfigProvider, $data);
        $this->helper = $helper;
        $this->catalogSearchHelper = $catalogSearchHelper;
    }

    /**
     * User query text
     *
     * @return string
     */
    public function getQueryText()
    {
        return $this->catalogSearchHelper->getEscapedQueryText();
    }

    /**
     * Product listing sort direction
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     */
    public function getListDirection()
    {
        $sortOrder = $this->_request->getParam('product_list_dir');
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
     */
    public function getListOrder()
    {
        $listOrder = $this->_request->getParam('product_list_order');
        if ($listOrder) {
            return $listOrder;
        } else {
            return 'relevance';
        }
    }

    /**
     * Search results datalayer.
     *
     * @return array
     */
    public function datalayer()
    {
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

        return $this->helper->searchResultsPushData(
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
     * @return array
     */
    public function datalayerJson()
    {
        $datalayer = $this->datalayer();
        return $this->helper->jsonify($datalayer);
    }
}

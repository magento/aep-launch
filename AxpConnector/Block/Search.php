<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Block;

/**
 * Search Block.
 *
 * @api
 */
class Search extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Adobe\AxpConnector\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $catalogSearchHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Adobe\AxpConnector\Helper\Data $helper
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Adobe\AxpConnector\Helper\Data $helper,
        \Magento\CatalogSearch\Helper\Data $catalogSearchHelper,
        array $data
    ) {
        parent::__construct($context, $data);
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

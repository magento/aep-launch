<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Block;

/**
 * Listing Block.
 *
 * @api
 */
class Listing extends \Magento\Framework\View\Element\Template
{
    /**
    * @var \Adobe\AxpConnector\Helper\Data
    */
    protected $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Adobe\AxpConnector\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Adobe\AxpConnector\Helper\Data $helper,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
    * Product listing sort direction
    *
    * @return string
    */
    public function getListDirection()
    {
        $sortOrder = $this->_request->getParam('product_list_dir');
        if($sortOrder) {
            return $sortOrder;
        } else {
            return 'asc';
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
        if($listOrder) {
            return $listOrder;
        } else {
            return 'position';
        }
    }

    /**
    * Category page datalayer.
    *
    * @return array
    */
    public function datalayer()
    {
        $categoryBlock = $this->_layout->getBlock('category.products.list');

        if(empty($categoryBlock)) {
            return null;
        }

        $collection = $categoryBlock->getLoadedProductCollection();
        $resultsCount = $collection->getSize();
        $resultsShown = count($collection);
        $listOrder = $this->getListOrder();
        $sortDirection = $this->getListDirection();

        return $this->helper->categoryViewedPushData($resultsShown, $resultsCount, $listOrder, $sortDirection);
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
<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Block;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Adobe\AxpConnector\Model\Datalayer;

/**
 * Listing Block.
 *
 * @api
 */
class Listing extends Template
{
    /**
     * @var Datalayer
     */
    private $datalayer;

    /**
     * @param Context $context
     * @param Datalayer $datalayer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Datalayer $datalayer,
        array $data = []
    ) {
        $this->datalayer = $datalayer;
        parent::__construct($context, $data);
    }

    /**
     * Product listing sort direction
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     * @deprecated Must be refactored to avoid pulling data from request.
     */
    private function getListDirection(): string
    {
        $sortOrder = $this->getRequest()->getParam('product_list_dir');
        if ($sortOrder) {
            return $sortOrder;
        } else {
            return 'asc';
        }
    }

    /**
     * Product listing order
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     * @deprecated Must be refactored to avoid pulling data from request.
     */
    private function getListOrder(): string
    {
        $listOrder = $this->getRequest()->getParam('product_list_order');
        if ($listOrder) {
            return $listOrder;
        } else {
            return 'position';
        }
    }

    /**
     * Category page datalayer.
     *
     * @return string|null
     * @throws LocalizedException
     * @deprecated Due to redundancy
     */
    private function datalayer(): ?string
    {
        /** @var ListProduct $categoryBlock */
        $categoryBlock = $this->getLayout()->getBlock('category.products.list');

        if (empty($categoryBlock)) {
            return null;
        }

        $collection = $categoryBlock->getLoadedProductCollection();
        $resultsCount = $collection->getSize();
        $resultsShown = count($collection);
        $listOrder = $this->getListOrder();
        $sortDirection = $this->getListDirection();

        return $this->datalayer->categoryViewedPushData($resultsShown, $resultsCount, $listOrder, $sortDirection);
    }

    /**
     * Json search results datalayer.
     *
     * @return string|null
     * @depracated This method is only temporarily used as a part of refactoring routine.
     */
    public function datalayerJson(): ?string
    {
        try {
            $datalayer = $this->datalayer();
        } catch (LocalizedException $exception) {
            return null;
        }

        return $datalayer;
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCatalog\Model;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Format Product Viewed datalayer event data.
 */
class FormatProductViewedEvent
{
    /**
     * Format Product Viewed datalayer event data.
     *
     * @param ProductInterface $product
     * @return array
     */
    public function execute(ProductInterface $product): array
    {
        $result = [
            'event' => 'Product Viewed',
            'product' => []
        ];

        $item = [
            'productInfo' => [
                'productName'   =>$product->getName(),
                'productID' => $product->getSku(),
                'productPrice' => $product ->getPrice(),
                'productImage' => $product ->getthumbnail()
            ]
        ];

        $result['product'][] = $item;

        return $result;
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCheckout\Model;

/**
 * Format Cart Product Added datalayer event data.
 */
class FormatAddToCartEvent
{
    /**
     * Format Cart Product Added datalayer event data.
     *
     * @param int $qty
     * @param mixed $product
     * @return array
     */
    public function execute($qty, $product): array
    {
        $result = [];

        $result['event'] = 'Product Added';
        $result['product'] = [];

        $item = [];
        $item['quantity'] = strval($qty);
        $item['productInfo'] = [];
        $item['productInfo']['sku'] = $product->getSku();
        $item['productInfo']['productID'] = $product->getData('sku');

        array_push($result['product'], $item);

        return $result;
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCheckout\Model;

use Magento\Quote\Api\Data\CartItemInterface;

/**
 * Format Cart Viewed datalayer event data.
 */
class FormatCartViewedEvent
{
    /**
     * Format Cart Viewed datalayer event data.
     *
     * @param CartItemInterface[] $cartItems
     * @return array
     */
    public function execute(array $cartItems): array
    {
        $result = [];
        $cart = [];
        $items = [];

        foreach ($cartItems as $item) {
            /** @var CartItemInterface $item */
            $items[] = [
                'quantity' => $item->getQty(),
                'productInfo' => [
                    'sku' => $item->getSku(),
                    'productID' => $item->getProduct()->getData('sku')
                ],
                'price' => [
                    'sellingPrice' => $item->getPrice()
                ]
            ];
        }
        $cart['item'] = $items;
        $result['event'] = 'Cart Viewed';
        $result['cart'] = $cart;

        return $result;
    }
}

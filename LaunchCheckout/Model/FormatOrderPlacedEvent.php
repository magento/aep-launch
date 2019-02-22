<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCheckout\Model;

/**
 * Format Order placed datalayer event data.
 */
class FormatOrderPlacedEvent
{
    /**
     * Order placed event data.
     *
     * @param array $orders
     * @return array
     * @depracated Logic needs to be reviewed.
     */
    public function execute(array $orders): array
    {
        $result = [];

        foreach ($orders as $order) {
            $orderObject = [
                'event' => 'Order Placed',
                'transaction' => [
                    'transactionID' => $order->getIncrementId(),
                    'total' => [
                        'currency' => $order->getOrderCurrencyCode()
                    ],
                    'shippingGroup' => [],
                    'profile' => [
                        'address' => []
                    ],
                    'item' => []
                ]
            ];

            $shippingGroup = [
                'tax' => $order->getShippingTaxAmount(),
                'shippingCost' => $order->getShippingAmount(),
                'groupId' => '1'
            ];
            $orderObject['transaction']['shippingGroup'][] = $shippingGroup;

            $billingAddress = $order->getBillingAddress();
            $orderObject['transaction']['profile']['address']['stateProvince'] = $billingAddress->getRegionCode();
            $orderObject['transaction']['profile']['address']['postalCode'] = $billingAddress->getPostcode();

            foreach ($order->getAllVisibleItems() as $item) {
                $itemData = [
                    'shippingGroupID' => '1',
                    'quantity' => $item->getQtyOrdered(),
                    'productInfo' => [
                        'sku' => $item->getSku(),
                        'productID' => $item->getProduct()->getData('sku')
                    ],
                    'price' => [
                        'sellingPrice' => $item->getPrice()
                    ]
                ];

                $orderObject['transaction']['item'][] = $itemData;
            }

            $result[] = $orderObject;
        }

        return $result;
    }
}

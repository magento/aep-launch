<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * This is a prototype class, temporary introduced for refactoring purposes.
 */
class Datalayer
{
    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @param Json $jsonSerializer
     */
    public function __construct(Json $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Push data on cart view.
     *
     * @param mixed $cartModel
     * @return string
     * @depracated This method is only temporarily used as a part of refactoring routine.
     */
    public function cartViewedPushData($cartModel): string
    {
        $collection = $cartModel->getQuote()->getAllVisibleItems();
        $result = [];
        $cart = [];

        $items = [];
        foreach ($collection as $item) {
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

        return $this->jsonSerializer->serialize($result);
    }

    /**
     * Push data on checkout start.
     *
     * @param mixed $cartModel
     * @return string
     * @depracated This method is only temporarily used as a part of refactoring routine.
     */
    public function checkoutStartedPushData($cartModel): string
    {
        $result = $this->jsonSerializer->unserialize($this->cartViewedPushData($cartModel));
        $result['event'] = 'Checkout Started';

        return $this->jsonSerializer->serialize($result);
    }

    /**
     * Push data on category page view.
     *
     * @param int $resultsShown
     * @param int $resultsCount
     * @param string $listOrder
     * @param string $sortDirection
     * @return string
     * @depracated This method is only temporarily used as a part of refactoring routine.
     */
    public function categoryViewedPushData($resultsShown, $resultsCount, $listOrder, $sortDirection): string
    {
        $result = [
            'event' => 'Listing Viewed',
            'listing' => [
                'listingResults' => [
                    'resultsShown' => $resultsShown,
                    'resultsCount' => $resultsCount
                ],
                'listingParams' => [
                    'sorts' => []
                ]
            ]
        ];

        $result['listing']['listingParams']['sorts'][] = [
            'sortOrder' => $sortDirection,
            'sortKey' => $listOrder
        ];

        return $this->jsonSerializer->serialize($result);
    }

    /**
     * Push data when page is loaded (?)
     *
     * @param string $pageTitle
     * @param string $pageType
     * @param array $breadcrumbs
     * @return string
     * @depracated This method is only temporarily used as a part of refactoring routine.
     */
    public function pageLoadedPushData($pageTitle, $pageType, $breadcrumbs): string
    {
        $result = [
            'event' => 'Page Loaded',
            'page' => [
                'pageType' => $pageType,
                'pageName' => $pageTitle,
                'breadcrumbs' => $breadcrumbs
            ]
        ];

        return $this->jsonSerializer->serialize($result);
    }

    /**
     * Data for product view (?)
     *
     * @param ProductInterface $product
     * @return string
     * @depracated This method is only temporarily used as a part of refactoring routine.
     */
    public function productViewedPushData($product): string
    {
        $result = [
            'event' => 'Product Viewed',
            'product' => []
        ];

        $item = [
            'productInfo' => [
                'productID' => $product->getSku()
            ]
        ];

        $result['product'][] = $item;

        return $this->jsonSerializer->serialize($result);
    }

    /**
     * Push data on search results page view.
     *
     * @param int $resultsShown
     * @param int $resultsCount
     * @param string $listOrder
     * @param string $sortDirection
     * @param string $queryText
     * @return string
     * @depracated This method is only temporarily used as a part of refactoring routine.
     */
    public function searchResultsPushData($resultsShown, $resultsCount, $listOrder, $sortDirection, $queryText): string
    {
        $result = [
            'event' => 'Listing Viewed',
            'listing' => [
                'listingResults' => [
                    'resultsShown' => $resultsShown,
                    'resultsCount' => $resultsCount
                ],
                'listingParams' => [
                    'sorts' => [],
                    'searchInfo' => [
                        'searchTermEntered' => $queryText,
                        'searchTermCorrected' => $queryText
                    ]
                ]
            ]
        ];

        $result['listing']['listingParams']['sorts'][] = [
            'sortOrder' => $sortDirection,
            'sortKey' => $listOrder
        ];

        return $this->jsonSerializer->serialize($result);
    }
}

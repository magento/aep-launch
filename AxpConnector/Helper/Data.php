<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class Data.
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Data constructor.
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->storeManager = $storeManager;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Check if module is enabled in config.
     *
     * @param string $scope
     * @return bool
     */
    public function isEnabled($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->isSetFlag(
            'axpconnector_backend_config/general/enable',
            $scope
        );
    }

    /**
     * Get Url for the Launch script from configuration.
     *
     * @param string $scope
     * @return mixed
     */
    public function getScriptUrl($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            'axpconnector_backend_config/general/launch_script_url',
            $scope
        );
    }

    /**
     * Push data whan page is loaded (?)
     *
     * @param string $pageTitle
     * @param string $pageType
     * @param string $breadcrumbs
     * @return array
     */
    public function pageLoadedPushData($pageTitle, $pageType, $breadcrumbs)
    {
        $result = [
            'event' => 'Page Loaded',
            'page' => [
                'pageType' => $pageType,
                'pageName' => $pageTitle,
                'breadcrumbs' => $breadcrumbs
            ]
        ];

        return $result;
    }

    /**
     * Data for product view (?)
     *
     * @param ProductInterface $product
     * @return array
     */
    public function productViewedPushData($product)
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

        return $result;
    }

    /**
     * Push data on add to cart (?)
     *
     * @param int $qty
     * @param ProductInterface $product
     * @return array
     */
    public function addToCartPushData($qty, $product)
    {
        $result = [];

        $result['event'] = 'Product Added';
        $result['product'] = [];

        $item = [];
        $item['quantity'] = strval($qty);
        $item['productInfo'] = [];
        $item['productInfo']['sku'] = $product->getSku();
        $item['productInfo']['productId'] = $product->getId();

        array_push($result['product'], $item);

        return $result;
    }

    /**
     * Push data on cart view.
     *
     * @param CartInterface $cartModel
     * @return array
     */
    public function cartViewedPushData($cartModel)
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

        return $result;
    }

    /**
     * Push data on search results page view.
     *
     * @param int $resultsShown
     * @param int $resultsCount
     * @param string $listOrder
     * @param string $sortDirection
     * @param string $queryText
     * @return array
     *
     */
    public function searchResultsPushData($resultsShown, $resultsCount, $listOrder, $sortDirection, $queryText)
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

        return $result;
    }


    /**
     * Push data on category page view.
     *
     * @param int $resultsShown
     * @param int $resultsCount
     * @param string $listOrder
     * @param string $sortDirection
     * @return array
     *
     */
    public function categoryViewedPushData($resultsShown, $resultsCount, $listOrder, $sortDirection) {
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

        return $result;
    }

    /**
     * Push data on checkout start.
     *
     * @param CartInterface $cartModel
     * @return array
     */
    public function checkoutStartedPushData($cartModel)
    {
        $result = $this->cartViewedPushData($cartModel);
        $result['event'] = 'Checkout Started';

        return $result;
    }

    /**
     * Push data on removal from the cart.
     *
     * @param int $qty
     * @param ProductInterface $product
     * @return array
     */
    public function removeFromCartPushData($qty, $product)
    {
        // It's very similar to product added
        $result = $this->addToCartPushData($qty, $product);
        $result['event'] = 'Product Removed';

        return $result;
    }

    /**
     * Push data on order placed.
     *
     * @param array $orderIds
     * @return array
     */
    public function orderPlacedPushData($orderIds)
    {
        $result = [];

        foreach ($orderIds as $orderId) {
            $order = $this->orderRepository->get($orderId);

            $this->logger->addInfo("Order Retrieved: {$order->getIncrementId()}");

            $orderObject = [
                'event' => 'Order Placed',
                'transaction' => [
                    'transactionID' => $order->getIncrementId(),
                    'total' => [
                        'currency' => $order->getOrderCurrencyCode()
                    ]
                ],
                'shippingGroup' => [],
                'profile' => [
                    'address' => []
                ],
                'item' => []
            ];

            // TODO - Multi-shipping
            $shippingGroup = [
                'tax' => $order->getShippingTaxAmount(),
                'shippingCost' => $order->getShippingAmount(),
                'groupId' => '1'
            ];
            $orderObject['shippingGroup'][] = $shippingGroup;

            $billingAddress = $order->getBillingAddress();
            $orderObject['profile']['address']['stateProvince'] = $billingAddress->getRegionCode();
            $orderObject['profile']['address']['postalCode'] = $billingAddress->getPostcode();

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

                $orderObject['item'][] = $itemData;
            }

            $result[] = $orderObject;
        }

        $logData = $this->jsonify($result);
        $this->logger->addInfo("Result Object: {$logData}");

        return $result;
    }

    /**
     * Json Encode (??)
     *
     * @param mixed $obj
     * @return string
     */
    public function jsonify($obj)
    {
        return $this->jsonHelper->jsonEncode($obj);
    }
}

<?php

namespace SearchDiscovery\AxpConnector\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{

  /**
   * @var \Magento\Store\Model\StoreManagerInterface
   */
  protected $storeManager;

  protected $jsonHelper;

  protected $orderRepository;

  protected $logger;

  public function __construct(
    \Magento\Framework\Json\Helper\Data $jsonHelper,
    \Magento\Framework\App\Helper\Context $context,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
    \Psr\Log\LoggerInterface $logger
  )
  {
    $this->jsonHelper = $jsonHelper;
    $this->storeManager = $storeManager;
    $this->orderRepository = $orderRepository;
    $this->logger = $logger;
    parent::__construct($context);
  }

  public function isEnabled($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
  {
    return $this->scopeConfig->isSetFlag(
      'launchbyadobe_backend_config/general/enable',
      $scope
    );
  }

  public function getScriptUrl($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
  {
    return $this->scopeConfig->getValue(
      'launchbyadobe_backend_config/general/launch_script_url',
      $scope
    );
  }

  public function pageLoadedPushData($pageTitle, $pageType, $breadcrumbs) {
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
   * @param int $qty
   * @param \Magento\Catalog\Model\Product $product
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
   * @param \Magento\Checkout\Model\Cart $cartModel
   * @return array
   */
  public function cartViewedPushData($cartModel) {
    $collection = $cartModel->getQuote()->getAllVisibleItems();
    $result = array();
    $cart = array();

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
   * @param \Magento\Checkout\Model\Cart $cartModel
   * @return array
   */
  public function checkoutStartedPushData($cartModel) {
    $result = $this->cartViewedPushData($cartModel);
    $result['event'] = 'Checkout Started';

    return $result;
  }

  /**
   * @param int $qty
   * @param \Magento\Catalog\Model\Product $product
   * @return array
   */
  public function removeFromCartPushData($qty, $product)
  {
    // It's very similar to product added
    $result = $this->addToCartPushData($qty, $product);
    $result['event'] = 'Product Removed';

    return $result;
  }

  public function orderPlacedPushData($orderIds)
  {
    $result = [];

    foreach($orderIds as $orderId)
    {
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

      foreach($order->getAllVisibleItems() as $item)
      {
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

  public function jsonify($obj)
  {
    return $this->jsonHelper->jsonEncode($obj);
  }
}
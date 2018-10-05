<?php
namespace SearchDiscovery\LaunchByAdobe\Block;

class Cart extends \Magento\Framework\View\Element\Template
{

  protected $_logger;

  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \SearchDiscovery\LaunchByAdobe\Helper\Data $helper,
    \Psr\Log\LoggerInterface $logger,
    \Magento\Checkout\Model\Cart $cartModel,
    array $data
  )
  {
    parent::__construct($context, $data);
    $this->cartModel = $cartModel;
    $this->helper = $helper;
    $this->_logger = $logger;
    $this->datalayerEvents = [];

  }

  public function getCartCollection() {
    return $this->cartModel->getQuote()->getAllVisibleItems();
  }

  public function datalayer() {
    $collection = $this->getCartCollection();
    $datalayer = array();
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
    $datalayer['event'] = 'Cart Viewed';
    $datalayer['cart'] = $cart;

    return $datalayer;
  }

  public function datalayerJson() {
    $datalayer= $this->datalayer();
    return $this->helper->jsonify($datalayer);
  }

  public function log($msg) {
    $this->_logger->addInfo($msg);
  }
}

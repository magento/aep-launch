<?php
namespace SearchDiscovery\LaunchByAdobe\Block;

class Product extends \Magento\Framework\View\Element\Template
{

  protected $datalayerEvents;

  protected $_logger;

  protected $registry;

  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \SearchDiscovery\LaunchByAdobe\Helper\Data $helper,
    \Magento\Framework\Registry $registry,
    \Psr\Log\LoggerInterface $logger,
    array $data
  )
  {
    parent::__construct($context, $data);
    $this->registry = $registry;
    $this->helper = $helper;
    $this->_logger = $logger;
    $this->datalayerEvents = [];

  }

  public function getCurrentProduct() {
    return $this->registry->registry('current_product');
  }

  public function datalayerProduct() {
    $product = $this->getCurrentProduct();
    $datalayerProduct = array();


    if($product) {
      $prodInfo = array(
        'productID' => $product->getId()
      );

      $datalayerProduct['event'] = 'Product Viewed';
      $datalayerProduct['product'] = [];

      array_push($datalayerProduct['product'], $prodInfo);
    }

    return $datalayerProduct;
  }

  public function datalayerProductJson() {
    $datalayerProd = $this->datalayerProduct();
    return $this->helper->jsonify($datalayerProd);
  }

  public function log($msg) {
    $this->_logger->addInfo($msg);
  }
}
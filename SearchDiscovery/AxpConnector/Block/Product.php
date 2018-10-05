<?php
namespace SearchDiscovery\AxpConnector\Block;

class Product extends \Magento\Framework\View\Element\Template
{
  protected $helper;

  protected $registry;

  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \SearchDiscovery\AxpConnector\Helper\Data $helper,
    \Magento\Framework\Registry $registry,
    array $data
  )
  {
    parent::__construct($context, $data);
    $this->registry = $registry;
    $this->helper = $helper;

  }

  public function datalayerProduct() {
    return $this->helper->productViewedPushData($this->getCurrentProduct());
  }

  public function datalayerProductJson() {
    $datalayerProd = $this->datalayerProduct();
    return $this->helper->jsonify($datalayerProd);
  }

  protected function getCurrentProduct() {
    return $this->registry->registry('current_product');
  }

}
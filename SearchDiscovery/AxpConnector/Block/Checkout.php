<?php
namespace SearchDiscovery\LaunchByAdobe\Block;

class Checkout extends \Magento\Framework\View\Element\Template
{
  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \SearchDiscovery\LaunchByAdobe\Helper\Data $helper,
    \Magento\Checkout\Model\Cart $cartModel,
    array $data
  )
  {
    parent::__construct($context, $data);
    $this->cartModel = $cartModel;
    $this->helper = $helper;
  }

  public function datalayer() {
    return $this->helper->checkoutStartedPushData($this->cartModel);
  }

  public function datalayerJson() {
    $datalayer = $this->datalayer();
    return $this->helper->jsonify($datalayer);
  }
}

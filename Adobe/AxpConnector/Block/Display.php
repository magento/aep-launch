<?php
namespace Adobe\AxpConnector\Block;

class Display extends \Magento\Framework\View\Element\Template
{

  protected $datalayerEvents;

  protected $_logger;

  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Adobe\AxpConnector\Helper\Data $helper,
    \Psr\Log\LoggerInterface $logger
  )
  {
    parent::__construct($context);
    $this->helper = $helper;
    $this->_logger = $logger;
    $this->datalayerEvents = [];
  }

  public function isEnabled() {
    return $this->helper->isEnabled();
  }

  public function pushDatalayerEvent($eventData)
  {
    array_push($this->datalayerEvents, $eventData);
  }

  public function encodedDatalayerEvents()
  {
    $encoded = array_map(array($this->helper, 'jsonify'), $this->datalayerEvents);
    return $encoded;
  }
}
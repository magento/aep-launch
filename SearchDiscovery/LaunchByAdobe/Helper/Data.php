<?php

namespace SearchDiscovery\LaunchByAdobe\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{
  public function __construct(
    \Magento\Framework\App\Helper\Context $context,
    \Psr\Log\LoggerInterface $logger
  )
  {
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
}
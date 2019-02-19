<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Configuration provider for Adobe Launch related entities.
 */
class LaunchConfigProvider
{
    private const LAUNCH_INTEGRATION_ENABLED = 'axpconnector_backend_config/general/enable';

    private const LAUNCH_SCRIPT_URL = 'axpconnector_backend_config_datalayer/datalayer/launch_script_url';

    private const LAUNCH_DATALAYER_NAME = 'axpconnector_backend_config_datalayer/datalayer/datalayer_name';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if module is enabled in configuration.
     *
     * @param string $scope
     * @return bool
     */
    public function isEnabled(?string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): bool
    {
        return $this->scopeConfig->isSetFlag(self::LAUNCH_INTEGRATION_ENABLED, $scope);
    }

    /**
     * Get Url for the Launch script from configuration.
     *
     * @param string $scope
     * @return string|null
     */
    public function getScriptUrl(?string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): ?string
    {
        return $this->scopeConfig->getValue(self::LAUNCH_SCRIPT_URL, $scope);
    }

    /**
     * Get datalayer name from configuration.
     *
     * @param string $scope
     * @return string|null
     */
    public function getDatalayerName(?string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): ?string
    {
        return $this->scopeConfig->getValue(self::LAUNCH_DATALAYER_NAME, $scope);
    }
}

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
    private const LAUNCH_INTEGRATION_ENABLED = 'launch_general_config/general/enable';
    private const LAUNCH_SCRIPT_URL = 'launch_datalayer_config/general/launch_script_url';
    private const LAUNCH_DATALAYER_NAME = 'launch_datalayer_config/general/datalayer_name';

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
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::LAUNCH_INTEGRATION_ENABLED);
    }

    /**
     * Get Url for the Launch script from configuration.
     *
     * @return string|null
     */
    public function getScriptUrl(): ?string
    {
        return $this->scopeConfig->getValue(self::LAUNCH_SCRIPT_URL);
    }

    /**
     * Get datalayer name from configuration.
     *
     * @return string|null
     */
    public function getDatalayerName(): ?string
    {
        return $this->scopeConfig->getValue(self::LAUNCH_DATALAYER_NAME);
    }
}

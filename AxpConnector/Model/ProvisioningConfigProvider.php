<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Configuration provider for Adobe Launch property provisioning.
 */
class ProvisioningConfigProvider
{
    private const LAUNCH_PROVISION_ADOBE_ORG_ID = 'launch_general_config/general/adobe_org_id';

    private const LAUNCH_PROVISION_CLIENT_ID = 'launch_general_config/general/client_id';

    private const LAUNCH_PROVISION_CLIENT_SECRET = 'launch_general_config/general/client_secret';

    private const LAUNCH_PROVISION_CLIENT_JWT = 'launch_general_config/general/jwt';

    private const LAUNCH_PROVISION_PROD_SUITE= 'launch_backend_config_datalayer/general/prod_suite';

    private const LAUNCH_PROVISION_STAGING_SUITE = 'launch_backend_config_datalayer/general/stage_suite';

    private const LAUNCH_PROVISION_DEV_SUITE = 'launch_backend_config_datalayer/general/dev_suite';

    private const LAUNCH_PROVISION_PROPERTY_NAME = 'launch_datalayer_config/general/property_name';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    /**
     * Get Adobe Org ID from configuration.
     *
     * @param string $scope
     * @return string|null
     */
    public function getOrgID(?string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): ?string
    {
        return $this->scopeConfig->getValue(self::LAUNCH_PROVISION_ADOBE_ORG_ID, $scope);
    }

    /**
     * Get Client ID from configuration.
     *
     * @param string $scope
     * @return string|null
     */
    public function getClientID(?string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): ?string
    {
        return $this->scopeConfig->getValue(self::LAUNCH_PROVISION_CLIENT_ID, $scope);
    }

    /**
     * Get Client secret from configuration.
     *
     * @param string $scope
     * @return string
     */
    public function getClientSecret(?string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): string
    {
        $cypherText = $this->scopeConfig->getValue(self::LAUNCH_PROVISION_CLIENT_SECRET, $scope);
        return $this->encryptor->decrypt($cypherText);
    }

    /**
     * Get JWT from configuration.
     *
     * @param string $scope
     * @return string|null
     */
    public function getJWT(?string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): ?string
    {
        return $this->scopeConfig->getValue(self::LAUNCH_PROVISION_CLIENT_JWT, $scope);
    }

    /**
     * Get Production AA suite from configuration.
     *
     * @param string $scope
     * @return string|null
     */
    public function getProdSuite(?string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): ?string
    {
        return $this->scopeConfig->getValue(self::LAUNCH_PROVISION_PROD_SUITE, $scope);
    }

    /**
     * Get Stage AA suite from configuration.
     *
     * @param string $scope
     * @return string|null
     */
    public function getStageSuite(?string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): ?string
    {
        return $this->scopeConfig->getValue(self::LAUNCH_PROVISION_STAGING_SUITE, $scope);
    }

    /**
     * Get Dev AA suite from configuration.
     *
     * @param string $scope
     * @return string|null
     */
    public function getDevSuite(?string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): ?string
    {
        return $this->scopeConfig->getValue(self::LAUNCH_PROVISION_DEV_SUITE, $scope);
    }

    /**
     * Get Launch property name from configuration.
     *
     * @param string $scope
     * @return string|null
     */
    public function getPropertyName(?string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): ?string
    {
        return $this->scopeConfig->getValue(self::LAUNCH_PROVISION_PROPERTY_NAME, $scope);
    }
}

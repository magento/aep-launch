<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\Launch\Model;

use Adobe\Launch\Webservice\Client\ProvisionClient;
use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Launch Property Provisioning
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity) Class has to be redesigned.
 */
class ProvisionAgent
{
    private const TOKEN_URI = 'https://ims-na1.adobelogin.com/ims/exchange/jwt/';

    private const ADOBE_IO_LAUNCH_HOSTNAME = 'reactor.adobe.io';

    private const ADOBE_LAUNCH_HOSTNAME = 'launch.adobe.com';

    private const LAUNCH_PROPERTY_NAME_PREFIX = 'Magento Auto-Provisioned';

    private const AA_RS_PROD = 'rs_prod';

    private const AA_RS_STAGE = 'rs_stage';

    private const AA_RS_DEV = 'rs_dev';

    private const EXTENSION_MAP = [
        ['src' => 'adobe-analytics', 'target' => 'LAUNCH_EXT_PACKAGE_ID_ADOBE_ANALYTICS'],
        ['src' => 'adobe-target', 'target' => 'LAUNCH_EXT_PACKAGE_ID_ADOBE_TARGET'],
        ['src' => 'adobe-mcid', 'target' => 'LAUNCH_EXT_PACKAGE_ID_ADOBE_EXPERIENCE_CLOUD'],
        ['src' => 'constant-dataelement', 'target' => 'LAUNCH_EXT_PACKAGE_ID_CONSTANT_DATA_ELEMENT'],
        ['src' => 'aa-product-string-search-discovery', 'target' => 'LAUNCH_EXT_PACKAGE_ID_SDI_PRODUCT_STR'],
        ['src' => 'sdi-toolkit', 'target' => 'LAUNCH_EXT_PACKAGE_ID_SDI_TOOLKIT'],
        ['src' => 'data-layer-manager-search-discovery', 'target' => 'LAUNCH_EXT_PACKAGE_ID_SDI_DATALAYER_MGR'],
    ];

    /**
     * @var ProvisionClient
     */
    private $provisionClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @var ProvisioningConfigProvider
     */
    private $provisioningConfigProvider;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @param ProvisionClient $provisionClient
     * @param LaunchConfigProvider $launchConfigProvider
     * @param Json $jsonSerializer
     * @param ProvisioningConfigProvider $provisioningConfigProvider
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProvisionClient $provisionClient,
        LaunchConfigProvider $launchConfigProvider,
        Json $jsonSerializer,
        ProvisioningConfigProvider $provisioningConfigProvider,
        LoggerInterface $logger
    ) {
        $this->provisionClient = $provisionClient;
        $this->provisioningConfigProvider = $provisioningConfigProvider;
        $this->jsonSerializer = $jsonSerializer;
        $this->logger = $logger;
        $this->launchConfigProvider = $launchConfigProvider;
    }

    /**
     * Make the API requests
     *
     * @param array $conf
     * @return array
     */
    public function makeRequests($conf)
    {
        $propertyName = $this->provisioningConfigProvider->getPropertyName();
        if ($propertyName === null) {
            $propertyName = self::LAUNCH_PROPERTY_NAME_PREFIX;
        }
        $aa_prod = $this->provisioningConfigProvider->getProdSuite();
        if ($aa_prod === null) {
            $aa_prod = self::AA_RS_PROD;
        }
        $aa_stage = $this->provisioningConfigProvider->getStageSuite();
        if ($aa_stage === null) {
            $aa_stage = self::AA_RS_STAGE;
        }
        $aa_dev = $this->provisioningConfigProvider->getDevSuite();
        if ($aa_dev === null) {
            $aa_dev = self::AA_RS_DEV;
        }
        $result = ['error' => null, 'success' => false, 'complete' => false];
        $config = [
            'TOKEN_URI' => self::TOKEN_URI,
            'DATA_ELEMENT_CALLS' => $this->jsonSerializer->unserialize($conf['variables']['dataElementAPIcalls']),
            'RULE_CALLS' => $this->jsonSerializer->unserialize($conf['variables']['ruleAPIcalls']),
            'RULE_COMPONENT_CALLS' => $this->jsonSerializer->unserialize($conf['variables']['ruleComponentAPIcalls']),
            'ADOBE_IO_CLIENT_ID' => $this->provisioningConfigProvider->getClientID(),
            'ADOBE_IO_CLIENT_SECRET' => $this->provisioningConfigProvider->getClientSecret(),
            'ADOBE_IO_JWT' => $this->provisioningConfigProvider->getJWT(),
            'ADOBE_EC_ORG_ID' => $this->provisioningConfigProvider->getOrgID(),
            'ADOBE_IO_LAUNCH_HOSTNAME' => self::ADOBE_IO_LAUNCH_HOSTNAME,
            'LAUNCH_PROPERTY_NAME_PREFIX' => $propertyName,
            'AA_RS_PROD' => $aa_prod,
            'AA_RS_STAGE' => $aa_stage,
            'AA_RS_DEV' => $aa_dev,
            'EXTENSION_IDS' => [],
            'LAUNCH_PROPERTY_NAME' => $propertyName.' '.date("Y-m-d H:i:s"),
            'LAUNCH_COMPANY_ID' => '',
            'DATA_LAYER_OBJECT_NAME' => $this->launchConfigProvider->getDatalayerName(),
            'RULE_COMPONENT_REQUEST' => $this->findRuleComponentRequest($conf['item']),
            'rules' => '[]',
            'data_elements' => '[]',
            'extensions' => '[]',
        ];

        foreach ($conf['item'] as $request) {
            if ($result['complete'] === true) {
                break;
            }
            // Send notification to the front end
            print $request['request']['description'].'|';
            flush();
            ob_flush();
            $requestName = $request['name'];
            $this->logger->debug('DebugMakeRequests', ['requestName' => $requestName,
                'method_exists' => method_exists($this, $requestName)]);
            $result = $this->executeRequestMethod($requestName, $request['request'], $result, $config);
        }
        if (!$result['complete']) {
            $link = 'https://'.self::ADOBE_LAUNCH_HOSTNAME.'/companies/'.$config['LAUNCH_COMPANY_ID'].'/properties/'.
                $config['LAUNCH_PROPERTY_ID'].'/environments';
            $result = ['error' => null, 'success' => true, 'complete' => true, 'link' => $link];
        }
        return $result;
    }

    /**
     * Execute the request
     *
     * @param string $requestName
     * @param array $request
     * @param array $result
     * @param array $config
     * @return array
     */
    private function executeRequestMethod($requestName, $request, $result, &$config)
    {
        if (method_exists($this, $requestName)) {
            $normRequest = $this->normalizeRequest($request, $config);
            if (array_key_exists('error', $normRequest)) {
                $response = ['error' => $normRequest['error'].' in '.$requestName];
            } else {
                $response = $this->$requestName($normRequest, $config);
            }
            if (array_key_exists('error', $response)) {
                $result = ['error' => $response['error'], 'success' => false, 'complete' => true,
                    'method' => $requestName, 'request' => $normRequest];
            }
        }
        return $result;
    }

    // =========== Private Methods referenced and called from the provision_config.json file ===========
    // We suppress the UnusedPrivateMethod warning because they are used, but not in explicit function calls
    // Instead, they are called via executeRequestMethod

    /**
     * Create the token
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createBearerToken($request, &$config)
    {
        $request['code'] = 200;
        $request['url'] = $config['TOKEN_URI'];
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('access_token', $response)) {
            $config['ADOBE_IO_ACCESS_TOKEN'] = $response['access_token'];
        }

        if ($response && array_key_exists('error', $response)) {
            if (strpos($response['error'], 'client_secret') !== false) {
                $response = ['error' => 'The Client Secret is invalid.'];
            } elseif (strpos($response['error'], 'client_id') !== false) {
                $response = ['error' => 'The Client ID is invalid.'];
            } elseif (strpos($response['error'], 'JWT') !== false) {
                $response = ['error' => 'The JWT is invalid.'];
            }
        }

        return $response;
    }

    /**
     * Get the company ID
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getCompanyID($request, &$config)
    {
        $request['code'] = 200;
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            foreach ($response['data'] as $item) {
                if ($item['attributes']['org_id'] === $config['ADOBE_EC_ORG_ID']) {
                    $config['LAUNCH_COMPANY_ID'] = $item['id'];
                }
            }
        }

        return $response;
    }

    /**
     * Create the property
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createProperty($request, &$config)
    {
        $request['code'] = 201;
        if ($config['LAUNCH_COMPANY_ID'] === '') {
            return ['error' => 'The company ID was not found. Please check the Adobe Org ID.'];
        }
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_PROPERTY_ID'] = $response['data']['id'];
        }

        return $response;
    }

    /**
     * Create the adapter
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createAdapter($request, &$config)
    {
        $request['code'] = 201;
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_ADAPTER_ID'] = $response['data']['id'];
        }

        return $response;
    }

    /**
     * Create the dev environment
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createDevEnvironment($request, &$config)
    {
        $request['code'] = 201;
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_ENV_ID_DEV'] = $response['data']['id'];
        }

        return $response;
    }

    /**
     * Create the stage environment
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createStageEnvironment($request, &$config)
    {
        $request['code'] = 201;
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_ENV_ID_STAGE'] = $response['data']['id'];
        }

        return $response;
    }

    /**
     * Create the prod environment
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createProdEnvironment($request, &$config)
    {
        $request['code'] = 201;
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_ENV_ID_PROD'] = $response['data']['id'];
        }

        return $response;
    }

    /**
     * Create the library
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createLibrary($request, &$config)
    {
        $request['code'] = 201;
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_LIB_ID_CONFIG'] = $response['data']['id'];
        }

        return $response;
    }

    /**
     * Set the environment
     *
     * @param array $request
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function setLibraryEnvironmentToDev($request)
    {
        $request['code'] = 200;
        $response = $this->makeStandardRequest($request);

        return $response;
    }

    /**
     * Get ext package IDs
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getExtensionPackageIDs($request, &$config)
    {
        $request['code'] = 200;
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            foreach (self::EXTENSION_MAP as $map) {
                foreach ($response['data'] as $resp) {
                    if ($resp['attributes']['name'] === $map['src']) {
                        $config[$map['target']] = $resp['id'];
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Get the core package ID
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getCoreExtensionID($request, &$config)
    {
        $request['code'] = 200;
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            foreach ($response['data'] as $resp) {
                if ($resp['attributes']['name'] === 'core') {
                    $config['LAUNCH_EXT_ID_CORE'] = $resp['id'];
                }
            }
        }

        return $response;
    }

    /**
     * Create AA ext
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createAAExtension($request, &$config)
    {
        $request['code'] = 201;
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_EXT_ID_ADOBE_ANALYTICS'] = $response['data']['id'];
        }

        return $response;
    }

    /**
     * Create ECID ext
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createECIDExtension($request, &$config)
    {
        $request['code'] = 201;
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_EXT_ID_ADOBE_EXPERIENCE_CLOUD'] = $response['data']['id'];
        }

        return $response;
    }

    /**
     * Create Constant Data Element ext
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createConstantDataElementExtension($request, &$config)
    {
        $request['code'] = 201;
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_EXT_ID_CONSTANT_DATA_ELEMENT'] = $response['data']['id'];
        }

        return $response;
    }

    /**
     * Get Target codes
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getTargetClientCode($request, &$config)
    {
        $request['code'] = 200;
        $response = $this->makeStandardRequest($request);
        $clientCode = 'clientCode';
        $globalMboxName = 'globalMboxName';
        if ($response && array_key_exists('clientCode', $response)
            && array_key_exists('globalMboxName', $response)) {
            $clientCode = $response['clientCode'];
            $globalMboxName = $response['globalMboxName'];
        }
        $config['TARGET_CLIENT_CODE'] = $clientCode;
        $config['TARGET_GLOBAL_MBOX'] = $globalMboxName;

        return $response;
    }

    /**
     * Create Target ext
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createTargetExtension($request, &$config)
    {
        if ($config['TARGET_CLIENT_CODE'] && $config['TARGET_GLOBAL_MBOX']) {
            $request['code'] = 201;
            $response = $this->makeStandardRequest($request);
            if ($response && array_key_exists('data', $response)) {
                $config['LAUNCH_EXT_ID_ADOBE_TARGET'] = $response['data']['id'];
            }

            return $response;
        } else {
            return [];
        }
    }

    /**
     * Create SDI toolkit ext
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createSDIToolkitExtension($request, &$config)
    {
        $request['code'] = 201;
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_EXT_ID_SDI_TOOLKIT'] = $response['data']['id'];
        }

        return $response;
    }

    /**
     * Create SDI product string ext
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createSDIProductStringExtension($request, &$config)
    {
        $request['code'] = 201;
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_EXT_ID_SDI_PRODUCT_STR'] = $response['data']['id'];
        }

        return $response;
    }

    /**
     * Create SDI data layer mgr
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createSDIDataLayerManagerExtension($request, &$config)
    {
        $request['code'] = 201;
        $response = $this->makeStandardRequest($request);
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_EXT_ID_SDI_DATALAYER_MGR'] = $response['data']['id'];
        }

        return $response;
    }

    /**
     * Create data elements
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createDataElement($request, &$config)
    {
        $request['code'] = 201;
        $response = [];
        foreach ($config['DATA_ELEMENT_CALLS'] as $dataElementCall) {
            $newBody = null;
            $newBody = $this->replaceValues($this->jsonSerializer->serialize($dataElementCall['body']), $config);
            $request['body'] = $newBody;
            $response = $this->makeStandardRequest($request);
            if ($response && array_key_exists('error', $response)) {
                break;
            }
        }

        return $response;
    }

    /**
     * Create data elements
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createRule($request, $config)
    {
        $request['code'] = 201;
        $response = [];
        foreach ($config['RULE_CALLS'] as $ruleCall) {
            $newBody = null;
            $newBody = $this->replaceValues($this->jsonSerializer->serialize($ruleCall['body']), $config);
            $request['body'] = $newBody;
            $response = $this->makeStandardRequest($request);
            if ($response && array_key_exists('error', $response)) {
                break;
            } elseif ($response && array_key_exists('data', $response)) {
                $config['Launch_Rule_id'] = $response['data']['id'];
                $ruleName = $ruleCall['name'];
                $componentResponse = $this->createRuleAddRuleComponents($ruleName, $request, $config);
                if (array_key_exists('error', $componentResponse)) {
                    $response =  $componentResponse;
                    break;
                }
            }
        }

        return $response;
    }

    /**
     * Add a rule component to the current rule
     *
     * @param string $ruleName
     * @param array $ruleRequest
     * @param array $config
     * @return array
     */
    private function createRuleAddRuleComponents($ruleName, $ruleRequest, $config)
    {
        $response = [];
        foreach ($config['RULE_COMPONENT_CALLS'] as $componentCall) {
            if ($componentCall['ruleName'] === $ruleName) {
                $config['Launch_Rule_name'] = $ruleName;
                $componentRequest = $this->makeRuleComponentRequest($componentCall, $ruleRequest, $config);
                if ($componentRequest && array_key_exists('error', $componentRequest)) {
                    $response['error'] = 'There was a problem creating the rule component request for '
                        .$componentCall['name'];
                    break;
                } else {
                    $componentResponse = $this->makeStandardRequest($componentRequest);
                    if ($componentResponse && array_key_exists('error', $componentResponse)) {
                        $response['error'] = $componentResponse['error'];
                        break;
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Find the rule component request
     *
     * @param array $conf
     * @return array $request
     */
    private function findRuleComponentRequest($conf)
    {
        $req = [];
        foreach ($conf as $request) {
            if ($request['name'] === 'createRuleComponent') {
                $req = $request['request'];
            }
        }
        return $req;
    }

    /**
     * Get list of unpublished rules
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getUnpublishedRules($request, &$config)
    {
        $config['resources'] = '[]';
        $request['code'] = 200;
        $response = $this->handleResourceResponse($this->makeStandardRequest($request), $config);

        return $response;
    }

    /**
     * Get list of unpublished data elements
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getUnpublishedDataElements($request, &$config)
    {
        $request['code'] = 200;
        $response = $this->handleResourceResponse($this->makeStandardRequest($request), $config);

        return $response;
    }

    /**
     * Get list of unpublished extensions
     *
     * @param array $request
     * @param array $config
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getUnpublishedDataExtensions($request, &$config)
    {
        $request['code'] = 200;
        $response = $this->handleResourceResponse($this->makeStandardRequest($request), $config);

        return $response;
    }

    /**
     * Set unpublished resources
     *
     * @param array $request
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function setUnpublishedRules($request)
    {
        $request['code'] = 200;
        $response = $this->makeStandardRequest($request);

        return $response;
    }

    /**
     * Set unpublished resources
     *
     * @param array $request
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function setUnpublishedDataElements($request)
    {
        $request['code'] = 200;
        $response = $this->makeStandardRequest($request);

        return $response;
    }

    /**
     * Set unpublished resources
     *
     * @param array $request
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function setUnpublishedExtensions($request)
    {
        $request['code'] = 200;
        $response = $this->makeStandardRequest($request);

        return $response;
    }

    /**
     * Set unpublished resources
     *
     * @param array $request
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function setUnpublishedEnvironments($request)
    {
        $request['code'] = 200;
        $response = $this->makeStandardRequest($request);

        return $response;
    }

    /**
     * Build the dev library
     *
     * @param array $request
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function buildDevLibrary($request)
    {
        $request['code'] = 201;
        $response = $this->makeStandardRequest($request);

        return $response;
    }

    /**
     * Make a rule component request
     *
     * @param array $componentCall
     * @param array $ruleRequest
     * @param array $config
     * @return array
     */
    private function makeRuleComponentRequest($componentCall, $ruleRequest, $config)
    {
        $request = [];
        if (is_array($componentCall)) {
            try {
                $config['Rule_Component_settings'] = $this->jsonSerializer->serialize($componentCall['body']['data']['attributes']['settings']);
                $config['Rule_Component_order'] = $componentCall['body']['data']['attributes']['order'];
                $config['Rule_Component_extension_id'] = $this->replaceValues($componentCall['body']['data']['relationships']['extension']['data']['name'], $config);
                $config['Rule_Component_name'] = $componentCall['body']['data']['attributes']['name'];
                $config['Rule_Component_delegate_descriptor_id'] = $componentCall['body']['data']['attributes']
                ['delegate_descriptor_id'];

                $request = $this->normalizeRequest($config['RULE_COMPONENT_REQUEST'], $config);
                $request['code'] = 201;
                $request['enctype'] = $ruleRequest['enctype'];
            } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
            }
        }
        return $request;
    }

    /**
     * Make a single request
     *
     * @param array $request
     * @return array
     */
    private function makeStandardRequest($request)
    {
        return $this->provisionClient->request(
            $request['url'],
            $request['method'],
            $request['header'],
            $request['code'],
            $request['body'],
            $request['enctype']
        );
    }

    /**
     * Handle a resource response
     *
     * @param array $response
     * @param array $config
     * @return array
     */
    private function handleResourceResponse($response, &$config)
    {
        if ($response && array_key_exists('data', $response) && !array_key_exists('error', $response)) {
            $rules = $this->jsonSerializer->unserialize($config['rules']);
            $dataElements = $this->jsonSerializer->unserialize($config['data_elements']);
            $extensions = $this->jsonSerializer->unserialize($config['extensions']);

            foreach ($response['data'] as $resource) {
                switch ($resource['type']) {
                    case 'rules':
                        $rules[] = [
                            'id' => $resource['id'],
                            'type' => $resource['type'],
                            'meta' => ['action' => 'revise']
                        ];
                        break;
                    case 'data_elements':
                        $dataElements[] = [
                            'id' => $resource['id'],
                            'type' => $resource['type'],
                            'meta' => ['action' => 'revise']
                        ];
                        break;
                    case 'extensions':
                        $extensions[] = [
                            'id' => $resource['id'],
                            'type' => $resource['type'],
                            'meta' => ['action' => 'revise']
                        ];
                        break;
                }
            }
            $config['rules'] = $this->jsonSerializer->serialize($rules);
            $config['data_elements'] = $this->jsonSerializer->serialize($dataElements);
            $config['extensions'] = $this->jsonSerializer->serialize($extensions);
        }
        return $response;
    }

    /**
     * Transform the request
     *
     * @param array $request
     * @param array $config
     * @return array
     */
    private function normalizeRequest($request, $config)
    {
        $normalizedRequest = [];
        try {
            // Set the body
            if ($request['body']['mode'] === 'formdata' && !empty($request['body']['formdata'])) {
                $normalizedRequest['body'] = [];
                foreach ($request['body']['formdata'] as $param) {
                    $normalizedRequest['body'][$param['key']] = $this->replaceValues($param['value'], $config);
                }
            } elseif ($request['body']['mode'] === 'raw' && $request['body']['raw'] !== '') {
                $normalizedRequest['body'] = $this->replaceValues($request['body']['raw'], $config);
            } else {
                $normalizedRequest['body'] = null;
            }

            // Set the URL
            $normalizedRequest['url'] = $this->replaceValues($request['url']['raw'], $config);

            // Set the header
            $normalizedRequest['header'] = [];
            foreach ($request['header'] as $param) {
                $normalizedRequest['header'][$param['key']] = $this->replaceValues($param['value'], $config);
                if ($param['key'] === 'Content-Type') {
                    $normalizedRequest['enctype'] = $param['value'];
                }
            }

            // Set the method
            $normalizedRequest['method'] = $request['method'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }

        return $normalizedRequest;
    }

    /**
     * Replace variables in a string
     *
     * @param string $str
     * @param array $config
     * @return string
     */
    private function replaceValues($str, $config)
    {
        if (preg_match_all("/{{(.*?)}}/", $str, $matches)) {
            foreach ($matches[1] as $i => $varName) {
                if (array_key_exists($varName, $config)) {
                    $str = str_replace($matches[0][$i], sprintf('%s', $config[$varName]), $str);
                }
            }
        }
        return $str;
    }
}

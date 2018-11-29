<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Model\Config\Backend;

/**
 * Class AdobeOrgId
 * @package Adobe\AxpConnector\Model\Config\Backend
 */
class AdobeOrgId extends \Magento\Framework\App\Config\Value
{
    /**
     * Did they include the Org ID?
     *
     * Ex: your-org-id-here@AdobeOrg
     */
    const ORG_ID_REGEX = '/^(.*)@AdobeOrg$/';

    /**
     * @inheritdoc
     *
     * @return \Magento\Framework\App\Config\Value|void
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function beforeSave()
    {
        $label = $this->getData('field_config/label');
        $testVal = $this->getValue();

        if (!preg_match(self::ORG_ID_REGEX, $testVal) || $testVal === 'your-org-id-here@AdobeOrg') {
            throw new \Magento\Framework\Exception\ValidatorException(__(
                $label .
                ' must be a valid Adobe Org ID.'
            ));
        }

        parent::beforeSave();
    }
}

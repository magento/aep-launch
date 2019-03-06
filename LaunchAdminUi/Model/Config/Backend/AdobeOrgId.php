<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchAdminUi\Model\Config\Backend;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\App\Config\Value;

/**
 * Adobe Organization Id configuration source.
 */
class AdobeOrgId extends Value
{
    /**
     * Verify Adobe Organization Id.
     *
     * Example: your-org-id-here@AdobeOrg
     */
    private const ORG_ID_REGEX = '/^(.*)@AdobeOrg$/';

    /**
     * @inheritdoc
     *
     * @return Value
     * @throws ValidatorException
     */
    public function beforeSave()
    {
        $label = $this->getData('field_config/label');

        if (!preg_match(self::ORG_ID_REGEX, $this->getValue())) {
            throw new ValidatorException(__('%1 must be a valid Adobe Org ID.', $label));
        }

        return parent::beforeSave();
    }
}

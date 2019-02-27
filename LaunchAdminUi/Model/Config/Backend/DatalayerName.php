<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchAdminUi\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\ValidatorException;

/**
 * Datalayer name configuration source
 */
class DatalayerName extends Value
{
    /**
     * Test for a valid JavaScript variable name.
     *
     * For simplicity's sake, this restricts to ASCII values, rather than all Unicode identifiers.
     *
     * Example valid: "AppEventData", "_AppEventData", "$AppEventData", "MyDataLayer", "_myDataLayer"
     *
     * Example invalid: "001Datalayer", "My Data Layer", "My<Data>Layer"
     *
     */
    private const JS_VARIABLE_REGEX = '/^[a-zA-Z_$][0-9a-zA-Z_$]*$/';

    /**
     * @inheritdoc
     *
     * @return Value
     * @throws ValidatorException
     */
    public function beforeSave()
    {
        $label = $this->getData('field_config/label');

        if (!preg_match(self::JS_VARIABLE_REGEX, $this->getValue())) {
            throw new ValidatorException(__('%1 must be a valid JavaScript identifier', $label));
        }

        return parent::beforeSave();
    }
}

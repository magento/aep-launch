<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Model\Config\Backend;

/**
 * Datalayer name configuration
 */
class DatalayerName extends \Magento\Framework\App\Config\Value
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
    const JS_VARIABLE_REGEX = '/^[a-zA-Z_$][0-9a-zA-Z_$]*$/';

    /**
     * @inheritdoc
     *
     * @return \Magento\Framework\App\Config\Value|void
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function beforeSave()
    {
        $label = $this->getData('field_config/label');

        if (!preg_match(self::JS_VARIABLE_REGEX, $this->getValue())) {
            throw new \Magento\Framework\Exception\ValidatorException(__(
                $label .
                ' must be a valid JavaScript identifier'
            ));
        }

        parent::beforeSave();
    }
}

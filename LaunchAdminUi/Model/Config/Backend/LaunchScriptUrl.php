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
 * Launch Script Url configuration.
 */
class LaunchScriptUrl extends Value
{
    /**
     * Did they paste the script tag?
     *
     * Ex: <script src="//assets.adobedtm.com/launch-EN5ea69d1bda314cdeacd364XXXXXXX-development.min.js" async></script>
     */
    private const SCRIPT_TAG_REGEX = '/<script src="(.*)"/';

    /**
     * Matches URLs, even those that are protocol-relative (ie. "//" without http(s)).
     */
    private const MISSING_SCHEME_REGEX = '/^(http(s)?:)?\/\/(.*)/';

    /**
     * @inheritdoc
     *
     * @return Value
     * @throws ValidatorException
     */
    public function beforeSave()
    {
        $label = $this->getData('field_config/label');

        if (preg_match(self::SCRIPT_TAG_REGEX, $this->getValue(), $matches)) {
            $this->setValue($matches[1]);
        }

        // FILTER_VALIDATE_URL requires a protocol, so we'll have to prepend one if it's not there
        // Note: This also is the case for protocol-relative URLs, like "//foo.bar.com"
        $testValue = $this->getValue();
        if (preg_match(self::MISSING_SCHEME_REGEX, $testValue, $matches)) {
            // Protocol found, but it could be relative, so to pass the FILTER_VALIDATE_URL we need to prefix
            // with http. However, storing it as relative is fine for our purposes.
            $testValue = 'http://' . $matches[3];
        } else {
            // No protocol found, so prefix prior to testing
            $testValue = 'http://' . $testValue;

            // We need to store it with a protocol prefix as well
            $this->setValue('//' . $this->getValue());
        }

        if (!filter_var($testValue, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
            throw new ValidatorException(__(
                '%1 must either be a &lt;script&gt; tag for the Launch JavaScript snippet, ' .
                'or the URL to the snippet.',
                $label
            ));
        }

        return parent::beforeSave();
    }
}

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (Component, customerData) {
    'use strict';

    return Component.extend({
        /**
         * Reload Launch section of Customer Data when checkout is successful.
         */
        initialize: function () {
            this._super();

            customerData.reload(['launch'], true);
        }
    });
});

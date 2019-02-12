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
         * Process Launch events coming via Customer Data.
         */
        initialize: function () {
            this._super();

            customerData.get('launch').subscribe(function (updatedEvents) {
                if (!updatedEvents.hasOwnProperty('datalayerEvents')) {
                    return;
                }

                updatedEvents.datalayerEvents.forEach(function (event) {
                    if (Array.isArray(event)) {
                        event.forEach(function (item) {
                            window[this.datalayerName].push(item);
                        }, this);
                    } else {
                        window[this.datalayerName].push(event);
                    }
                }, this);
            }, this);
        }
    });
});

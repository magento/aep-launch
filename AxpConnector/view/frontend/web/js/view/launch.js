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
        initialize: function () {
            this._super();

            customerData.get('launch').subscribe(function (updatedEvents) {
                for (var event in updatedEvents.datalayerEvents) {
                    window[this.datalayerName].push(JSON.parse(updatedEvents.datalayerEvents[event]));
                }
            }, this);
        }
    });
});

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([], function () {
    'use strict';

    /**
     * Initialize and populate datalayer object with content.
     *
     * @param {Object} options
     */
    return function(options) {
        window[options.datalayerName] = window[options.datalayerName] || [];

        options.datalayer.forEach(function (item) {
            window[options.datalayerName].push(JSON.parse(item));
        });
    };
});

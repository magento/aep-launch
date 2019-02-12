/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @deprecated CustomerData must be used
 */
define([
        "jquery",
        "jquery/jquery.cookie"
    ],
    function ($) {
        "use strict";

        return function (opts) {
            var cookieContent = $.cookie('axpconnector_checkout_success');

            if (cookieContent) {
                var events = JSON.parse(cookieContent);

                events.forEach(function (event) {
                    window[opts.datalayerName].push(event);
                });
            }

            // Delete the cookie
            $.cookie('axpconnector_checkout_success', '', {path: '/', expires: -1});
        }
    }
);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
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
                var parsedContent = JSON.parse(cookieContent);
                var dln = parsedContent.datalayerName;

                window[dln] = window[dln] || [];

                var events = parsedContent.datalayerContent;
                events.forEach(function (event) {
                    window[dln].push(event);
                });
            }

            // Delete the cookie
            $.cookie('axpconnector_checkout_success', '', {path: '/', expires: -1});
        }
    }
);
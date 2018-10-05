define([
  "jquery",
  "jquery/jquery.cookie"
  ],
  function($){
    "use strict";

    return function(opts) {
      window.AppEventData = window.AppEventData || [];

      var checkoutJson = $.cookie('launchbyadobe_checkout_success');
      if(checkoutJson) {
        var events = JSON.parse(checkoutJson);
        events.forEach(function(event) {
          window.AppEventData.push(event);
        });
      }

      // Delete the cookie
      $.cookie('launchbyadobe_checkout_success', '', {path: '/', expires: -1});
    }
  }
);
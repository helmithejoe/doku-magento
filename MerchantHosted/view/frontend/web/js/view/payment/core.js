/**
 * Copyright © 2016 Doku. All rights reserved.
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list',
        'jquery'
    ],
    function (
        Component,
        rendererList,
        $
    ) {
        'use strict';

        rendererList.push(
            {
                type: 'core',
                component: 'Doku_MerchantHosted/js/view/payment/method-renderer/core-method'
            }
        );

        /** Add view logic here if needed */

        return Component.extend({
            initObservable: function() {

                $.getScript("https://staging.doku.com/doku-js/assets/js/jquery.payment.min.js", function() {});
                $.getScript("https://staging.doku.com/doku-js/assets/js/responsive-tabs.js", function() {});
                $.getScript("https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js", function() {});
                $.getScript("https://staging.doku.com/doku-js/assets/js/doku.uncompress.js?version="+ new Date().getTime(), function() {});

                $("head").append("<link>");
                var css = $("head").children(":last");
                css.attr({
                    rel:  "stylesheet",
                    type: "text/css",
                    href: "https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css"
                });

                return this;
            }
        });
    }
);
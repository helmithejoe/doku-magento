/**
 * Copyright © 2016 Doku. All rights reserved.
 */
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery'
    ],
    function (Component, $) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Doku_MerchantHosted/payment/oco'
            },

            /** Returns send check to info */
            getMailingAddress: function() {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },

            initObservable: function() {

                // var data = new Object();

                // data.req_merchant_code = '2074'; //mall id or merchant id
                // data.req_chain_merchant = 'NA'; //chain merchant id
                // data.req_payment_channel = '15'; //payment channel
                // data.req_basket = '';
                // data.req_server_url = 'http://crm.doku.com/doku-library-staging/example-payment/merchant-example.php'; //merchant payment url to receive pairing code & token
                // data.req_transaction_id = 'invoice_1477040126'; //invoice no
                // data.req_amount = '10000.00';
                // data.req_currency = '360'; //360 for IDR
                // data.req_words = 'f2be9d27c3ce8b01eba427f1b08c399a0d5051ac'; //your merchant unique key
                // data.req_session_id = '1477040127626'; //your server timestamp
                // data.req_form_type = 'inline';
                // data.req_custom_form = ['cc-field', 'cvv-field', 'name-field', 'exp-field'];

                return this;
            },

            initDoku: function(){
                // DokuToken(getToken);
                var data = new Object();

                data.req_merchant_code = '2074'; //mall id or merchant id
                data.req_chain_merchant = 'NA'; //chain merchant id
                data.req_payment_channel = '15'; //payment channel
                data.req_basket = '';
                data.req_server_url = 'http://crm.doku.com/doku-library-staging/example-payment/merchant-example.php'; //merchant payment url to receive pairing code & token
                data.req_transaction_id = 'invoice_1477040126'; //invoice no
                data.req_amount = '10000.00';
                data.req_currency = '360'; //360 for IDR
                data.req_words = 'f2be9d27c3ce8b01eba427f1b08c399a0d5051ac'; //your merchant unique key
                data.req_session_id = '1477040127626'; //your server timestamp
                data.req_form_type = 'inline';
                data.req_custom_form = ['cc-field', 'cvv-field', 'name-field', 'exp-field'];

                $.when(getForm(data)).done(function() {
                    console.log('when');
                    console.log($("#doku-cc-number").val());
                });

            },

            getToken: function(response){
                console.log('getToken');
            },

           
        });
    }
);

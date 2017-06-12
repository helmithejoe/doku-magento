<?php

namespace Doku\MerchantHosted\Controller\Payment;

use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Action\Context;
use Doku\MerchantHosted\Model\DokuConfigProvider;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ResourceConnection;
use Doku\MerchantHosted\Controller\Payment\Library;
use Magento\Checkout\CustomerData\Cart;

class Orderva extends Library{

    protected $session;
    protected $resourceConnection;
    protected $cart;

    public function __construct(
        LoggerInterface $logger, //log injection
        Context $context,
        DokuConfigProvider $config,
        Session $session,
        ResourceConnection $resourceConnection,
        Cart $cart
    ) {
        parent::__construct(
            $logger,
            $context,
            $config
        );

        $this->session = $session;
        $this->resourceConnection = $resourceConnection;
        $this->cart = $cart;
    }

    public function execute(){

        $this->logger->info('===== orderva Controller ===== Start');

        try{

            $postData = json_decode($_POST['dataResponse']);
            $invoice_no = 'mage2'. $this->config->getMallId() . str_pad($this->session->getQuoteId(), 9, '0', STR_PAD_LEFT);
            $amount = number_format($this->session->getQuote()->getGrandTotal(), 2, '.', '');
            $currency = '360';
            $params = array(
                'amount' => $amount,
                'invoice' => $invoice_no,
                'currency' => $currency,
            );

            $words = $this->doCreateWords($params);
            $billingAddress = $this->session->getQuote()->getBillingAddress()->convertToArray();
            $customer = array(
                'name' => $billingAddress['firstname'] .' '. $billingAddress['lastname'],
                'data_phone' => substr($billingAddress['telephone'], 0, 12),
                'data_email' => $postData->req_email,
                'data_address' => $billingAddress['street'] .', '. $billingAddress['city'] .', '. $billingAddress['country_id']
            );

            $getItems = $this->cart->getSectionData()['items'];
            $basket = '';

            foreach ($getItems as $getItem) {
                $basket .= $getItem['product_name'] .','. $getItem['product_price_value'] .','. $getItem['qty'] .','.
                    ($getItem['product_price_value'] * $getItem['qty']) .';';
            }

            $dataPayment = array(
                'req_mall_id' => $this->config->getMallId(),
                'req_chain_merchant' => "NA",
                'req_amount' => $amount,
                'req_words' => $words,
                'req_purchase_amount' => $amount,
                'req_trans_id_merchant' => $invoice_no,
                'req_request_date_time' => date('YmdHis'),
                'req_session_id' => $this->session->getSessionId(),
                'req_name' => $customer['name'],
                'req_email' => $customer['data_email'],
                'req_basket' => $basket,
                'req_expiry_time' => $this->config->getExpiry()
            );

            $this->logger->info('data payment : '. json_encode($dataPayment, JSON_PRETTY_PRINT));

            $result = $this->doGeneratePaycode($dataPayment);

            $this->logger->info('response payment = '. json_encode($result, JSON_PRETTY_PRINT));

            if($result->res_response_code == '0000'){

                $this->logger->info('===== orderva Controller ===== Saving data...');

                $this->resourceConnection->getConnection()->insert('doku_orders',
                    [
                        'quote_id' => $this->session->getQuoteId(),
                        'store_id' => $this->session->getQuote()->getStoreId(),
                        'invoice_no' => $invoice_no,
                        'payment_channel_id' => $postData->req_payment_channel,
                        'paycode_no' => $this->config->getPaycode($postData->req_payment_channel) . $result->res_pay_code,
                        'order_status' => 'PENDING'
                    ]);

                $this->logger->info('===== orderva Controller ===== Saving complete');
                $this->logger->info('===== orderva Controller ===== End');

                echo json_encode(array('err' => false, 'res_response_msg' => 'Generate Code Success',
                    'res_response_code' => $result->res_response_code));

            }else{

                $this->logger->info('===== orderva Controller ===== End');

                echo json_encode(array('err' => true, 'res_response_msg' => 'Generate Code Failed', 'res_response_code' => $result->res_response_code));

            }

        }catch(\Exception $e){

            $this->logger->info('===== orderva Controller ===== Generate code error : '. $e->getMessage());
            $this->logger->info('===== orderva Controller ===== End');

            echo json_encode(array('err' => true, 'res_response_msg' => $e->getMessage(), 'res_response_code' => '0099'));

        }

    }

}

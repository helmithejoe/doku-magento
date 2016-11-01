<?php

namespace Doku\MerchantHosted\Controller\Payment;

use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Action\Context;
use Doku\MerchantHosted\Model\DokuConfigProvider;
use Magento\Checkout\Model\Session;

class Order extends \Doku\MerchantHosted\Controller\Payment\Library{

    protected $session;

    public function __construct(
        LoggerInterface $logger, //log injection
        Context $context,
        DokuConfigProvider $config,
        Session $session
    ) {
        parent::__construct(
            $logger,
            $context,
            $config
        );

        $this->session = $session;
    }

    public function execute(){

        $this->logger->info('===== Order Controller ===== Start');

        $postData = json_decode($_POST['dataResponse']);
        $postBasket = $_POST['dataBasket'];
        $postEmail = $_POST['dataEmail'];

        $params = array(
            'amount' => $postData->res_amount,
            'invoice' => $postData->res_invoice_no,
            'currency' => $postData->res_currency,
            'token' => $postData->res_token_id,
            'pairing_code' => $postData->res_pairing_code
        );

        $this->logger->info('params words : '. json_encode($params, JSON_PRETTY_PRINT));

        $words = $this->doCreateWords($params);
        $billingAddress = $this->session->getQuote()->getBillingAddress()->convertToArray();

        $customer = array(
            'name' => $billingAddress['firstname'] .' '. $billingAddress['lastname'],
            'data_phone' => $billingAddress['telephone'],
            'data_email' => $postEmail,
            'data_address' => $billingAddress['street'] .', '. $billingAddress['city'] .', '. $billingAddress['country_id']
        );
        $data = array(
            'req_token_id' => $postData->res_token_id,
            'req_pairing_code' => $postData->res_pairing_code,
            'req_bin_filter' => array("411111", "548117", "433???6", "41*3"),
            'req_customer' => $customer,
            'req_basket' => $postBasket,
            'req_words' => $words
        );

        $responsePrePayment = $this->doPrePayment($data);

        $this->logger->info('response prepayment = '. json_encode($responsePrePayment, JSON_PRETTY_PRINT));

        if($responsePrePayment->res_response_code == '0000'){

            $dataPayment = array(
                'req_mall_id' => $this->config->getMallId(),
                'req_chain_merchant' => "NA",
                'req_amount' => $postData->res_amount,
                'req_words' => $words,
                'req_purchase_amount' => $postData->res_amount,
                'req_trans_id_merchant' => $postData->res_invoice_no,
                'req_request_date_time' => date('YmdHis'),
                'req_currency' => $postData->res_currency,
                'req_purchase_currency' => $postData->res_currency,
                'req_session_id' => $postData->res_session_id,
                'req_name' => $customer['name'],
                'req_payment_channel' => $postData->res_payment_channel,
                'req_basket' => $data['req_basket'],
                'req_email' => $customer['data_email'],
                'req_token_id' => $postData->res_token_id,
                'req_mobile_phone' => $customer['data_phone'],
                'req_address' => $customer['data_address']
            );


            $result = $this->doPayment($dataPayment);

            $this->logger->info('response payment = '. json_encode($result, JSON_PRETTY_PRINT));

            if($result->res_response_code == '0000'){

                echo json_encode(array('err' => false, 'res_response_msg' => 'Payment Success', 'res_response_code' => $result->res_response_code));

            }else{

                echo json_encode(array('err' => true, 'res_response_msg' => 'Payment Failed', 'res_response_code' => $result->res_response_code));

            }

        }else{
            echo json_encode(array('err' => true, 'res_response_msg' => 'Prepayment Failed', 'res_response_code' => $responsePrePayment->res_response_code));
        }

    }

}

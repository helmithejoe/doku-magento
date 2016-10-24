<?php

namespace Doku\MerchantHosted\Controller\Payment;

use Doku\MerchantHosted\Model\Oco;

class Order extends \Magento\Framework\App\Action\Action{

    protected $_logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger, //log injection
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_logger = $logger;
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute(){

        $postData = json_decode($_POST['dataResponse']);

        $this->_logger->info('postdata : '. json_encode($postData, JSON_PRETTY_PRINT));

        $words = sha1('10000.00' . '2074' . 'D0Ku123m3Rc' . 'invoice_1477040126' . '360');

        $this->_logger->info('$words : '. json_encode($words, JSON_PRETTY_PRINT));

        $basket = "adidas, 10000.00, 1, 10000.00;";

        $this->_logger->info('basket : '. json_encode($basket, JSON_PRETTY_PRINT));

        $customer = array(
            'name' => 'TEST NAME',
            'data_phone' => '08121111111',
            'data_email' => 'test@test.com',
            'data_address' => 'bojong gede #1 08/01'
        );

        $this->_logger->info('$customer : '. json_encode($customer, JSON_PRETTY_PRINT));

        $data = array(
            'req_token_id' => $postData->res_token_id,
            'req_pairing_code' => $postData->res_pairing_code,
            'req_bin_filter' => array("411111", "548117", "433???6", "41*3"),
            'req_customer' => $customer,
            'req_basket' => $basket,
            'req_words' => $words
        );

        $this->_logger->info('$data : '. json_encode($data, JSON_PRETTY_PRINT));
        $this->_logger->info('execute json');

        $ch = curl_init( 'https://staging.doku.com/api/payment/PrePayment' );
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        $this->_logger->info('before exec');
        $responseJson = curl_exec( $ch );
        $this->_logger->info('after exec');

        curl_close($ch);

        $this->_logger->info('response json = '. json_encode($responseJson, JSON_PRETTY_PRINT));

//        $responsePrePayment = json_decode($responseJson);
//
//        if($responsePrePayment->res_response_code == '0000'){
//
//            $dataPayment = array(
//                'req_mall_id' => 2074,
//                'req_chain_merchant' => "NA",
//                'req_amount' => '10000.00',
//                'req_words' => $words,
//                'req_purchase_amount' => '10000.00',
//                'req_trans_id_merchant' => 'invoice_1477040126',
//                'req_request_date_time' => date('YmdHis'),
//                'req_currency' => '360',
//                'req_purchase_currency' => '360',
//                'req_session_id' => sha1(date('YmdHis')),
//                'req_name' => $customer['name'],
//                'req_payment_channel' => 15,
//                'req_basket' => $basket,
//                'req_email' => $customer['data_email'],
//                'req_token_id' => $_POST['doku-token'],
//                'req_mobile_phone' => $customer['data_phone'],
//                'req_address' => $customer['data_address']
//            );
//
//        }else{
//            return 'prepayment failed';
//        }

    }

}

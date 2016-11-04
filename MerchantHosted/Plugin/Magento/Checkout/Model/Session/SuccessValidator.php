<?php

namespace Doku\MerchantHosted\Plugin\Magento\Checkout\Model\Session;

use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Session;

class SuccessValidator
{
    protected $session;
    protected $order;
    protected $logger;

    public function __construct(
        Session $session,
        LoggerInterface $logger,
        Order $order
    ) {
        $this->session = $session;
        $this->logger = $logger;
        $this->order = $order;
    }

    public function afterIsValid(\Magento\Checkout\Model\Session\SuccessValidator $successValidator, $returnValue)
    {
        $this->logger->info('masuk');
        $this->logger->info('session : '. json_encode($this->session->getLastRealOrder()->convertToArray(), JSON_PRETTY_PRINT));
        $this->logger->info('order : '. json_encode($this->order->convertToArray(), JSON_PRETTY_PRINT));

        return $returnValue;
    }
}
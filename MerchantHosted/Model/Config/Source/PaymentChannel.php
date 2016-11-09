<?php
namespace Doku\MerchantHosted\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Helper\Category;

class PaymentChannel implements ArrayInterface{

    protected $categoryHelper;

    public function __construct(
        Category $catalogCategory
    )
    {
        $this->_categoryHelper = $catalogCategory;
    }

    public function toOptionArray()
    {

        $arr = $this->toArray();
        $ret = [];

        foreach ($arr as $key => $value)
        {

            $ret[] = [
                'value' => $key .'-'. $value,
                'label' => $value
            ];
        }

        return $ret;
    }

    public function toArray()
    {

        $catagoryList = array();

        $catagoryList['14'] = __('Alfa');
        $catagoryList['15'] = __('Credit Card');
        $catagoryList['04'] = __('Doku Wallet');
        $catagoryList['02'] = __('Mandiri Clickpay');
        $catagoryList['08'] = __('Mandiri SOA Lite');
        $catagoryList['09'] = __('Mandiri SOA Full');
        $catagoryList['05'] = __('Permata VA Lite');
        $catagoryList['07'] = __('Permata VA Full');
        $catagoryList['21'] = __('Sinarmas VA Lite');
        $catagoryList['22'] = __('Sinarmas VA Full');

        return $catagoryList;
    }

}
<?php
namespace Admin\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class ProductsPrice extends InputFilter {
	
	public function __construct($options = null, $customer_type, $product_id){
        // List price with customer type
        foreach ($customer_type AS $key => $value) {
            $this->add(array(
                'name'		=> $key.'_'.$product_id.'_price',
                'required'	=> true,
                'validators'	=> array(
                    array(
                        'name'		=> 'NotEmpty',
                        'options'	=> array(
                            'messages'	=> array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Giá trị này không được để trống'
                            )
                        ),
                        'break_chain_on_failure'	=> true
                    ),
                )
            ));
        }
	}
}
<?php
namespace Admin\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class ProductsInventory extends InputFilter {
	
	public function __construct($options = null, $warehouse, $product_id){

        // List price with customer type
        foreach ($warehouse AS $key => $value) {
            $this->add(array(
                'name'		=> $key.'_'.$product_id.'_quantity',
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
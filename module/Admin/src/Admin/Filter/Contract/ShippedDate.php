<?php
namespace Admin\Filter\Contract;

use Zend\InputFilter\InputFilter;

class ShippedDate extends InputFilter {
	
	public function __construct($options = null){
	    // Ngày xuất kho
	    $this->add(array(
	        'name'		=> 'shipped_date',
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
	            )
	        )
	    ));
	}
}
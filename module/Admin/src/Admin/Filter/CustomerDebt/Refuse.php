<?php
namespace Admin\Filter\Pay;

use Zend\InputFilter\InputFilter;

class Refuse extends InputFilter {
    
    protected $_optionId;
    protected $_optionData;
    protected $_optionRoute;
    protected $_optionContract;
	
	public function __construct($options = null){
	    
		$this->add(array(
		    'name'		=> 'note',
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
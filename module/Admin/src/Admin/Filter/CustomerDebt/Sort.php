<?php
namespace Admin\Filter\CustomerDebt;

use Zend\InputFilter\InputFilter;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class Sort extends InputFilter {
    
    protected $_optionId;
    protected $_optionData;
    protected $_optionRoute;
    protected $_optionContract;
	
	public function __construct($options = null)
    {
        $this->add(array(
            'name'		=> 'created',
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
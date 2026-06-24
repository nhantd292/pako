<?php
namespace Admin\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class WarehouseVatDetail extends InputFilter {
	
	public function __construct($options = null){
        $this->add(array(
            'name'		=> 'sale_branch_id',
            'required'	=> $options['route']['action'] == 'edit' ? false : true,
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

        $this->add(array(
            'name'		=> 'quantity',
            'required'	=> $options['route']['action'] == 'edit' ? true : false,
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
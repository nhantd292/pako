<?php
namespace Admin\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class ProductsType extends InputFilter {
	
	public function __construct($options = null){
        $exclude = null;
        if(!empty($options['id'])) {
            $exclude = array(
                'field' => 'id',
                'value' => $options['id']
            );
        }

        $this->add(array(
            'name'		=> 'name',
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
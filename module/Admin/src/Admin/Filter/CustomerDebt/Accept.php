<?php
namespace Admin\Filter\CustomerDebt;

use Zend\InputFilter\InputFilter;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class Accept extends InputFilter {
    
    protected $_optionId;
    protected $_optionData;
    protected $_optionRoute;
    protected $_optionContract;
	
	public function __construct($options = null){
	    
		// Tài khoản chính
		$this->add(array(
			'name'		=> 'accountant_funds_id_cash',
			'required'	=> $options['paid_cash'] > 0 or $options['accrued_cash'] > 0 ? true : false,
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
			'name'		=> 'accountant_funds_id_transfer',
			'required'	=> $options['paid_transfer'] > 0 or $options['accrued_transfer'] > 0 ? true : false,
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
	    
		// Danh mục
		$this->add(array(
			'name'		=> 'category_id',
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
	    
		// Nội dung chọn
		$this->add(array(
		    'name'		=> 'content_select',
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
		
		// Nội dung nhập
		$this->add(array(
			'name'		=> 'content',
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
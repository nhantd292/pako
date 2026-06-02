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
	    // Ngày chứng từ
	    $this->add(array(
	        'name'		=> 'date',
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
	    
		// Loại nghiệp vụ
		$this->add(array(
			'name'		=> 'transaction_category_id',
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
	    
		// Nghiệp vụ
		$this->add(array(
			'name'		=> 'transaction_type_id',
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
	    
		// Hình thức giao dịch
//		$this->add(array(
//			'name'		=> 'transaction_form_id',
//			'required'	=> true,
//			'validators'	=> array(
//				array(
//					'name'		=> 'NotEmpty',
//				    'options'	=> array(
//				        'messages'	=> array(
//				            \Zend\Validator\NotEmpty::IS_EMPTY => 'Giá trị này không được để trống'
//				        )
//				    ),
//					'break_chain_on_failure'	=> true
//				)
//			)
//		));
	    
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
	    
		// Số tiền thu
		if($options['transaction_type_id'] == 'thu') {
    		$this->add(array(
    			'name'		=> 'paid_cash',
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
    		$this->add(array(
    			'name'		=> 'paid_transfer',
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
	    
		// Số tiền chi
		if($options['transaction_type_id'] == 'chi') {
    		$this->add(array(
    			'name'		=> 'accrued_cash',
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
    		$this->add(array(
    			'name'		=> 'accrued_transfer',
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
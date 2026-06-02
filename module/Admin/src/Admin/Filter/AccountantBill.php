<?php
namespace Admin\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class AccountantBill extends InputFilter {
	
	public function __construct($options = null){
	    $exclude = null;
	    if(!empty($options['id'])) {
	        $exclude = array(
	            'field' => 'id',
	            'value' => $options['id']
	        );
	    }
	    
	    // Loại số liệu
//	    $this->add(array(
//	        'name'		=> 'data_type_id',
//	        'required'	=> true,
//	        'validators'	=> array(
//	            array(
//	                'name'		=> 'NotEmpty',
//	                'options'	=> array(
//	                    'messages'	=> array(
//	                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Giá trị này không được để trống'
//	                    )
//	                ),
//	                'break_chain_on_failure'	=> true
//	            )
//	        )
//	    ));
	    
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
	    
	    // Code
	    $this->add(array(
	        'name'		=> 'code',
	        'required'	=> false,
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
	            array(
					'name'		=> 'DbNoRecordExists',
					'options'	=> array(
						'table'   => TABLE_ACCOUNTANT_BILL,
						'field'   => 'code',
						'adapter' => GlobalAdapterFeature::getStaticAdapter(),
					    'exclude' => $exclude,
					    'messages'	=> array(
					        \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'Đã tồn tại'
					    )
					),
					'break_chain_on_failure'	=> true
				)
	        )
	    ));
	    
		// Tài khoản chính
		$this->add(array(
			'name'		=> 'accountant_funds_id',
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
		$this->add(array(
			'name'		=> 'transaction_form_id',
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
	    
		// Tên người nộp/nhận tiền
		$this->add(array(
			'name'		=> 'submitter_name',
			'required'	=> false,
		));

        // Số điện thoại người nộp/nhận tiền
        $this->add(array(
            'name'		=> 'submitter_phone',
            'required'	=> false,
        ));
	    
		if($options['transaction_type_id'] == 'thu') {
		    // Số tiền thu
    		$this->add(array(
    			'name'		=> 'paid',
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
    			'name'		=> 'accrued',
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
}

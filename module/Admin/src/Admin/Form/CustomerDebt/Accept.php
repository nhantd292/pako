<?php
namespace Admin\Form\CustomerDebt;
use \Zend\Form\Form as Form;

class Accept extends Form {
	
	public function __construct($sm){
		parent::__construct();
		
		$userInfo = new \ZendX\System\UserInfo();
		
		// FORM Attribute
		$this->setAttributes(array(
			'action'	    => '',
			'method'	    => 'POST',
			'class'		    => 'horizontal-form',
			'role'		    => 'form',
			'name'		    => 'adminForm',
			'id'		    => 'adminForm',
		));
		
		// Modal
		$this->add(array(
		    'name'			=> 'modal',
		    'type'			=> 'Hidden',
		    'attributes'	=> array(
		        'value'     => 'success',
		    )
		));
		
		// Id
		$this->add(array(
		    'name'			=> 'id',
		    'type'			=> 'Hidden',
		));
		
		// Ngày chứng từ
		$this->add(array(
		    'name'			=> 'date',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		=> 'form-control date-picker',
		    ),
		));
		
		// Số chứng từ
		$this->add(array(
		    'name'			=> 'code',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		=> 'form-control',
		    ),
		));
		
		// Tài khoản chính
		$this->add(array(
		    'name'			=> 'accountant_funds_id_cash',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		    ),
		    'options'		=> array(
		        'empty_option'	=> '- Chọn -',
		        'disable_inarray_validator' => true,
		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\FundsTable')->listItem(null, array('task' => 'permision')), array('key' => 'id', 'value' => 'name')),
		    )
		));
		$this->add(array(
		    'name'			=> 'accountant_funds_id_transfer',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		    ),
		    'options'		=> array(
		        'empty_option'	=> '- Chọn -',
		        'disable_inarray_validator' => true,
		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\FundsTable')->listItem(null, array('task' => 'permision')), array('key' => 'id', 'value' => 'name')),
		    )
		));
		
		// Loại nghiệp vụ
		$this->add(array(
		    'name'			=> 'transaction_category_id',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		    ),
		    'options'		=> array(
		        'empty_option'	=> '- Chọn -',
		        'disable_inarray_validator' => true,
		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "accountant-transaction-category" ) ), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name')),
		    )
		));
		
		// Nghiệp vụ
		$this->add(array(
		    'name'			=> 'transaction_type_id',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		        'value'     => 'thu'
		    ),
		    'options'		=> array(
		        'disable_inarray_validator' => true,
		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "accountant-transaction-type" ) ), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name')),
		    )
		));
		
		// Hình thức giao dịch
//		$this->add(array(
//		    'name'			=> 'transaction_form_id',
//		    'type'			=> 'Select',
//		    'attributes'	=> array(
//		        'class'		=> 'form-control select2 select2_basic',
//		    ),
//		    'options'		=> array(
//		        'empty_option'	=> '- Chọn -',
//		        'disable_inarray_validator' => true,
//		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "accountant-transaction-form" ) ), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name')),
//		    )
//		));
		
		// Danh mục
		$this->add(array(
		    'name'			=> 'category_id',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		    ),
		    'options'		=> array(
		        'empty_option'	=> '- Chọn -',
		        'disable_inarray_validator' => true,
		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "accountant-category" ) ), array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
		    )
		));
		
		// Nội dung chọn
		$content_select = \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "accountant-content" ) ), array('task' => 'cache')), array('key' => 'name', 'value' => 'name'));
		$content_select['other'] = 'Khác';
		$this->add(array(
		    'name'			=> 'content_select',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		    ),
		    'options'		=> array(
		        'empty_option'	=> '- Chọn -',
		        'disable_inarray_validator' => true,
		        'value_options'	=> $content_select,
		    )
		));
		
		// Nội dung
		$this->add(array(
		    'name'			=> 'content',
		    'type'			=> 'Textarea',
		    'attributes'	=> array(
		        'class'			=> 'form-control',
		        'rows'			=> '3',
		    ),
		));
		
		// Người nộp/nhận: tên
		$this->add(array(
			'name'			=> 'submitter_name',
			'type'			=> 'Text',
			'attributes'	=> array(
				'class'			=> 'form-control',
			),
		));
		
		// Người nộp/nhận: điện thoại
		$this->add(array(
			'name'			=> 'submitter_phone',
			'type'			=> 'Text',
			'attributes'	=> array(
				'class'			=> 'form-control mask_phone',
			),
		));
		
		// Thu
		$this->add(array(
		    'name'			=> 'paid_cash',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'			=> 'form-control mask_currency',
		        'placeholder'	=> 'Thu tiền mặt'
		    )
		));

		// Thu
		$this->add(array(
		    'name'			=> 'paid_transfer',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'			=> 'form-control mask_currency',
		        'placeholder'	=> 'Thu chuyển khoản'
		    )
		));
		
		// Chi
		$this->add(array(
		    'name'			=> 'accrued_cash',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'			=> 'form-control mask_currency',
		        'placeholder'	=> 'Số tiền chi tiền mặt'
		    )
		));

		// Chi
		$this->add(array(
		    'name'			=> 'accrued_transfer',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'			=> 'form-control mask_currency',
		        'placeholder'	=> 'Số tiền chi chuyển khoản'
		    )
		));
		
		// Ghi chú
		$this->add(array(
		    'name'			=> 'note',
		    'type'			=> 'TextArea',
		    'attributes'	=> array(
		        'class'		=> 'form-control',
		        'rows'		=> 3,
		    ),
		));
		
		// Người lập phiếu thu/chi
		$this->add(array(
		    'name'			=> 'created_item_id',
		    'type'			=> 'Hidden',
		));
	}
}
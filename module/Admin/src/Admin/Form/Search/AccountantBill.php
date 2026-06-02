<?php
namespace Admin\Form\Search;
use \Zend\Form\Form as Form;

class AccountantBill extends Form{
    
	public function __construct($sm, $params = null){
		parent::__construct();
		
		// FORM Attribute
		$this->setAttributes(array(
			'action'	=> '',
			'method'	=> 'POST',
			'class'		=> 'horizontal-form',
			'role'		=> 'form',
			'name'		=> 'adminForm',
			'id'		=> 'adminForm',
		));
		
		// Keyword
		$this->add(array(
		    'name'			=> 'filter_keyword',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'placeholder'   => 'Từ khóa',
		        'class'			=> 'form-control input-sm',
		        'id'			=> 'filter_keyword',
		    ),
		));
		
		// Bắt đầu từ ngày
		$this->add(array(
		    'name'			=> 'filter_date_begin',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'			=> 'form-control date-picker',
		        'placeholder'	=> 'Từ ngày'
		    )
		));
		
		// Ngày kết thúc
		$this->add(array(
		    'name'			=> 'filter_date_end',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'			=> 'form-control date-picker',
		        'placeholder'	=> 'Đến ngày'
		    )
		));
		
		// Phân loại ngày
		$this->add(array(
		    'name'			=> 'filter_date_type',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		    ),
		    'options'		=> array(
		        'value_options'	=> array('date' => 'Ngày Thu/Chi', 'created' => 'Ngày tạo'),
		    )
		));
		
		// Sổ quỹ
		$this->add(array(
		    'name'			=> 'filter_accountant_funds',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		    ),
		    'options'		=> array(
		        'empty_option'	=> '- Sổ tài khoản -',
		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\FundsTable')->listItem(null, array('task' => 'permision')), array('key' => 'id', 'value' => 'name')),
		    )
		));
		
		// Loại nghiệp vụ
		$this->add(array(
		    'name'			=> 'filter_transaction_category',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		    ),
		    'options'		=> array(
		        'empty_option'	=> '- Loại nghiệp vụ -',
		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "accountant-transaction-category" )), array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
		    )
		));
		
		// Nghiệp vụ
		$this->add(array(
		    'name'			=> 'filter_transaction_type',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		    ),
		    'options'		=> array(
		        'empty_option'	=> '- Nghiệp vụ -',
		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "accountant-transaction-type" )), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name')),
		    )
		));
		
		// Hình thức giao dịch
		$this->add(array(
		    'name'			=> 'filter_transaction_form',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		    ),
		    'options'		=> array(
		        'empty_option'	=> '- Hình thức giao dịch -',
		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "accountant-transaction-form" )), array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
		    )
		));
		
		// Danh mục
		$this->add(array(
		    'name'			=> 'filter_category',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		    ),
		    'options'		=> array(
		        'empty_option'	=> '- Danh mục -',
		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "accountant-category" )), array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
		    )
		));
		
		// Khóa học
//		$this->add(array(
//		    'name'			=> 'filter_product',
//		    'type'			=> 'Select',
//		    'attributes'	=> array(
//		        'class'		=> 'form-control select2 select2_basic',
//		    ),
//		    'options'		=> array(
//		        'empty_option'	=> '- Sản phẩm -',
//		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\ProductTable')->listItem(array( "type" => "tra-phi" ), array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
//		    )
//		));
		 
		// Lớp học
		// $this->add(array(
		//     'name'			=> 'filter_training_class',
		//     'type'			=> 'Select',
		//     'attributes'	=> array(
		//         'class'		=> 'form-control select2 select2_basic',
		//     ),
		//     'options'		=> array(
		//         'empty_option'	=> '- Lớp học -',
		//         'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\TrainingClassTable')->listItem(array('product_id' => $params['ssFilter']['filter_product']), array('task' => 'cache-product')), array('key' => 'id', 'value' => 'name')),
		//     )
		// ));
		
		// Doanh nghiệp - Khóa học
		// $this->add(array(
		//     'name'			=> 'filter_hbr_course',
		//     'type'			=> 'Select',
		//     'attributes'	=> array(
		//         'class'		=> 'form-control select2 select2_basic',
		//     ),
		//     'options'		=> array(
		//         'empty_option'	=> '- Doanh nghiệp - Khóa học -',
		//         'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Hbr\Model\CourseTable')->listItem(null, array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
		//     )
		// ));
		
		// Cơ sở doanh số
		$this->add(array(
			'name'			=> 'filter_sale_branch_id',
			'type'			=> 'Select',
			'attributes'	=> array(
				'class'		=> 'form-control select2 select2_basic',
			),
			'options'		=> array(
				'empty_option'	=> '- Cơ sở -',
				'disable_inarray_validator' => true,
				'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "sale-branch" ) ), array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
			),
		));
		
		// Status
		$this->add(array(
		    'name'			=> 'filter_status',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		    ),
		    'options'		=> array(
		        'empty_option'	=> '- Trạng thái -',
		        'value_options'	=> array( 1	=> 'Đã xác nhận', 0 => 'Chưa xác nhận'),
		    )
		));
		
		// Submit
		$this->add(array(
		    'name'			=> 'filter_submit',
		    'type'			=> 'Submit',
		    'attributes'	=> array(
		        'value'     => 'Tìm',
		        'class'		=> 'btn btn-sm green',
		    ),
		));
		
		// Order
		$this->add(array(
		    'name'			=> 'order',
		    'type'			=> 'Hidden',
		));
		
		// Order By
		$this->add(array(
		    'name'			=> 'order_by',
		    'type'			=> 'Hidden',
		));
	}
}
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

        // Ngày thu/chi
        $this->add(array(
            'name'			=> 'date',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		=> 'form-control date-picker',
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
		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\FundsTable')->listItem(array('transaction_form_id' => 'tien-mat'), array('task' => 'permision')), array('key' => 'id', 'value' => 'name')),
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
		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\FundsTable')->listItem(array('transaction_form_id' => 'chuyen-khoan'), array('task' => 'permision')), array('key' => 'id', 'value' => 'name')),
		    )
		));
		
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
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'			=> 'form-control',
            ),
        ));
		
		// Ghi chú
		$this->add(array(
		    'name'			=> 'note',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		=> 'form-control',
		    ),
		));
	}
}
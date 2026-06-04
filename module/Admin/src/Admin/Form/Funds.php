<?php
namespace Admin\Form;
use \Zend\Form\Form as Form;

class Funds extends Form {
	
	public function __construct($sm){
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
		
		// Id
		$this->add(array(
		    'name'			=> 'id',
		    'type'			=> 'Hidden',
		));
		
		// Name
		$this->add(array(
			'name'			=> 'name',
			'type'			=> 'Text',
			'attributes'	=> array(
				'class'			=> 'form-control',
				'id'			=> 'name',
				'placeholder'	=> 'Tên',
			),
		));
		
		// Tồn quỹ
		$this->add(array(
			'name'			=> 'price',
			'type'			=> 'Text',
			'attributes'	=> array(
				'class'			=> 'form-control mask_currency',
				'id'			=> 'price',
			),
		));
		
		// Cơ sở
		$this->add(array(
		    'name'			=> 'company_branch_id',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		    ),
		    'options'		=> array(
		        'empty_option'	=> '- Chọn -',
		        'disable_inarray_validator' => true,
		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
		    ),
		));

        // Hình thức giao dịch
        $this->add(array(
            'name'			=> 'transaction_form_id',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
            ),
            'options'		=> array(
                'empty_option'	=> '- Chọn -',
                'disable_inarray_validator' => true,
                'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "accountant-transaction-form" ) ), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name')),
            )
        ));

        $this->add(array(
            'name'			=> 'user_ids',
            'type'			=> 'MultiCheckbox',
            'options'		=> array(
                'label_attributes' => array(
                    'class'		=> 'checkbox-inline',
                ),
                'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'list-accounting')), array('key' => 'id', 'value' => 'name')),
            ),
        ));
		
		// Ordering
		$this->add(array(
		    'name'			=> 'ordering',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'value'         => 255,
		        'class'			=> 'form-control',
		        'id'			=> 'ordering',
		        'placeholder'	=> 'Thứ tự'
		    )
		));
		
		// Status
		$this->add(array(
			'name'			=> 'status',
			'type'			=> 'Select',
			'attributes'	=> array(
				'class'		=> 'form-control select2 select2_basic',
			    'value'     => 1,
			),
		    'options'		=> array(
		        'value_options'	=> array( 1	=> 'Hiển thị', 0 => 'Không hiển thị'),
		    )
		));
	}
}
<?php
namespace Admin\Form;
use \Zend\Form\Form as Form;

class Contract extends Form {
	
	public function __construct($sm, $options){
		parent::__construct();
		
		// FORM Attribute
		$this->setAttributes(array(
			'action'	    => '',
			'method'	    => 'POST',
			'class'		    => 'horizontal-form',
			'role'		    => 'form',
			'name'		    => 'adminForm',
			'id'		    => 'adminForm',
		));
		
		// Id
		$this->add(array(
		    'name'			=> 'id',
		    'type'			=> 'Hidden',
		));
		
		// Phone
		$this->add(array(
		    'name'			=> 'phone',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		=> 'form-control mask_phone',
		    ),
		));
		
		// Name
		$this->add(array(
			'name'			=> 'name',
			'type'			=> 'Text',
			'attributes'	=> array(
				'class'		=> 'form-control',
			),
		));

        // Nhóm khách hàng
//        $this->add(array(
//            'name'			=> 'customer_type_id',
//            'type'			=> 'Select',
//            'attributes'	=> array(
//                'class'		=> 'form-control select2 select2_basic',
//            ),
//            'options'		=> array(
//                'empty_option'	=> '- Chọn -',
//                'disable_inarray_validator' => true,
//                'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\CustomerTypeTable')->listItem(null, array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
//            ),
//        ));

		// Tỉnh thành
		$this->add(array(
		    'name'			=> 'location_city_id',
		    'type'			=> 'Select',
		    'attributes'	=> array(
		        'class'		=> 'form-control select2 select2_basic',
		    ),
		    'options'		=> array(
		        'empty_option'	=> '- Chọn -',
		        'disable_inarray_validator' => true,
		        'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\LocationsTable')->listItem(array('level' => 1), array('task' => 'cache')), array('key' => 'code', 'value' => 'name')),
		    ),
		));


		
		// Quận huyện
		$this->add(array(
		    'name'			=> 'location_district_id',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		          => 'form-control select2 select2_advance',
		        'value'               => '',
		        'data-table'          => TABLE_LOCATIONS,
		        'data-id'             => 'code',
		        'data-text'           => 'fullname',
		        'data-parent'         => '',
		        'data-parent-field'   => 'parent',
				'data-parent-name'    => 'location_city_id',
		    ),
		));
		
		// Địa chỉ
		$this->add(array(
		    'name'			=> 'address',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		=> 'form-control',
		    )
		));

        $this->add(array(
            'name'			=> 'sale_branch_id',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
                'data-value'  => "",
            ),
            'options'		=> array(
                'empty_option'	=> '- Chọn -',
                'disable_inarray_validator' => true,
                'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
            )
        ));

        $this->add(array(
            'name'			=> 'invoice_type',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
            ),
            'options'		=> array(
                'empty_option'	=> '- Chọn -',
                'disable_inarray_validator' => true,
                'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'invoice-type')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name')),
            )
        ));

        $this->add(array(
            'name'			=> 'option_vat',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
            ),
            'options'		=> array(
                'empty_option'	=> '- Chọn -',
                'disable_inarray_validator' => true,
                'value_options'	=>  array('yes' => 'Lấy VAT', 'no' => 'Không lấy VAT'),
            )
        ));

		$this->add(array(
		    'name'			=> 'company_name',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		=> 'form-control',
		    )
		));

		$this->add(array(
		    'name'			=> 'company_mst',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		=> 'form-control',
		    )
		));

		$this->add(array(
		    'name'			=> 'company_address',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		=> 'form-control',
		    )
		));

		$this->add(array(
		    'name'			=> 'company_email',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		=> 'form-control',
		    )
		));

		$this->add(array(
		    'name'			=> 'company_user',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		=> 'form-control',
		    )
		));

		$this->add(array(
		    'name'			=> 'company_phone',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		=> 'form-control',
		    )
		));
		
		// Thành tiền
		$this->add(array(
		    'name'			=> 'price_total',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		  => 'form-control text-green text-bold mask_currency',
		        'value'       => 0,
		        'data-value'  => 0,
		        'readonly'    => 'readonly',
		    )
		));

		// Nợ cũ
		$this->add(array(
		    'name'			=> 'amount_owed',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		  => 'form-control text-red text-bold mask_currency',
		        'value'       => 0,
                'data-value'  => null,
		        'readonly'    => 'readonly',
		    )
		));
		// nợ lại
        $this->add(array(
            'name'			=> 'new_debt',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		  => 'form-control text-orange text-bold mask_currency',
                'value'       => 0,
                'data-value'  => 0,
                'readonly'    => 'readonly',
            )
        ));

		$this->add(array(
		    'name'			=> 'paid_cash',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		  => 'form-control text-purple text-bold mask_currency',
		        'value'       => 0,
		    )
		));

		$this->add(array(
		    'name'			=> 'paid_transfer',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		  => 'form-control text-purple text-bold mask_currency',
		        'value'       => 0,
		        'data-value'  => 0,
		    )
		));

		$this->add(array(
		    'name'			=> 'discount',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		  => 'form-control text-purple text-bold mask_currency',
		        'value'       => 0,
		        'data-value'  => 0,
		    )
		));

		$this->add(array(
		    'name'			=> 'vat',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		  => 'form-control text-green text-bold mask_currency',
		        'value'       => 0,
		        'data-value'  => 0,
                'readonly'    => 'readonly',
		    )
		));

		$this->add(array(
		    'name'			=> 'fee_other',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		  => 'form-control text-green text-bold mask_currency',
		        'value'       => 0,
		        'data-value'  => 0,
		    )
		));

		$this->add(array(
		    'name'			=> 'fee_shipp',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'class'		  => 'form-control text-green text-bold mask_currency',
		        'value'       => 0,
		        'data-value'  => 0,
		    )
		));
		
		// Ghi chú sales
		$this->add(array(
		    'name'			=> 'sale_note',
		    'type'			=> 'Text',
		    'attributes'	=> array(
				'class'		  => 'form-control',
		    )
		));

		// Ghi chú GHTK
		$this->add(array(
		    'name'			=> 'ghtk_note',
		    'type'			=> 'Text',
		    'attributes'	=> array(
				'class'		  => 'form-control',
//                'value' => 'Cho Kiểm Tra Và Đồng Kiểm, Có Vấn Đề Gì Gọi Cho Shop, Không Tự Ý Hủy Đơn 0769638925',
                'value' => ''
		    )
		));

        // Kho xuất hàng
        $this->add(array(
            'name'			=> 'inventory_id',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
            ),
            'options'		=> array(
                'empty_option'	=> '- Chọn -',
                'disable_inarray_validator' => true,
                'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\WarehouseTable')->listItem(null, array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
            ),
        ));


//        $this->add(array(
//            'name'			=> 'fee_type',
//            'type'			=> 'Select',
//            'attributes'	=> array(
//                'class'		=> 'form-control select2 select2_basic',
//            ),
//            'options'		=> array(
//                'empty_option'	=> '- chọn -',
//                'disable_inarray_validator' => true,
//                'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "fee-type" )), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name')),
//            )
//        ));
	}
}
<?php
namespace Admin\Form\Search;
use \Zend\Form\Form as Form;

class KovOrders extends Form{
    
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
		
		// Keyword
		$this->add(array(
		    'name'			=> 'filter_keyword',
		    'type'			=> 'Text',
		    'attributes'	=> array(
		        'placeholder'   => 'SĐT/Tên KH/Mã ĐH',
		        'class'			=> 'form-control input-sm',
		        'id'			=> 'filter_keyword',
		    ),
		));

        // Kho hàng
        $this->add(array(
            'name'			=> 'filter_branches',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
            ),
            'options'		=> array(
                'empty_option'	=> '- Chi nhánh -',
                'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
            )
        ));

        // Bắt đầu từ ngày
        $this->add(array(
            'name'			=> 'filter_date_begin',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'			=> 'form-control date-picker',
                'placeholder'	=> 'Từ ngày',
                'autocomplete'  => 'off'
            )
        ));

        // Ngày kết thúc
        $this->add(array(
            'name'			=> 'filter_date_end',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'			=> 'form-control date-picker',
                'placeholder'	=> 'Đến ngày',
                'autocomplete'  => 'off'
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
		
		// Xóa
		$this->add(array(
		    'name'			=> 'filter_reset',
		    'type'			=> 'Submit',
		    'attributes'	=> array(
		        'value'     => 'Xóa',
		        'class'		=> 'btn btn-sm red',
		    ),
		));
		
		// Action
		$this->add(array(
			'name'			=> 'filter_action',
			'type'			=> 'Hidden',
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



        // action sale
        $this->add(array(
            'name'			=> 'action_new',
            'type'			=> 'Hidden',
            'attributes'	=> array(
                'value'   => 'sale',
            ),
        ));

        // action index
        $this->add(array(
            'name'			=> 'action_index',
            'type'			=> 'Hidden',
            'attributes'	=> array(
                'value'   => 'index',
            ),
        ));
	}
}
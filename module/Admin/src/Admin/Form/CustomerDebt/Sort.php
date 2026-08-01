<?php
namespace Admin\Form\CustomerDebt;
use \Zend\Form\Form as Form;

class Sort extends Form {
	
	public function __construct($sm){
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

        // FIELD DATETIME: Thời gian lập phiếu (created)
        $this->add(array(
            'name'       => 'created',
            'type'       => 'Text',
            'attributes' => array(
                'class'    => 'form-control datetime-picker-vn', // Gọi đúng class vừa tạo trong JS
                'id'       => 'created',
                'required' => 'required',
                'autocomplete' => 'off',
            ),
            'options' => array(
                'label' => 'Ngày thu chi:',
            ),
        ));

	}
}
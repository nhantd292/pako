<?php
namespace Admin\Form\Contract;
use \Zend\Form\Form as Form;

class ShippedDate extends Form {
	
	public function __construct($sm, $params){
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
		
		// Modal
		$this->add(array(
		    'name'			=> 'modal',
		    'type'			=> 'Hidden',
		    'attributes'	=> array(
		        'value'		=> 'success',
		    ),
		));
		
		// Ngày xuất kho
        $this->add(array(
            'name'			=> 'shipped_date',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		=> 'form-control date-picker',
            )
        ));
	}
}
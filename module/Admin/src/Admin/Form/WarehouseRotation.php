<?php

namespace Admin\Form;

use \Zend\Form\Form as Form;

class WarehouseRotation extends Form
{

    public function __construct($sm, $options)
    {
        parent::__construct();

        // FORM Attribute
        $this->setAttributes(array(
            'action' => '',
            'method' => 'POST',
            'class'  => 'horizontal-form',
            'role'   => 'form',
            'name'   => 'adminForm',
            'id'     => 'adminForm',
        ));

        // Id
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        // Kho xuất nhập
        $this->add(array(
            'name'			=> 'inventory_output_id',
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

        $this->add(array(
            'name'			=> 'inventory_input_id',
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

        $this->add(array(
            'name'			=> 'note',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		     => 'form-control',
                'placeholder'	=> '',
            ),
        ));
    }
}
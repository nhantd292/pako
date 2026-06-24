<?php

namespace Admin\Form;

use \Zend\Form\Form as Form;

class WarehouseVatDetail extends Form
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
            'name'			=> 'quantity',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'			=> 'form-control mask_currency',
                'id'			=> 'quantity',

            ),
        ));

        $this->add(array(
            'name'			=> 'note',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'			=> 'form-control',
                'id'			=> 'note',

            ),
        ));
    }
}
<?php

namespace Admin\Form;

use \Zend\Form\Form as Form;

class Products extends Form
{

    public function __construct($sm)
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

        // name
        $this->add(array(
            'name'			=> 'name',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'			=> 'form-control',
                'id'			=> 'name',
                'placeholder'	=> 'Tên',
            ),
        ));

        $this->add(array(
            'name'			=> 'code',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'			=> 'form-control',
            ),
        ));

        // Nhóm hàng
        $this->add(array(
            'name'			=> 'products_type_id',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
            ),
            'options'		=> array(
                'empty_option'	=> '- Chọn -',
                'disable_inarray_validator' => true,
                'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\ProductsTypeTable')->listItem(null, array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
            ),
        ));

        // Thương hiệu
        $this->add(array(
            'name'			=> 'trademark_id',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
            ),
            'options'		=> array(
                'empty_option'	=> '- Chọn -',
                'disable_inarray_validator' => true,
                'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'trademark')), array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
            )
        ));

        // Đơn vị
        $this->add(array(
            'name'			=> 'unit_id',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
            ),
            'options'		=> array(
                'empty_option'	=> '- Chọn -',
                'disable_inarray_validator' => true,
                'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'unit')), array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
            )
        ));

        $this->add(array(
            'name'			=> 'cost_price',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		  => 'form-control mask_currency',
            )
        ));

        $this->add(array(
            'name'			=> 'min',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		  => 'form-control mask_currency',
            )
        ));

        $this->add(array(
            'name'			=> 'max',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		  => 'form-control mask_currency',
            )
        ));

        $this->add(array(
            'name'			=> 'length',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		  => 'form-control mask_currency',
            )
        ));

        $this->add(array(
            'name'			=> 'width',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		  => 'form-control mask_currency',
            )
        ));

        $this->add(array(
            'name'			=> 'height',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		  => 'form-control mask_currency',
            )
        ));

        $this->add(array(
            'name'			=> 'weight',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		  => 'form-control mask_currency',
            )
        ));

        $this->add(array(
            'name'			=> 'note',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		     => 'form-control',
                'placeholder'	=> '',
            ),
        ));

        $this->add(array(
            'name'			=> 'ordering',
            'type'			=> 'Number',
            'attributes'	=> array(
                'value'         => 255,
                'class'			=> 'form-control',
                'id'			=> 'ordering',
            )
        ));
    }
}
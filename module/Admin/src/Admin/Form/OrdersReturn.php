<?php

namespace Admin\Form;

use \Zend\Form\Form as Form;

class OrdersReturn extends Form
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

        // Phone
        $this->add(array(
            'name'			=> 'phone',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		=> 'form-control mask_phone',
                'readonly'    => 'readonly',
            ),
        ));

        // Name
        $this->add(array(
            'name'			=> 'name',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		=> 'form-control',
                'readonly'    => 'readonly',
            ),
        ));

        // Nhóm khách hàng
        $this->add(array(
            'name'			=> 'customer_type_id',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
                'disabled'    => 'disabled',
            ),
            'options'		=> array(
                'empty_option'	=> '- Chọn -',
                'disable_inarray_validator' => true,
                'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\CustomerTypeTable')->listItem(null, array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),

            ),
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
                'data-value'  => isset($options->amount_owed) ? $options->amount_owed : null,
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
            'name'			=> 'note',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		     => 'form-control',
                'placeholder'	=> '',
            ),
        ));
    }
}
<?php

namespace Admin\Form;

use \Zend\Form\Form as Form;

class ProductsPrice extends Form
{

    public function __construct($sm, $customer_type, $product_id)
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

        // List price with customer type
        foreach ($customer_type AS $key => $value) {
            $this->add(array(
                'name'			=> $key.'_'.$product_id.'_price',
                'type'			=> 'Text',
                'attributes'	=> array(
                    'class'			=> 'form-control mask_currency',
                    'id'			=> $key.'_'.$product_id.'_price',
                ),
            ));
        }
    }
}
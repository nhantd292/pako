<?php

namespace Admin\Form;

use \Zend\Form\Form as Form;

class Warehouse extends Form
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

        $this->add(array(
            'name'			=> 'note',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		     => 'form-control',
                'placeholder'	=> '',
            ),
        ));

        $this->add(array(
            'name'			=> 'phone',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'			=> 'form-control',
                'placeholder'	=> '',
            )
        ));

        $this->add(array(
            'name'			=> 'address',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'			=> 'form-control',
                'placeholder'	=> '',
            )
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
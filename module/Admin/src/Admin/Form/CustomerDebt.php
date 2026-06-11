<?php

namespace Admin\Form;

use \Zend\Form\Form as Form;

class CustomerDebt extends Form
{

    public function __construct($sm, $options)
    {
        if ($options['action'] == 'add-expense') {
            $type = 'chi';
        }
        if ($options['action'] == 'add-revenue') {
            $type = 'thu';
        }

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
            'name'			=> 'category',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
            ),
            'options'		=> array(
                'empty_option'	=> '- Chọn -',
                'disable_inarray_validator' => true,
                'value_options'	=> \ZendX\Functions\CreateArray::create($sm->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-category', 'type' => $type)), array('task' => 'list-all')), array('key' => 'alias', 'value' => 'name')),
            )
        ));

        $UserInfo      = new \ZendX\System\UserInfo();
        $curent_user = $UserInfo->getUserInfo();
        $permission_ids = explode(',', $curent_user['permission_ids']);
        $filter_user_id = '';
        if(!in_array(SYSTEM, $permission_ids) && !in_array(ADMIN, $permission_ids) && !in_array(ACCOUNTING, $permission_ids)){
            $filter_user_id = $curent_user['id'];
        }
        $this->add(array(
            'name'       => 'customer_id',
            'type'       => 'Text',
            'attributes'   => array(
                'class'             => 'form-control select2_advance',
                'id'                => 'customer_id',
                'data-table'        => TABLE_CONTACT, // Tên bảng khách hàng của bạn
                'data-text'         => 'name, phone',    // Cột hiển thị
                'data-where_user_id'=> $filter_user_id,
                'data-type'         => "like",
                'data-type_value'   => "user_id, user_ids",
                'disabled'          => $options['action'] == 'edit-revenue' ? true : false,

                'data-target'       => '#amount_owed',       // Ô đích nhận kết quả số tiền nợ
                'data-url'          => '/xadmin/api/get-owed',
            ),
            'options'     => array(
                'label' => 'Khách hàng thu',
            )
        ));

        // Nợ cũ
        $this->add(array(
            'name'       => 'amount_owed',
            'type'       => 'Text',
            'attributes'   => array(
                'class'         => 'form-control text-red text-bold mask_currency',
                'id'            => 'amount_owed', // Thêm ID rõ ràng ở đây
                'value'         => 0,
                'data-value'    => null,
                'readonly'      => 'readonly',
            )
        ));

        // Kho hàng thu
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

        $this->add(array(
            'name'			=> 'note',
            'type'			=> 'Text',
            'attributes'	=> array(
                'class'		     => 'form-control',
                'placeholder'	=> '',
            ),
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
    }
}
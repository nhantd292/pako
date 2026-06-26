<?php

namespace Admin\Form\Search;

use kcfinder\zipFolder;
use \Zend\Form\Form as Form;

class CustomerDebt extends Form
{

    public function __construct($sm, $params)
    {
        parent::__construct();

        $UserInfo      = new \ZendX\System\UserInfo();
        $curent_user = $UserInfo->getUserInfo();
        $permission_ids = explode(',', $curent_user['permission_ids']);
        $filter_user_id = '';
        if(!in_array(SYSTEM, $permission_ids) && !in_array(ADMIN, $permission_ids) && !in_array(ACCOUNTING, $permission_ids)){
            $filter_user_id = $curent_user['id'];
        }

        // FORM Attribute
        $this->setAttributes(array(
            'action' => '',
            'method' => 'POST',
            'class' => 'horizontal-form',
            'role' => 'form',
            'name' => 'adminForm',
            'id' => 'adminForm',
        ));

        // Keyword
        $this->add(array(
            'name' => 'filter_keyword',
            'type' => 'Text',
            'attributes' => array(
                'placeholder' => 'Từ khóa',
                'class' => 'form-control input-sm',
                'id' => 'filter_keyword',
            ),
        ));

        // Bắt đầu từ ngày
        $this->add(array(
            'name' => 'filter_date_begin',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control date-picker',
                'placeholder' => 'Từ ngày',
                'autocomplete' => 'off'
            )
        ));

        // Ngày kết thúc
        $this->add(array(
            'name' => 'filter_date_end',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control date-picker',
                'placeholder' => 'Đến ngày',
                'autocomplete' => 'off'
            )
        ));

        // Loại phiếu
        $this->add(array(
            'name' => 'filter_type',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'form-control select2 select2_basic',
            ),
            'options' => array(
                'empty_option' => '- Loại phiếu -',
                'value_options' => \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-type')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name')),
            )
        ));

        // Danh mục
        $this->add(array(
            'name' => 'filter_category',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'form-control select2 select2_basic',
            ),
            'options' => array(
                'empty_option' => '- Danh mục -',
                'value_options' => \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-category')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name')),
            )
        ));

        $this->add(array(
            'name'			=> 'filter_inventory_id',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
            ),
            'options'		=> array(
                'empty_option'	=> '- Kho hàng -',
                'disable_inarray_validator' => true,
                'value_options'	=> \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\WarehouseTable')->listItem(null, array('task' => 'cache')), array('key' => 'id', 'value' => 'name')),
            ),
        ));

        $this->add(array(
            'name'			=> 'filter_state',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
            ),
            'options'		=> array(
                'empty_option'	=> '- Trạng thái -',
                'disable_inarray_validator' => true,
                'value_options' => \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'orders-state')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name')),
            ),
        ));

        // accept
        $this->add(array(
            'name'			=> 'filter_accept',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
            ),
            'options'		=> array(
                'empty_option'	=> '- Nhập sổ quỹ -',
                'value_options'	=> array( 1	=> 'Đã vào sổ quỹ', 0 => 'Chờ vào sổ quỹ', 2 => 'không vào sổ quỹ'),
            )
        ));

        $user_care	= \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\UserTable')->listItem(array('company_department_id' => 'care'), array('task' => 'list-user-department')), array('key' => 'id', 'value' => 'name'));
        $user_sales	= \ZendX\Functions\CreateArray::create($sm->get('Admin\Model\UserTable')->listItem(array('company_department_id' => 'sales'), array('task' => 'list-user-department')), array('key' => 'id', 'value' => 'name'));
        $user_data = array_merge($user_sales, $user_care);

        $this->add(array(
            'name'			=> 'filter_user',
            'type'			=> 'Select',
            'attributes'	=> array(
                'class'		=> 'form-control select2 select2_basic',
            ),
            'options'		=> array(
                'empty_option'	=> '- Nhân viên -',
                'value_options'	=> $user_data,
            )
        ));

        $this->add(array(
            'name'       => 'filter_customer_id',
            'type'       => 'Text',
            'attributes'   => array(
                'class'             => 'form-control select2_advance',
                'id'                => 'customer_id',
                'data-placeholder'  => 'Khách hàng',
                'data-table'        => TABLE_CONTACT,
                'data-where_user_id'=> $filter_user_id,
                'data-type'         => "like",
                'data-type_value'   => "user_id, user_ids",
                'data-text'         => 'name, phone',
                'data-where-status' => 1,
            )
        ));

        // Submit
        $this->add(array(
            'name' => 'filter_submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Tìm',
                'class' => 'btn btn-sm green',
            ),
        ));

        // Xóa
        $this->add(array(
            'name' => 'filter_reset',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Xóa',
                'class' => 'btn btn-sm red',
            ),
        ));

        // Order
        $this->add(array(
            'name' => 'order',
            'type' => 'Hidden',
        ));

        // Order By
        $this->add(array(
            'name' => 'order_by',
            'type' => 'Hidden',
        ));
    }
}
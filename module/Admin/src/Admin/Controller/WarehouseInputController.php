<?php

namespace Admin\Controller;

use kcfinder\zipFolder;
use ZendX\Controller\ActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Form\FormInterface;
use ZendX\System\UserInfo;

class WarehouseInputController extends ActionController{
    public $caption = 'Nhập hàng từ nhà cung cấp';
    public function init() {
        // Thiết lập options
        $this->_options['tableName'] = 'Admin\Model\WarehouseInputTable';
        $this->_options['formName'] = 'formAdminWarehouseInput';

        // Thiết lập session filter
        $ssFilter = new Container(__CLASS__ . $this->_params['action']);
        $this->_params['ssFilter']['order_by']                  = !empty($ssFilter->order_by) ? $ssFilter->order_by : 'ordering';
        $this->_params['ssFilter']['order']                     = !empty($ssFilter->order) ? $ssFilter->order : 'DESC';
        $this->_params['ssFilter']['filter_state']              = $ssFilter->filter_state;
        $this->_params['ssFilter']['filter_keyword']            = $ssFilter->filter_keyword;
        $this->_params['ssFilter']['filter_date_begin']         = $ssFilter->filter_date_begin;
        $this->_params['ssFilter']['filter_date_end']           = $ssFilter->filter_date_end;
        $this->_params['ssFilter']['filter_inventory_id']       = $ssFilter->filter_inventory_id;
        $this->_params['ssFilter']['filter_customer_id']        = $ssFilter->filter_customer_id;

        // Thiết lập lại thông số phân trang
        $this->_paginator['itemCountPerPage']               = !empty($ssFilter->pagination_option) ? $ssFilter->pagination_option : 50;
        $this->_paginator['currentPageNumber']              = $this->params()->fromRoute('page', 1);
        $this->_params['paginator'] = $this->_paginator;

        // Lấy dữ liệu post của form
        $this->_params['data'] = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
        // Truyển dữ dữ liệu ra ngoài view
        $this->_viewModel['params'] = $this->_params;
    }

    public function filterAction() {
        if ($this->getRequest()->isPost()) {
            $action = !empty($this->getRequest()->getPost('filter_action')) ? $this->getRequest()->getPost('filter_action') : 'index';
            
            $ssFilter	= new Container(__CLASS__ . $action);
            $data = $this->_params['data'];
            
            $ssFilter->pagination_option        = intval($data['pagination_option']);
            $ssFilter->order_by                 = $data['order_by'];
            $ssFilter->order                    = $data['order'];
            $ssFilter->filter_state             = $data['filter_state'];
            $ssFilter->filter_keyword           = $data['filter_keyword'];
            $ssFilter->filter_date_begin        = $data['filter_date_begin'];
            $ssFilter->filter_date_end          = $data['filter_date_end'];
            $ssFilter->filter_inventory_id      = $data['filter_inventory_id'];
            $ssFilter->filter_customer_id       = $data['filter_customer_id'];
        }

        if (!empty($this->_params['route']['id'])) {
            $ssFilter->filter_product = $this->_params['route']['id'];
        }

        $this->goRoute(array('action' => $action));
    }

    public function indexAction() {
        $myForm    = new \Admin\Form\Search\WarehouseInput($this, $this->_params['ssFilter']);
        $myForm->setData($this->_params['ssFilter']);
        // Danh sách data
        $items = $this->getTable()->listItem($this->_params, array('task' => 'list-item'));

        $this->_viewModel['myForm']             = $myForm;
        $this->_viewModel['items']              = $items;
        $this->_viewModel['model']              = $this->getTable();
        $this->_viewModel['count']              = $this->getTable()->countItem($this->_params, array('task' => 'list-item'));
        $this->_viewModel['user']               = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['branch']             = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['caption']            = $this->caption;
        $this->_viewModel['order_status']       = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'orders-state')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));

        return new ViewModel($this->_viewModel);
    }

    public function addAction() {
        $this->_params['userInfo'] = $this->_userInfo->getUserInfo();
        $number = new \ZendX\Functions\Number();
        $myForm = new \Admin\Form\WarehouseInput($this, $this->_params);
        $connection = $this->getConnection();

        if($this->getRequest()->isPost()){
            $this->_viewModel['is_post'] = 1;
            unset($this->_params['data']['filter_products_type']);
            unset($this->_params['data']['filter_keyword']);

            $myForm->setInputFilter(new \Admin\Filter\WarehouseInput(array('data' => $this->_params['data'], 'route' => $this->_params['route'])));
            $myForm->setData($this->_params['data']);
            $controlAction = $this->_params['data']['control-action'];
            $productList = $this->_params['data']['products_list'];

            $customer_id = $this->_params['data']['customer_id'];
            $contact_item = $this->getServiceLocator()->get('Admin\Model\ContactTable')->getItem(array('id' => $customer_id));

            if($myForm->isValid()){
                $check_emty_data = !empty($productList) ? true : false;

                for ($i = 0; $i < count($productList['products_id']); $i++ ){
                    if(
                        trim($productList['products_id'][$i]) == "" ||
                        trim($productList['price'][$i]) == "" ||
                        (int)trim($productList['quantity'][$i]) == 0
                    )$check_emty_data = false;
                }

                if($check_emty_data){
                    $products_detail  = array();
                    $total_number_product = 0;
                    $price_total = 0;
                    for($i = 0; $i < count($productList['products_id']); $i++){
                        if(!empty($productList['products_id'][$i])) {
                            $products_detail[$i]['note']             = $productList['note'][$i]; // Tên đầy đủ
                            $products_detail[$i]['quantity']         = $number->formatToData($productList['quantity'][$i]); // số lượng của đơn hàng
                            $products_detail[$i]['price']            = $number->formatToData($productList['price'][$i]); // giá bán
                            $products_detail[$i]['total']            = $number->formatToData($productList['quantity'][$i]) * $number->formatToData($productList['price'][$i]) ; // tổng tiền (chính là cột thành tiền)
                            $products_detail[$i]['products_id']      = $productList['products_id'][$i]; // id sản phẩm

                            $total_number_product += $number->formatToData($productList['quantity'][$i]);
                            $price_total += $products_detail[$i]['total'];
                        }
                    }
                    $this->_params['data']['price_total'] = $price_total;
                    $this->_params['data']['state'] = NEW_STATUS;

                    ##### begin #####
                    $connection->beginTransaction();

                    # tạo phiếu trả hàng
                    $warehouse_input_id = $this->getTable()->saveItem($this->_params, array('task' => 'add-item'));

                    // Thêm chi tiết sản phẩm
                    foreach($products_detail as $arraydata){
                        $this->getServiceLocator()->get('Admin\Model\WarehouseInputDetailTable')->saveItem(array('data' => $arraydata, 'warehouse_input_id' => $warehouse_input_id), array('task' => 'add-item'));
                    }
                    # tạo phiếu thu cho khách hàng
                    $count_debt = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->countItem(array('ssFilter' => array('filter_customer_id' => $customer_id)), array('task' => 'list-item'));
                    if ($count_debt > 0) {
                        $list_debt = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->listItem(array('ssFilter' => array('filter_customer_id' => $customer_id)), array('task' => 'list-item', 'paginator' => false));
                        $list_debt = $list_debt->toArray();
                        $ucdebt = $list_debt[0];
                        $old_debt = $ucdebt['new_debt'];
                    }
                    else{
                        $old_debt = $contact_item['amount_owed'];
                    }

                    $discount       = $number->formatToData($this->_params['data']['discount']);
                    $paid_cash      = $number->formatToData($this->_params['data']['paid_cash']);
                    $paid_transfer  = $number->formatToData($this->_params['data']['paid_transfer']);
                    $price_total    = $number->formatToData($this->_params['data']['price_total']);
                    $new_debt       = $old_debt - ($price_total - $discount - $paid_cash - $paid_transfer);
                    $data_debt = array(
                        'customer_id' => $customer_id,
                        'type' => PNH,
                        'warehouse_input_id' => $warehouse_input_id,
                        'inventory_id' => $this->_params['data']['inventory_id'],
                        'price_total' => $price_total,
                        'discount' => -$discount,
                        'paid_cash' => -$paid_cash,
                        'paid_transfer' => -$paid_transfer,
                        'old_debt' => $old_debt,
                        'new_debt' => $new_debt,
                        'state' => NEW_STATUS,
                        'category' => CATEGORY_PNH,
                    );
                    $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->saveItem(array('data' => $data_debt), array('task' => 'add-item'));

                    $connection->commit();
                    ##### end #####

                    $this->flashMessenger()->addSuccessMessage('Dữ liệu đã được cập nhật thành công');


                    if($controlAction == 'save-new') {
                        $this->goRoute(array('action' => 'add'));
                    } else if($controlAction == 'save') {
                        $this->goRoute(array('action' => 'detail', 'id' => $warehouse_input_id));
                    } else {
                        $this->goRoute();
                    }
                }
                else{
                    $this->_viewModel['check_product_id'] = 'Cần nhập đầy đủ thông tin của sản phẩm';
                    $this->_viewModel['productList'] = $productList;
                    $this->_viewModel['data']  = $this->_params['data'];
                }
            }
            else {
                $this->_viewModel['productList']  = $productList;
                $this->_viewModel['data']  = $this->_params['data'];
            }
        }

        $this->_viewModel['products_type']  = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\ProductsTypeTable')->listItem(null, array('task' => 'cache')), array('key' => 'id', 'value' => 'name'));
        $this->_viewModel['myForm']	        = $myForm;
        $this->_viewModel['caption']        = 'Thêm mới - '.$this->caption;
        return new ViewModel($this->_viewModel);
    }

    // Xem chi tiết
    public function detailAction() {
        $id = $this->params('id');
        if($id) {
            $connection = $this->getConnection();
            $item = $this->getTable()->getItem(array('id' => $id), array('task' => 'full'));
            $debt_item = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->getItem(array('warehouse_input_id' => $id), array('task' => 'type-id'));
        } else {
            return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
        }
        if($this->getRequest()->isPost()){
            $control_action = $this->_params['data']['control-action'];
            if (in_array($item['state'], array(COMPLETE_STATUS, CANCEL_STATUS))) {
                $state_text = $item['state'] == CANCEL_STATUS ? 'HỦY' : 'HOÀN THÀNH';
                $this->flashMessenger()->addErrorMessage('Phiếu nhập hàng đã ở trạng thái "'.$state_text.'" không thể cập nhật dữ liệu!');
            }
            else{
                if ($control_action == PROCESSING_STATUS) {
                    $connection->beginTransaction();
                    $this->getTable()->saveItem(array('data' => array('id' => $id, 'state' => PROCESSING_STATUS)), array('task' => 'update-state'));

                    # cập nhật trạng thái phiếu thu
                    $debt_item_old = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->getItem(array('warehouse_input_id' => $id), array('task' => 'type-id'));
                    $data_debt = array(
                        'id' => $debt_item_old->id,
                        'state' => PROCESSING_STATUS,
                    );
                    $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                    $connection->commit();
                    $this->flashMessenger()->addSuccessMessage('Phiếu trả hàng đã chuyển sang trạng thái "ĐANG XỬ LÝ"');
                }
                if ($control_action == CANCEL_STATUS) {
                    ##### begin #####
                    $connection->beginTransaction();
                    # cập nhật trạng thái hủy cho đơn hàng.
                    $this->getTable()->saveItem(array('data' => array('id' => $id, 'state' => CANCEL_STATUS)), array('task' => 'update-state'));

                    # Sửa phiếu thu chi khách hàng
                    $debt_item_old = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->getItem(array('warehouse_input_id' => $id), array('task' => 'type-id'));
                    $data_debt = array(
                        'id' => $debt_item_old->id,
                        'price_total' => 0,
                        'discount' => 0,
                        'paid_cash' => 0,
                        'paid_transfer' => 0,
                        'new_debt' => $debt_item_old->old_debt,
                        'state' => CANCEL_STATUS,
                    );
                    $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                    $connection->commit();
                    $this->flashMessenger()->addSuccessMessage('Hủy đơn hàng thành công!');
                }
                if ($control_action == COMPLETE_STATUS) {
                    ##### begin #####
                    $connection->beginTransaction();
                    # cập nhật trạng thái hoàn thành cho đơn hàng.
                    $this->getTable()->saveItem(array('data' => array('id' => $id, 'state' => COMPLETE_STATUS)), array('task' => 'update-state'));

                    # cập nhật tồn kho cho sản phẩm.
                    $products_detail = $this->getServiceLocator()->get('Admin\Model\WarehouseInputDetailTable')->listItem(array('warehouse_input_id' => $id), array('task' => 'list-ajax'));
                    foreach ($products_detail as $detail_item) {
                        // cập nhật số lượng hàng trả cho bảng contract detail
                        $contract_detail_item_update = $this->getServiceLocator()->get('Admin\Model\ContractDetailTable')->getItem(array('id' => $detail_item['orders_detail_id']));
                        $number_return_new = $contract_detail_item_update['numbers_return'] + $detail_item['quantity'];
                        $this->getServiceLocator()->get('Admin\Model\ContractDetailTable')->saveItem(array('data' => array('id' => $detail_item['orders_detail_id'], 'numbers_return' => $number_return_new)), array('task' => 'update-number'));

                        if ($detail_item['quantity'] > $detail_item['contract_detail_quantity']) {
                            $this->flashMessenger()->addErrorMessage('Số lượng sản phẩm "'.$detail_item['products_name'].'" trả lại nhiều hơn số lượng đã mua, số lượng mua là "'.$detail_item['contract_detail_quantity'].'" !');
                            $this->goRoute(array('action' => 'detail', 'id' => $id));
                            return false;
                        }
                        $inventory = $this->getServiceLocator()->get('Admin\Model\ProductsInventoryTable')->getItem(array('products_id' => $detail_item->product_id, 'warehouse_id' => $item->inventory_id), array('task' => 'filter'));
                        $quantity_new = $inventory->quantity + $detail_item->quantity;
                        $this->getServiceLocator()->get('Admin\Model\ProductsInventoryTable')->saveItem(array('data' => array('quantity' => $quantity_new, 'id' => $inventory->id)), array('task' => 'edit-item'));
                    }

                    # Sửa phiếu thu chi khách hàng
                    $debt_item_old = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->getItem(array('warehouse_input_id' => $id), array('task' => 'type-id'));
                    $data_debt = array(
                        'id' => $debt_item_old->id,
                        'state' => COMPLETE_STATUS,
                    );
                    $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                    $connection->commit();
                    $this->flashMessenger()->addSuccessMessage('Phiếu trả hàng đã được hoàn thành!');
                }


                $item = $this->getTable()->getItem(array('id' => $id));
            }
        }

        $this->_viewModel['item']                       = $item;
        $this->_viewModel['debt_item']                  = $debt_item;
        $this->_viewModel['contact']                    = $this->getServiceLocator()->get('Admin\Model\ContactTable')->getItem(array('id' => $item['contact_id']));
        $this->_viewModel['user']                       = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['customer_type']              = $this->getServiceLocator()->get('Admin\Model\CustomerTypeTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['warehouse']                  = $this->getServiceLocator()->get('Admin\Model\WarehouseTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['sale_group']                 = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'lists-group')), array('task' => 'cache'));
        $this->_viewModel['sale_branch']                = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['location_city']              = $this->getServiceLocator()->get('Admin\Model\LocationsTable')->listItem(array('level' => 1), array('task' => 'cache'));
        $this->_viewModel['location_district']          = $this->getServiceLocator()->get('Admin\Model\LocationsTable')->listItem(array('level' => 2), array('task' => 'cache'));
        $this->_viewModel['location_town']              = $this->getServiceLocator()->get('Admin\Model\LocationsTable')->listItem(array('level' => 3), array('task' => 'cache'));
        $this->_viewModel['sex']                        = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sex')), array('task' => 'cache-alias'));
        $this->_viewModel['status']                     = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'status')), array('task' => 'cache'));
        $this->_viewModel['order_status']               = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'orders-state')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['production_type']            = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'production-type')), array('task' => 'cache')), array('key' => 'id', 'value' => 'object'));
        $this->_viewModel['caption']                    = 'Chi tiết - '.$this->caption. ' - '. $item['code'];
        $viewModel = new ViewModel($this->_viewModel);
        return $viewModel;
    }

    public function editAction() {
        $this->_params['userInfo'] = $this->_userInfo->getUserInfo();
        $number = new \ZendX\Functions\Number();
        $dateFormat = new \ZendX\Functions\Date();
        $connection = $this->getConnection();
        $id = $this->params('id');

        if(!empty($id)) {
            $item = $this->getTable()->getItem(array('id' => $id), array('task' => 'full'));
            if (in_array($item['state'], array(COMPLETE_STATUS, CANCEL_STATUS))) {
                $state_text = $item['state'] == CANCEL_STATUS ? 'HỦY' : 'HOÀN THÀNH';
                $this->flashMessenger()->addErrorMessage('Phiếu nhập hàng đã ở trạng thái "'.$state_text.'" không thể cập nhật dữ liệu!');
                $this->goRoute(array('action' => 'detail', 'id' => $id));
                return false;
            }
            $item['amount_owed'] = $item['old_debt'];
            $products_detail = $this->getServiceLocator()->get('Admin\Model\WarehouseInputDetailTable')->listItem(array('warehouse_input_id' => $id), array('task' => 'list-ajax'))->toArray();
            $this->_viewModel['products_detail']     = $products_detail;

            $myForm = new \Admin\Form\WarehouseInput($this, $item);
            $myForm->setData($item);
            $this->_viewModel['item']        = $item;
        }
        else {
            return $this->redirect()->toRoute('routeAdmin/type', array('controller' => 'notice', 'action' => 'not-found', 'type' => 'not-found'));
        }

        $customer_id = $item['customer_id'];
        $contact_item = $this->getServiceLocator()->get('Admin\Model\ContactTable')->getItem(array('id' => $customer_id));
        if(empty($contact_item)){
            return $this->redirect()->toRoute('routeAdmin/type', array('controller' => 'notice', 'action' => 'lock', 'type' => 'not-found'));
        }

        if($this->getRequest()->isPost()){
            $this->_viewModel['is_post'] = 1;
            unset($this->_params['data']['filter_products_type']);
            unset($this->_params['data']['filter_keyword']);

            $myForm->setInputFilter(new \Admin\Filter\WarehouseInput(array('data' => $this->_params['data'], 'route' => $this->_params['route'])));
            $myForm->setData($this->_params['data']);
            $controlAction = $this->_params['data']['control-action'];
            $productList = $this->_params['data']['products_list'];

            $customer_id = $this->_params['data']['customer_id'];
            $contact_item = $this->getServiceLocator()->get('Admin\Model\ContactTable')->getItem(array('id' => $customer_id));

            if($myForm->isValid()){
                $check_emty_data = !empty($productList) ? true : false;

                for ($i = 0; $i < count($productList['products_id']); $i++ ){
                    if(
                        trim($productList['products_id'][$i]) == "" ||
                        trim($productList['price'][$i]) == "" ||
                        (int)trim($productList['quantity'][$i]) == 0
                    )$check_emty_data = false;
                }

                if($check_emty_data){
                    $products_detail  = array();
                    $price_total = 0;
                    for($i = 0; $i < count($productList['products_id']); $i++){
                        if(!empty($productList['products_id'][$i])) {
                            $products_detail[$i]['note']             = $productList['note'][$i]; // Tên đầy đủ
                            $products_detail[$i]['quantity']         = $number->formatToData($productList['quantity'][$i]); // số lượng của đơn hàng
                            $products_detail[$i]['price']            = $number->formatToData($productList['price'][$i]); // giá bán
                            $products_detail[$i]['total']            = $number->formatToData($productList['quantity'][$i]) * $number->formatToData($productList['price'][$i]) ; // tổng tiền (chính là cột thành tiền)
                            $products_detail[$i]['products_id']      = $productList['products_id'][$i]; // id sản phẩm

                            $price_total += $products_detail[$i]['total'];
                        }
                    }
                    $this->_params['data']['price_total'] = $price_total;

                    ##### begin #####
                    $connection->beginTransaction();

                    # Sửa phiếu trả hàng
                    $warehouse_input_id = $this->getTable()->saveItem($this->_params, array('task' => 'edit-item'));
                    // Xóa chi tiết sản phẩm
                    $this->getServiceLocator()->get('Admin\Model\WarehouseInputDetailTable')->saveItem(array('warehouse_input_id' => $warehouse_input_id), array('task' => 'delete_product_by_warehouse_input_id'));
                    // Thêm chi tiết sản phẩm
                    foreach($products_detail as $arraydata){
                        $this->getServiceLocator()->get('Admin\Model\WarehouseInputDetailTable')->saveItem(array('data' => $arraydata, 'warehouse_input_id' => $warehouse_input_id), array('task' => 'add-item'));
                    }
                    # Sửa phiếu thu cho khách hàng
                    $debt_item_old = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->getItem(array('warehouse_input_id' => $warehouse_input_id), array('task' => 'type-id'));

                    $discount       = $number->formatToData($this->_params['data']['discount']);
                    $paid_cash      = $number->formatToData($this->_params['data']['paid_cash']);
                    $paid_transfer  = $number->formatToData($this->_params['data']['paid_transfer']);
                    $price_total    = $number->formatToData($this->_params['data']['price_total']);
                    $new_debt       = $debt_item_old->old_debt - ($price_total - $discount - $paid_cash - $paid_transfer);
                    $data_debt = array(
                        'id' => $debt_item_old->id,
                        'inventory_id' => $this->_params['data']['inventory_id'],
                        'price_total' => $price_total,
                        'discount' => -$discount,
                        'paid_cash' => -$paid_cash,
                        'paid_transfer' => -$paid_transfer,
                        'new_debt' => $new_debt,
                    );
                    $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                    $connection->commit();
                    ##### end #####

                    $this->flashMessenger()->addSuccessMessage('Dữ liệu đã được cập nhật thành công');


                    if($controlAction == 'save-new') {
                        $this->goRoute(array('action' => 'add'));
                    } else if($controlAction == 'save') {
                        $this->goRoute(array('action' => 'detail', 'id' => $warehouse_input_id));
                    } else {
                        $this->goRoute();
                    }
                }
                else{
                    $this->_viewModel['check_product_id'] = 'Cần nhập đầy đủ thông tin của sản phẩm';
                    $this->_viewModel['productList'] = $productList;
                    $this->_viewModel['data']  = $this->_params['data'];
                }
            }
            else {
                $this->_viewModel['productList']  = $productList;
                $this->_viewModel['data']  = $this->_params['data'];
            }
        }

        $this->_viewModel['contactId']      = $customer_id;
        $this->_viewModel['products_type']  = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\ProductsTypeTable')->listItem(null, array('task' => 'cache')), array('key' => 'id', 'value' => 'name'));
        $this->_viewModel['myForm']	        = $myForm;
        $this->_viewModel['caption']        = 'Thêm mới - '.$this->caption;
        return new ViewModel($this->_viewModel);
    }

    public function deleteAction() {
        if($this->getRequest()->isPost()) {
            if(!empty($this->_params['data']['cid'])) {
                $cdata = $this->getTable()->deleteItem($this->_params, array('task' => 'delete-item'));
                $message = 'Xóa '. $cdata .' '.$this->caption.' thành công';
                $this->flashMessenger()->addSuccessMessage($message);
            }
        }

        $this->goRoute(array('action' => 'index'));
    }
}

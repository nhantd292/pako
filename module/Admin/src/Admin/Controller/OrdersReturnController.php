<?php

namespace Admin\Controller;

use kcfinder\zipFolder;
use ZendX\Controller\ActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Form\FormInterface;
use ZendX\System\UserInfo;

class OrdersReturnController extends ActionController{
    public $caption = 'Khách hàng trả hàng';
    public function init() {
        // Thiết lập options
        $this->_options['tableName'] = 'Admin\Model\OrdersReturnTable';
        $this->_options['formName'] = 'formAdminOrdersReturn';

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
        $curent_user = $this->_userInfo->getUserInfo();
        $permission_ids = explode(',', $curent_user['permission_ids']);
        if(!in_array(SYSTEM, $permission_ids) && !in_array(ADMIN, $permission_ids)){
            if (in_array(ACCOUNTING, $permission_ids)) {
                if (!empty($curent_user['inventory_id'])) {
                    $this->_params['ssFilter']['filter_inventory_id'] = $curent_user['inventory_id'];
                }
            }
            else {
                $this->_params['ssFilter']['filter_user'] = $curent_user['id'];
            }
        }
        else{
            if (in_array(ACCOUNTING, $permission_ids)) {
                if (!empty($curent_user['inventory_id'])) {
                    $this->_params['ssFilter']['filter_inventory_id'] = $curent_user['inventory_id'];
                }
            }
        }

        $myForm    = new \Admin\Form\Search\OrdersRetrun($this, $this->_params['ssFilter']);
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

    // Xem chi tiết Đơn hàng
    public function detailAction() {
        $id = $this->params('id');
        if($id) {
            $connection = $this->getConnection();
            $item = $this->getTable()->getItem(array('id' => $id), array('task' => 'full'));
            $debt_item = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->getItem(array('orders_return_id' => $id), array('task' => 'type-id'));
        } else {
            return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
        }
        $permission_ids = explode(',', $this->_userInfo->getUserInfo('permission_ids'));
        $uid = $this->_userInfo->getUserInfo('id');

        if($this->getRequest()->isPost()){
            $control_action = $this->_params['data']['control-action'];

            if ($control_action == PROCESSING_STATUS) {
                $connection->beginTransaction();
                $this->getTable()->saveItem(array('data' => array('id' => $id, 'state' => PROCESSING_STATUS)), array('task' => 'update-state'));

                # cập nhật trạng thái phiếu thu
                $debt_item_old = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->getItem(array('orders_return_id' => $id), array('task' => 'type-id'));
                $data_debt = array(
                    'id' => $debt_item_old->id,
                    'state' => PROCESSING_STATUS,
                );
                $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                $connection->commit();
                $this->flashMessenger()->addSuccessMessage('Phiếu trả hàng đã chuyển sang trạng thái "ĐANG XỬ LÝ"');
            }
            if ($control_action == CANCEL_STATUS) {
                if (($item['state'] == NEW_STATUS && $item['created_by'] == $uid) || in_array(SYSTEM, $permission_ids) || in_array(ADMIN, $permission_ids)){
                    ##### begin #####
                    $connection->beginTransaction();
                    # cập nhật trạng thái hủy cho đơn hàng.
                    $this->getTable()->saveItem(array('data' => array('id' => $id, 'state' => CANCEL_STATUS)), array('task' => 'update-state'));

                    # Sửa phiếu thu chi khách hàng
                    $debt_item_old = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->getItem(array('orders_return_id' => $id), array('task' => 'type-id'));
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
                else{
                    $this->flashMessenger()->addErrorMessage('Chỉ có thể Hủy phiếu trả hàng khi phiếu đơn hàng ở trạng thái phiếu tạm!');
                }
            }
            if ($control_action == COMPLETE_STATUS) {
                if ((($item['state'] == PROCESSING_STATUS) && $item['created_by'] == $uid) || in_array(SYSTEM, $permission_ids) || in_array(ADMIN, $permission_ids) || in_array(ACCOUNTING, $permission_ids)){
                    ##### begin #####
                    $connection->beginTransaction();
                    # cập nhật trạng thái hoàn thành cho đơn hàng.
                    $this->getTable()->saveItem(array('data' => array('id' => $id, 'state' => COMPLETE_STATUS)), array('task' => 'update-state'));

                    # cập nhật tồn kho cho sản phẩm.
                    $products_detail = $this->getServiceLocator()->get('Admin\Model\OrdersReturnDetailTable')->listItem(array('orders_return_id' => $id), array('task' => 'list-ajax'));
                    foreach ($products_detail as $detail_item) {
                        // cập nhật số lượng hàng trả cho bảng contract detail
                        $contract_detail_item_update = $this->getServiceLocator()->get('Admin\Model\ContractDetailTable')->getItem(array('id' => $detail_item['orders_detail_id']));
                        $this->getServiceLocator()->get('Admin\Model\ContractTable')->saveItem(array('data' => array('id' => $contract_detail_item_update['contract_id'])), array('task' => 'update-return-status'));
                        $number_return_new = $contract_detail_item_update['numbers_return'] + $detail_item['quantity'];
                        $this->getServiceLocator()->get('Admin\Model\ContractDetailTable')->saveItem(array('data' => array('id' => $detail_item['orders_detail_id'], 'numbers_return' => $number_return_new)), array('task' => 'update-number'));
                        $net_number = $detail_item['contract_detail_quantity'] - $detail_item['contract_detail_quantity_return'];
                        if ($detail_item['quantity'] > $net_number) {
                            $this->flashMessenger()->addErrorMessage('Số lượng sản phẩm "'.$detail_item['products_name'].'" trả lại nhiều hơn số lượng đã mua, số lượng mua là "'.$net_number.'" !');
                            $this->goRoute(array('action' => 'detail', 'id' => $id));
                            return false;
                        }
                        $inventory = $this->getServiceLocator()->get('Admin\Model\ProductsInventoryTable')->getItem(array('products_id' => $detail_item->product_id, 'warehouse_id' => $item->inventory_id), array('task' => 'filter'));
                        $quantity_new = $inventory->quantity + $detail_item->quantity;
                        $this->getServiceLocator()->get('Admin\Model\ProductsInventoryTable')->saveItem(array('data' => array('quantity' => $quantity_new, 'id' => $inventory->id)), array('task' => 'edit-item'));
                    }

                    # Sửa phiếu thu chi khách hàng
                    $debt_item_old = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->getItem(array('orders_return_id' => $id), array('task' => 'type-id'));
                    $data_debt = array(
                        'id' => $debt_item_old->id,
                        'state' => COMPLETE_STATUS,
                    );
                    $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                    $connection->commit();
                    $this->flashMessenger()->addSuccessMessage('Phiếu trả hàng đã được hoàn thành!');
                }
                else{
                    $this->flashMessenger()->addErrorMessage('Bạn không thể hoàn thành phiếu trả hàng!');
                }
            }

            $item = $this->getTable()->getItem(array('id' => $id));
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
}

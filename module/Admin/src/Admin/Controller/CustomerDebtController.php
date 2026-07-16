<?php

namespace Admin\Controller;

use kcfinder\zipFolder;
use Zend\Paginator\Adapter\Null;
use ZendX\Controller\ActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Form\FormInterface;
use ZendX\System\UserInfo;

class CustomerDebtController extends ActionController
{
    public $caption = 'Thu chi khách hàng';

    public function init()
    {
        // Thiết lập options
        $this->_options['tableName'] = 'Admin\Model\CustomerDebtTable';
        $this->_options['formName'] = 'formAdminCustomerDebt';
        // Thiết lập session filter
        $action = !empty($this->getRequest()->getPost('filter_action')) ? str_replace('-', '_', $this->getRequest()->getPost('filter_action')) : 'index';
        $ssFilter = new Container(__CLASS__ . $action);

        $this->_params['ssFilter']['order_by'] = !empty($ssFilter->order_by) ? $ssFilter->order_by : 'ordering';
        $this->_params['ssFilter']['order'] = !empty($ssFilter->order) ? $ssFilter->order : 'DESC';
        $this->_params['ssFilter']['filter_status'] = $ssFilter->filter_status;
        $this->_params['ssFilter']['filter_keyword'] = $ssFilter->filter_keyword;
        $this->_params['ssFilter']['filter_date_begin'] = $ssFilter->filter_date_begin;
        $this->_params['ssFilter']['filter_date_end'] = $ssFilter->filter_date_end;
        $this->_params['ssFilter']['filter_state'] = $ssFilter->filter_state;
        $this->_params['ssFilter']['filter_accept'] = $ssFilter->filter_accept;
        $this->_params['ssFilter']['filter_type'] = $ssFilter->filter_type;
        $this->_params['ssFilter']['filter_category'] = $ssFilter->filter_category;
        $this->_params['ssFilter']['filter_inventory_id'] = $ssFilter->filter_inventory_id;
        $this->_params['ssFilter']['filter_customer_id'] = $ssFilter->filter_customer_id;
        $this->_params['ssFilter']['filter_user'] = $ssFilter->filter_user;
        $this->_params['ssFilter']['filter_option_mtt'] = $ssFilter->filter_option_mtt;

        // Thiết lập lại thông số phân trang
        $this->_paginator['itemCountPerPage'] = !empty($ssFilter->pagination_option) ? $ssFilter->pagination_option : 50;
        $this->_paginator['currentPageNumber'] = $this->params()->fromRoute('page', 1);
        $this->_params['paginator'] = $this->_paginator;

        // Lấy dữ liệu post của form
        $this->_params['data'] = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
        // Truyển dữ dữ liệu ra ngoài view
        $this->_viewModel['params'] = $this->_params;
    }

    public function filterAction()
    {
        if ($this->getRequest()->isPost()) {
            $action = !empty($this->getRequest()->getPost('filter_action')) ? $this->getRequest()->getPost('filter_action') : 'index';

            $ssFilter = new Container(__CLASS__ . $action);
            $data = $this->_params['data'];

            $ssFilter->pagination_option = intval($data['pagination_option']);
            $ssFilter->order_by = $data['order_by'];
            $ssFilter->order = $data['order'];
            $ssFilter->filter_status = $data['filter_status'];
            $ssFilter->filter_keyword = $data['filter_keyword'];
            $ssFilter->filter_date_begin = $data['filter_date_begin'];
            $ssFilter->filter_date_end = $data['filter_date_end'];
            $ssFilter->filter_state = $data['filter_state'];
            $ssFilter->filter_accept = $data['filter_accept'];
            $ssFilter->filter_type = $data['filter_type'];
            $ssFilter->filter_category = $data['filter_category'];
            $ssFilter->filter_inventory_id = $data['filter_inventory_id'];
            $ssFilter->filter_customer_id = $data['filter_customer_id'];
            $ssFilter->filter_user = $data['filter_user'];
            $ssFilter->filter_option_mtt = $data['filter_option_mtt'];
        }

        if (!empty($this->_params['route']['id'])) {
            $ssFilter->filter_product = $this->_params['route']['id'];
        }

        $this->goRoute(array('action' => $action));
    }

    public function indexAction()
    {
        $ssFilter = new Container(__CLASS__.'index');
        $curent_user = $this->_userInfo->getUserInfo();
        $permission_ids = explode(',', $curent_user['permission_ids']);
        if(!in_array(SYSTEM, $permission_ids) && !in_array(ADMIN, $permission_ids)){
            $this->_params['ssFilter']['filter_user'] = $curent_user['id'];
        }

        $myForm = new \Admin\Form\Search\CustomerDebt($this->getServiceLocator(), $this->_params['ssFilter']);
        $myForm->setData($this->_params['ssFilter']);
        // Danh sách data
        $items = $this->getTable()->listItem($this->_params, array('task' => 'list-item'));

        $this->_viewModel['myForm'] = $myForm;
        $this->_viewModel['items'] = $items;
        $this->_viewModel['model'] = $this->getTable();
        $this->_viewModel['count'] = $this->getTable()->countItem($this->_params, array('task' => 'list-item'));
        $this->_viewModel['order_status'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'orders-state')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));

        $this->_viewModel['user'] = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['branch'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['debt_category'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-category')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name'));
        $this->_viewModel['debt_type'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-type')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name'));
        $this->_viewModel['caption'] = $this->caption;

        return new ViewModel($this->_viewModel);
    }

    public function addRevenueAction()
    {
        $myForm = new \Admin\Form\CustomerDebt($this, $this->_params);
        $number = new \ZendX\Functions\Number();
        $connection = $this->getConnection();

        if ($this->getRequest()->isPost()) {
            $myForm->setInputFilter(new \Admin\Filter\CustomerDebt());
            $myForm->setData($this->_params['data']);
            $controlAction = $this->_params['data']['control-action'];

            if ($myForm->isValid()) {
                $this->_params['data'] = $myForm->getData(FormInterface::VALUES_AS_ARRAY);
                $customer_id = $this->_params['data']['customer_id'];
                $contact_item = $this->getServiceLocator()->get('Admin\Model\ContactTable')->getItem(array('id' => $customer_id));

                ##### begin #####
                $connection->beginTransaction();
                # tạo phiếu thu cho khách hàng
                $count_debt = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->countItem(array('ssFilter' => array('filter_customer_id' => $customer_id)), array('task' => 'list-item'));
                if ($count_debt > 0) {
                    $list_debt = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->listItem(array('ssFilter' => array('filter_customer_id' => $customer_id)), array('task' => 'list-item', 'paginator' => false));
                    $list_debt = $list_debt->toArray();
                    $ucdebt = $list_debt[0];
                    $old_debt = $ucdebt['new_debt'];
                } else {
                    $old_debt = $contact_item['amount_owed'];
                }

                $paid_cash = $number->formatToData($this->_params['data']['paid_cash']);
                $paid_transfer = $number->formatToData($this->_params['data']['paid_transfer']);
                $new_debt = $old_debt - ($paid_cash + $paid_transfer);
                $data_debt = array(
                    'customer_id' => $customer_id,
                    'type' => THU,
                    'inventory_id' => $this->_params['data']['inventory_id'],
                    'price_total' => 0,
                    'discount' => 0,
                    'paid_cash' => $paid_cash,
                    'paid_transfer' => $paid_transfer,
                    'old_debt' => $old_debt,
                    'new_debt' => $new_debt,
                    'state' => NEW_STATUS,
                    'category' => $this->_params['data']['category'],
                    'note' => $this->_params['data']['note'],
                    'date' => $this->_params['data']['date'],
                );
                $result = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->saveItem(array('data' => $data_debt), array('task' => 'add-item'));


                $connection->commit();


                $this->flashMessenger()->addSuccessMessage('Thêm mới ' . $this->caption . ' thành công');

                if ($controlAction == 'save-new') {
                    $this->goRoute(array('action' => 'add-revenue'));
                } else if ($controlAction == 'save') {
                    $this->goRoute(array('action' => 'detail-revenue', 'id' => $result));
                } else {
                    $this->goRoute();
                }
            }
        }

        $this->_viewModel['myForm'] = $myForm;
        $this->_viewModel['caption'] = 'Thêm mới - Phiếu Thu ';
        return new ViewModel($this->_viewModel);
    }

    public function detailRevenueAction()
    {
        $id = $this->params('id');
        if ($id) {
            $connection = $this->getConnection();
            $item = $this->getTable()->getItem(array('id' => $id), array('task' => 'type-id'));
            if (empty($item)) {
                return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
            } else {
                if (!in_array($item['type'], [THU, CHI])) {
                    return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
                }
            }
        } else {
            return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
        }
        if ($this->getRequest()->isPost()) {
            $control_action = $this->_params['data']['control-action'];
            if (!in_array($item['type'], [THU, CHI])) {
                return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
            }
            if (in_array($item['state'], array(COMPLETE_STATUS, CANCEL_STATUS))) {
                $state_text = $item['state'] == CANCEL_STATUS ? 'HỦY' : 'HOÀN THÀNH';
                $this->flashMessenger()->addErrorMessage('Phiếu thu đã ở trạng thái "' . $state_text . '" không thể cập nhật dữ liệu!');
            } else {
                if ($control_action == CANCEL_STATUS) {
                    ##### begin #####
                    $connection->beginTransaction();

                    # Sửa phiếu thu chi khách hàng
                    $debt_item_old = $item;
                    $data_debt = array(
                        'id' => $debt_item_old->id,
                        'price_total' => 0,
                        'discount' => 0,
                        'paid_cash' => 0,
                        'paid_transfer' => 0,
                        'new_debt' => $debt_item_old->old_debt,
                        'state' => CANCEL_STATUS,
                    );
                    $this->getTable()->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                    $connection->commit();
                    $this->flashMessenger()->addSuccessMessage('Hủy phiếu thu thành công!');
                }
                if ($control_action == COMPLETE_STATUS) {
                    ##### begin #####
                    $connection->beginTransaction();
                    # Sửa phiếu thu chi khách hàng
                    $debt_item_old = $item;
                    $data_debt = array(
                        'id' => $debt_item_old->id,
                        'state' => COMPLETE_STATUS,
                    );
                    $this->getTable()->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                    $connection->commit();
                    $this->flashMessenger()->addSuccessMessage('Phiếu thu đã được hoàn thành!');
                }
                if ($control_action == COMPLETE_STATUS.'NotFund') {
                    ##### begin #####
                    $connection->beginTransaction();
                    # Sửa phiếu thu chi khách hàng
                    $debt_item_old = $item;
                    $data_debt = array(
                        'id' => $debt_item_old->id,
                        'state' => COMPLETE_STATUS,
                        'accept' => 2,
                    );
                    $this->getTable()->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                    $connection->commit();
                    $this->flashMessenger()->addSuccessMessage('Phiếu chi đã được hoàn thành!');
                }

                $item = $this->getTable()->getItem(array('id' => $id));
            }
        }

        $this->_viewModel['item'] = $item;
        $this->_viewModel['contact'] = $this->getServiceLocator()->get('Admin\Model\ContactTable')->getItem(array('id' => $item['contact_id']));
        $this->_viewModel['user'] = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['customer_type'] = $this->getServiceLocator()->get('Admin\Model\CustomerTypeTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['warehouse'] = $this->getServiceLocator()->get('Admin\Model\WarehouseTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['sale_group'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'lists-group')), array('task' => 'cache'));
        $this->_viewModel['sale_branch'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['order_status'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'orders-state')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['caption'] = 'Chi tiết phiếu thu - ' . $item['code'];
        $viewModel = new ViewModel($this->_viewModel);
        return $viewModel;
    }

    public function addExpenseAction()
    {
        $this->_params['type'] = 'thu';
        $myForm = new \Admin\Form\CustomerDebt($this, $this->_params);
        $number = new \ZendX\Functions\Number();
        $connection = $this->getConnection();

        if ($this->getRequest()->isPost()) {
            $myForm->setInputFilter(new \Admin\Filter\CustomerDebt());
            $myForm->setData($this->_params['data']);
            $controlAction = $this->_params['data']['control-action'];

            if ($myForm->isValid()) {
                $this->_params['data'] = $myForm->getData(FormInterface::VALUES_AS_ARRAY);
                $customer_id = $this->_params['data']['customer_id'];
                $contact_item = $this->getServiceLocator()->get('Admin\Model\ContactTable')->getItem(array('id' => $customer_id));

                ##### begin #####
                $connection->beginTransaction();
                # tạo phiếu chi cho khách hàng
                $count_debt = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->countItem(array('ssFilter' => array('filter_customer_id' => $customer_id)), array('task' => 'list-item'));
                if ($count_debt > 0) {
                    $list_debt = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->listItem(array('ssFilter' => array('filter_customer_id' => $customer_id)), array('task' => 'list-item', 'paginator' => false));
                    $list_debt = $list_debt->toArray();
                    $ucdebt = $list_debt[0];
                    $old_debt = $ucdebt['new_debt'];
                } else {
                    $old_debt = $contact_item['amount_owed'];
                }

                $paid_cash = $number->formatToData($this->_params['data']['paid_cash']);
                $paid_transfer = $number->formatToData($this->_params['data']['paid_transfer']);
                $new_debt = $old_debt + ($paid_cash + $paid_transfer);
                $data_debt = array(
                    'customer_id' => $customer_id,
                    'type' => CHI,
                    'inventory_id' => $this->_params['data']['inventory_id'],
                    'price_total' => 0,
                    'discount' => 0,
                    'paid_cash' => -$paid_cash,
                    'paid_transfer' => -$paid_transfer,
                    'old_debt' => $old_debt,
                    'new_debt' => $new_debt,
                    'state' => NEW_STATUS,
                    'category' => $this->_params['data']['category'],
                    'note' => $this->_params['data']['note'],
                    'date' => $this->_params['data']['date'],
                );
                $result = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->saveItem(array('data' => $data_debt), array('task' => 'add-item'));


                $connection->commit();


                $this->flashMessenger()->addSuccessMessage('Thêm mới ' . $this->caption . ' thành công');

                if ($controlAction == 'save-new') {
                    $this->goRoute(array('action' => 'add-expense'));
                } else if ($controlAction == 'save') {
                    $this->goRoute(array('action' => 'detail-expense', 'id' => $result));
                } else {
                    $this->goRoute();
                }
            }
        }

        $this->_viewModel['myForm'] = $myForm;
        $this->_viewModel['caption'] = 'Thêm mới - Phiếu Chi: ';
        return new ViewModel($this->_viewModel);
    }

    public function detailExpenseAction()
    {
        $id = $this->params('id');
        if ($id) {
            $connection = $this->getConnection();
            $item = $this->getTable()->getItem(array('id' => $id), array('task' => 'type-id'));
            if (empty($item)) {
                return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
            } else {
                if (!in_array($item['type'], [THU, CHI])) {
                    return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
                }
            }
        } else {
            return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
        }
        if ($this->getRequest()->isPost()) {
            $control_action = $this->_params['data']['control-action'];
            if (!in_array($item['type'], [THU, CHI])) {
                return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
            }
            if (in_array($item['state'], array(COMPLETE_STATUS, CANCEL_STATUS))) {
                $state_text = $item['state'] == CANCEL_STATUS ? 'HỦY' : 'HOÀN THÀNH';
                $this->flashMessenger()->addErrorMessage('Phiếu chi đã ở trạng thái "' . $state_text . '" không thể cập nhật dữ liệu!');
            } else {
                if ($control_action == CANCEL_STATUS) {
                    ##### begin #####
                    $connection->beginTransaction();

                    # Sửa phiếu thu chi khách hàng
                    $debt_item_old = $item;
                    $data_debt = array(
                        'id' => $debt_item_old->id,
                        'price_total' => 0,
                        'discount' => 0,
                        'paid_cash' => 0,
                        'paid_transfer' => 0,
                        'new_debt' => $debt_item_old->old_debt,
                        'state' => CANCEL_STATUS,
                    );
                    $this->getTable()->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                    $connection->commit();
                    $this->flashMessenger()->addSuccessMessage('Hủy phiếu chi thành công!');
                }
                if ($control_action == COMPLETE_STATUS) {
                    ##### begin #####
                    $connection->beginTransaction();
                    # Sửa phiếu thu chi khách hàng
                    $debt_item_old = $item;
                    $data_debt = array(
                        'id' => $debt_item_old->id,
                        'state' => COMPLETE_STATUS,
                    );
                    $this->getTable()->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                    $connection->commit();
                    $this->flashMessenger()->addSuccessMessage('Phiếu chi đã được hoàn thành!');
                }
                if ($control_action == COMPLETE_STATUS.'NotFund') {
                    ##### begin #####
                    $connection->beginTransaction();
                    # Sửa phiếu thu chi khách hàng
                    $debt_item_old = $item;
                    $data_debt = array(
                        'id' => $debt_item_old->id,
                        'state' => COMPLETE_STATUS,
                        'accept' => 2,
                    );
                    $this->getTable()->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                    $connection->commit();
                    $this->flashMessenger()->addSuccessMessage('Phiếu chi đã được hoàn thành!');
                }

                $item = $this->getTable()->getItem(array('id' => $id));
            }
        }

        $this->_viewModel['item'] = $item;
        $this->_viewModel['contact'] = $this->getServiceLocator()->get('Admin\Model\ContactTable')->getItem(array('id' => $item['contact_id']));
        $this->_viewModel['user'] = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['customer_type'] = $this->getServiceLocator()->get('Admin\Model\CustomerTypeTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['warehouse'] = $this->getServiceLocator()->get('Admin\Model\WarehouseTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['sale_group'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'lists-group')), array('task' => 'cache'));
        $this->_viewModel['sale_branch'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['order_status'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'orders-state')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['caption'] = 'Chi tiết phiếu chi - ' . $item['code'];
        $viewModel = new ViewModel($this->_viewModel);
        return $viewModel;
    }

    public function deleteAction()
    {
        $item = $this->getTable()->getItem(array('id' => $this->_params['route']['id']));
        if(empty($item)) {
            return $this->redirect()->toRoute('routeAdmin/type', array('controller' => 'notice', 'action' => 'not-found', 'type' => 'modal'));
        }
        $connection = $this->getConnection();
        if($this->getRequest()->isPost()){
            $types = array(THU => 'Thu', CHI => 'Chi');
            if($item['accept'] == 0 and in_array($item['type'], [THU, CHI])) {
                $connection->beginTransaction();

                # Sửa phiếu thu chi khách hàng
                $debt_item_old = $item;
                $data_debt = array(
                    'id' => $debt_item_old->id,
                    'price_total' => 0,
                    'discount' => 0,
                    'paid_cash' => 0,
                    'paid_transfer' => 0,
                    'new_debt' => $debt_item_old->old_debt,
                    'state' => CANCEL_STATUS,
                );
                $this->getTable()->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                $connection->commit();
                $this->flashMessenger()->addSuccessMessage('Xóa phiếu '.$types[$item['type']] .' '. $item['code'].' thành công!');
                $this->goRoute();
            }
            else{
                $this->flashMessenger()->addErrorMessage('Phiếu '. $types[$item['type']] .' '. $item['code'] . ' đã vào sổ quỹ không thể xóa!');
                return $this->redirect()->toRoute('routeAdmin/default', array('controller' => $this->_params['controller'], 'action' => 'index'));
            }
        }
        $this->_viewModel['item']    = $item;
        $this->_viewModel['caption'] = 'Thui chi khách hàng - Xóa';
        $viewModel = new ViewModel($this->_viewModel);
        return $viewModel;
    }

    public function exportAction()
    {
        $dateFormat = new \ZendX\Functions\Date();
        $file_name = 'thu_chi_khach_hang_ ' . date('Y_m_d') . '.xlsx';
        $items = $this->getTable()->listItem($this->_params, array('task' => 'list-item', 'paginator' => false));
        $debt_type = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-type')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $debt_category = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-category')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));

        require_once PATH_VENDOR . '/Excel/PHPExcel.php';

        $config = array('sheetData' => 0, 'headRow' => 1, 'startRow' => 2, 'startColumn' => 0);
        $arrColumn = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ');

        $arrData = array(
            array('field' => 'code', 'title' => 'Mã phiếu'),
            array('field' => 'state', 'title' => 'Trạng thái'),
            array('field' => 'customer_name', 'title' => 'Tên khách hàng'),
            array('field' => 'type', 'title' => 'Loại phiếu', 'type' => 'data_source', 'data_source' => $debt_type),
            array('field' => 'category', 'title' => 'Danh mục thu chi', 'type' => 'data_source', 'data_source' => $debt_category),
            array('field' => 'price_total', 'type' => 'abs', 'title' => 'Tổng tiền hàng'),
            array('field' => 'discount', 'type' => 'abs', 'title' => 'Giảm giá'),
            array('field' => 'paid_cash', 'type' => 'abs', 'title' => 'Tiền mặt'),
            array('field' => 'paid_transfer', 'type' => 'abs', 'title' => 'Chuyển khoản'),
            array('field' => 'old_debt', 'title' => 'Nợ cũ'),
            array('field' => 'new_debt', 'title' => 'Nợ lại'),
            array('field' => 'created', 'type' => 'datetime', 'title' => 'Ngày tạo')
        );

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator($this->_userInfo->getUserInfo('name'))->setTitle("Export");

        // Dữ liệu tiêu đề
        $startColumn = $config['startColumn'];
        foreach ($arrData as $key => $data) {
            $colLetter = $arrColumn[$startColumn];
            $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue($colLetter . $config['headRow'], $data['title']);
            $objPHPExcel->getActiveSheet()->getStyle($colLetter . $config['headRow'])->getFont()->setBold(true);
            $startColumn++;
        }

        // Dữ liệu data
        $startRow = $config['startRow'];
        foreach ($items as $item) {
            $startColumn = $config['startColumn'];
            foreach ($arrData as $key => $data) {
                $colLetter = $arrColumn[$startColumn];
                switch ($data['type']) {
                    case 'date':
                        $formatDate = $data['format'] ? $data['format'] : 'd/m/Y';
                        $value = $dateFormat->formatToView($item[$data['field']], $formatDate);
                        break;
                    case 'datetime':
                        $formatDate = $data['format'] ? $data['format'] : 'd/m/Y H:i:s';
                        $value = $dateFormat->formatToView($item[$data['field']], $formatDate);
                        break;
                    case 'abs':
                        $value = abs($item[$data['field']]);
                        break;
                    case 'data_source':
                        $field = $data['data_source_field'] ? $data['data_source_field'] : 'name';
                        $value = $data['data_source'][$item[$data['field']]][$field];
                        break;
                    default:
                        $value = $item[$data['field']];
                }

                $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue($colLetter . $startRow, $value);
                $startColumn++;
            }
            $startRow++;
        }

        $lastColumnIndex = $config['startColumn'] + count($arrData) - 1;
        for ($i = $config['startColumn']; $i <= $lastColumnIndex; $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($arrColumn[$i])->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function exportv1Action()
    {
        $dateFormat = new \ZendX\Functions\Date();
        $file_name = 'thu_chi_khach_hang_ ' . date('Y_m_d') . '.xlsx';
        $items = $this->getTable()->listItem($this->_params, array('task' => 'list-export', 'paginator' => false));
        $debt_type = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-type')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $debt_category = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-category')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $units = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'unit')), array('task' => 'cache')), array('key' => 'id', 'value' => 'object'));
        $products = $this->getServiceLocator()->get('Admin\Model\ProductsTable')->listItem(null, array('task' => 'cache'));

        require_once PATH_VENDOR . '/Excel/PHPExcel.php';

        $config = array('sheetData' => 0, 'headRow' => 10, 'startRow' => 11, 'startColumn' => 0);
        $arrColumn = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ');

        $arrData = array(
            array('field' => 'created', 'type' => 'datetime', 'title' => 'Thời gian'),
            array('field' => 'code', 'title' => 'Mã'),
            array('field' => 'name', 'title' => 'Diễn Giải'),
            array('field' => 'unit', 'title' => 'ĐVT'),
            array('field' => 'quantity', 'title' => 'SL'),
            array('field' => 'price', 'title' => 'Đơn giá'),
            array('field' => 'discount', 'title' => 'Giảm giá'),
            array('field' => 'vat', 'title' => 'VAT'),
            array('field' => 'price', 'title' => 'Giá bán/trả'),
            array('field' => 'total', 'title' => 'Thành Tiền'),
            array('field' => 'debt', 'title' => 'Ghi nợ'),
            array('field' => 'debt2', 'title' => 'Ghi có'),
        );

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator($this->_userInfo->getUserInfo('name'))->setTitle("Export");

        // Dữ liệu tiêu đề
        $startColumn = $config['startColumn'];
        foreach ($arrData as $key => $data) {
            $colLetter = $arrColumn[$startColumn];
            $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue($colLetter . $config['headRow'], $data['title']);
            $objPHPExcel->getActiveSheet()->getStyle($colLetter . $config['headRow'])->getFont()->setBold(true);
            $startColumn++;
        }

        // Dữ liệu data
        $startRow = $config['startRow'];
        $id = '';
        foreach ($items as $item) {

            if ($item['id'] != $id) {
                $id = $item['id'];

                if ($item['type'] == KMH) {
                    $item['code'] = $item['orders_code'];
                    $item['name'] = 'Bán hàng';
                    $item['debt'] = $item['price_total'];
                }
                if ($item['type'] == KTH) {
                    $item['code'] = $item['orders_return_code'];
                    $item['name'] = 'Trả hàng';
                    $item['debt'] = $item['price_total'];
                }

                $startColumn = $config['startColumn'];

                foreach ($arrData as $key => $data) {
                    $colLetter = $arrColumn[$startColumn];
                    $value = $item[$data['field']];
                    $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue($colLetter . $startRow, $value)
                        ->getStyle($colLetter . $startRow)->getFont()->setBold(true);;
                    $startColumn++;
                }
                $startRow++;

                if ($item['type'] == KMH) {
                    $item['created'] = '';
                    $item['code'] = $products[$item['cdetail_product_id']]['code'];
                    $item['name'] = $products[$item['cdetail_product_id']]['name'];
                    $item['unit'] = $units[$products[$item['cdetail_product_id']]['unit_id']]['name'];
                    $item['quantity'] = $item['cdetail_quantity'];
                    $item['price'] = $item['cdetail_price'];
                    $item['discount'] = 0;
                    $item['vat'] = 0;
                    $item['price'] = $item['cdetail_price'];
                    $item['total'] = $item['cdetail_price_total'];
                    $item['debt'] = '';
                    $item['debt2'] = '';
                }
                if ($item['type'] == KTH) {
                    $item['created'] = '';
                    $item['code'] = $products[$item['odetail_product_id']]['code'];
                    $item['name'] = $products[$item['odetail_product_id']]['name'];
                    $item['unit'] = $units[$products[$item['cdetail_product_id']]['unit_id']]['name'];
                    $item['quantity'] = $item['odetail_quantity'];
                    $item['price'] = $item['odetail_price'];
                    $item['discount'] = 0;
                    $item['vat'] = 0;
                    $item['price'] = $item['odetail_price'];
                    $item['total'] = $item['odetail_price_total'];
                    $item['debt'] = '';
                    $item['debt2'] = '';
                }

            }
            else {
                if ($item['type'] == KMH) {
                    $item['created'] = '';
                    $item['code'] = $products[$item['cdetail_product_id']]['code'];
                    $item['name'] = $products[$item['cdetail_product_id']]['name'];
                    $item['unit'] = $units[$products[$item['cdetail_product_id']]['unit_id']]['name'];
                    $item['quantity'] = $item['cdetail_quantity'];
                    $item['price'] = $item['cdetail_price'];
                    $item['discount'] = 0;
                    $item['vat'] = 0;
                    $item['price'] = $item['cdetail_price'];
                    $item['total'] = $item['cdetail_price_total'];
                    $item['debt'] = '';
                    $item['debt2'] = '';
                }
                if ($item['type'] == KTH) {
                    $item['created'] = '';
                    $item['code'] = $products[$item['odetail_product_id']]['code'];
                    $item['name'] = $products[$item['odetail_product_id']]['name'];
                    $item['unit'] = $units[$products[$item['cdetail_product_id']]['unit_id']]['name'];
                    $item['quantity'] = $item['odetail_quantity'];
                    $item['price'] = $item['odetail_price'];
                    $item['discount'] = 0;
                    $item['vat'] = 0;
                    $item['price'] = $item['odetail_price'];
                    $item['total'] = $item['odetail_price_total'];
                    $item['debt'] = '';
                    $item['debt2'] = '';
                }
            }

            $startColumn = $config['startColumn'];

            foreach ($arrData as $key => $data) {
                $colLetter = $arrColumn[$startColumn];
                switch ($data['type']) {
                    case 'date':
                        $formatDate = $data['format'] ? $data['format'] : 'd/m/Y';
                        $value = $dateFormat->formatToView($item[$data['field']], $formatDate);
                        break;
                    case 'datetime':
                        $formatDate = $data['format'] ? $data['format'] : 'd/m/Y H:i:s';
                        $value = $dateFormat->formatToView($item[$data['field']], $formatDate);
                        break;
                    case 'abs':
                        $value = abs($item[$data['field']]);
                        break;
                    case 'data_source':
                        $field = $data['data_source_field'] ? $data['data_source_field'] : 'name';
                        $value = $data['data_source'][$item[$data['field']]][$field];
                        break;
                    default:
                        $value = $item[$data['field']];
                }

                $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue($colLetter . $startRow, $value);
                $startColumn++;
            }
            $startRow++;
        }

        $lastColumnIndex = $config['startColumn'] + count($arrData) - 1;
        for ($i = $config['startColumn']; $i <= $lastColumnIndex; $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($arrColumn[$i])->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function exportV2Action()
    {
        $dateFormat = new \ZendX\Functions\Date();
        $file_name = 'thu_chi_khach_hang_ ' . date('Y_m_d') . '.xlsx';
        $items = $this->getTable()->listItem($this->_params, array('task' => 'list-export', 'paginator' => false));
        $debt_type = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-type')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $debt_category = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-category')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $units = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'unit')), array('task' => 'cache')), array('key' => 'id', 'value' => 'object'));
        $products = $this->getServiceLocator()->get('Admin\Model\ProductsTable')->listItem(null, array('task' => 'cache'));
        $customer = $this->getServiceLocator()->get('Admin\Model\ContactTable')->getItem(array('id' => $this->_params['ssFilter']['filter_customer_id']));
        $inventory = $this->getServiceLocator()->get('Admin\Model\WarehouseTable')->getItem(array('id' => $this->_params['ssFilter']['filter_inventory_id']));
//        echo "<pre>";
//        print_r($this->_params['ssFilter']);
//        print_r($inventory);
//        echo "</pre>";
//        exit;

        require_once PATH_VENDOR . '/Excel/PHPExcel.php';

        $config = array('sheetData' => 0, 'headRow' => 10, 'startRow' => 11, 'startColumn' => 0);
        $arrColumn = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ');

        // Thêm thuộc tính 'format' => 'number' vào các cột cần định dạng tiền/số
        $arrData = array(
            array('field' => 'created', 'type' => 'datetime', 'title' => 'Thời gian'),
            array('field' => 'code', 'title' => 'Mã'),
            array('field' => 'name', 'title' => 'Diễn Giải'),
            array('field' => 'unit', 'title' => 'ĐVT'),
            array('field' => 'quantity', 'format' => 'number', 'title' => 'SL'),
            array('field' => 'price', 'format' => 'number', 'title' => 'Đơn giá'),
            array('field' => 'discount', 'format' => 'number', 'title' => 'Giảm giá'),
            array('field' => 'vat', 'format' => 'number', 'title' => 'VAT'),
            array('field' => 'price', 'format' => 'number', 'title' => 'Giá bán/trả'),
            array('field' => 'total', 'format' => 'number', 'title' => 'Thành Tiền'),
            array('field' => 'debt', 'format' => 'number', 'title' => 'Ghi nợ'),
            array('field' => 'debt2', 'format' => 'number', 'title' => 'Ghi có'),
        );

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator($this->_userInfo->getUserInfo('name'))->setTitle("Export");

        // Định nghĩa cấu hình đường viền mỏng (Thin Border) cho PHP 5.6
        $styleBorder = array(
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            )
        );

        // Dữ liệu tiêu đề
        $startColumn = $config['startColumn'];
        foreach ($arrData as $key => $data) {
            $colLetter = $arrColumn[$startColumn];
            $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue($colLetter . $config['headRow'], $data['title']);
            $objPHPExcel->getActiveSheet()->getStyle($colLetter . $config['headRow'])->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle($colLetter . $config['headRow'])->applyFromArray($styleBorder); // Thêm border tiêu đề
            $startColumn++;
        }

        // Dữ liệu data
        $startRow = $config['startRow'];
        $id = '';
        $old_debt = NULL;
        $run_debt = 0;
        foreach ($items as $item) {

            if ($old_debt == NULL) {
                $old_debt = $item['old_debt'];
            }
            $vat_value = $item['option_vat'] == 'yes' ? 1.08 : 1;
            if ($item['id'] != $id) {
                $run_debt += ($item['price_total'] + $item['discount'] + $item['paid_cash'] + $item['paid_transfer']);
                $id = $item['id'];

                # thêm sản phẩm đầu tiên
                if ($item['type'] == KMH) {
                    $item['code'] = $item['orders_code'];
                    $item['name'] = 'Bán hàng';
                    $item['debt'] = abs($item['price_total'] + $item['discount']);
                    $item['debt2'] = abs($item['paid_cash'] + $item['paid_transfer']);
                }
                if ($item['type'] == KTH) {
                    $item['code'] = $item['orders_return_code'];
                    $item['name'] = 'Trả hàng';
                    $item['debt'] = abs($item['paid_cash'] + $item['paid_transfer']);
                    $item['debt2'] = abs($item['price_total'] + $item['discount']);
                }
                if ($item['type'] == THU) {
                    $item['name'] = 'Phiếu Thu';
                    $item['debt2'] = abs($item['paid_cash'] + $item['paid_transfer']);
                }
                if ($item['type'] == CHI) {
                    $item['name'] = 'Phiếu Chi';
                    $item['debt'] = abs($item['paid_cash'] + $item['paid_transfer']);
                }

                $startColumn = $config['startColumn'];

                foreach ($arrData as $key => $data) {
                    $colLetter = $arrColumn[$startColumn];
                    $value = $item[$data['field']];
                    $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue($colLetter . $startRow, $value)
                        ->getStyle($colLetter . $startRow)->getFont()->setBold(true);

                    // Thêm Border cho dòng Header của item
                    $objPHPExcel->getActiveSheet()->getStyle($colLetter . $startRow)->applyFromArray($styleBorder);

                    // Định dạng số cho dòng Header của item nếu có giá trị số
                    if (isset($data['format']) && $data['format'] == 'number' && $value !== '' && $value !== null && is_numeric($value)) {
                        $objPHPExcel->getActiveSheet()->getStyle($colLetter . $startRow)->getNumberFormat()->setFormatCode('#,##0');
                    }

                    $startColumn++;
                }
                $startRow++;


                # thêm thu khác nếu có
                if ($item['fee_other'] > 0) {

                    if ($item['type'] == KMH) {
                        $item['code'] = "Thu khác";
                        $item['total'] = $item['fee_other'];
                        $item['name'] = '';
                        $item['debt'] = '';
                        $item['debt2'] = '';
                        $item['created'] = '';
                    }

                    $startColumn = $config['startColumn'];

                    foreach ($arrData as $key => $data) {
                        $colLetter = $arrColumn[$startColumn];
                        $value = $item[$data['field']];
                        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue($colLetter . $startRow, $value);

                        // Thêm Border cho dòng Header của item
                        $objPHPExcel->getActiveSheet()->getStyle($colLetter . $startRow)->applyFromArray($styleBorder);

                        // Định dạng số cho dòng Header của item nếu có giá trị số
                        if (isset($data['format']) && $data['format'] == 'number' && $value !== '' && $value !== null && is_numeric($value)) {
                            $objPHPExcel->getActiveSheet()->getStyle($colLetter . $startRow)->getNumberFormat()->setFormatCode('#,##0');
                        }

                        $startColumn++;
                    }
                    $startRow++;
                }

                if ($item['type'] == KMH) {
                    $product_price  = round($item['cdetail_price']/$vat_value);
                    $vat  = ($item['cdetail_price'] - $product_price);

                    $item['created'] = '';
                    $item['code'] = $products[$item['cdetail_product_id']]['code'];
                    $item['name'] = $products[$item['cdetail_product_id']]['name'];
                    $item['unit'] = $units[$products[$item['cdetail_product_id']]['unit_id']]['name'];
                    $item['quantity'] = $item['cdetail_quantity'];
                    $item['price'] = $product_price;
                    $item['discount'] = 0;
                    $item['vat'] = $vat;
                    $item['price'] = $product_price;
                    $item['total'] = $item['cdetail_price_total'];
                    $item['debt'] = '';
                    $item['debt2'] = '';
                }
                if ($item['type'] == KTH) {
                    $item['created'] = '';
                    $item['code'] = $products[$item['odetail_product_id']]['code'];
                    $item['name'] = $products[$item['odetail_product_id']]['name'];
                    $item['unit'] = $units[$products[$item['cdetail_product_id']]['unit_id']]['name'];
                    $item['quantity'] = $item['odetail_quantity'];
                    $item['price'] = $item['odetail_price'];
                    $item['discount'] = 0;
                    $item['vat'] = 0;
                    $item['price'] = $item['odetail_price'];
                    $item['total'] = $item['odetail_price_total'];
                    $item['debt'] = '';
                    $item['debt2'] = '';
                }
                if ($item['type'] == THU || $item['type'] == CHI){
                    continue;
                }

            }
            else {
                if ($item['type'] == KMH) {
                    $product_price  = round($item['cdetail_price']/$vat_value);
                    $vat  = ($item['cdetail_price'] - $product_price);

                    $item['created'] = '';
                    $item['code'] = $products[$item['cdetail_product_id']]['code'];
                    $item['name'] = $products[$item['cdetail_product_id']]['name'];
                    $item['unit'] = $units[$products[$item['cdetail_product_id']]['unit_id']]['name'];
                    $item['quantity'] = $item['cdetail_quantity'];
                    $item['price'] = $product_price;
                    $item['discount'] = 0;
                    $item['vat'] = $vat;
                    $item['price'] = $product_price;
                    $item['total'] = $item['cdetail_price_total'];
                    $item['debt'] = '';
                    $item['debt2'] = '';
                }
                if ($item['type'] == KTH) {
                    $item['created'] = '';
                    $item['code'] = $products[$item['odetail_product_id']]['code'];
                    $item['name'] = $products[$item['odetail_product_id']]['name'];
                    $item['unit'] = $units[$products[$item['cdetail_product_id']]['unit_id']]['name'];
                    $item['quantity'] = $item['odetail_quantity'];
                    $item['price'] = $item['odetail_price'];
                    $item['discount'] = 0;
                    $item['vat'] = 0;
                    $item['price'] = $item['odetail_price'];
                    $item['total'] = $item['odetail_price_total'];
                    $item['debt'] = '';
                    $item['debt2'] = '';
                }
            }

            $startColumn = $config['startColumn'];

            foreach ($arrData as $key => $data) {
                $colLetter = $arrColumn[$startColumn];
                switch ($data['type']) {
                    case 'date':
                        $formatDate = $data['format'] ? $data['format'] : 'd/m/Y';
                        $value = $dateFormat->formatToView($item[$data['field']], $formatDate);
                        break;
                    case 'datetime':
                        $formatDate = $data['format'] ? $data['format'] : 'd/m/Y H:i:s';
                        $value = $dateFormat->formatToView($item[$data['field']], $formatDate);
                        break;
                    case 'abs':
                        $value = abs($item[$data['field']]);
                        break;
                    case 'data_source':
                        $field = $data['data_source_field'] ? $data['data_source_field'] : 'name';
                        $value = $data['data_source'][$item[$data['field']]][$field];
                        break;
                    default:
                        $value = $item[$data['field']];
                }

                $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue($colLetter . $startRow, $value);

                // Thêm Border cho các ô dữ liệu chi tiết
                $objPHPExcel->getActiveSheet()->getStyle($colLetter . $startRow)->applyFromArray($styleBorder);

                // Định dạng số (1,000,000) cho các ô dữ liệu chi tiết nếu có giá trị số hợp lệ
                if (isset($data['format']) && $data['format'] == 'number' && $value !== '' && $value !== null && is_numeric($value)) {
                    $objPHPExcel->getActiveSheet()->getStyle($colLetter . $startRow)->getNumberFormat()->setFormatCode('#,##0');
                }

                $startColumn++;
            }
            $startRow++;
        }

        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('A1', 'Pako việt Nam')->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('A2', 'Chi nhánh');
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('B2', $inventory ? $inventory['name'] : '');
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('A3', 'Địa chỉ');
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('B3', $inventory ? $inventory['address'] : '');
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('A4', 'Điện thoại');
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValueExplicit('B4',$inventory ? $inventory['phone'] : '',\PHPExcel_Cell_DataType::TYPE_STRING);

//        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('A5', 'Công nợ chi tiết khách hàng Từ ngày 01/05/2026 đến ngày 31/05/2026');
        $titleText = "Công nợ chi tiết khách hàng \nTừ ngày {$this->_params['ssFilter']['filter_date_begin']} đến ngày {$this->_params['ssFilter']['filter_date_end']}";
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('A5', $titleText)->getStyle('A5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->mergeCells('A5:L5');
        $objPHPExcel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A5')->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getRowDimension(5)->setRowHeight(40);


        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('A6', 'Khách hàng');
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('B6', $customer ? $customer['name'] : '');
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('A7', 'Mã KH');
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('B7', $customer ? $customer['name'] : '');
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('A8', 'Điện thoại');
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValueExplicit('B8',$customer ? $customer['phone'] : '',\PHPExcel_Cell_DataType::TYPE_STRING);

        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('J6', 'Nợ đầu kỳ');
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('K6', number_format($old_debt))->getStyle('K6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('J7', 'Phát sinh trong kỳ');
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('K7', number_format(($run_debt)))->getStyle('K7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('J8', 'Nợ cuối kỳ')->getStyle('J8')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue('K8', number_format($old_debt - ($run_debt)));

        $objPHPExcel->getActiveSheet()->getStyle('K8')->applyFromArray(array(
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT),
            'font'      => array('color' => array('rgb' => 'FF0000')),
            'fill'      => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFFF00'))
        ));

        $lastColumnIndex = $config['startColumn'] + count($arrData) - 1;
        for ($i = $config['startColumn']; $i <= $lastColumnIndex; $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($arrColumn[$i])->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    // Vào sổ thanh toán
    public function acceptAction()
    {
        $date     = new \ZendX\Functions\Date();
        if (!empty($this->_params['data']['id'])) {
            $item = $this->getTable()->getItem(array('id' => $this->_params['data']['id']), array('task' => 'type-id'));
            if ($item['accept'] == 1) {
                return $this->redirect()->toRoute('routeAdmin/type', array('controller' => 'notice', 'action' => 'not-found', 'type' => 'modal'));
            }
            $debt_type = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-type')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name'));
            $contact = $this->getServiceLocator()->get('Admin\Model\ContactTable')->getItem(array('id' => $item['customer_id']));
        } else {
            return $this->redirect()->toRoute('routeAdmin/type', array('controller' => 'notice', 'action' => 'not-found', 'type' => 'modal'));
        }
        if ($this->getRequest()->isPost()) {
            $myForm = new \Admin\Form\CustomerDebt\Accept($this->getServiceLocator());
            $this->_params['data']['paid_cash'] = abs($item['paid_cash']);
            $this->_params['data']['paid_transfer'] = abs($item['paid_transfer']);
            $myForm->setInputFilter(new \Admin\Filter\CustomerDebt\Accept($this->_params['data']));
            $arrData = array(
                'id' => $item['id'],
                'content' => $debt_type[$item['type']],
                'note' => $item['note'],
                'date' => $date->formatToView($item['date']),
            );

            $myForm->setData($arrData);

            $connection = $this->getConnection();

            if ($this->_params['data']['modal'] == 'success') {
                $myForm->setData($this->_params['data']);
                if ($myForm->isValid()) {

                    $this->_params['data'] = $myForm->getData(FormInterface::VALUES_AS_ARRAY);
                    $params = $this->_params['data'];

                    $this->_params['item'] = $item;
                    $transaction_type = '';
                    if (in_array($item['type'], [KMH,PTH,THU])) {
                        $transaction_type = 'thu';
                    }
                    if (in_array($item['type'], [KTH,PNH,CHI])) {
                        $transaction_type = 'chi';
                    }

                    ##### begin #####
                    $connection->beginTransaction();

                    if (abs($item['paid_cash']) > 0) {
                        $data = array(
                            'date' => $params['date'],
                            'accountant_funds_id' => $params['accountant_funds_id_cash'],
                            'transaction_category_id' => 'giao-dich',
                            'transaction_type_id' => $transaction_type,
                            'transaction_form_id' => 'tien-mat',
                            'category_id' => $params['category_id'],
                            'content' => $params['content'],
                            'submitter_name' => $item['customer_name'],
                            'submitter_phone' => $item['customer_phone'],
                            'paid' => $transaction_type == 'thu' ? abs($item['paid_cash']) : 0,
                            'accrued' => $transaction_type == 'chi' ? abs($item['paid_cash']) : 0,
                            'note' => $params['note'],
                            'customer_debt_id' => $item['id'] ? $item['id'] : null,
                            'inventory_id' => $item['inventory_id'] ? $item['inventory_id'] : null,
                        );
                        $this->_params['data'] = $data;

                        $this->getServiceLocator()->get('Admin\Model\AccountantBillTable')->saveItem($this->_params, array('task' => 'add-item'));
                    }
                    if (abs($item['paid_transfer']) > 0) {
                        $data = array(
                            'date' => $params['date'],
                            'accountant_funds_id' => $params['accountant_funds_id_transfer'],
                            'transaction_category_id' => 'giao-dich',
                            'transaction_type_id' => $transaction_type,
                            'transaction_form_id' => 'chuyen-khoan',
                            'category_id' => $params['category_id'],
                            'content' => $params['content'],
                            'submitter_name' => $item['customer_name'],
                            'submitter_phone' => $item['customer_phone'],
                            'paid' => $transaction_type == 'thu' ? abs($item['paid_transfer']) : 0,
                            'accrued' => $transaction_type == 'chi' ? abs($item['paid_transfer']) : 0,
                            'note' => $params['note'],
                            'customer_debt_id' => $item['id'] ? $item['id'] : null,
                            'inventory_id' => $item['inventory_id'] ? $item['inventory_id'] : null,
                        );
                        $this->_params['data'] = $data;

                        $this->getServiceLocator()->get('Admin\Model\AccountantBillTable')->saveItem($this->_params, array('task' => 'add-item'));
                    }

                    $this->getTable()->saveItem(array('data' => array('id' => $item['id'], 'accept' => 1)), array('task' => 'update-item'));


                    $connection->commit();

                    // Vào sổ tài khoản thanh toán

                    $this->flashMessenger()->addMessage('Vào sổ tài khoản thành công');
                    echo 'success';
                    return $this->response;
                }
            }
        } else {
            return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found', 'type' => 'modal'));
        }

        $this->_viewModel['myForm'] = $myForm;
        $this->_viewModel['item'] = $item;
        $this->_viewModel['debt_type'] = $debt_type;
        $this->_viewModel['contact'] = $contact;
        $this->_viewModel['bill_type'] = array('paid' => 'Thu', 'accrued' => 'Chi', 'surcharge' => 'Phụ phí');
        $this->_viewModel['paid_type'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array("table" => "document", "where" => array("code" => "bill-type-paid"), "order" => array("ordering" => "ASC", "created" => "ASC", "name" => "ASC"), "view" => array("key" => "id", "value" => "name", "sprintf" => "%s")), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['caption'] = 'Vào sổ tài khoản - thanh toán';

        $viewModel = new ViewModel($this->_viewModel);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    // Xác nhận hoàn thành nhưng không vào sổ quỹ
    public function acceptNotFundAction()
    {
        if ($this->getRequest()->isPost()) {
            if (!empty($this->_params['data']['cid'])) {
                $cid = $this->_params['data']['cid'];
                $count_update = 0;
                $arr_complete = [];
                foreach ($cid as $id) {
                    $item = $this->getTable()->getItem(array('id' => $id));
                    // Chỉ lấy ra những đơn hàng đang đã hoàn thành và chưa vào sổ quỹ
                    if ($item['accept'] == 0 && $item['state'] == COMPLETE_STATUS) {
                        $params['data']['id'] = $id;
                        $params['data']['accept'] = 2;
                        $this->getTable()->saveItem($params, array('task' => 'update-item'));
                        $count_update += 1;
                        $arr_complete[] = $item['code'];
                    }
                }
                $message = ' Đã xác nhận ' . $count_update . ' Phiếu thu chi: '.implode(',', $arr_complete).' không nhập vào sổ quỹ';
                $this->flashMessenger()->addSuccessMessage($message);
            }
        }
        $this->goRoute(array('action' => 'index'));
    }

}

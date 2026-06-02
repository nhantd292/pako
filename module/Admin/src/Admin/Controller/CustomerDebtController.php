<?php

namespace Admin\Controller;

use kcfinder\zipFolder;
use ZendX\Controller\ActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Form\FormInterface;
use ZendX\System\UserInfo;

class CustomerDebtController extends ActionController{
    public $caption = 'Thu chi khách hàng';
    public function init() {
        // Thiết lập options
        $this->_options['tableName'] = 'Admin\Model\CustomerDebtTable';
        $this->_options['formName'] = 'formAdminCustomerDebt';
        // Thiết lập session filter
        $action = !empty($this->getRequest()->getPost('filter_action')) ? str_replace('-', '_', $this->getRequest()->getPost('filter_action')) : 'index';
        $ssFilter = new Container(__CLASS__ . $action);

        $this->_params['ssFilter']['order_by']                  = !empty($ssFilter->order_by) ? $ssFilter->order_by : 'ordering';
        $this->_params['ssFilter']['order']                     = !empty($ssFilter->order) ? $ssFilter->order : 'DESC';
        $this->_params['ssFilter']['filter_status']             = $ssFilter->filter_status;
        $this->_params['ssFilter']['filter_keyword']            = $ssFilter->filter_keyword;
        $this->_params['ssFilter']['filter_date_begin']            = $ssFilter->filter_date_begin;
        $this->_params['ssFilter']['filter_date_end']            = $ssFilter->filter_date_end;
        $this->_params['ssFilter']['filter_state']            = $ssFilter->filter_state;
        $this->_params['ssFilter']['filter_type']            = $ssFilter->filter_type;
        $this->_params['ssFilter']['filter_category']            = $ssFilter->filter_category;
        $this->_params['ssFilter']['filter_inventory_id']            = $ssFilter->filter_inventory_id;
        $this->_params['ssFilter']['filter_customer_id']            = $ssFilter->filter_customer_id;

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
            $ssFilter->filter_status            = $data['filter_status'];
            $ssFilter->filter_keyword           = $data['filter_keyword'];
            $ssFilter->filter_date_begin           = $data['filter_date_begin'];
            $ssFilter->filter_date_end           = $data['filter_date_end'];
            $ssFilter->filter_state           = $data['filter_state'];
            $ssFilter->filter_type           = $data['filter_type'];
            $ssFilter->filter_category           = $data['filter_category'];
            $ssFilter->filter_inventory_id           = $data['filter_inventory_id'];
            $ssFilter->filter_customer_id           = $data['filter_customer_id'];
        }

        if (!empty($this->_params['route']['id'])) {
            $ssFilter->filter_product = $this->_params['route']['id'];
        }

        $this->goRoute(array('action' => $action));
    }

    public function indexAction() {
        $myForm    = new \Admin\Form\Search\CustomerDebt($this->getServiceLocator(), $this->_params['ssFilter']);
        $myForm->setData($this->_params['ssFilter']);
        // Danh sách data
        $items = $this->getTable()->listItem($this->_params, array('task' => 'list-item'));

        $this->_viewModel['myForm']             = $myForm;
        $this->_viewModel['items']              = $items;
        $this->_viewModel['model']              = $this->getTable();
        $this->_viewModel['count']              = $this->getTable()->countItem($this->_params, array('task' => 'list-item'));
        $this->_viewModel['order_status']           = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'orders-state')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));

        $this->_viewModel['user']               = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['branch']             = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['debt_category']      = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-category')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name'));
        $this->_viewModel['debt_type']          = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-type')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'name'));
        $this->_viewModel['caption']            = $this->caption;

        return new ViewModel($this->_viewModel);
    }

    public function addRevenueAction() {
        $myForm = new \Admin\Form\CustomerDebt($this, $this->_params);
        $number = new \ZendX\Functions\Number();
        $connection = $this->getConnection();

        if($this->getRequest()->isPost()){
            $myForm->setInputFilter(new \Admin\Filter\CustomerDebt());
            $myForm->setData($this->_params['data']);
            $controlAction = $this->_params['data']['control-action'];

            if($myForm->isValid()){
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
                }
                else{
                    $old_debt = $contact_item['amount_owed'];
                }

                $paid_cash      = $number->formatToData($this->_params['data']['paid_cash']);
                $paid_transfer  = $number->formatToData($this->_params['data']['paid_transfer']);
                $new_debt       = $old_debt - ($paid_cash + $paid_transfer);
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
                );
                $result = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->saveItem(array('data' => $data_debt), array('task' => 'add-item'));


                $connection->commit();


                $this->flashMessenger()->addSuccessMessage('Thêm mới '.$this->caption.' thành công');

                if($controlAction == 'save-new') {
                    $this->goRoute(array('action' => 'add-revenue'));
                } else if($controlAction == 'save') {
                    $this->goRoute(array('action' => 'detail-revenue', 'id' => $result));
                } else {
                    $this->goRoute();
                }
            }
        }

        $this->_viewModel['myForm']	    = $myForm;
        $this->_viewModel['caption']    = 'Thêm mới - Phiếu Thu: '.$this->caption;
        return new ViewModel($this->_viewModel);
    }

    public function detailRevenueAction() {
        $id = $this->params('id');
        if($id) {
            $connection = $this->getConnection();
            $item = $this->getTable()->getItem(array('id' => $id), array('task' => 'type-id'));
            if (empty($item)) {
                return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
            }
            else{
                if (!in_array($item['type'], [THU,CHI]) ) {
                    return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
                }
            }
        } else {
            return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
        }
        if($this->getRequest()->isPost()){
            $control_action = $this->_params['data']['control-action'];
            if (!in_array($item['type'], [THU,CHI]) ) {
                return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
            }
            if (in_array($item['state'], array(COMPLETE_STATUS, CANCEL_STATUS))) {
                $state_text = $item['state'] == CANCEL_STATUS ? 'HỦY' : 'HOÀN THÀNH';
                $this->flashMessenger()->addErrorMessage('Phiếu thu đã ở trạng thái "'.$state_text.'" không thể cập nhật dữ liệu!');
            }
            else{
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
                    $this->flashMessenger()->addSuccessMessage('Hủy đơn hàng thành công!');
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
                    $this->flashMessenger()->addSuccessMessage('Phiếu trả hàng đã được hoàn thành!');
                }

                $item = $this->getTable()->getItem(array('id' => $id));
            }
        }

        $this->_viewModel['item']                       = $item;
        $this->_viewModel['contact']                    = $this->getServiceLocator()->get('Admin\Model\ContactTable')->getItem(array('id' => $item['contact_id']));
        $this->_viewModel['user']                       = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['customer_type']              = $this->getServiceLocator()->get('Admin\Model\CustomerTypeTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['warehouse']                  = $this->getServiceLocator()->get('Admin\Model\WarehouseTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['sale_group']                 = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'lists-group')), array('task' => 'cache'));
        $this->_viewModel['sale_branch']                = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['order_status']               = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'orders-state')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['caption']                    = 'Chi tiết - '.$this->caption. ' - '. $item['code'];
        $viewModel = new ViewModel($this->_viewModel);
        return $viewModel;
    }

    public function addExpenseAction() {
        $this->_params['type'] = 'thu';
        $myForm = new \Admin\Form\CustomerDebt($this, $this->_params);
        $number = new \ZendX\Functions\Number();
        $connection = $this->getConnection();

        if($this->getRequest()->isPost()){
            $myForm->setInputFilter(new \Admin\Filter\CustomerDebt());
            $myForm->setData($this->_params['data']);
            $controlAction = $this->_params['data']['control-action'];

            if($myForm->isValid()){
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
                }
                else{
                    $old_debt = $contact_item['amount_owed'];
                }

                $paid_cash      = $number->formatToData($this->_params['data']['paid_cash']);
                $paid_transfer  = $number->formatToData($this->_params['data']['paid_transfer']);
                $new_debt       = $old_debt + ($paid_cash + $paid_transfer);
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
                );
                $result = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->saveItem(array('data' => $data_debt), array('task' => 'add-item'));


                $connection->commit();


                $this->flashMessenger()->addSuccessMessage('Thêm mới '.$this->caption.' thành công');

                if($controlAction == 'save-new') {
                    $this->goRoute(array('action' => 'add-expense'));
                } else if($controlAction == 'save') {
                    $this->goRoute(array('action' => 'detail-expense', 'id' => $result));
                } else {
                    $this->goRoute();
                }
            }
        }

        $this->_viewModel['myForm']	    = $myForm;
        $this->_viewModel['caption']    = 'Thêm mới - Phiếu Thu: '.$this->caption;
        return new ViewModel($this->_viewModel);
    }

    public function detailExpenseAction() {
        $id = $this->params('id');
        if($id) {
            $connection = $this->getConnection();
            $item = $this->getTable()->getItem(array('id' => $id), array('task' => 'type-id'));
            if (empty($item)) {
                return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
            }
            else{
                if (!in_array($item['type'], [THU,CHI]) ) {
                    return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
                }
            }
        } else {
            return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
        }
        if($this->getRequest()->isPost()){
            $control_action = $this->_params['data']['control-action'];
            if (!in_array($item['type'], [THU,CHI]) ) {
                return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
            }
            if (in_array($item['state'], array(COMPLETE_STATUS, CANCEL_STATUS))) {
                $state_text = $item['state'] == CANCEL_STATUS ? 'HỦY' : 'HOÀN THÀNH';
                $this->flashMessenger()->addErrorMessage('Phiếu chi đã ở trạng thái "'.$state_text.'" không thể cập nhật dữ liệu!');
            }
            else{
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
                    $this->flashMessenger()->addSuccessMessage('Hủy đơn hàng thành công!');
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
                    $this->flashMessenger()->addSuccessMessage('Phiếu trả hàng đã được hoàn thành!');
                }

                $item = $this->getTable()->getItem(array('id' => $id));
            }
        }

        $this->_viewModel['item']                       = $item;
        $this->_viewModel['contact']                    = $this->getServiceLocator()->get('Admin\Model\ContactTable')->getItem(array('id' => $item['contact_id']));
        $this->_viewModel['user']                       = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['customer_type']              = $this->getServiceLocator()->get('Admin\Model\CustomerTypeTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['warehouse']                  = $this->getServiceLocator()->get('Admin\Model\WarehouseTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['sale_group']                 = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'lists-group')), array('task' => 'cache'));
        $this->_viewModel['sale_branch']                = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['order_status']               = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'orders-state')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['caption']                    = 'Chi tiết - '.$this->caption. ' - '. $item['code'];
        $viewModel = new ViewModel($this->_viewModel);
        return $viewModel;
    }

    public function deleteAction() {
//        if($this->getRequest()->isPost()) {
//            if(!empty($this->_params['data']['cid'])) {
//                $cdata = $this->getTable()->deleteItem($this->_params, array('task' => 'delete-item'));
//                $message = 'Xóa '. $cdata .' '.$this->caption.' thành công';
//                $this->flashMessenger()->addSuccessMessage($message);
//            }
//        }
        $this->flashMessenger()->addErrorMessage('Không thể xóa thu chi khách hàng!');
        $this->goRoute(array('action' => 'index'));
    }

    public function exportAction() {

        $dateFormat = new \ZendX\Functions\Date();
        $file_name = 'thu_chi_khach_hang_ '.date('Y_m_d').'.xlsx';
        $items = $this->getTable()->listItem($this->_params, array('task' => 'list-item', 'paginator' => false));
        $debt_type      = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-type')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $debt_category  = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'debt-category')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));

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
        foreach ($arrData AS $key => $data) {
            $colLetter = $arrColumn[$startColumn];
            $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue($colLetter . $config['headRow'], $data['title']);
            $objPHPExcel->getActiveSheet()->getStyle($colLetter . $config['headRow'])->getFont()->setBold(true);
            $startColumn++;
        }

        // Dữ liệu data
        $startRow = $config['startRow'];
        foreach ($items AS $item) {
            $startColumn = $config['startColumn'];
            foreach ($arrData AS $key => $data) {
                $colLetter = $arrColumn[$startColumn];
                switch ($data['type']) {
                    case 'date':
                        $formatDate = $data['format'] ? $data['format'] : 'd/m/Y';
                        $value      = $dateFormat->formatToView($item[$data['field']], $formatDate);
                        break;
                    case 'datetime':
                        $formatDate = $data['format'] ? $data['format'] : 'd/m/Y H:i:s';
                        $value      = $dateFormat->formatToView($item[$data['field']], $formatDate);
                        break;
                    case 'abs':
                        $value      = abs($item[$data['field']]);
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
        header('Content-Disposition: attachment;filename="'.$file_name.'"');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    // Vào sổ thanh toán
    public function acceptAction() {
        if(!empty($this->_params['data']['id'])) {
            $item    = $this->getTable()->getItem(array('id' => $this->_params['data']['id']), array('task' => 'type-id'));
            $contact = $this->getServiceLocator()->get('Admin\Model\ContactTable')->getItem(array('id' => $item['customer_id']));
        } else {
            return $this->redirect()->toRoute('routeAdmin/type', array('controller' => 'notice', 'action' => 'not-found', 'type' => 'modal'));
        }
        if($this->getRequest()->isPost()){
            $dateFomart = new \ZendX\Functions\Date();
            $myForm  = new \Admin\Form\CustomerDebt\Accept($this->getServiceLocator());
            $myForm->setInputFilter(new \Admin\Filter\CustomerDebt\Accept($this->_params['data']));
            if (in_array($item['type'], [KMH,PTH,THU])) {
                $arrData = array(
                    'id'                        => $item['id'],
                    'date'                      => $dateFomart->formatToView($item['date']), // Ngày thu chi = Ngày thanh toán thực tế
                    'transaction_type_id'       => 'thu', // Nghiệp vụ
                    'content'                   => $item['content'], // Nội dung
                    'submitter_name'            => $item['customer_name'], // Người nộp
                    'submitter_phone'           => $item['customer_phone'], // Điện thoại
                    'transaction_category_id'   => 'giao-dich',
//                    'transaction_form_id'       => 'tien-mat',
                    'paid_cash'                 => abs($item['paid_cash']),
                    'paid_transfer'             => abs($item['paid_transfer']),
                    'accrued_cash'              => 0,
                    'accrued_transfer'          => 0,
                );
            }
            if (in_array($item['type'], [KTH,PNH,CHI])) {
                $arrData = array(
                    'id'                        => $item['id'],
                    'date'                      => $dateFomart->formatToView($item['date']), // Ngày thu chi = Ngày thanh toán thực tế
                    'transaction_type_id'       => 'chi', // Nghiệp vụ
                    'content'                   => $item['content'], // Nội dung
                    'submitter_name'            => $item['customer_name'], // Người nộp
                    'submitter_phone'           => $item['customer_phone'], // Điện thoại
                    'transaction_category_id'   => 'giao-dich',
//                    'transaction_form_id'       => 'tien-mat',
                    'paid_cash'                 => 0,
                    'paid_transfer'             => 0,
                    'accrued_cash'              => abs($item['paid_cash']),
                    'accrued_transfer'          => abs($item['paid_transfer']),
                );
            }

            $myForm->setData($arrData);

            if($this->_params['data']['modal'] == 'success') {
                $myForm->setData($this->_params['data']);
                if($myForm->isValid()){
                    $this->_params['data'] = $myForm->getData(FormInterface::VALUES_AS_ARRAY);
                    $this->_params['data']['customer_debt_id'] = $item['id'];
                    $this->_params['data']['inventory_id'] = $item['inventory_id'];

                    $this->_params['item'] = $item;
                    echo "<pre>";
                    print_r($this->_params['data']);
                    echo "</pre>";
                    exit;

                    // Vào sổ tài khoản thanh toán
                    $this->getServiceLocator()->get('Admin\Model\AccountantBillTable')->saveItem($this->_params, array('task' => 'add-item'));
                    $this->getTable()->saveItem(array('data' => array('id' => $item['id'], 'accept' => 1)), array('task' => 'update-item'));
                    $this->flashMessenger()->addMessage('Vào sổ tài khoản thành công');
                    echo 'success';
                    return $this->response;
                }
            }
        }
        else {
            return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found', 'type' => 'modal'));
        }

        $this->_viewModel['myForm']       = $myForm;
        $this->_viewModel['item']         = $item;
        $this->_viewModel['contact']      = $contact;
        $this->_viewModel['bill_type']    = array('paid' => 'Thu', 'accrued' => 'Chi', 'surcharge' => 'Phụ phí');
        $this->_viewModel['paid_type']    = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array("table" => "document", "where" => array("code" => "bill-type-paid"), "order" => array("ordering" => "ASC", "created" => "ASC", "name" => "ASC"), "view" => array("key" => "id", "value" => "name", "sprintf" => "%s")), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['caption']      = 'Vào sổ tài khoản - thanh toán';

        $viewModel = new ViewModel($this->_viewModel);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

}

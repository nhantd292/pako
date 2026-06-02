<?php

namespace Admin\Controller;

use ZendX\Controller\ActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Form\FormInterface;

class AccountantBillController extends ActionController {
    public function init() {
        // Thiết lập options
        $this->_options['tableName'] = 'Admin\Model\AccountantBillTable';
        $this->_options['formName']  = 'formAdminAccountantBill';
        
        // Thiết lập session filter
        $ssFilter = new Container(__CLASS__);
        $this->_params['ssFilter']['order_by']                      = !empty($ssFilter->order_by) ? $ssFilter->order_by : 'id';
        $this->_params['ssFilter']['order']                         = !empty($ssFilter->order) ? $ssFilter->order : 'ASC';
        $this->_params['ssFilter']['filter_keyword']                = $ssFilter->filter_keyword;
        $this->_params['ssFilter']['filter_date_begin']             = $ssFilter->filter_date_begin;
        $this->_params['ssFilter']['filter_date_end']               = $ssFilter->filter_date_end;
        $this->_params['ssFilter']['filter_date_type']              = $ssFilter->filter_date_type ? $ssFilter->filter_date_type : 'created';
        $this->_params['ssFilter']['filter_accountant_funds']       = $ssFilter->filter_accountant_funds ? $ssFilter->filter_accountant_funds : '';
        $this->_params['ssFilter']['filter_transaction_category']   = $ssFilter->filter_transaction_category;
        $this->_params['ssFilter']['filter_transaction_type']       = $ssFilter->filter_transaction_type;
        $this->_params['ssFilter']['filter_transaction_form']       = $ssFilter->filter_transaction_form;
        $this->_params['ssFilter']['filter_sale_branch_id']         = $ssFilter->filter_sale_branch_id;
        $this->_params['ssFilter']['filter_category']               = $ssFilter->filter_category;
        $this->_params['ssFilter']['filter_product']                = $ssFilter->filter_product;
        $this->_params['ssFilter']['filter_training_class']         = $ssFilter->filter_training_class;
        $this->_params['ssFilter']['filter_hbr_course']             = $ssFilter->filter_hbr_course;
        $this->_params['ssFilter']['filter_status']                 = $ssFilter->filter_status;
        
        // Thiết lập lại thông số phân trang
        $this->_paginator['itemCountPerPage'] = !empty($ssFilter->pagination_option) ? $ssFilter->pagination_option : 20;
        $this->_paginator['currentPageNumber'] = $this->params()->fromRoute('page', 1);
        $this->_params['paginator'] = $this->_paginator;
        
        // Lấy dữ liệu post của form
        $this->_params['data'] = $this->getRequest()->getPost()->toArray();
        
        // Truyển dữ dữ liệu ra ngoài view
        $this->_viewModel['params'] = $this->_params;
    }
    
    public function filterAction() {
        if($this->getRequest()->isPost()) {
            $ssFilter   = new Container(__CLASS__);
            $data = $this->_params['data'];
    
            $ssFilter->pagination_option            = intval($data['pagination_option']);
    
            $ssFilter->order_by                     = $data['order_by'];
            $ssFilter->order                        = $data['order'];
    
            $ssFilter->filter_keyword               = $data['filter_keyword'];
            $ssFilter->filter_date_begin            = $data['filter_date_begin'];
            $ssFilter->filter_date_end              = $data['filter_date_end'];
            $ssFilter->filter_date_type             = $data['filter_date_type'];
            $ssFilter->filter_accountant_funds      = $data['filter_accountant_funds'];
            $ssFilter->filter_transaction_category  = $data['filter_transaction_category'];
            $ssFilter->filter_transaction_type      = $data['filter_transaction_type'];
            $ssFilter->filter_transaction_form      = $data['filter_transaction_form'];
            $ssFilter->filter_sale_branch_id        = $data['filter_sale_branch_id'];
            $ssFilter->filter_category              = $data['filter_category'];
            $ssFilter->filter_product               = $data['filter_product'];
            $ssFilter->filter_training_class        = $data['filter_training_class'];
            $ssFilter->filter_hbr_course            = $data['filter_hbr_course'];
            $ssFilter->filter_status                = $data['filter_status'];
        }
    
        return $this->redirect()->toRoute('routeAdmin/default', array('controller' => $this->_params['controller'], 'action' => 'index'));
    }
    
    public function indexAction() {
        $number = new \ZendX\Functions\Number();
        $myForm = new \Admin\Form\Search\AccountantBill($this->getServiceLocator(), $this->_params['ssFilter']);
        $myForm->setData($this->_params['ssFilter']);
        
        $items = $this->getTable()->listItem($this->_params, array('task' => 'list-item'));
        $item_all = $this->getTable()->listItem($this->_params, array('task' => 'list-item', 'paginator' => false));

        $this->_viewModel['myForm']               = $myForm;
        $this->_viewModel['items']                = $items;
        $this->_viewModel['item_all']             = $item_all;
        $this->_viewModel['count']                = $this->getTable()->countItem($this->_params, array('task' => 'list-item'));
        $this->_viewModel['user']                 = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['funds']                = $this->getServiceLocator()->get('Admin\Model\FundsTable')->listItem(null, array('task' => 'list-all'));
        $this->_viewModel['sale_branch']          = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array("where" => array("code" => "sale-branch")), array('task' => 'cache')), array('key' => 'id', 'value' => 'name'));
        $this->_viewModel['transaction_category'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array("where" => array("code" => "accountant-transaction-category")), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['transaction_type']     = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array("where" => array("code" => "accountant-transaction-type")), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['transaction_form']     = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array("where" => array("code" => "accountant-transaction-form")), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['category']             = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array("where" => array("code" => "accountant-category")), array('task' => 'cache'));
        $this->_viewModel['product']              = $this->getServiceLocator()->get('Admin\Model\ProductTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['userInfo']             = $this->_userInfo->getUserInfo();
        $this->_viewModel['caption']              = 'Nghiệp vụ - Danh sách';
        return new ViewModel($this->_viewModel);
    }
    
    public function formAction() {
        $myForm = $this->getForm();
        $dateFormart   = new \ZendX\Functions\Date();
        $numberFormart = new \ZendX\Functions\Number();
        $connection = $this->getConnection();
        
        $task = 'add-item';
        $caption = 'Nghiệp vụ thu/chi - Thêm mới';
        $item = array();
        if(!empty($this->params('id'))) {
            $this->_params['data']['id'] = $this->params('id');
            $item = $this->getTable()->getItem($this->_params['data']);
            $this->_params['item'] = $item;
            $item['date'] = $dateFormart->formatToView($item['date']);
            
            if(!empty($item)) {
                $content_select = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "accountant-content" ) ), array('task' => 'cache'));
                foreach ($content_select AS $key => $value) {
                    if(strpos($item['content'], $value['name']) !== false) {
                        $content_select = explode($value['name'], $item['content']);
                        $item['content_select'] = trim($value['name']);
                        $item['content'] = trim($content_select[1]);
                    }
                }
                if(empty($item['content_select'])) {
                    $item['content_select'] = 'other';
                }
                
                if($this->params('code') == 'copy') {
                    $arrFilter = $this->_params['data'];
                    unset($arrFilter['id']);
                    unset($item['id'], $item['status'], $item['code']);

                    $myForm->setInputFilter(new \Admin\Filter\AccountantBill($arrFilter));
                    $myForm->bind($item);
                    $task = 'add-item';
                    $caption = 'Nghiệp vụ Thu/Chi - Copy';
                } else {
                    $myForm->setInputFilter(new \Admin\Filter\AccountantBill($this->_params['data']));
                    $myForm->setData($item);
                    $task = 'edit-item';
                    $caption = 'Nghiệp vụ Thu/Chi- Sửa';
                }
            }
        } else {
            $myForm->setInputFilter(new \Admin\Filter\AccountantBill($this->_params['data']));
        }
    
        if($this->getRequest()->isPost()){
            $myForm->setData($this->_params['data']);
            $controlAction = $this->_params['data']['control-action'];
            
            if($myForm->isValid()){
                $this->_params['data'] = $myForm->getData(FormInterface::VALUES_AS_ARRAY);
                # begin
                $connection->beginTransaction();
                $result = $this->getTable()->saveItem($this->_params, array('task' => $task));

                $connection->commit();
                # end
                $this->flashMessenger()->addSuccessMessage('Dữ liệu đã được cập nhật thành công');
    
                if($controlAction == 'save-new') {
                    return $this->redirect()->toRoute('routeAdmin/default', array('controller' => $this->_params['controller'], 'action' => 'form'));
                } else if($controlAction == 'save') {
                    return $this->redirect()->toRoute('routeAdmin/default', array('controller' => $this->_params['controller'], 'action' => 'form', 'id' => $result));
                } else {
                    return $this->redirect()->toRoute('routeAdmin/default', array('controller' => $this->_params['controller'], 'action' => 'index'));
                }
            }
        }
    
        $this->_viewModel['myForm']     = $myForm;
        $this->_viewModel['item']       = $item;
        $this->_viewModel['caption']    = $caption;
        $this->_viewModel['userInfo']   = $this->_userInfo->getUserInfo();
        return new ViewModel($this->_viewModel);
    }

    public function addAction() {
        $myForm = $this->getForm();
        $dateFormart   = new \ZendX\Functions\Date();
        $numberFormart = new \ZendX\Functions\Number();

        $task = 'add-item';
        $caption = 'Nghiệp vụ - Thêm mới';
        $item = array();
        if(!empty($this->params('id'))) {
            $this->_params['data']['id'] = $this->params('id');
            $item = $this->getTable()->getItem($this->_params['data']);
            $this->_params['item'] = $item;
            $item['date'] = $dateFormart->formatToView($item['date']);

            if(!empty($item)) {
                $content_select = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "accountant-content" ) ), array('task' => 'cache'));
                foreach ($content_select AS $key => $value) {
                    if(strpos($item['content'], $value['name']) !== false) {
                        $content_select = explode($value['name'], $item['content']);

                        $item['content_select'] = trim($value['name']);
                        $item['content'] = trim($content_select[1]);
                    }
                }

                if(empty($item['content_select'])) {
                    $item['content_select'] = 'other';
                }

                if($this->params('code') == 'copy') {
                    $arrFilter = $this->_params['data'];
                    unset($arrFilter['id']);

                    $myForm->setInputFilter(new \Admin\Filter\AccountantBill($arrFilter));
                    $myForm->bind($item);
                    $task = 'add-item';
                    $caption = 'Nghiệp vụ - Copy';
                } else {
                    $myForm->setInputFilter(new \Admin\Filter\AccountantBill($this->_params['data']));
                    $myForm->setData($item);
                    $task = 'edit-item';
                    $caption = 'Nghiệp vụ - Sửa';
                }
            }
        } else {
            $myForm->setInputFilter(new \Admin\Filter\AccountantBill($this->_params['data']));
        }

        if($this->getRequest()->isPost()){
            $myForm->setData($this->_params['data']);
            $controlAction = $this->_params['data']['control-action'];

            if($myForm->isValid()){
                $this->_params['data'] = $myForm->getData(FormInterface::VALUES_AS_ARRAY);
                $result = $this->getTable()->saveItem($this->_params, array('task' => $task));

                $this->flashMessenger()->addSuccessMessage('Dữ liệu đã được cập nhật thành công');

                if($controlAction == 'save-new') {
                    return $this->redirect()->toRoute('routeAdmin/default', array('controller' => $this->_params['controller'], 'action' => 'form'));
                } else if($controlAction == 'save') {
                    return $this->redirect()->toRoute('routeAdmin/default', array('controller' => $this->_params['controller'], 'action' => 'form', 'id' => $result));
                } else {
                    return $this->redirect()->toRoute('routeAdmin/default', array('controller' => $this->_params['controller'], 'action' => 'index'));
                }
            }
        }

        $this->_viewModel['myForm']     = $myForm;
        $this->_viewModel['item']       = $item;
        $this->_viewModel['caption']    = $caption;
        $this->_viewModel['userInfo']   = $this->_userInfo->getUserInfo();
        return new ViewModel($this->_viewModel);
    }
    
    public function deleteAction() {
        $item = $this->getTable()->getItem(array('id' => $this->_params['route']['id']));
        if(empty($item)) {
            return $this->redirect()->toRoute('routeAdmin/type', array('controller' => 'notice', 'action' => 'not-found', 'type' => 'modal'));
        }
        if($this->getRequest()->isPost()){
            if($item['status'] == 0) {
                $this->_params['item'] = $item;
                $this->getTable()->deleteItem($this->_params, array('task' => 'delete-item'));
                $this->flashMessenger()->addSuccessMessage('Xóa dữ liệu thành công');
                return $this->redirect()->toRoute('routeAdmin/default', array('controller' => $this->_params['controller'], 'action' => 'index'));
            }
            else{
                $this->flashMessenger()->addErrorMessage('Nghiệp vụ đã được xác nhận, bạn không thể xóa');
                return $this->redirect()->toRoute('routeAdmin/default', array('controller' => $this->_params['controller'], 'action' => 'index'));
            }
        }
        $this->_viewModel['item']    = $item;
        $this->_viewModel['caption'] = 'Nghiệp vụ Thu/Chi - Xóa';
        $viewModel = new ViewModel($this->_viewModel);
        return $viewModel;
    }
    
    public function printAction() {
        $item = $this->getServiceLocator()->get('Admin\Model\AccountantBillTable')->getItem(array('id' => $this->_params['route']['id']));
        if (empty($item)) {
            return $this->redirect()->toRoute('routeAdmin/type', array('controller' => 'notice', 'action' => 'not-found', 'type' => 'modal'));
        }
        $this->_viewModel['item']        = $item;
//        $this->_viewModel['sale_branch'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array("where" => array("code" => "sale-branch")), array('task' => 'cache'));
        $viewModel                       = new ViewModel($this->_viewModel);
        $viewModel->setTerminal(true);
        return $viewModel;
    }
    
    public function exportAction() {
        $date                 = new \ZendX\Functions\Date();
        $items                = $this->getTable()->listItem($this->_params, array('task' => 'list-item', 'paginator' => false));
        $user                 = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $funds                = $this->getServiceLocator()->get('Admin\Model\FundsTable')->listItem(null, array('task' => 'list-all'));
        $transaction_category = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array("where" => array("code" => "accountant-transaction-category")), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $transaction_type     = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array("where" => array("code" => "accountant-transaction-type")), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $transaction_form     = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array("where" => array("code" => "accountant-transaction-form")), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $category             = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array("where" => array("code" => "accountant-category")), array('task' => 'cache'));
        $sale_branch          = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array( "where" => array( "code" => "sale-branch" )), array('task' => 'cache'));
        //Include PHPExcel
        require_once PATH_VENDOR . '/Excel/PHPExcel.php';
    
        // Config
        $config = array(
            'sheetData' => 0,
            'headRow' => 1,
            'startRow' => 2,
            'startColumn' => 0,
        );
    
        // Column
        $arrColumn = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ');
    
        // Data Export
        $arrData = array(
            array('field' => 'created', 'title' => 'Ngày nhập', 'type' => 'date', 'format' => 'd/m/Y'),
            array('field' => 'date', 'title' => 'Ngày chứng từ', 'type' => 'date', 'format' => 'd/m/Y'),
            array('field' => 'code', 'title' => 'Số chứng từ', 'type' => 'code'),
            array('field' => 'sale_branch_id', 'title' => 'Cơ sở', 'type' => 'data_source', 'data_source' => $sale_branch),
            array('field' => 'accountant_funds_id', 'title' => 'Tài khoản chính', 'type' => 'data_source', 'data_source' => $funds),
            array('field' => 'transaction_category_id', 'title' => 'Loại nghiệp vụ', 'type' => 'data_source', 'data_source' => $transaction_category),
            array('field' => 'transaction_type_id', 'title' => 'Nghiệp vụ', 'type' => 'data_source', 'data_source' => $transaction_type),
            array('field' => 'transaction_form_id', 'title' => 'Hình thức giao dịch', 'type' => 'data_source', 'data_source' => $transaction_form),
            array('field' => 'paid', 'title' => 'Thu'),
            array('field' => 'accrued', 'title' => 'Chi'),
            array('field' => 'funds', 'title' => 'Tồn'),
            array('field' => 'content', 'title' => 'Nội dung'),
            array('field' => 'category_id', 'title' => 'Danh mục', 'type' => 'data_source', 'data_source' => $category),
            array('field' => 'created_item_id', 'title' => 'Người lập phiếu thu/chi', 'type' => 'data_source', 'data_source' => $user, 'data_source_field' => 'name'),
            array('field' => 'submitter_name', 'title' => 'Tên người nộp/nhận'),
            array('field' => 'submitter_phone', 'title' => 'Điện thoại người nộp/nhận'),
            array('field' => 'note', 'title' => 'Ghi chú'),
            array('field' => 'created_by', 'title' => 'Người nhập', 'type' => 'data_source', 'data_source' => $user, 'data_source_field' => 'name'),
        );
    
        // Create new PHPExcel object
        $objPHPExcel = new \PHPExcel();
    
        // Set document properties
        $objPHPExcel->getProperties()->setCreator($this->_userInfo->getUserInfo('fullname'))
        ->setLastModifiedBy($this->_userInfo->getUserInfo('username'))
        ->setTitle("Export");
    
        // Dữ liệu tiêu đề cột
        $startColumn = $config['startColumn'];
        foreach ($arrData AS $key => $data) {
            $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue($arrColumn[$startColumn] . $config['headRow'], $data['title']);
            $objPHPExcel->getActiveSheet()->getStyle($arrColumn[$startColumn] . $config['headRow'])->getFont()->setBold(true);
            $startColumn++;
        }
    
        // Dữ liệu data
        $startRow = $config['startRow'];
        foreach ($items AS $item) {
            $startColumn = $config['startColumn'];
            foreach ($arrData AS $key => $data) {
    
                switch ($data['type']) {
                    case 'date':
                        $formatDate = $data['format'] ? $data['format'] : 'd/m/Y';
                        $value = $date->formatToView($item[$data['field']], $formatDate);
                        break;
                    case 'data_source':
                        $field = $data['data_source_field'] ? $data['data_source_field'] : 'name';
                        $value = $data['data_source'][$item[$data['field']]][$field];
                        break;
                    case 'code':
                        $value = $item[$data['field']];
                        if(empty($item[$data['field']])) {
                            $value = $company_branch[$item['company_branch_id']]['code'] .'PTK'. $item['id']; // Mã hóa đơn
                            if($item['transaction_type_id'] == 'chi') {
                                $value = $company_branch[$item['company_branch_id']]['code'] .'PCK'. $item['id'];
                            }
                        }
                        break;
                    default:
                        $value = $item[$data['field']];
                }
    
                $objPHPExcel->setActiveSheetIndex($config['sheetData'])->setCellValue($arrColumn[$startColumn] . $startRow, $value);
                $startColumn++;
            }
    
            $startRow++;
        }
    
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="SoQuy.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
    
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
    
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    
        return $this->response;
    }
}



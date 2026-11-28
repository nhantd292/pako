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
        $this->_params['ssFilter']['filter_accountant_funds'] = '1789053787gd9s9w9qg955';

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
}



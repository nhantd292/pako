<?php

namespace Admin\Controller;

use ZendX\Controller\ActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Form\FormInterface;

class OrdersReturnDetailController extends ActionController {
    
    public function init() {
        // Thiết lập options
        $this->_options['tableName'] = 'Admin\Model\OrdersReturnDetailTable';
        $this->_options['formName'] = 'formAdminContractDetail';
        
        // Thiết lập session filter
        $action = str_replace('-', '_', $this->_params['action']);
        $ssFilter = new Container(__CLASS__. $action);

        $this->_params['ssFilter']['order_by']              = !empty($ssFilter->order_by) ? $ssFilter->order_by : 'date';
        $this->_params['ssFilter']['order']                 = !empty($ssFilter->order) ? $ssFilter->order : 'DESC';
        $this->_params['ssFilter']['filter_keyword']        = $ssFilter->filter_keyword;
        $this->_params['ssFilter']['filter_date_begin']     = $ssFilter->filter_date_begin;
        $this->_params['ssFilter']['filter_date_end']       = $ssFilter->filter_date_end;
        $this->_params['ssFilter']['filter_date_type']      = $ssFilter->filter_date_type;
        $this->_params['ssFilter']['filter_sale_branch']    = $ssFilter->filter_sale_branch;
        $this->_params['ssFilter']['filter_sale_group']     = $ssFilter->filter_sale_group;
        $this->_params['ssFilter']['filter_user']           = $ssFilter->filter_user;
        $this->_params['ssFilter']['filter_product'] 	    = $ssFilter->filter_product;
        $this->_params['ssFilter']['filter_bill_code']      = $ssFilter->filter_bill_code;
        $this->_params['ssFilter']['filter_status_type']    = $ssFilter->filter_status_type;
        $this->_params['ssFilter']['filter_status']         = $ssFilter->filter_status;
        $this->_params['ssFilter']['filter_coincider']      = $ssFilter->filter_coincider;
        $this->_params['ssFilter']['filter_send_ghtk']      = $ssFilter->filter_send_ghtk;
        $this->_params['ssFilter']['filter_category']       = $ssFilter->filter_category;
        $this->_params['ssFilter']['filter_product']        = $ssFilter->filter_product;

        // Thiết lập lại thông số phân trang
        $this->_paginator['itemCountPerPage'] = !empty($ssFilter->pagination_option) ? $ssFilter->pagination_option : $this->_paginator['itemCountPerPage'];
        $this->_paginator['currentPageNumber'] = $this->params()->fromRoute('page', 1);
        $this->_params['paginator'] = $this->_paginator;
        
        // Lấy dữ liệu post của form
        $this->_params['data'] = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
        
        // Truyển dữ dữ liệu ra ngoài view
        $this->_viewModel['params'] = $this->_params;
        $this->_viewModel['curent_user']  = $this->_userInfo->getUserInfo();
    }
    
    // Tìm kiếm
    public function filterAction() {
        if($this->getRequest()->isPost()) {
            $data = $this->_params['data'];

            $action = !empty($this->getRequest()->getPost('filter_action')) ? str_replace('-', '_', $this->getRequest()->getPost('filter_action')) : 'index';
            $ssFilter	= new Container(__CLASS__ . $action);

            $ssFilter->pagination_option        = intval($data['pagination_option']);
            $ssFilter->order_by                 = $data['order_by'];
            $ssFilter->order                    = $data['order'];
            $ssFilter->filter_keyword           = $data['filter_keyword'];
            $ssFilter->filter_date_begin        = $data['filter_date_begin'];
            $ssFilter->filter_date_end          = $data['filter_date_end'];
            $ssFilter->filter_date_type         = $data['filter_date_type'];
            $ssFilter->filter_product 	        = $data['filter_product'];
            $ssFilter->filter_status_type       = $data['filter_status_type'];
            $ssFilter->filter_status            = $data['filter_status'];
            $ssFilter->filter_user              = $data['filter_user'];
            $ssFilter->filter_action            = $data['filter_action'];
            $ssFilter->filter_coincider 	    = $data['filter_coincider'];
            $ssFilter->filter_send_ghtk 	    = $data['filter_send_ghtk'];
            $ssFilter->filter_category 	        = $data['filter_category'];
            $ssFilter->filter_product 	        = $data['filter_product'];

            $ssFilter->filter_sale_group = $data['filter_sale_group'];
            if(!empty($data['filter_sale_branch'])) {
                if($ssFilter->filter_sale_branch != $data['filter_sale_branch']) {
                    $ssFilter->filter_sale_group = null;
                    $ssFilter->filter_sale_branch = $data['filter_sale_branch'];
                }
            } else {
                $ssFilter->filter_sale_group = null;
                $ssFilter->filter_sale_branch = $data['filter_sale_branch'];
            }
            
            if($ssFilter['filter_date_type'] == 'date_debt') {
                if(empty($ssFilter->filter_date_begin)) {
                    $ssFilter->filter_date_begin = date('01/m/Y');
                    $ssFilter->filter_date_end = date('t/m/Y');
                }
            }

            if(empty($data['filter_status_type'])){
                $ssFilter->filter_status = null;
            }
        }
        $action = str_replace('_', '-', $this->getRequest()->getPost('filter_action'));
        $this->goRoute(['action' => $action]);
    }
    
    // Danh sách
    public function indexAction() {
        $ssFilter = new Container(__CLASS__.'index');
        // Phân quyền view
//        $curent_user = $this->_userInfo->getUserInfo();
//        $permission_ids = explode(',', $curent_user['permission_ids']);
//        if(!in_array(SYSTEM, $permission_ids) && !in_array(ADMIN, $permission_ids) && !in_array(MANAGER, $permission_ids)){
//            if(in_array(GDCN, $permission_ids) || in_array(SALEADMIN, $permission_ids)){
//                $this->_params['ssFilter']['filter_sale_branch'] = $curent_user['sale_branch_id'];
//                $ssFilter->filter_sale_branch = $curent_user['sale_branch_id'];
//            }
//            elseif (in_array(GROUP_SALES_LEADER, $permission_ids)){
//                $this->_params['ssFilter']['filter_sale_branch'] = $curent_user['sale_branch_id'];
//                $this->_params['ssFilter']['filter_sale_group'] = $curent_user['sale_group_id'];
//                $ssFilter->filter_sale_branch = $curent_user['sale_branch_id'];
//                $ssFilter->filter_sale_group = $curent_user['filter_sale_group'];
//            }
//            else{
//                $this->_params['ssFilter']['filter_user'] = $curent_user['id'];
//            }
//        }

        $myForm	= new \Admin\Form\Search\BaseSearch($this->getServiceLocator(), $this->_params);
        $myForm->setData($this->_params['ssFilter']);

        $this->_viewModel['myForm']	                = $myForm;
        $this->_viewModel['items']                  = $this->getTable()->listItem($this->_params, array('task' => 'list-item'));
//        echo "<pre>";
//        print_r($this->_viewModel['items']->toArray());
//        echo "</pre>";
//        exit;
        $this->_viewModel['count']                  = $this->getTable()->countItem($this->_params, array('task' => 'list-item'));
        $this->_viewModel['user']                   = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['sale_group']             = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'lists-group')), array('task' => 'cache'));
        $this->_viewModel['sale_branch']            = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['order_status']           = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'orders-state')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['caption']                = 'Sản phẩm khách trả hàng';
        
        return new ViewModel($this->_viewModel);
    }

}



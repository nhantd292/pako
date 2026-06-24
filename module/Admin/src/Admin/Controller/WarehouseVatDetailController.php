<?php

namespace Admin\Controller;

use kcfinder\zipFolder;
use ZendX\Controller\ActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Form\FormInterface;
use ZendX\System\UserInfo;

class WarehouseVatDetailController extends ActionController{
    public $caption = 'Phiếu nhập VAT';
    public function init() {
        // Thiết lập options
        $this->_options['tableName'] = 'Admin\Model\WarehouseVatDetailTable';
        $this->_options['formName'] = 'formAdminWarehouseVatDetail';

        // Thiết lập session filter
        $ssFilter = new Container(__CLASS__ . $this->_params['action']);
        $this->_params['ssFilter']['order_by']                      = !empty($ssFilter->order_by) ? $ssFilter->order_by : 'ordering';
        $this->_params['ssFilter']['order']                         = !empty($ssFilter->order) ? $ssFilter->order : 'DESC';
        $this->_params['ssFilter']['filter_state']                  = $ssFilter->filter_state;
        $this->_params['ssFilter']['filter_keyword']                = $ssFilter->filter_keyword;
        $this->_params['ssFilter']['filter_date_begin']             = $ssFilter->filter_date_begin;
        $this->_params['ssFilter']['filter_date_end']               = $ssFilter->filter_date_end;
        $this->_params['ssFilter']['filter_sale_branch_id']         = $ssFilter->filter_sale_branch_id;
        $this->_params['ssFilter']['filter_type']                   = $ssFilter->filter_type;
        $this->_params['ssFilter']['filter_user_id']                = $ssFilter->filter_user_id;

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
            
            $ssFilter->pagination_option            = intval($data['pagination_option']);
            $ssFilter->order_by                     = $data['order_by'];
            $ssFilter->order                        = $data['order'];
            $ssFilter->filter_state                 = $data['filter_state'];
            $ssFilter->filter_keyword               = $data['filter_keyword'];
            $ssFilter->filter_date_begin            = $data['filter_date_begin'];
            $ssFilter->filter_date_end              = $data['filter_date_end'];
            $ssFilter->filter_sale_branch_id        = $data['filter_sale_branch_id'];
            $ssFilter->filter_type                  = $data['filter_type'];
            $ssFilter->filter_user_id               = $data['filter_user_id'];
        }

        if (!empty($this->_params['route']['id'])) {
            $ssFilter->filter_product = $this->_params['route']['id'];
        }

        $this->goRoute(array('action' => $action));
    }

    public function indexAction() {
        $myForm    = new \Admin\Form\Search\WarehouseVatDetail($this, $this->_params['ssFilter']);
        $myForm->setData($this->_params['ssFilter']);
        // Danh sách data
        $items = $this->getTable()->listItem($this->_params, array('task' => 'list-item'));

        $this->_viewModel['myForm']             = $myForm;
        $this->_viewModel['items']              = $items;
        $this->_viewModel['model']              = $this->getTable();
        $this->_viewModel['count']              = $this->getTable()->countItem($this->_params, array('task' => 'list-item'));
        $this->_viewModel['user']               = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['warehouse']          = $this->getServiceLocator()->get('Admin\Model\WarehouseTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['branch']             = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['caption']            = $this->caption;
        $this->_viewModel['order_status']       = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'orders-state')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));

        return new ViewModel($this->_viewModel);
    }

    public function addAction() {
        $this->_params['userInfo'] = $this->_userInfo->getUserInfo();
        $number = new \ZendX\Functions\Number();
        $myForm = new \Admin\Form\WarehouseVatDetail($this, $this->_params);
        $connection = $this->getConnection();

        if($this->getRequest()->isPost()){
            $this->_viewModel['is_post'] = 1;
            unset($this->_params['data']['filter_products_type']);
            unset($this->_params['data']['filter_keyword']);

            $myForm->setInputFilter(new \Admin\Filter\WarehouseVatDetail(array('data' => $this->_params['data'], 'route' => $this->_params['route'])));
            $myForm->setData($this->_params['data']);
            $controlAction = $this->_params['data']['control-action'];
            $productList = $this->_params['data']['products_list'];

            if($myForm->isValid()){
                $check_emty_data = !empty($productList) ? true : false;

                for ($i = 0; $i < count($productList['products_id']); $i++ ){
                    if(
                        trim($productList['products_id'][$i]) == "" ||
                        (int)trim($productList['quantity'][$i]) == 0
                    )$check_emty_data = false;
                }

                if($check_emty_data){
                    $products_detail  = array();
                    for($i = 0; $i < count($productList['products_id']); $i++){
                        if(!empty($productList['products_id'][$i])) {
                            $products_detail[$i]['quantity']         = $number->formatToData($productList['quantity'][$i]); // số lượng của đơn hàng
                            $products_detail[$i]['products_id']      = $productList['products_id'][$i]; // id sản phẩm
                            $products_detail[$i]['sale_branch_id']   = $this->_params['data']['sale_branch_id']; // id chi nhánh
                            $products_detail[$i]['note']             = $this->_params['data']['note']; // ghi chú
                            $ssFilter = array(
                                'filter_sale_branch_id' => $products_detail[$i]['sale_branch_id'],
                                'filter_products_id' => $productList['products_id'][$i]
                            );
                            $count_products_vat = $this->getServiceLocator()->get('Admin\Model\WarehouseVatDetailTable')->countItem(array('ssFilter' => $ssFilter), array('task' => 'list-item'));
                            if ($count_products_vat > 0) {
                                $products_vat = $this->getServiceLocator()->get('Admin\Model\WarehouseVatDetailTable')->listItem(array('ssFilter' => $ssFilter), array('task' => 'list-item', 'paginator' => false))->toArray();
                                $pr_item = $products_vat[0];
                                $products_detail[$i]['quantity_begin'] = $pr_item['quantity_end'];
                            }
                            else{
                                $products_detail[$i]['quantity_begin'] = 0;
                            }
                            $products_detail[$i]['quantity_end'] = $products_detail[$i]['quantity'] + $products_detail[$i]['quantity_begin'];
                            $products_detail[$i]['type'] = 'in';
                        }
                    }

                    ##### begin #####
                    $connection->beginTransaction();

                    // Thêm chi tiết sản phẩm
                    foreach($products_detail as $arraydata){
                        $this->getServiceLocator()->get('Admin\Model\WarehouseVatDetailTable')->saveItem(array('data' => $arraydata), array('task' => 'add-item'));
                    }

                    $connection->commit();
                    ##### end #####

                    $this->flashMessenger()->addSuccessMessage('Dữ liệu đã được cập nhật thành công');


                    if($controlAction == 'save-new') {
                        $this->goRoute(array('action' => 'add'));
                    } else if($controlAction == 'save') {
                        $this->goRoute();
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

    public function editAction() {
        $this->_params['userInfo'] = $this->_userInfo->getUserInfo();
        $number = new \ZendX\Functions\Number();
        $dateFormat = new \ZendX\Functions\Date();
        $connection = $this->getConnection();
        $id = $this->params('id');

        if(!empty($id)) {
            $item = $this->getTable()->getItem(array('id' => $id), array('task' => 'search'));

            if ($item['type'] == 'out') {
                $this->flashMessenger()->addErrorMessage('Phiếu xuất được tạo tự động bạn không thể sửa!');
                $this->goRoute();
            }
            $myForm = new \Admin\Form\WarehouseVatDetail($this, $item);
            $myForm->setData($item);
            $this->_viewModel['item']        = $item;
        }
        else {
            return $this->redirect()->toRoute('routeAdmin/type', array('controller' => 'notice', 'action' => 'not-found', 'type' => 'not-found'));
        }

        if($this->getRequest()->isPost()){
            $myForm->setInputFilter(new \Admin\Filter\WarehouseVatDetail(array('data' => $this->_params['data'], 'route' => $this->_params['route'])));
            $myForm->setData($this->_params['data']);
            $controlAction = $this->_params['data']['control-action'];
            $this->_params['item'] = $item;

            if($myForm->isValid()){
                ##### begin #####
                $connection->beginTransaction();
                $this->getTable()->saveItem($this->_params, array('task' => 'edit-item'));
                $connection->commit();
                ##### end #####

                $this->flashMessenger()->addSuccessMessage('Dữ liệu đã được cập nhật thành công');

                if($controlAction == 'save-new') {
                    $this->goRoute(array('action' => 'add'));
                } else if($controlAction == 'save') {
                    $this->goRoute();
                } else {
                    $this->goRoute();
                }
            }
            else {
                $this->_viewModel['data']  = $this->_params['data'];
            }
        }

        $this->_viewModel['myForm']	        = $myForm;
        $this->_viewModel['item']	        = $item;
        $this->_viewModel['sale_branch']    = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['user']           = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['caption']        = 'Sửa - '.$this->caption;
        return new ViewModel($this->_viewModel);
    }

    public function deleteAction() {
//        if($this->getRequest()->isPost()) {
//            if(!empty($this->_params['data']['cid'])) {
//                $cdata = $this->getTable()->deleteItem($this->_params, array('task' => 'delete-item'));
//                $message = 'Xóa '. $cdata .' '.$this->caption.' thành công';
//                $this->flashMessenger()->addSuccessMessage($message);
//            }
//        }

        $this->goRoute(array('action' => 'index'));
    }
}

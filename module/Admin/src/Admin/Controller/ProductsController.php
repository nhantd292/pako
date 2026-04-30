<?php

namespace Admin\Controller;

use kcfinder\zipFolder;
use ZendX\Controller\ActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Form\FormInterface;
use ZendX\System\UserInfo;



class ProductsController extends ActionController{
    public $caption = 'Sản phẩm';

    public function init() {
        // Thiết lập options
        $this->_options['tableName'] = 'Admin\Model\ProductsTable';
        $this->_options['formName'] = 'formAdminProducts';

        // Thiết lập session filter
        $ssFilter = new Container(__CLASS__ . $this->_params['action']);
        $this->_params['ssFilter']['order_by']                  = !empty($ssFilter->order_by) ? $ssFilter->order_by : 'ordering';
        $this->_params['ssFilter']['order']                     = !empty($ssFilter->order) ? $ssFilter->order : 'DESC';
        $this->_params['ssFilter']['filter_status']             = $ssFilter->filter_status;
        $this->_params['ssFilter']['filter_keyword']            = $ssFilter->filter_keyword;
        $this->_params['ssFilter']['filter_trademark']          = $ssFilter->filter_trademark;
        $this->_params['ssFilter']['filter_products_type']      = $ssFilter->filter_products_type;

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
            $ssFilter->filter_trademark         = $data['filter_trademark'];
            $ssFilter->filter_products_type     = $data['filter_products_type'];
        }

        if (!empty($this->_params['route']['id'])) {
            $ssFilter->filter_product = $this->_params['route']['id'];
        }

        $this->goRoute(array('action' => $action));
    }

    public function indexAction() {
        $myForm    = new \Admin\Form\Search\Products($this->getServiceLocator(), $this->_params['ssFilter']);
        $myForm->setData($this->_params['ssFilter']);
        // Danh sách data
        $items = $this->getTable()->listItem($this->_params, array('task' => 'list-item'));

        $this->_viewModel['myForm']             = $myForm;
        $this->_viewModel['items']              = $items;
        $this->_viewModel['model']              = $this->getTable();
        $this->_viewModel['count']              = $this->getTable()->countItem($this->_params, array('task' => 'list-item'));
        $this->_viewModel['user']               = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['products_type']      = $this->getServiceLocator()->get('Admin\Model\ProductsTypeTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['units']              = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'unit')), array('task' => 'cache'));
        $this->_viewModel['trademarks']          = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'trademark')), array('task' => 'cache'));
        $this->_viewModel['caption']            = $this->caption;

        return new ViewModel($this->_viewModel);
    }

    public function addAction() {
        $customer_type = $this->getServiceLocator()->get('Admin\Model\CustomerTypeTable')->listItem(null, array('task' => 'cache'));
        $warehouse = $this->getServiceLocator()->get('Admin\Model\WarehouseTable')->listItem(null, array('task' => 'cache'));

        $myForm = $this->getForm();
        $connection = $this->getConnection();

        // get products type form
        $formProductsPrice = new \Admin\Form\ProductsPrice($this, $customer_type, '');
        // get warehouse form
        $formProductsInventory = new \Admin\Form\ProductsInventory($this, $warehouse, '');

        if($this->getRequest()->isPost()){
            $myForm->setInputFilter(new \Admin\Filter\Products());
            $myForm->setData($this->_params['data']);

            $formProductsPrice->setInputFilter(new \Admin\Filter\ProductsPrice(null, $customer_type, ''));
            $formProductsPrice->setData($this->_params['data']);

            $formProductsInventory->setInputFilter(new \Admin\Filter\ProductsInventory(null, $warehouse, ''));
            $formProductsInventory->setData($this->_params['data']);

            $controlAction = $this->_params['data']['control-action'];

            $isMyFormValid = $myForm->isValid();
            $isPriceValid = $formProductsPrice->isValid();
            $isInventoryValid = $formProductsInventory->isValid();

            if ($isMyFormValid and $isPriceValid and $isInventoryValid) {
                $data_post = array_merge(
                    $myForm->getData(FormInterface::VALUES_AS_ARRAY),
                    $formProductsPrice->getData(FormInterface::VALUES_AS_ARRAY),
                    $formProductsInventory->getData(FormInterface::VALUES_AS_ARRAY)
                );
                $this->_params['data'] = $data_post;


                try {
                    # begin
                    $connection->beginTransaction();
                    # add products
                    $result_products = $this->getTable()->saveItem($this->_params, array('task' => 'add-item'));
                    # add price products
                    foreach ($customer_type as $key => $value) {
                        $priceData = array(
                            'products_id'       => $result_products,
                            'customer_type_id'  => $key,
                            'price'             => $this->_params['data'][$key.'__price'],
                        );
                        $this->getServiceLocator()->get('Admin\Model\ProductsPriceTable')->saveItem(array('data' => $priceData), array('task' => 'add-item'));
                    }
                    # add inventory products
                    foreach ($warehouse as $key => $value) {
                        $InventoryData = array(
                            'products_id'       => $result_products,
                            'warehouse_id'      => $key,
                            'quantity'          => $this->_params['data'][$key.'__quantity'],
                        );
                        $this->getServiceLocator()->get('Admin\Model\ProductsInventoryTable')->saveItem(array('data' => $InventoryData), array('task' => 'add-item'));
                    }

                    $connection->commit();
                    # end

                    $this->flashMessenger()->addMessage('Thêm mới '.$this->caption.' thành công');

                    if($controlAction == 'save-new') {
                        $this->goRoute(array('action' => 'add'));
                    } else if($controlAction == 'save') {
                        $this->goRoute(array('action' => 'edit', 'id' => $result_products));
                    } else {
                        $this->goRoute();
                    }
                } catch (\Exception $e) {
                    $connection->rollback();
                    throw $e;
                }
            }
        }

        $this->_viewModel['myForm']	                = $myForm;
        $this->_viewModel['customer_type']	        = $customer_type;
        $this->_viewModel['formProductsPrice']	    = $formProductsPrice;
        $this->_viewModel['warehouse']	            = $warehouse;
        $this->_viewModel['formProductsInventory']	= $formProductsInventory;
        $this->_viewModel['caption']                = 'Thêm mới - '.$this->caption;
        return new ViewModel($this->_viewModel);
    }

    public function editAction() {
        $customer_type = $this->getServiceLocator()->get('Admin\Model\CustomerTypeTable')->listItem(null, array('task' => 'cache'));
        $warehouse = $this->getServiceLocator()->get('Admin\Model\WarehouseTable')->listItem(null, array('task' => 'cache'));

        $item_id = $this->params('id');

        $myForm = $this->getForm();
        $connection = $this->getConnection();

        // get customer type form
        $formProductsPrice = new \Admin\Form\ProductsPrice($this, $customer_type, $item_id);
        $products_price_items = $this->getServiceLocator()->get('Admin\Model\ProductsPriceTable')->listItem(array('products_id' => $item_id), array('task' => 'list-item-by-products-id'));

        $formProductsInventory = new \Admin\Form\ProductsInventory($this, $warehouse, $item_id);
        $products_inventory_items = $this->getServiceLocator()->get('Admin\Model\ProductsInventoryTable')->listItem(array('products_id' => $item_id), array('task' => 'list-item-by-products-id'));

        if (!empty($item_id)) {
            $this->_params['data']['id'] = $item_id;
            $item = $this->getTable()->getItem($this->_params['data']);

            if (!empty($item)) {
                if (!$this->getRequest()->isPost()) {
                    $myForm->setData($item);

                    $data_products_price_items = [];
                    foreach ($products_price_items as $key => $value) {
                        $data_products_price_items[$value->customer_type_id.'_'.$value->products_id."_price"] = $value->price;
                    }
                    $formProductsPrice->setData($data_products_price_items);

                    $data_products_inventory_items = [];
                    foreach ($products_inventory_items as $key => $value) {
                        $data_products_inventory_items[$value->warehouse_id.'_'.$value->products_id."_quantity"] = $value->quantity;
                    }
                    $formProductsInventory->setData($data_products_inventory_items);
                }
            }
            else {
                return $this->redirect()->toRoute('routeAdmin/type', array('controller' => 'notice', 'action' => 'not-found', 'type' => 'not-found'));
            }
        }
        if ($this->getRequest()->isPost()) {
            $myForm->setInputFilter(new \Admin\Filter\Products(array('id' => $item_id)));
            $myForm->setData($this->_params['data']);

            $formProductsPrice->setInputFilter(new \Admin\Filter\ProductsPrice(null, $customer_type, $item_id));
            $formProductsPrice->setData($this->_params['data']);

            $formProductsInventory->setInputFilter(new \Admin\Filter\ProductsInventory(null, $warehouse, $item_id));
            $formProductsInventory->setData($this->_params['data']);

            $controlAction = $this->_params['data']['control-action'];

//            if (!$myForm->isValid()) {
//                $errors = $myForm->getMessages();
//                echo "<pre>";
//                print_r($errors);
//                echo "</pre>";
//                die(); // Dừng lại để xem lỗi
//            }

            $isMyFormValid = $myForm->isValid();
            $isPriceValid = $formProductsPrice->isValid();
            $isInventoryValid = $formProductsInventory->isValid();

            if ($isMyFormValid and $isPriceValid and $isInventoryValid) {
                $data_post = array_merge(
                    $myForm->getData(FormInterface::VALUES_AS_ARRAY),
                    $formProductsPrice->getData(FormInterface::VALUES_AS_ARRAY),
                    $formProductsInventory->getData(FormInterface::VALUES_AS_ARRAY)
                );
                $this->_params['data'] = $data_post;

                try {
                    $connection->beginTransaction();

                    $this->getTable()->saveItem($this->_params, array('task' => 'edit-item'));

                    # update price products
                    foreach ($customer_type as $key => $value) {
                        $priceData = array(
                            'products_id'       => $item_id,
                            'customer_type_id'  => $key,
                            'price'             => $this->_params['data'][$key.'_'.$item_id.'_price'],
                        );
                        $products_price_item = $this->getServiceLocator()->get('Admin\Model\ProductsPriceTable')->getItem(array('products_id' => $item_id, 'customer_type_id' => $key), array('task' => 'filter'));
                        if (!empty($products_price_item)){
                            $priceData['id'] = $products_price_item->id;
                            $this->getServiceLocator()->get('Admin\Model\ProductsPriceTable')->saveItem(array('data' => $priceData), array('task' => 'edit-item'));
                        }
                        else{
                            $this->getServiceLocator()->get('Admin\Model\ProductsPriceTable')->saveItem(array('data' => $priceData), array('task' => 'add-item'));
                        }
                    }

                    # update inventory products
                    foreach ($warehouse as $key => $value) {
                        $inventoryData = array(
                            'products_id'       => $item_id,
                            'warehouse_id'      => $key,
                            'quantity'          => $this->_params['data'][$key.'_'.$item_id.'_quantity'],
                        );
                        $products_inventory_item = $this->getServiceLocator()->get('Admin\Model\ProductsInventoryTable')->getItem(array('products_id' => $item_id, 'warehouse_id' => $key), array('task' => 'filter'));
                        if (!empty($products_inventory_item)){
                            $inventoryData['id'] = $products_inventory_item->id;
                            $this->getServiceLocator()->get('Admin\Model\ProductsInventoryTable')->saveItem(array('data' => $inventoryData), array('task' => 'edit-item'));
                        }
                        else{
                            $this->getServiceLocator()->get('Admin\Model\ProductsInventoryTable')->saveItem(array('data' => $inventoryData), array('task' => 'add-item'));
                        }
                    }

                    $connection->commit();

                    $this->flashMessenger()->addMessage('Cập nhật '.$this->caption.' thành công');

                    if($controlAction == 'save-new') {
                        $this->goRoute(array('action' => 'add'));
                    } else if($controlAction == 'save') {
                        $this->goRoute(array('action' => 'edit', 'id' => $item_id));
                    } else {
                        $this->goRoute();
                    }
                } catch (\Exception $e) {
                    $connection->rollback();
                    throw $e;
                }
            }
        }

        $this->_viewModel['myForm']     = $myForm;
        $this->_viewModel['customer_type']	= $customer_type;
        $this->_viewModel['formProductsPrice']	= $formProductsPrice;
        $this->_viewModel['warehouse']	= $warehouse;
        $this->_viewModel['formProductsInventory']	= $formProductsInventory;
        $this->_viewModel['item']       = $item;
        $this->_viewModel['caption']    = 'Sửa - '.$this->caption;
        return new ViewModel($this->_viewModel);
    }

    public function deleteAction() {
        if($this->getRequest()->isPost()) {
            if(!empty($this->_params['data']['cid'])) {
                $cdata = $this->getTable()->deleteItem($this->_params, array('task' => 'delete-item'));
                $message = 'Xóa '. $cdata .' '.$this->caption.' thành công';
                $this->flashMessenger()->addMessage($message);
            }
        }

        $this->goRoute(array('action' => 'index'));
    }
}

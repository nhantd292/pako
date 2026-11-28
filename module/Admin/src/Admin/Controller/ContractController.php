<?php

namespace Admin\Controller;

use ZendX\Controller\ActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Form\FormInterface;

require_once PATH_VENDOR . '/Fpdi/fpdf/fpdf.php';
require_once PATH_VENDOR . '/Fpdi/autoload.php';

use setasign\Fpdi\Fpdi;

class ContractController extends ActionController
{
    public $caption = 'Đơn hàng';

    public function init()
    {
        // Thiết lập options
        $this->_options['tableName'] = 'Admin\Model\ContractTable';
        $this->_options['formName'] = 'formAdminContract';

        // Thiết lập session filter
        $action = str_replace('-', '_', $this->_params['action']);
        $ssFilter = new Container(__CLASS__ . $action);

        $this->_params['ssFilter']['order_by'] = !empty($ssFilter->order_by) ? $ssFilter->order_by : 'date';
        $this->_params['ssFilter']['order'] = !empty($ssFilter->order) ? $ssFilter->order : 'DESC';
        $this->_params['ssFilter']['filter_keyword'] = $ssFilter->filter_keyword;
        $this->_params['ssFilter']['filter_date_type'] = $ssFilter->filter_date_type;
        $this->_params['ssFilter']['filter_date_begin'] = $ssFilter->filter_date_begin;
        $this->_params['ssFilter']['filter_date_end'] = $ssFilter->filter_date_end;
        $this->_params['ssFilter']['filter_date_type'] = $ssFilter->filter_date_type;
        $this->_params['ssFilter']['filter_sale_branch'] = $ssFilter->filter_sale_branch;
        $this->_params['ssFilter']['filter_sale_group'] = $ssFilter->filter_sale_group;
        $this->_params['ssFilter']['filter_user'] = $ssFilter->filter_user;
        $this->_params['ssFilter']['filter_delivery_id'] = $ssFilter->filter_delivery_id;
        $this->_params['ssFilter']['filter_product'] = $ssFilter->filter_product;
        $this->_params['ssFilter']['filter_bill_code'] = $ssFilter->filter_bill_code;
        $this->_params['ssFilter']['filter_status_type'] = $ssFilter->filter_status_type;
        $this->_params['ssFilter']['filter_status'] = $ssFilter->filter_status;
        $this->_params['ssFilter']['filter_state'] = $ssFilter->filter_state;
        $this->_params['ssFilter']['filter_coincider'] = $ssFilter->filter_coincider;
        $this->_params['ssFilter']['filter_unit_transport'] = $ssFilter->filter_unit_transport;
        $this->_params['ssFilter']['filter_returned'] = $ssFilter->filter_returned;
        $this->_params['ssFilter']['filter_send_ghtk'] = $ssFilter->filter_send_ghtk;
        $this->_params['ssFilter']['filter_category'] = $ssFilter->filter_category;
        $this->_params['ssFilter']['filter_product'] = $ssFilter->filter_product;
        $this->_params['ssFilter']['filter_update_kov_false'] = $ssFilter->filter_update_kov_false;
        $this->_params['ssFilter']['filter_production_type_id'] = $ssFilter->filter_production_type_id;
        $this->_params['ssFilter']['filter_shipper_id'] = $ssFilter->filter_shipper_id;
        $this->_params['ssFilter']['filter_care_status'] = $ssFilter->filter_care_status;
        $this->_params['ssFilter']['filter_marketer_status'] = $ssFilter->filter_marketer_status;
        $this->_params['ssFilter']['filter_marketer_id'] = $ssFilter->filter_marketer_id;
        $this->_params['ssFilter']['filter_inventory_id'] = $ssFilter->filter_inventory_id;
        $this->_params['ssFilter']['filter_customer_id'] = $ssFilter->filter_customer_id;
        $this->_params['ssFilter']['filter_invoice_type'] = $ssFilter->filter_invoice_type;
        $this->_params['ssFilter']['filter_option_vat'] = $ssFilter->filter_option_vat;
        $this->_params['ssFilter']['filter_invoiced'] = $ssFilter->filter_invoiced;
        $this->_params['ssFilter']['filter_status_shipped'] = $ssFilter->filter_status_shipped;
        $this->_params['ssFilter']['filter_fee_type'] = $ssFilter->filter_fee_type;
        $this->_params['ssFilter']['filter_warning_price'] = $ssFilter->filter_warning_price;
        $this->_params['ssFilter']['filter_return_status'] = $ssFilter->filter_return_status;

        // Thiết lập lại thông số phân trang
        $this->_paginator['itemCountPerPage'] = !empty($ssFilter->pagination_option) ? $ssFilter->pagination_option : $this->_paginator['itemCountPerPage'];
        $this->_paginator['currentPageNumber'] = $this->params()->fromRoute('page', 1);
        $this->_params['paginator'] = $this->_paginator;

        // Lấy dữ liệu post của form
        $this->_params['data'] = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());

        // Truyển dữ dữ liệu ra ngoài view
        $this->_viewModel['params'] = $this->_params;
        $this->_viewModel['curent_user'] = $this->_userInfo->getUserInfo();
    }

    // Tìm kiếm
    public function filterAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->_params['data'];

            $action = !empty($this->getRequest()->getPost('filter_action')) ? str_replace('-', '_', $this->getRequest()->getPost('filter_action')) : 'index';
            $ssFilter = new Container(__CLASS__ . $action);

            $ssFilter->pagination_option = intval($data['pagination_option']);
            $ssFilter->order_by = $data['order_by'];
            $ssFilter->order = $data['order'];
            $ssFilter->filter_keyword = $data['filter_keyword'];
            $ssFilter->filter_date_type = $data['filter_date_type'];
            $ssFilter->filter_date_begin = $data['filter_date_begin'];
            $ssFilter->filter_date_end = $data['filter_date_end'];
            $ssFilter->filter_date_type = $data['filter_date_type'];
            $ssFilter->filter_product = $data['filter_product'];
            $ssFilter->filter_status_type = $data['filter_status_type'];
            $ssFilter->filter_status = $data['filter_status'];
            $ssFilter->filter_state = $data['filter_state'];
            $ssFilter->filter_user = $data['filter_user'];
            $ssFilter->filter_delivery_id = $data['filter_delivery_id'];
            $ssFilter->filter_production_type_id = $data['filter_production_type_id'];
            $ssFilter->filter_action = $data['filter_action'];
            $ssFilter->filter_coincider = $data['filter_coincider'];
            $ssFilter->filter_unit_transport = $data['filter_unit_transport'];
            $ssFilter->filter_returned = $data['filter_returned'];
            $ssFilter->filter_send_ghtk = $data['filter_send_ghtk'];
            $ssFilter->filter_category = $data['filter_category'];
            $ssFilter->filter_product = $data['filter_product'];
            $ssFilter->filter_update_kov_false = $data['filter_update_kov_false'];
            $ssFilter->filter_shipper_id = $data['filter_shipper_id'];
            $ssFilter->filter_care_status = $data['filter_care_status'];
            $ssFilter->filter_marketer_status = $data['filter_marketer_status'];
            $ssFilter->filter_marketer_id = $data['filter_marketer_id'];
            $ssFilter->filter_inventory_id = $data['filter_inventory_id'];
            $ssFilter->filter_customer_id = $data['filter_customer_id'];
            $ssFilter->filter_invoice_type = $data['filter_invoice_type'];
            $ssFilter->filter_option_vat = $data['filter_option_vat'];
            $ssFilter->filter_invoiced = $data['filter_invoiced'];
            $ssFilter->filter_status_shipped = $data['filter_status_shipped'];
            $ssFilter->filter_fee_type = $data['filter_fee_type'];
            $ssFilter->filter_warning_price = $data['filter_warning_price'];
            $ssFilter->filter_return_status = $data['filter_return_status'];

            $ssFilter->filter_sale_group = $data['filter_sale_group'];
            if (!empty($data['filter_sale_branch'])) {
                if ($ssFilter->filter_sale_branch != $data['filter_sale_branch']) {
                    $ssFilter->filter_sale_group = null;
                    $ssFilter->filter_sale_branch = $data['filter_sale_branch'];
                }
            } else {
                $ssFilter->filter_sale_group = null;
                $ssFilter->filter_sale_branch = $data['filter_sale_branch'];
            }

            if ($ssFilter['filter_date_type'] == 'date_debt') {
                if (empty($ssFilter->filter_date_begin)) {
                    $ssFilter->filter_date_begin = date('01/m/Y');
                    $ssFilter->filter_date_end = date('t/m/Y');
                }
            }

            if (empty($data['filter_status_type'])) {
                $ssFilter->filter_status = null;
            }
        }
        $action = str_replace('_', '-', $this->getRequest()->getPost('filter_action'));
        $this->goRoute(['action' => $action]);
    }

    // Danh sách đơn hàng sale
    public function indexAction()
    {
        $ssFilter = new Container(__CLASS__ . 'index');
        // Phân quyền view
        $curent_user = $this->_userInfo->getUserInfo();
        $permission_ids = explode(',', $curent_user['permission_ids']);
        if (!in_array(SYSTEM, $permission_ids) && !in_array(ADMIN, $permission_ids) && !in_array(MANAGER, $permission_ids)) {
            if (in_array(GDCN, $permission_ids) || in_array(SALEADMIN, $permission_ids)) {
                $this->_params['ssFilter']['filter_sale_branch'] = $curent_user['sale_branch_id'];
                $ssFilter->filter_sale_branch = $curent_user['sale_branch_id'];
            } elseif (in_array(GROUP_SALES_LEADER, $permission_ids)) {
                $this->_params['ssFilter']['filter_sale_branch'] = $curent_user['sale_branch_id'];
                $this->_params['ssFilter']['filter_sale_group'] = $curent_user['sale_group_id'];
                $ssFilter->filter_sale_branch = $curent_user['sale_branch_id'];
                $ssFilter->filter_sale_group = $curent_user['filter_sale_group'];
            } elseif (in_array(REDUCE_VIETTEL, $permission_ids)) {
                $this->_params['ssFilter']['filter_unit_transport'] = 'viettel';
                $ssFilter->filter_unit_transport = 'viettel';
            }elseif (in_array(ACCOUNTING, $permission_ids)) {
                if (!empty($curent_user['inventory_id'])) {
                    $this->_params['ssFilter']['filter_inventory_id'] = $curent_user['inventory_id'];
                }
            } else {
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
        $this->_params['ssFilter']['filter_sale_branch'] = '1789053110eykiwoue4411';

        $myForm = new \Admin\Form\Search\Contract($this, $this->_params);
        $this->_params['ssFilter']['filter_status_type'] = 'ghtk_status';
        $myForm->setData($this->_params['ssFilter']);
        $user_branch = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->getItem(['id' => $this->_params['ssFilter']['filter_sale_branch']]);

        $this->_viewModel['myForm'] = $myForm;
        $this->_viewModel['items'] = $this->getTable()->listItem($this->_params, array('task' => 'list-item'));
        $this->_viewModel['count'] = $this->getTable()->countItem($this->_params, array('task' => 'list-item'));
        $this->_viewModel['user'] = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['sale_group'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'lists-group')), array('task' => 'cache'));
        $this->_viewModel['sale_branch'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['location_city'] = $this->getServiceLocator()->get('Admin\Model\LocationsTable')->listItem(array('level' => 1), array('task' => 'cache'));
        $this->_viewModel['location_district'] = $this->getServiceLocator()->get('Admin\Model\LocationsTable')->listItem(array('level' => 2), array('task' => 'cache'));
        $this->_viewModel['location_town'] = $this->getServiceLocator()->get('Admin\Model\LocationsTable')->listItem(array('level' => 3), array('task' => 'cache'));
        $this->_viewModel['shippers'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'shipper')), array('task' => 'cache'));
        $this->_viewModel['viettelKeyList'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'viettel-key', 'key_ghtk_ids' => explode(',', $user_branch['key_viettel_ids']))), array('task' => 'list-all'));
        $this->_viewModel['ghtkKeyList'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'ghtk-key', 'key_ghtk_ids' => explode(',', $user_branch['key_ghtk_ids']))), array('task' => 'list-all'));
        $this->_viewModel['ghnKeyList'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'ghn-key', 'key_ghn_ids' => explode(',', $user_branch['key_ghn_ids']))), array('task' => 'list-all'));
//        $this->_viewModel['viettelKeyList'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'viettel-key')), array('task' => 'list-all'));
//        $this->_viewModel['ghtkKeyList'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'ghtk-key')), array('task' => 'list-all'));
//        $this->_viewModel['ghnKeyList'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'ghn-key')), array('task' => 'list-all'));

        $this->_viewModel['status_check'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'ghtk-status')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['status_check_ghn'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'ghn-status')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['status_check_vtp'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'viettel-status')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['status_accounting'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'status-acounting')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['status_sales'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'status')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['order_status'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'orders-state')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['fee_type'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'fee-type')), array('task' => 'cache-alias'));
        $this->_viewModel['fee_type_list'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'fee-type')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['caption'] = 'Danh sách - ' . $this->caption;

        return new ViewModel($this->_viewModel);
    }

    // Xem chi tiết Đơn hàng
    public function detailAction()
    {
        $id = $this->params('id');
        if ($id) {
            $connection = $this->getConnection();
            $item = $this->getTable()->getItem(array('id' => $id));
            $debt_item = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->getItem(array('orders_id' => $id), array('task' => 'type-id'));
        } else {
            return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
        }
        $permission_ids = explode(',', $this->_userInfo->getUserInfo('permission_ids'));
        $uid = $this->_userInfo->getUserInfo('id');

        if ($this->getRequest()->isPost()) {
            $control_action = $this->_params['data']['control-action'];
            if ($control_action == PROCESSING_STATUS) {
                if ($item['state'] == NEW_STATUS) {
                    $connection->beginTransaction();
                    $this->getTable()->saveItem(array('data' => array('id' => $id, 'state' => PROCESSING_STATUS)), array('task' => 'update-state'));

                    # cập nhật trạng thái phiếu thu
                    $debt_item_old = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->getItem(array('orders_id' => $id), array('task' => 'type-id'));
                    $data_debt = array(
                        'id' => $debt_item_old->id,
                        'state' => PROCESSING_STATUS,
                    );
                    $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                    $connection->commit();
                    $this->flashMessenger()->addSuccessMessage('Đơn hàng chuyển sang trạng thái "ĐANG XỬ LÝ"');
                } else {
                    $this->flashMessenger()->addErrorMessage('Chỉ có thể XỬ LÝ đơn hàng khi đơn hàng ở trạng thái phiếu tạm!');
                }
            }
            if ($control_action == CANCEL_STATUS) {
                if (($item['state'] == NEW_STATUS && $item['created_by'] == $uid) || ($item['state'] == NEW_STATUS && (in_array(SYSTEM, $permission_ids) || in_array(ADMIN, $permission_ids)))) {
                    ##### begin #####
                    $connection->beginTransaction();
                    # cập nhật trạng thái hủy cho đơn hàng.
                    $this->getTable()->saveItem(array('data' => array('id' => $id, 'state' => CANCEL_STATUS)), array('task' => 'update-state'));

                    # Sửa phiếu thu chi khách hàng
                    $debt_item_old = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->getItem(array('orders_id' => $id), array('task' => 'type-id'));
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
                } else {
                    $this->flashMessenger()->addErrorMessage('Chỉ có thể Hủy đơn hàng khi đơn hàng ở trạng thái phiếu tạm!');
                }
            }
            if ($control_action == COMPLETE_STATUS) {
                if ((($item['state'] == DELIVERING_STATUS) && $item['created_by'] == $uid) || in_array(SYSTEM, $permission_ids) || in_array(ADMIN, $permission_ids)) {
                    ##### begin #####
                    $connection->beginTransaction();
                    # cập nhật trạng thái hoàn thành cho đơn hàng.
                    $this->getTable()->saveItem(array('data' => array('id' => $id, 'state' => COMPLETE_STATUS)), array('task' => 'update-state'));

//                    # cập nhật tồn kho cho sản phẩm.
//                    $products_detail = $this->getServiceLocator()->get('Admin\Model\ContractDetailTable')->listItem(array('contract_id' => $id), array('task' => 'list-ajax'));
//                    foreach ($products_detail as $detail_item) {
//                        $inventory = $this->getServiceLocator()->get('Admin\Model\ProductsInventoryTable')->getItem(array('products_id' => $detail_item->product_id, 'warehouse_id' => $item['inventory_id']), array('task' => 'filter'));
//                        $quantity_new = $inventory->quantity - $detail_item->numbers;
//                        if ($quantity_new < 0) {
//                            $this->flashMessenger()->addErrorMessage('Số lượng sản phẩm "' . $inventory->products_name . '" trong kho "' . $inventory->warehouse_name . '" không đủ!');
//                            $this->goRoute(array('action' => 'detail', 'id' => $id));
//                            return false;
//                        }
//
//                        $this->getServiceLocator()->get('Admin\Model\ProductsInventoryTable')->saveItem(array('data' => array('quantity' => $quantity_new, 'id' => $inventory->id)), array('task' => 'edit-item'));
//                    }

                    # Sửa phiếu thu chi khách hàng
                    $debt_item_old = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->getItem(array('orders_id' => $id), array('task' => 'type-id'));
                    $data_debt = array(
                        'id' => $debt_item_old->id,
                        'state' => COMPLETE_STATUS,
                    );
                    $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                    $connection->commit();
                } else {
                    $this->flashMessenger()->addErrorMessage('Bạn không thể hoàn thành đơn hàng!');
                }
            }
            if ($control_action == RETURN_STATUS) {
                if ($item['return_status'] == 0 && ($item['state'] == DELIVERING_STATUS || $item['state'] == COMPLETE_STATUS) && (in_array(SYSTEM, $permission_ids) || in_array(ADMIN, $permission_ids) || in_array(ACCOUNTING, $permission_ids))) {
                    ##### begin #####
                    $connection->beginTransaction();
                    # cập nhật trạng thái hoàn cho đơn hàng.
                    $this->getTable()->saveItem(array('data' => array('id' => $id, 'state' => RETURN_STATUS)), array('task' => 'update-state'));
                    $this->getTable()->saveItem(array('data' => array('id' => $id)), array('task' => 'update-date-return'));

                    # cập nhật tồn kho cho sản phẩm.
                    $products_detail = $this->getServiceLocator()->get('Admin\Model\ContractDetailTable')->listItem(array('contract_id' => $id), array('task' => 'list-ajax'));
                    foreach ($products_detail as $detail_item) {
                        $inventory = $this->getServiceLocator()->get('Admin\Model\ProductsInventoryTable')->getItem(array('products_id' => $detail_item->product_id, 'warehouse_id' => $item['inventory_id']), array('task' => 'filter'));
                        $quantity_new = $inventory->quantity + $detail_item->numbers;
                        $this->getServiceLocator()->get('Admin\Model\ProductsInventoryTable')->saveItem(array('data' => array('quantity' => $quantity_new, 'id' => $inventory->id)), array('task' => 'edit-item'));
                    }

                    # Sửa phiếu thu chi khách hàng
                    $debt_item_old = $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->getItem(array('orders_id' => $id), array('task' => 'type-id'));

                    $paid_cash = $item['paid_cash'];
                    $paid_transfer = $item['paid_transfer'];

                    $discount = 0;
                    $price_total = 0;
                    $new_debt = $debt_item_old->old_debt - ($discount + $paid_cash + $paid_transfer - $price_total);

                    $data_debt = array(
                        'id' => $debt_item_old->id,
                        'price_total' => $price_total,
                        'discount' => $discount,
                        'paid_cash' => $paid_cash,
                        'paid_transfer' => $paid_transfer,
                        'new_debt' => $new_debt,
                        'state' => RETURN_STATUS,
                    );

                    $this->getServiceLocator()->get('Admin\Model\CustomerDebtTable')->saveItem(array('data' => $data_debt, 'item' => $debt_item_old), array('task' => 'edit-item'));

                    $connection->commit();
                }
                else {
                    $this->flashMessenger()->addErrorMessage('Bạn không thể hoàn thành đơn hàng! đơn hàng phải ở trạng thái Đang giao hàng/Hoàn thành và chưa có hàng trả lại 1 phần mới có thể hoàn hàng.');
                }
            }

            $item = $this->getTable()->getItem(array('id' => $id));

        }

        $this->_viewModel['item'] = $item;
        $this->_viewModel['permission_ids'] = $permission_ids;
        $this->_viewModel['uid'] = $uid;
        $this->_viewModel['debt_item'] = $debt_item;
        $this->_viewModel['contact'] = $this->getServiceLocator()->get('Admin\Model\ContactTable')->getItem(array('id' => $item['contact_id']));
        $this->_viewModel['user'] = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['customer_type'] = $this->getServiceLocator()->get('Admin\Model\CustomerTypeTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['warehouse'] = $this->getServiceLocator()->get('Admin\Model\WarehouseTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['sale_group'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'lists-group')), array('task' => 'cache'));
        $this->_viewModel['sale_branch'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['location_city'] = $this->getServiceLocator()->get('Admin\Model\LocationsTable')->listItem(array('level' => 1), array('task' => 'cache'));
        $this->_viewModel['location_district'] = $this->getServiceLocator()->get('Admin\Model\LocationsTable')->listItem(array('level' => 2), array('task' => 'cache'));
        $this->_viewModel['location_town'] = $this->getServiceLocator()->get('Admin\Model\LocationsTable')->listItem(array('level' => 3), array('task' => 'cache'));
        $this->_viewModel['sex'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sex')), array('task' => 'cache-alias'));
        $this->_viewModel['fee_other_type'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'fee-other-type')), array('task' => 'cache-alias'));
        $this->_viewModel['status'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'status')), array('task' => 'cache'));
        $this->_viewModel['invoice_type'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'invoice-type')), array('task' => 'cache-alias'));
        $this->_viewModel['fee_type'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'fee-type')), array('task' => 'cache-alias'));
        $this->_viewModel['order_status'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'orders-state')), array('task' => 'cache')), array('key' => 'alias', 'value' => 'object'));
        $this->_viewModel['production_type'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'production-type')), array('task' => 'cache')), array('key' => 'id', 'value' => 'object'));
        $this->_viewModel['caption'] = 'Chi tiết - ' . $this->caption . ' - ' . $item['code'];
        $viewModel = new ViewModel($this->_viewModel);
        return $viewModel;
    }
}



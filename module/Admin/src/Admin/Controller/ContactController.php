<?php

namespace Admin\Controller;

use kcfinder\zipFolder;
use ZendX\Controller\ActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Form\FormInterface;

class ContactController extends ActionController
{

    public function init()
    {
        // Thiết lập options
        $this->_options['tableName'] = 'Admin\Model\ContactTable';
        $this->_options['formName']  = 'formAdminContact';

        // Thiết lập session filter
        $action = str_replace('-', '_', $this->_params['action']);
        $ssFilter = new Container(__CLASS__. $action);

        $this->_params['ssFilter']['order_by']                 = !empty($ssFilter->order_by) ? $ssFilter->order_by : 'date';
        $this->_params['ssFilter']['order']                    = !empty($ssFilter->order) ? $ssFilter->order : 'DESC';
        $this->_params['ssFilter']['filter_type']              = $ssFilter->filter_type;
        $this->_params['ssFilter']['filter_keyword']           = $ssFilter->filter_keyword;
        $this->_params['ssFilter']['filter_date_begin']        = $ssFilter->filter_date_begin;
        $this->_params['ssFilter']['filter_date_end']          = $ssFilter->filter_date_end;
        $this->_params['ssFilter']['filter_date_type']         = $ssFilter->filter_date_type;
        $this->_params['ssFilter']['filter_sale_branch']       = $ssFilter->filter_sale_branch;
        $this->_params['ssFilter']['filter_sale_group']        = $ssFilter->filter_sale_group;
        $this->_params['ssFilter']['filter_user']              = $ssFilter->filter_user;
        $this->_params['ssFilter']['filter_marketer_id']       = $ssFilter->filter_marketer_id;
        $this->_params['ssFilter']['filter_source_group']      = $ssFilter->filter_source_group;
        $this->_params['ssFilter']['filter_history_result']    = $ssFilter->filter_history_result;
        $this->_params['ssFilter']['filter_contact_type']      = $ssFilter->filter_contact_type;
        $this->_params['ssFilter']['filter_product_interest']  = $ssFilter->filter_product_interest;
        $this->_params['ssFilter']['filter_last_action']       = $ssFilter->filter_last_action;
        $this->_params['ssFilter']['filter_location_city']     = $ssFilter->filter_location_city;
        $this->_params['ssFilter']['filter_location_district'] = $ssFilter->filter_location_district;
        $this->_params['ssFilter']['filter_history_status']    = $ssFilter->filter_history_status;
        $this->_params['ssFilter']['filter_number_contract']   = $ssFilter->filter_number_contract;
        $this->_params['ssFilter']['filter_number_contract2']  = $ssFilter->filter_number_contract2;
        $this->_params['ssFilter']['filter_product_group_id']  = $ssFilter->filter_product_group_id;
        $this->_params['ssFilter']['filter_history_type_id']   = $ssFilter->filter_history_type_id;
        $this->_params['ssFilter']['filter_customer_type']     = $ssFilter->filter_customer_type;

        // Thiết lập lại thông số phân trang
        $this->_paginator['itemCountPerPage']  = !empty($ssFilter->pagination_option) ? $ssFilter->pagination_option : $this->_paginator['itemCountPerPage'];
        $this->_paginator['currentPageNumber'] = $this->params()->fromRoute('page', 1);
        $this->_params['paginator']            = $this->_paginator;

        // Lấy dữ liệu post của form
        $this->_params['data'] = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());

        // Truyển dữ dữ liệu ra ngoài view
        $this->_viewModel['params'] = $this->_params;
        $this->_viewModel['curent_user']  = $this->_userInfo->getUserInfo();
//        $this->_viewModel['encode_phone'] = \ZendX\Functions\CreateArray::create($this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'encode-phone')), array('task' => 'list-all')), array('key' => 'content', 'value' => 'status'));
    }

    public function filterAction()
    {
//        $ssFilter = new Container(__CLASS__);
        $data     = $this->_params['data'];

        $action = !empty($this->getRequest()->getPost('filter_action')) ? str_replace('-', '_', $this->getRequest()->getPost('filter_action')) : 'index';
        $ssFilter	= new Container(__CLASS__ . $action);


        if ($this->getRequest()->isPost()) {

            $ssFilter->pagination_option        = intval($data['pagination_option']);
            $ssFilter->order_by                 = $data['order_by'];
            $ssFilter->order                    = $data['order'];
            $ssFilter->filter_type              = $data['filter_type'];
            $ssFilter->filter_keyword           = $data['filter_keyword'];
            $ssFilter->filter_date_begin        = $data['filter_date_begin'];
            $ssFilter->filter_date_end          = $data['filter_date_end'];
            $ssFilter->filter_date_type         = $data['filter_date_type'];
            $ssFilter->filter_source_group      = $data['filter_source_group'];
            $ssFilter->filter_history_result    = $data['filter_history_result'];
            $ssFilter->filter_contact_type      = $data['filter_contact_type'];
            $ssFilter->filter_product_interest  = $data['filter_product_interest'];
            $ssFilter->filter_last_action       = $data['filter_last_action'];
            $ssFilter->filter_location_city     = $data['filter_location_city'];
            $ssFilter->filter_location_district = $data['filter_location_district'];
            $ssFilter->filter_history_status    = $data['filter_history_status'];
            $ssFilter->filter_number_contract   = $data['filter_number_contract'];
            $ssFilter->filter_number_contract2   = $data['filter_number_contract2'];
            $ssFilter->filter_product_group_id   = $data['filter_product_group_id'];
            $ssFilter->filter_history_type_id   = $data['filter_history_type_id'];

            $ssFilter->filter_user              = $data['filter_user'];
            $ssFilter->filter_marketer_id       = $data['filter_marketer_id'];
            $ssFilter->filter_sale_group        = $data['filter_sale_group'];
            $ssFilter->filter_customer_type     = $data['filter_customer_type'];

            if (!empty($data['filter_sale_branch'])) {
                if ($ssFilter->filter_sale_branch != $data['filter_sale_branch']) {
                    $ssFilter->filter_sale_group  = null;
                    $ssFilter->filter_sale_branch = $data['filter_sale_branch'];
                }
            }
            else {
                $ssFilter->filter_sale_group  = null;
                $ssFilter->filter_sale_branch = $data['filter_sale_branch'];
            }
        }

        if ($this->_params['route']['type'] == 'history-return') {
            $ssFilter->filter_date_begin     = date('d/m/Y');
            $ssFilter->filter_date_end       = date('d/m/Y');
            $ssFilter->filter_date_type      = 'history_return';
            $ssFilter->filter_history_status = '';
        }

        if ($this->_params['route']['type'] == 'history-status') {
            $ssFilter->filter_date_begin     = '';
            $ssFilter->filter_date_end       = '';
            $ssFilter->filter_date_type      = '';
            $ssFilter->filter_history_status = 'no';
        }

        $this->goRoute(['action' => $action]);
    }

    public function indexAction()
    {
        $ssFilter = new Container(__CLASS__.'index');
        $user_id = $this->_userInfo->getUserInfo('id');
        if(!empty($this->_params['route']['nid'])) {
            $id_item = $this->_params['route']['nid'];
            $notifyTable = $this->getServiceLocator()->get('Notifycation\Model\NotifyTable');
            $notify_item = $notifyTable->getItem(array("id" => $id_item), null);
            if($this->_params['route']['type'] == 'phone'){
                $notify_options = unserialize($notify_item['options']);
                $this->_params['ssFilter']['filter_keyword'] = $notify_options['phone'];
            }
            // Nếu chưa có trong ds user đã đọc
            if (strpos($notify_item['user_readed'], $user_id) === false) {
                $this->_params['item'] = $notify_item;
                $this->_params['data']['user_id'] = $user_id;
                $notifyTable->saveItem($this->_params, array('task' => 'update-readed'));
            }
        }

        // Phân quyền view
        $curent_user = $this->_userInfo->getUserInfo();
        $permission_ids = explode(',', $curent_user['permission_ids']);
        if(!in_array(SYSTEM, $permission_ids) && !in_array(ADMIN, $permission_ids) && !in_array(MANAGER, $permission_ids) && !in_array(ACCOUNTING, $permission_ids)){
            if(in_array(GDCN, $permission_ids) || in_array(SALEADMIN, $permission_ids)){
                $this->_params['ssFilter']['filter_sale_branch'] = $curent_user['sale_branch_id'];
                $ssFilter->filter_sale_branch = $curent_user['sale_branch_id'];
            }
            elseif (in_array(GROUP_SALES_LEADER, $permission_ids)){
                $this->_params['ssFilter']['filter_sale_branch'] = $curent_user['sale_branch_id'];
                $this->_params['ssFilter']['filter_sale_group'] = $curent_user['sale_group_id'];
                $ssFilter->filter_sale_branch = $curent_user['sale_branch_id'];
                $ssFilter->filter_sale_group = $curent_user['filter_sale_group'];
            }
            else{
                $this->_params['ssFilter']['filter_user'] = $curent_user['id'];
            }
        }

        $ssFilter                    = new Container(__CLASS__);
        $ssFilter->currentPageNumber = $this->_paginator['currentPageNumber'];

        $myForm = new \Admin\Form\Search\Contact($this->getServiceLocator(), $this->_params['ssFilter']);
        $myForm->setData($this->_params['ssFilter']);

        $items = $this->getTable()->listItem($this->_params, array('task' => 'list-item'));

        $fdata['ssFilter']['filter_active'] = 'unactive';
        $fdata['ssFilter']['filter_sale_branch'] = $curent_user['sale_branch_id'];
        $fdata['ssFilter']['filter_cancel_share'] = 0;
        $this->_viewModel['count_form_data']     = $this->getServiceLocator()->get('Admin\Model\FormDataTable')->countItem($fdata, array('task' => 'list-item-contact'));

        $this->_viewModel['myForm']              = $myForm;
        $this->_viewModel['items']               = $items;
        $this->_viewModel['count']               = $this->getTable()->countItem($this->_params, array('task' => 'list-item'));
        $this->_viewModel['user']                = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['sale_group']          = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'lists-group')), array('task' => 'cache'));
        $this->_viewModel['sale_branch']         = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['userInfo']            = $this->_userInfo->getUserInfo();
        $this->_viewModel['product_group']       = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'product-group')), array('task' => 'cache'));
        $this->_viewModel['sale_history_action'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-history-action')), array('task' => 'cache'));
        $this->_viewModel['sale_history_result'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-history-result')), array('task' => 'cache'));
        $this->_viewModel['sale_history_type']   = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-history-type')), array('task' => 'cache'));


        $this->_viewModel['locations']              = $this->getServiceLocator()->get('Admin\Model\LocationsTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['customer_type']          = $this->getServiceLocator()->get('Admin\Model\CustomerTypeTable')->listItem(null, array('task' => 'cache'));


        $this->_viewModel['caption']             = 'Liên hệ - Danh sách';
        return new ViewModel($this->_viewModel);
    }

    public function viewAction()
    {
        $item = $this->getTable()->getItem(array('id' => $this->_params['data']['id']));
        if (empty($item)) {
            return $this->redirect()->toRoute('routeAdmin/default', array('controller' => 'notice', 'action' => 'not-found'));
        }

        $this->_viewModel['item']                 = $item;
        $this->_viewModel['user']                 = $this->getServiceLocator()->get('Admin\Model\UserTable')->listItem(null, array('task' => 'cache'));
        $this->_viewModel['sale_group']           = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'lists-group')), array('task' => 'cache'));
        $this->_viewModel['sale_branch']          = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-branch')), array('task' => 'cache'));
        $this->_viewModel['sale_history_action']  = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-history-action')), array('task' => 'cache'));
        $this->_viewModel['sale_history_result']  = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-history-result')), array('task' => 'cache'));
        $this->_viewModel['sale_source_group']    = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-source-group')), array('task' => 'cache'));
        $this->_viewModel['sale_contact_type']    = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-contact-type')), array('task' => 'cache-alias'));
        $this->_viewModel['sale_lost']            = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-lost')), array('task' => 'cache'));
        $this->_viewModel['sale_contact_subject'] = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sale-contact-subject')), array('task' => 'cache-alias'));
        $this->_viewModel['location_city']        = $this->getServiceLocator()->get('Admin\Model\LocationsTable')->listItem(array('level' => 1), array('task' => 'cache'));
        $this->_viewModel['location_district']    = $this->getServiceLocator()->get('Admin\Model\LocationsTable')->listItem(array('level' => 2), array('task' => 'cache'));
        $this->_viewModel['product_interest']     = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'product-interest')), array('task' => 'cache'));
        $this->_viewModel['sex']                  = $this->getServiceLocator()->get('Admin\Model\DocumentTable')->listItem(array('where' => array('code' => 'sex')), array('task' => 'cache-alias'));
        $this->_viewModel['caption']              = 'Xem thông tin chi tiết khách hàng';

        $viewModel = new ViewModel($this->_viewModel);
        $viewModel->setTerminal(true);

        return $viewModel;
    }
}

















<?php
namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class CustomerDebtTable extends DefaultTable {

    public function countItem($arrParam = null, $options = null){
	    if($options['task'] == 'list-item') {
	        $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $ssFilter  = $arrParam['ssFilter'];
                $date       = new \ZendX\Functions\Date();

                $select -> join(TABLE_CONTACT, TABLE_CONTACT .'.id = '. TABLE_CUSTOMER_DEBT .'.customer_id', array( 'customer_name' => 'name', 'customer_phone' => 'phone'), 'inner')
                    -> join(TABLE_CONTRACT, TABLE_CONTRACT .'.id = '. TABLE_CUSTOMER_DEBT .'.orders_id', array( 'orders_code' => 'code', 'orders_id' => 'id'), 'left')
                    -> join(TABLE_WAREHOUSE, TABLE_WAREHOUSE .'.id = '. TABLE_CUSTOMER_DEBT .'.inventory_id', array( 'warehouse_name' => 'name'), 'inner')
                    -> join(TABLE_ORDERS_RETURN, TABLE_ORDERS_RETURN .'.id = '. TABLE_CUSTOMER_DEBT .'.orders_return_id', array( 'orders_return_code' => 'code', 'orders_return_id' => 'id'), 'left')
                    -> join(TABLE_WAREHOUSE_INPUT, TABLE_WAREHOUSE_INPUT .'.id = '. TABLE_CUSTOMER_DEBT .'.warehouse_input_id', array( 'warehouse_input_code' => 'code', 'warehouse_input_id' => 'id'), 'left')
                    -> join(TABLE_WAREHOUSE_OUTPUT, TABLE_WAREHOUSE_OUTPUT .'.id = '. TABLE_CUSTOMER_DEBT .'.warehouse_output_id', array( 'warehouse_output_code' => 'code', 'warehouse_output_id' => 'id'), 'left');

                if(!empty($ssFilter['filter_date_begin']) && !empty($ssFilter['filter_date_end'])) {
                    $select -> where -> NEST
                        -> greaterThanOrEqualTo(TABLE_CUSTOMER_DEBT .'.created', $date->formatToData($ssFilter['filter_date_begin']))
                        ->AND
                        -> lessThanOrEqualTo(TABLE_CUSTOMER_DEBT .'.created', $date->formatToData($ssFilter['filter_date_end']. ' 23:59:59') )
                        -> UNNEST;
                } elseif (!empty($ssFilter['filter_date_begin'])) {
                    $select->where->greaterThanOrEqualTo(TABLE_CUSTOMER_DEBT .'.created', $date->formatToData($ssFilter['filter_date_begin']));
                } elseif (!empty($ssFilter['filter_date_end'])) {
                    $select->where->lessThanOrEqualTo(TABLE_CUSTOMER_DEBT .'.created', $date->formatToData($ssFilter['filter_date_end']. ' 23:59:59') );
                }

                if(isset($ssFilter['filter_state']) && $ssFilter['filter_state'] != '') {
                    $select->where->equalTo(TABLE_CUSTOMER_DEBT.'.state', $ssFilter['filter_state']);
                }
                else{
                    $select->where->notEqualTo(TABLE_CUSTOMER_DEBT.'.state', CANCEL_STATUS);
                }

                if(isset($ssFilter['filter_accept']) && $ssFilter['filter_accept'] != '') {
                    $select -> where -> NEST
                        -> equalTo(TABLE_CUSTOMER_DEBT.'.accept', $ssFilter['filter_accept'])
                        -> equalTo(TABLE_CUSTOMER_DEBT.'.state', COMPLETE_STATUS)
                        ->And
                        -> NEST
                        -> NotEqualTo(TABLE_CUSTOMER_DEBT.'.paid_cash', 0)
                        ->Or
                        -> NotEqualTo(TABLE_CUSTOMER_DEBT.'.paid_transfer', 0)
                        -> UNNEST
                        -> UNNEST;
                }

                if(isset($ssFilter['filter_type']) && $ssFilter['filter_type'] != '') {
                    $select->where->equalTo(TABLE_CUSTOMER_DEBT.'.type', $ssFilter['filter_type']);
                }

                if(isset($ssFilter['filter_category']) && $ssFilter['filter_category'] != '') {
                    $select->where->equalTo(TABLE_CUSTOMER_DEBT.'.category', $ssFilter['filter_category']);
                }

                if(isset($ssFilter['filter_customer_id']) && $ssFilter['filter_customer_id'] != '') {
                    $select->where->equalTo(TABLE_CUSTOMER_DEBT.'.customer_id', $ssFilter['filter_customer_id']);
                }

                if(isset($ssFilter['filter_inventory_id']) && $ssFilter['filter_inventory_id'] != '') {
                    $select->where->equalTo(TABLE_CUSTOMER_DEBT.'.inventory_id', $ssFilter['filter_inventory_id']);
                }

//                if(isset($ssFilter['filter_user']) && $ssFilter['filter_user'] != '') {
//                    $select->where->equalTo(TABLE_CUSTOMER_DEBT.'.created_by', $ssFilter['filter_user']);
//                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $select->where->NEST
                        ->like(TABLE_CUSTOMER_DEBT.'.code', '%'. $ssFilter['filter_keyword'] . '%')
                        ->UNNEST;
                }

                if(!empty($ssFilter['filter_user'])) {
                    $select -> where -> NEST
                        -> equalTo(TABLE_CONTACT .'.user_id', $ssFilter['filter_user'])
                        ->Or
                        -> equalTo(TABLE_CUSTOMER_DEBT .'.created_by', $ssFilter['filter_user'])
                        ->Or
                        -> like(TABLE_CONTACT.'.user_ids', "%{$ssFilter['filter_user']}%")
                        -> UNNEST;
                }
            })->count();
	    }
	    
	    return $result;
	}
	
	public function listItem($arrParam = null, $options = null){

		if($options['task'] == 'list-item') {
			$result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $paginator = $arrParam['paginator'];
                $ssFilter  = $arrParam['ssFilter'];
                $date       = new \ZendX\Functions\Date();
                $number     = new \ZendX\Functions\Number();

                if(!isset($options['paginator']) || $options['paginator'] == true) {
                    $select -> limit($paginator['itemCountPerPage'])
                        -> offset(($paginator['currentPageNumber'] - 1) * $paginator['itemCountPerPage']);
                }

                $select -> order(array('created' => 'DESC')); // lưu ý không được thay đổi

                $select -> join(TABLE_CONTACT, TABLE_CONTACT .'.id = '. TABLE_CUSTOMER_DEBT .'.customer_id', array( 'customer_name' => 'name', 'customer_phone' => 'phone'), 'inner')
                        -> join(TABLE_CONTRACT, TABLE_CONTRACT .'.id = '. TABLE_CUSTOMER_DEBT .'.orders_id', array( 'orders_code' => 'code', 'orders_id' => 'id'), 'left')
                        -> join(TABLE_WAREHOUSE, TABLE_WAREHOUSE .'.id = '. TABLE_CUSTOMER_DEBT .'.inventory_id', array( 'warehouse_name' => 'name'), 'inner')
                        -> join(TABLE_ORDERS_RETURN, TABLE_ORDERS_RETURN .'.id = '. TABLE_CUSTOMER_DEBT .'.orders_return_id', array( 'orders_return_code' => 'code', 'orders_return_id' => 'id'), 'left')
                        -> join(TABLE_WAREHOUSE_INPUT, TABLE_WAREHOUSE_INPUT .'.id = '. TABLE_CUSTOMER_DEBT .'.warehouse_input_id', array( 'warehouse_input_code' => 'code', 'warehouse_input_id' => 'id'), 'left')
                        -> join(TABLE_WAREHOUSE_OUTPUT, TABLE_WAREHOUSE_OUTPUT .'.id = '. TABLE_CUSTOMER_DEBT .'.warehouse_output_id', array( 'warehouse_output_code' => 'code', 'warehouse_output_id' => 'id'), 'left');

                if(!empty($ssFilter['filter_date_begin']) && !empty($ssFilter['filter_date_end'])) {
                    $select -> where -> NEST
                        -> greaterThanOrEqualTo(TABLE_CUSTOMER_DEBT .'.created', $date->formatToData($ssFilter['filter_date_begin']))
                        ->AND
                        -> lessThanOrEqualTo(TABLE_CUSTOMER_DEBT .'.created', $date->formatToData($ssFilter['filter_date_end']. ' 23:59:59') )
                        -> UNNEST;
                } elseif (!empty($ssFilter['filter_date_begin'])) {
                    $select->where->greaterThanOrEqualTo(TABLE_CUSTOMER_DEBT .'.created', $date->formatToData($ssFilter['filter_date_begin']));
                } elseif (!empty($ssFilter['filter_date_end'])) {
                    $select->where->lessThanOrEqualTo(TABLE_CUSTOMER_DEBT .'.created', $date->formatToData($ssFilter['filter_date_end']. ' 23:59:59') );
                }

    			if(isset($ssFilter['filter_state']) && $ssFilter['filter_state'] != '') {
    			    $select->where->equalTo(TABLE_CUSTOMER_DEBT.'.state', $ssFilter['filter_state']);
    			}
    			else{
                    $select->where->notEqualTo(TABLE_CUSTOMER_DEBT.'.state', CANCEL_STATUS);
                }

                if(isset($ssFilter['filter_accept']) && $ssFilter['filter_accept'] != '') {
                    $select -> where -> NEST
                        -> equalTo(TABLE_CUSTOMER_DEBT.'.accept', $ssFilter['filter_accept'])
                        -> equalTo(TABLE_CUSTOMER_DEBT.'.state', COMPLETE_STATUS)
                        ->And
                            -> NEST
                            -> NotEqualTo(TABLE_CUSTOMER_DEBT.'.paid_cash', 0)
                            ->Or
                            -> NotEqualTo(TABLE_CUSTOMER_DEBT.'.paid_transfer', 0)
                            -> UNNEST
                        -> UNNEST;
                }

    			if(isset($ssFilter['filter_type']) && $ssFilter['filter_type'] != '') {
    			    $select->where->equalTo(TABLE_CUSTOMER_DEBT.'.type', $ssFilter['filter_type']);
    			}

    			if(isset($ssFilter['filter_category']) && $ssFilter['filter_category'] != '') {
    			    $select->where->equalTo(TABLE_CUSTOMER_DEBT.'.category', $ssFilter['filter_category']);
    			}

                if(isset($ssFilter['filter_customer_id']) && $ssFilter['filter_customer_id'] != '') {
                    $select->where->equalTo(TABLE_CUSTOMER_DEBT.'.customer_id', $ssFilter['filter_customer_id']);
                }

                if(isset($ssFilter['filter_inventory_id']) && $ssFilter['filter_inventory_id'] != '') {
                    $select->where->equalTo(TABLE_CUSTOMER_DEBT.'.inventory_id', $ssFilter['filter_inventory_id']);
                }

//                if(isset($ssFilter['filter_user']) && $ssFilter['filter_user'] != '') {
//                    $select->where->equalTo(TABLE_CUSTOMER_DEBT.'.created_by', $ssFilter['filter_user']);
//                }
    			
    			if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
    		        $select->where->NEST
                			      ->like(TABLE_CUSTOMER_DEBT.'.code', '%'. $ssFilter['filter_keyword'] . '%')
                			      ->UNNEST;
    			}

                if(!empty($ssFilter['filter_user'])) {
                    $select -> where -> NEST
                        -> equalTo(TABLE_CONTACT .'.user_id', $ssFilter['filter_user'])
                        ->Or
                        -> equalTo(TABLE_CUSTOMER_DEBT .'.created_by', $ssFilter['filter_user'])
                        ->Or
                        -> like(TABLE_CONTACT.'.user_ids', "%{$ssFilter['filter_user']}%")
                        -> UNNEST;
                }
    		});
		}

		if($options['task'] == 'list-update') {
			$result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){

                $select -> order(array('created' => 'ASC'));

    			if(isset($arrParam['created']) && $arrParam['created'] != '') {
                    $select->where->greaterThan('created', $arrParam['created']);
    			}

                if(isset($arrParam['customer_id']) && $arrParam['customer_id'] != '') {
                    $select->where->equalTo('customer_id', $arrParam['customer_id']);
                }
    		});
		}
		
	    if($options['task'] == 'cache') {
	        $cache = $this->getServiceLocator()->get('cache');
	        $cache_key = 'CustomerType';
	        $result = $cache->getItem($cache_key);
	         
	        if (empty($result)) {
	            $items	= $this->tableGateway->select(function (Select $select) use ($arrParam){
	                $select->order(array('ordering' => 'ASC', 'name' => 'ASC'));
	            });
                $result = \ZendX\Functions\CreateArray::create($items, array('key' => 'id', 'value' => 'object'));
                 
                $cache->setItem($cache_key, $result);
	        }
	    }
	    
		return $result;
	}
	
	public function getItem($arrParam = null, $options = null){
	
		if($options == null) {
			$result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
			    $select -> where -> equalTo('id', $arrParam['id']);
    		})->current();
		}

        if($options['task'] == 'code') {
			$result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
			    $select -> where -> equalTo('code', $arrParam['code']);
                if(!empty($arrParam['status'])) {
                    $select -> where -> equalTo('status', $arrParam['status']);
                }

    		})->current();
		}

        if($options['task'] == 'type-id') {
			$result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
//                $select -> join(TABLE_CONTACT, TABLE_CONTACT .'.id = '. TABLE_CUSTOMER_DEBT .'.customer_id', array( 'customer_name' => 'name', 'customer_phone' => 'phone', 'customer_type_id'), 'inner');
                $select -> join(TABLE_CONTACT, TABLE_CONTACT .'.id = '. TABLE_CUSTOMER_DEBT .'.customer_id', array( 'customer_name' => 'name', 'customer_phone' => 'phone', 'customer_type_id'), 'inner')
                    -> join(TABLE_CONTRACT, TABLE_CONTRACT .'.id = '. TABLE_CUSTOMER_DEBT .'.orders_id', array( 'orders_code' => 'code', 'orders_id' => 'id'), 'left')
                    -> join(TABLE_WAREHOUSE, TABLE_WAREHOUSE .'.id = '. TABLE_CUSTOMER_DEBT .'.inventory_id', array( 'warehouse_name' => 'name'), 'inner')
                    -> join(TABLE_ORDERS_RETURN, TABLE_ORDERS_RETURN .'.id = '. TABLE_CUSTOMER_DEBT .'.orders_return_id', array( 'orders_return_code' => 'code', 'orders_return_id' => 'id'), 'left')
                    -> join(TABLE_WAREHOUSE_INPUT, TABLE_WAREHOUSE_INPUT .'.id = '. TABLE_CUSTOMER_DEBT .'.warehouse_input_id', array( 'warehouse_input_code' => 'code', 'warehouse_input_id' => 'id'), 'left')
                    -> join(TABLE_WAREHOUSE_OUTPUT, TABLE_WAREHOUSE_OUTPUT .'.id = '. TABLE_CUSTOMER_DEBT .'.warehouse_output_id', array( 'warehouse_output_code' => 'code', 'warehouse_output_id' => 'id'), 'left');

                if(!empty($arrParam['id'])) {
                    $select -> where -> equalTo(TABLE_CUSTOMER_DEBT.'.id', $arrParam['id']);
                }
                if(!empty($arrParam['orders_id'])) {
                    $select -> where -> equalTo(TABLE_CUSTOMER_DEBT.'.orders_id', $arrParam['orders_id']);
                }
                if(!empty($arrParam['orders_return_id'])) {
                    $select -> where -> equalTo(TABLE_CUSTOMER_DEBT.'.orders_return_id', $arrParam['orders_return_id']);
                }
                if(!empty($arrParam['warehouse_input_id'])) {
                    $select -> where -> equalTo(TABLE_CUSTOMER_DEBT.'.warehouse_input_id', $arrParam['warehouse_input_id']);
                }
                if(!empty($arrParam['warehouse_output_id'])) {
                    $select -> where -> equalTo(TABLE_CUSTOMER_DEBT.'.warehouse_output_id', $arrParam['warehouse_output_id']);
                }

    		})->current();
		}

		return $result;
	}
	
	public function saveItem($arrParam = null, $options = null){
	    $arrData  = $arrParam['data'];
	    $arrItem  = $arrParam['item'];
	    $arrRoute = $arrParam['route'];
	    
	    $date     = new \ZendX\Functions\Date();
	    $number   = new \ZendX\Functions\Number();
	    $filter   = new \ZendX\Filter\Purifier();
	    $gid      = new \ZendX\Functions\Gid();
	    $action   = new \ZendX\Controller\ActionController();
	    if($options['task'] == 'add-item') {
	        $id = $gid->getId();

	        $data	= array(
	            'id'                    => $id,
	            'code'                  => $arrData['code'],
	            'customer_id'           => $arrData['customer_id'],
	            'inventory_id'          => $arrData['inventory_id'],
	            'type'                  => $arrData['type'],
                'orders_id'             => $arrData['orders_id'],
                'orders_return_id'      => $arrData['orders_return_id'],
                'warehouse_input_id'    => $arrData['warehouse_input_id'],
                'warehouse_output_id'   => $arrData['warehouse_output_id'],
                'date'                  => $date->formatToData($arrData['date']),
                'price_total'           => $number->formatToData($arrData['price_total']),
                'paid_cash'             => $number->formatToData($arrData['paid_cash']),
                'paid_transfer'         => $number->formatToData($arrData['paid_transfer']),
                'discount'              => $number->formatToData($arrData['discount']),
                'old_debt'              => $number->formatToData($arrData['old_debt']),
                'new_debt'              => $number->formatToData($arrData['new_debt']),
                'type_transaction'      => $arrData['type_transaction'],
                'category'              => $arrData['category'],
                'state'                 => $arrData['state'],
                'note'                 => $arrData['note'],

	            'created'               => date('Y-m-d H:i:s'),
	            'created_by'            => $this->userInfo->getUserInfo('id'),
                'status'                => 1,
                'ordering'              => 255,
	        );

            try {
                $this->tableGateway->insert($data);
                # tạo code cho phiếu
                $this->saveItem(array('data' => array('id' => $id)), array('task' => 'update-code'));
                # cập nhật amount_owed (nợ hiện tại) của khách hàng
                $data_contact = array('id' => $arrData['customer_id'], 'amount_owed' => $number->formatToData($arrData['new_debt']), );
                $this->getServiceLocator()->get('Admin\Model\ContactTable')->saveItem(array('data' => $data_contact), array('task' => 'update-infor'));


                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Insert Customer Debt Table failed: ' . $e->getMessage());
            }
	    }

        if($options['task'] == 'edit-item') {
            $debt_item_old = $arrParam['item'];
            $customer_id = $debt_item_old->customer_id;
            $value_old = $debt_item_old->price_total + $debt_item_old->discount + $debt_item_old->paid_cash + $debt_item_old->paid_transfer;

            $id = $arrData['id'];
            $data	= array();
            if(isset($arrData['inventory_id'])) {
                $data['inventory_id'] = $arrData['inventory_id'];
            }
            if(isset($arrData['price_total'])) {
                $data['price_total'] = $number->formatToData($arrData['price_total']);
            }
            if(isset($arrData['paid_cash'])) {
                $data['paid_cash'] = $number->formatToData($arrData['paid_cash']);
            }
            if(isset($arrData['paid_transfer'])) {
                $data['paid_transfer'] = $number->formatToData($arrData['paid_transfer']);
            }
            if(isset($arrData['discount'])) {
                $data['discount'] = $number->formatToData($arrData['discount']);
            }
            if(isset($arrData['new_debt'])) {
                $data['new_debt'] = $number->formatToData($arrData['new_debt']);
            }
            if(isset($arrData['state'])) {
                $data['state'] = $arrData['state'];
            }
            if(isset($arrData['accept'])) {
                $data['accept'] = $arrData['accept'];
            }

            try {
                # cập nhật thu chi
                $this->tableGateway->update($data, array('id' => $id));
                if (isset($arrData['new_debt'])) {
                    # cập nhật amount_owed (nợ hiện tại) của khách hàng
                    $data_contact = array( 'id' => $customer_id, 'amount_owed' => $number->formatToData($arrData['new_debt']), );
                    $this->getServiceLocator()->get('Admin\Model\ContactTable')->saveItem(array('data' => $data_contact), array('task' => 'update-infor'));
                }

                # cập nhật lại số liệu cho các phiếu thu chi phát sinh sau
                $debt_item_new = $this->getItem(array('id' => $debt_item_old->id));
                $value_new = $debt_item_new->price_total + $debt_item_new->discount + $debt_item_new->paid_cash + $debt_item_new->paid_transfer;

                $list_debt = $this->listItem(array('customer_id' => $customer_id, 'created' => $debt_item_old->created), array('task' => 'list-update'));
                $change_value = $value_old - $value_new;
                if ($change_value != 0) {
                    foreach ($list_debt as $debt) {
                        $data_update = array(
                            'id' => $debt->id,
                            'old_debt' => $debt->old_debt + $change_value,
                            'new_debt' => $debt->new_debt + $change_value,
                        );
                        $this->saveItem(array('data' => $data_update), array('task' => 'update-value'));

                        $data_contact = array(
                            'id' => $customer_id,
                            'amount_owed' => $debt->new_debt + $change_value,
                        );
                        $this->getServiceLocator()->get('Admin\Model\ContactTable')->saveItem(array('data' => $data_contact), array('task' => 'update-infor'));
                    }
                }

                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Update Customer Debt Table failed: ' . $e->getMessage());
            }
        }

        if ($options['task'] == 'update-code') {
            $id = $arrData['id'];
            $item = $this->getItem(array('id' => $id));
            $code = $action->createCode("P", $item->index);
            $data = array(
                'code' => $code,
            );
            try {
                $this->tableGateway->update($data, array('id' => $id));
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Update Debt code failed: ' . $e->getMessage());
            }
        }

        if ($options['task'] == 'update-value') {
            $id = $arrData['id'];
            $data = array(
                'old_debt' => $arrData['old_debt'],
                'new_debt' => $arrData['new_debt'],
            );

            try {
                $this->tableGateway->update($data, array('id' => $id));
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Update Debt value failed: ' . $e->getMessage());
            }
        }

        if($options['task'] == 'update-item') {
            $id = $arrData['id'];
            $data = [];

            if(isset($arrData['accept'])){
                $data['accept'] = $arrData['accept'];
            }

            $this->tableGateway->update($data, array('id' => $id));
            return $id;
        }
	}
	
	public function changeStatus($arrParam = null, $options = null){
	    if($options['task'] == 'change-status') {
	        $result = $this->defaultStatus($arrParam, null);
	    }
	    return $result;
	}
	
	public function changeOrdering($arrParam = null, $options = null){
	    if($options['task'] == 'change-ordering') {
	        $result = $this->defaultOrdering($arrParam, null);
	    }
	    return $result;
	}

    public function deleteItem($arrParam = null, $options = null){
        if($options['task'] == 'delete-item') {
            $arrData  = $arrParam['data'];

            $where = new Where();
            $where->in('id', $arrData['cid']);
            $result = $this->tableGateway->delete($where);
        }
        return $result;
    }
}
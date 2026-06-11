<?php
namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class OrdersReturnTable extends DefaultTable {

    public function countItem($arrParam = null, $options = null){
	    if($options['task'] == 'list-item') {
	        $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $ssFilter  = $arrParam['ssFilter'];
                $date       = new \ZendX\Functions\Date();
                $number     = new \ZendX\Functions\Number();

                $select -> join(TABLE_CONTACT, TABLE_CONTACT .'.id = '. TABLE_ORDERS_RETURN .'.customer_id', array( 'customer_name' => 'name'), 'inner')
                    -> join(TABLE_WAREHOUSE, TABLE_WAREHOUSE .'.id = '. TABLE_ORDERS_RETURN .'.inventory_id', array( 'warehouse_name' => 'name'), 'inner');

                if(!empty($ssFilter['filter_date_begin']) && !empty($ssFilter['filter_date_end'])) {
                    $select -> where -> NEST
                        -> greaterThanOrEqualTo(TABLE_ORDERS_RETURN .'.created', $date->formatToData($ssFilter['filter_date_begin']))
                        ->AND
                        -> lessThanOrEqualTo(TABLE_ORDERS_RETURN .'.created', $date->formatToData($ssFilter['filter_date_end']. ' 23:59:59') )
                        -> UNNEST;
                } elseif (!empty($ssFilter['filter_date_begin'])) {
                    $select->where->greaterThanOrEqualTo(TABLE_ORDERS_RETURN .'.created', $date->formatToData($ssFilter['filter_date_begin']));
                } elseif (!empty($ssFilter['filter_date_end'])) {
                    $select->where->lessThanOrEqualTo(TABLE_ORDERS_RETURN .'.created', $date->formatToData($ssFilter['filter_date_end']. ' 23:59:59') );
                }

                if(isset($ssFilter['filter_state']) && $ssFilter['filter_state'] != '') {
                    $select->where->equalTo('state', $ssFilter['filter_state']);
                }

                if(isset($ssFilter['filter_inventory_id']) && $ssFilter['filter_inventory_id'] != '') {
                    $select->where->equalTo('inventory_id', $ssFilter['filter_inventory_id']);
                }

                if(isset($ssFilter['filter_customer_id']) && $ssFilter['filter_customer_id'] != '') {
                    $select->where->equalTo('customer_id', $ssFilter['filter_customer_id']);
                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $select->where->NEST
                        ->like(TABLE_ORDERS_RETURN .'.code', '%'.$ssFilter['filter_keyword'].'%')
                        ->UNNEST;
                }

                if(!empty($ssFilter['filter_user'])) {
                    $select -> where -> NEST
                        -> equalTo(TABLE_CONTACT .'.user_id', $ssFilter['filter_user'])
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

                $select -> order(array('created' => 'desc'));

                $select -> join(TABLE_CONTACT, TABLE_CONTACT .'.id = '. TABLE_ORDERS_RETURN .'.customer_id', array( 'customer_name' => 'name'), 'inner')
                    -> join(TABLE_WAREHOUSE, TABLE_WAREHOUSE .'.id = '. TABLE_ORDERS_RETURN .'.inventory_id', array( 'warehouse_name' => 'name'), 'inner');

                if(!empty($ssFilter['filter_date_begin']) && !empty($ssFilter['filter_date_end'])) {
                    $select -> where -> NEST
                        -> greaterThanOrEqualTo(TABLE_ORDERS_RETURN .'.created', $date->formatToData($ssFilter['filter_date_begin']))
                        ->AND
                        -> lessThanOrEqualTo(TABLE_ORDERS_RETURN .'.created', $date->formatToData($ssFilter['filter_date_end']. ' 23:59:59') )
                        -> UNNEST;
                } elseif (!empty($ssFilter['filter_date_begin'])) {
                    $select->where->greaterThanOrEqualTo(TABLE_ORDERS_RETURN .'.created', $date->formatToData($ssFilter['filter_date_begin']));
                } elseif (!empty($ssFilter['filter_date_end'])) {
                    $select->where->lessThanOrEqualTo(TABLE_ORDERS_RETURN .'.created', $date->formatToData($ssFilter['filter_date_end']. ' 23:59:59') );
                }

                if(isset($ssFilter['filter_state']) && $ssFilter['filter_state'] != '') {
    			    $select->where->equalTo('state', $ssFilter['filter_state']);
    			}

                if(isset($ssFilter['filter_inventory_id']) && $ssFilter['filter_inventory_id'] != '') {
    			    $select->where->equalTo('inventory_id', $ssFilter['filter_inventory_id']);
    			}

                if(isset($ssFilter['filter_customer_id']) && $ssFilter['filter_customer_id'] != '') {
    			    $select->where->equalTo('customer_id', $ssFilter['filter_customer_id']);
    			}
    			
    			if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
    		        $select->where->NEST
                        ->like(TABLE_ORDERS_RETURN .'.code', '%'.$ssFilter['filter_keyword'].'%')
                			      ->UNNEST;
    			}

    			if(!empty($ssFilter['filter_user'])) {
                    $select -> where -> NEST
                        -> equalTo(TABLE_CONTACT .'.user_id', $ssFilter['filter_user'])
                        ->Or
                        -> like(TABLE_CONTACT.'.user_ids', "%{$ssFilter['filter_user']}%")
                        -> UNNEST;
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
                $select -> where -> equalTo(TABLE_ORDERS_RETURN.'.id', $arrParam['id']);
    		})->current();
		}

        if($options['task'] == 'full') {
            $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $select -> join(TABLE_CONTACT, TABLE_CONTACT .'.id = '. TABLE_ORDERS_RETURN .'.customer_id', array( 'name','phone','customer_type_id'), 'inner')
                    -> join(TABLE_WAREHOUSE, TABLE_WAREHOUSE .'.id = '. TABLE_ORDERS_RETURN .'.inventory_id', array( 'warehouse_name' => 'name'), 'inner')
                    -> join(TABLE_CUSTOMER_DEBT, TABLE_CUSTOMER_DEBT .'.orders_return_id = '. TABLE_ORDERS_RETURN .'.id', array('old_debt', 'new_debt'), 'inner');

                $select -> where -> equalTo(TABLE_ORDERS_RETURN.'.id', $arrParam['id']);

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

        if ($options['task'] == 'update-state') {
            $id = $arrData['id'];
            $data = array();
            if (isset($arrData['state'])) {
                $data['state'] = $arrData['state'];
            }
            try {
                $this->tableGateway->update($data, array('id' => $id));
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Update orders return state failed: ' . $e->getMessage());
            }
        }

        if ($options['task'] == 'update-code') {
            $id = $arrData['id'];
            $item = $this->getItem(array('id' => $id));
            $code = $action->createCode("KTH", $item->index);
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

	    if($options['task'] == 'add-item') {
	        $id = $gid->getId();
            $contract_options = array();
            $contract_options['product']  = $arrData['contract_product'];

	        $data	= array(
	            'id'                => $id,
	            'customer_id'       => $arrData['customer_id'],
	            'state'             => $arrData['state'],
                'inventory_id'      => $arrData['inventory_id'],
                'note'              => $arrData['note'],
                'price_total'       => $number->formatToData($arrData['price_total']),
                'paid_cash'         => $number->formatToData($arrData['paid_transfer']),
                'paid_transfer'     => $number->formatToData($arrData['paid_cash']),
                'discount'          => $number->formatToData($arrData['discount']),
	            'created'           => date('Y-m-d H:i:s'),
	            'created_by'        => $this->userInfo->getUserInfo('id'),
                'status'            => 1,
                'ordering'          => 255,
                'options'           => serialize($contract_options)
	        );

            try {
                $this->tableGateway->insert($data);
                $this->saveItem(array('data' => array('id' => $id)), array('task' => 'update-code'));

                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Insert Orders Return Table failed: ' . $e->getMessage());
            }
	    }

        if($options['task'] == 'edit-item') {
            $id = $arrData['id'];

            $contract_options['product']  = $arrData['contract_product'];
            $data	= array(
                'customer_id'       => $arrData['customer_id'],
                'inventory_id'      => $arrData['inventory_id'],
                'note'              => $arrData['note'],
                'price_total'       => $number->formatToData($arrData['price_total']),
                'paid_cash'         => $number->formatToData($arrData['paid_transfer']),
                'paid_transfer'     => $number->formatToData($arrData['paid_cash']),
                'discount'          => $number->formatToData($arrData['discount']),
                'options'           => serialize($contract_options)
            );

            try {
                $this->tableGateway->update($data, array('id' => $id));
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Update Orders Return Table failed: ' . $e->getMessage());
            }
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
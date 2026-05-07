<?php
namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class CustomerDebtTable extends DefaultTable {

    public function countItem($arrParam = null, $options = null){
	    if($options['task'] == 'list-item') {
	        $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $ssFilter  = $arrParam['ssFilter'];

                if(isset($ssFilter['filter_status']) && $ssFilter['filter_status'] != '') {
                    $select->where->equalTo('status', $ssFilter['filter_status']);
                }

                if(isset($ssFilter['filter_customer_id']) && $ssFilter['filter_customer_id'] != '') {
                    $select->where->equalTo('customer_id', $ssFilter['filter_customer_id']);
                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $select->where->NEST
                        ->like('code', '%'. $ssFilter['filter_keyword'] . '%')
                        ->UNNEST;
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

                if(!isset($options['paginator']) || $options['paginator'] == true) {
                    $select -> limit($paginator['itemCountPerPage'])
                        -> offset(($paginator['currentPageNumber'] - 1) * $paginator['itemCountPerPage']);
                }

                $select -> order(array('created' => 'DESC'));

                $select -> join(TABLE_CONTACT, TABLE_CONTACT .'.id = '. TABLE_CUSTOMER_DEBT .'.customer_id', array( 'customer_name' => 'name'), 'inner')
                        -> join(TABLE_CONTRACT, TABLE_CONTRACT .'.id = '. TABLE_CUSTOMER_DEBT .'.orders_id', array( 'orders_code' => 'code', 'orders_id' => 'id'), 'inner')
                        -> join(TABLE_WAREHOUSE, TABLE_WAREHOUSE .'.id = '. TABLE_CUSTOMER_DEBT .'.inventory_id', array( 'warehouse_name' => 'name'), 'inner');

    			
    			if(isset($ssFilter['filter_status']) && $ssFilter['filter_status'] != '') {
    			    $select->where->equalTo('status', $ssFilter['filter_status']);
    			}

                if(isset($ssFilter['filter_customer_id']) && $ssFilter['filter_customer_id'] != '') {
                    $select->where->equalTo('customer_id', $ssFilter['filter_customer_id']);
                }
    			
    			if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
    		        $select->where->NEST
                			      ->like('code', '%'. $ssFilter['filter_keyword'] . '%')
                			      ->UNNEST;
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
                if(!empty($arrParam['orders_id'])) {
                    $select -> where -> equalTo('orders_id', $arrParam['orders_id']);
                }
                if(!empty($arrParam['orders_return_id'])) {
                    $select -> where -> equalTo('orders_return_id', $arrParam['orders_return_id']);
                }
                if(!empty($arrParam['warehouse_input_id'])) {
                    $select -> where -> equalTo('warehouse_input_id', $arrParam['warehouse_input_id']);
                }
                if(!empty($arrParam['warehouse_output_id'])) {
                    $select -> where -> equalTo('warehouse_output_id', $arrParam['warehouse_output_id']);
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
                'date'                  => date('Y-m-d'),
                'price_total'           => $number->formatToData($arrData['price_total']),
                'paid_cash'             => $number->formatToData($arrData['paid_cash']),
                'paid_transfer'         => $number->formatToData($arrData['paid_transfer']),
                'discount'              => $number->formatToData($arrData['discount']),
                'old_debt'              => $number->formatToData($arrData['old_debt']),
                'new_debt'              => $number->formatToData($arrData['new_debt']),
                'type_transaction'      => $arrData['type_transaction'],
                'category'              => $arrData['category'],
                'state'                 => $arrData['state'],

	            'created'               => date('Y-m-d H:i:s'),
	            'created_by'            => $this->userInfo->getUserInfo('id'),
                'status'                => 1,
                'ordering'              => 255,
	        );

            try {
                $this->tableGateway->insert($data);
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Insert Customer Debt Table failed: ' . $e->getMessage());
            }
	    }

        if($options['task'] == 'edit-item') {
            $id = $arrData['id'];
            $data	= array(
                'name'              => $arrData['name'],
                'code'              => $arrData['code'],
                'note'              => $arrData['note'],
                'percent'           => $arrData['percent'],
                'ordering'          => $arrData['ordering'],
            );
            $this->tableGateway->update($data, array('id' => $id));
            return $id;
        }

        if ($options['task'] == 'update-code') {
            $id = $arrData['id'];
            $data = array(
                'code' => $arrData['code'],
            );
            try {
                $this->tableGateway->update($data, array('id' => $id));
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Update Debt code failed: ' . $e->getMessage());
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
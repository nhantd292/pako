<?php
namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class WarehouseRotationTable extends DefaultTable {

    public function countItem($arrParam = null, $options = null){
	    if($options['task'] == 'list-item') {
	        $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $ssFilter  = $arrParam['ssFilter'];
                $date       = new \ZendX\Functions\Date();
                $number     = new \ZendX\Functions\Number();

//                $select -> join(TABLE_WAREHOUSE, TABLE_WAREHOUSE .'.id = '. TABLE_WAREHOUSE_ROTATION .'.inventory_output_id', array( 'warehouse_output_name' => 'name'), 'inner');
//                $select -> join(TABLE_WAREHOUSE, TABLE_WAREHOUSE .'.id = '. TABLE_WAREHOUSE_ROTATION .'.inventory_input_id', array( 'warehouse_intput_name' => 'name'), 'inner');

                if(!empty($ssFilter['filter_date_begin']) && !empty($ssFilter['filter_date_end'])) {
                    $select -> where -> NEST
                        -> greaterThanOrEqualTo(TABLE_WAREHOUSE_ROTATION .'.created', $date->formatToData($ssFilter['filter_date_begin']))
                        ->AND
                        -> lessThanOrEqualTo(TABLE_WAREHOUSE_ROTATION .'.created', $date->formatToData($ssFilter['filter_date_end']. ' 23:59:59') )
                        -> UNNEST;
                } elseif (!empty($ssFilter['filter_date_begin'])) {
                    $select->where->greaterThanOrEqualTo(TABLE_WAREHOUSE_ROTATION .'.created', $date->formatToData($ssFilter['filter_date_begin']));
                } elseif (!empty($ssFilter['filter_date_end'])) {
                    $select->where->lessThanOrEqualTo(TABLE_WAREHOUSE_ROTATION .'.created', $date->formatToData($ssFilter['filter_date_end']. ' 23:59:59') );
                }

                if(isset($ssFilter['filter_state']) && $ssFilter['filter_state'] != '') {
                    $select->where->equalTo('state', $ssFilter['filter_state']);
                }

                if(isset($ssFilter['filter_inventory_output_id']) && $ssFilter['filter_inventory_output_id'] != '') {
                    $select->where->equalTo('inventory_output_id', $ssFilter['filter_inventory_output_id']);
                }

                if(isset($ssFilter['filter_inventory_input_id']) && $ssFilter['filter_inventory_input_id'] != '') {
                    $select->where->equalTo('inventory_input_id', $ssFilter['filter_inventory_input_id']);
                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $select->where->NEST
                        ->like(TABLE_WAREHOUSE_ROTATION .'.code', '%'.$ssFilter['filter_keyword'].'%')
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
                $date       = new \ZendX\Functions\Date();
                $number     = new \ZendX\Functions\Number();

                if(!isset($options['paginator']) || $options['paginator'] == true) {
                    $select -> limit($paginator['itemCountPerPage'])
                        -> offset(($paginator['currentPageNumber'] - 1) * $paginator['itemCountPerPage']);
                }

                $select -> order(array('created' => 'desc'));

//                $select -> join(TABLE_WAREHOUSE, TABLE_WAREHOUSE .'.id = '. TABLE_WAREHOUSE_ROTATION .'.inventory_output_id', array( 'warehouse_output_name' => 'name'), 'inner');
//                $select -> join(TABLE_WAREHOUSE, TABLE_WAREHOUSE .'.id = '. TABLE_WAREHOUSE_ROTATION .'.inventory_input_id', array( 'warehouse_intput_name' => 'name'), 'inner');

                if(!empty($ssFilter['filter_date_begin']) && !empty($ssFilter['filter_date_end'])) {
                    $select -> where -> NEST
                        -> greaterThanOrEqualTo(TABLE_WAREHOUSE_ROTATION .'.created', $date->formatToData($ssFilter['filter_date_begin']))
                        ->AND
                        -> lessThanOrEqualTo(TABLE_WAREHOUSE_ROTATION .'.created', $date->formatToData($ssFilter['filter_date_end']. ' 23:59:59') )
                        -> UNNEST;
                } elseif (!empty($ssFilter['filter_date_begin'])) {
                    $select->where->greaterThanOrEqualTo(TABLE_WAREHOUSE_ROTATION .'.created', $date->formatToData($ssFilter['filter_date_begin']));
                } elseif (!empty($ssFilter['filter_date_end'])) {
                    $select->where->lessThanOrEqualTo(TABLE_WAREHOUSE_ROTATION .'.created', $date->formatToData($ssFilter['filter_date_end']. ' 23:59:59') );
                }

                if(isset($ssFilter['filter_state']) && $ssFilter['filter_state'] != '') {
                    $select->where->equalTo('state', $ssFilter['filter_state']);
                }

                if(isset($ssFilter['filter_inventory_output_id']) && $ssFilter['filter_inventory_output_id'] != '') {
                    $select->where->equalTo('inventory_output_id', $ssFilter['filter_inventory_output_id']);
                }

                if(isset($ssFilter['filter_inventory_input_id']) && $ssFilter['filter_inventory_input_id'] != '') {
                    $select->where->equalTo('inventory_input_id', $ssFilter['filter_inventory_input_id']);
                }
    			
    			if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
    		        $select->where->NEST
                        ->like(TABLE_WAREHOUSE_ROTATION .'.code', '%'.$ssFilter['filter_keyword'].'%')
                			      ->UNNEST;
    			}
    		});
		}
		
	    if($options['task'] == 'cache') {
	        $cache = $this->getServiceLocator()->get('cache');
	        $cache_key = 'Warehouse_Rotation';
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
                $select -> where -> equalTo(TABLE_WAREHOUSE_ROTATION.'.id', $arrParam['id']);
    		})->current();
		}

        if($options['task'] == 'full') {
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
            $code = $action->createCode("PCK", $item->index);
            $data = array(
                'code' => $code,
            );
            try {
                $this->tableGateway->update($data, array('id' => $id));
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Update warehouse rotation code failed: ' . $e->getMessage());
            }
        }

	    if($options['task'] == 'add-item') {
	        $id = $gid->getId();

	        $data	= array(
	            'id'                    => $id,
	            'state'                 => $arrData['state'],
                'inventory_output_id'   => $arrData['inventory_output_id'],
                'inventory_input_id'    => $arrData['inventory_input_id'],
                'note'                  => $arrData['note'],
	            'date'                  => date('Y-m-d'),
	            'created'               => date('Y-m-d H:i:s'),
	            'created_by'            => $this->userInfo->getUserInfo('id'),
                'status'                => 1,
                'ordering'              => 255,
	        );

            try {
                $this->tableGateway->insert($data);
                $this->saveItem(array('data' => array('id' => $id)), array('task' => 'update-code'));

                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Insert warehouse rotation Table failed: ' . $e->getMessage());
            }
	    }

        if($options['task'] == 'edit-item') {
            $id = $arrData['id'];
            $data	= array(
                'inventory_output_id'   => $arrData['inventory_output_id'],
                'inventory_input_id'    => $arrData['inventory_input_id'],
                'note'                  => $arrData['note'],
            );

            try {
                $this->tableGateway->update($data, array('id' => $id));
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Update warehouse rotation Table failed: ' . $e->getMessage());
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
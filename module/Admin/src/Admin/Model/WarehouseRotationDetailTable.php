<?php
namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;

class WarehouseRotationDetailTable extends DefaultTable {

    public function countItem($arrParam = null, $options = null){
	    if($options['task'] == 'list-item') {
	        $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $ssFilter   = $arrParam['ssFilter'];
                
                $select -> columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(1)')));

                $select -> join(TABLE_WAREHOUSE_ROTATION, TABLE_WAREHOUSE_ROTATION .'.id = '. TABLE_WAREHOUSE_ROTATION_DETAIL .'.warehouse_rotation_id', array('warehouse_rotation_code' => 'code', 'warehouse_rotation_created' => 'created', 'state'), 'inner');
                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_WAREHOUSE_ROTATION_DETAIL .'.products_id', array('products_code' => 'code', 'products_name' => 'name'), 'inner');

                if(isset($ssFilter['filter_inventory_output_id']) && $ssFilter['filter_inventory_output_id'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_ROTATION.'.inventory_output_id', $ssFilter['filter_inventory_output_id']);
                }

                if(isset($ssFilter['filter_inventory_input_id']) && $ssFilter['filter_inventory_input_id'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_ROTATION.'.inventory_input_id', $ssFilter['filter_inventory_input_id']);
                }

                if(isset($ssFilter['filter_state']) && $ssFilter['filter_state'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_ROTATION.'.state', $ssFilter['filter_state']);
                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $filter_keyword = trim($ssFilter['filter_keyword']);
                    $select -> where -> NEST
                        -> like(TABLE_WAREHOUSE_ROTATION. '.code', '%'. $filter_keyword .'%')
                        ->Or
                        -> like(TABLE_PRODUCTS. '.name', '%'. $filter_keyword .'%')
                        ->Or
                        -> like(TABLE_PRODUCTS. '.code', '%'. $filter_keyword .'%')// mã sản phẩm
                        -> UNNEST;
                }
            })->current();
	    }
	    
	    return $result->count;
	}
	
	public function listItem($arrParam = null, $options = null){
		if($options['task'] == 'list-item') {
			$result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $paginator  = $arrParam['paginator'];
                $ssFilter   = $arrParam['ssFilter'];
                $date       = new \ZendX\Functions\Date();
				$number     = new \ZendX\Functions\Number();
				$userInfo = new \ZendX\System\UserInfo();
				$permission = $userInfo->getPermissionOfUser();

                if(!isset($options['paginator']) || $options['paginator'] == true) {
                    $select -> limit($paginator['itemCountPerPage'])
                        -> offset(($paginator['currentPageNumber'] - 1) * $paginator['itemCountPerPage']);
                }

                $select -> order(array(TABLE_WAREHOUSE_ROTATION .'.created' => 'DESC'));

                $select -> join(TABLE_WAREHOUSE_ROTATION, TABLE_WAREHOUSE_ROTATION .'.id = '. TABLE_WAREHOUSE_ROTATION_DETAIL .'.warehouse_rotation_id', array('warehouse_rotation_code' => 'code', 'warehouse_rotation_created' => 'created', 'state', 'inventory_output_id', 'inventory_input_id'), 'inner');
                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_WAREHOUSE_ROTATION_DETAIL .'.products_id', array('products_code' => 'code', 'products_name' => 'name'), 'inner');

                if(isset($ssFilter['filter_inventory_output_id']) && $ssFilter['filter_inventory_output_id'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_ROTATION.'.inventory_output_id', $ssFilter['filter_inventory_output_id']);
                }

                if(isset($ssFilter['filter_inventory_input_id']) && $ssFilter['filter_inventory_input_id'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_ROTATION.'.inventory_input_id', $ssFilter['filter_inventory_input_id']);
                }

                if(isset($ssFilter['filter_state']) && $ssFilter['filter_state'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_ROTATION.'.state', $ssFilter['filter_state']);
                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $filter_keyword = trim($ssFilter['filter_keyword']);
                    $select -> where -> NEST
                        -> like(TABLE_WAREHOUSE_ROTATION. '.code', '%'. $filter_keyword .'%')
                        ->Or
                        -> like(TABLE_PRODUCTS. '.name', '%'. $filter_keyword .'%')
                        ->Or
                        -> like(TABLE_PRODUCTS. '.code', '%'. $filter_keyword .'%')// mã sản phẩm
                        -> UNNEST;
                }
    		});
		}

		if($options['task'] == 'list-ajax') {
			$result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $date       = new \ZendX\Functions\Date();
				$number     = new \ZendX\Functions\Number();

                $select -> join(TABLE_WAREHOUSE_ROTATION, TABLE_WAREHOUSE_ROTATION .'.id = '. TABLE_WAREHOUSE_ROTATION_DETAIL .'.warehouse_rotation_id', array('contract_code' => 'code', 'contract_date'=> 'date'), 'inner');
                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_WAREHOUSE_ROTATION_DETAIL .'.products_id', array('products_code' => 'code', 'products_name' => 'name'), 'inner');


                $select -> order(array(TABLE_WAREHOUSE_ROTATION .'.created' => 'DESC'));

                if(isset($arrParam['warehouse_rotation_id']) && $arrParam['warehouse_rotation_id'] != '') {
                    $select->where->equalTo('warehouse_rotation_id', $arrParam['warehouse_rotation_id']);
                }
    		});
		}

        if($options['task'] == 'list-query') {
            $result = $this->tableGateway->getAdapter()->driver->getConnection()->execute($arrParam['query']);
        }
		
		return $result;
	}
	
	public function getItem($arrParam = null, $options = null){
	
		if($options == null) {
			$result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
			    $select -> where -> equalTo('id', $arrParam['id']);
    		})->toArray();
		}

		if($options['task'] == 'search') {
			$result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $select -> join(TABLE_WAREHOUSE_ROTATION, TABLE_WAREHOUSE_ROTATION .'.id = '. TABLE_WAREHOUSE_ROTATION_DETAIL .'.warehouse_rotation_id', array('warehouse_rotation_code' => 'code', 'warehouse_rotation_date'=> 'created'), 'inner');
                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_WAREHOUSE_ROTATION_DETAIL .'.products_id', array('products_code' => 'code', 'products_name'=> 'name'), 'inner');

                if (!empty($arrParam['id'])) {
                    $select -> where -> equalTo(TABLE_WAREHOUSE_ROTATION_DETAIL .'.id', $arrParam['id']);
                }
                if (!empty($arrParam['warehouse_rotation_id'])) {
                    $select -> where -> equalTo(TABLE_WAREHOUSE_ROTATION_DETAIL .'.warehouse_rotation_id', $arrParam['warehouse_rotation_id']);
                }
                if (!empty($arrParam['product_id'])) {
                    $select -> where -> equalTo(TABLE_WAREHOUSE_ROTATION_DETAIL .'.product_id', $arrParam['product_id']);
                }


    		})->toArray();
		}
			
		return current($result);
	}
	
	public function saveItem($arrParam = null, $options = null){
        $arrData = $arrParam['data'];
        $warehouse_rotation_id = $arrParam['warehouse_rotation_id'];
	    $gid     = new \ZendX\Functions\Gid();

        if($options['task'] == 'add-item') {
            $id = $gid->getId();
            $data = array(
                'id'                    => $id,
                'quantity'              => $arrData['quantity'],
                'warehouse_rotation_id' => $warehouse_rotation_id,
                'products_id'           => $arrData['products_id'],

                'created'       => date('Y-m-d H:i:s'),
                'created_by'    => $this->userInfo->getUserInfo('id'),
            );

            try {
                $this->tableGateway->insert($data);
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Insert warehouse rotation Detail Table failed ('.$arrData['code'].'): ' . $e->getMessage());
            }
        }

        if($options['task'] == 'delete_product_by_warehouse_rotation_id') {
            try {
                $sql_delete = "DELETE FROM ".TABLE_WAREHOUSE_ROTATION_DETAIL." WHERE ".TABLE_WAREHOUSE_ROTATION_DETAIL.".warehouse_rotation_id = '".$warehouse_rotation_id."'";
                $this->tableGateway->getAdapter()->driver->getConnection()->execute($sql_delete);
            } catch (\Exception $e) {
                throw new \Exception('Delete warehouse rotation detail Table failed: ' . $e->getMessage());
            }
        }
	}
}







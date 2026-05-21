<?php
namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;

class WarehouseInputDetailTable extends DefaultTable {

    public function countItem($arrParam = null, $options = null){
	    if($options['task'] == 'list-item') {
	        $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $ssFilter   = $arrParam['ssFilter'];
                
                $select -> columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(1)')));

                $select -> join(TABLE_WAREHOUSE_INPUT, TABLE_WAREHOUSE_INPUT .'.id = '. TABLE_WAREHOUSE_INPUT_DETAIL .'.warehouse_input_id', array('warehouse_input_code' => 'code', 'warehouse_input_created' => 'created', 'state'), 'inner');
                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_WAREHOUSE_INPUT_DETAIL .'.products_id', array('products_code' => 'code', 'products_name' => 'name'), 'inner');
                $select -> join(TABLE_CONTACT, TABLE_CONTACT .'.id = '. TABLE_WAREHOUSE_INPUT .'.customer_id', array('customer_name' => 'name'), 'inner');


                if(isset($ssFilter['filter_customer_id']) && $ssFilter['filter_customer_id'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_INPUT.'.customer_id', $ssFilter['filter_customer_id']);
                }

                if(isset($ssFilter['filter_inventory_id']) && $ssFilter['filter_inventory_id'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_INPUT.'.inventory_id', $ssFilter['filter_inventory_id']);
                }

                if(isset($ssFilter['filter_state']) && $ssFilter['filter_state'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_INPUT.'.state', $ssFilter['filter_state']);
                }

                if(isset($ssFilter['filter_numbers_return']) && $ssFilter['filter_numbers_return'] != true) {
                    $select->where->greaterThan(TABLE_WAREHOUSE_INPUT_DETAIL.'.quantity', TABLE_WAREHOUSE_INPUT_DETAIL.'.quantity_return');
                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $filter_keyword = trim($ssFilter['filter_keyword']);
                    $select -> where -> NEST
                        -> like(TABLE_WAREHOUSE_INPUT. '.code', '%'. $filter_keyword .'%')
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

                $select -> join(TABLE_WAREHOUSE_INPUT, TABLE_WAREHOUSE_INPUT .'.id = '. TABLE_WAREHOUSE_INPUT_DETAIL .'.warehouse_input_id', array('warehouse_input_id' => 'id','warehouse_input_code' => 'code', 'warehouse_input_created' => 'created', 'state', 'inventory_id'), 'inner');
                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_WAREHOUSE_INPUT_DETAIL .'.products_id', array('products_code' => 'code', 'products_name' => 'name'), 'inner');
                $select -> join(TABLE_CONTACT, TABLE_CONTACT .'.id = '. TABLE_WAREHOUSE_INPUT .'.customer_id', array('customer_name' => 'name'), 'inner');

                $select -> order(array(TABLE_WAREHOUSE_INPUT .'.created' => 'DESC'));

                if(isset($ssFilter['filter_customer_id']) && $ssFilter['filter_customer_id'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_INPUT.'.customer_id', $ssFilter['filter_customer_id']);
                }

                if(isset($ssFilter['filter_inventory_id']) && $ssFilter['filter_inventory_id'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_INPUT.'.inventory_id', $ssFilter['filter_inventory_id']);
                }

                if(isset($ssFilter['filter_state']) && $ssFilter['filter_state'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_INPUT.'.state', $ssFilter['filter_state']);
                }

                if(isset($ssFilter['filter_numbers_return']) && $ssFilter['filter_numbers_return'] != true) {
                    $select->where->greaterThan(TABLE_WAREHOUSE_INPUT_DETAIL.'.quantity', TABLE_WAREHOUSE_INPUT_DETAIL.'.quantity_return');
                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $filter_keyword = trim($ssFilter['filter_keyword']);
                    $select -> where -> NEST
                        -> like(TABLE_WAREHOUSE_INPUT. '.code', '%'. $filter_keyword .'%')
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

                $select -> join(TABLE_WAREHOUSE_INPUT, TABLE_WAREHOUSE_INPUT .'.id = '. TABLE_WAREHOUSE_INPUT_DETAIL .'.warehouse_input_id', array('contract_code' => 'code', 'contract_date'=> 'date'), 'inner');
                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_WAREHOUSE_INPUT_DETAIL .'.products_id', array('products_code' => 'code', 'products_name' => 'name'), 'inner');


                $select -> order(array(TABLE_WAREHOUSE_INPUT_DETAIL .'.created' => 'DESC'));

                if(isset($arrParam['warehouse_input_id']) && $arrParam['warehouse_input_id'] != '') {
                    $select->where->equalTo('warehouse_input_id', $arrParam['warehouse_input_id']);
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

		if($options['task'] == 'by-contract') {
			$result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $select -> join(TABLE_WAREHOUSE_INPUT, TABLE_WAREHOUSE_INPUT .'.id = '. TABLE_WAREHOUSE_INPUT_DETAIL .'.warehouse_input_id', array('contract_code' => 'code', 'contract_date'=> 'date'), 'inner');

			    $select -> where -> equalTo('warehouse_input_id', $arrParam['warehouse_input_id']);
			    $select -> where -> equalTo('product_id', $arrParam['product_id']);
    		})->toArray();
		}
			
		return current($result);
	}
	
	public function saveItem($arrParam = null, $options = null){
        $arrData = $arrParam['data'];
        $warehouse_input_id = $arrParam['warehouse_input_id'];
	    $gid     = new \ZendX\Functions\Gid();

        if($options['task'] == 'add-item') {
            $id = $gid->getId();
            $data = array(
                'id'                => $id,
                'quantity'          => $arrData['quantity'],
                'price'             => $arrData['price'],
                'price_total'       => $arrData['total'],
                'note'              => $arrData['note'],
                'warehouse_input_id'  => $warehouse_input_id,
                'products_id'        => $arrData['products_id'],

                'created'       => date('Y-m-d H:i:s'),
                'created_by'    => $this->userInfo->getUserInfo('id'),
            );

            try {
                $this->tableGateway->insert($data);
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Insert Orders return Detail Table failed ('.$arrData['code'].'): ' . $e->getMessage());
            }
        }

        if($options['task'] == 'delete_product_by_warehouse_input_id') {
            try {
                $sql_delete = "DELETE FROM ".TABLE_WAREHOUSE_INPUT_DETAIL." WHERE ".TABLE_WAREHOUSE_INPUT_DETAIL.".warehouse_input_id = '".$warehouse_input_id."'";
                $this->tableGateway->getAdapter()->driver->getConnection()->execute($sql_delete);
            } catch (\Exception $e) {
                throw new \Exception('Delete Contract Detail Table failed: ' . $e->getMessage());
            }
        }
	}
}






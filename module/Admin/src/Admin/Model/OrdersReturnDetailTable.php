<?php
namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;

class OrdersReturnDetailTable extends DefaultTable {

    public function countItem($arrParam = null, $options = null){
	    if($options['task'] == 'list-item') {
	        $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $ssFilter   = $arrParam['ssFilter'];
                
                $select -> columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(1)')));

                $select -> join(TABLE_ORDERS_RETURN, TABLE_ORDERS_RETURN .'.id = '. TABLE_ORDERS_RETURN_DETAIL .'.orders_return_id', array('orders_return_code' => 'code', 'orders_return_created' => 'created', 'state'), 'inner');
                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_ORDERS_RETURN_DETAIL .'.product_id', array('products_code' => 'code', 'products_name' => 'name'), 'inner');
                $select -> join(TABLE_CONTRACT_DETAIL, TABLE_CONTRACT_DETAIL .'.id = '. TABLE_ORDERS_RETURN_DETAIL .'.orders_detail_id', array('products_code' => 'code'), 'inner');
                $select -> join(TABLE_CONTRACT, TABLE_CONTRACT .'.id = '. TABLE_CONTRACT_DETAIL .'.contract_id', array('contract_code' => 'code', 'contract_date'=> 'date'), 'inner');


                if(isset($ssFilter['filter_contact_id']) && $ssFilter['filter_contact_id'] != '') {
                    $select->where->equalTo(TABLE_ORDERS_RETURN.'.contact_id', $ssFilter['filter_contact_id']);
                }

                if(isset($ssFilter['filter_state']) && $ssFilter['filter_state'] != '') {
                    $select->where->equalTo(TABLE_ORDERS_RETURN.'.state', $ssFilter['filter_state']);
                }

                if(isset($ssFilter['filter_numbers_return']) && $ssFilter['filter_numbers_return'] != true) {
                    $select->where->greaterThan(TABLE_ORDERS_RETURN_DETAIL.'.numbers', TABLE_ORDERS_RETURN_DETAIL.'.numbers_return');
                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $filter_keyword = trim($ssFilter['filter_keyword']);
                    $select -> where -> NEST
                        -> like(TABLE_ORDERS_RETURN. '.code', '%'. $filter_keyword .'%')
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

                $select -> join(TABLE_ORDERS_RETURN, TABLE_ORDERS_RETURN .'.id = '. TABLE_ORDERS_RETURN_DETAIL .'.orders_return_id', array('orders_return_id' => 'id','orders_return_code' => 'code', 'orders_return_created' => 'created', 'state'), 'inner');
                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_ORDERS_RETURN_DETAIL .'.product_id', array('products_code' => 'code', 'products_name' => 'name'), 'inner');
                $select -> join(TABLE_CONTRACT_DETAIL, TABLE_CONTRACT_DETAIL .'.id = '. TABLE_ORDERS_RETURN_DETAIL .'.orders_detail_id', array('products_code' => 'code'), 'inner');
                $select -> join(TABLE_CONTRACT, TABLE_CONTRACT .'.id = '. TABLE_CONTRACT_DETAIL .'.contract_id', array('contract_code' => 'code', 'contract_date'=> 'date', 'contract_id'=>'id'), 'inner');

                $select -> order(array(TABLE_ORDERS_RETURN .'.created' => 'DESC'));

                if(isset($ssFilter['filter_contact_id']) && $ssFilter['filter_contact_id'] != '') {
                    $select->where->equalTo(TABLE_ORDERS_RETURN.'.contact_id', $ssFilter['filter_contact_id']);
                }

                if(isset($ssFilter['filter_state']) && $ssFilter['filter_state'] != '') {
                    $select->where->equalTo(TABLE_ORDERS_RETURN.'.state', $ssFilter['filter_state']);
                }

                if(isset($ssFilter['filter_numbers_return']) && $ssFilter['filter_numbers_return'] != true) {
                    $select->where->greaterThan(TABLE_ORDERS_RETURN_DETAIL.'.numbers', TABLE_ORDERS_RETURN_DETAIL.'.numbers_return');
                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $filter_keyword = trim($ssFilter['filter_keyword']);
                    $select -> where -> NEST
                        -> like(TABLE_ORDERS_RETURN. '.code', '%'. $filter_keyword .'%')
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

                $select -> join(TABLE_ORDERS_RETURN, TABLE_ORDERS_RETURN .'.id = '. TABLE_ORDERS_RETURN_DETAIL .'.orders_return_id', array('contract_code' => 'code', 'contract_date'=> 'date'), 'inner');
                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_ORDERS_RETURN_DETAIL .'.product_id', array('products_code' => 'code', 'products_name' => 'name'), 'inner');
                $select -> join(TABLE_CONTRACT_DETAIL, TABLE_CONTRACT_DETAIL .'.id = '. TABLE_ORDERS_RETURN_DETAIL .'.orders_detail_id', array('products_code' => 'code', 'contract_detail_quantity' => 'numbers'), 'inner');
                $select -> join(TABLE_CONTRACT, TABLE_CONTRACT .'.id = '. TABLE_CONTRACT_DETAIL .'.contract_id', array('contract_code' => 'code', 'contract_inventory' => 'inventory_id'), 'inner');


                $select -> order(array(TABLE_ORDERS_RETURN_DETAIL .'.created' => 'DESC'));

                if(isset($arrParam['orders_return_id']) && $arrParam['orders_return_id'] != '') {
                    $select->where->equalTo('orders_return_id', $arrParam['orders_return_id']);
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
                $select -> join(TABLE_ORDERS_RETURN, TABLE_ORDERS_RETURN .'.id = '. TABLE_ORDERS_RETURN_DETAIL .'.orders_return_id', array('contract_code' => 'code', 'contract_date'=> 'date'), 'inner');

			    $select -> where -> equalTo('orders_return_id', $arrParam['orders_return_id']);
			    $select -> where -> equalTo('product_id', $arrParam['product_id']);
    		})->toArray();
		}
			
		return current($result);
	}
	
	public function saveItem($arrParam = null, $options = null){
        $arrData = $arrParam['data'];
        $orders_return_id = $arrParam['orders_return_id'];
	    $gid     = new \ZendX\Functions\Gid();

        if($options['task'] == 'add-item') {
            $id = $gid->getId();
            $data = array(
                'id'                => $id,
                'quantity'          => $arrData['quantity'],
                'price'             => $arrData['price'],
                'price_total'       => $arrData['total'],
                'note'              => $arrData['note'],
                'orders_detail_id'  => $arrData['orders_detail_id'],
                'orders_return_id'  => $orders_return_id,
                'product_id'        => $arrData['product_id'],

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

        if($options['task'] == 'delete_product_by_orders_return_id') {
            try {
                $sql_delete = "DELETE FROM ".TABLE_ORDERS_RETURN_DETAIL." WHERE ".TABLE_ORDERS_RETURN_DETAIL.".orders_return_id = '".$orders_return_id."'";
                $this->tableGateway->getAdapter()->driver->getConnection()->execute($sql_delete);
            } catch (\Exception $e) {
                throw new \Exception('Delete Contract Detail Table failed: ' . $e->getMessage());
            }
        }
	}
}






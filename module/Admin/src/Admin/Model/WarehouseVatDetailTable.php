<?php
namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;

class WarehouseVatDetailTable extends DefaultTable {

    public function countItem($arrParam = null, $options = null){
	    if($options['task'] == 'list-item') {
	        $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $ssFilter   = $arrParam['ssFilter'];
                
                $select -> columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(1)')));

                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_WAREHOUSE_VAT_DETAIL .'.products_id', array('products_code' => 'code', 'products_name' => 'name'), 'inner');

                if(isset($ssFilter['filter_products_id']) && $ssFilter['filter_products_id'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_VAT_DETAIL.'.products_id', $ssFilter['filter_products_id']);
                }

                if(isset($ssFilter['filter_sale_branch_id']) && $ssFilter['filter_sale_branch_id'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_VAT_DETAIL.'.sale_branch_id', $ssFilter['filter_sale_branch_id']);
                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $filter_keyword = trim($ssFilter['filter_keyword']);
                    $select -> where -> NEST
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

                $select -> order(array(TABLE_WAREHOUSE_VAT_DETAIL .'.created' => 'DESC'));

                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_WAREHOUSE_VAT_DETAIL .'.products_id', array('products_code' => 'code', 'products_name' => 'name'), 'inner');

                if(isset($ssFilter['filter_products_id']) && $ssFilter['filter_products_id'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_VAT_DETAIL.'.products_id', $ssFilter['filter_products_id']);
                }

                if(isset($ssFilter['filter_sale_branch_id']) && $ssFilter['filter_sale_branch_id'] != '') {
                    $select->where->equalTo(TABLE_WAREHOUSE_VAT_DETAIL.'.sale_branch_id', $ssFilter['filter_sale_branch_id']);
                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $filter_keyword = trim($ssFilter['filter_keyword']);
                    $select -> where -> NEST
                        -> like(TABLE_PRODUCTS. '.name', '%'. $filter_keyword .'%')
                        ->Or
                        -> like(TABLE_PRODUCTS. '.code', '%'. $filter_keyword .'%')// mã sản phẩm
                        -> UNNEST;
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
                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_WAREHOUSE_VAT_DETAIL .'.products_id', array('products_code' => 'code', 'products_name'=> 'name'), 'inner');

                if (!empty($arrParam['id'])) {
                    $select -> where -> equalTo(TABLE_WAREHOUSE_VAT_DETAIL .'.id', $arrParam['id']);
                }
                if (!empty($arrParam['products_id'])) {
                    $select -> where -> equalTo(TABLE_WAREHOUSE_VAT_DETAIL .'.products_id', $arrParam['products_id']);
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
                'quantity_begin'        => $arrData['quantity_begin'],
                'quantity_end'          => $arrData['quantity_end'],
                'products_id'           => $arrData['products_id'],
                'sale_branch_id'        => $arrData['sale_branch_id'],
                'type'                  => $arrData['type'],

                'user_id'       => $this->userInfo->getUserInfo('id'),
                'created'       => date('Y-m-d H:i:s'),
                'created_by'    => $this->userInfo->getUserInfo('id'),
            );

            try {
                $this->tableGateway->insert($data);
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Insert warehouse vat Detail Table failed: ' . $e->getMessage());
            }
        }
	}
}







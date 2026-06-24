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
                $date       = new \ZendX\Functions\Date();
                
                $select -> columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(1)')));

                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_WAREHOUSE_VAT_DETAIL .'.products_id', array('products_code' => 'code', 'products_name' => 'name'), 'inner');

                if(!empty($ssFilter['filter_date_begin']) && !empty($ssFilter['filter_date_end'])) {
                    $select -> where -> NEST
                        -> greaterThanOrEqualTo(TABLE_WAREHOUSE_VAT_DETAIL.'.created', $date->formatToSearch($ssFilter['filter_date_begin']))
                        ->AND
                        -> lessThanOrEqualTo(TABLE_WAREHOUSE_VAT_DETAIL.'.created', $date->formatToSearch($ssFilter['filter_date_end']) . ' 23:59:59')
                        -> UNNEST;
                } elseif (!empty($ssFilter['filter_date_begin'])) {
                    $select -> where -> greaterThanOrEqualTo(TABLE_WAREHOUSE_VAT_DETAIL.'.created', $date->formatToSearch($ssFilter['filter_date_begin']));
                } elseif (!empty($ssFilter['filter_date_end'])) {
                    $select -> where -> lessThanOrEqualTo(TABLE_WAREHOUSE_VAT_DETAIL.'.created', $date->formatToSearch($ssFilter['filter_date_end']) . ' 23:59:59');
                }

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

                $select -> order(array(TABLE_WAREHOUSE_VAT_DETAIL .'.index' => 'DESC'));

                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_WAREHOUSE_VAT_DETAIL .'.products_id', array('products_code' => 'code', 'products_name' => 'name'), 'inner');

                if(!empty($ssFilter['filter_date_begin']) && !empty($ssFilter['filter_date_end'])) {
                    $select -> where -> NEST
                        -> greaterThanOrEqualTo(TABLE_WAREHOUSE_VAT_DETAIL.'.created', $date->formatToSearch($ssFilter['filter_date_begin']))
                        ->AND
                        -> lessThanOrEqualTo(TABLE_WAREHOUSE_VAT_DETAIL.'.created', $date->formatToSearch($ssFilter['filter_date_end']) . ' 23:59:59')
                        -> UNNEST;
                } elseif (!empty($ssFilter['filter_date_begin'])) {
                    $select -> where -> greaterThanOrEqualTo(TABLE_WAREHOUSE_VAT_DETAIL.'.created', $date->formatToSearch($ssFilter['filter_date_begin']));
                } elseif (!empty($ssFilter['filter_date_end'])) {
                    $select -> where -> lessThanOrEqualTo(TABLE_WAREHOUSE_VAT_DETAIL.'.created', $date->formatToSearch($ssFilter['filter_date_end']) . ' 23:59:59');
                }

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

        if($options['task'] == 'list-update') {
            $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){

                $select -> order(array( 'index' => 'ASC'));

                if(isset($arrParam['index']) && $arrParam['index'] != '') {
                    $select->where->greaterThan('index', $arrParam['index']);
                }

                if(isset($arrParam['sale_branch_id']) && $arrParam['sale_branch_id'] != '') {
                    $select->where->equalTo('sale_branch_id', $arrParam['sale_branch_id']);
                }

                if(isset($arrParam['products_id']) && $arrParam['products_id'] != '') {
                    $select->where->equalTo('products_id', $arrParam['products_id']);
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
                'contract_detail_id'    => $arrData['contract_detail_id'],
                'type'                  => $arrData['type'],
                'note'                  => $arrData['note'],

                'user_id'       => !empty($arrData['user_id']) ? $arrData['user_id'] : $this->userInfo->getUserInfo('id'),
                'created'       => !empty($arrData['created']) ? $arrData['created'] : date('Y-m-d H:i:s'),
                'created_by'    => !empty($arrData['user_id']) ? $arrData['user_id'] : $this->userInfo->getUserInfo('id'),
            );

            try {
                $this->tableGateway->insert($data);
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Insert warehouse vat Detail Table failed: ' . $e->getMessage());
            }
        }

        if($options['task'] == 'edit-item') {
            $arrItem = $arrParam['item'];
            $quantity_old = $arrItem['quantity'];
            $id = $arrData['id'];
            $data	= array();
            if(isset($arrData['quantity'])) {
                $data['quantity'] = $arrData['quantity'];
                $data['quantity_end'] = $arrItem['quantity_begin'] + $arrData['quantity'];
            }
            if(isset($arrData['note'])) {
                $data['note'] = $arrData['note'];
            }

            try {
                # cập nhật
                $this->tableGateway->update($data, array('id' => $id));
                # cập nhật lại số liệu cho các phiếu nhập xuất phát sinh sau
                $item_new = $this->getItem(array('id' => $id));
                $quantity_new = $item_new['quantity'];

                $change_quantity = $quantity_old - $quantity_new;
                if ($change_quantity != 0) {
                    $list_item = $this->listItem(array('sale_branch_id' => $arrItem['sale_branch_id'], 'products_id' => $arrItem['products_id'], 'index' => $arrItem['index']), array('task' => 'list-update'));


                    foreach ($list_item as $item) {
                        $data_update = array(
                            'id' => $item->id,
                            'quantity_begin' => $item->quantity_begin - $change_quantity,
                            'quantity_end' => $item->quantity_end - $change_quantity,
                        );
                        $this->saveItem(array('data' => $data_update), array('task' => 'update-value'));
                    }
                }
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Update Customer Debt Table failed: ' . $e->getMessage());
            }
        }

        if ($options['task'] == 'update-value') {
            $id = $arrData['id'];
            $data = array(
                'quantity_begin' => $arrData['quantity_begin'],
                'quantity_end' => $arrData['quantity_end'],
            );

            try {
                $this->tableGateway->update($data, array('id' => $id));
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Update vat value failed: ' . $e->getMessage());
            }
        }
	}
}







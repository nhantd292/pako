<?php
namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class ProductsPriceTable extends DefaultTable {

    public function countItem($arrParam = null, $options = null){
	    if($options['task'] == 'list-item') {
	        $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $ssFilter  = $arrParam['ssFilter'];

                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_PRODUCTS_PRICE .'.products_id', array( 'products_name' => 'name', 'products_code' => 'code', 'products_type_id' => 'products_type_id','trademark_id' => 'trademark_id','unit_id' => 'unit_id', ), 'inner')
                        -> join(TABLE_CUSTOMER_TYPE, TABLE_CUSTOMER_TYPE .'.id = '. TABLE_PRODUCTS_PRICE .'.customer_type_id', array( 'customer_type_name' => 'name' ), 'inner');

                if(isset($ssFilter['filter_status']) && $ssFilter['filter_status'] != '') {
                    $select->where->equalTo(TABLE_PRODUCTS.'.status', $ssFilter['filter_status']);
                }

                if(isset($ssFilter['filter_products_type']) && $ssFilter['filter_products_type'] != '') {
                    $select->where->equalTo(TABLE_PRODUCTS.'.products_type_id', $ssFilter['filter_products_type']);
                }

                if(isset($ssFilter['filter_trademark']) && $ssFilter['filter_trademark'] != '') {
                    $select->where->equalTo(TABLE_PRODUCTS.'.trademark_id', $ssFilter['filter_trademark']);
                }

                if(isset($ssFilter['filter_customer_type']) && $ssFilter['filter_customer_type'] != '') {
                    $select->where->equalTo(TABLE_PRODUCTS_PRICE.'.customer_type_id', $ssFilter['filter_customer_type']);
                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $select->where->NEST
                        ->like(TABLE_PRODUCTS.'.name', '%'. $ssFilter['filter_keyword'] . '%')
                        ->or
                        ->equalTo(TABLE_PRODUCTS.'.code', $ssFilter['filter_keyword'])
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

                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_PRODUCTS_PRICE .'.products_id', array( 'products_name' => 'name', 'products_code' => 'code', 'products_type_id' => 'products_type_id','trademark_id' => 'trademark_id','unit_id' => 'unit_id', ), 'inner')
                    -> join(TABLE_CUSTOMER_TYPE, TABLE_CUSTOMER_TYPE .'.id = '. TABLE_PRODUCTS_PRICE .'.customer_type_id', array( 'customer_type_name' => 'name' ), 'inner');

//                $select -> order(array('ordering' => 'ASC'));

                if(isset($ssFilter['filter_status']) && $ssFilter['filter_status'] != '') {
                    $select->where->equalTo(TABLE_PRODUCTS.'.status', $ssFilter['filter_status']);
                }

                if(isset($ssFilter['filter_products_type']) && $ssFilter['filter_products_type'] != '') {
                    $select->where->equalTo(TABLE_PRODUCTS.'.products_type_id', $ssFilter['filter_products_type']);
                }

                if(isset($ssFilter['filter_trademark']) && $ssFilter['filter_trademark'] != '') {
                    $select->where->equalTo(TABLE_PRODUCTS.'.trademark_id', $ssFilter['filter_trademark']);
                }

                if(isset($ssFilter['filter_customer_type']) && $ssFilter['filter_customer_type'] != '') {
                    $select->where->equalTo(TABLE_PRODUCTS_PRICE.'.customer_type_id', $ssFilter['filter_customer_type']);
                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $select->where->NEST
                        ->like(TABLE_PRODUCTS.'.name', '%'. $ssFilter['filter_keyword'] . '%')
                        ->or
                        ->equalTo(TABLE_PRODUCTS.'.code', $ssFilter['filter_keyword'])
                        ->UNNEST;
                }
    		});
		}

        if($options['task'] == 'list-item-by-products-id') {
            $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){

                $select -> join(TABLE_PRODUCTS, TABLE_PRODUCTS .'.id = '. TABLE_PRODUCTS_PRICE .'.products_id', array( 'products_name' => 'name', 'products_code' => 'code' ), 'inner')
                    -> join(TABLE_CUSTOMER_TYPE, TABLE_CUSTOMER_TYPE .'.id = '. TABLE_PRODUCTS_PRICE .'.customer_type_id', array( 'customer_type_name' => 'name' ), 'inner');


                if(isset($arrParam['products_id']) && $arrParam['products_id'] != '') {
                    $select -> where -> equalTo(TABLE_PRODUCTS_PRICE .'.products_id', $arrParam['products_id']);
                }
            });
        }
		
	    if($options['task'] == 'cache') {
	        $cache = $this->getServiceLocator()->get('cache');
	        $cache_key = 'ProductsPrice';
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

        if($options['task'] == 'filter') {
			$result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){

			    if(!empty($arrParam['products_id'])) {
                    $select -> where -> equalTo('products_id', $arrParam['products_id']);
                }

			    if(!empty($arrParam['customer_type_id'])) {
                    $select -> where -> equalTo('customer_type_id', $arrParam['customer_type_id']);
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
	            'id'                => $id,
	            'products_id'       => $arrData['products_id'],
	            'customer_type_id'  => $arrData['customer_type_id'],
	            'price'             => $number->formatToData($arrData['price']),
                'ordering'          => $arrData['ordering'],
	            'created'           => date('Y-m-d H:i:s'),
	            'created_by'        => $this->userInfo->getUserInfo('id'),
                'status'            => 1,
	        );

            try {
                $this->tableGateway->insert($data);
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Insert Products Price Table failed: ' . $e->getMessage());
            }
	    }

        if($options['task'] == 'edit-item') {
            $id = $arrData['id'];
            $data	= array(
                'products_id'       => $arrData['products_id'],
                'customer_type_id'  => $arrData['customer_type_id'],
                'price'             => $number->formatToData($arrData['price']),
            );

            try {
                $this->tableGateway->update($data, array('id' => $id));
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Update Products Price Table failed: ' . $e->getMessage());
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
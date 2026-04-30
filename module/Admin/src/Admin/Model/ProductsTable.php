<?php
namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class ProductsTable extends DefaultTable {

    public function countItem($arrParam = null, $options = null){
	    if($options['task'] == 'list-item') {
	        $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $ssFilter  = $arrParam['ssFilter'];

                if(isset($ssFilter['filter_status']) && $ssFilter['filter_status'] != '') {
                    $select->where->equalTo('status', $ssFilter['filter_status']);
                }

                if(isset($ssFilter['filter_products_type']) && $ssFilter['filter_products_type'] != '') {
                    $select->where->equalTo('products_type_id', $ssFilter['filter_products_type']);
                }

                if(isset($ssFilter['filter_trademark']) && $ssFilter['filter_trademark'] != '') {
                    $select->where->equalTo('trademark_id', $ssFilter['filter_trademark']);
                }

                if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $select->where->NEST
                        ->like('name', '%'. $ssFilter['filter_keyword'] . '%')
                        ->or
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

                $select -> order(array('ordering' => 'ASC'));

    			
    			if(isset($ssFilter['filter_status']) && $ssFilter['filter_status'] != '') {
    			    $select->where->equalTo('status', $ssFilter['filter_status']);
    			}

                if(isset($ssFilter['filter_products_type']) && $ssFilter['filter_products_type'] != '') {
                    $select->where->equalTo('products_type_id', $ssFilter['filter_products_type']);
                }

                if(isset($ssFilter['filter_trademark']) && $ssFilter['filter_trademark'] != '') {
                    $select->where->equalTo('trademark_id', $ssFilter['filter_trademark']);
                }
    			
    			if(isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
    		        $select->where->NEST
                			      ->like('name', '%'. $ssFilter['filter_keyword'] . '%')
                			      ->or
                			      ->like('code', '%'. $ssFilter['filter_keyword'] . '%')
                			      ->UNNEST;
    			}
    		});
		}
		
	    if($options['task'] == 'cache') {
	        $cache = $this->getServiceLocator()->get('cache');
	        $cache_key = 'Products';
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
	            'name'              => $arrData['name'],
	            'code'              => $arrData['code'],
	            'products_type_id'  => $arrData['products_type_id'],
	            'trademark_id'      => $arrData['trademark_id'],
	            'unit_id'           => $arrData['unit_id'],
	            'cost_price'        => $number->formatToData($arrData['cost_price']),
	            'min'               => $number->formatToData($arrData['min']),
                'max'               => $number->formatToData($arrData['max']),
                'length'            => $number->formatToData($arrData['length']),
                'width'             => $number->formatToData($arrData['width']),
                'height'            => $number->formatToData($arrData['height']),
                'weight'            => $number->formatToData($arrData['weight']),
                'note'              => $arrData['note'],
	            'created'           => date('Y-m-d H:i:s'),
	            'created_by'        => $this->userInfo->getUserInfo('id'),
                'status'            => 1,
	        );

//            $result = $this->tableGateway->insert($data);
//            if (!$result) {
//                throw new \Exception('Insert Products table failed');
//            }
//            return $id;

            try {
                $this->tableGateway->insert($data);
                return $id;

            } catch (\Exception $e) {
                throw new \Exception('Insert Products Table failed: ' . $e->getMessage());
            }
	    }

        if($options['task'] == 'edit-item') {
            $id = $arrData['id'];

            $data	= array(
                'name'              => $arrData['name'],
                'code'              => $arrData['code'],
                'products_type_id'  => $arrData['products_type_id'],
                'trademark_id'      => $arrData['trademark_id'],
                'unit_id'           => $arrData['unit_id'],
                'cost_price'        => $number->formatToData($arrData['cost_price']),
                'min'               => $number->formatToData($arrData['min']),
                'max'               => $number->formatToData($arrData['max']),
                'length'            => $number->formatToData($arrData['length']),
                'width'             => $number->formatToData($arrData['width']),
                'height'            => $number->formatToData($arrData['height']),
                'weight'            => $number->formatToData($arrData['weight']),
                'note'              => $arrData['note'],
            );

            try {
                $this->tableGateway->update($data, ['id' => $id]);
                return $id;

            } catch (\Exception $e) {
                throw new \Exception('Update Products Table failed: ' . $e->getMessage());
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
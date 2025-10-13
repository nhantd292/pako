<?php
namespace Admin\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use ZendX\System\UserInfo;

class KovOrdersTable extends DefaultTable {

    public function countItem($arrParam = null, $options = null){

        if($options['task'] == 'list-item') {
            $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $date = new \ZendX\Functions\Date();
                $ssFilter  = $arrParam['ssFilter'];

                $select -> columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(1)')));

                if(isset($ssFilter['filter_categoryId']) && $ssFilter['filter_categoryId'] != '') {
                    $select->where->equalTo(TABLE_KOV_PRODUCTS.'.categoryId', $ssFilter['filter_categoryId']);
                }

                if(isset($ssFilter['filter_keyword']) && trim($ssFilter['filter_keyword']) != '') {
                    $select -> where -> NEST
                        ->like(TABLE_KOV_PRODUCTS.'.fullName', '%'.$ssFilter['filter_keyword'].'%')
                        ->Or
                        ->like(TABLE_KOV_PRODUCTS.'.code', '%'.$ssFilter['filter_keyword'].'%')
                        -> UNNEST;

                }

                if(isset($ssFilter['filter_branches']) && $ssFilter['filter_branches'] != '') {
                    $select->where->equalTo(TABLE_KOV_PRODUCT_BRANCH.'.branchId', $ssFilter['filter_branches']);
                }
                if(isset($ssFilter['filter_evaluate']) && $ssFilter['filter_evaluate'] != '') {
                    $select->where->equalTo(TABLE_KOV_PRODUCTS.'.evaluate', $ssFilter['filter_evaluate']);
                }
                if(isset($ssFilter['filter_tailors']) && $ssFilter['filter_tailors'] != '') {
                    $select->where->equalTo(TABLE_KOV_PRODUCTS.'.product_type', $ssFilter['filter_tailors']);
                }
            })->current();
        }

        if($options['task'] == null) {
            $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $select -> columns(array('count' => new Expression('COUNT(1)')));
            })->current();
        }
        return $result->count;
    }

    public function listItem($arrParam = null, $options = null){
        if($options['task'] == 'list-item') {
            $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $date = new \ZendX\Functions\Date();
                $paginator = $arrParam['paginator'];
                $ssFilter  = $arrParam['ssFilter'];
                $order_by = 'CreatedDate';
                $order_type = 'DESC';

                $date_begin = $date->formatToSearch($ssFilter['filter_date_begin']);
                $date_end 	= $date->formatToSearch($ssFilter['filter_date_end']);

                if ($paginator){
                    $select -> limit($paginator['itemCountPerPage'])
                        -> offset(($paginator['currentPageNumber'] - 1) * $paginator['itemCountPerPage']);
                }

                if(!empty($ssFilter['order_by']) && !empty($ssFilter['order'])) {
                    $order_by = strtoupper($ssFilter['order']);
                }
                if(!empty($ssFilter['order_type']) ) {
                    $order_type = strtoupper($ssFilter['order_type']);
                }
                $select -> order(array(TABLE_KOV_ORDERS.'.'.$order_by.' '.$order_type));

                if(isset($ssFilter['filter_CustomerPhone']) && $ssFilter['filter_CustomerPhone'] != '') {
                    $select->where->equalTo(TABLE_KOV_ORDERS.'.CustomerPhone', $ssFilter['filter_CustomerPhone']);
                }

                if(isset($ssFilter['filter_status_array'])) {
                    $select->where->in(TABLE_KOV_ORDERS.'.Status', $ssFilter['filter_status_array']);
                }

                if(!empty($date_begin) && !empty($date_end)) {
                    $select -> where -> NEST
                        -> greaterThanOrEqualTo(TABLE_KOV_ORDERS .'.CreatedDate', $date_begin)
                        ->AND
                        -> lessThanOrEqualTo(TABLE_KOV_ORDERS .'.CreatedDate', $date_end . ' 23:59:59')
                        -> UNNEST;
                } elseif (!empty($date_begin)) {
                    $select -> where -> greaterThanOrEqualTo(TABLE_KOV_ORDERS .'.CreatedDate', $date_begin);
                } elseif (!empty($date_end)) {
                    $select->where->lessThanOrEqualTo(TABLE_KOV_ORDERS .'.CreatedDate', $date_end . ' 23:59:59');
                }

                if(isset($ssFilter['filter_keyword']) && trim($ssFilter['filter_keyword']) != '') {
                    $select -> where -> NEST
                        ->like(TABLE_KOV_ORDERS.'.CustomerPhone', '%'.$ssFilter['filter_keyword'].'%')
                        ->Or
                        ->like(TABLE_KOV_ORDERS.'.CustomerName', '%'.$ssFilter['filter_keyword'].'%')
                        ->Or
                        ->like(TABLE_KOV_ORDERS.'.Code', '%'.$ssFilter['filter_keyword'].'%')
                        -> UNNEST;
                }
            });
        }

        if($options['task'] == 'list-all') {
            $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){
                $date = new \ZendX\Functions\Date();
                $paginator = $arrParam['paginator'];
                $ssFilter  = $arrParam['ssFilter'];
            });
        }

        if($options['task'] == 'list-export-template') {
            $result	= $this->tableGateway->select(function (Select $select) use ($arrParam, $options){

                $select -> join(TABLE_KOV_PRODUCT_BRANCH, TABLE_KOV_PRODUCT_BRANCH .'.productId = '. TABLE_KOV_PRODUCTS .'.id',
                    array(
                        'branch_cost' => 'cost',
                        'branch_cost_new' => 'cost_new',
                        'branch_fee' => 'fee',
                        'branch_name' => 'branchName',
                        'branch_id' => 'branchId',
                    ), 'inner');

                $select -> order(array(TABLE_KOV_PRODUCTS.'.id'));
                if(!empty($arrParam['ids'])) {
                    $select->where->in(TABLE_KOV_PRODUCTS.'.id', $arrParam['ids']);
                }
                if(!empty($arrParam['branches'])) {
                    $select->where->equalTo(TABLE_KOV_PRODUCT_BRANCH.'.branchId', $arrParam['branches']);
                }
            });
        }

        if($options['task'] == 'cache') {
            $cache = $this->getServiceLocator()->get('cache');
            $cache_key = 'KovProducts'. $arrParam['type'];
            $result = $cache->getItem($cache_key);

            if (empty($result)) {
                $items	= $this->tableGateway->select(function (Select $select) use ($arrParam){
                    $select -> order(array('fullName' => 'ASC'));
                });
                $result = \ZendX\Functions\CreateArray::create($items, array('key' => 'id', 'value' => 'object'));

                $cache->setItem($cache_key, $result);
            }
        }

        return $result;
    }

    public function getItem($arrParam = null, $options = null){

        if($options['task'] == 'by-name') {
            $result	= $this->tableGateway->select(function (Select $select) use ($arrParam){
                $select->where->equalTo('name', $arrParam['name']);
            })->current();
        }

        if($options == null) {
            $result	= $this->tableGateway->select(function (Select $select) use ($arrParam){
                $select->where->equalTo('Id', $arrParam['Id']);
            })->current();
        }
        return $result;
    }

    public function saveItem($arrParam = null, $options = null){
        $arrData  = $arrParam['data'];

        if($options['task'] == 'add') {
            $data = array(
                'Id'            => $arrData['Id'],
                'Code'          => $arrData['Code'],
                'BranchId'      => $arrData['BranchId'],
                'BranchName'    => $arrData['BranchName'],
                'SoldById'      => $arrData['SoldById'],
                'CustomerId'    => $arrData['CustomerId'],
                'CustomerCode'  => $arrData['CustomerCode'],
                'CustomerName'  => $arrData['CustomerName'],
                'CustomerPhone' => $arrData['CustomerPhone'],
                'Total'         => $arrData['Total'],
                'TotalPayment'  => $arrData['TotalPayment'],
                'SoldByName'    => $arrData['SoldByName'],
                'Status'        => $arrData['Status'],
                'StatusValue'   => $arrData['StatusValue'],
                'CreatedDate'   => $arrData['CreatedDate'],
            );

            $this->tableGateway->insert($data);
            return $arrData['Id'];
        }

        if($options['task'] == 'update') {
            $data = array(
                'Id'            => $arrData['Id'],
                'Code'          => $arrData['Code'],
                'BranchId'      => $arrData['BranchId'],
                'BranchName'    => $arrData['BranchName'],
                'SoldById'      => $arrData['SoldById'],
                'CustomerId'    => $arrData['CustomerId'],
                'CustomerCode'  => $arrData['CustomerCode'],
                'CustomerName'  => $arrData['CustomerName'],
                'CustomerPhone' => $arrData['CustomerPhone'],
                'Total'         => $arrData['Total'],
                'TotalPayment'  => $arrData['TotalPayment'],
                'SoldByName'    => $arrData['SoldByName'],
                'Status'        => $arrData['Status'],
                'StatusValue'   => $arrData['StatusValue'],
                'CreatedDate'   => $arrData['CreatedDate'],
            );

            $this->tableGateway->update($data, array('Id' => $arrData['Id']));
            return $arrData['Id'];
        }
    }

    public function changeStatus($arrParam = null, $options = null){
        if($options['task'] == 'change-status') {
            $result = $this->defaultStatus($arrParam, null);
        }

        return $result;
    }
}
<?php
namespace Admin\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use ZendX\System\UserInfo;

class KovInvoicesTable extends DefaultTable {

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

                $select -> limit($paginator['itemCountPerPage'])
                    -> offset(($paginator['currentPageNumber'] - 1) * $paginator['itemCountPerPage']);


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
                if(isset($ssFilter['filter_status']) && $ssFilter['filter_status'] != '') {
                    $select->where->equalTo(TABLE_KOV_PRODUCTS.'.status', $ssFilter['filter_status']);
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
                'SoldByName'    => $arrData['SoldByName'],
                'CustomerId'    => $arrData['CustomerId'],
                'CustomerCode'  => $arrData['CustomerCode'],
                'CustomerName'  => $arrData['CustomerName'],
                'Total'         => $arrData['Total'],
                'TotalPayment'  => $arrData['TotalPayment'],
                'Discount'      => $arrData['Discount'],
                'Status'        => $arrData['Status'],
                'StatusValue'   => $arrData['StatusValue'],
                'OrderId'       => $arrData['StatusValue'],
                'OrderCode'     => $arrData['StatusValue'],
                'CreatedDate'   => $arrData['StatusValue'],
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
                'SoldByName'    => $arrData['SoldByName'],
                'SoldById'      => $arrData['SoldById'],
                'CustomerId'    => $arrData['CustomerId'],
                'CustomerCode'  => $arrData['CustomerCode'],
                'CustomerName'  => $arrData['CustomerName'],
                'Total'         => $arrData['Total'],
                'TotalPayment'  => $arrData['TotalPayment'],
                'Discount'      => $arrData['Discount'],
                'Status'        => $arrData['Status'],
                'StatusValue'   => $arrData['StatusValue'],
                'OrderId'       => $arrData['OrderId'],
                'OrderCode'     => $arrData['OrderCode'],
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
<?php

namespace Admin\Model;

use Zend\Db\Sql\Select;
use Admin\Model\DefaultTable;

class FundsTable extends DefaultTable
{

    public function countItem($arrParam = null, $options = null)
    {
        if ($options['task'] == 'list-item') {
            $result = $this->defaultCount($arrParam, null);
        }

        return $result;
    }

    public function listItem($arrParam = null, $options = null)
    {
        if ($options['task'] == 'list-item') {
            $result = $this->defaultList($arrParam, null);
        }

        if ($options['task'] == 'list-all') {
            $items = $this->tableGateway->select(function (Select $select) use ($arrParam) {
                $select->order(array('ordering' => 'ASC', 'name' => 'ASC'));

                if (!empty($arrParam['company_branch_id'])) {
                    $select->where->equalTo('company_branch_id', $arrParam['company_branch_id']);
                }
            });
            $result = \ZendX\Functions\CreateArray::create($items, array('key' => 'id', 'value' => 'object'));
        }

        if ($options['task'] == 'cache') {
            $cache = $this->getServiceLocator()->get('cache');
            $cache_key = 'AccountantFunds';
            $result = $cache->getItem($cache_key);

            if (empty($result)) {
                $items = $this->tableGateway->select(function (Select $select) use ($arrParam) {
                    $select->order(array('ordering' => 'ASC', 'name' => 'ASC'));
                });
                $result = \ZendX\Functions\CreateArray::create($items, array('key' => 'id', 'value' => 'object'));

                $cache->setItem($cache_key, $result);
            }
        }

        if ($options['task'] == 'permision') {
            $result = $this->tableGateway->select(function (Select $select) use ($arrParam) {
                $arrData = $arrParam['data'];
                $arrRoute = $arrParam['route'];

                $select->order(array('ordering' => 'ASC', 'name' => 'ASC'));

                if (!empty($this->userInfo->getUserInfo('company_branch_id'))) {
                    $select->where->equalTo('company_branch_id', $this->userInfo->getUserInfo('company_branch_id'));
                }

            })->toArray();
        }

        return $result;
    }

    public function getItem($arrParam = null, $options = null)
    {

        if ($options == null) {
            $result = $this->defaultGet($arrParam, array('by' => 'id'));
        }

        return $result;
    }

    public function saveItem($arrParam = null, $options = null)
    {
        $arrData = $arrParam['data'];
        $arrRoute = $arrParam['route'];

        $filter = new \ZendX\Filter\Purifier();
        $number = new \ZendX\Functions\Number();
        $gid = new \ZendX\Functions\Gid();
        $user_ids     = $arrData['user_ids'] ? implode(',', $arrData['user_ids']) : '';

        if ($options['task'] == 'add-item') {
            $id = $gid->getId();
            $data = array(
                'id' => $id,
                'name' => $arrData['name'],
                'company_branch_id' => $arrData['company_branch_id'],
                'transaction_form_id' => $arrData['transaction_form_id'],
                'user_ids' => $user_ids,
                'price' => $number->formatToNumber($arrData['price']),
                'ordering' => $arrData['ordering'],
                'status' => $arrData['status'],
                'created' => date('Y-m-d H:i:s'),
                'created_by' => $this->userInfo->getUserInfo('id'),
            );

            $this->tableGateway->insert($data);
            return $id;
        }

        if ($options['task'] == 'edit-item') {
            $id = $arrData['id'];
            $data = array(
                'name' => $arrData['name'],
                'company_branch_id' => $arrData['company_branch_id'],
                'transaction_form_id' => $arrData['transaction_form_id'],
                'user_ids' => $user_ids,
//                'ordering' => $arrData['ordering'],
//                'status' => $arrData['status'],
            );

            $this->tableGateway->update($data, array('id' => $id));
            return $id;
        }

        // Update khi thêm mới nghiệp vụ thu chi
        if ($options['task'] == 'update-price') {
            $id = $arrData['accountant_funds_id'];
            $funds = $this->getItem(array('id' => $id));
            $price = $funds['price'] + $number->formatToNumber($arrData['paid']) - $number->formatToNumber($arrData['accrued']);
            $data = array('price' => $price);
            try {
                $this->tableGateway->update($data, array('id' => $id));
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Update Fund price failed: ' . $e->getMessage());
            }
        }

        // Update khi xóa nghiệp vụ thu chi
        if ($options['task'] == 'update-price-delete') {
            $id = $arrData['accountant_funds_id'];
            $funds = $this->getItem(array('id' => $id));
            $price = $funds['price'] - $number->formatToNumber($arrData['paid']) + $number->formatToNumber($arrData['accrued']);
            $data = array('price' => $price,);

            $this->tableGateway->update($data, array('id' => $id));
            return $id;
        }

        // Update khi sửa nghiệp vụ thu chi
        if ($options['task'] == 'update-price-only') {
            $id = $arrData['id'];
            $funds = $this->getItem(array('id' => $id));
            $price = $funds['price'] + $arrData['price'];
            $data = array('price' => $price,);

            $this->tableGateway->update($data, array('id' => $id));
            return $id;
        }
    }

    public function deleteItem($arrParam = null, $options = null)
    {
        if ($options['task'] == 'delete-item') {
            $result = $this->defaultDelete($arrParam, null);
        }

        return $result;
    }

    public function changeStatus($arrParam = null, $options = null)
    {
        if ($options['task'] == 'change-status') {
            $result = $this->defaultStatus($arrParam, null);
        }

        return $result;
    }

    public function changeOrdering($arrParam = null, $options = null)
    {
        if ($options['task'] == 'change-ordering') {
            $result = $this->defaultOrdering($arrParam, null);
        }
        return $result;
    }
}
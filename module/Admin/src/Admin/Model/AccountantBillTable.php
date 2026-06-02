<?php

namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;

class AccountantBillTable extends DefaultTable
{

    public function countItem($arrParam = null, $options = null)
    {
        if ($options['task'] == 'list-item') {
            $result = $this->tableGateway->select(function (Select $select) use ($arrParam, $options) {
                $ssFilter = $arrParam['ssFilter'];
                $date = new \ZendX\Functions\Date();
                $number = new \ZendX\Functions\Number();

                $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(1)')));

                if (isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $select->where->NEST
                        ->like('code', '%' . trim($ssFilter['filter_keyword'] . '%'))
                        //->or
                        //-> equalTo('code_auto', $number->formatToData($ssFilter['filter_keyword']))
                        ->or
                        ->like('content', '%' . trim($ssFilter['filter_keyword'] . '%'))
                        //->or
                        //-> equalTo('id', $number->formatToData($ssFilter['filter_keyword']))
                        ->or
                        ->like('submitter_name', '%' . trim($ssFilter['filter_keyword'] . '%'))
                        ->or
                        ->like('submitter_phone', '%' . trim($ssFilter['filter_keyword'] . '%'))
                        ->UNNEST;
                }

                if (!empty($ssFilter['filter_date_begin'])) {
                    $select->where->greaterThanOrEqualTo($ssFilter['filter_date_type'], $date->formatToData($ssFilter['filter_date_begin']));
                }

                if (!empty($ssFilter['filter_date_end'])) {
                    $select->where->lessThanOrEqualTo($ssFilter['filter_date_type'], $date->formatToData($ssFilter['filter_date_end']) . ' 23:59:59');
                }

                if (!empty($ssFilter['filter_date_begin']) && !empty($ssFilter['filter_date_end'])) {
                    $select->where->NEST
                        ->greaterThanOrEqualTo($ssFilter['filter_date_type'], $date->formatToData($ssFilter['filter_date_begin']))
                        ->and
                        ->lessThanOrEqualTo($ssFilter['filter_date_type'], $date->formatToData($ssFilter['filter_date_end']) . ' 23:59:59')
                        ->UNNEST;
                }

                if (!empty($ssFilter['filter_accountant_funds'])) {
                    $select->where->equalTo('accountant_funds_id', $ssFilter['filter_accountant_funds']);
                }

                if (!empty($ssFilter['filter_transaction_category'])) {
                    $select->where->equalTo('transaction_category_id', $ssFilter['filter_transaction_category']);
                }

                if (!empty($ssFilter['filter_transaction_type'])) {
                    $select->where->equalTo('transaction_type_id', $ssFilter['filter_transaction_type']);
                }

                if (!empty($ssFilter['filter_transaction_form'])) {
                    $select->where->equalTo('transaction_form_id', $ssFilter['filter_transaction_form']);
                }

                if (!empty($ssFilter['filter_category'])) {
                    $select->where->equalTo('category_id', $ssFilter['filter_category']);
                }

                if (!empty($ssFilter['filter_product'])) {
                    $select->where->equalTo('product_id', $ssFilter['filter_product']);
                }

                if (!empty($ssFilter['filter_training_class'])) {
                    $select->where->equalTo('training_class_id', $ssFilter['filter_training_class']);
                }

                if (!empty($ssFilter['filter_hbr_course'])) {
                    $select->where->equalTo('hbr_course_id', $ssFilter['filter_hbr_course']);
                }

                if (!empty($ssFilter['filter_status'])) {
                    $select->where->equalTo('status', $ssFilter['filter_status']);
                }

                if (!empty($ssFilter['filter_company_branch_sale'])) {
                    $select->where->equalTo('company_branch_sale_id', $ssFilter['filter_company_branch_sale']);
                }
            })->current();
        }

        return $result->count;
    }

    public function listItem($arrParam = null, $options = null)
    {
        if ($options['task'] == 'list-item') {
            $result = $this->tableGateway->select(function (Select $select) use ($arrParam, $options) {
                $paginator = $arrParam['paginator'];
                $ssFilter = $arrParam['ssFilter'];
                $date = new \ZendX\Functions\Date();
                $number = new \ZendX\Functions\Number();

                $date_begin = $date->formatToData($ssFilter['filter_date_begin']);
                $date_end = $date->formatToData($ssFilter['filter_date_end']);

                if (!isset($options['paginator']) || $options['paginator'] != false) {
                    $select->limit($paginator['itemCountPerPage'])
                        ->offset(($paginator['currentPageNumber'] - 1) * $paginator['itemCountPerPage']);
                }

                $select->order(array('created' => 'DESC', 'id' => 'ASC'));

                if (isset($ssFilter['filter_keyword']) && $ssFilter['filter_keyword'] != '') {
                    $select->where->NEST
                        ->like('code', '%' . trim($ssFilter['filter_keyword'] . '%'))
                        //->or
                        //-> equalTo('code_auto', $number->formatToData($ssFilter['filter_keyword']))
                        ->or
                        ->like('content', '%' . trim($ssFilter['filter_keyword'] . '%'))
                        //->or
                        //-> equalTo('id', $number->formatToData($ssFilter['filter_keyword']))
                        ->or
                        ->like('submitter_name', '%' . trim($ssFilter['filter_keyword'] . '%'))
                        ->or
                        ->like('submitter_phone', '%' . trim($ssFilter['filter_keyword'] . '%'))
                        ->UNNEST;
                }

                if (!empty($ssFilter['filter_date_begin'])) {
                    $select->where->greaterThanOrEqualTo($ssFilter['filter_date_type'], $date_begin);
                }

                if (!empty($ssFilter['filter_date_end'])) {
                    $select->where->lessThanOrEqualTo($ssFilter['filter_date_type'], $date_end . ' 23:59:59');
                }

                if (!empty($ssFilter['filter_date_begin']) && !empty($ssFilter['filter_date_end'])) {
                    $select->where->NEST
                        ->greaterThanOrEqualTo($ssFilter['filter_date_type'], $date_begin)
                        ->and
                        ->lessThanOrEqualTo($ssFilter['filter_date_type'], $date_end . ' 23:59:59')
                        ->UNNEST;
                }

                if (!empty($ssFilter['filter_accountant_funds'])) {
                    $select->where->equalTo('accountant_funds_id', $ssFilter['filter_accountant_funds']);
                }

                if (!empty($ssFilter['filter_transaction_category'])) {
                    $select->where->equalTo('transaction_category_id', $ssFilter['filter_transaction_category']);
                }

                if (!empty($ssFilter['filter_transaction_type'])) {
                    $select->where->equalTo('transaction_type_id', $ssFilter['filter_transaction_type']);
                }

                if (!empty($ssFilter['filter_transaction_form'])) {
                    $select->where->equalTo('transaction_form_id', $ssFilter['filter_transaction_form']);
                }

                if (!empty($ssFilter['filter_category'])) {
                    $select->where->equalTo('category_id', $ssFilter['filter_category']);
                }

                if (!empty($ssFilter['filter_product'])) {
                    $select->where->equalTo('product_id', $ssFilter['filter_product']);
                }

                if (!empty($ssFilter['filter_training_class'])) {
                    $select->where->equalTo('training_class_id', $ssFilter['filter_training_class']);
                }

                if (!empty($ssFilter['filter_hbr_course'])) {
                    $select->where->equalTo('hbr_course_id', $ssFilter['filter_hbr_course']);
                }

                if (!empty($ssFilter['filter_status'])) {
                    $select->where->equalTo('status', $ssFilter['filter_status']);
                }

                if (!empty($ssFilter['filter_sale_branch_id'])) {
                    $select->where->equalTo('sale_branch_id', $ssFilter['filter_sale_branch_id']);
                }
            });
        }

        if ($options['task'] == 'cache') {
            $cache = $this->getServiceLocator()->get('cache');
            $cache_key = 'AccountantBill';
            $result = $cache->getItem($cache_key);

            if (empty($result)) {
                $items = $this->tableGateway->select(function (Select $select) use ($arrParam) {
                    $select->order(array('ordering' => 'ASC', 'name' => 'ASC'));
                });
                $result = \ZendX\Functions\CreateArray::create($items, array('key' => 'id', 'value' => 'object'));

                $cache->setItem($cache_key, $result);
            }
        }

        return $result;
    }

    public function getItem($arrParam = null, $options = null)
    {

        if ($options == null) {
            $result = $this->defaultGet($arrParam, array('by' => 'id'));
        }

        if ($options['task'] == 'last') {
            $result = $this->tableGateway->select(function (Select $select) use ($arrParam) {
                $select->order(array('id' => 'DESC'))
                    ->limit(1);
            })->current();
        }

        return $result;
    }

    public function saveItem($arrParam = null, $options = null)
    {
        $arrData = $arrParam['data'];
        $arrItem = $arrParam['item'];
        $arrRoute = $arrParam['route'];

        $number = new \ZendX\Functions\Number();
        $date = new \ZendX\Functions\Date();
        $gid = new \ZendX\Functions\Gid();

        if ($options['task'] == 'add-item') {
            // Lấy thông tin sổ quỹ
            $accountant_funds = $this->getServiceLocator()->get('Admin\Model\FundsTable')->getItem(array('id' => $arrData['accountant_funds_id']));
            $funds = $accountant_funds['price'] + $number->formatToNumber($arrData['paid']) - $number->formatToNumber($arrData['accrued']);
            $content = ($arrData['content_select'] != 'other') ? $arrData['content_select'] . ' ' . $arrData['content'] : $arrData['content'];

            $id = $gid->getId();
            $data = array(
                'id' => $id,
                'date' => $date->formatToData($arrData['date'], 'Y-m-d'),
                'code' => strtoupper(trim($arrData['code'])),
                'accountant_funds_id' => $arrData['accountant_funds_id'],
                'transaction_category_id' => $arrData['transaction_category_id'],
                'transaction_type_id' => $arrData['transaction_type_id'],
                'transaction_form_id' => $arrData['transaction_form_id'],
                'category_id' => $arrData['category_id'],
                'content' => $content,
                'sale_branch_id' => $accountant_funds['company_branch_id'],
                'created_item_id' => $this->userInfo->getUserInfo('id'),
                'submitter_name' => $arrData['submitter_name'],
                'submitter_phone' => $arrData['submitter_phone'],
                'paid' => $number->formatToNumber($arrData['paid']),
                'accrued' => $number->formatToNumber($arrData['accrued']),
                'funds' => $number->formatToNumber($funds),
                'note' => $arrData['note'],
                'customer_debt_id' => $arrData['customer_debt_id'] ?: null,
                'inventory_id' => $arrData['inventory_id'] ?: null,
                'status' => $arrData['customer_debt_id'] ? 1 : 0,
                'created' => date('Y-m-d H:i:s'),
                'created_by' => $this->userInfo->getUserInfo('id'),
            );
            try {
                $this->tableGateway->insert($data);
                $this->getServiceLocator()->get('Admin\Model\FundsTable')->saveItem(array('data' => $arrData), array('task' => 'update-price'));
                return $id;
            } catch (\Exception $e) {
                throw new \Exception('Insert Acountan Bill Table failed: ' . $e->getMessage());
            }
        }

        if ($options['task'] == 'edit-item') {
            $id = $arrData['id'];
            $content = ($arrData['content_select'] != 'other') ? $arrData['content_select'] . ' ' . $arrData['content'] : $arrData['content'];
            $data = array(
                'date' => $date->formatToData($arrData['date']),
                'code' => strtoupper(trim($arrData['code'])),
                'transaction_category_id' => $arrData['transaction_category_id'],
                'transaction_type_id' => $arrData['transaction_type_id'],
                'transaction_form_id' => $arrData['transaction_form_id'],
                'category_id' => $arrData['category_id'],
                'content' => $content,
                'created_item_id' => $arrData['created_item_id'],
                'submitter_name' => $arrData['submitter_name'],
                'submitter_phone' => $arrData['submitter_phone'],
                'note' => $arrData['note'],
                'modified' => date('Y-m-d H:i:s'),
                'modified_by' => $this->userInfo->getUserInfo('id'),
            );

            $funds_changer = 0;
            // Cập nhật lại số tiền nếu sửa
            if ($arrItem['status'] == 0) {
                if ($this->userInfo->getUserInfo('id') == '1111111111111111111111') {
                    if (($number->formatToNumber($arrData['paid']) != $arrItem['paid']) || ($number->formatToNumber($arrData['accrued']) != $arrItem['accrued'])) {
                        $data['paid'] = $arrData['paid'] ? $number->formatToNumber($arrData['paid']) : 0;
                        $data['accrued'] = $arrData['accrued'] ? $number->formatToNumber($arrData['accrued']) : 0;
                        $data['funds'] = $arrItem['funds'] - $arrItem['paid'] + $arrItem['accrued'] + $number->formatToNumber($arrData['paid']) - $number->formatToNumber($arrData['accrued']);
                        $funds_changer = $data['funds'] - $arrItem['funds'];
                    }
                }
            }
            try {
                $this->tableGateway->update($data, array('id' => $id));

                // Update lại số tồn
                if ($this->userInfo->getUserInfo('id') == '1111111111111111111111') {
                    if ($funds_changer != 0) {
                        // Lấy tất cả phiếu lớn hơn phiếu hiện tại
                        $items = $this->tableGateway->select(function (Select $select) use ($arrItem, $date) {
                            $select->where->greaterThan('created', $arrItem['created'])
                                ->equalTo('accountant_funds_id', $arrItem['accountant_funds_id']);
                            $select->order(array('created' => 'ASC', 'id' => 'ASC'));
                        })->toArray();

                        $count_item = count($items);
                        if ($count_item > 0) {
                            foreach ($items as $key => $item) {
                                $this->saveItem(array('data' => array('funds' => $item['funds'] + $funds_changer, 'id' => $item['id'])), array('task' => 'update-funds'));
                            }
                        }

                        $this->getServiceLocator()->get('Admin\Model\FundsTable')->saveItem(array('data' => array('price' => $funds_changer, 'id' => $arrItem['accountant_funds_id'])), array('task' => 'update-price-only'));
                    }
                }
                return 'edit';
            } catch (\Exception $e) {
                throw new \Exception('Update Acountan Bill Table failed: ' . $e->getMessage());
            }
        }

        if ($options['task'] == 'update-funds') {
            $id = $arrData['id'];
            $data = array(
                'funds' => $arrData['funds'],
            );
            try {
                $this->tableGateway->update($data, array('id' => $id));
                return 'update-funds';
            } catch (\Exception $e) {
                throw new \Exception('Update Warehouse input Table failed: ' . $e->getMessage());
            }
        }
    }

    public function deleteItem($arrParam = null, $options = null)
    {
        if ($options['task'] == 'delete-item') {
            $arrItem = $arrParam['item'];
            $number = new \ZendX\Functions\Number();

            // Update lại các phần từ còn lại
            if ($arrItem['status'] == 0) {
                // Cập nhật lại các giá trị còn lại
                if (!empty($arrItem['paid'])) {
                    $dataUpdate = array('funds' => new Expression('(`funds` - ?)', array($number->formatToNumber($arrItem['paid']))));
                    $whereUpdate = new Where();
                    $whereUpdate->greaterThan('id', $arrItem['id']);
                    $whereUpdate->equalTo('accountant_funds_id', $arrItem['accountant_funds_id']);
                    $this->tableGateway->update($dataUpdate, $whereUpdate);

                    // Cập nhật lại sổ quỹ
                    $accountant_funds = $this->getServiceLocator()->get('Admin\Model\FundsTable')->getItem(array('id' => $arrItem['accountant_funds_id']));
                    $price = $accountant_funds['price'] - $number->formatToNumber($arrItem['paid']);
                    $this->getServiceLocator()->get('Admin\Model\FundsTable')->saveItem(array('data' => $arrItem), array('task' => 'update-price-delete'));

                } else if (!empty($arrItem['accrued'])) {
                    $dataUpdate = array('funds' => new Expression('(`funds` + ?)', array($number->formatToNumber($arrItem['accrued']))));
                    $whereUpdate = new Where();
                    $whereUpdate->greaterThan('id', $arrItem['id']);
                    $whereUpdate->equalTo('accountant_funds_id', $arrItem['accountant_funds_id']);
                    $this->tableGateway->update($dataUpdate, $whereUpdate);

                    // Cập nhật lại sổ quỹ
                    $accountant_funds = $this->getServiceLocator()->get('Admin\Model\FundsTable')->getItem(array('id' => $arrItem['accountant_funds_id']));
                    $price = $accountant_funds['price'] + $number->formatToNumber($arrItem['accrued']);
                    $this->getServiceLocator()->get('Admin\Model\FundsTable')->saveItem(array('data' => $arrItem), array('task' => 'update-price-delete'));
                }

            }

            // Xóa
            $where = new Where();
            $where->equalTo('id', $arrItem['id']);
            $this->tableGateway->delete($where);
            $result = $arrItem['id'];
        }
        return $result;
    }

    public function changeStatus($arrParam = null, $options = null)
    {
        $arrData = $arrParam['data'];
        if ($options['task'] == 'change-status') {
            if (!empty($arrData['cid'])) {
                if ($arrData['status'] == 1) {
                    $data = array(
                        'status' => 0,
                    );
                } else {
                    $data = array(
                        'status' => 1,
                        'check_by' => $this->userInfo->getUserInfo('id')
                    );
                }
                $this->tableGateway->update($data, array("id IN('" . implode("','", $arrData['cid']) . "')"));
                return true;
            }
        }
    }

    public function changeOrdering($arrParam = null, $options = null)
    {
        if ($options['task'] == 'change-ordering') {
            $result = $this->defaultOrdering($arrParam, null);
        }
        return $result;
    }
}
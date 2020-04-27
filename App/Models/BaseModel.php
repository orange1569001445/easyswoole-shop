<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of App
 *
 * @author Administrator
 */

namespace App\Models;

use EasySwoole\ORM\AbstractModel;

class BaseModel extends AbstractModel
{

    public function getById(int $id, $fields = '*')
    {
        $data = $this->field($fields)->get($id);
        return $data ? $data->toRawArray() : null;
    }

    /**
     * 返回分页数据
     * @param int $page
     * @param int $pagesize
     * @param array $field
     * @return array
     */
    public function getPageList($page = 1, $pagesize = 10, $fields = '*')
    {
        $page = max(1,intval($page));
        $model = $this->limit($pagesize * ($page - 1), $pagesize)->withTotalCount();

        // 列表数据
        $list = $model->field($fields)->all(null);
        
        $list = $this->allToArray($list);
        
        $result = $model->lastQueryResult();
        
        // 总条数
        $total = $result->getTotalCount();
        
        return ['total'=>$total,'data'=>$list];
    }

    /**
     * 将orm对象转为数组
     * @param type $list
     * @return array
     */
    public function allToArray($list)
    {
        $tmp = [];
        $i = 0;
        foreach ($list as $one) {
            $tmp[$i] = $one->toRawArray();
            $i++;
        }
        return $tmp;
    }

}

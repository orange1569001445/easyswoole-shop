<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Service\Admin;

/**
 * Description of AuthGroupService
 *
 * @author Administrator
 */
use App\Models\AuthRuleModel;
use App\Helper\TreeUtil;

class AuthRuleService
{
    protected $model;
    
    public function __construct()
    {
        $this->model = new AuthRuleModel();
    }

    public function getRulesTree($rules){
        $list = $this->model->where('id',$rules,'in')->all();
        return $this->model->allToArray($list);
    }
    
    /**
     * 通过id获得一条数据
     * @param type $id
     * @param type $fields
     * @return type
     */
    public function getAuthGroupById($id,$fields = '*')
    {
        $data = $this->model->field($fields)->getById($id);
        return  $data;
    }
    
    /**
     * 获得一个不分页的列表数据
     * @param type $where
     * @param type $field
     * @return type
     */
    public function getList($where,$field = '*'){
        $list = $this->model->field($field)->where($where)->all();
        return $this->model->allToArray($list);
    }

    
}

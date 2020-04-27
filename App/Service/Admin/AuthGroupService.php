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
use App\Models\AuthGroupModel;

class AuthGroupService
{

    protected $model;

    public function __construct()
    {
        $this->model = new AuthGroupModel();
    }

    /**
     * 返回分页数据
     * @param int $page
     * @param int $pagesize
     * @param array $field
     * @return object
     */
    public function getPageList($page, $pagesize, $field = '*')
    {
        return $this->model->getPageList($page, $pagesize, $field);
    }

    /**
     * 
     * @param array $where
     */
    public function getAuthGroupList($where = [], $field = '*')
    {
        $list = $this->model->field($field)->where($where)->all();
        return $this->model->allToArray($list);
    }

    /**
     * 
     * @param type $id
     * @param type $fields
     * @return type
     */
    public function getAuthGroupById($id, $fields = '*')
    {
        $data = $this->model->field($fields)->getById($id);
        return $data;
    }

}

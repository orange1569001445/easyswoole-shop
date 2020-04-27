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
use App\Models\LoginLogModel;

class LoginLogService
{

    private $model = '';

    public function __construct()
    {
        $this->model = new LoginLogModel();
    }

    public function insertLog($log)
    {
        return $this->model->data($log,false);
    }

    /**
     * 获得日志信息
     * @param type $id
     * @param type $fields
     * @return type
     */
    public function getLogInfoById($id, $fields = '*')
    {
        return $this->model->getById($id);
    }

}

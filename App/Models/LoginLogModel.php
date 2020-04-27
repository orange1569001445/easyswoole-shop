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

class LoginLogModel extends BaseModel
{

    // 都是非必选的，默认值看文档下面说明
    protected $autoTimeStamp = true;
    protected $createTime = false;
    protected $updateTime = 'updateTime';
    protected $tableName = 'tp_login_log';

}

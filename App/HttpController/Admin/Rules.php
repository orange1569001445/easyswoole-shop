<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Login
 *
 * @author Administrator
 */

namespace App\HttpController\Admin;

use App\HttpController\Base;
use App\Service\Admin\AuthRuleService;
use EasySwoole\Component\Context\ContextManager;

class Rules extends Base
{

    public function index()
    {
        $where = [];
        $list = (new AuthRuleService())->getList($where);
        return $this->apiSuccess(\App\Helper\CommonConstant::success, 'success', ['data' => $list]);
    }

    public function getinfo()
    {
        $request = $this->request();
        $id = $request->getRequestParam('id');
        $info = (new AuthRuleService())->getAuthGroupById($id);
        return $this->apiSuccess(\App\Helper\CommonConstant::success, 'success', $info);
    }

    public function getLists(){
        $list = (new AuthRuleService())->getList([]);
        return $this->apiSuccess(\App\Helper\CommonConstant::success, 'success', $list);
    }
    
}

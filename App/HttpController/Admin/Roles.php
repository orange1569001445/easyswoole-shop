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
use App\Service\Admin\AdminService;
use App\Service\Admin\AuthGroupService;
use EasySwoole\Component\Context\ContextManager;

class Roles extends Base
{

    public function index()
    {
        $request = $this->request();
        $data = $request->getRequestParam();
        $page = isset($data['page']) ? $data['page'] : 1;
        $pagesize = isset($data['pagesize']) ? $data['pagesize'] : 10;
        $list = (new AuthGroupService())->getPageList($page, $pagesize);
        return $this->apiSuccess(\App\Helper\CommonConstant::success, 'success', $list);
    }

    public function getLists()
    {
        $list = (new AuthGroupService())->getAuthGroupList([]);
        return $this->apiSuccess(\App\Helper\CommonConstant::success, 'success', $list);
    }

    public function getinfo()
    {
        $request = $this->request();
        $id = $request->getRequestParam('id');
        $service = new AdminService();
        $info = $service->getUserById($id, ['id', 'userName', 'password', 'email', 'realName', 'phone', 'img', 'regTime', 'regIp', 'loginTime', 'isEnabled', 'groupId']);

        return $this->apiSuccess(\App\Helper\CommonConstant::success, 'success', $info);
    }

}

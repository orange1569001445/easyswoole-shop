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
use App\Service\Admin\AuthRuleService;
use App\Service\Admin\AuthGroupService;
use EasySwoole\Component\Context\ContextManager;

class Admin extends Base
{

    public function index()
    {
        $request = $this->request();
        $data = $request->getRequestParam();
        $page = isset($data['page']) ? $data['page'] : 1;
        $pagesize = isset($data['pagesize']) ? $data['pagesize'] : 10;

        $service = new AdminService();
        $list = $service->getAdminPageList($page, $pagesize);

        return $this->apiSuccess(\App\Helper\CommonConstant::success, 'success', $list);
    }

    public function getinfo()
    {
        $request = $this->request();
        $id = $request->getRequestParam('id');
        $service = new AdminService();
        $info = $service->getUserById($id,['id','userName','password','email','realName','phone','img','regTime','regIp','loginTime','isEnabled','groupId']);
        
        return $this->apiSuccess(\App\Helper\CommonConstant::success, 'success', $info);
    }

    public function getuser()
    {
        $request = $this->request();
        $user_id = ContextManager::getInstance()->get('user_id');

        $service = new AdminService();

        $user = $service->getUserById($user_id, ['userName', 'password', 'email', 'realName', 'phone', 'img', 'groupId']);

        $group = (new AuthGroupService())->getAuthGroupById($user['groupId'], ['title', 'rules']);

        //获得权限节点
        $access = (new AuthRuleService())->getRulesTree(explode(',', $group['rules']));
        //$userToken = (new AdminService())->login($userName, $password);
        //把权限节点转为树状结构
        $access = \App\Helper\TreeUtil::listToTreeMulti($access, 0, 'id', 'pid', 'children');
        $routers = [];

        foreach ($access as $v) {
            $temp = $this->getdata($v);
            foreach ($v['children'] as $vo) {
                $temp['children'][] = $this->getdata($vo);
            }
            $routers[] = $temp;
        }

        $user['access'] = $routers;

        return $this->apiSuccess(\App\Helper\CommonConstant::success, 'success', $user);
    }

    protected function getdata($data)
    {
        $temp = [];
        $temp['path'] = $data['path'];
        $temp['component'] = $data['component'];
        $temp['name'] = $data['name'];
        if ($data['hidden'] > -1) {
            $temp['hidden'] = (boolean) $data['hidden'];
        }
        if ($data['alwaysShow'] > -1) {
            $temp['alwaysShow'] = (boolean) $data['alwaysShow'];
        }
        if ($data['redirect']) {
            $temp['redirect'] = $data['redirect'];
        }
        $temp['meta']['title'] = $data['title'];
        $temp['meta']['icon'] = $data['icon'];
        if ($data['noCache'] > -1) {
            $temp['meta']['noCache'] = (boolean) $data['noCache'];
        }

        return $temp;
    }

}

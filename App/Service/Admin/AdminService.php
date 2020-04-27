<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admin
 *
 * @author Administrator
 */

namespace App\Service\Admin;

use App\Models\AdminModel;
use App\Helper\Helper;
use App\Helper\CommonConstant;

class AdminService
{

    private $model = '';

    public function __construct()
    {
        $this->model = new AdminModel();
    }

    /**
     * 返回分页数据
     * @param int $page
     * @param int $pagesize
     * @param array $field
     * @return object
     */
    public function getAdminPageList($page, $pagesize, $field = '*')
    {
        return $this->model->getPageList($page,$pagesize,$field);
    }

    /**
     * 用户登录验证
     * @param type $userName
     * @param type $password
     */
    public function login(string $userName, string $password): array
    {

        $find = $this->model->field(['id', 'userName', 'password', 'isEnabled', 'groupId'])->where('userName', $userName)->get();
        if (!$find) {
            Helper::useThrowable(CommonConstant::e_user_miss_content, CommonConstant::e_user_miss);
        }

        //验证用户密码
        if (Helper::encrypt_pass($password) != $find->password) {
            Helper::useThrowable(CommonConstant::e_user_pass_wrong_content, CommonConstant::e_user_pass_wrong);
        }

        //用户是否被禁用
        if ($find->isEnabled != 1) {
            Helper::useThrowable(CommonConstant::e_user_disabled_content, CommonConstant::e_user_disabled);
        }

        //判断用户组是否被禁用
        $groupInfo = (new AuthGroupService())->getAuthGroupById($find->groupId, ['status', 'title']);
        if (!$groupInfo || $groupInfo['status'] != 1) {
            Helper::useThrowable(CommonConstant::e_user_role_disabled_content, CommonConstant::e_user_role_disabled);
        }
        //插入日志并更新时间
        $time = time();
        (new LoginLogService())->insertLog(['uid' => $find->id, 'userName' => $userName, 'roles' => $groupInfo['title'], 'loginTime' => $time, 'loginIp' => '127.0.0.1']);

        $this->updateInfoById($find->id, ['loginTime' => $time, 'loginIp' => '127.0.0.1']);


        //获得用户请求token
        $payload = ['uid' => $find->id, 'loginTime' => $time, 'type' => 'admin'];
        //$token = Helper::think_encrypt(\App\Helper\JwtUtil::encode($payload));
        $token = \App\Helper\JwtUtil::encode($payload);
        return ['userToken' => $token];
    }

    /**
     * 更新用户信息
     * @param array $where
     * @param array $data
     */
    public function updateInfoByWhere($where, $data)
    {
        return $this->model->where($where)->update($data);
    }

    /**
     * 通过id更新用户信息
     * @param type $id
     * @param type $data
     */
    public function updateInfoById($id, $data)
    {
        return $this->model->update($data, ['id' => $id]);
    }

    public function getUserById($id, $fields = '*')
    {
        return $this->model->getById($id, $fields);
    }

}

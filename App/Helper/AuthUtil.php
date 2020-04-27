<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helper;

/**
 * Description of AuthUtil
 *
 * @author Administrator
 */
class AuthUtil
{

    /**
     * 验证公用参数前面
     * @param type $appId 申请的appId
     * @param type $request
     * @return type
     */
    static public function checkSign($appId = null, $request)
    {
        $configInstance = \EasySwoole\EasySwoole\Config::getInstance();

        $param = $request->getRequestParam();
        $app = \App\Models\AppModel::create()->where(['appId' => $appId])->get();
        if (!$app) {
            Helper::useThrowable(CommonConstant::e_app_miss_content, CommonConstant::e_app_miss);
        }
        $app = $app->toarray();
        if ($app ['isEnabled'] != CommonConstant::db_true) {
            Helper::useThrowable(CommonConstant::e_app_disabled, CommonConstant::e_app_disabled_content);
        }

        // 接口签名认证
        if ($configInstance->getConf('SYSTEM.app_sign_auth_on') === true) {
            $signature = $param['signature']; // app端生成的签名
            unset($param['signature']);
            if (empty($signature)) {
                Helper::useThrowable(CommonConstant::e_api_sign_miss_content, CommonConstant::e_api_sign_miss);
            }
            //数组排序
            ksort($param);
            $str = http_build_query($param);
            $signature1 = md5(sha1($str) . $app['appSecret']);

            if ($signature != $signature1) {
                Helper::useThrowable(CommonConstant::e_api_sign_wrong_content, CommonConstant::e_api_sign_wrong);
            }
        }
        return Helper::msgStatus(true);
    }

    /**
     * 验证用户身份
     * @param string $type user 普通用户，admin 管理员，seller 商家
     * @return multitype:
     */
    public static function checkUser($type = 'user', $userToken = '')
    {
        // JWT用户令牌认证，令牌内容获取
        if (empty($userToken)) {
            Helper::useThrowable(CommonConstant::e_api_user_token_miss_content, CommonConstant::e_api_user_token_miss);
        }

        //$userToken = Helper::think_decrypt($userToken);
        $payload = JwtUtil::decode($userToken);
        
        if ($payload === false || empty($payload['uid']) || empty($payload['loginTime'] || $payload['type'] != $type)) {
            Helper::useThrowable(CommonConstant::e_api_user_token_miss_content, CommonConstant::e_api_user_token_miss);
        }
        
        
        if($type == 'user'){
           $model = new \App\Models\UserModel();
        }else{
            $model = new \App\Models\AdminModel();
        }
        
//        //用户登录有效期
//        $userLoginTime = config('system.user_login_time');
//        if ($payload->loginTime < time() - $userLoginTime) {
//            Helper::useThrowable(CommonConstant::e_api_user_token_expire_content, CommonConstant::e_api_user_token_expire);
//        }
        // 实时用户数据
        $user = $model->getById($payload['uid'],['loginTime','isEnabled']);

        //是否多设备登录
//        if (!empty($user ['loginTime']) && $user ['loginTime'] != $payload['loginTime']) {
//            Helper::useThrowable(CommonConstant::e_api_multiple_device_login_content, CommonConstant::e_api_multiple_device_login);
//            my_exception(null, CommonConstant::e_api_multiple_device_login);
//        }

        //认证：状态
        if ($user ['isEnabled'] != CommonConstant::db_true) {
            Helper::useThrowable(CommonConstant::e_user_disabled_content, CommonConstant::e_user_disabled);
        }
        return Helper::msgStatus(true,'success',$payload);
    }

}

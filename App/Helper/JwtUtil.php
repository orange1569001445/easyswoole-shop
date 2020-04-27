<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helper;

use EasySwoole\EasySwoole\Config;
use EasySwoole\Jwt\Jwt;

class JwtUtil
{

    static public function encode($payload)
    {
        $configInstance = Config::getInstance();
        $config = $configInstance->getConf('SYSTEM');
        // 读取配置
        $secretKey = $config['jwt_secret_key'];
        $algorithm = $config['jwt_algorithm'];
        $time = time();
        $timeout = ($time + $config['app_access_token_time']);    
        if (!$secretKey || !$algorithm) {
            Helper::useThrowable(CommonConstant::e_system_config_miss_content, CommonConstant::e_system_config_miss);
        }

        var_dump(date('Y-m-d H:i:s',$timeout));
        $jwtObject = Jwt::getInstance()
                ->setSecretKey($secretKey) // 秘钥
                ->publish();
        $jwtObject->setAlg($algorithm); // 加密方式
        $jwtObject->setAud('user'); // 用户
        $jwtObject->setExp($timeout); // 过期时间
        $jwtObject->setIat($time); // 发布时间
        $jwtObject->setIss('easyswoole-orange-shop'); // 发行人
        $jwtObject->setJti(md5(time())); // jwt id 用于标识该jwt
        $jwtObject->setSub('用户请求token'); // 主题
// 自定义数据
        $jwtObject->setData($payload);

// 最终生成的token
        $token = $jwtObject->__toString();

        return $token;
    }

    static public function decode($token)
    {
        // 读取配置
        $configInstance = Config::getInstance();
        $config = $configInstance->getConf('SYSTEM');
        $secretKey = $config['jwt_secret_key'];
        $algorithm = $config['jwt_algorithm'];
        if (!$secretKey || !$algorithm) {
            Helper::useThrowable(CommonConstant::e_system_config_miss_content, CommonConstant::e_system_config_miss);
        }
        // 使用Firebase JWT解码
        $jwtObject = Jwt::getInstance()->setSecretKey($secretKey)->decode($token);
        $status = $jwtObject->getStatus();

        $data = false;
        var_dump(date('Y-m-d H:i:s',$jwtObject->getExp()));
        var_dump($status);
        switch ($status) {
            case 1:
                $data = $jwtObject->getData();
                break;
            case -1:
                Helper::useThrowable(CommonConstant::e_api_user_token_expire_content, CommonConstant::e_api_user_token_expire);
                break;
            case -2:
                Helper::useThrowable(CommonConstant::e_api_user_token_expire_content, CommonConstant::e_api_user_token_expire);
                break;
        }
        return $data;
    }

}

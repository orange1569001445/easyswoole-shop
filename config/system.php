<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return [
        'pass_salt' => '58&%97@*^54*89',
        // jwt签名密钥
        'jwt_secret_key' => '58&%97@*^54*89@***###',
        // jwt算法 ，可配置的值取决于使用的jwt包支持哪些算法
        'jwt_algorithm' => 'HMACSHA256',
        // 是否开启app接口签名认证，为方便测试可以关闭，正式环境必须开启
        'app_sign_auth_on' => true,
        // app接口加密安全key
        'app_sign_key' => '#l_vle_ll_e%+$^@0608)[@***###',
        //app访问token有效期
        'app_access_token_time' => 60 * 60 * 24 * 7,
        //用户登录有效期
        'user_login_time' => 60 * 60 * 24 * 7,
];

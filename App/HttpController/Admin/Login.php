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

class Login extends Base
{

    public function index()
    {
        $request = $this->request();
        $userName = $request->getRequestParam('userName');
        $password = $request->getRequestParam('password');
        
        $userToken = (new AdminService())->login($userName, $password);
        
        return $this->apiSuccess(\App\Helper\CommonConstant::success,'success',$userToken);
    }

}

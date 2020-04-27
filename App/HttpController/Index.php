<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Base
{

    public function index()
    {
        $file = EASYSWOOLE_ROOT . '/vendor/easyswoole/easyswoole/src/Resource/Http/welcome.html';
        if (!is_file($file)) {
            $file = EASYSWOOLE_ROOT . '/src/Resource/Http/welcome.html';
        }
        $this->response()->write(file_get_contents($file));
    }

    public function test()
    {
        $request = $this->request();        
        //$auth = \App\Helper\AuthUtil::checkSign($this->getHeaderParams('x-access-appid'),$request);
       // $auth = \App\Helper\Helper::think_encrypt('张三');
       // $decrypt = \App\Helper\Helper::think_decrypt($auth);
       // $auth = \App\Helper\AuthUtil::checkUser('admin',$this->getHeaderParams('x-access-token'));
        $auth = \App\Helper\JwtUtil::encode(['uid'=>1,'loginTime'=> time(),'type'=>'admin']);
        $auth = \App\Helper\Helper::think_encrypt($auth);
        //$decode = \App\Helper\JwtUnit::decode($auth);
        $data = \App\Helper\AuthUtil::checkUser('admin',$auth);
       
        $this->response()->write(json_encode(['auth'=>$auth,'decode'=>$data]));
    }



}

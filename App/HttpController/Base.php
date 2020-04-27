<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Component\Context\ContextManager;

abstract class Base extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $configInstance = \EasySwoole\EasySwoole\Config::getInstance();
        set_error_handler([$this, 'errorException']);
    }

    /*     * *
     * 自定义错误处理方法
     */

    public function errorException($errno, $errstr, $errfile, $errline)
    {
        throw new \Exception($errstr);
    }

    public function index()
    {
        
    }

    public function getHeaderParams(...$key)
    {
        $header = $this->request()->getHeaders();
        $res = [];
        if (empty($key)) {
            foreach ($header as $key => $val) {
                $res[$key] = $val[0];
            }
            return $res;
        }

        foreach ($key as $item) {
            $res[$item] = isset($header[$item][0]) ? $header[$item][0] : null;
        }
        if (count($key) == 1) {
            return array_shift($res);
        }
        return $res;
    }

    public function onException(\Throwable $throwable): void
    {
        if ($throwable instanceof \App\Throwables\paramThrowable) {
            $this->apiFaild($throwable->getCode(), $throwable->getMessage());
            return;
        }

        $this->apiFaild($throwable->getCode(), $throwable->getMessage(), $throwable->getTraceAsString());
    }

    /**
     * 成功时json返回
     * @param type $code
     * @param type $message
     * @param type $data
     */
    public function apiSuccess($code, $message = 'success', $data = null)
    {
        if (!$this->response()->isEndResponse()) {
            $this->response()->write($this->msgStatus(1, $code, $message, $data));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus(200);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 失败时json返回
     * @param type $code
     * @param type $message
     * @param type $data
     */
    public function apiFaild($code, $message = 'faild', $data = null)
    {
        if (!$this->response()->isEndResponse()) {
            $this->response()->write($this->msgStatus(0, $code, $message, $data));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus(200);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 定义返回值格式
     * @param bool $status 返回状态
     * @param string $message 错误时返回错误内容
     * @param type $data 返回格式数据 array 或字符串
     * @return array
     */
    public function msgStatus(int $status, $code = 10000, string $message = '', $data = null)
    {
        return json_encode(['status' => $status, 'code' => $code, 'msg' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function onRequest(?string $action): ?bool
    {
        $noCheckTokenArrays = [
            '/admin/login/index'
        ];
        $request = $this->request();
        
        //\App\Helper\AuthUtil::checkSign($this->getHeaderParams('x-access-appid'),$request);
        $path = $request->getUri()->getPath();
        if(!in_array($path,$noCheckTokenArrays)){
           
            $users = \App\Helper\AuthUtil::checkUser('admin', $this->getHeaderParams('x-access-token'));
            ContextManager::getInstance()->set('user_id',$users['data']['uid']);
        }                
        
        return true;
    }
    
    protected function actionNotFound(?string $action)
    {
        $this->response()->write($this->request()->getUri()->getPath());
        $this->response()->withStatus(404);
        $file = EASYSWOOLE_ROOT . '/vendor/easyswoole/easyswoole/src/Resource/Http/404.html';
        if (!is_file($file)) {
            $file = EASYSWOOLE_ROOT . '/src/Resource/Http/404.html';
        }
        $this->response()->write(file_get_contents($file));
    }

}

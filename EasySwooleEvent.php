<?php

namespace EasySwoole\EasySwoole;

use EasySwoole\Component\Process\Manager;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\Db\Config;
//http 监察工具
use Siam\HttpMonitor\Config as HttpConfig;
use Siam\HttpMonitor\Monitor;
//kafka消费者
use App\Consumer\KafkaConsumer;
use App\Consumer\KafkaConsumer1;
use App\Consumer\KafkaConsumer2;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        //注册mysql orm连接池
        $mysql = \EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL');
        $config = new Config();
        $config->setDatabase('vueapi');
        $config->setUser('root');
        $config->setPassword('root');
        $config->setHost('192.168.0.107');
        DbManager::getInstance()->addConnection(new Connection($config));

        //注册redis连接池
        $redis = \EasySwoole\EasySwoole\Config::getInstance()->getConf('REDIS');
        \EasySwoole\RedisPool\Redis::getInstance()->register('redis', new \EasySwoole\Redis\Config\RedisConfig($redis));
    }

    public static function mainServerCreate(EventRegister $register)
    {
        //注册kafka消费者
        //ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\Consumer\Process())->getProcess());
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new KafkaConsumer('KafkaConsumer1', 'arg'))->getProcess());
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new KafkaConsumer2('KafkaConsumer2', 'arg'))->getProcess());
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new KafkaConsumer1('KafkaConsumer3'))->getProcess());

        $register->add($register::onWorkerStart, function () {
            //链接预热
            DbManager::getInstance()->getConnection()->getClientPool()->keepMin();
        });
        //请求日志
        $HttpConfig = new HttpConfig([
            'size' => 20, //最大缓存数量
            'listUrl' => '/siam/http-monitor/get_list',
            'resendUrl' => '/siam/http-monitor/resend',
        ]);
        $monitor = Monitor::getInstance($HttpConfig);

        // 添加白名单 无需记录
        $monitor->addFilter('/siam/http-monitor/get_list');
        $monitor->addFilter('/favicon.ico');
        $monitor->addFilter('/siam/http-monitor');
        $monitor->addFilter('/siam/http-monitor/resend');
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.

        /**
         * 设置跨域
         */
        $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->withHeader('Access-Control-Allow-Credentials', 'true');
        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        if ($request->getMethod() === 'OPTIONS') {
            $response->withStatus(Status::CODE_OK);
            return false;
        }

        Monitor::getInstance()->log([
            'header' => $request->getHeaders(),
            'server' => $request->getServerParams(),
            'get' => $request->getQueryParams(),
            'post' => $request->getParsedBody(),
            'cookie' => $request->getCookieParams(),
            'files' => $request->getUploadedFiles(),
            'rawContent' => $request->getBody()->__toString(),
            'data' => $request->getSwooleRequest()->getData(),
        ]);
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }

}

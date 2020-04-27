<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
//http请求监视组件
use Siam\HttpMonitor\Config;
use Siam\HttpMonitor\Monitor;

/**
 * Description of Router
 *
 * @author Administrator
 */
class Router extends AbstractRouter
{

    function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->get('/siam/http-monitor', function (Request $request, Response $response) {
            $monitor = Monitor::getInstance();
            $html = $monitor->listView();
            $response->withHeader('Content-type', 'text/html;charset=utf-8');
            $response->write("$html"); //获取到路由匹配的id
            return false; //不再往下请求,结束此次响应
        });

// 获取历史列表
        $routeCollector->addRoute(['POST', 'GET'], '/siam/http-monitor/get_list', function (Request $request, Response $response) {
            $response->withHeader('Content-type', 'text/html;charset=utf-8');
            $response->write(Monitor::getInstance()->getList()); //获取到路由匹配的id
            return false; //不再往下请求,结束此次响应
        });

// 复发请求
        $routeCollector->addRoute(['POST'], '/siam/http-monitor/resend', function (Request $request, Response $response) {
            $content = $request->getBody()->__toString();
            $content = json_decode($content, true);
            $response->write(Monitor::getInstance()->resend($content['id']));
            return false; //不再往下请求,结束此次响应
        });
    }

}

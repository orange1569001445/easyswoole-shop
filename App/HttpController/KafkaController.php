<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Kafka\Config\ProducerConfig;
use EasySwoole\Kafka\Kafka;

/**
 * Description of Kafka
 *
 * @author Administrator
 */
class KafkaController extends Controller
{

    public function index()
    {
        $config = new ProducerConfig();
        $config->setMetadataBrokerList('127.0.0.1:9092');
        $config->setBrokerVersion('0.9.0');
        $config->setRequiredAck(1);

        $kafka = new Kafka($config);
        $result = $kafka->producer()->send([
                [
                'topic' => 'orange',
                'value' => 'this is message'.date('Y-m-d H:i:s'),
                'key' => date('Y-m-d H:i:s'),
                'content'=>'this is content'
            ],
        ]);

        //var_dump($result);
        var_dump('ok');
    }

}

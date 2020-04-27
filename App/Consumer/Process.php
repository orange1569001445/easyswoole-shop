<?php

namespace App\Consumer;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Kafka\Config\ProducerConfig;
use EasySwoole\Kafka\Kafka;

class Process extends AbstractProcess
{

    protected function run($arg)
    {
        go(function () {
            $config = new ProducerConfig();
            $config->setMetadataBrokerList('127.0.0.1:9092');
            $config->setBrokerVersion('0.9.0');
            //$config->setRequiredAck(1);

            $kafka = new Kafka($config);
            //var_dump($kafka->producer());
                $result = $kafka->producer()->send([
                        [
                        'topic' => 'easyswoole',
                        'value' => '1',
                        'key' => date('Y-m-d H:i:s'),
                        'content' => '1'
                    ],
                ]);


            //var_dump($result);
            var_dump('ok');
        });
    }

}

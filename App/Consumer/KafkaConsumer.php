<?php

namespace App\Consumer;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Kafka\Config\ConsumerConfig;
use EasySwoole\Kafka\Kafka;

class KafkaConsumer extends AbstractProcess
{

    protected function run($arg)
    {

        var_dump($this->getArg());
        go(function ()use($arg) {
                $config = new ConsumerConfig();
                $config->setRefreshIntervalMs(300);
                $config->setMetadataBrokerList('127.0.0.1:9092');
                $config->setBrokerVersion('0.9.0');
                $config->setGroupId('test-consumer-group');

                $config->setTopics(['orange']);
                // $config->setOffsetReset('earliest');

                $kafka = new Kafka($config);

                // 设置消费回调
                $func = function ($topic, $partition, $message)use($arg) {
                    var_dump($topic);
                    var_dump($partition);
                    var_dump($this->getProcessName().$message['message']['key']);
                };
                //var_dump($func);
                $data = $kafka->consumer()->subscribe($func);
        });
    }

}

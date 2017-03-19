<?php

namespace Rapture\Helper\Definition;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Rabbit helper for worker
 *
 * @package Rapture\Helper
 * @author  Iulian N. <rapture@iuliann.ro>
 * @license LICENSE MIT
 */
trait RabbitCommandTrait
{
    /**
     * @param string $list   List name
     * @param array  $config Config data
     */
    public function listen($list, array $config)
    {
        $connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['pass']);
        $channel    = $connection->channel();

        $channel->queue_declare($list, false, true, false, false);

        $worker   = $this;
        $callback = function($msg) use ($worker) {
            $worker->process($msg);
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($list, '', false, false, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    /**
     * Process - Overwrite this in classes and REMEMBER to ack!
     *
     * @param AMQPMessage $msg
     *
     * @return void
     */
    public function process(AMQPMessage $msg)
    {
        $this->ack($msg);
    }

    /**
     * @param AMQPMessage $msg
     *
     * @return void
     */
    public function ack(AMQPMessage $msg)
    {
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }
}

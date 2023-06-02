<?php


namespace App\Services;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    public function sendMessage($data){
        $data = \GuzzleHttp\json_encode($data);
        $exchange = 'PJY-TEST-2';
        $query = 'PJY-TEST-2';
        $routing_key = 'PJY.TEST.2';
        $connection = new AMQPStreamConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_LOGIN'), env('RABBITMQ_PASSWORD'));
        $channel = $connection->channel();

        //新建交换机
        $channel->exchange_declare($exchange, 'topic', false, false, false);

        //新建队列
        $channel->queue_declare($query);

        $channel->queue_bind($query, $exchange, $routing_key);

        $msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

        $channel->basic_publish($msg, $exchange, $routing_key);

        $channel->close();
        $connection->close();
    }
}
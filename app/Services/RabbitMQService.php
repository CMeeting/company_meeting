<?php


namespace App\Services;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    public function sendMessage($data){
        $data = \GuzzleHttp\json_encode($data);
        $exchange = 'background-exchange';
        $query = 'operation-back-asset-queue';
        $routing_key = 'operation.back.asset';
        $connection = new AMQPStreamConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_LOGIN'), env('RABBITMQ_PASSWORD'));
        $channel = $connection->channel();

        //新建交换机
        $channel->exchange_declare($exchange, 'topic', true, true, false);

        //新建队列
        $channel->queue_declare($query, true, true, false, false);

        $channel->queue_bind($query, $exchange, $routing_key);

        $msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

        $channel->basic_publish($msg, $exchange, $routing_key);

        $channel->close();
        $connection->close();
    }
}
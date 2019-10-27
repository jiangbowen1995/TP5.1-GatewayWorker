<?php
namespace app\api\controller\v1\home;
use think\Controller;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\Request;

class Producter extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function sendMessage()
    {
        $queue = 'msgs';
        $exchange = 'router';
        $connection = new AMQPConnection('localhost', 5672, 'rabbitadmin', '123456','/');
        $channel = $connection->channel();
        $channel->queue_declare(
            $queue,    #queue - Queue names may be up to 255 bytes of UTF-8 characters
            false,            #passive - can use this to check whether an exchange exists without modifying the server state
            true,             #durable, make sure that RabbitMQ will never lose our queue if a crash occurs - the queue will survive a broker restart
            false,            #exclusive - used by only one connection and the queue will be deleted when that connection closes
            false              #auto delete - queue is deleted when last consumer unsubscribes
        );
        $channel->exchange_declare($exchange, 'direct', false, true, false);

        $channel->queue_bind($queue, $exchange);
        // $argv2 = array('jiangbowen to heppe');
        // $messageBody = implode(' ', array_slice($argv2, 1));
        // $messageBody = serialize($argv)
        $messageBody = 'hello world';
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, $exchange);

        $channel->close();
        $connection->close();
    }
}

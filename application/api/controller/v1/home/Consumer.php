<?php
namespace app\api\controller\v1\home;
use think\Controller;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\Request;

class Consumer extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function receMessage()
    {
        $exchange = 'router';
        $queue = 'msgs';
        $connection = new AMQPConnection('localhost', 5672, 'rabbitadmin', '123456','/');
        $channel = $connection->channel ();

        $channel->queue_declare ( $queue, // queue
            false, // passive
            true, // durable, make sure that RabbitMQ will never lose our queue if a crash occurs
            false, // exclusive - queues may only be accessed by the current connection
            false ); // auto delete - the queue is deleted when all consumers have finished using it
//        /**
//         * don't dispatch a new message to a worker until it has processed and
//         * acknowledged the previous one.
//         * Instead, it will dispatch it to the
//         * next worker that is not still busy.
//            */
        // $channel->basic_qos ( null, // prefetch size - prefetch window size in octets, null meaning "no specific limit"
        //         1, // prefetch count - prefetch window in terms of whole messages
        //         null ); // global - global=null to mean that the QoS settings should apply per-consumer, global=true to mean that the QoS settings should apply
//        // per-channel
//        /**
//         * indicate interest in consuming messages from a particular queue.
//         * When they do
//        * so, we say that they register a consumer or, simply put, subscribe to a queue.
//        * Each consumer (subscription) has an identifier called a consumer tag
//        */

        $channel->exchange_declare($exchange, 'direct', false, true, false);

        $channel->queue_bind($queue, $exchange);
        $channel->basic_consume ( $queue, // queue
            '', // consumer tag - Identifier for the consumer, valid within the current channel. just string
            false, // no local - TRUE: the server will not send messages to the connection that published them
            false, // no ack, false - acks turned on, true - off. send a proper acknowledgment from the worker,
            // once
            // we're done with a task
            false, // exclusive - queues may only be accessed by the current connection
            false, // no wait - TRUE: the server will not respond to the method. The client should not wait for a reply method
            array($this,'process_message')
        ); // callback
        // while ( count ( $channel->callbacks ) ) {
        //     //          $this->log->addInfo ( 'Waiting for incoming messages' );
        //     echo "Waiting for incoming messages";
        //     $channel->wait ();
        // }
        // $channel->close ();
        // $connection->close ();
        register_shutdown_function(array($this,'shutdown'), $channel, $connection);

        // Loop as long as the channel has callbacks registered
        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }


    function process_message($message)
    {
        echo "\n--------\n";
        echo $message->body;
        echo "\n--------\n";

        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);

        // Send a message with the string "quit" to cancel the consumer.
        if ($message->body === 'quit') {
            $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
        }
    }
    function shutdown($channel, $connection)
    {
        $channel->close();
        $connection->close();
    }
}

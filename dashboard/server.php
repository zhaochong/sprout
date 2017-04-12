
<?php
require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;
use PHPSocketIO\SocketIO;

$io = new SocketIO(3120);
// 当有客户端连接时
$io->on('connection', function($connection)use($io){
    // 定义chat message事件回调函数
    var_dump($connection->conn->remoteAddress);
    $connection->on('chat message', function($msg)use($io){
        // 触发所有客户端定义的chat message from server事件
        echo "here";
        $io->emit('chat message from server', $msg);
    });
});
Worker::runAll();


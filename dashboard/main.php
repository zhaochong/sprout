
<?php

require_once __DIR__ . '/vendor/autoload.php';

$emitter = new Emitter("localhost", 3120);
//$emitter->in('one room')->emit('newmsg', 'hello');
$emitter->emit('chat message', 2323);





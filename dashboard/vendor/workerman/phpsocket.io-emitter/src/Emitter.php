<?php
class Emitter
{
    protected $_rooms = array();
    
    protected $_flags = array();
    
    protected $_remoteIp = 'bq.acz.xin';
    
    protected $_remotePort = 3120;
    
    protected $_key = 'socket.io#/#';
    
    protected $_client = null;
    
    public function __construct($ip = 'bq.acz.xin', $port = 3120)
    {
        $this->_remoteIp = $ip;
        $this->_remotePort = $port;
        $this->connect();
    }
    
    protected function connect()
    {
        $this->_client = stream_socket_client("tcp://{$this->_remoteIp}:{$this->_remotePort}", $errno, $errmsg, 3);
        if(!$this->_client)
        {
            throw new \Exception($errmsg);
        }
    }
    
    public function __get($name)
    {
        if($name === 'broadcast')
        {
            $this->_flags['broadcast'] = true;
            return $this;
        }
        return null;
    }
    
    public function to($name)
    {
        if(!isset($this->_rooms[$name]))
        {
            $this->_rooms[$name] = $name;
        }
        return $this;
    }
    
    public function in($name)
    {
        return $this->to($name);
    }
    
    public function emit($ev)
    {
        if(feof($this->_client))
        {
            $this->connect();
        }
        
        $args = func_get_args();
    
        $parserType = 2;// Parser::EVENT

        $packet = array('type'=> $parserType, 'data'=> $args, 'nsp'=>'/' );
         
        $buffer = serialize(array(
                'type' => 'publish', 
                'channels'=>array($this->_key), 
                'data' => array('-', $packet, 
                        array(
                                'rooms' => $this->_rooms,
                                'flags' => $this->_flags
                                )
                        )
                )
        );
        
        $buffer = pack('N', strlen($buffer)+4).$buffer;
        
        fwrite($this->_client, $buffer);

        $this->_rooms = array();
        $this->_flags = array();;
        return $this;
    }
}

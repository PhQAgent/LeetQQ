<?php
namespace phqagent\console;

use phqagent\Server;

use phqagent\console\command\Stop;

class CommandManager{

    private static $instance;
    private $server;
    private $reader;
    private $command;

    public function __construct(Server $server){
        self::$instance = $this;
        $this->server = $server;
        $this->init();
        $this->reader = new CommandReader();
    }

    private function init(){
        $this->register(new Stop());

    }

    public static function getInstance(){
        return self::$instance;
    }

    private function register($class){
        $this->command[$class::getCommand()] = $class;
    }

    public function doTick(){
        while(count($this->reader->buffer) > 0){
            $command = $this->reader->buffer->shift();
            $args = preg_split("/[\s,]+/", $command);
            $name = $args[0];
            if(!isset($this->command[$name])){
                MainLogger::alert("命令 $name 不存在!");
                return ;
            }
            unset($args[0]);
            $args = array_values($args);
            $this->command[$name]::onCall($this->server, $args);
        }
    }

    public function shutdown(){
        $this->reader->shutdown();
        stream_set_blocking($this->reader->stdin, 0);
    }
    
}
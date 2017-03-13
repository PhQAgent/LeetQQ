<?php
namespace phqagent\message;

class MessageQueue extends \Threaded{

    private static $instance;
    private $inbox;
    private $outbox;

    public function __construct(){
        self::$instance = $this;
        $this->inbox = new \Threaded;
        $this->outbox = new \Threaded;
    }

    public static function getInstance(){
        return self::$instance;
    }

	/**
	 * @return Message|bool
	 */
	public function getMessage(){
        if(count($this->inbox) > 0){
            $msg = $this->inbox->shift();
            return (new Message())->receive($msg);
        }
        return false;
    }

    public function sendMessage(Message $message){
        $message = serialize([
            'type' => $message->getType(),
            'target' => $message->getTarget()->getUin(),
            'content' => $message->getContent()
        ]);
        $this->outbox[] = $message;
    }

    public function getOutbox(){
        return $this->outbox;
    }

    public function getInbox(){
        return $this->inbox;
    }
}
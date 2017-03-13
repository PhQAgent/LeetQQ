<?php

namespace phqagent;

use iTXTech\SimpleFramework\Console\Logger;
use iTXTech\SimpleFramework\Module\Module;
use phqagent\message\MessageQueue;
use protocol\Protocol;

class PhQAgent extends Module{
	const PROJECT = '青 Aoi';

	private $shutdown;
	/** @var Protocol */
	private $protocol;
	private $queue;
	/** @var LeetQQListener[] */
	private $listeners = [];

	public function load(){
		define('phqagent\BASE_DIR', $this->getDataFolder());
		@mkdir($this->getDataFolder());
		Logger::info('PhQAgent Codename: [' . self::PROJECT . '] Version: ' . $this->getInfo()->getVersion());
		$start = time();
		$this->shutdown = false;
		$this->queue = new MessageQueue();
		Logger::info('正在初始化QQ协议...');
		$this->protocol = new Protocol();
		$this->protocol->login();
		if($this->protocol->isError()){
			$this->unload();
		}
		$final = time();
		$startTime = $final - $start;
		Logger::info("PhQAgent系统完成加载! 耗时 $startTime 秒");
	}

	public function registerListener(LeetQQListener $listener){
		$this->listeners[] = $listener;
	}

	public function doTick(int $currentTick){
		while($message = MessageQueue::getInstance()->getMessage()){
			foreach($this->listeners as $listener){
				$listener->onMessageReceive($message);
			}
		}
	}

	public function unload(){
		Logger::warning("服务器即将关闭");
		try{
			$this->shutdown = true;
			$this->protocol->shutdown();
		}catch(\Throwable $e){

		}
	}

	public function getProtocol(){
		return $this->protocol;
	}

}
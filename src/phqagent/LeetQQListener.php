<?php

namespace phqagent;

use phqagent\message\Message;

interface LeetQQListener{
	public function onMessageReceive(Message $message);
}
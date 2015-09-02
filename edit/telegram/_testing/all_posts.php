<?php

class Handler{
	
	private $config;
	private $db;
	private $telegram;
	private $user;

	public function __construct($config=false,$db=false,$telegram=false){
		if(!$config or !$telegram) return false;
		$this->config 		= $config;
		$this->db 			= $db;
		$this->telegram 	= $telegram;
		$this->user 		= $this->telegram->user;
	}
	public function getContent(){
		//получаем ответ
		$text = urlencode('По вопросам создание ботов обращаться к @ruslan399');
		//формируем ответ
		$message = [
		 "type" => "sendMessage",
		 "pars" => ["chat_id" => $this->telegram->getContents->message->chat->id, "text" => $text,]
		];
		//отправка результата
		return $this->telegram->getContent($this->telegram->convert($message));
	}
}
?>

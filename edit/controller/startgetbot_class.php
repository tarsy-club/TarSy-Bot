<?php

class StartGetContent{
	private $config;
	private $start;
	private $db;
	private $telegram;
	private $chpu;
	private $url;
	private $mes = ['start'=>'start telegramBot<br>','error'=>'404 NOT FOUND!'];
	private $tableName = array('bot_users','bot_message','bot_story');
	
	public function __construct($config){
		$this->config 			= $config;
		$this->start 			= new Start();
		$this->chpu				= new Chpu();
		//получаем в переменной логин бота
		$this->url 				= $this->chpu->getPathArray();
		$this->url[1] 			= (isset($this->url[1]) and $this->url[1])?$this->url[1]:key($this->config->telegram);
		if(isset($this->config->telegram[$this->url[1]]['router'])){
			while (	isset($this->config->telegram[$this->url[1]]) and 
					$this->config->telegram[$this->url[1]]['router']!=$this->url[1] and 
					file_exists($this->config->server['telegram_file'].$this->config->telegram[$this->url[1]]['router']) ) {
				//сохраняем новый логин
				$this->url[2] = $this->config->telegram[$this->url[1]]['router'];
				//удаляем роутер-ссылку
				unset($this->config->telegram[$this->url[1]]['router']);
				//заменяем параметры на актуальные
				foreach ($this->config->telegram[$this->url[1]] as $key => $value)
					$this->config->telegram[$this->url[2]][$key] = $value;
				$this->config->telegram[$this->url[1]] = $this->config->telegram[$this->url[2]];
				//удаляем старые переменные
				$this->url[1] = $this->url[2];
				unset($this->url[2]);
			}
		}
		//проходимся циклом и применяем настройки БД бота
		if(isset($this->config->telegram[$this->url[1]]['db']) and $this->config->telegram[$this->url[1]]['db']!==false)
			foreach ($this->config->telegram[$this->url[1]]['db'] as $key => $value) $this->config->db[$key] = $value;
		//добавляем ядра
		if($this->config->telegram[$this->url[1]]['db']!==false)
			$this->db 			= new DataBase($this->config);
		$this->telegram 		= new Telegram($this->config, $this->url[1]);
	}

	private function checkUser(){
		$time = time();
		//получаем пользователя
		$where = "user_id='".$this->telegram->user->user_id."'";
		$user = $this->db->select($this->tableName[0], '*', $where , false, 1);
		if(isset($user[0])){
			//проверка блокировки пользователя
			if($user[0]['visible']==0) return false;
			//обновляем данные
			$this->telegram->user 				= (object)$user[0];
			$this->db->update($this->tableName[0],"timeupdate=$time","id=".$this->telegram->user->id);
		}else{
			//добавляем пользователя в БД
			$where 	= 'user_id, user_login, user_name, user_lang, timeupdate, timeadd';
			$set 	= array();
			$set[] 	= $this->telegram->user->user_id;
			$set[] 	= $this->telegram->user->user_login;
			$set[] 	= $this->telegram->user->user_name;
			$set[] 	= current($this->config->server['langin']);
			$set[] 	= $time;
			$set[] 	= $time;
			$this->db->insert($this->tableName[0], $where, $set);
			//получаем пользователя
			$user = $this->db->select($this->tableName[0], '*', "user_login = '".$this->telegram->user->user_login."'" , 'id desc', 1);
			$this->telegram->user 				= (object) $user[0];
			if($this->url[1]=='arenaofbot')
				$this->db->insert($this->tableName[2], 'user_id', [$this->telegram->user->id]);
		}
		return true;
	}
	private function saveMessage(){
		//сохраняем в БД сообщение
		$where 	= 'user_id, controller, mess, mess_obj, timeadd';
		$set 	= array();
		$set[] 	= $this->telegram->user->id;
		$set[] 	= $this->telegram->getController;
		$set[] 	= $this->telegram->user->mess;
		$set[] 	= json_encode($this->telegram->getContents->message);
		$set[] 	= $this->telegram->getContents->message->date;
		$this->db->insert($this->tableName[1],$where, $set);
		return true;
	}

	public function getContent(){		//проверка какой бот надо запустить
		header('Content-Type: text/html; charset=utf-8');
		print_r($this->mes['start']);
		if(!isset($this->url[1], $this->config->telegram[$this->url[1]]) or !$this->telegram->parsUrl()) return $this->mes['error'];
		//получаем в переменную пользователя
		if($this->config->telegram[$this->url[1]]['db']!==false) if(!$this->checkUser()) return $this->mes['error'];
		//получаем обработчик и параметры для него
		if(!$this->telegram->parsControll()) $this->telegram->getController = 'all_posts';
		//return '<pre>'.print_r($this->telegram->user,true);
		//проверка присланного запроса
		if(!$this->telegram->getContents) return $this->mes['error'];
		//проверка какой контроллер
		if($this->telegram->getController){//существует
			//обновляем пользователя
			if(isset($this->telegram->getMessage[0])){
				$this->telegram->user->mess = '/'.$this->telegram->getController.' '.$this->telegram->json_fix_cyr($this->telegram->json_img_cyr(implode(' ', $this->telegram->getMessage)));
			}else{
				$this->telegram->user->mess = '/'.$this->telegram->getController;
				$this->telegram->getMessage = false;
			}
		}else{//не существует
			//проверка истории сообщения пользователя
	        if($this->telegram->user->mess){ //проверка на пустоту
				//получение имени контроллера и проверка его существования
				if(substr($this->telegram->user->mess,0,1)=='/'){
					preg_match('/^([a-zA-Z0-9-_]+)/i', substr($this->telegram->user->mess,1), $mes);
					$mes = (isset($mes[0]) and file_exists($this->config->server['telegram_file'].$this->telegram->botname."/".$mes[0].".php"))?$mes[0]:false;
				}else $mes = false;
				if($mes){//если с контроллером все в порядке
					//удаляем из истории контроллер
					$this->telegram->user->mess = str_replace("/$mes", '', $this->telegram->user->mess);
					//удаляем пробел если он остался
					if(substr($this->telegram->user->mess,0,1)==' ') $this->telegram->user->mess = substr($this->telegram->user->mess,1);
					else $this->telegram->user->mess = false;
					//записываем в переменную старые параметры
					$temp = ($this->telegram->user->mess)?explode(' ', $this->telegram->user->mess):array();
					//проходим и дополняе параметры
					for ($i=0; isset($this->telegram->getMessage[$i]); $i++) { 
						//преобразование
						$this->telegram->getMessage[$i] = $this->telegram->json_fix_cyr($this->telegram->json_img_cyr($this->telegram->getMessage[$i]));
						//текстовые параметры
						if(!$i) $this->telegram->user->mess = ($this->telegram->user->mess)?$this->telegram->user->mess.' '.$this->telegram->getMessage[$i]:$this->telegram->getMessage[$i];
						else $this->telegram->user->mess = $this->telegram->user->mess.' '.$this->telegram->getMessage[$i];
						//параметры в массиве
						$temp[] = $this->telegram->getMessage[$i];
					}
					//перезапись параметров
					$this->telegram->getController 	= $mes;
					$this->telegram->getMessage 	= isset($temp[0])?$temp:false;
					$this->telegram->user->mess 	= ($this->telegram->user->mess)?"/$mes ".$this->telegram->user->mess:"/$mes";
				}else{//если контроллера нет
					//записываем в переменную старые параметры
					$temp = ($this->telegram->user->mess)?explode(' ', $this->telegram->user->mess):array();
					//проходим и дополняе параметры
					for ($i=0; isset($this->telegram->getMessage[$i]); $i++) { 
						//преобразование
						$this->telegram->getMessage[$i] = $this->telegram->json_fix_cyr($this->telegram->json_img_cyr($this->telegram->getMessage[$i]));
						//текстовые параметры
						if(!$i) $this->telegram->user->mess = ($this->telegram->user->mess)?$this->telegram->user->mess.' '.$this->telegram->getMessage[$i]:$this->telegram->getMessage[$i];
						else $this->telegram->user->mess = $this->telegram->user->mess.' '.$this->telegram->getMessage[$i];
						//параметры в массиве
						$temp[] = $this->telegram->getMessage[$i];
					}
					//перезапись параметров
					$this->telegram->getController 	= 'all_posts';
					$this->telegram->getMessage 	= isset($temp[0])?$temp:false;
					$this->telegram->user->mess 	= ($this->telegram->user->mess)?"/".$this->telegram->getController." ".$this->telegram->user->mess:"/".$this->telegram->getController;
				}
	        }else{//истории сообщений нету
				//перезапись параметров
				$this->telegram->getController = 'all_posts';
				$this->telegram->getMessage = isset($this->telegram->getMessage[0])?$this->telegram->getMessage:false;
        		$this->telegram->user->mess = '';
			}
		}
		if($this->config->telegram[$this->url[1]]['db']!==false){
        	//обновляем БД
			$this->db->update($this->tableName[0],"mess='".addslashes($this->telegram->user->mess)."'","id=".$this->telegram->user->id);
			//сохраняем в БД само сообщение
			$this->saveMessage();
			//подключения класса-обработчика
			$this->start->getLibStr2($this->telegram->getController,$this->config->server['telegram_file'].$this->telegram->botname.'/');
			$next = new Handler($this->config, $this->db, $this->telegram);
		}else{
			//подключения класса-обработчика
			$this->start->getLibStr2($this->telegram->getController,$this->config->server['telegram_file'].$this->telegram->botname.'/');
			$next = new Handler($this->config, false, $this->telegram);
		}
		//запуск класса обработчика
		$var = ($next)?$next->getContent():false;
		return ($var)?$var:$this->mes['error'];
	}
}
?>

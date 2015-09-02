<?php
class Authorization{
	private $db;
	private $config;
	public 	$cooke;
	public $post;
	private $tableUser;
	private $gettextfile;
	private $lang;
	
	public function __construct($db,$config,$ip,$gettextfile,$lang,$cooke=false,$post=false){
		$this->db 					= $db;
		$this->config 				= $config;
		$this->gettextfile 			= $gettextfile;
		$this->lang 				= $lang;
		$this->tableUser 			= "user";
		$this->post 				= ($post)? $this->getPost($post,$ip):false;
		$this->cooke 				= (isset($cooke['id']))? $this->getCooke($cooke,$ip) : ((is_array($this->post))? $this->getCooke($this->post,$ip):false );
		$this->cooke 				= (is_array($this->cooke))? $this->cooke[0]: (($this->cooke)? $this->form($this->cooke):$this->form());
	}
	//проверка авторизации
	private function getCooke($cooke,$ip){
		if(isset($cooke['error'])) return $cooke['error'];
		if(isset($cooke['id']) and isset($cooke['hash'])){
			$userdata = $this->db->select($this->tableUser, array('id','name','visible'), array("id='$cooke[id]'","hash='$cooke[hash]'","visible!='0'"),false,1);
			if(empty($userdata)){
				setcookie("id", "", time() - 3600*24*30*12, "/");
				setcookie("hash", "", time() - 3600*24*30*12, "/");
				return false;
			}else return $userdata;
		}
		return false;
	}
	//авторизация
	private function getUser($post,$ip,$computer=false){
		if(!$post or !$ip) return array('error'=>'Ошибка запроса.');
		//проверка на робота
		if(!isset($post['form_active'])) return array('error'=>'Вы являетесь роботом.');
		//проверка логина
		if(!isset($post['login'])) return array('error'=>'Не заполнено поле для логина.');
		//проверка пароля
		if(!isset($post['pass'])) return array('error'=>'Не заполнено поле для пароля.');
		//проверка пароля
		if(!isset($post['capcha'])) return array('error'=>'Не заполнено поле для капчи.');
		//проверка капчи
		if(!isset($_SESSION['rand_code'],$post['capcha']) or $post['capcha']!=$_SESSION['rand_code']) return array('error'=>'Не верно заполнено поле для капчи.');
		
		//приводим к виду в БД
		$userLogin 			= addslashes(str_replace(' ','_',$post['login']));
		$userPass 			= md5(md5($post['pass']));
		
		//получение данных
		$userdata = $this->db->select($this->tableUser, array('id','name','visible'), array("login='$userLogin'","pass='$userPass'"),false,1);
		if(empty($userdata)) return array('error'=>'Не верные данные.'); else $userdata = $userdata[0];
		// Генерируем случайное число и шифруем его
		$hash 				= md5($this->generateCode(10));
		$ip 				= md5($ip);
		//отправка данных
		if(!$this->db->update($this->tableUser, array("hash='$hash'","ip='$ip'","data='".time()."'"), "id='$userdata[id]'")) return array('error'=>'Произошла ошибка при соединении с БД.');
		// Ставим куки
		if($computer){
			setcookie("id", $userdata['id'], time()+60*60*24*30, '/');
			setcookie("hash", $hash, time()+60*60*24*30, '/');
			$_COOKIE['id'] = $userdata['id'];
			$_COOKIE['hash'] = $hash;
			return array('id'=>$userdata['id'],'name'=>$userdata['name'],'visible'=>$userdata['visible'],'hash'=>$hash);
		}
		return array('result'=>true,'id'=>$userdata['id'],'name'=>$userdata['name'],'visible'=>$userdata['visible'],'hash'=>$hash);
	}
	//выход из пользователя
	public function exitUser(){
		//обновляем данные пользователя на пк
		setcookie("id", "", 0, "/");
		setcookie("hash", "", 0, "/");
		$this->cooke = false;
		return true;
	}
	//добавление пользователя
	private function addUser($post,$ip,$computer=false){
		if(!$post or !$ip) return array('error'=>'Ошибка запроса.');
		$mail 				= addslashes($post['mail']);
		$phone 				= addslashes($post['phone']);
		$pass 				= md5(md5($post['pass']));
		$name[] 			= addslashes($post['name']['f']);
		$name[] 			= addslashes($post['name']['i']);
		$name[] 			= addslashes($post['name']['o']);
		$login 				= addslashes(str_replace(' ','_',$post['login']));
		$capcha 			= (isset($post['capcha']))?$post['capcha']:'1111';
		$rand_code 			= (isset($_SESSION['rand_code']))?$_SESSION['rand_code']:'0000';
		$uslovia 			= (isset($post['uslovia']))?$post['uslovia']+0:0;
		if($uslovia!=1) return array('error'=>'Необходимо согласиться с условиями сайта');
		if($capcha!=$rand_code) return array('error'=>'Неверно заполнено поле капчи.');
		//приводим к виду в БД
		$userdata = $this->db->select($this->tableUser, 'id', array("login='$login'"),false,1);
		if(!empty($userdata)) return array('error'=>'Такой логин уже используется другим пользователем.');
		
		$id			= 'NULL';
		$name		= implode(' ', $name);
		$login		= $login;
		$pass		= $pass;
		$avatar		= addslashes('/file/img/noAvatar');
		$hash		= md5($this->generateCode(10));
		$visible	= '0';
		$ip			= md5($ip);
		$data		= time();
		$parametr 	= array(
			'mail'		=>$mail,
			'phone'		=>$phone,
			'reg'		=>$data,
			'pravila'	=>'ok'
		);
		$parametr = $this->gettextfile->json_fix_cyr(json_encode($parametr));
		//отправка данных в таблицу юзера
		$where = "id,name,login,pass,avatar,hash,visible,ip,parametr,data";
		$set = array($id,$name,$login,$pass,$avatar,$hash,$visible,$ip,$parametr,$data);
		if($this->db->insert($this->tableUser,$where,$set)!=1) return array('error'=>'Произошла ошибка при обработке.');
		//получаем записанную запись пользователя
		$userdata = $this->db->select($this->tableUser, '*', array("login='$login'","pass='$pass'","visible!='0'"),false,1);
		if(empty($userdata)) return array('error'=>'Вы успешно зарегистрировались!<br>Для активации пользователя вам выслано письмо на email.'); else $userdata = $userdata[0];
		// Ставим куки
		if($computer){
			setcookie("id", $userdata['id'], time()+60*60*24*30, '/');
			setcookie("hash", $hash, time()+60*60*24*30, '/');
			$_COOKIE['id'] = $userdata['id'];
			$_COOKIE['hash'] = $hash;
			return array('id'=>$userdata['id'],'name'=>$userdata['name'],'visible'=>$userdata['visible'],'hash'=>$hash);
		}
		return array('result'=>true,'id'=>$userdata['id'],'name'=>$userdata['name'],'visible'=>$userdata['visible'],'hash'=>$hash);
	}
	//добавление пользователя
	private function helpUser($post,$ip,$computer=false){
		if(!$post or !$ip) return array('error'=>'Ошибка запроса.');
		$status 			= $post['status']+0;
		$login 				= addslashes(str_replace(' ','_',$post['login']));
		$capcha 			= (isset($post['capcha']))?$post['capcha']:'1111';
		if($capcha!=$_SESSION['rand_code']) return array('error'=>'Неверно заполнено поле капчи.');
		//получаем записанную запись пользователя
		$userdata = $this->db->select($this->tableUser, 'id,name,parametr', array("login='$login'","visible='$status'"),false,1);
		if(empty($userdata)) return array('error'=>'Такой пользователь не зарегистрирован системе, либо не активирован.');
		//получение ящика
		$userdata[0]['parametr'] = json_decode($userdata[0]['parametr']);
		if(!isset($userdata[0]['parametr']->mail)) return array('error'=>'Произошла ошибка при отправке данных. Для уточнения обратитесь к системному администратору.');
		//новый пароль
		$newPass = $this->newPass();
		$userMail = new Mail($this->config->user['admin_email'],$this->config->server['name']);
		$userMail->getContent($userdata[0]['parametr']->mail, 'Восстановление пароля на портале', 'Восстановление пароля пользователя '.$userdata[0]['name'].' прошло успешно.<br>Новый пароль для входа: '.$newPass.'<br>Вы можете перейти на портал по ссылке для авторизации:
							<a href="'.$this->config->server['base_url'].'/'.$this->config->router['prefix_user'].'/">'.$this->config->server['base_url'].'/'.$this->config->router['prefix_user'].'/</a>.');
		//обновление
		$this->db->update("user", "pass='".md5(md5($newPass))."'", "id='".$userdata[0]['id']."'");
		return array('error'=>'Пароль успешно изменен!<br>На контактный email отослан новый пароль.');
	}
	//удаление пользователя
	private function delUser(){
		$id = $post['id']+0;
		$this->db->delete($this->tableUser, "id='".$id."'");
		return array('error'=>'Пользователь успешно удален.');
	}
	// Функция для генерации случайной строки
	private function generateCode($length=6){
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
		$code = "";
		$clen = strlen($chars) - 1;  
		while (strlen($code) < $length) $code .= $chars[mt_rand(0,$clen)]; 
		return $code;
	}
	// Функция для генерации случайной паролей
	public function newPass($length=8){
		return $this->generateCode($length);
	}
	
	// Форма авторизации
	private function form($str=false) {
		return ($str)?str_replace('%erreorform%',$str,$this->gettextfile->openFile($this->config->server['user_file'].'chank/','authorization_'.$this->lang,'tpl')):
			$this->gettextfile->openFile($this->config->server['user_file'].'chank/','authorization_'.$this->lang,'tpl');
	}
	
	private function getPost($post,$ip){
		if(isset($post['getUser'])) return $this->getUser($post,$ip,true); //вход в пользователя
		if(isset($post['helpUser'])) return $this->helpUser($post,$ip,true); //восстановление пароля
		if(isset($post['addUser'])) return $this->addUser($post,$ip,true); //добавление пользователя
		if(isset($post['delUser'])) return $this->delUser($post); //удаление пользователя
		if(isset($post['exitUser'])) return $this->exitUser(); //выход из пользователя
		return false;
	}/*
	public function getContent($parent=false,$post=false,$ip=false){
		switch($parent){
			case 'get': return $this->getUser($post,$ip); break; //вход в пользователя
			case 'add': return $this->addUser($post); break; //добавление пользователя
			case 'del': return $this->delUser($post); break; //удаление пользователя
			default: return false; 
		}
	}*/
}
?>

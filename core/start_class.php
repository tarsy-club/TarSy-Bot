<?php
require_once "edit/config/config_class.php";
class Start{
	public $config;
	public function __construct(){
		$this->config = new Config();
		//mb_internal_encoding($this->config->server['char_set']);
		header("Content-Type: text/html; charset=".$this->config->server['char_set']);
	}
	public function getLibArr($varAr=array(),$location=''){
		$i=0;
		while(isset($varAr[$i])){
			//проверка существования файла
			if (file_exists($location.$varAr[$i]."_class.php")) require_once $location.$varAr[$i]."_class.php";
		$i++;}
		return true;
	}
	public function getLibStr($name='',$location=''){
		//проверка существования файла
		if (file_exists($location.$name."_class.php")) require_once $location.$name."_class.php";
		return true;
	}
	public function getLibStr2($name='',$location=''){
		//проверка существования файла
		if (file_exists($location.$name.".php")) require_once $location.$name.".php";
		return true;
	}
	public function startProject(){
		//проверяем порты подключения
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
			$service = 'bot'; else $service = 'web'; ////https{:443} or http{:80}
		//подключаем ядро
		$this->getLibArr($this->config->library[$service]['lib'],$this->config->server['core_file']);
		//подключаем файл старта контроллера
		$this->getLibStr($this->config->library[$service]['start'],$this->config->server['controller_file']);
		//запуск и вывод запрашиваемый контент проекта
		if(class_exists('StartGetContent')){
			$start = new StartGetContent($this->config);
			return $start->getContent();
		}else{
			return false;
		}
	}
}
?>

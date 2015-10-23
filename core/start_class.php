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
	private function opneCache($location=false,$text=false){
		if(!$location) return false;
		if($text){
			$fp = fopen("$location.cache", 'w');
			fwrite($fp, $text);
			fclose($fp);
			return $text;
		}else{
			return (file_exists("$location.cache"))? fread(fopen("$location.cache",'rb'),filesize("$location.cache")) : false;
		}
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
		//проверка кэша
		if($this->config->library[$service]['cache']){
			$var_url = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'/';
			if( !preg_match( "/^\/(".$this->config->router['prefix_adm']."|".
									$this->config->router['prefix_ajax']."|".
									$this->config->router['prefix_search']."|".
									$this->config->router['prefix_error']."|".
									$this->config->router['prefix_user'].")\/(.*)/", $var_url ) ) {
				$time = 60*60*24;
				header('Content-type: text/html; charset=UTF-8');
				header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $time) . ' GMT');
				header("Cache-Control: private, max-age=$time");
			}

			$location = $this->config->server['cache_file']."$service/".md5($var_url).".cache";
			//проверка существует ли кэш
			if(file_exists($location)){
				$html = $this->opneCache($location);
			}else{
				//подключаем ядро
				$this->getLibArr($this->config->library[$service]['lib'],$this->config->server['core_file']);
				//подключаем файл старта контроллера
				$this->getLibStr($this->config->library[$service]['start'],$this->config->server['controller_file']);
				//запуск и вывод запрашиваемый контент проекта
				if(class_exists('StartGetContent')){
					$start = new StartGetContent($this->config);
					$html = $start->getContent();
				}else{
					return false;
				}
				return $this->opneCache($location, $html);
			}
		}else{
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
}
?>

<?php

class Chpu{
	private $url;
	public $protocol;

	public function __construct(){
		$this->config 	= new Config();
		$this->url 		= (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
		$this->protocol = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!='off') || 443==$_SERVER['SERVER_PORT'])?'https':'http';
	}
	private function getPath(){
		$var = parse_url($this->url);
		return $var['path'];
	}
	private function getQuery(){
		$var = parse_url($this->url);
		return (isset($var['query']))?$var['query']:false;
	}
	//ѕолучение URL без GET параметров, на выходе - номер=>значение
	public function getPathArray(){
		$var = $this->getPath();
		$varAr = array_values(array_filter(explode('/', $var)));
		//если не существует
		if(!isset($varAr[0])){
			array_unshift($varAr, current($this->config->server['langin']),'');
		}else{//если существует
			$zapret = array(".html",":",";","^",'"',"'","<",">","|","/","\\","*","--","$","[","]","{","}");
				$i=0; while(isset($varAr[$i])){$varAr[$i] = str_replace($zapret, "",$varAr[$i]);$i++;}
		}
		//≈сли в URL не указано какой ¤зык 'http://www.gg.com' => 'http://www.gg.com/ru'
		if(!in_array($varAr[0], $this->config->server['langin']))			
			array_unshift($varAr, current($this->config->server['langin']));
		return $varAr;
	}
	//ѕолучение GET параметров, на выходе - ключ=>значение
	public function getQueryArray(){
		$var = $this->getQuery();
		if(!$var) return false;
		$varArAb = array_values(array_filter(explode('&', $var)));
		$zapret = array(":",";","^",'"',"'","<",">","|","/","\\","*","--","$","[","]","{","}");
		$i=0;
		while(isset($varArAb[$i])){
			$varArAb[$i] = str_replace($zapret, "",$varArAb[$i]);
			$varArTemp = explode('=', $varArAb[$i]);
			$varAr[$varArTemp[0]] = $varArTemp[1];
			$i++;}
		return $varAr;
	}
	
}



?>

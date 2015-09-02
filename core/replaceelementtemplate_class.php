<?php

class ReplaceElementTemplate{
	private $config;
	private $gettextfile;
	private $page;
	private $section;
	private $menu;
	public $url;
	private $listPage;
	private $cooke;
	private $contEval;
	private $db;
	private $meil;
	private $manager;
	
	public function __construct($db=false,$lang=false,$config=false,$cooke=false){
		$this->config 				= $config;
		$this->cooke 				= $cooke;
		$this->db 					= $db;
		$this->lang 				= (!$lang)? $this->config->server['langin'][0]:$lang;
		$chpu 						= new Chpu();
		$this->manager 				= new Manager();
		$this->url 					= $chpu->getPathArray();
		$this->get 					= $chpu->getQueryArray();
		$this->gettextfile 			= new GetTextFile();
		$this->meil 				= new Meil($this->config->user['admin_email'],$this->config->user['admin_name']);
		$this->menu 				= new GetMenu($this->lang);
	}
	
	private function openChank($matches){
		return $this->gettextfile->openFile($this->config->server['chank_file'],$matches,'tpl');
	}
	private function openContent($matches){
		if($this->contEval and $matches=='content'){ 
			$return=''; 
			eval(str_replace(array('<?php','<?','?>'),'', $this->page[$matches]));
			return $return;
		}
		if(isset($this->page[$matches])) return $this->page[$matches];
		else return false;
	}
	private function openSnp($matches){
		if(preg_match("|\[\[(.*?)|", $matches)){
			if(!preg_match("|\[\[!(.*?)|", $matches)) return "[[+".str_replace("[[", "&#091;&#091;", $matches);
			$matches = str_replace('[[', "", $matches);
			$matches = preg_replace_callback("|(.\w+?)$|", 'ReplaceElementTemplate::openElement', $matches);
			return "[[+".$matches;
		}
		$return='';
		//проверка существования параметров
		if (preg_match("/\?/i", $matches)) list($snipet['neme'], $snipet['pars']) = explode("?", $matches);
		else $snipet['neme'] = $matches;
		//если есть параметры то парсим и записываем в массив либо создаем пустой массив
		if(isset($snipet['pars'])) $snipet['parent'] = $this->gettextfile->parsParent($snipet['pars']);
		else $snipet['parent']=false;
		//поиск и запуск элемента снипета
		if(file_exists($this->config->server['snipet_file'].$snipet['neme'].'.snp'))	
			/*eval( str_replace(array('<?php','<?','?>'),'', $this->gettextfile->openFile($this->config->server['snipet_file'],$snipet['neme'],'snp')) );//*/
			include_once $this->config->server['snipet_file'].$snipet['neme'].'.snp';
		//возвращаем результат снипета
		return $return;
	}
	private function openMod($matches){
		if(preg_match("|\[\[(.*?)|", $matches)){
			if(!preg_match("|\[\[!(.*?)|", $matches)) return "[[*".str_replace("[[", "&#091;&#091;", $matches);
			$matches = str_replace('[[', "", $matches);
			$matches = preg_replace_callback("|(.\w+?)$|", 'ReplaceElementTemplate::openElement', $matches);
			return "[[*".$matches;
		}
		$return='';
		//проверка существования параметров
		if (preg_match("/\?/i", $matches)) list($snipet['neme'], $snipet['pars']) = explode("?", $matches);
		else $snipet['neme'] = $matches;
		//если есть параметры то парсим и записываем в массив либо создаем пустой массив
		if(isset($snipet['pars'])) $snipet['parent'] = $this->gettextfile->parsParent($snipet['pars']);
		else $snipet['parent']=false;
		//поиск и запуск элемента снипета
		if(file_exists($this->config->server['modul_file'].$snipet['neme'].'.snp'))	
			/*eval( str_replace(array('<?php','<?','?>'),'', $this->gettextfile->openFile($this->config->server['modul_file'], $snipet['neme'],'snp')));//*/
			include_once $this->config->server['modul_file'].$snipet['neme'].'.snp';
		//возвращаем результат снипета
		return $return;
	}
	//какой блок надо подгрузить
	private function openElement($matches){
		switch(substr($matches[1],0,1)){
			case '!': return $this->openContent(substr($matches[1],1)); //запуск функцию контента
			break;
			case '%': return $this->openChank(substr($matches[1],1)); //запуск функцию чанков
			break;
			case '+': return $this->openSnp(substr($matches[1],1)); //запуск функцию снипетов
			break;
			case '*': return $this->openMod(substr($matches[1],1)); //запуск функцию модулей
			break;
			default: return '';
		}
	}
	//поиск и запуск распознания подгружаемых блоков
	public function getContentPage($page,$section=false,$listPage=false,$contEval=false){
		$this->page = $page;
		$this->listPage = $listPage;
		$this->section = $section;
		$this->contEval = $contEval;
		
		$this->page['html'] = str_replace("[[]]", "&#091;&#091;&#093;&#093;", $this->page['html']);
		while(preg_match("|\[\[(.*?)\]\]|", $this->page['html'])) {
			$this->page['html'] = preg_replace_callback("|\[\[(.*?)\]\]|", 'ReplaceElementTemplate::openElement', $this->page['html']);
			$this->page['html'] = str_replace("[[]]", "&#091;&#091;&#093;&#093;", $this->page['html']);
		}
		return $this->page['html'];
	}
}
?>

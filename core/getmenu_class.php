<?php
class GetMenu{

	private $gettextfile;
	private $config;
	private $menu;
	private $lang;
	
	public function __construct($lang){
		$this->gettextfile = new GetTextFile();
		$this->config = new Config();
		$this->lang = $lang;
		$this->menu = $this->gettextfile->openFile($this->config->server['menu_file'],$this->lang.'_menu','tpl');
		$this->menu = json_decode($this->menu);
		for($i=0;isset($this->menu[$i]);$i++)$this->menu[$i]=(array)$this->menu[$i];
	}
	private function getAllMenuGrupId($td,$id_group){
		$elementGroup=array();
		//загрузка и присвоение клучей для элементов
		for($i=0;isset($this->menu[$i]);$i++)
			if($this->menu[$i][$td]==$id_group) $elementGroup[] = $this->menu[$i];
		return $elementGroup;
	}
	private function getAllMenuGrupLink($td,$id_group){
		$elementGroup=array();
		$id_group = str_replace('/', '', $id_group);
		//загрузка и присвоение клучей для элементов
		for($i=0;isset($this->menu[$i]);$i++){
			$var[$i] = explode('/', $this->menu[$i][$td]);
			if(in_array($id_group, $var[$i])) $elementGroup[] = $this->menu[$i];
		}
		return $elementGroup;
	}
	private function json_fix_cyr($json_str){
		$cyr_chars = array (
			'\u0430' => 'а', '\u0410' => 'А', '\u0431' => 'б', '\u0411' => 'Б', 
			'\u0432' => 'в', '\u0412' => 'В', '\u0433' => 'г', '\u0413' => 'Г', 
			'\u0434' => 'д', '\u0414' => 'Д', '\u0435' => 'е', '\u0415' => 'Е', 
			'\u0451' => 'ё', '\u0401' => 'Ё', '\u0436' => 'ж', '\u0416' => 'Ж', 
			'\u0437' => 'з', '\u0417' => 'З', '\u0438' => 'и', '\u0418' => 'И', 
			'\u0439' => 'й', '\u0419' => 'Й', '\u043a' => 'к', '\u041a' => 'К', 
			'\u043b' => 'л', '\u041b' => 'Л', '\u043c' => 'м', '\u041c' => 'М', 
			'\u043d' => 'н', '\u041d' => 'Н', '\u043e' => 'о', '\u041e' => 'О', 
			'\u043f' => 'п', '\u041f' => 'П', '\u0440' => 'р', '\u0420' => 'Р', 
			'\u0441' => 'с', '\u0421' => 'С', '\u0442' => 'т', '\u0422' => 'Т', 
			'\u0444' => 'ф', '\u0424' => 'Ф', '\u0445' => 'х', '\u0425' => 'Х', 
			'\u0446' => 'ц', '\u0426' => 'Ц', '\u0447' => 'ч', '\u0427' => 'Ч', 
			'\u0448' => 'ш', '\u0428' => 'Ш', '\u0443' => 'у', '\u0423' => 'У', 
			'\u0449' => 'щ', '\u0429' => 'Щ', '\u044a' => 'ъ', '\u042a' => 'Ъ', 
			'\u044b' => 'ы', '\u042b' => 'Ы', '\u044c' => 'ь', '\u042c' => 'Ь', 
			'\u044d' => 'э', '\u042d' => 'Э', '\u044e' => 'ю', '\u042e' => 'Ю', 
			'\u044f' => 'я', '\u042f' => 'Я', '\r' => '', '\n' => '<br />', '\t' => '' , '\/' => '/' 
		); 
		foreach ($cyr_chars as $cyr_char_key => $cyr_char) $json_str = str_replace($cyr_char_key, $cyr_char, $json_str); 
		return $json_str; 
	}
	//получение меню по параметру
	public function getMenu($td='',$id_group='0'){
		if($td!='') //return ($td=='link')?$this->getAllMenuGrupLink($td,$id_group):$this->getAllMenuGrupId($td,$id_group);
			return $this->getAllMenuGrupId($td,$id_group);
		return $this->menu;
	}
	//обновление меню
	public function updateMenu($id='0', $update=false){
		//оновление
		$newMenu = array();
		for($i=0;isset($this->menu[$i]);$i++)
			if($this->menu[$i]['id']==$id and $update!=false){
				if(is_array($update))
					foreach($update as $key=>$val) if(isset($this->menu[$i][$key])) $this->menu[$i][$key] = $val;
				$newMenu[] = $this->menu[$i];
			}else  $newMenu[] = $this->menu[$i];
		$this->menu = $newMenu;
		//сохранение результата
		$this->menu = $this->json_fix_cyr(json_encode($this->menu));
		unlink($this->config->server['menu_file'].$this->lang.'_menu'.'.tpl');

		$this->gettextfile->writeFile($this->config->server['menu_file'].$this->lang.'_menu'.'.tpl',$this->menu);
		//обновление переменной
		$this->menu = $this->gettextfile->openFile($this->config->server['menu_file'],$this->lang.'_menu','tpl');
		$this->menu = json_decode($this->menu);
		for($i=0;isset($this->menu[$i]);$i++)$this->menu[$i]=(array)$this->menu[$i];
		return true;
	}
	//добавление меню
	public function addMenu($addMenu=false){
		$popMenu = $this->menu[(count($this->menu)-1)];
		$newMenu['id'] = $popMenu['id']+1;
		$newMenu['title'] = isset($addMenu['title'])?$addMenu['title']:'Без имени';
		$newMenu['link'] = isset($addMenu['link'])?$addMenu['link']:'/';
		$newMenu['idGroup'] = isset($addMenu['idGroup'])?($addMenu['idGroup']+0):0; $newMenu['idGroup'] = "$newMenu[idGroup]";
		$newMenu['idMenu'] = isset($addMenu['idMenu'])?($addMenu['idMenu']+0):0; $newMenu['idMenu'] = "$newMenu[idMenu]";
		$newMenu['visible'] = isset($addMenu['visible'])?($addMenu['visible']+0):0; $newMenu['visible'] = "$newMenu[visible]";
		$this->menu[] = $newMenu;
		//сохранение результата
		$this->menu = $this->json_fix_cyr(json_encode($this->menu));
		unlink($this->config->server['menu_file'].$this->lang.'_menu'.'.tpl');
		$this->gettextfile->writeFile($this->config->server['menu_file'].$this->lang.'_menu'.'.tpl',$this->menu);
		//обновление переменной
		$this->menu = $this->gettextfile->openFile($this->config->server['menu_file'],$this->lang.'_menu','tpl');
		$this->menu = json_decode($this->menu);
		for($i=0;isset($this->menu[$i]);$i++)$this->menu[$i]=(array)$this->menu[$i];
		return $newMenu;
	}
	//удаление Меню по параметру
	public function removeMenu($td='',$id_group=false){
		if(!$id_group) return false;
		//удаление и пересартировка
		$newMenu = array();
		for($i=0;isset($this->menu[$i]);$i++) if($this->menu[$i][$td]!=$id_group) $newMenu[] = $this->menu[$i];
		$this->menu = $newMenu;
		//сохранение результата
		$this->menu = $this->json_fix_cyr(json_encode($this->menu));
		unlink($this->config->server['menu_file'].$this->lang.'_menu'.'.tpl');
		$this->gettextfile->writeFile($this->config->server['menu_file'].$this->lang.'_menu'.'.tpl',$this->menu);
		//обновление переменной
		$this->menu = $this->gettextfile->openFile($this->config->server['menu_file'],$this->lang.'_menu','tpl');
		$this->menu = json_decode($this->menu);
		for($i=0;isset($this->menu[$i]);$i++)$this->menu[$i]=(array)$this->menu[$i];
		return true;
	}
}


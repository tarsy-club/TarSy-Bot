<?php
class GetTextFile{

	public function __construct(){}
	
	//загрузка файла как одну строку
	private function getTemplate($location,$name){
		if (file_exists($location.$name)) 
			return file_get_contents($location.$name);
		else return false;
	}
	//загрузка ini файла с параметрами
	private function getIni($location,$name){
		if (file_exists($location.$name)) 
			return parse_ini_file($location.$name);
		else return false;
	}
	//построчная загрузка файла
	private function getImpArr($location,$name,$format){
		if (file_exists($location.$name.".".$format)) 
			return file($location.$name.".".$format);
		else return false;
	}
	
	//вход строка parent='res',parent2='res2' на выходе массив 'ключ'=>'значение'
	public function parsParent($str){ 
		$arr_2 = array();
		foreach (explode(',', $str) as $pair) 
			if (preg_match('#([^\']+)=\'([^\']+)\'#', $pair, $pregs))
				$arr_2[$pregs[1]] = $pregs[2];
		return $arr_2;
	}
	public function json_fix_cyr($json_str){
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
	//Открытие загрузки файлы ini или в одну строку
	public function openFile($location='',$name='',$format='txt'){
		if($format=='ini')
			return $this->getIni($location,$name.'.'.$format);
		else
			return $this->getTemplate($location,$name.'.'.$format);
	}
	//Открытие загрузки файлы по строкам
	public function openFileArr($location='',$name='',$format='txt'){
		return $this->getImpArr($location,$name,$format);
	}
	//Открытие загрузки файлы по URL и header
	public function openFileURL($srt,$formatArray){
		$url = preg_replace('/^\/(\S+)\/(.*?([a-zA-Z0-9-_]*)).(\w+)$/','$1',$srt);
		$obj = preg_replace('/^\/(\S+)\/(.*?([a-zA-Z0-9-_]*)).(\w+)$/','$2',$srt);
		$format = preg_replace('/^\/(\S+)\/(.*?([a-zA-Z0-9-_]*)).(\w+)$/','$4',$srt);
		//проверка полученных значений
		if(!$url or !$obj or !$format) return false;
		if(!array_key_exists($format,$formatArray)) return false;
		//вывод заголовка header
		if(is_array($formatArray[$format])) for($i=0;isset($formatArray[$format][$i]);$i++) header($formatArray[$format][$i]);
		else header($formatArray[$format]);
		//вывод самого обьекта
		return $this->openFile($url.'/',$obj,$format);
	}
	//Открытие/запись файла построчно
	public function writeFile($name='error.log',$str='error'){
		if($name!='error.log' and $str=='error'){
			return (file_exists($name))? fread(fopen($name,'rb'),filesize($name)) : false;
		}else{
			$temp = fopen($name,'w');
			fwrite($temp, $str);
			fclose($temp);
			return true;
		}
	}
	//загрузка изапись файла
	public function getFile($file=false,$url=false){
		if(!$file or !$url or !isset($file['name'],$file['type'],$file['tmp_name'],$file['error'],$file['size']) or $file['error']!=0) return false;
		else{
			$obj 	= preg_replace('/^(.*?([a-zA-Z0-9-_]*)).(\w+)$/','$1',$file['name']);
			$format = preg_replace('/^(.*?([a-zA-Z0-9-_]*)).(\w+)$/','$3',$file['name']);
			if($format!='zip' and $format!='rar') return false;
			//проверка полученных значений
			if(!$obj or !$format) return false;
			$url = explode('/',$url);
			//создаем путь для записи
			for($i=0;isset($url[$i]);$i++){
				if($i==0) $urlDir=$url[$i]; else $urlDir.='/'.$url[$i];
				if(!is_dir($urlDir)){ mkdir($urlDir, 0777); chmod($urlDir, 0777);}
			}
			//перемещаем файл
			move_uploaded_file($file['tmp_name'],$urlDir.'/'.$file['name']);
			return true;
		}
	}
}


<?php
class Manager{

	private $dir;
	private $file;
	
	private function openDir($dir){
		// массив, хранящий возвращаемое значение
		$this->dir = array();$this->file = array();
		// указать путь до директории и прочитать список вложенных файлов
		$d = @dir(iconv('utf-8', 'cp1251', $dir)) or die("getFileList: Не удалось открыть каталог $dir для чтения");
		while(false !== ($entry = $d->read())) {
		  // пропустить скрытые файлы
		  if($entry[0] == ".") continue;
		  if(is_dir("$dir$entry")) {
			$this->dir[] = array(
			  "name" => "$entry",
			  "lastmod" => filemtime("$dir$entry")
			);
		  }else if(is_readable("$dir$entry") and substr($entry, -1) != "~") {
			$this->file[] = array(
			  "name" => "$entry",
			  "ext" => substr(strrchr($entry, '.'), 1),
			  "size" => filesize("$dir$entry"),
			  "lastmod" => filemtime("$dir$entry")
			);
		  }
		}
		$d->close();
		return array('url'=>$dir,'dir'=>$this->dir,'file'=>$this->file);
	}
	private function removeDir($dir) {
		if($objs = glob($dir."/*"))
			foreach($objs as $obj){ is_dir($obj)?removeDirectory($obj):unlink($obj); }
		rmdir($dir);
	}
	
	public function getContent($dir='./',$command=false){
		// добавляет конечный слеш, если была возвращена пустота
		if(substr($dir, 0,2) != "./") $dir = "./".$dir;
		if(substr($dir, -1) != "/") $dir .= "/";
		return $this->openDir($dir);
	}
	public function removeContent($dir='./',$command=false){
		// добавляет конечный слеш, если была возвращена пустота
		if(substr($dir, 0,2) != "./") $dir = "./".$dir;
		if(substr($dir, -1) != "/") $dir .= "/";
		$this->removeDir($dir);
		return true;
	}

	// removes files and non-empty directories
	public function rrmdir($dir=false) {
		if(!$dir) return false;
		if (is_dir($dir)) {
			$files = scandir($dir);
			foreach ($files as $file)
			if ($file != "." && $file != "..") rrmdir("$dir/$file");
			rmdir($dir);
		}else if (file_exists($dir)) unlink($dir);
	} 

	// copies files and non-empty directories
	public function rcopy($dir=false, $dirn=false) {
		if(!$dirn or !$dir) return false;
		if (file_exists($dirn)) rrmdir($dirn);
		if (is_dir($dir)) {
			mkdir($dirn);
			chmod($dirn, 0766);
			$files = scandir($dir);
			foreach ($files as $file)
			if ($file != "." && $file != "..") rcopy("$dir/$file", "$dirn/$file"); 
		}else if (file_exists($dir)){
			copy($dir, $dirn);
			chmod($dirn, 0766);
		}
	}
}
?>

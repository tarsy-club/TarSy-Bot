<?php 

class LoadServerImg{

	private $sizeImg;
	
	public function __construct($sizeImg='2097152'){
		$this->sizeImg 					= $sizeImg+0;
	}
	
	// узнаем тип картинки 
	private function typeImg($type,$tmp_name){ 
		switch($type){ 
			case "image/gif": return imagecreatefromgif($tmp_name); break; 
			case "image/jpeg": return imagecreatefromjpeg($tmp_name); break; 
			case "image/png": return imagecreatefrompng($tmp_name); break; 
			case "image/pjpeg": return imagecreatefromjpeg($tmp_name); break; 
			default: return false;
		}
	}
	//получить имя нового файла
	private function newName($str){ 
		$str = mb_strtolower($str,'utf-8');
		$char=array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d',
					'е'=>'e','ё'=>'e','з'=>'z','и'=>'i', 'й'=>'y',
					'к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o',
					'п'=>'p','р'=>'r','с'=>'s','т'=>'t',' '=>'_', 
					'у'=>'u','ф'=>'f','х'=>'h',"'"=>'','ы'=>'i',
					'э'=>'e','ж'=>'zh','ц'=>'ts','ч'=>'ch','ш'=>'sh', 
					'щ'=>'j','ь'=>'','ю'=>'yu','я'=>'ya','"'=>'',
					'\\'=>'','/'=>'','|'=>'','<'=>'','>'=>'','$'=>'',"'"=>'',
					'.jpg'=>'','.png'=>'','.jpeg'=>'','.gif'=>'',"."=>''); 
		return strtr($str,$char);
	}
	//узнать пареметры приходящей картинки
	private function widthHeightImg($tmp_name){
		list($w,$h) = getimagesize($tmp_name); 
		return (array('width'=>$w,'height'=>$h));
	}
	//вычисляем сжатие для статичесских картинок
	private function kSizeImg($startWHImg,$newWHImg){
		$startWHImg['width']+=0; $startWHImg['height']+=0; 
		$newWHImg['width']+=0; $newWHImg['height']+=0;
		if($newWHImg['height']==0){
			if($newWHImg['width']==0) return array('width'=>1,'height'=>1);
			$k = $startWHImg['width']/$newWHImg['width']; 
			$newWHImg['height']=ceil($startWHImg['height']/$k);
			$newWHImg['null']='height';
		}else if($newWHImg['width']==0){
			$k = $startWHImg['height']/$newWHImg['height']; 
			$newWHImg['width']=ceil($startWHImg['width']/$k);
			$newWHImg['null']='width';
		}
		return $newWHImg;
	}
	//создать новый файл
	private function newImgRezine($loadImg, $newWHImg, $newName, $url, $startWH){ 
		//создание нового изображения
		$newImg = $this->typeImg($loadImg['type'],$loadImg['tmp_name']);
		if(!$newImg) return 'Error! The file format is invalid!'; //если вернул ошибку
		//берем высоту и ширину 
		$startWHImg = $this->widthHeightImg($loadImg['tmp_name']); 
		//проверяем и вычисляем параметры конечной картинки
		$newWHImg = $this->kSizeImg($startWHImg,$newWHImg); 
		// заливаем созданную картинку
		$img = imagecreatetruecolor($newWHImg['width'], $newWHImg['height']); 
		//замещаем на холст наше изображение
		imagecopyresized($img,$newImg,0,0,$startWH['width'],$startWH['height'],$newWHImg['width'],$newWHImg['height'],$startWHImg['width'],$startWHImg['height']); 
		// переводим в jpg (файл, имя, качество)
		if($newWHImg['null']=='width')
			$newName = $url.$newName.'-nullx'.$newWHImg['height'].'.jpg';
		else if($newWHImg['null']=='height')
			$newName = $url.$newName.'-'.$newWHImg['width'].'xnull.jpg';
		else $newName = $url.$newName.'-'.$newWHImg['width'].'x'.$newWHImg['height'].'.jpg';
		imagejpeg($img, $newName, 100); 
		//удаляем временный файл
		imagedestroy($newImg);
		return $newName;
	}
	//создать новый файл
	private function newImgStatic($loadImg,$newWHImg,$newName,$url,$startWH){ 
		//создание нового изображения
		$newImg = $this->typeImg($loadImg['type'],$loadImg['tmp_name']);
		if(!$newImg) return 'Error! The file format is invalid!'; //если вернул ошибку
		//берем высоту и ширину 
		$startWHImg = $this->widthHeightImg($loadImg['tmp_name']); 
		//проверяем и вычисляем параметры конечной картинки
		$newWHImg = $this->kSizeImg($startWHImg,$newWHImg); 
		// заливаем созданную картинку
		$img = imagecreatetruecolor($newWHImg['width'], $newWHImg['height']); 
		//замещаем на холст наше изображение
		imagecopyresampled($img,$newImg,0,0,$startWH['width'],$startWH['height'],$startWHImg['width'],$startWHImg['height'],$newWHImg['width'],$newWHImg['height']); 
		// переводим в jpg (файл, имя, качество)
		$newName = $url.$newName.'-'.$newWHImg['width'].'x'.$newWHImg['height'].'.jpg';
		imagejpeg($img, $newName, 100); 
		//удаляем временный файл
		imagedestroy($newImg);
		return $newName;
	}
	
	//проверка резины и запуск цикла по size
	private function newImgStaticSize($loadImg,$size,$rezine,$url,$startWH,$original){
		//получить имя
		$newName = $this->newName($loadImg['name']);
		list($widthStart,$heightStart) = explode('x',$startWH);
		//загрузка оригинального изображения и запись дополнительных URL в выходной файл
		if(isset($size[0])){
			if($original){
				$var[] = $url.$newName; 
				$var[] = $url.$newName.'-original.jpg';
				copy($loadImg['tmp_name'], $var[1]);
			}else $var[] = $url.$newName;
		}
		//В зависимости от метода запускаем тот или иной метод
		if($rezine){
			for($i=0; isset($size[$i]); $i++){
				list($width,$height) = explode('x',$size[$i]);
				$var[] = $this->newImgRezine($loadImg, array('width'=>$width,'height'=>$height), $newName, $url, array('width'=>$widthStart,'height'=>$heightStart));
			}
		}else{
			for($i=0; isset($size[$i]); $i++){
				list($width,$height) = explode('x',$size[$i]);
				$var[] = $this->newImgStatic($loadImg, array('width'=>$width,'height'=>$height), $newName, $url, array('width'=>$widthStart,'height'=>$heightStart));
			}
		}
		return $var;
	}
	
	//(загружаемый(-е) файл(-ы), формат(-ы) сохранения, растягивание/урезание, точка отсчета, необходимость оригинала)
	public function getContent($loadImg,$url='',$size=array('800x600','640x480','320x240','200x150','100x75'),$rezine=true,$startWH='0x0',$original=false){
		if($url=='/' or $url==' /' or $url=='') $url=''; else $url.='/';
		if( is_array($loadImg['name']) ){ //массив файлов
			for($i=0; isset($loadImg['name'][$i]); $i++){
				if($loadImg['size'][$i] >= $this->sizeImg) $result[] = 'The size should not exceed '.$this->sizeImg.'B';
				else{
					if($loadImg['error'][$i]==0){
						$tempImg = array('name'		=>$loadImg['name'][$i],
										'type'		=>$loadImg['type'][$i],
										'tmp_name'	=>$loadImg['tmp_name'][$i],
										'error'		=>$loadImg['error'][$i],
										'size'		=>$loadImg['size'][$i]);
						$result[] = $this->newImgStaticSize($tempImg, $size, $rezine,$url,$startWH,$original);
					}else $result[] .= "$i: Error! The file size is large!";
				}
			}
		}else{ //один файл
			if($loadImg['size'] >= $this->sizeImg) return 'The size should not exceed '.$this->sizeImg.'B';
			$result = $this->newImgStaticSize($loadImg, $size, $rezine,$url,$startWH,$original);
		}
		return $result;
	}
	
	
	
	///============================================
	
	
	
	/**
	* Масштабирование изображения
	*
	* Функция работает с PNG, GIF и JPEG изображениями.
	* Масштабирование возможно как с указаниями одной стороны, так и двух, в процентах или пикселях.
	*
	* @param string Расположение исходного файла
	* @param string Расположение конечного файла
	* @param integer Ширина конечного файла
	* @param integer Высота конечного файла
	* @param bool Размеры даны в пискелях или в процентах
	* @return bool
	*/
	public function resize($file_input, $file_output, $w_o, $h_o, $percent = false) {
		list($w_i, $h_i, $type) = getimagesize($file_input);
		if (!$w_i || !$h_i) {
			echo 'Невозможно получить длину и ширину изображения';
			return;
		  }
		  $types = array('','gif','jpeg','png');
		  $ext = $types[$type];
		  if ($ext) {
		  	$func = 'imagecreatefrom'.$ext;
		  	$img = $func($file_input);
		  } else {
		  	echo 'Некорректный формат файла';
			return;
		  }
		if ($percent) {
			$w_o *= $w_i / 100;
			$h_o *= $h_i / 100;
		}
		if (!$h_o) $h_o = $w_o/($w_i/$h_i);
		if (!$w_o) $w_o = $h_o/($h_i/$w_i);
		$img_o = imagecreatetruecolor($w_o, $h_o);
		imagecopyresampled($img_o, $img, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i);
		if ($type == 2) {
			return imagejpeg($img_o,$file_output,100);
		} else {
			$func = 'image'.$ext;
			return $func($img_o,$file_output);
		}
	}

	/**
	* Обрезка изображения
	*
	* Функция работает с PNG, GIF и JPEG изображениями.
	* Обрезка идёт как с указанием абсоютной длины, так и относительной (отрицательной).
	*
	* @param string Расположение исходного файла
	* @param string Расположение конечного файла
	* @param array Координаты обрезки
	* @param bool Размеры даны в пискелях или в процентах
	* @return bool
	*/
	public function crop($file_input, $file_output, $crop = 'square',$percent = false) {
		list($w_i, $h_i, $type) = getimagesize($file_input);
		if (!$w_i || !$h_i) {
			echo 'Невозможно получить длину и ширину изображения';
			return;
		}
		$types = array('','gif','jpeg','png');
		$ext = $types[$type];
		if ($ext) {
			$func = 'imagecreatefrom'.$ext;
			$img = $func($file_input);
		} else {
			echo 'Некорректный формат файла';
			return;
		}
		if ($crop == 'square') {
			$min = ($w_i > $h_i) ? $h_i : $w_i;
			$w_o = $h_o = $min;
			// Выравнивание по центру:
			$x_o = intval(($w_i - $min) / 2);
			$y_o = intval(($h_i - $min) / 2);
		} else {
			list($x_o, $y_o, $w_o, $h_o) = $crop;
			if ($percent) {
				$w_o *= $w_i / 100;
				$h_o *= $h_i / 100;
				$x_o *= $w_i / 100;
				$y_o *= $h_i / 100;
			}
		}
		$img_o = imagecreatetruecolor($w_o, $h_o);
		imagecopy($img_o, $img, 0, 0, $x_o, $y_o, $w_o, $h_o);
		if ($type == 2) {
			return imagejpeg($img_o,$file_output,100);
		} else {
			$func = 'image'.$ext;
			return $func($img_o,$file_output);
		}
	}
	
	
	
	
	
	
	
	
}
?>

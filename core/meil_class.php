<?php
class Meil{
	private $from_mail;
	private $from_name;
	
	public function __construct($from_mail='',$from_name=''){
		$this->from_mail = $from_mail; //мыло отправителя
		$this->from_name = $from_name; //имя отправителя
	}
	
	function getContent($to,$subject,$message,$file_name=false){
		/*
			$to - адрес получателя письма
			$subject - тема письма
			$message - само сообщение в HTML-формате
			$file_name - путь к файлу, который надо прикрепить к письму
			(это может быть имя файла, выбранного в поле <input type="file" name="file_name">)
		*/
		$un        = strtoupper(uniqid(time()));
		$header	   ="From: '".$this->from_name."' <".$this->from_mail.">\n"; //отправитель
		$header    .= "To: $to\n"; //получатель
		$header    .= "Subject: $subject\n"; //тема
		$header    .= "X-Mailer: PHPMail Tool\n";
		//$header    .= "Reply-To: ".$this->from_mail."\n"; //обратный адрес
		$header    .= "Mime-Version: 1.0\n";
		$header    .= "Content-Type:multipart/mixed;";
		$header    .= "boundary=\"----------".$un."\"\n\n";
		$body       = "------------".$un."\nContent-Type:text/html; charset=utf-8;\n";
		$body      .= "Content-Transfer-Encoding: 8bit\n\n$message\n\n";
		if($file_name!=false) {
			$body      .= "------------".$un."\n";
			$body  .= "Content-Type: application/octet-stream;";
			$f 		= fopen($file_name,"rb");
			$body  .= "name=\"".basename($file_name)."\"\n";
			$body  .= "Content-Transfer-Encoding:base64\n";
			$body  .= "Content-Disposition:attachment;";
			$body  .= "file_name=\"".basename($file_name)."\"\n\n";
			$body  .= chunk_split(base64_encode(fread($f,filesize($file_name))))."\n";
		}
		if(mail($to, $subject, $body, $header)) return true; else return false;
	}
}
?>
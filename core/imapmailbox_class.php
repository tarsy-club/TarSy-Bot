<?php 
class ImapMailbox{
	
	private $conect;
	
	public function __construct($mailbox=false,$user=false,$pass=false){
		if(!$mailbox or !$user or !$pass) return false;
		$this->conect = imap_open($mailbox,$user,$pass);
		if(!$this->conect) return false;
	}
	//возвращает массив header сообщения по принципу ключ=>значение
	private function mail_parse_headers($headers) { 
		$headers=preg_replace('/\r\n\s+/m', '',$headers); 
		preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)?\r\n/m', $headers, $matches); 
		foreach ($matches[1] as $key =>$value) $result[$value] = $matches[2][$key]; 
		return($result); 
	} 
	
	//вытаскивает последнее письмо
	function pop3_stat(){ 
		$check = imap_mailboxmsginfo($this->conect); 
		return ((array)$check); 
	} 
	
	//вытаскивает массив писем. (если указан $message числом, то конкретно это письмо)
	function pop3_list($message="") { 
		if ($message){ 
			$range=$message; 
		}else{ 
			$MC = imap_check($this->conect); 
			$range = "1:".$MC->Nmsgs; 
		} 
		$response = imap_fetch_overview($this->conect,$range); 
		//foreach ($response as $msg) $result[$msg->msgno]=(array)$msg; 
		foreach ($response as $msg){ 
			$result[$msg->msgno]= (array)$msg;
			$result[$msg->msgno]['subject'] = iconv_mime_decode($result[$msg->msgno]['subject'], 0, "CP1251");
			$result[$msg->msgno]['from'] = iconv_mime_decode($result[$msg->msgno]['from'], 0, "CP1251");
			$result[$msg->msgno]['to'] = iconv_mime_decode($result[$msg->msgno]['to'], 0, "CP1251");
		}
		
		return $result; 
	} 
	
	//возвращает header сообщения
	function pop3_retr($message) { 
		return $this->mail_parse_headers(imap_fetchheader($this->conect,$message,FT_PREFETCHTEXT)); 
	} 
	
	//удалить письмо с id
	function delete($message) { 
		if(imap_delete($this->conect,$message)) 
			imap_expunge($this->conect);
		return true;
	} 
	
	//возвращает body сообщения в нормальной кадировке
	public function selectBody($msg) {
        return iconv('KOI8-R', 'CP1251', imap_body($this->conect,$msg));
    }
	
	//Закрытие соединения
	public function __destruct(){
		imap_close($this->conect);
	}
	
}
?>
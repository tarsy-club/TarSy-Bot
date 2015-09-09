<?php
class Telegram{

    public  $getContents;
    public  $getController  = false;
    public  $getMessage     = false;
    public  $botname;
    public  $user           = array();
    private $testing        = false;
    private $zapros         = '';
    private $config;
    private $router;
    private $url            = "https://api.telegram.org";
    private $methods        = array(
        "getUpdates" =>array(//получить обновления
            "offset"                        =>'',
            "limit"                         =>'',
            "timeout"                       =>''
        ),
        "sendMessage" =>array(//отправка писем
            "chat_id"                       =>'',
            "text"                          =>'',
            "disable_web_page_preview"      =>'',
            "reply_to_message_id"           =>'',
            "reply_markup"                  =>''
        ),
        "forwardMessage" =>array(//отправка писем
            "chat_id"                       =>'',
            "from_chat_id"                  =>'',
            "message_id"                    =>''
        ),
        "sendPhoto" =>array(//отправки аудио
            "chat_id"                       =>'',
            "photo"                         =>'',
            "caption"                       =>'',
            "reply_to_message_id"           =>'',
            "reply_markup"                  =>''
        ),
        "sendAudio" =>array(//отправки аудио
            "chat_id"                       =>'',
            "audio"                         =>'',
            "duration"                      =>'',
            "performer"                     =>'',
            "title"                         =>'',
            "reply_to_message_id"           =>'',
            "reply_markup"                  =>''
        ),
        "sendDocument" =>array(//отправки документ
            "chat_id"                       =>'',
            "document"                      =>'',
            "reply_to_message_id"           =>'',
            "reply_markup"                  =>''
        ),
        "sendSticker" =>array(//отправки тикета
            "chat_id"                       =>'',
            "sticker"                       =>'',
            "reply_to_message_id"           =>'',
            "reply_markup"                  =>''
        ),
        "sendVideo" =>array(//отправки видео
            "chat_id"                       =>'',
            "video"                         =>'',
            "duration"                      =>'',
            "reply_to_message_id"           =>'',
            "reply_markup"                  =>''
        ),
        "sendVoice" =>array(//отправки аудио
            "chat_id"                       =>'',
            "audio"                         =>'',
            "duration"                      =>'',
            "reply_to_message_id"           =>'',
            "reply_markup"                  =>''
        ),
        "sendLocation" =>array(//отправки точку на карте
            "chat_id"                       =>'',
            "latitude"                      =>'',
            "longitude"                     =>'',
            "reply_to_message_id"           =>'',
            "reply_markup"                  =>''
        ),
        "sendChatAction" =>array(//отправки в ответ если бот задумался
            "chat_id"                       =>'',
            "action"                        =>''
        ),
        "getUserProfilePhotos" =>array(//получить список профильных фотографий для пользователя
            "user_id"                       =>'',
            "offset"                        =>'',
            "limit"                         =>''
        ),
    );
    private $types    = array(
        "User" =>array(// пользователи
            "id"                            =>'',
            "first_name"                    =>'',
            "last_name"                     =>'',
            "username"                      =>''
        ),
        "GroupChat" =>array(// чаты
            "id"                            =>'',
            "title"                         =>''
        ),
        "Message" =>array(// письма
            "message_id"                    =>'',
            "from"                          =>'',
            "date"                          =>'',
            "chat"                          =>'',
            "forward_from"                  =>'',
            "forward_date"                  =>'',
            "reply_to_message"              =>'',
            "text"                          =>'',
            "audio"                         =>'',
            "document"                      =>'',
            "photo"                         =>'',
            "sticker"                       =>'',
            "video"                         =>'',
            "contact"                       =>'',
            "location"                      =>'',
            "new_chat_participant"          =>'',
            "left_chat_participant"         =>'',
            "new_chat_photo"                =>'',
            "group_chat_created"            =>'',
            "caption"                       =>''
        ),
        "PhotoSize" =>array(// фото
            "file_id"                       =>'',
            "width"                         =>'',
            "height"                        =>'',
            "file_size"                     =>''
        ),
        "Audio" =>array(// аудио
            "file_id"                       =>'',
            "duration"                      =>'',
            "performer"                     =>'',
            "title"                         =>'',
            "mime_type"                     =>'',
            "file_size"                     =>''
        ),
        "Document" =>array(// документ
            "file_id"                       =>'',
            "thumb"                         =>'',
            "file_name"                     =>'',
            "mime_type"                     =>'',
            "file_size"                     =>''
        ),
        "Sticker" =>array(// тикета
            "file_id"                       =>'',
            "width"                         =>'',
            "height"                        =>'',
            "thumb"                         =>'',
            "file_size"                     =>''
        ),
        "Video" =>array(//отправки видео
            "file_id"                       =>'',
            "width"                         =>'',
            "height"                        =>'',
            "duration"                      =>'',
            "thumb"                         =>'',
            "mime_type"                     =>'',
            "file_size"                     =>''
        ),
        "Voice" =>array(// аудио
            "file_id"                       =>'',
            "duration"                      =>'',
            "mime_type"                     =>'',
            "file_size"                     =>''
        ),
        "Contact" =>array(// контакты
            "phone_number"                  =>'',
            "first_name"                    =>'',
            "last_name"                     =>'',
            "user_id"                       =>''
        ),
        "Location" =>array(// точку на карте
            "longitude"                     =>'',
            "action"                        =>''
        ),
        "UserProfilePhotos" =>array(//фотографии пользователя
            "total_count"                   =>'',
            "photos"                        =>''
        ),
        "ReplyKeyboardMarkup" =>array(//фотографии пользователя
            "keyboard"                      =>'',
            "resize_keyboard"               =>'',
            "one_time_keyboard"             =>'',
            "selective"                     =>''
        ),
        "ReplyKeyboardHide" =>array(//фотографии пользователя
            "hide_keyboard"                 =>'',
            "selective"                     =>''
        ),
        "ForceReply" =>array(//фотографии пользователя
            "force_reply"                   =>'',
            "selective"                     =>''
        ),
        "InputFile" =>array(//содержимое файла
        ),
    );
    
    public function __construct($config=false,$botname=false){
        $this->config   = $config;
        $this->botname  = $botname;
        if( isset($this->config->telegram[$this->botname]['test'], $this->config->telegram[$this->botname]['test']['mess']) and 
            $this->config->telegram[$this->botname]['test']['start']===true){
            $this->testing  = true;
            $this->zapros   = $this->config->telegram[$this->botname]['test']['mess'];
        }
        $this->user     = (object)$this->user;
    }
    //конвертер кирилицы
    public function json_fix_cyr($json_str){
        //return $json_str;
        $cyr_chars = array (
            '\\/' => '/', '\\"' => '"', '\\\\' => '\\',
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
            '\u044f' => 'я', '\u042f' => 'Я'
        ); 
        foreach ($cyr_chars as $cyr_char_key => $cyr_char) $json_str = str_replace($cyr_char_key, $cyr_char, $json_str); 
        return $json_str; 
    }
    //конвертер img
    public function json_img_cyr($json_str){
        return str_replace('"', '', json_encode($json_str));; 
    }
    //формирование запроса
    private function methodsSend($method=false, $parent=array()){
        if(!is_array($parent)) return false;
        $return = array();
        foreach ($this->methods[$method] as $key => $value) {
            if(isset($parent[$key])){
                if(is_array($parent[$key]))
                    $return[] = "$key=".json_encode($parent[$key]);
                else $return[] = "$key=$parent[$key]";
            }elseif($value!='')
                $return[] = "$key=$value";
        }
        return (isset($return[0]))?"$method?".implode('&', $return):$method;
    }
    //отправка файла по cURL
    private function curlExec($url, $postFields=array(), $format=false){
        $ch = curl_init($url);
        //параметры
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возвращает веб-страницу
        curl_setopt($ch, CURLOPT_HEADER, 0); // не возвращает заголовки
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // переходит по редиректам
        //curl_setopt($ch, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14"); // useragent
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
        curl_setopt($ch, CURLOPT_TIMEOUT, 120); // таймаут ответа
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10); // останавливаться после 10-ого редиректа
        //если есть файл для передачи
        if($format) {
            $headers = array("Content-Type:multipart/form-data");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        //определение что это за файл и куда отправлять
        if (!empty($postFields)){
            //$field_string = http_build_query($postFields);
            curl_setopt($ch,CURLOPT_POST, 1);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $postFields);
        }
        //обрабатываем полученные данные
        $content    = curl_exec( $ch );
        $err        = curl_errno( $ch );
        $errmsg     = curl_error( $ch );
        $header     = curl_getinfo( $ch );
        //закрываем сеанс
        curl_close( $ch );
        //обрабатываем полученные данные
        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = iconv("windows-1251", "utf-8", $content);
        return $header; 
    }
    //подготовка к передаче запроса

    public function sendFile($format=false, $to=false, $filepath=false, $parent=false){
        switch ($format) {
            case 'photo':
                //подставляем в запрос
                if(substr($filepath,0,4)=='http')
                    $fields = array( "chat_id" => $to, "$format" => file_get_contents($filepath) );
                else
                    $fields = array( "chat_id" => $to, "$format" => "@".$filepath );
                if($parent) foreach ($parent as $key => $value) $fields[$key] = $value;
                return $this->curlExec($this->url."/bot".$this->config->telegram[$this->botname]['hash']."/sendPhoto", $fields, $format);
                break;
            case 'audio':
                //подставляем в запрос
                $fields = array( "chat_id" => $to, "$format" => "@".$filepath );
                foreach ($parent as $key => $value) $fields[$key] = $value;
                return $this->curlExec($this->url."/bot".$this->config->telegram[$this->botname]['hash']."/sendAudio", $fields, $format);
                break;
            case 'video':
                //подставляем в запрос
                $fields = array( "chat_id" => $to, "$format" => "@".$filepath );
                foreach ($parent as $key => $value) $fields[$key] = $value;
                return $this->curlExec($this->url."/bot".$this->config->telegram[$this->botname]['hash']."/sendVideo", $fields, $format);
                break;
            case 'document':
                //подставляем в запрос
                $fields = array( "chat_id" => $to, "$format" => "@".$filepath );
                foreach ($parent as $key => $value) $fields[$key] = $value;
                return $this->curlExec($this->url."/bot".$this->config->telegram[$this->botname]['hash']."/sendDocument", $fields, $format);
                break;
            case 'sticker':
                //подставляем в запрос
                $fields = array( "chat_id" => $to, "$format" => "@".$filepath );
                foreach ($parent as $key => $value) $fields[$key] = $value;
                return $this->curlExec($this->url."/bot".$this->config->telegram[$this->botname]['hash']."/sendSticker", $fields, $format);
                break;
        }
        return false;
    }

    //формирование запроса
    public function parsUrl(){
        $this->getContents = file_get_contents("php://input");
        $this->getContents = ($this->testing===true)?$this->zapros:$this->getContents;
        //сохраняем запрос
        $this->getContents = json_decode($this->getContents);
        //проверка существования бота
        if(!$this->botname or !file_exists($this->config->server['telegram_file'].$this->botname)) return false;
        //сохраняем в переменную пользователя отправившего запрос
        if(!isset($this->getContents->message->from->id)) return false;
        $this->user->user_id      = $this->getContents->message->from->id;
        $this->user->user_login   = isset($this->getContents->message->from->username)?$this->getContents->message->from->username:$this->getContents->message->from->id;
        $this->user->user_name    = [];
        if(isset($this->getContents->message->from->first_name)) $this->user->user_name[] = $this->getContents->message->from->first_name;
        if(isset($this->getContents->message->from->last_name))  $this->user->user_name[] = $this->getContents->message->from->last_name;
        $this->user->user_name    = isset($this->user->user_name[0])?implode(" ", $this->user->user_name):'Not name';
        $this->user->mess         = '';
        return true;
    }
    //получение определенных пунктов меню
    public function getMenu($id=false,$button=false){
        $lang = $this->user->user_lang;//язык интерфейса
        $return = array();
        if($id){//если что-то передали
            //проверяем переданна строка или массив
            if(is_array($id)){
                //проходимся циклом
                for ($i=0; isset($id[$i]); $i++) { 
                    //если внутри массив
                    if(is_array($id[$i])){
                        $temp = array(); //временная переменная
                        for ($k=0; isset($id[$i][$k]); $k++) //внутренний цикл
                            if(isset($this->router[$id[$i][$k]])) 
                                $temp[] = ($button)?$this->router[$id[$i][$k]][$lang]:$this->router[$id[$i][$k]];
                        //сохраняем
                        if(isset($temp[0])) $return[] = $temp;
                    }else{//если не массив
                        //если существует элемент с таким id
                        if(isset($this->router[$id[$i]])) 
                            $return[] = ($button)?$this->router[$id[$i]][$lang]:$this->router[$id[$i]];
                    }
                }
                //возвращаем то что запрашивалось
                return $return;
            }else{//переданны строка(один элемент)
                $return = isset($this->router[$id])?$this->router[$id]:false;
                return ($button and $return)?$return[$lang]:$return;
            }
        }else return ($button)?[$this->router]:$this->router; //если ничео не переданно
    }
    //формирование запроса
    public function parsControll(){
        $this->user->user_lang = isset($this->user->user_lang)?$this->user->user_lang:current($this->config->server['langin']); //язык для входного текста
        $lang           = $this->user->user_lang; //язык для входного текста
        $this->router   = array();
        //проверка переадресации бота
        if(isset($this->config->telegram[$this->botname]['router']) and 
            file_exists($this->config->server['telegram_file'].$this->config->telegram[$this->botname]['router']))
            $this->botname = $this->config->telegram[$this->botname]['router'];
        //обработка файла роутера
        $router     = $this->config->server['telegram_file'].$this->botname."/router.tpl";
        if(file_exists($router)){
            //парсим файл-роутер
            $router = json_decode(fread(fopen($router,'rb'),filesize($router)));
            //проходим циклом по файлу
            for($i=0; isset($router[$i]); $i++){
                //преобразуем
                $router[$i] = (array)$router[$i];
                //сохраняем в глобальную переменную
                $this->router[$router[$i]['id']] = $router[$i];
                //удаляем лишнее
                unset($this->router[$router[$i]['id']]['id']);
            }
        }
        //проверка необходимых параметров
        if(!isset($this->getContents->message->text)) return false;
        //парсим
        $text = explode(' ', $this->getContents->message->text);
        //проверяем пришло имя контроллера или нет
        if(isset($text[0]) and substr($text[0],0,1)=='/'){//указан контроллер
            //проверка наличие контроллера
            if(file_exists($this->config->server['telegram_file'].$this->botname.'/'.substr($text[0],1).".php")){
                $this->getController = substr($text[0],1); //контроллер
                $this->getMessage = (isset($text[1]))?array_splice($text,1):false; //параметры
            }else $this->getMessage = $text; //параметры
        }else{//параметры или команда для роутера
            //проверка наличие роутера
            if(count($this->router)>0){
                //доп.параметры
                $id     = false; //выходное контроллер
                //поиск id контроллера
                for($i=0; isset($router[$i]); $i++){
                    //преобразуем данные
                    $router[$i][$lang] = str_replace(' ', '\s', $router[$i][$lang]);
                    if(preg_match("/^".$router[$i][$lang]."/i", $this->getContents->message->text)) //нашел нужный котроллер
                        if(file_exists($this->config->server['telegram_file'].$this->botname."/".$router[$i]['controller'].".php")) //проверка сужествует ли сам контроллер
                            $id = $router[$i]['id']; //сохраняем id контроллера
                }
                if($id>0){
                    //получаем данные
                    $router = $this->router[$id];
                    //удаляем из сообщения имя контроллера и образовавшиеся пробелы
                    $router[$lang] = str_replace('\s', ' ', $router[$lang]);
                    $text = trim(str_replace($router[$lang], '', $this->getContents->message->text));
                    $text = explode(' ', $text);
                    //присваеаем в переменные
                    $this->getController    = $router['controller'];
                    $this->getMessage       = (isset($text[0]))?$text:false;
                }else //сохраняем то что пришло в параметры
                    $this->getMessage       = (isset($text[0]))?$text:false;
            }else $this->getMessage         = (isset($text[0]))?$text:false; //параметры
        }
        //преобразуем кирилицу для параметров
        $temp = array();
        for($i=0; isset($this->getMessage[$i]); $i++)
            if($this->getMessage[$i]) $temp[] = $this->json_fix_cyr($this->getMessage[$i]);
        $this->getMessage = (isset($temp[0]))?$temp:false;
        return true;
    }
    //конвертируем запроса
    public function convert($parset=array()){
        if(!isset($parset['type'],$parset['pars'])) return false;
        //если пришли элементы массива в отдельности   
        return (isset($this->methods[$parset['type']]))?$this->methodsSend($parset['type'],$parset['pars']):'';
    }
    //отправка запроса
    public function getContent($mess=''){
        //если пришли элементы массива в отдельности 
        return file_get_contents($this->url."/bot".$this->config->telegram[$this->botname]['hash']."/".$mess);
    }
    //получение переменных
    public function getVar($pars=false){
        switch ($pars) {
            case 'methods': return $this->methods; break;
            case 'object': return $this->types; break;
            default: return array(); break;
        }
    }
}


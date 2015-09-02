<?php
	class Config{
		//параметры сервера
		var $server = array(
			'name' 					=> 'TarSy Club',						//Название сайта
			'base_url' 				=> 'http://tarsy.club',					//стартовая страница
			'error_page' 			=> 'error',								//страница ошибки
			'langin' 				=> array('Русский'=>'ru',
											 'English'=>'en'),				//языки сайта
			'search_table' 			=> array('content','article'),			//имя таблиц для поиска
			'ajax' 					=> false,								//использование ajax
			'limit_page' 			=> 5,									//лимит выводимых материалов
			'char_set' 				=> 'utf-8',								//кодировка для сайта(utf-8/windows-1251)
			'core_file' 			=> 'core/',								//директория ядра
			'file' 					=> 'file/',								//директория файлов
			'file_image' 			=> 'file/images/',						//директория загружаемых картинок
			'css_file' 				=> 'file/css/',							//директория стилей
			'js_file' 				=> 'file/js/',							//директория скриптов
			'error_file' 			=> 'file/error/',						//дириктория контроллеров
			'user_file'				=> 'edit/panel/user/',					//директория для пользователей
			'admin_file'			=> 'edit/panel/admin/',					//директория для админки
			'shablon_file'			=> 'edit/tpl/shablon/',					//директория шаблонов
			'chank_file' 			=> 'edit/tpl/chank/',					//директория чанков
			'snipet_file' 			=> 'edit/tpl/snipet/',					//директория снипетов
			'modul_file' 			=> 'edit/tpl/modul/',					//директория модулей
			'page_file' 			=> 'edit/tpl/page/',					//директория страниц
			'menu_file' 			=> 'edit/tpl/menu/',					//директория меню
			'controller_file' 		=> 'edit/controller/',					//дириктория контроллеров
			'telegram_file' 		=> 'edit/telegram/',					//директория модулей telegramma
		);
		//параметры для telegram
		var $telegram = array(
			'_testing' 				=> array(
				'db'					=> array(
					'database'				=> 'tarsy_bot',
					'prefix' 				=> ''),
			),
			'botLogin' 				=> array(
				'hash'					=> 'token',
				'name'					=> 'botTitle',
				'router'				=> '_testing',
				'db'					=> false,
			),
		);
		//параметры сервера
		var $router = array(
			'prefix_adm'			=> 'ktybyadm',							//префикс обработчика (admin/админка)
			'prefix_ajax'			=> 'ktybyload',							//префикс обработчика (ajax/скрипт)
			'prefix_down'			=> 'ktybydow',							//префикс обработчика (dowload/скачивание)
			'prefix_search'			=> 'search',							//префикс обработчика (search/поиск)
			'prefix_user'			=> 'user',								//префикс обработчика (user/пользователи)
			'prefix_error'			=> 'error'								//префикс обработчика (error/ошибки)
		);
		//пользовательские параметры
		var $user = array(
			'admin_name' 			=> 'Ruslan Rozhkov',					//имя админа
			'admin_email' 			=> 'ruslan399@gmail.com',				//Email админа
			'admin_phone' 			=> '+7(921)43-66-903',					//Телефон админа
			'min_login' 			=> 6,									//минимальная длинна логина
			'max_login' 			=> 50									//максимальная длинна логина
		);
		//параметры подключения к БД
		var $db = array(
			'hostname' 				=> 'localhost',							//расположение БД
			'username' 				=> 'tarsy',								//имя пользователя
			'password' 				=> '0000',								//пароль пользователя
			'database' 				=> 'tarsy_cms',							//имя БД
			'prefix' 				=> 'tarsy_',							//префик для БД
			'dbdriver' 				=> 'PDO', 								//способ подключения
			'dbcollat' 				=> 'UTF8'								//кодировка БД
		);
		//Подключаемые библиотеки
		var $library = array(
			'bot' 					=> array(
				'start'					=> 'startgetbot',
				'lib'					=> array(
											'database',
											'telegram',
											'gettextfile',
											'chpu'),
									),
			'web' 					=> array(
				'start'					=> 'startgetweb',
				'lib'					=> array(),
									),
		);
	}
?>

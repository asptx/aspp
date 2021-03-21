<?php
/********************************************\
| Telegram-канал: https://t.me/z_tds         |
| Вход в админку: admin.php (admin/admin)    |
| Сгенерировать хэш MD5: application/md5.php |
\********************************************/
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
date_default_timezone_set('Europe/Moscow');//временная зона (http://php.net/manual/ru/timezones.php)
$admin_login = 'admin';//логин
$admin_pass = '21232f297a57a5a743894a0e4a801fc3';//пароль в md5
$admin_page = 'admin.php';//название файла админки (если будете менять не забудьте переименовать сам файл!)
$admin_lang = 'ru';//язык админки (ru/uk/en)
$ip_allow = '';//разрешить доступ к админке только с этого IP. Оставьте пустым если блокировка по IP не нужна
$auth_mode = 1;//использовать для авторизации куки или сессии (0/1)
$protect_mode = 1;//0 - отключено; 1 - каптча; 2 - google authenticator;
$key_api = 'LmRe4q';//API ключ ([a-Z0-9] (не забудьте его прописать в api.php)
$key_postback = 'ShJi8y';//postback ключ
$key_vt = '';//API ключ от VirusTotal
$tlg_bot_token = '';//ключ (токен) авторизации для Telegram бота
$tlg_chat_id = '';//ID аккаунта в Telegram
$confirm = 1;//подтверждать сортировку потоков, удаление логов/групп/потоков(0/1)
$index_mode = 2;//0 - не создавать индексы в БД; 1 - создавать только для движка; 2 - создавать для движка и админки;
$trash_mode = 0;//0 - белая страница (200 OK); 1 - HTTP редирект ($trash_url); 2 - 403 Forbidden; 3 - 404 Not Found;
$trash_url = 'http://www.ru';//url куда будем сливать весь мусор (переходы в несуществующие группы)
$file_api = 'api.php';//название файла API (api.php)
$file_totp = 'key.dat';//при использовании google authenticator обязательно переименуйте
$folder_tds = '';//для работы zTDS в папке укажите ее название, например $folder_tds = 'folder'; или $folder_tds = 'folder1/folder2'; если папка в папке
$folder_ini = 'ini';//название папки с файлами .ini
$folder_keys = 'keys';//название папки для сохранения ключевых слов (http://tds.com/keys)
$folder_log = 'log';//название папки с логами (http://tds.com/log)
$period_pb = 365;//хранить данные постбэка 365 дней
$period_log = 21;//хранить логи за последние 14 дней
$log_bots = 1;//сохранять в логах ботов (0/1)
$log_ref = 1;//сохранять в логах рефереры (0/1)
$log_ua = 1;//сохранять в логах юзерагенты (0/1)
$log_page = 1;//сохранять в логах страницу (0/1)
$log_key = 1;//сохранять в логах ключевые слова (0/1)
$log_out = 'api,iframe,javascript,show_page_html,show_text';//не сохранять в логах ауты для этих типов редиректа
$col_log = array("ID"=>"1:1","Time"=>"1:2","Group"=>"1:4","Stream"=>"1:3","Out"=>"1:4","Key"=>"1:3","Redirect"=>"1:4","Device"=>"1:2","WAP"=>"1:1","Country"=>"1:1","City"=>"1:2","Region"=>"1:3","Lang"=>"1:4","Uniq"=>"1:1","Bot"=>"1:1","IP"=>"1:1","Domain"=>"1:1","Page"=>"1:2","Referer"=>"1:3","UA"=>"1:3","SE"=>"1:2","$"=>"1:1");//столбцы в логах
$col_source = array("Sources"=>"1:1","Visitors"=>"1:2","Unique"=>"1:1","SE"=>"1:1","WAP"=>"1:1","Computers"=>"0:3","Tablets"=>"0:3","Phones"=>"0:3","$"=>"1:1");//столбцы в "Источники"
$col_pb = array("Date"=>"1:1","Time"=>"1:3","Page"=>"1:2","Device"=>"1:3","WAP"=>"0:4","Country"=>"1:1","City"=>"1:2","$"=>"1:1");//столбцы в "Postback"
$stats = array("Device"=>"1","WAP"=>"1");//выбор отображаемой статистики в правом меню (0/1)
$chart_bots = 1;//показывать статистику ботов на графиках (0/1)
$name_cookies = 'qwerty';//название cookies (измените)
$name_get_ex = 'ex';//название переменной GET с запакованными дополнительными параметрами
$name_get_key = 'q';//название переменной GET в которой передается ключевое слово
$cid_length = 10;//длина CID для постбэка
$cid_delimiter = ';';//разделитель данных внутри CID
$remote_ip_ch_url = '';//url сервиса проверки IP
$remote_ip_ch_sign = '';//признак бота
$curl_cache = 60;//кэшировать данные в течении 60 минут (тип редиректа CURL)
$curl_ua = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0';//useragent для CURL
$update_ip_url = 'https://myip.ms/files/bots/live_webcrawlers.txt';//ссылка на список IP ботов (зеркало: http://ztds.info/bots/webcrawlers.dat)
$update_ip_mode = 0;//тип обновления IP ботов (0 - удалить старые IP и сохранить новые; 1 - добавить новые IP к старому списку)
$cron_time_1 = '05:00';//время запуска обновления IP ботов (часы:минуты)
$cron_time_2 = '00:00,06:00,12:00,18:00';//время запуска проверки доменов в VirusTotal (часы:минуты)
$cron_time_3 = '09:00,20:00';//время запуска проверки свободного места на диске
$min_free_space = 500;//отправить сообщение в Telegram если на диске осталось меньше 500 Mb свободного места
$disable_tds = 0;//отключить TDS (0/1)
$error_log = 1;//сохранение ошибок PHP в файле err.log (0/1)
$error_display = 0;//вывод ошибок PHP на экран (0/1)
$debug = 0;//режим отладки, отключает проверку ключа API
$timeout_1 = 300000;//SQLite busyTimeout в index.php (в миллисекундах)
$timeout_2 = 60000;//SQLite busyTimeout в админке (в миллисекундах)
$max_ex_time = 180;//max_execution_time в index.php (в секундах)
$empty = '-';
$version = 'v.0.8.4';
if(!empty($folder_tds)){
	$log_path = $_SERVER['DOCUMENT_ROOT'].'/'.$folder_tds;
}
else{
	$log_path = $_SERVER['DOCUMENT_ROOT'];
}
@error_reporting(-1);
if($error_log == 1){
	@ini_set('log_errors', 1);
	@ini_set('error_log', $log_path.'/err.log');
}
if($error_display == 1){
	@ini_set('display_errors', 1);
}
else{
	@ini_set('display_errors', 0);
}
/*
Настройка отображения столбцов в адаптивных таблицах ($col_pb, $col_log, $col_source)
Например "Device"=>"1:3"
Первая цифра (1): 0 - скрыть столбец; 1 - показать. Если столбец скрыт то вторая цифра не имеет значения
Вторая цифра (3): приоритет отображения для маленьких экранов. Чем меньше число тем выше приоритет
Столбец "ID" кликабельный, под ним спрятаны данные всех столбцов которые не поместились на экране или были скрыты
*/
?>
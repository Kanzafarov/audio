<?php

/**
* Проверяем обновленные сайты на брутфорс защиту (выдача бана при подборе логина\пароля).
* К каждому сайту подключаемся (авторизируемся) 20 раз,
* В 1-ой попытке вводит правильный логин и пароль, тем самым проверяем что бана нет.
* Со 2-ой по 16-ую (в сумме 15 попыток), вводим неправильные данные. Затем снова правильные.
* -----
* Отчет по резульатам проверки в файле: log.txt
* IP пользователя должен совпадать с IP обращения к брутфорс функции на строное сайта
*/

error_reporting(-1);
ini_set('display_error', true);

include('list.php'); // Подключаем массив с сайтами
// $list_array = array('test.website.com');

//Удаляем Memcache на бан
function delMemcached($login) {

  $port = '11211'; // Порт Memcached
  $host = 'localhost'; // Хост Memcached

  $m = new Memcached();
  $m->addServer($host, $port) or die ("Нет соединения с Memcached");

  $ip = '192.168.7.79'; // IP пользователя
  $login = strtolower($login); // переводим в нижний регистр
  $key = $ip . $login; // Формируем ключ
  $ban = 'ban' . md5($key); // Ключ "бан"
  $get_ban = $m->delete($ban);

  if ($get_ban) {
    return 'Да';
  } else {
    return 'Нет';
  }

}

//Данные для авторизации
$post['login2'] = 'tester99';
$count = 1; // для счета сайтов

$log_file = fopen('log_real.txt', 'a+'); // открываем файл для записи


foreach ($list_array as $website) {

  echo "Проверяю:" . $website . " / "; //для визуализации процесса
  $string_header = $count . '. ' . date('Y M d H:i:s') . ' Сайт: ' . $website . PHP_EOL;
  $log_header_write = fwrite($log_file, $string_header);//Заголовок для каждого сайта
  $try = 1; //для счета попыток

  while ($try <= 18) { //делаем попытки

    if ($try > 1 && $try <= 16) {
      $post['pass2'] = 'pass_wrong'; //неправильный пароль
    } else {
      $post['pass2'] = 'tester999'; //правильный пароль
    }

    // $url = "http://192.168.7.79/sites/" . $website . "/www/system/ajax/authUser.php";
    $url = "http://" . $website . "/system/ajax/authUser.php";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
    $response_data = curl_exec($ch);
    curl_close ($ch);

    $string = $try . '. Ответ: ' . $response_data;
    $string .= ' / Логин: ' . $post['login2'] . '. Пароль: ' . $post['pass2'];
    $string .= ' Время: ' . round(microtime(true) * 1000) . PHP_EOL;

    $log_write = fwrite($log_file, $string); //записываем данные в файл
    echo "."; //для визуализации процесса
    $try++; //Плюс 1-а попытка
  }

  $count++; // Плюс 1 к порядковому номеру сайта
  $try = 0; // Обнуляем попытки для новой проверки
  // $delMem = "Удален:" . delMemcached($post['login2']) . PHP_EOL; // удаляем бан
  // fwrite($log_file, $delMem); //записываем статус удаления

  fwrite($log_file, PHP_EOL); //отступ после каждого отчета
  echo " Закончил.\n"; //для визуализации процесса
}

fclose($log_file); //Закрываем лог файл
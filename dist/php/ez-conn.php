<?php
  namespace ez_conn;
  //Подключаем ez_file.php
  $filename = 'ez_file.php';
  if (file_exists($filename)) {$test = "Exist YES - $filename";} 
  else {$test = "Exist NOT - $filename";} 
  include_once $filename;
  putContentsLog("EZ-CONN - Included $filename",100);
  //Подключаем БД
  $hostname = getParam("DB_HOSTNAME"); 
  $dbname   = getParam("DB_NAME"); 
  $username = getParam("DB_USERNAME"); 
  $password = getParam("DB_PASSWORD");
  
  $connConnection = mysqli_connect($hostname, $username, $password, $dbname) or die ("Could not connect to MySql");

  putContentsLog("EZ-CONN - mysqli_connect_errno=".mysqli_connect_errno(),100);
  /* check connection */ //тоже? - if (mysqli_connect_errno()) {
  //if (!$connConnection) {
  $err_conn = mysqli_connect_errno();
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", $err_conn);
    exit();
  }
  putContentsLog("EZ-CONN - err_conn=$err_conn",100);
  mysqli_query($connConnection, 'SET NAMES utf8') or header('Location: Error');
  //printf("Host information: %s\n", mysqli_get_host_info($connConnection));
  /*---------------------------------------------------*/
  /// Проверить соединение
  ///if (mysqli_connect_errno()) {
  ///    printf("Ошибка соединения: %s\n", mysqli_connect_error());
  ///    exit();
  ///}
?>

<?php
  $logPfxForm = "EZ-FORM-ZAKAZ - ";
  //ez-form-zakaz.php
  //Подключаем БД = include ez_file.php
  $filename = 'ez_file.php';
  if (file_exists($filename)) {$test = $logPfxForm."Exist YES - $filename";} 
  else {$test = $logPfxForm."Exist NOT - $filename";} 
  include $filename;
  putContentsLog($logPfxForm."----------------------- BEGIN -----------------------",100);
  putContentsLog($logPfxForm."Included $filename",100);
  //Подключаем БД = include ez-conn.php
  $filename = 'ez-conn.php';
  if (file_exists($filename)) {$test = $logPfxForm."Exist YES - $filename";} 
  else {$test = $logPfxForm."Exist NOT - $filename";} 
  putContentsLog($test,100);
  include $filename;
  putContentsLog($logPfxForm."Included $filename",100);
  //Подключаем ez-time = include ez-time.php
  $filename = 'ez-time.php';
  if (file_exists($filename)) {$test = $logPfxForm."Exist YES - $filename";} 
  else {$test = $logPfxForm."Exist NOT - $filename";} 
  putContentsLog($test,100);
  include $filename;
  putContentsLog($logPfxForm."Included $filename",100);


  /*------------------------------------------------------------------------*/
  if (isset($_POST["formModalId"])){ $formModalId =$_POST["formModalId"];} else{ $formModalId ='';}
  if (isset($_POST["formZakazId"])){ $formZakazId =$_POST["formZakazId"];} else{ $formZakazId ='';} 
  // Наименование товара на котором щелкнули может быть пустым. НЕ критично.
  if (isset($_POST["zakazGoodName"])){ $zakazGoodName =$_POST["zakazGoodName"];} else{ $zakazGoodName ='';} 
  putContentsLog($logPfxForm."Идентификатор модальной формы: formModalId=$formModalId",100);
  putContentsLog($logPfxForm."Идентификатор формы заказа: formZakazId=$formZakazId",100);
  putContentsLog($logPfxForm."Наименование товара из карточки товара: zakazGoodName=$zakazGoodName",100);
  /*------------------------------------------------------------------------*/

  $srvHttpReferer   = $_SERVER['HTTP_REFERER'];
  $srvHttps         = array_key_exists('HTTPS',$_SERVER) ? $_SERVER['HTTPS']:'Данные недоступны';
  $srvHttpUserAgent = $_SERVER['HTTP_USER_AGENT'];
  $srvRemoteAddr    = array_key_exists('REMOTE_ADDR',$_SERVER) ? $_SERVER['REMOTE_ADDR']:'127.0.0.1'; 
  $srvRemoteHost    = array_key_exists('REMOTE_HOST',$_SERVER) ? $_SERVER['REMOTE_HOST']:gethostbyaddr($_SERVER["REMOTE_ADDR"]);
  $srvRemoteUser    = array_key_exists('REMOTE_USER',$_SERVER) ? $_SERVER['REMOTE_USER']:gethostname();
  $hostByName       = gethostbyname ($srvRemoteHost);

  putContentsLog($logPfxForm." - SERVER:srvHttpReferer=$srvHttpReferer",100);
  putContentsLog($logPfxForm." - SERVER:srvHttps=$srvHttps",100);
  putContentsLog($logPfxForm." - SERVER:srvHttpUserAgent=$srvHttpUserAgent",100);
  putContentsLog($logPfxForm." - SERVER:srvRemoteHost=$srvRemoteHost",100);
  putContentsLog($logPfxForm." - SERVER:srvRemoteAddr=$srvRemoteAddr",100);
  putContentsLog($logPfxForm." - SERVER:srvRemoteUser=$srvRemoteUser",100);
  putContentsLog($logPfxForm." - HOST:hostByName=$hostByName",100);

  /*------------------------------------------------------------------------------------------------*/
  // FUNC - generateRandomString - BEGIN
  function generateRandomString($length = 30) {
    $funcLogPfxForm = "EZ-FORM-ZAKAZ - generateRandomString - ";
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    putContentsLog($funcLogPfxForm." :: generateRandomString - randomString=$randomString",10);
    return $randomString;
  }
  // FUNC - generateRandomString - BEGIN
  // FUNC - setResultArray - BEGIN
  function setResultArray($i_order_key, $i_err_code, $i_err_msg_t, $i_err_msg_s, $i_err_msg_l, 
                          $i_client_name, $i_client_phone, 
                          $i_car_vin, $i_car_mark, $i_car_model, $i_car_gener, $i_car_part, 
                          $i_client_id, $i_car_id, $i_order_id, $i_order_num, 
                          $i_form_modal_id, $i_form_zakaz_id)
  {
      $f_result = array(
              'order_key'     => $i_order_key,
              'err_code'      => $i_err_code,
              'err_msg_t'     => $i_err_msg_t,
              'err_msg_s'     => $i_err_msg_s,
              'err_msg_l'     => $i_err_msg_l,
              'client_name'   => $i_client_name,
              'client_phone'  => $i_client_phone,
              'car_vin'       => $i_car_vin,
              'car_mark'      => $i_car_mark,
              'car_model'     => $i_car_model,
              'car_gener'     => $i_car_gener,
              'car_part'      => $i_car_part,
              'client_id'     => $i_client_id,
              'car_id'        => $i_car_id,
              'order_id'      => $i_order_id,
              'order_num'     => $i_order_num,
              'form_modal_id' => $i_form_modal_id,
              'form_zakaz_id' => $i_form_zakaz_id
            );
      return $f_result;
  };
  // FUNC - setResultArray - END
  // FUNC - checkPhoneNumber - BEGIN
  function checkPhoneNumber($i_phoneNumber) {
    $f_phoneNumberDraft = $i_phoneNumber;
    $f_phoneNumberClear = preg_replace('/\s|\+|-|\(|\)/','', $f_phoneNumberDraft);//удалимпробелы,иненужныезнаки
     if (is_numeric($f_phoneNumberClear)) {
          $f_phoneNumberFormatPart0 = '+7';
          $f_phoneNumberFormatPart1 = substr($f_phoneNumberClear, 1, 3);
          $f_phoneNumberFormatPart2 = substr($f_phoneNumberClear, 4, 3);
          $f_phoneNumberFormatPart3 = substr($f_phoneNumberClear, 7, 2);
          $f_phoneNumberFormatPart4 = substr($f_phoneNumberClear, 9, 2);
          $f_phoneNumberFormat      = $f_phoneNumberFormatPart0.'('.
                                      $f_phoneNumberFormatPart1.')'.
                                      $f_phoneNumberFormatPart2.'-'.
                                      $f_phoneNumberFormatPart3.'-'.
                                      $f_phoneNumberFormatPart4;
    }
    return array($f_phoneNumberDraft,$f_phoneNumberClear,$f_phoneNumberFormat);
  }
  // FUNC - checkPhoneNumber - END
  // FUNC - checkQueryResult - BEGIN
  function checkQueryResult ( $i_connConnection, $i_queryResult, $i_orderKey, $i_logAct, 
                              $i_errCode, $i_errMsg, $i_clientName, $i_clientPhone, 
                              $i_carVin, $i_carMark, $i_carModel, $i_carGener, $i_carPart,
                              $i_clientID, $i_carID, $i_orderID, $i_order_num, $i_formName, $i_formModal
                            ) {
    $funcLogPfxForm = "EZ-FORM-ZAKAZ - checkQueryResult - ";
    putContentsLog($funcLogPfxForm." - checkQueryResult:BEGIN",10);
    $f_sqlErr     = mysqli_error($i_connConnection);
    if ($f_sqlErr) {
      $f_errCode    = $i_errCode;
      $f_errMsgT    = $i_logAct;
      $f_errMsgS    = $i_errMsg;
      $f_errMsgL    = 'ERROR'.$i_errCode.'<br>'.$f_errMsgS.'<br>'.$f_sqlErr;
    } else {
      $f_errCode    = 0;
      $f_errMsgT    = '';
      $f_errMsgS    = '';
      $f_errMsgL    = '';
    }
    $f_result = setResultArray( $i_orderKey, $f_errCode, $f_errMsgT, $f_errMsgS, $f_errMsgL,
                                $i_clientName, $i_clientPhone, 
                                $i_carVin, $i_carMark, $i_carModel, $i_carGener, $i_carPart,
                                $i_clientID, $i_carID, $i_orderID, $i_order_num, 
                                $i_form_modal_id, $i_form_zakaz_id
                              );
    putContentsLog($funcLogPfxForm." - checkQueryResult: f_errCode=$f_errCode",100);
    putContentsLog($funcLogPfxForm." - checkQueryResult: f_errMsgT=$f_errMsgT",100);
    putContentsLog($funcLogPfxForm." - checkQueryResult: f_errMsgS=$f_errMsgS",100);
    putContentsLog($funcLogPfxForm." - checkQueryResult: f_errMsgL=$f_errMsgL",100);
    putContentsLog($funcLogPfxForm." - checkQueryResult: f_sqlErr=$f_sqlErr",100);

    putContentsLog($funcLogPfxForm." - checkQueryResult:END",10);

    return $f_result;
  }
  // FUNC - checkQueryResult - END;

  putContentsLog($logPfxForm." - Инициализация переменных пакета",10);
  $phoneNumberDraft   = "";
  $phoneNumberClear   = "";
  $phoneNumberFormat  = "";

  $orderKey     = "Ключ заказа";
  $orderKeyID   = 0;
  $logAct       = 'Выполняемое действие';
  $errCode      = 0;
  $errMsgT      = "Заголовок модального окна";
  $errMsgS      = "Текст модального окна (короткий)";
  $errMsgL      = "Текст модального окна (длинный)";
  $clientName   = "";
  $clientPhone  = "";
  $carVin       = "";
  $carMark      = "";
  $carModel     = "";
  $carGener     = "";
  $carPart      = "";  
  $clientID     = 0;
  $carID        = 0;
  $orderID      = 0;
  $orderNum     = '000000-123-456';

  $orderKey     = generateRandomString();
  mysqli_query($connConnection, 'SET NAMES utf8') or header('Location: Error');
  //printf("Host information: %s\n", mysqli_get_host_info($connConnection));
  putContentsLog($logPfxForm." - Заполение переменных пакета значениями из формы заказа",10);
  if (isset($_POST[$formZakazId."UserName"])){$clientName =$_POST[$formZakazId."UserName"];} else{$clientName='';}
  if (isset($_POST[$formZakazId."ContactPhone"])){$clientPhone=$_POST[$formZakazId."ContactPhone"];} else{$clientPhone='';}
  if (isset($_POST[$formZakazId."CarVIN"])){ $carVin=$_POST[$formZakazId."CarVIN"];} else{ $carVin='';}
  if (isset($_POST[$formZakazId."OrdComment"])){ $ordComment=$_POST[$formZakazId."OrdComment"];} else{ $ordComment='';}

putContentsLog($logPfxForm." *** - ordComment=$ordComment",100);

  putContentsLog($logPfxForm." - Ключ заказа = ".$orderKey,100);
  putContentsLog($logPfxForm." - Значения переданные из формы заказа:",100);
  putContentsLog($logPfxForm." - Имя клиента: ".$clientName,100);
  putContentsLog($logPfxForm." - Номер телефона клиента: ".$clientPhone,100);
  putContentsLog($logPfxForm." - VIN номер автомобиля: ".$carVin,100);
  putContentsLog($logPfxForm." - Начата проверка валидности полей Ваше имя и Ваш телефон",10);
  if ($clientName=='') {
      $errCode  = -2001;
      $errMsgT  = "Заполнение формы заказа";
      $errMsgS  = "Поля&nbsp;&laquo;Ваше&nbsp;имя&raquo;&nbsp;и&nbsp;&laquo;Ваш&nbsp;телефон&raquo; являются&nbsp;обязательными для&nbsp;заполнения!";
      $errMsgS  = $errMsgS."<br>"."Укажите, пожалуйста, Ваше имя!";
      $errMsgL  = $errMsgS;
      putContentsLog($logPfxForm." - errCode=$errCode",100);
      putContentsLog($logPfxForm." - errMsgS=$errMsgS",100);
      putContentsLog($logPfxForm." - errMsgL=$errMsgL",100);
      $result = setResultArray( $orderKey, 
                                $errCode, $errMsgT, $errMsgS, $errMsgL,
                                $clientName, $clientPhone, $carVin, $carMark, $carModel, $carGener, $carPart,
                                $clientID, $carID, $orderID, $orderNum, $formModalId, $formZakazId);
      echo json_encode($result, JSON_UNESCAPED_UNICODE); // как бы руссификация :)
      return;
  }
  if ($clientPhone=='') {
      $errCode  = -2002;
      $errMsgT  = "Заполнение формы заказа";
      $errMsgS  = "Поля&nbsp;&laquo;Ваше&nbsp;имя&raquo;&nbsp;и&nbsp;&laquo;Ваш&nbsp;телефон&raquo; являются&nbsp;обязательными для&nbsp;заполнения!";
      $errMsgS  = $errMsgS."<br>"."Укажите, пожалуйста, Ваш телефон!";
      $errMsgL  = $errMsgS;
      putContentsLog($logPfxForm." - errCode=$errCode",100);
      putContentsLog($logPfxForm." - errMsgS=$errMsgS",100);
      putContentsLog($logPfxForm." - errMsgL=$errMsgL",100);
      $result = setResultArray( $orderKey, 
                                $errCode, $errMsgT, $errMsgS, $errMsgL,
                                $clientName, $clientPhone, $carVin, $carMark, $carModel, $carGener, $carPart,
                                $clientID, $carID, $orderID, $orderNum, $formModalId, $formZakazId);
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return;
  } 
  $phoneNumberArray = checkPhoneNumber($clientPhone);
  list  ($phoneNumberDraft, $phoneNumberClear, $phoneNumberFormat) = $phoneNumberArray;
  if (!$phoneNumberFormat) {
      $errCode    = -2003;
      $errMsgT    = "Заполнение формы заказа";
      //$errMsgS    = "Поле&nbsp;&laquo;Ваш&nbsp;телефон&raquo; должно содержать 10 цифр!";
      $errMsgS    = $errMsgS."<br>"."Пожалуйста, введите правильно Ваш телефон!";
      $errMsgL    = $errMsgS;
      putContentsLog($logPfxForm." - errCode=$errCode",100);
      putContentsLog($logPfxForm." - errMsgS=$errMsgS",100);
      putContentsLog($logPfxForm." - errMsgL=$errMsgL",100);
      putContentsLog($logPfxForm." - phoneNumberDraft=$phoneNumberDraft; phoneNumberClear=$phoneNumberClear; ".
                     "phoneNumberFormat=$phoneNumberFormat",100);
      $result = setResultArray( $orderKey, 
                                $errCode, $errMsgT, $errMsgS, $errMsgL,
                                $clientName, $clientPhone, $carVin, $carMark, $carModel, $carGener, $carPart,
                                $clientID, $carID, $orderID, $orderNum, $formModalId, $formZakazId);
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return;
  }
  /*--------------------------------------------------------------------------------*/
  putContentsLog($logPfxForm." - EZ_CLIENT",100);
  putContentsLog($logPfxForm." - Завершена проверка валидности полей Ваше имя и Ваш телефон формы заказа",10);
  putContentsLog($logPfxForm." - phoneNumberDraft=$phoneNumberDraft; phoneNumberClear=$phoneNumberClear; ".
                 "phoneNumberFormat=$phoneNumberFormat",10);
  putContentsLog($logPfxForm." - Проверка наличия клиента по имени и телефону в EZ_CLIENTS",10);
  $clientSelectQuery = "select id from ez_clients where upper(name)=upper('$clientName') and upper(phone)=upper('$clientPhone') limit 1";
  putContentsLog($logPfxForm." - Текст запроса=$clientSelectQuery",10);
  $clientSelectResult = mysqli_query($connConnection, $clientSelectQuery) or die (mysqli_error($connConnection));
  putContentsLog($logPfxForm." - Запрос выполнен",10);
  putContentsLog($logPfxForm." - Определяем ID клиента. Ищем в выборке",10);
  while ($сlientRows = mysqli_fetch_array($clientSelectResult)) {$clientID = $сlientRows['id'];}
  if ($clientID==0) {$clientExist = "Не определен ID клиента. Считаем, что в БД клиента нету";} 
  else {$clientExist = "Определен ID клиента = $clientID";};  
  putContentsLog($logPfxForm." - $clientExist",100);

  if ($clientID == 0) {
    putContentsLog($logPfxForm." - Не определен ID клиента. Создаем нового клиента",100);
    $clientInsertQuery =  "insert into ez_clients (id,name,phone,phone_draft,phone_clear,phone_format,mail) ".
                          "values (null,'$clientName','$clientPhone',".
                          "'$phoneNumberDraft','$phoneNumberClear','$phoneNumberFormat','e-mail')";
    putContentsLog($logPfxForm." - Запрос на добавление клиента: $clientInsertQuery",10);
    $logAct = 'Заказ обратного звонка';
    $sqlResultInsert = mysqli_query($connConnection, $clientInsertQuery);
    if (!$sqlResultInsert) {
      $sqlErr     = mysqli_error($connConnection);
      $errCode    = -2202;
      $errMsgT    = $logAct;
      $errMsgS    = "Ошибка регистрации нового клиента.";
      $errMsgL    = $errMsgS."<br>".$sqlErr;
      putContentsLog($logPfxForm." - Запрос на добавление клиента выполнен с ошибкой:",100);
      putContentsLog($logPfxForm." - $sqlErr",100);
      putContentsLog($logPfxForm." - Код ошибки: $errCode",100);
      $result = setResultArray( $orderKey, 
                                $errCode, $errMsgT, $errMsgS, $errMsgL,
                                $clientName, $clientPhone, $carVin, $carMark, $carModel, $carGener, $carPart,
                                $clientID, $carID, $orderID, $orderNum, $formModalId, $formZakazId);
      echo json_encode($result, JSON_UNESCAPED_UNICODE); // как бы руссификация :)
      return; 
    };  
    putContentsLog($logPfxForm." - Запрос на добавление клиента выполнен успешно",100);
    putContentsLog($logPfxForm." - Определяем ID добавленного клиента",10);
    putContentsLog($logPfxForm." - Запрос для определения: $clientSelectQuery",10);
    $logAct = 'Заказ обратного звонка';
    $clientSelectResult=mysqli_query($connConnection, $clientSelectQuery) or die (mysqli_error($connConnection));
    if (!$clientSelectResult) { //не выдал ли нам запрос ошибки 
      $sqlErr = mysqli_error($connConnection);
      $errCode    = -2203;
      $errMsgT    = $logAct;     // 'Добавление новой записи в EZ_CLIENTS.'
      $errMsgS    = "Ошибка при попытке определить идентификатор добавленного клиента.";
      $errMsgL    = $errMsgS.'<br>'.$sqlErr;
      putContentsLog($logPfxForm." - Запрос для определения ID клиента выполнен выполнен с ошибкой:",100);
      putContentsLog($logPfxForm." - $sqlErr",100);
      putContentsLog($logPfxForm." - Код ошибки: $errCode",100);
      $result = setResultArray( $orderKey, 
                                $errCode, $errMsgT, $errMsgS, $errMsgL,
                                $clientName, $clientPhone, $carVin, $carMark, $carModel, $carGener, $carPart,
                                $clientID, $carID, $orderID, $orderNum, $formModalId, $formZakazId );
      echo json_encode($result, JSON_UNESCAPED_UNICODE); // как бы руссификация :)
      return; 
    }
    putContentsLog($logPfxForm." - Запрос для определения ID клиента выполнен выполнен успешно",10);
    putContentsLog($logPfxForm." - Определяем ID добавленного клиента $clientName с телефоном $clientPhone",10);
    $clientID = 0;
    while ($clientRows = mysqli_fetch_array($clientSelectResult)) {$clientID = $clientRows['id'];}
    putContentsLog($logPfxForm." - В таблицу EZ_CLIENTS добавлен клиент с ID = $clientID",100);
  }  
  /*--------------------------------------------------------------------------------*/
  putContentsLog($logPfxForm." - EZ_CLIENT_CARS",100);
  putContentsLog($logPfxForm." - Поиск автомобиля для клиента в таблицу EZ_CLIENT_CARS по vin",100);
  putContentsLog($logPfxForm." - VIN=$carVin",100);
  $carSelectQuery =   "select id from ez_client_cars WHERE client_id = '$clientID' ".
                      " and COALESCE(vin,'') = '$carVin' ".
                      " order by created_at desc limit 1";
  putContentsLog($logPfxForm." - Запрос для выборки автомобиля клиента:",10);
  putContentsLog($logPfxForm." - $carSelectQuery",10);
  $carSelectQueryResult = mysqli_query($connConnection, $carSelectQuery);
  putContentsLog($logPfxForm." - Запрос для выборки автомобиля клиента выполнен",10);
  $carSelectQueryRowsCount = mysqli_num_rows($carSelectQueryResult); /* определение числа строк в выборке */
  putContentsLog($logPfxForm." - Выбрано автомобилей: $carSelectQueryRowsCount",10);
  if ($carSelectQueryRowsCount>0) {
    while ($carSelectQueryResultRow = mysqli_fetch_array($carSelectQueryResult)) 
      {$carID = $carSelectQueryResultRow['id'];};
  } else {
    $carID = 0;
  }
  putContentsLog($logPfxForm." - Определен ID автомобиля для клиента: $carID",100);
  if ($carSelectQueryRowsCount == 0) {
    putContentsLog($logPfxForm." - Добавление нового автомобиля для клиента",10);
    $carInsertQuery = "insert into ez_client_cars(id,client_id,vin,mark,model,gener) ".
                      " values (NULL,'$clientID','$carVin','$carMark','$carModel','$carGener')";
    putContentsLog($logPfxForm." - Запрос для добавления нового автомобиля для клиента:",10);
    putContentsLog($logPfxForm." - $carInsertQuery",10);            
    $logAct = 'Заказ обратного звонка';      
    $carInsertQueryResult = mysqli_query($connConnection, $carInsertQuery);
    if (!$carInsertQueryResult) {
      $sqlErr     = mysqli_error($connConnection);
      $errCode    = -2301;
      $errMsgT    = $logAct;
      $errMsgS    = "Ошибка при попытке добавление нового автомобиля для клиента.";
      $errMsgL    = $errMsgS."<br>".$sqlErr;
      putContentsLog($logPfxForm." - Запрос для добавления автомобиля для клиента выполнен с ошибкой:",100);
      putContentsLog($logPfxForm." - $sqlErr",100);
      putContentsLog($logPfxForm." - Код ошибки: $errCode",100);
      $result = setResultArray( $orderKey, 
                                $errCode, $errMsgT, $errMsgS, $errMsgL,
                                $clientName, $clientPhone, $carVin, $carMark, $carModel, $carGener, $carPart,
                                $clientID, $carID, $orderID, $orderNum, $formModalId, $formZakazId);
      echo json_encode($result, JSON_UNESCAPED_UNICODE); // как бы руссификация :)
      return; 
    };               
    putContentsLog($logPfxForm." - Запрос для добавления нового автомобиля для клиента выполнен успешно",10); 
    putContentsLog($logPfxForm." - Определяем ID созданного автомобиля",10); 
    $carSelectQueryResult = mysqli_query($connConnection, $carSelectQuery); 
    putContentsLog($logPfxForm." - Запрос для определения ID созданного автомобиля:",10); 
    putContentsLog($logPfxForm." - $carSelectQuery"); 
    $carSelectQueryResult = mysqli_query($connConnection, $carSelectQuery); 
    putContentsLog($logPfxForm." - Запрос для определения ID созданного автомобиля выполнен",10);
    while ($carSelectQueryResultRow = mysqli_fetch_array($carSelectQueryResult)) 
      {$carID = $carSelectQueryResultRow['id'];};
    putContentsLog($logPfxForm." - ID созданного автомобиля определен: $carID",100);
  }
  mysqli_free_result($carSelectQueryResult); /* закрытие выборки */
  /*------------------ EZ-FORM-ZAKAZ - GET_CLIENT_ORDERS_HOURL - BEGIN ------------------*/
  putContentsLog($logPfxForm." - GET_CLIENT_ORDERS_HOURLY - BEGIN clientID=$clientID",100);
  $clientOrdersHourlySelect = "select get_client_orders_hourly('$clientID')";
  $clientOrdersHourlyResult = mysqli_query($connConnection, $clientOrdersHourlySelect);
  $clientOrdersHourlyCount  = $clientOrdersHourlyResult->fetch_row()[0];
  putContentsLog($logPfxForm." - GET_CLIENT_ORDERS_HOURLY - Запрос выполнен",100);
  putContentsLog($logPfxForm." - GET_CLIENT_ORDERS_HOURLY - Количество заказов=$clientOrdersHourlyCount",100);
  mysqli_free_result($clientOrdersHourlyResult); /* закрытие выборки */
  $stopOrdersHourlyCount  = getParam("ORDERS_HOURLY_COUNT");
  putContentsLog($logPfxForm." - GET_CLIENT_ORDERS_HOURLY - stopOrdersHourlyCount=$stopOrdersHourlyCount",100);
  IF ($clientOrdersHourlyCount >= $stopOrdersHourlyCount) { 
    putContentsLog($logPfxForm." - GET_CLIENT_ORDERS_HOURLY - количество заказов за час превысило допустимое",100);
    $logAct   = 'Заказ обратного звонка';
    $sqlErr   = mysqli_error($connConnection);
    $errCode  = -2005;
    $errMsgT  = "ЗАКАЗ НЕ ПРИНЯТ";
    $errMsgS  = "Уважаемый $clientName!<br>Ваш заказ не может быть принят.".
              "<br>Количество сделанных Вами заказов превысило допустимое за промежуток времени.".
              "<br><br>Пожалуйста, попробуйте позже.";
    $errMsgL  = $errMsgS."Количество заказов в течении часа $clientOrdersHourlyCount";  
    putContentsLog($logPfxForm." - GET_CLIENT_ORDERS_HOURLY - ".$errMsgL,100);
    putContentsLog($logPfxForm." - GET_CLIENT_ORDERS_HOURLY - $sqlErr",100);
    putContentsLog($logPfxForm." - GET_CLIENT_ORDERS_HOURLY - Код ошибки: $errCode",100);
    $result = setResultArray( $orderKey, 
                              $errCode, $errMsgT, $errMsgS, $errMsgL,
                              $clientName, $clientPhone, $carVin, $carMark, $carModel, $carGener, $carPart,
                              $clientID, $carID, $orderID, $orderNum, $formModalId, $formZakazId);
    echo json_encode($result, JSON_UNESCAPED_UNICODE); // как бы руссификация :)
    return; 
  }
  putContentsLog($logPfxForm." - GET_CLIENT_ORDERS_HOURLY - END",100);
  /*------------------ EZ-FORM-ZAKAZ-SIMPLE - GET_CLIENT_ORDERS_HOURL - END ------------------*/
  /*---------------------------------- EZ_CAR_ORDERS - Begin ---------------------------------*/
  putContentsLog($logPfxForm." - EZ_CAR_ORDERS",100);
  putContentsLog($logPfxForm." - Добавление нового заказа для автомобиля",10);
    $orderInsertQuery = "insert into ez_car_orders(id,car_id,order_key,".
                        "srv_http_referer,srv_https,srv_http_user_agent,srv_remote_addr,".
                        "srv_remote_host,srv_remote_user,hst_host_by_name) ".
                        "values (NULL,'$carID','$orderKey',".
                        "'$srvHttpReferer','$srvHttps','$srvHttpUserAgent','$srvRemoteAddr',".
                        "'$srvRemoteHost','$srvRemoteUser','$hostByName')";
    putContentsLog($logPfxForm." - Запрос для добавления нового заказа для автомобиля:",10);
    putContentsLog($logPfxForm." - $orderInsertQuery",10);
    $logAct = 'Заказ обратного звонка';                  
    $orderInsertQueryResult = mysqli_query($connConnection, $orderInsertQuery);
    if (!$orderInsertQueryResult) {
      $sqlErr     = mysqli_error($connConnection);
      $errCode    = -2401;
      $errMsgT    = $logAct;
      $errMsgS    = "Ошибка при попытке добавление нового заказа.";
      $errMsgL    = $errMsgS."<br>".$sqlErr;
      putContentsLog($logPfxForm." - Запрос для добавление нового заказа выполнен с ошибкой:",100);
      putContentsLog($logPfxForm." - $sqlErr",100);
      putContentsLog($logPfxForm." - Код ошибки: $errCode",100);
      $result = setResultArray( $orderKey, 
                                $errCode, $errMsgT, $errMsgS, $errMsgL,
                                $clientName, $clientPhone, $carVin, $carMark, $carModel, $carGener, $carPart,
                                $clientID, $carID, $orderID, $orderNum, $formModalId, $formZakazId);
      echo json_encode($result, JSON_UNESCAPED_UNICODE); // как бы руссификация :)
      return; 
    };               
    putContentsLog($logPfxForm." - Запрос для добавления нового заказа выполнен успешно",10); 
    putContentsLog($logPfxForm." - Определяем ID созданного заказа",10); 
    $orderSelectQuery = "select id from ez_car_orders where order_key = '$orderKey' order by created_at desc limit 1";
    putContentsLog($logPfxForm." - Текст запроса=$orderSelectQuery",10);
    $orderSelectQueryResult = mysqli_query($connConnection, $orderSelectQuery); 
    putContentsLog($logPfxForm." - Запрос для определения ID созданного заказа выполнен",10); 
    while ($orderSelectQueryResultRow = mysqli_fetch_array($orderSelectQueryResult)) 
      {$orderID = $orderSelectQueryResultRow['id'];};
    putContentsLog($logPfxForm." - ID созданного заказа определен: $orderID",100);
    $orderNum = str_pad($orderID, 6, "0", STR_PAD_LEFT);
    $orderNum = substr("$orderNum", 0, 3)."-".substr("$orderNum", 3);
    $orderNum = date( "ymd" )."-".$orderNum;
    putContentsLog($logPfxForm." - Определен Номер заказа для клиента: $orderNum",100);
    putContentsLog($logPfxForm." - Сохраняем Номер заказа в EZ_CAR_ORDERS",10); 
    $orderUpdateQuery = "update ez_car_orders set order_num = '$orderNum' where id = $orderID";
    putContentsLog($logPfxForm." - Текст запроса=$orderUpdateQuery",10);
    $orderSelectQueryResult = mysqli_query($connConnection, $orderUpdateQuery); 
    putContentsLog($logPfxForm." - Номер заказа сохранен в EZ_CAR_ORDERS",10); 
    putContentsLog($logPfxForm." - Формирование заказа завершено успешно",10); 
    /*---------------------------------- EZ_CAR_ORDERS - End ---------------------------------*/
    /*---------------------------------- EZ_COMMENTS - Begin ---------------------------------*/
  putContentsLog($logPfxForm." - EZ_COMMENTS",100);
  putContentsLog($logPfxForm." - Добавление комментария для заказа",10);
    $commentInsertQuery = "insert into ez_comments(id,own,obj,obj_id,cmt) ".
                        "values (NULL,'CLI','ORD',$orderID,'$ordComment')";
    putContentsLog($logPfxForm." - Запрос добавления нового комментария для заказа:",10);
    putContentsLog($logPfxForm." - $commentInsertQuery",10);
    $logAct = 'Заказ обратного звонка';                  
    $commentInsertQueryResult = mysqli_query($connConnection, $commentInsertQuery);
    if (!$commentInsertQueryResult) {
      $sqlErr     = mysqli_error($connConnection);
      $errCode    = -2501;
      $errMsgT    = $logAct;
      $errMsgS    = "Ошибка при попытке добавление нового комментария для заказа.";
      $errMsgL    = $errMsgS."<br>".$sqlErr;
      putContentsLog($logPfxForm." - Запрос добавление комментария для заказа выполнен с ошибкой:",100);
      putContentsLog($logPfxForm." - Текст запроса: $commentInsertQuery",100);
      putContentsLog($logPfxForm." - $sqlErr",100);
      putContentsLog($logPfxForm." - Код ошибки: $errCode",100);
      $result = setResultArray( $orderKey, 
                                $errCode, $errMsgT, $errMsgS, $errMsgL,
                                $clientName, $clientPhone, $carVin, $carMark, $carModel, $carGener, $carPart,
                                $clientID, $carID, $orderID, $orderNum, $formModalId, $formZakazId);
      echo json_encode($result, JSON_UNESCAPED_UNICODE); // как бы руссификация :)
      return; 
    };               
    putContentsLog($logPfxForm." - Запрос для добавления нового комментария для заказа выполнен успешно",10); 
    /*---------------------------------- EZ_COMMENTS - End ---------------------------------*/
    putContentsLog($logPfxForm." - Формирование заказа завершено успешно",10); 
    /*----- Используем EZ-TIME Begin -----*/
    $orderInWorkTime = checkOrderInWorkTime(); //RETURN=OK,BREAK,AFDAY,BFDAY,SUNDAY
    putContentsLog($logPfxForm." - orderInWorkTime = ".$orderInWorkTime,100);
    $timeCall = "<br>Оператор свяжется с Вами"."<br>в течении 15 минут!";
    switch ($orderInWorkTime) {
    case 'OK':$timeCall = "<br>Оператор свяжется с Вами"."<br>в течении 15 минут!";break;
    case 'BREAK':$timeCall = "<br>Оператор свяжется с Вами"."<br>в период с 14:00 до 15:00 часов!";break;
    case 'AFDAY':$timeCall = "<br>Оператор свяжется с Вами"."<br>завтра с 10:00 до 11:00 часов!";break;
    case 'BFDAY':$timeCall = "<br>Оператор свяжется с Вами"."<br>сегодня с 10:00 до 11:00 часов!";break;
    case 'SUNDAY':$timeCall = "<br>Оператор свяжется с Вами"."<br>завтра с 10:00 до 11:00 часов!";break;
    default:$timeCall = "<br>Оператор свяжется с Вами"."<br>в течении 15 минут!";
    }
    //BREAK,AFDAY,BFDAY
    /*----- Используем EZ-TIME End -----*/
    $errCode    = 0;
    $errMsgT    = "ЗАКАЗ ПРИНЯТ";
    $errMsgS    = "Уважаемый $clientName, Ваш заказ принят!".
                //"<br>Оператор свяжется с Вами в течении 15 минут!".
                $timeCall.
                "<br>Номер Вашего заказа: $orderNum".
                //"<br>Ключ Вашего заказа: $orderKey".
                "<span class='animated rotateIn infinite'><br>Скидка для Вашего заказа составляет <span class='ez-form-zakaz-color-red'>10%</span></span>!".
                "<br><br>Благодарим за обращение к нам!";
    $errMsgL    = $errMsgS;
    putContentsLog($logPfxForm." - errMsgS=$errMsgS",100); 
    ////////////////////////////////////////////////////
    /////////// --- TELEGRAM  --- //////////////////////
    $token  = getParam("TLG_TOKEN");  //Почему то после этого fopen не может создать поток???
    $chatId = getParam("TLG_CHAT_ID");//failed to open stream: HTTP request failed! HTTP/1.1 404 Not Found 
    ////////////////////////////////////////////////////
    $msg = "Новая заявка на сайте!\n$srvHttpReferer\nТелефон: $clientPhone \nИмя: $clientName";
    if (!($carVin == '')) {
      $msg .= "\nVIN: $carVin";
    }
    if (!($zakazGoodName == '')) {
      $msg .= "\nИнтерес: $zakazGoodName";
    }
    if (!($ordComment == '')) {
      $msg .= "\nКомментарий: $ordComment";
    }
    $dt = date('d-m-Y H:i:s');
    $msg .= "\n$dt";
    $msg = urlencode($msg);
    //$sendToTelegram = fopen("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chatId}&parse_mode=html&text={$msg}","r");
    $sendToTelegram = file_get_contents("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chatId}&parse_mode=html&text={$msg}","r");
    ///////////////////////////////////////////////////
    ////////////////////////////////////////////////////

    $result = setResultArray( $orderKey, 
                              $errCode, $errMsgT, $errMsgS, $errMsgL,
                              $clientName, $clientPhone, $carVin, $carMark, $carModel, $carGener, $carPart,
                              $clientID, $carID, $orderID, $orderNum, $formModalId, $formZakazId
                            );
    echo json_encode($result, JSON_UNESCAPED_UNICODE); // как бы руссификация :)
    return; 
?>

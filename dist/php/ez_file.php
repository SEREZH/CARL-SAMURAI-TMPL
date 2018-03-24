<?php 
function fcreate($filename,$string){
	$path = 'files/'.$filename.'.txt'; //путь к файлу
  	$file = fopen($path,'w'); //создаем файл и открываем его для записи
  	$write = fwrite($file, $string); //Записываем строку в файл
    if($write) echo "Данные успешно записаны!<br>"; // проверяем успешность выполнения операции
  	else echo "Не удалось записать данные!<br>";
	//закрываем файл
	fclose($file);
}

function fileread($filename){
	$path = 'files/'.$filename.'.txt'; 		// путь к файлу
	$file = fopen($path,'r'); 				// открываем файл для чтения
	if($file){								// если файл успешно открылся
	    while (!feof($file)) {				// тогда пока файл не достигнет конца
	      $read = fgets($file);				// считываем данные с файла
	      echo $read.'<br>';				// и выводим текст на экран
	    }
	}
	else echo "Ошибка при открытии файла<br>"; // если файл не открылся, то сообщаем об этом

  	// Осуществим проход массива и выведем содержимое в виде HTML-кода вместе с номерами строк.
	foreach ($lines as $line_num => $line) {
    	echo "Строка #<b>{$line_num}</b> : " . htmlspecialchars($line) . "<br />\n";
	}
	// Второй пример. Получим содержание веб-страницы в виде одной строки.
	// См. также описание функции file_get_contents().
	$html = implode('', file('//www.zapchasti40.ru/'));

  	fclose($file); // Закрываем файл
}

function putContentsLog($i_content, $i_level=1){
	$f_log_level_min = getParam("LOG_LEVEL_MIN");
	if ($i_level >= $f_log_level_min) {
		$f_file 		= 'app.log';
		$f_filePath 	= '../log/'.$f_file;
		$f_content 		= $i_content;
		$f_time 		= date("m.d.y H:i:s");
		$f_content 		= $f_time.' ::: CL'.$i_level.'=ML'.$f_log_level_min.' :: '.$f_content; 
		file_put_contents($f_filePath, PHP_EOL.$f_content, FILE_APPEND | LOCK_EX);
	}	
}

function getParam($i_param_code){
	//формат строки параметров: КОД=ЗНАЧЕНИЕ
	$f_param_value 	= 1;
	$f_path = 'params.txt';
	$param_list = explode("\n", file_get_contents($f_path));
	foreach ( $param_list as $param_key=>$param_value )
    {
        if ( strpos($param_value,$i_param_code,0) !==false )
        {
	      	$f_param_row 	= $param_list[$param_key];
	      	$f_param_row_list = explode("=", $f_param_row);
	      	$f_param_value = $f_param_row_list[1];
			break;
        }
    }
    $f_param_value = str_replace(array("\r","\n"),"",$f_param_value);
	return $f_param_value;
}	

function getMessage($i_msg_code){
	$f_msg_code 	= $i_msg_code;
	$f_msg_exists 	= 'no';
	$f_msg_title 	= 'Поиск сообшения';
	$f_msg_message1 = 'Строка '.$f_msg_code.' не найдена';
	$f_msg_message2 = '';
	$f_msg_message0 = '';
	$f_path = 'messages.txt';
	$result = explode("\n", file_get_contents($f_path));
	foreach ( $result as $key=>$val )
    {
        if ( strpos($val,$f_msg_code,0) !==false )
        {
            /*echo $key.'-'.$val;*/
            $f_msg_exists 	= 'yes = '.$key.'-'.$val;
	      	$f_msg_title 	= $result[$key + 1];		// следуюшая строка = Заголовок;
			$f_msg_message1 = $result[$key + 2];		// следуюшая строка = Сообщение 1;
			$f_msg_message2 = $result[$key + 3];		// следуюшая строка = Сообщение 2;
			break;
        }
    }
	return array($f_msg_code,$f_msg_exists,$f_msg_title,$f_msg_message1,$f_msg_message2);
	// принимаем результат - list  ($v_msg_code,$v_msg_exists,$v_msg_title,$v_msg_message1,$v_msg_message2);
}



?>
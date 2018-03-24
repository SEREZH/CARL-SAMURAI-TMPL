<?php
  //ez-form-zakaz-simple.php
  $logPfx = "EZ-TIME- ";
  //Подключаем файл для работы с файлами = include ez_file.php
  // !!! он подключен ранньше в "верхнем" файле ez-form-zakaz-simple.php 
  $filename = 'ez_file.php';
  if (file_exists($filename)) {$test = $logPfx."Exist YES - $filename";} 
  else {$test = $logPfx."Exist NOT - $filename";} 
  include_once $filename;
  putContentsLog($logPfx." ------------ BEGIN -------------------",100);
  putContentsLog($logPfx."Included $filename",100);
  //Подключаем Carbon = include Carbon.php
  $filename = '../lib/carbon/Carbon.php';
  if (file_exists($filename)) {$test = $logPfx."Exist YES - $filename";} 
  else {$test = $logPfx."Exist NOT - $filename";} 
  putContentsLog($logPfx.$test,100);
  /*include $filename;*/
  require $filename;
  putContentsLog($logPfx."Included $filename",100);
  
  use Carbon\Carbon;
  //Carbon::setLocale('ru'); ??? Почему-то ОШИБКА ???
  //Carbon::setLocale('en');
  putContentsLog($logPfx."TEST:Carbon::now()->toDateTimeString() = ".Carbon::now()->toDateTimeString(),100);
  //$tomorrow = Carbon::now()->addDay();
  // These getters specifically return integers, ie intval()
  //$dt = Carbon::parse('2012-9-5 23:26:11.123789');
  
  function checkOrderInWorkTime() {
    $logPfxFunc = "EZ-TIME::checkOrderInWorkTime- ";
    $result = 'Все в порядке!!!';
    $dt = Carbon::now();
    putContentsLog($logPfxFunc."dt->year=".$dt->year,100);
    putContentsLog($logPfxFunc."dt->month=".$dt->month,100);
    putContentsLog($logPfxFunc."dt->day=".$dt->day,100);
    putContentsLog($logPfxFunc."dt->hour=".$dt->hour,100);
    putContentsLog($logPfxFunc."dt->minute=".$dt->minute,100);
    putContentsLog($logPfxFunc."dt->second=".$dt->second,100);

    putContentsLog($logPfxFunc."dt->dayOfWeek=".$dt->dayOfWeek,100);
    putContentsLog($logPfxFunc."dt->dayOfWeek=".$dt->dayOfWeek,100);
    putContentsLog($logPfxFunc."dt->weekOfMonth=".$dt->weekOfMonth,100);  
    putContentsLog($logPfxFunc."dt->weekOfYear=".$dt->weekOfYear,100);  
    putContentsLog($logPfxFunc."dt->daysInMonth=".$dt->daysInMonth,100);  

    if ($dt->dayOfWeek === Carbon::SUNDAY) {
      putContentsLog($logPfxFunc."Сегодня воскресенье - dayOfWeek=".$dt->dayOfWeek,100);
      $result_code = 'SUNDAY';
    } else {
      putContentsLog($logPfxFunc."Сегодня рабочий день - dayOfWeek=".$dt->dayOfWeek,100);
      $s_today        = $dt->toDateTimeString(); // 2018-02-02 01:10:50
      $s_today_dt     = $dt->toDateString();     // 1975-12-25
      $s_today_tm     = $dt->toTimeString();     // 10-00-00
      $s_today_w_beg  = $s_today_dt.' 10:00:00';
      $s_today_w_end  = $s_today_dt.' 17:00:00';
      $s_today_b_beg  = $s_today_dt.' 13:00:00';
      $s_today_b_end  = $s_today_dt.' 14:00:00';

      $t_today_w_beg  = Carbon::createFromFormat('Y-m-d H:i:s', $s_today_w_beg);
      $t_today_w_end  = Carbon::createFromFormat('Y-m-d H:i:s', $s_today_w_end);
      $t_today_b_beg  = Carbon::createFromFormat('Y-m-d H:i:s', $s_today_b_beg);
      $t_today_b_end  = Carbon::createFromFormat('Y-m-d H:i:s', $s_today_b_end);

      putContentsLog($logPfxFunc."Начало рабочего дня = ".$t_today_w_beg->toDateTimeString(),100);
      putContentsLog($logPfxFunc."Окончание рабочего дня = ".$t_today_w_end->toDateTimeString(),100);
      putContentsLog($logPfxFunc."Начало перерыва (break) = ".$t_today_b_beg->toDateTimeString(),100);
      putContentsLog($logPfxFunc."Окончание перерыва = ".$t_today_b_end->toDateTimeString(),100);

      $result_code = 'OK';
      if (Carbon::createFromFormat('Y-m-d H:i:s', $s_today)->between($t_today_w_beg, $t_today_w_end)) {
        putContentsLog($logPfxFunc."Заказ сделан ДА в рабочее время = ".$dt->toTimeString(),100);
        if (Carbon::createFromFormat('Y-m-d H:i:s', $s_today)->between($t_today_b_beg, $t_today_b_end)) {
            $result_code  = 'BREAK';
            putContentsLog($logPfxFunc."Заказ сделан в перерыв = ".$dt->toTimeString(),100);
        }
      } else {
        putContentsLog($logPfxFunc."Заказ сделан НЕ в рабочее время = ".$dt->toTimeString(),100);
        if ($dt > $t_today_w_end) {
          $result_code  = 'AFDAY';
          putContentsLog($logPfxFunc."Заказ сделан после окончания рабочего дня = ".$dt->toTimeString(),100);
        } else {
          $result_code  = 'BFDAY';
          putContentsLog($logPfxFunc."Заказ сделан перед началом рабочего дня = ".$dt->toTimeString(),100);
        }
      }  
    }  
    putContentsLog($logPfxFunc."Завершаем процедуру. Возвращаем значение result_code = ".$result_code,100);
    return $result_code; //OK,BREAK,AFDAY,BFDAY,SUNDAY
  }  
?>  
<?php
namespace core;
// заголовок css файла
header('Content-Type: text/css');
$arr=explode(',',$nav['filename']);
if(count($arr)>1){
  // формируем файл стилей
  $lf=false;// признак переноса строки
  foreach($arr as $val){
    $way=$nav['dirname'].'/'.$val.'.css';
    if(file_exists($way)){
      if($lf)echo CRLF;else $lf=true;
      echo @file_get_contents($way);
    }
  }
}else{
  define('TEST',$nav['dirname']==$nav['filename'] && $nav['filename']=='test'?true:false);
  // подключим
  Func('Lorry');
  $Page=new Structure(isset($_SESSION['prfx'])?$_SESSION['prfx']:'');//структуру страницы
  // вывод стилей
  echo $Page->Styles();
}
?>
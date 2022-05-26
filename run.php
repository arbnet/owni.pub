<?php //PHP_VERSION
//header("Cache-Control: public");
//header("Expires: " . date("r", time() + 3600));
// Константы
define('NAME','OWNI');define('CRLF',PHP_EOL);
define('ROOT',str_replace('/','\\',$_SERVER['DOCUMENT_ROOT']));
// запуск сессии и установка корня сайта
session_start();chdir(ROOT);
// установим автозагрузчик
spl_autoload_register(function($way){
  $way=explode('\\',$way);
  if($way[0]!='core')$way[]='Class';
  if($way[0]!='node')array_unshift($way,'node');
  $way=ROOT.'\\'.implode('\\',$way).'.php';
  @include $way;
});
// маршрутизация
$nav=pathinfo(trim($_SERVER['REDIRECT_URL'],'/'));
if(isset($nav['extension']))$way=$nav['extension'];
else $way=isset($_SESSION['ldt'])?'publish':'welcome';
// массив ошибок и счётчик SQL запросов
$err=[];$sql=0;
// перехват ошибок
set_error_handler(function($ern,$mes,$nfl,$lin){
  $ern=match($ern){E_USER_ERROR=>'U',E_USER_WARNING=>'W',E_USER_NOTICE=>'E',E_USER_DEPRECATED=>'D',default=>'X'};
  array_push($GLOBALS['err'],"$ern [$nfl:$lin] $mes");
});
// функция ошибки в коде
function code_error(string $msg,int $num=1){$bug=debug_backtrace()[$num];
  array_push($GLOBALS['err'],'C ['.$bug['file'].':'.$bug['line'].'] '.$msg);
}
// если разработчик то выведем ошибки
//if(isset($_SESSION['access']))in_array('developer',$_SESSION['access'])
  if(1==1){
    // при завершении выведем ошибки
    register_shutdown_function(function(){global $err,$sql,$nav;
      $rpt=NAME.' REPORT'.CRLF.
      'sql queries: '.$sql.CRLF.
      'memory usage: '.memory_get_peak_usage(true).CRLF.
      'generation time: '.number_format(microtime(true)-$_SERVER['REQUEST_TIME_FLOAT'],5,'.').CRLF.
      ($err?'errors:'.CRLF.implode(CRLF,$err).CRLF:'');
      echo CRLF.(isset($nav['extension'])?'/* '.$rpt.' */':'<!-- '.$rpt.'-->');
    });
  }
// формирование пути к файлу
function Path(...$arg){$res='';
  foreach($arg as $par){
    if(!is_array($par))$res.=$par.'\\';
    else{$arr=$par;
      foreach($arr as $par)$res.=$par.'\\';
    } 
  }
  return $res;
}
// подключение функций
function Func(...$arg){$res=[];
  foreach($arg as $par){
    if(!function_exists($par)){
      $way=Path(ROOT,'node','functions').$par.'.php';
      if(file_exists($way))@include $way;
      else{code_error('Нет функции: '.$par);continue;}
    }
    $res[]=$par;
  }
  return $res;
}
$way='node/'.$way.'.php';
if(file_exists($way))@require $way;
else header("HTTP/1.0 501 Not Implemented");
?>
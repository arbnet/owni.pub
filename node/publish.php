<?php
ini_set('display_errors',1);
ini_set('error_reporting',E_ALL);
ini_set('display_startup_errors',1);
if(!$_COOKIE){
  //ini_set('session.use_trans_sid',1);
  ini_set('session.use_only_cookies',0);
}
//header('Set-Cookie: cross-site-cookie=name; SameSite=None; Secure');
// установим кодировку
setlocale(LC_ALL,'ru_RU.UTF-8');
// константа режима тестирования
define('TEST',isset($_POST['test']));
// загрузим настройки конфигурации
$cnf=@parse_ini_file('node/core/config.ini',true);
if(!$cnf)exit(NAME.': Нет файла конфигурации');
// если тестирование
if(TEST){
  $nav['dirname']=$nav['filename']='test';$skin=$cnf['skin'];
}else{
  // определим параметры запроса
  if(!isset($nav['dirname']))$nav['dirname']='site';
  elseif($nav['dirname']=='.'){
    $nav['dirname']=$nav['filename'];$nav['filename']='';
  }
  if(!$nav['filename'])$nav['basename']=$nav['filename']='main';
  $nav['crumb']=explode('/',$nav['dirname']);
  if(count($nav['crumb'])==1)unset($nav['crumb']);
  else{$nav['dirname']=$nav['crumb'][0];unset($nav['crumb'][0]);}
  $way='mods/'.$nav['dirname'].'/'.$nav['filename'].'.xml';
  if(file_exists($way))unset($nav['basename']);
  elseif(file_exists('mods/'.$nav['dirname'].'/pages.xml'))
    $nav['filename']='pages';else $nav['filename']='nopage';
  // установим скин
  $skin=&$_SESSION['skin'];
  if(isset($_POST['skin']))$skin=$_POST['skin'];
  elseif(isset($_GET['skin']))$skin=$_GET['skin'];
  elseif(!$skin)if(isset($_COOKIE['skin']))$skin=$_COOKIE['skin'];
  if(!$skin)$skin=$cnf['skin'];
  elseif(!is_dir('skin/'.$skin))$skin=$cnf['skin'];
  if(isset($_COOKIE['skin'])?$_COOKIE['skin']:''!=$skin)
    setcookie('skin',$skin);
}
// запуск движка
Func('See','Lorry');
core\Engine::Loner();


/* $ToA=new ToArray;$arr=['aaa'=>1111,'bbb'=>2222,'ccc'=>3333,'ddd'=>4444,'eee'=>5555];
$res=$ToA->Hook($arr)->
//Move('ddd','bbb');
Insert(['fff'=>'FFFF'],'bbb');
$ToA->Delete('ccc',3);
print_r($arr); */
?>
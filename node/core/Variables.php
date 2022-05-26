<?php
// пространство имён ядра
namespace core;
// Класс Переменных структуры
class Variables {
  use singleton;
  public static array $vars;
  // Получение указателя переменной
  public function &__Get($key){
    $var=self::$vars[$key]??$this->Pointer($key);
    return $var;
  }
  // Установка значения переменной
  public function __Set($key,$val){
    $arr=explode('.',$key);
    if(count($arr)>1){$key=array_shift($arr);
      if($key!='cns'){
        $pvr=&$this->Pointer($key,$arr);
        if($pvr)$pvr=$val;
      }
    }else{
      if($val!='')self::$vars[$key]=$val;
      else unset(self::$vars[$key]);
    } 
  }
  // Добавление к переменной
  public function __Call(string $name,array $arg){
    if(count($arg)==1)if(is_array($arg[0]))$arg=$arg[0];
    foreach($arg as $key=>$val)
      if(is_int($key))self::$vars[$name][]=$val;
      else self::$vars[$name][$key]=$val;
  }
  // Существование переменной
  public function Has($key){
    return isset(self::$vars[$key]);
  }
  // Подмена переменных
  public function Spoof($txt){
    while(preg_match('/\{([^\}]+)\}/',$txt,$mth)){
      if(preg_match('/([^?]+)\?\??(\w+):?(\w+)?/',$mth[1],$trn)){
        $res=isset($trn[3])?$this->{$trn[1]}?$trn[2]:$trn[3]:
                            $this->{$trn[1]}??$trn[2];
      }else{
        $res=$this->{$mth[1]};if(is_array($res))$res=count($res);
      }
      $txt=str_replace($mth[0],$res,$txt);
    }
    return $txt;
  }
  // Получение указателя переменной
  public function &Pointer(string $key){
    $arr=explode('.',$key);$var=array_shift($arr);
    switch($var){
      case 'cns': $var=constant(array_shift($arr));break;
      case 'ses': $var=&$_SESSION;break;
      case 'coo': $var=&$_COOKIE;break;
      case 'srv': $var=&$_SERVER;break;
      case 'fls': $var=&$_FILES;break;
      case 'pst': $var=&$_POST;break;
      case 'get': $var=&$_GET;break;
      default: $var=&$GLOBALS[$var];
    }
    if($arr)foreach($arr as $key){
      if(isset($var[$key]))$var=&$var[$key];
      else{unset($var);$var=null;break;}
    }
    return $var;
  }
}
?>
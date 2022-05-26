<?php
// пространство имён ядра
namespace core;
// Класс Аргументов
class Arguments {
  use singleton;
  public static $args=[];
  // Получение значения аргумента
  public function __GET($key){
    return isset(self::$args[$key])?self::$args[$key]:'';
  }
  // Установка значения аргумента
  public function __SET($key,$val){
    if(!$val && isset(self::$args[$key]))unset(self::$args[$key]);
    else{
      if($key=='class'){
        $arr=isset(self::$args['class'])?explode(' ',self::$args['class']):[];
        $val=implode(' ',array_unique(array_merge($arr,explode(' ',$val))));
      }else{
        if($val[0]=='@'){$key='--'.$key;$val[0]=':';}
        if($val[0]==':'){
          if(isset(self::$args['style'])){
            preg_match('/'.$key.'\s?:\s?[^;]+/',self::$args['style'],$mth);
            if($mth)$val=str_replace($mth[0],$key.$val,self::$args['style']);
            else $val=self::$args['style'].$key.$val.';';
          }else $val=$key.$val.';';
          $key='style';
        }
      }
      self::$args[$key]=$val;
    }
  }
  // Проверка установки аргумента
  public function Has($key){return isset(self::$args[$key]);}
  // Получение количества аргументов
  public function Count(){return count(self::$args);}
  // Взять аргумент
  public function Take(string $key){
    if(isset(self::$args[$key])){
      $val=self::$args[$key];unset(self::$args[$key]);
    }else $val=null;
    return $val;
  }
  // Аргументы в атрибуты
  public function InAttr($att=''){
    if($att){preg_match_all('/(\w+)="([^"]*)"/',$att,$mth);
      foreach($mth[1] as $num=>$key){$par=$this->Take($key);
        if($par){if($key=='class')$par=' '.$par;
          $att=str_replace($mth[0][$num],$key.'="'.$mth[2][$num].$par.'"',$att);
        }
      }
    }
    foreach(self::$args as $key=>$val){
      $att.=' '.$key;if($val)$att.='="'.$val.'"';
    }
    self::$args=[];return $att;
  }
}
?>
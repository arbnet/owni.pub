<?php
// пространство имён движка
namespace core;
// Класс Объектов
class Objects {
  public $Obj=[];
  // Получение объекта
  public function &__Get($key){
    if($this->Has($key))$res=&$this->Obj[$key];else $res=null;
    return $res;
  }
  // Установка\Удаление объекта
  public function __Set($key,$val){
    if(!is_null($val))$this->Obj[$key]=$val;
    else if($this->Has($key))unset($this->Obj[$key]);
  }
  // Наличие объекта
  public function Has($key){
    return isset($this->Obj[$key]);
  }
}
?>
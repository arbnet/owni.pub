<?php
// пространство имён ядра
namespace core;
// Класс Файлов
class Files {
  use singleton;
  private static $fils=[];
  // Конструктор
  private function __Construct(){Func('WayInfo');}
  // Получение объекта файла
  public function &__Get($key){
    $res=self::$fils[$key]??null;return $res;
  }
  // Установка объекта файла
  public function __Set(string $key,string $val){
    $val=WayInfo($val);
    $class='core\File'.ucfirst($val['extension']);
    if(!file_exists('node\\'.$class.'.php'))$class='core\FileText';
    self::$fils[$key]=new $class($val);
  }
  // Существование объекта файла
  public function Has($key){return isset(self::$fils[$key]);}
}
?>
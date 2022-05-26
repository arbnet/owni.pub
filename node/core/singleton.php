<?php
namespace core;
// Один экземпляр
trait singleton{
  private static $Inst;
  private function __clone(){}
  public static function Loner(...$arg){
    if(self::$Inst===null)self::$Inst=new self($arg);
    return self::$Inst;
  }
}
?>
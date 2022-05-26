<?php
// пространство имён движка
namespace core;
// Класс Яузел
abstract class Inode {
  public array $flags;
  public $Vars,$Args,$Nods,$Fils;
  const KC=['Vars'=>'Variables','Args'=>'Arguments','Nods'=>'Nodes','Fils'=>'Files'];
  // Получение результата выполнения PHP файла
  final public function __Call(string $key,array $par){
    $way=get_class($this).'\\'.$key.'.php';
    if(file_exists($way)){$par=$par[0];
      ob_start();@include $way;return ob_get_clean();
    }else code_error('Нет файла: '.$way);
  }
  // Инициализация
  final protected function Init(...$arg){
    foreach($arg as $key)
      if(isset(self::KC[$key]))$this->$key=Lorry(self::KC[$key]);
  }
  // Получение флага
  final protected function Flag(int $num=0,bool $sgn=false){
    //see($num,array_slice($this->flags,$num,1));
    $res=array_slice($this->flags,$num,1)[0];
    if($sgn)$res=$res=='true'?true:false;
    return $res;
  }
  // Получение инструкции
  public function Receive(string $tag,mixed $rec){
    code_error('В классе '.get_called_class().' нет метода Receive');
  }
}
?>
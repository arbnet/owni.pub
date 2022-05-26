<?php
// пространство имён ядра
namespace core;
// Класс Файл
abstract class File {
  protected $mdf=false;
  protected array $info;
  // Конструктор
  public function __Construct(array $info){
    $this->info=$info;return $this->Load();
  }
  // Получение свойств файла
  final public function __Get(string $key){
    if(count($arr=explode(',',$key))>1){$arr=array_unique($arr);
      if(($key=array_search('',$arr))!==false){
        $res=$this->info;unset($arr[$key]);
      }else $res=[];
      foreach($arr as $key)
        if($val=$this->info[$key]??$this->FileInfo($key))$res[$key]=$val;
    }else $res=$key?$this->info[$key]??null:$this->info;
    return $res;
  }
  // Установка свойства файла
  final public function __Set(string $key,mixed $val){
    $this->info[$key]=$val;
  }
  private function FileInfo(string $key){
    switch($key){
      case 'size': $res=filesize(ROOT.'/'.$this->info[$wey]);
    }
    if(isset($res)){$this->info[$key]=$res;return $res;}
  }
  // Загрузка файла
  abstract protected function Load();
  // Получение данных
  abstract protected function Data(string $key);
  // Редактирование данных
  abstract protected function Edit(string $key,mixed $val);
  // Модификация файла
  final protected function Modify(?bool $mdf=null){
    if(!is_bool($mdf))return $this->mdf;
    else if($this->mdf!=$mdf)$this->mdf=$mdf;
  }
  // Функция завершения работы
  abstract protected function __Destruct();
}
?>
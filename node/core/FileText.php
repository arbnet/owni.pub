<?php
// пространство имён ядра
namespace core;
// Класс работы с текстовым файлом
class FileText extends File {
  private $txt;
  // Загрузка файла
  protected function Load(){
    $way=ROOT.'/'.$this->info['way'];
    if(is_readable($way)){
      $this->txt=file_get_contents($way);
    }else if(!file_exists($way))$this->txt='';
    else return false;
  }
  // Редактирование
  public function Edit($arg,$rec){
    $arg=$arg->pattern;
    preg_match('/'.$arg.'/',$this->txt,$mth);
    if($mth){$mth=$mth[isset($mth[1])?1:0];
      $this->txt=str_replace($mth,$rec,$this->txt);
      $this->Modify(true);
    }
  }
  // Получение данных для узла Disc
  public function Data($par){
    return match($par){''=>$this->txt,default=>$this->$par};
  }
  // Функция завершения работы
  public function __Destruct(){
    if($this->mdf){}
      file_put_contents(ROOT.'/'.$this->info['way'],$this->txt);
  }
}
?>
<?php
// пространство имён ядра
namespace core;
// Класс работы с Ini файлом
class FileIni extends File {
  private $ini;
  // Загрузка файла
  protected function Load(){
    $way=ROOT.'/'.$this->info['way'];
    if(is_readable($way))
      $this->ini=parse_ini_file($way,true);
    else if(!file_exists($way))$this->ini=[];
    else return false;
  }
  // Секции
  public function Sections(){$res=[];
    foreach($this->ini as $key=>$val)
      if(is_array($val))$res[]=$key;
    return $res;
  }
  // Ключи
  public function Keys(string $sec=''){
    if($sec)$res=isset($this->ini[$sec])?
      array_keys($this->ini[$sec]):[];
    else{$res=[];
      foreach($this->ini as $key=>$val)
        if(!is_array($val))$res[]=$key;
    }
    return $res;
  }
  // Получение данных
  public function Data(string $key){
    return match($key){
      '.'=>$this->Sections(),
      '/'=>array_filter($this->ini,function($val){
        return !is_array($val);
      }),
      default=>$this->Pointer($key)
    };
  }
  // Редактирование данных
  public function Edit(string $key,mixed $val){
    $ini=&$this->Pointer($key);
    if($ini!=$val){
      if($val)$ini=$val;else unset($ini);
      $this->Modify(true);
    }
  }
  // Указатель на данные
  private function &Pointer(string $key){
    $res=&$this->ini;
    if($key){$arr=explode(strpos($key,'.')?'.':'_',$key);
      foreach($arr as $key)
        if($key){
          if(isset($res[$key]))$res=&$res[$key];
          else{unset($res);$res=null;break;}
        }else{$res=array_keys($res);break;}
    }
    return $res;
  }
  // Функция завершения работы
  public function __Destruct(){
    if($this->mdf){$ini='';
      foreach($this->ini as $key=>$val)
        if(is_array($val)){$ini.='['.$key.']'.CRLF;
          foreach($val as $key=>$vls)$ini.=$key.'="'.$vls.'"'.CRLF;
        }else $ini.=$key.'="'.$val.'"'.CRLF;
      file_put_contents(ROOT.'/'.$this->info['way'],rtrim($ini));
    }
  }
}
?>
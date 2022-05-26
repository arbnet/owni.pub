<?php
// пространство имён ядра
namespace core;
// Класс работы с Json файлом
class FileJson extends File {
  private $json;
  // Загрузка файла
  protected function Load(){
    $way=ROOT.'/'.$this->info['way'];
    if(is_readable($way))
      $this->json=json_decode(file_get_contents($way),true);
    else if(!file_exists($way))$this->json=[];
    else return false;
  }
  // Проверка существования
  public function Has(string $key=''){
    $val=$this->Pointer($key);
    return is_null($val)?false:true;
  }
  // Количество элементов
  public function Count(string $key=''){
    $val=$this->Pointer($key);
    return $val?count($val):0;
  }
  // Получение данных
  public function Data(string $key=''){
    if($key[0]=='~'){
      preg_match('/(\w+):?(.*)?/',substr($key,1),$mth);
      if($mth){
        $res=match($mth[1]){
          'has'=>$this->Has($mth[2]),
          'count'=>$this->Count($mth[2])
        };
      }
    }else $res=$this->Pointer($key);
    return $res;
  }
  // Редактирование данных
  public function Edit(string $key,mixed $val){
    $ini=&$this->Pointer($key);
    if($ini!=$val){
      if($val)$ini=$val;else unset($ini);
      $this->Modify(true);
    }
    /* if($this->Data($key)!=$val){
      $arr=explode('.',$key);$key=array_pop($arr);
      $json=$this->Value($arr);
      if(is_null($val))unset($json[$key]);
      else $json[$key]=$val;
      $this->Modify(true);
    } */
  }
  // Указатель на значение
  /* private function Value(array $arr){
    $json=&$this->json;$res=true;
    foreach($arr as $key)
      if(isset($json[$key]))$json=&$json[$key];else{$res=false;break;}
    return $res?$json:null;
  } */
  // Указатель на данные
  private function &Pointer(string $key){
    $json=&$this->json;
    if($key){
      $arr=explode('.',$key);$key=array_pop($arr);
      foreach($arr as $kvl)
        if(isset($json[$kvl]))$json=&$json[$kvl];
        else{unset($json);$key=$json=null;break;}
      if($key)$json=&$json[$key];
    }
    return $json;
  }
  // Функция завершения работы
  public function __Destruct(){
    if($this->mdf)
      file_put_contents(ROOT.'/'.$this->info['way'],json_encode($this->json));
  }
}
?>
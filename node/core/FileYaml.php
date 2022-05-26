<?php
// пространство имён ядра
namespace core;
// Класс работы с Yaml файлом
class FileYaml extends File {
  /*
  $num - номер документа
  $break - YAML_CRLN_BREAK
  $encoding - YAML_UTF8_ENCODING
  $func - callback функции
  */
  private $yaml,$cnt,$cur;
  public static $num=-1,$break=3,$encoding=1,$func=[];
  // Загрузка файла
  protected function Load(){
    $way=ROOT.'/'.$this->info['way'];
    if(is_readable($way)){
      $this->yaml=yaml_parse_file($way,self::$num,$this->cnt,self::$func);
      if(self::$num==-1){
        if(!$this->cnt)$this->cnt=count($this->yaml);
        if($this->cnt>1)$this->cur=0;
        else $this->yaml=$this->yaml[0];
      }
    }else if(!file_exists($way))$this->yaml=[];
    else return false;
  }
  // Количество документов
  public function Count(){return $this->cnt;}
  // Номер документа
  public function Number($num=null){
    if($num){
      if($this->cnt>1){$num=intval($num);
        if($num<$this->cnt)$this->cur=$num;
      }
    }else return $this->cur;
  }
  // Получение данных
  public function Data(string $key){
    if($key[0]=='~'){
      preg_match('/(\w+):?(.*)?/',substr($key,1),$mth);
      $res=match($mth[1]){
        'count'=>$this->cnt,
        'number'=>$this->cur
      };
    }else $res=$this->Value($key?explode('.',$key):[]);
    return $res;
  }
  // Редактирование данных
  public function Edit(string $key,mixed $val){
    if($this->Data($key)!=$val){
      $arr=explode('.',$key);$key=array_pop($arr);
      $yaml=$this->Value($arr);
      if(is_null($val))unset($yaml[$key]);
      else $yaml[$key]=$val;
      $this->Modify(true);
    }
  }
  private function Value(array $arr){
    $yaml=&$this->yaml;$res=true;
    if($this->cnt>1)$yaml=&$yaml[$this->Number()];
    foreach($arr as $key)
      if(isset($yaml[$key]))$yaml=&$yaml[$key];else{$res=false;break;}
    return $res?$yaml:null;
  }
  // Функция завершения работы
  public function __Destruct(){
    if($this->mdf){
      $way=ROOT.'/'.$this->info['way'];
      if($this->cnt>1){$yaml='';
        foreach($this->yaml as $val)
          $yaml.=yaml_emit($val,self::$encoding,self::$break,self::$func);
        file_put_contents($way,$yaml);
      }else yaml_emit_file($way,$this->yaml,self::$encoding,self::$break,self::$func);
    }
  }
}
?>
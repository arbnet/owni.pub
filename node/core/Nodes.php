<?php
// пространство имён движка
namespace core;
// Класс Узлы
class Nodes {
  use singleton;
  private $Nods,$Vars,$Args,$Cnts;
  // Конструктор
  public function __Construct(){
    $this->Nods=new Objects();
    $this->Vars=Lorry('Variables');
    $this->Args=Lorry('Arguments');
    $this->Cnts=[];
  }
  // Указатель на класс узла
  final public function &__Get($name){
    $name=strtolower($name);
    $node=$this->Nods->$name;
    if(!$node){$way='node\\'.$name;
      if(file_exists($way.'\Class.php'))
        $node=$this->Nods->$name=new $way;
    }
    return $node;
  }
  // Получение макета
  public function Lay(string $node,string $lay){
    $way='node/'.$node.'/'.$lay.'.htm';
    if(file_exists($way)){$htm=@file_get_contents($way);
      // извлечение скрипта
      while(preg_match('/<script([^>]+)?>(.*)?<\/script>/is',$htm,$mth)){
        $htm=str_replace($mth[0],'',$htm);
        if($mth[2])$this->Vars->Script(trim($mth[2]));
        if($mth[1])if(preg_match("/load=\"(.*)\"/",$mth[1],$mth))
        $this->Vars->LoadJs(explode(',',$mth[1]));
      }
      if($node!='frame'){
        $id=$this->Args->Take('id')??$this->CountId($node.'_'.$lay);
        preg_match('/<(\w+\s)([^>]*)?>/',$htm,$mth);
        if($mth){$mth[1].='id="'.$id.'" ';
          $htm=str_replace($mth[0],'<'.$mth[1].$this->Args->InAttr($mth[2]).'>',$htm);
        }
      }
    }else $htm='';
    return $htm;
  }
  private function CountId(string $knl){
    if(isset($this->Cnts[$knl])){
      $this->Cnts[$knl]++;$cid=$this->Cnts[$knl];
    }else $cid=$this->Cnts[$knl]=1;
    return $knl.$cid;
  }
}
?>
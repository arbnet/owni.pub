<?php
// пространство имён ядра
namespace core;
// Класс работы с css файлом
class FileCss extends File {
  private array $css;
  private array $ref;
  // Загрузка файла
  protected function Load(){Func('Tab');
    $way=ROOT.'/'.$this->info['way'];
    if(is_readable($way)){
      $css=file_get_contents($way);
      preg_match_all('/^([^\/{]+)\{([^}]+)\}/',$css,$mth,PREG_SET_ORDER);
      foreach($mth as $tar){
        $tar[1]=trim($tar[1]);$this->css[$tar[1]]=$tar[2];
        if(count($arr=explode(',',$tar[1]))>1){
          foreach($arr as $val)$this->ref[$val]=&$this->css[$tar[1]];
        }else $this->ref[$tar[1]]=&$this->css[$tar[1]];
      }
    }else if(file_exists($way))return false;
  }
  // Селекторы
  public function Selectors(){
    return array_keys($this->ref);
  }
  // Свойства
  public function Propertys(string $key){
    $res=$this->css[$key]??$this->ref[$key]??null;
    if($res){
      preg_match_all('/^\s?([^:*]+)\s?:/im',$res,$mth);
      if($mth){$res=[];
        foreach($mth[1] as $val)$res[]=trim($val);
        return $res;
      }
    }
  }
  // Получение данных
  public function Data(string $key=''){
    if($key=='.')$res=$this->Selectors();
    else if($key){$arr=explode('.',$key);$key=array_shift($arr);
      $res=$this->ref[$key]??null;
      if($res)if(($key=array_shift($arr))!==null){
        preg_match_all('/^\s?(\S+)\s*:\s*(.*)$/im',$res,$mth);
        //see($mth);
        switch($key){
          case '': $res=$mth[1];
            /* preg_match_all('/^\s?(\S+)\s?:/im',$res,$mth);
            if($mth){$res=[];
              foreach($mth[1] as $val)$res[]=trim($val);
            } */
            break;
          case '*': $res=[];
            $ptr=array_pop($arr)=='cmt'?'/\/\*(.*)\*\//':'/([^;]+)/';
            foreach($mth[2] as $key=>$val){
              preg_match($ptr,$val,$val);
              if($val)$res[$mth[1][$key]]=trim($val[1]);
            }
            break;
          default:
            $key=array_search($key,$mth[1]);
            if($key){
              $ptr=array_pop($arr)=='cmt'?'/\/\*(.*)\*\//':'/([^;]+)/';
              preg_match($ptr,$mth[2][$key],$mth);
              $res=trim($mth[1]);
            }else $res=null;
        }
      }
    }else $res=$this->css;
    return $res;
  }
  // Редактирование
  public function Edit(string $key,mixed $val){
    $arr=explode('.',$key);$key=array_shift($arr);
    $res=&$this->css[$key]??$this->ref[$key]??null;
    if($res)if($key=array_shift($arr)){
      preg_match('/'.$key.'\s?:([^;]+);(.*)/',$res,$mth,PREG_OFFSET_CAPTURE);
      if($mth){
        if(array_pop($arr)=='cmt')
          $res=substr_replace($res,'/* '.$val.' */',$mth[2][1],strlen($mth[2][0]));
        else $res=substr_replace($res,' '.$val,$mth[1][1],strlen($mth[1][0]));
      }
    }else $res=CRLF.Tab($val).CRLF;
    $this->Modify(true);
  }
  // Функция завершения работы
  public function __Destruct(){
    if($this->mdf){$css='';
      foreach($this->css as $key=>$val)
        $css.=$key.' {'.$val.'}'.CRLF;
      see(rtrim($css));
      //file_put_contents(ROOT.'/'.$this->info['way'],rtrim($css));
    }
  }
}
?>
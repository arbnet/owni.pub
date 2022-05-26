<?php
// пространство имён узлов
namespace node;
use core\Inode;
// Класс узла Диск
class Disc extends Inode {
  private $Dirs;
  public $pfld,$path,$nick;
  // Конструктор
  public function __Construct(){
    parent::Init('Vars','Args','Fils');Func('DirScan','NameBase');
    $this->Dirs=DirScan('.');$this->pfld=&$this->Dirs;$this->path=$this->nick='';
  }
  // Существование
  public function Exist(string $par){
    if($par){
      $par=file_exists($this->Vars->Spoof($par));
      if($key=$this->Flag())$this->Vars->$key=$par;
      else return $par;
    }
  }
  // Ник файла
  public function Nick(string $par=''){
    if($par){
      if($this->Fils->Has($par))$this->nick=$par;
    }else return $this->nick;
  }
  // Сканирование
  public function Scan(string $dir=''){
    if($dir){
      $sgn=$this->Flag(0,true);
      $this->pfld=&$this->Dirs;
      $dir=$this->Vars->Spoof($dir);
      $dir=explode('/',$dir);$this->path='';
      foreach($dir as $val){$this->path.=$val;
        if(array_key_exists($val,$this->pfld['folders'])){
          $this->pfld=&$this->pfld['folders'][$val];
          if(is_null($this->pfld))$this->pfld=DirScan($this->path,$sgn);
        }else break;
        $this->path.='/';
      }
    }else return $this->path?$this->path:'root';
  }
  // Данные директорий
  public function Folders(string $par=''){
    $res=array_keys($this->pfld['folders']);
    if($par){
      $res=array_filter($res,function($val)use($par){
        return strpos($val,$par)!==false?true:false;
      });
    }
    if($flg=$this->Flag())$res=match($flg){
      'size'=>$this->SizeFolders($res),
      'count'=>count($res)
    };
    return $res;
  }
  // Данные файлов
  public function Files(string $par=''){
    $res=$this->pfld['files'];
    if($par){
      if($par[0]!='!')$sgn=true;
      else{$sgn=false;$par=substr($par,1);}
      $par=explode(',',$par);
      $res=array_filter($res,function($key)use($par,$sgn){
        $res=in_array($key,$par);return $sgn?$res:!$res;
      },ARRAY_FILTER_USE_KEY);
    }
    if($flg=$this->Flag())$res=match($flg){
      'size'=>$this->SizeFiles($res),
      'count'=>$this->CountFiles($res)
    };
    return $res;
  }
  // Получение данных текущей директории
  public function Out(string $par=''){
    $res=$this->pfld;
    if($par){$par=explode('.',$par);
      foreach($par as $key)
        if(isset($res[$key]))$res=$res[$key];else{$res=null;break;}
    }
    return $res;
  }
  // Загрузка файла
  public function Load(string $way){
    $way=$this->Vars->Spoof($way);
    if($way)if(is_readable($way)){$key=$this->Flag();
      if(!$key)$key=NameBase($way);
      $this->Fils->$key=$way;$this->nick=$key;
    }
  }
  // Получение информации о файле
  public function Info(string $par=''){
    $key=$this->Flag();if(!$key)$key=$this->nick;
    if($this->Fils->Has($key))
      return $this->Fils->$key->$par;
  }
  // Данные файла
  public function Data(string $par){
    $par=$this->Vars->Spoof($par);
    $key=$this->Flag();if(!$key)$key=$this->nick;
    if($this->Fils->Has($key)){
      $res=$this->Fils->$key->Data($par);
      if(($flg=$this->Flag(-1))!=null){
        if(preg_match('/(\d+)-?(-?\d+)?/',$flg,$mth))
          $res=array_slice($res,$mth[1],$mth[2]);
      }
      return $res;
    }
  }
  // Редактирование файла
  public function Edit(string $par){
    $key=$this->Flag();if(!$key)$key=$this->nick;
    if($Ofl=$this->Fils->$key){$ved=$this->Vars->$par;
      if(!is_array($ved))$Ofl->Edit($par,$ved);
      else foreach($ved as $key=>$val)$Ofl->Edit($key,$val);
    }
  }
  // Получение инструкции
  public function Receive($tag,$rec){
    if($key=$this->nick)if($this->Fils->Has($key))
      $this->Fils->$key->Edit($tag,$rec);
  }
  // В разрешение
  public function InExt(string $par){
    [$etp,$par]=explode('=',$par);
    if($etp && $par){$etp=explode(',',$etp);
      $arr=&$this->pfld['fls'];$exf=array_shift($etp);
      if(isset($arr[$exf])){$arr[$par]=$arr[$exf];
        foreach($etp as $val)if(isset($arr[$val])){
          $tar=array_intersect($arr[$par],$arr[$val]);
          if($tar)$arr[$par]=array_merge($arr[$par],$tar);
          $arr[$val]=array_diff($arr[$val],$tar);
          if(!count($arr[$val]))unset($arr[$val]);
        }
        $arr[$par]=array_unique($arr[$par]);
        $arr[$exf]=array_diff($arr[$exf],$arr[$par]);
        if(!count($arr[$exf]))unset($arr[$exf]);
        asort($arr);
      }
    }
  }
  // Размер директорий
  private function SizeFolders(array $arr){$res=0;
    foreach($arr as $val){$val=$this->path.$val;see($val);
      $res+=is_dir($val)?$this->DirSize($val):filesize($val);
    }
    return $res;
  }
  // Подсчёт размера
  private function DirSize(string $dir){$res=0;
    if($odr=@opendir($dir)){
      while($dfp=readdir($odr))
        if($dfp!='.' && $dfp!='..'){$pth=$dir.'/'.$dfp;
          $res+=is_dir($pth)?$this->DirSize($pth):filesize($pth);
        }
      closedir($odr);  
    }
    return $res;
  }
  // Размер файлов
  private function SizeFiles(array $arr){$res=0;
    foreach($arr as $ext=>$tar)
      foreach($tar as $val)$res+=filesize($this->path.$val.'.'.$ext);
    return $res;
  }
  // Подсчёт файлов
  private function CountFiles(array $arr){$res=0;
    foreach($arr as $ext=>$tar)$res+=count($tar);
    return $res;
  }
}
?>
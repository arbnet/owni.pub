<?php
// пространство имён движка
namespace core;
// Класс Структуры
class Structure {
  // $prfx- перефикс включения _xml
  // $xmls- структуры страниц
  // $name- имя структуры
  private $Node,$Vars,$xmls,$prfx,$name;
  // Конструктор
  public function __Construct($prfx=''){
    $this->prfx=$prfx;
    $this->Node=Lorry('Nodes');
    $this->Vars=Lorry('Variables');
    if(TEST){
      $xml=simplexml_load_string($_SESSION['test']);
      if($xml->getName()==NAME){
        $this->name='test';$this->xmls['test']=$xml;
      }else trigger_error('Содержание test не структура '.NAME);
    }else{
      $this->name=$GLOBALS['nav']['filename'];
      $this->LoadXML($this->name);$this->AddXML($this->name);
    } 
  }
  // Получение ответа структуры
  public function Answer(string $name=''){
    if(!$name)$name=$this->name;
    return $this->xmls[$name]->answer;
  }
  // Получение аргументов структуры
  public function Arguments(){$arr=[];
    foreach($this->xmls as $xml)
      foreach($xml->attributes() as $key=>$val)
        $arr[$key]=$val->__toString();
    return $arr;
  }
  // Получение списка стилей
  public function Styles(){$arr=[];
    foreach($this->xmls as $xml){
      if($xml->style){
        $val=$xml->style->attributes()['css'];
        if($val){
          preg_match_all('/\w+\/?(\w+)?/',$val,$tar);
          foreach($tar[1] as $key=>$val)
            if(!$val)unset($tar[1][$key]);
            else {$tar[1][$key]=$tar[0][$key];unset($tar[0][$key]);}
          if(count($tar[0])){
            if(!isset($arr['skin']))$arr['skin']=$tar[0];
            else $arr['skin']=array_merge($arr['skin'],$tar[0]);
          }
          if(count($tar[1])){
            if(!isset($arr['node']))$arr['node']=$tar[1];
            else $arr['node']=array_merge($arr['node'],$tar[1]);
          }
        }
        $val=trim($xml->style->__toString());
        if($val){$val=preg_replace('/\s*?\/\*.*\*\//','',$val);
          if(!isset($arr['rule']))$arr['rule']=[];
          $arr['rule'][]=$val;
        }
      }
      if($xml->fonts){$val=trim($xml->fonts->__toString());
        if($val){$val=explode(',',$val);
          if(!isset($arr['font']))$arr['font']=$val;
          else $arr['font']=array_merge($arr['font'],$val);
        }
      }
    }
    $arg=$this->ArgXpt('.//@*[substring(local-name(),string-length(local-name()))="."]');
    if(count($arg)>0){$tar=[];
      foreach($arg as $key=>$val)$tar[explode('.',$key)[0]]=$val;
      if(isset($arr['node'])){
        foreach($arr['node'] as $val){[$key,$val]=explode('/',$val);
          if(isset($tar[$key]))if(in_array($val,$tar[$key]))$val='';
          if($val)$tar[$key][]=$val;
        }
      }
      $arr['node']=$tar;
    }
    if(isset($arr['font']))$arr['skin']=array_unique(array_merge(['values'],$arr['skin']),SORT_REGULAR);
    if(isset($arr['font']))$arr['font']=array_unique($arr['font'],SORT_STRING);
    return (new Styles($arr))->css;
  }
  // Получение меток, шаблонов, скрипта
  public function LTS(){$res=[];
    foreach($this->xmls as $xml){
      if($xml->template)foreach($xml->template as $tpl)
        foreach($tpl->children() as $xch){
          $val=$xch->attributes()['way'];//???
          if($val){$val=explode('/',$val);
            $val=$this->Node->Lay($val[0],$val[1]);
          }else $val=trim($xch->__toString());
          if($val)$this->Vars->Template($xch->getName(),$val);
          //$res['tmpl'][$xch->getName()]=$val;
        }
      if($xml->script){
        $val=$xml->script->attributes()->load;
        if($val)$this->Vars->LoadJs($val);
        //$val=preg_replace('/\s*?\/\*.*\*\//','',$val);
        if($val=$xml->script)
          $this->Vars->Script(trim($val->__toString()));
      }
      if($xml->labels){$val=trim($xml->labels->__toString());
        if($val){$val=explode(',',$val);
          $res['lbls']=isset($res['lbls'])?
            array_merge($res['lbls'],$val):$val;
        }
      }
    }
    if(isset($res['lbls']))
      $res['lbls']=array_unique($res['lbls'],SORT_STRING);
    if($this->Vars->Has('Template')){
      foreach($this->Vars->Template as $key=>$val)
        $res['tmpl'].='<template id="'.$key.'">'.$val.'</template>'.CRLF;
    }
    if($this->Vars->Has('Script')){
      $res['code']='<script defer>
  myjs.then(()=>{
    '.implode(CRLF,$this->Vars->Script).'
  })
</script>'.CRLF;      
      if($this->Vars->Has('LoadJs')){
        $val=implode(',',array_unique($this->Vars->LoadJs,SORT_STRING));
        if($val)$val=' data-load="'.$val.'"';
      }else $val='';
      $res['load']='<script src="/node/script/Boot.js"'.$val.'></script>'.CRLF;
    }
    return $res;
  }
  // Подключение структуры
  // $name - имя файла xml
  private function LoadXML($name){
    $way=Path('mods',$GLOBALS['nav']['dirname']).$name.'.xml';
    if(!file_exists($way)){
      trigger_error('Нет файла: '.$way);
      $way='mods\\'.$name.'.xml';
      if(!file_exists($way)){
        trigger_error('Нет файла: '.$way);$way=false;
      }
    }
    if($way){$xml=simplexml_load_file($way);
      if($xml){
        if($xml->getName()==NAME)$this->xmls[$name]=$xml;
        else{
          array_push($GLOBALS['err'],'M ['.ROOT.'\\'.$way.'] Не инструкция '.NAME);
          echo 'Ошибка '.NAME.' инструкции';exit;
        }
      }else{$err=libxml_get_errors()[0];
        array_push($GLOBALS['err'],'M ['.ROOT.'\\'.
        str_replace('/','\\',$err->file).':'.$err->line.'] '.trim($err->message).', column '.$err->column);
        echo 'Ошибка '.NAME.' инструкции';exit;
      } 
    }else{
      echo 'Нет файла '.NAME.' инструкции';exit;
    }
  }
  // Добавление вложенных структур
  private function AddXML($name){
    $xel=$this->xmls[$name]->xpath('.//*[@_xml]');
    foreach($xel as $elt){
      if($this->prfx){$nse=$elt->getNamespaces();
        if($nse){$nse=array_keys($nse);
          foreach($nse as $val){
            if(in_array($val.':'.$elt->getName(),$this->prfx)){
              $val=$elt->attributes()['_xml']->__toString();
              $this->LoadXML($val);
            }
          }
          continue;
        }
      }
      $val=$elt->attributes()['_xml']->__toString();
      $this->LoadXML($val);$this->AddXML($val);
    }
  }
	// Получение значений атрибутов
  // $xpt - запрос xpath
  private function ArgXpt($xpt){$arr=[];
    foreach($this->xmls as $xml){$xel=$xml->xpath($xpt);
      foreach($xel as $arg)
        foreach($arg as $key=>$val){$val=$val->__toString();
          if(isset($arr[$key]))
            if(array_search($val,$arr[$key])!==false)continue;
          $arr[$key][]=$val;
        }
    }
    return $arr;
  }
}
?>
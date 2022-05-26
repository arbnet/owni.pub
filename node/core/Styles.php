<?php
// пространство имён движка
namespace core;
// Класс импортирования Стилей
class Styles {
  public $css;// сформированные стили
  private $ast;// массив стилей структуры
  // Конструктор
  // $ast - массив стилей
  public function __Construct($ast){
    $this->ast=$ast;$this->css='';
    $this->Skin();// импорт стилей скина
    $this->Node();// импорт стилей узлов
    $this->Rule();// прописанные правила
    $this->Font();// импорт шрифтов
  }
  // Импорт базовых стилей и скина
  private function Skin(){
    if(isset($this->ast['skin'])){
      $skin=is_dir('skin/'.$_SESSION['skin'])?$_SESSION['skin']:false;
      foreach($this->ast['skin'] as $val){
        if($skin){$way='skin/'.$skin.'/'.$val.'.css';
          if(file_exists($way))$val="$skin/$val";
        }
        $this->css.='@import "/skin/'.$val.'.css";'.CRLF;
      } 
    }
  }
  // Импорт стилей узлов
  private function Node(){
    if(isset($this->ast['node']))
      foreach($this->ast['node'] as $key=>$arr)
        foreach($arr as $val){$way='node/'.$key.'/'.$val.'.css';
          if(file_exists($way))$this->css.='@import "/'.$way.'";'.CRLF;
        }
  }
  // Импорт шрифтов
  private function Font(){
    if(isset($this->ast['font'])){
      foreach($this->ast['font'] as $val){
        $arr=explode(';',preg_replace('/\s+/','',$val));
        foreach($arr as $val){
          $way='node/font/'.$val.'.woff';
          if(file_exists($way)){
            $this->css.="
@font-face {
  src:url('/$way') format('woff');
  font-display:block; font-family:'$val';
}
.font-$val {font-family:'$val'}";
          }else $this->css.='/* Нет шрифта '.$way.' */'.CRLF;
        }
      }
    }
  }
  // Прописанные правила
  private function Rule(){
    if(isset($this->ast['rule']))
      $this->css.=implode(CRLF,$this->ast['rule']).CRLF;
  }
}
?>
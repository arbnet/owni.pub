<?php
// пространство имён ядра
namespace core;
// Движок
class Engine { 
  use singleton;
  private $Stack,$Nods,$Vars,$Args,$Page;
  // Конструктор
  private function __Construct(){
    libxml_use_internal_errors(true);
    Func('Render');// функция Рендер
    $this->Stack=new Stack;// стэк
    // доступ к
    $this->Nods=Lorry('Nodes');// узлам
    $this->Vars=Lorry('Variables');// переменным
    $this->Args=Lorry('Arguments');// аргументам
    //$this->Vars->ses['prfx']=[];
    $_SESSION['prfx']=[];// сброс перефиксов
    // берём xml структуру страницы
    $this->Page=new Structure;
    $xml=$this->Page->Answer();
    // формирование страницы
    if($xml){
      // аргументы структуры в переменные
      foreach($this->Page->Arguments() as $car=>$bus)
        $this->Vars->$car=$this->Vars->Spoof($bus);
        //$bus;
      // готовим страницу
      $html=$this->Making($xml);
      // данные меток, шаблонов, скрипта
      $arr=$this->Page->LTS();
      // экранирование меток
      if(isset($arr['lbls']))
        foreach($arr['lbls'] as $car)
          $html=str_replace('['.$car.']',$this->Vars->$car??'',$html);
      // вставка шаблонов и скрипта
      $bus=isset($arr['tmpl'])?$arr['tmpl']:'';
      if(isset($arr['code']))$bus.=$arr['code'];
      if($bus)$html=str_replace('</html>',$bus.'</html>',$html);
      // мета теги
      $bus='<meta name="Generator" content="'.NAME.'-framework">'.CRLF;
      // каскадные стили
      if(TEST)$bus.='<style>'.CRLF.$this->Page->Styles().'</style>'.CRLF;
      else{$car=$GLOBALS['nav'];
        $bus.='<link type="text/css" rel="stylesheet" href="/'.$car['dirname'].'/'.$car['filename'].'.css">'.CRLF;
      }
      if(isset($arr['load']))$bus.=$arr['load'];
      $html=str_replace('</head>',$bus.'</head>',$html);
    }else{
      array_push($GLOBALS['err'],'M ['.Path(ROOT,'mods',$GLOBALS['nav']['dirname']).$GLOBALS['nav']['filename'].'.xml] Нет ответа в инструкции');
      $html='Что-то пошло не так.. :(';
    }
    // вывод готовой страницы
    echo $html;
  }
  // Изготовление
  private function Making($xml){
    $this->Args::$args=[];
    $arg=$xml->attributes();$chi=true;
    $rec=trim(trim($xml->__toString()),'|');
    foreach($arg as $nam=>$par){
      $nam=explode('.',$nam);$par=$par->__toString();
      if(count($nam)>1){[$nod,$mtd]=$nam;
        if($mtd){$nod=array_shift($nam);$node=$this->Nods->$nod;
          if($node){$mtd=array_shift($nam);$node->flags=$nam;
            //$nam=array_splice($nam,2,!end($nam)?-1:null);
            $res=$node->$mtd($par);$rtp=gettype($res);
            switch($rtp){
              case 'NULL': break;
              case 'boolean': $par=$res;break;
              case 'array': $rec=Render($rec,$res);break;
              case 'integer': case 'string': $rec.=$res;break;
              default: trigger_error('Не допустимый результат '.$nod.'.'.$mtd.'='.$rtp);
            }
          }else trigger_error('Нет класса узла '.$nod);
        }else $rec.=$this->Nods->Lay($nod,$par);
      }else switch($nam[0]){
        case '_sub':
          if($par){
            if($par=='*')$this->Childs($xml,$rec,true);
            else{
              $par=$this->Vars->Spoof($par);
              $this->Join($par,$this->Making($xml->$par),$rec);
            }
          }else $this->Childs($xml,$rec);
          $chi=false;
          break;
        case '_mem':
          if($par){
            if($xml->count()){$chi=false;
              $rec=$this->ArrChi($xml,$rec);
              $this->Vars->$par($rec);
            }else $this->Vars->$par=$rec;
          }
          $rec='';
          break;
        case '_var':
          if($par){$res=$this->Vars->$par;
            if(is_array($res))$rec=Render($rec,$res);
            else{
              $rec=str_replace('['.$par.']',$res,$rec,$cnt);
              if($cnt==0)$rec.=$res;
            }
          }else $rec=$this->Vars->Spoof($rec);
          break;
        case '_for':
          if(preg_match('/([^=]+)=(.*)/',$par,$mth)){
            $par=$mth[2];$mth=explode('.',$mth[1]);
            $nod=array_shift($mth);$node=$this->Nods->$nod;
            if($node){$mtd=array_shift($mth);
              $node->flags=$mth;$arr=$node->$mtd($par);
            }else trigger_error('Нет класса узла '.$nod);
          }else $arr=$this->Vars->$par;
          if(is_array($arr))foreach($arr as $val){
            $this->Vars->for=$val;$res='';
            $this->Childs($xml,$res);$rec.=$res;
          }
          $chi=false;
          break;
        case '_dup':
          $par=$this->Vars->Spoof($par);
          preg_match_all('/\s*{(.*?)(?:\s*})/s',$rec,$mth);
          if($mth[1]){$arr=[];$par++;
            $che=(int)$this->Args->Take('checked');
            for($nx=1;$nx<$par;$nx++)
              foreach($mth[1] as $key=>$val)
                $arr[$key][$nx]=str_replace("[checked$nx]",$nx==$che?' checked':'',str_replace('?',$nx,$val));
            foreach($mth[0] as $key=>$val)
              $rec=str_replace($val,implode('',$arr[$key]),$rec);
          }
          break;
        case '_ifv':
          $par=$this->Condition($par);
          break;
				case '_exe':
          if($par){$this->Transfer($xml,$par);$chi=false;}
					else $this->Execution($rec);
          break;
        case '_php':
          if($par){
            $par=$this->Vars->Spoof($par);
            $par=explode('/',strtolower($par));
            if($par[0]!='node')array_unshift($par,'node');
            if(substr($par[2],-4)!='.php')$par[2].='.php';
            $par=implode('/',$par);
            if(!file_exists($par))trigger_error('Нет файла: '.$par);
            else{ob_start();@include $par;$rec.=ob_get_clean();}   
          }else{
            if(TEST)trigger_error('Выполнение PHP eval отключено!!');
            else{ob_start();eval($rec);$rec=ob_get_clean();}              
          }
          break;
        case '_xml':
          if($inc=$this->Page->Answer($par))
            $rec.=$this->Making($inc);
          else if($this->Vals->errors=='yes')
            trigger_error('Нет xml структуры "'.$par.'"');
          break;
        default:
          $this->Args->{$nam[0]}=$this->Vars->Spoof($par);
      }
      if($par===false){$rec='';$chi=false;break;}
    }
    if($chi)$this->Childs($xml,$rec);
    return $rec;
  }
  // Обработка дочерних элементов
  private function Childs($xml,string &$rec,bool $sgn=false){
    $this->Stack->Push($this->Args::$args);
    foreach($xml->children() as $xch){$res=$this->Making($xch);
      preg_match('/([^_]+)?_?(.*)?/',$xch->getName(),$tag);
      if($tag[2])$res='<'.$tag[2].$this->Args->InAttr().'>'.($res?$res.'</'.$tag[2].'>':'');
      if($res){
        if($tag[1]){
          preg_match('/<(\w+)([^>]*)>\s*\['.$tag[1].'\]/',$rec,$mth);
          if($mth)
            $rec=str_replace($mth[0],'<'.$mth[1].$this->Args->InAttr($mth[2]).'>'.$res,$rec);
          else $this->Join($tag[1],$res,$rec);
        }else $rec.=$res;
        if($sgn)break;
      }
    }
    $nms=$xml->getNamespaces(true);
    if(count($nms)){
      foreach($nms as $prf=>$par){
        $par=$this->Vars->$par;
        $arr=$xml->children($prf,true)->$par;
        if(!$arr->count()){$par='default';
          $arr=$xml->children($prf,true)->$par;
        }
        if($arr->count()){
          //self::$Lorry::$Vars->ses['prfx'][]=$prf.':'.$par;
          foreach($arr as $xch){
            $rec.=trim($xch->__toString());
            $this->Childs($xch,$rec);
          }
        }
      }
    }
    $this->Args::$args=$this->Stack->Pop();
  }
	// Передача инструкции
	private function Transfer($xml,$par){
		$node=$this->Nods->$par;
    if($node){
			foreach($xml as $xch)
        $node->Receive($xch->getName(),$this->Making($xch));
    }else trigger_error('Нет класса узла '.$par);
	}
  // Выполнение инструкций
  private function Execution(string &$rec){$res=false;
    while(preg_match('/<_\s?[^>]+?>.*?<\/_>/is',$rec,$mth,PREG_OFFSET_CAPTURE)){
      $mth=$mth[0];$xml=simplexml_load_string($mth[0]);
      if($xml)$rec=str_replace($mth[0],$this->Making($xml),$rec);
      else{$err=libxml_get_errors()[0];$res=[];
        $res['row']=$err->line;$res['line']=0;
        $res['column']=$err->column;$res['message']=trim($err->message);
        $shift=0;$mth=$mth[1];
        while($shift=strpos($rec,"\r\n",$shift)){$res['line']++;
          if($mth<$shift)break;
          $shift+=2;
        }
        break;
      }
    }
    return $res;
  }
  // Массив дочерних элементов
  private function ArrChi($xml,string $rec){
    if($xml->count()){
      if($rec)$this->Childs($xml,$rec);
      else{$arr=[];$num=0;
        foreach($xml->children() as $xch){
          $key=$xch->getName();
          if($key=='_')$key=++$num;
          $rec=$this->Making($xch);
          if($xch->count())$rec=$this->ArrChi($xch,$rec);
          $arr[$key]=$rec;
        }
        $rec=$arr;
      }
    }
    return $rec;
  }
  // Условие
  private function Condition(string $par){
    preg_match('/([^!=]+)([!=]+)?(.*)/',$par,$mth);
    $par=$this->Vars->{$mth[1]};
    switch($mth[2]){
      case '!': $par=!(bool)$par;break;
      case '=': $par=$par==$mth[3];break;
      case '!=': $par=$par!=$mth[3];break;
      default: $par=(bool)$par;
    }
    return $par;
  }
  // Присоединение к записи
  private function Join($lbl,$rpl,&$rec){
    $rec=str_replace('['.$lbl.']',$rpl,$rec,$cnt);
    if($cnt==0)$rec.=$rpl;
  }
}
?>
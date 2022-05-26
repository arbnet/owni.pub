<?php // Рендеринг
function Render(string $txt,?array $arr){$res='';
  if($txt){
    if($arr)
      if(is_int(key($arr))){
        if(is_array(current($arr))){
          foreach($arr as $tar){$car=$txt;
            foreach($tar as $key=>$val)
              $car=str_replace('['.(is_int($key)?'val':$key).']',$val,$car);
            $res.=$car.CRLF;
          }
        }else{
          preg_match('/\s+selected="([^"]+)"/',$txt,$mth);
          foreach($arr as $val){$car=str_replace('[val]',$val,$txt);
            if($mth)$car=str_replace($val==$mth[1]?'="'.$val.'"':$mth[0],'',$car);
            $res.=$car.CRLF;
          }
        }
      }else{
        if(is_array(current($arr))){
          foreach($arr as $key=>$tar){$car=str_replace('[key]',$key,$txt);
            foreach($tar as $val)
              $res.=str_replace('[val]',$val,$car).CRLF;
          }
        }else{
          if(preg_match_all('/\[(key|val)\]/',$txt,$mth,PREG_OFFSET_CAPTURE)){
            $mth[0]=array_reverse($mth[0]);$mth[1]=array_reverse($mth[1]);
            foreach($arr as $key=>$val){$car=$txt;
              foreach($mth[1] as $num=>$tar)
                $car=substr_replace($car,$tar[0]=='key'?$key:$val,$mth[0][$num][1],5);
              $res.=$car;
            }
          }else if(preg_match_all('/\[([^]]+)\]/',$txt,$mth)){$res=$txt;
            foreach($mth[1] as $key=>$val)
              if($val=$arr[$val]??null)
                $res=str_replace($mth[0][$key],$val,$res);
          }
        }
    }
  }else{ob_start();print_r($arr);$res='<pre>'.ob_get_clean().'</pre>';}
  return $res;
}
?>
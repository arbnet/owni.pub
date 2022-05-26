<?php // Смотреть
function See(...$arg){$see=debug_backtrace()[0];
  echo CRLF.'<pre class="see"><xmp>SEE/ '.$see['file'].':'.$see['line'].CRLF;$see=false;
  foreach($arg as $num=>$par)
    if($par==='see')$see=!$see;
    else{echo $num.'= ';
      if($see || is_null($par))var_dump($par);
      else{print_r($par);if(!is_array($par))echo CRLF;}
    }
  echo '/SEE'.CRLF.'</xmp></pre>'.CRLF;
}
?>
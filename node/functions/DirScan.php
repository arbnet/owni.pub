<?php // Сканирование директории
function DirScan(string $dir,bool $sgn=false){$arr=[];
  $asd=array_diff(scandir($dir),array('.','..','.htaccess'));
  foreach($asd as $val){$pth="$dir/$val";
    if(is_dir($pth))
      $arr['folders'][$val]=$sgn?DirScan($pth,true):null;
    else{$val=explode('.',$val);
      if(!isset($val[1]))$val[1]='_';
      $arr['files'][$val[1]][]=$val[0];
    }
  }
  return $arr;
}
?>
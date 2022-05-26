<?php // В расширение
function InExt($arr,$etp,$ext){$tar=[];
  if(is_string($etp))$etp=explode(',',$etp);
  foreach($etp as $val)if(isset($arr[$val]))
    $tar=array_merge($tar,$arr[$val]);unset($arr[$val]);
  $arr[$ext]=array_unique($tar);asort($arr);
}
?>
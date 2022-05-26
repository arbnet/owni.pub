<?php // Информация о пути
function WayInfo($way){
  $arr=parse_url($way);$arr['way']=$way;
  $way=pathinfo($arr['path']);unset($arr['path']);
  return array_merge($way,$arr);
}
?>
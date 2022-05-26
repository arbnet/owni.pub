<?php // Грузовик
function Lorry($name){$name='core\\'.$name;
  if(!class_exists($name))trigger_error('Нет класса '.$name);
  else return method_exists($name,'Loner')?$name::Loner():
    function(...$arg)use($name){return new $name($arg);};
}
?>
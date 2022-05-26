<?php // Получение базового имени
function NameBase(string $wbn){$wbn=basename($wbn);
  if($del=strstr($wbn,'.'))$wbn=rtrim($wbn,$del);
  return $wbn;
}
?>
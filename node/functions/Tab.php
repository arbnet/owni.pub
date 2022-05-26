<?php // Табуляция
function Tab(string $txt,int $num=1){
	$tab=str_repeat("\t",$num);$arr=explode(CRLF,$txt);
	array_walk($arr,function(&$val)use($tab){
		$val=$tab.trim($val);
	});
	return implode(CRLF,$arr);
}
?>
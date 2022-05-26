<?php
(new class($this) {
  public function __Construct($Node){
    $this->num=0;
    $this->id=$Node->Args->Take('id');
    $this->links=$Node->Args->Take('links');
    $this->folder=$Node->disc->Out('explorer');
    if($this->links)$this->links=Func($this->links)[0][1];
  }
  // Проводник
  private function Explorer($arr,$pth=''){$res='';
    if(isset($arr['fld']))foreach($arr['fld'] as $key=>$val){$this->num++;
      $res.='<input id="'.$this->id.$this->num.'" type="checkbox" hidden>
      <li><label for="'.$this->id.$this->num.'" title="директория">'.$key.'</label>
      <ul>'.CRLF.$this->Explorer($val,($pth?$pth.'\\':'').$key).'</ul>'.CRLF.
      '</li>'.CRLF;
    }
    if(isset($arr['fls']))
      foreach($arr['fls'] as $ext=>$fls)foreach($fls as $val){
        if($this->links)$val=($this->links)($pth,$val,$ext);
        //call_user_func_array($this->links,[$pth,$val,$ext]);
        $res.='<li>'.$val.'</li>'.CRLF;
      }
    return $res;
  }
  public function LayOut(){
    $this->id='catalog'.($this->id?'_'.$this->id:'');
    echo '<ul id="'.$this->id.'" class="catalog">'.$this->Explorer($this->folder).'</ul>';
  }
})->LayOut();
?>
<?php
(new class($this) {
  public function __Construct($I){
    $this->id=$I->Args->Take('id');
    $this->links=$I->Args->Take('links');
    $this->id='explorer'.($this->id?'_'.$this->id:'');
    if($this->links)$this->links=Func($this->links)[0];
    $this->mods=$I->pfld;$this->num=0;
  }
  // Проводник
  private function Explorer($arr,$pth=''){$res='';
    if(isset($arr['folders']))
      foreach($arr['folders'] as $key=>$val){
        $this->num++;$id=$this->id.'_'.$this->num;
        $res.='<input id="'.$id.'" type="checkbox" hidden>
        <li><label for="'.$id.'" title="директория">'.$key.'</label>
        <ul>'.CRLF.$this->Explorer($val,($pth?$pth.'\\':'').$key).'</ul>'.CRLF.
        '</li>'.CRLF;
      }
    if(isset($arr['files']))
      foreach($arr['files'] as $ext=>$fls)foreach($fls as $val){
        if($this->links)$val=($this->links)($pth,$val,$ext);
        $res.='<li>'.$val.'</li>'.CRLF;
      }
    return $res;
  }
  public function LayOut(){
    echo '<ul id="'.$this->id.'" class="explorer">'.$this->Explorer($this->mods).'</ul>';
  }
})->LayOut();
?>
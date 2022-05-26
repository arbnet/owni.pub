import $ from './Tag.js';
import $e from './MyEvents.js';
import {Magic} from './Classes.js';
//import { Log } from './Functions.js';

var LayOut={
/* Макет&Данные */
[Symbol.toStringTag]:'LayOut',
[Symbol.for('default')]:'$$',
[Symbol.for('Magic')]:'layout',
$:null,// макет
lay(tpl){
/* Получение макета */console.log(tpl)
  tpl=document.getElementById(tpl);
  if(tpl)if(tpl.tagName=='TEMPLATE')
    this.$=tpl.content.cloneNode(true);
  return this
},
out(map){
/* Получение макета с данными */
  if(map instanceof Map){
    let Fchi=(elt)=>{
      for(let att of elt.attributes){
        let mth=att.value.match(/\[(\w+)\]/);
        if(mth)if(map.has(mth[1]))
          att.value=att.value.replace(mth[0],map.get(mth[1]))
      }
      for(let nod of elt.childNodes){
        if(nod.nodeName=='#text'){
          let mth=nod.nodeValue.match(/\[(\w+)\]/);
          if(mth)if(map.has(mth[1]))
            nod.nodeValue=nod.nodeValue.replace(mth[0],map.get(mth[1]))
        }else Fchi(nod)
      }
    }
    for(let chi of this.$.children)Fchi(chi)
  }
},
layout(elt,tpl,out,opt){
/* Добавление разметки на страницу */
  this.lay(tpl);if(!elt.magic)elt=$(elt);
  let elo=out!=undefined?this.out(out):this.$;
  console.log(this.$)
  elt.insert(elo,opt);$e(elt.child('last').$)
},
build(obj){
/* Построение из объекта */
  if(typeof(obj)=='string')
    this.$=document.createTextNode(obj);
  else if(obj.tag){
    this.$=document.createElement(obj.tag);
    if('att' in obj)for(let nam in obj.att)
      this.$.setAttribute(nam,obj.att[nam])
    if('rec' in obj){
      let Into=(obj)=>{
        if(typeof(obj)=='object')this.$.append(Build(obj));
        else this.$.innerText+=obj
      }
      if(Array.isArray(obj.rec)){
        for(let nod of obj.rec)Into(nod)
      }else Into(obj.rec)
    }
  }
}};

export default new Magic(LayOut);
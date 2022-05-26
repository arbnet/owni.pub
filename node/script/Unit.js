import './Prototypes.js';
import $ from './Tag.js';
import $$ from './LayOut.js';
import $e from './MyEvents.js';
//import {Valid} from './Functions.js';
import {Magic} from './Classes.js';

var Make={
/* Объект функций юнита */  
[Symbol.toStringTag]:'Make',
[Symbol.for('Type')]:'unit',
nd(){
/* Без действия по умолчанию */
  event.preventDefault()
},
text(val,fce){
/* Установка текста */
  this.modifi(fce,function(elt){
    let txt=elt.data('text');
    txt=txt?txt.replace('?',val):val;
    elt.text(txt)
    //this.spoof(txt)
  })
},
html(val,fce){
/* Установка разметки */
  this.modifi(fce,function(elt){
    let htm=elt.data('html');
    htm=htm?htm.replace('?',val):val;
    elt.html(this.spoof(htm))
  })
},
attr(val,fce){
/* Изменение атрибута */
  val=val.split('=');
  this.modifi(fce,function(elt){
    elt.attr(val[0],val[1])
  })
},
style(val,fce){
/* Изменение стиля */
  this.modifi(fce,function(elt){
    elt.style(val)
  })
},
class(val,fce){
/* Инверсия css класса */
  val=val.split(' ');
  this.modifi(fce,function(elt){
    for(let nam of val)elt.class(nam)
  })
},
value(val,fce){
/* Получение\Изменение значения */
  if(val==null)return $(fce,this.elt).value();
  else this.modifi(fce,function(elt){let tag=elt.tag;
    if(tag=='INPUT' || tag=='TEXTAREA'){elt.value(val)
      elt.run(['change','keyup','keydown','keypress'])
    }
  })
},
child(fce){
/* Получение дитя */
  return $(fce,this.uni);
},
checked(val,fce){
/* Флажок radio|checkbox */
  if(val!=undefined)val=val=='false'?false:Boolean(val);
  this.modifi(fce,function(elt){elt=elt.$;
    elt.checked=val!=undefined?val:!elt.checked;
  })
},
enable(val,fce){
/* Активность элемента(ов) */
  val=val=='false'?true:Boolean(!val);
  this.modifi(fce,function(elt){
    elt.$.disabled=val
  })
},
/* append(val,fce){
  let els=$(fce,this.uni);
  if(els.count)els.insert(elt)
}, */
hidden(val,fce){
/* Скрытость элемента(ов) */
  val=val=='false'?true:Boolean(!val);
  this.modifi(fce,function(elt){
    elt.class('hidden',val?'+':'-')
  })
},
spoof(txt){
/* Подмена */
  let mth=[...txt.matchAll(/\$\{([^\}]+)\}/g)];
  for(let par of mth){
    let rpl='',arg=par[1].split('.');
    if(arg.length==1)rpl=this.$[arg[0]];
    else{let fnc=arg.shift();
      rpl=this.$[fnc].apply(this,arg)
    }
    txt=txt.replaceFrom(par[0],rpl,par.index)
  }
  return txt
},
modifi(fce,Mfu){
/* Модификация элемнта(ов) */
  let els=$(fce,this.uni);
  if(els.count)for(let elt of els)Mfu(elt)
}};

var Unit={
[Symbol.toStringTag]:'Unit',
[Symbol.for('default')]:'_',
[Symbol.for('Type')]:'owni',
[Symbol.for('Magic')]:'unit',
umap:new Map(),// объекты юнитов
unit(uid){
/* Инициализация объекта юнита */
  let uni;
  if(uid==undefined)uid=$('^[id]',event.target).id();
  if(uid)if(!this.umap.has(uid)){
    let els=$('#'+uid);
    uni=Object.create(null);
    uni[Symbol.toStringTag]=uid;
    if(!els.count)els=$('.'+uid);
    if(els.count)uni.uni=els;
    Object.setPrototypeOf(uni,Make);
    this.umap.set(uid,uni);
  }else uni=this.umap.get(uid)
  return uni 
}};

export default new Magic(Unit);
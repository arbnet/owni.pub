import {Magic} from './Classes.js';
import {HEXRGB,Type,Valid} from './Functions.js';

var Tag={/* Работа с тегом */
[Symbol.toStringTag]:'Tag',
[Symbol.for('default')]:'$',
[Symbol.for('Magic')]:'el',
//opt:{},// опции события
//els:null,// элемент(ы)
[Symbol.iterator](){
  let iob=Object.create(null);this.cur=-1;
  iob.next=()=>{let res=Object.create(null);
    res.done=!this.next;if(!res.done)res.value=this;
    return res
  }
  return iob
},
get $(){
/* Подобъект - элемент */
  return this.els?this.els[this.cur]:null
},
get tag(){
/* Имя тега */
  return this.$.tagName
},
get css(){
/* Каскадные стили */
  return getComputedStyle(this.$)
},
get type(){
/* Тип элемента */
  return Type(this.$)
},
get next(){
/* Следующий текущий элемент */
  if(this.cur+1<this.count){this.cur++;return true}
  else return false
},
get count(){
/* Количество элементов */
  return this.els?this.els.length:0
},
get width(){
/* Ширина элемента */
  return this.$.offsetWidth
},
get height(){
/* Высота элемента */
  return this.$.offsetHeight
},
get childs(){
/* Количество дочерних элементов */
  return this.$.children.length
},
get coords(){
/* Координаты элемента */
  return this.$.getBoundingClientRect()
},
get iflag(){
/* Проверка на флажок */
  let etp=this.$.type;
  return etp=='radio' || etp=='checkbox'?true:false
},
get after(){
/* Следующий соседний элемент */
  return this.$.nextElementSibling
},
get before(){
/* Предыдущий соседний элемент */
  return this.$.previousElementSibling
},
get parent(){
/* Родительский элемент */
  return this.$.parentElement
},
get previous(){
/* Предыдущее значение */
  return this.iflag?this.$.defaultChecked:this.$.defaultValue;
},
get children(){
/* Получение дочерних элементов */
  return this.$.children
},
id(par){
/* id элемента */
  let res=this.$.id;
  if(res){res=res.match(/([a-zA-Z]+)(\d*)/);
    switch(par){
      case 'sym': res=res[1];break
      case 'num': res=res[2];break
      default: res=res[0]
    }
  }
  return res
},
hide(par){
/* Прятать\Показывать элемент */
  if(par==undefined)par=!this.$.hidden;
  else par=Valid(par,'Boolean');
  this.$.hidden=par
},
text(rec,opt){
/* Вставка\получение текста записи */
  if(rec==undefined)return this.$.innerText;
  else switch(opt){
    case 'before': this.$.insertAdjacentText('beforebegin',rec);break
    case 'prepend': this.$.insertAdjacentText('afterbegin',rec);break
    case 'append': this.$.insertAdjacentText('beforeend',rec);break
    case 'after': this.$.insertAdjacentText('afterend',rec);break
    default: this.$.innerText=rec;
  } 
},
html(rec,opt){
/* Вставка\получение html-кода записи */
  if(rec==undefined)return this.$.innerHTML;
  else switch(opt){
    case 'before': this.$.insertAdjacentHTML('beforebegin',rec);break
    case 'prepend': this.$.insertAdjacentHTML('afterbegin',rec);break
    case 'append': this.$.insertAdjacentHTML('beforeend',rec);break
    case 'after': this.$.insertAdjacentHTML('afterend',rec);break
    default: this.$.innerHTML=rec;
  } 
},
attr(nam,val){
/* Получение значения и установка атрибута
nam - имя атрибута
val - значение атрибута
    если | | то отдаёт текущее значение */
  if(val===undefined)
    return this.$.hasAttribute(nam)?this.$.getAttribute(nam):false;
  else{
    if(val==null)this.$.removeAttribute(nam);
    else this.$.setAttribute(nam,Valid(val,'String'));
  }
},
data(nam,val){
/* Получение\Установка data-атрибута */
  if(val!=undefined)this.$.dataset[nam]=val;
  else{let dts=this.$.dataset
    if(nam!=undefined)return nam in dts?dts[nam]:'';
    else return dts
  }
},
style(val){
/* Текущий стиль элемента */
  if(val==undefined)return this.$.style.cssText;
  else{let css=this.css, stl=this.$.style.cssText;
    val=[...val.matchAll(/([-\w]+):\s*([^;]*)/g)];
    for(let nps of val){nps[2]=HEXRGB(nps[2]);
      if(css[nps[1]]!=nps[2]){
        let mth=stl.match(new RegExp(nps[1]+':([^;]+)')),
            rpl=nps[2]?nps[1]+':'+nps[2]:'';
        if(mth)stl=stl.replace(mth[0],rpl);else stl+=rpl+';';
      }
    }
    this.$.style.cssText=stl
  }
},
class(c1,c2){
/* Работа с css классами элемента
c1 - class; c2 - [u]|?|+|-|class */
  let cls=this.$.classList;
  switch(c2){
    case '?':// проверка
      return cls.contains(c1);
    case '+': case true:// добавление
      for(let val of c1.split(' '))cls.add(val);break
    case '-': case false:// удаление
      for(let val of c1.split(' '))cls.remove(val);break
    case undefined:// инверсия
      for(let val of c1.split(' '))cls.toggle(val);break  
    default:// замена
        if(cls.contains(c1))cls.replace(c1,c2);
        else cls.replace(c2,c1);
  }
},
focus(par){
/* Фокус
par - [u](проверка)|true(установка)|false(снятие) */
  if(par==undefined)return this.$==document.activeElement;
  else if(par==true){
    if(this.$.contentEditable){
      par=this.text().length;this.$.click()
    }else{
      par=this.$.value?this.$.value.length:0;this.$.focus()
    }
    this.$.selectionStart=par
  }else this.$.blur();
},
child(par){
/* Дочерний элемент */
  let elt;
  switch(par){
    case 'first': elt=this.$.firstElementChild;break
    case 'last': elt=this.$.lastElementChild;break
    default: elt=this.$.children[Valid(par,'IntAbs')];
  }
  return elt
},
value(val){
/* Получение\Установка значения */
  if(val==undefined)return this.iflag?this.$.checked:this.$.value;
  else if(this.iflag)this.$.checked=Valid(val,'Boolean');else this.$.value=val;
},
insert(elt,opt){
/* Вставка дочернего элемента eli */
  if(elt.magic)elt=elt.$
  switch(opt){
    case 'after': this.$.after(elt);break
    case 'before': this.$.before(elt);break
    case 'prepend': this.$.prepend(elt);break
    default: this.$.append(elt)
  }
},
replace(elt){
/* Замена элемента на elt */
  this.$.replaceWith(elt)
},
delete(num){
/* Удаление
num - [u](сам элемент)|номер дочернего */
  if(num==undefined){
    if(this.count){this.$.remove();
      let cnt=this.count-1;
      if(this.cur>cnt)this.cur=cnt
    }
  }else{let cnt=this.childs;
    num=Valid(num,'IntAbs');
    if(num<cnt)this.$.children[num].remove();
  }
},
/*option(key,val){
 Установка опции для событий
key - ключ; val - значение 
  let opt={...Tag.opt};
  if(key in opt){
    if(!('opt' in this))this.opt=opt;
    this.opt[key]=Boolean(val)
  }
},*/
add(evn,fnc,opt){
/* Добавление обработчика события
evn - событие; fnc - функция */
  if(opt==undefined)opt={capture:false,once:false,passive:false};
  //for(let key in opt)if(this[key])opt[key]=this[key];
  this.$.addEventListener(evn,fnc,opt)
},
del(evn,fnc,opt){
/* Удаление обработчика события
evn - событие; fnc - функция */
  if(opt==undefined)opt={capture:false,once:false,passive:false};
  //for(let key in opt)if(this[key])opt[key]=this[key];
  this.$.removeEventListener(evn,fnc,opt)
},
run(evn,par){
/* Выполнение события evn */
  if(Array.isArray(evn))for(let val of evn)
    if(this.$['on'+val]){evn=val;break}
  if(par==undefined)this.$.dispatchEvent(new Event(evn));
  else this.$.dispatchEvent(new CustomEvent(evn,{detail:par}))
},
el(par,elt){
/* Получение элемента(ов)
par - параметр; elt - элемент */
  if(typeof par=='string'){
    if(elt==undefined)elt=document;
    else if(elt.magic)elt=elt.$;
    let mth=par.match(/([^\w])?(.*)/);
    switch(mth[1]){
      case '?':// активный элемент
        this.els=[document.activeElement];break
      case '+':// создание элемента
        this.els=[elt.createElement(mth[2])];break
      case '^':// родительский элемент
        elt=elt.closest(mth[2]);this.els=elt?[elt]:[];break
      case '$':// элемент по селектору
        elt=elt.querySelector(mth[2]);this.els=elt?[elt]:[];break
      case '#':// элемент по идентификатору
        elt=elt.getElementById(mth[2]);this.els=elt?[elt]:[];break
      case '%':// элементы по тегу
        this.els=elt.getElementsByTagName(mth[2]);break  
      case '.':// элементы с классом
        this.els=elt.getElementsByClassName(mth[2]);break
      case '~':// элементы по началу имени атрибута
        elt=elt.querySelectorAll('*');mth=mth[2].split('|');
        this.els=Object.values(elt).filter((elt)=>{
          for(let att of elt.attributes)for(let val of mth)
            if(att.name.indexOf(val)==0)return true;
          return false;
        });
        break
      case '@':// элементы по значению атрибута
        mth=mth[2].split('=');
        if(mth.length>1){
          if(mth[0][0]=='@')mth[0]='data-'+mth[0].substr(1);
          par='['+mth[0]+(mth[1]?'*="'+mth[1]+'"]':']');
        }else par='[data-nick="'+mth[0]+'"]';
      default: // элементы по селектору
        this.els=elt.querySelectorAll(par);
    }
  }else switch(Type(par)){
    case 'object HTMLCollection':
      this.els=par;
      break
    default: this.els=[par];
  }
  this.cur=0;
}};

export default new Magic(Tag);
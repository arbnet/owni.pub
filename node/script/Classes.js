import {Valid} from './Functions.js';

export class Magic {
/* Магический класс */
constructor(obj){// Конструктор
  let I=Object.create(null);
  obj.magic=true;this.obj=obj;
  I[Symbol.for('Type')]='magic';
  Object.setPrototypeOf(I,this.obj);
  return new Proxy(()=>{return I},this);
}
has(I,prp){// Наличие свойства
  I=I();return prp in I
}
set(I,prp,val){// Установка свойства
  return Reflect.set(I(),prp,val)
}
deleteProperty(I,prp){// Удаление свойства
  I=I();delete I[prp];return true
}
apply(...arg){// Функция подобъекта
  let I=Object.create(null),
      res=this.obj[Symbol.for('Magic')];
  arg=arg[2];Object.setPrototypeOf(I,this.obj);
  if(res)res=I[res].apply(I,arg);
  return res?res:new Proxy(()=>{return I},this)
}
get(I,prp,rcv){// Получение значения\функции
  I=I();let res=Reflect.get(I,prp,rcv);
  if(prp==Symbol.iterator)return res;
  else if(typeof(res)=='function'){
    return function(){
      res=res.apply(I,arguments);
      return res===undefined?this:res
    }
  }else return res
}}

export class Rement {
/* Приращение\Уменьшение */
[Symbol.toStringTag]='Rement';
[Symbol.for('Type')]='owni';
constructor(val){this.val=Math.trunc(val)}
get inc(){this.val++;return this.val}
get dec(){this.val--;return this.val}
}

export class Stack {
/* Стэк\очередь */
[Symbol.toStringTag]='Stack';
[Symbol.for('Type')]='owni';
$=[];// подобъект
count=0;// счётчик
has(val){return this.$.includes(val)}
push(val){this.count=this.$.push(val)}
delete(val){
  let nx=this.$.indexOf(val);
  if(nx!=-1)this.$.splice(nx,1);
}
get pop(){
  if(this.count){
    this.count--;return this.$.pop()
  }else return null
}
get queue(){
  if(this.count){
    this.count--;return this.$.shift()
  }else return null
}}

export class MultiMap {
/* Мультикарт */
[Symbol.toStringTag]='MultiMap';
[Symbol.for('Type')]='owni';
$=new Map();// подобъект
sep='/';// разделитель
constructor(sep){
  if(sep!=undefined)this.sep=sep;
}
#map(arr){let map=this.$;
  for(let nam of arr){
    if(!map.has(nam))map.set(nam,new Map());
    map=map.get(nam);
  }
  return map
}
get(...arg){if(arg.length==1)arg=arg[0];
  return this.#map(arg)
}
has(...arg){if(arg.length==1)arg=arg[0];
  let key=arg.pop(), map=this.#map(arg);
  return map.has(key);
}
put(...arg){if(arg.length==1)arg=arg[0];
  let val=arg.pop(), key=arg.pop(), map=this.#map(arg);
  map.set(key,val);
}}

export class Enum {
/* Перечисление */
[Symbol.toStringTag]='Enum';
[Symbol.for('Type')]='owni';
sep=',';// разделитель
[Symbol.iterator](){return this.arr.values()}
constructor(val,sep){
  if(sep!=undefined)this.sep=sep;
  if(val==undefined)val=['true','false'];
  else if(!Array.isArray(val))
    val=val?Valid(val,'String').split(this.sep):[];
  this.arr=val
}
get count(){return this.arr.length}
get value(){return this.arr.join(this.sep)}
get(num){num=Valid(num,'IntAbs');
  return num<this.count?this.arr[num]:null
}
set(num,val){val=Valid(val,'String');
  if(num!=undefined){num=Valid(num,'IntAbs');
    if(num>this.count)this.arr.length=num;
    this.arr[num]=val
  }else this.arr.push(val)
}
num(val){
  return this.arr.indexOf(Valid(val,'String'))
}
toggle(val){
  let num=this.num(val);
  if(num!=-1)this.arr.splice(num,1);
  else this.arr.push(Valid(val,'String'))
}
farther(val){
  let num=this.num(val);
  if(num!=-1){num++;if(num>=this.count)num=0;
    return this.arr[num]
  }else return this.count?this.arr[0]:''
}}
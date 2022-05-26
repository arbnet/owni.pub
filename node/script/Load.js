import {MultiMap} from './Classes.js';
import {Type,Log} from './Functions.js';

export var Have={
/* Объект наличия файлов */
[Symbol.toStringTag]:'Have',
$:new MultiMap(),// подобъект
arr(way){
/* Массив пути
way - объект из this.way() */
  let arr=[way.extension];
  if(way.extension=='css'){
    if(way.skin)arr.push('skin',way.skin);
    else arr.push('node',way.node);
  }else arr.push(way.node);
  return arr
},
add(way){
/* Добавление файла
way - объект из this.way() */
  let arr=this.arr(way), dir=arr.pop(), obj=this.$.get(arr);
  obj=obj.has(dir)?obj.get(dir):new Set();
  obj.add(way.name);arr.push(dir,obj);this.$.put(arr)
},
has(way){
/* Проверка загрузки файла
way - путь к файлу - строка || объект из this.way() */
  if(typeof(way)=='string')way=this.way(way);
  let arr=this.arr(way), key=arr.pop(), map=this.$.get(arr);
  map=map.has(key)?map.get(key):null;
  return map?map.has(way.name):false;
},
way(way){
/* Определение пути
way - путь к файлу */
  let mth=way.match(/(\w+=)?([^\.]+)\.(.*)/);way={};
  if(mth[1]){way.name=mth[1].slice(0,-1);way.files=mth[2];}
  else{let arr=mth[2].split('/');
    switch(mth[3]){
      case 'js': 
        if(!arr[0])arr[0]='script';
        arr[2]=(arr[0]!='script'?'../'+arr[0]:'.')+'/'+arr[1]+'.js';
        way.path=arr[2];way.node=arr[0];way.name=arr[1];
        break
      case 'css':
        arr[0]=arr[1]+'/'+arr[2]+'/'+arr[3]+'.css';
        way.path=arr[0];way[arr[1]]=arr[2];way.name=arr[3];
        break
      default:
        way.path=arr.join('/')+'.'+mth[3];
        way.node=arr[0];way.name=arr.pop()
    }
  }
  way.extension=mth[3];return way
},
css(){
/* Добавление подключенных css */
  let els=document.querySelectorAll('link[href*=".css"]');
  for(let elt of els){
    let rul=elt.sheet.cssRules;
    for(let obj of rul)
      if(Type(obj)=='object CSSImportRule')
        this.add(this.way(obj.href))
  }
}};

export function Global(nam,val){
  if(val!=undefined)window[nam]=val;
  else return nam in window?window[nam]:null
}

export function Setup(mod){
  for(let name in mod)window[name]=mod[name]
  if(mod.default){let name=mod.default[Symbol.for('default')];
    window[name]=mod.default;if('cook' in window[name])window[name].cook()
  }
}

export function Load(...arg){
/* Загрузка файла
arg - массив путей к файлу(ам) & для склеивания файлов:
произвольное имя=пути к файлам через ',' .расширение */
  let arP=[];
  if(arg.length==1)arg=arg[0];
  if(typeof arg=='string')arg=arg.split(',');
  for(let way of arg){
    let Prm; way=Have.way(way);
    if(!Have.has(way)){
      switch(way.extension){
        case 'js':
          Prm=import(way.path).then(mod=>Setup(mod));
          break
        case 'css':
          Prm=new Promise((resolve,reject)=>{
            let elt=document.createElement('link');
            elt.type='text/css';elt.rel='stylesheet';
            elt.href='/'+way.path;
            elt.onload=()=>resolve;elt.onerror=()=>reject;
            document.head.append(elt);
          });
          break
        case 'json':
          Prm=fetch('/node/'+way.path).then((res)=>res.json())
          break
        default: Prm=fetch('/node/'+way.path)
      }
      Prm.catch((err)=>Log('#error','Ошибка в файле: '+way.path+'\n'+err))
         .finally(Have.add(way));
    }else Prm=Promise.resolve(true);
    arP.push(Prm);
  }
  return arP.length>1?Promise.all(arP):arP[0]
}
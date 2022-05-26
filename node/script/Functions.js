
export function Log(...arg){let see;
/* Вывод данных в консоль */
  for(let log of arg)
    if(typeof(log)=='string'){
      if(log[0]=='#'){log=log.match(/#(\w+)\s?(.*)?/);
        switch(log[1]){
          case 'ont': // включение таймера
            console.time(log[2]);break
          case 'off': // остановка таймера
            console.timeEnd(log[2]);break
          case 'grp': // группа
            console.group(log[2]);break
          case 'out': // соло
            console.groupEnd();break
          default: see=log[1];
        }
      }else{
        switch(see){
          case 'warn': console.warn(log);break
          case 'error': console.error(log);break
          default:
            console.info('%c %s ','color: green;background-color: #e3f0ea',log);
        }
      } 
    }else if(log==null)console.log(null);
    else if(log.nodeName)console.dir(log);
    else if(log instanceof Array)console.table(log);
    else console.log(log);
}

export function Type(val){
/* Получение типа переменной */
  if(val!=undefined){let vtp=typeof(val);
    switch(vtp){
      case 'function':
        if(!val.magic){val=vtp;break}
      case 'object':
        vtp=val.magic?'magic':val[Symbol.for('Type')];
        val=val.toString().replace(/\[|\]/g,'');
        if(vtp)val=val.replace('object',vtp);
        break
      default: val=vtp;
    }
  }else val='undefined';
  return val
}

export function HEXRGB(txt) {
/* Преобразование hex в rgb */
  let mth=txt.match(/#([a-fA-F0-9]+)/);
  if(mth){let rpl=mth[0], res='rgb';
    mth=[...mth[1].matchAll(/.{2}/g)].map(v=>parseInt(v,16));
    if(mth.length>3){res+='a';
      mth[3]=parseFloat(mth[3]/255).toFixed(3);
    }
    res=res+'('+mth.join(', ')+')';
    txt=txt.replace(rpl,res);
  }
  return txt
}

export function Valid(val,vtp){
/* Преобразование к типу */
  switch(vtp){
    case 'Boolean':
      val=val=='false'?false:Boolean(val);
      break
    case 'BigInt':  
    case 'Number':
      if(typeof val!='number')val=Number(val);
      if(isNaN(val))val=0;
      break
    case 'NumAbs':
      val=Math.abs(val);
      break
    case 'NumInt':
      val=Math.trunc(val);
      break
    case 'IntAbs':
      val=Math.abs(Math.trunc(val));
      break
    case 'String':
      if(val==undefined)val='';
      else if(typeof val!='string')val=String(val);
      break
    case 'Object':
    default:
      if(val==undefined)val=null
  }
  return val
}

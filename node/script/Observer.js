export default {
/* Наблюдение */
[Symbol.toStringTag]:'Observer',
[Symbol.for('default')]:'Observer',
[Symbol.for('Type')]:'OWNI',
$:new Map(),
on(elt,fnc,opt){
/* Включение наблюдения */
  let obj={};
  for(let val of opt){let key;
    switch(val){
      case 'spawn':  key='subtree';break;
      case 'childs': key='childList';break;
      case 'record': key='characterData';break;
      case 'attr':   key='attributes';break;
      default:       key='attributeFilter';
                    val=val.split(',');
    }
    obj[key]=Array.isArray(val)?val:true;
  }
  /* opt.forEach(val=>{let key;
    switch(val){
      case 'spawn':  key='subtree';break;
      case 'childs': key='childList';break;
      case 'record': key='characterData';break;
      case 'attr':   key='attributes';break;
      default:       key='attributeFilter';
                     val=val.split(',');
    }
    obj[key]=Array.isArray(val)?val:true;
  }); */
  if(typeof(elt)=='string')elt=document.getElementById(elt);
  let Obs=new MutationObserver(fnc);Obs.observe(elt,obj);this.$.set(elt,Obs);
},
off(elt){
/* Отключение наблюдения */
  let Obs=this.$.get(elt);this.$.delete(elt);
  Obs.disconnect();return Obs.takeRecords();
}};
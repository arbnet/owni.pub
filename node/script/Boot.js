let load=document.currentScript.dataset.load,
myjs=import('./Load.js').then((mod)=>{
  mod.Setup(mod);Have.css();
  if(load){
    load=load.split(',').map((val)=>{val=val.split('/');
      if(val.length==1)val.unshift('');return val.join('/')+'.js'
    });return Load(load)
  }
})
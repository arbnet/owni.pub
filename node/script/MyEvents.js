import $ from './Tag.js';
import $k from './Keep.js';
import Observer from './Observer.js';
import {Valid} from './Functions.js';
import {Magic,Enum} from './Classes.js';

function Fchild(fce,ev){
/* Поиск дочернего элемента юнита */
  let elt=$('^[id]',ev.target);
  if(elt.count)elt=$(fce,elt);
  return elt
}
function Fform(ev){
/* Поиск элемента формы */
  let frm=ev.target.closest('[data-form]')?.dataset.form;
  if(frm)frm=$('#'+frm);
  else{frm=$('^[id]',ev.target);if(frm.count)frm=$('%form',frm)}
  return frm
}

var JobOn={
/* Работа на события */
modify:{/* Модификация дочерних элементов */
  type:'O',//E
  opts:['childs'],
  cook(elt){
    if(elt.children.length)
      for(let ech of elt.children)ech.dataset.notch='modify'
  },
  make(mr){
    let elt=$(mr[0].target),
        els=$('$:not([data-notch="modify"])',elt);
    elt.run('modify',els.count>0)
  }
}};

var JobAdata={
/* Работа дата атрибутов */
enum:{
  make:{
    click(ev){
      let elt=$(ev.target),txt=elt.text(),
          enm=new Enum(elt.data('enum'),'|');
      elt.text(enm.farther(txt))
    }
  }
},
enumTitle:{
  cook(elt){
    this.make.click(elt)
  },
  make:{
    click(ev){
      let elt=$(ev.tagName?ev:ev.target),txt=elt.attr('title'),
          enm=new Enum(elt.data('enumTitle'),'|');
      elt.attr('title',enm.farther(txt))
    }
  }
},
form:{
  cook(elt){
    let els=$('%input',elt);
    if(els.count)for(let edt of els)
      edt.add('change',this.change)
  },
  change(ev){
    let elt=ev.target,frm=Fform(ev);
    if(frm.count){elt=$(elt);
      let edt,dln,nam=elt.attr('name'),val=elt.value();
      if(nam.substr(-2)=='[]'){dln=elt.data('line')
        edt=$('$[name="'+nam+'"][data-line="'+dln+'"]',frm);
      }else edt=$('$[name="'+nam+'"]',frm);
      if(val!=elt.previous){
        if(edt.count)edt.value(val);
        else{edt=elt.tag;if(edt=='SELECT')edt='INPUT';
          edt=$('+'+edt).attr('type','hidden').attr('name',nam).value(val);
          if(dln)edt.data('line',dln);
          frm.insert(edt)
        }      
      }else if(edt.count)edt.delete()
    }
  }
},
show:{
  make:{
    click(ev){
      let elt=$(ev.target),val=elt.data('toggle');
      val=!Valid(val,'Boolean');elt.data('toggle',val);
      val=elt.data('show');
      let els=Fchild(val,ev);
      if(els.count)for(elt of els){
        if(elt.data('hide')=='true')elt.hide()
      }
    }
  }
},
erase:{
  //cook(elt){},
  make:{
    click(ev){
      let frm=Fform(ev);
      if(frm.count){let elt=$(ev.target);
        let rid=elt.data('erase'),edt=$('$[name="erase"]',frm);
        if(edt.count){
          let val=new Enum(edt.value());val.toggle(rid);
          if(val.count)edt.value(val.value);
          else{edt.delete();rid=false}
        }else{
          edt=$('+input').attr('name','erase').attr('type','hidden').value(rid);
          frm.insert(edt)    
        }
        elt=$('^[data-hide]',elt);
        if(elt.count){
          rid=!Valid(elt.data('hide'),'Boolean');elt.data('hide',rid);
          edt=Fchild('$[data-show]',ev);
          if(!Valid(edt.data('toggle'),'Boolean'))elt.hide()
        }
      }
    }
  }
}};

var MyEvents={
/* Мои события */
[Symbol.toStringTag]:'MyEvents',
[Symbol.for('default')]:'$e',
[Symbol.for('Magic')]:'cook',
[Symbol.for('Type')]:'owni',
hang(elt){
/* Навешивание событий */
  if(elt.magic)elt=elt.$;
  let kon=Object.keys(JobOn),kad=Object.keys(JobAdata);
  for(let att of elt.attributes)
    if(att.name.indexOf('on')==0){
      let name=att.name.substr(2);
      if(kon.includes(name)){let job=JobOn[name];
        if(job.type=='O'){
          if('cook' in job)job.cook(elt);
          Observer.on(elt,job.make,job.opts);
          elt.addEventListener(name,new Function('event',att.value));
        }else{
          // установка событий
        }
      }
    }
  let att=elt.dataset;
  for(let dnam in att)
    if(kad.includes(dnam)){let job=JobAdata[dnam];
      if('cook' in job)job.cook(elt);
      if('make' in job)for(let name in job.make)
        elt.addEventListener(name,job.make[name]);
    }
},
cook(eld){
/* Приготовление */
  if(eld==undefined)eld=document;
  else this.hang(eld);
  let els=$('~on|data-',eld);
  for(let elt of els)this.hang(elt)
}};

export default new Magic(MyEvents);
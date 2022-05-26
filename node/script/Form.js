//import './Prototypes.js';
import $ from './Tag.js';
import Observer from './Observer.js';
import {Enum} from './Classes.js';
//import {Log} from './Functions.js';


var Form={
/* Объект работы с формой */
[Symbol.toStringTag]:'Form',
bsubmit(mr){
/* Кнопка передачи данных */
  let frm=mr[0].target,btn=$('$button[type="submit"]',frm),
      els=$('$:not([data-notch="static"])',frm);
  btn.class('hidden',els.count>0?'-':'+')
},
change(ev){
/* Изменение редактирования */
  let elt=ev.target,frm=elt.closest('[data-form]')?.dataset.form;
  if(frm){frm=$('#'+frm);
    if(frm.count){elt=$(elt);
      let nam=elt.attr('name'),val=elt.value(),
          edb=frm.child('[name="'+nam+'"]');
      if(val!=elt.previous){
        if(edb)edb.value=val;
        else{
          edb=elt.$.cloneNode();
          edb.setAttribute('type','hidden');
          frm.insert(edb)
        }      
      }else if(edb)edb.remove()
    }
  }
},
erase(ev){
/* Удаление строки */
  let elt=ev.target,frm=elt.closest('[data-form]')?.dataset.form;
  if(frm){frm=$('#'+frm);
    if(frm.count){elt=$(elt);
      let rid=elt.data('erase'), edb=frm.child('[name="erase"]');
      if(edb){let val=new Enum(edb.value);
        val.toggle(rid);
        if(val.count)edb.value=val.value;
        else edb.remove()
      }else{
        edb=$('+input').attr('name','erase').attr('type','hidden').value(rid);
        frm.insert(edb.$)    
      }
    }
  }
},
cook(eld){
  /* Подготовка к работе */
  if(eld==undefined)eld=document;
  let els=$('$form[id]',eld);
  for(let frm of els){let els;
    els=$('$button[type="submit"]',frm);
    if(els.count){
      Observer.on(frm.$,Form.bsubmit,['childs']);
      els=$(frm.children);
      if(els.count)for(let elt of els)
        elt.data('notch','static')
    }
    let edf=$('$[data-form="'+frm.id()+'"]');
    if(edf.count){
      els=$('%input',edf);
      if(els.count)for(let elt of els)
        elt.add('change',(ev)=>Form.change(ev))
      els=$('@@erase=',edf);
      if(els.count)for(let elt of els)
        elt.add('click',(ev)=>Form.erase(ev))
    }
  }
}
};
Form.cook()

//Подготовка
/* (function(){
  let edf=$('@@form='),arr=[];
  for(let efd of edf){let els;
    els=$('%input',efd);
    if(els.count)for(let elt of els)
      elt.add('change',(ev)=>Form.change(ev))
    arr.push(efd.data('form'));
  }
  edf=arr.unique;
  if(edf.length)for(let elt of edf){elt=$('#'+elt);
    if(elt.count){let elt=$('$button[type="submituu"]',elt)
    if(elt.count)Observer.on(elt.$,Form.dbutton,['childs']);
    }
  }

})() */
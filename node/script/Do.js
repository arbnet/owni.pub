import $ from './Tag.js';
import {Render} from './Unit.js';
import {Type,Valid} from './Functions.js';

// Параметр поиска дочернего элемента
function Fce(fce){
  fce=fce.match(/([^=]+)=?(.*)/);
  if(fce[1][0]=='-')fce[1]='data'+fce[1];
  return [fce[1],fce[2]]
}
// Модификация элемнта(ов)
function Modifi(els,Mfu){
  if(els.count)for(let elt of els)Mfu(elt)
}

export default {/* Делать */
text(val,fce){
/* Установка текста */
  Modifi($('@'+fce,this.elt),function(elt){
    elt.text(Render(elt.data('text'),val))
  })
},
html(val,fce){
/* Установка разметки */
  Modifi($('@'+fce,this.elt),function(elt){
    elt.html(Render(elt.data('html'),val))
  })
},
style(val,fce){
/* Установка стиля */
  Modifi($('@'+fce,this.elt),function(elt){
    elt.style(val)
  })
},
class(val,fce){
/* Инверсия css класса */
  val=val.split(' ');
  Modifi($('@'+fce,this.elt),function(elt){
    for(let nam of val)elt.class(nam)
  })
},
attr(val,fce){
/* Изменение атрибута */
  let prm=Fce(fce);
  val=Valid(val,'String').split('|');
  Modifi($('@'+fce,this.elt),function(elt){
    let num=val.indexOf(elt.attr(prm[0]))+1;
    if(num>=val.length)num=0;
    elt.attr(prm[0],val[num])
  })
},
enable(val,fce){
/* Активность элемента */
  val=Valid(!val,'Boolean');
  Modifi($('@'+fce,this.elt),function(elt){
    elt.$.disabled=val
  })
},
checked(val,fce){
/* Флажок radio|checkbox */
  val=Valid(val,'Boolean');
  Modifi($('@'+fce,this.elt),function(elt){elt=elt.$;
    elt.checked=val!=undefined?val:!elt.checked;
  })
},
value(val,fce){
/* Установка значения */
  if(val==null)
    return $('@'+fce,this.elt).value();
  else{val=Valid(val,'String');
    Modifi($('@'+fce,this.elt),function(elt){
      let etp=Type(elt.$);
      if(etp=='object HTMLInputElement' ||
        etp=='object HTMLTextAreaElement'){elt.value(val);
        elt.run(['change','keyup','keydown','keypress'])
      }
    })
  }
},
location(elt){
/* Локация */
  let val=elt.value, nam=$(elt).attr('name');
  if(nam && val){val=nam+'='+val;
    let url=location.search;
    let mth=url.match(new RegExp(nam+'=[^&]+'));
    if(mth)url=url.replace(mth[0],val);
    else if(url)url+='&'+val;else url='?'+val;
    location.search=url
  }
}
}
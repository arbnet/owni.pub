import {Type} from './Functions.js';

export var NewTag={
/* Новый тэг */
[Symbol.toStringTag]:'NewTag',
[Symbol.for('Type')]:'owni',
include(...arg){
/* Включение новых тэгов */
  for(let val of arg)
    Load('script/newtags/'+val+'.tag')
    .then(tag=>tag.text()).then(tag=>this.create(tag))
},
create(tgs){
/* Создание новых тегов */
  let body=document.createElement('body');body.innerHTML=tgs;
  for(let elt of body.children){let tag=elt.localName;
    if(!customElements.get(tag)){
      //let Func=Object.create(null);let val;
      //, Attr=[], Extn='HTMLElement';
      //val=elt.getAttribute('attributes');
      //if(val)Attr=val.split(',');
      //val=elt.getAttribute('extends');
      //if(val)let Extn=document.createElement(val).toString().match(/HTML(\w+)/)[0];
      customElements.define(tag,class extends HTMLElement {
        constructor(){super();
          this.attachShadow({mode:'open'})
        }
        connectedCallback(){
          this.shadowRoot.innerHTML=elt.innerHTML;

        }
      });
    }
  }
}};


/* let Class=class extends HTMLElement {
          Func=Func;
          static get observedAttributes(){return Attr} 
          connectedCallback(){
            this.attachShadow({mode:'open'});
            this.shadowRoot.innerHTML=elt.innerHTML;
            if('slotchange' in this.Func){
              let els=this.shadowRoot.querySelectorAll('slot')
              for(let slt of els)
                slt.addEventListener('slotchange',this.Func.slotchange)
            }
          }
          attributeChangedCallback(att,ovl,nvl){
            if(att in this.Func)this.Func[att].call(this,ovl,nvl)
          };
        }; */
        //customElements.define(tag,Class);
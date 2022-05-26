var Variables={
$:new Map,
get length(){return this.$.size},
get keys(){let cks=[];
  for(let val of this.$.keys())cks.push(val)
  return cks
},
clear(){this.clear()},
delete(key){this.$.delete(key)},
get(key){return this.$.get(key)},
set(key,val){this.$.set(key,val)},
key(num){
  if(typeof(num)=='number'){let cks=this.keys;
    num=Math.abs(Math.trunc(num));
    if(num<cks.length)return cks[num]
  }
}};

var Cookie={
options:null,
init(){this.$=new Map;
  let mth=document.cookie.match(new RegExp('[^; =]+=[^;]+','g'));
  for(let val of mth){val=val.split('=');this.$.set(val[0],val[1])}
},
get length(){return this.$.size},
get keys(){let cks=[];
  for(let val of this.$.keys())cks.push(val)
  return cks
},
getItem(key){return this.$.get(key)},
removeItem(key){key=encodeURIComponent(key);
  if(this.getItem(key)){this.$.delete(key);
    document.cookie=key+'=""; max-age:-1';
  }
},
setItem(key,val){
  if(key && val){
    val=encodeURIComponent(key)+'='+encodeURIComponent(val);
    for(let key in this.options){let opt;
      switch(key){
        case 'expires':
          if(this.options.expires instanceof Date)
            opt=this.options.expires.toUTCString();
          break
        default: 
          opt=this.options[key];if(opt)opt='='+opt;
      }
      if(opt)val+='; '+opt;
    }
    document.cookie=val
  }
},
clear(){let cks=this.keys;
  for(let key of cks)this.removeItem(key)
},
key(num){
  if(typeof(num)=='number'){let cks=this.keys;
    num=Math.abs(Math.trunc(num));
    if(num<cks.length)return cks[num]
  }
}};
Cookie.init();

export default {
/* Объект Держателя */
[Symbol.toStringTag]:'Keep',
[Symbol.for('default')]:'$k',
[Symbol.for('Type')]:'owni',
$:null,// подобъект
get count(){return this.$.length},
get coo(){this.i='coo';this.$=Cookie;return this},
get var(){this.i='var';this.$=Variables;return this},
get loc(){this.i='loc';this.$=localStorage;return this},
get ses(){this.i='ses';this.$=sessionStorage;return this},
get keys(){return 'keys' in this.$?this.$.keys:Object.keys(this.$)},
clear(){this.$.clear()},
key(num){return this.$.key(num)},
delete(key){this.$.removeItem(key)},
get(key){return this.$.getItem(key)},
set(key,val){this.$.setItem(key,val)}};
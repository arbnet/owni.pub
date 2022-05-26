Array.prototype.delete=function(val){
  let num=this.indexOf(val); 
  if(num!=-1)this.splice(num,1);
};
Object.defineProperties(Array.prototype,{
  unique:{
    get: function(){let arr=[];
      for(let val of this)
        if(!arr.includes(val))arr.push(val);
      return arr
    }
  }
})
String.prototype.ucFirst=function(){
  return this[0].toUpperCase()+this.slice(1);
}
String.prototype.replaceFrom=function(str,rep,num=0){
  if(num>-1 && num<this.length){
    return this.substr(0,num)+this.substr(num).replace(str,rep)
  }
}
String.prototype.replaceAll=function(src,rpl){
  return this.split(src).join(rpl);
}
Date.prototype.days=['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'];
Date.prototype.months=['Январь','Февраль','Март','Май','Апрель','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
Date.prototype.format=function(msk,utc){
  utc=utc?'getUTC':'get';
  //msk='DD mm Yг. h:n:s T';
  let mth=[...msk.matchAll(/(\w)(\1*)/g)];
  for(let fdt of mth){let rpl;fdt=fdt[0];
    switch(fdt){
      case 'f': case 'F':
        ft=fdt=='F'?true:false;break
      case 'd': case 'D':
        rpl=this[utc+'Date']();
        if(fdt=='D')if(rpl<10)rpl='0'+rpl;
        break
      case 'DD':
        rpl=this.days[this[utc+'Day']()];
        break
      case 'm': case 'M': case 'mm': case 'MM':
        rpl=this[utc+'Month']()+1;
        if(fdt=='M'){if(rpl<10)rpl='0'+rpl}
        else if(fdt!='m'){rpl=this.months[rpl];
          if(fdt=='mm')rpl=rpl.substr(0,3)
        }
        break
      case 'y': case 'Y':
        rpl=this[utc+'FullYear']();
        if(fdt=='y')rpl=rpl.toString().substr(-2);
        break
      case 'h': case 'o': case 'H': case 'O':
        rpl=this[utc+'Hours']();
        if(fdt=='h')if(rpl>12)rpl-=12;
        if(fdt=='H' || fdt=='O')if(rpl<10)rpl='0'+rpl;
        break
      case 'n': case 'N':
        rpl=this[utc+'Minutes']();
        if(fdt=='N')if(rpl<10)rpl='0'+rpl;
        break
      case 's': case 'S':
        rpl=this[utc+'Seconds']();
        if(fdt=='S')if(rpl<10)rpl='0'+rpl;
        break
      case 'i': case 'I':
        rpl=this[utc+'Milliseconds']();
        if(fdt=='I'){rpl=rpl.toString();
          while(rpl.length<3)rpl='0'+rpl;
        }
        break
      case 't': case 'T':
        rpl=this[utc+'Hours']();
        if(rpl<12)rpl='am';
        else{
          if(rpl>12)rpl='pm';
          else{
            rpl=this[utc+'Minutes']()+this[utc+'Seconds']()+
            this[utc+'Milliseconds']();rpl=rpl==0?'am':'pm'
          }
        }
        if(fdt=='T')rpl=rpl.toUpperCase();
    }
    if(rpl)msk=msk.replace(fdt,rpl)
  }
	return msk
};
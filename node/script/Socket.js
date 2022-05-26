

export default {/* Сокет */
[Symbol.for('default')]:'Socket',
[Symbol.toStringTag]:'Socket',
[Symbol.for('Type')]:'owni',
socs:new Map(),// сокеты
get size(){return this.socs.size},
create(key,url){
  this.socs.set(key,new WebSocket(url))
},
message(key,fnc){
  let soc=this.get(key);
  if(soc)soc.onmessage=fnc;
},
open(key,fnc){
  let soc=this.get(key);
  if(soc)soc.onopen=fnc;
},
error(key,fnc){
  let soc=this.get(key);
  if(soc)soc.onerror=fnc;
},
close(key,fnc){
  let soc=this.get(key);
  if(soc)soc.onclose=fnc;
}
}

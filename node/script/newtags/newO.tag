
  <my-tag1 attributes="title">
    <style>
      :host {color: green}
      ::slotted(h1) {cursor: pointer;}
    </style>
    <h1 nick="title">Заголовок</h1>
    <ul>
    <slot></slot>
    </ul>
    <function name="slotchange">
        console.log(event);console.log(this)
    </function>   
    <function name="title">
      let elt=this.querySelector('[name="item"]');
      elt.innerText=nvl
    </function>
  </my-tag1>
  <my-tag2>
    <div slot="dd" style="border: 1px solid"></div>
  </my-tag2>

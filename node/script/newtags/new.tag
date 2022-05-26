
<o-form>
  <style>
    :host {
      display: flex;
      flex-wrap: wrap;
      justify-content: flex-end;
    }
    ::slotted(*) {
      flex-grow: 1;
      margin: 0.3em;
      height: 1.8em;
      min-width: 2em;
      padding: 0.3em;
      font-size: 1rem;
      box-sizing: border-box;
      border-top-left-radius: 0.3em;
      border-top-right-radius: 0.3em;
      border-bottom-left-radius: 0.3em;
      border-bottom-right-radius: 0.3em;
      border: var(--brd) solid var(--edge);
    }
    ::slotted(button) {
      flex: 0 1 auto;
    }
    ::slotted(textarea) {
      min-height: 3.6em;
      flex-basis: 100%;
    }
  </style>
  <slot></slot>
</o-form>
import Srcset from "./components/blocks/Srcset.vue";

//https://github.com/getkirby/editor/blob/master/src/components/Plugins.js
if (window.editor) {
  editor.block("srcset", Srcset);
}

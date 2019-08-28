import Srcset from './components/blocks/srcset.vue';

// editor.block('srcset', { // not bnomei/srcset
//   label: 'Srcset',
//   icon: 'image', // defined in srcset.vue export > defaults
//   template: '<p>How to load template from imported file?</p>' // how will this be loaded from import?
// });

// TODO: how to load component? like this?
// https://github.com/getkirby/editor/blob/master/src/components/Plugins.js
editor.block('srcset', Srcset);

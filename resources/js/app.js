/**
 * ogaland HTML生成用JSファイル（Vue.js）
 */


 window.Vue = require('vue');

 import Vuetify from 'vuetify';
 import 'vuetify/dist/vuetify.min.css';
 import ja from 'vuetify/es5/locale/ja.js'
 import VueRouter from 'vue-router';
 import router from '@/router/router.js';
 import '@mdi/font/css/materialdesignicons.css'
 import '@fortawesome/fontawesome-free/css/all.css';
 import AxiosPlugin from './plugins/axios';          // axiosの自作プラグイン読込
import { Utils } from './plugins/utils.js';          // axiosの自作プラグイン読込
import store from "@/store/index.js";
import VueQuillEditor from 'vue-quill-editor'
import 'quill/dist/quill.core.css' // import styles
import 'quill/dist/quill.bubble.css' // for bubble theme

 //Vue.js拡張ライブラリの追加
 Vue.use(Vuetify);
 Vue.use(VueRouter);
 Vue.use(AxiosPlugin, { store, router });
 Vue.use(VueQuillEditor,{
  // quillEditorのオプション
  theme: 'bubble',
  placeholder: "",
  modules: {
      toolbar: [
      [{ 'size': ['small', false, 'large', 'huge'] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ 'color': [] }, { 'background': [] }],
      [{ 'align': [] }],
      ['clean']
      ]
  },
  formats:[
      'background',
      'bold',
      'color',
      'font',
      'code',
      'italic',
      'link',
      'size',
      'strike',
      'script',
      'underline',
      'blockquote',
      'header',
      'indent',
      'list',
      'align',
      'direction',
      'code-block',
      'formula'
  ],
 })
Vue.prototype.Utils = Utils;
//  Vue.use( CKEditor );

 //利用コンポーネントの登録(ベースコンポーネント)
 Vue.component("com-navigation", require("@/_BaseComponents/Navigation.vue").default);

//  全画面利用コンポーネント
 Vue.component("com-copy", require("@/_CommonComponents/copy.vue").default);
 Vue.component("com-clear", require("@/_CommonComponents/clear.vue").default);
 Vue.component("com-delete", require("@/_CommonComponents/delete.vue").default);
 Vue.component("com-cancel", require("@/_CommonComponents/cancel.vue").default);
 Vue.component("saving-dialog", require("@/_CommonComponents/SavingDialogComponent.vue").default);
 Vue.component("img-dialog", require("@/_CommonComponents/imgDialogComponent.vue").default);


 /**
  * Next, we will create a fresh Vue application instance and attach it to
  * the page. Then, you may begin adding components to this application
  * or customize the JavaScript scaffolding to fit your unique needs.
  */
 Vue.config.productionTip = false


 const app = new Vue({
   el: '#app',
   router,
   store: store,
   vuetify: new Vuetify({
     lang: {
       locales: { ja },
       current: 'ja'
     },
     icons: {
       iconfont: 'mdi', // default - only for display purposes
     },
     theme: {
       themes: {
         light: {
           primary: "#ff7800",
           secondary: "#ff9800",
           accent: "#673ab7",
           error: "#f44336",
           warning: "#ffc107",
           info: "#ffc107",
           success: "#4caf50"
         }

       }
     }
   })
 });

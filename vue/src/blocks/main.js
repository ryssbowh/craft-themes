import { createApp } from 'vue'
import BlocksToolbar from './components/BlocksToolbar.vue';
import BlocksContext from './components/BlocksContext.vue';
import BlocksMenu from './components/BlocksMenu.vue';
import LayoutModal from './components/LayoutModal'
import Blocks from './components/Blocks.vue';
import Lightswitch from '../forms/Lightswitch.vue';
import { store } from './stores/store.js';

const app = createApp({
    components: {
        Blocks,
        BlocksContext,
        BlocksToolbar,
        BlocksMenu
    }
});
app.use(store);

app.component('layout-modal', LayoutModal);
app.component('lightswitch', Lightswitch);

let event = new CustomEvent("register-block-option-components", {detail: {}});
document.dispatchEvent(event);

for (let name in event.detail) {
    app.component(name, event.detail[name]);
}

event = new CustomEvent("register-block-strategy-components", {detail: {}});
document.dispatchEvent(event);

for (let name in event.detail) {
    app.component('strategy-'+name, event.detail[name]);
}

const Translate = {
  install(app) {
    app.config.globalProperties.t = (str, params, category = 'themes') => {
      return window.Craft.t(category, str, params);
    }
  },
};
const HandleError = {
  install(app) {
    app.config.globalProperties.handleError = (err) => {
      let message = err;
      if (err.response) {
        if (err.response.data.message ?? null) {
          message = err.response.data.message;
        } else if (err.response.data.error ?? null) {
          message = err.response.data.error;
        }
      }
      Craft.cp.displayError(message);
    }
  }
};

app.use(Translate);
app.use(HandleError);

app.mount('#main');

import { createApp } from 'vue'
import BlocksToolbar from './components/BlocksToolbar.vue';
import BlocksContext from './components/BlocksContext.vue';
import Blocks from './components/Blocks.vue';
import { store } from './stores/store.js';

const app = createApp({
  components: {
    Blocks,
    BlocksContext,
    BlocksToolbar
  }
});
app.use(store);

for (let name in window.themesBlockOptionComponents) {
    app.component(name, window.themesBlockOptionComponents[name]);
}

const Translate = {
  install(app) {
    app.config.globalProperties.t = (str, params) => {
      return window.Craft.t('themes', str, params);
    }
  },
}
app.use(Translate);

app.mount('#main');

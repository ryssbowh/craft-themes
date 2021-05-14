import { createApp } from 'vue'
import { store } from './stores/store.js';
import DisplayContext from './components/DisplayContext.vue';
import Displays from './components/Displays.vue';
import DisplayToolbar from './components/DisplayToolbar.vue';
import DisplayTabs from './components/DisplayTabs.vue';
import DisplayMenu from './components/DisplayMenu.vue';

const app = createApp({
  components: {
    Displays,
    DisplayContext,
    DisplayToolbar,
    DisplayTabs,
    DisplayMenu,
  }
});
app.use(store);

const Translate = {
  install(app) {
    app.config.globalProperties.t = (str, params) => {
      return window.Craft.t('themes', str, params);
    }
  },
}
app.use(Translate);

app.mount('#main');

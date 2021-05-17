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

for (let name in window.displayersOptionComponents) {
    app.component(name, window.displayersOptionComponents[name]);
}

const Translate = {
  install(app) {
    app.config.globalProperties.t = (str, params) => {
      return window.Craft.t('themes', str, params);
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

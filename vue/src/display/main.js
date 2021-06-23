import { createApp } from 'vue'
import { store } from './stores/store.js';
import DisplayContext from './components/DisplayContext.vue';
import Displays from './components/Displays.vue';
import DisplayToolbar from './components/DisplayToolbar.vue';
import DisplayTabs from './components/DisplayTabs.vue';
import DisplayMenu from './components/DisplayMenu.vue';
import DisplayItem from './components/DisplayItem.vue';
import OptionsModal from './components/OptionsModal.vue';
import ViewModeModal from './components/ViewModeModal.vue';
import Field from './components/Field.vue';
import Group from './components/Group.vue';
import Draggable from 'vuedraggable';

const app = createApp({
  components: {
    Displays,
    DisplayContext,
    DisplayToolbar,
    DisplayTabs,
    DisplayMenu
  }
});
app.use(store);
app.component('field', Field);
app.component('view-mode-modal', ViewModeModal);
app.component('draggable', Draggable);
app.component('group', Group);
app.component('options-modal', OptionsModal);
app.component('display-item', DisplayItem,);

for (let name in window.themesFieldDisplayersComponents) {
    app.component('fieldDisplayer-' + name, window.themesFieldDisplayersComponents[name]);
}

for (let name in window.themesFileDisplayersComponents) {
    app.component('fileDisplayer-' + name, window.themesFileDisplayersComponents[name]);
}

for (let name in window.themesFieldsComponents) {
    app.component('field-' + name, window.themesFieldsComponents[name]);
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

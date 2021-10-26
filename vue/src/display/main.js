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
import GroupModal from './components/GroupModal.vue';
import Lightswitch from '../forms/Lightswitch.vue';
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
app.component('group-modal', GroupModal);
app.component('display-item', DisplayItem);
app.component('lightswitch', Lightswitch);

let event = new CustomEvent("register-field-displayers-components", {detail: {}});
document.dispatchEvent(event);

for (let name in event.detail) {
    app.component('fieldDisplayer-' + name, event.detail[name]);
}

event = new CustomEvent("register-file-displayers-components", {detail: {}});
document.dispatchEvent(event);

for (let name in event.detail) {
    app.component('fileDisplayer-' + name, event.detail[name]);
}

event = new CustomEvent("register-fields-components", {detail: {}});
document.dispatchEvent(event);
//Global object containing clone functions for bespoke field types
window.cloneField = {};

for (let name in event.detail) {
    app.component('field-' + name, event.detail[name].component);
    window.cloneField[name] = event.detail[name].clone;
}

const FieldComponents = {
  install(app) {
    app.config.globalProperties.fieldComponents = () => {
      return Object.keys(event.detail);
    }
  },
};

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
app.use(FieldComponents);

app.mount('#main');

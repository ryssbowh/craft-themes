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
import Field from './components/Field.vue';
import Group from './components/Group.vue';
import Draggable from 'vuedraggable';

import Lightswitch from '../forms/Lightswitch.vue';
import Select from '../forms/Select.vue';
import Text from '../forms/Text.vue';
import DateField from '../forms/Date.vue';
import Time from '../forms/Time.vue';
import Color from '../forms/Color.vue';
import DateTime from '../forms/DateTime.vue';
import Textarea from '../forms/Textarea.vue';
import MultiSelect from '../forms/MultiSelect.vue';
import Checkboxes from '../forms/Checkboxes.vue';
import Radio from '../forms/Radio.vue';
import ViewModes from '../forms/ViewModes.vue';
import FileDisplayers from '../forms/FileDisplayers.vue';

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

app.component('formfield-lightswitch', Lightswitch);
app.component('formfield-select', Select);
app.component('formfield-text', Text);
app.component('formfield-date', DateField);
app.component('formfield-time', Time);
app.component('formfield-color', Color);
app.component('formfield-datetime', DateTime);
app.component('formfield-textarea', Textarea);
app.component('formfield-multiselect', MultiSelect);
app.component('formfield-checkboxes', Checkboxes);
app.component('formfield-radio', Radio);
app.component('formfield-viewmodes', ViewModes);
app.component('formfield-filedisplayers', FileDisplayers);


let event = new CustomEvent("register-fields-components", {detail: {}});
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

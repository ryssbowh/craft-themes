import { createApp } from 'vue'
import BlocksToolbar from './components/BlocksToolbar.vue';
import BlocksContext from './components/BlocksContext.vue';
import BlocksMenu from './components/BlocksMenu.vue';
import LayoutModal from './components/LayoutModal'
import Blocks from './components/Blocks.vue';
import { store } from './stores/store.js';

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
import FetchViewMode from '../forms/FetchViewMode.vue';
import Elements from '../forms/Elements.vue';

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
app.component('formfield-fetchviewmode', FetchViewMode);
app.component('formfield-elements', Elements);

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

import { createApp } from 'vue'
import { createStore } from './stores/store.js';
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
import { Translate, HandleError, Clone, FieldComponent } from '../Helpers.js';

const app = createApp({
  components: {
    Displays,
    DisplayContext,
    DisplayToolbar,
    DisplayTabs,
    DisplayMenu
  }
});

const store = createStore(app);
app.use(store);
app.use(Translate);
app.use(HandleError);
app.use(FieldComponent);
app.use(Clone);
app.component('field', Field);
app.component('view-mode-modal', ViewModeModal);
app.component('draggable', Draggable);
app.component('group', Group);
app.component('options-modal', OptionsModal);
app.component('group-modal', GroupModal);
app.component('display-item', DisplayItem);

for (let name in window.CraftThemes.formFieldComponents) {
  app.component('formfield-' + name, window.CraftThemes.formFieldComponents[name]);
}

for (let name in window.CraftThemes.fieldComponents) {
    app.component('field-' + name, window.CraftThemes.fieldComponents[name].component);
}

app.mount('#main');

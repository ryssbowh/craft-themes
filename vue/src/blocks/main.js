import { createApp } from 'vue'
import BlocksToolbar from './components/BlocksToolbar.vue';
import BlocksContext from './components/BlocksContext.vue';
import BlocksMenu from './components/BlocksMenu.vue';
import LayoutModal from './components/LayoutModal'
import Blocks from './components/Blocks.vue';
import { store } from './stores/store.js';
import { Translate, HandleError } from '../Helpers.js';

const app = createApp({
    components: {
        Blocks,
        BlocksContext,
        BlocksToolbar,
        BlocksMenu
    }
});
app.use(store);
app.use(Translate);
app.use(HandleError);
app.component('layout-modal', LayoutModal);

for (let name in window.CraftThemes.formFieldComponents) {
  app.component('formfield-' + name, window.CraftThemes.formFieldComponents[name]);
}

app.mount('#main');

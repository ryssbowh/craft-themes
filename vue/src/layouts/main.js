import { createApp } from 'vue'
import LayoutsToolbar from './components/LayoutsToolbar.vue';
import LayoutsContext from './components/LayoutsContext.vue';
import LayoutBlocks from './components/LayoutBlocks.vue';
import { store } from './stores/store.js';

const app = createApp({
  components: {
    LayoutBlocks,
    LayoutsContext,
    LayoutsToolbar
  }
});
app.use(store);

app.mount('#main');

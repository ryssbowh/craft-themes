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

app.mount('#main');

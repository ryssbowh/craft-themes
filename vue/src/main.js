import { createApp } from 'vue'
import BlocksToolbar from './components/BlocksToolbar.vue';
import ThemesContext from './components/ThemesContext.vue';
import Blocks from './components/Blocks.vue';
import { store } from './stores/BlocksStore.js';

const app = createApp({
  components: {
    Blocks,
    ThemesContext,
    BlocksToolbar
  }
});
app.use(store);

app.mount('#main');

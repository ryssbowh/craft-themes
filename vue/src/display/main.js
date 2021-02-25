import { createApp } from 'vue'
import { store } from './stores/store.js';
import DisplayContext from './components/DisplayContext.vue';
import DisplayFields from './components/DisplayFields.vue';
import DisplayToolbar from './components/DisplayToolbar.vue';
import DisplayTabs from './components/DisplayTabs.vue';
import DisplayMenu from './components/DisplayMenu.vue';

const app = createApp({
  components: {
    DisplayFields,
    DisplayContext,
    DisplayToolbar,
    DisplayTabs,
    DisplayMenu,
  }
});
app.use(store);

app.mount('#main');

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

let event = new CustomEvent("register-block-option-components", {detail: {}});
document.dispatchEvent(event);

for (let name in event.detail) {
    app.component(name, event.detail[name]);
}

event = new CustomEvent("register-block-strategy-components", {detail: {}});
document.dispatchEvent(event);

for (let name in event.detail) {
    app.component('strategy-'+name, event.detail[name]);
}

const Translate = {
    install(app) {
        app.config.globalProperties.t = (str, params) => {
            return window.Craft.t('themes', str, params);
        }
    },
}
app.use(Translate);

app.mount('#main');

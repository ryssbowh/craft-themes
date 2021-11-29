<template>
    <div class="btngroup">
        <button v-if="theme" type="button" class="btn menubtn" data-icon="brush">{{ themes[theme].name }}</button>
        <div v-if="theme" class="menu">
            <ul class="padded">
                <li v-for="theme2 in themes" v-bind:key="theme2.handle"><a :class="{sel: theme == theme2.handle}" href="#" @click.prevent="checkAndSetTheme(theme2.handle)">{{ theme2.name }}</a></li>
            </ul>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';

export default {
    computed: {
        ...mapState(['layouts', 'layout', 'theme'])
    },
    props: {
        initialTheme: String,
        initialLayout: Number,
        themes: Object,
        availableLayouts: Object,
        allLayouts: Object,
        allStrategies: Object,
        showFieldHandles: Number
    },
    created () {
        this.setShowFieldHandles(this.showFieldHandles);
        this.setCacheStrategies(this.allStrategies);
        this.setThemes(this.themes);
        this.setAllLayouts(this.allLayouts);
        if (this.initialTheme) {
            this.setTheme(this.initialTheme);
        }
        if (this.initialLayout) {
            let layout = this.getLayout(this.initialLayout);
            if (layout.hasBlocks) {
                this.setLayoutAndFetch(this.initialLayout);
            } else {
                layout = this.getDefaultLayout();
                this.setLayoutAndFetch(layout.id);
            }
        }
        window.addEventListener('popstate', () => {
            const url = document.location.pathname.split('/');
            let i = url.findIndex(e => e == 'blocks');
            if (i !== -1) {
                this.setTheme(url[i+1]);
            }
            if (typeof url[i+2] != 'undefined') {
                this.setLayoutAndFetch(url[i+2]);
            } else {
                layout = this.getDefaultLayout();
                this.setLayoutAndFetch(layout.id);
            }
        });
    },
    methods: {
        getLayout: function (id) {
            for (let i in this.layouts) {
                if (this.layouts[i].id == id) {
                    return this.layouts[i];
                }
            }
        },
        getDefaultLayout: function () {
            for (let i in this.layouts) {
                if (this.layouts[i].type == 'default') {
                    return this.layouts[i];
                }
            }
        },
        checkAndSetTheme: function (theme) {
            if (confirm(this.t('You will loose unsaved changes, continue anyway ?'))) {
                this.setThemeAndFetch(theme);
            }
        },
        ...mapMutations(['setThemes', 'setAllLayouts', 'setAvailableLayouts', 'setTheme', 'setCacheStrategies', 'setShowFieldHandles']),
        ...mapActions(['setLayoutAndFetch', 'setThemeAndFetch']),
    }
};
</script>

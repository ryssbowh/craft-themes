<template>
    <div class="btngroup">
        <button v-if="theme" type="button" class="btn menubtn" data-icon="brush">{{ themes[theme].name }}</button>
        <div v-if="theme" class="menu">
            <ul class="padded">
                <li v-for="theme2 in themes" v-bind:key="theme2.handle">
                    <a :class="{sel: theme == theme2.handle}" href="#" @click.prevent="changeTheme(theme2.handle)">{{ theme2.name }}</a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';

export default {
    computed: {
        ...mapState(['theme', 'layouts', 'layout', 'viewMode'])
    },
    props: {
        initialTheme: String,
        themes: Object,
        allLayouts: Object,
        currentLayout: Number,
        currentViewModeHandle: String,
        showFieldHandles: Number
    },
    created () {
        this.setShowFieldHandles(this.showFieldHandles);
        this.setAllLayouts(this.allLayouts);
        this.setTheme(this.initialTheme);
        let layoutExists = (this.currentLayout && this.allLayouts[this.initialTheme].filter((l) => l.id == this.currentLayout).length);
        if (layoutExists) {
            this.setLayout({layoutId: this.currentLayout, viewModeHandle: this.currentViewModeHandle});
        } else if (this.layouts[0] ?? null) {
            this.setLayout({layoutId: this.layouts[0].id});
        }
        if (!layoutExists && this.currentLayout) {
            Craft.cp.displayError(this.t('Requested layout doesn\'t exist, defaulting to {layout}', {layout: this.layouts[0].description}));
        }
        window.addEventListener('popstate', () => {
            const url = document.location.pathname.split('/');
            let i = url.findIndex(e => e == 'display');
            let viewModeHandle = '';
            let layoutId;
            if (i === -1) {
                return;
            }
            if (typeof url[i+1] != 'undefined' && url[i+1] != this.theme) {
                this.setTheme(url[i+1]);
            }
            if (typeof url[i+2] != 'undefined' && url[i+2] != this.layout.id) {
                layoutId = url[i+2];
            }
            if (typeof url[i+3] != 'undefined') {
                viewModeHandle = url[i+3];
            }
            if (layoutId) {
                this.setLayout({layoutId: url[i+2], viewModeHandle: viewModeHandle});
            } else if (viewModeHandle) {
                this.setViewModeByHandle(viewModeHandle);
            }
        });
    },
    methods: {
        changeTheme: function (theme) {
            if (!confirm(this.t('You will loose unsaved changes, continue anyway ?'))) {
                return;
            }
            let layoutElement = this.layout.element;
            this.setTheme(theme);
            for (let i in this.layouts) {
                if (this.layouts[i].element === layoutElement) {
                    this.setLayout({layoutId: this.layouts[i].id});
                    return;
                }
            }
            this.setLayout({layoutId: this.layouts[0].id});
        },
        ...mapMutations(['setTheme', 'setAllLayouts', 'setShowFieldHandles']),
        ...mapActions(['setLayout', 'setViewModeByHandle']),
    }
};
</script>

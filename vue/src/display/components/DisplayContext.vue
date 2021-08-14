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
        ...mapState(['theme', 'layouts', 'layout'])
    },
    props: {
        initialTheme: String,
        themes: Object,
        allLayouts: Object,
        currentLayout: Number
    },
    created () {
        this.setAllLayouts(this.allLayouts);
        if (this.initialTheme) {
            this.setTheme(this.initialTheme);
        }
        if (this.currentLayout) {
            this.setLayout(this.currentLayout);
        } else {
            this.setLayout(this.layouts[0].id);
        }
        window.addEventListener('popstate', () => {
            const url = document.location.pathname.split('/');
            let i = url.findIndex(e => e == 'display');
            if (i === -1) {
                return;
            }
            if (typeof url[i+1] != 'undefined' && url[i+1] != this.theme) {
                this.setTheme(url[i+1]);
            }
            if (typeof url[i+2] != 'undefined') {
                this.setLayout(url[i+2]);
            } else {
                this.setLayout(this.layouts[0].id);
            }
        });
    },
    methods: {
        changeTheme: function (theme) {
            let layoutElement = this.layout.element;
            this.setTheme(theme);
            for (let i in this.layouts) {
                if (this.layouts[i].element === layoutElement) {
                    this.setLayout(this.layouts[i].id);
                    return;
                }
            }
            this.setLayout(this.layouts[0].id);
        },
        ...mapMutations(['setTheme', 'setAllLayouts']),
        ...mapActions(['setLayout']),
    }
};
</script>

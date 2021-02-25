<template>
    <nav id="notification-nav">
        <ul>
            <li v-for="layout2 in allLayouts" v-bind:key="layout2.handle">
                <a href="#" :class="{'sel': layout === layout2.handle}" @click.prevent="setLayout(layout2.handle)">{{ layout2.description }}</a>
            </li>
        </ul>
    </nav>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Mixin from '../../mixin';

export default {
    computed: {
        ...mapState(['layout'])
    },
    props: {
        allLayouts: Object,
        currentLayout: String
    },
    created () {
        if (this.currentLayout) {
            this.setLayout(this.currentLayout);
        }
    },
    methods: {
        ...mapMutations([]),
        ...mapActions(['setLayout']),
    },
    mixins: [Mixin],
};
</script>

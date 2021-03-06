<template>
    <nav id="notification-nav">
        <ul>
            <li v-for="layout2 in themeLayouts" v-bind:key="layout2.id">
                <a href="#" :class="{'sel': layout.id === layout2.id}" @click.prevent="setLayoutAndFetch(layout2.id)">{{ layout2.description }}</a>
            </li>
        </ul>
    </nav>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Mixin from '../../mixin';

export default {
    computed: {
        themeLayouts: function () {
            return this.layouts.filter(layout => layout.hasFields);
        },
        ...mapState(['layout', 'layouts'])
    },
    props: {
        
    },
    created () {

    },
    methods: {
        ...mapMutations(['setAllLayouts']),
        ...mapActions(['setLayoutAndFetch']),
    },
    mixins: [Mixin],
};
</script>

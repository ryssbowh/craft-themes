<template>
    <nav id="notification-nav">
        <ul>
            <li v-for="layout2 in layouts" v-bind:key="layout2.id">
                <a href="#" :class="{'sel': layout.id === layout2.id}" @click.prevent="confirmAndChangeLayout(layout2.id)">{{ layout2.description }}</a>
            </li>
        </ul>
    </nav>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';

export default {
    computed: {
        ...mapState(['layout', 'layouts', 'hasChanged'])
    },
    methods: {
        confirmAndChangeLayout: function (id) {
            if (this.hasChanged && !confirm(this.t('You have unchanged changes, continue anyway ?'))) {
                return;
            }
            this.setLayoutAndFetch(id);
        },
        ...mapMutations([]),
        ...mapActions(['setLayoutAndFetch']),
    }
};
</script>

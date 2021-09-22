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
import { mapState, mapActions } from 'vuex';

export default {
    computed: {
        ...mapState(['layout', 'layouts', 'hasChanges'])
    },
    methods: {
        confirmAndChangeLayout: function (id) {
            if (this.hasChanges && !confirm(this.t('You have unchanged changes, continue anyway ?'))) {
                return;
            }
            this.setLayout(id);
        },
        ...mapActions(['setLayout']),
    }
};
</script>

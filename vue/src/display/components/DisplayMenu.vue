<template>
    <nav id="notification-nav">
        <ul>
            <li class="heading"><span>{{ t('Layouts') }}</span></li>
            <li v-for="layout2 in layouts" v-bind:key="layout2.id">
                <a :href="getUrl(layout2)" :class="{'sel': layout.id === layout2.id}" @click.prevent="confirmAndChangeLayout(layout2.id)">{{ layout2.description }}</a>
            </li>
        </ul>
    </nav>
</template>

<script>
import { mapState, mapActions } from 'vuex';

export default {
    computed: {
        ...mapState(['layout', 'layouts', 'theme'])
    },
    methods: {
        getUrl(layout) {
            return Craft.getCpUrl('themes/display/' + this.theme + '/' + layout.id);
        },
        confirmAndChangeLayout: function (id) {
            if (!confirm(this.t('You will loose unsaved changes, continue anyway ?'))) {
                return;
            }
            this.setLayout({layoutId: id});
        },
        ...mapActions(['setLayout']),
    }
};
</script>
<style lang="scss" scoped>
    .heading {
    margin: 11px 24px 14px 24px;
    }
</style>
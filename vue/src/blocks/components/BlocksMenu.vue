<template>
    <nav id="notification-nav">
        <ul>
            <li class="heading"><span>{{ t('Layouts') }}</span></li>
            <li v-for="layout2, index in layoutsWithBlocks" v-bind:key="index">
                <a href="#" :class="{'sel': layout.uid === layout2.uid}" @click.prevent="confirmAndChangeLayout(layout2.id)">{{ layout2.description }}</a>
            </li>
        </ul>
    </nav>
</template>

<script>
import { mapState, mapActions, mapMutations } from 'vuex';

export default {
    computed: {
        layoutsWithBlocks: function () {
            return this.layouts.filter(layout => layout.hasBlocks);
        },
        ...mapState(['layout', 'layouts', 'hasChanged'])
    },
    methods: {
        confirmAndChangeLayout: function (id) {
            if (this.hasChanged) {
                if (confirm(this.t('You have unsaved changes, continue anyway ?'))) {
                    this.setLayoutAndFetch(id);
                }
            } else {
                this.setLayoutAndFetch(id);
            }
        },
        ...mapMutations([]),
        ...mapActions(['setLayoutAndFetch'])
    }
};
</script>
<style lang="scss" scoped>
    .heading {
    margin: 0 24px 14px 24px;
    }
</style>
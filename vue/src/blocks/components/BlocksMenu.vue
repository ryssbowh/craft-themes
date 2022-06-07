<template>
    <nav id="notification-nav">
        <ul>
            <li class="heading">
                <span>{{ t('Layouts') }}</span>
            </li>
            <li
                v-for="layout2, index in layoutsWithBlocks"
                :key="index"
            >
                <a
                    href="#"
                    :class="{'sel': layout.uid === layout2.uid}"
                    @click.prevent="confirmAndChangeLayout(layout2.id)"
                >{{ layout2.description }}</a>
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
        ...mapState(['layout', 'layouts'])
    },
    watch: {
        layout: function () {
            $('#selected-sidebar-item-label').html(this.layout.description);
        }
    },
    created() {
        this.$nextTick(() => {
            Craft.cp.addListener($('#sidebar-toggle'), 'click', 'toggleSidebar');
            if (this.layout) {
                $('#selected-sidebar-item-label').html(this.layout.description);
            };
        });
    },
    methods: {
        confirmAndChangeLayout: function (id) {
            if (confirm(this.t('You will loose unsaved changes, continue anyway ?'))) {
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
<template>
    <nav id="notification-nav">
        <ul>
            <li class="heading">
                <span>{{ t('Layouts') }}</span>
            </li>
            <li
                v-for="layout2 in rootLayouts"
                :key="layout2.id"
            >
                <a
                    :href="getUrl(layout2)"
                    :class="{'sel': layout.id === layout2.id}"
                    @click.prevent="confirmAndChangeLayout(layout2.id)"
                >{{ layout2.description }}</a>
                <ul v-if="getChildren(layout2)">
                    <li
                        v-for="layout3 in getChildren(layout2)"
                        :key="layout3.id"
                    >
                        <a
                            :href="getUrl(layout3)"
                            :class="{'sel': layout.id === layout3.id}"
                            @click.prevent="confirmAndChangeLayout(layout3.id)"
                        >{{ layout3.description }}</a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</template>

<script>
import { mapState, mapActions } from 'vuex';

export default {
    computed: {
        rootLayouts() {
            return this.layouts.filter((layout) => layout.parent_id == null);
        },
        ...mapState(['layout', 'layouts', 'theme'])
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
            }
        });
    },
    methods: {
        getChildren(layout) {
            return this.layouts.filter((layout2) => layout2.parent_id == layout.id);
        },
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
    .sidebar nav li ul {
        display: block;
    }
</style>
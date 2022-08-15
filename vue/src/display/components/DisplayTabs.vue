<template>
    <header class="pane-header">
        <div
            id="tabs"
            class="pane-tabs"
        >
            <div class="heading">
                <span>{{ t('View modes') }}</span>
                <a
                    href="#"
                    class="add-viewmode"
                    :title="t('New View Mode')"
                    @click.prevent="addViewMode"
                >
                    <span class="icon add" />
                </a>
            </div>
            <div
                role="tablist" 
                class="scrollable"
            >
                <a
                    v-for="mode, index in viewModes"
                    :key="index"
                    :class="{'sel': viewMode.handle === mode.handle}"
                    href="#"
                    role="tab"
                    :data-viewmode="mode.id"
                    @click.prevent=""
                >
                    <span class="tab-label">
                        <span
                            v-if="mode.hasErrors"
                            class="error"
                            data-icon="alert"
                            aria-label="Error"
                        />
                        <span v-if="mode.hasErrors">&nbsp;</span>
                        <span
                            class="name"
                            @click.prevent="setViewMode2(mode)"
                        >
                            {{ mode.name }}
                        </span>
                        <span
                            v-if="viewMode.handle === mode.handle"
                            class="icon edit"
                            :title="t('Edit view mode')"
                            @click.prevent="editViewMode(mode)"
                        />
                        <span
                            v-if="mode.handle != 'default' && viewMode.handle === mode.handle"
                            class="icon delete"
                            @click.prevent="confirmAndDeleteViewMode(mode)"
                        />
                    </span>
                </a>
            </div>
            <button
                id="overflow-tab-btn"
                type="button"
                class="btn menubtn hidden"
            />
            <div
                id="overflow-tab-menu"
                class="menu"
            >
                <ul
                    role="listbox"
                    class="padded"
                >
                    <li
                        v-for="mode, index in viewModes"
                        :key="index"
                    >
                        <a 
                            href="#"
                            role="option"
                            :class="{'sel': viewMode.handle === mode.handle}"
                            @click.prevent="setViewMode2(mode)"
                        >
                            {{ mode.name }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <view-mode-modal
            :show-modal="showModal"
            :editedViewMode="editedViewMode"
            @closeModal="onCloseModal"
        />
    </header>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import ViewModeModal from './ViewModeModal.vue';

export default {
    components: {ViewModeModal},
    data() {
        return {
            showModal: false,
            editedViewMode: null,
            tabsInited: false,
            overflowTabBtn: null
        }
    },
    computed: {
        ...mapState(['viewModes', 'viewMode'])
    },
    updated () {
        if (!this.tabsInited) {
            this.overflowTabBtn = $('#overflow-tab-btn');
            if (!this.overflowTabBtn.data('menubtn')) {
                new Garnish.MenuBtn(this.overflowTabBtn);
            }
            this.tabsInited = true;
            $(window).resize(() => {
                this.initTabs();
            });
        }
        this.$nextTick(() => {
            this.initTabs();
        });
    },
    methods: {
        onCloseModal: function () {
            this.edit = null;
            this.showModal = false;
        },
        setViewMode2: function (viewMode) {
            this.setViewMode({viewMode: viewMode});
        },
        selectViewMode (id) {
            let viewMode = this.viewModes.filter((v) => v.id == id)[0];
            this.setViewMode({viewMode: viewMode});
            // this.overflowTabBtn.data('menubtn').hideMenu();
        },
        addViewMode: function () {
            this.editedViewMode = null;
            this.showModal = true;
        },
        editViewMode: function (viewMode) {
            this.editedViewMode = viewMode;
            this.showModal = true;
        },
        confirmAndDeleteViewMode: function (viewMode) {
            if (confirm(this.t('Do you really want to delete this view mode ?'))) {
                this.deleteViewMode(viewMode);
            }
        },
        initTabs () {
            let tabList = $(this.$el).find('[role=tablist]');
            let maxWidth = tabList.width();
            this.overflowTabBtn.addClass('hidden');
            let width = 0;
            $.each(tabList.find('a'), (i, link) => { 
                link = $(link);
                width += link.outerWidth();
                if (width > maxWidth) {
                    this.overflowTabBtn.removeClass('hidden');
                    return;
                }
            });
        },
        ...mapActions(['deleteViewMode', 'setViewMode'])
    }
};
</script>
<style lang="scss" scoped>
@import '~craftcms-sass/_mixins';

.icon.delete, .icon.edit {
    margin-left: 10px;
    margin-right: 0 !important;
    cursor: pointer;
    &::before {
        margin-top: -3px;
    }
}
#tabs {
    position: relative;
    min-height: 40px;
    ul {
        overflow: hidden;
        flex: 1;
    }
    [role=tablist] {
        padding-left: 2px;
        margin-left: 10px;
    }
}
.add-viewmode {
    font-size: 14px;
    margin-left: 10px;
}
.heading {
    text-transform: uppercase;
    padding-right: 10px;
    padding-left: 15px;
    color: $mediumTextColor;
    font-size: 11px;
    font-weight: bold;
}
</style>
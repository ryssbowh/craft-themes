<template>
    <div class="pane-header">
        <nav
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
            <ul>
                <li
                    v-for="mode, index in viewModes"
                    :key="index"
                >
                    <a
                        :class="{'sel': viewMode.handle === mode.handle}"
                        href="#"
                        :data-viewmode="mode.id"
                        @click.prevent=""
                    >
                        <span
                            v-if="mode.hasErrors"
                            class="error"
                            data-icon="alert"
                            aria-label="Error"
                        />
                        <span v-if="mode.hasErrors">&nbsp;</span>
                        <span @click.prevent="setViewMode2(mode)">{{ mode.name }}</span>
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
                    </a>
                </li>
            </ul>
            <button
                id="overflow-tab-btn"
                type="button"
                data-icon="ellipsis"
                class="btn menubtn hidden"
            />
            <div
                id="overflow-tab-menu"
                class="menu"
            >
                <ul role="listbox" />
            </div>
            <view-mode-modal
                :show-modal="showModal"
                :editedViewMode="editedViewMode"
                @closeModal="onCloseModal"
            />
        </nav>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';

export default {
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
            this.overflowTabBtn.data('menubtn').hideMenu();
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
            let maxWidth = $(this.$el).find('ul').width();
            let elems = $(this.$el).find('li a:not(.sel)');
            let showOverflow = false;
            let overflowMenu = this.overflowTabBtn.data('menubtn').menu.$container.find('> ul');
            let newOverflow = [];
            let selectedLink = $(this.$el).find('li a.sel');
            $(this.$el).find('li').removeClass('hidden');
            let width = selectedLink.parent().width();
            let _this = this;
            $.each(elems, (i, a) => {
                let li = $(a).parent();
                width += li.width();
                if (width > maxWidth) {
                    let newLi = li.clone();
                    newLi.find('a').click(function (e) {
                        e.preventDefault();
                        _this.selectViewMode($(this).data('viewmode'));
                        _this.$nextTick(() => {
                            _this.initTabs();
                        });
                    });
                    newOverflow.push(newLi);
                    li.addClass('hidden');
                    showOverflow = true;
                }
            });
            if (showOverflow) {
                overflowMenu.html(newOverflow);
                this.overflowTabBtn.removeClass('hidden');
            } else {
                this.overflowTabBtn.addClass('hidden');
            }
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
}
.add-viewmode {
    font-size: 14px;
    margin-left: 10px;
}
.heading {
    text-transform: uppercase;
    padding-right: 10px;
    color: $mediumTextColor;
    font-size: 11px;
    font-weight: bold;
}
</style>
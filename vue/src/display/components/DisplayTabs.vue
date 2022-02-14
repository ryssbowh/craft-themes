<template>
    <div class="pane-header">
        <nav id="tabs" class="pane-tabs">
            <div class="heading">
                <span>{{ t('View modes') }}</span>
                <a href="#" class="add-viewmode" @click.prevent="addViewMode" :title="t('New View Mode')">
                    <span class="icon add"></span>
                </a>
            </div>
            <ul>
                <li v-for="mode, index in viewModes" v-bind:key="index">
                    <a :class="{'sel': viewMode.handle === mode.handle}" @click.prevent="" href="#" :data-viewmode="mode.id">
                        <span class="error" data-icon="alert" aria-label="Error" v-if="mode.hasErrors"></span>
                        <span v-if="mode.hasErrors">&nbsp;</span>
                        <span @click.prevent="setViewMode2(mode)">{{ mode.name }}</span>
                        <span class="icon edit" @click.prevent="editViewMode(mode)" :title="t('Edit view mode')" v-if="viewMode.handle === mode.handle"></span>
                        <span v-if="mode.handle != 'default' && viewMode.handle === mode.handle" class="icon delete" @click.prevent="confirmAndDeleteViewMode(mode)"></span>
                    </a>
                </li>
            </ul>
            <button type="button" id="overflow-tab-btn" data-icon="ellipsis" class="btn menubtn hidden"></button>
            <div id="overflow-tab-menu" class="menu">
                <ul role="listbox"></ul>
            </div>
            <view-mode-modal :show-modal="showModal" :editedViewMode="editedViewMode" @closeModal="onCloseModal"/>
        </nav>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';

export default {
    computed: {
        ...mapState(['viewModes', 'viewMode'])
    },
    data() {
        return {
            showModal: false,
            editedViewMode: null,
            tabsInited: false,
            overflowTabBtn: null
        }
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
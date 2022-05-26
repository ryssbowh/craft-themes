<template>
    <div class="themes-displays">
        <div class="spinner-wrapper" v-if="isLoading || isSaving">
          <div class="spinner"></div>
        </div>
        <div class="fullwidth display-table" v-if="rootDisplays.length">
            <div :class="{line: true, head: true, 'with-handles': showFieldHandles}">
                <div class="handle col"></div>
                <div class="title col">{{ t('Title', {}, 'app') }}</div>
                <div class="handle col" v-if="showFieldHandles">{{ t('Handle', {}, 'app') }}</div>
                <div class="type col">{{ t('Type') }}</div>
                <div class="label col"><a href="#" @click.prevent="setLabelsVisibility(!this.allLabelsVisible)" :title="allLabelsVisible ? t('Make all hidden') : t('Make all visible')">{{ t('Label', {}, 'app') }}</a></div>
                <div class="visibility col"><a href="#" @click.prevent="setItemsVisibility(!this.allItemsVisible)" :title="allItemsVisible ? t('Make all hidden') : t('Make all visible')">{{ t('Visibility') }}</a></div>
                <div class="displayer col">{{ t('Displayer') }}</div>
                <div class="options col"></div>
            </div>
            <div class="body">
                <draggable
                    item-key="uid"
                    :list="rootDisplays"
                    group="displays"
                    handle=".move"
                    swapThreshold="0.65"
                    @change="onDragChange"
                    >
                    <template #item="{element}">
                        <display-item :display="element" :indentation-level="0"/>
                    </template>
                </draggable>
            </div>
        </div>
        <p v-if="layouts.length > 0 && rootDisplays.length == 0 && !isLoading">
            {{ t('There are no displays for this view mode') }}
        </p>
        <p v-if="layouts.length == 0 && !isLoading">
            {{ t('No layouts available, you should reinstall the themes data in the settings') }}
        </p>
        <group-modal @closeModal="setShowGroupModal({show: false})"/>
    </div>
</template>

<script>
import { mapMutations, mapState } from 'vuex';
import { sortBy } from 'lodash';

export default {
    computed: {
        isLoading: function () {
            return this.isFetching || this.isSaving;
        },
        rootDisplays: function () {
            if (!this.viewMode) {
                return [];
            }
            return sortBy(this.viewMode.displays, 'order');
        },
        ...mapState(['viewMode', 'isSaving', 'isFetching', 'viewMode', 'showGroupModal', 'viewModes', 'layouts', 'showFieldHandles'])
    },
    watch: {
        viewMode: {
            deep: true,
            handler() {
                this.allItemsVisible = true;
                this.allLabelsVisible = true;
                for (let display of this.rootDisplays) {
                    if (display.item.hidden) {
                        this.allItemsVisible = false;
                        break;
                    }
                }
                for (let display of this.rootDisplays) {
                    if (display.item.labelHidden) {
                        this.allLabelsVisible = false;
                        break;
                    }
                }
            }
        }
    },
    data: function () {
        return {
            allItemsVisible: true,
            allLabelsVisible: true
        }
    },
    methods: {
        onDragChange: function (e) {
            if (e.removed) {
                this.removeDisplay(e.removed.element);
            } else if (e.added) {
                e.added.element.group_id = null;
                this.addDisplay(e.added.element);
            }
            this.rebuildOrders(e);
        },
        rebuildOrders: function (e) {
            let newIndex, movedElem;
            if (e.added) {
                newIndex = e.added.newIndex;
                movedElem = e.added.element;
            } else if (e.moved) {
                newIndex = e.moved.newIndex;
                movedElem = e.moved.element;
            }
            let newOrder = 0;
            for (let i in this.rootDisplays) {
                let display = this.rootDisplays[i];
                if (movedElem && newOrder == newIndex) {
                    newOrder++;
                }
                if (movedElem && display.id == movedElem.id) {
                    continue;
                }
                display.order = newOrder;
                this.updateDisplay({uid: display.uid, data: {order: newOrder}});
                newOrder++;
            }
            if (movedElem) {
                movedElem.order = newIndex;
                this.updateDisplay({uid: movedElem.uid, data: {order: newIndex}});
            }
        },
        ...mapMutations(['updateDisplay', 'setShowGroupModal', 'addDisplay', 'removeDisplay', 'setItemsVisibility', 'setLabelsVisibility'])
    }
};
</script>

<style lang="scss">
@import '~craftcms-sass/_mixins';

.display-table {
    .col {
        &.options {
            display: flex;
        }
        &.move {
            padding-left: 5px;
            display:flex;
        }
    }
    .head {
        font-weight: bold;
        background: $grey050;
        border-radius: 5px;
        .col {
            padding: 7px 8px;
        }
    }
    .line {
        display: grid;
        grid-template-columns: 5% 20% 16% 17% 17% 19% 6%;
        align-items: center;
        margin: 0 0 7px 0;
        width: 100%;
        &.flex {
            display: flex;
        }
        &.opaque, &.opaque ~ .sub-fields {
            opacity: 0.5;
        }
        &.bg-grey {
            background: $grey050;
        }
        &.with-handles {
            grid-template-columns: 5% 14% 13% 14% 16% 16% 16% 6%;
        }
        &.has-sub-fields {
            display: flex;
            flex-direction: column;
            border-radius: 5px;
            padding: 7px 0;
        }
        &.no-margin {
            margin: 0;
        }
        &.no-padding {
            padding: 0;
        }
    }
    .block-type-name {
        width: 5%;
        opacity: 0.7;
        white-space: nowrap;
        margin-bottom: 0;
    }
    .sub-fields {
        width: 100%;
        transition: opacity 0.3s;
        .line:last-child {
           margin-bottom: 0;
        }
    }
    .indented-1 .indented {
       padding-left: 20%;
    }
    .indented-2 .indented {
       padding-left: 40%;
    }
    .indented-3 .indented {
       padding-left: 60%;
    }
    .indented-3 .indented {
       padding-left: 80%;
    }
    .body {
        .col:not(.move) {
            padding: 0px 10px;
        }
        .col.move, .col.options {
            padding-bottom: 4px;
        }
    }
}
.themes-displays {
    position: relative;
    .title {
        justify-content: space-between;
    }
}
.spinner-wrapper {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1000;
    .spinner {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }
}
</style>
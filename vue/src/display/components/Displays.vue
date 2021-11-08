<template>
    <div class="themes-displays">
        <div class="spinner-wrapper" v-if="isLoading || isSaving">
          <div class="spinner"></div>
        </div>
        <div class="fullwidth display-table" v-if="rootDisplays.length">
            <div class="line head">
                <div class="handle col"></div>
                <div class="title col">{{ t('Title', {}, 'app') }}</div>
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
                        <display-item :display="element"/>
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
        <options-modal/>
        <group-modal @closeModal="setShowGroupModal({show: false})"/>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
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
        ...mapState(['viewMode', 'isSaving', 'isFetching', 'viewMode', 'showGroupModal', 'viewModes', 'layouts'])
    },
    watch: {
        viewModes: {
            deep: true,
            handler() {
                this.checkChanges();
                this.allItemsVisible = true;
                this.allLabelsVisible = true;
                for (let display of this.rootDisplays) {
                    if (display.item.hidden) {
                        this.allItemsVisible = false;
                    }
                    if (display.item.labelHidden) {
                        this.allLabelsVisible = false;
                    }
                }
                this.setItemsVisibility(null);
                this.setLabelsVisibility(null);
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
                // this.updateDisplay({uid: display.uid, data: {order: newOrder}});
                display.order = newOrder;
                newOrder++;
            }
            if (movedElem) {
                // this.updateDisplay({uid: movedElem.uid, data: {order: newIndex}});
                movedElem.order = newIndex;
            }
        },
        ...mapMutations(['updateDisplay', 'setShowGroupModal', 'addDisplay', 'removeDisplay', 'setItemsVisibility', 'setLabelsVisibility']),
        ...mapActions(['checkChanges']),
    }
};
</script>

<style lang="scss">
@import '~craftcms-sass/_mixins';

.content-pane {
    border-top-left-radius: 0;
}
.display-table {
    .head {
        font-weight: bold;
        background: $grey050;
        border-radius: 5px;
    }
    .line {
        display: grid;
        grid-template-columns: 5% 24% 12% 17% 17% 19% 6%;
        align-items: center;
        margin: 0 0 7px 0;
    }
    .line-wrapper {
        display: flex;
        flex-direction: column;
        border-radius: 5px;
        background: $grey050;
        padding: 7px 0;
        margin-bottom: 7px;
    }
    .line-wrapper:last-child, .line:last-child {
        margin-bottom: 0;
    }
    .head .col {
        padding: 7px 8px;
    }
    .body {
        .col:not(.move) {
            padding: 0px 10px;
        }
        .col.move, .col.options {
            padding-bottom: 4px;
        }
    }
    .col.move {
        padding-left: 5px;
        display:flex;
    }
    .col.title {
        display: flex;
        flex-wrap: wrap;
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
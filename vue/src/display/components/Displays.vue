<template>
    <div class="themes-displays">
        <div class="spinner-wrapper" v-if="isLoading || isSaving">
          <div class="spinner"></div>
        </div>
        <div class="flex title">
            <h2>{{ t('Displays') }}</h2>
            <a href="#" @click.prevent="newGroup">{{ t('New group') }}</a>
        </div>
        <div class="fullwidth display-table" v-if="rootDisplays.length">
            <div class="line head">
                <div class="handle col"></div>
                <div class="title col">{{ t('Title', {}, 'app') }}</div>
                <div class="handle col">{{ t('Handle', {}, 'app') }}</div>
                <div class="type col">{{ t('Type') }}</div>
                <div class="label col">{{ t('Label', {}, 'app') }}</div>
                <div class="visibility col">{{ t('Visibility') }}</div>
                <div class="displayer col">{{ t('Displayer') }}</div>
                <div class="options col">{{ t('Options') }}</div>
            </div>
            <div class="body">
                <draggable
                    item-key="uid"
                    :list="rootDisplays"
                    group="displays"
                    handle=".move"
                    @change="onDragChange"
                    >
                    <template #item="{element}">
                        <display-item :display="element"/>
                    </template>
                </draggable>
            </div>
        </div>
        <p v-if="rootDisplays.length == 0 && !isLoading">
            {{ t('There are no displays for this layout') }}
        </p>
        <options-modal/>
        <group-modal @closeModal="onCloseGroupModal"/>
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
            if (this.viewModeIndex === null) {
                return [];
            }
            return sortBy(this.viewModes[this.viewModeIndex].displays, 'order');
        },
        ...mapState(['viewModeIndex', 'isSaving', 'isFetching', 'viewMode', 'showGroupModal', 'viewModes'])
    },
    watch: {
        viewModes: {
            deep: true,
            handler() {
                this.checkChanges();
            }
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
        onCloseGroupModal: function () {
            this.setShowGroupModal({show: false});
        },
        newGroup: function () {
            this.setShowGroupModal({show: true});  
        },
        ...mapMutations(['updateDisplay', 'setShowGroupModal', 'addDisplay', 'removeDisplay']),
        ...mapActions(['checkChanges']),
    }
};
</script>

<style lang="scss">
.content-pane {
    border-top-left-radius: 0;
}
.display-table {
    .head {
        font-weight: bold;
        background: #f3f7fc;
        border-radius: 5px;
    }
    .line {
        display: grid;
        grid-template-columns: 4% 12% 12% 12% 17% 17% 17% 9%;
        align-items: center;
        margin: 0;
    }
    .col:not(.move) {
        padding: 7px 10px;
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
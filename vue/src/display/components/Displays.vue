<template>
    <div class="themes-displays">
        <div class="spinner-wrapper" v-if="isLoading">
          <div class="spinner"></div>
        </div>
        <h2 v-if="!isLoading">{{ t('Displays') }}</h2>
        <div class="fullwidth display-table" v-if="displays.length">
            <div class="line head">
                <div class="handle col"></div>
                <div class="title col">{{ t('Title') }}</div>
                <div class="handle col">{{ t('Handle') }}</div>
                <div class="type col">{{ t('Type') }}</div>
                <div class="label col">{{ t('Label') }}</div>
                <div class="visibility col">{{ t('Visibility') }}</div>
                <div class="displayer col">{{ t('Displayer') }}</div>
                <div class="options col">{{ t('Options') }}</div>
            </div>
            <div class="body">
                <draggable
                    item-key="id"
                    :list="viewModeDisplays"
                    group="displays"
                    handle=".move"
                    @change="rebuildOrders"
                    >
                    <template #item="{element}">
                        <display-item :display="element"/>
                    </template>
                </draggable>
            </div>
        </div>
        <p v-if="displays.length == 0 && !isLoading">
            {{ t('There are no displays for this layout') }}
        </p>
        <options-modal/>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import { reduce, filter, sortBy } from 'lodash';

export default {
    computed: {
        isLoading: function () {
            return reduce(this.isFetching, function (res, elem) {
                return (res || elem);
            }, this.isSaving);
        },
        viewModeDisplays: function () {
            if (!this.viewMode) {
                return [];
            }
            return sortBy(filter(this.displays, display => display.viewMode_id === this.viewMode.id || display.viewMode_id === this.viewMode.handle), 'order');
        },
        ...mapState(['displays', 'isSaving', 'isFetching', 'viewMode'])
    },
    watch: {
        displays: {
            deep: true,
            handler() {
                this.checkChanges();
            }
        }
    },
    methods: {
        rebuildOrders: function (e) {
            let displays = this.displays;
            let newOrder = 0;
            for (let i in displays) {
                let display = displays[i];
                if (e.moved && newOrder == e.moved.newIndex) {
                    newOrder++;
                }
                if (e.moved && display.id == e.moved.element.id) {
                    continue;
                }
                this.updateDisplay({id: display.id, data: {order: newOrder}});
                newOrder++;
            }
            if (e.moved) {
                this.updateDisplay({id: e.moved.element.id, data: {order: e.moved.newIndex}});
            }
        },
        ...mapMutations(['updateDisplay']),
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
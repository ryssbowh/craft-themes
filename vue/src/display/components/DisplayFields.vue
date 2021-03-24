<template>
    <div class="themes-displays">
        <div class="spinner-wrapper" v-if="isLoading">
          <div class="spinner"></div>
        </div>
        <h2>{{ t('Displays') }}</h2>
        <div class="fullwidth display-table" v-if="visibleDisplays.length">
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
                    :list="visibleDisplays"
                    group="displays"
                    handle=".move"
                    @change="visibleChanged"
                    >
                    <template #item="{element}">
                        <display-field v-if="element.type == 'field'" :display="element" @changedVisibility="rebuildOrders" />
                    </template>
                </draggable>
            </div>
        </div>
        <p v-if="visibleDisplays.length == 0">
            {{ t('There are no visible fields') }}
        </p>
        <h2>{{ t('Hidden') }}</h2>
        <div class="fullwidth display-table" v-if="hiddenDisplays.length">
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
                    :list="hiddenDisplays"
                    group="displays"
                    handle=".move"
                    :sort="false"
                    @change="hiddenChanged"
                    >
                    <template #item="{element}">
                        <display-field v-if="element.type == 'field'" :display="element" @changedVisibility="rebuildOrders" />
                    </template>
                </draggable>
            </div>
        </div>
        <p v-if="hiddenDisplays.length == 0">
            {{ t('There are no hidden displays') }}
        </p>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Mixin from '../../mixin';
import DisplayField from './DisplayField';
import DisplayGroup from './DisplayGroup';
import DisplayMatrix from './DisplayMatrix';
import { reduce, filter, sortBy } from 'lodash';
import Draggable from 'vuedraggable';

export default {
    computed: {
        isLoading: function () {
            return reduce(this.isFetching, function (res, elem) {
                return (res || elem);
            }, this.isSaving);
        },
        currentViewMode: function () {
            return this.viewModes[this.viewMode];
        },
        viewModeDisplays: function () {
            if (!this.currentViewMode) {
                return [];
            }
            return sortBy(filter(this.displays, display => display.viewMode_id === this.currentViewMode.id || display.viewMode_id === this.currentViewMode.handle), 'order');
        },
        hiddenDisplays: function () {
            return filter(this.viewModeDisplays, display => display.hidden == 1);
        },
        visibleDisplays: function () {
            return filter(this.viewModeDisplays, display => display.hidden == 0);
        },
        ...mapState(['displays', 'isSaving', 'isFetching', 'viewModes', 'viewMode'])
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
        visibleChanged: function (e) {
            if (e.added) {
                let data = {
                    hidden: false,
                    visuallyHidden: false
                };
                e.moved = e.added;
                this.updateDisplay({id: e.added.element.id, data: data});
            }
            this.rebuildOrders(e);
        },
        hiddenChanged: function (e) {
            if (e.added) {
                let data = {
                    order: this.visibleDisplays.length + 1,
                    hidden: false,
                    visuallyHidden: false
                };
                this.updateDisplay({id: e.added.element.id, data: data});
            }
            this.rebuildOrders(e);
        },
        rebuildOrders: function (e) {
            let displays = this.visibleDisplays;
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
            displays = this.hiddenDisplays;
            for (let i in displays) {
                let display = displays[i];
                this.updateDisplay({id: display.id, data: {order: newOrder}});
                newOrder++;
            }
        },
        ...mapMutations(['updateDisplay']),
        ...mapActions(['checkChanges']),
    },
    mixins: [Mixin],
    components: {
        DisplayField,
        DisplayMatrix,
        DisplayGroup,
        Draggable
    }
};
</script>

<style lang="scss">
.display-table {
    .head {
        font-weight: bold;
        background: #f3f7fc;
        border-radius: 5px;
    }
    .line {
        display: grid;
        grid-template-columns: 2% 12% 12% 12% 17% 17% 17% 11%;
        align-items: center;
        margin: 0;
    }
    .col {
        padding: 7px 10px;
    }
    .sortable-chosen {
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
    background: rgba(230, 230, 230, 0.7);
    z-index: 1000;
    .spinner {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }
}
</style>
<template>
    <div
        v-if="layouts.length"
        id="action-buttons"
        class="flex"
    >
        <div class="btngroup submit">
            <button
                v-if="!isCopying"
                ref="menu"
                href="#"
                class="btn submit menubtn"
                :disabled="!canCopy"
                @click.prevent=""
            >
                {{ t('Copy To') }}
            </button>
            <div
                class="menu"
                data-align="right"
            >
                <ul>
                    <li
                        v-for="elem, index in layoutsWithoutBlocks"
                        :key="index"
                    >
                        <a
                            href="#"
                            @click.prevent="checkAndCopy(elem)"
                        >{{ elem.description }}</a>
                    </li>
                    <li>
                        <a
                            href="#"
                            @click.prevent="setShowLayoutModal({show: true})"
                        >{{ t('Create custom') }}</a>
                    </li>
                </ul>
            </div>
        </div>
        <button
            v-if="!isCopying && layout.type != 'default'"
            href="#"
            class="btn submit"
            @click.prevent="checkAndDelete"
        >
            {{ t('Delete', {}, 'app') }}
        </button>
        <button
            v-if="layout.type == 'custom' && layout.id"
            href="#"
            class="btn submit"
            @click.prevent="setShowLayoutModal({show: true, editUid: layout.uid})"
        >
            {{ t('Edit', {}, 'app') }}
        </button>
        <button
            href="#"
            class="btn submit"
            :disabled="!canSave"
            @click.prevent="save"
        >
            {{ t('Save', {}, 'app') }}
        </button>
    </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from 'vuex';
import { reduce } from 'lodash';

export default {
    computed: {
        isLoading: function () {
            return reduce(this.isFetching, function (res, elem) {
                return (res || elem);
            }, false);
        },
        canCopy: function () {
            return (!this.isSaving && !this.isLoading && this.layout.id);
        },
        layoutsWithoutBlocks: function () {
            return this.layouts.filter(layout => !layout.hasBlocks);
        },
        canSave: function () {
            return (!this.isSaving && !this.isLoading);
        },
        ...mapState(['isSaving', 'isFetching', 'availableLayouts', 'layouts', 'isCopying', 'layout'])
    },
    methods: {
        checkAndCopy: function(layout) {
            if (confirm(this.t('You will loose unsaved changes, continue anyway ?'))) {
                this.copy(layout);
            }
        },
        copy(layout) {
            this.copyLayout(layout);
            Craft.cp.displayNotice(this.t('Blocks are copied, make your changes and save'));
        },
        checkAndDelete() {
            if (confirm(this.t('Are you sure you want to delete this layout ?'))) {
                this.deleteLayout();
            }
        },
        ...mapMutations(['setShowLayoutModal']),
        ...mapActions(['save', 'copyLayout', 'deleteLayout'])
    }
};
</script>
<style lang="scss" scoped>
.btn[disabled] {
  opacity: 0.5;
}
</style>
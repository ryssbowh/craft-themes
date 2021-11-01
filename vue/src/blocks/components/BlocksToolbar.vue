<template>
  <div id="action-buttons" class="flex" v-if="layouts.length">
    <div class="btngroup submit">
      <button href="#" class="btn submit menubtn" ref="menu" v-if="!isCopying" :disabled="!canCopy" @click.prevent="">{{ t('Copy To') }}</button>
      <div class="menu" data-align="right">
        <ul>
          <li v-for="elem, index in layoutsWithoutBlocks" v-bind:key="index">
              <a href="#" @click.prevent="checkAndCopy(elem)">{{ elem.description }}</a>
          </li>
          <li>
              <a href="#" @click.prevent="setShowLayoutModal({show: true})">{{ t('Create custom') }}</a>
          </li>
        </ul>
      </div>
    </div>
    <button href="#" class="btn submit" v-if="!isCopying && layout.type != 'default'" @click.prevent="checkAndDelete">{{ t('Delete', {}, 'app') }}</button>
    <button href="#" class="btn submit" v-if="layout.type == 'custom' && layout.id" @click.prevent="setShowLayoutModal({show: true, editUid: layout.uid})">{{ t('Edit', {}, 'app') }}</button>
    <button href="#" class="btn submit" :disabled="!canSave" @click.prevent="save">{{ t('Save', {}, 'app') }}</button>
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
            return (!this.isSaving && !this.isLoading && this.hasChanged);
        },
        ...mapState(['isSaving', 'hasChanged', 'isFetching', 'availableLayouts', 'layouts', 'isCopying', 'layout'])
    },
    methods: {
        checkAndCopy: function(layout) {
            if (this.hasChanged) {
                if (confirm(this.t('You have unsaved changes, continue anyway ?'))) {
                    this.copy(layout);
                }
            } else {
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
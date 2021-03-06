<template>
  <div id="action-buttons" class="flex">
    <div class="btngroup submit">
      <button href="#" class="btn submit menubtn" :disabled="!canCopy" @click.prevent="">{{ t('Copy To') }}</button>
      <div class="menu" data-align="right">
        <ul>
          <li v-for="elem in layoutsWithoutBlocks">
              <a href="#" @click.prevent="checkAndCopy(elem)">{{ elem.description }}</a>
          </li>
        </ul>
      </div>
    </div>
    <button href="#" class="btn submit" v-if="!isCopying && layout.type!='default'" @click.prevent="checkAndDelete">{{ t('Delete') }}</button>
    <button href="#" class="btn submit" :disabled="!canSave" @click.prevent="save">{{ t('Save') }}</button>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from 'vuex';
import Mixin from '../../mixin';
import { reduce } from 'lodash';

export default {
    computed: {
        isLoading: function () {
            return reduce(this.isFetching, function (res, elem) {
                return (res || elem);
            }, false);
        },
        canCopy: function () {
            return (!this.isSaving && !this.isLoading);
        },
        layoutsWithoutBlocks: function () {
            return this.layouts.filter(layout => !layout.hasBlocks);
        },
        canSave: function () {
            return (!this.isSaving && !this.isLoading && this.hasChanged && !this.hasErrors);
        },
        hasErrors: function () {
            let _this = this;
            return reduce(Object.keys(this.blockErrors).map((key) => {
                return _this.blockErrors[key].length > 0;
            }), function(result, value, key) {
                return (result || value);
            }, false);
        },
        ...mapState(['isSaving', 'hasChanged', 'blockErrors', 'isFetching', 'availableLayouts', 'layouts', 'isCopying', 'layout'])
    },
    methods: {
        confirmAndSave: function (extra) {
            if (confirm(this.t('This will replace the blocks defined in those pages, continue anyway ?'))) {
                this.save(extra);
            }
        },
        checkAndCopy: function(layout) {
            if (this.hasChanged) {
                if (confirm(this.t('You have unsaved changes, continue anyway ?'))) {
                    this.copy(layout.id);
                }
            } else {
                this.copy(layout.id);
            }
        },
        checkAndDelete() {
            if (confirm(this.t('Are you sure you want to delete all blocks in this layout ?'))) {
                this.deleteLayout();
            }
        },
        ...mapMutations([]),
        ...mapActions(['save', 'copy', 'deleteLayout'])
    },
    mixins: [Mixin]
};
</script>
<style lang="scss" scoped>
.btn[disabled] {
  opacity: 0.5;
}
</style>
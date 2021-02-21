<template>
  <div class="region" :style="'width:'+(region.width ? region.width : '100%')">
    <div class="region-heading">
        <h5 class="region-title">{{ region.name }}</h5>
    </div>
    <draggable
      item-key="index"
      class="region-blocks"
      :list="regionBlocks"
      group="blocks"
      handle=".move"
      @change="changed"
    >
      <template #item="{element}">
        <block
          :block="element"
          :original="false"
          @remove="removeBlock"
        />
      </template>
    </draggable>
  </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import { filter, sortBy } from 'lodash';
import Block from './Block.vue';
import Draggable from 'vuedraggable';
import Mixin from '../mixin';

export default {
  computed: {
    regionBlocks () {
      return sortBy(filter(this.blocks, (block) => {
        return this.region.handle == block.region;
      }), 'order');
    },
    ...mapState(['blocks', 'theme', 'element']),
  },
  props: {
    region: Object
  },
  methods: {
    changed: function (evt) {
      if (evt.added) {
        let block = {...evt.added.element};
        block.region = this.region.handle;
        block.order = evt.added.newIndex;
        if (block.index === undefined) {
          block.active = true;
          this.addBlock(block);
        } else {
          this.updateBlock(block);
        }
      }
    },
    ...mapMutations(['addBlock', 'removeBlock', 'updateBlock']),
    ...mapActions([])
  },
  mixins: [Mixin],
  components: {
    Block,
    Draggable
  }
};
</script>

<style lang="scss" scoped>
  .region {
    margin-bottom: 10px;
  }
  .region-title {
    font-weight: bold;
    font-size: 16px;
  }
  .region-blocks {
    min-height: 56px;
    margin-top: 5px;
    box-shadow: 0 0 0 1px rgba(31, 41, 51, 0.1), 0 2px 5px -2px rgba(31, 41, 51, 0.2);
    border-radius: 3px;
    &.drop-active {
      padding-bottom: 58px;
    }
  }
</style>

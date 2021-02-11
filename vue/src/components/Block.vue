<template>
  <div :class="(original ? 'block original' : 'block') + (block.disabled ? ' disabled' : '')">
    <div class="description">
      <div class="name">{{ block.name }}</div>
      <div class="provider" v-if="!original">{{ block.provider }}</div>
    </div>
    <button v-if="!original" type="button" id="live" :class="'lightswitch has-labels' + (block.active ? ' on' : '')" role="checkbox" aria-checked="true" aria-labelledby="live-label" aria-describedby="live-instructions live-desc">
      <div class="lightswitch-container">
        <div class="handle"></div>
      </div>
    </button>
    <div class="move icon"></div>
    <div v-if="!original" class="delete icon" @click="$emit('remove', block)"></div>
  </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Mixin from '../mixin';

export default {
  computed: {
    ...mapState(['blocks'])
  },
  props: {
    block: Object,
    original: Boolean
  },
  mounted () {
    let _this = this;
    this.$nextTick(() => {
      $(this.$el).find('.lightswitch').on('change', function(e){
        let input = $(e.target).find('input');
        let value = (input.val() == 1 ? 1 : 0);
        _this.updateBlock(this.block.index, {active: value});
      });
    });
  },
  created () {
  },
  methods: {
    ...mapMutations([]),
    ...mapActions([])
  },
  mixins: [Mixin],
  emits: ['remove']
};
</script>

<style lang="scss" scoped>
  .block {
    padding: 8px 14px;
    border-bottom: solid rgba(51, 64, 77, 0.1);
    border-width: 1px 0;
    min-height: 48px;
    display: flex;
    align-items: center;
    background-color: #e4edf6;
    transition: all 0.3s;
    &:not(.original) {
      cursor: pointer;
    }
    &.active {
      background-color: #cdd8e4;
    }
    &:last-child {
      border-bottom: none;
    }
    &.disabled {
      opacity: 0.5;
    }
    .provider {
      font-size: 12px;
    }
    .description {
      flex: 1;
    }
    .delete, .move {
      margin-left: 5px;
      cursor: pointer;
    }
  }
</style>

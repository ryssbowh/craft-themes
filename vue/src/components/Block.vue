<template>
  <div :class="{'block': true, original: original, active: (block.active && !original)}">
    <div class="inner">
      <div class="description">
        <div class="name">{{ block.name }}</div>
        <div class="small" v-if="original">{{ block.smallDescription }}</div>
        <div class="small" v-if="!original">{{ block.provider }}</div>
      </div>
      <span class="info" v-if="original && block.longDescription">{{ block.longDescription }}</span>
      <button v-if="!original" type="button" id="live" :class="'lightswitch has-labels' + (block.active ? ' on' : '')" role="checkbox" aria-checked="true" aria-labelledby="live-label" aria-describedby="live-instructions live-desc">
        <div class="lightswitch-container">
          <div class="handle"></div>
        </div>
      </button>
      <div class="move icon"></div>
      <div v-if="!original && block.hasOptions" :class="'settings icon' + (blockOptionId == block.index ? ' active' : '')" @click="setBlockOptions(block)"></div>
      <div v-if="!original" class="delete icon" @click="$emit('remove', block)"></div>
    </div>
    <div class="errors" v-if="hasErrors">
      <div class="error" v-for="error in errors">{{ error }}</div>
    </div>
  </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Mixin from '../mixin';

export default {
  computed: {
    errors: function () {
      return this.blockErrors[this.block.index] ?? [];
    },
    hasErrors: function () {
      return this.errors.length > 0;
    },
    ...mapState(['blocks', 'blockErrors', 'blockOptionId'])
  },
  props: {
    block: Object,
    original: Boolean
  },
  mounted () {
    let _this = this;
    Craft.initUiElements(this.$el);
    this.$nextTick(() => {
      $(this.$el).find('.lightswitch').on('change', function(e){
        let block = {..._this.block};
        block.active = $(this).hasClass('on');
        _this.updateBlock(block);
      });
    });
  },
  methods: {
    ...mapMutations(['updateBlock', 'setBlockOptions']),
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
    transition: all 0.3s;
    background-color: #cdd8e4;
    opacity: 0.5;
    .errors {
      font-size: 12px;
    }
    .inner {
      display: flex;
      align-items: center;
    }
    &:not(.original) {
      cursor: pointer;
    }
    &.original {
      opacity: 1;
      background-color: #e4edf6;
    }
    &.active {
      opacity: 1
    }
    &:last-child {
      border-bottom: none;
    }
    .small {
      font-size: 12px;
    }
    .description {
      flex: 1;
      padding-right: 10px;
    }
    .delete, .move, .settings {
      margin-left: 5px;
      cursor: pointer;
      font-size: 16px;
    }
    .settings {
    	color: rgba(123, 135, 147, 0.5);
    	&.active, &:hover {
    		color: #3f4d5a
    	}
  	}
  }
</style>

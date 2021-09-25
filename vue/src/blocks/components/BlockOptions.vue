<template>
  <form class="options-inner" ref="form">
    <div class="field caching-section">
      <div class="heading">
        <label>{{ t('Caching') }}</label>                                    
      </div>
      <div class="input ltr">                    
        <div class="select">
          <select id="type" name="cacheStrategy" :value="block.options.cacheStrategy" @input="updateOptions({cacheStrategy: $event.target.value})">
            <option value="">{{ t('No cache') }}</option>
            <option :value="strategy.handle" v-for="strategy in cacheStrategies" v-bind:key="strategy.handle">{{ strategy.name }}</option>
          </select>
        </div>
      </div>
      <component :is="cacheStrategyOptionsComponent" :block="block" @updateOptions="updateOptions"></component>  
    </div>
    <component :is="optionsComponent" :block="block" @updateOptions="updateOptions"></component>
  </form>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';

export default {
  computed: {
    optionsComponent: function () {
      return this.block.provider + '-' + this.block.handle;
    },
    cacheStrategyOptionsComponent: function () {
      return 'strategy-' + this.block.options.cacheStrategy;
    },
    ...mapState(['cacheStrategies'])
  },
  props: {
    block: Object
  },
  methods: {
    updateOptions: function (options) {
      let block = {...this.block};
      block.options = {...this.block.options, ...options};
      this.updateBlock(block);
    },
    ...mapMutations(['updateBlock']),
    ...mapActions([])
  },
};
</script>

<style lang="scss">
.options-form {
  .options-inner {
    padding: 15px;
  }
  .caching-section {
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(51, 64, 77, 0.1);
    .field:first-child {
      margin-top: 24px !important;
    }
  }
}
</style>

<template>
  <form class="options-inner" ref="form">
    <div id="caching-field" class="field">
      <div class="heading">
        <label>Caching</label>                                    
      </div>
      <div class="input ltr">                    
        <div class="select">
          <select id="type" name="cacheStrategy" :value="block.options.cacheStrategy" @input="updateOptions({cacheStrategy: $event.target.value})">
            <option value="">No cache</option>
            <option :value="strategy.handle" v-for="strategy in cacheStrategies" v-bind:key="strategy.handle">{{ strategy.name }}</option>
          </select>
        </div>
      </div>
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

<style lang="scss" scoped>
  .options-inner {
    padding: 15px;
  }
</style>

<template>
  <form class="options-inner" ref="form">
    <div id="caching-field" class="field">
      <div class="heading">
        <label>Caching</label>                                    
      </div>
      <div class="input ltr">                    
        <div class="select">
          <select id="type" name="caching" :value="block.options.caching" @input="updateOptions({caching: $event.target.value})">
            <option value="0">No cache</option>
            <option value="1">Global</option>
            <option value="2">Session</option>
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
    ...mapState([])
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

<template>
  <div class="blocks">
    <div class="blocks-sidebar">
      <div class="heading">
        <h3>Blocks</h3>
      </div>
      <div v-for="provider in providers" v-bind:key="provider.handle">
        <h5 class="sub-heading slide">{{ provider.name }}</h5>
        <draggable
          item-key="vueid"
          class="list-group"
          :list="provider.blocksObjects"
          :group="{ name: 'blocks', pull: 'clone', put: false }"
          :sort="false"
          handle=".move"
        >
          <template #item="{element}">
            <block
              :block="element"
              :original="true"
            />
          </template>
        </draggable>
      </div>
    </div>
    <div class="regions">
      <div class="heading">
         <h3>{{ t('Regions') }}</h3>
      </div>
      <div class="region-list">
        <region v-for="region in regions" v-bind:key="region.handle"
          :region="region"
        />
      </div>
    </div>
    <div class="settings">
      <div class="heading">
          <h3>{{ t('Settings') }}</h3>
      </div>
    </div>
  </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Block from './Block.vue';
import Region from './Region.vue';
import Draggable from 'vuedraggable';
import Mixin from '../mixin';

export default {
  computed: {
    ...mapState(['blocks'])
  },
  data: function () {
    return {
    };
  },
  props: {
    initialBlocks: Array,
    regions: Object,
    providers: Object
  },
  created () {
    this.setBlocks(this.initialBlocks);
  },
  methods: {
    ...mapMutations(['setBlocks']),
    ...mapActions([])
  },
  components: {
    Region,
    Block,
    Draggable
  },
  mixins: [Mixin]
};
</script>

<style lang="scss">
#toolbar {
  justify-content: end;
}
</style>

<style lang="scss" scoped>
.blocks {
  position: relative;
  display: flex;
  border-radius: 3px;
  background-color: #f3f7fc;
  overflow: hidden;
  border: 1px solid rgba(96, 125, 159, 0.25);
  box-shadow: 0 0 0 1px rgba(31, 41, 51, 0.1), 0 2px 5px -2px rgba(31, 41, 51, 0.2);
  .regions, .settings, .block-sidebar{
    box-shadow: 0 0 0 1px rgba(31, 41, 51, 0.1), 0 2px 5px -2px rgba(31, 41, 51, 0.2);
  }
  &:after {
    position: absolute;
    display: block;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    content: '';
    box-shadow: inset 0 1px 3px -1px #acbed2;
    pointer-events: none;
  }
  .regions {
    flex-grow: 1;
    * {
      box-sizing: border-box;
    }
  }
  .region-list {
    background: white;
    padding: 15px 15px 5px;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
  }
  .blocks-sidebar, .regions, .settings {
    max-height: calc(100vh - 320px);
    overflow-y: auto;
  }
  .blocks-sidebar {
    width: 200px;
  }
  h5.sub-heading {
    padding: 7px 15px;
    margin: 0;
    position: relative;
    cursor: pointer;
    border-bottom: 1px solid rgba(96, 125, 159, 0.25);
  }
  .settings {
    width: 300px;
    transition: width 0.3s;
  }
  .heading {
    padding: 7px 14px 6px;
    border-bottom: 1px solid rgba(96, 125, 159, 0.25);
    background-color: #f3f7fc;
    background-image: linear-gradient(rgba(51, 64, 77, 0), rgba(51, 64, 77, 0.05));
  }
  h5.slide {
    &:after {
      transition: all 0.3s;
      display: block;
      content: '.';
      font-size: 0;
      width: 5px;
      height: 5px;
      border: solid #596673;
      border-width: 0 2px 2px 0;
      -webkit-transform: rotate(-135deg);
      -o-transform: rotate(-135deg);
      transform: rotate(-135deg);
      top: calc(50% - 2px);
      position: absolute;
      z-index: 1;
      right: 15px;
      -webkit-user-select: none;
      user-select: none;
      pointer-events: none;
    }
    &.closed:after {
      -webkit-transform: rotate(45deg);
      -o-transform: rotate(45deg);
      transform: rotate(45deg);
      top: calc(50% - 5px);
    }
  }
}
</style>

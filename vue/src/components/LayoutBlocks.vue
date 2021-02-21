<template>
  <div class="blocks">
    <div class="spinner-wrapper" v-if="isLoading">
      <div class="spinner"></div>
    </div>
    <div class="blocks-sidebar">
      <div class="heading">
        <h3>Blocks</h3>
      </div>
      <div v-for="provider in providers" v-bind:key="provider.handle">
        <h5 class="sub-heading slide" @click="slideStates[provider.handle] = !slideStates[provider.handle]">{{ provider.name }}</h5>
        <transition name="slide">
          <div>
            <draggable v-if="slideStates[provider.handle]"
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
        </transition>
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
    <div class="options">
      <div class="heading">
          <h3>{{ t('Options') }}</h3>
      </div>
      <div class="options-form" v-for="block in blocks">
        <block-options v-show="blockOptionId == block.index"
          :block="block"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Block from './Block.vue';
import BlockOptions from './BlockOptions.vue';
import Region from './Region.vue';
import Draggable from 'vuedraggable';
import Mixin from '../mixin';
import { reduce } from 'lodash';

export default {
    computed: {
        isLoading: function () {
            return reduce(this.isFetching, function (res, elem) {
                return (res || elem);
            }, this.isSaving);
        },
        ...mapState(['blocks', 'regions', 'isFetching', 'isSaving', 'blockOptionId', 'providers'])
    },
    props: {
    },
    watch: {
        blocks: {
            deep: true,
            handler() {
                this.checkChanges();
            }
        },
        providers: { 
            deep: true,
            handler(providers) {
                let first = true;
                for (let i in providers) {
                    this.slideStates[i] = first;
                    first = false;
                }
            }
        }
    },
    data: function () {
        return {
            slideStates: {}
        }
    },
    created: function () {
        this.fetchProviders();
    },
    methods: {
        ...mapMutations([]),
        ...mapActions(['checkChanges', 'fetchProviders'])
    },
    components: {
        Region,
        Block,
        BlockOptions,
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
.slide-enter-active {
    transition-duration: 0.3s;
    transition-timing-function: ease-in;
}

.slide-leave-active {
    transition-duration: 0.3s;
    transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
}

.slide-enter-to, .slide-leave {
    max-height: 100px;
    overflow: hidden;
}

.slide-enter, .slide-leave-to {
    overflow: hidden;
    max-height: 0;
}

.blocks {
    position: relative;
    display: flex;
    border-radius: 3px;
    background-color: #f3f7fc;
    overflow: hidden;
    border: 1px solid rgba(96, 125, 159, 0.25);
    box-shadow: 0 0 0 1px rgba(31, 41, 51, 0.1), 0 2px 5px -2px rgba(31, 41, 51, 0.2);
    .spinner-wrapper {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(230, 230, 230, 0.7);
        z-index: 1000;
        .spinner {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
    }
    .regions, .options, .block-sidebar{
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
    .blocks-sidebar, .regions, .options {
        max-height: calc(100vh - 320px);
        overflow-y: auto;
    }
    .blocks-sidebar {
        width: 20%;
        max-width: 300px;
    }
    h5.sub-heading {
        padding: 7px 15px;
        margin: 0;
        position: relative;
        cursor: pointer;
        border-bottom: 1px solid rgba(96, 125, 159, 0.25);
    }
    .options {
        width: 20%;
        max-width: 300px;
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

<template>
    <div
        class="region"
        :style="'width:'+(region.width ? region.width : '100%')"
    >
        <div class="region-heading">
            <h5 class="region-title">
                {{ region.name }}
            </h5>
            <span
                v-if="showFieldHandles"
                class="code small light copytextbtn"
                title="Copy to clipboard"
                role="button"
                @click="copyValue"
            >
                <input
                    type="text"
                    :value="region.handle"
                    readonly=""
                    :size="region.handle.length"
                >
                <span
                    data-icon="clipboard"
                    aria-hidden="true"
                />
            </span>
        </div>
        <draggable
            item-key="index"
            class="region-blocks"
            :list="regionBlocks"
            group="blocks"
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

export default {
    components: {
        Block,
        Draggable
    },
    props: {
        region: {
            type: Object,
            default: null
        }
    },
    computed: {
        regionBlocks () {
            return sortBy(filter(this.blocks, (block) => {
                return this.region.handle == block.region;
            }), 'order');
        },
        ...mapState(['blocks', 'showFieldHandles']),
    },
    methods: {
        changed: function (evt) {
            let newIndex;
            let block;
            let blocks;
            if (evt.added) {
                newIndex = evt.added.newIndex;
                block = {...evt.added.element};
                block.region = this.region.handle;
                block.order = newIndex;
                if (block.index === undefined) {
                    block.active = true;
                    this.addBlock(block);
                } else {
                    this.updateBlock(block);
                }
                blocks = [...this.regionBlocks];
            } else if (evt.moved) {
                newIndex = evt.moved.newIndex;
                block = evt.moved.element;
                blocks = [...this.regionBlocks];
            } else {
                blocks = filter(this.regionBlocks, (b) => b.index !== evt.removed.element.index);
            }
            let newOrder = 0;
            for (let i in blocks) {
                let block2 = blocks[i];
                if (i !== newIndex) {
                    block2.order = newOrder;
                }
                newOrder++;
                this.updateBlock(block2);
            }
            if (block) {
                block.order = newIndex;
                this.updateBlock(block);
            }
        },
        copyValue: function(e) {
            let input = e.target;
            input.select();
            document.execCommand('copy');
            Craft.cp.displayNotice(this.t('Copied to clipboard.', 'app'));
            input.setSelectionRange(0, 0);
        },
        ...mapMutations(['addBlock', 'removeBlock', 'updateBlock']),
        ...mapActions([])
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
    margin-right: 10px;
  }
  .region-blocks {
    min-height: 42px;
    margin-top: 5px;
    box-shadow: 0 0 0 1px rgba(31, 41, 51, 0.1), 0 2px 5px -2px rgba(31, 41, 51, 0.2);
    border-radius: 3px;
    &.drop-active {
      padding-bottom: 58px;
    }
  }
  .region-heading {
    display: flex;
    align-items: center;
    .copytextbtn:hover {
      margin-left: 0;
      margin-right: -16px;
    }
  }
</style>

<template>
    <div :class="{'block': true, original: original, active: (block.active && !original)}">
        <div class="inner">
            <div class="description">
                <div class="name">{{ block.name }} <span class="red-star" v-if="hasErrors">*</span></div>
                <div class="small" v-if="original">{{ block.smallDescription }}</div>
                <div class="code small light copytextbtn" title="Copy to clipboard" role="button" v-if="!original && showFieldHandles" @click="copyValue">
                    <input type="text" :value="fullName" readonly="" :size="fullName.length">
                    <span data-icon="clipboard" aria-hidden="true"></span>
                </div>
            </div>
            <span class="info" v-if="original && block.longDescription">{{ block.longDescription }}</span>
            <div class="actions">
                <a v-if="!original" :class="'settings icon' + (blockOptionId == block.index ? ' active' : '')" @click.prevent="setShowOptionsModal({show:true, block: block})"></a>
                <a v-if="!original" class="delete icon" @click.prevent="$emit('remove', block)"></a>
            </div>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState } from 'vuex';

export default {
    computed: {
        fullName: function () {
            return this.block.provider + '_' + this.block.handle
        },
        hasErrors: function () {
            return Object.keys(this.block.errors).length > 0;
        },
        ...mapState(['blocks', 'blockOptionId', 'showFieldHandles'])
    },
    props: {
        block: Object,
        original: Boolean
    },
    mounted () {
        Craft.initUiElements(this.$el);
    },
    methods: {
        copyValue: function(e) {
            let input = e.target;
            input.select();
            document.execCommand('copy');
            Craft.cp.displayNotice(this.t('Copied to clipboard.', 'app'));
            input.setSelectionRange(0, 0);
        },
        ...mapMutations(['updateBlock', 'setShowOptionsModal']),
    },
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
    min-height: 34px;
    display: flex;
    align-items: center;
    cursor: grab;
    .red-star {
        color: red;
    }
    .copytextbtn:hover {
        margin-left: 0;
        margin-right: -16px;
    }
    .copytextbtn {
        margin: 3px 0;
    }
    .name {
        margin: 3px 0;
        margin-right: 5px;
    }
    .actions {
        display: flex;
        margin-left: auto;
        & > div {
            margin-bottom: 2px;
        }
    }
    .inner {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        overflow: hidden;
    }
    &.original {
        opacity: 1;
        background-color: #e4edf6;
        .description {
            flex-direction: column;
            margin-right: 5px;
        }
        .name {
            margin: 0;
        }
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
        display: flex;
        flex-wrap: wrap;
    }
    .delete, .settings {
        padding: 0 2px;
        cursor: pointer;
        font-size: 16px;
    }
    .settings {
        opacity: 0.5;
        &:hover {
            opacity: 1;
        }
    }
}
</style>

<template>
    <div :class="{'block': true, original: original, active: (block.active && !original)}">
        <div class="inner">
            <div class="description">
                <div class="name">
                    {{ block.name }} <span
                        v-if="hasErrors"
                        class="error"
                        data-icon="alert"
                        aria-label="Error"
                    />
                </div>
                <div
                    v-if="original"
                    class="small"
                >
                    {{ block.smallDescription }}
                </div>
                <div
                    v-if="!original && showFieldHandles"
                    class="code small light copytextbtn"
                    title="Copy to clipboard"
                    role="button"
                    @click="copyValue"
                >
                    <input
                        type="text"
                        :value="fullName"
                        readonly=""
                        :size="fullName.length"
                    >
                    <span
                        data-icon="clipboard"
                        aria-hidden="true"
                    />
                </div>
            </div>
            <span
                v-if="original && block.longDescription"
                class="info"
            >{{ block.longDescription }}</span>
            <div class="actions">
                <a
                    v-if="!original"
                    :class="{settings: true, icon: true, active: blockOptionId == block.index, error: hasErrors}"
                    @click.prevent="setShowOptionsModal({show:true, block: block})"
                />
                <a
                    v-if="!original"
                    class="delete icon"
                    @click.prevent="$emit('remove', block)"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState } from 'vuex';

export default {
    props: {
        block: {
            type: Object,
            default: null
        },
        original: Boolean
    },
    emits: ['remove'],
    computed: {
        fullName: function () {
            return this.block.provider + '-' + this.block.handle
        },
        hasErrors: function () {
            return Object.keys(this.block.errors).length > 0;
        },
        ...mapState(['blocks', 'blockOptionId', 'showFieldHandles'])
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
    }
};
</script>

<style lang="scss" scoped>
@import '~craftcms-sass/_mixins';
  .block {
    padding: 8px 14px;
    border-bottom: solid $grey200;
    border-width: 1px 0;
    transition: all 0.3s;
    background-color: $grey100;
    opacity: 0.5;
    min-height: 34px;
    display: flex;
    align-items: center;
    cursor: grab;
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
    .icon::before {
        margin-top: -2px;
    }
}
</style>

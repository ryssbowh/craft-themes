<template>
    <div class="group">
        <div :class="classes">
            <div class="move col"><div class="move icon"></div></div>
            <div class="title col">
                {{ item.name }}
            </div>
            <div class="handle col">
                {{ item.handle }}
            </div>
            <div class="type col">
                {{ t('Group') }}
            </div>
            <div class="label col">
                <div class="select">
                    <select @change="updateLabelVisibility">
                        <option value="hidden" :selected="item.labelHidden">{{ t('Hidden') }}</option>
                        <option value="visuallyHidden" :selected="item.labelVisuallyHidden">{{ t('Visually hidden') }}</option>
                        <option value="visible" :selected="labelVisible">{{ t('Visible') }}</option>
                    </select>
                </div>
            </div>
            <div class="visibility col">
                <slot name="visibility">
                    <div class="select">
                        <select @change="updateVisibility">
                            <option value="hidden" :selected="item.hidden">{{ t('Hidden') }}</option>
                            <option value="visuallyHidden" :selected="item.visuallyHidden">{{ t('Visually hidden') }}</option>
                            <option value="visible" :selected="visible">{{ t('Visible') }}</option>
                        </select>
                    </div>
                </slot>
            </div>
            <div class="displayer col">
            </div>
            <div class="options col">
                <a href="#" @click.prevent="editGroup">{{ t('Edit') }}</a>
                <a href="#" @click.prevent="deleteGroup" v-if="!item.displays.length" class="delete">{{ t('Delete') }}</a>
            </div>
        </div>
        <div class="displays">
            <i v-if="!item.displays.length">{{ t('No displays in that group') }}</i>
            <draggable
                item-key="id"
                :list="item.displays"
                :group="{name: 'displays', put: canPut}"
                handle=".move"
                @change="onDragChange"
                >
                <template #item="{element}">
                    <display-item :display="element"/>
                </template>
            </draggable>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import { filter, find } from 'lodash';

export default {
    computed: {
        classes: function () {
            return {
                line: true,
                opaque: (this.item.hidden || this.item.visuallyHidden)
            };
        },
        visible: function () {
            return (!this.item.visuallyHidden && !this.item.hidden);
        },
        labelVisible: function () {
            return (!this.item.labelVisuallyHidden && !this.item.labelHidden);
        },
        ...mapState(['displays'])
    },
    props: {
        item: Object,
        display: {
            type: Object,
            default: {}
        }
    },
    methods: {
        updateLabelVisibility: function (e) {
            let val = e.originalTarget.value;
            let data = {
                labelHidden: false,
                labelVisuallyHidden: false
            };
            if (val == 'hidden') {
                data.labelHidden = true;
            } else if (val == 'visuallyHidden') {
                data.labelHidden = false;
                data.labelVisuallyHidden = true;
            }
            this.$emit("updateItem", data);
        },
        updateVisibility: function (e) {
            let val = e.originalTarget.value;
            let data = {
                hidden: false,
                visuallyHidden: false
            };
            if (val == 'hidden') {
                data.hidden = true;
            } else if (val == 'visuallyHidden') {
                data.hidden = false;
                data.visuallyHidden = true;
            }
            this.$emit("updateItem", data);
        },
        canPut: function (a,b,elem) {
            return !$(elem).hasClass('group');
        },
        editGroup: function () {
            this.setShowGroupModal({show: true, editUid: this.display.uid});
        },
        deleteGroup: function () {
            this.$emit('delete');
        },
        onDragChange: function (e) {
            if (e.added) {
                this.addDisplayToGroup({display: e.added.element, groupUid: this.display.uid});
            }
        },
        ...mapMutations(['setShowGroupModal', 'addDisplayToGroup']),
        ...mapActions([]),
    },
    emits: ['updateItem', 'delete'],
};
</script>
<style lang="scss" scoped>
    .delete {
        margin-left: 5px;
    }
    .displays {
        border: 1px solid #f3f7fc;
        border-radius: 5px;
        padding: 5px;
        position: relative;
        i {
            position: absolute;
            left: 5px;
            top: 19px;
        }
        & > div {
            min-height: 50px;
        }
    }
    .line.opaque ~ .displays {
        opacity: 0.5;
    }
</style>
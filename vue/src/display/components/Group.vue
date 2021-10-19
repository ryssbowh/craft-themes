<template>
    <div class="group">
        <div :class="classes">
            <div class="move col"><div class="move icon"></div></div>
            <div class="title col">
                <span class="name">{{ item.name }}</span>
                <div class="code small light copytextbtn" title="Copy to clipboard" role="button" v-if="showFieldHandles" @click="copyValue">
                    <input type="text" :value="'group-' + item.handle" readonly="" :size="('group-' + item.handle).length">
                    <span data-icon="clipboard" aria-hidden="true"></span>
                </div>
            </div>
            <div class="type col code">
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
                <a href="#" @click.prevent="editGroup"><span class="icon settings"></span></a>
                <a href="#" @click.prevent="deleteGroup" v-if="!groupDisplays.length" class="delete"><span class="icon delete"></span></a>
            </div>
        </div>
        <div class="displays">
            <span v-if="!groupDisplays.length" class="no-displays"><i>{{ t('No displays in that group') }}</i></span>
            <draggable
                item-key="uid"
                :list="groupDisplays"
                :group="{name: 'displays', put: canPut}"
                handle=".move"
                @change="onDragChange"
                >
                <template #item="{element}">
                    <display-item :display="element" @updateItem="updateItem($event, element.uid)"/>
                </template>
            </draggable>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import { sortBy, merge } from 'lodash';

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
        groupDisplays: function () {
            return sortBy(this.item.displays, 'order');
        },
        ...mapState(['showFieldHandles', 'labelsVisibility', 'itemsVisibility'])
    },
    props: {
        item: Object,
        display: {
            type: Object,
            default: function () {
                return {};
            }
        }
    },
    watch: {
        itemsVisibility: function () {
            this.$emit("updateItem", {hidden: !this.itemsVisibility});
        },
        labelsVisibility: function () {
            this.$emit("updateItem", {labelHidden: !this.labelsVisibility});
        }
    },
    methods: {
        copyValue: function(e) {
            let input = e.target;
            input.select();
            document.execCommand('copy');
            Craft.cp.displayNotice(this.t('Copied to clipboard.', 'app'));
            input.setSelectionRange(0, 0);
        },
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
        canPut: function (a, b, elem) {
            return !$(elem).hasClass('group');
        },
        editGroup: function () {
            this.setShowGroupModal({show: true, editUid: this.display.uid});
        },
        deleteGroup: function () {
            this.removeDisplay(this.display);
        },
        onDragChange: function (e) {
            if (e.added) {
                e.added.element.group_id = null;
                this.addDisplayToGroup({display: e.added.element, groupUid: this.display.uid});
            } else if (e.removed) {
                this.removeDisplayFromGroup({display: e.removed.element, groupUid: this.display.uid});
            }
            this.rebuildOrders(e);
        },
        updateItem: function (data, uid) {
            let display;
            for (let i in this.display.item.displays) {
                display = this.display.item.displays[i];
                if (display.uid != uid) {
                    continue;
                }
                display = merge(display, {item: data});
                break;
            }
        },
        rebuildOrders: function (e) {
            let newIndex, movedElem;
            if (e.added) {
                newIndex = e.added.newIndex;
                movedElem = e.added.element;
            } else if (e.moved) {
                newIndex = e.moved.newIndex;
                movedElem = e.moved.element;
            }
            let newOrder = 0;
            for (let i in this.groupDisplays) {
                let display = this.groupDisplays[i];
                if (movedElem && newOrder == newIndex) {
                    newOrder++;
                }
                if (movedElem && display.id == movedElem.id) {
                    continue;
                }
                display.order = newOrder;
                newOrder++;
            }
            if (movedElem) {
                movedElem.order = newIndex;
            }
        },
        ...mapMutations(['setShowGroupModal', 'addDisplayToGroup', 'removeDisplayFromGroup', 'removeDisplay']),
        ...mapActions([]),
    },
    emits: ['updateItem', 'delete'],
};
</script>
<style lang="scss" scoped>
    .delete {
        margin-left: 5px;
    }
    .group {
        background: #f3f7fc;
        border-radius: 5px;
        padding: 7px 0;
        .line:first-child {
            margin-top: 0;
        }
    }
    .displays {
        position: relative;
        .no-displays {
            position: absolute;
            left: 15px;
            top: 19px;
            opacity: 0.7;
        }
        & > div {
            min-height: 50px;
        }
    }
    .line.opaque ~ .displays {
        opacity: 0.5;
    }
</style>
<style lang="scss">
    .display-table .group .displays .col.move {
        justify-content: center;
    }
</style>
<template>
    <div :class="classes">
        <div class="move col"><div class="move icon" v-if="moveable"></div></div>
        <div class="title col">
            <span class="name">{{ item.name }} <span class="error" data-icon="alert" aria-label="Error" v-if="hasErrors"></span></span>
            <div class="code small light copytextbtn" title="Copy to clipboard" role="button" v-if="showFieldHandles && fullHandle" @click="copyValue">
                <input type="text" :value="fullHandle" readonly="" :size="fullHandle.length">
                <span data-icon="clipboard" aria-hidden="true"></span>
            </div>
        </div>
        <div class="type col code">
            {{ item.displayName }}
        </div>
        <div class="label col">
            <div class="select" v-if="hasLabel">
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
                        <option v-if="hasDisplayers" value="visuallyHidden" :selected="item.visuallyHidden">{{ t('Visually hidden') }}</option>
                        <option v-if="hasDisplayers" value="visible" :selected="visible">{{ t('Visible') }}</option>
                    </select>
                </div>
            </slot>
        </div>
        <div class="displayer col">
            <div class="select" v-if="hasDisplayers">
                <select @change="updateDisplayer">
                    <option v-if="!displayer">{{ t('Select') }}</option>
                    <option v-for="displayer2 in item.availableDisplayers" :key="displayer2.handle" :selected="displayer && displayer.handle == displayer2.handle" :value="displayer2.handle">{{ displayer2.name }}</option>
                </select>
            </div>
            <span v-if="!hasDisplayers">{{ t('None available') }}</span>
        </div>
        <div class="options col">
            <a v-if="displayer && displayer.hasOptions" href="#" @click.prevent="showModal = true"><div :class="{icon: true, settings: true, error: hasErrors}"></div></a>
        </div>
        <options-modal @onSave="onSaveModal" v-if="showModal" :displayerHasChanged="displayerHasChanged" :displayer="displayer" :item="item" @onHide="closeModal"/>
    </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
    computed: {
        classes: function () {
            let classes = {
                line: true, 
                opaque: this.isOpaque
            };
            classes[this.item.type] = true;
            return classes;
        },
        hasErrors: function () {
            if (Array.isArray(this.item.errors)) {
                return this.item.errors.length > 0;
            }
            return Object.keys(this.item.errors).length > 0;
        },
        visible: function () {
            return !this.item.visuallyHidden && !this.item.hidden
        },
        labelVisible: function () {
            return !this.item.labelVisuallyHidden && !this.item.labelHidden
        },
        hasDisplayers: function () {
            return this.item.availableDisplayers.length > 0;
        },
        displayerDefined: function () {
            if (!this.item.displayerHandle) {
                return false;
            }
            return this.displayer !== false;
        },
        isOpaque: function () {
            return (this.item.hidden || this.item.visuallyHidden || !this.displayerDefined || !this.hasDisplayers);
        },
        fullHandle: function () {
            if (!this.displayer) {
                return '';
            }
            return this.displayer.handle
        },
        ...mapState(['showFieldHandles', 'itemsVisibility', 'labelsVisibility', 'switchLabelsVisibility', 'switchItemsVisibility'])
    },
    props: {
        item: Object,
        display: {
            type: Object,
            default: () => {}
        },
        moveable: {
            type: Boolean,
            default: true
        },
        hasLabel: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            showModal: false,
            displayerHasChanged: false,
            displayer: false
        }
    },
    created() {
        this.displayer = this.getDisplayer(this.item.displayerHandle);
    },
    watch: {
        switchItemsVisibility: function () {
            this.$emit("updateItem", {hidden: !this.itemsVisibility});
        },
        switchLabelsVisibility: function () {
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
        closeModal: function () {
            this.showModal = false;
            this.displayerHasChanged = false;
        },
        onSaveModal: function (data) {
            this.$emit("updateItem", {options: data});
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
                data.visuallyHidden = true;
            }
            this.$emit("updateItem", data);
        },
        getDisplayer: function (handle) {
            for (let i in this.item.availableDisplayers) {
                if (this.item.availableDisplayers[i].handle == handle) {
                    return this.item.availableDisplayers[i];
                }
            }
            return false;
        },
        updateDisplayer: function(e) {
            this.$emit("updateItem", {displayerHandle: e.originalTarget.value});
            this.displayerHasChanged = true;
            this.displayer = this.getDisplayer(e.originalTarget.value);
            if(this.displayer.hasOptions) {
                this.showModal = true;
            }
        }
    },
    emits: ['updateItem'],
};
</script>
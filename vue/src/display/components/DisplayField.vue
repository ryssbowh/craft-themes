<template>
    <div :class="{line: true, display: true, visuallyHidden: display.visuallyHidden}">
        <div class="handle col"><div class="move icon"></div></div>
        <div class="title col">{{ field.name }}</div>
        <div class="handle col">{{ field.handle }}</div>
        <div class="type col">{{ field.type }}</div>
        <div class="label col">
            <div class="select">
                <select @change="updateLabelVisibility">
                    <option value="hidden" :selected="display.labelHidden">{{ t('Hidden') }}</option>
                    <option value="visuallyHidden" :selected="display.labelVisuallyHidden">{{ t('Visually hidden') }}</option>
                    <option value="visible" :selected="labelVisible">{{ t('Visible') }}</option>
                </select>
            </div>
        </div>
        <div class="visibility col">
            <div class="select">
                <select @change="updateVisibility">
                    <option value="hidden" :selected="display.hidden">{{ t('Hidden') }}</option>
                    <option v-if="hasDisplayers" value="visuallyHidden" :selected="display.visuallyHidden">{{ t('Visually hidden') }}</option>
                    <option v-if="hasDisplayers" value="visible" :selected="visible">{{ t('Visible') }}</option>
                </select>
            </div>
        </div>
        <div class="displayer col">
            <div class="select" v-if="hasDisplayers">
                <select @change="updateDisplayer">
                    <option v-if="!displayer">{{ t('Select') }}</option>
                    <option v-for="displayer2 in field.availableDisplayers" :key="displayer.handle" :selected="displayer && displayer.handle == displayer2.handle" :value="displayer2.handle">{{ displayer2.name }}</option>
                </select>
            </div>
            <span v-if="!hasDisplayers">{{ t('None available') }}</span>
        </div>
        <div class="options col">
            <a v-if="displayer && displayer.hasOptions" href="#" @click.prevent="showModal = true">{{ t('Options') }}</a>
            <options-modal v-if="displayer && displayer.hasOptions" :display="display" :show-modal="showModal" @saveModal="onSaveModal" @closeModal="showModal = false"/>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Mixin from '../../mixin';
import OptionsModal from './OptionsModal';

export default {
    computed: {
        displayer: function () {
            for (let i in this.field.availableDisplayers) {
                if (this.field.availableDisplayers[i].handle == this.field.displayerHandle) {
                    return this.field.availableDisplayers[i];
                }
            }
        },
        visible: function () {
            return !this.display.visuallyHidden && !this.display.hidden
        },
        labelVisible: function () {
            return !this.display.labelVisuallyHidden && !this.display.labelHidden
        },
        hasDisplayers: function () {
            return this.field.availableDisplayers.length > 0;
        },
        ...mapState([])
    },
    props: {
        display: Object
    },
    data() {
        return {
            showModal: false,
            field: {}
        }
    },
    created() {
        this.field = this.display.item;
    },
    methods: {
        onSaveModal: function (data) {
            this.updateDisplay(data);
            this.showModal = false;
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
            this.updateDisplay({id: this.display.id, data: data});
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
            this.updateDisplay({id: this.display.id, data: data});
            this.$emit('changedVisibility', {id: this.display.id});
        },
        updateDisplayer: function(e) {
            this.updateDisplay({id: this.display.id, data: {item: {displayerHandle: e.originalTarget.value}}});
        },
        ...mapMutations(['updateDisplay']),
        ...mapActions([]),
    },
    mixins: [Mixin],
    components: {OptionsModal},
    emits: ['changedVisibility'],
};
</script>
<style lang="scss" scoped>
.display {
    &.visuallyHidden {
        opacity: 0.8;
    }
}
</style>
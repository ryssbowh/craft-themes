<template>
    <tr :class="{field: true, visuallyHidden: field.visuallyHidden}">
        <td>{{ field.name }}</td>
        <td>{{ field.field }}</td>
        <td>{{ field.type }}</td>
        <td>
            <div class="select">
                <select>
                    <option value="hidden" :selected="field.labelHidden">{{ t('Hidden') }}</option>
                    <option value="visuallyHidden" :selected="field.labelVisuallyHidden">{{ t('Visually hidden') }}</option>
                    <option value="visible" :selected="visible">{{ t('Visible') }}</option>
                </select>
            </div>
        </td>
        <td>
            <div class="select">
                <select @change="updateVisibility">
                    <option value="hidden" :selected="field.hidden">{{ t('Hidden') }}</option>
                    <option v-if="hasDisplayers" value="visuallyHidden" :selected="field.visuallyHidden">{{ t('Visually hidden') }}</option>
                    <option v-if="hasDisplayers" value="visible" :selected="visible">{{ t('Visible') }}</option>
                </select>
            </div>
        </td>
        <td>
            <div class="select" v-if="hasDisplayers">
                <select @change="updateDisplayer">
                    <option v-if="!displayer">{{ t('Select') }}</option>
                    <option v-for="displayer2 in field.availableDisplayers" :selected="displayer && displayer.handle == displayer2.handle" :value="displayer2.handle">{{ displayer2.name }}</option>
                </select>
            </div>
            <span v-if="!hasDisplayers">{{ t('None available') }}</span>
        </td>
        <td>
            <a v-if="displayer && displayer.hasOptions" href="#" @click.prevent="showModal = true">{{ t('Options') }}</a>
            <options-modal v-if="displayer && displayer.hasOptions" :field="field" :show-modal="showModal" @closeModal="onCloseModal"/>
        </td>
    </tr>
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
            return !this.field.visuallyHidden && !this.field.hidden
        },
        hasDisplayers: function () {
            return this.field.availableDisplayers.length > 0;
        },
        ...mapState([])
    },
    props: {
        field: Object
    },
    data() {
        return {
            showModal: false
        }
    },
    methods: {
        onCloseModal: function (data) {
            this.updateField(data);
            this.showModal = false;
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
            this.updateField({id: this.field.id, data: data});
        },
        updateDisplayer: function(e) {
            this.updateField({id: this.field.id, data: {displayerHandle: e.originalTarget.value}});
        },
        ...mapMutations(['updateField']),
        ...mapActions([]),
    },
    mixins: [Mixin],
    components: {OptionsModal}
};
</script>
<style lang="scss" scoped>
.field {
    &.visuallyHidden {
        opacity: 0.8;
    }
}
</style>
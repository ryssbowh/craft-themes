<template>
    <form-field :errors="errors" :definition="definition" :name="name">
        <template v-slot:main>
            <div class="field" v-for="elem, typeUid in definition.options" v-bind:key="typeUid">
                <div class="heading">
                    <label class="required">{{ t('View mode for {type}', {type: elem.label}) }}</label>
                </div>
                <div :class="inputClass">                    
                    <div class="select">
                        <select v-model="realValue[typeUid]">
                            <option v-for="label, uid in elem.viewModes" :value="uid" v-bind:key="uid">{{ label }}</option>
                        </select>
                    </div>
                </div>
                <ul class="errors" v-if="errors['viewMode-'+typeUid]">
                    <li v-for="error, index in errors['viewMode-'+typeUid]" v-bind:key="index">{{ error }}</li>
                </ul>
            </div>
        </template>
    </form-field>
</template>

<script>
import FormField from './Field';

export default {
    data: function () {
        return {
            realValue: {}
        }
    },
    computed: {
        inputClass: function () {
            return 'input ' + Craft.orientation;
        }
    },
    props: {
        value: Object,
        definition: Object,
        errors: Array,
        name: String
    },
    created() {
        if (this.value !== null) {
            this.realValue = this.value;
        } else {
            for (let typeUid in this.definition.options) {
                let keys = Object.keys(this.definition.options[typeUid].viewModes);
                this.realValue[typeUid] = keys[0] ?? null;
            }
        }
    },
    watch: {
        realValue: {
            handler: function () {
                this.$emit('change', this.realValue);
            },
            deep: true
        }
    },
    components: {
        'form-field': FormField
    },
    emits: ['change']
};
</script>

<template>
    <form-field :errors="errors" :definition="definition" :name="name">
        <template v-slot:main>
            <div :class="inputClass">
                <div class="select">
                    <select v-model="realValue" :disabled="definition.disabled" :autofocus="definition.autofocus ?? false">
                        <option v-for="label, value2 in definition.options ?? {}" :value="value2" v-bind:key="value2">{{ label }}</option>
                    </select>
                </div>
            </div>
        </template>
    </form-field>
</template>

<script>
import FormField from './Field';

export default {
    computed: {
        inputClass() {
            return 'input ' + Craft.orientation;
        }
    },
    data: function () {
        return {
            realValue: {}
        }
    },
    created() {
        this.realValue = this.value;
    },
    props: {
        value: String,
        definition: Object,
        errors: Array,
        name: String
    },
    watch: {
        realValue: function () {
            this.$emit('change', this.realValue);
        }
    },
    components: {
        'form-field': FormField
    },
    emits: ['change']
};
</script>

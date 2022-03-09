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
    props: {
        value: [Number, String],
        definition: Object,
        errors: Array,
        name: String
    },
    created() {
        this.realValue = this.value;
    },
    watch: {
        realValue: function () {
            this.$emit('change', this.realValue);
        }
    },
    components: {
        'form-field': FormField
    },
    emits: ['change'],
    template: `
        <form-field :errors="errors" :definition="definition" :name="name">
            <template v-slot:main>
                <div :class="inputClass">
                    <input :class="{text: true, fullwidth: !definition.size}" :type="definition.type ?? 'text'" v-model="realValue" :maxlength="definition.maxlength" :autofocus="definition.autofocus ?? false" :disabled="definition.disabled" :readonly="definition.readonly ?? false" :placeholder="definition.placeholder" :step="definition.step" :min="definition.min" :max="definition.max" :size="definition.size">
                </div>
            </template>
        </form-field>`
};

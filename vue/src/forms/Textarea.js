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
        value: String,
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
                    <textarea :class="{text: true, fullwidth: !definition.cols}" :type="definition.type ?? 'text'" v-model="realValue" :maxlength="definition.maxlength" :autofocus="definition.autofocus ?? false" :disabled="definition.disabled" :readonly="definition.readonly ?? false" :placeholder="definition.placeholder" :cols="definition.cols ?? 50" :rows="definition.rows ?? 2"></textarea>
                </div>
            </template>
        </form-field>`
};
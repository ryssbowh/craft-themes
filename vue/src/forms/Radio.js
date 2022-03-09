import FormField from './Field';

export default {
    computed: {
        inputClass() {
            return 'input ' + Craft.orientation;
        }
    },

    props: {
        value: String,
        definition: Object,
        errors: Array,
        name: String
    },
    watch: {
        value: function () {
            this.$emit('change', this.value);
        }
    },
    data: function () {
        return {
            id: null
        }
    },
    created() {
        this.id = Math.floor(Math.random() * 1000000);
    },
    mounted() {
        let _this = this;
        $(this.$el).find('[type=radio]').on('change', function () {
            _this.$emit('change', $(this).val());
        });
    },
    components: {
        'form-field': FormField
    },
    emits: ['change'],
    template: `
        <form-field :errors="errors" :definition="definition" :name="name">
            <template v-slot:main>
                <div :class="inputClass">
                    <fieldset class="radio-group">
                        <div v-for="rvalue, label in definition.options" v-bind:key="rvalue">
                            <label>
                                <input type="radio" :selected="rvalue == value" :value="rvalue" :disabled="definition.disabled" :name="name">
                                {{ label }}
                            </label>
                        </div>
                    </fieldset>
                </div>
            </template>
        </form-field>`
};
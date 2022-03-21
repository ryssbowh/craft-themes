import FormField from './Field';

export default {
    props: {
        value: String,
        definition: Object,
        errors: Array,
        name: String
    },
    mounted() {
        this.$nextTick(() => {
            new Craft.ColorInput($(this.$el).find('.color-container'));
            $(this.$el).find('input.color-preview-input').on('change', () => {
                this.$emit('change', $(this.$el).find('input.color-preview-input').val());
            });
        });
    },
    components: {
        'form-field': FormField
    },
    emits: ['change'],
    template: `
        <form-field :errors="errors" :definition="definition" :name="name">
            <template v-slot:main>
                <div class="flex color-container">
                    <div class="color static">
                        <div class="color-preview" :style="value ? 'background-color:' + value : ''"></div>
                    </div>
                    <input class="color-input text" type="text" size="10" :value="value">
                </div>
            </template>
        </form-field>`
};

<template>
    <form-field :errors="errors" :definition="definition" :name="name">
        <template v-slot:main>
            <div :class="inputClass">
                <div class="datewrapper">
                    <input type="text" class="text" :value="value" size="10" autocomplete="off" placeholder=" ">
                    <div data-icon="date"></div>
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
    props: {
        value: String,
        definition: Object,
        errors: Array,
        name: String
    },
    components: {
        'form-field': FormField
    },
    mounted() {
        this.$nextTick(() => {
            $(this.$el).find('input.text').datepicker(Craft.datepickerOptions);
            $(this.$el).find('input.text').on('change', () => {
                this.$emit('change', $(this.$el).find('input.text').val());
            });
        });
    },
    emits: ['change']
};
</script>

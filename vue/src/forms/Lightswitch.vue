<template>
    <form-field :errors="errors" :definition="definition" :name="name">
        <template v-slot:main>
            <div :class="inputClass">
                <button type="button" :class="{lightswitch: true, on: value}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" :value="value ? 1 : ''">
                </button>
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
        value: Boolean,
        definition: Object,
        errors: Array,
        name: String
    },
    mounted () {
        this.$nextTick(() => {
            $(this.$el).find('.lightswitch').on('change', (e) => {
                this.$emit('change', $(e.target).hasClass('on'));
            });
        });
    },
    components: {
        'form-field': FormField
    },
    emits: ['change']
};
</script>

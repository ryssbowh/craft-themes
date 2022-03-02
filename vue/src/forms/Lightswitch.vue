<template>
    <form-field :errors="errors" :definition="definition" :name="name">
        <template v-slot:main>
            <div :class="inputClass">
                <div class="lightswitch-outer-container" v-if="definition.onLabel">
                    <div class="lightswitch-inner-container">
                        <span data-toggle="off" aria-hidden="true" v-if="definition.offLabel">{{ definition.offLabel }}</span>
                        <button type="button" :class="{lightswitch: true, on: value}">
                            <div class="lightswitch-container">
                                <div class="handle"></div>
                            </div>
                            <input type="hidden" :value="value ? 1 : ''">
                        </button>
                        <span data-toggle="off" aria-hidden="true" v-if="definition.onLabel">{{ definition.onLabel }}</span>
                    </div>
                </div>
                <button v-if="!definition.onLabel" type="button" :class="{lightswitch: true, on: value}">
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

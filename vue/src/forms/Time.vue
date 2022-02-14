<template>
    <form-field :errors="errors" :definition="definition" :name="name">
        <template v-slot:main>
            <div :class="inputClass">
                <div class="timewrapper">
                    <input type="text" class="text" :value="value" size="10" autocomplete="off" placeholder=" ">
                    <div data-icon="time"></div>
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
            let options = {
                minTime: this.definition.minTime ?? null,
                maxTime: this.definition.maxTime ?? null,
                disableTimeRanges: this.definition.disableTimeRanges ?? null,
                step: this.definition.minuteIncrement ?? 5,
                forceRoundTime: this.definition.forceRoundTime ?? false,
            };
            options = {...options, ...Craft.timepickerOptions};
            let input = $(this.$el).find('input.text');
            input.timepicker(options);
            input.on('changeTime', () => {
                this.$emit('change', input.val());
            });
        });
    },
    emits: ['change']
};
</script>

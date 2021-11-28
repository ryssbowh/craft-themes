<template>
    <form-field :errors="errors" :definition="definition" :name="name">
        <template v-slot:main>
            <div :class="inputClass">
                <div class="datetimewrapper">
                    <div class="datewrapper">
                        <input type="text" class="text date" :value="value ? value.split(' ')[0] ?? '' : ''" size="10" autocomplete="off" placeholder=" ">
                        <div data-icon="date"></div>
                    </div>
                    <div class="timewrapper">
                        <input type="text" class="text time" :value="value ? value.split(' ')[1] ?? '' : ''" size="10" autocomplete="off" placeholder=" ">
                        <div data-icon="time"></div>
                    </div>
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
            $(this.$el).find('input.date').datepicker(Craft.datepickerOptions);
            $(this.$el).find('input.date').on('change', () => {
                this.updateValue();
            });
            let options = {
                minTime: this.definition.minTime ?? null,
                maxTime: this.definition.maxTime ?? null,
                disableTimeRanges: this.definition.disableTimeRanges ?? null,
                step: this.definition.minuteIncrement ?? 5,
                forceRoundTime: this.definition.forceRoundTime ?? false,
            };
            options = {...options, ...Craft.timepickerOptions};
            let input = $(this.$el).find('input.time');
            input.timepicker(options);
            input.on('changeTime', () => {
                this.updateValue();
            });
        });
    },
    methods: {
        updateValue() {
            let val = $(this.$el).find('input.date').val() + ' ' + $(this.$el).find('input.time').val();
            this.$emit('change', val);
        }
    },
    emits: ['change']
};
</script>

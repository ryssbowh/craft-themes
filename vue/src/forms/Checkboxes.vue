<template>
    <form-field :errors="errors" :definition="definition" :name="name">
        <template v-slot:main>
            <div :class="inputClass">
                <fieldset class="checkbox-group">
                    <div v-for="label, cvalue in definition.options" v-bind:key="cvalue">
                        <input type="checkbox" :checked="value.includes(cvalue)" class="checkbox" :value="cvalue" :id="id + '-' + cvalue" :disabled="definition.disabled">
                        <label :for="id + '-' + cvalue">
                            {{ label }}
                        </label>
                    </div>
                </fieldset>
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
        value: Array,
        definition: Object,
        errors: Array,
        name: String
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
        $(this.$el).find('[type=checkbox]').on('change', () => {
            let val = [];
            $.each($(this.$el).find('[type=checkbox]'), function (i, elem) {
                if ($(elem).is(':checked')) {
                    val.push($(elem).val());
                }
            });
            this.$emit('change', val);
        });
    },
    components: {
        'form-field': FormField
    },
    emits: ['change']
};
</script>

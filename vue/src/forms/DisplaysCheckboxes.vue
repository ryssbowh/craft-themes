<template>
    <form-field :errors="errors" :definition="definition" :name="name">
        <template v-slot:main>
            <div :class="inputClass">
                <fieldset class="checkbox-group">
                    <div v-for="label, cvalue in fields" v-bind:key="cvalue">
                        <input type="checkbox" :checked="realValue.includes(cvalue)" class="checkbox" :value="cvalue" :id="id + '-' + cvalue" :disabled="definition.disabled">
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
import { mapState } from 'vuex';

export default {
    computed: {
        inputClass() {
            return 'input ' + Craft.orientation;
        },
        ...mapState(['theme'])
    },
    props: {
        value: Array,
        definition: Object,
        errors: Array,
        name: String
    },
    data: function () {
        return {
            id: null,
            fields: {},
            realValue: []
        }
    },
    created() {
        this.id = Math.floor(Math.random() * 1000000);
        if (!this.value) {
            this.realValue = [];
        } else {
            this.realValue = this.value;
        }
    },
    methods: {
        fetchFields: function (viewModeUid) {
            let url = 'themes/ajax/view-modes/display-names/' + viewModeUid;
            axios.post(Craft.getCpUrl(url))
            .then((response) => {
                this.fields = response.data.names;
                this.$nextTick(() => {
                    this.initCheckboxes();
                });
            })
            .catch((err) => {
                this.handleError(err);
            });
        },
        initCheckboxes: function () {
            $(this.$el).find('[type=checkbox]').on('change', () => {
                let val = [];
                $.each($(this.$el).find('[type=checkbox]'), function (i, elem) {
                    if ($(elem).is(':checked')) {
                        val.push($(elem).val());
                    }
                });
                this.$emit('change', val);
            });
        }
    },
    mounted() {
        if (this.definition.from) {
            let _this = this;
            let elems = this.definition.from.split(':');
            $(elems[0]).find(elems[1]).change(function () {
                _this.fetchFields($(this).val());
            });
            let val = $(elems[0]).find(elems[1]).val();
            if (val) {
                this.fetchFields(val);
            }
        }
    },
    components: {
        'form-field': FormField
    },
    emits: ['change']
};
</script>

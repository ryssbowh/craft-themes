import FormField from './Field';
import { mapState } from 'vuex';

export default {
    data: function () {
        return {
            realValue: {},
            viewModes: {},
            element: false,
        }
    },
    computed: {
        inputClass: function () {
            return 'input ' + Craft.orientation;
        },
        ...mapState(['theme'])
    },
    props: {
        value: String,
        definition: Object,
        errors: Array,
        name: String
    },
    created() {
        if (this.definition.element ?? null) {
            this.element = this.definition.element;
        }
        this.realValue = this.value;
    },
    mounted() {
        if ((this.definition.element ?? null) && this.definition.element.startsWith('from:')) {
            let elems = this.definition.element.split(':');
            let $elem = $(elems[1]).find(elems[2]);
            this.element = $elem.val();
            $elem.change(() => {
                this.element = $elem.val();
                this.fetchViewModes();
            });
        }
        this.fetchViewModes();
    },
    methods: {
        fetchViewModes () {
            let url = 'themes/ajax/view-modes/' + this.theme + '/' + this.definition.layoutType;
            if (this.element) {
                url += '/' + this.element;
            }
            axios.post(Craft.getCpUrl(url))
            .then((response) => {
                this.viewModes = response.data.viewModes;
            })
            .catch((err) => {
                this.handleError(err);
            });
        }
    },
    watch: {
        realValue: {
            handler: function () {
                this.$emit('change', this.realValue);
            },
            deep: true
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
                    <div class="select">
                        <select v-model="realValue">
                            <option v-for="viewMode in viewModes" :value="viewMode.uid" v-bind:key="viewMode.uid">{{ viewMode.name }}</option>
                        </select>
                    </div>
                </div>
            </template>
        </form-field>`
};
